<?php
/*This file is the only model that can CRUD products' data/


/*
*/
function setPagedProducts($rules, &$paged_products) {
  global $settings;

  /*Basic rules validation*/
  if (!isset( $rules) ||
      gettype($rules) != 'array'
  ) {
    return 'Invalid searching rules';
  }


  /*
    If an important searching criteria is missing from '$rules'
    we'll use a predefined one from the common settings
  */
  $default_search_parameters = $settings['controllers']['products']['default_search_parameters'];

  if (!isset($rules['page'   ])) $rules['page'   ] = $default_search_parameters['page'   ];
  if (!isset($rules['count'  ])) $rules['count'  ] = $default_search_parameters['count'  ];
  if (!isset($rules['sort_by'])) $rules['sort_by'] = $default_search_parameters['sort_by'];
  if (!isset($rules['lang'   ])) $rules['lang'   ] = $default_search_parameters['lang'   ];


  /*Determine the range of the returned paged products*/
  if (gettype($rules['page']*1) != 'integer' ||
              $rules['page']*1 < 1
  ) {
    return 'Invalid {integer} "page" search rule';
  }
  if (gettype($rules['count']*1) != 'integer' ||
              $rules['count']*1 < 1
  ) {
    return 'Invalid {integer} "count" search rule';
  }


  /*Check the order of products, a.k.a. sorting rule*/
  $order_clasuse = '';
  if (isset($rules['sort_by'])) {
    $order_clasuse = 'ORDER BY ';

    switch ($rules['sort_by']) {
      case 'id':
      case 'price':
      case 'priority':
      case 'created_at':
      case 'last_updated_at':
        $order_clasuse .= 'product.'.$rules['sort_by'];
      break;

      case 'name':
        $order_clasuse .= 'product.name_translate_key';
      break;

      case 'category':
        $order_clasuse .= 'category.name_translate_key';
      break;


      default:
        return 'Invalid {string} "sort_by" search rule';
      break;
    }
  }



  /*Check if the callee wants to search by a free floating text*/
  $text_search = '';
  if (isset($rules['text_search'])) {
    if (gettype($rules['text_search']) != 'string') {
      return 'Invalid {string} "text_search" search rule';
    }

    $text_search = $rules['text_search'];
  }


  /*
    Start collecting all possible 'WHERE' limitations
    so we can manage them all later in the query
  */
  $where_clauses = array('product.enabled = 1 AND product.name_translate_key LIKE :text_search');


  /*Check if we need to limit the list of products to only one category products*/
  if (isset($rules['category_id'])) {

    /*Basic category type checking*/
    /*NOTE: callee 'category_id' in the DB is presented by 'category.name_translate_key'*/
    if (gettype($rules['category_id']) != 'string' ||
        !sizeof($rules['category_id'])
    ) {
      return 'Invalid {string} "category_id" search rule';
    }


    /*Used to validate product-category*/
    require_once('./models/categories.php');

    /*Be sure that the category we want to search for does exists*/
    $category = getCategoryByName( $rules['category_id'] );
    if (!$category) {
      return 'Category with name \''.$rules['category_id'].'\' does not exists';
    }

    $where_clauses[] = 'product.category_id='.$category['id'];
  }



  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query_string = 'SELECT
                    product.*

                  FROM
                    product

                  JOIN
                    category on product.category_id = category.id

                  WHERE
                    '.join(' AND
                    ', $where_clauses).'

                  GROUP BY
                    product.id

                  '.$order_clasuse;

  $query  = $db->prepare( $query_string );
  $result = $query->execute(array(
    'text_search' => '%'.$text_search.'%'
  ));



  /*
    Check if we can return some products for the already set rues.
    This check may save time for not needed filter matching
  */
  $paged_products = array('products' => array());
  $products       = $query->fetchAll(PDO::FETCH_ASSOC);
  if (!$products ||
      gettype($products) != 'array' ||
      !sizeof($products)
  ) {
    return;
  }



  /*Check if callee have some filters he want to match the product results*/
  if (isset($rules['filters'])) {
    if (gettype($rules['filters']) != 'array' ||
        !sizeof($rules['filters'])
    ) {
      return 'Invalid {array} "filters" search rule';
    }


    /*Used to get all filter rules*/
    require_once('./models/filters.php');


    /*Check if product matches all incoming filter rules*/
    foreach ($products as $product) {
      foreach ($rules['filters'] as $filter_name => $filter_value) {

        /*
          An error here will brake the search
          while we could just use only the valid filters to generate results
        */
        $filter = getFilterByName( $filter_name );
        if (!$filter) continue;


        $query  = $db->prepare('SELECT
                                  filter_value as value

                                FROM
                                  product_filter

                                WHERE
                                  product_filter.product_id = :product_id AND
                                  product_filter.filter_id  = :filter_id
        ');
        $result = $query->execute(array(
          'product_id' => $product['id'],
          'filter_id'  => $filter['db_id']
        ));


        /*Check if the product '$product' have the filter '$filter'*/
        $product_filter_values = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$result ||
            gettype($product_filter_values) != 'array' ||
            !sizeof($product_filter_values)
        ) {
          continue;
        }


        /*Check if some product value matches the incoming filter rule*/
        $is_product_match_filter = false;
        foreach ($product_filter_values as $product_filter_value) {
          $product_filter_value = $product_filter_value['value'];

          switch ($filter['type']) {
            case 'double_slider':
              if ($product_filter_value >= $filter_value['min']*1 &&
                  $product_filter_value <= $filter_value['max']*1
              ) {
                $is_product_match_filter = true;
              }
            break;


            case 'multiple_choice':
              if (in_array( $product_filter_value, $filter_value)) {
                $is_product_match_filter = true;
              }
            break;


            case 'select':
              if ($product_filter_value == $filter_value) {
                $is_product_match_filter = true;
              }
            break;


            case 'checkbox':
              $product_filter_value = getCommonBoolean( $product_filter_value );
              $filter_value         = getCommonBoolean( $filter_value );

              if ($product_filter_value == $filter_value) {
                $is_product_match_filter = true;
              }
            break;
          }


          /*Prevent further value matching if a product value already matched a filter range values*/
          if ($is_product_match_filter) break;
        }


        /*Save main product information for all filter-matched products*/
        if ($is_product_match_filter) {
          $paged_products['products'][] = array(
            'id'    => $product['name_translate_key'],
            'price' => $product['price']
          );
        }
      }

    }/*End of 'foreach - $products'*/
  }


  /*Procedure completed with no errors*/
  return;
}



