<?php
class Validation
{
	public static function Filename( $filename )
	{
		if ( !preg_match( "/^[a-zA-Z_]+$/", $filename ) )
		{
			return false;
		}

		return true;
	}

	public static function Email( $email )
	{
		if ( !preg_match( "/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email ) )
		{
			return false;
		}

		return true;
	}

	public static function IsAlpha( $string )
	{
		if ( !preg_match( "/^[[:alpha:]]+$/", $string ) )
		{
			return false;
		}

		return true;
	}
}
?>
