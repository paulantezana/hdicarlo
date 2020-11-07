<?php

require_once(MODEL_PATH . '/Exhibitor.php');
require_once(MODEL_PATH . '/ExhibitorStates.php');
require_once(MODEL_PATH . '/Size.php');
require_once(MODEL_PATH . '/Customer.php');
require_once(MODEL_PATH . '/Country.php');
require_once(MODEL_PATH . '/IdentityDocumentType.php');

class ExhibitorController extends Controller
{
    protected $connection;
    protected $exhibitorModel;
    private $countryModel;
    private $customerModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->exhibitorModel = new Exhibitor($connection);
        $this->exhibitorStateModel = new ExhibitorStates($connection);
        $this->countryModel = new Country($connection);
        $this->customerModel = new Customer($connection);
    }

    public function home()
    {
        try {
            // authorization($this->connection, 'cliente', 'listar');
            $sizeModel = new Size($this->connection);
            $size = $sizeModel->getAll();

            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentType = $identityDocumentTypeModel->getAll();

            $geoLevel1 = $this->countryModel->listGeoLevel1(1);
            $customer = $this->customerModel->getAll();

            $this->render('admin/exhibitor.view.php', [
                'size' => $size,
                'geoLevel1' => $geoLevel1,
                'customer' => $customer,
                'identityDocumentType' => $identityDocumentType,
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function detail()
    {
        try {
            $exhibitorId = $_GET['exhibitorId'] ?? 0;
            if ($exhibitorId == 0) {
                $this->redirect('/admin');
            }

            $exhibitor = $this->exhibitorModel->getById($exhibitorId);
            $customer = $this->customerModel->getById($exhibitor['customer_id']);

            $this->render('admin/exhibitorDetail.view.php', [
                'exhibitor' => $exhibitor,
                'customer' => $customer,
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function states(){
        $res = new Result();
        try {
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $current = isset($body['current']) ? $body['current'] : 1;
            $exhibitorId = $body['exhibitorId'];

            $res->result = $this->exhibitorStateModel->scrollByExhibitorId($exhibitorId, $current);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function table()
    {
        $res = new Result();
        try {
            // authorization($this->connection, 'cliente', 'listar');
            $page = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
            $limit = htmlspecialchars(isset($_GET['limit']) ? $_GET['limit'] : 10);
            $search = htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '');

            $exhibitor = $this->exhibitorModel->paginate($page, $limit, $search);

            $res->view = $this->render('admin/partials/exhibitorTable.php', [
                'exhibitor' => $exhibitor,
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

            $res->result = $this->exhibitorModel->getById($body['exhibitorId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function getByCode()
    {
        $res = new Result();
        try {
            // authorization($this->connection, 'cliente', 'modificar');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            if (!isset($body['code'])) {
                throw new Exception('No se envió el código');
            }

            $code = htmlspecialchars($body['code']);
            if (empty($code)) {
                throw new Exception('Ingrese almenos un codigo');
            }

            $exhibitor = $this->exhibitorModel->getByCode($code);
            if ($exhibitor == false) {
                throw new Exception('No se encontro ningun resultado');
            }

            $exhibitorView =   $this->render('admin/partials/exhibitorDetail.partial.php', [
                'exhibitor' => $exhibitor,
            ], '', true);

            $res->view = $exhibitorView;
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

            $geoId = explode('_', $body['geoId']);
            $latLong = $body['latitude'] . ',' . $body['longitude'];

            $res->result = $this->exhibitorModel->insert([
                'code' => htmlspecialchars($body['code']),
                'sizeId' => htmlspecialchars($body['sizeId']),
                'countryId' => $geoId[0],
                'geoLevel1Id' => $geoId[1],
                'geoLevel2Id' => $geoId[2],
                'geoLevel3Id' => $geoId[3],
                'latLong' => $latLong,
                'address' => htmlspecialchars($body['address']),
                'customerId' => htmlspecialchars($body['customerId']),
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

            $geoId = explode('_', $body['geoId']);
            $latLong = $body['latitude'] . ',' . $body['longitude'];

            $currentDate = date('Y-m-d H:i:s');
            $this->exhibitorModel->updateById($body['exhibitorId'], [
                'code' => htmlspecialchars($body['code']),
                'size_id' => htmlspecialchars($body['sizeId']),
                'country_id' => $geoId[0],
                'geo_level_1_id' => $geoId[1],
                'geo_level_2_id' => $geoId[2],
                'geo_level_3_id' => $geoId[3],
                'lat_long' => $latLong,
                'address' => htmlspecialchars($body['address']),
                'customer_id' => htmlspecialchars($body['customerId']),

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
            $this->exhibitorModel->updateById($body['exhibitorId'], [
                'state' => 0,

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
            if (($body['geoId'] ?? '') == '') {
                $res->message .= 'Falta especificar la ciudad | ';
                $res->success = false;
            }
            if (($body['code'] ?? '') == '') {
                $res->message .= 'Falta ingresar el código | ';
                $res->success = false;
            }
            if (($body['customerId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el cliente | ';
                $res->success = false;
            }
            if (($body['sizeId'] ?? '') == '') {
                $res->message .= 'Falta especificar el tamañp | ';
                $res->success = false;
            }
        }

        if ($type == 'update') {
            if (($body['exhibitorId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id cliente | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message), '|');

        return $res;
    }
}
