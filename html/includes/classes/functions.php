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
			printf( '<span class="error_text_top">The Following %s Ocurred!</span><br />', htmlentities( $title ) );
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

	public static function Error( $code, $message )
	{
		global $error_code;
		global $error_message;

		$error_code 	= $code;
		$error_message 	= $message;

		return false;
	}

	public static function ValidationError( $errors )
	{
		global $error_validation;

		$error_validation = $errors;

		return false;
	}

	public static function EmailExists( $db, $email )
	{
		$count = $db->single( 'SELECT id FROM users WHERE email = ?', $null, $email );

		if ( $count === 1 )
		{
			return true;
		}

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
		print "<h1>{$h1}</h1>";
		print "<p>{$p}</p>";

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

	public static function TopNavigation( &$db, &$user )
	{
		$db_weeks = new Weeks( $db );

		if ( !$user->logged_in )
		{
			print '<span>Welcome, Guest. Please login to start making your picks for week ' . htmlentities( $db_weeks->Current() ) . '.</span>';
		} else {
			print '<span>Welcome, ' . htmlentities( $user->account[ 'name' ] ) . '! You have ' . htmlentities( $user->account[ 'wins' ] ) . ' wins and ' . htmlentities( $user->account[ 'losses' ] ) . ' losses and currently in ' . Functions::Place( $user->account[ 'current_place' ] ) . ' place.</span>';
		}
	}

	public static function UserNavigation( &$db, &$user )
	{
		$db_weeks = new Weeks( $db );

		if ( $user->logged_in )
		{
			$admin 	= ( $user->account[ 'admin' ] ) ? '<li><a href="?view=admin" title="Admin Control Panel">Admin Control Panel</a></li>' : '';
			$weekid = $db_weeks->Previous();

			print <<<EOT
				<h1>User Links</h1>
				<ul>
				  <li><a href="" title="Home">Home Page</a></li>
				  <li><a href="?module=controlpanel" title="User Control Panel">User Control Panel</a></li>
				  {$admin}
				  <li><a href="?module=makepicks" title="Make Picks">Make Picks</a></li>
				  <li><a href="?module=viewpicks&week={$weekid}" title="User Picks">View Other's Picks</a></li>
				  <li><a href="?module=weeklyrecords" title="Weekly User Records">View Weekly Records</a></li>
				  <li><a href="?module=leaderboard" title="Leader Board">View Leader Board</a></li>
				  <li><a href="?module=logout" title="Logout" id="logout">Logout</a></li>
				</ul>
EOT;
		}
	}

	public static function ValidateScreen( &$db, &$user, &$extra_screen_content )
	{
		$view	= Functions::Get( 'view' );
		$module = Functions::Get( 'module' );

		if ( empty( $module ) )
		{
			$module = 'index';
		}

		if ( $user->logged_in )
		{
			$action = Functions::Get( 'action' );

			if ( $user->account[ 'force_password' ] && ( $module != 'forgotpassword' || ( $module == 'forgotpassword' &&  $action != 'changepassword' ) ) )
			{
				header( sprintf( 'location: %s?module=forgotpassword&action=changepassword', INDEX ) );
				return false;
			}
		}

		if ( !Validation::Filename( $module ) )
		{
			return Functions::Error( 'NFL-FUNCTIONS-1', 'Invalid module name' );
		}

		if ( $view === 'admin' && $user->logged_in && $user->account[ 'admin' ] )
		{
			$path = "includes/runtime/admin/modules/{$module}.php";
		} else {
			$path = "includes/runtime/non-admin/modules/{$module}.php";
		}

		if ( !file_exists( $path ) )
		{
			return Functions::Error( 'NFL-FUNCTIONS-2', "Module '{$module}' does not exist." );
		}

		ob_start();
		require_once( $path );
		$extra_screen_content = ob_get_contents();
		ob_clean();

		if ( !function_exists( 'Module_Content' ) )
		{
			return Functions::Error( 'NFL-FUNCTIONS-3', "The module '{$module}' is missing the Module_Content function." );
		}

		$validation = array();
		$action 	= Functions::Post( 'action' );

		if ( $action === 'update' )
		{
			if ( !function_exists( 'Module_Validate' ) )
			{
				return Functions::Error( 'NFL-FUNCTIONS-4', "The module '{$module}' is missing the Module_Validate function." );
			}

			if ( !function_exists( 'Module_Update' ) )
			{
				return Functions::Error( 'NFL-FUNCTIONS-5', "The module '{$module}' is missing the Module_Update function." );
			}

			if ( !Module_Validate( $db, $user, $validation ) )
			{
				return true;
			}

			if ( !Module_Update( $db, $user, $validation ) )
			{
				return false;
			}
		}

		return true;
	}

	public static function PrintR( $data )
	{
		print '<pre>';
		print_r( $data );
		print '</pre>';
	}

	public static function Worst_Record( &$db, $week_id, &$record )
	{
		return $db->single( 'SELECT
								MIN( (
										SELECT
											NULLIF( COUNT( p.id ), 0 )
										FROM
											picks p,
											games g
										WHERE
											p.game_id 		= g.id 		AND
											p.winner_pick 	= g.winner 	AND
											p.user_id 		= u.id 		AND
											g.week 			= ? ) ) AS wins,
								MAX( (
										SELECT
											COUNT( p.id )
										FROM
											picks p,
											games g
										WHERE
											p.game_id 		= g.id 		AND
											p.winner_pick 	= g.loser 	AND
											p.user_id 		= u.id		AND
											g.week 			= ? ) ) AS losses
						     FROM
								users u', $record, $week_id, $week_id );
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
}
