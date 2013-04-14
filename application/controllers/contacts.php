<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of contacts
 *
 * @author Tedy
 */
class contacts extends CI_Controller {
      public function __construct()
	{
		parent::__construct();
		$this->load->model('toughtModel');
                $this->load->model('partnerModel');
                $this->load->model('adsModel');
                $this->load->model('categoryModel');
                $this->load->model('subcategoryModel');
                $this->load->model('eventModel');
	}
    
    function index($language)
    {
        //TODO get language
        $language = strtolower($language);
        //get adds for today type = 2, limit = 2
        $data['ads'] = $this->adsModel->getAds( null,2,2,$language);
        $data['partners'] = $this->partnerModel->getPartners($language);
        $data['tought'] = $this->toughtModel->getTought(null,1,$language);
        $data['categories'] = $this->categoryModel->
                getAllCategoriesName($language);
        $data['subcategories'] = $this->subcategoryModel->
                getSubcategoriesForCategory($catId = null,$language);
        $data['language']=$language;
        $this->load->helper('printLayout_helper');
        
        printLayout($this, "templates/header","contactView",$data);;
    }
}

?>
