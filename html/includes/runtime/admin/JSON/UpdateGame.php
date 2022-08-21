<?php

class JSON_UpdateGame extends JSONAdminAction
{
	public function execute()
	{
		$db_games	= new Games( $this->_db );
		$game_id	= Functions::Post_Int( 'game_id' );
		$scored 	= Functions::Post_Boolean( 'scored' );

		if ( !$db_games->Load( $game_id, $loaded_game ) )
		{
			return $this->setError( array( 'NFL-GAMES_UPDATE-6', 'Failed to load game' ) );
		}

		if ( $scored === true )
		{
			return $this->_Update_Scores( $this->_db, $user, $loaded_game );
		}

		return $this->_Update_Games( $this->_db, $user, $loaded_game );
	}

	private function _Update_Scores( &$db, &$user, &$game )
	{
		$db_games	= new Games( $db );
		$db_teams	= new Teams( $db );
		$awayScore	= Functions::Post_Int( 'awayScore' );
		$homeScore	= Functions::Post_Int( 'homeScore' );

		if ( $awayScore < 0 || $homeScore < 0 )
		{
			return $this->setError( array( 'NFL-GAMES_UPDATE-0', 'Invalid game score' ) );
		}

		if ( $homeScore == $awayScore )
		{
			$game[ 'tied' ]		= 1;
			$game[ 'winner' ]	= 0;
			$game[ 'loser' ]	= 0;
		}
		else
		{
			$game[ 'tied' ]		= 0;
			$game[ 'winner' ]	= $homeScore > $awayScore ? $game[ 'home' ] : $game[ 'away' ];
			$game[ 'loser' ]	= $homeScore > $awayScore ? $game[ 'away' ] : $game[ 'home' ];
		}

		$game[ 'homeScore' ]	= $homeScore;
		$game[ 'awayScore' ] 	= $awayScore;
		$game[ 'final' ]		= 1;

		if ( !$db_games->Update( $game ) )
		{
			return $this->setDBError();
		}

		if ( !$db_teams->Recalculate_Records() )
		{
			return $this->setDBError();
		}

		if ( !Functions::Update_Records( $this->_db ) )
		{
			return $this->setError( 'Failed to update weekly / user records' );
		}

		if ( !$db_games->Load( $game[ 'id' ], $game ) )
		{
			return $this->setDBError();
		}

		return $this->setData( $game );
	}

	private function _Update_Games( &$db, &$user, &$game )
	{
		$db_games	= new Games( $db );
		$db_teams	= new Teams( $db );
		$db_weeks	= new Weeks( $db );
		$week_id	= Functions::Post_Int( 'week' );
		$away_id	= Functions::Post_Int( 'away' );
		$home_id	= Functions::Post_Int( 'home' );
		$month		= Functions::Post_Int( 'month' );
		$day		= Functions::Post_Int( 'day' );
		$year		= Functions::Post_Int( 'year' );
		$hour		= Functions::Post_Int( 'hour' );
		$minute		= Functions::Post_Int( 'minute' );

		$mktime = mktime( $hour, $minute, 0, $month, $day, $year );

		if ( !checkdate( $month, $day, $year ) || $mktime === false || $hour > 23 || $minute > 59 )
		{
			return $this->setError( array( 'NFL-GAMES_UPDATE-1', 'Invalid game date' ) );
		}

		$team_count = $db_teams->Load( $away_id, $loaded_away );

		if ( $team_count === false )
		{
			return $this->setDBError();
		}

		if ( $team_count === 0 )
		{
			return $this->setError( array( 'NFL-GAMES_UPDATE-2', 'Failed to load the away team' ) );
		}

		$team_count = $db_teams->Load( $home_id, $loaded_home );

		if ( $team_count === false )
		{
			return $this->setDBError();
		}

		if ( $team_count === 0 )
		{
			return $this->setError( array( 'NFL-GAMES_UPDATE-3', 'Failed to load the home team' ) );
		}

		$week_count = $db_weeks->Load( $week_id, $loaded_week );

		if ( $week_count === false )
		{
			return $this->setDBError();
		}

		if ( $week_count === 0 )
		{
			return $this->setError( array( 'NFL-GAMES_UPDATE-4', 'Failed to load week' ) );
		}

		$date = new DateTime( date( 'Y-m-d H:i:s', $mktime ) );

		$game[ 'week' ] = $week_id;
		$game[ 'away' ] = $away_id;
		$game[ 'home' ] = $home_id;
		$game[ 'date' ] = $date->getTimestamp();

		if ( !$db_games->Update( $game ) )
		{
			return $this->setDBError();
		}

		if ( !$this->_Picks_Update_Week( $db, $game[ 'id' ], $week_id ) )
		{
			return $this->setDBError();
		}

		if ( !$db_games->Load( $game[ 'id' ], $game ) )
		{
			return $this->setDBError();
		}

		return $this->setData( $game );
	}

	private function _Picks_Update_Week( &$db, $game_id, $week )
	{
		if ( !$db->query( 'UPDATE picks SET week = ? WHERE game_id = ?', $week, $game_id ) )
		{
			return false;
		}

		return true;
	}
}
