<?php

class InnerController extends Controller
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function home()
    {
        try {
            $this->render('inner/dashboard.view.php', [
            ], 'layouts/inner.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/inner.layout.php');
        }
    }
}
