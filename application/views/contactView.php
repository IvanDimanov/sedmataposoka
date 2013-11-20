<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form class="form formContacts" action="">
	<h1><?=$ui_labels['contacts']['content_title']?></h1>
	<p><?=$ui_labels['contacts']['description']?></p>
	<div class="inputHolder">
		<label for="name"><?=$ui_labels['contacts']['name']?></label>
		<input class="inputMain" type="text" id="name" />
		<p class="errorTxt"><?=$ui_labels['contacts']['error_name']?></p>
	</div>
	<div class="inputHolder">
		<label for="lname"><?=$ui_labels['contacts']['last_name']?></label>
		<input class="inputMain" type="text" id="lname" />
		<p class="errorTxt"><?=$ui_labels['contacts']['error_last_name']?></p>
	</div>
	<div class="inputHolder">
		<label for="email"><?=$ui_labels['contacts']['email']?></label>
		<input class="inputMain" type="email" id="email" />
		<p class="errorTxt"><?=$ui_labels['contacts']['error_email']?></p>
	</div>
	<div class="inputHolder">
		<label for="message"><?=$ui_labels['contacts']['message']?></label>
		<textarea id="message"></textarea>
		<p class="errorTxt"><?=$ui_labels['contacts']['error_message']?></p>
	</div>
	<input class="bttn" type="submit" id="contactSubmit" value="<?=$ui_labels['contacts']['button']?>" />
</form>