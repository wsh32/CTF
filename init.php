<?php
// Definitions
define( 'DB_HOST', 'localhost' );
define( 'DB_USER', '' );
define( 'DB_PASS', '' );
define( 'DB_NAME', '' );
// Debug
ini_set( 'display_errors', '1' );
// Database
class Database
{
	private static $database;
	public static function getConnection()
	{
		if ( !self::$database )
		{
			self::$database = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
			self::$database->set_charset("utf8");
        }
		return self::$database;
    }
}
//Custom

session_start();
if( !isset( $_SESSION['token'] ) )
{
	$_SESSION['token'] = hash( 'ripemd160', sha1( uniqid( '', true ) ) );
}

if( !isset( $_SESSION['teamid'] ) )
{
	$_SESSION['teamid'] = false;
}
define( 'INITIALIZED', true );
?>
