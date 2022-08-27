<?php

class Screen_MakePicks extends Screen_User
{
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

		if ( $count === false )	return $this->setDBError();
		else if ( $count == 0 )	return $this->setError( array( "#Error#", "Failed to load week" ) );

		$then->setTimestamp( $loaded_week[ 'date' ] );

		if ( $now > $then )
		{
			return true;
		}

		print <<<EOT
<script type="text/javascript">\n
	var now 		= new Date( {$now->getTimestamp()} * 1000 + 1 ); // add one second since the interval doesn't fire for 1 second
	var then 		= new Date( {$then->getTimestamp()} * 1000 );
	var interval	= setInterval( function()
	{
		var diff, seconds, minutes, hours, days;

		now.setSeconds( now.getSeconds() + 1 );
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

		if ( $count === false )	return $this->setDBError();
		else if ( $count == 0 )	return $this->setError( array( "#Error#", "Failed to load week" ) );

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
			return $this->setDBError();
		}

		if ( $games_count === 0 )
		{
			return Functions::Information( 'No games found', 'No games have been added for this week yet.' );
		}

		if ( !$db_weeks->Load( $week, $loaded_week ) )
		{
			return $this->setDBError();
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


		$previous_week_result	= $db_weeks->Load( $loaded_week[ 'id' ] - 1, $previous_week );
		$next_week_result		= $db_weeks->Load( $loaded_week[ 'id' ] + 1, $next_week );

		print( '<h1><div style="text-align:center;">' );

		if ( $previous_week_result )
		{
			printf( '<a href="?screen=make_picks&week=%d" title="Week %d">&#171; Week %d</a> | ', $previous_week[ 'id' ], $previous_week[ 'id' ], $previous_week[ 'id' ] );
		}

		printf( 'Week %d', $loaded_week[ 'id'] );

		if ( $next_week_result )
		{
			printf( ' | <a href="?screen=make_picks&week=%d" title="Week %d">Week %d &#187; </a>', $next_week[ 'id' ], $next_week[ 'id' ], $next_week[ 'id' ] );
		}

		print( '</div></h1>' );

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
	}
}
