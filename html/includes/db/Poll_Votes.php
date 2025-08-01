<?php

class DatabaseTablePollVotes extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE poll_votes
				(
					id 			int( 11 ) AUTO_INCREMENT,
					poll_id 	int( 11 ),
					answer_id 	int( 11 ),
					user_id 	int( 11 ),
					date 		int( 11 ),
					ip 			varchar( 255 ),
					PRIMARY KEY ( id ),
					UNIQUE KEY 	poll_votes_1 ( poll_id, user_id )
				)";

		return $this->query( $sql );
	}

	public function Total_Poll( $poll_id )
	{
		return $this->select( 'SELECT id FROM poll_votes WHERE poll_id = ?', $null, $poll_id );
	}

	public function Total_Answer( $answer_id )
	{
		return $this->select( 'SELECT id FROM poll_votes WHERE answer_id = ?', $null, $answer_id );
	}

	public function Delete_User( $user_id )
	{
		return $this->query( 'DELETE FROM poll_votes WHERE user_id = ?', $user_id );
	}

	public function Insert( &$vote )
	{
		$vote[ 'date' ] = time();
		$vote[ 'ip' ] 	= $_SERVER[ 'REMOTE_ADDR' ];

		return $this->query( 'INSERT INTO poll_votes ( poll_id, answer_id, user_id, date, ip ) VALUES ( ?, ?, ?, ?, ? )', $vote[ 'poll_id' ], $vote[ 'answer_id' ], $vote[ 'user_id' ], $vote[ 'date' ], $vote[ 'ip' ] );
	}

	public function Delete_Answer( $answer_id )
	{
		return $this->query( 'DELETE FROM poll_votes WHERE answer_id = ?', $answer_id );
	}

	public function Delete_Poll( $poll_id )
	{
		return $this->query( 'DELETE FROM poll_votes WHERE poll_id = ?', $poll_id );
	}

	public function List_Load_Poll( $poll_id, &$votes )
	{
		return $this->select( 'SELECT * FROM poll_votes WHERE poll_id = ?', $votes, $poll_id );
	}
}
