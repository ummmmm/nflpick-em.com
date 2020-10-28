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
		for( $i = 1; $i <= 17; $i++ )
		{
			Draw::Option( $i, $default, $i );
		}
	}
}

class Functions
{
	public static function Module_Updated( $message )
	{
		global $updated_message;

		$updated_message = $message;

		return true;
	}

	public static function HandleModuleUpdate()
	{
		global $updated_message;

		if ( !is_null( $updated_message ) && is_string( $updated_message ) && trim( $updated_message ) != '' )
		{
			print "<p><b>{$updated_message}</b></p>";
			unset( $updated_message );
		}

		return true;
	}

	public static function HandleModuleErrors()
	{
		global $error_validation;

		if ( !is_array( $error_validation ) )
		{
			return false;
		}

		$count = count( $error_validation );

		if ( $count > 0 )
		{
			$output 	= '';
			$message	= '';
			$title 		= ( $count === 1 ) ? 'Error Has' : $count . ' Errors Have';

			foreach( $error_validation as $error )
			{
				$message .= "- {$error}<br />";
			}

			print '<div class="error">';
			printf( '<span class="error_text_top">The Following %s Occurred!</span><br />', htmlentities( $title ) );
			printf( '<span class="error_text">%s</span>', $message );
			print '</div>';

			unset( $error_validation );
		}

		return true;
	}

	public static function OutputError()
	{
		global $error_code;
		global $error_message;

		$error_code 	= ( is_null( $error_code ) ) ? 'NFL-FUNCTIONS-0' : $error_code;
		$error_message 	= ( is_null( $error_message ) ) ? 'An unknown error has occurred.' : $error_message;
		print '<h1>An error has occurred</h1>';
		print '<div>Error Code: ' . htmlentities( $error_code ) . '</div>';
		print '<div>Error Message: ' . htmlentities( $error_message ) . '</div>';

		return true;
	}

	public static function ValidationError( $errors )
	{
		global $error_validation;

		$error_validation = $errors;

		return false;
	}

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

	public static function Trim_Boolean( $value )
	{
		return $value ? 1 : 0;
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

		return $interval->format( '%d days %h hours %i minutes %s seconds' );
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

	public static function Worst_Record( &$db, $week_id, &$record )
	{
		$db_picks	= new Picks( $db );
		$db_games	= new Games( $db );
		$db_users	= new Users( $db );
		$game_count	= $db_games->List_Load_Week( $week_id, $games );
		$wins		= 0;
		$losses		= 0;

		if ( !$game_count )
		{
			return false;
		}

		if ( !$db_users->List_Load( $users ) )
		{
			return false;
		}

		foreach ( $users as $user )
		{
			$user_wins 			= 0;
			$user_losses 		= 0;
			$user_pick_count	= $db_picks->List_Load_User_Week_Picked( $user[ 'id' ], $week_id, $picks );

			if ( $game_count != $user_pick_count )
			{
				continue;
			}

			foreach ( $games as $game )
			{
				if ( $game[ 'winner' ] == 0 )
				{
					continue;
				}

				foreach ( $picks as $pick )
				{
					if ( $game[ 'id' ] != $pick[ 'game_id' ] )
					{
						continue;
					}

					if ( $pick[ 'winner_pick' ] == $game[ 'winner' ] )
					{
						$user_wins++;
					}
					else
					{
						$user_losses++;
					}
				}
			}

			if ( $user_losses > $losses )
			{
				$wins	= $user_wins;
				$losses = $user_losses;
			}
		}

		$record = array( 'wins' => $wins, 'losses' => $losses );

		return true;
	}

	public static function Worst_Record_Calculated( &$db, $week_id, &$record )
	{
		if ( Functions::Worst_Record( $db, $week_id, $record ) === false )
		{
			return false;
		}

		$record[ 'total' ]	= $record[ 'wins' ] + $record[ 'losses' ];

		if ( $record[ 'wins' ] < 2 )
		{
			$record[ 'wins' ] 	= 0;
			$record[ 'losses' ] = $record[ 'total' ];
		}
		else
		{
			$record[ 'wins' ] 	-= 2;
			$record[ 'losses' ] += 2;
		}

		return true;
	}

	public static function Timestamp()
	{
		$date = new DateTime();

		return $date->format( 'Y-m-d H:i:s' );
	}

	public static function Fix_User_Records( &$db, &$users )
	{
		$db_picks		= new Picks( $db );
		$db_users		= new Users( $db );
		$db_weeks		= new Weeks( $db );
		$weeks_count 	= $db_weeks->List_Load( $weeks );

		if ( $weeks_count === false )
		{
			return false;
		}

		foreach( $weeks as $week )
		{
			if ( $week[ 'locked' ] !== 1 )
			{
				continue;
			}

			$total_games = $db_weeks->Total_Games( $week[ 'id' ] );

			if ( $total_games === false )
			{
				return false;
			}

			foreach( $users as &$user )
			{
				$missing_count = $db_picks->Missing( $user[ 'id' ], $week[ 'id' ] );

				if ( $missing_count === false )
				{
					return false;
				}

				if ( $missing_count === 0 )
				{
					continue;
				}

				if ( $missing_count !== $total_games )
				{
					$user[ 'losses' ] += $missing_count;
				}
				else
				{
					if ( !Functions::Worst_Record_Calculated( $db, $week[ 'id' ], $worst_record ) )
					{
						return false;
					}

					if ( $worst_record[ 'total' ] !== $total_games )
					{
						continue;
					}

					$user[ 'wins' ] 	+= $worst_record[ 'wins' ];
					$user[ 'losses' ] 	+= $worst_record[ 'losses' ];
				}

				if ( !$db_users->Update_Record( $user[ 'id' ], $user[ 'wins' ], $user[ 'losses' ] ) )
				{
					return false;
				}
			}
		}

		return true;
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
}
