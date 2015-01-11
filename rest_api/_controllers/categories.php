<?php
/*
  This file will manage all Categories requests
  like returning the entire category-subcategory schema
*/


/*Show all category and subcategory connections in a JSON from the DB*/
function showAllCategories() {
  require_once('./models/categories.php');

  $categories = getAllCategories();
  if (gettype($categories) != 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored categories"}');
  }

  header('HTTP/1.1 200 OK');
  die('{"categories":'.json_unicode_encode( $categories ).'}');
}



/*
  Will look into the URL and retrieve DB record of
  main category for URL /:category_name or
  subcategory   for URL /:category_name/:subcategory_name.
  If we have no DB records for category nor subcategory
  this function will return 'null'.
*/
function getCategoryFromURL() {
  global $request;

  /*Load all categories DB-related functions*/
  require_once('./models/categories.php');

  /*Load DB data for all requested categories*/
  if (sizeof( $request['url_conponents'] ) == 3) {
    $category    = getCategoryByName( $request['url_conponents'][1] );
    $subcategory = getCategoryByName( $request['url_conponents'][2] );

  } else if (sizeof( $request['url_conponents'] ) == 2) {
    $category = getCategoryByName( $request['url_conponents'][1] );

  } else {
    return;
  }


  /*Check if we have info for all requested categories*/
  if (gettype($category)    != 'array' ||
      isset($subcategory)              &&
      gettype($subcategory) != 'array'
  ) {
    return;
  }


  /*Check that the main category is not a subcategory*/
  if (gettype($category['parent_id']) != 'NULL'
  ) {
    return;
  }


  /*Check that the subcategory is actually a child of the main category*/
  if (isset($subcategory) && 
      $subcategory['parent_id'] !== $category['id']
  ) {
    return;
  }


  /*Returns the last child-node*/
  return isset($subcategory) ? $subcategory : $category;
}



