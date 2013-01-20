<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of createSubcategory
 *
 * @author Tedy
 */
class createSubcategory extends CI_Controller {

    //put your code here
    function index() {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model('categoryModel');

        $this->form_validation->set_rules('categoryId', 'subcategory name', 'greater_than[0]');
        $this->form_validation->set_message('greater_than', 'You have to select category');
        $this->form_validation->set_rules('name', 'subcategory name', 'trim|required|min_length[5]|max_length[30]|addslashes|is_unique[subcategory.name]|xss_clean');
        $this->form_validation->set_rules('descr', 'subcategory description', 'trim||xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $data['categories'] = $this->categoryModel->getAllCategoriesName();
            $this->load->view('createSubcategoryView', $data);
        } else {

            $this->load->model('subcategoryModel');
            $this->subcategoryModel->insertSubcategory();
            $data['formName'] = 'subcategory';
            $this->load->view('formSuccess', $data);
        }
    }

}

?>
