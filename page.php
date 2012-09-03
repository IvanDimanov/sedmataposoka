<?php


require('debug.php');
require('classes/Event.php');


$callbackId1 = Event::on('userLoggedIn', function ($arg) {
  echo ' ';
  echo 'callback 1';
  _l($arg);
});
Event::off('userLoggedIn', $callbackId1);


$callbackId2 = Event::on('userLoggedIn', function ($arg) {
  echo ' ';
  echo 'callback 2';
  _l($arg);
});


$callbackId3 = Event::on('userLoggedIn', function ($arg) {
  echo ' ';
  echo 'callback 3';
  _l($arg);
});
Event::off('userLoggedIn', $callbackId3);


$callbackId4 = Event::on('userLoggedIn', function ($arg) {
  echo ' ';
  echo 'callback 4';
  _l($arg);
});


// Event::off('userLoggedIn');
Event::trigger('userLoggedIn', array('custom argument 1', 'custom argument 2', 'custom argument 3'));

