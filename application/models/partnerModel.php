<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of parthnerModel
 *
 * @author Tedy
 */
class partnerModel extends CI_Model {
    //put your code here
    
    function getPartners()
    {
        $this->db->select('name, link, logoSrc');
        $query = $this->db->get('partner');
       
        
        return $query->result_array();
    }
}

?>
