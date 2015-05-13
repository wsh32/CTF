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
	//if( isset( $_SESSION['teamid'] ) && ( $_SESSION['teamid'] !== false ) && ( (int) $_SESSION['teamid'] > 0 ) )
	//{
		return true;
	//}
	//else
	//{
	//	return false;
	//}
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
		$xml_team->addChild( 'score', $answer['score'] );
	}
	$result->close();
	return $xml;
}

// Latest solves
function get_solves( $id )
{
	$database = Database::getConnection();
	
	$limit = 20;
	if( $id < 1 )
	{ // First call
		$limit = 1;
	}
	$result = $database->query
	(
		"SELECT
			solves.id,
			teams.name,
			challenges.score
		FROM
			solves
		JOIN
			teams
		ON
			solves.team = teams.id
		JOIN
			challenges
		ON
			solves.challenge = challenges.id
		WHERE
			solves.id > '" . $database->real_escape_string( $id ) . "'
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
		$xml_attack = $xml->addChild( 'attack' );
		$xml_attack->addChild( 'id', $answer['id'] );
		$xml_attack->addChild( 'teamname', htmlspecialchars( $answer['name'] ));
		$xml_attack->addChild( 'challenge', $answer['challenge'] );
	}
	return $xml;
}


// Challenge browser
function get_challenges()
{
	$database = Database::getConnection();
	
	$result = $database->query
		(
			"SELECT
				challenges.id,
				challenges.title,
				challenges.score,
				challenges.hidden
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
		}
	return $xml;
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
	$xml->addChild( 'description', htmlentities( $answer['description'] ) );
	return $xml;
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

// Attack
function submit_key( $key, $id, $teamname, $token )
{
	$database = Database::getConnection();
	
	$team = get_teamid($teamname);
	
	if( $team == "notarealteam" )	{
		exit();
	}
	
	$hash = $key;
	
	$result = $database->query
	(
		"SELECT
			challenges.id,
			challenges.title,
			challenges.score,
			challenges.deactivated,
			challenges.hidden,
			(
				SELECT
					COUNT( solves.id )
				FROM
					solves
				WHERE
					solves.challenge = challenges.id
					AND solves.team = '" . $database->real_escape_string( $team ) . "'
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
			AND challenges.hidden != 1 AND challenges.id = '" . $id ."'"
	);
	
	if(!$result)	{
		die("MySQL Syntax Error");
	}

	$answer = array();
	if( mysqli_num_rows( $result ) === 1 )
	{
		$data = mysqli_fetch_assoc( $result );
		
		$title = $data['title'];
		
		if( $data['already_solved'] == '1' )
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
				$additional = 5;
			}
			else if( $data['number_solved_all'] === '1' )
			{
				$additional = 3;
			}
			else if( $data['number_solved_all'] === '2' )
			{
				$additional = 1;
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
		usleep(10000); //Discourages brute forcing.
		$_SESSION['failcount'] += 1;
		
		$answer['code'] = 3;
		$answer['text'] = "Wrong, try again";
	}
	$xml = new SimpleXMLElement( '<solve></solve>' );
	
	if( !empty( $answer['text'] ) )
	{
		$xml->addChild( 'text', $answer['text'] );
	}
	
	$xml->addChild( 'code', $answer['code'] );
	
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
		$hash = $password;
		
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
			$_SESSION['failcount'] = 0;
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
	}
	else
	{
		$answer['code'] = 2;
	}
	
	$_SESSION['teamid'] = false;
	return $answer['code'];
}

// Create Account
function create_account( $team_name, $password, $repeat, $token )
{
	global $disable_create;
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
			teams.name = '" . $database->real_escape_string( $team_name ) . "'
		UNION SELECT
			temp.name
		FROM
			temp
		WHERE 
			temp.name = '" . $database->real_escape_string( $team_name ) . "'"
	);
	
	if( mysqli_num_rows( $duplicate_name ) != 0 )
	{
		$answer['code'] = 2;
		$answer['text'] = 'Another team beat you to this name';
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
	else if( preg_match( '/\s/', $team_name ) )
	{
		$answer['code'] = 3;
		$answer['text'] = 'Spaces are not allowed in the team name. Thank you.';
	}
	else if( empty( $password ) )
	{
		$answer['code'] = 3;
		$answer['text'] = 'Not adding a password is dangerous. If you cannot think of one, try Tantium.';
	}
	else if( $password != $repeat )
	{
		$answer['code'] = 3;
		$answer['text'] = 'If you cannot enter in your password twice...you should get a better one. Consider using Tantium.';
	}
	else if( preg_match('/[\#\&\'\"]/', $team_name)) 
	{
		$answer['code'] = 3;
		$answer['text'] = "DON'T ACT LIKE YOU DON'T KNOW THAT YOU HAVE ENTERED DANGEROUS AND ILLEGAL CHARACTERS ON PURPOSE TO DESTROY THIS WEBSITE!";
	}
	else
	{
		$name = $database->real_escape_string( $team_name );
		
		$hash = hash( 'sha512', $password );
		$key = hash( 'ripemd160', sha1( $_SESSION['token'] . microtime() . mt_rand() / mt_getrandmax() ) );
		
		$change = $database->query
		(
			"INSERT INTO temp
				(`name`, `password`, `key`)
			VALUES
				('$name', '$hash', '$key')"
		);
		
		if( !$change )
		{
			die( 'MySQL: Syntax error' );
		}
		
		$answer['code'] = 1;
		$answer['text'] = 'The account has been created successfully.';
	}
	$xml = new SimpleXMLElement( '<info></info>' );
	$xml->addChild( 'code', $answer['code'] );
	$xml->addChild( 'text', $answer['text'] );
	
	return $xml;
}

?>