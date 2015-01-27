<?php
/*This file holds the entire rooting of all Partners client requests*/


/*Try to printout a specified by ID Partner*/
function showPartner() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');

  $partner_id = $request['url_conponents'][1];
  $partner    = getPartnerByID( $partner_id );

  if (gettype($partner) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Partner with ID: '.$partner_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $partner ));
}



/*Shows all saved Partners taken from the DB*/
function showAllPartners() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');

  $partners = getAllPartners();
  if (gettype($partners) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored Partners"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $partners ));
}



/*Tries to find all Partners, matches sent filters*/
function showFilteredPartners() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');

  $filters = $request['data'];
  if ($error_message = validatePartnersFilters( $filters )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  $partners = getFilteredPartners( $filters );
  if (gettype($partners) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $partners).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $partners ));
}



/*Attempt to create new Partner using user data input*/
function createPartnerController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');

  /*Use DB model function to determine if the input can be correctly used*/
  $partner_properties = validatePartnerProperties( $request['data'] );
  if (gettype($partner_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $partner_properties).'"}');
  }

  /*Try to create a Partner with validated properties*/
  $new_partner = createPartner( $partner_properties );
  if (gettype($new_partner) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $new_partner).'"}');
  }


  /*Return new successfully created Partner*/
  header('HTTP/1.1 201 Created');
  die(json_unicode_encode( $new_partner ));
}



/*
  Ask the model to update Partner with ID sent as 1st and only URL parameter.
  Will print a possible update error or the full updated Partner on success.
*/
function updatePartnerController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');


  /*Be sure we try to update an existing Partner*/
  $partner_id = $request['url_conponents'][1];
  $partner    = getPartnerByID( $partner_id );
  if (gettype($partner) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Partner with ID: '.$partner_id.'"}');
  }


  /*Use DB model function to determine if the input can be correctly used*/
  $partner_properties = validatePartnerProperties( $request['data'], false);
  if (gettype($partner_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $partner_properties).'"}');
  }


  /*Try to update the Partner with validated properties*/
  $updated_partner = updatePartner( $partner_id, $partner_properties );
  if (gettype($updated_partner) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_partner).'"}');
  }


  /*Return the successfully updated Partner*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_partner ));
}



/*
  Will ask the model to save the incoming file from '$_FILE' into '/partners/' folder and
  update the image path into the DB record.
*/
function updatePartnerImageController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');


  /*Be sure we try to update an existing Partner*/
  $partner_id = $request['url_conponents'][1];
  $partner    = getPartnerByID( $partner_id );
  if (gettype($partner) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Partner with ID: '.$partner_id.'"}');
  }

  /*Let the model manage file save and file name DB records*/
  $updated_partner = updatePartnerImage( $partner_id );
  if (gettype($updated_partner) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_partner).'"}');
  }


  /*Return the successfully updated Partner*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_partner ));
}



/*
  Asks the DB model to remove all records for the Partner
  referenced with its ID as 1st and only URL parameter
*/
function deletePartnerController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/partners.php');

  $partner_id = $request['url_conponents'][1];
  $partner    = getPartnerByID( $partner_id );

  if (gettype($partner) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Partner with ID: '.$partner_id.'"}');
  }


  if ($error_message = deletePartner( $partner['id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /partners/:partner_id
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 2          &&
    $request['url_conponents'][0]        === 'partners' &&
    $request['method']                   === 'GET'
) {
  showPartner();
  return;
}


/*Validates rooting:
  /partners
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1          &&
    $request['url_conponents'][0]        === 'partners' &&
    $request['method']                   === 'GET'
) {

  /*Check if we need to show some or all Partners from the DB*/
  if (sizeof( $request['data'] )) {
    showFilteredPartners();
  } else {
    showAllPartners();
  }
  return;
}


/*Validates rooting:
  /partners
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 1          &&
    $request['url_conponents'][0]        === 'partners' &&
    $request['method']                   === 'POST'     &&
    sizeof( $request['data'] )
) {
  createPartnerController();
  return;
}


/*Validates rooting:
  /partners/:partner_id
in methods:
  PUT
*/
if (sizeof( $request['url_conponents'] ) === 2          &&
    $request['url_conponents'][0]        === 'partners' &&
    $request['method']                   === 'PUT'      &&
    sizeof( $request['data'] )
) {
  updatePartnerController();
  return;
}


/*Validates rooting:
  /partners/:partner_id/image
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 3          &&
    $request['url_conponents'][0]        === 'partners' &&
    $request['url_conponents'][2]        === 'image'    &&
    $request['method']                   === 'POST'
) {
  updatePartnerImageController();
  return;
}


/*Validates rooting:
  /partners/:partner_id
in methods:
  DELETE
*/
if (sizeof( $request['url_conponents'] ) === 2          &&
    $request['url_conponents'][0]        === 'partners' &&
    $request['method']                   === 'DELETE'
) {
  deletePartnerController();
  return;
}


/*No suitable Partners rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');