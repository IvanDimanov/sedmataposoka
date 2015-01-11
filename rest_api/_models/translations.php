<?php
/*
  This file holds functions that will bring
  Translation values from the DB to any callee
*/



/*
  Returns an indexed list of all available languages as strings.
  Returns 'false' on any occurred error.
*/
function getAllLanguageNames() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved types*/
  $query = $db->prepare('SELECT DISTINCT lang FROM translation');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  /*Be sure we have a correct/expected result from our DB*/
  $language_names = $query->fetchAll(PDO::FETCH_ASSOC);
  if (gettype( $language_names ) != 'array' ||
      !sizeof( $language_names )
  ) {
    return;
  }


  /*Convert the somehow associative DB result to an index array in '$result'*/
  $result = array();
  foreach ($language_names as $language_name) {
    $result[] = $language_name['lang'];
  }

  return $result;
}



/*
  Will bring from the DB all currency multipliers
  in JSON where keys are language names and values are the multiplier number
*/
function getCurrencyMultipliers() {
  $multipliers = array(
    'langs'   => array(),
    'default' => ''
  );


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Loop through all JSON valid language records*/
  foreach ($db->query('SELECT * FROM translation') as $row) {

    /*
      Safely remove known chars that may
      bring trouble while parsing with 'json_decode()'
    */
    $row['json_file'] = str_replace("\n", '', $row['json_file']);
    $translation = json_decode( $row['json_file'], true);

    if (gettype($translation) != 'NULL'   &&
        isset($translation['multiplier']) &&
        is_numeric($translation['multiplier'])
    ) {
      $multipliers['langs'][ $row['lang'] ] = $translation['multiplier'];

      /*Any language with multiplier of 1 is the default language*/
      if ($translation['multiplier'] === 1) {
        $multipliers['default'] = $row['lang'];
      }
    }
  }


  /*Leave no loose end*/
  deleteDBConnection($db);

  return $multipliers;
}



/*
  Returns a valid JSON (if possible) to all translation records
  we have in DB for the incoming '$language_name'
*/
function getLanguageTranslation($language_name) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $translation_query = $db->prepare('SELECT json_file FROM translation WHERE lang = :language_name LIMIT 1');
  $translation_query->execute(array('language_name' => $language_name));


  /*
    Safely remove known chars that may
    bring trouble while parsing with 'json_decode()'
  */
  $translation_string = $translation_query->fetch(PDO::FETCH_ASSOC);
  $translation_string = $translation_string['json_file'];
  $translation_string = str_replace("\n", '', $translation_string);
  $translation        = json_decode( $translation_string, true);

  /*Leave no loose end*/
  deleteDBConnection($db);

  return $translation;
}



