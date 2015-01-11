<?php
/*
  All server API calls will be redirected to here.
  This file is responsible for user login policy.
  This file will try to use a suitable server controller (to return a meaningful response)
  or will return 404 response status
*/

/*Used to navigate through the file/folder space*/
define('CONTROLLERS_BASE_FOLDER_PATH', getcwd().'/controllers/');
define('MODELS_BASE_FOLDER_PATH'     , getcwd().'/models/');

/*Leaves a 'lg()' function, suitable for all types of debug*/
require_once('debug.php');

/*Common set of helper function*/
require_once('utils.php');


/*Keeps all general requesting parameters in 1 place*/
$request = array(
  'url'    => isset($_GET['url'])               ? strtolower($_GET['url'])               : '',
  'method' => isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : ''
);
unset($_GET['url']);


/*Set a single place where the request data will be available regardless of the requested method*/
switch ($request['method']) {
  case 'GET':
    $request['data'] = $_GET;
  break;

  case 'POST':
    $request['data'] = $_POST;
  break;

  case 'PUT':
    parse_str( file_get_contents('php://input'), $request['data'] );
  break;
}


/*Secure correct URL format*/
$request['url'] = str_replace('\\'     , '/', $request['url']);
$request['url'] = preg_replace('/\/+$/', '' , $request['url']);

$request['url_conponents'] = explode('/', $request['url']);
array_shift( $request['url_conponents'] );


/*Define a possible controller who'll handle the request*/
$request['controller_name'] = isset( $request['url_conponents'][0] ) ? $request['url_conponents'][0] : '';
$request['controller_path'] = CONTROLLERS_BASE_FOLDER_PATH.$request['controller_name'].'.php';


/*
  Will load all Server settings from './settings.json',
  parse them into the global '$settings' variable
  or return an Error if some of the settings are missing or unusable
*/
require_once('settings.php');


/*Try to find the correct controller to handle the request or drop it with a 404*/
if (file_exists($request['controller_path'])) {
  require_once('set_user_session.php');
  require_once($request['controller_path']);
  return;
} else {
  header('HTTP/1.1 501 Not Implemented');
  die('{"error":"Service for method \''.$request['method'].'\' and URL \''.$request['url'].'\' not found"}');
}