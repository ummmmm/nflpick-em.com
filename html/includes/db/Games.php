<?php

class Games
{
	private $_db;
	private $_error;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE games
				(
					id 			int( 3 ) AUTO_INCREMENT,
					away 		int( 2 ),
					home 		int( 2 ),
					date 		datetime,
					week 		int( 2 ),
					winner 		int( 2 ),
					loser 		int( 2 ),
					homeScore 	int( 2 ),
					awayScore 	int( 2 ),
					PRIMARY KEY ( id )
				)";

		return $this->_db->query( $sql );
	}

	public function List_Load( &$games )
	{
		return $this->_db->select( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, homeTeam.stadium AS stadium,
										awayTeam.team AS awayTeam, awayTeam.wins AS awayWins, awayTeam.losses AS awayLosses, awayTeam.abbr AS awayAbbr,
										homeTeam.team AS homeTeam, homeTeam.wins AS homeWins, homeTeam.losses AS homeLosses, homeTeam.abbr AS homeAbbr
									FROM
										games s,
										teams awayTeam,
										teams homeTeam
									WHERE
										s.away = awayTeam.id AND
										s.home = homeTeam.id
									ORDER BY
										s.date, s.id',
									$games );
	}

	public function List_Load_Week( $week, &$games )
	{
		return $this->_db->select( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, homeTeam.stadium AS stadium,
										awayTeam.team AS awayTeam, awayTeam.wins AS awayWins, awayTeam.losses AS awayLosses, awayTeam.abbr AS awayAbbr,
										homeTeam.team AS homeTeam, homeTeam.wins AS homeWins, homeTeam.losses AS homeLosses, homeTeam.abbr AS homeAbbr
									FROM
										games s,
										teams awayTeam,
										teams homeTeam
									WHERE
										s.away = awayTeam.id AND
										s.home = homeTeam.id AND
										s.week = ?
									ORDER BY
										s.date, s.id',
									$games,
									$week );
	}

	public function Insert( &$game )
	{
		$game[ 'winner' ] 		= 0;
		$game[ 'loser' ]		= 0;
		$game[ 'homeScore' ]	= 0;
		$game[ 'awayScore' ]	= 0;

		if ( !$this->_db->insert( 'games', $game ) )
		{
			return $this->_Set_Error( $this->_db->Get_Error() );
		}

		return true;
	}

	public function Update( $game )
	{
		return $this->_db->query( 'UPDATE
								games
							SET
								away 		= ?,
								home		= ?,
								date		= ?,
								week		= ?,
								winner		= ?,
								loser		= ?,
								homeScore	= ?,
								awayScore	= ?
							WHERE
								id = ?',
							$game[ 'away' ], $game[ 'home' ], $game[ 'date' ], $game[ 'week' ], $game[ 'winner' ], $game[ 'loser' ], $game[ 'homeScore' ], $game[ 'awayScore' ], $game[ 'id' ] );
	}

	public function Delete( $gameid )
	{
		$db_picks = new Picks( $db );

		if ( !$db_picks->Delete_Game( $gameid ) )
		{
			return false;
		}

		return $this->Delete_LowLevel( $gameid );
	}

	public function Delete_LowLevel( $gameid )
	{
		return $this->_db->query( 'DELETE FROM games WHERE id = ?', $gameid );
	}

	public function Load( $gameid, &$game )
	{
		return $this->_db->single( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, homeTeam.stadium AS stadium,
										awayTeam.team AS awayTeam, awayTeam.wins AS awayWins, awayTeam.losses AS awayLosses, awayTeam.abbr AS awayAbbr,
										homeTeam.team AS homeTeam, homeTeam.wins AS homeWins, homeTeam.losses AS homeLosses, homeTeam.abbr AS homeAbbr
									FROM
										games s
									LEFT JOIN ( SELECT * FROM teams ) awayTeam ON
										s.away = awayTeam.id
									LEFT JOIN ( SELECT * FROM teams ) homeTeam ON
										s.home = homeTeam.id
									WHERE
										s.id = ?
									ORDER BY
										s.date, s.id', $game, $gameid );
	}

	public function Load_Week_Teams( $week, $away, $home, &$game )
	{
		return $this->_db->single( 'SELECT * FROM games WHERE week = ? AND away = ? AND home = ?', $game, $week, $away, $home );
	}

	public function Exists( $gameid, $weekid, $home, $away )
	{
		$count = $this->_db->single( 'SELECT id FROM games WHERE id = ? AND week = ? AND ( ( away = ? AND home = ? ) OR ( away = ? AND home = ? ) )', $null, $gameid, $weekid, $home, $away, $away, $home );

		if ( !$count )
		{
			return false;
		}

		return true;
	}

	public function Exists_Week_Teams( $weekid, $homeid, $awayid, &$game )
	{
		$count = $this->_db->single( 'SELECT id FROM games WHERE week = ? AND home = ? AND away = ?', $game, $weekid, $homeid, $awayid );

		return ( $count ) ? true : false;
	}

	public function Create_Games()
	{
		$games		= array();
		$db_teams	= new Teams( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$url 		= sprintf( 'http://football.myfantasyleague.com/%d/export?TYPE=nflSchedule&W=', date( 'Y' ) );

		for ( $i = 1; $i <= 17; $i++ )
		{
			$xml = simplexml_load_file( sprintf( '%s%d', $url, $i ) );

			foreach ( $xml->matchup as $matchup )
			{
				$kickoff 	= ( int ) 		$matchup[ 'kickoff' ];
				$team1 		= ( string ) 	$matchup->team[ 0 ][ 'id' ];
				$team2 		= ( string ) 	$matchup->team[ 1 ][ 'id' ];

				if ( !$db_weeks->Load( $i, $null ) )
				{
					return $this->_Set_Error( sprintf( 'Failed to load week %d', $i ) );
				}

				if ( !$db_teams->Load_O_Abbr( $team1, $loaded_team1 ) || !$db_teams->Load_O_Abbr( $team2, $loaded_team2 ) )
				{
					return $this->_Set_Error( sprintf( 'Failed to load either %s or %s', $team1, $team2 ) );
				}

				if ( ( int ) $matchup->team[ 0 ][ 'isHome' ] )
				{
					$away = $loaded_team2[ 'id' ];
					$home = $loaded_team1[ 'id' ];
				}
				else
				{
					$away = $loaded_team1[ 'id' ];
					$home = $loaded_team2[ 'id' ];	
				}

				if ( $this->Exists_Week_Teams( $i, $home, $away, $null ) )
				{
					return $this->_Set_Error( sprintf( 'Game already exists: %s vs. %s for week %d', $team1, $team2, $i ) );
				}

				$gamedate = new DateTime();
				$gamedate->setTimestamp( $kickoff );
				array_push( $games, array( 'away' => $away, 'home' => $home, 'date' => $gamedate->format( 'Y-m-d H:i:s' ), 'week' => $i ) );
			}
		}

		foreach ( $games as $game )
		{
			if ( !$this->Insert( $game ) )
			{
				return false;
			}
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
