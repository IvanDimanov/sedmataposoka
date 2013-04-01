<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of search
 *
 * @author Tedy
 */
class search extends CI_Controller {
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('eventModel');
    }
    
    function index()
    {}
    
    function dateSearch($EndDate)
    {
        $this->eventModel->getAllEvents($EndDate);
        return 'datasearch';
    }
    
    function wordSearch()
    {}
}

?>
