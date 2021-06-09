<?php

class Order extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('orders', 'order_id', $connection);
    }
    
    public function insert(array $customer, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO orders (date_of_issue, lat_long, picture_path, date_of_delivery, observation, total, company_id, exhibitor_id, user_id, created_at, created_user_id)
                                                    VALUES (:date_of_issue, :lat_long, :picture_path, :date_of_delivery, :observation, :total, :company_id, :exhibitor_id, :user_id, :created_at, :created_user_id)');

            $stmt->bindParam(':date_of_issue', $currentDate);
            $stmt->bindParam(':lat_long', $customer['latLong']);
            $stmt->bindValue(':picture_path', '');
            $stmt->bindParam(':date_of_delivery', $customer['dateOfDelivery']);
            $stmt->bindParam(':observation', $customer['observation']);
            $stmt->bindParam(':total', $customer['total']);

            $stmt->bindParam(':company_id', $customer['companyId']);
            $stmt->bindParam(':exhibitor_id', $customer['exhibitorId']);
            $stmt->bindParam(':user_id', $customer['userId']);

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

    public function paginateByCompanyId(int $companyId, int $page, int $limit = 10, string $search = '')
    {
        try {
            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM orders WHERE company_id = '{$companyId}'")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT ord.*, ex.code as exhibitor_code, cus.social_reason AS customer_social_reason
                                        FROM orders AS ord 
                                        INNER JOIN exhibitors AS ex ON ord.exhibitor_id = ex.exhibitor_id
                                        INNER JOIN customers AS cus ON ex.customer_id = cus.customer_id
                                        WHERE ord.company_id = :company_id
                                        LIMIT $offset, $limit");
            // $stmt->bindValue(':search', '%' . $search . '%');
            $stmt->bindValue(':company_id', $companyId);
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

    public function reportChart($filter){
        $stmt = $this->db->prepare("SELECT DATE(created_at) as created_at_query, COUNT(order_id) as count  FROM orders
                                    WHERE created_at BETWEEN :start_date AND :end_date
                                    GROUP BY created_at_query");

        $stmt->bindParam(':start_date', $filter['startDate']);
        $stmt->bindParam(':end_date', $filter['endDate']);
    
        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }

        return $stmt->fetchAll();
    }
}