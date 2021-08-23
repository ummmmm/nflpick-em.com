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

		return $this->_db->query( $sql );
	}

	public function List_Load( &$games )
	{
		return $this->_db->select( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, s.stadium,
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
		return $this->_db->select( 'SELECT
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, s.stadium,
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

		return $this->_db->insert( 'games', $game );
	}

	public function Update( $game )
	{
		return $this->_db->query( 'UPDATE
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
										s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, s.stadium,
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
					return $this->_Set_Error( sprintf( 'Failed to load away team %s', $away_abbr ) );
				}

				if ( !$db_teams->Load_Abbr( $home_abbr, $home_team ) )
				{
					return $this->_Set_Error( sprintf( 'Failed to load home team %s', $home_abbr ) );
				}

				if ( $this->Exists_Week_Teams( $week[ 'id' ], $home_team[ 'id' ], $away_team[ 'id' ], $null ) )
				{
					return $this->_Set_Error( sprintf( 'Game already exists: %s vs. %s for week %d', $away_team[ 'team' ], $home_team[ 'team' ], $week[ 'id' ] ) );
				}

				array_push( $games, array( 'away' => $away_team[ 'id' ], 'home' => $home_team[ 'id' ], 'stadium' => $stadium, 'date' => $date->getTimestamp(), 'week' => $week[ 'id' ] ) );
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
