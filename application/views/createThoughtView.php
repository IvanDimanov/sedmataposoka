<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include('templates/header.php');
?>
<h1> create category Item </h1>

<?php echo validation_errors();
$this->load->helper('url');?>

<?php echo form_open(base_url().'createThought') ?>

<label for="text">Text*</label> 
<input type="input" name="text" /><br />
<label for="author">Author</label>
<textarea name="author"></textarea><br />
<label for="startDate">Start Date</label> 
<input type="input" name="startDate" /><br />
<label for="endDate">End Date</label> 
<input type="input" name="endDate" /><br />

<input type="submit" name="submit" value="Create news item" /> 

</form>

