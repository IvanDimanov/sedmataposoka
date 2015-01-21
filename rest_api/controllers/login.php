<?php
/*
  This file holds the logic for all user login & authentication policies and
  is been used when when the Client-Browser wants to login a particular user
  or just wants to check his login status
*/


/*
  This function will check if we can authenticate such a '$user'.
  If so, the function will print all of the user records
  or in login failure, will print an JSON error message 
*/
function clearLogin() {
  global $request, $user, $settings;

  /*Load the functions that will make DB checks possible*/
  require_once('./models/users.php');


  /*Cache main request*/
  $user_request = $request['data'];


  /*Basic name validation*/
  if (!isset( $user_request['name'])              ||
      gettype($user_request['name']) !== 'string' ||
      !sizeof($user_request['name'])
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"login":false}');
  }


  /*Basic password validation*/
  if (!isset( $user_request['password'])              ||
      gettype($user_request['password']) !== 'string' ||
      !sizeof($user_request['password'])
  ) {
    header('HTTP/1.1 400 Bad Request');
    die('{"login":false}');
  }


  /*
    Check if we need to start asking for a recaptcha
    duo to maximum error logins exceeded
  */
  if (!checkErrorLogins()) {

    /*Supply with reCAPTCHA validation functions*/
    require_once('./recaptcha/recaptchalib.php');


    $recaptcha_is_valid = false;
    if (isset($user_request['captchaResponse' ]) &&
        isset($user_request['captchaChallenge'])
    ) {
      $recaptcha          = recaptcha_check_answer( $settings['recaptcha']['private_key'], $_SERVER['REMOTE_ADDR'], $user_request['captchaChallenge'], $user_request['captchaResponse']);
      $recaptcha_is_valid = $recaptcha->is_valid;
    }


    /*Check if the recaptcha answer from the Client-Browser was correct*/
    if (!$recaptcha_is_valid) {
      header('HTTP/1.1 400 Bad Request');
      die('{"login":false,"captcha_correct":false,"captcha_required":true}');
    }
  }


  /*
    Use the DB module function to determine
    if the user have a valid login details
  */
  if ($user = authenticate( $user_request['name'], $user_request['password'] )) {
    $user['login']    = true;
    $_SESSION['user'] = $user;

    $response = array(
      'login'            => true,
      'captcha_required' => false,
      'user_type'        => $user['type'],
      'user_data'        => $user
    );

    if (isset($recaptcha_is_valid)) {
      $response['captcha_correct'] = $recaptcha_is_valid;
    }

    header('HTTP/1.1 200 OK');
    die(json_unicode_encode( $user ));
  }


  /*
    Check if we need to start asking for a "captcha"
    since the IP exceeded the limit of "total error logins" in the timeframe of "time after captcha is required" (both in settings.json)
  */
  $captcha_request = checkErrorLogins(true) ? '' : ',"captcha_required":true';


  /*Print a "unauthorized" message since the authentication method didn't return a user details*/
  header('HTTP/1.1 400 Bad Request');
  die('{"login":false'.$captcha_request.'}');
}


/*
  Will print the entire user session data if possible
  or a common not-logged-in error message
*/
function checkLogin() {
  global $user;

  if (isset($user)) {
    header('HTTP/1.1 200 OK');
    die(json_unicode_encode( $user ));
  }

  header('HTTP/1.1 400 Bad Request');
  die('{"login":false}');
}


/*
  Removes all sensitive user data,
  tries to destroy the current session, and
  prints a status about it.
*/
function logout() {
  global $user;

  if (!isset($user)) {
    header('HTTP/1.1 404 Not Found');
    die();
  }


  /*
    Be sure to remove all sensitive data
    even if 'session_destroy()' doesn't work
  */
  unset($user);
  unset($_SESSION['user']);
  
  if (session_destroy()) {
    header('HTTP/1.1 204 No Content');
    die();

  } else {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"Unable to destroy the user session completely"}');
  }
}



/*
  Check if the request is meant to login a user with some $request['data']
  or it just want to check & get all of the session user data
*/
if ($request['method'] === 'POST') {

  if (sizeof( $request['data'] )) {
    clearLogin();
    return;

  } else {
    checkLogin();
    return;
  }
}

if ($request['method'] === 'DELETE' &&
    !sizeof( $request['data'] )
) {
  logout();
  return;
}


/*No suitable Login rooting was found*/
header('HTTP/1.1 400 Bad Request');
die('{"error":"No suitable rooting was found for method \''.$request['method'].'\' and URL \''.$request['url'].'\'"}');