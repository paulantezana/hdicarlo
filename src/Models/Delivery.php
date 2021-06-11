<?php

class Delivery extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('deliveries', 'delivery_id', $connection);
    }

    public function getIncomeYearByCompanyId(int $companyId, int $year)
    {
        try {
            
            $stmt = $this->db->prepare("SELECT ex.code as exhibitor_code, cus.social_reason AS customer_social_reason,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 1 THEN del.total ELSE 0 END) AS del_1,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 2 THEN del.total ELSE 0 END) AS del_2,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 3 THEN del.total ELSE 0 END) AS del_3,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 4 THEN del.total ELSE 0 END) AS del_4,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 5 THEN del.total ELSE 0 END) AS del_5,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 6 THEN del.total ELSE 0 END) AS del_6,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 7 THEN del.total ELSE 0 END) AS del_7,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 8 THEN del.total ELSE 0 END) AS del_8,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 9 THEN del.total ELSE 0 END) AS del_9,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 10 THEN del.total ELSE 0 END) AS del_10,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 11 THEN del.total ELSE 0 END) AS del_11,
                                            SUM(CASE WHEN MONTH(del.date_of_issue) = 12 THEN del.total ELSE 0 END) AS del_12,
                                            SUM(del.total) AS del_total
                                        FROM deliveries AS del
                                        INNER JOIN exhibitors AS ex ON del.exhibitor_id = ex.exhibitor_id
                                        INNER JOIN customers AS cus ON ex.customer_id = cus.customer_id
                                        WHERE del.company_id = :company_id AND del.canceled = 0 AND YEAR(del.date_of_issue) = :year_date GROUP BY del.exhibitor_id");
            $stmt->bindParam(":company_id", $companyId);
            $stmt->bindParam(":year_date", $year);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }
    
    public function insert(array $delivery, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO deliveries (date_of_issue, lat_long, picture_path, date_of_delivery, observation, total, company_id, exhibitor_id, user_id, created_at, created_user_id)
                                                    VALUES (:date_of_issue, :lat_long, :picture_path, :date_of_delivery, :observation, :total, :company_id, :exhibitor_id, :user_id, :created_at, :created_user_id)');

            $stmt->bindParam(':date_of_issue', $currentDate);
            $stmt->bindParam(':lat_long', $delivery['latLong']);
            $stmt->bindValue(':picture_path', '');
            $stmt->bindParam(':date_of_delivery', $delivery['dateOfDelivery']);
            $stmt->bindParam(':observation', $delivery['observation']);
            $stmt->bindParam(':total', $delivery['total']);

            $stmt->bindParam(':company_id', $delivery['companyId']);
            $stmt->bindParam(':exhibitor_id', $delivery['exhibitorId']);
            $stmt->bindParam(':user_id', $delivery['userId']);

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

    public function paginateByCompanyId(int $companyId, int $page, int $limit = 10, string $search = '', array $filter = [])
    {
        try {
            $customQuery = '';
            if($filter['filterByDate'] == 1){
                $customQuery = " AND  YEAR(del.date_of_issue) = '{$filter['filterYear']}'";
            } else if($filter['filterByDate'] == 2){
                $customQuery = " AND  DATE_FORMAT(del.date_of_issue,'%Y-%m') = '{$filter['filterMonth']}'";
            } else if($filter['filterByDate'] == 3){
                $customQuery = " AND  DATE(del.date_of_issue) = '{$filter['filterDay']}'";
            }

            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM deliveries AS del WHERE del.company_id = {$companyId} {$customQuery}")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT del.*, ex.code as exhibitor_code, cus.social_reason AS customer_social_reason, user.user_name
                                        FROM deliveries AS del 
                                        INNER JOIN exhibitors AS ex ON del.exhibitor_id = ex.exhibitor_id
                                        INNER JOIN customers AS cus ON ex.customer_id = cus.customer_id
                                        INNER JOIN users AS user ON del.user_id = user.user_id
                                        WHERE del.company_id = :company_id {$customQuery}
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
        $stmt = $this->db->prepare("SELECT DATE(created_at) as created_at_query, COUNT(delivery_id) as count  FROM deliveries
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