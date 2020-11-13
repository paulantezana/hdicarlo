<?php

class Exhibitor extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('exhibitors', 'exhibitor_id', $connection);
    }

    public function count()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM exhibitors WHERE state = 1");
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

    public function getById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT exh.*,
                                                CONCAT(exh.country_id, '_', exh.geo_level_1_id, '_', exh.geo_level_2_id, '_', exh.geo_level_3_id) as geo_id,
                                                CONCAT(level1.name, '-', level2.name, '-', level3.name) as geo_name,
                                                cus.social_reason as customer_social_reason
                                            FROM exhibitors as exh
                                            INNER JOIN customers AS cus ON exh.customer_id = cus.customer_id
                                            LEFT JOIN countries AS coun ON exh.country_id = coun.country_id
                                            LEFT JOIN geo_level_1 AS level1 ON exh.geo_level_1_id = level1.geo_level_1_id
                                            LEFT JOIN geo_level_2 AS level2 ON exh.geo_level_2_id = level2.geo_level_2_id
                                            LEFT JOIN geo_level_3 AS level3 ON exh.geo_level_3_id = level3.geo_level_3_id
                                            WHERE exh.exhibitor_id = :exhibitor_id LIMIT 1");
            $stmt->bindParam(":exhibitor_id", $id);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function getByCode(string $code)
    {
        try {
            $stmt = $this->db->prepare("SELECT exh.*,
                                                CONCAT(exh.country_id, '_', exh.geo_level_1_id, '_', exh.geo_level_2_id, '_', exh.geo_level_3_id) as geo_id,
                                                CONCAT(level1.name, '-', level2.name, '-', level3.name) as geo_name,
                                                cus.social_reason as customer_social_reason
                                            FROM exhibitors as exh
                                            INNER JOIN customers AS cus ON exh.customer_id = cus.customer_id
                                            LEFT JOIN countries AS coun ON exh.country_id = coun.country_id
                                            LEFT JOIN geo_level_1 AS level1 ON exh.geo_level_1_id = level1.geo_level_1_id
                                            LEFT JOIN geo_level_2 AS level2 ON exh.geo_level_2_id = level2.geo_level_2_id
                                            LEFT JOIN geo_level_3 AS level3 ON exh.geo_level_3_id = level3.geo_level_3_id
                                            WHERE exh.code = :code LIMIT 1");
            $stmt->bindParam(":code", $code);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function monitoring(string $currentDate,int $days)
    {
        try {
            $endDate = strtotime ('+'.$days.' day' , strtotime ($currentDate));
			$endDate = date ('Y-m-d',$endDate);
 
            $stmt = $this->db->prepare("SELECT del.*, us.full_name as user_full_name, us.user_name FROM deliveries AS del 
                                        INNER JOIN users AS us ON del.user_id = us.user_id
                                        WHERE del.date_of_delivery BETWEEN :startDate AND :endDate");
            $stmt->bindParam(':startDate', $currentDate);
            $stmt->bindParam(':endDate', $endDate);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function paginate(int $page, int $limit = 10, string $search = '')
    {
        try {
            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM exhibitors WHERE code LIKE '%{$search}%' AND state = 1")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT exh.*,
                                                cus.document_number as customer_document_number,
                                                cus.social_reason as customer_social_reason,
                                                siz.description as size_description
                                        FROM exhibitors as exh
                                        INNER JOIN customers AS cus ON exh.customer_id = cus.customer_id
                                        INNER JOIN sizes AS siz ON exh.size_id = siz.size_id
                                        WHERE exh.code LIKE :search AND exh.state = 1 LIMIT $offset, $limit");
            $stmt->bindValue(':search', '%' . $search . '%');

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetchAll();

            $paginate = [
                'current' => $page,
                'pages' => $totalPages,
                'limit' => $limit,
                'data' => $data,
            ];
            return $paginate;
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $exhibitor, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO exhibitors (code, address, size_id, country_id, geo_level_1_id, geo_level_2_id, geo_level_3_id, lat_long, customer_id, created_at, created_user_id)
                                                    VALUES (:code, :address, :size_id, :country_id, :geo_level_1_id, :geo_level_2_id, :geo_level_3_id, :lat_long, :customer_id, :created_at, :created_user_id)');

            $stmt->bindParam(':code', $exhibitor['code']);
            $stmt->bindParam(':address', $exhibitor['address']);
            $stmt->bindParam(':size_id', $exhibitor['sizeId']);
            $stmt->bindParam(':country_id', $exhibitor['countryId']);
            $stmt->bindParam(':geo_level_1_id', $exhibitor['geoLevel1Id']);
            $stmt->bindParam(':geo_level_2_id', $exhibitor['geoLevel2Id']);
            $stmt->bindParam(':geo_level_3_id', $exhibitor['geoLevel3Id']);
            $stmt->bindParam(':lat_long', $exhibitor['latLong']);
            $stmt->bindParam(':customer_id', $exhibitor['customerId']);

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
