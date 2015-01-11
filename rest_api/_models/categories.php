<?php
/*This file holds all DB bindings for all available Categories*/


/*Will return a category-subcategory JSON schema with the total amount of products each category have*/
function getAllCategories() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Load all categories, their parent-child links, and the total amount of products each category have
    but know that parent category 'total_items' is its own products but not a sum of all subcategory 'total_items'.
  */
  $query = $db->prepare('SELECT
                          category.id                 as id,
                          category.name_translate_key as name,
                          category.parent_id          as parent_id,
                          COUNT(product.id)           as total_items

                        FROM
                          category

                        left JOIN
                          product
                        ON
                          product.category_id = category.id

                        GROUP BY
                          category.id
                        ');
  
  /*Prevent calculation on DB load fail*/
  if (!$query->execute()) {
    return;
  }


  /*Create a complete JSON schema of all main and sub categories with their total products assigned*/
  $categories = array();
  foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $record) {

    /*Check if current record is main category or subcategory*/
    $parent_id = $record['parent_id'];
    if (!$parent_id) {

      /*Create main category entry*/
      $categories[ $record['id'] ] = array(
        'id'         => $record['name'],
        'totalItems' => $record['total_items']
      );

    } else {

      /*Check if current subcategory entry can be assigned to a main category*/
      if (!isset($categories[ $parent_id ])) {
        $categories[ $parent_id ] = array(
          'id'            => '',
          'totalItems'    => 0,
          'subcategories' => array()
        );
      }

      /*Check if the main category is ready to be assigned a subcategory entry*/
      if (!isset($categories[ $parent_id ]['subcategories'])) {
        $categories[ $parent_id ]['subcategories'] = array();
      }


      /*Assign a subcategory entry to its main category*/
      $categories[ $parent_id ]['subcategories'][] = array(
        'id'         => $record['name'],
        'totalItems' => $record['total_items']
      );
    }
  }


  /*Remove category DB IDs as array indexes*/
  return array_values( $categories );
}



/*Returns full DB information for a given "Translation name"*/
function getCategoryByName($name_translate_key) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query = $db->prepare('SELECT * FROM category WHERE name_translate_key = :name_translate_key LIMIT 1');
  $query->execute(array('name_translate_key' => $name_translate_key));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*Returns full DB information for a given Category DB ID*/
