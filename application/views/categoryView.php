

<?php
$this->load->helper('url');
?>


			<div class="categoryMainHolder clear">
				<div class="categoryHolder">
					<div class="clear">
						<?php 
							echo '<img src="'.base_url().'img/' . $categoryInfo['pictureSrc'] . '" alt="logoImg" height="42" width="42">';
						?>
						<h1 class="title"><?php echo $categoryInfo['name']; ?></h1>
						<p><?php echo $categoryInfo['descr']; ?></p>
					</div>
					<div class="subCategories">
						<?php
							for ($i = 0; $i < sizeof($subcategories); $i++) {
								if ($subcategories[$i]['catId'] === $categoryInfo['id']) {
								echo '<a href="' . base_url() . 'subcategory/' . $subcategories[$i]['id'] . '">' .
								$subcategories[$i]['name'] . ' </a>';
								}
							}
						?>
					</div>
				</div>
			</div>



<?php
//$this->load->helper('url');
/*echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/today'>
    Today's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/tomorrow'>
    Tomorrow's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/week'>
    Week's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
echo "<a href='".base_url()."event/search/".$categoryInfo['id']."/month'>
    Month's Events for category <b>".$categoryInfo['name']."</b> </a></br>";
//anchor("event/search/".$categoryInfo['id']."/today",
//    "Today's Events for category <b>".$categoryInfo['name']."</b>");
 * 
 */
?>