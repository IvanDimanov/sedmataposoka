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
    
    function getAllEventsForSubcategory($day = null,$subcatId,$language)
    {
        $day = isset($day)?$day:Date('Y:m:d');
        $this->db->select('subcatId,eventtitle.'.$language.' as title,
            eventdescr.'.$language.' as descr, 
            startDate, endDate, fee, link');
        $this->db->from('event');
        $this->db->join('eventtitle','eventtitle.id = event.titleId');
        $this->db->join('eventdescr','eventdescr.id = event.descrId');
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
    
    function getAllEvents($day = null,$catId = null,$language)
    {
        $day = isset($day)?$day:Date('Y:m:d');
        $this->db->select('eventtitle.'.$language.' as event_title, 
            eventdescr.'.$language.' as event_descr,
            categoryname.'.$language.' as category_name, 
            subcategoryname.'.$language.' as subcategory_name');
        $this->db->from('event');
        $this->db->join('subcategory','event.subcatId = subcategory.id');
        $this->db->join('category','subcategory.catId = category.id');
        $this->db->join('eventtitle','eventtitle.id = event.titleId');
        $this->db->join('eventdescr','eventdescr.id = event.descrId');
        $this->db->join('categoryname','categoryname.id = category.nameId');
        $this->db->join('subcategoryname','subcategoryname.id = subcategory.nameId');
       
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
         //$this->db->order_by('category.name,subcategory.name');
        $this->db->order_by('category_name,subcategory_name');
         
        $query = $this->db->get();
        
        var_dump($query->result_array());
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
