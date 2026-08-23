<?php
require_once( 'Database.php' );
require_once( 'functions.php' );

class API
{
	private const API_BASE_URL		= 'https://site.api.espn.com/apis/site/v2/sports/football/nfl';
	private const API_USER_AGENT	= 'curl/8.7.1';

	private $_db_manager;

	public function __construct( $db_manager )
	{
		$this->_db_manager = $db_manager;
	}

	private function db()
	{
		return $this->_db_manager;
	}

	private function get_data( $path )
	{
		return json_decode( file_get_contents( sprintf( '%s/%s', self::API_BASE_URL, $path ), context: stream_context_create( [ 'http' => [ 'method' => 'GET', 'user_agent' => 'curl/8.7.1' ] ] ) ) );
	}

	public function create_weeks()
	{
		$data			= $this->get_data( 'scoreboard?week=1' );
		$timezone		= new DateTimeZone('America/Los_Angeles');
		$week_date		= null;

		foreach ( $data->events as $event )
		{
			$date = new DateTime( $event->date );
			$date->setTimezone( $timezone );

			if ( $date->format( 'N' ) === '7' )
			{
				$date		= $date->setTime( 10, 0, 0 );
				$week_date	= $date->getTimestamp();

				break;
			}
		}

		if ( $week_date == null )
		{
			throw new NFLPickEmException( 'Failed to determine week 1 date' );
		}

		$data = $this->get_data( 'scoreboard?week=0' );

		foreach ( $data->leagues[ 0 ]->calendar as $entry )
		{
			if ( $entry->label == 'Regular Season' )
			{
				foreach ( $entry->entries as $entry )
				{
					$week = array( 'id' => $entry->value, 'date' => $week_date, 'locked' => 0 );
					$this->db()->weeks()->Insert( $week );

					$week_date = strtotime( '+1 week', $week_date );
				}

				break;
			}
		}

		if ( $this->db()->weeks()->List_Load( $null ) == 0 )
		{
			throw new NFLPickEmException( 'Failed to create the weeks data' );
		}
	}

	public function create_teams()
	{
		$teams	= array();
		$data	= $this->get_data( 'teams?limit=100' );

		foreach ( $data->sports[ 0 ]->leagues[ 0 ]->teams as $entry )
		{
			$team_data = $this->get_data( sprintf( 'teams/%d', $entry->team->id ) );

			$this->db()->teams()->Insert( array( 'team' => $team_data->team->displayName, 'abbr' => $team_data->team->abbreviation ) );
		}

		if ( $this->db()->teams()->List_Load( $null ) == 0 )
		{
			throw new NFLPickEmException( 'Failed to create the teams data' );
		}
	}

	public function create_games()
	{
		$db_teams = $this->db()->teams();
		$db_weeks = $this->db()->weeks();

		$db_weeks->List_Load( $weeks );

		foreach ( $weeks as $week )
		{
			$data = $this->get_data( sprintf( 'scoreboard?week=%d', $week[ 'id' ] ) );

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

				if ( $this->db()->games()->Exists_Week_Teams( $week[ 'id' ], $home_team[ 'id' ], $away_team[ 'id' ], $null ) )
				{
					throw new NFLPickEmException( sprintf( 'Game already exists: %s vs. %s for week %d', $away_team[ 'team' ], $home_team[ 'team' ], $week[ 'id' ] ) );
				}

				$game = array( 'away' => $away_team[ 'id' ], 'home' => $home_team[ 'id' ], 'stadium' => $stadium, 'date' => $date->getTimestamp(), 'week' => $week[ 'id' ] );
				$this->db()->games()->Insert( $game );
			}
		}

		if ( $this->db()->teams()->List_Load( $null ) == 0 )
		{
			throw new NFLPickEmException( 'Failed to create the games data' );
		}
	}

	public function update_scores()
	{
		$db_games	= $this->db()->games();
		$db_teams	= $this->db()->teams();
		$db_users	= $this->db()->users();
		$db_weeks	= $this->db()->weeks();
		$data		= $this->get_data( sprintf( 'scoreboard?week=%d', $db_weeks->Previous() ) );
		$week_id	= $data->week->number;

		if ( !$db_weeks->IsLocked( $week_id ) )
		{
			printf( 'Week %d is not locked yet, no scores updated', $week_id );

			return false;
		}

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

			if ( !$db_teams->Load_Abbr( $home->team->abbreviation, $homeTeam ) ||
				 !$db_teams->Load_Abbr( $away->team->abbreviation, $awayTeam ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the teams could not be loaded<br />', htmlentities( $away->team->displayName ), htmlentities( $home->team->displayName ) );
				continue;
			}

			if ( !$db_games->Load_Week_Teams( $week_id, $awayTeam[ 'id' ], $homeTeam[ 'id' ], $game ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game could not be found<br />', htmlentities( $awayTeam[ 'team' ] ), htmlentities( $homeTeam[ 'team' ] ) );
				continue;
			}

			if ( !$competition->status->type->completed )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game is not over yet<br />', htmlentities( $awayTeam[ 'team' ] ), htmlentities( $homeTeam[ 'team' ] ) );
				continue;
			}

			$away_score = $away->score;
			$home_score = $home->score;

			if ( $home_score == $away_score )
			{
				$game[ 'tied' ]		= 1;
				$game[ 'winner' ]	= 0;
				$game[ 'loser' ]	= 0;
			}
			else
			{
				$game[ 'tied' ]		= 0;
				$game[ 'winner' ] 	= ( $home_score > $away_score ) ? $homeTeam[ 'id' ] : $awayTeam[ 'id' ];
				$game[ 'loser' ]	= ( $home_score > $away_score ) ? $awayTeam[ 'id' ] : $homeTeam[ 'id' ];
			}

			$game[ 'homeScore' ]	= $home_score;
			$game[ 'awayScore' ]	= $away_score;
			$game[ 'final' ]		= 1;

			$db_games->Update( $game );
		}

		$db_teams->Recalculate_Records();

		if ( !Functions::Update_Records( $this->db() ) )
		{
			throw new NFLPickEmException( 'Failed to update weekly / user records' );
		}

		printf( '<p><b>Games Updated</b></p>' );

		return true;
	}

	public function update_game_times()
	{
		$this->db()->weeks()->List_Load( $weeks );

		foreach ( $weeks as $week )
		{
			$data = $this->get_data( sprintf( 'scoreboard?week=%d', $week[ 'id' ] ) );

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
				$date		= new DateTime( $event->date );

				if ( !$this->db()->teams()->Load_Abbr( $away_abbr, $away_team ) )
				{
					throw new NFLPickEmException( sprintf( 'Failed to load away team %s', $away_abbr ) );
				}

				if ( !$this->db()->teams()->Load_Abbr( $home_abbr, $home_team ) )
				{
					throw new NFLPickEmException( sprintf( 'Failed to load home team %s', $home_abbr ) );
				}

				if ( !$this->db()->games()->Load_Week_Teams( $week[ 'id' ], $away_team[ 'id' ], $home_team[ 'id' ], $game ) )
				{
					throw new NFLPickEmException( sprintf( 'Failed to find game %s vs. %s for week %d', $away_team[ 'team' ], $home_team[ 'team' ], $week[ 'id' ] ) );
				}

				if ( $game[ 'date' ] != $date->getTimestamp() )
				{
					$game[ 'date' ] = $date->getTimestamp();
					$this->db()->games()->Update( $game );
				}
			}
		}
	}
}
