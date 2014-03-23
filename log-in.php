<?php

if(isset($_GET['error']) $error = $_GET['error'];

if(isset($_POST['email'], $_POST['pass'])) {
  require_once "db-api/all.php";

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $user = getUser($email, $pass);
  if($user == NULL) {
    $error = "Invalid email or password.";
  } else {
    $hash = $user->startSession();
    $expiration = time() + (60 * 60 * 24 * 3);
    setcookie('hash', $hash, $expiration, "/");
    header("Location: dashboard.php");
    die();
  }
}

?>
<html>
	<head>
		<meta charset ="utf-8">
		<link rel = "stylesheet" href = "bootstrap/css/bootstrap.css">
		<link rel = "stylesheet" href = "bootstrap/css/log-in.css">
	</head>

  <body>

    <div class="container">

      <form class="form-signin" role="form" action="#" method="post">
        <h2 class="form-signin-heading">Please sign in</h2>
        <?php if(isset($error)) echo '<div class="error">' . $error . '</div>'; ?>
        <input type="email" class="form-control" placeholder="Email address" id="email" name="email" required autofocus />
        <input type="password" class="form-control" placeholder="Password" id="pass" name="pass" required />
        <label class="checkbox">
          <input type="checkbox" value="remember-me" /> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit"> <a href = "#"> Sign in</button>
      </form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>