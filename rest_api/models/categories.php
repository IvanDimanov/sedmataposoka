<?php
/*This file holds all DB functions which effects Categories*/


/*Used to convert raw DB result to expected JSON-like result*/
function reorderCategoryRecord(&$category) {
  if (gettype($category) !== 'array' ||
      !sizeof($category)
  ) {
    return $category;
  }


  /*Convert name structure 'name_*' to array*/
  $category['name'] = array(
    'bg' => $category['name_bg'],
    'en' => $category['name_en']
  );

  unset( $category['name_bg'] );
  unset( $category['name_en'] );


  /*Convert description structure 'description_*' to array*/
  $category['description'] = array(
    'bg' => $category['description_bg'],
    'en' => $category['description_en']
  );

  unset( $category['description_bg'] );
  unset( $category['description_en'] );

  return $category;
}



/*Return all the info we have for Category with ID '$id' or return 'false'*/
function getCategoryByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          category.id        as id,
                          category.imagePath as imagePath,

                          categoryname.id as name_id,
                          categoryname.bg as name_bg,
                          categoryname.en as name_en,

                          categorydescription.id as description_id,
                          categorydescription.bg as description_bg,
                          categorydescription.en as description_en

                        FROM category

                        JOIN categoryname
                        ON category.nameId = categoryname.id

                        JOIN categorydescription
                        ON category.descriptionId = categorydescription.id

                        WHERE category.id = :id
                        LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  /*Set DB values in ordered JSON-like list*/
  return reorderCategoryRecord( $query->fetch(PDO::FETCH_ASSOC) );
}



/*Returns an indexed array of all Categories records we have in DB*/
function getAllCategories() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved Categories*/
  $query = $db->prepare('SELECT
                          category.id        as id,
                          category.imagePath as imagePath,

                          categoryname.id as name_id,
                          categoryname.bg as name_bg,
                          categoryname.en as name_en,

                          categorydescription.id as description_id,
                          categorydescription.bg as description_bg,
                          categorydescription.en as description_en

                        FROM category

                        JOIN categoryname
                        ON category.nameId = categoryname.id

                        JOIN categorydescription
                        ON category.descriptionId = categorydescription.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  $categories = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($categories) === 'array' &&
      sizeof( $categories)
  ) {
    foreach ($categories as &$category) reorderCategoryRecord( $category );
  }


  return $categories;
}



/*
  Will return an error string message if the incoming '$filters'
  are not suitable for filtering search for Categories.
  Please be advised that keys in '$filters' that are not used
  for filtering will be deleted from '$filters'.
*/
function validateCategoriesFilters(&$filters) {
  if (!isset( $filters) ||
      gettype($filters) !== 'array'
  ) {
    return 'Invalid filters';
  }


  if (isset($filters['ids'])) {
    if (gettype($filters['ids']) !== 'array') {
      return 'Filter "ids" must be an array';
    }

    foreach ($filters['ids'] as $index => $id) {
      if (!is_numeric($id) &&
          gettype($id) !== 'integer'
      ) {
        return 'Filter "ids" have an invalid ID at index '.$index;
      }
    }
  }


  if (isset($filters['name'])) {
    if (gettype($filters['name']) !== 'string' ||
        !sizeof($filters['name'])
    ) {
      return 'Filter "name" must be a non empty string';
    }
  }


  if (isset($filters['description'])) {
    if (gettype($filters['description']) !== 'string' ||
        !sizeof($filters['description'])
    ) {
      return 'Filter "description" must be a non empty string';
    }
  }


  if (isset($filters['imagePath'])) {
    if (gettype($filters['imagePath']) !== 'string' ||
        !sizeof($filters['imagePath'])
    ) {
      return 'Filter "imagePath" must be a non empty string';
    }
  }


  /*Be sure to return a list containing only valid filters as keys*/
  $valid_filtering_keys = array('ids', 'name', 'description', 'imagePath');
  foreach ($filters as $key => $value) {
    if (!in_array($key, $valid_filtering_keys)) {
      unset( $filters[ $key ] );
    }
  }


  /*
    Return error free response
    since none of the above found an issue
  */
  return;
}



