<?php
/*This file holds all DB functions which effects Subcategories*/


/*Used to convert raw DB result to expected JSON-like result*/
function reorderSubcategoryRecord(&$subcategory) {
  if (gettype($subcategory) !== 'array' ||
      !sizeof($subcategory)
  ) {
    return $subcategory;
  }


  /*Convert name structure 'name_*' to array*/
  $subcategory['name'] = array(
    'bg' => $subcategory['name_bg'],
    'en' => $subcategory['name_en']
  );

  unset( $subcategory['name_bg'] );
  unset( $subcategory['name_en'] );


  /*Convert description structure 'description_*' to array*/
  $subcategory['description'] = array(
    'bg' => $subcategory['description_bg'],
    'en' => $subcategory['description_en']
  );

  unset( $subcategory['description_bg'] );
  unset( $subcategory['description_en'] );

  return $subcategory;
}



/*Return all the info we have for Subcategory with ID '$id' or return 'false'*/
function getSubcategoryByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          subcategory.id         as id,
                          subcategory.categoryId as categoryId,
                          subcategory.imagePath  as imagePath,

                          subcategoryname.id as name_id,
                          subcategoryname.bg as name_bg,
                          subcategoryname.en as name_en,

                          subcategorydescription.id as description_id,
                          subcategorydescription.bg as description_bg,
                          subcategorydescription.en as description_en

                        FROM subcategory

                        JOIN subcategoryname
                        ON subcategory.nameId = subcategoryname.id

                        JOIN subcategorydescription
                        ON subcategory.descriptionId = subcategorydescription.id

                        WHERE subcategory.id = :id
                        LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  /*Set DB values in ordered JSON-like list*/
  return reorderSubcategoryRecord( $query->fetch(PDO::FETCH_ASSOC) );
}



/*Return all Subcategories that are subs of main Category with ID '$categoryId'*/
function getSubcategoriesByCategoryID($categoryId) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          subcategory.id         as id,
                          subcategory.categoryId as categoryId,
                          subcategory.imagePath  as imagePath,

                          subcategoryname.id as name_id,
                          subcategoryname.bg as name_bg,
                          subcategoryname.en as name_en,

                          subcategorydescription.id as description_id,
                          subcategorydescription.bg as description_bg,
                          subcategorydescription.en as description_en

                        FROM subcategory

                        JOIN subcategoryname
                        ON subcategory.nameId = subcategoryname.id

                        JOIN subcategorydescription
                        ON subcategory.descriptionId = subcategorydescription.id

                        WHERE subcategory.categoryId = :categoryId');
  $result = $query->execute(array(
    'categoryId' => $categoryId
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  $subcategories = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($subcategories) === 'array' &&
      sizeof( $subcategories)
  ) {
    foreach ($subcategories as &$subcategory) reorderSubcategoryRecord( $subcategory );
  }


  return $subcategories;
}



/*Returns an indexed array of all Subcategories records we have in DB*/
function getAllSubcategories() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved Subcategories*/
  $query = $db->prepare('SELECT
                          subcategory.id         as id,
                          subcategory.categoryId as categoryId,
                          subcategory.imagePath  as imagePath,

                          subcategoryname.id as name_id,
                          subcategoryname.bg as name_bg,
                          subcategoryname.en as name_en,

                          subcategorydescription.id as description_id,
                          subcategorydescription.bg as description_bg,
                          subcategorydescription.en as description_en

                        FROM subcategory

                        JOIN subcategoryname
                        ON subcategory.nameId = subcategoryname.id

                        JOIN subcategorydescription
                        ON subcategory.descriptionId = subcategorydescription.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  $subcategories = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($subcategories) === 'array' &&
      sizeof( $subcategories)
  ) {
    foreach ($subcategories as &$subcategory) reorderSubcategoryRecord( $subcategory );
  }


  return $subcategories;
}



