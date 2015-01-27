<?php
/*This file holds all DB functions which effects Partners*/


/*Used to convert raw DB result to expected JSON-like result*/
function reorderPartnerRecord(&$partner) {
  if (gettype($partner) !== 'array' ||
      !sizeof($partner)
  ) {
    return $partner;
  }


  /*Convert name structure 'name_*' to array*/
  $partner['name'] = array(
    'bg' => $partner['name_bg'],
    'en' => $partner['name_en']
  );

  unset( $partner['name_bg'] );
  unset( $partner['name_en'] );

  return $partner;
}



/*Return all the info we have for Partner with ID '$id' or return 'false'*/
function getPartnerByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          partner.id        as id,
                          partner.imagePath as imagePath,
                          partner.link      as link,
                          partnername.id    as name_id,
                          partnername.bg    as name_bg,
                          partnername.en    as name_en

                        FROM partner
                        JOIN partnername
                        ON partner.nameId = partnername.id
                        WHERE partner.id = :id
                        LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  /*Set DB values in ordered JSON-like list*/
  return reorderPartnerRecord( $query->fetch(PDO::FETCH_ASSOC) );
}



/*Returns an indexed array of all Partners records we have in DB*/
function getAllPartners() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved Partners*/
  $query = $db->prepare('SELECT
                          partner.id        as id,
                          partner.imagePath as imagePath,
                          partner.link      as link,
                          partnername.id    as name_id,
                          partnername.bg    as name_bg,
                          partnername.en    as name_en

                        FROM partner
                        JOIN partnername
                        ON partner.nameId = partnername.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  $partners = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($partners) === 'array' &&
      sizeof( $partners)
  ) {
    foreach ($partners as &$partner) reorderPartnerRecord( $partner );
  }


  return $partners;
}



/*
  Will return an error string message if the incoming '$filters'
  are not suitable for filtering search for Partners.
  Please be advised that keys in '$filters' that are not used
  for filtering will be deleted from '$filters'.
*/
function validatePartnersFilters(&$filters) {
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


  if (isset($filters['link'])) {
    if (gettype($filters['link']) !== 'string' ||
        !sizeof($filters['link'])
    ) {
      return 'Filter "link" must be a non empty string';
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
  $valid_filtering_keys = array('ids', 'name', 'link', 'imagePath');
  foreach ($filters as $key => $value) {
    if (!in_array($key, $valid_filtering_keys, true)) {
      unset( $filters[ $key ] );
    }
  }


  /*
    Return error free response
    since none of the above found an issue
  */
  return;
}



/*Returns an indexed array of all Partners records we have in DB*/
function getFilteredPartners($filters) {

  /*Be sure we can safely use the input for filtering*/
  if ($error_message = validatePartnersFilters( $filters )) {
    return $error_message;
  }

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $where_clauses = array();

  if (isset($filters['ids'])) {
    $where_clauses []= 'partner.id IN ('.implode(', ', $filters['ids']).')';
    unset( $filters['ids'] );
  }

  if (isset($filters['name'])) {
    $where_clauses []= '(partnername.bg LIKE :name OR
                         partnername.en LIKE :name)';
    $filters['name'] = '%'.$filters['name'].'%';
  }

  if (isset($filters['link'])) {
    $where_clauses []= 'partner.link LIKE :link';
    $filters['link'] = '%'.$filters['link'].'%';
  }

  if (isset($filters['imagePath'])) {
    $where_clauses []= 'partner.imagePath LIKE :imagePath';
    $filters['imagePath'] = '%'.$filters['imagePath'].'%';
  }


  /*Filter the Partners by the saved rules in '$where_clauses'*/
  $query = $db->prepare('SELECT
                          partner.id        as id,
                          partner.imagePath as imagePath,
                          partner.link      as link,
                          partnername.id    as name_id,
                          partnername.bg    as name_bg,
                          partnername.en    as name_en

                         FROM partner
                         JOIN partnername
                         ON partner.nameId = partnername.id
                         '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  $partners = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($partners) === 'array' &&
      sizeof( $partners)
  ) {
    foreach ($partners as &$partner) reorderPartnerRecord( $partner );
  }


  return $partners;
}



/*
  Returns a list out of '$properties' that can safely be used to create or update a Partner.
  if 2nd argument '$mandatory_validation' is set to 'true' then only set properties will be validated,
  e.g. if 'type' is set to 'Z' it will be invalid but if not set at all, no error will be send.
*/
function validatePartnerProperties(&$properties, $mandatory_validation = true) {
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
      isset($properties['link'])
  ) {
    if (!isset($properties['link']) ||
        !filter_var($properties['link'], FILTER_VALIDATE_URL)
    ) {
      return 'Invalid "link" property';
    }
  }


  /*Be sure to return a list containing only valid properties as keys*/
  $valid_property_keys = array('name', 'link');
  foreach ($properties as $key => $value) {
    if (!in_array($key, $valid_property_keys, true)) {
      unset( $properties[ $key ] );
    }

    /*Be sure to leave only valid name values*/
    if ($key === 'name' &&
        gettype($properties['name']) === 'array'
    ) {
      $valid_name_keys = array('bg', 'en');

      foreach ($properties['name'] as $name_key => $name_value) {
        if (!in_array($name_key, $valid_name_keys, true)) {
          unset( $properties['name'][ $name_key ] );
        }
      }
    }/*End of "if key is name"*/
  }


  if (isset(   $properties['name'] ) &&
      !sizeof( $properties['name'] )
  ) {
    unset( $properties['name'] );
  }


  return $properties;
}



