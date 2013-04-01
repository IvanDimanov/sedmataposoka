<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1> Banner </h1>
<?php
foreach ($banners as $banner) {
    echo '<h1>' . $banner['title'] . '</h1>';
    echo '<img src="' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $banner['link'] . '">Banner Link</a>';
}


$this->load->helper('url');
echo "</br><a href='".base_url()."search/dateSearch/0'>
    Dnes</a></br>";
echo "<a href='".base_url()."event/search/dateSearch/7'>
    Week</a></br>";
echo "<a href='".base_url()."event/search/dateSearch/14'>
    Week</a></br>";
echo "<a href='".base_url()."event/search/dateSearch/30'>
    Week</a></br>";

var_dump($events);
foreach($events as $event)
{
echo "<a href='".base_url()."event/".$event['eventId']."'>
    ".$event['event_title']."</a></br>";
}
?>
