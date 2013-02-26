<?php include('templates/header.php'); ?>

<?php 

foreach($events as $event)
{
    echo $event['event_title']."</br>";
    echo $event['event_descr']."</br>";
    echo $event['category_name']."</br>";
    echo $event['subcategory_name']."</br>";
    echo '<hr />';
}

?>



<?php include('templates/footer.php'); ?>