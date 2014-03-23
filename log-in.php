<?php

if(isset($_GET['error'])) {
  $error = $_GET['error'];
}

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

$register = false;

if(isset($_GET['error0'])) {
  $register = true;
  $error0 = $_GET['error0'];
}

if(isset($_POST[''])) {
  $register = true;

}

?>
<html>
	<head>
		<meta charset ="utf-8">
		<link rel = "stylesheet" href = "bootstrap/css/bootstrap.css">
		<link rel = "stylesheet" href = "bootstrap/css/log-in.css">
    <script type="text/javascript">
      function showSignUp() {
        document.getElementById('sign-up').style.display = 'block';
        document.getElementById('noaccount').style.display = 'none';
      }
    </script>
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
        <button class="btn btn-lg btn-primary btn-block" type="submit"> <a href = "#">Sign in</a></button>
        <div id="noaccount"><a href="#" onclick="showSignUp()">Don't have an account yet?</a></div>
      </form>

      <form class="form-signin" role="form" action="#" method="post" id="sign-up"<?php if($register) echo ' style="display: block"'; ?>>
        <h2 class="form-signin-heading">Register</h2>
        <?php if(isset($error0)) echo '<div class="error">' . $error0 . '</div>'; ?>
        <input type="text" class="form-control" placeholder="First Name" id="fname" name="fname" required />
        <input type="text" class="form-control" placeholder="Last Name" id="lname" name="lname" required />
        <input type="email" class="form-control" placeholder="Email address" id="email" name="email" required autofocus />
        <input type="password" class="form-control" placeholder="Password" id="pass" name="pass" required />
        <label class="checkbox">
          <input type="checkbox" value="remember-me" /> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit"> <a href = "#">Register</a></button>
      </form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>