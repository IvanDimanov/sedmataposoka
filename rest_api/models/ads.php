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



/*Return all the info we have for ad with ID '$id' or return 'false'*/
function getAdByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT *
                         FROM ads
                         JOIN adstitle
                         ON ads.titleId = adstitle.id
                         WHERE ads.id = :id
                         LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  /*Send the entire query result in indexed array*/
  return $query->fetch(PDO::FETCH_ASSOC);
}



/*
  Will return an error string message if the incoming '$filters'
  is not suitable for filtering search for Ads
*/
function validateAdsFilters($filters) {
  if (!isset( $filters) ||
      gettype($filters) !== 'array'
  ) {
    return 'Invalid filters';
  }


  if (isset($filters['ids'])) {
    if (gettype($filters['ids']) !== 'array') {
      return 'Filter "ids" must be an array';
    }

    foreach ($filters['ids'] as $index => $id) {
      if (!is_numeric($id) &&
          gettype($id) !== 'integer'
      ) {
        return 'Filter "ids" have an invalid ID at index '.$index;
      }
    }
  }


  if (isset($filters['types'])) {
    if (gettype($filters['types']) !== 'array') {
      return 'Filter "types" must be an array';
    }

    foreach ($filters['types'] as $index => $type) {
      if (!is_numeric($type) &&
          gettype($type) !== 'integer'
      ) {
        return 'Filter "types" have an invalid type at index '.$index;
      }
    }
  }


  if (isset($filters['title'])) {
    if (gettype($filters['title']) !== 'string' ||
        !sizeof($filters['title'])
    ) {
      return 'Filter "title" must be a non empty string';
    }
  }


  if (isset($filters['link'])) {
    if (gettype($filters['link']) !== 'string' ||
        !sizeof($filters['link'])
    ) {
      return 'Filter "link" must be a non empty string';
    }
  }


  if (isset($filters['imagePath'])) {
    if (gettype($filters['imagePath']) !== 'string' ||
        !sizeof($filters['imagePath'])
    ) {
      return 'Filter "imagePath" must be a non empty string';
    }
  }


  $date_format = 'Y-m-d H:i:s';

  if (isset($filters['fromDate'])) {
    if (!isValidateDate( $filters['fromDate'], $date_format )) {
      return 'Filter "fromDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['toDate'])) {
    if (!isValidateDate( $filters['toDate'], $date_format )) {
      return 'Filter "toDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['fromDate']) &&
      isset($filters['toDate'  ])
  ) {
    $fromDate = DateTime::createFromFormat( $date_format, $filters['fromDate']);
    $toDate   = DateTime::createFromFormat( $date_format, $filters['toDate'  ]);

    if ($fromDate > $toDate) {
      return 'Filter "fromDate" must to be greater then filter "toDate"';
    }
  }


  /*
    Return error free response
    since none of the above found an issue
  */
  return;
}



/*Returns an indexed array of all ads records we have in DB*/
function getFilteredAds($filters) {

  /*Be sure we can safely use the input for filtering*/
  if ($error_message = validateAdsFilters( $filters )) {
    return $error_message;
  }

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