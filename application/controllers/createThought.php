<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of createThought
 *
 * @author Tedy
 */
class createThought extends CI_Controller {

    //put your code here
    function index() {
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('thoughtModel');

        $this->form_validation->set_rules('text', 'Thought text', 'trim|required|addslashes');
        $this->form_validation->set_rules('author', 'Author', 'trim|max_length[30]|addslashes');
        $this->form_validation->set_rules('startDate', 'start date', 'callback_validateDate');
        $this->form_validation->set_rules('endDate', 'end date', 'callback_validateDate');
        $this->form_validation->set_message('validateDate', 'your date is not in valid format');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('createThoughtView');
        } else {
            if ($this->thoughtModel->insertThought()) {
                $data['formName'] = 'thought';
                $this->load->view('formSuccess', $data);
            } else {
                echo 'error while inserting into database';
            }
        }
    }
    
    public function validateDate($date) {
        $pattern = '/([0-9]{4}:[0-9]{2}:[0-9]{2})/';
        return (bool) (preg_match($pattern, $date));
    }

}

?>
