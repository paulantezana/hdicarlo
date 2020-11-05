<?php

require_once(MODEL_PATH . '/Country.php');

class CountryController  extends Controller
{
    private $connection;
    private $countryModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->countryModel = new Country($connection);
    }

    public function geoSearch()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $geoLocatios = $this->countryModel->searchLocationById(1, $body['search']);

            $res->result = $geoLocatios;
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }

        echo json_encode($res);
    }
    
    public function listGeoLevel1()
    {
        $res = new Result();
        try {
            // $postData = file_get_contents('php://input');
            // $body = json_decode($postData, true);

            $geoLocatios = $this->countryModel->listGeoLevel1(1);

            $res->result = $geoLocatios;
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }

        echo json_encode($res);
    }

    public function listGeoLevel2ByLeve1()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $geoLocatios = $this->countryModel->listGeoLevel2ByLeve1($body['geoLevel1']);

            $res->result = $geoLocatios;
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }

        echo json_encode($res);
    }

    public function listGeoLevel3ByLeve2()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $geoLocatios = $this->countryModel->listGeoLevel3ByLeve2($body['geoLevel2']);

            $res->result = $geoLocatios;
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }

        echo json_encode($res);
    }
}
