
<h2><?php echo $subcategoryInfo['name']; ?></h2>

<h2><?php echo $subcategoryInfo['descr']; ?></h2>

<?php
foreach($events as $event)
{
    echo "<a href='".base_url()."event/".$event['eventId']."'>
    ".$event['title']."</a></br>";
    echo $event['descr'].'</br>';
}

?>

