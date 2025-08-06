<?php

class JSON_MakePicks extends JSONUserAction
{
	public function execute()
	{
		$db_games	= $this->db()->games();
		$db_picks	= $this->db()->picks();
		$db_teams	= $this->db()->teams();
		$db_weeks	= $this->db()->weeks();
		$date_now 	= time();

		$gameid 	= Functions::Post( 'gameid' );
		$winner		= Functions::Post( 'winner' );
		$loser		= Functions::Post( 'loser' );

		if ( !$db_games->Load( $gameid, $game ) )															throw new NFLPickEmException( 'Game does not exist' );
		else if ( !$db_weeks->Load( $game[ 'week' ], $week ) )												throw new NFLPickEmException( 'Week does not exist' );
		else if ( $week[ 'locked' ] === 1 || $date_now > $week[ 'date' ] )									throw new NFLPickEmException( 'This week has already been locked.  You can no longer make picks.' );
		else if ( $date_now > $game[ 'date' ] )																throw new NFLPickEmException( 'This game has already started and can no longer be updated.' );
		else if ( !$db_teams->Load( $winner, $winning_team ) || !$db_teams->Load( $loser, $losing_team ) )	throw new NFLPickEmException( 'Away / home team does not exist' );

		$pick_exists = $db_picks->Load_User_Game( $this->auth()->getUserID(), $gameid, $pick );

		$pick[ 'user_id' ]		= $this->auth()->getUserID();
		$pick[ 'game_id' ]		= $gameid;
		$pick[ 'winner_pick' ] 	= $winner;
		$pick[ 'loser_pick' ]	= $loser;
		$pick[ 'week' ]			= $game[ 'week' ];

		if ( !$pick_exists )	$db_picks->Insert( $pick );
		else					$db_picks->Update( $pick );

		$remaining = $db_picks->Remaining( $this->auth()->getUserID(), $week[ 'id' ] );

		return $this->setData( array( 'remaining' => $remaining, 'message' => sprintf( 'You have picked the <b>%s</b> to beat the <b>%s</b>', $winning_team[ 'team' ], $losing_team[ 'team' ] ) ) );
	}
}
