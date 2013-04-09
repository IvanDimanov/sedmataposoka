<div class="EventMainHolder clear">
	<h1 class="title"><?php echo $event['event_title']; ?></h1>
	<p><?php echo $event['event_descr']; ?></p>
	<div class="info">
		<p><span class="bold">вход: </span><?php echo $event["fee"]?></p>
		<p><span class="bold">начало: </span><?php echo $event["startDate"]?></p>
		<p><span class="bold">край: </span><?php echo $event["endDate"]?></p>
		<p><span class="bold">място: </span><a href="" ><?php echo $event["link"]?></a></p>
	</div>				
</div>

