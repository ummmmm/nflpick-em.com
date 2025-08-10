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

		$user_id			= $this->input()->value_int( 'user_id' );
		$week_id			= $this->input()->value_int( 'week_id' );
		$wins				= $this->input()->value_int( 'wins' );
		$losses				= $this->input()->value_int( 'losses' );

		if ( !$db_users->Load( $user_id, $user ) )												throw new NFLPickEmException( 'User does not exist' );
		else if ( !$db_weeks->Load( $week_id, $week ) )											throw new NFLPickEmException( 'Week does not exist' );
		else if ( !$week[ 'locked' ] )															throw new NFLPickEmException( 'Week is not locked' );
		else if ( !$db_weekly_records->Load_User_Week( $user_id, $week_id, $weekly_record ) )	throw new NFLPickEmException( 'Weekly record does not exist' );

		$missing_count = $db_picks->Missing( $user_id, $week_id );

		if ( $missing_count === 0 )
		{
			throw new NFLPickEmException( 'User is not missing any picks' );
		}

		if ( !$db_games->List_Load_Week( $week_id, $games ) )
		{
			throw new NFLPickEmException( 'Games do not exist' );
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
			throw new NFLPickEmException( 'Weekly records cannot be updated until all games are final' );
		}

		$this->_load_min_wins_user_week( $user_id, $week_id, $min_wins );

		if ( $wins < $min_wins )													throw new NFLPickEmException( sprintf( 'User cannot have less than %d wins', $min_wins ) );
		else if ( $wins < 0 || $losses < 0 )										throw new NFLPickEmException( 'Wins / Losses cannot be less than 0' );
		else if ( ( $wins + $losses ) != ( count( $games ) - $tied_game_count ) )	throw new NFLPickEmException( sprintf( 'Wins + Losses must equal the number of non-tied games in the week (%d)', ( count( $games ) - $tied_game_count ) ) );

		$weekly_record[ 'wins' ]	= $wins;
		$weekly_record[ 'losses' ]	= $losses;
		$weekly_record[ 'ties' ]	= $tied_game_count;
		$weekly_record[ 'manual' ]	= 1;

		$db_weekly_records->Update( $weekly_record );

		Functions::Update_User_Records( $this->db() );

		return true;
	}

	private function _load_min_wins_user_week( $user_id, $week_id, &$min_wins )
	{
		$this->db()->single( 'SELECT
								COUNT( p.id ) AS wins
							  FROM
								games g,
								picks p
							  WHERE
								g.final			= 1			AND
								p.game_id		= g.id		AND
								p.winner_pick	= g.winner	AND
								p.user_id 		= ?			AND
								p.week			= ?', $results, $user_id, $week_id );

		$min_wins = $results[ 'wins' ];
	}
}
