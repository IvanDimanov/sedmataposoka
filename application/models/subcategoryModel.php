<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of subcategoryModel
 *
 * @author Tedy
 */
class subcategoryModel extends CI_Model {

    //put your code here
    //return all subcat if catId is not set
    function getSubcategoriesForCategory($catId = null,$language) {
        $this->db->select(' subcategory.id,subcategory.catId, 
            subcategoryname.'.$language.' as name, 
            subcategorydescr.'.$language.' as descr');
        $this->db->from('subcategory');
        $this->db->join('subcategoryname', 'subcategoryname.id = subcategory.nameId');
        $this->db->join('subcategorydescr', 'subcategorydescr.id = subcategory.descrId');
        if (isset($catId)) {
            $this->db->where('subcategory.catId = "' . $catId . ' "');
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*   function getAllSubcategories()
      {
      $this->db->select(' subcategory.id,subcategory.catId, subcategory.name, subcategory.descr');
      $this->db->from('subcategory,category');

      $query = $this->db->get();
      return $query->result_array();
      }
     * */

    function insertSubcategory() {
        
        $data = array(
            'catId' => $this->input->post('categoryId'),
            'name' => $this->input->post('name'),
            'descr' => $this->input->post('descr')
        );
        $this->db->insert('subcategory', $data);
    }

}

?>
