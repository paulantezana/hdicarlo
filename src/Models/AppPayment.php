<?php

class AppPayment extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('app_payments', 'app_payment_id', $connection);
    }

    public function getLasNumberByCompanyId(int $companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT IFNULL(MAX(number),0) + 1 as max_number FROM app_payments WHERE company_id = :company_id");
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetch();
            if ($data === false) {
                return 0;
            }

            return $data['max_number'];
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function getAllByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare('SELECT pay.*, user.user_name FROM app_payments AS pay INNER JOIN users AS user ON pay.user_id = user.user_id WHERE pay.company_id = :company_id');
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function paginateByCompanyId(int $companyId, int $page, int $limit = 20, string $search = '', int $contractId = 0, $searchStartDate = 0, $searchEndDate = 0)
    {
        try {
            $offset = ($page - 1) * $limit;
            $aditionalQuery = '';
            if ($contractId > 0) {
                $aditionalQuery = ' AND pay.contract_id = :contract_id ';
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) as total
                                        FROM app_payments AS pay
                                        INNER JOIN contracts AS con ON pay.contract_id = con.contract_id
                                        INNER JOIN customers AS cus ON con.customer_id = cus.customer_id
                                        INNER JOIN users as user ON pay.user_id = user.user_id
                                        WHERE (DATE(pay.datetime_of_issue) BETWEEN :start_date_of_issue AND :end_date_of_issue) 
                                        AND cus.social_reason LIKE '%{$search}%' AND pay.company_id = :company_id
                                        " . $aditionalQuery);
            $stmt->bindParam(":start_date_of_issue", $searchStartDate);
            $stmt->bindParam(":end_date_of_issue", $searchEndDate);
            $stmt->bindParam(':company_id', $companyId);
            if ($contractId > 0) {
                $stmt->bindParam(":contract_id", $contractId);
            }
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }

            $totalRows = $stmt->fetch();
            $totalPages = ceil($totalRows['total'] / $limit);


            $stmt = $this->db->prepare("SELECT pay.*,
                                            con.number AS contract_number,
                                            cus.document_number AS customer_document_number,
                                            cus.social_reason AS customer_social_reason,
                                            cus.fiscal_address AS customer_fiscal_address,
                                            cus.email AS customer_email,
                                            cus.telephone AS customer_telephone,
                                            user.user_name AS user_name
                                        FROM app_payments AS pay
                                        INNER JOIN contracts AS con ON pay.contract_id = con.contract_id
                                        INNER JOIN customers AS cus ON con.customer_id = cus.customer_id
                                        INNER JOIN users as user ON pay.user_id = user.user_id
                                        WHERE (DATE(pay.datetime_of_issue) BETWEEN :start_date_of_issue AND :end_date_of_issue) 
                                        AND cus.social_reason LIKE '%{$search}%' AND pay.company_id = :company_id
                                        ".$aditionalQuery." 
                                        ORDER BY pay.app_payment_id DESC
                                        LIMIT $offset, $limit");
            $stmt->bindParam(":start_date_of_issue", $searchStartDate);
            $stmt->bindParam(":end_date_of_issue", $searchEndDate);
            $stmt->bindParam(':company_id', $companyId);
            if ($contractId > 0) {
                $stmt->bindParam(":contract_id", $contractId);
            }
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetchAll();

            return [
                'current' => $page,
                'pages' => $totalPages,
                'limit' => $limit,
                'data' => $data,
                'total' => $totalRows['total'],
            ];
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function getIncome($year)
    {
        try {
            
            $stmt = $this->db->prepare("SELECT plan.description AS app_plan_description,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 1 THEN pay.total ELSE 0 END) AS pay_1,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 2 THEN pay.total ELSE 0 END) AS pay_2,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 3 THEN pay.total ELSE 0 END) AS pay_3,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 4 THEN pay.total ELSE 0 END) AS pay_4,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 5 THEN pay.total ELSE 0 END) AS pay_5,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 6 THEN pay.total ELSE 0 END) AS pay_6,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 7 THEN pay.total ELSE 0 END) AS pay_7,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 8 THEN pay.total ELSE 0 END) AS pay_8,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 9 THEN pay.total ELSE 0 END) AS pay_9,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 10 THEN pay.total ELSE 0 END) AS pay_10,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 11 THEN pay.total ELSE 0 END) AS pay_11,
                                            SUM(CASE WHEN MONTH(pay.date_time_of_issue) = 12 THEN pay.total ELSE 0 END) AS pay_12,
                                            SUM(pay.total) AS pay_total
                                        FROM app_payments AS pay
                                        INNER JOIN companies AS con ON pay.company_id = con.company_id
                                        INNER JOIN app_plans AS plan ON con.app_plan_id = plan.app_plan_id
                                        WHERE pay.canceled = 0 AND YEAR(pay.date_time_of_issue) = :year_date GROUP BY plan.app_plan_id");
            $stmt->bindParam(":year_date", $year);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function getByIdPrint(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT pay.*,
                                            cus.document_number as customer_document_number,
                                            cus.social_reason as customer_social_reason,
                                            cus.fiscal_address as customer_fiscal_address,
                                            cus.email as customer_email,
                                            cus.telephone as customer_telephone,
                                            user.user_name as user_name
                                        FROM app_payments AS pay
                                        INNER JOIN contracts AS con ON pay.contract_id = con.contract_id
                                        INNER JOIN customers AS cus ON con.customer_id = cus.customer_id
                                        INNER JOIN users as user ON pay.user_id = user.user_id
                                        WHERE app_payment_id= :app_payment_id LIMIT 1");
            $stmt->bindParam(":app_payment_id", $id);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function getLastPaymentByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT pay.* FROM app_payments as pay WHERE pay.company_id = :company_id AND pay.canceled = 0 AND pay.is_last = 1 LIMIT 1");
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function setLastByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE app_payments SET is_last = 1 WHERE app_payment_id = (
                                                    SELECT pay.app_payment_id FROM app_payments as pay
                                                    WHERE pay.company_id = :company_id AND pay.canceled = 0
                                                    ORDER BY pay.app_payment_id DESC LIMIT 1 
                                                )");
            $stmt->bindParam(':company_id', $companyId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function updateAllIsLastToFalse(int $companyId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('UPDATE app_payments SET is_last = 0, updated_at = :updated_at, updated_user_id = :updated_user_id WHERE company_id = :company_id');
            $stmt->bindParam(':company_id', $companyId);
            $stmt->bindParam(':updated_at', $currentDate);
            $stmt->bindParam(':updated_user_id', $userId);

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $appPayment, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO app_payments (description, date_time_of_issue, reference, number, is_last, from_date_time,
                                                                    to_date_time, total, user_id, company_id, created_at, created_user_id)
                                                            VALUES (:description, :date_time_of_issue, :reference, :number, :is_last, :from_date_time,
                                                                    :to_date_time, :total, :user_id, :company_id, :created_at, :created_user_id)');

            $stmt->bindParam(':description', $appPayment['description']);
            $stmt->bindParam(':date_time_of_issue', $currentDate);
            $stmt->bindParam(':reference', $appPayment['reference']);
            $stmt->bindValue(':number', $this->getLasNumberByCompanyId($appPayment['companyId']));
            $stmt->bindValue(':is_last', true, PDO::PARAM_BOOL);
            $stmt->bindParam(':from_date_time', $appPayment['fromDatetime']);
            $stmt->bindParam(':to_date_time', $appPayment['toDatetime']);
            $stmt->bindParam(':total', $appPayment['total']);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':company_id', $appPayment['companyId']);

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

    public function reportChartByCompanyId($companyId, $filter)
    {
        $stmt = $this->db->prepare("SELECT DATE(created_at) as created_at_query, COUNT(app_payment_id) as count FROM app_payments
                                    WHERE created_at BETWEEN :start_date AND :end_date AND canceled = 0 AND company_id = :company_id
                                    GROUP BY created_at_query");

        $stmt->bindParam(':start_date', $filter['startDate']);
        $stmt->bindParam(':end_date', $filter['endDate']);
        $stmt->bindParam(':company_id', $companyId);

        if (!$stmt->execute()) {
            throw new Exception($stmt->errorInfo()[2]);
        }

        return $stmt->fetchAll();
    }
}
