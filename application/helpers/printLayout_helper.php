<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('test_method')) {

    function printLayout($pointer, $templateLayout = null, $mainLayout, $data) {

        /*Load all page general information labels for the requested '$language'*/
        $data['ui_labels'] = $pointer->uiLabelsModel->getLabelsForLanguage( $data['language'] );

        //v header-a se zarejda catalog ads baner thought of day 
        $pointer->load->view('templates/header', $data);
        $pointer->load->view($mainLayout, $data);
        $pointer->load->view('templates/footer', $data);
    }
}
?>
