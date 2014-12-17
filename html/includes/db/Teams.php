<?php

class Teams
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE teams
				(
					id 		int( 11 ) AUTO_INCREMENT,
					team 	varchar( 255 ),
					picture varchar( 20 ),
					conf 	varchar( 15 ),
					stadium varchar( 255 ),
					wins 	int( 2 ),
					losses 	int( 2 ),
					abbr 	varchar( 10 ),
					PRIMARY KEY ( id )
				)";

		return $this->_db->query( $sql );
	}

	public function Update_Wins( $teamid )
	{
		return $this->_db->query( 'UPDATE teams SET wins = wins + 1 WHERE id = ?', $teamid );
	}

	public function Update_Losses( $teamid )
	{
		return $this->_db->query( 'UPDATE teams SET losses = losses + 1 WHERE id = ?', $teamid );
	}

	public function Load( $teamid, &$team )
	{
		return $this->_db->single( 'SELECT * FROM teams WHERE id = ?', $team, $teamid );
	}

	public function Load_Name( $name, &$team )
	{
		return $this->_db->single( 'SELECT * FROM teams WHERE team LIKE CONCAT( \'%\', ?, \'%\' )', $team, $name );
	}

	public function List_Load( &$teams )
	{
		return $this->_db->select( 'SELECT * FROM teams', $teams );
	}

	public function Delete( $teamid )
	{
		return $this->_db->query( 'DELETE FROM teams WHERE id = ?', $teamid );
	}

	public function Update( $team )
	{
		return $this->_db->query( 'UPDATE
								teams
							SET
								team = ?,
								picture = ?,
								conf	= ?,
								stadium	= ?,
								wins	= ?,
								losses	= ?,
								abbr	= ?
							WHERE
								id = ?',
							$team[ 'team' ], $team[ 'picture' ], $team[ 'conf' ], $team[ 'stadium' ], $team[ 'wins' ], $team[ 'losses' ], $team[ 'abbr' ],
							$team[ 'id' ] );
	}

	public function Byes( $week_id, &$bye_teams )
	{
		return $this->_db->single( 'SELECT GROUP_CONCAT( team ORDER BY team SEPARATOR \', \' ) AS bye_teams FROM teams t WHERE NOT EXISTS( SELECT g.id FROM games g WHERE ( g.away = t.id OR g.home = t.id ) AND g.week = ? )', $bye_teams, $week_id );
	}

	public function Load_Abbr( $abbr, &$team )
	{
		return $this->_db->single( 'SELECT * FROM teams WHERE REPLACE( abbr, ".", "" ) LIKE CONCAT ( UPPER( ? ), \'%\' )', $team, $abbr );
	}

	public function Recalculate_Records()
	{
		return $this->_db->query( 'UPDATE
								teams t
							SET
								t.wins 		= ( SELECT COUNT( g.id ) FROM games g WHERE g.winner 	= t.id ),
								t.losses	= ( SELECT COUNT( g.id ) FROM games g WHERE g.loser		= t.id )' );
	}
}
