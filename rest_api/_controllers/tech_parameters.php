<?php
/*This file will handle all routs that involve 'tech_parameters'*/


/*Presents a list of all known product/category technical parameters*/
function showAllTechParameters() {
  global $user;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  /*Load all tech_params DB-related functions*/
  require_once('./models/tech_parameters.php');


  /*Try to correctly get all technical parameters from DB*/
  $tech_params = getAllTechParams();
  if (gettype($tech_params) != 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all technical parameters"}');
  }


  /*Return technical parameters list in the expected 'data' property*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( array('data' => array_values( $tech_params ))));
}



/*Basic validation of all expected arguments, used to create new technical parameter*/
function createTechParameterController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $new_tech_param_name = $request['url_conponents'][1];
  $data                = $request['data'];

  /*Load all technical parameters' DB-related functions*/
  require_once('./models/tech_parameters.php');

  /*Check if there's already a tech parameter with this name in our DB*/
  if (getTechParamByName( $new_tech_param_name )) {
    header('HTTP/1.1 409 Conflict');
    die();
  }


  /*Basic type validation*/
  if (!isset($data['name'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'name\' property"}');
  }
  if (!isset($data['units'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'units\' property"}');
  }


  /*Check if all creation methodology pass with no issues*/
  if ($error_message = createTechParameter( $new_tech_param_name, $data['name'], $data['units'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }

  /*New technical parameter was successfully created*/
  header('HTTP/1.1 201 Created');
  die();
}



/*
  Will made some basic vars validation before sending them all
  to the DB model for internal detailed validation and records altering
*/
function updateTechParameterController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $tech_param_name = $request['url_conponents'][1];
  $data            = $request['data'];

  /*Load all technical parameters' DB-related functions*/
  require_once('./models/tech_parameters.php');

  /*Check if the parameter we want to update does not exist in our DB*/
  if (!getTechParamByName( $tech_param_name )) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*Basic values filtering*/
  $tech_param_translation_names = isset($data['name' ]) ? $data['name' ] : null;
  $units                        = isset($data['units']) ? $data['units'] : null;

  /*Check if the entire updating process passed with no issues*/
  if ($error_message = updateTechParameter( $tech_param_name, $tech_param_translation_names, $units )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Updated with no errors*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Authorize the removal of incoming technical parameter*/
function deleteTechParameterController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $tech_param_name = $request['url_conponents'][1];

  /*Load all technical parameters' DB-related functions*/
  require_once('./models/tech_parameters.php');

  /*Check if the parameter we want to delete does not exist in our DB*/
  if (!getTechParamByName( $tech_param_name )) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*Try to delete parameter from the DB*/
  if ($error_message = deleteTechParameter( $tech_param_name )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Removed with no errors*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /tech_parameters
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) == 1          &&
    $request['url_conponents'][0] == 'tech_parameters' &&
    $request['method']            == 'GET'
) {
  showAllTechParameters();
  return;
}



/*Validates rooting:
  /tech_parameters/:tech_parameter
in methods:
  POST, PUT, DELETE
*/
if (sizeof( $request['url_conponents'] ) == 2  &&
    $request['url_conponents'][0] == 'tech_parameters'
) {
  switch ($request['method']) {
    case 'POST':
      createTechParameterController();
      return;
    break;

    case 'PUT':
      updateTechParameterController();
      return;
    break;

    case 'DELETE':
      deleteTechParameterController();
      return;
    break;
  }
}



/*No suitable Technical Parameters rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');