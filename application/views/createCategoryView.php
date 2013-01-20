<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 
 * 
 */

include('templates/header.php');
?>
<h1> create category Item </h1>

<?php echo validation_errors(); 
$this->load->helper('url');?>

<?php echo form_open(base_url().'createcategory') ?>

	<label for="name">Name*</label> 
	<input type="input" name="name" /><br />

	<label for="descr">Description</label>
	<textarea name="descr"></textarea><br />
	
	<input type="submit" name="submit" value="Create news item" /> 

</form>

