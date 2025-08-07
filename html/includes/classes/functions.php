<?php

class Draw
{
	public static function Hidden( $name, $value )
	{
		print '<input type="hidden" id="' . htmlentities( $name ) . '" name="' . htmlentities( $name ) . '" value="' . htmlentities( $value ) . '" />';
	}

	public static function Radio( $name, $value, $default, $prompt = '' )
	{
		if ( $value === $default )
		{
			print '<input type="radio" name="' . htmlentities( $name ) . '" value="' . htmlentities( $value ) . '" checked /> '. $prompt;
		} else {
			print '<input type="radio" name="' . htmlentities( $name ) . '" value="' . htmlentities( $value ) . '" /> '. $prompt;
		}
	}

	public static function Option( $value, $default, $text )
	{
		$output = '<option value="' . htmlentities( $value ) . '"';
		if ( $value == $default )
		{
			$output .= ' selected';
		}

		print $output . '>' . $text . '</option>';
	}

	public static function Months( $default = null )
	{
		for( $i = 1; $i <= 12; $i++ )
		{
			Draw::Option( $i, $default, date( 'F', mktime( 0, 0, 0, $i ) ) );
		}
	}

	public static function Days( $default = null )
	{
		for( $i = 1; $i <= 31; $i++ )
		{
			Draw::Option( $i, $default, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
		}
	}

	public static function Years( $default = null )
	{
		$year = ( int ) date( 'Y' );

		for( $i = $year; $i <= $year + 1; $i++ )
		{
			Draw::Option( $i, $default, $i );
		}
	}

	public static function Hours( $default = null )
	{
		for( $i = 0; $i <= 23; $i++ )
		{
			Draw::Option( $i, $default, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
		}
	}

	public static function Minutes( $default = null )
	{
		for( $i = 0; $i <= 59; $i++ )
		{
			Draw::Option( $i, $default, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
		}
	}

	public static function Teams( $teams, $default = null )
	{
		foreach( $teams as $team )
		{
			Draw::Option( $team[ 'id' ], $default, $team[ 'team' ] );
		}
	}

	public static function Weeks( $default = null )
	{
		for( $i = 1; $i <= 18; $i++ )
		{
			Draw::Option( $i, $default, $i );
		}
	}
}

class Functions
{
	public static function Random( $length )
	{
		$string 	= '';
		$charset 	= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
		$count 		= strlen( $charset );

		while( $length-- )
		{
			$string .= $charset[ mt_rand( 0, $count - 1 ) ];
		}

		return $string;
	}

	public static function Place( $number )
	{
		$mod10	= $number % 10;
		$mod100 = $number % 100;

		if ( $mod100 >= 11 && $mod100 <= 13 )
		{
			$s = "th";
		}
		else if ( $mod10 === 1 )
		{
			$s = "st";
		}
		else if ( $mod10 === 2 )
		{
			$s = "nd";
		}
		else if ( $mod10 === 3 )
		{
			$s = "rd";
		} else {
			$s = "th";
		}

		return "{$number}<sup>{$s}</sup>";
	}

	public static function Post_Int( $value )
	{
		return isset( $_POST[ $value ] ) ? (int)$_POST[ $value ] : 0;
	}

	public static function Post_Boolean( $value )
	{
		if ( isset( $_POST[ $value ] ) )
		{
			if ( $_POST[ $value ] === 'true' )
			{
				return true;
			}
		}

		return false;
	}

	public static function Post_Active( $value )
	{
		if ( isset( $_POST[ $value ] ) )
		{
			if ( $_POST[ $value ] )
			{
				return 1;
			}
		}

		return 0;
	}

	public static function Post_Array( $value )
	{
		return isset( $_POST[ $value ] ) ? array_map( 'trim', $_POST[ $value ] ) : array();
	}

	public static function Get( $value )
	{
		return isset( $_GET[ $value ] ) ? trim( $_GET[ $value ] ) : '';
	}

	public static function Post( $value )
	{
		return isset( $_POST[ $value ] ) ? trim( $_POST[ $value ] ) : '';
	}

	public static function Cookie( $value )
	{
		return isset( $_COOKIE[ $value ] ) ? trim( $_COOKIE[ $value ] ) : '';
	}

	public static function Information( $h1, $p )
	{
		printf( "<h1>%s</h1>\n", $h1 );
		printf( "<p>%s</p>\n", $p );

		return true;
	}

	public static function FormatDate( $unix )
	{
		$date = new DateTime();
		$date->setTimestamp( $unix );
		$date->setTimezone( new DateTimezone( 'America/Los_Angeles' ) );

		return $date->format( 'm/d/y' ) . ' at '. $date->format( 'h:i a' );
	}

	public static function TimeUntil( $time )
	{
		$now	 	= new DateTime();
		$then		= new DateTime();
		$then->setTimestamp( $time );
		$interval	= $now->diff( $then );

		return sprintf( '%d days %d hours %d minutes %d seconds', $interval->days, $interval->h, $interval->i, $interval->s );
	}

	public static function VerifyPassword( $plaintext, $hashed )
	{
		if ( crypt( $plaintext, $hashed ) !== $hashed )
		{
			return false;
		}

		return true;
	}

	public static function HashPassword( $password )
	{
		return crypt( $password, '$6$rounds=10000$' . Functions::GenerateSalt() . '$' );
	}

	public static function GenerateSalt()
	{
		$hex_salt		= '';
		$binary_salt 	= openssl_random_pseudo_bytes( 16 );

		for( $i = 0; $i < 16; $i++ )
		{
			$hex_salt .= bin2hex( substr( $binary_salt, $i, 1 ) );
		}

		return strtoupper( $hex_salt );
	}

	public static function PrintR( $data )
	{
		print '<pre>';
		print_r( $data );
		print '</pre>';
	}

	public static function Timestamp()
	{
		$date = new DateTime();

		return $date->format( 'Y-m-d H:i:s' );
	}

	public static function Strip_Nulls( $string )
	{
		$string_ns = "";

		for ( $i = 0; $i < strlen( $string ); $i++ )
		{
			$char = substr( $string, $i, 1 );

			if ( ord( $char ) != 0 )
			{
				$string_ns .= $char;
			}
		}

		return $string_ns;
	}

	public static function Get_Config_Section( $file, $section_name, &$section )
	{
		$file 		= Functions::Strip_Nulls( $file );
		$settings	= @parse_ini_file( $file, true );

		if ( !$settings )
		{
			return false;
		}

		if ( !array_key_exists( $section_name, $settings ) )
		{
			return false;
		}

		$section = $settings[ $section_name ];

		return true;
	}

	public static function Update_Records( DatabaseManager $db_manager )
	{
		return Functions::Update_Weekly_Records( $db_manager ) && Functions::Update_User_Records( $db_manager );
	}

	public static function Update_Weekly_Records( DatabaseManager &$db_manager )
	{
		return $db_manager->connection()->query( 'UPDATE
													weekly_records wr
												  SET
													wr.wins		= ( SELECT COUNT( g.id ) FROM games g, picks p WHERE g.id = p.game_id AND p.user_id = wr.user_id AND p.week = wr.week_id AND g.final = 1 AND g.winner = p.winner_pick ),
													wr.losses	= ( SELECT COUNT( g.id ) FROM games g, picks p WHERE g.id = p.game_id AND p.user_id = wr.user_id AND p.week = wr.week_id AND g.final = 1 AND g.winner = p.loser_pick ),
													wr.ties		= ( SELECT COUNT( g.id ) FROM games g, picks p WHERE g.id = p.game_id AND p.user_id = wr.user_id AND p.week = wr.week_id AND g.final = 1 AND g.tied = 1 )
												  WHERE
													wr.manual	= 0' );
	}

	public static function Update_User_Records( DatabaseManager $db_manager )
	{
		$db_users = $db_manager->users();

		if ( !$db_manager->connection()->query( 'UPDATE
													users u
												 SET
													u.wins		= ( SELECT SUM( wr.wins ) FROM weekly_records wr WHERE wr.user_id = u.id ),
													u.losses	= ( SELECT SUM( wr.losses ) FROM weekly_records wr WHERE wr.user_id = u.id )' ) )
		{
			return false;
		}

		if ( !$db_users->List_Load( $users ) )
		{
			return false;
		}

		foreach ( $users as &$user )
		{
			if ( !$db_manager->connection()->single( 'SELECT COUNT( id ) + 1 AS place FROM users WHERE wins > ?', $current, $user[ 'wins' ] ) )
			{
				return false;
			}

			if ( !$db_manager->connection()->query( 'UPDATE users SET current_place = ? WHERE id = ?', $current[ 'place' ], $user[ 'id' ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public static function Turnstile_Active( &$settings )
	{
		return $settings[ 'turnstile_sitekey' ] != '' && $settings[ 'turnstile_secretkey' ] != '';
	}

	public static function Turnstile_Validate( &$settings, $turnstile )
	{
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify" );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( array( "secret" => $settings[ 'turnstile_secretkey' ], "response" => $turnstile, "ip" => $_SERVER[ "REMOTE_ADDR" ] ) ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		$response = json_decode( curl_exec( $ch ), true );

		curl_close( $ch );

		return $response[ 'success' ];
	}
}
