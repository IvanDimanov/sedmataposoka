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
		$this->load->model('thoughtModel');
                $this->load->model('partnerModel');
                $this->load->model('adsModel');
                $this->load->model('categoryModel');
                $this->load->model('subcategoryModel');
                $this->load->model('eventModel');
	}

    //put your code here
    //TODO if no cat id display events for all subcats ????? Limit to 10 
    /*function search($catId,$subcatId, $date,$language)
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
    */
    function index($language,$eventId) {
        
        
        
        //TODO get language
        $language = strtolower($language);
        
       // $this->load->view('templates/header');
        //get banner for today type=1, limi = 7
        $data['banners'] = $this->adsModel->getAds( null,1,7,$language);
        //get adds for today type = 2, limit = 2
        $data['ads'] = $this->adsModel->getAds( null,2,2,$language);
        $data['partners'] = $this->partnerModel->getPartners($language);
        $data['thought'] = $this->thoughtModel->getThought(null,1,$language);
        $data['categories'] = $this->categoryModel->
                getAllCategoriesName($language);
        $data['subcategories'] = $this->subcategoryModel->
                getSubcategoriesForCategory($catId = null,$language);
        $data['events']= $this->eventModel->getEvent($eventId,$language);
        $data['event'] = $data['events'][0];
        $data['language']=$language;
        $this->load->helper('printLayout_helper');
        printLayout($this, "templates/header","eventView",$data);
        
        
       
    }
}

?>
