<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of login
 *
 * @author Tedy
 */
class login extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('adminModel');
    }

    function index() {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim||min_length[5]|max_length[30]|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', '
            trim|required|min_length[5]|max_length[30]|xss_clean|callback_checkDatabase');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('loginView');
        } else {
            $data['formName']='adminPanel';
            $this->load->view('formSuccess');
            
        }
    }

    function checkDatabase() {
        $this->load->library('session');
        $username = $this->input->post('username');
        //check if user exist in database
        $result = $this->adminModel->isAdmin();
        if ($result) {
            $sess_array = array();
            foreach ($result as $row) {
                $sess_array['id'] = $row->id;
                $sess_array['name'] = $row->name;
                $this->session->set_userdata('loggedIn', $sess_array);
            }
            return true;
        } else {
            $this->form_validation->set_message('checkDatabase', 'Invalid username or password');
            return false;
        }
    }

}

?>
