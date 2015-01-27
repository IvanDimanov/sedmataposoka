<?php
/*This file holds all DB functions which effects Events*/


/*Used to convert raw DB result to expected JSON-like result*/
function reorderEventRecord(&$event) {
  if (gettype($event) !== 'array' ||
      !sizeof($event)
  ) {
    return $event;
  }


  /*Convert name structure 'name_*' to array*/
  $event['name'] = array(
    'bg' => $event['name_bg'],
    'en' => $event['name_en']
  );

  unset( $event['name_bg'] );
  unset( $event['name_en'] );


  /*Convert description structure 'description_*' to array*/
  $event['description'] = array(
    'bg' => $event['description_bg'],
    'en' => $event['description_en']
  );

  unset( $event['description_bg'] );
  unset( $event['description_en'] );

  return $event;
}



/*Return all the info we have for Event with ID '$id' or return 'false'*/
function getEventByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          event.id            as id,
                          event.subcategoryId as subcategoryId,
                          event.link          as link,
                          event.fee           as fee,
                          event.startDate     as startDate,
                          event.endDate       as endDate,

                          eventname.id as name_id,
                          eventname.bg as name_bg,
                          eventname.en as name_en,

                          eventdescription.id as description_id,
                          eventdescription.bg as description_bg,
                          eventdescription.en as description_en

                        FROM event

                        JOIN eventname
                        ON event.nameId = eventname.id

                        JOIN eventdescription
                        ON event.descriptionId = eventdescription.id

                        WHERE event.id = :id
                        LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  /*Set DB values in ordered JSON-like list*/
  return reorderEventRecord( $query->fetch(PDO::FETCH_ASSOC) );
}



/*Return all Events that are subs of Subcategory with ID '$subcategoryId'*/
function getEventsBySubcategoryID($subcategoryId) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          event.id            as id,
                          event.subcategoryId as subcategoryId,
                          event.link          as link,
                          event.fee           as fee,
                          event.startDate     as startDate,
                          event.endDate       as endDate,

                          eventname.id as name_id,
                          eventname.bg as name_bg,
                          eventname.en as name_en,

                          eventdescription.id as description_id,
                          eventdescription.bg as description_bg,
                          eventdescription.en as description_en

                        FROM event

                        JOIN eventname
                        ON event.nameId = eventname.id

                        JOIN eventdescription
                        ON event.descriptionId = eventdescription.id

                        WHERE event.subcategoryId = :subcategoryId');
  $result = $query->execute(array(
    'subcategoryId' => $subcategoryId
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  $events = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($events) === 'array' &&
      sizeof( $events)
  ) {
    foreach ($events as &$event) reorderEventRecord( $event );
  }


  return $events;
}



/*Returns an indexed array of all Events records we have in DB*/
function getAllEvents() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved Events*/
  $query = $db->prepare('SELECT
                          event.id            as id,
                          event.subcategoryId as subcategoryId,
                          event.link          as link,
                          event.fee           as fee,
                          event.startDate     as startDate,
                          event.endDate       as endDate,

                          eventname.id as name_id,
                          eventname.bg as name_bg,
                          eventname.en as name_en,

                          eventdescription.id as description_id,
                          eventdescription.bg as description_bg,
                          eventdescription.en as description_en

                        FROM event

                        JOIN eventname
                        ON event.nameId = eventname.id

                        JOIN eventdescription
                        ON event.descriptionId = eventdescription.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  $events = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($events) === 'array' &&
      sizeof( $events)
  ) {
    foreach ($events as &$event) reorderEventRecord( $event );
  }


  return $events;
}



