<?php include('templates/header.php'); ?>


<h2><?php echo $categoryInfo['name']; ?></h2>

<h2><?php echo $categoryInfo['descr']; ?></h2>

<?php
$this->load->helper('url');
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/today'>
    Today's Events for category <b>".$categoryInfo['name']."</b> </a>";
//anchor("event/search/".$categoryInfo['id']."/today",
//    "Today's Events for category <b>".$categoryInfo['name']."</b>");
?>
<?php include('templates/footer.php'); ?>