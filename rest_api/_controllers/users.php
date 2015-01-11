<?php
/*THis file will address all '/users*' requests like creating and managing new store client-users*/


/*This file should be used for User profile modifications only*/
if ($request['url_conponents'][0] != 'users') {
  header('HTTP/1.1 400 Bad Request');
  die('{"error":"User service for method \''.$request['method'].'\' and URL \''.$request['url'].'\' not found"}');
}


/*Include all model functions needed for an user management*/
require_once('./models/users.php');


/*
  This function will find the user record (by '$request_id')
  set it to "not active", generate activation token/code
  and send an email about the change to the user in question
*/
function setUserAccessToken($request_id) {
  global $settings;

  $user = getUserByRequestID( $request_id );
  if (!$user) {
    return;
  }

  /*Create a string that can only activate the user profile*/
  $access_token = getRandomString(20);

  /*Mark the user profile as not active with an '$access_token' to be used on activation*/
  if ($error_message = updateUser( $user['request_id'], array(
    'is_active'               => false,
    'access_token_value'      => $access_token,
    'access_token_created_at' => time()
  ))) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  };


  /*Try to send an Access email from which the newly registered user can active his profile*/
  $user_email      = $user['email'];
  $subject         = 'Online Store Access';
  $activation_link = 'https://'.$settings['domain'].'/#/password-reset?reset-token='.$access_token;
  $message         = 'Please visit our <a href="'.$activation_link.'">activation page</a> in order to complete your login request<br />'.$activation_link;
  $headers         = 'From: online_store@idimanov.com'."\r\n".
                     'Content-Type: text/html; charset=utf-8';

  if (!mail( $user_email, $subject, $message, $headers)) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Unable to send email to \''.$user_email.'\'"}');
  }
}



/*Attempt to create a new user via the model functions with some pre-save checks*/
function createUserController() {
  global $request, $user;

  /*Validate the ID which is used for every callee request to specify an user*/
  $request_id = $request['url_conponents'][1];
  if (!preg_match('/^[1-9][0-9]{1,20}$/', $request_id)) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid 2nd parameter \'User ID\'"}');
  }


  /*Cache all user values and trim all inner strings*/
  $new_user               = $request['data'];
  $new_user['request_id'] = $request_id;
  trimAll( $new_user );


  /*Be sure not to overwrite an already existing user*/
  if (getUserByRequestID( $new_user['request_id'] )) {
    header('HTTP/1.1 409 Conflict');
    die('{"error":"User with ID \''.$new_user['request_id'].'\' already exists"}');
  }


  /*Convert Object-like 'address' property to full string-based*/
  if (isset(  $new_user['address']) &&
      gettype($new_user['address']) == 'array'
  ) {
    if (isset(  $new_user['address']['city']) &&
        gettype($new_user['address']['city']) == 'string'
    ) {
      $new_user['address_city'] = $new_user['address']['city'];
      unset( $new_user['address']['city'] );
    }

    if (isset(  $new_user['address']['street']) &&
        gettype($new_user['address']['street']) == 'string'
    ) {
      $new_user['address_street'] = $new_user['address']['street'];
      unset( $new_user['address']['street'] );
    }

    if (isset(  $new_user['address']['postcode']) &&
        gettype($new_user['address']['postcode']) == 'string'
    ) {
      $new_user['address_postcode'] = $new_user['address']['postcode'];
      unset( $new_user['address']['postcode'] );
    }
  }


  /*Convert Object-like 'invoice' property to full string-based*/
  if (isset(  $new_user['invoice']) &&
      gettype($new_user['invoice']) == 'array'
  ) {
    if (isset(  $new_user['invoice']['company_name']) &&
        gettype($new_user['invoice']['company_name']) == 'string'
    ) {
      $new_user['invoice_company_name'] = $new_user['invoice']['company_name'];
      unset( $new_user['invoice']['company_name'] );
    }

    if (isset(  $new_user['invoice']['company_number']) &&
        gettype($new_user['invoice']['company_number']) == 'string'
    ) {
      $new_user['invoice_company_number'] = $new_user['invoice']['company_number'];
      unset( $new_user['invoice']['company_number'] );
    }

    if (isset(  $new_user['invoice']['company_bulstat']) &&
        gettype($new_user['invoice']['company_bulstat']) == 'string'
    ) {
      $new_user['invoice_company_bulstat'] = $new_user['invoice']['company_bulstat'];
      unset( $new_user['invoice']['company_bulstat'] );
    }

    if (isset(  $new_user['invoice']['company_address']) &&
        gettype($new_user['invoice']['company_address']) == 'string'
    ) {
      $new_user['invoice_company_address'] = $new_user['invoice']['company_address'];
      unset( $new_user['invoice']['company_address'] );
    }

    if (isset(  $new_user['invoice']['company_city']) &&
        gettype($new_user['invoice']['company_city']) == 'string'
    ) {
      $new_user['invoice_company_city'] = $new_user['invoice']['company_city'];
      unset( $new_user['invoice']['company_city'] );
    }

    if (isset(  $new_user['invoice']['company_dds']) &&
        gettype($new_user['invoice']['company_dds']) == 'string'
    ) {
      $new_user['invoice_company_dds'] = $new_user['invoice']['company_dds'];
      unset( $new_user['invoice']['company_dds'] );
    }
  }


  /*Check if the DB model function faced any problems creating our new user*/
  if ($error_message = createUser( $new_user )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  }


  /*Set the user as inactive and send an instruction email about it*/
  setUserAccessToken( $new_user['request_id'] );

  header('HTTP/1.1 201 Created');
  die();
}



