<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of categoryModel
 *
 * @author Tedy
 */
class categoryModel extends CI_Model {

    //put your code here
    function getAllCategoriesName() {
        $this->db->select('id,name');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('category');

        return $query->result_array();
    }
    
    function getCategoryInfo($catId)
    {
        $this->db->select('id,name,descr');
        $this->db->where('id', $catId);
        $query = $this->db->get('category');

        $result = $query->result_array();
        return $result[0];
    }

    function insertCategory() {
        $data = array(
            'name' => $this->input->post('name'),
            'descr' => $this->input->post('descr')
        );
        return $this->db->insert('category',$data);
    }

}

?>
