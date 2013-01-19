<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php include('templates/header.php'); ?>

<h1>Login</h1>

<?php
echo validation_errors();
$this->load->helper('url');
echo form_open(base_url().'login');

?>
<label for="username">Username:</label>
<input type="text" size="20" id="username" name="username"/>
<br/>
<label for="password">Password:</label>
<input type="password" size="20" id="passowrd" name="password"/>
<br/>
<input type="submit" value="Login"/>
</form>


<?php include('templates/footer.php'); ?>
