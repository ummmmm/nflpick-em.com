<?php

class DatabaseTableGames extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE games
				(
					id 			int( 3 ) AUTO_INCREMENT,
					away 		int( 2 ),
					home 		int( 2 ),
					stadium		varchar( 255 ),
					date 		int( 11 ),
					week 		int( 2 ),
					winner 		int( 2 ),
					loser 		int( 2 ),
					homeScore 	int( 2 ),
					awayScore 	int( 2 ),
					tied		boolean,
					final		boolean,
					PRIMARY KEY ( id )
				)";

		return $this->query( $sql );
	}

	public function List_Load( &$games )
	{
		return $this->select( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, s.stadium, s.tied, s.final,
										awayTeam.team AS awayTeam, awayTeam.wins AS awayWins, awayTeam.losses AS awayLosses, awayTeam.ties AS awayTies, awayTeam.abbr AS awayAbbr,
										homeTeam.team AS homeTeam, homeTeam.wins AS homeWins, homeTeam.losses AS homeLosses, homeTeam.ties AS homeTies, homeTeam.abbr AS homeAbbr
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
		return $this->select( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, s.stadium, s.tied, s.final,
										awayTeam.team AS awayTeam, awayTeam.wins AS awayWins, awayTeam.losses AS awayLosses, awayTeam.ties AS awayTies, awayTeam.abbr AS awayAbbr,
										homeTeam.team AS homeTeam, homeTeam.wins AS homeWins, homeTeam.losses AS homeLosses, homeTeam.ties AS homeTies, homeTeam.abbr AS homeAbbr
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
		$game[ 'tied' ]			= 0;
		$game[ 'final' ]		= 0;

		return $this->query( 'INSERT INTO games
							  ( away, home, stadium, date, week, winner, loser, homeScore, awayScore, tied, final )
							  VALUES
							  ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )',
							  $game[ 'away' ], $game[ 'home' ], $game[ 'stadium' ], $game[ 'date' ], $game[ 'week' ], $game[ 'winner' ], $game[ 'loser' ], $game[ 'homeScore' ], $game[ 'awayScore' ], $game[ 'tied' ], $game[ 'final' ] );
	}

	public function Update( $game )
	{
		return $this->query( 'UPDATE
									games
								   SET
									away 		= ?,
									home		= ?,
									stadium		= ?,
									date		= ?,
									week		= ?,
									winner		= ?,
									loser		= ?,
									homeScore	= ?,
									awayScore	= ?,
									tied		= ?,
									final		= ?
								   WHERE
									id = ?',
							$game[ 'away' ], $game[ 'home' ], $game[ 'stadium' ], $game[ 'date' ], $game[ 'week' ], $game[ 'winner' ], $game[ 'loser' ], $game[ 'homeScore' ], $game[ 'awayScore' ], $game[ 'tied' ], $game[ 'final' ],
							$game[ 'id' ] );
	}

	public function Load( $gameid, &$game )
	{
		return $this->single( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, s.stadium, s.tied, s.final,
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
		return $this->single( 'SELECT * FROM games WHERE week = ? AND away = ? AND home = ?', $game, $week, $away, $home );
	}

	public function Exists_Week_Teams( $weekid, $homeid, $awayid, &$game )
	{
		$count = $this->single( 'SELECT id FROM games WHERE week = ? AND home = ? AND away = ?', $game, $weekid, $homeid, $awayid );

		return ( $count ) ? true : false;
	}
}
