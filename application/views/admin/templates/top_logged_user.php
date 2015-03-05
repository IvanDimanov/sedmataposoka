<!-- Fixed navbar for loged user-->
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
    	<a class="logo" href="<?php echo base_url()?>admin/home" ><img src="<?php echo base_url()?>img/logo_sedmata_posoka.png" alt="logo" /></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="<?php echo base_url()?>admin/categories">Categories</a></li>
        <li><a href="<?php echo base_url()?>admin/subcategories">Subcategories</a></li>
        <li><a href="<?php echo base_url()?>admin/events">Events</a></li>
        <li><a href="<?php echo base_url()?>admin/thoughts">Thoughts</a></li>
        <li><a href="<?php echo base_url()?>admin/partners">Partners</a></li>
      </ul>
      <button type="button" class="btn btn-sm btn-default log_out" id="log_out">Log out</button>
    </div><!--/.nav-collapse -->
  </div>
</nav>