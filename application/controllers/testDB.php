<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testDB
 *
 * @author Tedy
 */
class TestDB extends CI_Controller{
    public function __construct()
	{
		parent::__construct();
		$this->load->model('thoughtModel');
                $this->load->model('partnerModel');
                $this->load->model('adsModel');
                $this->load->model('categoryModel');
                $this->load->model('subcategoryModel');
                $this->load->model('eventModel');
	}

    function index(){
       //get thought for current day
        $todayday = date('Y:m:d');
        $data['thought'] = $this->thoughtModel->getThought(null,1);
        
        //get all parthners
        $data['partners'] = $this->partnerModel->getPartners();
        $data['banner'] = $this->adsModel->getAds( null,1, 1);
        $data['categories'] = $this->categoryModel->getAllCategoriesName();
        //$data['subcategories'] = $this->subcategoryModel->getSubcategoriesForCategory(1);
        $data['subcategories'] = $this->subcategoryModel->getSubcategoriesForCategory($catId = null);
        //$data['events'] = $this->eventModel->getAllEventsForSubcategory(null,2);
        $data['events'] = $this->eventModel->getAllEvents(0);
        var_dump($data);
        
        $this->load->view('testDBView',$data);
    }
}

?>
