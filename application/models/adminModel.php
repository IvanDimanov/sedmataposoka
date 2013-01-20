<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of adminModel
 *
 * @author Tedy
 */
class adminModel extends CI_Model {

    //put your code here
    function getSalt($username) {
        $this->db->select('salt');
        $this->db->where('name', $username);
        $query = $this->db->get('admin');
        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            //todo error
            return false;
        }
    }

    function isAdmin() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $salt = $this->getSalt($username);
        $cryptPassword = hash_hmac("sha256", $password, $salt['salt']);

        $this->db->select('id, name');
        $this->db->where('name = "' . $username . '" AND pass = "' . $cryptPassword . '"');
        $query = $this->db->get('admin');

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

}

?>
