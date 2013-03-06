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
    function getLast($type,$language)
    {
        $this->db->select('ads.imagePath, ads.link, adstitle.'.$language.' as title');
        $this->db->join('adstitle',' adstitle.id = ads.titleId ');
        $this->db->where('ads.type = "'.$type.'"');
        $this->db->order_by('ads.endDate','Desc');
        $this->db->limit(1);
        return $this->db->get('ads');
        
    }
    
    function getAds($day = null,$type,$limit = null,$language = "bg")
    {
        $day = isset($day)?$day:Date('Y:m:d');
        
        //limit should be between 0 and 7
        if( !isset($limit) )
        {
            $limit = 7;
        }  
        if($limit == 0 || $limit > 7)
        {
             $limit = 7;
        }
       
        $this->db->select('ads.imagePath, ads.link, adstitle.'.$language.' as title');
        $this->db->join('adstitle',' adstitle.id = ads.titleId ');
        $this->db->where('ads.type = "'.$type.'" AND startDate <= "'.$day.'" 
            AND endDate >= "'.$day.'"');
        if($limit != null)
        {
            $this->db->limit($limit);
        }
        $query = $this->db->get('ads');
        
        //if no record for date within start and end date period 
        //return latest banner
        if($query->num_rows() <= 0 and $type == 1)
        {
            $query = $this->getLast($type,$language);
        }
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
