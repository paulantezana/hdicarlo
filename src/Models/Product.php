<?php

class Product extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('products', 'product_id', $connection);
    }

    public function countByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE state = 1 AND company_id = :company_id");
            $stmt->bindParam(':company_id', $companyId);
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

    public function getAllByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE state = 1 AND company_id = :company_id');
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function paginateByCompanyId(int $companyId, int $page, int $limit)
    {
        try {
            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM products WHERE state = 1 AND company_id = '{$companyId}'")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT * FROM products WHERE state = 1 AND company_id = :company_id LIMIT $offset, $limit");
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetchAll();

            return [
                'current' => $page,
                'pages' => $totalPages,
                'limit' => $limit,
                'data' => $data,
                'total' => $totalRows,
            ];
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $product, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO products (title, bar_code, price, company_id, created_at, created_user_id)
                                                    VALUES (:title, :bar_code, :price, :company_id, :created_at, :created_user_id)');

            $stmt->bindParam(':title', $product['title']);
            $stmt->bindParam(':bar_code', $product['barCode']);
            $stmt->bindParam(':price', $product['price']);
            $stmt->bindParam(':company_id', $product['companyId']);

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
