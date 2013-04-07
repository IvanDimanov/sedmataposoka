<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form class="form formContacts" action="">
	<h1>Контакти</h1>
	<p>Ако имате въпроси, моля свържете се с нас:</p>
	<div class="inputHolder">
		<label for="name">Име</label>
		<input class="inputMain" type="text" id="name" />
		<p class="errorTxt">Моля въведете име!</p>
	</div>
	<div class="inputHolder">
		<label for="lname">Фамилия</label>
		<input class="inputMain" type="text" id="lname" />
		<p class="errorTxt">Моля въведете фамилия!</p>
	</div>
	<div class="inputHolder">
		<label for="email">Емайл</label>
		<input class="inputMain" type="email" id="email" />
		<p class="errorTxt">Моля въведете валиден емайл адрес!</p>
	</div>
	<div class="inputHolder">
		<label for="message">Съобщение</label>
		<textarea id="message"></textarea>
		<p class="errorTxt">Моля въведете съобщение!</p>
	</div>
	<input class="bttn" type="submit" id="contactSubmit" />
</form>