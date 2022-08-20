<?php

class JSON_UpdateWeeklyRecords extends JSONAdminAction
{
	public function execute()
	{
		$db_users			= new Users( $this->_db );
		$db_weeks			= new Weeks( $this->_db );
		$db_games			= new Games( $this->_db );
		$db_picks			= new Picks( $this->_db );
		$db_weekly_records	= new Weekly_Records( $this->_db );

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

		if ( !Functions::Update_User_Records( $this->_db ) )
		{
			return $this->setError( array( '#Error#', 'Failed to update user records' ) );
		}

		return true;
	}
}
