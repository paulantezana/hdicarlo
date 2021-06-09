<?php

require_once MODEL_PATH . '/Order.php';
require_once MODEL_PATH . '/OrderItem.php';
require_once MODEL_PATH . '/Product.php';
require_once MODEL_PATH . '/Exhibitor.php';
require_once MODEL_PATH . '/ExhibitorHistory.php';

class OrderController extends Controller
{
    private $connection;
    private $orderModel;
    private $orderItemModel;
    private $exhibitorModel;
    private $exhibitorStatesModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->orderModel = new Order($connection);
        $this->orderItemModel = new OrderItem($connection);
        $this->exhibitorModel = new Exhibitor($connection);
        $this->exhibitorStatesModel = new ExhibitorHistory($connection);
    }

    public function home()
    {
        try {
            authorization($this->connection, 'order');
            $exhibitorId = $_GET['exhibitorId'] ?? 0;
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $productModel = new Product($this->connection);
            $products = $productModel->getAllByCompanyId($companyId);

            $exhibitor = $this->exhibitorModel->getById($exhibitorId);
            if($exhibitor == false){
                $exhibitor = [];
            }

            $this->render('admin/order.view.php', [
                'exhibitor' => $exhibitor,
                'products' => $products,
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
            authorization($this->connection, 'order_create');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $validate = $this->validate($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $orderId = $this->orderModel->insert([
                'latLong' => $body['latitude'] . ',' . $body['longitude'],
                'dateOfDelivery' => $body['dateOfDelivery'],
                'observation' => htmlspecialchars(trim($body['observation'])),
                'total' => $body['total'],
                'companyId' => $companyId,
                'exhibitorId' => $body['exhibitorId'],
                'userId' => $_SESSION[SESS_KEY],
            ], $_SESSION[SESS_KEY]);

            foreach ($body['item'] as $key => $row) {
                $this->orderItemModel->insert([
                    'description'=> htmlspecialchars(trim($row['description'])),
                    'observation'=> htmlspecialchars(trim($row['observation'])),
                    'quantity'=> $row['quantity'],
                    'unitPrice'=> $row['unitPrice'],
                    'productId'=> $row['productId'],
                    'total'=> $row['total'],
                    'orderId'=> $orderId,
                ],$_SESSION[SESS_KEY]);
            }

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