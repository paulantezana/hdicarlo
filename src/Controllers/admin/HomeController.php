<?php

require_once(MODEL_PATH . '/User.php');
require_once(MODEL_PATH . '/Exhibitor.php');
require_once(MODEL_PATH . '/Customer.php');

class HomeController extends Controller
{
    private $connection;
    private $userModel;
    private $exhibitorModel;
    private $customerModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->userModel = new User($connection);
        $this->exhibitorModel = new Exhibitor($connection);
        $this->customerModel = new Customer($connection);
    }

    public function home()
    {
        try {
            $userCount = 0; //$this->userModel->count();
            $exhibitorCount = $this->exhibitorModel->count();
            $customerCount = $this->customerModel->count();

            $this->render('admin/dashboard.view.php', [
                'userCount' => $userCount,
                'exhibitorCount' => $exhibitorCount,
                'customerCount' => $customerCount,
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function help()
    {
        try {
            $this->render('help.view.php', [
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function getGlobalInfo()
    {
        $res = new Result();
        try {
            
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }
}
