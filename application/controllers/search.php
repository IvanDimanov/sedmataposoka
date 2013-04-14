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
    
    function dateSearch($language,$EndDate)
    {
        $language=  strtolower($language);
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
        
        
        $data['events']=$this->eventModel->getAllEvents($EndDate,null,$language);
        $data['language']=$language;
        $this->load->helper('printLayout_helper');
        printLayout($this,'templates/header','homeView',$data);
        
    }
    
    function wordSearch()
    {}
}

?>
