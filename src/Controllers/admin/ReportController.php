<?php

require_once(MODEL_PATH . '/Order.php');
require_once(MODEL_PATH . '/OrderItem.php');
require_once(MODEL_PATH . '/Delivery.php');

class ReportController extends Controller
{
    private $connection;
    private $orderModel;
    private $orderItemModel;
    private $deliveryModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->orderModel = new Order($connection);
        $this->orderItemModel = new OrderItem($connection);
        $this->deliveryModel = new Delivery($connection);
    }

    public function orderReport()
    {
        try {
            // authorization($this->connection, 'order');
            $this->render('admin/reportOrder.view.php', [], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }
    
    public function orderReportTable()
    {
        $res = new Result();
        try {
            $page = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
            $limit = htmlspecialchars(isset($_GET['limit']) ? $_GET['limit'] : 10);
            $search = htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '');
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $orders = $this->orderModel->paginateByCompanyId($companyId, $page, $limit, $search);

            $res->view = $this->render('admin/partials/reportOrderTable.php', [
                'orders' => $orders,
            ], '', true);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function orderCancel()
    {
        $res = new Result();
        try {
            // authorization($this->connection, 'entrust', 'anular');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->orderModel->updateById($body['orderId'], [
                'canceled' => 1,
                'canceled_observation' => $body['message'],

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);

            $res->success = true;
            $res->message = 'El registro se anuló exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function orderItems()
    {
        $res = new Result();
        try {
            // authorization($this->connection, 'entrust', 'listar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $orderItem = $this->orderItemModel->getAllByOrderId($body['orderId']);
            $res->view = $this->render('admin/partials/reportOrderItemTable.php', [
                'orderItem' => $orderItem,
            ], '', true);
            $res->success = true;
            $res->message = '';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function orderChart()
    {
        $res = new Result();
        try {
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