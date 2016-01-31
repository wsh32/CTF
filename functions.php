<?php

if( !defined( 'INITIALIZED' ) ) #Not needed
{ // No direct call, needs config
	exit();
}

function output_xml( $xml ) #Not needed
{
	header( 'Content-Type: text/xml' );
	exit( $xml->asXML() );
}

function valid_token( $token ) #Not needed
{
	return ( $_SESSION['token'] === $token );
}

function is_loggedin()
{
	if( isset( $_SESSION['teamid'] ) && ( $_SESSION['teamid'] !== false ) && ( (int) $_SESSION['teamid'] > 0 ) )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_session()
{
	$answer = array();
	if( is_loggedin() )
	{
		$answer['loggedin'] = 1;
	}
	else
	{
		$answer['loggedin'] = 0;
	}
	$xml = new SimpleXMLElement( '<status></status>' );
	$xml->addChild( 'loggedin', $answer['loggedin'] );
	$xml->addChild( 'token', $_SESSION['token'] );
	$xml->addChild( 'teamname', get_teamname( $_SESSION['teamid'] ) );
	return $xml;
}

function get_ranking()
{
	$database = Database::getConnection();
	
	$result = $database->query
	(
		"SELECT
			teams.id,
			teams.name,
			teams.school,
			teams.score
		FROM
			teams
		ORDER BY
			teams.score DESC,
			teams.name ASC"
	);
	if( !$result )
	{
		die( 'MySQL: Syntax error' );
	}
	$xml = new SimpleXMLElement( '<ranking></ranking>' );
	
	$current['rank'] = 1;
	$current['score'] = -1;
	while( $answer = mysqli_fetch_assoc( $result ) )
	{
		if( $current['score'] < 0 )
		{
			$current['score'] = $answer['score'];
		}
		if( $answer['score'] < $current['score'] )
		{
			$current['score'] = $answer['score'];
			$current['rank']++;
		}
		$xml_team = $xml->addChild( 'team' );
		$xml_team->addChild( 'id', $answer['id'] );
		$xml_team->addChild( 'rank', $current['rank'] );
		$xml_team->addChild( 'name', htmlspecialchars( $answer['name'] ));
		$xml_team->addChild( 'school', htmlspecialchars( $answer['school'] ));
		$xml_team->addChild( 'score', $answer['score'] );
	}
	$result->close();
	return $xml;
}

// Latest solves
function get_solves()
{
	$database = Database::getConnection();
	
	$limit = 4;
	$result = $database->query
	(
		"SELECT
			solves.id,
			solves.team,
			solves.challenge
		FROM
			solves
		ORDER BY
			solves.id DESC
		LIMIT " .
			(int) $limit
	);
	if( !$result )
	{
		die( 'MySQL: Syntax error' );
	}
	$xml = new SimpleXMLElement( '<solves></solves>' );
	while( $answer = mysqli_fetch_assoc( $result ) )
	{
		$xml_solve = $xml->addChild( 'solve' );
		$xml_solve->addChild( 'id', $answer['id'] );
		$xml_solve->addChild( 'teamname', htmlspecialchars( get_teamname($answer['team']) ) );
		$xml_solve->addChild( 'challenge', htmlspecialchars( get_challengename( $answer['challenge'] ) ) );
	}
	return $xml;
}


// Challenge browser
function get_challenges()
{
	$database = Database::getConnection();
	
	if(is_loggedin()){
		$team = $_SESSION['teamid'];
		$result = $database->query
			(
				"SELECT
					challenges.id,
					challenges.title,
					challenges.score,
					challenges.hidden,
					challenges.locked,
					challenges.groupid,
					(
						SELECT
							COUNT( solves.id )
						FROM
							solves
						WHERE
							solves.challenge = challenges.id
							AND solves.team = " . $database->real_escape_string( $team ) . "
					) AS already_solved
				FROM
					challenges
				WHERE
					challenges.hidden != 1
				ORDER BY
					challenges.title ASC"
			);
		if( !$result )
		{
			die( 'MySQL: Syntax error' );
		}
		$xml = new SimpleXMLElement( '<challenges></challenges>' );
		while( $answer = mysqli_fetch_assoc( $result ) )
		{
			$xml_challenge = $xml->addChild( 'challenge' );
			$xml_challenge->addChild( 'id', $answer['id'] );
			$xml_challenge->addChild( 'title', $answer['title'] );
			$xml_challenge->addChild( 'score', $answer['score'] );
			$xml_challenge->addChild( 'locked', $answer['locked'] );
			$xml_challenge->addChild( 'groupid', $answer['groupid'] );
			$xml_challenge->addChild( 'solved', $answer['already_solved'] );
		}
		return $xml;
	}	else	{
		$result = $database->query
			(
				"SELECT
					challenges.id,
					challenges.title,
					challenges.score,
					challenges.hidden,
					challenges.locked,
					challenges.groupid
				FROM
					challenges
				WHERE
					challenges.hidden != 1
				ORDER BY
					challenges.title ASC"
			);
		if( !$result )
		{
			die( 'MySQL: Syntax error' );
		}
		$xml = new SimpleXMLElement( '<challenges></challenges>' );
		while( $answer = mysqli_fetch_assoc( $result ) )
		{
			$xml_challenge = $xml->addChild( 'challenge' );
			$xml_challenge->addChild( 'id', $answer['id'] );
			$xml_challenge->addChild( 'title', $answer['title'] );
			$xml_challenge->addChild( 'score', $answer['score'] );
			$xml_challenge->addChild( 'groupid', $answer['groupid'] );
			$xml_challenge->addChild( 'locked', $answer['locked'] );
			$xml_challenge->addChild( 'solved', 0 );
		}
		return $xml;
	}
}

function get_challenge( $id )
{
	$database = Database::getConnection();
	
	$result = $database->query
	(
		"SELECT
			challenges.id,
			challenges.title,
			challenges.description,
			challenges.score,
			challenges.hidden
		FROM
			challenges
		WHERE
			challenges.id = '" . $database->real_escape_string( $id ) . "'
			AND challenges.hidden != 1"
	);
	if( !$result )
	{
		die( 'MySQL: Syntax error' );
	}
	$answer = array();
	
	if( mysqli_num_rows( $result ) === 1 )
	{
		$answer = mysqli_fetch_assoc( $result );
	}
	else
	{
		$answer = array( 'id' => 0, 'title' => '', 'description' => '', 'image' => '' ); 
	}
	$xml = new SimpleXMLElement( '<challenge></challenge>' );
	$xml->addChild( 'id', $answer['id'] );
	$xml->addChild( 'title', $answer['title'] );
	$xml->addChild( 'score', $answer['score'] );
	$xml->addChild( 'description', htmlspecialchars( $answer['description'] ) );
	return $xml;
}

function get_challengename( $id )
{
	$database = Database::getConnection();
	
	$result = $database->query
	(
		"SELECT
			challenges.id,
			challenges.title
		FROM
			challenges
		WHERE
			challenges.id = '" . $database->real_escape_string( $id ) . "'
			AND challenges.hidden != 1"
	);
	if( !$result )
	{
		die( 'MySQL: Syntax error' );
	}
	$answer = array();
	
	if( mysqli_num_rows( $result ) === 1 )
	{
		$answer = mysqli_fetch_assoc( $result );
	}
	else
	{
		$answer = array( 'id' => 0, 'title' => '', 'description' => '', 'image' => '' ); 
	}
	return $answer['title'];
}

function get_teamid( $id )	{
	$database = Database::getConnection();
	
	$result = $database->query
	(
		"SELECT
			teams.id
		FROM
			teams
		WHERE
			teams.name = '" . $database->real_escape_string( $id ) ."'"
	);
	
	if( !$result )
	{
		die( 'MySQL: Syntax error' );
	}
	
	if( mysqli_num_rows( $result ) === 1 )
	{
		$data = mysqli_fetch_assoc( $result );
		$id = $data["id"];
	}	else	{
		$id = "notarealteam";
	}
	return $id;
}

function get_teamname( $id )	{
	$database = Database::getConnection();
	
	$result = $database->query
	(
		"SELECT
			teams.name
		FROM
			teams
		WHERE
			teams.id = '" . $database->real_escape_string( $id ) ."'"
	);
	
	if( !$result )
	{
		die( 'MySQL: Syntax error' );
	}

	if( mysqli_num_rows( $result ) === 1 )
	{
		$data = mysqli_fetch_assoc( $result );
		$id = $data['name'];
	}	else	{
		$id = "notarealteam";
	}
	
	return $id;
}

// Attack
function submit_key( $key, $id, $teamname, $token )
{
	$database = Database::getConnection();
	
	$team = $_SESSION["teamid"];
	
	$answer = array();
	
	if( $team )	{

		$hash = $key;

		$result = $database->query
		(
			"SELECT
				challenges.id,
				challenges.title,
				challenges.score,
				challenges.locked,
				challenges.hidden,
				(
					SELECT
						COUNT( solves.id )
					FROM
						solves
					WHERE
						solves.challenge = challenges.id
						AND solves.team = " . $database->real_escape_string( $team ) . "
				) AS already_solved,
				(
					SELECT
						COUNT( solves.id )
					FROM
						solves
					WHERE
						solves.challenge = challenges.id
				) AS number_solved_all
			FROM
				challenges
			WHERE
				challenges.key = '" . $database->real_escape_string( $hash ) . "'
				AND challenges.hidden != 1 AND challenges.id = " . $id
		);

		if(!$result)	{
			die("MySQL Syntax error");
		}

		if( mysqli_num_rows( $result ) === 1 )
		{
			$data = mysqli_fetch_assoc( $result );
			
			$title = $data['title'];
			
			if( $data['locked'] == '1' )
			{
				$answer['code'] = 2;
				$answer['text'] = '"' . $title . '" is locked!';
			}
			else if( $data['already_solved'] == '1' )
			{
				$answer['code'] = 2;
				$answer['text'] = '"' . $title . '" has already been completed!';
			}
			else if( $data['already_solved'] == '0')
			{
				// Additional score			
				$additional = 0;
				if( $data['number_solved_all'] === '0' )
				{
					$additional = $data['score']*.1;
				}
				else if( $data['number_solved_all'] === '1' )
				{
					$additional = $data['score']*.05;
				}
				else if( $data['number_solved_all'] === '2' )
				{
					$additional = $data['score']*.02;
				}
				// Insert
				$database->query
				(
					"INSERT INTO
						solves
						(
							team,
							challenge,
							additional,
							date
						)
						VALUES
						(
							'" . $database->real_escape_string( $team ) . "',
							'" . $database->real_escape_string( $data['id'] ) . "',
							'" . $database->real_escape_string( $additional )  . "',
							NOW()
						)"
				);
				
				// Update Cached Score
				update_score( $team, $additional + $data['score'] );
				$answer['code'] = 1;
				$answer['text'] = "Correct!";
			}
		}
		else
		{
			
			$result = $database->query
			(
				"SELECT
					challenges.id,
					challenges.title,
					challenges.score,
					challenges.hidden,
					(
						SELECT
							COUNT( solves.id )
						FROM
							solves
						WHERE
							solves.challenge = challenges.id
							AND solves.team = " . $database->real_escape_string( $team ) . "
					) AS already_solved,
					(
						SELECT
							COUNT( solves.id )
						FROM
							solves
						WHERE
							solves.challenge = challenges.id
					) AS number_solved_all
				FROM
					challenges
				WHERE
					challenges.hidden != 1 AND challenges.id = " . $id
			);
			
			$data = mysqli_fetch_assoc( $result );
			$points =  $data['score']*.25*-1;
			update_score( $team, $points);
			
			$answer['code'] = 3;
			$answer['text'] = "Wrong, try again";
		}
	}	else	{
		$answer['code'] = 4;
		$answer['text'] = "Please, log in";
	}
	
	$xml = new SimpleXMLElement( '<solve></solve>' );
	
	if( !empty( $answer['text'] ) )
	{
		$xml->addChild( 'text', $answer['text'] );
	}
	
	return $xml;
}

function update_score( $id, $score )
{
	$database = Database::getConnection();
	$id = (int) $database->real_escape_string( $id );
	$score = (int) $database->real_escape_string( $score );
	
	$result = $database->query
	(
		"UPDATE
			teams
		SET
			teams.score = teams.score + '$score'
		WHERE
			teams.id = '$id'"
	);
}

function login( $team, $password, $token ) 
{
	$database = Database::getConnection();
	$answer = array();
	if( is_loggedin() )
	{
		$answer['code'] = 2;
		$answer['text'] = 'You are already logged in!';
	}
	else
	{
		$hash = hash('sha512',$password);
		
		$result = $database->query
		(
			"SELECT
				teams.id
			FROM
				teams
			WHERE
				BINARY teams.name = '" . $database->real_escape_string( $team ) . "'
				AND teams.password = '" . $database->real_escape_string( $hash ) . "'"
		);
		
		if( !$result )
		{
			die( 'MySQL: Syntax error' );
		}
		else if( mysqli_num_rows( $result ) === 1 )
		{
			$answer['code'] = 1;
			$data = mysqli_fetch_assoc( $result );
			$_SESSION['teamid'] = (int) $data['id'];
			$answer['text'] = 'You have been logged in';
		}
		else
		{
			$answer['code'] = 3;
			$answer['text'] = 'Incorrect username and/or password.';
			$_SESSION['teamid'] = false;
		}
	}
	$xml = new SimpleXMLElement( '<login></login>' );
	$xml->addChild( 'code', $answer['code'] );
	$xml->addChild( 'text', $answer['text'] );
	return $xml;
}

function logout( $token )  
{
	if( !valid_token( $token ) )
	{
		session_unset();
		session_destroy(); 
		session_start();
		$_SESSION['token'] = hash( 'ripemd160', sha1( uniqid( '', true ) ) );
		exit();
	}
	$answer = array();
	if( is_loggedin() )
	{
		$answer['code'] = 1;
		$answer['text'] = 'You have been logged out';
	}
	else
	{
		$answer['code'] = 2;
		$answer['text'] = 'You are not logged in!';
	}
	
	$_SESSION['teamid'] = false;
	$xml = new SimpleXMLElement( '<logout></logout>' );
	$xml->addChild( 'code', $answer['code'] );
	$xml->addChild( 'text', $answer['text'] );
	return $xml;
}

// Create Account
function create_account( $team_name, $password, $repeat, $email, $code, $token )
{
	$disable_create = false;
	$database = Database::getConnection();
	
	if( !valid_token( $token ) )
	{
		exit();
	}
	
	$answer = array();
	
	$duplicate_name = $database->query
	(
		"SELECT
			teams.name
		FROM 
			teams
		WHERE 
			teams.name = '" . $database->real_escape_string( $team_name ) . "'"
	);
	
	$duplicate_email = $database->query
	(
		"SELECT
			teams.email
		FROM
			teams
		WHERE
			teams.email = '" . $database->real_escape_string( $email ) . "'"
	);
	
	if( $disable_create )
	{
		$answer['code'] = 5;
		$answer['text'] = 'Account creation is temporarily disabled';
	}
	else if( mysqli_num_rows( $duplicate_name ) != 0 )
	{
		$answer['code'] = 2;
		$answer['text'] = 'Another team beat you to this name';
	}
	else if( mysqli_num_rows( $duplicate_email ) != 0 )
	{
		$answer['code'] = 2;
		$answer['text'] = 'HEY! NO DUPLICATE ACCOUNTS!';
	}
	else if( empty( $team_name ) )
	{
		$answer['code'] = 3;
		$answer['text'] = 'You must enter a team name!';
	}
	else if( strlen( $team_name ) > 15 )
	{
		$answer['code'] = 3;
		$answer['text'] = 'Your team name must be under 15 characters long!';
	}
	else if( empty( $code ) )
	{
		$answer['code'] = 3;
		$answer['text'] = 'You must enter a registration code!';
	}
	else if( strlen( $code ) != 5 )
	{
		$answer['code'] = 3;
		$answer['text'] = 'Invalid code!';
	}
	else if( $team_name == 'notarealteam' )
	{
		$answer['code'] = 3;
		$answer['text'] = 'You must be a real team!';
	}
	else if( preg_match( '/\s/', $team_name ) )
	{
		$answer['code'] = 3;
		$answer['text'] = 'Spaces are not allowed in the team name. Thank you.';
	}
	else if( empty( $password ) )
	{
		$answer['code'] = 4;
		$answer['text'] = 'Not adding a password is dangerous. I highly suggest trying one.';
	}
	else if( empty( $email ) )
	{
		$answer['code'] = 4;
		$answer['text'] = 'Not adding an email is dangerous. I highly suggest trying one.';
	}
	else if ( strlen( $password ) < 6 )
	{
		$answer['code'] = 4;
		$answer['text'] = 'Your password sucks.';
	}
	else if( $password != $repeat )
	{
		$answer['code'] = 4;
		$answer['text'] = 'If you cannot enter in your password twice...you should get a better one';
	}
	else if( preg_match('/[\#\&\'\"]/', $team_name) || preg_match('/[\#\&\'\"]/', $email) || preg_match('/[\#\&\'\"]/', $code) ) 
	{
		$answer['code'] = 3;
		$answer['text'] = "DON'T ACT LIKE YOU DON'T KNOW THAT YOU HAVE ENTERED DANGEROUS AND ILLEGAL CHARACTERS ON PURPOSE TO DESTROY THIS WEBSITE!";
	}
	else
	{
		
		$coderesult = $database->query
		(
			"SELECT
				codes.school
			FROM
				codes
			WHERE
				codes.value = '" . $database->real_escape_string( $code ) ."'"
		);
		
		if( !$coderesult )
		{
			die( 'MySQL: Syntax error' );
		}

		if( mysqli_num_rows( $coderesult ) === 1 )
		{
			$data = mysqli_fetch_assoc( $coderesult );
			$school = $data['school'];
			$ready = true;
		}	else	{
			$answer['code'] = 4;
			$answer['text'] = "Invalid code!";
			$ready = false;
		}
		
		if( $ready )	{
			$name = $database->real_escape_string( $team_name );
			$hash = hash( 'sha512', $password );
			$key = hash( 'ripemd160', sha1( $_SESSION['token'] . microtime() . mt_rand() / mt_getrandmax() ) );
			
			$change = $database->query
			(
				"INSERT INTO teams
					(`name`, `password`, `email`, `school`, `ip`)
				VALUES
					('".$name."', '".$hash."', '".$database->real_escape_string($email)."','".$school."','".$_SERVER['REMOTE_ADDR']."')"
			);
			
			if( !$change )
			{
				die( 'MySQL: Syntax error' );
			}
			
			$answer['code'] = 1;
			$answer['text'] = 'The account has been created successfully.';
		}
		
	}
	$xml = new SimpleXMLElement( '<info></info>' );
	$xml->addChild( 'code', $answer['code'] );
	$xml->addChild( 'text', $answer['text'] );
	
	return $xml;
}

function get_state()	{
	$database = Database::getConnection();
	$result = $database->query
	(
		"SELECT
			var.value
		FROM
			var
		WHERE
			var.name='state'"
	);
	if( !$result )	{
		die("MySQL: Syntax Error");
	}
	$data = mysqli_fetch_assoc( $result );
	$state = $data['value'];
	
	if ($state > 10)	{
		$state = 10;
	}
	
	$xml = new SimpleXMLElement( '<info></info>' );
	$xml->addChild( 'state', $state );
	
	return $xml;
}
?>
