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
    'id' => $request_id
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


  /*Basic unique request ID validation*/
  if (!isset($new_user['request_id']) ||
      !preg_match('/^[1-9][0-9]{1,20}$/', $new_user['request_id'])
  ) {
    return 'Invalid "request_id" property';
  }


  /*Be sure not to overwrite an already existing user*/
  if (getUserByID( $new_user['request_id'] )) {
    return 'User with ID \''.$new_user['request_id'].'\' already exists';
  }


  /*Validate mandatory fields*/
  if (!isset($new_user['email']) || !filter_var($new_user['email'], FILTER_VALIDATE_EMAIL)) {
    return 'Invalid user "email" property';
  }
  if (!isset( $new_user['password']) ||
      gettype($new_user['password']) != 'string' ||
      strlen( $new_user['password']) < 4
  ) {
    return 'Invalid user "password" property';
  }
  if (!isset( $new_user['first_name']) ||
      gettype($new_user['first_name']) != 'string' ||
      strlen( $new_user['first_name']) < 2
  ) {
    return 'Invalid user "first_name" property';
  }
  if (!isset( $new_user['last_name']) ||
      gettype($new_user['last_name']) != 'string' ||
      strlen( $new_user['last_name']) < 2
  ) {
    return 'Invalid user "last_name" property';
  }
  if (!isset( $new_user['phone']) ||
      gettype($new_user['phone']) != 'string' ||
      strlen( $new_user['phone']) < 6
  ) {
    return 'Invalid user "phone" property';
  }
  if (!isset( $new_user['address_city']) ||
      gettype($new_user['address_city']) != 'string' ||
      strlen( $new_user['address_city']) < 2
  ) {
    return 'Invalid user "address_city" property';
  }
  if (!isset( $new_user['address_street']) ||
      gettype($new_user['address_street']) != 'string' ||
      strlen( $new_user['address_street']) < 5
  ) {
    return 'Invalid user "address_street" property';
  }


  /*
    if an incorrect 'address_postcode' we'll just gonna set a default 'null'
    since it's an optional property
  */
  if (!isset( $new_user['address_postcode']) ||
      gettype($new_user['address_postcode']) != 'string'
  ) {
    $new_user['address_postcode'] = null;
  }


  /*Validate all 'invoice' properties if 'invoice_required' is set*/
  if (isset($new_user['invoice_required']) &&
            $new_user['invoice_required'] == true
  ) {
    if (!isset( $new_user['invoice_company_name']) ||
        gettype($new_user['invoice_company_name']) != 'string' ||
        strlen( $new_user['invoice_company_name']) < 3
    ) {
      return 'Invalid "invoice_company_name" property';
    }
    if (!isset( $new_user['invoice_company_number']) ||
        gettype($new_user['invoice_company_number']) != 'string' ||
        strlen( $new_user['invoice_company_number']) < 3
    ) {
      return 'Invalid "invoice_company_number" property';
    }
    if (!isset( $new_user['invoice_company_bulstat']) ||
        gettype($new_user['invoice_company_bulstat']) != 'string' ||
        strlen( $new_user['invoice_company_bulstat']) < 3
    ) {
      return 'Invalid "invoice_company_bulstat" property';
    }
    if (!isset( $new_user['invoice_company_city']) ||
        gettype($new_user['invoice_company_city']) != 'string' ||
        strlen( $new_user['invoice_company_city']) < 2
    ) {
      return 'Invalid "invoice_company_city" property';
    }
    if (!isset( $new_user['invoice_company_address']) ||
        gettype($new_user['invoice_company_address']) != 'string' ||
        strlen( $new_user['invoice_company_address']) < 5
    ) {
      return 'Invalid "invoice_company_address" property';
    }
    if (!isset( $new_user['invoice_company_dds']) ||
        gettype($new_user['invoice_company_dds']) != 'string' ||
        strlen( $new_user['invoice_company_dds']) < 2
    ) {
      return 'Invalid "invoice_company_dds" property';
    }

  } else {

    /*All 'invoice' properties must be set even if not sent*/
    $new_user['invoice_required'] = false;
    $new_user['invoice'         ] = array(
      'company_name'    => null,
      'company_number'  => null,
      'company_bulstat' => null,
      'company_city'    => null,
      'company_address' => null,
      'company_dds'     => null
    );
  }


  /*
    if an incorrect 'additional_info' we'll just gonna set a default 'null'
    since it's an optional property
  */
  if (!isset( $new_user['additional_info']) ||
      gettype($new_user['additional_info']) != 'string'
  ) {
    $new_user['additional_info'] = null;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();

  /*Try to create new user in its main table*/
  $query = $db->prepare('INSERT user ( request_id,  is_active,  user_type,  email,  password,  first_name,  last_name,  phone,  address_city,  address_street,  address_postcode,  invoice_required,  invoice_company_name,  invoice_company_number,  invoice_company_bulstat,  invoice_company_address,  invoice_company_city,  invoice_company_dds,  additional_info)
                              VALUES (:request_id, :is_active, :user_type, :email, :password, :first_name, :last_name, :phone, :address_city, :address_street, :address_postcode, :invoice_required, :invoice_company_name, :invoice_company_number, :invoice_company_bulstat, :invoice_company_address, :invoice_company_city, :invoice_company_dds, :additional_info)
  ');
  $result = $query->execute(array(
    'request_id'              => $new_user['request_id'],
    'is_active'               => false,
    'user_type'               => 'user',
    'email'                   => $new_user['email'],
    'password'                => $new_user['password'],
    'first_name'              => $new_user['first_name'],
    'last_name'               => $new_user['last_name'],
    'phone'                   => $new_user['phone'],
    'address_city'            => $new_user['address_city'],
    'address_street'          => $new_user['address_street'],
    'address_postcode'        => $new_user['address_postcode'],
    'invoice_required'        => $new_user['invoice_required'],
    'invoice_company_name'    => $new_user['invoice_company_name'],
    'invoice_company_number'  => $new_user['invoice_company_number'],
    'invoice_company_bulstat' => $new_user['invoice_company_bulstat'],
    'invoice_company_address' => $new_user['invoice_company_address'],
    'invoice_company_city'    => $new_user['invoice_company_city'],
    'invoice_company_dds'     => $new_user['invoice_company_dds'],
    'additional_info'         => $new_user['additional_info']
  ));


  /*Be sure the user is saved and available in the DB*/
  if (!$result ||
      !getUserByID( $new_user['request_id'] )
  ) {
    return 'Unable to create user';
  }


  /*Exit with no error message sent*/
  return;
}



/*
  Find the user with specified '$request_id' and
  replace all of his data with the one from '$update_data'.
  NOTE: Returns an error message or null
*/
function updateUser($request_id, $update_data) {

  /*Be sure we update already existing user*/
  if (!getUserByID( $request_id )) {
    return 'User with ID \''.$request_id.'\' does not exist';
  }


  /*Secure basic user property holder*/
  if (gettype($update_data) != 'array') {
    return 'Invalid 2nd argument "user data to be updated"';
  }


  /*Used to generally update every user DB property and return a proper error message on fail*/
  function updateProperty($request_id, $key, $value) {

    /*Access the common DB connection handler*/
    require_once('./models/db_manager.php');
    $db = getDBConnection();

    $query  = $db->prepare('UPDATE user SET '.$key.'=:value WHERE request_id=:request_id');
    $result = $query->execute(array(
      'value'      => $value,
      'request_id' => $request_id
    ));

    /*Be sure the user is updated correctly in the DB*/
    $user       = getUserByID( $request_id );
    $user_value = $user[ $key ];
    $user_value = gettype($value) == 'boolean' ? getCommonBoolean( $user_value ) : $user_value;

    if (!$result ||
        $user_value != $value
    ) {
      return 'Unable to update user "'.$key.'" property';
    }
  }


  if (isset($update_data['email'])) {
    if (!filter_var($update_data['email'], FILTER_VALIDATE_EMAIL)) {
      return 'Invalid user "email" property';
    }

    if ($error_message = updateProperty( $request_id, 'email', $update_data['email'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['password'])) {
    if (gettype($update_data['password']) != 'string' ||
         strlen($update_data['password']) < 4
    ) {
      return 'Invalid user "password" property';
    }

    if ($error_message = updateProperty( $request_id, 'password', $update_data['password'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['first_name'])) {
    if (gettype($update_data['first_name']) != 'string' ||
         strlen($update_data['first_name']) < 2
    ) {
      return 'Invalid user "first_name" property';
    }

    if ($error_message = updateProperty( $request_id, 'first_name', $update_data['first_name'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['last_name'])) {
    if (gettype($update_data['last_name']) != 'string' ||
         strlen($update_data['last_name']) < 2
    ) {
      return 'Invalid user "last_name" property';
    }

    if ($error_message = updateProperty( $request_id, 'last_name', $update_data['last_name'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['phone'])) {
    if (gettype($update_data['phone']) != 'string' ||
         strlen($update_data['phone']) < 6
    ) {
      return 'Invalid user "phone" property';
    }

    if ($error_message = updateProperty( $request_id, 'phone', $update_data['phone'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['address_city'])) {
    if (gettype($update_data['address_city']) != 'string' ||
         strlen($update_data['address_city']) < 2
     ) {
      return 'Invalid user "address_city" property';
    }

    if ($error_message = updateProperty( $request_id, 'address_city', $update_data['address_city'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['address_street'])) {
    if (gettype($update_data['address_street']) != 'string' ||
         strlen($update_data['address_street']) < 5
     ) {
      return 'Invalid user "address_street" property';
    }

    if ($error_message = updateProperty( $request_id, 'address_street', $update_data['address_street'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('address_postcode', $update_data)) {
    if (gettype($update_data['address_postcode']) != 'NULL' && (
        gettype($update_data['address_postcode']) != 'string' ||
         strlen($update_data['address_postcode']) < 5
      )
     ) {
      return 'Invalid user "address_postcode" property';
    }

    if ($error_message = updateProperty( $request_id, 'address_postcode', $update_data['address_postcode'] )) {
      return $error_message;
    }
  }


  if (isset($update_data['is_active'])) {
    $update_data['is_active'] = getCommonBoolean( $update_data['is_active'] );

    if (gettype($update_data['is_active']) != 'boolean') {
      return 'Invalid user "is_active" property';
    }

    if ($error_message = updateProperty( $request_id, 'is_active', $update_data['is_active'] )) {
      return $error_message;
    }
  }


  if (isset($update_data['invoice_required'])) {
    $update_data['invoice_required'] = getCommonBoolean( $update_data['invoice_required'] );

    if (gettype($update_data['invoice_required']) != 'boolean'
    ) {
      return 'Invalid user "invoice_required" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_required', $update_data['invoice_required'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('invoice_company_name', $update_data)) {
    if (gettype($update_data['invoice_company_name']) != 'NULL' && (
        gettype($update_data['invoice_company_name']) != 'string' ||
         strlen($update_data['invoice_company_name']) < 3
      )
    ) {
      return 'Invalid "invoice_company_name" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_company_name', $update_data['invoice_company_name'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('invoice_company_number', $update_data)) {
    if (gettype($update_data['invoice_company_number']) != 'NULL' && (
        gettype($update_data['invoice_company_number']) != 'string' ||
         strlen($update_data['invoice_company_number']) < 3
      )
    ) {
      return 'Invalid "invoice_company_number" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_company_number', $update_data['invoice_company_number'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('invoice_company_bulstat', $update_data)) {
    if (gettype($update_data['invoice_company_bulstat']) != 'NULL' && (
        gettype($update_data['invoice_company_bulstat']) != 'string' ||
         strlen($update_data['invoice_company_bulstat']) < 3
      )
    ) {
      return 'Invalid "invoice_company_bulstat" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_company_bulstat', $update_data['invoice_company_bulstat'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('invoice_company_city', $update_data)) {
    if (gettype($update_data['invoice_company_city']) != 'NULL' && (
        gettype($update_data['invoice_company_city']) != 'string' ||
         strlen($update_data['invoice_company_city']) < 2
      )
    ) {
      return 'Invalid "invoice_company_city" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_company_city', $update_data['invoice_company_city'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('invoice_company_address', $update_data)) {
    if (gettype($update_data['invoice_company_address']) != 'NULL' && (
        gettype($update_data['invoice_company_address']) != 'string' ||
         strlen($update_data['invoice_company_address']) < 5
      )
    ) {
      return 'Invalid "invoice_company_address" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_company_address', $update_data['invoice_company_address'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('invoice_company_dds', $update_data)) {
    if (gettype($update_data['invoice_company_dds']) != 'NULL' && (
        gettype($update_data['invoice_company_dds']) != 'string' ||
         strlen($update_data['invoice_company_dds']) < 2
      )
    ) {
      return 'Invalid "invoice_company_dds" property';
    }

    if ($error_message = updateProperty( $request_id, 'invoice_company_dds', $update_data['invoice_company_dds'] )) {
      return $error_message;
    }
  }


  if (isset(    $update_data['additional_info'])) {
    if (gettype($update_data['additional_info']) != 'NULL' &&
        gettype($update_data['additional_info']) != 'string'
    ) {
      return 'Invalid user "additional_info" property';
    }

    if ($error_message = updateProperty( $request_id, 'additional_info', $update_data['additional_info'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('access_token_value', $update_data)) {
    if (gettype($update_data['access_token_value']) != 'NULL' && (
        gettype($update_data['access_token_value']) != 'string' ||
         strlen($update_data['access_token_value']) < 3
      )
    ) {
      return 'Invalid "access_token_value" property';
    }

    if ($error_message = updateProperty( $request_id, 'access_token_value', $update_data['access_token_value'] )) {
      return $error_message;
    }
  }


  if (array_key_exists('access_token_created_at', $update_data)) {
    if (gettype($update_data['access_token_created_at']) != 'NULL' && (
        gettype($update_data['access_token_created_at']) != 'integer' ||
                $update_data['access_token_created_at']  < 0
      )
    ) {
      return 'Invalid "access_token_created_at" property';
    }

    if ($error_message = updateProperty( $request_id, 'access_token_created_at', $update_data['access_token_created_at'] )) {
      return $error_message;
    }
  }


  /*All properties were updated successfully*/
  return;
}



/*
  This function will use the incoming valid user record and
  rearrange its 'address' and 'invoice' properties so they can be mode Object-like oriented.
  Result 'array' will be returned.
*/
function reorderUserObjectData($user) {

  /*Require valid user record*/
  if (!isset( $user)            ||
      gettype($user) != 'array' ||
      !sizeof($user)
  ) {
    return array();
  }


  /*Create an Object-like 'address' property from the "in-line" records*/
  if (!isset($user['address'])) {
    $user['address'] = array();
  }
  if (array_key_exists('address_street', $user)) {
    $user['address']['street'] = $user['address_street'];
    unset($user['address_street']);
  }
  if (array_key_exists('address_city', $user)) {
    $user['address']['city'] = $user['address_city'];
    unset($user['address_city']);
  }
  if (array_key_exists('address_postcode', $user)) {
    $user['address']['postcode'] = $user['address_postcode'];
    unset($user['address_postcode']);
  }


  /*Create an Object-like 'invoice' property from the "in-line" records*/
  if (!isset($user['invoice'])) {
    $user['invoice'] = array();
  }
  if (array_key_exists('invoice_company_name', $user)) {
    $user['invoice']['company_name'] = $user['invoice_company_name'];
    unset($user['invoice_company_name']);
  }
  if (array_key_exists('invoice_company_number', $user)) {
    $user['invoice']['company_number'] = $user['invoice_company_number'];
    unset($user['invoice_company_number']);
  }
  if (array_key_exists('invoice_company_bulstat', $user)) {
    $user['invoice']['company_bulstat'] = $user['invoice_company_bulstat'];
    unset($user['invoice_company_bulstat']);
  }
  if (array_key_exists('invoice_company_address', $user)) {
    $user['invoice']['company_address'] = $user['invoice_company_address'];
    unset($user['invoice_company_address']);
  }
  if (array_key_exists('invoice_company_city', $user)) {
    $user['invoice']['company_city'] = $user['invoice_company_city'];
    unset($user['invoice_company_city']);
  }
  if (array_key_exists('invoice_company_dds', $user)) {
    $user['invoice']['company_dds'] = $user['invoice_company_dds'];
    unset($user['invoice_company_dds']);
  }


  return $user;
}