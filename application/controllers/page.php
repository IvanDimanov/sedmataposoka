<?php

/* Class for testing CI page activities */
class Page extends CI_Controller {
  
  function index() {
    
    $data['title']       = 'Test PHP page title';
    $data['header_info'] = 'Test header info';
    $data['test_var']    = 'Test variable from controller/page.php';
    
    $this->load->view('page.php', $data);
  }
}

/* End of file page.php */