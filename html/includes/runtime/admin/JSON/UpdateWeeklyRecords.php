<?php

class JSON_UpdateWeeklyRecords extends JSONAdminAction
{
	public function execute()
	{
		$db_users			= $this->db()->users();
		$db_weeks			= $this->db()->weeks();
		$db_games			= $this->db()->games();
		$db_picks			= $this->db()->picks();
		$db_weekly_records	= $this->db()->weeklyrecords();

		$user_id	= Functions::Post_Int( 'user_id' );
		$week_id	= Functions::Post_Int( 'week_id' );
		$wins		= Functions::Post_Int( 'wins' );
		$losses		= Functions::Post_Int( 'losses' );

		if ( !$db_users->Load( $user_id, $user ) )
		{
			return $this->setError( array( '#Error#', 'User does not exist' ) );
		}

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			return $this->setError( array( '#Error#', 'Week does not exist' ) );
		}

		if ( !$week[ 'locked' ] )
		{
			return $this->setError( array( '#Error#', 'Week is not locked' ) );
		}

		if ( !$db_weekly_records->Load_User_Week( $user_id, $week_id, $weekly_record ) )
		{
			return $this->setError( array( '#Error#', 'Weekly record does not exist' ) );
		}

		$missing_count = $db_picks->Missing( $user_id, $week_id );

		if ( $missing_count === false )
		{
			return $this->setDBError();
		}

		if ( $missing_count === 0 )
		{
			return $this->setError( array( '#Error#', 'User is not missing any picks' ) );
		}

		if ( !$db_games->List_Load_Week( $week_id, $games ) )
		{
			return $this->setError( array( '#Error#', 'Games do not exist' ) );
		}

		$tied_game_count	= 0;
		$games_in_progress	= false;

		foreach ( $games as &$game )
		{
			if ( !$game[ 'final' ] )
			{
				$games_in_progress = true;
				break;
			}

			if ( $game[ 'tied' ] )
			{
				$tied_game_count++;
			}
		}

		if ( $games_in_progress )
		{
			return $this->setError( array( '#Error#', 'Weekly records cannot be updated until all games are final' ) );
		}

		if ( !$this->_load_min_wins_user_week( $user_id, $week_id, $min_wins ) )
		{
			return false;
		}

		if ( $wins < $min_wins )
		{
			return $this->setError( array( '#Error#', sprintf( 'User cannot have less than %d wins', $min_wins ) ) );
		}

		if ( $wins < 0 || $losses < 0 )
		{
			return $this->setError( array( '#Error#', 'Wins / Losses cannot be less than 0' ) );
		}

		if ( ( $wins + $losses ) != ( count( $games ) - $tied_game_count ) )
		{
			return $this->setError( array( '#Error#', sprintf( 'Wins + Losses must equal the number of non-tied games in the week (%d)', ( count( $games ) - $tied_game_count ) ) ) );
		}

		$weekly_record[ 'wins' ]	= $wins;
		$weekly_record[ 'losses' ]	= $losses;
		$weekly_record[ 'ties' ]	= $tied_game_count;
		$weekly_record[ 'manual' ]	= 1;

		if ( !$db_weekly_records->Update( $weekly_record ) )
		{
			return $this->setDBError();
		}

		if ( !Functions::Update_User_Records( $this->db() ) )
		{
			return $this->setError( array( '#Error#', 'Failed to update user records' ) );
		}

		return true;
	}

	private function _load_min_wins_user_week( $user_id, $week_id, &$min_wins )
	{
		if ( !$this->db()->single( 'SELECT
									COUNT( p.id ) AS wins
								   FROM
									games g,
									picks p
								   WHERE
									g.final			= 1			AND
									p.game_id		= g.id		AND
									p.winner_pick	= g.winner	AND
									p.user_id 		= ?			AND
									p.week			= ?', $results, $user_id, $week_id ) )
		{
			return $this->setDBError();
		}

		$min_wins = $results[ 'wins' ];

		return true;
	}
}
