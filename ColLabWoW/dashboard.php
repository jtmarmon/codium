<?php

require_once "Mobile_Detect.php";
if((new Mobile_Detect)->isMobile()) {
  echo "We noticed that you are using a mobile device! Codium does not work on mobile devices. If you would like to learn how to code or teach others how to code, please visit us on your desktop computer.";
  die();
}

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

    header("Location: " . $course->getURL());
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

      var open = '';

      function show(id) {
        open = id;
        document.getElementById(id).style.display = "block";
        document.getElementById('pop-up-bg').style.display = "block";
      }

      function hide() {
        document.getElementById(open).style.display = "none";
        document.getElementById('pop-up-bg').style.display = "none";
      }
    </script>
    <script type="text/javascript">
    function updateURL()
    {
      document.getElementById('customURL').innerHTML = document.getElementById('customURLInput');
    }
    </script>
        <style type = "text/css">
          .navbar
          {
            background-color: #5cb85c;
            border-color: #4cae4c;
          }
      .navbar a
      {
        color: #FFF !important;
      }
      .navbar a:hover
      {
        color: #DBDBDB !important;
      }
      </style>

  </head>

  <body>
    <div id="pop-up-bg" onclick="hide()"></div>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Codium</a>
        </div>
        <div class="navbar-collapse collapse">
           <ul class="nav navbar-nav navbar-right">
             <li><?php echo $user->getName(); ?></li>
             <li><a href="log-out.php">Log Out</a></li>
             </ul>
         </div>
      </div>
    </div>

    <?php

      $hosting = $user->hosting();
      foreach($hosting as $course) {
        echo '<div class="pop-up" id="host-' . $course->id . '">';
          echo '<a class="pop-up-close" onclick="hide()"><span>x</span></a>';
          echo '<div class="pop-up-text">';
            echo '<table class="table table-striped pop-table">';
              echo '<thead>';
                echo '<tr>';
                  echo '<th>Name</th>';
                  echo '<th>Email</th>';
                echo '</tr>';
              echo '</thead>';
              echo '<tbody>';
                $students = $course->invited();
                if(count($students) == 0) {
                  echo '<tr>';
                    echo '<td colspan="2">No students enrolled or invited :(</td>';
                  echo '</tr>';
                } else {
                  foreach($students as $student) {
                    $studentu = getUserByEmail($student);
                    echo '<tr>';
                    if($studentu == NULL) {
                      echo '<td class="no-accept" title="This user has not yet accepted your invitation.">Unknown</td>';
                      echo '<td class="no-accept" title="This user has not yet accepted your invitation.">' . $student . '</td>';
                    } else {
                      echo '<td>' . $studentu->fname . ' ' . $studentu->lname . '</td>';
                      echo '<td>' . $studentu->email . '</td>';
                    }
                    echo '</tr>';
                  }
                }
              echo '</tbody>';
            echo '</table>';
          echo '</div>';
        echo '</div>';
      }

      $enrolled = $user->enrolled();
      foreach($enrolled as $course) {
        echo '<div class="pop-up" id="enroll-' . $course->id . '">';
          echo '<div class="pop-up-text">';
            echo '<table class="table table-striped pop-table">';
              echo '<thead>';
                echo '<tr>';
                  echo '<th>Name</th>';
                  echo '<th>Email</th>';
                echo '</tr>';
              echo '</thead>';
              echo '<tbody>';
                $students = $course->enrolled();
                if(count($students) == 0) {
                  echo '<tr>';
                    echo '<td colspan="2">No students enrolled :(</td>';
                  echo '</tr>';
                } else {
                  foreach($students as $student) {
                    echo '<tr>';
                      echo '<td>' . $student->fname . ' ' . $student->lname . '</td>';
                      echo '<td>' . $student->email . '</td>';
                    echo '</tr>';
                  }
                }
              echo '</tbody>';
            echo '</table>';
          echo '</div>';
        echo '</div>';
      }

    ?>
        
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
              <input type="text" id = "customURLInput" style = "display:inline; width:300px;" class="form-control-nospace" onkeyup = "updateURL()" placeholder="Custom URL (Optional)">
              <h6 id = "customURL"> www.codium.com/ </h6>
              <input type="submit" class="btn btn-md btn-success" value="Start Class" />
            </form>
          </div>

          <h3 class="sub-header">Classes I'm hosting</h3>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th width="60%">Class Name</th>
                  <th width="20%">Enrolled</th>
                  <th width="20%">Class List</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  if(count($hosting) == 0) {
                    echo '<tr id="nothing">';
                    echo '<td colspan="3">You are not hosting any courses :(</td>';
                    echo "</tr>";
                  } else {
                    foreach($hosting as $course) {
                      $meta = ' onclick="window.location=\'' . $course->getURL() . '\'"';
                      echo '<tr>';
                      echo '<td' . $meta . '>' . $course->name . "</td>";
                      echo '<td' . $meta . '>' . count($course->enrolled()) . "</td>";
                      echo '<td><a class="btn btn-md btn-success" onclick="show(\'host-' . $course->id . '\')">Show Class List</a></td>';
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
                  <th width="40%">Class Name</th>
                  <th width="20%">Teacher</th>
                  <th width="20%">Enrolled</th>
                  <th width="20%">Class List</th>
                </tr>
              </thead>
              <tbody>
                  <?php
                  if(count($enrolled) == 0) {
                    echo '<tr id="nothing">';
                    echo '<td colspan="4">You are not enrolled in any courses :(</td>';
                    echo "</tr>";
                  } else {
                    foreach($enrolled as $course) {
                      $meta = ' onclick="window.location=\'' . $course->getURL() . '\'"';
                      echo '<tr>';
                      echo '<td' . $meta . '>' . $course->name . "</td>";
                      echo '<td' . $meta . '>' . $course->owner->fname . " " . $course->owner->lname . "</td>";
                      echo '<td' . $meta . '>' . count($course->enrolled()) . "</td>";
                      echo '<td><a class="btn btn-md btn-success" onclick="show(\'enroll-' . $course->id . '\')">Show Class List</a></td>';
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
