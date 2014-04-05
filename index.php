<?php

if(isset($_COOKIE['hash'])) {
  require_once "db-api/all.php";
  $user = getUserByHash($_COOKIE['hash']);
  if($user != NULL) {
    header("Location: dashboard.php");
    die();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Codium</title>

    <!-- Bootstrap core CSS -->
    <link href="frameworks/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="frameworks/bootstrap/css/cover.css" rel="stylesheet">
    <link href="css/landing.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body >

    <div class="site-wrapper1" >

      <div class="site-wrapper-inner">

        <div class="cover-container" style = "margin-bottom:10%">
<!--
          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">Cover</h3>
              <ul class="nav masthead-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Features</a></li>
                <li><a href="#">Contact</a></li>
              </ul>
            </div>
          </div> -->
          
          <div class="inner cover" style = "margin-left:40%">
            <img  src = "img/codium3.png">
            <p class="lead" style = "margin-left:40%; white-space:nowrap;">The forefront of Codjamacation &#8482;</p>
            <p class="lead" style = "margin-left:28%; width:100%">
              <a href="#page2" style = "display:inline-block;" class="btn btn-lg btn-success">About us</a>
              <a href="log-in.php" style = "display:inline-block;" class="btn btn-lg btn-success">Sign in/Register</a>
            </p>
          </div>

          <div class="mastfoot">
            <div class="inner">
              <p>&copy 2014 Codium</p>
            </div>
          </div>

        </div>

      </div>

    </div>

    <div class="site-wrapper2" id = "page2">

      <div class="site-wrapper-inner">

        <div class="cover-container" style = "margin-bottom:10%">
<!--
          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">Cover</h3>
              <ul class="nav masthead-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Features</a></li>
                <li><a href="#">Contact</a></li>
              </ul>
            </div>
          </div> -->

          <div class="inner cover" id = "page2-inner-cover">
            <a href = "#"> <img  src = "img/codium_white.png"></a>
            <p> Created for hackBCA 2014. </p>
            <p> Codium allows teachers to be there for their students, even when they can't really be there. </p>
            </div>
            <div id = "team">
              <h3> The Team: </h3>
              <a href = "http://facebook.com/jason.marmon"> <img src = "img/jason.png" style = "display:inline-block;"> </a>
              <a href = "https://github.com/gregthegeek"> <img src = "img/greg.png" style = "display:inline-block; margin-left:30%;"> </a>

            </div>

          
        </div>

      </div>

    </div>
    <!--
    <div class="site-wrapper" >

      <div class="site-wrapper-inner">

        <div class="cover-container" style = "margin-bottom:10%">

          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">Cover</h3>
              <ul class="nav masthead-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Features</a></li>
                <li><a href="#">Contact</a></li>
              </ul>
            </div>
          </div> 
          <div class="inner cover" style="margin-top:-30%">
            <h1> About us </h1>
            <div class="row">
              <div class = "circle-img" style="background-image: url(codium3.png);"></div>
            </div>
          </div>

          <div class="mastfoot">
            <div class="inner">
              <p>&copy 2014 Codium</p>
            </div>
          </div>

        </div>

      </div>
    </div>  
-->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <script src="../../assets/js/docs.min.js"></script>
  </body>
</html>
