<?php

require_once MODEL_PATH . '/Product.php';

class ProductController extends Controller
{
    private $connection;
    private $productModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->productModel = new Product($connection);
    }

    public function home()
    {
        try {
            authorization($this->connection, 'product');
            $this->render('admin/product.view.php', [], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function table()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'product_list');
            $page = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
            $limit = htmlspecialchars(isset($_GET['limit']) ? $_GET['limit'] : 20);
            $search = htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '');
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $product = $this->productModel->paginateByCompanyId($companyId, $page, $limit, $search);

            $res->view = $this->render('admin/partials/productTable.php', [
                'product' => $product,
            ], '', true);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function id()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'product_list');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->productModel->getById($body['productId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }


    public function create()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'product_create');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $res->result = $this->productModel->insert([
                'title'=> htmlspecialchars($body['title']),
                'barCode'=> htmlspecialchars($body['barCode']),
                'price'=> htmlspecialchars($body['price']),
                'companyId'=> $_SESSION[SESS_USER]['company_id'],
            ], $_SESSION[SESS_KEY]);
            $res->success = true;
            $res->message = 'El registro se inserto exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function update()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'product_update');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'update');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $currentDate = date('Y-m-d H:i:s');
            $this->productModel->updateById($body['productId'], [
                'title'=> htmlspecialchars($body['title']),
                'bar_code'=> htmlspecialchars($body['barCode']),
                'price'=> htmlspecialchars($body['price']),

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);

            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function delete()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'product_delete');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->productModel->updateById($body['productId'], [
                'state'=> 0,

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);
            
            $res->success = true;
            $res->message = 'El registro se eliminó exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function validateInput($body, $type = 'create')
    {
        $res = new Result();
        $res->success = true;

        if ($type == 'create' || $type == 'update') {
            if (($body['title'] ?? '') == '') {
                $res->message .= 'Falta ingresar el titulo | ';
                $res->success = false;
            }

            if (($body['price'] ?? '') == '') {
                $res->message .= 'Falta ingresar el precio | ';
                $res->success = false;
            }
        }

        if ($type == 'update') {
            if (($body['productId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id del product | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message),'|');

        return $res;
    }
}
