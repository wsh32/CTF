<?php
if(session_id() == '' || !isset($_SESSION))
{
    session_start();
}
if( !isset( $_SESSION['authorized'] ) )
{
	$_SESSION['authorized'] = 0;
}
else
{
	if( $_POST['key'] === 'cams2016' )
	{
		$_SESSION['authorized'] = 1;
	}
}
if( $_SESSION['authorized'] === 1 ) {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
		<title>ADMIN AREA</title>

		<!-- CSS  -->
		<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
		<link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
		<link href="favicon.png" rel="shortcut icon"/>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	</head>

	<body>
		<header>
			<nav class="blue" role="navigation">
				<div class="container">
					<div class="nav-wrapper"><a id="logo-container" href="index.html" class="brand-logo hide-on-med-and-down">CAMS Math Competition</a>
						<ul class="right hide-on-small-only">
							<li><a href="index.html">Home</a></li>
							<li><a href="scoreboard.html">Scoreboard</a></li>
							<li><a href="questions.html">Questions</a></li>
							<li><a href="account.html">Account</a></li>
						</ul>
						<ul id="slide-out" class="side-nav">
							<li class="logo"><a id="logo-container" href="/" class="brand-logo">
								<object id="front-page-logo" type="image/png"></object></a></li>
							<li><a href="index.html">Home</a></li>
							<li><a href="scoreboard.html">Scoreboard</a></li>
							<li><a href="questions.html">Questions</a></li>
							<li><a href="account.html">Account</a></li>
						</ul>
						<a href="#" data-activates="slide-out" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
					</div>
				</div>
			</nav>
		</header>
		
		<main>
			<div class="no-pad-bot section" id="index-banner">
				<div class="container">
					<h1 class="header center blue-text text-darken-2">ADMIN AREA</h1>
				</div>
			</div>
			
			<div class="container">
				<a class="btn btn-large waves-effect waves-light blue" onclick="reset()">Get ready for competition!</a><br><br>
				<a class="btn btn-large waves-effect waves-light green" onclick="sprintStart()">Start the SPRINT Round!</a><br><br>
				<a class="btn btn-large waves-effect waves-light red" onclick="sprintEnd()">End the SPRINT Round!</a><br><br>
				<a class="btn btn-large waves-effect waves-light green" onclick="targetStart()">Start the TARGET Round!</a><br><br>
				<a class="btn btn-large waves-effect waves-light red" onclick="targetEnd()">End the TARGET Round!</a>
			</div>
			
			<script>
				var pass="568881308990110724214147374";
				
				function reset()	{
					send(6);
				}
				
				function sprintStart()	{
					send(1);
				}
				
				function sprintEnd()	{
					send(2);
				}
				
				function targetStart()	{
					send(3);
				}
				
				function targetEnd()	{
					send(4);
				}
				
				function send(val)	{
					var fd = new FormData();
					fd.append( 'passwd' , pass );
					fd.append( 'val' , val );

					$.ajax({
						type: 'POST',
						url: 'cron.php',
						data: fd,
						processData: false,
						contentType: false,
						success: function(data) {
							Materialize.toast(data, '1000');
						}
					});
				}
			</script>
		</main>

		<footer class="grey darken-2 page-footer">
			<div class="container">
				<div class="row">
					<div class="col l6 s12">
						<h5 class="white-text">CAMS Math Competition</h5>
						<p class="grey-text text-lighten-4">An online math competition hosted by CAMS Math Club</p>
					</div>
					<div class="col l3 s12">
						<h5 class="white-text">Pages</h5>
						<ul>
							<li><a class="white-text" href="index.html">Home</a></li>
							<li><a class="white-text" href="scoreboard.html">Scoreboard</a></li>
							<li><a class="white-text" href="questions.html">Questions</a></li>
							<li><a class="white-text" href="account.html">Account</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="footer-copyright">
				<div class="container">
					&copy; CAMS Math Club | Made with <a class="grey-text text-lighten-2" target="_blank" href="http://materializecss.com/">MaterializeCSS</a>
					<a class="grey-text text-lighten-2 right" target="_blank" href="LICENSE">MIT License</a>
				</div>
			</div>
		</footer>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="js/materialize.min.js"></script>
		<script src="js/ctf.js"></script>
		<script src="scripts/challenges.js"></script>
	</body>

</html>
<?php
} else {
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>ADMIN AREA</title>
		<link href="css/prism.css" rel="stylesheet" />
	</head>
	
	<h1>Private Area</h1>
	
	<figure>
	<form action="#" method="post">
		Password: <input type="password" name="key" formenctype="multipart/form-data" autofocus>
		<input type="submit" value="Submit">
	</form>
	</figure>
</html>
<?php
}
?>