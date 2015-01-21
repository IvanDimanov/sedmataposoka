<?php
/*
  This file is meant to be a bridge between Login/Authentication users records in the DB
  and its usage by the business logic in the 'controllers' folder
*/


/*Returns all know user information or 'false'*/
function getUserByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to find the user by its unique ID*/
  $query  = $db->prepare('SELECT * FROM admin WHERE id = :id LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  return $query->fetch(PDO::FETCH_ASSOC);
}


/*Returns all know user information or 'false'*/
function getUserByName($name) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to find the user by its unique name*/
  $query  = $db->prepare('SELECT * FROM admin WHERE name = :name LIMIT 1');
  $result = $query->execute(array(
    'name' => $name
  ));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*
  If the incoming data match an user record in our DB
  by a criteria of 'name' AND 'password' and have a 'is_active === true',
  the function will return the entire user record we have for it,
  otherwise it'll return 'false'
*/
function authenticate($name, $password) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to find the user by its unique name*/
  $query  = $db->prepare('SELECT * FROM admin where password = sha( concat(:password, salt)) AND name=:name AND is_active = 1 LIMIT 1');
  $result = $query->execute(array(
    'name'     => $name,
    'password' => $password
  ));

  return $query->fetch(PDO::FETCH_ASSOC);
}



