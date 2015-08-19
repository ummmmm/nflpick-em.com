<?php

class Screen_MakePicks implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function requirements()
	{
		return array( "user" => true );
	}

	public function head()
	{
		$week_id	= Functions::Get( "week" );

		if ( !$week_id )
		{
			return true;
		}

		$db_weeks	= new Weeks( $this->_db );
		$now 		= new DateTime();
		$then		= new DateTime();
		$count		= $db_weeks->Load( $week_id, $loaded_week );

		if ( $count === false )	return $this->_screen->setDBError();
		else if ( $count == 0 )	return $this->_screen->setError( array( "#Error#", "Failed to load week" ) );

		$now->setTimezone( new DateTimeZone( 'UTC' ) );
		$then->setTimezone( new DateTimeZone( 'UTC' ) );
		$then->setTimestamp( $loaded_week[ 'date' ] );

		if ( $now > $then )
		{
			return true;
		}

		print <<<EOT
<script type="text/javascript">\n
	var now 		= new Date( {$now->format( 'Y, m, d, H, i, s' )} );
	var then 		= new Date( {$then->format( 'Y, m, d, H, i, s' )} );
	var interval	= setInterval( function()
	{
		var diff, seconds, minutes, hours, days;

		now.setUTCSeconds( now.getUTCSeconds() + 1 );
		diff	= ( then.getTime() - now.getTime() ) / 1000;
		seconds = Math.floor( diff % 60 );
		minutes = Math.floor( ( diff / 60 ) % 60 );
		hours 	= Math.floor( ( ( diff / 60 ) / 60 ) % 24 );
		days 	= Math.floor( ( ( diff / 60 ) / 60 ) / 24 );

		if ( days == 0 && hours == 0 && minutes == 0 && seconds == 0 )
		{
			$.fn.load_picks( {$week_id} );
			clearInterval( interval );
		}

		$( '#timeUntil' ).text( days + ' days ' + hours + ' hours ' + minutes + ' minutes ' + seconds + ' seconds' );
	}, 1000	 );
</script>
EOT;

		return true;
	}

	public function jquery()
	{
		$week_id 	= Functions::Get( "week" );

		if ( !$week_id )
		{
			return true;
		}

		$db_weeks	= new Weeks( $this->_db );
		$count		= $db_weeks->Load( $week_id, $loaded_week );

		if ( $count === false )	return $this->_screen->setDBError();
		else if ( $count == 0 )	return $this->_screen->setError( array( "#Error#", "Failed to load week" ) );

		printf( "$.fn.load_picks( %d );", $loaded_week[ 'id' ] );

		return true;
	}

	public function content()
	{
		$db_games	= new Games( $this->_db );
		$db_picks	= new Picks( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$weekid 	= Functions::Get( 'week' );

		if ( empty( $weekid ) )
		{
			return $this->_WeekList( $db_weeks );
		}

		$count = $db_games->List_Load_Week( $weekid, $games );

		foreach( $games as &$game )
		{
			$db_picks->Load_User_Game( $this->_auth->getUserID(), $game[ 'id' ], $game[ 'pick' ] );
		}

		return $this->_GameLayout( $weekid, $db_weeks );
	}

	private function _WeekList( &$db_weeks )
	{
		$count_weeks = $db_weeks->List_Load( $weeks );

		if ( $count_weeks === false )
		{
			return false;
		}

		if ( $count_weeks === 0 )
		{
			return Functions::Information( 'No Weeks Added', 'No weeks have been added yet.' );
		}

		print '<h1>Pick \'Em Weeks</h1>';

		foreach( $weeks as $week )
		{
			$locked = $week[ 'locked' ] ? ' - <b>Locked</b>' : '';
			print '<p><a href="?screen=make_picks&week=' . $week[ 'id' ] . '" title="Week ' . $week[ 'id' ] . '">Week ' . $week[ 'id' ] . '</a> ' . $locked . '</p>';
		}

		return true;
	}

	private function _GameLayout( $week, $db_weeks )
	{
		$db_games		= new Games( $this->_db );
		$db_picks		= new Picks( $this->_db );
		$games_count 	= $db_games->List_Load_Week( $week, $games );

		if ( $games_count === false )
		{
			return $this->_screen->setDBError();
		}

		if ( $games_count === 0 )
		{
			return Functions::Information( 'No games found', 'No games have been added for this week yet.' );
		}

		if ( !$db_weeks->Load( $week, $loaded_week ) )
		{
			return $this->_screen->setDBError();
		}

		$remaining = $db_picks->Remaining( $this->_auth->getUserID(), $week );
		$timeUntil = Functions::TimeUntil( $loaded_week[ 'date' ] );

		$now 		= new DateTime();
		$then		= new DateTime();
		$then->setTimestamp( $loaded_week[ 'date' ] );
		$time_left	= '';

		if ( $now < $then )
		{
			$time_left .= '- You can still change your picks by clicking the team names.<br />';
			$time_left .= '- You have <span id="timeUntil">' . $timeUntil . '</span> until all your picks are due.<br />';
			$time_left .= '- You have <span id="remainingPicks">' . $remaining .'</span> picks remaining.</p>';
			$time_left .= '<p><input type="button" onclick="$.fn.emailPicks( ' . $week . ' )" value="Email Picks" /></p>';
			$time_left .= '<div id="jquery-picks_emailSent" style="display: none;">&nbsp;</div>';
		}

		print <<<EOT
			<h1>Notes</h1>
			<p><b>Left</b> team is the visiting team. <b>Right</b> team is the home team. <br />
			- Games in light red you have yet to pick who you want. <br />
			- Games in light yellow you have chosen who you want. <br />
			- Games in light grey have already been played, and you can no longer change. <br />
			{$time_left}
EOT;

		print '<div id="picks_loading">Loading...</div>';

		return true;


		$gameDays = array();

		foreach( $games as $game )
		{
			$day = new DateTime( $game[ 'date' ], new DateTimezone( 'America/Los_Angeles' ) );

			if ( !array_key_exists( $day->format( 'l' ), $gameDays ) )
			{
				$gameDays[ $day->format( 'l' ) ] = array();
			}

			array_push( $gameDays[ $day->format( 'l' ) ], $game );
		}

		foreach( $gameDays as $day => $games )
		{
			$gameDate = new DateTime( $games[ 0 ][ 'date' ], new DateTimezone( 'America/Los_Angeles' ) );

			print '<h1>' . $day . '<br /><span class="record">' . $gameDate->format( 'F d, Y' ) . '</span></h1>';

			foreach( $games as $game )
			{
				$gameTime 	= new DateTime( $game[ 'date' ], new DateTimezone( 'America/Los_Angeles' ) );
				$now 		= new DateTime();
				$now->setTimezone( new DateTimezone( 'America/Los_Angeles' ) );

				if ( !$db_picks->Load_User_Game( $this->_auth->getUserID(), $game[ 'id' ], $pick ) )
				{
					$display 	= 'none';
					$text		= '&nbsp;';
					$class		= 'notMade';
				} else {
					$class		= 'made';
					$display	= 'block';
					$winnerPick	= ( $pick[ 'winner_pick' ] == $game[ 'home' ] ) ? $game[ 'homeTeam' ] : $game[ 'awayTeam' ];
					$loserPick	= ( $pick[ 'loser_pick' ] == $game[ 'home' ] ) ? $game[ 'homeTeam' ] : $game[ 'awayTeam' ];
					$text 		= "You have picked the <b>{$winnerPick}</b> to beat the <b>{$loserPick}</b>";
				}

				$class = ( $now > $gameTime || $loaded_week[ 'locked' ] ) ? 'past' : $class;

				print <<<EOT
					<a name="pick{$game[ 'id' ]}"></a>
					<div id="notMade{$game[ 'id' ]}" class="make_picks {$class}">
					<a onclick="$.fn.makePicks( {$week}, {$game[ 'id' ]}, {$game[ 'away' ]}, {$game[ 'home' ]} );" href="javascript:;">{$game[ 'awayTeam' ]}</a>
					<span class="record">({$game[ 'awayWins' ]} - {$game[ 'awayLosses' ]})</span>
					<strong>vs.</strong>
					<a onclick="$.fn.makePicks( {$week}, {$game[ 'id' ]}, {$game[ 'home' ]}, {$game[ 'away' ]} );" href="javascript:;">{$game[ 'homeTeam' ]}</a>
					<span class="record">({$game[ 'homeWins' ]} - {$game[ 'homeLosses' ]})</span>
					<br />{$game[ 'stadium' ]} - {$gameTime->format( 'h:i a' ) }<br />
					<div id="status{$game[ 'id' ] }" style="display:{$display};">{$text}</div>
					</div>
EOT;
			}
		}

		return true;
	}
}
