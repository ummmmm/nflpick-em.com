<?php

class DatabaseTablePicks extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE `picks`
				(
					id 			int( 11 ) AUTO_INCREMENT,
					user_id 	int( 11 ),
					game_id 	int( 11 ),
					winner_pick int( 11 ),
					loser_pick 	int( 11 ),
					ip 			varchar( 50 ),
					updated 	int( 11 ),
					week 		int( 11 ),
					PRIMARY KEY ( id ),
					UNIQUE KEY picks_1 ( user_id, game_id ),
					KEY picks_2 ( user_id, week )
				)";

		return $this->query( $sql );
	}

	public function Insert( $pick )
	{
		$ip 		= $_SERVER[ 'REMOTE_ADDR' ];
		$updated	= time();

		return $this->query( 'INSERT INTO picks
								   ( user_id, game_id, winner_pick, loser_pick, ip, updated, week )
								   VALUES
								   ( ?, ?, ?, ?, ?, ?, ? )',
								   $pick[ 'user_id' ], $pick[ 'game_id' ], $pick[ 'winner_pick' ], $pick[ 'loser_pick' ], $ip, $updated, $pick[ 'week' ] );
	}

	public function Update( $pick )
	{
		$ip 	= $_SERVER[ 'REMOTE_ADDR' ];
		$time 	= time();

		return $this->query( 'UPDATE
									picks
							       SET
									winner_pick	= ?,
									loser_pick	= ?,
									ip			= ?,
									updated		= ?
							       WHERE
									id			= ?',
							$pick[ 'winner_pick' ], $pick[ 'loser_pick' ], $ip, $time,
							$pick[ 'id' ] );
	}

	public function Delete( $pick_id )
	{
		return $this->query( 'DELETE FROM picks WHERE id = ?', $pick_id );
	}

	public function Delete_User( $userid )
	{
		return $this->query( 'DELETE FROM picks WHERE user_id = ?', $userid );
	}

	public function Delete_Game( $gameid )
	{
		return $this->query( 'DELETE FROM picks WHERE game_id = ?', $gameid );
	}

	public function Remaining( $userid, $weekid )
	{
		$count = $this->single( 'SELECT
										COUNT( g.id ) AS remaining
								      FROM
										games g
										LEFT OUTER JOIN picks p ON p.game_id = g.id AND p.user_id = ?
									   WHERE
										g.week = ? AND
										g.date > ? AND
										p.id IS NULL',
									   $remaining, $userid, $weekid, time() );

		if ( $count === false )
		{
			return false;
		}

		return $remaining[ 'remaining' ];
	}

	public function Missing( $userid, $weekid )
	{
		$count = $this->single( 'SELECT
										COUNT( * ) AS count
									  FROM
									  	games g
									  	LEFT OUTER JOIN picks p ON p.game_id = g.id AND p.user_id = ?
									  WHERE
									  	g.week = ? AND
									  	p.id IS NULL',
									  $missing, $userid, $weekid );

		if ( $count === false )
		{
			return false;
		}

		return $missing[ 'count' ];
	}

	public function List_Load_UserWeek( $user_id, $week_id, &$picks )
	{
		return $this->select( 'SELECT
										p.*,
										wt.team AS winner,
										lt.team AS loser
									FROM
										picks p,
										games g,
										teams wt,
										teams lt
									WHERE
										p.user_id		= ?		AND
										p.week			= ?		AND
										p.game_id		= g.id	AND
										p.winner_pick	= wt.id	AND
										p.loser_pick	= lt.id
									ORDER BY
										g.date, g.id',
									$picks, $user_id, $week_id );
	}

	public function Load_User_Game( $userid, $gameid, &$pick )
	{
		return $this->single( 'SELECT * FROM picks WHERE user_id = ? AND game_id = ?', $pick, $userid, $gameid );
	}

	public function List_Load( &$picks )
	{
		return $this->select( 'SELECT * FROM picks ORDER BY id ASC', $picks );
	}
}