/*Returns an indexed array of all Categories records we have in DB*/
function getFilteredCategories($filters) {

  /*Be sure we can safely use the input for filtering*/
  if ($error_message = validateCategoriesFilters( $filters )) {
    return $error_message;
  }

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $where_clauses = array();

  if (isset($filters['ids'])) {
    $where_clauses []= 'category.id IN ('.implode(', ', $filters['ids']).')';
    unset( $filters['ids'] );
  }

  if (isset($filters['name'])) {
    $where_clauses []= '(categoryname.bg LIKE :name OR
                         categoryname.en LIKE :name)';
    $filters['name'] = '%'.$filters['name'].'%';
  }

  if (isset($filters['description'])) {
    $where_clauses []= '(categorydescription.bg LIKE :description OR
                         categorydescription.en LIKE :description)';
    $filters['description'] = '%'.$filters['description'].'%';
  }

  if (isset($filters['imagePath'])) {
    $where_clauses []= 'category.imagePath LIKE :imagePath';
    $filters['imagePath'] = '%'.$filters['imagePath'].'%';
  }


  /*Filter the Categories by the saved rules in '$where_clauses'*/
  $query = $db->prepare('SELECT
                          category.id        as id,
                          category.imagePath as imagePath,

                          categoryname.id as name_id,
                          categoryname.bg as name_bg,
                          categoryname.en as name_en,

                          categorydescription.id as description_id,
                          categorydescription.bg as description_bg,
                          categorydescription.en as description_en

                        FROM category

                        JOIN categoryname
                        ON category.nameId = categoryname.id

                        JOIN categorydescription
                        ON category.descriptionId = categorydescription.id
                        '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  $categories = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($categories) === 'array' &&
      sizeof( $categories)
  ) {
    foreach ($categories as &$category) reorderCategoryRecord( $category );
  }


  return $categories;
}



