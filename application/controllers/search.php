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
        //check $endDate 0 to 100 if not load 404 page 
        $language = strtolower($language);
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);


        $data['events'] = $this->eventModel->getAllEvents($EndDate, null, $language);
        $data['language'] = $language;


        //to do select from db according selected lenaguage
        $ui_labels = $this->uiLabelsModel->getLabelsForLanguage( $language );
        $data['viewedEventsTitle'] = $ui_labels['search']['results'].' ';
        switch ($EndDate) {
            case 0 : $data['viewedEventsTitle'].= $ui_labels['search']['today'];
                break;
            case 7: $data['viewedEventsTitle'].= $ui_labels['search']['week'];
                break;
            case 14: $data['viewedEventsTitle'].= $ui_labels['search']['2_weeks'];
                break;
            case 30: $data['viewedEventsTitle'].= $ui_labels['search']['month'];
                break;
            default :
                $date = Date('Y:m:d', mktime(0, 0, 0, date("m"), date("d") + $EndDate, date("Y")));
                $data['viewedEventsTitle'].= ' '.$date;
                break;
        }

        $this->load->helper('printLayout_helper');
        printLayout($this, 'templates/header', 'homeView', $data);
    }

    function wordSearch($language) {
        $language = strtolower($language);
        //$language = 'en';
        $searchTxt = $this->input->post('searchTxt');
        //var_dump($searchTxt);
        //$searchTxt = 'Party';
        $searchTxt = preg_replace('!\s+!', ' ', $searchTxt);
        $words = explode(' ', $searchTxt);
        $weightedEvents = array();
        $eventsInfo = array();
        foreach ($words as $word) {
            $word = mysql_real_escape_string($word);
            $events = $this->eventModel->getEventTitleByWord($language, $word);
            //var_dump($events);
            echo $word;
            foreach ($events as $event) {
                if (array_key_exists($event['eventId'], $weightedEvents)) {
                    $weightedEvents[$event['eventId']]+=2;
                } else {
                    $weightedEvents[$event['eventId']] = 2;
                }
            }


            $eventsDescr = $this->eventModel->getEventDescrByWord($language, $word);
            //echo'<br/> eventDescr';
            //var_dump($eventsDescr);
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
        for ($i = 0; $i < sizeof($weightedEvents); $i++) {
            $temp = $this->eventModel->getEvent($eventId[$i], $language);
            //array_push($arrayInfo, $temp);
            $arrayInfo[$i] = $temp[0];
        }


        echo'</br></br></br>';
        //load library to get data neded for header
        $this->load->library('load_data');
        $data = $this->load_data->populateHeaderData($language);
        $ui_labels = $this->uiLabelsModel->getLabelsForLanguage( $language );

        $data['events'] = $arrayInfo;

        $data['language'] = $language;
        //to do select from db according selected lenaguage
        $data['viewedEventsTitle'] = $ui_labels['search']['results'].' '.$searchTxt;

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
