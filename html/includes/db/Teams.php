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
					abbr 	varchar( 3 ),
					o_abbr	varchar( 3 ),
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

		if ( empty( $team[ 'o_abbr' ] ) )
		{
			$team[ 'o_abbr' ] = $team[ 'abbr' ];
		}

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
		return $this->_db->single( 'SELECT * FROM teams WHERE REPLACE( abbr, ".", "" ) LIKE CONCAT ( UPPER( ? ), \'%\' )', $team, $abbr );
	}

	public function Load_O_Abbr( $o_abbr, &$team )
	{
		return $this->_db->single( 'SELECT * FROM teams WHERE o_abbr = ?', $team, $o_abbr );
	}

	public function Recalculate_Records()
	{
		return $this->_db->query( 'UPDATE
									teams t
								   SET
									t.wins 		= ( SELECT COUNT( g.id ) FROM games g WHERE g.winner 	= t.id ),
									t.losses	= ( SELECT COUNT( g.id ) FROM games g WHERE g.loser		= t.id )' );
	}

	private function _Default_Teams()
	{
		$teams = array();

		array_push( $teams, array( 'team' => 'Buffalo Bills', 			'conf' => 'AFC East',	'stadium' => 'Ralph Wilson Stadium', 				'abbr' => 'BUF',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Miami Dolphins', 			'conf' => 'AFC East',	'stadium' => 'Sun Lift Stadium', 					'abbr' => 'MIA',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'New England Patriots', 	'conf' => 'AFC East',	'stadium' => 'Gillette Stadium', 					'abbr' => 'NE',		'o_abbr' => 'NEP' ) );
		array_push( $teams, array( 'team' => 'New York Jets', 			'conf' => 'AFC East',	'stadium' => 'MetLife Stadium', 					'abbr' => 'NYJ',	'o_abbr' => '' ) );

		array_push( $teams, array( 'team' => 'Baltimore Ravens', 		'conf' => 'AFC North', 	'stadium' => 'M&T Bank Stadium', 					'abbr' => 'BAL',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Carolina Panthers', 		'conf' => 'AFC North', 	'stadium' => 'Bank of America Stadium', 			'abbr' => 'CAR',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Cincinnati Bengals', 		'conf' => 'AFC North', 	'stadium' => 'Paul Brown Stadium', 					'abbr' => 'CIN',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Cleveland Browns', 		'conf' => 'AFC North', 	'stadium' => 'FirstEnergy Stadium', 				'abbr' => 'CLE',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Pittsburgh Steelers', 	'conf' => 'AFC North', 	'stadium' => 'Heinz Field', 						'abbr' => 'PIT',	'o_abbr' => '' ) );

		array_push( $teams, array( 'team' => 'Houston Texans', 			'conf' => 'AFC South', 	'stadium' => 'Reliant Stadium', 					'abbr' => 'HOU',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Indianapolis Colts', 		'conf' => 'AFC South', 	'stadium' => 'Lucas Oil Stadium', 					'abbr' => 'IND',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Jacksonville Jaguars', 	'conf' => 'AFC South', 	'stadium' => 'EverBank Field', 						'abbr' => 'JAC',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Tennessee Titans', 		'conf' => 'AFC South', 	'stadium' => 'LP Field', 							'abbr' => 'TEN',	'o_abbr' => '' ) );

		array_push( $teams, array( 'team' => 'Arizona Cardinals', 		'conf' => 'AFC West', 	'stadium' => 'University of Phoenix Stadium', 		'abbr' => 'ARI',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Denver Broncos', 			'conf' => 'AFC West', 	'stadium' => 'Sports Authority Field at Mile High', 'abbr' => 'DEN',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Kansas City Chiefs', 		'conf' => 'AFC West', 	'stadium' => 'Arrowhead Stadium', 					'abbr' => 'KC',		'o_abbr' => 'KCC' ) );
		array_push( $teams, array( 'team' => 'Oakland Raiders', 		'conf' => 'AFC West', 	'stadium' => 'O.co Coliseum', 						'abbr' => 'OAK',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'San Diego Chargers', 		'conf' => 'AFC West', 	'stadium' => 'Qualcomm Stadium', 					'abbr' => 'SD',		'o_abbr' => 'SDC' ) );

		array_push( $teams, array( 'team' => 'Dallas Cowboys', 			'conf' => 'NFC East', 	'stadium' => 'AT&T Stadium', 						'abbr' => 'DAL',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'New York Giants', 		'conf' => 'NFC East', 	'stadium' => 'MetLife Stadium', 					'abbr' => 'NYG',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Philadelphia Eagles', 	'conf' => 'NFC East', 	'stadium' => 'Lincoln Financial Field', 			'abbr' => 'PHI',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Washington Redskins', 	'conf' => 'NFC East', 	'stadium' => 'FedEx Field', 						'abbr' => 'WAS',	'o_abbr' => '' ) );

		array_push( $teams, array( 'team' => 'Chicago Bears', 			'conf' => 'NFC North', 	'stadium' => 'Soldier Field', 						'abbr' => 'CHI',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Detroit Lions', 			'conf' => 'NFC North', 	'stadium' => 'Ford Field', 							'abbr' => 'DET',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Green Bay Packers', 		'conf' => 'NFC North', 	'stadium' => 'Lambeau Field', 						'abbr' => 'GB',		'o_abbr' => 'GBP' ) );
		array_push( $teams, array( 'team' => 'Minnesota Vikings', 		'conf' => 'NFC North', 	'stadium' => 'Mall of America Field', 				'abbr' => 'MIN',	'o_abbr' => '' ) );

		array_push( $teams, array( 'team' => 'Atlanta Falcons', 		'conf' => 'NFC South', 	'stadium' => 'Georgia Dome', 						'abbr' => 'ATL',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'New Orleans Saints', 		'conf' => 'NFC South', 	'stadium' => 'Mercedes-Benz Superdome', 			'abbr' => 'NO',		'o_abbr' => 'NOS' ) );
		array_push( $teams, array( 'team' => 'Tampa Bay Buccaneers', 	'conf' => 'NFC South', 	'stadium' => 'Raymond James Stadium', 				'abbr' => 'TB',		'o_abbr' => 'TBB' ) );

		array_push( $teams, array( 'team' => 'San Francisco 49ers', 	'conf' => 'NFC West', 	'stadium' => 'Candlestick Park', 					'abbr' => 'SF',		'o_abbr' => 'SFO' ) );
		array_push( $teams, array( 'team' => 'Seattle Seahawks', 		'conf' => 'NFC West', 	'stadium' => 'CenturyLink Field', 					'abbr' => 'SEA',	'o_abbr' => '' ) );
		array_push( $teams, array( 'team' => 'Los Angeles Rams', 		'conf' => 'NFC West', 	'stadium' => 'Los Angeles Memorial Coliseum',		'abbr' => 'LA',		'o_abbr' => '' ) );

		return $teams;
	}
}
