<?php
/*This file holds the entire rooting of all Ads client requests*/


/*Shows all saved Ads taken from the DB*/
function showAllAds() {
  require_once('./models/ads.php');

  $ads = getAllAds();
  if (gettype($ads) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored ads"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $ads ));
}



/*Validates rooting:
  /ads
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1 &&
    $request['url_conponents'][0] === 'ads'    &&
    $request['method']            === 'GET'
) {
  showAllAds();
  return;
}


/*No suitable Ads rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');