<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of home
 *
 * @author Tedy
 */
class home extends CI_Controller{
    //put your code here
    function __construct()
    {
        parent::__construct();
                $this->load->model('eventModel');
    }
    
    function index()
    {
        //TODO get language
        $language = 'bg';
        
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
                
        $data['events'] = $this->eventModel->getAllEvents(0,null,$language);
                  
        $this->load->helper('printLayout_helper');
        printLayout($this,null,"homeView",$data);
    }
}

?>
