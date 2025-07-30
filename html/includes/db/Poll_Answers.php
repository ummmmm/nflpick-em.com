<?php

class DatabaseTablePollAnswers extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE poll_answers
				(
					id 		int( 11 ) AUTO_INCREMENT,
					poll_id int( 11 ),
					answer	varchar( 255 ),
					PRIMARY KEY ( id )
				)";

		return $this->query( $sql );
	}

	public function Insert( $answer )
	{
		return $this->query( 'INSERT INTO poll_answers ( poll_id, answer ) VALUES ( ?, ? )', $answer[ 'poll_id' ], $answer[ 'answer' ] );
	}

	public function Update( $answer )
	{
		return $this->query( 'UPDATE poll_answers SET answer = ? WHERE id = ?', $answer[ 'answer' ], $answer[ 'id' ] );
	}

	public function List_Load_Poll( $poll_id, &$answers )
	{
		return $this->select( 'SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY id DESC', $answers, $poll_id );
	}

	public function Load( $answer_id, &$answer )
	{
		return $this->single( 'SELECT * FROM poll_answers WHERE id = ?', $answer, $answer_id );
	}

	public function Load_Poll( $answer_id, $poll_id, &$answer )
	{
		return $this->single( 'SELECT * FROM poll_answers WHERE id = ? AND poll_id = ?', $answer, $answer_id, $poll_id );
	}

	public function Delete( $answer_id )
	{
		return $this->query( 'DELETE FROM poll_answers WHERE id = ?', $answer_id );
	}

	public function Answers_Delete_Poll( $poll_id )
	{
		return $this->query( 'DELETE FROM poll_answers WHERE poll_id = ?', $poll_id );
	}
}
