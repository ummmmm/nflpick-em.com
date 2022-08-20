<?php

class Weekly_Records
{
	private $_db;
	private $_error;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE weekly_records
				(
					id 		int( 11 ) AUTO_INCREMENT,
					user_id	int( 11 ),
					week_id	int( 11 ),
					wins 	int( 11 ),
					losses	int( 11 ),
					ties	int( 11 ),
					manual	tinyint( 1 ),
					PRIMARY KEY ( id ),
					UNIQUE INDEX ( user_id, week_id )
				)";

		return $this->_db->query( $sql );
	}

	public function Insert_User( $user_id )
	{
		$db_weeks = new Weeks( $this->_db );

		if ( !$db_weeks->List_Load( $weeks ) )
		{
			return false;
		}

		foreach ( $weeks as $week )
		{
			$weekly_record[ 'user_id' ] = $user_id;
			$weekly_record[ 'week_id' ] = $week[ 'id' ];

			if ( !$this->_db->query( 'INSERT INTO weekly_records
								   ( user_id, week_id, wins, losses, ties, manual )
								   VALUES
								   ( ?, ?, 0, 0, 0, 0 )',
								   $weekly_record[ 'user_id' ], $weekly_record[ 'week_id' ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public function Update( &$weekly_record )
	{
		return $this->_db->query( 'UPDATE
									weekly_records
								   SET
									wins = ?,
									losses = ?,
									ties = ?,
									manual = ?
								   WHERE
									id = ?',
									$weekly_record[ 'wins' ], $weekly_record[ 'losses' ], $weekly_record[ 'ties' ], $weekly_record[ 'manual' ],
									$weekly_record[ 'id' ] );
	}

	public function Load_User_Week( $user_id, $week_id, &$weekly_record )
	{
		return $this->_db->single( 'SELECT * FROM weekly_records WHERE user_id = ? AND week_id = ?', $weekly_record, $user_id, $week_id );
	}

	private function _Set_Error( $error )
	{
		$this->_error = $error;
		return false;
	}

	public function Get_Error()
	{
		return $this->_error;
	}
}
