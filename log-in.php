<?php

if(isset($_GET['error'])) {
  $error = $_GET['error'];
}

if(isset($_POST['email'], $_POST['pass'])/* && strlen($_POST['email']) > 0 && strlen($_POST['pass']) > 0*/) {
  require_once "db-api/all.php";

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $user = getUser($email, $pass);
  if($user == NULL) {
    $error = "Invalid email or password.";
  } else {
    $hash = $user->startSession();
    $expiration = time() + (60 * 60 * 24 * 3); // TODO: care about "remember me" button
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

if(isset($_POST['fname'], $_POST['lname'], $_POST['emailr'], $_POST['passr'])) {
  $register = true;
  require_once "db-api/all.php";

  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $email = $_POST['emailr'];
  $pass = $_POST['passr'];
  if(doesUserExist($email)) {
    $error0 = "A account with that email already exists.";
  } else {
    $user = addUser($fname, $lname, $email, $pass); // TODO: test
    $hash = $user->startSession();
    $expiration = time() + (60 * 60 * 24 * 3); // TODO: care about "remember me" button
    setcookie('hash', $hash, $expiration, "/");
    header("Location: dashboard.php");
    die();
  }
}

function a($var) {
  if(isset($_POST[$var])){ 
    return 'value="' . $_POST[$var] . '" ';
  }
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
        <input type="email" class="form-control first" placeholder="Email" id="email" name="email" required autofocus <?php echo a('email'); ?>/>
        <input type="password" class="form-control last" placeholder="Password" id="pass" name="pass" required />
        <label class="checkbox">
          <input type="checkbox" value="remember-me" /> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <div id="noaccount"><a href="#" onclick="showSignUp()">Don't have an account yet?</a></div>
      </form>

      <form class="form-signin" role="form" action="#" method="post" id="sign-up"<?php if($register) echo ' style="display: block"'; ?>>
        <h2 class="form-signin-heading">Register</h2>
        <?php if(isset($error0)) echo '<div class="error">' . $error0 . '</div>'; ?>
        <input type="text" class="form-control first" placeholder="First Name" id="fname" name="fname" required <?php echo a('fname'); ?>/>
        <input type="text" class="form-control" placeholder="Last Name" id="lname" name="lname" required <?php echo a('lname'); ?>/>
        <input type="email" class="form-control" placeholder="Email" id="emailr" name="emailr" required <?php echo a('emailr'); ?>/>
        <input type="password" class="form-control last" placeholder="Password" id="passr" name="passr" required />
        <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
      </form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>