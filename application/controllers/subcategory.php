<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class subcategory extends CI_Controller {
    //put your code here
    public function __construct()
	{
		parent::__construct();
		$this->load->model('toughtModel');
                $this->load->model('partnerModel');
                $this->load->model('adsModel');
                $this->load->model('categoryModel');
                $this->load->model('subcategoryModel');
                $this->load->model('categoryModel');
                $this->load->model('eventModel');
	}
        
    function index($language,$subcatId = 1)
    {
        //todo set language
        $language = strtolower($language);
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
        
        $data['subcategoryInfo'] = $this->subcategoryModel->getSubcategriesInfo($subcatId, $language); 
        //get all events for current subcategory for 1 month
        $data['events'] = $this->eventModel->getAllEventsForSubcategory(30 ,$subcatId,$language);
        $data['language'] = $language;
               
        //call helper function which loads header, footer,
        // template and mainlayout views
        $this->load->helper('printLayout_helper');
        printLayout($this, "templates/templateLayout","subcategoryView",$data);
    }
    
    
}

?>

