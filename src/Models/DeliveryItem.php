<?php

class DeliveryItem extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('delivery_items', 'delivery_item_id', $connection);
    }

    public function getAllByDeliveryId(int $deliveryId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM delivery_items as item WHERE item.state = 1 AND item.delivery_id = :delivery_id');
            $stmt->bindValue(':delivery_id', $deliveryId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $deliveryItem, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO delivery_items (description, observation, quantity, unit_price, product_id, total, delivery_id, created_at, created_user_id)
                                                    VALUES (:description, :observation, :quantity, :unit_price, :product_id, :total, :delivery_id, :created_at, :created_user_id)');
                                                                
            $stmt->bindParam(':description', $deliveryItem['description']);
            $stmt->bindParam(':observation', $deliveryItem['observation']);
            $stmt->bindParam(':quantity', $deliveryItem['quantity']);
            $stmt->bindParam(':unit_price', $deliveryItem['unitPrice']);
            $stmt->bindParam(':product_id', $deliveryItem['productId']);
            $stmt->bindParam(':total', $deliveryItem['total']);
            $stmt->bindParam(':delivery_id', $deliveryItem['deliveryId']);

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
