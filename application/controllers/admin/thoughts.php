<?php
/**
 * Will determine which HTML page will be loaded
 * in case of logged in and not logged in user
 */
class thoughts extends CI_Controller {

    function index() {

      /*Load any previously saved data*/
      session_start();

      /*
        Load the home  page for all logged users or
        load the login page for all the rest
      */
      if (isset(  $_SESSION['user']) &&
          gettype($_SESSION['user']) === 'array'
      ) {
        $this->load->view('admin/thoughts');
      } else {
        $this->load->view('admin/login');
      }
    }
}