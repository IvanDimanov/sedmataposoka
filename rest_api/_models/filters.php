<?php
/*
  This file holds functions that will return all product/category filters, and
  functions that will CRUD these filters.
*/


/*
  Returns an indexed list of all Filters presentation types
  we have currently saved in DB or
 'false' on some error
*/
function getAllPresentationTypes() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved types*/
  $query = $db->prepare('SELECT DISTINCT presentation_type FROM filter');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  /*Be sure we have a correct/expected result from our DB*/
  $presentation_types = $query->fetchAll(PDO::FETCH_ASSOC);
  if (gettype( $presentation_types ) != 'array' ||
      !sizeof( $presentation_types )
  ) {
    return;
  }


  /*Convert the somehow associative DB result to an index array in '$result'*/
  $result = array();
  foreach ($presentation_types as $presentation_type) {
    $result[] = $presentation_type['presentation_type'];
  }

  return $result;
}



/*
  Returns an associative array of all filters with their values
  in a schema predetermined from the filter presentation type.
  Returns 'false' on any type of error.
*/
function getAllFilters() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Combines all filters
    with their predetermined values from 'common_filter_value' or
    with their free-new      values from 'custom_filter_value'
  */
  $query = $db->prepare('SELECT
    filter.id                 as db_id,
    filter.name_translate_key as id,
    filter.presentation_type  as type,

    filter_value.custom_filter_value       as custom_value,
    filter_value.is_default_value          as is_default_value,
    common_filter_value.name_translate_key as value

    FROM filter
    LEFT JOIN filter_value        on filter_value.filter_id              = filter.id
    LEFT JOIN common_filter_value on filter_value.common_filter_value_id = common_filter_value.id
  ');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  /*There should be always some filters in the DB (at least 'price')*/
  $db_filters = $query->fetchAll(PDO::FETCH_ASSOC);
  if (!sizeof( $db_filters )) {
    return;
  }


  /*Collect and format all '$db_filters' in '$result_filters'*/
  $result_filters = array();
  foreach ($db_filters as $db_filter) {
    $filter_name = $db_filter['id'];

    /*Be sure to set correct filter values only once*/
    if (!isset( $result_filters[ $filter_name ]) ||
        gettype($result_filters[ $filter_name ]) != 'array'
    ) {

      $result_filters[ $filter_name ] = array(
        'db_id' => $db_filter['db_id'],
        'id'    => $filter_name,
        'type'  => $db_filter['type']
      );


      /*Create correct specific 'values' format*/
      switch ($result_filters[ $filter_name ]['type']) {
        case 'checkbox':
        case 'select':
        case 'multiple_choice':
          $result_filters[ $filter_name ]['values'        ] = array();
          $result_filters[ $filter_name ]['default_values'] = '';
        break;


        case 'double_slider':
          $result_filters[ $filter_name ]['values'] = array(
            'min' =>  9999999999,
            'max' => -9999999999
          );
          $result_filters[ $filter_name ]['default_values'] = array(
            'min' =>  9999999999,
            'max' => -9999999999
          );
        break;
      }
    }


    /*
      Determine if the current filtering value is
      a part from a predetermined values in table 'filter_value' or
      it is some custom scalar or new value as 'custom_filter_value' 
    */
    $value = null;
    if (gettype( $db_filter['custom_value'] ) != 'NULL') {
      $value = $db_filter['custom_value'];

    } else if (gettype( $db_filter['value'] ) != 'NULL') {
      $value = $db_filter['value'];
    }


    /*
      Follow the same specific recording format as
      the one determined for all filter presentation types
    */
    switch ($result_filters[ $filter_name ]['type']) {
      case 'checkbox':
      case 'select':
      case 'multiple_choice':
        if ($db_filter['is_default_value']) {
          $result_filters[ $filter_name ]['default_values'] = $value;
        } else {
          $result_filters[ $filter_name ]['values'][]       = $value;
        }
      break;


      case 'double_slider':
        $value = floatval( $value );

        if ($db_filter['is_default_value']) {
          $result_filters[ $filter_name ]['default_values'] = array(
            'min' => min( $result_filters[ $filter_name ]['default_values']['min'], $value ),
            'max' => max( $result_filters[ $filter_name ]['default_values']['max'], $value )
          );
        } else {
          $result_filters[ $filter_name ]['values'] = array(
            'min' => min( $result_filters[ $filter_name ]['values']['min'], $value ),
            'max' => max( $result_filters[ $filter_name ]['values']['max'], $value )
          );
        }
      break;
    }
  }


  return $result_filters;
}



