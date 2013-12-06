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
		
	}
    
    function index($language)
    {
        //TODO get language
        $language = strtolower($language);
        //get adds for today type = 2, limit = 2
        
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
        $data['language'] = $language;
        
        $this->load->helper('printLayout_helper');
        
        printLayout($this, "templates/header","contactView",$data);
    }
    
    function sendEmail($language) {
      $language = strtolower($language);

      $mailTo    = ADMIN_EMAIL; 
      $mailFrom  = $this->input->post('mailFrom');
      $firstName = $this->input->post('firstName');
      $lastName  = $this->input->post('lastName');
      $subject   = 'Sedmata posoka request from '. $firstName.' '.$lastName;
      $message   = $this->input->post('message');
      if ($message) $message = $message.'\r\n'.'From '.$mailFrom;

      $response  = array();
      $ui_labels = $this->uiLabelsModel->getLabelsForLanguage( $language );

      if (mail($mailTo, $subject, $message)) {
        $response['success'] = $ui_labels['send_email']['success'];
      } else {
        $response['error'] = $ui_labels['send_email']['error'];
      }

      return json_encode( $response );
    }
}