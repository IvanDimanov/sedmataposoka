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
    
    function getCategoryInfo($categoryId,$language)
    {
        $this->db->select('category.id,category.imagePath, 
            categoryname.'.$language.' as name,
            categorydescription.'.$language.' as descr');
        $this->db->join('categoryname','categoryname.id = category.nameId');
        $this->db->join('categorydescription','categorydescription.id = category.descriptionId');
        $this->db->where('category.id', $categoryId);
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
