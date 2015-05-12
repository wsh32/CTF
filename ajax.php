<?php
include( 'init.php' );
include( 'functions.php' );

if( !isset( $_GET['m'] ) || !is_string( $_GET['m'] ) )
{
	die("Get out. You are not supposed to be here.");
}

switch( $_GET['m'] )	{
	case 'get_ranking':
		output_xml
		(
			get_ranking()
		);
	case 'get_attacks':
		output_xml
		(
			get_attacks
			(
				5
			)
		);
	case 'get_challenges':
		output_xml
		(
			get_challenges()
		);
	case 'get_challenge':
		if( !isset( $_POST['id'] ) )
		{
			exit();
		}
		output_xml
		(
			get_challenge
			(
				$_POST['id']
			)
		);
	case 'submit_key':
		if( !isset( $_POST['key'] ) || !isset( $_POST['token'] ) || !isset( $_POST['id'] ) )
		{
			exit();
		}
		output_xml( submit_key($_POST['key'], $_POST['id'], $_POST['token']));
	case 'create_account':
		if( !isset( $_POST['team_name'] ) || !isset( $_POST['password'] ) || !isset( $_POST['repeat'] ) || !isset( $_POST['token'] ) )
		{
			exit();
		}
		output_xml
		(
			create_account
			(
				$_POST['team_name'],
				$_POST['password'],
				$_POST['repeat'],
				$_POST['token']	
			)
		);
	case 'login':
		if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) || !isset( $_POST['token'] ) )
		{
			exit();
		}
		output_xml
		(
			login
			(
				$_POST['username'],
				$_POST['password'],
				$_POST['token']
			)
		);
	case 'logout':
		if( !isset( $_POST['token'] ) )
		{
			exit();
		}
		output_xml( logout($_POST['token']));
	case 'get_session':
		output_xml
		(
			get_session()
		);
}
?>