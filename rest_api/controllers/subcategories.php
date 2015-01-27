<?php
/*This file holds the entire rooting of all Subcategories client requests*/


/*Try to printout a specified by ID Subcategory*/
function showSubcategory() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');

  $subcategory_id = $request['url_conponents'][1];
  $subcategory    = getSubcategoryByID( $subcategory_id );

  if (gettype($subcategory) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Subcategory with ID: '.$subcategory_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $subcategory ));
}



/*Shows all saved Subcategories taken from the DB*/
function showAllSubcategories() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');

  $subcategories = getAllSubcategories();
  if (gettype($subcategories) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored Subcategories"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $subcategories ));
}



/*Tries to find all Subcategories, matches sent filters*/
function showFilteredSubcategories() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');

  $filters = $request['data'];
  if ($error_message = validateSubcategoriesFilters( $filters )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  $subcategories = getFilteredSubcategories( $filters );
  if (gettype($subcategories) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $subcategories).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $subcategories ));
}



/*Attempt to create new Subcategory using user data input*/
function createSubcategoryController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');

  /*Use DB model function to determine if the input can be correctly used*/
  $subcategory_properties = validateSubcategoryProperties( $request['data'] );
  if (gettype($subcategory_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $subcategory_properties).'"}');
  }

  /*Try to create a Subcategory with validated properties*/
  $new_subcategory = createSubcategory( $subcategory_properties );
  if (gettype($new_subcategory) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $new_subcategory).'"}');
  }


  /*Return new successfully created Subcategory*/
  header('HTTP/1.1 201 Created');
  die(json_unicode_encode( $new_subcategory ));
}



/*
  Ask the model to update Subcategory with ID sent as 1st and only URL parameter.
  Will print a possible update error or the full updated Subcategory on success.
*/
function updateSubcategoryController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');


  /*Be sure we try to update an existing Subcategory*/
  $subcategory_id = $request['url_conponents'][1];
  $subcategory    = getSubcategoryByID( $subcategory_id );
  if (gettype($subcategory) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Subcategory with ID: '.$subcategory_id.'"}');
  }


  /*Use DB model function to determine if the input can be correctly used*/
  $subcategory_properties = validateSubcategoryProperties( $request['data'], false);
  if (gettype($subcategory_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $subcategory_properties).'"}');
  }


  /*Try to update the Subcategory with validated properties*/
  $updated_subcategory = updateSubcategory( $subcategory_id, $subcategory_properties );
  if (gettype($updated_subcategory) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_subcategory).'"}');
  }


  /*Return the successfully updated Subcategory*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_subcategory ));
}



/*
  Will ask the model to save the incoming file from '$_FILE' into '/subcategories/' folder and
  update the image path into the DB record.
*/
function updateSubcategoryImageController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');


  /*Be sure we try to update an existing Subcategory*/
  $subcategory_id = $request['url_conponents'][1];
  $subcategory    = getSubcategoryByID( $subcategory_id );
  if (gettype($subcategory) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Subcategory with ID: '.$subcategory_id.'"}');
  }

  /*Let the model manage file save and file name DB records*/
  $updated_subcategory = updateSubcategoryImage( $subcategory_id );
  if (gettype($updated_subcategory) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_subcategory).'"}');
  }


  /*Return the successfully updated Subcategory*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_subcategory ));
}



/*
  Asks the DB model to remove all records for the Subcategory
  referenced with its ID as 1st and only URL parameter
*/
function deleteSubcategoryController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/subcategories.php');

  $subcategory_id = $request['url_conponents'][1];
  $subcategory    = getSubcategoryByID( $subcategory_id );

  if (gettype($subcategory) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Subcategory with ID: '.$subcategory_id.'"}');
  }


  if ($error_message = deleteSubcategory( $subcategory['id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /subcategories/:subcategory_id
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 2               &&
    $request['url_conponents'][0]        === 'subcategories' &&
    $request['method']                   === 'GET'
) {
  showSubcategory();
  return;
}


/*Validates rooting:
  /subcategories
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1               &&
    $request['url_conponents'][0]        === 'subcategories' &&
    $request['method']                   === 'GET'
) {

  /*Check if we need to show some or all Subcategories from the DB*/
  if (sizeof( $request['data'] )) {
    showFilteredSubcategories();
  } else {
    showAllSubcategories();
  }
  return;
}


/*Validates rooting:
  /subcategories
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 1               &&
    $request['url_conponents'][0]        === 'subcategories' &&
    $request['method']                   === 'POST'          &&
    sizeof( $request['data'] )
) {
  createSubcategoryController();
  return;
}


/*Validates rooting:
  /subcategories/:subcategory_id
in methods:
  PUT
*/
if (sizeof( $request['url_conponents'] ) === 2               &&
    $request['url_conponents'][0]        === 'subcategories' &&
    $request['method']                   === 'PUT'           &&
    sizeof( $request['data'] )
) {
  updateSubcategoryController();
  return;
}


/*Validates rooting:
  /subcategories/:subcategory_id/image
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 3               &&
    $request['url_conponents'][0]        === 'subcategories' &&
    $request['url_conponents'][2]        === 'image'         &&
    $request['method']                   === 'POST'
) {
  updateSubcategoryImageController();
  return;
}


/*Validates rooting:
  /subcategories/:subcategory_id
in methods:
  DELETE
*/
if (sizeof( $request['url_conponents'] ) === 2               &&
    $request['url_conponents'][0]        === 'subcategories' &&
    $request['method']                   === 'DELETE'
) {
  deleteSubcategoryController();
  return;
}


/*No suitable Subcategories rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');