/*
  Returns a specific filter with all of its values and default values
  from the already generated list-of-all-filters
*/
function getFilterByName($filter_name) {
  $all_filters = getAllFilters();
  return isset($all_filters[ $filter_name ]) ? $all_filters[ $filter_name ] : null;
}



/*
  Will return a DB record (if same is possible) for the incoming '$value_name',
  while searching for it as a custom and common filter value.
*/
function getFilterValueByName($value_name) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Search the value as a custom filter value - common for the 'double_slider' filter type*/
  $query = $db->prepare('SELECT *
                         FROM filter_value
                         WHERE filter_value.custom_filter_value = :value_name
                         LIMIT 1');
  $query->execute(array(
    'value_name' => $value_name
  ));


  /*
    Return if possible the custom filter value or
    fall-back to search of common filter value
  */
  $filter_value = $query->fetch(PDO::FETCH_ASSOC);
  if ($filter_value) {
    return $filter_value;
  }


  /*Search the value as a common filter value - normal value for filter types like 'select' and 'multiple_choise'*/
  $query = $db->prepare('SELECT
                          filter_value.id                     as id,
                          filter_value.filter_id              as filter_id,
                          filter_value.common_filter_value_id as common_filter_value_id,
                          filter_value.custom_filter_value    as custom_filter_value,
                          filter_value.is_default_value       as is_default_value

                         FROM filter_value
                         JOIN common_filter_value on common_filter_value.id = filter_value.common_filter_value_id
                         WHERE common_filter_value.name_translate_key = :value_name
                         LIMIT 1');
  $query->execute(array(
    'value_name' => $value_name
  ));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*
  Returns DB record for filter value with name '$value_name'.
  NOTE: All values have associated translations,
        that's why it is important to add translation when creating new common filter value
*/
function getCommonFilterValueIDByName($value_name) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query = $db->prepare('SELECT * FROM common_filter_value WHERE name_translate_key = :value_name');
  $query->execute(array(
    'value_name' => $value_name
  ));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*
  Will convert all names from '$filter_translation_names' into '$translations'
  in a common DB translation schema
*/
function setFilterTranslations($filter_name, &$translations, $filter_translation_names) {

  /*Basic validation*/
  if (gettype($filter_translation_names) != 'array') {
    return '3rd argument must be an array of filter translation names';
  }


  /*Used to validate translation languages of a common filter*/
  require_once('./models/translations.php');

  /*Get all known to the DB translation languages*/
  $valid_language_names = getAllLanguageNames();
  if (gettype( $valid_language_names ) != 'array' ||
      !sizeof( $valid_language_names )
  ) {
    return 'Unable to load all translation languages';
  }


  /*'$translations' will have all valid translations for a common filter*/
  $translations = array();
  foreach ($filter_translation_names as $language_name => $translation) {

    /*Be sure that all translations are in valid languages*/
    if (!in_array( $language_name, $valid_language_names )) {
      return 'language \''.$language_name.'\' is not a valid translatable language';
    }

    /*Validate each translation filter name*/
    if (gettype( $translation ) != 'string') {
      return 'Invalid translation filter name \''.$translation.'\' in language \''.$language_name.'\'';
    }

    /*Collect only valid translatable filter names*/
    $translations[ $language_name ] = array(
      'filters' => array(
        $filter_name => $translation
      )
    );
  }


  /*Formating to '$translation' completed with no errors*/
  return;
}



/*
  Use a specific validation plan for both '$values' and '$default_values'
  in respect to the new filter '$type'
*/
function setValuesAndDefaultValuesPerType($type, &$values, &$default_values) {
  switch ($type) {
    case 'checkbox':

      /*Common boolean cast function for 'true', 'false', '1', '0', etc.*/
      $values = getCommonBoolean( $values );

      if (gettype($values) != 'boolean') {
        return 'Property \'values\' must be of type \'boolean\' but it was from type \''.gettype($values).'\'';
      }

      /*Such a boolean-to-string casting is made coz in our DB we have booleans as strings*/
      $values = $values ? 'true' : 'false';



      /*Common boolean cast function for 'true', 'false', '1', '0', etc.*/
      $default_values = getCommonBoolean( $default_values );

      if (gettype($default_values) != 'boolean') {
        return '\'default_values\' must be of type \'boolean\' but it was from type \''.gettype($default_values).'\'';
      }

      /*Such a boolean-to-string casting is made coz in our DB we have booleans as strings*/
      $default_values = $default_values ? 'true' : 'false';
    break;



    case 'select':
    case 'multiple_choice':
      if (gettype($values) != 'array') {
        return '\'values\' must be of type \'array\' but it was from type \''.gettype($values).'\'';
      }

      $values = array_unique( $values );
      if (sizeof($values) < 2) {
        return '\'values\' must have at least 2 unique options';
      }

      foreach ($values as $value) {
        if (gettype($value) != 'string' ||
            !sizeof($value)
        ) {
          return 'All of property \'values\' options must be a non-empty strings';
        }
      }


      if (gettype($default_values) != 'string') {
        return '\'default_values\' must be of type \'string\' but it was from type \''.gettype($default_values).'\'';
      }

      if (!in_array( $default_values, $values)) {
        return '\'default_values\' must be one of property \'values\' options';
      }
    break;



    case 'double_slider':
      if (gettype($values)  != 'array'  ||
          sizeof( $values)  != 2        ||
          !is_numeric($values['min']*1) ||
          !is_numeric($values['max']*1) ||
          $values['min']*1 > $values['max']*1
      ) {
        return '\'values\' must be of type \'array\' with {number} \'min\' and {number} \'max\' properties where \'min\' <= \'max\'';
      }

      $values['min'] *= 1;
      $values['max'] *= 1;


      if (gettype($default_values)  != 'array'  ||
          sizeof( $default_values)  != 2        ||
          !is_numeric($default_values['min']*1) ||
          !is_numeric($default_values['max']*1) ||
          $default_values['min']*1 > $default_values['max']*1
      ) {
        return '\'default_values\' must be of type array with {number} \'min\' and {number} \'max\' properties where \'min\' <= \'max\'';
      }

      $default_values['min'] *= 1;
      $default_values['max'] *= 1;


      if ($default_values['min'] > $values['max'] ||
          $default_values['min'] < $values['min'] ||

          $default_values['max'] > $values['max'] ||
          $default_values['max'] < $values['min']
      ) {
        return '\'default_values\' must be in the number range of [\'values\'.\'min\'; \'values\'.\'max\']';
      }
    break;



    default:
      return 'Unknown filter type \''.$type.'\'';
    break;
  }
}



/*
  Please use this function only in other model functions like 'createFilter()'.
  This function will take care which values from '$values' and '$default_values'
  are common for the system 'getCommonFilterValueIDByName()' and which are custom
*/
function saveFilterValues($filter_db_id, $filter_name, $values, $default_values) {

  /*Secure array iteration even the filter value and default value is just one variable*/
  if (gettype($values) != 'array') {
    $values = array( $values );
  }
  if (gettype($default_values) != 'array') {
    $default_values = array( $default_values );
  }


  /*Common function for both 'values' and 'default_values' DB recording*/
  function executeQuery($filter_db_id, $custom_filter_value, $common_filter_value_id, $is_default_value) {

    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Final attempt to record the prepared filter value in the DB*/
    $query  = $db->prepare('INSERT filter_value ( filter_id,  custom_filter_value,  common_filter_value_id,  is_default_value)
                                         VALUES (:filter_id, :custom_filter_value, :common_filter_value_id, :is_default_value)
    ');
    
    return $query->execute(array(
      'filter_id'              => $filter_db_id,
      'custom_filter_value'    => $custom_filter_value,
      'common_filter_value_id' => $common_filter_value_id,
      'is_default_value'       => $is_default_value
    ));
  }



  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Before setting new filter values we need to remove the old ones*/
  $query  = $db->prepare('DELETE FROM filter_value WHERE filter_id=:filter_id');
  $result = $query->execute(array('filter_id' => $filter_db_id));

  if (!$result) {
    return 'Unable to remove old values and default values records for filter "'.$filter_name.'"';
  }


  /*Record all filter values in the DB*/
  foreach ($values as $value) {

    /*Mark the filter value as new - unknown values for the DB are custom*/
    $custom_filter_value    = $value;
    $common_filter_value_id = null;

    /*
      Check if we already have records for the filter value and
      if so, we'll switch the logic to record-a-common-known-value
    */
    if ($common_value = getCommonFilterValueIDByName( $value )) {
      $custom_filter_value    = null;
      $common_filter_value_id = $common_value['id'];
    }

    if (!executeQuery( $filter_db_id, $custom_filter_value, $common_filter_value_id, false)) {
      return 'Unable to add value \''.$custom_filter_value.'\' into filter \''.$filter_name.'\'';
    }
  }


  /*Record the filter values in the DB that should be used as initial/default for the new filter*/
  foreach ($default_values as $default_value) {

    /*Initially set the default value as new/unknown to the DB*/
    $custom_filter_value    = $default_value;
    $common_filter_value_id = null;

    /*Check if already have some preset records for the iterated default value*/
    if ($common_value = getCommonFilterValueIDByName( $default_value )) {
      $custom_filter_value    = null;
      $common_filter_value_id = $common_value['id'];
    }

    if (!executeQuery( $filter_db_id, $custom_filter_value, $common_filter_value_id, true)) {
      return 'Unable to add default value \''.$custom_filter_value.'\' into filter \''.$filter_name.'\'';
    }
  }
}



/*Will use the incoming data to create new filter, accessible from 'getFilterByName()'*/
function createFilter($filter_name, $filter_translation_names, $type, $values, $default_values) {
  if (!isset($filter_name)) {
    return 'Missing 1st argument \'filter_name\'';
  }
  if (!isset($filter_translation_names)) {
    return 'Missing 2nd argument \'filter_translation_names\'';
  }
  if (!isset($type)) {
    return 'Missing 3rd argument \'type\'';
  }
  if (!isset($values)) {
    return 'Missing 4th argument \'values\'';
  }
  if (!isset($default_values)) {
    return 'Missing 5th argument \'default_values\'';
  }


  /*Check if the filter we want to add already exists in our DB*/
  if (getFilterByName( $filter_name )) {
    return 'filter with name \''.$filter_name.'\' already exists';
  }


  if (gettype($filter_translation_names) != 'array' ||
      !sizeof($filter_translation_names)
  ) {
    return '\'filter_translation_names\' is not a valid list of filter name translations';
  }


  /*Use a common function to convert incoming filter names into DB translation schema*/
  $translations = array();
  if ($error_message = setFilterTranslations( $filter_name, $translations, $filter_translation_names )) {
    return $error_message;
  }


  if (gettype($type) != 'string' ||
      !sizeof($type)
  ) {
    return '\'type\' is not a valid filter presentation type';
  }


  /*Get a list of all known to the DB filter presentation types*/
  $valid_presentation_types = getAllPresentationTypes();
  if (gettype( $valid_presentation_types ) != 'array' ||
      !sizeof( $valid_presentation_types )
  ) {
    return 'Unable to load all filters presentation types';
  }

  /*Be sure that we want to create a known presentation type filter*/
  if (!in_array($type, $valid_presentation_types)) {
    return '\'type\' is not one of the valid filter presentation types: '.implode(', ', $valid_presentation_types);
  }


  /*Set correct filter values for '$values' and '$default_values' for predefined filter '$type'*/
  if ($error_message = setValuesAndDefaultValuesPerType( $type, $values, $default_values )) {
    return $error_message;
  }


  /*Try to set the new filter name in all translation languages*/
  if ($error_message = setTranslation( $translations )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to create the new filter in its main table*/
  $query  = $db->prepare('INSERT filter ( name_translate_key, presentation_type)
                                 VALUES (:filter_name       , :type            )');
  $result = $query->execute(array(
    'filter_name' => $filter_name,
    'type'        => $type
  ));


  /*Be sure the filter is available from the DB*/
  $filter = getFilterByName( $filter_name );
  if (!$result ||
      !$filter
  ) {
    return 'Unable to create filter \''.$filter_name.'\'';
  }


  /*Will handle all saving queries for '$values' and '$default_values'*/
  if ($error_message = saveFilterValues( $filter['db_id'], $filter_name, $values, $default_values )) {
    return $error_message;
  }


  /*Complete with no error message*/
  return;
}



/*
  Used to update all filter information.
  If some of the incoming data is missing, we'll use the already saved one
*/
function updateFilter($filter_name, $filter_translation_names, $type, $values, $default_values) {
  if (!isset($filter_name)) {
    return 'Missing 1st argument \'filter_name\'';
  }


  /*Check if the filter we want to update does not exist in our DB*/
  $filter = getFilterByName( $filter_name );
  if (!$filter) {
    return 'filter with name \''.$filter_name.'\' does not exists';
  }


  /*Update filter translations if a valid ones are sent*/
  if (gettype($filter_translation_names) == 'array' &&
       sizeof($filter_translation_names)
  ) {

    /*Use a common function to convert incoming filter names into DB translation schema*/
    $translations = array();
    if ($error_message = setFilterTranslations( $filter_name, $translations, $filter_translation_names )) {
      return $error_message;
    }


    /*Try to set the updated filter name in all translation languages*/
    if ($error_message = setTranslation( $translations )) {
      return $error_message;
    }
  }


  /*Check if we need to update the filter type property*/
  /*NOTE: We always need to update filter values when setting new filter type*/
  if (gettype($type) == 'string' &&
      $type != $filter['type']
  ) {
    /*
      If we gonna update the filter type
      we for sure we'll need to update the type values as well
    */
    if (gettype($values        ) == 'NULL' ||
        gettype($default_values) == 'NULL'
    ) {
      return 'Unable to update filter \''.$filter_name.'\' to new type \''.$type.'\' without updating its records for \'values\' and \'default_values\'';
    }


    /*Get a list of all known to the DB filter presentation types*/
    $valid_presentation_types = getAllPresentationTypes();
    if (gettype( $valid_presentation_types ) != 'array' ||
        !sizeof( $valid_presentation_types )
    ) {
      return 'Unable to load all filters presentation types';
    }

    /*Be sure that we want to update to a known presentation type filter*/
    if (!in_array($type, $valid_presentation_types)) {
      return '\'type\' is not one of the valid filter presentation types: '.implode(', ', $valid_presentation_types);
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to update the filter in its main table*/
    $query  = $db->prepare('UPDATE filter SET presentation_type=:type WHERE id=:id');
    $result = $query->execute(array(
      'id'   => $filter['db_id'],
      'type' => $type
    ));

    /*Validate correct type update*/
    if (!$result) {
      return 'Unable to update filter type to \''.$type.'\'';
    }
  }


  /*
    Check if we need to update '$values' or '$default_values'.
    Please note that if only one is marked for update,
    the other will still have its existing value from the '$filter' object.
  */
  if (gettype($values        ) != 'NULL' ||
      gettype($default_values) != 'NULL'
  ) {

    /*Fill the blank value since both are in connection with filter type*/
    if (gettype($type          ) == 'NULL') $type           = $filter['type'];
    if (gettype($values        ) == 'NULL') $values         = $filter['values'];
    if (gettype($default_values) == 'NULL') $default_values = $filter['default_values'];

    /*Set correct filter values for '$values' and '$default_values' for predefined filter '$type'*/
    if ($error_message = setValuesAndDefaultValuesPerType( $type, $values, $default_values )) {
      return $error_message;
    }


    /*Will handle all saving queries for '$values' and '$default_values'*/
    if ($error_message = saveFilterValues( $filter['db_id'], $filter_name, $values, $default_values )) {
      return $error_message;
    }
  }


  /*Updating finished with no errors*/
  return;
}



/*Single place which we'll remove all filter properties and values*/
function deleteFilter($filter_name) {
  if (!isset($filter_name)) {
    return 'Missing 1st argument \'filter_name\'';
  }


  /*Check if the filter we want to delete does not exist in our DB*/
  $filter = getFilterByName( $filter_name );
  if (!$filter) {
    return 'filter with name \''.$filter_name.'\' does not exists';
  }



  /*Enclosed logic for removing filter translation*/
  function deleteFilterTranslation($filter_name) {
    require_once('./models/translations.php');

    /*Get all known to the DB translation languages*/
    $valid_language_names = getAllLanguageNames();
    if (gettype( $valid_language_names ) != 'array' ||
        !sizeof( $valid_language_names )
    ) {
      return 'Unable to load all translation languages';
    }


    /*'$translations' will have all valid translations for a common filter*/
    $translations = array();
    foreach ($valid_language_names as $language_name) {
      $translations[ $language_name ] = array(
        'filters' => array(
          $filter_name => null
        )
      );
    }


    /*Pass the removing process to responsible Translation model*/
    return deleteTranslation( $translations );
  }


  /*Try to remove all filter translation names*/
  if ($error_message = deleteFilterTranslation( $filter_name )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Filter values and default values have "foreign key constraint" so need to be removed before filter itself*/
  $query  = $db->prepare('DELETE FROM filter_value WHERE filter_id=:filter_id');
  $result = $query->execute(array('filter_id' => $filter['db_id']));

  if (!$result) {
    return 'Unable to remove values and default values records for filter "'.$filter_name.'"';
  }


  /*To complete the removal process we'll delete the filter from its main table*/
  $query  = $db->prepare('DELETE FROM filter WHERE id=:id');
  $result = $query->execute(array('id' => $filter['db_id']));

  if (!$result) {
    return 'Unable to remove filter "'.$filter_name.'" from its main table';
  }


  /*Removal process completed with no errors*/
  return;
}