<?php
// A template file used for loading all HTML page header elements

$this->load->helper('url');

/*Detect any requests coming as URL strings*/
$query = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';

/*Secure at least language URL var*/
$uri_string = $this->uri->uri_string();
$uri_string = $uri_string ? $uri_string : $language;

/*Combine the final URL link & remove the not needed 'index.php' location*/
$full_url = $this->config->site_url().'/'.$uri_string.$query;
$full_url = str_replace('/index.php', '', $full_url);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin log in</title>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/admin/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/admin/admin.css">
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/hmac-sha3.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/sha3-min.js"></script>
	
	<script type="text/javascript" src="<?php echo base_url();?>js/admin/admin.js"></script>
</head>
<body>
	<div class="container log-in-page">
	  <h1>Welcome to admin panel</h1>
      <form class="form-signin" id="form-admin-login">
        <h2 class="form-signin-heading">Please log in</h2>
        <label for="inputName" class="sr-only">Email address</label>
        <input type="text" id="inputName" class="form-control" placeholder="Name" required="" autofocus="">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" id="admin_log_in">Sign in</button>
      </form>
    </div>	
</body>
</html>