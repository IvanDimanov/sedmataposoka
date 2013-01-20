<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of event
 *
 * @author Tedy
 */
class event extends CI_Controller{
    //put your code here
    function search($catId, $date)
    {
        $this->load->model('eventModel');
        switch ($date) {
            case 'tomorrow':
                $date = mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
                break;

            case 'week':
                $date = mktime(0, 0, 0, date("m") , date("d")+7, date("Y"));
                break;

            case 'month':
                $date = mktime(0, 0, 0, date("m")+1, date("d"), date("Y"));
                break;

            default:
                $date = null;
                break;
        }
        $data['events'] = $this->eventModel->getAllEvents($date, $catId);
        
        $this->load->view('eventView',$data);
    }
}

?>
