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
    //return all subcat if categoryId is not set
    function getSubcategoriesForCategory($categoryId = null,$language) {
        $this->db->select(' subcategory.id,subcategory.categoryId, 
            subcategoryname.'.$language.' as name, 
            subcategorydescription.'.$language.' as descr');
        $this->db->from('subcategory');
        $this->db->join('subcategoryname', 'subcategoryname.id = subcategory.nameId');
        $this->db->join('subcategorydescription', 'subcategorydescription.id = subcategory.descriptionId');
        if (isset($categoryId)) {
            $this->db->where('subcategory.categoryId = "' . $categoryId . ' "');
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getSubcategriesInfo($subcategoryId, $language)
    {
        $this->db->select(' subcategory.id,subcategory.categoryId, 
            subcategoryname.'.$language.' as name, 
            subcategorydescription.'.$language.' as descr');
        $this->db->from('subcategory');
        $this->db->join('subcategoryname', 'subcategoryname.id = subcategory.nameId');
        $this->db->join('subcategorydescription', 'subcategorydescription.id = subcategory.descriptionId');
        $this->db->where('subcategory.id = "' . $subcategoryId . ' "');
                $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        $result = $query->result_array();
        
        return $result[0];
    }

    /*   function getAllSubcategories()
      {
      $this->db->select(' subcategory.id,subcategory.categoryId, subcategory.name, subcategory.descr');
      $this->db->from('subcategory,category');

      $query = $this->db->get();
      return $query->result_array();
      }
     * */

    function insertSubcategory() {
        
        $data = array(
            'categoryId' => $this->input->post('categoryId'),
            'name' => $this->input->post('name'),
            'descr' => $this->input->post('descr')
        );
        $this->db->insert('subcategory', $data);
    }

}

?>