/*
  Will return an error string message if the incoming '$filters'
  are not suitable for filtering search for Events.
  Please be advised that keys in '$filters' that are not used
  for filtering will be deleted from '$filters'.
*/
function validateEventsFilters(&$filters) {
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


  if (isset($filters['subcategoryIds'])) {
    if (gettype($filters['subcategoryIds']) !== 'array') {
      return 'Filter "subcategoryIds" must be an array';
    }

    foreach ($filters['subcategoryIds'] as $index => $id) {
      if (!is_numeric($id) &&
          gettype($id) !== 'integer'
      ) {
        return 'Filter "subcategoryIds" have an invalid ID at index '.$index;
      }
    }
  }


  if (isset($filters['name'])) {
    if (gettype($filters['name']) !== 'string' ||
        !sizeof($filters['name'])
    ) {
      return 'Filter "name" must be a non empty string';
    }
  }


  if (isset($filters['description'])) {
    if (gettype($filters['description']) !== 'string' ||
        !sizeof($filters['description'])
    ) {
      return 'Filter "description" must be a non empty string';
    }
  }


  if (isset($filters['link'])) {
    if (gettype($filters['link']) !== 'string' ||
        !sizeof($filters['link'])
    ) {
      return 'Filter "link" must be a non empty string';
    }
  }


  if (isset($filters['fee'])) {
    if (gettype($filters['fee']) !== 'string' ||
        !sizeof($filters['fee'])
    ) {
      return 'Filter "fee" must be a non empty string';
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
  $valid_filtering_keys = array('ids', 'subcategoryIds', 'name', 'description', 'link', 'fee', 'fromDate', 'toDate');
  foreach ($filters as $key => $value) {
    if (!in_array($key, $valid_filtering_keys, true)) {
      unset( $filters[ $key ] );
    }
  }


  /*
    Return error free response
    since none of the above found an issue
  */
  return;
}



/*Returns an indexed array of all Events records we have in DB*/
function getFilteredEvents($filters) {

  /*Be sure we can safely use the input for filtering*/
  if ($error_message = validateEventsFilters( $filters )) {
    return $error_message;
  }

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $where_clauses = array();

  if (isset($filters['ids'])) {
    $where_clauses []= 'event.id IN ('.implode(', ', $filters['ids']).')';
    unset( $filters['ids'] );
  }

  if (isset($filters['subcategoryIds'])) {
    $where_clauses []= 'event.subcategoryId IN ('.implode(', ', $filters['subcategoryIds']).')';
    unset( $filters['subcategoryIds'] );
  }

  if (isset($filters['name'])) {
    $where_clauses []= '(eventname.bg LIKE :name OR
                         eventname.en LIKE :name)';
    $filters['name'] = '%'.$filters['name'].'%';
  }

  if (isset($filters['description'])) {
    $where_clauses []= '(eventdescription.bg LIKE :description OR
                         eventdescription.en LIKE :description)';
    $filters['description'] = '%'.$filters['description'].'%';
  }

  if (isset($filters['link'])) {
    $where_clauses []= 'event.link LIKE :link';
    $filters['link'] = '%'.$filters['link'].'%';
  }

  if (isset($filters['fee'])) {
    $where_clauses []= 'event.fee LIKE :fee';
    $filters['fee'] = '%'.$filters['fee'].'%';
  }

  if (isset($filters['fromDate'])) {
    $where_clauses []= '(event.startDate >= :fromDate OR (
                          event.startDate <= :fromDate AND
                          event.endDate   > :fromDate
                        ))';
  }

  if (isset($filters['toDate'])) {
    $where_clauses []= '(event.endDate <= :toDate OR (
                          event.startDate <= :toDate AND
                          event.endDate   >  :toDate
                        ))';
  }


  /*Filter the Events by the saved rules in '$where_clauses'*/
  $query = $db->prepare('SELECT
                          event.id            as id,
                          event.subcategoryId as subcategoryId,
                          event.link          as link,
                          event.fee           as fee,
                          event.startDate     as startDate,
                          event.endDate       as endDate,

                          eventname.id as name_id,
                          eventname.bg as name_bg,
                          eventname.en as name_en,

                          eventdescription.id as description_id,
                          eventdescription.bg as description_bg,
                          eventdescription.en as description_en

                        FROM event

                        JOIN eventname
                        ON event.nameId = eventname.id

                        JOIN eventdescription
                        ON event.descriptionId = eventdescription.id
                        '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  $events = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($events) === 'array' &&
      sizeof( $events)
  ) {
    foreach ($events as &$event) reorderEventRecord( $event );
  }


  return $events;
}



