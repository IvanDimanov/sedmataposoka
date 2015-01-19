<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of thoughtModel
 *
 * @author Tedy
 */
class thoughtModel extends CI_Model {
    //put your code here
    
    function getLastThought($limit =1, $language)
   {
       //sort by date desc limit by $limit 
       $this->db->select('thought_text.'.$language.' as text, thought_author.'.$language.' as author' );
       $this->db->from('thought');
       $this->db->join('thought_author','thought_author.id = thought.authorId');
       $this->db->join('thought_text','thought_text.id = thought.textId');
       $this->db->order_by('thought.id','desc');
       $this->db->limit($limit);
       $query=$this->db->get();
       
       if($query->num_rows() > 0){
           return $query->result_array();
       }
       
       
   }
    
   function getThought($day = null ,$limit = 0,$language) {
        
       $day = isset($day)?$day:Date('Y:m:d');
       
        $this->db->select('thought_text.'.$language.' as text, thought_author.'.$language.' as author');
        $this->db->join('thought_author','thought_author.id = thought.authorId');
        $this->db->join('thought_text','thought_text.id = thought.textId');
        $this->db->where("`startDate` <=  '".$day."' AND  `endDate` >=  '".$day."'");
        if($limit > 0)
        {
            $this->db->limit($limit);
        }
        $query = $this->db->get('thought');
        
        if($query->num_rows() > 0)
        {
            //return first thought for specific day
            return $query->result_array();
            
        }
        else
        {
            //return last thought
            return $this->getLastThought(1,$language);
        }

   }
   
   
   
   function getRandomThought($thoughtsArr)
   {}
   
   
   function insertThought()
   {
       $data = array(
           'text' => $this->input->post('text'),
           'author' => $this->input->post('author'),
           'startDate' => $this->input->post('startDate'),
           'endDate' => $this->input->post('endDate'),
       );
       
       return $this->db->insert('thought',$data);
   }
}

?>
