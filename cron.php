<?php

include( 'init.php' );

$passwd = "568881308990110724214147374";

if( !isset( $_POST['passwd'] ) || !isset( $_POST['val'] ) || $_POST['passwd'] != $passwd ) 
{
	die("Get out. You are not supposed to be here.");
}

$database = Database::getConnection();

$name = "state";
$result = $database->query
(
	"SELECT
		var.value
	FROM
		var
	WHERE
		var.name='$name'"
);

if(!$result){
	die("MySQL: Syntax Error");
}

$data = mysqli_fetch_assoc( $result );
$value = $_POST['val'];
if	($value == 6)	{
	$change = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 1,
			challenges.locked = 1"
	);
	
	$change2 = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 0,
			challenges.locked = 0
		WHERE
			challenges.group = 9001"
	);
}	else if($value == 1)	{
	$change = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 1,
			challenges.locked = 1"
	);
	
	$change2 = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 0,
			challenges.locked = 0
		WHERE
			challenges.group = 1"
	);
}	else if($value == 2)	{
	$change = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 1,
			challenges.locked = 1"
	);
	
	$change2 = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 0,
			challenges.locked = 0
		WHERE
			challenges.group = 9001"
	);
}	else if($value == 3)	{
	$change = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 1,
			challenges.locked = 1"
	);
	
	$change2 = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 0,
			challenges.locked = 0
		WHERE
			challenges.group = 2"
	);
}	else if($value == 4)	{
	$change = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 1,
			challenges.locked = 1"
	);
	
	$change2 = $database->query
	(
		"UPDATE
			challenges
		SET
			challenges.hidden = 0,
			challenges.locked = 0
		WHERE
			challenges.group = 9001"
	);
}

if(!$change)	{
	die("MySQL Syntax Error!");
}

if(!$change2)	{
	die("MySQL Syntax Error!!");
}

$val_new = $value;
$cron_update = $database->query
(
	"UPDATE
		var
	SET
		var.value = $val_new
	WHERE
		var.name = '$name'"
);

if(!$cron_update)	{
	die("MySQL Syntax Error");
}

echo $value;
?>
