<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_LoadData
 *
 * @author Tedy
 */
class Load_Data extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
    }

    function index() {
        
    }

    function populateHeaderData($language) {
        //get original CodeIgniter object
        $language = strtolower($language);
        $CI = &get_instance();

        $CI->load->model('toughtModel');
        $CI->load->model('toughtModel');
        $CI->load->model('partnerModel');
        $CI->load->model('adsModel');
        $CI->load->model('categoryModel');
        $CI->load->model('subcategoryModel');

        //get banner for today type=1, limi = 7
        $data['banners'] = $CI->adsModel->getAds(null, 1, 7, $language);
        //get adds for today type = 2, limit = 2
        $data['ads'] = $CI->adsModel->getAds(null, 2, 2, $language);
        $data['partners'] = $CI->partnerModel->getPartners($language);
        $data['categories'] = $CI->categoryModel->
                getAllCategoriesName($language);
        $data['subcategories'] = $CI->subcategoryModel->
                getSubcategoriesForCategory($catId = null, $language);
        $data['tought'] = $CI->toughtModel->getTought(null, 1, $language);

        return $data;
    }

}

?>
