<?php
/*This file holds the entire rooting of all Ads client requests*/


/*Attempt to create new Ad using user data input*/
function createAdController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/ads.php');

  /*Use DB model function to determine if the input can be correctly used*/
  $ad_properties = validateAdProperties( $request['data'] );
  if (gettype($ad_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $ad_properties).'"}');
  }

  /*Try to create an Ad with validated properties*/
  $new_ad = createAd( $ad_properties );
  if (gettype($new_ad) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $new_ad).'"}');
  }


  /*Return new successfully created Ad*/
  header('HTTP/1.1 201 Created');
  die(json_unicode_encode( $new_ad ));
}



/*
  Ask the model to update Ad with ID sent as 1st and only URL parameter.
  Will print a possible update error or the full updated Ad on success.
*/
function updateAdController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/ads.php');


  /*Be sure we try to update an existing Ad*/
  $ad_id = $request['url_conponents'][1];
  $ad    = getAdByID( $ad_id );
  if (gettype($ad) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find ad with ID: '.$ad_id.'"}');
  }


  /*Use DB model function to determine if the input can be correctly used*/
  $ad_properties = validateAdProperties( $request['data'], false);
  if (gettype($ad_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $ad_properties).'"}');
  }


  /*Try to update the Ad with validated properties*/
  $updated_ad = updateAd( $ad_id, $ad_properties );
  if (gettype($updated_ad) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_ad).'"}');
  }


  /*Return the successfully updated Ad*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_ad ));
}



/*Shows all saved Ads taken from the DB*/
function showAllAds() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/ads.php');

  $ads = getAllAds();
  if (gettype($ads) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored ads"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $ads ));
}



/*Tries to find all ads matching sent filters*/
function showFilteredAds() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/ads.php');

  $filters = $request['data'];
  if ($error_message = validateAdsFilters( $filters )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  $ads = getFilteredAds( $filters );
  if (gettype($ads) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $ads).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $ads ));
}



/*Try to printout a specified by ID Ad*/
function showAd() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/ads.php');

  $ad_id = $request['url_conponents'][1];
  $ad    = getAdByID( $ad_id );

  if (gettype($ad) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find ad with ID: '.$ad_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $ad ));
}



/*
  Asks the DB model to remove all records for the Ad
  described from its ID as 1st and only URL parameter
*/
function deleteAdController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/ads.php');

  $ad_id = $request['url_conponents'][1];
  $ad    = getAdByID( $ad_id );

  if (gettype($ad) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find ad with ID: '.$ad_id.'"}');
  }


  if ($error_message = deleteAd( $ad['id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /ads
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 1      &&
    $request['url_conponents'][0]        === 'ads'  &&
    $request['method']                   === 'POST' &&
    sizeof( $request['data'] )
) {
  createAdController();
  return;
}


/*Validates rooting:
  /ads
in methods:
  PUT
*/
if (sizeof( $request['url_conponents'] ) === 2     &&
    $request['url_conponents'][0]        === 'ads' &&
    $request['method']                   === 'PUT' &&
    sizeof( $request['data'] )
) {
  updateAdController();
  return;
}


/*Validates rooting:
  /ads
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1     &&
    $request['url_conponents'][0]        === 'ads' &&
    $request['method']                   === 'GET'
) {

  /*Check if we need to show some or all Ads from the DB*/
  if (sizeof( $request['data'] )) {
    showFilteredAds();
  } else {
    showAllAds();
  }
  return;
}


/*Validates rooting:
  /ads/:ad_id
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 2     &&
    $request['url_conponents'][0]        === 'ads' &&
    $request['method']                   === 'GET'
) {
  showAd();
  return;
}


/*Validates rooting:
  /ads/:ad_id
in methods:
  DELETE
*/
if (sizeof( $request['url_conponents'] ) === 2     &&
    $request['url_conponents'][0]        === 'ads' &&
    $request['method']                   === 'DELETE'
) {
  deleteAdController();
  return;
}


/*No suitable Ads rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');