<?php
/*This file holds all DB functions which effects Ads*/


/*Returns an indexed array of all ads records we have in DB*/
function getAllAds() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved ads*/
  $query = $db->prepare('SELECT *
                         FROM ads
                         JOIN adstitle
                         ON ads.titleId = adstitle.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  /*Send the entire query result in indexed array*/
  return $query->fetchAll(PDO::FETCH_ASSOC);
}