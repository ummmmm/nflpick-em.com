<?php

class Functions
{
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

	public static function FormatDate( $unix )
	{
		$date = new DateTime();
		$date->setTimestamp( $unix );
		$date->setTimezone( new DateTimezone( 'America/Los_Angeles' ) );

		return $date->format( 'm/d/y' ) . ' at '. $date->format( 'h:i a' );
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
