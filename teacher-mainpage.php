<?php

require_once "Mobile_Detect.php";
if((new Mobile_Detect)->isMobile()) {
  echo "We noticed that you are using a mobile device! Codium does not work on mobile devices. If you would like to learn how to code or teach others how to code, please visit us on your desktop computer.";
  die();
}

if(!isset($_GET['id'])) {
    header("Location: index.html");
    die();
}

if(!isset($_COOKIE['hash'])) {
  header("Location: log-in.php?error=You%20must%20be%20logged%20in%20to%20see%20this%20page.&redirect=mainpage.php?id=" . $_GET['id']);
  die();
}

require_once "db-api/all.php";

$user = getUserByHash($_COOKIE['hash']);
if($user == NULL) {
  header("Location: log-in.php?error=Your%20session%20has%20expired.");
  die();
}

$course = getCourse($_GET['id']);
if($course == NULL) {
    header("Location: index.html");
    die();
}

if(!$course->isEnrolled($user) && !$course->isInvited($user)) {
    header("Location: denied.php?id=" . $course->id);
    die();
}

?>
<html>
	<head>
		<meta charset = "utf-8">
		<!--BOOTSTRAP & FONTS-->
		<link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:400,300,500' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700' rel='stylesheet' type='text/css'>
	    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
	    <link href="bootstrap/css/dashboard.css" rel="stylesheet">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	    <!--FIREPAD-->
		 <script src="https://cdn.firebase.com/v0/firebase.js"></script>
  		<script src="https://cdn.firebase.com/v0/firebase-simple-login.js"></script>

	    <link rel="stylesheet" href="firepad/firepad.css">
      <link rel="stylesheet" href="firechat/firechat-default.css">
      <link rel="stylesheet" href="codemirror/lib/codemirror.css" />
      <!--OPENTOK-->
    <script src="http://static.opentok.com/webrtc/v2.0/js/TB.min.js" ></script>
      <script src="codemirror/lib/codemirror.js"></script>
        <?php
            $modes = array('clojure', 'cobol', 'commonlisp', 'css', 'd', 'erlang', 'go', 'groovy', 'haskell', 'javascript', 'lua', 'octave', 'pascal', 'perl', 'php', 'python', 'r', 'ruby', 'scheme', 'smalltalk', 'sql');
            foreach($modes as $mode) {
                echo '<script src="codemirror/mode/' . $mode . '/' . $mode . '.js"></script>';
            }
        ?>
      <script src="firepad/firepad.js"></script>
      <script src="firechat/firechat-default.js"></script>
		<!--CUSTOM PAGE CSS -->
	    <link href = "mainpage.css" rel = "stylesheet">
	    <link href = "firechat-overload.css" rel = "stylesheet">
      <script type="text/javascript">
        function refreshSyntaxColors() {
            var l = document.getElementById("Language").value;
            firepad.codeMirror_.setOption("mode", l);
            var myRootRef = new Firebase(<?php echo "'https://codium.firebaseio.com/" . $course->getFirebaseIDFor($user) . "/lang'"; ?>);
            myRootRef.set(l);
        }

        var cpad = null;

        function view(id) {
          //document.getElementsByClassName("firepad")[0].removeChild(document.getElementsByClassName("CodeMirror")[0]);
          document.getElementById("firepad").removeChild(document.getElementsByClassName("firepad")[0]);
          //cpad.dispose();

          var firepadRef = new Firebase('http://codium.firebaseio.com/doc-' + <?php echo "'" . $course->id . "'"; ?> + '-' + id);
          var codeMirror = CodeMirror(document.getElementById('firepad'), { lineNumbers: true/*, mode: document.getElementById('Language').value*/});
          var firepad = Firepad.fromCodeMirror(firepadRef, codeMirror);
          cpad = firepad;

          refreshSyntaxColors();

          //document.getElementById("Language").value = 
          firepadRef.on('value', function(snapshot) {
            alert('lang is ' + snapshot.val());
          });
        }
      </script>
	</head>
	<body>
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation" style = "background-color:#5cb85c;">
      <div class="container-fluid">
        <div  class="navbar-header">
          <a class="navbar-brand"  href="dashboard.php">Codium</a>
     	</div>
        <div class="navbar-collapse collapse">
           <ul class="nav navbar-nav navbar-right">
             <li><?php echo $user->getName(); ?></li>
             <li><a href="log-out.php">Log Out</a></li>
             </ul>
         </div>
      </div>
    </div>

    </div>
		<h1 class = "page-header" style = "text-align:center;">Welcome to <?php echo $course->name; ?></h1>
		<div id = "tokbox"> </div>

        <div id="col-left">
            <h3 id="write-some-code">Write some code.</h3>
    		<form action="" id="language-form">
    				<select name="Language" id="Language">
    					<option value="javascript">JavaScript</option>
    					<option value="CSS">CSS</option>
    					<option value="HTML">HTML</option>
    					<option value="PHP">PHP</option>
    				</select>
    		</form>
            <div id="firepad"></div>
            <h4 id = "enter-student-name"> Select a student to view his/her code. </h4>
          <table>
            <?php
              foreach($course->enrolled() as $student) {
                echo '<tr><td><a class="btn btn-md btn-default" onclick="view(' . $student->id . ')">' . $student->getName() . '</a> </td></tr>';
              }
            ?>
          </table>
        </div>

        <div id="col-right">
            <h3 id="write-some-code">Chat with your classmates.</h3>
    		    <div id="firechat-wrapper"> </div>
            <div id = "firebase-student"></div>
        </div>
    <script type="text/javascript">
    function searchStudent()
    {
	    var key=e.keyCode || e.which;
		if (key==13){
		form.submit(); //call search function
		}
		
	}
    </script>
 	<script type='text/javascript'>
		       var chatRef = new Firebase('https://firechat-demo.firebaseio.com');
    var chat = new FirechatUI(chatRef, document.getElementById("firechat-wrapper"));
    var simpleLogin = new FirebaseSimpleLogin(chatRef, function(err, user) {
      if (user) {
        chat.setUser(user.id, 'Anonymous' + user.id.substr(0, 8));
        setTimeout(function() {
          chat._chat.enterRoom('-Iy1N3xs4kN8iALHV0QA');
        }, 500);
      } else {
        simpleLogin.login('anonymous');    
      }
    });
  	</script>
    <script type = "text/javascript">
    //THIS IS THE FIRPAD FOR A REGULAR USER
      var firepadRef = new Firebase(<?php echo "'http://codium.firebaseio.com/" . $course->getFirebaseIDFor($user) . "'";?>);
      var codeMirror = CodeMirror(document.getElementById('firepad'), { lineNumbers: true, mode: document.getElementById('Language').value});
      var firepad = Firepad.fromCodeMirror(firepadRef, codeMirror);
      cpad = firepad;
     
    </script>
    <script type="text/javascript">
			
			  var apiKey    = "44698282";
			  var sessionId = "1_MX40NDY5ODI4Mn5-U2F0IE1hciAyMiAxNDozMToxMSBQRFQgMjAxNH4wLjU3MDgyNDR-";
			  var token     = "T1==cGFydG5lcl9pZD00NDY5ODI4MiZzZGtfdmVyc2lvbj10YnJ1YnktdGJyYi12MC45MS4yMDExLTAyLTE3JnNpZz1jMWMxMDU3MDYyZjk4OTdmOWRmMzViMmQxY2FlYjE4ZjU4ZDA4MjBhOnJvbGU9cHVibGlzaGVyJnNlc3Npb25faWQ9MV9NWDQwTkRZNU9ESTRNbjUtVTJGMElFMWhjaUF5TWlBeE5Eb3pNVG94TVNCUVJGUWdNakF4Tkg0d0xqVTNNRGd5TkRSLSZjcmVhdGVfdGltZT0xMzk1NTI2MjQxJm5vbmNlPTAuMzQ0MTkyNjUwMzExOTg2ODcmZXhwaXJlX3RpbWU9MTM5NTUyOTgyMCZjb25uZWN0aW9uX2RhdGE9"; //TODO generate from PHP script (token & session id)
			 
			  function sessionConnectedHandler (event) {
			  	//TODO wrap this code in "if is teacher"
			     session.publish(publisher); 
			     subscribeToStreams(event.streams);
			  }
			  function subscribeToStreams(streams) {
			    for (var i = 0; i < streams.length; i++) {
			        var stream = streams[i];
			        if (stream.connection.connectionId
			               != session.connection.connectionId) {
			        	//TODO wrap this code in if isn't teacher
			            session.subscribe(stream, {width:700, height:700});
			        }
			    }
			  }
			  function streamCreatedHandler(event) {
			    subscribeToStreams(event.streams);
			  }
			 
			  var publisher = TB.initPublisher(apiKey,  "tokbox");
			  var session   = TB.initSession(sessionId);
			 
			  session.connect(apiKey, token);
			  session.addEventListener("sessionConnected",
			                           sessionConnectedHandler);
			 
			  session.addEventListener("streamCreated",
			                           streamCreatedHandler);

		</script>
		 
	</body>
</html>
