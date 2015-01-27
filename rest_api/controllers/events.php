<?php
/*This file holds the entire rooting of all Events client requests*/


/*Try to printout a specified by ID Event*/
function showEvent() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/events.php');

  $event_id = $request['url_conponents'][1];
  $event    = getEventByID( $event_id );

  if (gettype($event) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Event with ID: '.$event_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $event ));
}



/*Shows all saved Events taken from the DB*/
function showAllEvents() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/events.php');

  $events = getAllEvents();
  if (gettype($events) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored Events"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $events ));
}



/*Tries to find all Events, matches sent filters*/
function showFilteredEvents() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/events.php');

  $filters = $request['data'];
  if ($error_message = validateEventsFilters( $filters )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  $events = getFilteredEvents( $filters );
  if (gettype($events) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $events).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $events ));
}



/*Attempt to create new Event using user data input*/
function createEventController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/events.php');

  /*Use DB model function to determine if the input can be correctly used*/
  $event_properties = validateEventProperties( $request['data'] );
  if (gettype($event_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $event_properties).'"}');
  }

  /*Try to create an Event with validated properties*/
  $new_event = createEvent( $event_properties );
  if (gettype($new_event) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $new_event).'"}');
  }


  /*Return new successfully created Event*/
  header('HTTP/1.1 201 Created');
  die(json_unicode_encode( $new_event ));
}



/*
  Ask the model to update Event with ID sent as 1st and only URL parameter.
  Will print a possible update error or the full updated Event on success.
*/
function updateEventController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/events.php');


  /*Be sure we try to update an existing Event*/
  $event_id = $request['url_conponents'][1];
  $event    = getEventByID( $event_id );
  if (gettype($event) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Event with ID: '.$event_id.'"}');
  }


  /*Use DB model function to determine if the input can be correctly used*/
  $event_properties = validateEventProperties( $request['data'], false);
  if (gettype($event_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $event_properties).'"}');
  }


  /*Try to update the Event with validated properties*/
  $updated_event = updateEvent( $event_id, $event_properties );
  if (gettype($updated_event) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_event).'"}');
  }


  /*Return the successfully updated Event*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_event ));
}



/*
  Asks the DB model to remove all records for the Event
  referenced with its ID as 1st and only URL parameter
*/
function deleteEventController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/events.php');

  $event_id = $request['url_conponents'][1];
  $event    = getEventByID( $event_id );

  if (gettype($event) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Event with ID: '.$event_id.'"}');
  }


  if ($error_message = deleteEvent( $event['id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /events/:event_id
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 2        &&
    $request['url_conponents'][0]        === 'events' &&
    $request['method']                   === 'GET'
) {
  showEvent();
  return;
}


/*Validates rooting:
  /events
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1        &&
    $request['url_conponents'][0]        === 'events' &&
    $request['method']                   === 'GET'
) {

  /*Check if we need to show some or all Events from the DB*/
  if (sizeof( $request['data'] )) {
    showFilteredEvents();
  } else {
    showAllEvents();
  }
  return;
}


/*Validates rooting:
  /events
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 1        &&
    $request['url_conponents'][0]        === 'events' &&
    $request['method']                   === 'POST'   &&
    sizeof( $request['data'] )
) {
  createEventController();
  return;
}


/*Validates rooting:
  /events/:event_id
in methods:
  PUT
*/
if (sizeof( $request['url_conponents'] ) === 2        &&
    $request['url_conponents'][0]        === 'events' &&
    $request['method']                   === 'PUT'    &&
    sizeof( $request['data'] )
) {
  updateEventController();
  return;
}


/*Validates rooting:
  /events/:event_id
in methods:
  DELETE
*/
if (sizeof( $request['url_conponents'] ) === 2        &&
    $request['url_conponents'][0]        === 'events' &&
    $request['method']                   === 'DELETE'
) {
  deleteEventController();
  return;
}


/*No suitable Events rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');