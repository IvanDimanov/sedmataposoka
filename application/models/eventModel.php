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

    function getAllEventsForSubcategory($timeframe, $subcatId, $language) {
        $today = Date('Y:m:d');
        $timeframeEnd = Date('Y:m:d', mktime(0, 0, 0, date("m"), date("d") + $timeframe, date("Y")));
        $this->db->select('event.id as eventId, subcatId,eventtitle.' . $language . ' as title,
            eventdescr.' . $language . ' as descr, 
            startDate, endDate, fee, link');
        $this->db->from('event');
        $this->db->join('eventtitle', 'eventtitle.id = event.titleId');
        $this->db->join('eventdescr', 'eventdescr.id = event.descrId');
        $this->db->where('(subcatId = "' . $subcatId . '")');
        
       //case start and end date are in timerange
        $this->db->where("((`startDate` <=  '" . $today . "' 
                AND  `endDate` >=  '" . $timeframeEnd . "')");
        //case: start date is before timeframe, end date is after timeframe
        $this->db->or_where("(`startDate` <=  '" . $today . "' 
                AND  `endDate` >=  '" . $timeframeEnd . "')");
         //case: start date is IN timeframe, end date is after timeframe
        $this->db->or_where("(`startDate` >=  '" . $today . "' 
                AND `startDate` <= '".$timeframeEnd."'
                AND  `endDate` >=  '" . $timeframeEnd . "')");
        //case: end date is in timerange, start date is before timeRange
        $this->db->or_where("(`startDate` <=  '" . $today . "' 
                AND  `endDate` <=  '" . $timeframeEnd . "' AND `endDate` >= '".$today."'))");
        $query = $this->db->get();

        return $query->result_array();
    }

    
    //
    function getAllEvents($timeframe, $catId = null, $language) {
       $today = Date('Y:m:d');
        $timeframeEnd = Date('Y:m:d', mktime(0, 0, 0, date("m"), date("d") + $timeframe, date("Y")));
        $this->db->select('event.id as eventId, eventtitle.' . $language . ' as event_title, 
            eventdescr.' . $language . ' as event_descr,
            categoryname.' . $language . ' as category_name, 
            subcategoryname.' . $language . ' as subcategory_name,
            startDate, endDate    ');
        $this->db->from('event');
        $this->db->join('subcategory', 'event.subcatId = subcategory.id');
        $this->db->join('category', 'subcategory.catId = category.id');
        $this->db->join('eventtitle', 'eventtitle.id = event.titleId');
        $this->db->join('eventdescr', 'eventdescr.id = event.descrId');
        $this->db->join('categoryname', 'categoryname.id = category.nameId');
        $this->db->join('subcategoryname', 'subcategoryname.id = subcategory.nameId');

       
            //case start and end date are in timerange
        $this->db->where("((`startDate` >=  '" . $today . "' 
                AND  `endDate` <=  '" . $timeframeEnd . "')");
        //case: start date is before timeframe, end date is after timeframe
        $this->db->or_where("(`startDate` <=  '" . $today . "' 
                AND  `endDate` >=  '" . $timeframeEnd . "')");
         //case: start date is IN timeframe, end date is after timeframe
        $this->db->or_where("(`startDate` >=  '" . $today . "' 
                AND `startDate` <= '".$timeframeEnd."'
                AND  `endDate` >=  '" . $timeframeEnd . "')");
        //case: end date is in timerange, start date is before timeRange
        $this->db->or_where("(`startDate` <=  '" . $today . "' 
                AND  `endDate` <=  '" . $timeframeEnd . "' AND `endDate` >= '".$today."'))");
        
        if (isset($catId)) {
            echo '</br>cat id is set</br>';
            $this->db->where('category.id', $catId);
        }
        //$this->db->order_by('category.name,subcategory.name');
        $this->db->order_by('category_name,subcategory_name');

        $query = $this->db->get();

        return $query->result_array();
    }

    function getEvent($eventId,$language)
    {
        $this->db->select('event.id as eventId, eventtitle.' . $language . ' as event_title, 
            eventdescr.' . $language . ' as event_descr,
            startDate, endDate, fee, link    ');
        $this->db->from('event');
        $this->db->join('eventtitle', 'eventtitle.id = event.titleId');
        $this->db->join('eventdescr', 'eventdescr.id = event.descrId');
        $this->db->where('event.id ',$eventId);
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    function insertEvent() {
        $data = array(
            'subcatId' => $this->input->post('subcategoryId'),
            'title' => $this->input->post('name'),
            'descr' => $this->input->post('descr')
        );
        $this->db->insert('event', $data);
    }

}

?>
