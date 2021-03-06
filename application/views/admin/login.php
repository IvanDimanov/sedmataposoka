
<?php include('templates/header.php'); ?>

<div class="container log-in-page">
  <h1>Welcome to admin panel</h1>
  <form class="form-signin" id="form-admin-login">
    <h2 class="form-signin-heading">Please log in</h2>
    <label for="inputName" class="sr-only">Email address</label>
    <input type="text" id="inputName" class="form-control" placeholder="Name" required="" autofocus="">
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
    <!-- captcha will be shown when error log in is reached -->
    <div class="captcha">
      <div class="g-recaptcha" data-sitekey="6LcJSPoSAAAAABGa0Gf1of7UdLLs2PawIy5YXFPa"></div>
      <script type="text/javascript"
          src="https://www.google.com/recaptcha/api.js">
      </script>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit" id="admin_log_in">Sign in</button>
  </form>
</div>

<?php include('templates/footer.php'); ?>
