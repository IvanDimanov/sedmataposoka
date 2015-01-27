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
    
    function getPartners($language)
    {
        $this->db->select(' partner.link, partner.imagePath, partnername.'.$language.' as name');
        $this->db->join('partnername','partnername.id = partner.nameId');
        $query = $this->db->get('partner');
       
        
        return $query->result_array();
    }
}

?>
