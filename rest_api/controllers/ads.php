<?php
/*This file holds the entire rooting of all Ads client requests*/


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
    die('{"error":"Unable to find ad with ID '.$ad_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $ad ));
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
if (sizeof( $request['url_conponents'] ) === 2 &&
    $request['url_conponents'][0]        === 'ads'
) {
  showAd();
  return;
}


/*No suitable Ads rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');