<?php

require_once MODEL_PATH . '/AppPayment.php';

class ReportController extends Controller
{
    private $connection;
    private $appPaymentModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->appPaymentModel = new AppPayment($connection);
    }

    public function income()
    {
        try {
            $this->render('inner/reportIncome.view.php', [], 'layouts/inner.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/inner.layout.php');
        }
    }

    public function incomeTable()
    {
        $res = new Result();
        try {
            $year = htmlspecialchars($_GET['year'] ?? date('Y'));
            $paymentIncome = $this->appPaymentModel->getIncome($year);

            $res->view = $this->render('inner/partials/reportIncomeTable.php', [
                'paymentIncome' => $paymentIncome,
            ], '', true);
            $res->result = [
                'paymentIncome' => $paymentIncome,
            ];
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }
}