/*
  Will made all possible checks for the incoming $request['data']: 'email', 'password', and 'resetToken'
  to set the corresponding user profile active with preset password
*/
function setUserNewPassword() {
  global $request, $settings;

  /*Validate the ID which is used for every callee request to specify an user*/
  $request_id = $request['url_conponents'][1];
  if (!preg_match('/^[1-9][0-9]{1,20}$/', $request_id)) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid 2nd parameter \'User ID\'"}');
  }


  /*Cache all user values and trim all inner strings*/
  $user_request               = $request['data'];
  $user_request['request_id'] = $request_id;
  trimAll( $user_request );


  /*Validate user input*/
  if (!isset($user_request['email'])      || !filter_var($user_request['email'], FILTER_VALIDATE_EMAIL) ||
      !isset($user_request['password'  ]) || gettype($user_request['password'  ]) != 'string' || strlen($user_request['password'  ]) < 4 ||
      !isset($user_request['resetToken']) || gettype($user_request['resetToken']) != 'string' || strlen($user_request['resetToken']) < 4
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid data for \'request_id\', \'email\', \'password\' or \'resetToken\'"}');
  }


  /*Check if the request actually involve an already registered user*/
  /*
    To prevent someone trying to guess an user email
    we'll not return a 404 responses but the same 400 as the incoming data were not valid
  */
  $user = getUserByRequestID( $user_request['request_id'] );
  if (!$user) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid data for \'request_id\', \'email\', \'password\' or \'resetToken\'"}');
  }


  /*
    Be sure that the registered user is not already active
    or if he did made an activation request, the DB token is the same as the one sent as 'resetToken'
  */
  if ($user['is_active'] ||
      !isset($user['access_token_value']) ||
      $user['access_token_value'] !== $user_request['resetToken']
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid data for \'request_id\', \'email\', \'password\' or \'resetToken\'"}');
  }


  /*Confirm that the requested token wasn't expired*/
  if (!isset(  $user['access_token_created_at']) ||
      time() - $user['access_token_created_at'] > $settings['controllers']['create_user']['access_token_expiration_time']
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Your \'resetToken\' was expired"}');
  }


  /*Let the user profile active with required password*/
  if ($error_message = updateUser( $user['request_id'], array(
    'is_active'               => true,
    'password'                => $user_request['password'],
    'access_token_value'      => null,
    'access_token_created_at' => null
  ))) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"'.str_replace('"', '\"', $error_message).'"}');
  };


  header('HTTP/1.1 204 No Content');
  die();
}



/*
  This function will validate the incoming email as a valid user one,
  will validate each reCaptcha properties and if all is valid
  this function will call 'setUserAccessToken()'
  in order to set the user profile as "waiting for activation"
*/
function requestUserNewPassword() {
  global $request, $settings;

  /*Validate the ID which is used for every callee request to specify an user*/
  $request_id = $request['url_conponents'][1];
  if (!preg_match('/^[1-9][0-9]{1,20}$/', $request_id)) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid 2nd parameter \'User ID\'"}');
  }


  /*Cache all user values and trim all inner strings*/
  $user_request               = $request['data'];
  $user_request['request_id'] = $request_id;
  trimAll( $user_request );


  /*Validate user input*/
  if (!isset($user_request['email'           ]) || !filter_var($user_request['email'], FILTER_VALIDATE_EMAIL) ||
      !isset($user_request['captchaResponse' ]) || gettype($user_request['captchaResponse' ]) != 'string' || strlen($user_request['captchaResponse' ]) < 4 ||
      !isset($user_request['captchaChallenge']) || gettype($user_request['captchaChallenge']) != 'string' || strlen($user_request['captchaChallenge']) < 4
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid data for \'request_id\', \'email\', \'captchaResponse\' or \'captchaChallenge\'"}');
  }


  /*Check if the request actually involve an already registered user*/
  /*
    To prevent someone trying to guess an user email
    we'll not return a 404 responses but the same 400 as the incoming data were not valid
  */
  if (!getUserByRequestID( $user_request['request_id'] )) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid data for \'request_id\', \'email\', \'captchaResponse\' or \'captchaChallenge\'"}');
  }


  /*Supply with reCAPTCHA validation functions*/
  require_once('./recaptcha/recaptchalib.php');

  /*Secure correct reCaptcha request*/
  $recaptcha = recaptcha_check_answer( $settings['recaptcha']['private_key'], $_SERVER['REMOTE_ADDR'], $user_request['captchaChallenge'], $user_request['captchaResponse']);
  if (!$recaptcha->is_valid) {
    header('HTTP/1.1 400 Bad Request');
    die('{"error":"Invalid data for \'request_id\', \'email\', \'captchaResponse\' or \'captchaChallenge\'"}');
  }


  /*Mark the user profile as inactive till he re-access it using a sent via email token*/
  setUserAccessToken( $user_request['request_id'] );

  header('HTTP/1.1 204 No Content');
  die();
}



/*Check if all URL components are correct for new user creation*/
if ($request['method'] == 'POST' &&
    sizeof( $request['url_conponents'] ) == 2
) {
  createUserController();
  return;
}


/*This URL path is been used for users that want their newly created accounts active with new password*/
if ($request['method'] == 'POST'              &&
    sizeof( $request['url_conponents'] ) == 3 &&
    $request['url_conponents'][2] == 'set_new_password'
) {
  setUserNewPassword();
  return;
}


/*This URL will be used when an user wants to update only his password*/
if ($request['method'] == 'POST'                                      &&
    sizeof( $request['url_conponents'] ) == 3                         &&
    preg_match('/^[1-9][0-9]{1,20}$/', $request['url_conponents'][1]) &&
    $request['url_conponents'][2] == 'request_new_password'
) {
  requestUserNewPassword();
  return;
}


/*No suitable User rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');