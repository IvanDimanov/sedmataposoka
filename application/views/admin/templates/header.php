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
	<!-- TODO: add page title for different pages -->
    <title>Admin</title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/admin/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/admin/admin.css">
</head>
<body>