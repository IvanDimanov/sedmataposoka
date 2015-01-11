<?php
/*
  This file will load all Server settings from './settings.json',
  will secure each property value and when approved,
  will expose all settings to '$settings' global variable
*/


/*
  Load all custom settings that will determine some Server behavior
  like determine "how many unsuccessful login attempts an IP can make"
*/
$settings_file_path = './settings.json';
$settings           = json_decode(file_get_contents( $settings_file_path ), true);
if (!$settings) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Error loading settings file \''.$settings_file_path.'\'"}');
}


/*Secure all '$settings' properties*/

if (!isset( $settings['domain']) ||
    gettype($settings['domain']) != 'string' ||
    strlen( $settings['domain']) < 3
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'domain\' property"}');
}


if (!isset( $settings['database']) ||
    gettype($settings['database']) != 'array'
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'database\' property"}');
}


if (!isset( $settings['database']['host']) ||
    gettype($settings['database']['host']) != 'string' ||
    strlen( $settings['database']['host']) < 3
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'database.host\' property"}');
}


if (!isset( $settings['database']['name']) ||
    gettype($settings['database']['name']) != 'string' ||
    strlen( $settings['database']['name']) < 3
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'database.name\' property"}');
}


if (!isset( $settings['database']['user_name']) ||
    gettype($settings['database']['user_name']) != 'string' ||
    strlen( $settings['database']['user_name']) < 3
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'database.user_name\' property"}');
}


if (!isset( $settings['database']['password']) ||
    gettype($settings['database']['password']) != 'string' ||
    strlen( $settings['database']['password']) < 3
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'database.password\' property"}');
}


if (!isset( $settings['controllers']) ||
    gettype($settings['controllers']) != 'array'
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'controllers\' property"}');
}


if (!isset( $settings['controllers']['login']) ||
    gettype($settings['controllers']['login']) != 'array'
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'controllers.login\' property"}');
}


if (!isset( $settings['controllers']['login']['max_error_attempts']) ||
    gettype($settings['controllers']['login']['max_error_attempts']) != 'integer' ||
            $settings['controllers']['login']['max_error_attempts'] < 2
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'controllers.login.max_error_attempts\' property"}');
}


if (!isset( $settings['controllers']['login']['time_after_captcha_is_required']) ||
    gettype($settings['controllers']['login']['time_after_captcha_is_required']) != 'integer' ||
            $settings['controllers']['login']['time_after_captcha_is_required'] < 2
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'controllers.login.time_after_captcha_is_required\' property"}');
}


if (!isset( $settings['controllers']['create_user']) ||
    gettype($settings['controllers']['create_user']) != 'array'
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'controllers.create_user\' property"}');
}


if (!isset( $settings['controllers']['create_user']['access_token_expiration_time']) ||
    gettype($settings['controllers']['create_user']['access_token_expiration_time']) != 'integer' ||
            $settings['controllers']['create_user']['access_token_expiration_time'] < 2
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'controllers.create_user.access_token_expiration_time\' property"}');
}


if (!isset( $settings['recaptcha']) ||
    gettype($settings['recaptcha']) != 'array'
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'recaptcha\' property"}');
}


if (!isset( $settings['recaptcha']['public_key']) ||
    gettype($settings['recaptcha']['public_key']) != 'string' ||
    strlen( $settings['recaptcha']['public_key']) != 40
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'recaptcha.public_key\' property"}');
}


if (!isset( $settings['recaptcha']['private_key']) ||
    gettype($settings['recaptcha']['private_key']) != 'string' ||
    strlen( $settings['recaptcha']['private_key']) != 40
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('{"error":"Settings file \''.$settings_file_path.'\' has an invalid \'recaptcha.private_key\' property"}');
}