<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sedmataposokaModel
 *
 * @author Tedy
 */
class sedmataposokaModel extends CI_Model {
    //put your code here
    
    public function __construct() {
        parent::__construct();
        //databse is inicialized in config.php
        //and autoload in autoload.php
    }
    
    function getTought()
    {
        $currDate = Date('Y:m:d');
        //$currDate = strtotime($currDate);
        //get if tought exist for current date
        $this->db->select('text,author');
        $this->db->from('sedmataposoka.tought');
        $this->db->where('startDate <= '+$currDate+'AND endDate <= ' + $currDate);
        $query = $this->db->get();
        var_dump($query);
        
        
    }
    
    
    
}

?>
