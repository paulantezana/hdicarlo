<?php

class OrderItem extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('order_items', 'order_item_id', $connection);
    }

    public function getAllByOrderId(int $orderId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM order_items as item WHERE item.state = 1 AND item.order_id = :order_id');
            $stmt->bindValue(':order_id', $orderId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $orderItem, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare('INSERT INTO order_items (description, observation, quantity, unit_price, product_id, total, order_id, created_at, created_user_id)
                                                    VALUES (:description, :observation, :quantity, :unit_price, :product_id, :total, :order_id, :created_at, :created_user_id)');
                                                                
            $stmt->bindParam(':description', $orderItem['description']);
            $stmt->bindParam(':observation', $orderItem['observation']);
            $stmt->bindParam(':quantity', $orderItem['quantity']);
            $stmt->bindParam(':unit_price', $orderItem['unitPrice']);
            $stmt->bindParam(':product_id', $orderItem['productId']);
            $stmt->bindParam(':total', $orderItem['total']);
            $stmt->bindParam(':order_id', $orderItem['orderId']);

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
