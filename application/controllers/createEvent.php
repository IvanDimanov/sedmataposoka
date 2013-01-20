<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of createEvent
 *
 * @author Tedy
 */
class createEvent extends CI_Controller {

    //put your code here
    function index() {
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('subcategoryModel');
        $data['subcategories'] = $this->subcategoryModel->getSubcategoriesForCategory(null);

        $this->form_validation->set_rules('subcategoryId', 'subcategory name', 'greater_than[0]');
        $this->form_validation->set_message('greater_than', 'You have to select subcategory');
        $this->form_validation->set_rules('name', 'subcategory name', 'trim|required|min_length[5]|max_length[30]|addslashes|xss_clean');
        $this->form_validation->set_rules('startDate', 'start date', 'callback_validateDate');
        $this->form_validation->set_rules('endDate', 'end date', 'callback_validateDate');
        $this->form_validation->set_message('validateDate', 'your date is not in valid format');
        //todo fee link validation


        if ($this->form_validation->run() == FALSE) {

            $this->load->view('createEventView', $data);
        } else {
            $this->load->model('eventModel');
            $this->eventModel->insertEvent();
            $data['formName'] = 'subcategory';
            $this->load->view('formSuccess', $data);
        }
    }

    public function validateDate($date) {
        $pattern = '/([0-9]{4}:[0-9]{2}:[0-9]{2})/';
        return (bool) (preg_match($pattern, $date));
    }

}

?>
