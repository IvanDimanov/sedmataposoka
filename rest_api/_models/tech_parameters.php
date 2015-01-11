<?php
/*
  This file holds functions that will return all product/category technical parameters, and
  functions that will CRUD them.
*/


/*
  Returns an indexed array of all technical parameters from the DB.
  Returns 'false' on any type of error.
*/
function getAllTechParams() {
  require_once('./models/translations.php');

  /*All known translations for out technical parameters*/
  $translations = getTranslation(array('tech-params' => null));


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*
    Get all records from the main table
    that will be merged later with all '$translations'
  */
  $query = $db->prepare('SELECT name_translate_key as id, units
                         FROM tech_param
  ');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  /*Combine all tech translations with all records from the DB*/
  $tech_params = $query->fetchAll(PDO::FETCH_ASSOC);
  foreach ($translations as $language_name => $translation) {
    
    foreach ($tech_params as &$tech_param) {
      if (!isset( $tech_param['name']) ||
          gettype($tech_param['name']) != 'array'
      ) {
        $tech_param['name'] = array();
      }

      /*Be sure that every parameter in DB have its translation*/
      if (isset($translation[ $tech_param['id'] ])) {
        $tech_param['name'][ $language_name ] = $translation[ $tech_param['id'] ]['name'];
      }
    }
  }

  return $tech_params;
}



/*Returns a specific technical parameter with all of its properties and translations*/
function getTechParamByName($tech_param_name) {
  require_once('./models/translations.php');

  /*All known translations for out technical parameters*/
  $translations = getTranslation(array('tech-params' => array( $tech_param_name => null )));


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*
    Get all records from the main table
    that will be merged later with all '$translations'
  */
  $query = $db->prepare('SELECT
                          id                 as db_id,
                          name_translate_key as id,
                          units
                         FROM tech_param
                         WHERE name_translate_key=:name_translate_key
  ');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute(array('name_translate_key' => $tech_param_name ))) {
    return;
  }

  /*Check if there's any record for the specific '$tech_param_name'*/
  $tech_param = $query->fetch(PDO::FETCH_ASSOC);
  if (!$tech_param) {
    return;
  }


  /*Combine the specific tech translations with its record from the DB*/
  foreach ($translations as $language_name => $translation) {
    
    if (!isset( $tech_param['name']) ||
        gettype($tech_param['name']) != 'array'
    ) {
      $tech_param['name'] = array();
    }

    $tech_param['name'][ $language_name ] = $translation['name'];
  }

  return $tech_param;
}



/*
  Will convert all names from '$tech_param_translation_names' into '$translations'
  in a common DB translation schema
*/
function setTechParamTranslations($tech_param_name, &$translations, $tech_param_translation_names, $units) {

  /*Basic validation*/
  if (gettype($tech_param_translation_names) != 'array') {
    return '3rd argument must be an array of technical parameter translation names';
  }


  /*Used to validate translation languages of a common technical parameter*/
  require_once('./models/translations.php');

  /*Get all known to the DB translation languages*/
  $valid_language_names = getAllLanguageNames();
  if (gettype( $valid_language_names ) != 'array' ||
      !sizeof( $valid_language_names )
  ) {
    return 'Unable to load all translation languages';
  }


  /*'$translations' will have all valid translations for a common technical parameter*/
  $translations = array();
  foreach ($tech_param_translation_names as $language_name => $translation) {

    /*Be sure that all translations are in valid languages*/
    if (!in_array( $language_name, $valid_language_names )) {
      return 'language \''.$language_name.'\' is not a valid translatable language';
    }

    /*Validate each translation parameter name*/
    if (gettype( $translation ) != 'string') {
      return 'Invalid translation technical parameter name \''.$translation.'\' in language \''.$language_name.'\'';
    }

    /*Collect only valid translatable parameters names*/
    $translations[ $language_name ] = array(
      'tech-params' => array(
        $tech_param_name => array(
          'name'  => $translation,
          'units' => $units
        )
      )
    );
  }


  /*Formating to '$translation' completed with no errors*/
  return;
}



/*
  Used to create new technical parameters in the main DB table and translation DB records.
  Example input:
    $tech_param_name              => "length"
    $tech_param_translation_names => array('en' => 'Length', 'bg' => 'Дължина')
    $units                        => "cm"
*/
function createTechParameter($tech_param_name, $tech_param_translation_names, $units) {
  if (!isset($tech_param_name)) {
    return 'Missing 1st argument \'tech_param_name\'';
  }
  if (!isset($tech_param_translation_names)) {
    return 'Missing 2nd argument \'tech_param_translation_names\'';
  }
  if (!isset($units)) {
    return 'Missing 3rd argument \'units\'';
  }


  /*Check if the parameter we want to add already exists in our DB*/
  if (getTechParamByName( $tech_param_name )) {
    return 'technical parameter with name \''.$tech_param_name.'\' already exists';
  }


  if (gettype($tech_param_translation_names) != 'array' ||
      !sizeof($tech_param_translation_names)
  ) {
    return '\'tech_param_translation_names\' is not a valid list of new technical parameter translations';
  }


  /*Fill '$units' property validation*/
  if (gettype($units) != 'string' ||
      !sizeof($units)
  ) {
    return '"units" must be a non-empty string';
  }


  /*Use a common function to convert incoming parameter names into DB translation schema*/
  $translations = array();
  if ($error_message = setTechParamTranslations( $tech_param_name, $translations, $tech_param_translation_names, $units )) {
    return $error_message;
  }


  /*Try to set the new technical parameter name in all translation languages*/
  if ($error_message = setTranslation( $translations )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to create the new technical parameter in its main table*/
  $query  = $db->prepare('INSERT tech_param  ( name_translate_key,  units)
                                      VALUES (:name_translate_key, :units)');
  $result = $query->execute(array(
    'name_translate_key' => $tech_param_name,
    'units'              => $units
  ));


  /*Be sure the parameter is available from the DB*/
  $tech_param = getTechParamByName( $tech_param_name );
  if (!$result ||
      !$tech_param
  ) {
    return 'Unable to create technical parameter \''.$tech_param_name.'\'';
  }


  /*Complete with no error message*/
  return;
}



/*
  Used to update all technical parameter information.
  We'll update only the values that are sent
*/
function updateTechParameter($tech_param_name, $tech_param_translation_names, $units) {
  if (!isset($tech_param_name)) {
    return 'Missing 1st argument \'tech_param_name\'';
  }


  /*Check if the parameter we want to update does not exist in our DB*/
  $tech_param = getTechParamByName( $tech_param_name );
  if (!$tech_param) {
    return 'technical parameter with name \''.$tech_param_name.'\' does not exists';
  }


  /*Check if we need to update the parameter 'units' property*/
  if (gettype($units) == 'string' &&
      $units != $tech_param['units']
  ) {
    if (!sizeof( $units )) {
      return 'technical parameter "units" must be a non-empty string';
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to update the technical parameter in its main table*/
    $query  = $db->prepare('UPDATE tech_param SET units=:units WHERE name_translate_key=:name_translate_key');
    $result = $query->execute(array(
      'name_translate_key' => $tech_param['id'],
      'units'              => $units
    ));

    /*Validate correct units update*/
    if (!$result) {
      return 'Unable to update technical parameter units to \''.$units.'\'';
    }
  }


  /*Update parameter translations if a valid ones are sent*/
  if (gettype( $tech_param_translation_names ) == 'array' &&
       sizeof( $tech_param_translation_names )
  ) {

    /*Use latest 'units' property if same was updated in a function before this one*/
    $_tech_param = getTechParamByName( $tech_param_name );
    $units       = $_tech_param['units'];

    /*Use a common function to convert incoming parameter names into DB translation schema*/
    $translations = array();
    if ($error_message = setTechParamTranslations( $tech_param_name, $translations, $tech_param_translation_names, $units )) {
      return $error_message;
    }


    /*Try to set the updated parameter name in all translation languages*/
    if ($error_message = setTranslation( $translations )) {
      return $error_message;
    }
  }


  /*Updating finished with no errors*/
  return;
}



/*Single place which we'll remove all technical parameter properties and values*/
function deleteTechParameter($tech_param_name) {
  if (!isset($tech_param_name)) {
    return 'Missing 1st argument \'tech_param_name\'';
  }


  /*Check if the parameter we want to delete does not exist in our DB*/
  $tech_param = getTechParamByName( $tech_param_name );
  if (!$tech_param) {
    return 'technical parameter with name \''.$tech_param_name.'\' does not exists';
  }


  /*Enclosed logic for removing parameter translation*/
  function deleteTechParameterTranslation($tech_param_name) {
    require_once('./models/translations.php');

    /*Get all known to the DB translation languages*/
    $valid_language_names = getAllLanguageNames();
    if (gettype( $valid_language_names ) != 'array' ||
        !sizeof( $valid_language_names )
    ) {
      return 'Unable to load all translation languages';
    }


    /*'$translations' will have all valid translations for a common technical parameter*/
    $translations = array();
    foreach ($valid_language_names as $language_name) {
      $translations[ $language_name ] = array(
        'tech-params' => array(
          $tech_param_name => null
        )
      );
    }


    /*Pass the removing process to responsible Translation model*/
    return deleteTranslation( $translations );
  }


  /*Try to remove all parameter translation names*/
  if ($error_message = deleteTechParameterTranslation( $tech_param_name )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Remove the parameter from its main table*/
  $query  = $db->prepare('DELETE FROM tech_param WHERE name_translate_key=:name_translate_key');
  $result = $query->execute(array('name_translate_key' => $tech_param['id']));

  if (!$result) {
    return 'Unable to remove records for technical parameter "'.$tech_param_name.'"';
  }


  /*Removal process completed with no errors*/
  return;
}