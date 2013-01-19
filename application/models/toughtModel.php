<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of toughtModel
 *
 * @author Tedy
 */
class toughtModel extends CI_Model {
    //put your code here
    
    function getLastTought($limit =1)
   {
       //sort by date desc limit by $limit 
       $this->db->select('text, author');
       $this->db->from('tought');
       $this->db->order_by('id','desc');
       $this->db->limit($limit);
       $query=$this->db->get();
       
       if($query->num_rows() > 0){
           return $query->result_array();
       }
       
       
   }
    
   function getTought($day = null ,$limit = 0) {
        
       $day = isset($day)?$day:Date('Y:m:d');
       
        $this->db->select('text,author');
        $this->db->from('sedmataposoka.tought');
        $this->db->where("`startDate` <=  '".$day."' AND  `endDate` >=  '".$day."'");
        if($limit > 0)
        {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        
        if($query->num_rows() > 0)
        {
            //return first tought for specific day
            return $query->result_array();
            
        }
        else
        {
            //return last tought
            return $this->getLastTought(1);
        }

   }
   
   
   
   function getRandomTought($toughtsArr)
   {}
   
   
   function insertTought()
   {
       $data = array(
           'text' => $this->input->post('text'),
           'author' => $this->input->post('author'),
           'startDate' => $this->input->post('startDate'),
           'endDate' => $this->input->post('endDate'),
       );
       
       return $this->db->insert('tought',$data);
   }
}

?>
