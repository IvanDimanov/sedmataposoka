<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of category
 *
 * @author Tedy
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class category extends CI_Controller {
    //put your code here
    public function __construct()
	{
		parent::__construct();
                $this->load->model('categoryModel');
	}
        
    function index($language,$catId )
    {
        //todo set language
        $language = strtolower($language);
                
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
        $data['categoryInfo'] = $this->categoryModel->getCategoryInfo($catId, $language);   
        $data['language']=$language;
             
       
        //call helper function which loads header, footer,
        // template and mainlayout views
        $this->load->helper('printLayout_helper');
        printLayout($this,null,"categoryView",$data);
        
    }
    
    
    
    
}

?>
