<?php

class DatabaseTableSentPicks extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE sent_picks
				(
					id 		int( 11 ) AUTO_INCREMENT,
					user_id int( 11 ),
					picks 	text,
					date 	int( 11 ),
					ip 		varchar( 50 ),
					week 	tinyint( 11 ),
					active 	tinyint( 1 ),
					PRIMARY KEY ( id )
				)";

		return $this->query( $sql );
	}

	public function Insert( &$insert )
	{
		if ( !$this->Reset_Active_User_Week( $insert[ 'user_id' ], $insert[ 'week' ] ) )
		{
			return false;
		}

		return $this->Insert_LowLevel( $insert );
	}

	public function Insert_LowLevel( &$picks )
	{
		$picks[ 'ip' ]		= $_SERVER[ 'REMOTE_ADDR' ];
		$picks[ 'date' ]	= time();

		return $this->query( 'INSERT INTO sent_picks ( user_id, picks, date, ip, week, active ) VALUES ( ?, ?, ?, ?, ?, ? )', $picks[ 'user_id' ], $picks[ 'picks' ], $picks[ 'date' ], $picks[ 'ip' ], $picks[ 'week' ], $picks[ 'active' ] );
	}

	public function Delete_User( $user_id )
	{
		return $this->query( 'DELETE FROM sent_picks WHERE user_id = ?', $user_id );
	}

	public function Reset_Active_User_Week( $user_id, $week )
	{
		return $this->query( 'UPDATE sent_picks SET active = 0 WHERE user_id = ? AND week = ?', $user_id, $week );
	}
}
