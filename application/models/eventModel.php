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
    
    function getAllEventsForSubcategory($day = null,$subcatId)
    {
        $day = isset($day)?$day:Date('Y:m:d');
        $this->db->select('subcatId,title,descr,startDate,endDate,fee,link');
        $this->db->from('event');
        $this->db->where('subcatId = "'.$subcatId.'"');
        if($day != 0)
        {
            echo '<br/>day not 0</br>'.$day;
            $this->db->where("`startDate` <=  '".$day."' 
                AND  `endDate` >=  '".$day."'");
         }
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    function getAllEvents($day = null,$catId = null)
    {
        $day = isset($day)?$day:Date('Y:m:d');
        $this->db->select('event.title as event_title, category.name as category_name, subcategory.name as subcategory_name');
        $this->db->from('event');
        $this->db->join('subcategory','event.subcatId = subcategory.id');
        $this->db->join('category','subcategory.catId = category.id');
        if($day != 0)
        {
            echo '<br/>day not 0</br>'.$day;
            $this->db->where("`startDate` <=  '".$day."' 
                AND  `endDate` >=  '".$day."'");
         }
         if( isset($catId))
         {
             echo '</br>cat id is set</br>';
             $this->db->where('category.id',$catId);
         }
         $this->db->order_by('category.name,subcategory.name');
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    function insertEvent()
    {
        $data = array(
            'subcatId' => $this->input->post('subcategoryId'),
            'title' => $this->input->post('name'),
            'descr' => $this->input->post('descr')
        );
        $this->db->insert('event', $data);
        
    }
}

?>
