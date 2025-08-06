<?php

class DatabaseTableWeeks extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE weeks
				(
					id 		int( 11 ),
					date 	int( 11 ),
					locked 	tinyint( 1 ),
					PRIMARY KEY ( id )
				)";

		return $this->query( $sql );
	}

	public function Load( $week_id, &$week )
	{
		return $this->single( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w WHERE id = ?', $week, $week_id );
	}

	public function List_Load( &$weeks )
	{
		return $this->select( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w ORDER BY id', $weeks );
	}

	public function List_Load_Locked( &$weeks )
	{
		return $this->select( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w WHERE w.locked = 1 ORDER BY id', $weeks );
	}

	public function Insert( &$week )
	{
		return $this->query( 'INSERT INTO weeks ( id, date, locked ) VALUES ( ?, ?, ? )', $week[ 'id' ], $week[ 'date' ], $week[ 'locked' ] );
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
		return $this->query( 'UPDATE weeks SET date = ?, locked = ? WHERE id = ?', $week[ 'date' ], $week[ 'locked' ], $week[ 'id' ] );
	}

	public function Current()
	{
		$count = $this->single( 'SELECT id FROM weeks WHERE locked = 0 ORDER BY id', $week );

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
		$count = $this->single( 'SELECT id FROM weeks WHERE locked = 1 ORDER BY id DESC', $week );

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

	public function Create_Weeks( $start_date )
	{
		$data = json_decode( file_get_contents( 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?week=0' ) ); 

		foreach ( $data->leagues[ 0 ]->calendar as $entry )
		{
			if ( $entry->label == 'Regular Season' )
			{
				foreach ( $entry->entries as $entry )
				{
					$week[ 'id' ]		= $entry->value;
					$week[ 'date' ]		= $start_date;
					$week[ 'locked' ]	= 0;

					$this->Insert( $week );
				}

				return true;
			}
		}

		throw new NFLPickEmException( 'Failed to get the regular season weeks' );
	}
}
