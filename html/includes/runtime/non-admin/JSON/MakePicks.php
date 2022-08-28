<?php

class JSON_MakePicks extends JSONUserAction
{
	public function execute()
	{
		$db_games	= new Games( $this->_db );
		$db_picks	= new Picks( $this->_db );
		$db_teams	= new Teams( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$date_now 	= time();

		$gameid 	= Functions::Post( 'gameid' );
		$winner		= Functions::Post( 'winner' );
		$loser		= Functions::Post( 'loser' );

		if ( !$db_games->Load( $gameid, $game ) )
		{
			return $this->setError( array( "#Error#", "Game not found" ) );
		}

		if ( !$db_weeks->Load( $game[ 'week' ], $week ) )
		{
			return $this->setError( array( '#Error#', 'Week not found' ) );
		}

		if ( $week[ 'locked' ] === 1 || $date_now > $week[ 'date' ] )
		{
			return $this->setError( array( "#Error#", "This week has already been locked.  You can no longer make picks." ) );
		}

		if ( $date_now > $game[ 'date' ] )
		{
			return $this->setError( array( "#Error#", "This game has already started and can no longer be updated." ) );
		}

		if ( !$db_teams->Load( $winner, $winning_team ) || !$db_teams->Load( $loser, $losing_team ) )
		{
			return $this->setError( array( "#Error#", "Failed to load teams" ) );
		}

		$count_pick = $db_picks->Load_User_Game( $this->_auth->getUserID(), $gameid, $pick );

		if ( $count_pick === false )
		{
			return $this->setDBError();
		}

		$pick[ 'user_id' ]		= $this->_auth->getUserID();
		$pick[ 'game_id' ]		= $gameid;
		$pick[ 'winner_pick' ] 	= $winner;
		$pick[ 'loser_pick' ]	= $loser;
		$pick[ 'week' ]			= $game[ 'week' ];

		if ( $count_pick === 0 )
		{
			if ( !$db_picks->Insert( $pick ) )
			{
				return $this->setDBError();
			}
		}
		else
		{
			if ( !$db_picks->Update( $pick ) )
			{
				return $this->setDBError();
			}
		}

		$remaining = $db_picks->Remaining( $this->_auth->getUserID(), $week[ 'id' ] );

		return $this->setData( array( 'remaining' => $remaining, 'message' => 'You have picked the <b>' . $winning_team[ 'team' ] . '</b> to beat the <b>' . $losing_team[ 'team' ] . '</b>' ) );
	}
}