function getCategoryByID($category_id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query = $db->prepare('SELECT * FROM category WHERE id = :category_id LIMIT 1');
  $query->execute(array('category_id' => $category_id));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*Returns full list of subcategories for a category with an ID '$parent_id'*/
function getSubcategoriesByParentID($parent_id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  $query = $db->prepare('SELECT * FROM category WHERE parent_id=:parent_id');
  $query->execute(array('parent_id' => $parent_id));

  return $query->fetchAll(PDO::FETCH_ASSOC);
}



/*
  Save incoming '$category_name' as a relation to its '$parent_id' (parent category) and
  save all of its translations to any languages mentioned in '$translations'.
*/
function createCategory($category_name, $translations, $parent_id = null, $enabled = false) {

  /*Validate new category name*/
  preg_match('/^[0-9a-z-]+$/', $category_name, $matches);
  if (!$matches) {
    return '\'name\' should consist of small-case letters, numbers, and dashes';
  }

  /*Be sure that the category is indeed new but not duplicated*/
  if (getCategoryByName( $category_name )) {
    return 'Category with name \''.$category_name.'\' already exists';
  }

  /*Validate possible parent category*/
  if (gettype( $parent_id ) != 'NULL') {
    if (gettype( getCategoryByID( $parent_id ) ) != 'array') {
      return 'Parent category ID \''.$parent_id.'\' was not found';
    }
  }

  /*Secure valid '$enabled' value*/
  if (gettype( $enabled ) != 'boolean') {
    $enabled = false;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Prepare for translation setting issues*/
  if ($error_message = setTranslation( $translations )) {
    return $error_message;
  }


  /*Try to create the category in its main table*/
  $query = $db->prepare('INSERT category ( parent_id,  name_translate_key)
                                  VALUES (:parent_id, :name_translate_key)');
  $result = $query->execute(array(
    'parent_id'          => $parent_id,
    'name_translate_key' => $category_name
  ));


  /*Be sure the category is available in the DB*/
  if (!$result ||
      gettype( getCategoryByName( $category_name )) != 'array'
  ) {
    return 'Unable to create category \''.$category_name.'\'';
  }
}



/*
  This model will update the DB record for '$category_name' using any valid non-null values for
  {Array} '$translations', {Category ID} '$parent_id', and {Boolean} '$enabled'
*/
function updateCategory($category_name, $translations = null, $parent_id = null, $enabled = null) {

  $category = getCategoryByName( $category_name );

  /*Be sure that the category already exists*/
  if (!$category) {
    return 'Category with name \''.$category_name.'\' was not found';
  }

  /*Validate possible parent category*/
  /*
    Please note that once a category is made a subcategory
    it cannot be reset to main category but can only switch its master category
  */
  if (gettype( $parent_id ) != 'NULL') {
    if (gettype( getCategoryByID( $parent_id ) ) != 'array') {
      return 'Parent category ID \''.$parent_id.'\' was not found';
    }
  }

  /*Secure valid '$enabled' value*/
  if (gettype( $enabled ) != 'NULL' &&
      gettype( $enabled ) != 'boolean'
  ) {
    $enabled = null;
  }


  /*Check if we need to update the category settings*/
  if (gettype( $translations ) != 'NULL') {
    if ($error_message = setTranslation( $translations )) {
      return $error_message;
    }
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update the category parent, its master category*/
  /*
    Please note that once a category is made a subcategory
    it cannot be reset to main category but can only switch its master category
  */
  if (gettype($parent_id)    !== 'NULL' &&
      $category['parent_id'] !== $parent_id
  ) {

    /*Prepare to update the parent category with already validated '$parent_id'*/
    $query  = $db->prepare('UPDATE category SET parent_id=:parent_id WHERE id=:category_id');
    $result = $query->execute(array(
      'parent_id'   => $parent_id,
      'category_id' => $category['id']
    ));

    if (!$result) {
      return 'Unable to update category \''.$category_name.'\'';
    }
  }


  /*Check if we need to update the category parent, its master category*/
  if (gettype($enabled)    !== 'NULL' &&
      $category['enabled'] !== $enabled
  ) {

    /*Prepare to update the already validated '$enabled' property*/
    $query  = $db->prepare('UPDATE category SET enabled=:enabled WHERE id=:category_id');
    $result = $query->execute(array(
      'enabled'     => $enabled,
      'category_id' => $category['id']
    ));

    if (!$result) {
      return 'Unable to update category \''.$category_name.'\'';
    }
  }
}



/*
  Tries to remove a category from the main category table and
  calls a translation-removal to trim all category language information
*/
function deleteCategory($category_name, $translations) {

  $category = getCategoryByName( $category_name );

  /*Be sure that the category already exists*/
  if (!$category) {
    return 'Category with name \''.$category_name.'\' was not found';
  }


  /*
    Be sure that the category we want to remove
    does not have related to it subcategories.
  */
  $subcategories = getSubcategoriesByParentID( $category['id'] );
  if (sizeof( $subcategories )) {

    $subcategories_names = array();
    foreach ($subcategories as $subcategory) {
      $subcategories_names[] = $subcategory['name_translate_key'];
    }

    $subcategories_term = sizeof( $subcategories_names ) == 1 ? 'subcategory' : 'subcategories';
    $them_term          = sizeof( $subcategories_names ) == 1 ? 'it'          : 'them';

    return 'Category "'.$category_name.'" has related to it '.$subcategories_term.': "'.implode('", "', $subcategories_names).'". You must delete '.$them_term.' first.';
  }


  /*Try to remove all category related translations*/
  if ($error_message = deleteTranslation( $translations )) {
    return $error_message;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to remove the category record from the table in question*/
  $query = $db->prepare('DELETE FROM category WHERE id=:id LIMIT 1');
  $query->execute(array('id' => $category['id']));

  if ($query->rowCount() != 1) {
    return 'Unable to remove category "'.$category_name.'" from DB';
  }


  /*Entire category remove completed smoothly*/
  return null;
}