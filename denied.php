<?php

if(!isset($_GET['id'])) {
	// whatever, no time
	die();
}

?>
<html>
	<head>
		<meta charset = "utf-8">
		<link rel = "stylesheet" href = "bootstrap/css/bootstrap.css">
		<link rel = "stylesheet" href = "denied.css">
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700' rel='stylesheet' type='text/css'>

	</head>
	<body>
		<h1 id = "main-heading"> Uh-oh! </h1>
		<div id = "uhoh" align = "center">
			<img src = "uhoh.png">
			<p style = "white-space:nowrap;"> You don't have permission to access this class! </p>
			
			<a class="btn btn-lg btn-danger" onclick="alert('You know, you should really use the phone more often. Give them a call. They\'d like to hear from you.')">Click here to request access</a>
		</div>
	</body>
</html>