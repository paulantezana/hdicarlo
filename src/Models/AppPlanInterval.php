<?php

class AppPlanInterval extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('app_plan_intervals', 'app_plan_interval_id', $connection);
    }

    public function getAllByAppPlanId($appPlanId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM app_plan_intervals WHERE app_plan_id = :app_plan_id AND state = 1');
            $stmt->bindParam(':app_plan_id', $appPlanId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $appPlanInterval, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO app_plan_intervals (app_plan_id, app_payment_interval_id, price, created_at, created_user_id)
                                                    VALUES (:app_plan_id, :app_payment_interval_id, :price, :created_at, :created_user_id)');

            $stmt->bindParam(':app_plan_id', $appPlanInterval['appPlanId']);
            $stmt->bindParam(':app_payment_interval_id', $appPlanInterval['appPaymentIntervalId']);
            $stmt->bindParam(':price', $appPlanInterval['price']);

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
