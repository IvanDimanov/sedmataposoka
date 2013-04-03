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
        
    function index($subcatId = 1)
    {
        //todo set language
        $language = 'en';
        
        $data['subcategoryInfo'] = $this->subcategoryModel->getSubcategriesInfo($subcatId, $language);        
        
        
        //get banner for today type=1, limi = 7
        $data['banners'] = $this->adsModel->getAds( null,1,7,$language);
        //get adds for today type = 2, limit = 2
        $data['ads'] = $this->adsModel->getAds( null,2,2,$language);
        $data['partners'] = $this->partnerModel->getPartners($language);
        $data['tought'] = $this->toughtModel->getTought(null,1,$language);
        $data['categories'] = $this->categoryModel->
                getAllCategoriesName($language);
        $data['subcategories'] = $this->subcategoryModel->
                getSubcategoriesForCategory($catId = null,$language);
        //get all events for current subcategory for 1 month
        $data['events'] = $this->eventModel->getAllEventsForSubcategory(30 ,$subcatId,$language);
               
        //call helper function which loads header, footer,
        // template and mainlayout views
        $this->load->helper('printLayout_helper');
        printLayout($this, "templates/templateLayout","subcategoryView",$data);
    }
    
    
}

?>

