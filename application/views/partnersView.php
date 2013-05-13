<?php

foreach($partners as $partner)
{
    echo $partner['name']. ' ';
    echo 'link :' .$partner['link'];
    echo '<img src="'.$partner['logoSrc'].'"/>';
    
    
}
?>
