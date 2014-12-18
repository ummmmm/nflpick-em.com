<?php

class Poll_Votes
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

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

		return $this->_db->query( $sql );
	}

	public function Total_Poll( $poll_id )
	{
		return $this->_db->select( 'SELECT id FROM poll_votes WHERE poll_id = ?', $null, $poll_id );
	}

	public function Total_Answer( $answer_id )
	{
		return $this->_db->select( 'SELECT id FROM poll_votes WHERE answer_id = ?', $null, $answer_id );
	}

	public function Delete_User( $user_id )
	{
		return $this->_db->query( 'DELETE FROM poll_votes WHERE user_id = ?', $user_id );
	}

	public function Insert( &$vote )
	{
		$vote[ 'date' ] = time();
		$vote[ 'ip' ] 	= $_SERVER[ 'REMOTE_ADDR' ];

		return $this->_db->insert( 'poll_votes', $vote );
	}

	public static function Delete_Answer( $answer_id )
	{
		return $this->_db->query( 'DELETE FROM poll_votes WHERE answer_id = ?', $answer_id );
	}

	public static function Delete_Poll( $poll_id )
	{
		return $this->_db->query( 'DELETE FROM poll_votes WHERE poll_id = ?', $poll_id );
	}

	public static function List_Load_Poll( $poll_id, &$votes )
	{
		return $this->_db->select( 'SELECT * FROM poll_votes WHERE poll_id = ?', $votes, $poll_id );
	}
}
