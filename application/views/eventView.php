<div class="EventMainHolder clear">
	<h1 class="title"><?php echo $event['event_title']; ?></h1>
	<p><?php echo $event['event_descr']; ?></p>
	<div class="info">
		<p><span class="bold"><?=$ui_labels['event']['fee']       ?>: </span><?=$event["fee"]?></p>
		<p><span class="bold"><?=$ui_labels['event']['start_date']?>: </span><?=$event["startDate"]?></p>
		<p><span class="bold"><?=$ui_labels['event']['end_date']  ?>: </span><?=$event["endDate"]?></p>
		<p><span class="bold"><?=$ui_labels['event']['link']      ?>: </span><a href=""><?=$event["link"]?></a></p>
	</div>				
</div>

