<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of partners
 *
 * @author Tedy
 */
class partners extends CI_Controller {
      public function __construct()
	{
		parent::__construct();
                $this->load->model('partnerModel');
		
	}
    
    function index($language)
    {
        //TODO get language
        $language = strtolower($language);
        //get adds for today type = 2, limit = 2
        
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
        $data['language'] = $language;
        $data['partners'] = $this->partnerModel->getPartners($language);
        
        $this->load->helper('printLayout_helper');
        
        printLayout($this, "templates/header","partnersView",$data);;
    }
}

?>
