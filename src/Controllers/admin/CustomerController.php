<?php

require_once MODEL_PATH . '/Customer.php';
require_once MODEL_PATH . '/IdentityDocumentType.php';

class CustomerController extends Controller
{
    protected $connection;
    protected $customerModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->customerModel = new Customer($connection);
    }

    public function home()
    {
        try {
            // authorization($this->connection, 'cliente', 'listar');
            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentType = $identityDocumentTypeModel->getAll();

            $this->render('admin/customer.view.php', [
                'identityDocumentType' => $identityDocumentType,
            ], 'layouts/admin.layout.php');
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
            // authorization($this->connection, 'cliente', 'listar');
            $page = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
            $limit = htmlspecialchars(isset($_GET['limit']) ? $_GET['limit'] : 10);
            $search = htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '');

            $customer = $this->customerModel->paginate($page, $limit, $search);

            $res->view = $this->render('admin/partials/customerTable.php', [
                'customer' => $customer,
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
            // authorization($this->connection, 'cliente', 'modificar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->customerModel->getById($body['customerId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function searchBySocialReason()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->customerModel->searchBySocialReason($body['search']);
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
            // authorization($this->connection, 'cliente', 'crear');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $res->result = $this->customerModel->insert([
                'documentNumber'=> htmlspecialchars($body['documentNumber']),
                'identityDocumentCode'=> htmlspecialchars($body['identityDocumentCode']),
                'socialReason'=> htmlspecialchars($body['socialReason']),
                'commercialReason'=> htmlspecialchars($body['commercialReason']),
                'fiscalAddress'=> htmlspecialchars($body['fiscalAddress']),
                'email'=> htmlspecialchars($body['email']),
                'telephone'=> htmlspecialchars($body['telephone']),
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
            // authorization($this->connection, 'cliente', 'modificar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'update');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $currentDate = date('Y-m-d H:i:s');
            $this->customerModel->updateById($body['customerId'], [
                'document_number'=> htmlspecialchars($body['documentNumber']),
                'identity_document_code'=> htmlspecialchars($body['identityDocumentCode']),
                'social_reason'=> htmlspecialchars($body['socialReason']),
                'commercial_reason'=> htmlspecialchars($body['commercialReason']),
                'fiscal_address'=> htmlspecialchars($body['fiscalAddress']),
                'email'=> htmlspecialchars($body['email']),
                'telephone'=> htmlspecialchars($body['telephone']),

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
            // authorization($this->connection, 'cliente', 'eliminar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->customerModel->updateById($body['customerId'], [
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
            if (($body['identityDocumentCode'] ?? '') == '') {
                $res->message .= 'Falta especificar el tipo de documento | ';
                $res->success = false;
            }

            if (($body['documentNumber'] ?? '') == '') {
                $res->message .= 'Falta ingresar el número del documento | ';
                $res->success = false;
            }

            if (($body['socialReason'] ?? '') == '') {
                $res->message .= 'Falta ingresar la razón social | ';
                $res->success = false;
            }
        }

        if ($type == 'update') {
            if (($body['customerId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id cliente | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message),'|');

        return $res;
    }
}