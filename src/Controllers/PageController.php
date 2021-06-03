<?php

require_once(MODEL_PATH . '/GeoLocation.php');

class PageController extends Controller
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function home()
    {
        try {
            $this->render('home.view.php', [], 'layouts/site.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function ayuda()
    {
        try {
            $this->render('help.view.php', [], 'layouts/site.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function error404()
    {
        $message = isset($_GET['message']) ? $_GET['message'] : '';

        try {
            if (strtolower($_SERVER['HTTP_ACCEPT']) === 'application/json') {
                http_response_code(404);
            } else {
                $this->render('404.view.php', [
                    'message' => $message
                ], 'layouts/site.layout.php');
            }
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => __FUNCTION__ . " : " .  $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function error403()
    {
        $message = isset($_GET['message']) ? $_GET['message'] : '';

        try {
            if (strtolower($_SERVER['HTTP_ACCEPT']) === 'application/json') {
                http_response_code(403);
            } else {
                $this->render('403.view.php', [
                    'message' => $message
                ], 'layouts/site.layout.php');
            }
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => __FUNCTION__ . " : " .  $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function queryDocument()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            // $currentDate = date('Y-m-d H:i:s');
            $documentTypeId = $body['documentTypeId'];
            $documentNumber = $body['documentNumber'];

            if ($documentTypeId == '3' || $documentTypeId == '1') {
                $token = 'eyJ1c2VySWQiOjEsInVzZXJUb2tlbklkIjoxfQ.KEqxZNc0_PqcsJj786nZC1Knh8R52fUehftszS5x9vhGbrmTz-66DJXfVWgyo3jxKva35kHOuEZwqOb02Ysa7XARgNbtVI--MJsPe_6xl_kQaN6vrf731B7-8qxkrNTUU8s7yChDOCKmoQNVAFOwNIEz7TH71zgMw6SXZoIf1GA';
                if ($documentTypeId == 3) {
                    $url = 'https://ruc.paulantezana.com/api/v1/ruc';
                    $data = [
                        'ruc' => $documentNumber,
                        'token' => $token,
                    ];
                } else {
                    $url = 'https://ruc.paulantezana.com/api/v1/dni';
                    $data = [
                        'dni' => $documentNumber,
                        'token' => $token,
                    ];
                }

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    //   CURLOPT_MAXREDIRS => 10,
                    //   CURLOPT_TIMEOUT => 0,
                    //   CURLOPT_FOLLOWLOCATION => true,
                    //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($data),
                ));

                $response = curl_exec($curl);
                if (curl_errno($curl)) {
                    throw new Exception(curl_error($curl));
                }

                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($statusCode != 200) {
                    throw new Exception('Curl status Code: ' . $statusCode);
                }

                $dataResponse = json_decode($response, true);
                if (!($dataResponse['success'] == true)) {
                    throw new Exception($dataResponse['message']);
                }

                if ($documentTypeId == '3') {
                    $res->result = [
                        'full_name' => $dataResponse['result']['social_reason'],
                        'taxpayer_state' => $dataResponse['result']['taxpayer_state'],
                        'domicile_condition' => $dataResponse['result']['domicile_condition'],
                        'father_last__name' => '',
                        'mother_last_name' => '',
                        'full_address' => $dataResponse['result']['address'],
                    ];
                } elseif ($documentTypeId == '1') {
                    $res->result = [
                        'father_last__name' => $dataResponse['result']['lastName'],
                        'mother_last_name' => $dataResponse['result']['motherLastName'],
                        'full_name' => $dataResponse['result']['name'],
                        'full_address' => '',
                        'taxpayer_state' => '',
                        'domicile_condition' => '',
                    ];
                } else {
                    $res->result = [
                        'full_name' => '',
                        'full_address' => '',
                        'father_last__name' => '',
                        'mother_last_name' => '',
                        'taxpayer_state' => '',
                        'domicile_condition' => '',
                    ];
                }
            } else {
                throw new Exception('Documento no soportado');
            }

            $res->success = true;
            $res->message = 'Busqueda exitosa';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function searchLocationLastLevel()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $geoLocationModel = new GeoLocation($this->connection);
            $locations = $geoLocationModel->searchLocationLastLevel($body['search']);

            $res->result = $locations;
            $res->success = true;
            $res->message = 'Busqueda exitosa';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }
}
