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
    function getAllCategoriesName($language) {
        $this->db->select('category.id,categoryname.'.$language.' as name');
        $this->db->join('categoryname','categoryname.id = category.nameId');
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('category');

        return $query->result_array();
    }
    
    function getCategoryInfo($catId,$language)
    {
        $this->db->select('category.id,category.pictureSrc, 
            categoryname.'.$language.' as name,
            categorydescr.'.$language.' as descr');
        $this->db->join('categoryname','categoryname.id = category.nameId');
        $this->db->join('categorydescr','categorydescr.id = category.descrId');
        $this->db->where('category.id', $catId);
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
