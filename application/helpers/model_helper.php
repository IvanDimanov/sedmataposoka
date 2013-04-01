<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('test_method')) {

    function searchWordHelper($pointer, $tableName, $word, $language) {
        $pointer->db->select('id');
        $pointer->db->from('categoryname');
        $pointer->db->like($language,$word);
        $pointer = $this->db->get();
        
        return $query->array();
    }

}
?>
