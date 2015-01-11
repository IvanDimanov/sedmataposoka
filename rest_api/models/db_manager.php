<?php
/*
  This file will handle DB connections based on the global '$settings' variable.
  Will expose functions to establish and manage this connection
*/


/*Use the '$settings' to return a secured PDO object*/
function getDBConnection() {
  global $settings;

  $host      = $settings['database']['host'];
  $name      = $settings['database']['name'];
  $user_name = $settings['database']['user_name'];
  $password  = $settings['database']['password'];

  try {
    return new PDO('mysql:host='.$host.';dbname='.$name,
                   $user_name,
                   $password,
                   array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
                  );

  } catch (PDOException $error) {
    header('HTTP/1.1 500 Internal Server Error');
    die('{"error":"DataBase Error: '.$error->getMessage().'"}');
  }
}


/*Free the PDO object as described in http://php.net/manual/en/pdo.connections.php*/
function deleteDBConnection(&$db) {
  $db = null;
}