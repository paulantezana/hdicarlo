<?php

class Exhibitor extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('exhibitors', 'exhibitor_id', $connection);
    }

    public function countByCompanyId(int $companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM exhibitors WHERE company_id = :company_id AND state = 1");
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

    public function getById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT exh.*,
                                                geo.geo_name,
                                                cus.social_reason as customer_social_reason
                                            FROM exhibitors as exh
                                            INNER JOIN customers AS cus ON exh.customer_id = cus.customer_id
                                            INNER JOIN geo_location_view as geo ON exh.geo_location_id = geo.geo_location_id
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

    public function getByCodeAndCompanyId(int $companyId, string $code)
    {
        try {
            $stmt = $this->db->prepare("SELECT exh.*,
                                                geo.geo_name,
                                                cus.social_reason as customer_social_reason
                                            FROM exhibitors as exh
                                            INNER JOIN customers AS cus ON exh.customer_id = cus.customer_id
                                            INNER JOIN geo_location_view as geo ON exh.geo_location_id = geo.geo_location_id
                                            WHERE exh.company_id = :company_id AND  exh.code = :code LIMIT 1");
            $stmt->bindParam(":code", $code);
            $stmt->bindParam(":company_id", $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function monitoringByCompanyId(int $companyId, string $currentDate,int $days)
    {
        try {
            $endDate = strtotime ('+'.$days.' day' , strtotime ($currentDate));
			$endDate = date ('Y-m-d',$endDate);
 
            $stmt = $this->db->prepare("SELECT del.*, us.full_name as user_full_name, us.user_name FROM deliveries AS del 
                                        INNER JOIN users AS us ON del.user_id = us.user_id
                                        WHERE del.company_id = :company_id AND del.date_of_delivery BETWEEN :start_date AND :end_date");
            $stmt->bindParam(':start_date', $currentDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function paginateByCompanyId(int $companyId, int $page, int $limit = 10, string $search = '')
    {
        try {
            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM exhibitors WHERE company_id = '{$companyId}' AND code LIKE '%{$search}%' AND state = 1")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT exh.*,
                                                cus.document_number as customer_document_number,
                                                cus.social_reason as customer_social_reason,
                                                siz.description as size_description,
                                                geo.geo_name
                                        FROM exhibitors as exh
                                        INNER JOIN customers AS cus ON exh.customer_id = cus.customer_id
                                        INNER JOIN sizes AS siz ON exh.size_id = siz.size_id
                                        INNER JOIN geo_location_view as geo ON exh.geo_location_id = geo.geo_location_id
                                        WHERE exh.code LIKE :search AND exh.state = 1 AND exh.company_id = :company_id
                                        LIMIT $offset, $limit");
            $stmt->bindValue(':search', '%' . $search . '%');
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

    public function insert(array $exhibitor, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO exhibitors (code, address, size_id, geo_location_id, lat_long, customer_id, company_id, created_at, created_user_id)
                                                    VALUES (:code, :address, :size_id, :geo_location_id, :lat_long, :customer_id, :company_id, :created_at, :created_user_id)');
            $stmt->bindParam(':code', $exhibitor['code']);
            $stmt->bindParam(':address', $exhibitor['address']);
            $stmt->bindParam(':size_id', $exhibitor['sizeId']);
            $stmt->bindParam(':geo_location_id', $exhibitor['geoLocationId']);
            $stmt->bindParam(':lat_long', $exhibitor['latLong']);
            $stmt->bindParam(':customer_id', $exhibitor['customerId']);
            $stmt->bindParam(':company_id', $exhibitor['companyId']);

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
