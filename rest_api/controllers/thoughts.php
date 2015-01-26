<?php
/*This file holds the entire rooting of all Thoughts client requests*/


/*Try to printout a specified by ID Thought*/
function showThought() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');

  $thought_id = $request['url_conponents'][1];
  $thought    = getThoughtByID( $thought_id );

  if (gettype($thought) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Thought with ID: '.$thought_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $thought ));
}



/*Shows all saved Thoughts taken from the DB*/
function showAllThoughts() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');

  $thoughts = getAllThoughts();
  if (gettype($thoughts) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored Thoughts"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $thoughts ));
}



/*Tries to find all Thoughts, matches sent filters*/
function showFilteredThoughts() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');

  $filters = $request['data'];
  if ($error_message = validateThoughtsFilters( $filters )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  $thoughts = getFilteredThoughts( $filters );
  if (gettype($thoughts) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $thoughts).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $thoughts ));
}



/*Attempt to create new Thought using user data input*/
function createThoughtController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');

  /*Use DB model function to determine if the input can be correctly used*/
  $thought_properties = validateThoughtProperties( $request['data'] );
  if (gettype($thought_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $thought_properties).'"}');
  }

  /*Try to create a Thought with validated properties*/
  $new_thought = createThought( $thought_properties );
  if (gettype($new_thought) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $new_thought).'"}');
  }


  /*Return new successfully created Thought*/
  header('HTTP/1.1 201 Created');
  die(json_unicode_encode( $new_thought ));
}



/*
  Ask the model to update Thought with ID sent as 1st and only URL parameter.
  Will print a possible update error or the full updated Thought on success.
*/
function updateThoughtController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');


  /*Be sure we try to update an existing Thought*/
  $thought_id = $request['url_conponents'][1];
  $thought    = getThoughtByID( $thought_id );
  if (gettype($thought) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Thought with ID: '.$thought_id.'"}');
  }


  /*Use DB model function to determine if the input can be correctly used*/
  $thought_properties = validateThoughtProperties( $request['data'], false);
  if (gettype($thought_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $thought_properties).'"}');
  }


  /*Try to update the Thought with validated properties*/
  $updated_thought = updateThought( $thought_id, $thought_properties );
  if (gettype($updated_thought) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_thought).'"}');
  }


  /*Return the successfully updated Thought*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_thought ));
}



/*
  Will ask the model to save the incoming file from '$_FILE' into '/thoughts/' folder and
  update the image path into the DB record.
*/
function updateThoughtImageController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');


  /*Be sure we try to update an existing Thought*/
  $thought_id = $request['url_conponents'][1];
  $thought    = getThoughtByID( $thought_id );
  if (gettype($thought) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Thought with ID: '.$thought_id.'"}');
  }

  /*Let the model manage file save and file name DB records*/
  $updated_thought = updateThoughtImage( $thought_id );
  if (gettype($updated_thought) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_thought).'"}');
  }


  /*Return the successfully updated Thought*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_thought ));
}



/*
  Asks the DB model to remove all records for the Thought
  referenced with its ID as 1st and only URL parameter
*/
function deleteThoughtController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/thoughts.php');

  $thought_id = $request['url_conponents'][1];
  $thought    = getThoughtByID( $thought_id );

  if (gettype($thought) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Thought with ID: '.$thought_id.'"}');
  }


  if ($error_message = deleteThought( $thought['id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /thoughts/:thought_id
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 2          &&
    $request['url_conponents'][0]        === 'thoughts' &&
    $request['method']                   === 'GET'
) {
  showThought();
  return;
}


/*Validates rooting:
  /thoughts
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1          &&
    $request['url_conponents'][0]        === 'thoughts' &&
    $request['method']                   === 'GET'
) {

  /*Check if we need to show some or all Thoughts from the DB*/
  if (sizeof( $request['data'] )) {
    showFilteredThoughts();
  } else {
    showAllThoughts();
  }
  return;
}


/*Validates rooting:
  /thoughts
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 1          &&
    $request['url_conponents'][0]        === 'thoughts' &&
    $request['method']                   === 'POST'     &&
    sizeof( $request['data'] )
) {
  createThoughtController();
  return;
}


/*Validates rooting:
  /thoughts/:thought_id
in methods:
  PUT
*/
if (sizeof( $request['url_conponents'] ) === 2          &&
    $request['url_conponents'][0]        === 'thoughts' &&
    $request['method']                   === 'PUT'      &&
    sizeof( $request['data'] )
) {
  updateThoughtController();
  return;
}


/*Validates rooting:
  /thoughts/:thought_id/image
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 3          &&
    $request['url_conponents'][0]        === 'thoughts' &&
    $request['url_conponents'][2]        === 'image'    &&
    $request['method']                   === 'POST'
) {
  updateThoughtImageController();
  return;
}


/*Validates rooting:
  /thoughts/:thought_id
in methods:
  DELETE
*/
if (sizeof( $request['url_conponents'] ) === 2          &&
    $request['url_conponents'][0]        === 'thoughts' &&
    $request['method']                   === 'DELETE'
) {
  deleteThoughtController();
  return;
}


/*No suitable Thoughts rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');