/*
  This function will tell if the current IP is in the limit of "total error logins" for "time after captcha is required"
  @param {Boolean} $increment_counter - will tell if we need to mark the function call as an error login attempt
*/
function checkErrorLogins($increment_counter = false) {
  global $settings;

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $ip = $_SERVER['REMOTE_ADDR'];

  /*Check if the same IP address have some previous error login attempts*/
  $query  = $db->prepare('SELECT * FROM error_login WHERE ip = :ip');
  $result = $query->execute(array(
    'ip' => $ip
  ));


  /*Let this login attempt valid since we have no data to check it*/
  if (!$result) {
    return true;
  }


  /*Load safely all DB parameters*/
  $error_logins = $query->fetch(PDO::FETCH_ASSOC);
  $error_logins['count']            = isset($error_logins['count']           ) ? (int) $error_logins['count']            : 0;
  $error_logins['last_error_login'] = isset($error_logins['last_error_login']) ? (int) $error_logins['last_error_login'] : 0;


  /*
    Remove error records for the current IP
    if he didn't made an error in a time more than 'time_after_captcha_is_required'
  */
  if (time() - $error_logins['last_error_login'] > $settings['controllers']['login']['time_after_captcha_is_required']) {

    /*Remove outdated error records*/
    if (isset($error_logins['id'])) {
      $query = $db->prepare('DELETE FROM error_login WHERE id=:id');
      $query->execute(array(
        'id' => $error_logins['id']
      ));
    }


    $error_logins = array(
      'count'            => 0,
      'last_error_login' => 0
    );
  }


  /*
    Counter need to be incremented only if the error login is a fact
    but not if the callee just want to check the error status
  */
  if ($increment_counter) {
    $error_logins['count']++;
    $error_logins['last_error_login'] = time();
  }


  /*
    Check if the IP finally logged in correctly or
    we still need to keep track of his error attempts
  */
  if ($error_logins['count']) {

    /*Check if that's the first time IP made a mistake*/
    if (isset($error_logins['id'])) {

      $query = $db->prepare('UPDATE error_login SET count=:count, last_error_login=:last_error_login WHERE id=:id');
      $query->execute(array(
        'id'               => $error_logins['id'],
        'count'            => $error_logins['count'],
        'last_error_login' => $error_logins['last_error_login']
      ));

    } else {

      /*Start counting each IP error login attempt*/
      $query = $db->prepare('INSERT error_login  ( ip,  count,  last_error_login)
                                          VALUES (:ip, :count, :last_error_login)');
      $query->execute(array(
        'ip'               => $ip,
        'count'            => $error_logins['count'],
        'last_error_login' => $error_logins['last_error_login']
      ));
    }
  }


  /*
    Tell the callee if the current IP exceeded
    the limit of total error logins
  */
  return $error_logins['count'] < $settings['controllers']['login']['max_error_attempts'];
}



/*Run a series of data checks before saving '$new_user' in the list of all accessible users*/
/*NOTE: Returns an error message or null*/
function createUser($new_user) {

  /*Trim all inner strings*/
  trimAll( $new_user );

  /*Validate mandatory fields*/
  if (!isset( $user_request['name'])              ||
      gettype($user_request['name']) !== 'string' ||
      !sizeof($user_request['name'])
  ) {
    return 'Invalid user "name" property';
  }
  if (!isset( $new_user['password']) ||
      gettype($new_user['password']) != 'string' ||
      strlen( $new_user['password']) < 4
  ) {
    return 'Invalid user "password" property';
  }
  if (!isset( $new_user['type']) ||
      gettype($new_user['type']) != 'string' ||
      strlen( $new_user['type']) < 2
  ) {
    return 'Invalid user "type" property';
  }


  /*Prevent overwriting an existing user*/
  if (!getUserByName( $user_request['name'] )) {
    return 'User with name \''.$user_request['name'].'\' already exist';
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to create new user in its main table*/
  $query = $db->prepare('INSERT admin ( name,  type,  password,  is_active)
                               VALUES (:name, :type, :password, :is_active)
  ');
  $result = $query->execute(array(
    'name'      => $new_user['name'],
    'type'      => $new_user['type'],
    'password'  => $new_user['password'],
    'is_active' => false
  ));


  /*Be sure the user is saved and available in the DB*/
  if (!$result ||
      !getUserByName( $user_request['name'] )
  ) {
    return 'Unable to create user';
  }


  /*Exit with no error message sent*/
  return;
}



/*
  Find the user with specified '$id' and
  replace all of his data with the one from '$update_data'.
  NOTE: Returns an error message or null
*/
function updateUser($id, $update_data) {

  /*Be sure we update already existing user*/
  if (!getUserByID( $id )) {
    return 'User with ID \''.$id.'\' does not exist';
  }


  /*Secure basic user property holder*/
  if (gettype($update_data) !== 'array') {
    return 'Invalid 2nd argument "user data to be updated"';
  }


  /*Used to generally update every user DB property and return a proper error message on fail*/
  function updateProperty($id, $key, $value) {

    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    $query  = $db->prepare('UPDATE admin SET '.$key.'=:value WHERE id=:id');
    $result = $query->execute(array(
      'id'    => $id,
      'value' => $value
    ));

    /*Be sure the user is updated correctly in the DB*/
    $user       = getUserByID( $id );
    $user_value = $user[ $key ];
    $user_value = gettype($value) === 'boolean' ? getCommonBoolean( $user_value ) : $user_value;

    if (!$result ||
        $user_value != $value
    ) {
      return 'Unable to update user "'.$key.'" property';
    }
  }


  if (isset(    $update_data['name'])) {
    if (getname($update_data['name']) !== 'string' ||
         strlen($update_data['name']) < 2
    ) {
      return 'Invalid user "name" property';
    }

    /*Do not update the User name if the new value is the same as the old one*/
    $user = getUserByID( $id );
    if ($user['name'] != $update_data['name']) {

      /*
        Be sure that event when updated
        all users will continue to have unique names
      */
      if (getUserByName( $update_data['name'] )) {
        return 'User with name "'.$update_data['name'].'" already exist';
      }


      if ($error_message = updateProperty( $id, 'name', $update_data['name'] )) {
        return $error_message;
      }
    }
  }


  if (isset(    $update_data['type'])) {
    if (gettype($update_data['type']) !== 'string' ||
         strlen($update_data['type']) < 2
    ) {
      return 'Invalid user "type" property';
    }

    if ($error_message = updateProperty( $id, 'type', $update_data['type'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['password'])) {
    if (gettype($update_data['password']) !== 'string' ||
         strlen($update_data['password']) < 4
    ) {
      return 'Invalid user "password" property';
    }

    if ($error_message = updateProperty( $id, 'password', $update_data['password'] )) {
      return $error_message;
    }
  }


  if (isset($update_data['is_active'])) {
    $update_data['is_active'] = getCommonBoolean( $update_data['is_active'] );

    if (gettype($update_data['is_active']) !== 'boolean') {
      return 'Invalid user "is_active" property';
    }

    if ($error_message = updateProperty( $id, 'is_active', $update_data['is_active'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('access_token_value', $update_data)) {
    if (gettype($update_data['access_token_value']) !== 'NULL' && (
        gettype($update_data['access_token_value']) !== 'string' ||
         strlen($update_data['access_token_value']) < 3
      )
    ) {
      return 'Invalid "access_token_value" property';
    }

    if ($error_message = updateProperty( $id, 'access_token_value', $update_data['access_token_value'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('access_token_created_at', $update_data)) {
    if (gettype($update_data['access_token_created_at']) !== 'NULL' && (
        gettype($update_data['access_token_created_at']) !== 'integer' ||
                $update_data['access_token_created_at']  < 0
      )
    ) {
      return 'Invalid "access_token_created_at" property';
    }

    if ($error_message = updateProperty( $id, 'access_token_created_at', $update_data['access_token_created_at'] )) {
      return $error_message;
    }
  }


  /*All properties were updated successfully*/
  return;
}