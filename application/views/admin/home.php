<?php include('templates/header.php'); ?>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
        	<a class="logo" href="#" ><img src="<?php echo base_url()?>img/logo_sedmata_posoka.png" alt="logo" /></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">Categories</a></li>
            <li><a href="#contact">Articles</a></li>
          </ul>
          <button type="button" class="btn btn-sm btn-default log_out" id="log_out">Log out</button>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase container_custom" role="main">
      <div class="page-header">
        <h1>Welcome to sedmataposoka admin panel</h1>
        <p>Here you can add/delete/modify artticles.</p>
      </div>
    </div>
    
<?php include('templates/footer.php'); ?>      