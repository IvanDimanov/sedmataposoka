<?php
/*This file holds all DB functions which effects Ads*/


/*Used to convert raw DB result to expected JSON-like result*/
function reorderADrecord(&$ad) {
  if (gettype($ad) !== 'array' ||
      !sizeof($ad)
  ) {
    return $ad;
  }


  /*Convert title structure 'title_*' to array*/
  $ad['title'] = array(
    'bg' => $ad['title_bg'],
    'en' => $ad['title_en']
  );

  unset( $ad['title_bg'] );
  unset( $ad['title_en'] );

  return $ad;
}



/*Returns an indexed array of all ads records we have in DB*/
function getAllAds() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved ads*/
  $query = $db->prepare('SELECT
                          ads.id        as id,
                          ads.imagePath as imagePath,
                          ads.link      as link,
                          ads.type      as type,
                          ads.startDate as startDate,
                          ads.endDate   as endDate,
                          adstitle.id   as title_id,
                          adstitle.bg   as title_bg,
                          adstitle.en   as title_en

                        FROM ads
                        JOIN adstitle
                        ON ads.titleId = adstitle.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  $ads = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($ads) === 'array' &&
      sizeof( $ads)
  ) {
    foreach ($ads as &$ad) reorderADrecord( $ad );
  }


  return $ads;
}



/*Return all the info we have for ad with ID '$id' or return 'false'*/
function getAdByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          ads.id        as id,
                          ads.imagePath as imagePath,
                          ads.link      as link,
                          ads.type      as type,
                          ads.startDate as startDate,
                          ads.endDate   as endDate,
                          adstitle.id   as title_id,
                          adstitle.bg   as title_bg,
                          adstitle.en   as title_en

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


  /*Set DB values in ordered JSON-like list*/
  return reorderADrecord( $query->fetch(PDO::FETCH_ASSOC) );
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
  $query = $db->prepare('SELECT
                          ads.id        as id,
                          ads.imagePath as imagePath,
                          ads.link      as link,
                          ads.type      as type,
                          ads.startDate as startDate,
                          ads.endDate   as endDate,
                          adstitle.id   as title_id,
                          adstitle.bg   as title_bg,
                          adstitle.en   as title_en

                         FROM ads
                         JOIN adstitle
                         ON ads.titleId = adstitle.id
                         '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  $ads = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($ads) === 'array' &&
      sizeof( $ads)
  ) {
    foreach ($ads as &$ad) reorderADrecord( $ad );
  }


  return $ads;
}



