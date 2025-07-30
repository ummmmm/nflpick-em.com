<?php

class DatabaseTableTeams extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE teams
				(
					id 		int( 11 ) AUTO_INCREMENT,
					team 	varchar( 255 ),
					wins 	int( 11 ),
					losses 	int( 11 ),
					ties	int( 11 ),
					abbr 	varchar( 3 ),
					PRIMARY KEY ( id )
				)";

		if ( !$this->query( $sql ) )
		{
			return false;
		}

		$teams = $this->_Default_Teams();

		foreach ( $teams as $team )
		{
			if ( !$this->_Insert( $team ) )
			{
				return false;
			}
		}

		return true;
	}

	public function _Insert( $team )
	{
		$team[ 'wins' ] 	= 0;
		$team[ 'losses' ]	= 0;
		$team[ 'ties' ]		= 0;

		return $this->query( 'INSERT INTO teams ( team, wins, losses, ties, abbr ) VALUES ( ?, ?, ?, ?, ? )', $team[ 'team' ], $team[ 'wins' ], $team[ 'losses' ], $team[ 'ties' ], $team[ 'abbr' ] );
	}

	public function Update_Wins( $teamid )
	{
		return $this->query( 'UPDATE teams SET wins = wins + 1 WHERE id = ?', $teamid );
	}

	public function Update_Losses( $teamid )
	{
		return $this->query( 'UPDATE teams SET losses = losses + 1 WHERE id = ?', $teamid );
	}

	public function Load( $teamid, &$team )
	{
		return $this->single( 'SELECT * FROM teams WHERE id = ?', $team, $teamid );
	}

	public function Load_Name( $name, &$team )
	{
		return $this->single( 'SELECT * FROM teams WHERE team LIKE CONCAT( \'%\', ?, \'%\' )', $team, $name );
	}

	public function List_Load( &$teams )
	{
		return $this->select( 'SELECT * FROM teams ORDER BY id ASC', $teams );
	}

	public function Delete( $teamid )
	{
		return $this->query( 'DELETE FROM teams WHERE id = ?', $teamid );
	}

	public function Update( $team )
	{
		return $this->query( 'UPDATE
									teams
								   SET
									team 	= ?,
									wins	= ?,
									losses	= ?,
									abbr	= ?
								   WHERE
									id 		= ?',
								   $team[ 'team' ], $team[ 'wins' ], $team[ 'losses' ], $team[ 'abbr' ],
								   $team[ 'id' ] );
	}

	public function List_Load_Byes( $week_id, &$bye_teams )
	{
		return $this->select( 'SELECT
										t.*
									FROM
										teams t
										LEFT OUTER JOIN games g ON g.week = ? AND ( g.away = t.id OR g.home = t.id )
									WHERE
										g.id IS NULL',
									$bye_teams, $week_id );
	}

	public function Load_Abbr( $abbr, &$team )
	{
		return $this->single( 'SELECT * FROM teams WHERE abbr = ?', $team, $abbr );
	}

	public function Recalculate_Records()
	{
		return $this->query( 'UPDATE
									teams t
								   SET
									t.wins 		= ( SELECT COUNT( g.id ) FROM games g WHERE g.winner 	= t.id AND g.final = 1 AND g.tied = 0 ),
									t.losses	= ( SELECT COUNT( g.id ) FROM games g WHERE g.loser		= t.id AND g.final = 1 AND g.tied = 0 ),
									t.ties		= ( SELECT COUNT( g.id ) FROM games g WHERE ( g.away	= t.id OR g.home = t.id ) AND g.final = 1 AND g.tied = 1 )' );
	}

	private function _Default_Teams()
	{
		$teams		= array();
		$team_url 	= 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams/%d';
		$data		= json_decode( file_get_contents( 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/teams?limit=100' ) );

		foreach ( $data->sports[ 0 ]->leagues[ 0 ]->teams as $entry )
		{
			$team_data = json_decode( file_get_contents( sprintf( $team_url, $entry->team->id ) ) );

			array_push( $teams, array( 'team' => $team_data->team->displayName, 'abbr' => $team_data->team->abbreviation ) );

		}

		return $teams;
	}
}
