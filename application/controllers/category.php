<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of category
 *
 * @author Tedy
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class category extends CI_Controller {
    //put your code here
    function index($catId = 1)
    {
        //todo set language
        $language = 'en';
        $this->load->model('categoryModel');
        $data['categoryInfo'] = $this->categoryModel->getCategoryInfo($catId, $language);        
        $this->load->view('categoryView',$data);
    }
    
    
}

?>
