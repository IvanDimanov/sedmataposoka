<?php
/*This file holds all DB functions that effects Marketings Ads*/


/*Returns an indexed array of all marketings ads records we have in DB*/
function getAllMarketingAds() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved marketing ads*/
  $query = $db->prepare('SELECT image_url as image, heading, `text`, link FROM marketing_ad');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  /*Send the entire query result in indexed array*/
  return $query->fetchAll(PDO::FETCH_ASSOC);
}