/*
  Returns a list out of '$properties' that can safely be used to create or update a Category.
  if 2nd argument '$mandatory_validation' is set to 'true' then only set properties will be validated,
  e.g. if 'type' is set to 'Z' it will be invalid but if not set at all, no error will be send.
*/
function validateCategoryProperties(&$properties, $mandatory_validation = true) {
  if (!isset( $properties) ||
      gettype($properties) !== 'array'
  ) {
    return 'Invalid properties';
  }


  if ($mandatory_validation ||
      isset($properties['name'])
  ) {
    if (!isset( $properties['name']) ||
        gettype($properties['name']) !== 'array'
    ) {
      return 'Invalid "name" property';
    }


    if ($mandatory_validation ||
        isset($properties['name']['bg'])
    ) {
      if (!isset( $properties['name']['bg']) ||
          gettype($properties['name']['bg']) !== 'string'
      ) {
        return 'Invalid "name"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['name']['en'])
    ) {
      if (!isset( $properties['name']['en']) ||
          gettype($properties['name']['en']) !== 'string'
      ) {
        return 'Invalid "name"->"en" property';
      }
    }
  }


  if ($mandatory_validation ||
      isset($properties['description'])
  ) {
    if (!isset( $properties['description']) ||
        gettype($properties['description']) !== 'array'
    ) {
      return 'Invalid "description" property';
    }


    if ($mandatory_validation ||
        isset($properties['description']['bg'])
    ) {
      if (!isset( $properties['description']['bg']) ||
          gettype($properties['description']['bg']) !== 'string'
      ) {
        return 'Invalid "description"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['description']['en'])
    ) {
      if (!isset( $properties['description']['en']) ||
          gettype($properties['description']['en']) !== 'string'
      ) {
        return 'Invalid "description"->"en" property';
      }
    }
  }


  /*Be sure to return a list containing only valid properties as keys*/
  $valid_property_keys = array('name', 'description');
  foreach ($properties as $key => $value) {
    if (!in_array($key, $valid_property_keys)) {
      unset( $properties[ $key ] );
    }

    /*Be sure to leave only valid name values*/
    if ($key === 'name' &&
        gettype($properties['name']) === 'array'
    ) {
      $valid_name_keys = array('bg', 'en');

      foreach ($properties['name'] as $name_key => $name_value) {
        if (!in_array($name_key, $valid_name_keys)) {
          unset( $properties['name'][ $name_key ] );
        }
      }
    }/*End of "if key is name"*/


    /*Be sure to leave only valid description values*/
    if ($key === 'description' &&
        gettype($properties['description']) === 'array'
    ) {
      $valid_description_keys = array('bg', 'en');

      foreach ($properties['description'] as $description_key => $description_value) {
        if (!in_array($description_key, $valid_description_keys)) {
          unset( $properties['description'][ $description_key ] );
        }
      }
    }/*End of "if key is description"*/
  }


  if (isset(   $properties['name'] ) &&
      !sizeof( $properties['name'] )
  ) {
    unset( $properties['name'] );
  }

  if (isset(   $properties['description'] ) &&
      !sizeof( $properties['description'] )
  ) {
    unset( $properties['description'] );
  }


  return $properties;
}



/*
  Use 'validateCategoryProperties() to save already valid Category values.
  Will return error {string} in case of failure or newly created Category as {array}
*/
function createCategory($properties) {

  /*Be sure we can use all of the user input to create new Category*/
  $properties = validateCategoryProperties( $properties );
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Try to create Category name first in order to use the Name ID later
    when we create the Category in its main table
  */
  $query  = $db->prepare('INSERT INTO categoryname ( en,  bg) VALUES
                                                   (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['name']['en'],
    'bg' => $properties['name']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Category name';
  }


  /*Set reference ID to already saved name property*/
  unset( $properties['name'] );
  $properties['nameId'] = $db->lastInsertId();


  /*
    Try to create Category name first in order to use the Description ID later
    when we create the Category in its main table
  */
  $query  = $db->prepare('INSERT INTO categorydescription ( en,  bg) VALUES
                                                          (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['description']['en'],
    'bg' => $properties['description']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Category description';
  }


  /*Set reference ID to already saved description property*/
  unset( $properties['description'] );
  $properties['descriptionId'] = $db->lastInsertId();


  /*Try to create Category in its main table*/
  $query  = $db->prepare('INSERT INTO category ( nameId,  descriptionId) VALUES
                                               (:nameId, :descriptionId)');
  $result = $query->execute( $properties );

  if (!$result) {
    return 'Unable to create Category';
  }


  /*Try to show the creation result*/
  return getCategoryByID( $db->lastInsertId() );
}



/*
  Update the Category with ID '$category_id' with the values from '$properties'.
  Values validation is secured with 'validateCategoryProperties()'
*/
function updateCategory($category_id, $properties) {

  /*Be sure we have already existing record*/
  $category = getCategoryByID( $category_id );
  if (!$category) {
    return 'Unable to find Category with ID: '.$category_id;
  }


  /*Be sure we can use only correctly set Category properties*/
  $properties = validateCategoryProperties( $properties, false);
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update Category name since its not in Category main table*/
  if (isset(  $properties['name'])             &&
      gettype($properties['name']) === 'array' &&
      sizeof( $properties['name'])
  ) {

    $set_clauses = array();
    foreach ($properties['name'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Category name*/
    $query  = $db->prepare('UPDATE categoryname SET '.implode(', ', $set_clauses).' WHERE id='.$category['name_id']);
    $result = $query->execute( $properties['name'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Category name';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['name'] );
  }


  /*Check if we need to update Category description since its not in Category main table*/
  if (isset(  $properties['description'])             &&
      gettype($properties['description']) === 'array' &&
      sizeof( $properties['description'])
  ) {

    $set_clauses = array();
    foreach ($properties['description'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Category description*/
    $query  = $db->prepare('UPDATE categorydescription SET '.implode(', ', $set_clauses).' WHERE id='.$category['description_id']);
    $result = $query->execute( $properties['description'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Category description';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['description'] );
  }


  /*Check if there are properties to be updated in the main table*/
  if (gettype($properties) === 'array' &&
      sizeof( $properties)
  ) {
    $set_clauses = array();
    foreach ($properties as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Category in its main table*/
    $query  = $db->prepare('UPDATE category SET '.implode(', ', $set_clauses).' WHERE id='.$category['id']);
    $result = $query->execute( $properties );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Category';
    }
  }


  /*Return the updated record so callee can verify the process too*/
  return getCategoryByID( $category['id'] );
}



/*
  Try to update the Category image file
  by saving valid file from '$_FILE' and
  updating its new path in the DB.
*/
function updateCategoryImage($category_id) {
  global $settings;

  /*Be sure we have already existing record*/
  $category = getCategoryByID( $category_id );
  if (!$category) {
    return 'Unable to find Category with ID: '.$category_id;
  }


  /*Undefined | Multiple Files | $_FILES Corruption Attack*/
  if (!isset(  $_FILES['file']         ) ||
      !isset(  $_FILES['file']['error']) ||
      is_array($_FILES['file']['error'])
  ) {
    return 'Invalid "file" properties';
  }


  /*Check if upload fail in a common crash*/
  switch ($_FILES['file']['error']) {
    case UPLOAD_ERR_OK       : break;  /*No issues, no need to stop the process*/
    case UPLOAD_ERR_NO_FILE  : return 'No file to upload was sent';
    case UPLOAD_ERR_INI_SIZE :
    case UPLOAD_ERR_FORM_SIZE: return 'Uploaded file exceeded file size limit';
    default                  : return 'Error during upload';
  }


  /*Manually secure the file limit since UI form validation rules can be altered*/
  if ($_FILES['file']['size'] > $settings['controllers']['upload']['file_max_size_limit']) {
    return 'Uploaded file exceeded file size limit of '.$settings['controllers']['upload']['file_max_size_limit'].' bytes';
  }


  /*Set at least some basic name validation before save it in the server*/
  $file_name = $_FILES['file']['name'];
  if (!preg_match('/^[A-Za-z0-9-_\.]+\.[A-Za-z0-9-_]{1,5}$/', $file_name)) {
    return 'File name "'.$_FILES['file']['name'].'" is not a valid file name';
  }


  /*
    Set some unique file name.
    Example: "image_1.png" => "image_1_1422205667.png"
  */
  $file_name = split('\.', $file_name);
  array_splice( $file_name, sizeof( $file_name )-1, 0, time());
  $file_name = implode('.', $file_name);

  /*Mark the exact location we want the incoming file to be saved as a file and as a DB value*/
  $imagePath      = 'categories/'.$file_name;
  $file_loacation = $settings['controllers']['upload']['destination_folder_path'].'/'.$imagePath;

  /*Cut-paste the uploaded file from its temporary location*/
  if (!move_uploaded_file( $_FILES['file']['tmp_name'], $file_loacation )) {
    return 'Failed to move uploaded file into '.$file_loacation;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to update Category in its main table*/
  $query  = $db->prepare('UPDATE category SET imagePath=:imagePath WHERE id=:id');
  $result = $query->execute(array(
    'id'        => $category['id'],
    'imagePath' => $imagePath
  ));

  /*Prevent further update if current query fail*/
  if (!$result) {
    return 'Unable to update Category Image path in the DB';
  }


  /*Return the updated record so callee can verify the process too*/
  return getCategoryByID( $category['id'] );
}



/*
  Tries to remove all known records for Category with ID '$category_id'.
  Returns an error {string} or {boolean} 'false'.
*/
function deleteCategory($category_id) {
  global $settings;

  /*Be sure we have already existing record*/
  $category = getCategoryByID( $category_id );
  if (gettype($category) !== 'array') {
    return 'Unable to find Category with ID: '.$category_id;
  }


  require_once('./models/subcategories.php');

  /*Be sure no subcategories are bind to the one we want to delete*/
  $subcategories = getSubcategoriesByCategoryID( $category['id'] );
  if (sizeof( $subcategories )) {
    $are_term           = sizeof( $subcategories ) === 1 ? 'is'          : 'are';
    $subcategories_term = sizeof( $subcategories ) === 1 ? 'subcategory' : 'subcategories';

    return 'Unable to delete Category since there '.$are_term.' '.sizeof( $subcategories ).' '.$subcategories_term.' connected to it';
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to delete Category name first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM categoryname WHERE id=:id');
  $result = $query->execute(array('id' => $category['name_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Category name';
  }


  /*Try to delete Category description first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM categorydescription WHERE id=:id');
  $result = $query->execute(array('id' => $category['description_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Category description';
  }


  $query  = $db->prepare('DELETE FROM category WHERE id=:id');
  $result = $query->execute(array('id' => $category['id']));

  if (!$result) {
    return 'Unable to delete Category from its main table';
  }


  /*Remove associated with the record image from the hosting folder*/
  if (!unlink($settings['controllers']['upload']['destination_folder_path'].'/'.$category['imagePath'])) {
    return 'Unable to delete Category image';
  }


  /*Be sure no one can get the deleted information*/
  $deleted_category = getCategoryByID( $category['id'] );
  if (gettype($deleted_category) === 'array') {
    return 'Unable to delete Category';
  }

  return;
}