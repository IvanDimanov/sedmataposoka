<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of eventModel
 *
 * @author Tedy
 */
class eventModel extends CI_Model {

    //put your code here

    function getAllEventsForSubcategory($timeframe, $subcategoryId, $language) {
        $today        = Date('Y:m:d');
        $timeframeEnd = Date('Y:m:d', mktime(0, 0, 0, date("m"), date("d") + $timeframe, date("Y")));

        $this->db->select('event.id as eventId, subcategoryId, eventname.'.$language.' as title,
            eventdescription.'.$language.' as descr, 
            startDate, endDate, fee, link'
        );
        $this->db->from('event');
        $this->db->join('eventname', 'eventname.id = event.nameId');
        $this->db->join('eventdescription', 'eventdescription.id = event.descriptionId');
        $this->db->where('subcategoryId = '.$subcategoryId.'');

        /*NOTE: CodeIgniter doesn't put any separating brackets*/

        /*Searching time frame is in between event time frame*/
        $this->db->where("((`startDate` <= '".$today."' AND `endDate` >= '".$timeframeEnd."')");

        /*Event start date is in between the searching time frame*/
        $this->db->or_where("(`startDate` > '".$today."' AND `startDate` <= '".$timeframeEnd."')");

        /*Event end date is in between the searching time frame*/
        $this->db->or_where("(`endDate` > '".$today."' AND `endDate` <= '".$timeframeEnd."'))");

        $query = $this->db->get();
        return $query->result_array();
    }

    
    //
    function getAllEvents($timeframe, $categoryId = null, $language) {
       $today = Date('Y:m:d');
        $timeframeEnd = Date('Y:m:d', mktime(0, 0, 0, date("m"), date("d") + $timeframe, date("Y")));
        $this->db->select('event.id as eventId, eventname.' . $language . ' as event_title, 
            eventdescription.' . $language . ' as event_descr,
            categoryname.' . $language . ' as category_name, 
            subcategoryname.' . $language . ' as subcategory_name,
            startDate, endDate    ');
        $this->db->from('event');
        $this->db->join('subcategory', 'event.subcategoryId = subcategory.id');
        $this->db->join('category', 'subcategory.categoryId = category.id');
        $this->db->join('eventname', 'eventname.id = event.nameId');
        $this->db->join('eventdescription', 'eventdescription.id = event.descriptionId');
        $this->db->join('categoryname', 'categoryname.id = category.nameId');
        $this->db->join('subcategoryname', 'subcategoryname.id = subcategory.nameId');

       
            //case start and end date are in time range
        $this->db->where("((`startDate` >=  '" . $today . "' 
                AND  `endDate` <=  '" . $timeframeEnd . "')");
        //case: start date is before timeframe, end date is after timeframe
        $this->db->or_where("(`startDate` <=  '" . $today . "' 
                AND  `endDate` >=  '" . $timeframeEnd . "')");
         //case: start date is IN timeframe, end date is after timeframe
        $this->db->or_where("(`startDate` >=  '" . $today . "' 
                AND `startDate` <= '".$timeframeEnd."'
                AND  `endDate` >=  '" . $timeframeEnd . "')");
        //case: end date is in time range, start date is before time Range
        $this->db->or_where("(`startDate` <=  '" . $today . "' 
                AND  `endDate` <=  '" . $timeframeEnd . "' AND `endDate` >= '".$today."'))");
        
        if (isset($categoryId)) {
            echo '</br>cat id is set</br>';
            $this->db->where('category.id', $categoryId);
        }
        //$this->db->order_by('category.name,subcategory.name');
        $this->db->order_by('category_name,subcategory_name');

        $query = $this->db->get();
        return $query->result_array();
    }

    function getEvent($eventId,$language)
    {
        $this->db->select('event.id as eventId, eventname.' . $language . ' as event_title, 
            eventdescription.' . $language . ' as event_descr,
            startDate, endDate, fee, link    ');
        $this->db->from('event');
        $this->db->join('eventname', 'eventname.id = event.nameId');
        $this->db->join('eventdescription', 'eventdescription.id = event.descriptionId');
        $this->db->where('event.id ',$eventId);
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    function insertEvent() {
        $data = array(
            'subcategoryId' => $this->input->post('subcategoryId'),
            'title' => $this->input->post('name'),
            'descr' => $this->input->post('descr')
        );
        $this->db->insert('event', $data);
    }
    
    function getEventByWord($language,$word)
    {
        $this->db->select('event.id as eventId, eventname.' . $language . ' as event_title, 
            eventdescription.' . $language . ' as event_descr,
            categoryname.' . $language . ' as category_name, 
            subcategoryname.' . $language . ' as subcategory_name,
            startDate, endDate    ');
        $this->db->from('event');
        $this->db->join('subcategory', 'event.subcategoryId = subcategory.id');
        $this->db->join('category', 'subcategory.categoryId = category.id');
        $this->db->join('eventname', 'eventname.id = event.nameId');
        $this->db->join('eventdescription', 'eventdescription.id = event.descriptionId');
        $this->db->join('categoryname', 'categoryname.id = category.nameId');
        $this->db->join('subcategoryname', 'subcategoryname.id = subcategory.nameId');
        $this->db->like('eventname.'. $language, $word);
        $query = $this->db->get();
        
        return $query->result_array(); 
        
        
    }
    
    function geteventnameByWord($language,$word)
    {
        $this->db->select('event.id as eventId, eventname.' . $language . ' as event_title');
        $this->db->from('event');
        $this->db->join('eventname', 'eventname.id = event.nameId');
        $this->db->like('eventname.'. $language.' COLLATE UTF8_GENERAL_CI ', $word);
        $query = $this->db->get();
        
        return $query->result_array(); 
        
        
    }
    
    function geteventdescriptionByWord($language,$word)
    {
        
        $this->db->select('event.id as eventId, eventdescription.' . $language . ' as event_descr');
        $this->db->from('event');
        $this->db->join('eventdescription', 'eventdescription.id = event.descriptionId');
        $this->db->like('eventdescription.'. $language.' COLLATE UTF8_GENERAL_CI ', $word);
        $query = $this->db->get();
        
        return $query->result_array(); 
        
    }


}

?>
