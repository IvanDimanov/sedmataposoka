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

    function wordSearch($language) {
        $language = strtolower($language);
       // $searchTxt = $this->input->post['searchTxt'];
        $searchTxt= 'Party';
        $searchTxt = preg_replace('!\s+!', ' ', $searchTxt);
        $words = explode(' ', $searchTxt);
        $weightedEvents = array();
        $eventsInfo = array();
        foreach ($words as $word) {
            $word = mysql_real_escape_string($word);
            $events = $this->eventModel->getEventTitleByWord($language,$word);
            foreach ($events as $event) {
                if (array_key_exists($event['eventId'], $weightedEvents)) {
                    $weightedEvents[$event['eventId']]+=2;
                } else {
                    $weightedEvents[$event['eventId']] = 2;
                }
            }
            
            
             $eventsDescr = $this->eventModel->getEventDescrByWord($language,$word);
            echo '</br></br>';
            foreach ($eventsDescr as $eventDescr) {
                if (array_key_exists($eventDescr['eventId'], $weightedEvents)) {
                    $weightedEvents[$eventDescr['eventId']]+=1;
                    echo 'in array';
                } else {
                    $weightedEvents[$eventDescr['eventId']] = 1;
                }
            }
        }
            
            arsort($weightedEvents);
            
            $eventId = array_keys($weightedEvents);
            $arrayInfo = array();
            for($i=0;$i<sizeof($weightedEvents);$i++)
            {
                $temp = $this->eventModel->getEvent($eventId[$i],$language);
                //array_push($arrayInfo, $temp);
                $arrayInfo[$i]=$temp[0];
            }
            

            echo'</br></br></br>';
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

?>
