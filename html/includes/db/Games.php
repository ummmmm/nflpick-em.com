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

	public function Create_Games()
	{
		$games		= array();
		$db_teams	= $this->db()->teams();
		$db_weeks	= $this->db()->weeks();

		$null		= $db_weeks->List_Load( $weeks );
		$url		= 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?week=%d';

		foreach ( $weeks as $week )
		{
			$data = json_decode( file_get_contents( sprintf( $url, $week[ 'id' ] ) ) );

			foreach ( $data->events as $event )
			{
				$competition	= $event->competitions[ 0 ];
				$team1			= $competition->competitors[ 0 ];
				$team2			= $competition->competitors[ 1 ];

				if ( $team1->homeAway == 'home' )
				{
					$home = $team1;
					$away = $team2;
				}
				else
				{
					$home = $team2;
					$away = $team1;
				}

				$away_abbr	= $away->team->abbreviation;
				$home_abbr	= $home->team->abbreviation;
				$stadium	= $competition->venue->fullName;
				$date		= new DateTime( $event->date );

				if ( !$db_teams->Load_Abbr( $away_abbr, $away_team ) )
				{
					throw new NFLPickEmException( sprintf( 'Failed to load away team %s', $away_abbr ) );
				}

				if ( !$db_teams->Load_Abbr( $home_abbr, $home_team ) )
				{
					throw new NFLPickEmException( sprintf( 'Failed to load home team %s', $home_abbr ) );
				}

				if ( $this->Exists_Week_Teams( $week[ 'id' ], $home_team[ 'id' ], $away_team[ 'id' ], $null ) )
				{
					throw new NFLPickEmException( sprintf( 'Game already exists: %s vs. %s for week %d', $away_team[ 'team' ], $home_team[ 'team' ], $week[ 'id' ] ) );
				}

				array_push( $games, array( 'away' => $away_team[ 'id' ], 'home' => $home_team[ 'id' ], 'stadium' => $stadium, 'date' => $date->getTimestamp(), 'week' => $week[ 'id' ] ) );
			}
		}

		foreach ( $games as $game )
		{
			$this->Insert( $game );
		}

		return true;
	}
}
