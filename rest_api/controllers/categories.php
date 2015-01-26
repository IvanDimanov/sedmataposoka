<?php
/*This file holds the entire rooting of all Categories client requests*/


/*Try to printout a specified by ID Category*/
function showCategory() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');

  $category_id = $request['url_conponents'][1];
  $category    = getCategoryByID( $category_id );

  if (gettype($category) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Category with ID: '.$category_id.'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $category ));
}



/*Shows all saved Categories taken from the DB*/
function showAllCategories() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');

  $categories = getAllCategories();
  if (gettype($categories) !== 'array') {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to load all stored Categories"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $categories ));
}



/*Tries to find all Categories, matches sent filters*/
function showFilteredCategories() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');

  $filters = $request['data'];
  if ($error_message = validateCategoriesFilters( $filters )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  $categories = getFilteredCategories( $filters );
  if (gettype($categories) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $categories).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $categories ));
}



/*Attempt to create new Category using user data input*/
function createCategoryController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');

  /*Use DB model function to determine if the input can be correctly used*/
  $category_properties = validateCategoryProperties( $request['data'] );
  if (gettype($category_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $category_properties).'"}');
  }

  /*Try to create a Category with validated properties*/
  $new_category = createCategory( $category_properties );
  if (gettype($new_category) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $new_category).'"}');
  }


  /*Return new successfully created Category*/
  header('HTTP/1.1 201 Created');
  die(json_unicode_encode( $new_category ));
}



/*
  Ask the model to update Category with ID sent as 1st and only URL parameter.
  Will print a possible update error or the full updated Category on success.
*/
function updateCategoryController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');


  /*Be sure we try to update an existing Category*/
  $category_id = $request['url_conponents'][1];
  $category    = getCategoryByID( $category_id );
  if (gettype($category) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Category with ID: '.$category_id.'"}');
  }


  /*Use DB model function to determine if the input can be correctly used*/
  $category_properties = validateCategoryProperties( $request['data'], false);
  if (gettype($category_properties) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $category_properties).'"}');
  }


  /*Try to update the Category with validated properties*/
  $updated_category = updateCategory( $category_id, $category_properties );
  if (gettype($updated_category) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_category).'"}');
  }


  /*Return the successfully updated Category*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_category ));
}



/*
  Will ask the model to save the incoming file from '$_FILE' into '/categories/' folder and
  update the image path into the DB record.
*/
function updateCategoryImageController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');


  /*Be sure we try to update an existing Category*/
  $category_id = $request['url_conponents'][1];
  $category    = getCategoryByID( $category_id );
  if (gettype($category) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Category with ID: '.$category_id.'"}');
  }

  /*Let the model manage file save and file name DB records*/
  $updated_category = updateCategoryImage( $category_id );
  if (gettype($updated_category) !== 'array') {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $updated_category).'"}');
  }


  /*Return the successfully updated Category*/
  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $updated_category ));
}



/*
  Asks the DB model to remove all records for the Category
  referenced with its ID as 1st and only URL parameter
*/
function deleteCategoryController() {
  global $user, $request;

  /*Secure only logged "super admin" to have access to this function*/
  if (!isset($user['type']) ||
      $user['type'] !== 'super_admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  require_once('./models/categories.php');

  $category_id = $request['url_conponents'][1];
  $category    = getCategoryByID( $category_id );

  if (gettype($category) !== 'array') {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Unable to find Category with ID: '.$category_id.'"}');
  }


  if ($error_message = deleteCategory( $category['id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /categories/:category_id
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 2            &&
    $request['url_conponents'][0]        === 'categories' &&
    $request['method']                   === 'GET'
) {
  showCategory();
  return;
}


/*Validates rooting:
  /categories
in methods:
  GET
*/
if (sizeof( $request['url_conponents'] ) === 1            &&
    $request['url_conponents'][0]        === 'categories' &&
    $request['method']                   === 'GET'
) {

  /*Check if we need to show some or all Categories from the DB*/
  if (sizeof( $request['data'] )) {
    showFilteredCategories();
  } else {
    showAllCategories();
  }
  return;
}


/*Validates rooting:
  /categories
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 1            &&
    $request['url_conponents'][0]        === 'categories' &&
    $request['method']                   === 'POST'       &&
    sizeof( $request['data'] )
) {
  createCategoryController();
  return;
}


/*Validates rooting:
  /categories/:category_id
in methods:
  PUT
*/
if (sizeof( $request['url_conponents'] ) === 2            &&
    $request['url_conponents'][0]        === 'categories' &&
    $request['method']                   === 'PUT'        &&
    sizeof( $request['data'] )
) {
  updateCategoryController();
  return;
}


/*Validates rooting:
  /categories/:category_id/image
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) === 3            &&
    $request['url_conponents'][0]        === 'categories' &&
    $request['url_conponents'][2]        === 'image'      &&
    $request['method']                   === 'POST'
) {
  updateCategoryImageController();
  return;
}


/*Validates rooting:
  /categories/:category_id
in methods:
  DELETE
*/
if (sizeof( $request['url_conponents'] ) === 2            &&
    $request['url_conponents'][0]        === 'categories' &&
    $request['method']                   === 'DELETE'
) {
  deleteCategoryController();
  return;
}


/*No suitable Categories rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');