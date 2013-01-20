<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of adsModel
 *
 * @author Tedy
 */
class adsModel extends CI_Model {
    
    
    //get baner for  day
    function getLastBanner()
    {}
    
    function getAds($day = null,$type,$limit = null)
    {
        $day = isset($day)?$day:Date('Y:m:d');
       
        $this->db->select('imagePath, link, title');
        $this->db->where('type = "'.$type.'" AND startDate <= "'.$day.'" 
            AND endDate >= "'.$day.'"');
        if($limit != null)
        {
            $this->db->limit($limit);
        }
        $query = $this->db->get('ads');
        
        return $query->result_array();
    }
    
    //get ads limit by $limit 
 /*   getAds($limit = -1, $day = Date('...'))
    
    client:
        getAds(4)              => return 4 ads for today
        getAds(4, '2012-...')  => return 4 ads for specific day
        getAds()               => return all ads in DB
            */
    //admin panel
    
    
    
}

?>
