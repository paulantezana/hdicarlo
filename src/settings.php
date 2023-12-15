<?php
date_default_timezone_set('America/Lima');

function getEnvValueByKey()
{
  $file = new SplFileObject(ROOT_DIR . '/.env');
  $data = [];

  while (!$file->eof()) {
    $line = $file->fgets();
    if (strlen($line) > 3) {
      list($key, $value) = explode("=", $line, 2);
      $data[trim($key)] = trim($value ?? '');
    }
  }

  return $data;
}

function exceptions_error_handler($severity, $message, $filename, $lineno)
{
  $dateTime =  date('Y-m-d H:i:s');
  error_log("{$dateTime}: {$severity} ${message}" . PHP_EOL . $filename . "({$lineno})" . PHP_EOL, 3,  __DIR__ . '/../files/errors.log');
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}
set_error_handler('exceptions_error_handler');

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$requestUri = parse_url('http://example.com' . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
$virtualPath = '/' . ltrim(substr($requestUri, strlen($scriptName)), '/');
$hostName = (stripos($_SERVER['REQUEST_SCHEME'], 'https') === 0 ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];

define('HOST', $hostName);
define('URI', $requestUri);
define('URL_PATH', rtrim($scriptName,'/'));
define('URL',$virtualPath);

define('ROOT_DIR', $_SERVER["DOCUMENT_ROOT"] . rtrim($scriptName, '/'));
define('CONTROLLER_PATH', ROOT_DIR . '/src/Controllers');
define('MODEL_PATH', ROOT_DIR . '/src/Models');
define('VIEW_PATH', ROOT_DIR . '/src/Views');
define('CERVICE_PATH', ROOT_DIR . '/src/Services');
define('HELPER_PATH', ROOT_DIR . '/src/Helpers');

define('SESS_KEY','SnId_hdcarlo');
define('SESS_USER','SnUser_hdcarlo');
define('SESS_DATE_OF_DUE', 'SnDateOfDue_hdcarlo');
define('SESS_DATE_OF_DUE_DAY', 'SnDateOfDueDay_hdcarlo');

define('APP_NAME','DCARLO APP');
define('APP_AUTHOR','paulantezana');
define('APP_AUTHOR_WEB', 'http://paulantezana.com/');
define('APP_DESCRIPTION','Control de entrega');
define('APP_EMAIL','paulantezana.2@gmail.com');
define('APP_PHONE', '+51977898402');
define('APP_COLOR', '#364EC7');

define('APP_DEV',false);

define('FILE_PATH', '/files');

define('APP_ENV', getEnvValueByKey());

define('GOOGLE_API_KEY','AIzaSyAG7pSRfqKAObS76qjyyIeuImkVcooIt2I');
// define('GOOGLE_RE_SECRET_KEY','6LfcgMQZAAAAANIo0O9NzC5bJyPowYVt9gQMyqyo');
