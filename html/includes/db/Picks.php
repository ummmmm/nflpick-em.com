<?php

class Picks
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

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
					picked 		tinyint( 1 ),
					PRIMARY KEY ( id ),
					UNIQUE KEY picks_1 ( user_id, game_id ),
					KEY picks_2 ( user_id, week )
				)";

		return $this->_db->query( $sql );
	}

	public function Insert_All( $user_id )
	{
		$ip 		= $_SERVER[ 'REMOTE_ADDR' ];
		$updated	= time();

		return $this->_db->query( 'INSERT INTO picks ( user_id, game_id, winner_pick, loser_pick, ip, updated, week, picked )
							SELECT ?, id, 0, 0, ?, ?, week, 0 FROM games', $user_id, $ip, $updated );
	}

	public function Update( $pick )
	{
		$ip 	= $_SERVER[ 'REMOTE_ADDR' ];
		$time 	= time();

		return $this->_db->query( 'UPDATE
								picks
						    SET
								winner_pick	= ?,
								loser_pick	= ?,
								ip			= ?,
								updated		= ?,
								picked		= ?
						    WHERE
								user_id		= ? AND
								game_id		= ?',
							$pick[ 'winner_pick' ], $pick[ 'loser_pick' ], $ip, $time, $pick[ 'picked' ],
							$pick[ 'user_id' ], $pick[ 'game_id' ] );
	}

	public function Delete( $pick_id )
	{
		return $this->_db->query( 'DELETE FROM picks WHERE id = ?', $pick_id );
	}

	public function Delete_User( $userid )
	{
		return $this->_db->query( 'DELETE FROM picks WHERE user_id = ?', $userid );
	}

	public function Delete_Game( $gameid )
	{
		return $this->_db->query( 'DELETE FROM picks WHERE game_id = ?', $gameid );
	}

	public function Remaining( $userid, $weekid )
	{
		$date	= time();
		$count 	= $this->_db->single( 'SELECT
									COUNT( p.id ) AS remaining
								FROM
									picks p, games g
								WHERE
									p.user_id 		= ? 	AND
									p.week 			= ? 	AND
									p.picked	 	= 0 	AND
									p.game_id 		= g.id 	AND
									g.date 			> ?',
								$remaining, $userid, $weekid, $date );

		if ( $count === false )
		{
			return false;
		}

		return $remaining[ 'remaining' ];
	}

	public function Missing( $userid, $weekid )
	{
		$count = $this->_db->single( 'SELECT COUNT( id ) AS count FROM picks WHERE user_id = ? AND week = ? AND picked = 0', $missing, $userid, $weekid );

		if ( $count === false )
		{
			return false;
		}

		return $missing[ 'count' ];
	}

	public function UserWeekList_Load( $userid, $week, &$picks )
	{
		return $this->_db->select( 'SELECT p.*, ( SELECT t.team FROM teams t WHERE t.id = p.winner_pick ) AS winner, ( SELECT t.team FROM teams t WHERE t.id = p.loser_pick ) AS loser FROM picks p WHERE user_id = ? AND week = ?', $picks, $userid, $week );
	}

	public function Load_User_Game( $userid, $gameid, &$pick )
	{
		return $this->_db->single( 'SELECT * FROM picks WHERE user_id = ? AND game_id = ?', $pick, $userid, $gameid );
	}

	public function List_Load_User_Week( $userid, $week, &$picks )
	{
		return $this->_db->select( 'SELECT * FROM picks WHERE user_id = ? AND week = ?', $picks, $userid, $week );
	}

	public function List_Load_User_Week_Picked( $userid, $week, &$picks )
	{
		return $this->_db->select( 'SELECT * FROM picks WHERE user_id = ? AND week = ? AND picked = 1', $picks, $userid, $week );
	}

	public function List_Load( &$picks )
	{
		return $this->_db->select( 'SELECT * FROM picks ORDER BY id ASC', $picks );
	}
}
