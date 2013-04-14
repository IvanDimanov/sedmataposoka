
﻿<div class="subCategoryMainHolder">
	<div class="subCategoryHolder">
		<h1 class="title"><?php echo $subcategoryInfo['name']; ?></h1>
		<p><?php echo $subcategoryInfo['descr']; ?></p>
	</div>
	<h2>Събития</h2>
	<div class="subCategoryEventHolder">
		<?php
			foreach($events as $event)
			{
			
			echo "<div class='subCategoryEvent'><h3><a href='".base_url().$language.'/'."event/".$event['eventId']."'>
			".$event['title']."</a></h3>";
			echo "<p class='date'>date1</p>";
			echo '<p>'.$event['descr'].'</p></div>';
			}

		?>
	</div>	
</div>	
		

