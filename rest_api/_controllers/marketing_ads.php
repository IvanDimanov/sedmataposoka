<?php
/*This file holds the entire rooting of all Marketing Ads client requests*/


/*Shows all saved Marketings Ads taken from the DB*/
function showAllMarketingAds() {
  require_once('./models/marketing_ads.php');

  $marketing_ads = getAllMarketingAds();
  if (gettype($marketing_ads) != 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored marketing ads"}');
  }


  /*Present in expected 'slides' holder*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( array('slides' => $marketing_ads )));
}



/*Validates rooting:
  /marketing_ads
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) == 1    &&
    $request['url_conponents'][0] == 'marketing_ads' &&
    $request['method']            == 'GET'
) {
  showAllMarketingAds();
  return;
}


/*No suitable Marketing Ads rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');