/*
  Returns a list out of '$properties' that can safely be used to create or update an Ad.
  if 2nd argument '$mandatory_validation' is set to 'true' then only  set properties will be validated,
  e.g. if 'type' is set to 'Z' the will be invalid but if not set at all, no error will be send.
*/
function validateAdProperties(&$properties, $mandatory_validation = true) {
  if (!isset( $properties) ||
      gettype($properties) !== 'array'
  ) {
    return 'Invalid properties';
  }


  if ($mandatory_validation ||
      isset($properties['title'])
  ) {
    if (!isset( $properties['title']) ||
        gettype($properties['title']) !== 'array'
    ) {
      return 'Invalid "title" property';
    }


    if ($mandatory_validation ||
        isset($properties['title']['bg'])
    ) {
      if (!isset( $properties['title']['bg']) ||
          gettype($properties['title']['bg']) !== 'string'
      ) {
        return 'Invalid "title"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['title']['en'])
    ) {
      if (!isset( $properties['title']['en']) ||
          gettype($properties['title']['en']) !== 'string'
      ) {
        return 'Invalid "title"->"en" property';
      }
    }
  }


  if ($mandatory_validation ||
      isset($properties['type'])
  ) {
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
  }


  if ($mandatory_validation ||
      isset($properties['imagePath'])
  ) {
    if (!isset( $properties['imagePath'])              ||
        gettype($properties['imagePath']) !== 'string' ||
        !sizeof($properties['imagePath'])
    ) {
      return 'Invalid "imagePath" property';
    }
  }


  if ($mandatory_validation ||
      isset($properties['link'])
  ) {
    if (!isset($properties['link']) ||
        !filter_var($properties['link'], FILTER_VALIDATE_URL)
    ) {
      return 'Invalid "link" property';
    }
  }


  $date_format = 'Y-m-d H:i:s';


  if ($mandatory_validation ||
      isset($properties['startDate'])
  ) {
    if (!isset( $properties['startDate'] ) ||
        !getValidDate( $properties['startDate'], $date_format)
    ) {
      return 'Invalid "startDate" property';
    }
  }


  if ($mandatory_validation ||
      isset($properties['endDate'])
  ) {
    if (!isset( $properties['endDate'] ) ||
        !getValidDate( $properties['endDate'], $date_format)
    ) {
      return 'Invalid "endDate" property';
    }
  }


  if (isset($properties['startDate']) &&
      isset($properties['endDate'  ])
  ) {
    $startDate = strtotime($properties['startDate']);
    $endDate   = strtotime($properties['endDate'  ]);

    if ($startDate > $endDate) {
      return 'Ad "startDate" must not be greater then its "endDate"';
    }
  }


  /*Be sure to return a list containing only valid properties as keys*/
  $valid_property_keys = array('type', 'title', 'link', 'imagePath', 'startDate', 'endDate');
  foreach ($properties as $key => $value) {
    if (!in_array($key, $valid_property_keys)) {
      unset( $properties[ $key ] );
    }

    /*Be sure to leave only valid title values*/
    if ($key === 'title' &&
        gettype($properties['title']) === 'array'
    ) {
      $valid_title_keys = array('bg', 'en');

      foreach ($properties['title'] as $title_key => $title_value) {
        if (!in_array($title_key, $valid_title_keys)) {
          unset( $properties['title'][ $title_key ] );
        }
      }
    }/*End of "if key is title"*/
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



/*
  Update the Ad with id '$ad_id' with the values from '$properties'.
  Values validation is secured with 'validateAdProperties()'
*/
function updateAd($ad_id, $properties) {

  /*Be sure we have already existing record*/
  $ad = getAdByID( $ad_id );
  if (!$ad) {
    return 'Unable to find Ad with ID: '.$ad_id;
  }


  /*This will secure the rule "startDate <= endDate" even if only one of them needs to be updated*/
  if (isset($properties['startDate']) ||
      isset($properties['endDate'  ])
  ) {
    if (!isset($properties['startDate'])) $properties['startDate'] = $ad['startDate'];
    if (!isset($properties['endDate'  ])) $properties['endDate'  ] = $ad['endDate'  ];
  }


  /*Be sure we can use all correctly set Ad properties*/
  $properties = validateAdProperties( $properties, false);
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update Ad title since its not in Ad main table*/
  if (isset(  $properties['title']) &&
      gettype($properties['title']) === 'array'
  ) {

    $set_clauses = array();
    foreach ($properties['title'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Ad title*/
    $query  = $db->prepare('UPDATE adstitle SET '.implode(', ', $set_clauses).' WHERE id='.$ad['title_id']);
    $result = $query->execute( $properties['title'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Ad title';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['title'] );
  }


  $set_clauses = array();
  foreach ($properties as $key => $value) {
    $set_clauses []= $key.'=:'.$key;
  }

  /*Try to update Ad title*/
  $query  = $db->prepare('UPDATE ads SET '.implode(', ', $set_clauses).' WHERE id='.$ad['id']);
  $result = $query->execute( $properties );

  /*Prevent further update if current query fail*/
  if (!$result) {
    return 'Unable to update Ad';
  }


  /*Return the updated record so callee can verify the process too*/
  return getAdByID( $ad['id'] );
}