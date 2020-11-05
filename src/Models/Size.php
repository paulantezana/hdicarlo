<?php

class Size extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('sizes', 'size_id', $connection);
    }
    public function getAll()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM sizes WHERE state = 1');
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }
}