/*
  Will return an error string message if the incoming '$filters'
  are not suitable for filtering search for Subcategories.
  Please be advised that keys in '$filters' that are not used
  for filtering will be deleted from '$filters'.
*/
function validateSubcategoriesFilters(&$filters) {
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


  if (isset($filters['categoryIds'])) {
    if (gettype($filters['categoryIds']) !== 'array') {
      return 'Filter "categoryIds" must be an array';
    }

    foreach ($filters['categoryIds'] as $index => $id) {
      if (!is_numeric($id) &&
          gettype($id) !== 'integer'
      ) {
        return 'Filter "categoryIds" have an invalid ID at index '.$index;
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
  $valid_filtering_keys = array('ids', 'categoryIds', 'name', 'description', 'imagePath');
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



/*Returns an indexed array of all Subcategories records we have in DB*/
function getFilteredSubcategories($filters) {

  /*Be sure we can safely use the input for filtering*/
  if ($error_message = validateSubcategoriesFilters( $filters )) {
    return $error_message;
  }

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $where_clauses = array();

  if (isset($filters['ids'])) {
    $where_clauses []= 'subcategory.id IN ('.implode(', ', $filters['ids']).')';
    unset( $filters['ids'] );
  }

  if (isset($filters['categoryIds'])) {
    $where_clauses []= 'subcategory.categoryId IN ('.implode(', ', $filters['categoryIds']).')';
    unset( $filters['categoryIds'] );
  }

  if (isset($filters['name'])) {
    $where_clauses []= '(subcategoryname.bg LIKE :name OR
                         subcategoryname.en LIKE :name)';
    $filters['name'] = '%'.$filters['name'].'%';
  }

  if (isset($filters['description'])) {
    $where_clauses []= '(subcategorydescription.bg LIKE :description OR
                         subcategorydescription.en LIKE :description)';
    $filters['description'] = '%'.$filters['description'].'%';
  }

  if (isset($filters['imagePath'])) {
    $where_clauses []= 'subcategory.imagePath LIKE :imagePath';
    $filters['imagePath'] = '%'.$filters['imagePath'].'%';
  }


  /*Filter the Subcategories by the saved rules in '$where_clauses'*/
  $query = $db->prepare('SELECT
                          subcategory.id         as id,
                          subcategory.categoryId as categoryId,
                          subcategory.imagePath  as imagePath,

                          subcategoryname.id as name_id,
                          subcategoryname.bg as name_bg,
                          subcategoryname.en as name_en,

                          subcategorydescription.id as description_id,
                          subcategorydescription.bg as description_bg,
                          subcategorydescription.en as description_en

                        FROM subcategory

                        JOIN subcategoryname
                        ON subcategory.nameId = subcategoryname.id

                        JOIN subcategorydescription
                        ON subcategory.descriptionId = subcategorydescription.id
                        '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  $subcategories = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($subcategories) === 'array' &&
      sizeof( $subcategories)
  ) {
    foreach ($subcategories as &$subcategory) reorderSubcategoryRecord( $subcategory );
  }


  return $subcategories;
}



/*
  Returns a list out of '$properties' that can safely be used to create or update a Subcategory.
  if 2nd argument '$mandatory_validation' is set to 'true' then only set properties will be validated,
  e.g. if 'type' is set to 'Z' it will be invalid but if not set at all, no error will be send.
*/
function validateSubcategoryProperties(&$properties, $mandatory_validation = true) {
  if (!isset( $properties) ||
      gettype($properties) !== 'array'
  ) {
    return 'Invalid properties';
  }


  if ($mandatory_validation ||
      isset($properties['categoryId'])
  ) {
    if (!isset(     $properties['categoryId']) ||
        !is_numeric($properties['categoryId'])
    ) {
      return 'Invalid "categoryId" property';
    }


    /*Be sure we have a Category with the requested ID*/
    require_once('./models/categories.php');
    $categoty = getCategoryByID( $properties['categoryId'] );

    if (gettype($categoty) !== 'array') {
      return 'Invalid "categoryId" property value.';
    }
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
  $valid_property_keys = array('categoryId', 'name', 'description');
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
  Use 'validateSubcategoryProperties() to save already valid Subcategory values.
  Will return error {string} in case of failure or newly created Subcategory as {array}
*/
function createSubcategory($properties) {

  /*Be sure we can use all of the user input to create new Subcategory*/
  $properties = validateSubcategoryProperties( $properties );
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Try to create Subcategory name first in order to use the Name ID later
    when we create the Subcategory in its main table
  */
  $query  = $db->prepare('INSERT INTO subcategoryname ( en,  bg) VALUES
                                                      (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['name']['en'],
    'bg' => $properties['name']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Subcategory name';
  }


  /*Set reference ID to already saved name property*/
  unset( $properties['name'] );
  $properties['nameId'] = $db->lastInsertId();


  /*
    Try to create Subcategory name first in order to use the Description ID later
    when we create the Subcategory in its main table
  */
  $query  = $db->prepare('INSERT INTO subcategorydescription ( en,  bg) VALUES
                                                             (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['description']['en'],
    'bg' => $properties['description']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Subcategory description';
  }


  /*Set reference ID to already saved description property*/
  unset( $properties['description'] );
  $properties['descriptionId'] = $db->lastInsertId();


  /*Try to create Subcategory in its main table*/
  $query  = $db->prepare('INSERT INTO subcategory ( categoryId,  nameId,  descriptionId) VALUES
                                                  (:categoryId, :nameId, :descriptionId)');
  $result = $query->execute( $properties );

  if (!$result) {
    return 'Unable to create Subcategory';
  }


  /*Try to show the creation result*/
  return getSubcategoryByID( $db->lastInsertId() );
}



/*
  Update the Subcategory with ID '$subcategory_id' with the values from '$properties'.
  Values validation is secured with 'validateSubcategoryProperties()'
*/
function updateSubcategory($subcategory_id, $properties) {

  /*Be sure we have already existing record*/
  $subcategory = getSubcategoryByID( $subcategory_id );
  if (!$subcategory) {
    return 'Unable to find Subcategory with ID: '.$subcategory_id;
  }


  /*Be sure we can use only correctly set Subcategory properties*/
  $properties = validateSubcategoryProperties( $properties, false);
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update Subcategory name since its not in Subcategory main table*/
  if (isset(  $properties['name'])             &&
      gettype($properties['name']) === 'array' &&
      sizeof( $properties['name'])
  ) {

    $set_clauses = array();
    foreach ($properties['name'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Subcategory name*/
    $query  = $db->prepare('UPDATE subcategoryname SET '.implode(', ', $set_clauses).' WHERE id='.$subcategory['name_id']);
    $result = $query->execute( $properties['name'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Subcategory name';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['name'] );
  }


  /*Check if we need to update Subcategory description since its not in Subcategory main table*/
  if (isset(  $properties['description'])             &&
      gettype($properties['description']) === 'array' &&
      sizeof( $properties['description'])
  ) {

    $set_clauses = array();
    foreach ($properties['description'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Subcategory description*/
    $query  = $db->prepare('UPDATE subcategorydescription SET '.implode(', ', $set_clauses).' WHERE id='.$subcategory['description_id']);
    $result = $query->execute( $properties['description'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Subcategory description';
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

    /*Try to update Subcategory in its main table*/
    $query  = $db->prepare('UPDATE subcategory SET '.implode(', ', $set_clauses).' WHERE id='.$subcategory['id']);
    $result = $query->execute( $properties );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Subcategory';
    }
  }


  /*Return the updated record so callee can verify the process too*/
  return getSubcategoryByID( $subcategory['id'] );
}



/*
  Try to update the Subcategory image file
  by saving valid file from '$_FILE' and
  updating its new path in the DB.
*/
function updateSubcategoryImage($subcategory_id) {
  global $settings;

  /*Be sure we have already existing record*/
  $subcategory = getSubcategoryByID( $subcategory_id );
  if (!$subcategory) {
    return 'Unable to find Subcategory with ID: '.$subcategory_id;
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
  $imagePath      = 'subcategories/'.$file_name;
  $file_loacation = $settings['controllers']['upload']['destination_folder_path'].'/'.$imagePath;

  /*Cut-paste the uploaded file from its temporary location*/
  if (!move_uploaded_file( $_FILES['file']['tmp_name'], $file_loacation )) {
    return 'Failed to move uploaded file into '.$file_loacation;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to update Subcategory in its main table*/
  $query  = $db->prepare('UPDATE subcategory SET imagePath=:imagePath WHERE id=:id');
  $result = $query->execute(array(
    'id'        => $subcategory['id'],
    'imagePath' => $imagePath
  ));

  /*Prevent further update if current query fail*/
  if (!$result) {
    return 'Unable to update Subcategory Image path in the DB';
  }


  /*Return the updated record so callee can verify the process too*/
  return getSubcategoryByID( $subcategory['id'] );
}



/*
  Tries to remove all known records for Subcategory with ID '$subcategory_id'.
  Returns an error {string} or {boolean} 'false'.
*/
function deleteSubcategory($subcategory_id) {
  global $settings;

  /*Be sure we have already existing record*/
  $subcategory = getSubcategoryByID( $subcategory_id );
  if (gettype($subcategory) !== 'array') {
    return 'Unable to find Subcategory with ID: '.$subcategory_id;
  }


  require_once('./models/events.php');

  /*Be sure no events are bind to the one we want to delete*/
  $events = getEventsBySubcategoryID( $subcategory['id'] );
  if (sizeof( $events )) {
    $are_term    = sizeof( $events ) === 1 ? 'is'    : 'are';
    $events_term = sizeof( $events ) === 1 ? 'event' : 'events';

    return 'Unable to delete Subcategory since there '.$are_term.' '.sizeof( $events ).' '.$events_term.' connected to it';
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to delete Subcategory name first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM subcategoryname WHERE id=:id');
  $result = $query->execute(array('id' => $subcategory['name_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Subcategory name';
  }


  /*Try to delete Subcategory description first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM subcategorydescription WHERE id=:id');
  $result = $query->execute(array('id' => $subcategory['description_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Subcategory description';
  }


  $query  = $db->prepare('DELETE FROM subcategory WHERE id=:id');
  $result = $query->execute(array('id' => $subcategory['id']));

  if (!$result) {
    return 'Unable to delete Subcategory from its main table';
  }


  /*Remove associated with the record image from the hosting folder*/
  if (!unlink($settings['controllers']['upload']['destination_folder_path'].'/'.$subcategory['imagePath'])) {
    return 'Unable to delete Subcategory image';
  }


  /*Be sure no one can get the deleted information*/
  $deleted_subcategory = getSubcategoryByID( $subcategory['id'] );
  if (gettype($deleted_subcategory) === 'array') {
    return 'Unable to delete Subcategory';
  }

  return;
}