/*
  Use 'validatePartnerProperties() to save already valid Partner values.
  Will return error {string} in case of failure or newly created Partner as {array}
*/
function createPartner($properties) {

  /*Be sure we can use all of the user input to create new Partner*/
  $properties = validatePartnerProperties( $properties );
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Try to create Partner name first in order to use the Name ID later
    when we create the Partner in its main table
  */
  $query  = $db->prepare('INSERT INTO partnername ( en,  bg) VALUES
                                                  (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['name']['en'],
    'bg' => $properties['name']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Partner name';
  }


  /*Set reference ID to already saved name property*/
  unset( $properties['name'] );
  $properties['nameId'] = $db->lastInsertId();

  /*Try to create Partner in its main table*/
  $query  = $db->prepare('INSERT INTO partner ( link,  nameId) VALUES
                                              (:link, :nameId)');
  $result = $query->execute( $properties );

  if (!$result) {
    return 'Unable to create Partner';
  }


  /*Try to show the creation result*/
  return getPartnerByID( $db->lastInsertId() );
}



/*
  Update the Partner with ID '$partner_id' with the values from '$properties'.
  Values validation is secured with 'validatePartnerProperties()'
*/
function updatePartner($partner_id, $properties) {

  /*Be sure we have already existing record*/
  $partner = getPartnerByID( $partner_id );
  if (!$partner) {
    return 'Unable to find Partner with ID: '.$partner_id;
  }


  /*Be sure we can use only correctly set Partner properties*/
  $properties = validatePartnerProperties( $properties, false);
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update Partner name since its not in Partner main table*/
  if (isset(  $properties['name'])             &&
      gettype($properties['name']) === 'array' &&
      sizeof( $properties['name'])
  ) {

    $set_clauses = array();
    foreach ($properties['name'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Partner name*/
    $query  = $db->prepare('UPDATE partnername SET '.implode(', ', $set_clauses).' WHERE id='.$partner['name_id']);
    $result = $query->execute( $properties['name'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Partner name';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['name'] );
  }


  /*Check if there are properties to be updated in the main table*/
  if (gettype($properties) === 'array' &&
      sizeof( $properties)
  ) {
    $set_clauses = array();
    foreach ($properties as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Partner in its main table*/
    $query  = $db->prepare('UPDATE partner SET '.implode(', ', $set_clauses).' WHERE id='.$partner['id']);
    $result = $query->execute( $properties );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Partner';
    }
  }


  /*Return the updated record so callee can verify the process too*/
  return getPartnerByID( $partner['id'] );
}



/*
  Try to update the Partner image file
  by saving valid file from '$_FILE' and
  updating its new path in the DB.
*/
function updatePartnerImage($partner_id) {
  global $settings;

  /*Be sure we have already existing record*/
  $partner = getPartnerByID( $partner_id );
  if (!$partner) {
    return 'Unable to find Partner with ID: '.$partner_id;
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
  $imagePath      = 'partners/'.$file_name;
  $file_loacation = $settings['controllers']['upload']['destination_folder_path'].'/'.$imagePath;

  /*Cut-paste the uploaded file from its temporary location*/
  if (!move_uploaded_file( $_FILES['file']['tmp_name'], $file_loacation )) {
    return 'Failed to move uploaded file into '.$file_loacation;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to update Partner in its main table*/
  $query  = $db->prepare('UPDATE partner SET imagePath=:imagePath WHERE id=:id');
  $result = $query->execute(array(
    'id'        => $partner['id'],
    'imagePath' => $imagePath
  ));

  /*Prevent further update if current query fail*/
  if (!$result) {
    return 'Unable to update Partner Image path in the DB';
  }


  /*Return the updated record so callee can verify the process too*/
  return getPartnerByID( $partner['id'] );
}



/*
  Tries to remove all known records for Partner with ID '$partner_id'.
  Returns an error {string} or {boolean} 'false'.
*/
function deletePartner($partner_id) {
  global $settings;

  /*Be sure we have already existing record*/
  $partner = getPartnerByID( $partner_id );
  if (gettype($partner) !== 'array') {
    return 'Unable to find Partner with ID: '.$partner_id;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to delete Partner name first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM partnername WHERE id=:id');
  $result = $query->execute(array('id' => $partner['name_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Partner name';
  }


  $query  = $db->prepare('DELETE FROM partner WHERE id=:id');
  $result = $query->execute(array('id' => $partner['id']));

  if (!$result) {
    return 'Unable to delete Partner from its main table';
  }


  /*Remove associated with the record image from the hosting folder*/
  $file_loacation = $settings['controllers']['upload']['destination_folder_path'].'/'.$partner['imagePath'];
  if (is_file( $file_loacation )) {
    if (!unlink( $file_loacation )) {
      return 'Unable to delete Partner image';
    }
  }


  /*Be sure no one can get the deleted information*/
  $deleted_partner = getPartnerByID( $partner['id'] );
  if (gettype($deleted_partner) === 'array') {
    return 'Unable to delete Partner';
  }

  return;
}