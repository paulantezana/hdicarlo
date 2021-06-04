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
            $stmt = $this->db->prepare('INSERT INTO orders (date_of_issue, lat_long, picture_path, date_of_delivery, observation, company_id, exhibitor_id, user_id, created_at, created_user_id)
                                                    VALUES (:date_of_issue, :lat_long, :picture_path, :date_of_delivery, :observation, :company_id, :exhibitor_id, :user_id, :created_at, :created_user_id)');

            $stmt->bindParam(':date_of_issue', $currentDate);
            $stmt->bindParam(':lat_long', $customer['latLong']);
            $stmt->bindValue(':picture_path', '');
            $stmt->bindParam(':date_of_delivery', $customer['dateOfDelivery']);
            $stmt->bindParam(':observation', $customer['observation']);

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