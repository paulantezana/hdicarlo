<?php

class Customer extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('customers', 'customer_id', $connection);
    }

    public function countByCompanyId($companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM customers WHERE company_id = :company_id AND state = 1");
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

    public function searchBySocialReason(string $search, int $companyId)
    {
        try {
            $stmt = $this->db->prepare("SELECT customer_id, social_reason FROM customers WHERE company_id = :company_id AND social_reason LIKE :social_reason LIMIT 10");
            $stmt->bindValue(":company_id", $companyId);
            $stmt->bindValue(":social_reason", '%' . $search . '%');
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
            $totalRows = $this->db->query("SELECT COUNT(*) FROM customers WHERE company_id = '{$companyId}' AND social_reason LIKE '%{$search}%' AND state = 1")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT cus.*, tdt.description as identity_document_description FROM customers as cus
                                        INNER JOIN identity_document_types tdt on cus.identity_document_id = tdt.identity_document_id
                                        WHERE company_id = :company_id AND cus.social_reason LIKE :search AND cus.state = 1 LIMIT $offset, $limit");
            $stmt->bindValue(':company_id', $companyId);
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
                'total' => $totalRows,
            ];
            return $paginate;
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $customer, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO customers (document_number, identity_document_id, social_reason, commercial_reason, fiscal_address, email, telephone, company_id, created_at, created_user_id)
                                                    VALUES (:document_number, :identity_document_id, :social_reason, :commercial_reason, :fiscal_address, :email, :telephone, :company_id, :created_at, :created_user_id)');

            $stmt->bindValue(':document_number', $customer['documentNumber']);
            $stmt->bindValue(':identity_document_id', $customer['identityDocumentId']);
            $stmt->bindValue(':social_reason', $customer['socialReason']);
            $stmt->bindValue(':commercial_reason', $customer['commercialReason']);
            $stmt->bindParam(':fiscal_address', $customer['fiscalAddress']);
            $stmt->bindParam(':email', $customer['email']);
            $stmt->bindParam(':telephone', $customer['telephone']);
            $stmt->bindParam(':company_id', $customer['companyId']);

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