/*Returns full DB information for a given "Translation name"*/
function getProductByName($name_translate_key) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query = $db->prepare('SELECT * FROM product WHERE name_translate_key = :name_translate_key LIMIT 1');
  $query->execute(array('name_translate_key' => $name_translate_key));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*Returns full DB information for a given Product DB ID*/
function getProductByID($product_id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query = $db->prepare('SELECT * FROM product WHERE id = :product_id LIMIT 1');
  $query->execute(array('product_id' => $product_id));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*Sets a valid value to '$enabled' or returns an {string} error in case of failure*/
function setProductEnabledValue(&$enabled) {

  /*Check if any 'enabled' property were sent*/
  if (gettype($enabled) == 'NULL') {
    return 'Missing {Boolean} "enabled" JSON property';
  }


  /*Common boolean cast function for 'true', 'false', '1', '0', etc.*/
  $enabled = getCommonBoolean( $enabled );

  /*Check if we have a correct 'enabled' property*/
  if (gettype($enabled) != 'boolean') {
    return 'Invalid {Boolean} "enabled" JSON property';
  }

  /*Validation completed with no issues arose*/
  return;
}



/*Sets a valid value to '$price' or returns an {string} error in case of failure*/
function setProductPriceValue(&$price) {

  /*Check if we have a correct 'price' property*/
  if (!is_numeric($price)) {
    return 'Invalid {Number} "price" JSON property';
  }

  /*Secure number typed price*/
  $price *= 1;

  /*Validation completed with no issues arose*/
  return;
}



/*Sets a valid value to '$tech_parameters' or returns an {string} error in case of failure*/
function setProductTechParametersValue(&$tech_parameters) {

  /*Basic product tech params validation*/
  if (gettype($tech_parameters) != 'array' ||
      !sizeof($tech_parameters)
  ) {
    return '"tech_parameters" must be a valid list of all product parameters';
  }


  /*Used to validate all product technical parameters*/
  require_once('./models/tech_parameters.php');


  /*Validate all product technical parameters*/
  foreach ($tech_parameters as $tech_param_index => &$tech_parameter) {
    if (gettype($tech_parameter) != 'array' ||
        !sizeof($tech_parameter)
    ) {
      return 'Property "tech_parameters" have an invalid parameter at index "'.$tech_param_index.'"';
    }


    if (!isset($tech_parameter['id']) ||
        !getTechParamByName( $tech_parameter['id'] )
    ) {
      return 'Unknown tech parameter at index "'.$tech_param_index.'"';
    }


    if (!isset(     $tech_parameter['value']) ||
        !is_numeric($tech_parameter['value'])
    ) {
      return 'Tech parameter at index "'.$tech_param_index.'" have an invalid property "value"';
    }

    /*Safe number casting*/
    $tech_parameter['value'] *= 1;
  }


  /*Validation completed with no issues arose*/
  return;
}



/*Sets a valid value to '$filters' or returns an {string} error in case of failure*/
function setProductFiltersValue(&$filters) {

  /*Validate '$filters' correct existence*/
  if (gettype($filters) != 'array' ||
      !sizeof($filters)
  ) {
    return 'Missing {Array} "filters" JSON property';
  }


  /*Validate each product filter*/
  foreach ($filters as $filter_index => $filter) {
    if (gettype($filter) != 'array' ||
        !sizeof($filter)
    ) {
      return 'Product filter at index "'.$filter_index.'" is invalid';
    }


    /*Used to validate all product filters*/
    require_once('./models/filters.php');

    /*Basic value validation*/
    if (!isset( $filter['id']) ||
        gettype($filter['id']) != 'string'
    ) {
      return 'Filter at index "'.$filter_index.'" have an invalid "id" property';
    }


    /*Check if requested filter is available in or DB*/
    $db_filter = getFilterByName($filter['id']);
    if (!$db_filter) {
      return 'Unknown product filter at index "'.$filter['id'].'"';
    }


    /*Basic 'default_value' validation*/
    if (!isset(     $filter['default_value'])              ||
        gettype(    $filter['default_value']) != 'string'  &&
        gettype(    $filter['default_value']) != 'boolean' &&
        !is_numeric($filter['default_value'])
    ) {
      return 'Filter "'.$filter['id'].'" have an invalid "default_value" property';
    }


    /*Basic 'values' validation*/
    if (!isset( $filter['values'])            ||
        gettype($filter['values']) != 'array' ||
        !sizeof($filter['values'])
    ) {
      return 'Filter "'.$filter['id'].'" have an invalid "values" property';
    }


    /*Detailed 'values' validation and check for 'default_value' property*/
    foreach ($filter['values'] as $value_index => $value_props) {

      /*Basic filter value validation*/
      if (!isset(     $value_props['id'])              ||
          gettype(    $value_props['id']) != 'string'  &&
          gettype(    $value_props['id']) != 'boolean' &&
          !is_numeric($value_props['id'])
      ) {
        return 'Filter "'.$filter['id'].'" in value at index "'.$value_index.'" have an invalid "id" property';
      }


      /*Full 'added_price' index validation*/
      if (!isset(     $value_props['added_price']) ||
          !is_numeric($value_props['added_price'])
      ) {
        return 'Filter "'.$filter['id'].'" in value "'.$value_props['id'].'" have an invalid "added_price" property';
      }


      /*Validation strongly relies on the filter type in use*/
      switch ($db_filter['type']) {
        case 'checkbox':
        case 'select':
        case 'multiple_choice':

          /*
            Be sure that the product filter value and default are
            one of all possible values for the filter in general
          */
          if (!in_array( $value_props['id'], $db_filter['values'])) {
            return 'Filter "'.$filter['id'].'" in value "'.$value_props['id'].'" is not one from all the possible filter values';
          }

          if (!in_array( $filter['default_value'], $db_filter['values'])) {
            return 'Filter "'.$filter['id'].'" have an invalid default value "'.$filter['default_value'].'"';
          }
        break;


        case 'double_slider':

          /*Secure all product values and default values are in the correct [min, max] limit range for their filter*/
          if ($value_props['id']*1 < $db_filter['values']['min']*1 ||
              $value_props['id']*1 > $db_filter['values']['max']*1
          ) {
            return 'Filter "'.$filter['id'].'" in value "'.$value_props['id'].'" is out of its filter [min, max] range';
          }

          if ($filter['default_value']*1 < $db_filter['values']['min']*1 ||
              $filter['default_value']*1 > $db_filter['values']['max']*1
          ) {
            return 'Filter "'.$filter['id'].'" have a default value of "'.$filter['default_value'].'" that is out of its filter [min, max] range';
          }
        break;


        default:
          return 'Filter "'.$filter['id'].'" is from unknown type "'.$db_filter['type'].'"';
        break;
      }
    }
  }

  /*Validation completed with no issues arose*/
  return;
}



/*
  This function will use all 'setProduct*Value()' validation functions for all incoming arguments - '$tech_parameters' is optional.
  If all arguments are valid, will record a new category-product in its main and support tables.
*/
function createProduct($product_name, $category_name, $enabled, $price, $tech_parameters, $filters) {

  /*Validate new product name*/
  preg_match('/^[0-9a-z-]+$/', $product_name, $matches);
  if (!$matches) {
    return '"name" should consist of small-case letters, numbers, and dashes';
  }


  /*Be sure that the product is indeed new but not duplicated*/
  if (getProductByName( $product_name )) {
    return 'Product with name "'.$product_name.'" already exists';
  }


  /*Used to validate product-category*/
  require_once('./models/categories.php');

  /*Be sure that the category we want to bind a product to does exists*/
  $category = getCategoryByName( $category_name );
  if (!$category) {
    return 'Category with name \''.$category_name.'\' does not exists';
  }


  /*
    Use predefined validation rules to determine if all product dependent properties are relevant and
    if so, assign the valid values to the sent parameters in order they to be used in DB saving.
  */
  if ($error_message = setProductEnabledValue( $enabled )) {
    return $error_message;
  }
  if ($error_message = setProductPriceValue( $price )) {
    return $error_message;
  }

  /*'$tech_parameters' is optional so we should validate it only if it's sent*/
  if (gettype($tech_parameters) != 'NULL' &&
      $error_message = setProductTechParametersValue( $tech_parameters )
  ) {
    return $error_message;
  }
  if ($error_message = setProductFiltersValue( $filters )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to create the product in its main table*/
  $query = $db->prepare('INSERT product ( category_id,  name_translate_key,  enabled,  price, created_at)
                                 VALUES (:category_id, :name_translate_key, :enabled, :price, NOW())');
  $result = $query->execute(array(
    'category_id'        => $category['id'],
    'name_translate_key' => $product_name,
    'enabled'            => $enabled,
    'price'              => $price
  ));


  /*Be sure the product is available in the DB*/
  $product = getProductByName( $product_name );
  if (!$result ||
      gettype($product) != 'array'
  ) {
    return 'Unable to create product "'.$product_name.'"';
  }


  /*Used to add product-technical parameters*/
  require_once('./models/tech_parameters.php');

  /*Save all product tech parameters if such are needed to be saved*/
  if (gettype($tech_parameters) != 'NULL') {
    foreach ($tech_parameters as $product_tech_parameter) {
      $tech_parameter = getTechParamByName( $product_tech_parameter['id'] );

      /*Set the record in the "product - tech parameter" transition table*/
      $query = $db->prepare('INSERT product_tech_param ( product_id,  tech_param_id,  tech_param_value)
                                                VALUES (:product_id, :tech_param_id, :tech_param_value)');
      $query->execute(array(
        'product_id'       => $product['id'],
        'tech_param_id'    => $tech_parameter['db_id'],
        'tech_param_value' => $product_tech_parameter['value']
      ));
    }
  }


  /*Used to add product-filter*/
  require_once('./models/filters.php');

  /*Save each record in the "product - filter" transition table*/
  foreach ($filters as $product_filter) {
    $filter = getFilterByName( $product_filter['id'] );

    foreach ($product_filter['values'] as $value) {
      $query = $db->prepare('INSERT product_filter ( product_id,  filter_id,  filter_value,  is_default_value,  added_price)
                                            VALUES (:product_id, :filter_id, :filter_value, :is_default_value, :added_price)');
      $query->execute(array(
        'product_id'       => $product['id'],
        'filter_id'        => $filter['db_id'],
        'filter_value'     => $value['id'],
        'is_default_value' => $product_filter['default_value'] == $value['id'],
        'added_price'      => $value['added_price']
      ));
    }
  }


  /*Product creation completed with no errors*/
  return;
}



/*
  If any on the incoming parameters are not 'null',
  they'll be validated and if all is OK,
  their values will be updated in the main and all support DB tables
*/
function updateProduct($product_name, $category_name = null, $enabled = null, $price = null, $tech_parameters = null, $filters = null) {
  $product = getProductByName( $product_name );

  /*Be sure that the product already exists*/
  if (!$product) {
    return 'Product with name "'.$product_name.'" was not found';
  }



  /*Check if we need to update product category*/
  if (gettype($category_name) != 'NULL') {

    /*Used to validate product-category*/
    require_once('./models/categories.php');

    /*Be sure that the category we want to bind a product to does exists*/
    $category = getCategoryByName( $category_name );
    if (!$category) {
      return 'Category with name \''.$category_name.'\' does not exists';
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to update the product in its main table*/
    $query  = $db->prepare('UPDATE product SET category_id=:category_id WHERE id=:id');
    $result = $query->execute(array(
      'id'          => $product['id'],
      'category_id' => $category['id']
    ));


    /*Be sure the product is correctly updated*/
    $product = getProductByName( $product_name );
    if (!$result ||
        $product['category_id'] != $category['id']
    ) {
      return 'Unable to update product "'.$product_name.'" with new category "'.$category_name.'"';
    }
  }



  /*Check if we need to update product enabled*/
  if (gettype($enabled) != 'NULL') {

    /*Use the common value validator to secure correct update value*/
    if ($error_message = setProductEnabledValue( $enabled )) {
      return $error_message;
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to update the product in its main table*/
    $query  = $db->prepare('UPDATE product SET enabled=:enabled WHERE id=:id');
    $result = $query->execute(array(
      'id'      => $product['id'],
      'enabled' => $enabled
    ));


    /*Be sure the product is correctly updated*/
    $product = getProductByName( $product_name );
    if (!$result ||
        $product['enabled'] != $enabled
    ) {
      return 'Unable to update product "'.$product_name.'" with new enabled property "'.$enabled.'"';
    }
  }



  /*Check if we need to update product price*/
  if (gettype($price) != 'NULL') {

    /*Use the common value validator to secure correct update value*/
    if ($error_message = setProductPriceValue( $price )) {
      return $error_message;
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to update the product in its main table*/
    $query  = $db->prepare('UPDATE product SET price=:price WHERE id=:id');
    $result = $query->execute(array(
      'id' => $product['id'],
      'price' => $price
    ));


    /*Be sure the product is correctly updated*/
    $product = getProductByName( $product_name );
    if (!$result ||
        $product['price'] != $price
    ) {
      return 'Unable to update product "'.$product_name.'" with new price property "'.$price.'"';
    }
  }



  /*Check if we need to update product technical parameters*/
  if (gettype($tech_parameters) != 'NULL') {

    /*Use the common value validator to secure correct update value*/
    if ($error_message = setProductTechParametersValue( $tech_parameters )) {
      return $error_message;
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to remove current product tech params so we can add the new ones*/
    $query  = $db->prepare('DELETE FROM product_tech_param WHERE product_id=:product_id');
    $result = $query->execute(array(
      'product_id' => $product['id']
    ));

    if (!$result) {
      return 'Unable to remove old technical parameters from product "'.$product_name.'"';
    }


    /*Used to add product-technical parameters*/
    require_once('./models/tech_parameters.php');

    foreach ($tech_parameters as $product_tech_parameter) {
      $tech_parameter = getTechParamByName( $product_tech_parameter['id'] );

      /*Set the record in the "product - tech parameter" transition table*/
      $query = $db->prepare('INSERT product_tech_param ( product_id,  tech_param_id,  tech_param_value)
                                                VALUES (:product_id, :tech_param_id, :tech_param_value)');
      $query->execute(array(
        'product_id'       => $product['id'],
        'tech_param_id'    => $tech_parameter['db_id'],
        'tech_param_value' => $product_tech_parameter['value']
      ));
    }
  }



  /*Check if we need to update product filters*/
  if (gettype($filters) != 'NULL') {

    /*Use the common value validator to secure correct update value*/
    if ($error_message = setProductFiltersValue( $filters )) {
      return $error_message;
    }


    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    /*Try to remove current product filters so we can add the new ones*/
    $query  = $db->prepare('DELETE FROM product_filter WHERE product_id=:product_id');
    $result = $query->execute(array(
      'product_id' => $product['id']
    ));

    if (!$result) {
      return 'Unable to remove old filters from product "'.$product_name.'"';
    }


    /*Used to add product-filter*/
    require_once('./models/filters.php');

    /*Save each record in the "product - filter" transition table*/
    foreach ($filters as $product_filter) {
      $filter = getFilterByName( $product_filter['id'] );

      foreach ($product_filter['values'] as $value) {
        $query = $db->prepare('INSERT product_filter ( product_id,  filter_id,  filter_value,  is_default_value,  added_price)
                                              VALUES (:product_id, :filter_id, :filter_value, :is_default_value, :added_price)');
        $query->execute(array(
          'product_id'       => $product['id'],
          'filter_id'        => $filter['db_id'],
          'filter_value'     => $value['id'],
          'is_default_value' => $product_filter['default_value'] == $value['id'],
          'added_price'      => $value['added_price']
        ));
      }

    }/*End of 'foreach - $filters'*/
  }


  /*Update procedure finished with no errors*/
  return;
}



/*Tries to remove a product from its main and support product table*/
function deleteProduct($product_name) {
  $product = getProductByName( $product_name );

  /*Be sure that the product already exists*/
  if (!$product) {
    return 'Product with name "'.$product_name.'" was not found';
  }


  /*Used to remove product translation languages*/
  require_once('./models/translations.php');

  /*Get all known to the DB translation languages*/
  $valid_language_names = getAllLanguageNames();
  if (gettype( $valid_language_names ) != 'array' ||
      !sizeof( $valid_language_names )
  ) {
    return 'Unable to load all translation languages';
  }


  /*'$translations' will have all valid translations for the product we want to delete*/
  $translations = array();
  foreach ($valid_language_names as $language_name) {
    $translations[ $language_name ] = array(
      'products' => array(
        $product['id'] => null
      )
    );
  }


  /*Try to remove all product related translations*/
  if ($error_message = deleteTranslation( $translations )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to remove current product tech params*/
  $query  = $db->prepare('DELETE FROM product_tech_param WHERE product_id=:product_id');
  $result = $query->execute(array(
    'product_id' => $product['id']
  ));

  if (!$result) {
    return 'Unable to remove technical parameters from product "'.$product_name.'"';
  }


  /*Try to remove current product filters*/
  $query  = $db->prepare('DELETE FROM product_filter WHERE product_id=:product_id');
  $result = $query->execute(array(
    'product_id' => $product['id']
  ));

  if (!$result) {
    return 'Unable to remove filters from product "'.$product_name.'"';
  }


  /*Try to remove the product record from its main table*/
  $query = $db->prepare('DELETE FROM product WHERE id=:id LIMIT 1');
  $query->execute(array('id' => $product['id']));

  if ($query->rowCount() != 1) {
    return 'Unable to remove product "'.$product_name.'" from DB';
  }


  /*Entire product remove completed smoothly*/
  return null;
}