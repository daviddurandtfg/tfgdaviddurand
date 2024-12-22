<?php
namespace Softhub\Models;
// este archivo es src/Models/SoftwareModel.php
use PDO;
use Exception;

class SoftwareModel {
    private $db;
    private $table = 'software_catalog';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllSoftware($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;

            // Consulta principal para obtener software
            $query = "SELECT id, nombre, descripcion, categoria, version, activo, 
                            creado_en, actualizado_en
                     FROM {$this->table}
                     ORDER BY nombre ASC
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            // Consulta para contar total de registros
            $countStmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
            $totalSoftware = $countStmt->fetchColumn();

            return [
                'software' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $totalSoftware,
                'pages' => ceil($totalSoftware / $limit)
            ];
        } catch (Exception $e) {
            app_log("Error al obtener lista de software: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener la lista de software");
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nombre, descripcion, categoria, version, activo 
                FROM {$this->table} 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            app_log("Error al obtener software {$id}: " . $e->getMessage(), 'ERROR');
            throw new Exception("Software no encontrado");
        }
    }

    public function createSoftware($data) {
        try {
            $this->validateSoftwareData($data);

            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (nombre, descripcion, categoria, version, activo)
                 VALUES (:nombre, :descripcion, :categoria, :version, 1)"
            );

            $success = $stmt->execute([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'categoria' => $data['categoria'],
                'version' => $data['version']
            ]);

            if ($success) {
                $softwareId = $this->db->lastInsertId();
                app_log("Software creado: {$data['nombre']}", 'INFO');
                return $softwareId;
            }

            throw new Exception("Error al crear el software");
        } catch (Exception $e) {
            app_log("Error en creación de software: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function updateSoftware($id, $data) {
        try {
            $this->validateSoftwareData($data);

            $stmt = $this->db->prepare(
                "UPDATE {$this->table} 
                 SET nombre = :nombre,
                     descripcion = :descripcion,
                     categoria = :categoria,
                     version = :version,
                     actualizado_en = CURRENT_TIMESTAMP
                 WHERE id = :id"
            );

            $success = $stmt->execute([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'categoria' => $data['categoria'],
                'version' => $data['version'],
                'id' => $id
            ]);

            if ($success) {
                app_log("Software actualizado: ID {$id}", 'INFO');
                return true;
            }

            throw new Exception("Error al actualizar el software");
        } catch (Exception $e) {
            app_log("Error en actualización de software: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function toggleStatus($id) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE {$this->table}
                 SET activo = NOT activo,
                     actualizado_en = CURRENT_TIMESTAMP
                 WHERE id = ?"
            );

            $success = $stmt->execute([$id]);

            if ($success) {
                app_log("Estado de software actualizado: ID {$id}", 'INFO');
                return true;
            }

            throw new Exception("Error al cambiar el estado del software");
        } catch (Exception $e) {
            app_log("Error al cambiar estado del software: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    private function validateSoftwareData($data) {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors[] = "El nombre del software es requerido";
        }

        if (empty($data['version'])) {
            $errors[] = "La versión del software es requerida";
        }

        if (empty($data['categoria'])) {
            $errors[] = "La categoría del software es requerida";
        }

        if (!empty($errors)) {
            throw new Exception(implode(". ", $errors));
        }
    }
}