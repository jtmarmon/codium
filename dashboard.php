<?php

if(!isset($_COOKIE['hash'])) {
  header("Location: log-in.php?error=You%20must%20be%20logged%20in%20to%20see%20this%20page.");
  die();
}

require_once "db-api/all.php";

$user = getUserByHash($_COOKIE['hash']);
if($user == NULL) {
  header("Location: log-in.php?error=Your%20session%20has%20expired.");
  die();
}

if(isset($_POST['name'])) {
  $closed = isset($_POST['closed']) && $_POST['closed'] == 'on';
  if(!$closed || isset($_POST['class-list'])) {
    $course = $user->hostCourse($_POST['name'], time(), time(), !$closed);

    if($closed) {
      $split = preg_split('/$\R?^/m', $_POST['class-list']);
      foreach($split as $line) {
        $course->invite($line);
      }
    }

    header("Location: http://www.google.com/"); // TODO: change to class page
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

    <title>Codium | Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="bootstrap/css/dashboard.css" rel="stylesheet">
    <link href="dashboard-extra.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript">
      function checkOpen() {
        if(document.getElementById("closed").checked) {
          document.getElementById("students").style.display = "block";
        } else {
          document.getElementById("students").style.display = "none";
        }
      }
    </script>
        <style type = "text/css">
          .navbar
          {
            background-color: green;
          }
      .navbar a
      {
        color:white !important;
      }
      .navbar a:hover
      {
        color:#7D7D7D !important;
      }
      </style>
  </head>

  <body>


    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Codium</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="#">Profile</a></li>
            <li><a href="#">Help</a></li>
            </ul>
        </div>
      </div>
    </div>

        
        
    <h1 class="page-header">Dashboard</h1>

          <h3 class="sub-header">Start a class!</h3>
          <div id="start-class">
            <form method="post" action="#">
              <input type="text" class="form-control" placeholder="Class name" id="name" name="name" />
              <input type="checkbox" id="closed" name="closed" onchange="checkOpen()" value="on" />
              <label for="closed">Limit those who can enroll?</label>
              <div id="students">
                <p align="center">Please enter the email address of each student on a separate line.</p>
                <textarea id="class-list" style = "margin-top:5px; text-align:center;" name="class-list"> </textarea>
              </div>
              <input type="submit" class="btn btn-md btn-success" value="Start Class" />
            </form>
          </div>

          <h3 class="sub-header">Classes I'm hosting</h3>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Class Name</th>
                  <th>Enrolled</th>
                  <th>Class List</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $courses = $user->hosting();
                  if(count($courses) == 0) {
                    echo "<tr>";
                    echo '<td colspan="3">You are not hosting any courses :(</td>';
                    echo "</tr>";
                  } else {
                    foreach($courses as $course) {
                      echo "<tr>";
                      echo "<td>" . $course->name . "</td>";
                      echo "<td>" . count($course->enrolled()) . "</td>";
                      echo '<td><a href="class-list.php?id=' . $course->id . '" class="btn btn-md btn-success">Get Class List</a></td>';
                      echo "</tr>";
                    }
                  }
                ?>
              </tbody>
            </table>
          </div>
          <h3 class="sub-header">Classes I'm enrolled in </h3>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Class Name</th>
                  <th>Enrolled</th>
                  <th>Class List</th>
                </tr>
              </thead>
              <tbody>
                  <?php
                  $courses = $user->enrolled();
                  if(count($courses) == 0) {
                    echo "<tr>";
                    echo '<td colspan="3">You are not hosting any courses :(</td>';
                    echo "</tr>";
                  } else {
                    foreach($courses as $course) {
                      echo "<tr>";
                      echo "<td>" . $course->name . "</td>";
                      echo "<td>" . count($course->enrolled()) . "</td>";
                      echo '<td><a href="class-list.php?id=' . $course->id . '" class="btn btn-md btn-success">Get Class List</a></td>';
                      echo "</tr>";
                    }
                  }
                ?>
                </tr>
              </tbody>
            </table>
          </div>
  </body>
</html>
