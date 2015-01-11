<?php
/*This file will root each translation request from the Client-Browser*/


/*
  Send the Client-Browser a JSON of all currency multipliers
  for any language we have in the DB
*/
function languageMultipliers() {
  require_once('./models/translations.php');

  /*Collect all DB vales for currency multipliers*/
  $multipliers = getCurrencyMultipliers();

  if (!isset($multipliers)             ||
      gettype($multipliers) != 'array' ||
      !sizeof($multipliers)
  ) {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to retrieve language currency multipliers"}');
  }


  header('HTTP/1.1 200 OK');
  die( json_unicode_encode( $multipliers ));
}


/*Return a complete JSON translation for any given 'lang' GET parameter*/
function languageTranslation() {
  global $request;

  /*Validate incoming language parameter*/
  $language_name = isset( $request['url_conponents'][1] ) ? $request['url_conponents'][1] : null;
  if (gettype($language_name ) != 'string' ||
      strlen( $language_name ) < 1
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid \'language\' request property"}');
  }


  require_once('./models/translations.php');

  $translation = getLanguageTranslation( $language_name );

  if (!isset($translation)             ||
      gettype($translation) != 'array' ||
      !sizeof($translation)
  ) {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to retrieve translation properties for language \''.$language_name.'\'"}');
  }


  header('HTTP/1.1 200 OK');
  die( json_unicode_encode( $translation ));
}



/*Used when we need to add/alter already saved translation record in any language*/
function updateTranslationController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }

  $language_name = $request['url_conponents'][1];
  $data          = $request['data'];

  /*Load all translation DB-related functions*/
  require_once('./models/translations.php');

  /*Get all known to the DB translation languages*/
  $valid_language_names = getAllLanguageNames();
  if (gettype( $valid_language_names ) != 'array' ||
      !sizeof( $valid_language_names )
  ) {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load translation languages"}');
  }

  /*Be sure that all translations are in valid languages*/
  if (!in_array( $language_name, $valid_language_names )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"language \''.$language_name.'\' is not a valid translatable language"}');
  }


  /*Basic type validation*/
  if (gettype($data) != 'array' ||
      !sizeof($data)
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid translation data"}');
  }


  /*Put in a common "language => translation" form*/
  $translation = array(
    $language_name => $data
  );

  /*Check if DB model updated went perfectly*/
  if ($error_message = setTranslation( $translation )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Translation update completed successfully*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /translations
in methods:
  GET
*/
if ($request['method'] == 'GET'               &&
    sizeof( $request['url_conponents'] ) == 1 &&
    sizeof( $request['data'] )           == 0
) {
  languageMultipliers();
  return;
}



/*Validates rooting:
  /translations/:language_name
in methods:
  GET
*/
if ($request['method'] == 'GET'               &&
    sizeof( $request['url_conponents'] ) == 2 &&
    sizeof( $request['data'] )           == 0
) {
  languageTranslation();
  return;
}


/*Validates rooting:
  /translations/:language_name
in methods:
  PUT
*/
if ($request['method'] == 'PUT'               &&
    sizeof( $request['url_conponents'] ) == 2 &&
    sizeof( $request['data'] )
) {
  updateTranslationController();
  return;
}


/*No suitable Translation rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');