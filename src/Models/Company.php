<?php

class Company extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('companies', 'company_id', $connection);
    }

    public function getByIdPlan(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT com.*, plan.description AS app_plan_description, appPInter.price AS app_plan_price, appPayInter.date_interval AS app_plan_date_interval
                                            FROM companies AS com
                                            INNER JOIN app_plans AS plan ON com.app_plan_id = plan.app_plan_id
                                            INNER JOIN app_plan_intervals AS appPInter ON com.app_payment_interval_id = appPInter.app_payment_interval_id AND com.app_plan_id = appPInter.app_plan_id
                                            INNER JOIN app_payment_intervals AS appPayInter ON com.app_plan_id = appPayInter.app_payment_interval_id
                                            WHERE company_id = :company_id LIMIT 1");
            $stmt->bindParam(":company_id", $id);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function paginate(int $page, int $limit = 20,  string $search = '')
    {
        try {
            $offset = ($page - 1) * $limit;
            $totalRows = $this->db->query("SELECT COUNT(*) FROM companies as com WHERE com.document_number LIKE '%{$search}%'")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT com.*
                                                FROM companies as com
                                                WHERE com.document_number LIKE :document_number
                                                LIMIT $offset, $limit");
            $stmt->bindValue(':document_number', '%' . $search . '%');
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

    public function insert(array $company, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO companies (document_number, social_reason, commercial_reason, representative, phone, email, fiscal_address,
                                                            telephone, url_web, app_plan_id, contract_date_of_issue, app_payment_interval_id, created_at, created_user_id)
                                                    VALUES (:document_number, :social_reason, :commercial_reason, :representative, :phone, :email, :fiscal_address,
                                                            :telephone, :url_web, :app_plan_id, :contract_date_of_issue, :app_payment_interval_id, :created_at, :created_user_id)');

            $stmt->bindParam(':document_number', $company['documentNumber']);
            $stmt->bindParam(':social_reason', $company['socialReason']);
            $stmt->bindParam(':commercial_reason', $company['commercialReason']);
            $stmt->bindParam(':representative', $company['representative']);
            $stmt->bindParam(':phone', $company['phone']);
            $stmt->bindParam(':email', $company['email']);
            $stmt->bindParam(':fiscal_address', $company['fiscalAddress']);
            $stmt->bindParam(':telephone', $company['telephone']);
            $stmt->bindParam(':url_web', $company['urlWeb']);
            $stmt->bindParam(':app_plan_id', $company['appPlanId']);
            $stmt->bindParam(':contract_date_of_issue', $currentDate);
            $stmt->bindParam(':app_payment_interval_id', $company['appPaymentIntervalId']);

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
