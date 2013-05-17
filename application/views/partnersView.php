<div class="partnersHolder">
	<h1>Patners</h1>
	<a href="#" >to do links for partners</a>
</div>
<?php
foreach($partners as $partner)
{
    echo $partner['name']. ' ';
    echo 'link :' .$partner['link'];
    echo '<img src="'.$partner['logoSrc'].'"/>';
    
    
}
?>