<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of event
 *
 * @author Tedy
 */
class event extends CI_Controller{
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

    //put your code here
    //TODO if no cat id display events for all subcats ????? Limit to 10 
    function search($catId, $date,$language)
    {
        //TODO set language
        $language = 'en';
        $this->load->model('eventModel');
        switch ($date) {
            case 'tomorrow':
                $date = mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
                break;

            case 'week':
                $date = mktime(0, 0, 0, date("m") , date("d")+7, date("Y"));
                break;

            case 'month':
                $date = mktime(0, 0, 0, date("m")+1, date("d"), date("Y"));
                break;

            default:
                $date = null;
                break;
        }
        $data['events'] = $this->eventModel->getAllEvents($date, $catId,$language);
        
        $this->load->view('eventView',$data);
    }
    
    function index() {
        
        
        
        //TODO get language
        $language = 'bg';
        
        $this->load->view('templates/header');
        //get banner for today type=1, limi = 7
        $data['banners'] = $this->adsModel->getAds( null,1,7,$language);
        //get adds for today type = 2, limit = 2
        $data['ads'] = $this->adsModel->getAds( null,2,2,$language);
        echo 'ADS </br>';
        $data['partners'] = $this->partnerModel->getPartners($language);
        $data['tought'] = $this->toughtModel->getTought(null,1,$language);
        $data['categories'] = $this->categoryModel->
                getAllCategoriesName($language);
        $data['subcategories'] = $this->subcategoryModel->
                getSubcategoriesForCategory($catId = null,$language);
        
        $this->load->view('templates/templateLayout',$data);
        
         
        
        //TODO default values for catID, date
        //$this->search($catId=1, $date,$language);
        
        
        
        $this->load->view('templates/footer');
        
       //$this->load->helper('printLayout',$this, 'templateLayout', 'mainLayout', $data);
        // $this->load->helper('printLayout');
    }
}

?>
