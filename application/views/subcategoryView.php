<div class="subCategoryMainHolder">
	<div class="subCategoryHolder">
		<h1 class="title"><?php echo $subcategoryInfo['name']; ?></h1>
		<p><?php echo $subcategoryInfo['descr']; ?></p>
	</div>
	<?php if (count($events)) echo '<h2>'.$ui_labels['subcategory']['events'].'</h2>'; ?>
	<div class="subCategoryEventHolder">
		<?php
			foreach($events as $event)
			{
		
			echo "<div class='subCategoryEvent'><h3><a href='".base_url().$language.'/'."event/".$event['eventId']."'>
			".$event['title']."</a></h3>";
      echo '<p class="date"><span class="bold">'.$ui_labels['event']['start_date'].': </span>'.$event["startDate"].'</p>';
      echo '<p class="date"><span class="bold">'.$ui_labels['event']['end_date'  ].': </span>'.$event["endDate"]  .'</p>';
			echo '<p>'.$event['descr'].'</p></div>';
			}
		?>
	</div>
</div>