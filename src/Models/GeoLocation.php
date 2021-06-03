<?php

class GeoLocation extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('geo_locations', 'geo_location_id', $connection);
    }

    public function getById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT geo_location_id, geo_name, last_geo_name FROM geo_location_view WHERE geo_location_id LIKE :geo_location_id LIMIT 1");
            $stmt->bindValue(':geo_location_id', $id);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function searchLocationLastLevel($search){
        try {
            $stmt = $this->db->prepare("SELECT geo_location_id, geo_name FROM geo_location_view WHERE geo_name LIKE :search");
            $stmt->bindValue(':search', '%' . $search . '%');
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }
}