/*
  Returns a list out of '$properties' that can safely be used to create or update a Event.
  if 2nd argument '$mandatory_validation' is set to 'true' then only set properties will be validated,
  e.g. if 'type' is set to 'Z' it will be invalid but if not set at all, no error will be send.
*/
function validateEventProperties(&$properties, $mandatory_validation = true) {
  if (!isset( $properties) ||
      gettype($properties) !== 'array'
  ) {
    return 'Invalid properties';
  }


  if ($mandatory_validation ||
      isset($properties['subcategoryId'])
  ) {
    if (!isset(     $properties['subcategoryId']) ||
        !is_numeric($properties['subcategoryId'])
    ) {
      return 'Invalid "subcategoryId" property';
    }


    /*Be sure we have a Subcategory with the requested ID*/
    require_once('./models/subcategories.php');
    $subcategoty = getSubcategoryByID( $properties['subcategoryId'] );

    if (gettype($subcategoty) !== 'array') {
      return 'Invalid "subcategoryId" property value.';
    }
  }


  if ($mandatory_validation ||
      isset($properties['name'])
  ) {
    if (!isset( $properties['name']) ||
        gettype($properties['name']) !== 'array'
    ) {
      return 'Invalid "name" property';
    }


    if ($mandatory_validation ||
        isset($properties['name']['bg'])
    ) {
      if (!isset( $properties['name']['bg']) ||
          gettype($properties['name']['bg']) !== 'string'
      ) {
        return 'Invalid "name"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['name']['en'])
    ) {
      if (!isset( $properties['name']['en']) ||
          gettype($properties['name']['en']) !== 'string'
      ) {
        return 'Invalid "name"->"en" property';
      }
    }
  }


  if ($mandatory_validation ||
      isset($properties['description'])
  ) {
    if (!isset( $properties['description']) ||
        gettype($properties['description']) !== 'array'
    ) {
      return 'Invalid "description" property';
    }


    if ($mandatory_validation ||
        isset($properties['description']['bg'])
    ) {
      if (!isset( $properties['description']['bg']) ||
          gettype($properties['description']['bg']) !== 'string'
      ) {
        return 'Invalid "description"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['description']['en'])
    ) {
      if (!isset( $properties['description']['en']) ||
          gettype($properties['description']['en']) !== 'string'
      ) {
        return 'Invalid "description"->"en" property';
      }
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


  if ($mandatory_validation ||
      isset($properties['fee'])
  ) {
    if (!isset( $properties['fee']) ||
        gettype($properties['fee']) !== 'string'
    ) {
      return 'Invalid "fee" property';
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
      return 'Event "startDate" must not be greater then its "endDate"';
    }
  }


  /*Be sure to return a list containing only valid properties as keys*/
  $valid_property_keys = array('subcategoryId', 'name', 'description', 'link', 'fee', 'startDate', 'endDate');
  foreach ($properties as $key => $value) {
    if (!in_array($key, $valid_property_keys, true)) {
      unset( $properties[ $key ] );
    }

    /*Be sure to leave only valid name values*/
    if ($key === 'name' &&
        gettype($properties['name']) === 'array'
    ) {
      $valid_name_keys = array('bg', 'en');

      foreach ($properties['name'] as $name_key => $name_value) {
        if (!in_array($name_key, $valid_name_keys, true)) {
          unset( $properties['name'][ $name_key ] );
        }
      }
    }/*End of "if key is name"*/


    /*Be sure to leave only valid description values*/
    if ($key === 'description' &&
        gettype($properties['description']) === 'array'
    ) {
      $valid_description_keys = array('bg', 'en');

      foreach ($properties['description'] as $description_key => $description_value) {
        if (!in_array($description_key, $valid_description_keys, true)) {
          unset( $properties['description'][ $description_key ] );
        }
      }
    }/*End of "if key is description"*/
  }


  if (isset(   $properties['name'] ) &&
      !sizeof( $properties['name'] )
  ) {
    unset( $properties['name'] );
  }

  if (isset(   $properties['description'] ) &&
      !sizeof( $properties['description'] )
  ) {
    unset( $properties['description'] );
  }


  return $properties;
}



/*
  Use 'validateEventProperties() to save already valid Event values.
  Will return error {string} in case of failure or newly created Event as {array}
*/
function createEvent($properties) {

  /*Be sure we can use all of the user input to create new Event*/
  $properties = validateEventProperties( $properties );
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Try to create Event name first in order to use the Name ID later
    when we create the Event in its main table
  */
  $query  = $db->prepare('INSERT INTO eventname ( en,  bg) VALUES
                                                (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['name']['en'],
    'bg' => $properties['name']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Event name';
  }


  /*Set reference ID to already saved name property*/
  unset( $properties['name'] );
  $properties['nameId'] = $db->lastInsertId();


  /*
    Try to create Event name first in order to use the Description ID later
    when we create the Event in its main table
  */
  $query  = $db->prepare('INSERT INTO eventdescription ( en,  bg) VALUES
                                                       (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['description']['en'],
    'bg' => $properties['description']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Event description';
  }


  /*Set reference ID to already saved description property*/
  unset( $properties['description'] );
  $properties['descriptionId'] = $db->lastInsertId();


  /*Try to create Event in its main table*/
  $query  = $db->prepare('INSERT INTO event ( subcategoryId,  nameId,  descriptionId,  link,  fee,  startDate,  endDate) VALUES
                                            (:subcategoryId, :nameId, :descriptionId, :link, :fee, :startDate, :endDate)');
  $result = $query->execute( $properties );

  if (!$result) {
    return 'Unable to create Event';
  }


  /*Try to show the creation result*/
  return getEventByID( $db->lastInsertId() );
}



/*
  Update the Event with ID '$event_id' with the values from '$properties'.
  Values validation is secured with 'validateEventProperties()'
*/
function updateEvent($event_id, $properties) {

  /*Be sure we have already existing record*/
  $event = getEventByID( $event_id );
  if (!$event) {
    return 'Unable to find Event with ID: '.$event_id;
  }


  /*This will secure the rule "startDate <= endDate" even if only date needs to be updated*/
  if (isset($properties['startDate']) ||
      isset($properties['endDate'  ])
  ) {
    if (!isset($properties['startDate'])) $properties['startDate'] = $event['startDate'];
    if (!isset($properties['endDate'  ])) $properties['endDate'  ] = $event['endDate'  ];
  }


  /*Be sure we can use only correctly set Event properties*/
  $properties = validateEventProperties( $properties, false);
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update Event name since its not in Event main table*/
  if (isset(  $properties['name'])             &&
      gettype($properties['name']) === 'array' &&
      sizeof( $properties['name'])
  ) {

    $set_clauses = array();
    foreach ($properties['name'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Event name*/
    $query  = $db->prepare('UPDATE eventname SET '.implode(', ', $set_clauses).' WHERE id='.$event['name_id']);
    $result = $query->execute( $properties['name'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Event name';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['name'] );
  }


  /*Check if we need to update Event description since its not in Event main table*/
  if (isset(  $properties['description'])             &&
      gettype($properties['description']) === 'array' &&
      sizeof( $properties['description'])
  ) {

    $set_clauses = array();
    foreach ($properties['description'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Event description*/
    $query  = $db->prepare('UPDATE eventdescription SET '.implode(', ', $set_clauses).' WHERE id='.$event['description_id']);
    $result = $query->execute( $properties['description'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Event description';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['description'] );
  }


  /*Check if there are properties to be updated in the main table*/
  if (gettype($properties) === 'array' &&
      sizeof( $properties)
  ) {
    $set_clauses = array();
    foreach ($properties as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Event in its main table*/
    $query  = $db->prepare('UPDATE event SET '.implode(', ', $set_clauses).' WHERE id='.$event['id']);
    $result = $query->execute( $properties );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Event';
    }
  }


  /*Return the updated record so callee can verify the process too*/
  return getEventByID( $event['id'] );
}



/*
  Tries to remove all known records for Event with ID '$event_id'.
  Returns an error {string} or {boolean} 'false'.
*/
function deleteEvent($event_id) {

  /*Be sure we have already existing record*/
  $event = getEventByID( $event_id );
  if (gettype($event) !== 'array') {
    return 'Unable to find Event with ID: '.$event_id;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to delete Event name first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM eventname WHERE id=:id');
  $result = $query->execute(array('id' => $event['name_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Event name';
  }


  /*Try to delete Event description first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM eventdescription WHERE id=:id');
  $result = $query->execute(array('id' => $event['description_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Event description';
  }


  $query  = $db->prepare('DELETE FROM event WHERE id=:id');
  $result = $query->execute(array('id' => $event['id']));

  if (!$result) {
    return 'Unable to delete Event from its main table';
  }


  /*Be sure no one can get the deleted information*/
  $deleted_event = getEventByID( $event['id'] );
  if (gettype($deleted_event) === 'array') {
    return 'Unable to delete Event';
  }

  return;
}