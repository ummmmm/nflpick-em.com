<?php

class DatabaseTablePerfectWeekPaid extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE perfect_week_paid
				(
					week_id int( 11 ),
					user_id int( 11 )
				)";

		return $this->query( $sql );
	}

	public function Load( $week_id, $user_id, &$record )
	{
		return $this->single( 'SELECT * FROM perfect_week_paid WHERE week_id = ? AND user_id = ?', $record, $week_id, $user_id );
	}

	public function Insert( $week_id, $user_id )
	{
		$values = array( "week_id" => $week_id, "user_id" => $user_id );

		return $this->query( 'INSERT INTO perfect_week_paid ( week_id, user_id ) VALUES ( ?, ? )', $week_id, $user_id );
	}

	public function Delete( $week_id, $user_id )
	{
		return $this->query( 'DELETE FROM perfect_week_paid WHERE week_id = ? AND user_id = ?', $week_id, $user_id );
	}

	public function Delete_User( $user_id )
	{
		return $this->query( 'DELETE FROM perfect_week_paid WHERE user_id = ?', $user_id );
	}
}
