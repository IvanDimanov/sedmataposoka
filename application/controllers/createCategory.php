<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of createcategory
 *
 * @author Tedy
 */
class createCategory extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    //put your code here
    function index() {
        
        $this->form_validation->set_rules('name','name',
                'trim|required|min_length[5]|max_length[30]|addslashes|is_unique[category.name]');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->load->view('createCategoryView');
        }
        else
        {
            $this->load->model('categoryModel');
            $this->categoryModel->insertCategory();
            $data['formName']='category';
            $this->load->view('formSuccess',$data);
        }
    }

}

?>
