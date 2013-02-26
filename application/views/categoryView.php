<?php include('templates/header.php'); ?>

<?php 
echo '<img src="' . $categoryInfo['pictureSrc'] . '" alt="logoImg" height="42" width="42">';
?>

<h2><?php echo $categoryInfo['name']; ?></h2>

<h2><?php echo $categoryInfo['descr']; ?></h2>

<?php
$this->load->helper('url');
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/today'>
    Today's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/tomorrow'>
    Tomorrow's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/week'>
    Week's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/month'>
    Month's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
//anchor("event/search/".$categoryInfo['id']."/today",
//    "Today's Events for category <b>".$categoryInfo['name']."</b>");
?>
<?php include('templates/footer.php'); ?>