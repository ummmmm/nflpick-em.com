<?php

class JSON_MakePicks implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'user' => true, 'token' => true );
	}

	public function execute()
	{
		$db_games	= new Games( $this->_db );
		$db_picks	= new Picks( $this->_db );
		$db_teams	= new Teams( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$date_now 	= new DateTime();

		$week 		= Functions::Post( 'week' );
		$gameid 	= Functions::Post( 'gameid' );
		$winner		= Functions::Post( 'winner' );
		$loser		= Functions::Post( 'loser' );

		$count_week = $db_weeks->Load( $week, $loaded_week );
	
		if ( $count_week === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count_week === 0 )
		{
			return $this->_json->setError( array( '#Error#', sprintf( "Week '%d' could not be loaded", $week ) ) );
		}
		
		$date_week = new DateTime();
		$date_week->setTimestamp( $loaded_week[ 'date' ] );

		if ( $loaded_week[ 'locked' ] === 1 || $date_now > $date_week )
		{
			return $this->_json->setError( array( "#Error#", "This week has already been locked.  You can no longer make picks." ) );
		}
		
		if ( !$db_games->Load( $gameid, $game ) )
		{
			return $this->_json->setError( array( "#Error#", "Game not found" ) );
		}
		
		if ( !$db_games->Exists( $gameid, $week, $winner, $loser ) )
		{
			return $this->_json->setError( array( "#Error#", "Invalid game data" ) );
		}
		
		$date_start = new DateTime();
		$date_start->setTimestamp( $game[ 'date' ] );
		
		if ( $date_now > $date_start )
		{
			return $this->_json->setError( array( "#Error#", "This game has already started and can no longer be updated." ) );
		}
		
		if ( !$db_teams->Load( $winner, $winning_team ) || !$db_teams->Load( $loser, $losing_team ) )
		{
			return $this->_json->setError( array( "#Error#", "Failed to load teams" ) );
		}
		
		$count_pick = $db_picks->Load_User_Game( $this->_auth->getUserID(), $gameid, $pick );
		
		if ( $count_pick === false )
		{
			return $this->_setError( $db_picks->Get_Error() );
		}

		$pick[ 'game_id' ]		= $gameid;
		$pick[ 'winner_pick' ] 	= $winner;
		$pick[ 'loser_pick' ]	= $loser;
		$pick[ 'picked' ]		= 1;
		
		if ( !$db_picks->Update( $pick ) )
		{
			return $this->_json->DB_Error();
		}
		
		$remaining = $db_picks->Remaining( $this->_auth->getUserID(), $week );

		return $this->_json->setData( array( 'remaining' => $remaining, 'message' => 'You have picked the <b>' . $winning_team[ 'team' ] . '</b> to beat the <b>' . $losing_team[ 'team'] . '</b>' ) );
	}
}
