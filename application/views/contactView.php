<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form class="form formContacts" action="">
	<h1>��������</h1>
	<p>��� ����� �������, ���� �������� �� � ���:</p>
	<div class="inputHolder">
		<label for="name">���</label>
		<input class="inputMain" type="text" id="name" />
		<p class="errorTxt">���� �������� ���!</p>
	</div>
	<div class="inputHolder">
		<label for="lname">�������</label>
		<input class="inputMain" type="text" id="lname" />
		<p class="errorTxt">���� �������� �������!</p>
	</div>
	<div class="inputHolder">
		<label for="email">�����</label>
		<input class="inputMain" type="email" id="email" />
		<p class="errorTxt">���� �������� ������� ����� �����!</p>
	</div>
	<div class="inputHolder">
		<label for="message">���������</label>
		<textarea id="message"></textarea>
		<p class="errorTxt">���� �������� ���������!</p>
	</div>
	<input class="bttn" type="submit" id="contactSubmit" />
</form>