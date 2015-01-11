<?php
/*This file is meant to give access to all session saved User records in a global variable '$user'*/

/*
  Setting a 'HttpOnly' flag for every cookie session.
  This flag will prevent using cookies outside the HTTP protocol
  hence limiting scripts (JavaScript, VBScript, etc.) accessing these cookies.
*/
ini_set('session.cookie_httponly', true);

session_start();

/*Give a global access to all User records*/
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;