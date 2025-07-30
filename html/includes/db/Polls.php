<?php

class DatabaseTablePolls extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE polls
				(
					id 			int( 11 ) AUTO_INCREMENT,
					active		tinyint( 1 ),
					date 		int( 11 ),
					question 	varchar( 255 ),
					PRIMARY KEY ( id )
				)";

		return $this->query( $sql );
	}

	public function Load( $poll_id, &$poll )
	{
		return $this->single( 'SELECT * FROM polls WHERE id = ?', $poll, $poll_id );
	}

	public function List_Load( &$polls )
	{
		return $this->select( 'SELECT * FROM polls ORDER BY id DESC', $polls );
	}

	public function Insert( &$poll )
	{
		$poll[ 'date' ] = time();

		return $this->query( 'INSERT INTO polls ( active, date, question ) VALUES ( ?, ?, ? )', $poll[ 'active' ], $poll[ 'date' ], $poll[ 'question' ] );
	}

	public function Delete( $poll_id )
	{
		$poll_votes 	= $this->db_manager->pollvotes();
		$poll_answers	= $this->db_manager->pollanswers();

		if ( !$poll_votes->Delete_Poll( $poll_id ) || !$poll_answers->Answers_Delete_Poll( $poll_id ) || !$this->Delete_LowLevel( $poll_id ) )
		{
			return false;
		}

		return true;
	}

	public function Delete_LowLevel( $poll_id )
	{
		return $this->query( 'DELETE FROM polls WHERE id = ?', $poll_id );
	}

	public function Update( &$poll )
	{
		return $this->query( 'UPDATE polls SET active = ?, question = ? WHERE id = ?', $poll[ 'active' ], $poll[ 'question' ], $poll[ 'id' ] );
	}

	public function Latest( &$poll )
	{
		return $this->select( 'SELECT * FROM polls WHERE active = 1 ORDER BY id DESC LIMIT 1', $poll );
	}
}