/*
  Checks if we need to create a brand new category or
  a subcategory with a relation to an existing category.
  Then just sent the relevant data to the category model and
  wait for saving status information.
*/
function createCategoryController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  /*Check if such a category already exists*/
  if (gettype( getCategoryFromURL() ) != 'NULL') {
    header('HTTP/1.1 409 Conflict');
    die();
  }


  /*Load all categories DB-related functions*/
  require_once('./models/categories.php');


  $category_name = $request['url_conponents'][1];
  $category      = getCategoryByName( $category_name );


  /*Check if we're creating main or sub- category*/
  if (sizeof( $request['url_conponents'] ) === 2) {
    $new_category_name = $category_name;
    $parent_id         = null;

  } else if (sizeof( $request['url_conponents'] ) === 3) {
    $subcategory_name = $request['url_conponents'][2];

    /*Main category should be already created before adding subcategories*/
    if (!$category) {
      header('HTTP/1.1 404 Not Found');
      die();
    }

    $new_category_name = $subcategory_name;
    $parent_id         = $category['id'];

  } else {
    return;
  }


  $new_category_data = $request['data'];

  if (gettype( $new_category_data )         != 'array' ||
      !isset(  $new_category_data['name'] )            ||
      gettype( $new_category_data['name'] ) != 'array'
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing \'name\' JSON property"}');
  }


  /*Validate new category translation names*/
  if (!sizeof( $new_category_data['name'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"\'name\' property should have language translations"}');
  }


  /*Used to validate new category translations*/
  require_once('./models/translations.php');

  /*Be sure we have a valid list of all currently in use languages*/
  $valid_language_names = getAllLanguageNames();
  if (gettype( $valid_language_names ) != 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load translation languages"}');
  }


  /*The below object will have all valid translation category names*/
  $translations = array();
  foreach ($new_category_data['name'] as $language_name => $translation) {

    /*Be sure that all translations are in valid languages*/
    if (!in_array( $language_name, $valid_language_names )) {
      header('HTTP/1.1 400 Bad Request');
      die('{"error":"language \''.$language_name.'\' in \'name\' property is not a valid translatable language"}');
    }

    /*Validate each translation category name*/
    if (gettype( $translation ) != 'string') {
      header('HTTP/1.1 400 Bad Request');
      die('{"error":"Invalid translation category name \''.$translation.'\' for language \''.$language_name.'\' in \'name\' property"}');
    }

    /*Collect only valid translatable category names*/
    $translations[ $language_name ] = array(
      'categories' => array(
        $new_category_name => $translation
      )
    );
  }


  /*Take any valid boolean value or fall back to default 'false'*/
  $enabled = false;
  if (isset(   $new_category_data['enabled'] ) &&
      gettype( $new_category_data['enabled'] ) == 'boolean'
  ) {
    $enabled = $new_category_data['enabled'];
  }


  /*Check if the DB model function faced any problems creating the new category*/
  if ($error_message = createCategory( $new_category_name, $translations, $parent_id, $enabled )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*New category was created with no issues*/
  header('HTTP/1.1 201 Created');
  die();
}



/*
  This function check if any of the following is sent: 'parent_id', 'enabled', or 'name' aka translations
  and if so, will update the DB record for the mentioned category using a predefined model.
*/
function updateCategoryController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  /*Try to load any category or subcategory from the URL through the DB*/
  $category = getCategoryFromURL();

  /*Check if such a category exists*/
  if (gettype( $category ) == 'NULL') {
    header('HTTP/1.1 404 Not Found');
    die();
  }

  $category_name = $category['name_translate_key'];

  /*Check if there's anything that we want to update*/
  $category_data = $request['data'];
  if (gettype( $category_data ) != 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing data to update category \''.$category_name.'\'"}');
  }


  /*Check for category new translation*/
  $translations = null;
  if (isset(   $category_data['name'] )            &&
      gettype( $category_data['name'] ) == 'array' &&
      sizeof(  $category_data['name'] )
  ) {

    /*Used to validate new category translations*/
    require_once('./models/translations.php');

    /*Be sure we have a valid list of all currently in use languages*/
    $valid_language_names = getAllLanguageNames();
    if (gettype( $valid_language_names ) != 'array') {
      header('HTTP/1.1 500 Internal Server Error');
      die('{"error":"Unable to load translation languages"}');
    }


    /*The below object will have all valid translation category names*/
    $translations = array();
    foreach ($category_data['name'] as $language_name => $translation) {

      /*Be sure that all translations are in valid languages*/
      if (!in_array( $language_name, $valid_language_names )) {
        header('HTTP/1.1 400 Bad Request');
        die('{"error":"language \''.$language_name.'\' in \'name\' property is not a valid translatable language"}');
      }

      /*Validate each translation category name*/
      if (gettype( $translation ) != 'string') {
        header('HTTP/1.1 400 Bad Request');
        die('{"error":"Invalid translation category name \''.$translation.'\' for language \''.$language_name.'\' in \'name\' property"}');
      }

      /*Collect only valid translatable category names*/
      $translations[ $language_name ] = array(
        'categories' => array(
          $category_name => $translation
        )
      );
    }
  }


  /*Take any valid integer value or fall back to default 'null'*/
  $parent_id = null;
  if (isset(  $category_data['parent_id'] ) &&
      is_int( $category_data['parent_id'] )
  ) {
    $parent_id = $category_data['parent_id'];
  }


  /*Take any valid boolean value or fall back to default 'null'*/
  $enabled = null;
  if (isset(   $category_data['enabled'] ) &&
      gettype( $category_data['enabled'] ) == 'boolean'
  ) {
    $enabled = $category_data['enabled'];
  }


  /*Check if the DB model function faced any problems updating the category*/
  if ($error_message = updateCategory( $category_name, $translations, $parent_id, $enabled )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Category was updated with no issues*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*
  Been used when an admin wants to remove a category.
  If so, we'll generate the translations that such a category
  needs to be removed and will call the DB model to execute the task
*/
function deleteCategoryController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  /*Try to load any category or subcategory from the URL through the DB*/
  $category = getCategoryFromURL();

  /*Check if such a category exists*/
  if (gettype( $category ) == 'NULL') {
    header('HTTP/1.1 404 Not Found');
    die();
  }

  $category_name = $category['name_translate_key'];


  /*Used to validate new category translations*/
  require_once('./models/translations.php');

  /*Be sure we have a valid list of all currently in use languages*/
  $valid_language_names = getAllLanguageNames();
  if (gettype( $valid_language_names ) != 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load translation languages"}');
  }


  /*
    Create the translation schema that this category
    should normally have and same must be removed
  */
  $translations = array();
  foreach ($valid_language_names as $language) {
    $translations[ $language ] = array(
      'categories' => array(
        $category_name => null
      )
    );
  }


  /*Try to delete the category with the Category data model*/
  require_once('./models/categories.php');

  if ($error_message = deleteCategory( $category_name, $translations )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Category was deleted with no issues*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /categories
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) == 1     &&
    $request['url_conponents'][0] == 'categories' &&
    $request['method']            == 'GET'
) {
  showAllCategories();
  return;
}


/*Validates rooting:
  /categories/:category_name
  /categories/:category_name/:subcategory_name
in methods:
  POST
  PUT
  DELETE
*/
if ((sizeof( $request['url_conponents'] ) == 2 ||
     sizeof( $request['url_conponents'] ) == 3
    ) &&
    $request['url_conponents'][0] == 'categories'
) {
  switch ($request['method']) {
    case 'POST'  : createCategoryController(); return;
    case 'PUT'   : updateCategoryController(); return;
    case 'DELETE': deleteCategoryController(); return;
  }
}


/*No suitable Categories rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');