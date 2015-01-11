<?php
/*
  This file manages all Products requests
  like CRUD options for all products.
*/


/*
  This function will call a DB model that returns only a slice of all stored products.
  This slice we'll be called "page filtered result" and respects all criteria sent via $request['data'].
*/
function showPagedProducts() {
  global $request;

  /*Get access to all products-DB functions*/
  require_once('./models/products.php');

  if ($error_message = setPagedProducts( $request['data'], $paged_products )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }

  header('HTTP/1.1 200 OK');
  die(json_unicode_encode( $paged_products ));
}



/*
  Called when we need to add a full product to an already set category.
  This function will make only the basic validation rules
  while letting the DB model to do the full check and save the product.
*/
function createProductController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }

  $product_name = $request['url_conponents'][1];
  $data         = $request['data'];

  /*Load all products DB-related functions*/
  require_once('./models/products.php');


  /*Check if such a product already exists*/
  if (getProductByName( $product_name )) {
    header('HTTP/1.1 409 Conflict');
    die();
  }


  /*Check if 'category_name' was correctly sent*/
  if (!isset( $data['category_name'])             ||
      gettype($data['category_name']) != 'string' ||
      !sizeof($data['category_name'])
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid \'category_name\' property"}');
  }


  /*Used to validate product-category*/
  require_once('./models/categories.php');

  /*Be sure that the category we want to bind a product to does exists*/
  $category_name = $data['category_name'];
  if (!getCategoryByName( $category_name )) {
    header('HTTP/1.1 404 Not Found');
    die('{"error":"Category with name \''.$category_name.'\' does not exists"}');
  }


  /*Check if any 'enabled' property was sent*/
  if (!isset($data['enabled'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing {Boolean} \'enabled\' JSON property"}');
  }


  /*Check if we have a correct 'price' property*/
  if (!isset(     $data['price']) ||
      !is_numeric($data['price'])
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing {Number} \'price\' JSON property"}');
  }

  /*Secure number typed price*/
  $data['price'] *= 1;


  /*Index 'tech_parameters' should be set even if 'null'*/
  if (!isset($data['tech_parameters'])) {
    $data['tech_parameters'] = null;
  }


  /*Validate $data['filters'] existence*/
  if (!isset( $data['filters'])            ||
      gettype($data['filters']) != 'array' ||
      !sizeof($data['filters'])
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Missing {Array} \'filters\' JSON property"}');
  }


  /*Check if the DB model function faced any problems creating the new product*/
  if ($error_message = createProduct( $product_name, $category_name, $data['enabled'], $data['price'], $data['tech_parameters'], $data['filters'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*New product was created with no issues*/
  header('HTTP/1.1 201 Created');
  die();
}



/*
  Does a basic validation of all expected parameters and
  sends them to the product model for data update
*/
function updateProductController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }

  $product_name = $request['url_conponents'][1];
  $data         = $request['data'];

  /*Load all products DB-related functions*/
  require_once('./models/products.php');


  /*Check if such a product do really exists*/
  if (!getProductByName( $product_name )) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*Check if 'category_name' was correctly sent*/
  $category_name = null;
  if (isset($data['category_name'])) {

    if (gettype($data['category_name']) != 'string' ||
        !sizeof($data['category_name'])
    ) {
      header('HTTP/1.1 400 Bad Request');
      die('{"error":"Invalid \'category_name\' property"}');
    }


    /*Used to validate product-category*/
    require_once('./models/categories.php');

    /*Be sure that the category we want to bind a product to does exists*/
    $category_name = $data['category_name'];
    if (!getCategoryByName( $category_name )) {
      header('HTTP/1.1 404 Not Found');
      die('{"error":"Category with name \''.$category_name.'\' does not exists"}');
    }

  } else {

    /*Set a value that will not update the product*/
    $data['category_name'] = null;
  }


  /*Check if any 'enabled' property was sent*/
  if (isset($data['enabled'])) {
    $data['enabled'] = getCommonBoolean( $data['enabled'] );

    if (gettype($data['enabled']) != 'boolean') {
      header('HTTP/1.1 400 Bad Request');
      die('{"error":"Missing {Boolean} \'enabled\' JSON property"}');
    }

  } else {

    /*Set a value that will not update the product*/
    $data['enabled'] = null;
  }


  /*Check if we have a correct 'price' property*/
  if (isset($data['price'])) {

    if (!is_numeric($data['price'])) {
      header('HTTP/1.1 400 Bad Request');
      die('{"error":"Missing {Number} \'price\' JSON property"}');
    }

    /*Secure number typed price*/
    $data['price'] *= 1;

  } else {

    /*Set a value that will not update the product*/
    $data['price'] = null;
  }


  /*Index 'tech_parameters' should be set even if 'null'*/
  if (!isset($data['tech_parameters'])) {
    $data['tech_parameters'] = null;
  }


  /*Validate $data['filters'] existence*/
  if (isset($data['filters'])) {

    if (gettype($data['filters']) != 'array' ||
        !sizeof($data['filters'])
    ) {
      header('HTTP/1.1 400 Bad Request');
      die('{"error":"Missing {Array} \'filters\' JSON property"}');
    }

  } else {

    /*Set a value that will not update the product*/
    $data['filters'] = null;
  }


  /*Check if the DB model function faced any problems updating the product*/
  if ($error_message = updateProduct( $product_name, $category_name, $data['enabled'], $data['price'], $data['tech_parameters'], $data['filters'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*The product was updated with no issues*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Tells the product model to remove all records for the incoming product name*/
function deleteProductController() {
  global $user, $request;

  /*Secure only logged admins to have access to this function*/
  if (!isset($user)              ||
      !isset($user['user_type']) ||
      $user['user_type'] != 'admin'
  ) {
    header('HTTP/1.1 401 Unauthorized');
    die();
  }


  $product_name = $request['url_conponents'][1];

  /*Load all products DB-related functions*/
  require_once('./models/products.php');


  /*Check if such a product do really exists*/
  if (!getProductByName( $product_name )) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*Check if the DB model function faced any problems deleting the product*/
  if ($error_message = deleteProduct( $product_name )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*The product was deleted with no issues*/
  header('HTTP/1.1 204 No Content');
  die();
}



/*Validates rooting:
  /products
in methods:
  POST
*/
if (sizeof( $request['url_conponents'] ) == 1   &&
    $request['url_conponents'][0] == 'products' &&
    $request['method']            == 'POST'
) {
  showPagedProducts();
  return;
}


/*Validates rooting:
  /products/:product_id
in methods:
  POST
  PUT
  DELETE
*/
if (sizeof( $request['url_conponents'] ) == 2 &&
    $request['url_conponents'][0] == 'products'
) {
  switch ($request['method']) {
    case 'POST'  : createProductController(); return;
    case 'PUT'   : updateProductController(); return;
    case 'DELETE': deleteProductController(); return;
  }
}


/*No suitable Products rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');