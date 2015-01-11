<?php
/*This file will handle all routs that respect 'filters'*/


/*Presents a list of all known product/category filters*/
function showAllFilters() {
  global $user;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  /*Load all filters DB-related functions*/
  require_once('./models/filters.php');


  /*Try to correctly get all filters from DB*/
  $filters = getAllFilters();
  if (gettype($filters) != 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored filters"}');
  }


  /*Return filters list in the expected 'data' property*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( array('data' => array_values( $filters ))));
}



/*
  Used for creating new filters.
  Sets only basic validation rules over the incoming data
  while leaving the entire property check to the creation model
*/
function createFilterController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $new_filter_name = $request['url_conponents'][1];
  $data            = $request['data'];

  /*Load all filters DB-related functions*/
  require_once('./models/filters.php');

  /*Check if the filter we want to add already exists in our DB*/
  if (getFilterByName( $new_filter_name )) {
    header('HTTP/1.1 409 Conflict');
    die();
  }


  /*
    Confirm that all data was at least sent by the client
    before we sent it to the creation model
  */
  if (!isset($data['name'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'name\' property"}');
  }
  if (!isset($data['type'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'type\' property"}');
  }
  if (!isset($data['values'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'values\' property"}');
  }
  if (!isset($data['default_values'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'default_values\' property"}');
  }


  /*Check if all creation methodology pass with no issues*/
  if ($error_message = createFilter( $new_filter_name, $data['name'], $data['type'], $data['values'], $data['default_values'])) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }

  /*New filter was successfully created*/
  header('HTTP/1.1 201 Created');
  die();
}



/*
  Will made some basic vars validation before sending them all
  to the DB model for internal detailed validation and records altering
*/
function updateFilterController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $filter_name = $request['url_conponents'][1];
  $data        = $request['data'];

  /*Load all filters DB-related functions*/
  require_once('./models/filters.php');

  /*Check if the filter we want to update does not exist in our DB*/
  if (!getFilterByName( $filter_name )) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*Basic values filtering*/
  $filter_translation_names = isset($data['name'          ]) ? $data['name'          ] : null;
  $type                     = isset($data['type'          ]) ? $data['type'          ] : null;
  $values                   = isset($data['values'        ]) ? $data['values'        ] : null;
  $default_values           = isset($data['default_values']) ? $data['default_values'] : null;

  /*Check if the entire updating process passed with no issues*/
  if ($error_message = updateFilter( $filter_name, $filter_translation_names, $type, $values, $default_values )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Updated with no errors*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Authorize the removal of incoming filter*/
function deleteFilterController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $filter_name = $request['url_conponents'][1];

  /*Load all filters DB-related functions*/
  require_once('./models/filters.php');

  /*Check if the filter we want to delete does not exist in our DB*/
  if (!getFilterByName( $filter_name )) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*Try to delete it from the DB*/
  if ($error_message = deleteFilter( $filter_name )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Removed with no errors*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /filters
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) == 1  &&
    $request['url_conponents'][0] == 'filters' &&
    $request['method']            == 'GET'
) {
  showAllFilters();
  return;
}



/*Validates rooting:
  /filters/:filter
in methods:
  POST, PUT, DELETE
*/
if (sizeof( $request['url_conponents'] ) == 2  &&
    $request['url_conponents'][0] == 'filters'
) {
  switch ($request['method']) {
    case 'POST':
      createFilterController();
      return;
    break;

    case 'PUT':
      updateFilterController();
      return;
    break;

    case 'DELETE':
      deleteFilterController();
      return;
    break;
  }
}



/*No suitable Filters rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');