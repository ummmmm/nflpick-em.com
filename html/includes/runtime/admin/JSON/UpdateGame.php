<?php

class JSON_UpdateGame extends JSONAdminAction
{
	public function execute()
	{
		$db_games	= $this->db()->games();
		$game_id	= $this->input()->value_int( 'game_id' );
		$scored 	= $this->input()->value_bool( 'scored' );

		if ( !$db_games->Load( $game_id, $loaded_game ) )
		{
			throw new NFLPickEmException( 'Game does not exist' );
		}

		if ( $scored === true )
		{
			return $this->_Update_Scores( $user, $loaded_game );
		}

		return $this->_Update_Games( $user, $loaded_game );
	}

	private function _Update_Scores( &$user, &$game )
	{
		$db_games	= $this->db()->games();
		$db_teams	= $this->db()->teams();
		$awayScore	= $this->input()->value_int( 'awayScore' );
		$homeScore	= $this->input()->value_int( 'homeScore' );

		if ( $awayScore < 0 || $homeScore < 0 )
		{
			throw new NFLPickEmException( 'Invalid game score' );
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

		$db_games->Update( $game );
		$db_teams->Recalculate_Records();
		Functions::Update_Records( $this->db() );

		$db_games->Load( $game[ 'id' ], $game );

		return $this->setData( $game );
	}

	private function _Update_Games( &$user, &$game )
	{
		$db_games	= $this->db()->games();
		$db_teams	= $this->db()->teams();
		$db_weeks	= $this->db()->weeks();
		$week_id	= $this->input()->value_int( 'week' );
		$away_id	= $this->input()->value_int( 'away' );
		$home_id	= $this->input()->value_int( 'home' );
		$month		= $this->input()->value_int( 'month' );
		$day		= $this->input()->value_int( 'day' );
		$year		= $this->input()->value_int( 'year' );
		$hour		= $this->input()->value_int( 'hour' );
		$minute		= $this->input()->value_int( 'minute' );

		$mktime = mktime( $hour, $minute, 0, $month, $day, $year );

		if ( !checkdate( $month, $day, $year ) || $mktime === false || $hour > 23 || $minute > 59 )
		{
			throw new NFLPickEmException( 'Invalid game date' );
		}

		if ( !$db_teams->Load( $away_id, $loaded_away ) )
		{
			throw new NFLPickEmException( 'Away team does not exist' );
		}

		if ( !$db_teams->Load( $home_id, $loaded_home ) )
		{
			throw new NFLPickEmException( 'Home team does not exist' );
		}

		if ( !$db_weeks->Load( $week_id, $loaded_week ) )
		{
			throw new NFLPickEmException( 'Week does not exist' );
		}

		$date = new DateTime( date( 'Y-m-d H:i:s', $mktime ) );

		$game[ 'week' ] = $week_id;
		$game[ 'away' ] = $away_id;
		$game[ 'home' ] = $home_id;
		$game[ 'date' ] = $date->getTimestamp();

		$db_games->Update( $game );
		$this->_Picks_Update_Week( $game[ 'id' ], $week_id );
		$db_games->Load( $game[ 'id' ], $game );

		return $this->setData( $game );
	}

	private function _Picks_Update_Week( $game_id, $week )
	{
		$this->db()->query( 'UPDATE picks SET week = ? WHERE game_id = ?', $week, $game_id );
	}
}
