<?php
namespace Softhub\Models;
// este archivo es src/Models/UserModel.php
use PDO;
use Exception;

class UserModel {
    private $db;
    private $table = 'usuarios';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findAll($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;

            // Consulta principal para obtener usuarios
            $query = "SELECT id, nombre_usuario, rol, activo, ultimo_login, creado_en
                     FROM {$this->table}
                     ORDER BY creado_en DESC
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            // Consulta para contar total de registros
            $countStmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
            $totalUsers = $countStmt->fetchColumn();

            return [
                'users' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $totalUsers,
                'pages' => ceil($totalUsers / $limit)
            ];
        } catch (Exception $e) {
            app_log("Error al obtener usuarios: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener la lista de usuarios");
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nombre_usuario, rol, activo FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            app_log("Error al obtener usuario {$id}: " . $e->getMessage(), 'ERROR');
            throw new Exception("Usuario no encontrado");
        }
    }

    public function validateCredentials($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT id, nombre_usuario, contraseña, rol, activo FROM {$this->table} WHERE nombre_usuario = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['contraseña'])) {
                return $user;
            }
            return false;
        } catch (Exception $e) {
            app_log("Error en validación de credenciales: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al validar credenciales");
        }
    }

    public function create($userData) {
        try {
            $this->validateUserData($userData);

            if ($this->usernameExists($userData['nombre_usuario'])) {
                throw new Exception("El nombre de usuario ya está en uso");
            }

            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (nombre_usuario, contraseña, rol)
                 VALUES (:username, :password, :role)"
            );

            $hashedPassword = password_hash($userData['contraseña'], PASSWORD_DEFAULT);

            $success = $stmt->execute([
                'username' => $userData['nombre_usuario'],
                'password' => $hashedPassword,
                'role' => $userData['rol'] ?? 'usuario'
            ]);

            if ($success) {
                $userId = $this->db->lastInsertId();
                app_log("Usuario creado: {$userData['nombre_usuario']}", 'INFO');
                return $userId;
            }

            throw new Exception("Error al crear el usuario");
        } catch (Exception $e) {
            app_log("Error en creación de usuario: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function update($id, $userData) {
        try {
            // Validar que el usuario existe
            $currentUser = $this->findById($id);
            if (!$currentUser) {
                throw new Exception("Usuario no encontrado");
            }

            // Prevenir auto-desactivación de administradores
            if (isset($userData['activo']) &&
                $currentUser['rol'] === 'administrador' &&
                !$userData['activo']) {
                throw new Exception("No se puede desactivar una cuenta de administrador");
            }

            // Construir query dinámica basada en campos proporcionados
            $updateFields = [];
            $params = ['id' => $id];

            if (isset($userData['nombre_usuario'])) {
                if ($userData['nombre_usuario'] !== $currentUser['nombre_usuario'] &&
                    $this->usernameExists($userData['nombre_usuario'])) {
                    throw new Exception("El nombre de usuario ya está en uso");
                }
                $updateFields[] = "nombre_usuario = :username";
                $params['username'] = $userData['nombre_usuario'];
            }

            if (!empty($userData['contraseña'])) {
                $updateFields[] = "contraseña = :password";
                $params['password'] = password_hash($userData['contraseña'], PASSWORD_DEFAULT);
            }

            if (isset($userData['rol'])) {
                $updateFields[] = "rol = :role";
                $params['role'] = $userData['rol'];
            }

            if (isset($userData['activo'])) {
                $updateFields[] = "activo = :active";
                $params['active'] = $userData['activo'];
            }

            if (empty($updateFields)) {
                return true; // No hay cambios para realizar
            }

            $query = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute($params);

            if ($success) {
                app_log("Usuario actualizado: ID {$id}", 'INFO');
                return true;
            }

            throw new Exception("Error al actualizar el usuario");
        } catch (Exception $e) {
            app_log("Error en actualización de usuario: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function delete($id) {
        try {
            // Verificar si es el último administrador
            $user = $this->findById($id);
            if ($user['rol'] === 'administrador') {
                $adminCount = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE rol = 'administrador'")->fetchColumn();
                if ($adminCount <= 1) {
                    throw new Exception("No se puede eliminar el último administrador del sistema");
                }
            }

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $success = $stmt->execute([$id]);

            if ($success) {
                app_log("Usuario eliminado: ID {$id}", 'INFO');
                return true;
            }

            throw new Exception("Error al eliminar el usuario");
        } catch (Exception $e) {
            app_log("Error en eliminación de usuario: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    public function updateLastLogin($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET ultimo_login = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            app_log("Error al actualizar último login: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al actualizar último login");
        }
    }

    public function countUsers($onlyActive = false) {
        try {
            $query = "SELECT COUNT(*) FROM {$this->table}";
            if ($onlyActive) {
                $query .= " WHERE activo = 1";
            }
            return $this->db->query($query)->fetchColumn();
        } catch (Exception $e) {
            app_log("Error al contar usuarios: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener el conteo de usuarios");
        }
    }

    public function countUsersByRole($role) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE rol = ?");
            $stmt->execute([$role]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            app_log("Error al contar usuarios por rol: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener el conteo de usuarios por rol");
        }
    }

    public function getRecentLogins($limit = 5) {
        try {
            $query = "SELECT u.nombre_usuario, al.timestamp, al.ip_address
                     FROM auth_logs al
                     JOIN usuarios u ON al.usuario_id = u.id
                     WHERE al.accion = 'login'
                     ORDER BY al.timestamp DESC
                     LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            app_log("Error al obtener logins recientes: " . $e->getMessage(), 'ERROR');
            throw new Exception("Error al obtener el historial de logins");
        }
    }

    public function logAuthAction($userId, $action, $username = null) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO auth_logs (usuario_id, nombre_usuario, accion, ip_address, user_agent)
                 VALUES (?, ?, ?, ?, ?)"
            );

            return $stmt->execute([
                $userId,
                $username ?? $this->getUsernameById($userId),
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            app_log("Error al registrar acción de autenticación: " . $e->getMessage(), 'ERROR');
            // No lanzamos excepción aquí para no interrumpir el flujo principal
            return false;
        }
    }

    private function validateUserData($userData) {
        $errors = [];

        if (empty($userData['nombre_usuario'])) {
            $errors[] = "El nombre de usuario es requerido";
        } elseif (strlen($userData['nombre_usuario']) < 3) {
            $errors[] = "El nombre de usuario debe tener al menos 3 caracteres";
        }

        if (isset($userData['contraseña']) && strlen($userData['contraseña']) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }

        if (!empty($userData['rol']) && !in_array($userData['rol'], ['usuario', 'administrador'])) {
            $errors[] = "Rol no válido";
        }

        if (!empty($errors)) {
            throw new Exception(implode(". ", $errors));
        }
    }

    private function usernameExists($username, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE nombre_usuario = ?";
        $params = [$username];

        if ($excludeId !== null) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    private function getUsernameById($userId) {
        if (!$userId) return null;

        $stmt = $this->db->prepare("SELECT nombre_usuario FROM {$this->table} WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
}