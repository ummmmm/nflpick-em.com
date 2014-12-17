<?php
class Polls
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE polls
				(
					id 			int( 11 ) AUTO_INCREMENT,
					active		tinyint( 1 ),
					date 		datetime,
					question 	varchar( 255 ),
					PRIMARY KEY ( id )
				)";

		return $this->_db->query( $sql );
	}

	public function Load( $poll_id, &$poll )
	{
		return $this->_db->single( 'SELECT * FROM polls WHERE id = ?', $poll, $poll_id );
	}

	public function List_Load( &$polls )
	{
		return $this->_db->select( 'SELECT * FROM polls ORDER BY id DESC', $polls );
	}

	public function Insert( &$poll )
	{
		$poll[ 'date' ] = Functions::Timestamp();

		return $this->_db->insert( 'polls', $poll );
	}

	public function Delete( $poll_id )
	{
		$poll_votes 	= new Poll_Votes( $this->_db );
		$poll_answers	= new Poll_Answers( $this->_db );

		if ( !$poll_votes->Delete_Poll( $poll_id ) || !$poll_answers->Delete_Poll( $poll_id ) || !$this->Delete_LowLevel( $poll_id ) )
		{
			return false;
		}

		return true;
	}

	public function Delete_LowLevel( $poll_id )
	{
		return $this->_db->query( 'DELETE FROM polls WHERE id = ?', $poll_id );
	}

	public function Update( &$poll )
	{
		return $this->_db->query( 'UPDATE polls SET active = ?, question = ? WHERE id = ?', $poll[ 'active' ], $poll[ 'question' ], $poll[ 'id' ] );
	}

	public function Latest( &$poll )
	{
		return $this->_db->select( 'SELECT * FROM polls WHERE active = 1 ORDER BY id DESC LIMIT 1', $poll );
	}
}
