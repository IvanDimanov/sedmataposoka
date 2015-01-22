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
    if (gettype($filters['fromDate']) !== 'string' ||
        !getValidDate($filters['fromDate'], $date_format)
    ) {
      return 'Filter "fromDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['toDate'])) {
    if (gettype($filters['toDate']) !== 'string' ||
        !getValidDate($filters['toDate'], $date_format)
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



/*
  Returns a list out of '$properties' that can safely be used to create or update an Ad.
*/
function validateAdProperties(&$properties, $validate_only_set_properties = false) {
  if (!isset( $properties) ||
      gettype($properties) !== 'array'
  ) {
    return 'Invalid properties';
  }


if (
  isset( ) 
) {
  Validate
}


if (
  !validate_only_set_properties
  !isset( ) ||
) {
  Validate
}


if (
  isset( ) ||
  !validate_only_set_properties
) {
  Validate
}


  if (isset($properties['title']) ||
  ) {
    if (!isset( $properties['title']) ||
        gettype($properties['title']) !== 'array'
    ) {
      return 'Invalid "title" property';
    }

    if (!isset( $properties['title']['bg']) ||
        gettype($properties['title']['bg']) !== 'string'
    ) {
      return 'Invalid "title"->"bg" property';
    }
  }


  if (!isset( $properties['title']['en']) ||
      gettype($properties['title']['en']) !== 'string'
  ) {
    return 'Invalid "title"->"en" property';
  }

  if (!isset(     $properties['type']) ||
      !is_numeric($properties['type'])
  ) {
    return 'Invalid "type" property';
  }

  $properties['type'] *= 1;
  if ($properties['type'] !== 1 &&
      $properties['type'] !== 2
  ) {
    return 'Invalid "type" property value. Valid "type" values are: 1, 2';
  }

  if (!isset( $properties['imagePath'])              ||
      gettype($properties['imagePath']) !== 'string' ||
      !sizeof($properties['imagePath'])
  ) {
    return 'Invalid "imagePath" property';
  }

  if (!isset($properties['link']) ||
      !filter_var($properties['link'], FILTER_VALIDATE_URL)
  ) {
    return 'Invalid "link" property';
  }


  $date_format = 'Y-m-d H:i:s';

  if (!isset( $properties['startDate'] ) ||
      !getValidDate( $properties['startDate'], $date_format)
  ) {
    return 'Invalid "startDate" property';
  }

  if (!isset( $properties['endDate'] ) ||
      !getValidDate( $properties['endDate'], $date_format)
  ) {
    return 'Invalid "endDate" property';
  }

  $startDate = strtotime($properties['startDate']);
  $endDate   = strtotime($properties['endDate'  ]);

  if ($startDate > $endDate) {
    return 'Ad "startDate" must not be greater then its "endDate"';
  }


  /*Be sure to return a list containing only valid properties as keys*/
  $valid_property_keys = array('type', 'title', 'link', 'imagePath', 'startDate', 'endDate');
  foreach ($properties as $key => $value) {
    if (!in_array($key, $valid_property_keys)) {
      unset( $properties[ $key ] );
    }
  }


  return $properties;
}



/*
  Use 'validateAdProperties() to save already valid Ad properties.
  Will return error {string} of failure or newly created Ad as {array}
*/
function createAd($properties) {

  /*Be sure we can use all of the user input to create new Ad*/
  $properties = validateAdProperties( $properties );
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to create Ad title first*/
  $query  = $db->prepare('insert into adstitle ( en,  bg) values
                                               (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['title']['en'],
    'bg' => $properties['title']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Ad title';
  }


  /*Set reference to already saved title property*/
  unset( $properties['title'] );
  $properties['titleId'] = $db->lastInsertId();

  /*Try to create Ad in its main table*/
  $query  = $db->prepare('insert into ads ( imagePath,  link,  titleId,  type,  startDate,  endDate) values
                                          (:imagePath, :link, :titleId, :type, :startDate, :endDate)');
  $result = $query->execute( $properties );

  if (!$result) {
    return 'Unable to create Ad';
  }


  /*Try to show the creation result*/
  return getAdByID( $db->lastInsertId() );
}