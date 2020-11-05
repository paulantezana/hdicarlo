<?php


class Country extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct("countries","country_id",$db);
    }

    public function searchLocationById(int $countryId, string $search)
    {
        try {
            $stmt = $this->db->prepare("SELECT CONCAT(country_id, '_', geo_level_1_id, '_', geo_level_2_id, '_', geo_level_3_id) as geo_id,  geo_name
                                        FROM geo_search WHERE country_id = :country_id AND geo_name LIKE :geo_name LIMIT 10");
            $stmt->bindParam(':country_id', $countryId);
            $stmt->bindValue(':geo_name', '%' . $search . '%');
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function listGeoLevel1($countryId){
        try {
            $stmt = $this->db->prepare("SELECT geo_level_1_id, name FROM geo_level_1 WHERE country_id = :country_id ORDER BY name");
            $stmt->bindParam(':country_id', $countryId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function listGeoLevel2ByLeve1($geoLevel1Id){
        try {
            $stmt = $this->db->prepare("SELECT geo_level_2_id, name FROM geo_level_2 WHERE geo_level_1_id = :geo_level_1_id ORDER BY name");
            $stmt->bindParam(':geo_level_1_id', $geoLevel1Id);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function listGeoLevel3ByLeve2($geoLevel2d){
        try {
            $stmt = $this->db->prepare("SELECT geo_level_3_id, name FROM geo_level_3 WHERE geo_level_2_id = :geo_level_2_id ORDER BY name");
            $stmt->bindParam(':geo_level_2_id', $geoLevel2d);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }
}
