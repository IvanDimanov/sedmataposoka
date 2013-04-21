<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of search
 *
 * @author Tedy
 */
class search extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('eventModel');
    }

    function index() {
        
    }

    function dateSearch($language, $EndDate) {
        $language = strtolower($language);
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);


        $data['events'] = $this->eventModel->getAllEvents($EndDate, null, $language);
        $data['language'] = $language;
        $this->load->helper('printLayout_helper');
        printLayout($this, 'templates/header', 'homeView', $data);
    }

    function wordSearch($language, $word) {
        $searchTxt = $this->input->post['searchTxt'];
        $searchTxt = preg_replace('!\s+!', ' ', $searchTxt);
        $words = explode(' ', $searchTxt);
        $weightedEvents = array();
        $eventsInfo = array();
        foreach ($words as $word) {
            $word = mysql_real_escape_string($word);
            $events = $this->eventModel->getEventByWord($word);
            foreach ($events as $event) {
                if (in_array($event['eventId'], $weightedEvents)) {
                    $weightEvents['eventId']++;
                } else {
                    $weightEvents['eventId'] = 1;
                }
            }
            asort($eventsWeight);
            $language = strtolower($language);
            for($i=0;$i<=sizeof($eventsWeight);$i++)
            {
               $arrayInfo = $this->eventModel->getEvent($weightEvents['eventId'],$language);
            }

            
            //load library to get data neded for header
            $this->load->library('load_data');
            $data = $this->load_data->populateHeaderData($language);

            $data['events'] = $arrayInfo;
            $data['language'] = $language;
            
                 
            $this->load->helper('printLayout_helper');
            printLayout($this, 'templates/header', 'homeView', $data);
        }

        //arr[key][valuew]
        //$_POST['keyword']['1']
        //mysqlescape
        //napravo kum bazata
        //v event descr title  
    }

}

?>
