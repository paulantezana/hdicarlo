<?php

class User extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('users', 'user_id', $connection);
    }

    public function getAllByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE company_id = :company_id');
            $stmt->bindParam(":company_id", $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function getByIdShort(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id, user_role_id, user_name, full_name, email, avatar, full_name  FROM users WHERE user_id = :user_id LIMIT 1");
            $stmt->bindParam(":user_id", $id);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function countByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE state = 1 AND company_id = :company_id");
            $stmt->bindParam(":company_id", $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetch();
            if($data == false){
                return 0;
            }

            return $data['total'];
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function paginateByCompanyId(int $companyId, int $page, int $limit, string $search)
    {
        try {
            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM users WHERE user_name LIKE '%{$search}%' AND company_id = '{$companyId}'")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT users.*, ur.description as user_roles,
                                        ur.state as user_role_state
                                        FROM users
                                        INNER JOIN user_roles ur on users.user_role_id = ur.user_role_id
                                        WHERE users.user_name LIKE :search AND users.company_id = :company_id LIMIT $offset, $limit");
            $stmt->bindValue(':search', '%' . $search . '%');
            $stmt->bindParam(":company_id", $companyId);

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetchAll();

            $paginate = [
                'current' => $page,
                'pages' => $totalPages,
                'limit' => $limit,
                'data' => $data,
                'total' => $totalRows,
            ];
            return $paginate;
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function officeLogin(string $user)
    {
        try {
            $stmt = $this->db->prepare('SELECT users.user_id, users.user_name, users.full_name, users.email, users.avatar, users.company_id, users.user_role_id, users.state, users.password FROM users 
                                        INNER JOIN user_roles AS rol ON users.user_role_id = rol.user_role_id AND rol.state = 1
                                        WHERE users.email = :email AND users.state = 1 LIMIT 1');
            $stmt->bindParam(':email', $user);

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $dataUser = $stmt->fetch();

            if ($dataUser == false) {
                $stmt = $this->db->prepare('SELECT users.user_id, users.user_name, users.full_name, users.email, users.avatar, users.company_id, users.user_role_id, users.state, users.password FROM users 
                                            INNER JOIN user_roles AS rol ON users.user_role_id = rol.user_role_id AND rol.state = 1
                                            WHERE users.user_name = :user_name AND users.state = 1 LIMIT 1');
                $stmt->bindParam(':user_name', $user);

                if (!$stmt->execute()) {
                    throw new Exception($stmt->errorInfo()[2]);
                }
                $dataUser = $stmt->fetch();

                if ($dataUser == false) {
                    throw new Exception('Usuario no encontrado.');
                }
            }

            if ($dataUser['state'] == '0') {
                throw new Exception('Usted no esta autorizado para ingresar al sistema.');
            }

            return $dataUser;
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function innerLogin(string $user)
    {
        try {
            $stmt = $this->db->prepare('SELECT users.user_id, users.user_name, users.password, users.full_name, users.email, users.avatar, users.is_inner, users.state, users.user_role_id FROM users 
                                        WHERE users.email = :email AND users.state = 1 LIMIT 1');
            $stmt->bindParam(':email', $user);

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $dataUser = $stmt->fetch();

            if ($dataUser == false) {
                $stmt = $this->db->prepare('SELECT users.user_id, users.user_name, users.password, users.full_name, users.email, users.avatar, users.is_inner, users.state, users.user_role_id FROM users 
                                            WHERE users.user_name = :user_name AND users.state = 1 LIMIT 1');
                $stmt->bindParam(':user_name', $user);

                if (!$stmt->execute()) {
                    throw new Exception($stmt->errorInfo()[2]);
                }
                $dataUser = $stmt->fetch();

                if ($dataUser == false) {
                    throw new Exception('El usuario o contraseñas es icorrecta');
                }
            }

            if ($dataUser['state'] == '0') {
                throw new Exception('Usted no esta autorizado para ingresar al sistema.');
            }

            if($dataUser['is_inner'] == '0'){
                throw new Exception('Este usuario no esta autorizado');
            }

            return $dataUser;
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $user, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO users (user_name, email, password, full_name, last_name, identity_document_id, identity_document_number, user_role_id, company_id, created_at, created_user_id)
                                                    VALUES (:user_name, :email, :password, :full_name, :last_name, :identity_document_id, :identity_document_number, :user_role_id, :company_id, :created_at, :created_user_id)');

            $stmt->bindParam(':user_name', $user['userName']);
            $stmt->bindParam(':email', $user['email']);
            $stmt->bindValue(':password', $user['password']);
            $stmt->bindParam(':full_name', $user['fullName']);
            $stmt->bindParam(':last_name', $user['lastName']);
            $stmt->bindParam(':identity_document_id', $user['identityDocumentId']);
            $stmt->bindParam(':identity_document_number', $user['identityDocumentNumber']);
            $stmt->bindParam(':user_role_id', $user['userRoleId']);
            $stmt->bindParam(':company_id', $user['companyId']);

            $stmt->bindParam(':created_at', $currentDate);
            $stmt->bindParam(':created_user_id', $userId);

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }
}
