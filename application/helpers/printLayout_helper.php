<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('test_method')) {

    function printLayout($pointer, $templateLayout = null, $mainLayout, $data) {
        //v header-a se zarejda catalog ads baner tought of day 
        $pointer->load->view('templates/header', $data);
        
        //remove if isset always display header+ main+ footer
        
        //if (isset($templateLayout)) {
          //  $pointer->load->view($templateLayout, $data);
        //}
        $pointer->load->view($mainLayout, $data);
        $pointer->load->view('templates/footer', $data);
    }

}
?>
