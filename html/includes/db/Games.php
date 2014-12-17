<?php

class Games
{
	private $_db;

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

	public function List_Load( $week, &$games )
	{
		return $this->_db->select( 	'SELECT
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
									s.week = ?
								ORDER BY
									s.date, s.id',
								$games,
								$week );
	}

	public function Insert( &$game )
	{
		return $this->_db->insert( 'games', $game );
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
}
