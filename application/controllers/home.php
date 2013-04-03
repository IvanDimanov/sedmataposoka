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
		$this->load->model('toughtModel');
                $this->load->model('partnerModel');
                $this->load->model('adsModel');
                $this->load->model('categoryModel');
                $this->load->model('subcategoryModel');
                $this->load->model('eventModel');
    }
    
    function index()
    {
        //TODO get language
        $language = 'bg';
        
       // $this->load->view('templates/header');
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
        $data['events'] = $this->eventModel->getAllEvents(0,null,$language);
        
        
        
        $this->load->helper('printLayout_helper');
        printLayout($this, "templates/templateLayout","homeView",$data);
    }
}

?>
