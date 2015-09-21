<?php

class Screen_ViewPicks implements iScreen
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

	public function content()
	{
		$db_weeks	= new Weeks( $this->_db );
		$weekid 	= Functions::Get( 'week' );

		if ( $weekid === '' )
		{
			return $this->_WeekList( $db_weeks );
		}

		if ( !Validation::Week( $weekid ) )
		{
			return Functions::Information( 'Error', 'Invalid week.' );
		}

		if ( !$db_weeks->IsLocked( $weekid ) )
		{
			return Functions::Information( 'Error', 'Week ' . htmlentities( $weekid ) . ' is not locked yet.' );
		}

		return $this->_PickLayout( $weekid );
	}

	private function _WeekList( &$db_weeks )
	{
		print '<h1>Pick \'Em Weeks</h1>';
		if ( !$db_weeks->List_Load( $weeks ) )
		{
			return false;
		}

		foreach( $weeks as $week )
		{
			$locked = $week[ 'locked' ] ? '- <b>Locked</b>' : '';
			printf( '<p><a href="?screen=view_picks&week=%d" title="Week %d">Week %d</a> %s</p>', $week[ 'id' ], $week[ 'id' ], $week[ 'id' ], $locked );
		}

		return true;
	}

	private function _PickLayout( $weekid )
	{
		$db_users	= new Users( $this->_db );
		$db_games 	= new Games( $this->_db );
		$db_picks 	= new Picks( $this->_db );

		if ( $db_users->List_Load( $users ) === false )
		{
			return false;
		}

		$games_count = $db_games->List_Load_Week( $weekid, $games );

		if ( $games_count === false )
		{
			return false;
		}

		print "<h1>Week {$weekid} User Picks</h1>";
		print '<table class="picks" style="font-size:8px;" width="100%">';

		$user_records = array();

		foreach( $users as $loaded_user )
		{
			$initials 								= strtoupper( sprintf( "%s.%s.", substr( $loaded_user[ 'fname' ], 0, 1 ), substr( $loaded_user[ 'lname' ], 0, 1 ) ) );
			$user_records[ $loaded_user[ 'id' ] ] 	= array( 'losses' => 0, 'wins' => 0 );
			$missing_count 							= $db_picks->Missing( $loaded_user[ 'id' ], $weekid );

			if ( $missing_count === false )
			{
				return false;
			}

			print '<tr>';
			print '<td class="picks">';
			printf( '<a href="javascript:;" title="%s" onclick="$.fn.highlightPicks( %d, %d );">%s</a>', htmlentities( $loaded_user[ 'name' ] ), $loaded_user[ 'id' ], $weekid, htmlentities( $initials ) );
			print '</td>';

			foreach( $games as $game )
			{
				if ( !$db_picks->Load_User_Game( $loaded_user[ 'id' ], $game[ 'id' ], $pick ) )
				{
					return $this->_screen->setDBError();
				}

				if ( $pick[ 'picked' ] == 0 )
				{
					$team = '<span style="color:red;">N/A</span>';
				}
				else if ( $pick[ 'winner_pick' ] == $game[ 'away' ] )
				{
					$team = $game[ 'awayAbbr' ];
				}
				else
				{
					$team = $game[ 'homeAbbr' ];
				}

				if ( $pick[ 'winner_pick' ] == $game[ 'winner' ] )
				{
					$user_records[ $loaded_user[ 'id' ] ][ 'wins' ] += 1;
				}
				else if ( $game[ 'winner' ] != 0 )
				{
					$user_records[ $loaded_user[ 'id' ] ][ 'losses' ] += 1;
				}

				$output = ( $pick[ 'winner_pick' ] == $game[ 'winner' ] && $game[ 'winner' ] != 0 ) ? sprintf( "<b>%s</b>", htmlentities( $team ) ) : $team;

				printf( '<td userid="%d" gameid="%d">%s</td>', $loaded_user[ 'id' ], $game[ 'id' ], $output );
			}

			if ( $missing_count == $games_count )
			{
				if ( !Functions::Worst_Record_Calculated( $this->_db, $weekid, $user_records[ $loaded_user[ 'id' ] ] ) )
				{
					return false;
				}
			}

			print '<td><b>' . $user_records[ $loaded_user[ 'id' ] ][ 'wins' ] . ' - ' . $user_records[ $loaded_user[ 'id' ] ][ 'losses' ] . '</b></td>';

			print '</tr>';
		}

		print '</table>';
		print '<p><a href="javascript:;" id="highlightpicks" onclick="$.fn.highlightPicks( 0, ' . $weekid . ');">Highlighting On</a></p>';

		return true;
	}
}
