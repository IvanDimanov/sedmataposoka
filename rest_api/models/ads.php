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
  is not suitable for filtering search for Ads.
  Please be advised that keys in '$filters' that are not used
  for filtering will be deleted from '$filters'.
*/
function validateAdsFilters(&$filters) {
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
    if (gettype(   $filters['fromDate']) !== 'string' ||
        !strtotime($filters['fromDate'])
    ) {
      return 'Filter "fromDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['toDate'])) {
    if (gettype(   $filters['toDate']) !== 'string' ||
        !strtotime($filters['toDate'])
    ) {
      return 'Filter "toDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['fromDate']) &&
      isset($filters['toDate'  ])
  ) {
    $fromDate = strtotime($filters['fromDate']);
    $toDate   = strtotime($filters['toDate'  ]);

    if ($fromDate > $toDate) {
      return 'Filter "fromDate" must not be greater then filter "toDate"';
    }
  }


  /*Be sure to return a list containing only valid filters as keys*/
  $valid_filtering_keys = array('ids', 'types', 'title', 'link', 'imagePath', 'fromDate', 'toDate');
  foreach ($filters as $key => $value) {
    if (!in_array($key, $valid_filtering_keys)) {
      unset( $filters[ $key ] );
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


  $where_clauses = array();

  if (isset($filters['ids'])) {
    $where_clauses []= 'ads.id in ('.implode(', ', $filters['ids']).')';
    unset( $filters['ids'] );
  }

  if (isset($filters['types'])) {
    $where_clauses []= 'ads.type in ('.implode(', ', $filters['types']).')';
    unset( $filters['types'] );
  }

  if (isset($filters['title'])) {
    $where_clauses []= '(adstitle.bg like :title or
                         adstitle.en like :title)';
    $filters['title'] = '%'.$filters['title'].'%';
  }

  if (isset($filters['link'])) {
    $where_clauses []= 'ads.link like :link';
    $filters['link'] = '%'.$filters['link'].'%';
  }

  if (isset($filters['imagePath'])) {
    $where_clauses []= 'ads.imagePath like :imagePath';
    $filters['imagePath'] = '%'.$filters['imagePath'].'%';
  }

  if (isset($filters['fromDate'])) {
    $where_clauses []= '(ads.startDate >= :fromDate or (
                          ads.startDate <= :fromDate and
                          ads.endDate   > :fromDate
                        ))';
  }

  if (isset($filters['toDate'])) {
    $where_clauses []= '(ads.endDate <= :toDate or (
                          ads.startDate <= :toDate and
                          ads.endDate   >  :toDate
                        ))';
  }


  /*Gets a list of all currently saved ads*/
  $query = $db->prepare('SELECT *
                         FROM ads
                         JOIN adstitle
                         ON ads.titleId = adstitle.id
                         '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  /*Send the entire query result in indexed array*/
  return $query->fetchAll(PDO::FETCH_ASSOC);
}