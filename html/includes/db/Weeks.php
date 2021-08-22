<?php

class Weeks
{
	private $_db;
	private $_error;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE weeks
				(
					id 		int( 11 ),
					date 	int( 11 ),
					locked 	tinyint( 1 ),
					PRIMARY KEY ( id )
				)";

		return $this->_db->query( $sql );
	}

	public function Load( $week_id, &$week )
	{
		return $this->_db->single( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w WHERE id = ?', $week, $week_id );
	}

	public function List_Load( &$weeks )
	{
		return $this->_db->select( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w ORDER BY id', $weeks );
	}

	public function Insert( &$week )
	{
		return $this->_db->insert( 'weeks', $week );
	}

	public function IsLocked( $week_id )
	{
		$count = $this->Load( $week_id, $week );

		if ( !$count || $week[ 'locked' ] === 0 )
		{
			return false;
		}

		return true;
	}

	public function Update( $week )
	{
		return $this->_db->query( 'UPDATE weeks SET date = ?, locked = ? WHERE id = ?', $week[ 'date' ], $week[ 'locked' ], $week[ 'id' ] );
	}

	public function Current()
	{
		$count = $this->_db->single( 'SELECT id FROM weeks WHERE locked = 0 ORDER BY id', $week );

		if ( $count === false )
		{
			return false;
		}

		if ( $count === 0 )
		{
			return 1;
		}

		return $week[ 'id' ];
	}

	public function Previous()
	{
		$count = $this->_db->single( 'SELECT id FROM weeks WHERE locked = 1 ORDER BY id DESC', $week );

		if ( $count === false )
		{
			return false;
		}

		if ( $count === 0 )
		{
			return 1;
		}

		return $week[ 'id' ];
	}

	public function Total_Games( $week )
	{
		$count = $this->_db->single( 'SELECT COUNT( id ) AS total FROM games WHERE week = ?', $games, $week );

		if ( $count === false )
		{
			return false;
		}

		return $games[ 'total' ];
	}

	public function Create_Weeks( $start_date )
	{
		$count = $this->List_Load( $null );

		if ( $count === false )		return false;
		else if ( $count !== 0 )	return $this->_Set_Error( 'Weeks table must be empty to call Create_Weeks' );

		for ( $i = 1; $i <= 18; $i++ )
		{
			$week[ 'id' ] 		= $i;
			$week[ 'date']		= $start_date;
			$week[ 'locked' ]	= 0;

			if ( !$this->Insert( $week ) )
			{
				return false;
			}

			$start_date = strtotime( '+1 week', $start_date );
		}

		return true;
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
