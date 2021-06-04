<?php

require_once MODEL_PATH . '/Order.php';
require_once MODEL_PATH . '/Exhibitor.php';
require_once MODEL_PATH . '/ExhibitorHistory.php';

class OrderController extends Controller
{
    private $connection;
    private $orderModel;
    private $exhibitorModel;
    private $exhibitorStatesModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->orderModel = new Order($connection);
        $this->exhibitorModel = new Exhibitor($connection);
        $this->exhibitorStatesModel = new ExhibitorHistory($connection);
    }

    public function home()
    {
        try {
            $exhibitorId = $_GET['exhibitorId'] ?? 0;

            $exhibitor = $this->exhibitorModel->getById($exhibitorId);
            if($exhibitor == false){
                $exhibitor = [];
            }

            $this->render('admin/order.view.php', [
                'exhibitor' => $exhibitor,
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }
    
    public function save(){
        $res = new Result();
        $this->connection->beginTransaction();
        try {
            // authorization($this->connection, 'cliente', 'modificar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $validate = $this->validate($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $this->orderModel->insert([
                'latLong' => $body['latitude'] . ',' . $body['longitude'],
                'dateOfOrder' => $body['dateOfDelivery'],
                'observation' => htmlspecialchars(trim($body['observation'])),
                'companyId' => $companyId,
                'exhibitorId' => $body['exhibitorId'],
                'userId' => $_SESSION[SESS_KEY],
            ], $_SESSION[SESS_KEY]);

            $this->exhibitorStatesModel->insert([
                'exhibitorState' => 'ORDER',
                'exhibitorId' => $body['exhibitorId'],
            ],$_SESSION[SESS_KEY]);

            $this->connection->commit();
            $res->success = true;
            $res->message = "El proceso se completo exitosamente";
        } catch (Exception $e) {
            $res->message = $e->getMessage();
            $this->connection->rollBack();
        }
        echo json_encode($res);
    }
    
    private function validate($body){
        $res = new Result();
        $res->success = true;

        if (($body['exhibitorId'] ?? '') == '') {
            $res->message .= 'Falta ingresar el id de la exibidora | ';
            $res->success = false;
        }

        if (($body['code'] ?? '') == '') {
            $res->message .= 'Falta ingresar el codigo | ';
            $res->success = false;
        }

        if (($body['dateOfDelivery'] ?? '') == '') {
            $res->message .= 'Falta especificar la fecha de entrega | ';
            $res->success = false;
        }

        return $res;
    }
}