/*
  This function is a common place where any '$translations'
  will be checked if it has the correct schema as the translation in our DB.
  For example, a valid '$translations' is
    $translations = Array(
      [bg] => Array (
        [categories] => Array (
          [furniture] => Мебели
         )
       )
      [en] => Array (
        [categories] => Array (
          [furniture] => Furniture
         )
       )
     );

  In any case of invalid input
  the function will return an error string message,
  otherwise 'null'.
*/
function validateTranslations($translations) {

  /*Basic input validation*/
  if (gettype($translations) != 'array' ||
      !sizeof($translations)
  ) {
    return 'translation variable must be an Array of all language translations';
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Know that languages that the translation will be saved*/
  $language_names  = array_keys( $translations );
  $total_languages = sizeof( $language_names );

  /*Prepare the query which will have the exact number of languages as the incoming translation*/
  $bind_values = array_fill(0, $total_languages, '?');
  $bind_values = implode(', ', $bind_values);
  $query       = $db->prepare('SELECT count(lang) as total_languages FROM translation WHERE lang IN ('.$bind_values.')');

  /*Bind each language "?" variable with a language name value*/
  $bind_value_id = 1;
  foreach ($language_names as $language_name) {
    $query->bindValue( $bind_value_id++, $language_name );
  }


  /*Check if we can execute the query in the DB*/
  if (!$query->execute()) {
    return 'Unable to set translation in DB';
  }

  /*Be sure that we support all requested languages in DB*/
  $result = $query->fetch(PDO::FETCH_ASSOC);
  if ($result['total_languages'] != $total_languages) {
    return 'Not all '.$total_languages.'languages are available in DB';
  }


  /*Exit with no validation errors*/
  return null;
}



/*
  Merge the content of '$translations' with the current translation values in the DB.
  For example,
    $translations = Array(
      [bg] => Array (
        [categories] => Array (
          [furniture] => Мебели
         )
       )
      [en] => Array (
        [categories] => Array (
          [furniture] => Furniture
         )
       )
     );

  will find the 'categories' property in the specified languages and
  will add the key-value pair "[furniture] => XXXXX" into it.

  Please note that already existing translation information will be overwritten.
*/
function setTranslation($translations) {

  /*
    Use a common validator to be sure that '$translations'
    can be used with the current translation DB schema
  */
  if ($error_message = validateTranslations( $translations )) {
    return $error_message;
  }


  /*
    If we update several language translations
    we need to confirm that all languages have the same translation keys
  */
  if (sizeof( $translations ) > 1) {

    /*Collect all translations where there language is not mentioned*/
    $translations_comparison = array();
    foreach ($translations as $translation) {
      array_push( $translations_comparison, $translation );
    }

    /*Check if there's a translation key that is mentioned in one language but missed in the others*/
    $translation_keys_difference = call_user_func_array('array_diff_key', $translations_comparison);
    if (sizeof( $translation_keys_difference )) {

      /*Collect all keys where translation is missing for some language*/
      $different_keys = array();
      foreach ($translation_keys_difference as $key => $value) {
        array_push( $different_keys, $key );
      }

      return 'Translation in "'.implode(', ', $different_keys).'" was not found in all languages';
    }
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*
    Goes through all currently saved in DB translations
    in order to merge them with '$translations',
    validate them and save them back in DB
  */
  foreach ($db->query('SELECT * FROM translation') as $row) {
    $language_name = $row['lang'];

    /*
      Skip adding values for languages that exists in the DB
      but were not set in '$translations'
    */
    if (!isset( $translations[ $language_name ] )) {
      continue;
    }


    $add_translation = $translations[ $language_name ];
    $db_translation  = json_decode( $row['json_file'], true );

    /*Validate current DB value*/
    if (gettype($db_translation) != 'array') {
      return 'Unable to load DB translation for language '.$language_name;
    }


    /*Set new translation information the in the existing DB record*/
    /*Note that '$add_translation' will add AND overwrite matching properties in '$db_translation'*/
    $db_translation = array_merge_recursive_distinct( $db_translation, $add_translation );


    /*Save the updated translation record back in DB*/
    $query  = $db->prepare('UPDATE translation SET json_file=:db_translation WHERE lang=:language_name');
    $result = $query->execute(array(
      'language_name'  => $language_name,
      'db_translation' => json_unicode_encode( $db_translation )
    ));

    if (!$result) {
      return 'Unable to set translation back in DB';
    }
  }
}



/*
  Returns translations that matches the income argument schema.
  For example,
    $translation_schema = array(
      'tech-params' => array(
        'ram' => null
      )
    );

  The outcome result will be
    array(
      'bg' => array(
        'name'  => 'Динамична Памет',
        'units' => 'GBs'
       )
      'en' => array(
        'name'  => 'RAM',
        'units' => 'GBs'
       )
     ) 

  if we have enabled '$return_schema === true'
  The outcome result will be
    array(
      'bg' => array(
        'tech-params' => array(
          'ram' => array(
            'name' => 'Динамична Памет',
            'units' => 'GBs'
           )
         )
       )
      'en' => array(
        'tech-params' => array(
          'ram' => array(
            'name' => 'RAM',
            'units' => 'GBs'
           )
         )
       )
     )
*/
function getTranslation($translation_schema, $return_schema = false) {
  if (gettype($translation_schema) != 'array' ||
      !sizeof($translation_schema)
  ) {
    return '1st argument "$translation_schema" must be a non-empty array';
  }


  /*
    Tells if the result should implement the entire translation schema or
    just the matched translations
  */
  if (!isset($return_schema) ||
    $return_schema !== true
  ) {
    $return_schema = false;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Collect translations through all languages*/
  $translations = array();
  foreach ($db->query('SELECT * FROM translation') as $row) {
    $language_name  = $row['lang'];
    $db_translation = json_decode( $row['json_file'], true );

    /*Validate current DB value*/
    if (gettype($db_translation) != 'array') {
      return 'Unable to load DB translation for language '.$language_name;
    }


    /*Binding correct iteration arguments*/
    $search_db_translation     = &$db_translation;
    $search_translation_schema = $translation_schema;

    $translations[ $language_name ] = array();

    if ($return_schema) {
      $translation = &$translations[ $language_name ];
    } else {
      $translation = array();
    }


    /*
      Goes through each translation level to check
      if it matches the income '$translation_schema' and
      if so, we'll save all of the DB translation record for it
    */
    do {
      $key = key($search_translation_schema);

      /*Determine returned result form: full schema or just the found value*/
      if (gettype($search_translation_schema[ $key ]) != 'array') {
        if ($return_schema) {
          $translation[ $key ] = $search_db_translation[ $key ];
        } else {
          $translations[ $language_name ] = isset($search_db_translation[ $key ]) ? $search_db_translation[ $key ] : null;
        }

        break;
      }


      /*Take the search in one level deeper*/
      $translation[ $key ] = array();
      $translation         = &$translation[ $key ];

      /*Take the next in-deep level*/
      $search_translation_schema = &$search_translation_schema[ $key ];
      $search_db_translation     = &$search_db_translation    [ $key ];

    } while (
      gettype($search_translation_schema) == 'array' &&
      gettype($search_db_translation)     == 'array'
    );
  }


  return $translations;
}



/*
  Finds the exact property in the DB, mentioned in '$translations'
  and tries to remove it
*/
function deleteTranslation($translations) {

  /*Be sure we can use '$translations' safely*/
  if ($error_message = validateTranslations( $translations )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Know that languages we're about to delete*/
  $language_names  = array_keys( $translations );
  $total_languages = sizeof( $language_names );

  /*Prepare the query which will have the exact languages as the incoming translation*/
  $bind_values = array_fill(0, $total_languages, '?');
  $bind_values = implode(', ', $bind_values);
  $query       = $db->prepare('SELECT lang, json_file FROM translation WHERE lang IN ('.$bind_values.')');

  /*Bind each language "?" variable with a language name value*/
  $bind_value_id = 1;
  foreach ($language_names as $language_name) {
    $query->bindValue( $bind_value_id++, $language_name );
  }


  /*Check if we can execute the query in the DB*/
  if (!$query->execute()) {
    return 'Unable to set translation in DB';
  }


  /*
    Go over all translations in every DB language
    till we remove the exact property set in '$translations'
    in the iteratable language.
  */
  $db_translations = $query->fetchAll(PDO::FETCH_ASSOC);
  foreach ($db_translations as &$db_translation_row) {
    $language_name         = $db_translation_row['lang'];
    $translation           = $translations[ $language_name ];
    $db_translation        = json_decode( $db_translation_row['json_file'], true);
    $delete_db_translation = &$db_translation;

    /*
      Goes over all in-deep levels of '$translation' and '$delete_db_translation' simultaneity
      till we finally reach the property from '$translation' that needs to be removed from '$delete_db_translation'.
    */
    do {
      $key = key($translation);

      /*
        Check if we finally found the level where we cannot go more in-deep.
        This is the level we need to remove the variable '$key' from the DB record.
      */
      if (gettype($translation[ $key ]) != 'array') {
        unset( $delete_db_translation[ $key ] );
        break;
      }


      /*Take the next in-deep level*/
      $translation           = &$translation          [ $key ];
      $delete_db_translation = &$delete_db_translation[ $key ];

    } while (
      gettype($translation          ) == 'array' &&
      gettype($delete_db_translation) == 'array'
    );


    /*Try to save back the updated translation lists per each iterated language*/
    $query  = $db->prepare('UPDATE translation SET json_file=:db_translation WHERE lang=:language_name');
    $result = $query->execute(array(
      'language_name'  => $language_name,
      'db_translation' => json_unicode_encode( $db_translation )
    ));

    if (!$result) {
      return 'Save to deleted translation in language "'.$language_name.'"';
    }
  }


  /*Translation was deleted with no issues*/
  return null;
}