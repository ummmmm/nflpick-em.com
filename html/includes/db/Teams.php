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
					conf 	varchar( 15 ),
					stadium varchar( 255 ),
					wins 	int( 11 ),
					losses 	int( 11 ),
					ties	int( 11 ),
					abbr 	varchar( 3 ),
					PRIMARY KEY ( id )
				)";

		if ( !$this->_db->query( $sql ) )
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

		return $this->_db->insert( 'teams', $team );
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
		return $this->_db->select( 'SELECT * FROM teams ORDER BY id ASC', $teams );
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
									team 	= ?,
									conf	= ?,
									stadium	= ?,
									wins	= ?,
									losses	= ?,
									abbr	= ?
								   WHERE
									id 		= ?',
								   $team[ 'team' ], $team[ 'conf' ], $team[ 'stadium' ], $team[ 'wins' ], $team[ 'losses' ], $team[ 'abbr' ],
								   $team[ 'id' ] );
	}

	public function Byes( $week_id, &$bye_teams )
	{
		return $this->_db->single( 'SELECT GROUP_CONCAT( team ORDER BY team SEPARATOR \', \' ) AS bye_teams FROM teams t WHERE NOT EXISTS( SELECT g.id FROM games g WHERE ( g.away = t.id OR g.home = t.id ) AND g.week = ? )', $bye_teams, $week_id );
	}

	public function Load_Abbr( $abbr, &$team )
	{
		return $this->_db->single( 'SELECT * FROM teams WHERE abbr = ?', $team, $abbr );
	}

	public function Recalculate_Records()
	{
		return $this->_db->query( 'UPDATE
									teams t
								   SET
									t.wins 		= ( SELECT COUNT( g.id ) FROM games g WHERE g.winner 	= t.id AND g.final = 1 AND g.tied = 0 ),
									t.losses	= ( SELECT COUNT( g.id ) FROM games g WHERE g.loser		= t.id AND g.final = 1 AND g.tied = 0 ),
									t.ties		= ( SELECT COUNT( g.id ) FROM games g WHERE ( g.away	= t.id OR g.home = t.id ) AND g.final = 1 AND g.tied = 1 )' );
	}

	private function _Default_Teams()
	{
		$teams = array();

		array_push( $teams, array( 'team' => 'Buffalo Bills', 			'conf' => 'AFC East',	'stadium' => 'Bills Stadium', 						'abbr' => 'BUF'	 ) );
		array_push( $teams, array( 'team' => 'Miami Dolphins', 			'conf' => 'AFC East',	'stadium' => 'Hard Rock Stadium', 					'abbr' => 'MIA'	 ) );
		array_push( $teams, array( 'team' => 'New England Patriots', 	'conf' => 'AFC East',	'stadium' => 'Gillette Stadium', 					'abbr' => 'NE'	 ) );
		array_push( $teams, array( 'team' => 'New York Jets', 			'conf' => 'AFC East',	'stadium' => 'MetLife Stadium', 					'abbr' => 'NYJ'	 ) );

		array_push( $teams, array( 'team' => 'Baltimore Ravens', 		'conf' => 'AFC North', 	'stadium' => 'M&T Bank Stadium', 					'abbr' => 'BAL' ) );
		array_push( $teams, array( 'team' => 'Carolina Panthers', 		'conf' => 'AFC North', 	'stadium' => 'Bank of America Stadium', 			'abbr' => 'CAR' ) );
		array_push( $teams, array( 'team' => 'Cincinnati Bengals', 		'conf' => 'AFC North', 	'stadium' => 'Paul Brown Stadium', 					'abbr' => 'CIN' ) );
		array_push( $teams, array( 'team' => 'Cleveland Browns', 		'conf' => 'AFC North', 	'stadium' => 'FirstEnergy Stadium', 				'abbr' => 'CLE' ) );
		array_push( $teams, array( 'team' => 'Pittsburgh Steelers', 	'conf' => 'AFC North', 	'stadium' => 'Heinz Field', 						'abbr' => 'PIT' ) );

		array_push( $teams, array( 'team' => 'Houston Texans', 			'conf' => 'AFC South', 	'stadium' => 'NRG Stadium', 						'abbr' => 'HOU' ) );
		array_push( $teams, array( 'team' => 'Indianapolis Colts', 		'conf' => 'AFC South', 	'stadium' => 'Lucas Oil Stadium', 					'abbr' => 'IND' ) );
		array_push( $teams, array( 'team' => 'Jacksonville Jaguars', 	'conf' => 'AFC South', 	'stadium' => 'TIAA Bank Field', 					'abbr' => 'JAX' ) );
		array_push( $teams, array( 'team' => 'Tennessee Titans', 		'conf' => 'AFC South', 	'stadium' => 'Nissan Stadium', 						'abbr' => 'TEN' ) );

		array_push( $teams, array( 'team' => 'Arizona Cardinals', 		'conf' => 'AFC West', 	'stadium' => 'State Farm Stadium', 					'abbr' => 'ARI' ) );
		array_push( $teams, array( 'team' => 'Denver Broncos', 			'conf' => 'AFC West', 	'stadium' => 'Empower Field at Mile High',			'abbr' => 'DEN' ) );
		array_push( $teams, array( 'team' => 'Kansas City Chiefs', 		'conf' => 'AFC West', 	'stadium' => 'Arrowhead Stadium', 					'abbr' => 'KC'	) );
		array_push( $teams, array( 'team' => 'Las Vegas Raiders', 		'conf' => 'AFC West', 	'stadium' => 'Allegiant Stadium',					'abbr' => 'OAK' ) );
		array_push( $teams, array( 'team' => 'Los Angeles Chargers', 	'conf' => 'AFC West', 	'stadium' => 'SoFi Stadium',				 		'abbr' => 'LAC' ) );

		array_push( $teams, array( 'team' => 'Dallas Cowboys', 			'conf' => 'NFC East', 	'stadium' => 'AT&T Stadium', 						'abbr' => 'DAL' ) );
		array_push( $teams, array( 'team' => 'New York Giants', 		'conf' => 'NFC East', 	'stadium' => 'MetLife Stadium', 					'abbr' => 'NYG' ) );
		array_push( $teams, array( 'team' => 'Philadelphia Eagles', 	'conf' => 'NFC East', 	'stadium' => 'Lincoln Financial Field', 			'abbr' => 'PHI' ) );
		array_push( $teams, array( 'team' => 'Washington Redskins', 	'conf' => 'NFC East', 	'stadium' => 'FedExField', 							'abbr' => 'WAS' ) );

		array_push( $teams, array( 'team' => 'Chicago Bears', 			'conf' => 'NFC North', 	'stadium' => 'Soldier Field', 						'abbr' => 'CHI'	) );
		array_push( $teams, array( 'team' => 'Detroit Lions', 			'conf' => 'NFC North', 	'stadium' => 'Ford Field', 							'abbr' => 'DET'	) );
		array_push( $teams, array( 'team' => 'Green Bay Packers', 		'conf' => 'NFC North', 	'stadium' => 'Lambeau Field', 						'abbr' => 'GB'	) );
		array_push( $teams, array( 'team' => 'Minnesota Vikings', 		'conf' => 'NFC North', 	'stadium' => 'U.S. Bank Stadium',	 				'abbr' => 'MIN'	) );

		array_push( $teams, array( 'team' => 'Atlanta Falcons', 		'conf' => 'NFC South', 	'stadium' => 'Mercedes-Benz Stadium', 				'abbr' => 'ATL'	) );
		array_push( $teams, array( 'team' => 'New Orleans Saints', 		'conf' => 'NFC South', 	'stadium' => 'Mercedes-Benz Superdome', 			'abbr' => 'NO'	) );
		array_push( $teams, array( 'team' => 'Tampa Bay Buccaneers', 	'conf' => 'NFC South', 	'stadium' => 'Raymond James Stadium', 				'abbr' => 'TB'	) );

		array_push( $teams, array( 'team' => 'San Francisco 49ers', 	'conf' => 'NFC West', 	'stadium' => 'Levi\'s Stadium', 					'abbr' => 'SF'	) );
		array_push( $teams, array( 'team' => 'Seattle Seahawks', 		'conf' => 'NFC West', 	'stadium' => 'CenturyLink Field', 					'abbr' => 'SEA'	) );
		array_push( $teams, array( 'team' => 'Los Angeles Rams', 		'conf' => 'NFC West', 	'stadium' => 'SoFi Stadium',						'abbr' => 'LA'	) );

		return $teams;
	}
}
