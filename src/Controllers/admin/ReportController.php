<?php

require_once MODEL_PATH . '/Order.php';
require_once MODEL_PATH . '/Delivery.php';

class ReportController extends Controller
{
    private $connection;
    private $orderModel;
    private $deliveryModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->orderModel = new Order($connection);
        $this->deliveryModel = new Delivery($connection);
    }

    public function orderChart()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'usuario', 'modificar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->orderModel->reportChart($body);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function deliveryChart()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'usuario', 'modificar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->deliveryModel->reportChart($body);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }
}