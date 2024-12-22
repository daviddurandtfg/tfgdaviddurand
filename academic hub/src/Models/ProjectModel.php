<?php
namespace Softhub\Models;
// este archivo es src/Models/ProjectModel.php
use PDO;
use Exception;

class ProjectModel {
    private $db;
    private $table = 'proyectos';
    private $relationTable = 'proyecto_software';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllProjects($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;

            // Consulta principal para obtener proyectos
            $query = "SELECT p.id, p.codigo, p.nombre, p.descripcion, p.activo,
                            p.creado_en, p.actualizado_en,
                            COUNT(ps.software_id) as total_software
                     FROM {$this->table} p
                     LEFT JOIN {$this->relationTable} ps ON p.id = ps.proyecto_id
                     GROUP BY p.id
                     ORDER BY p.creado_en DESC
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            // Consulta para contar total de registros
            $countStmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
            $totalProjects = $countStmt->fetchColumn();

            return [
                'projects' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $totalProjects,
                'pages' => ceil($totalProjects / $limit)
            ];
        } catch (Exception $e) {
            app_log("Error al obtener lista de proyectos: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener la lista de proyectos");
        }
    }

    public function findById($id) {
        try {
            // Obtener información básica del proyecto
            $stmt = $this->db->prepare("
                SELECT id, codigo, nombre, descripcion, activo
                FROM {$this->table}
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$project) {
                return null;
            }

            // Obtener software asociado
            $stmt = $this->db->prepare("
                SELECT s.id, s.nombre, s.version, ps.asignado_en, u.nombre_usuario as asignado_por
                FROM {$this->relationTable} ps
                JOIN software_catalog s ON ps.software_id = s.id
                JOIN usuarios u ON ps.asignado_por = u.id
                WHERE ps.proyecto_id = ?
            ");
            $stmt->execute([$id]);
            $project['software'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $project;
        } catch (Exception $e) {
            app_log("Error al obtener proyecto {$id}: " . $e->getMessage(), 'ERROR');
            throw new Exception("Proyecto no encontrado");
        }
    }

    public function create($data) {
        try {
            $this->validateProjectData($data);

            $this->db->beginTransaction();

            // Insertar proyecto
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (codigo, nombre, descripcion, activo)
                VALUES (:codigo, :nombre, :descripcion, 1)
            ");

            $stmt->execute([
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion']
            ]);

            $projectId = $this->db->lastInsertId();

            // Asignar software si se proporciona
            if (!empty($data['software'])) {
                $this->assignSoftware($projectId, $data['software'], $data['usuario_id']);
            }

            $this->db->commit();
            app_log("Proyecto creado: {$data['nombre']}", 'INFO');
            return $projectId;

        } catch (Exception $e) {
            $this->db->rollBack();
            app_log("Error en creación de proyecto: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function update($id, $data) {
        try {
            $this->validateProjectData($data);

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET codigo = :codigo,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    actualizado_en = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $success = $stmt->execute([
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'id' => $id
            ]);

            // Actualizar asignaciones de software si se proporciona
            if (isset($data['software'])) {
                // Eliminar asignaciones existentes
                $stmt = $this->db->prepare("DELETE FROM {$this->relationTable} WHERE proyecto_id = ?");
                $stmt->execute([$id]);

                // Crear nuevas asignaciones
                if (!empty($data['software'])) {
                    $this->assignSoftware($id, $data['software'], $data['usuario_id']);
                }
            }

            $this->db->commit();
            app_log("Proyecto actualizado: ID {$id}", 'INFO');
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            app_log("Error en actualización de proyecto: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function toggleStatus($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET activo = NOT activo,
                    actualizado_en = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            $success = $stmt->execute([$id]);

            if ($success) {
                app_log("Estado de proyecto actualizado: ID {$id}", 'INFO');
                return true;
            }

            throw new Exception("Error al cambiar el estado del proyecto");
        } catch (Exception $e) {
            app_log("Error al cambiar estado del proyecto: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function getAvailableSoftware($projectId = null) {
        try {
            $query = "
                SELECT id, nombre, version, categoria
                FROM software_catalog
                WHERE activo = 1
                ORDER BY nombre ASC
            ";

            if ($projectId) {
                $query = "
                    SELECT s.id, s.nombre, s.version, s.categoria,
                           CASE WHEN ps.proyecto_id IS NOT NULL THEN 1 ELSE 0 END as asignado
                    FROM software_catalog s
                    LEFT JOIN proyecto_software ps ON s.id = ps.software_id AND ps.proyecto_id = :projectId
                    WHERE s.activo = 1
                    ORDER BY s.nombre ASC
                ";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['projectId' => $projectId]);
            } else {
                $stmt = $this->db->query($query);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            app_log("Error al obtener software disponible: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener el software disponible");
        }
    }

    private function assignSoftware($projectId, $softwareIds, $userId) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->relationTable} (proyecto_id, software_id, asignado_por)
            VALUES (?, ?, ?)
        ");

        foreach ($softwareIds as $softwareId) {
            $stmt->execute([$projectId, $softwareId, $userId]);
        }
    }

    private function validateProjectData($data) {
        $errors = [];

        if (empty($data['codigo'])) {
            $errors[] = "El código del proyecto es requerido";
        } elseif (!preg_match('/^[A-Z0-9]{3,10}$/', $data['codigo'])) {
            $errors[] = "El código debe contener entre 3 y 10 caracteres alfanuméricos en mayúsculas";
        }

        if (empty($data['nombre'])) {
            $errors[] = "El nombre del proyecto es requerido";
        }

        if (!empty($errors)) {
            throw new Exception(implode(". ", $errors));
        }
    }

    public function codeExists($code, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE codigo = ?";
        $params = [$code];

        if ($excludeId !== null) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}