<?php

class Screen_ViewPicks extends Screen_User
{
	public function content()
	{
		$db_weeks	= $this->db()->weeks();
		$week_id 	= Functions::Get( 'week' );

		if ( $week_id === '' )
		{
			return $this->_WeekList( $db_weeks );
		}

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			return Functions::Information( 'Error', 'Invalid week.' );
		}

		if ( !$week[ 'locked' ] )
		{
			return Functions::Information( 'Error', 'Week ' . htmlentities( $week_id ) . ' is not locked yet.' );
		}

		return $this->_PickLayout( $week );
	}

	private function _WeekList( &$db_weeks )
	{
		if ( !$db_weeks->List_Load( $weeks ) )
		{
			return false;
		}

		print( "<h1>Pick 'Em Weeks</h1>" );

		foreach( $weeks as $week )
		{
			$locked = $week[ 'locked' ] ? '- <b>Locked</b>' : '';
			printf( '<p><a href="?screen=view_picks&week=%d" title="Week %d">Week %d</a> %s</p>', $week[ 'id' ], $week[ 'id' ], $week[ 'id' ], $locked );
		}

		return true;
	}

	private function _PickLayout( &$week )
	{
		$week_id			= $week[ 'id' ];
		$db_users			= $this->db()->users();
		$db_games 			= $this->db()->games();
		$db_picks 			= $this->db()->picks();
		$db_weeks			= $this->db()->weeks();
		$db_weekly_records	= $this->db()->weeklyrecords();

		if ( !$this->_Users_List_Load( $users ) )
		{
			return false;
		}

		$games_count = $db_games->List_Load_Week( $week_id, $games );

		if ( $games_count === false )
		{
			return false;
		}

		$previous_week_result	= $db_weeks->Load( $week[ 'id' ] - 1, $previous_week );
		$next_week_result		= $db_weeks->Load( $week[ 'id' ] + 1, $next_week );

		print( '<h1><div style="text-align:center;">' );

		if ( $previous_week_result )
		{
			printf( '<a href="?screen=view_picks&week=%d" title="Week %d">&#171; Week %d</a> | ', $previous_week[ 'id' ], $previous_week[ 'id' ], $previous_week[ 'id' ] );
		}

		printf( 'Week %d', $week[ 'id' ] );

		if ( $next_week_result && $next_week[ 'locked' ] == 1 )
		{
			printf( ' | <a href="?screen=view_picks&week=%d" title="Week %d">Week %d &#187; </a>', $next_week[ 'id' ], $next_week[ 'id' ], $next_week[ 'id' ] );
		}

		print( '</div></h1>' );

		printf( '<h1>Week %d User Picks</h1>', $week_id );
		print( '<table class="picks" style="font-size:8px;" width="100%">' );

		foreach( $users as $loaded_user )
		{
			$initials 		= strtoupper( sprintf( "%s.%s.", substr( $loaded_user[ 'fname' ], 0, 1 ), substr( $loaded_user[ 'lname' ], 0, 1 ) ) );
			$missing_count 	= $db_picks->Missing( $loaded_user[ 'id' ], $week_id );

			if ( $missing_count === false )
			{
				return false;
			}

			if ( !$db_weekly_records->Load_User_Week( $loaded_user[ 'id' ], $week_id, $weekly_record ) )
			{
				return $this->setError( 'Unable to load weekly records' );
			}

			print '<tr>';
			print '<td class="picks">';
			printf( '<a href="javascript:;" title="%s" onclick="$.fn.highlightPicks( %d, %d );">%s</a>', htmlentities( $loaded_user[ 'name' ] ), $loaded_user[ 'id' ], $week_id, htmlentities( $initials ) );
			print '</td>';

			foreach( $games as $game )
			{
				$result = $db_picks->Load_User_Game( $loaded_user[ 'id' ], $game[ 'id' ], $pick );

				if ( $result === false )
				{
					return $this->setDBError();
				}

				if ( count( $pick ) === 0 )
				{
					$output = '<span style="color:red;">N/A</span>';
				}
				else if ( $game[ 'final' ] )
				{
					if ( $game[ 'tied' ] )
					{
						$output = sprintf( '<u>%s</u>', ( $pick[ 'winner_pick' ] == $game[ 'home' ] ) ? $game[ 'homeAbbr' ] : $game[ 'awayAbbr' ] );
					}
					else if ( $pick[ 'winner_pick' ] == $game[ 'home' ] )
					{
						if ( $pick[ 'winner_pick' ] == $game[ 'winner' ] )
						{
							$output = sprintf( '<b>%s</b>', htmlentities( $game[ 'homeAbbr' ] ) );
						}
						else
						{
							$output = sprintf( '<i>%s</i>', htmlentities( $game[ 'homeAbbr' ] ) );
						}
					}
					else
					{
						if ( $pick[ 'winner_pick' ] == $game[ 'winner' ] )
						{
							$output = sprintf( '<b>%s</b>', htmlentities( $game[ 'awayAbbr' ] ) );
						}
						else
						{
							$output = sprintf( '<i>%s</i>', htmlentities( $game[ 'awayAbbr' ] ) );
						}
					}
				}
				else
				{
					$output = htmlentities( $pick[ 'winner_pick' ] == $game[ 'home' ] ? $game[ 'homeAbbr' ] : $game[ 'awayAbbr' ] );
				}

				printf( '<td userid="%d" gameid="%d">%s</td>', $loaded_user[ 'id' ], $game[ 'id' ], $output );
			}

			if ( $missing_count == 0 ) // no missing picks, the weely record is correct
			{
				printf( '<td><b>%d - %d</b></td>', $weekly_record[ 'wins' ], $weekly_record[ 'losses' ] );
			}
			else if ( $weekly_record[ 'manual' ] == 1 ) // missing picks and the weekly record was manually set
			{
				printf( '<td style="color:red;"><b>%d - %d</b></td>', $weekly_record[ 'wins' ], $weekly_record[ 'losses' ] );
			}
			else // missing picks and the weekly record has not been manually set yet
			{
				printf( '<td style="color:red;"><b>%d - %d (TBD)</b></td>', $weekly_record[ 'wins' ], $weekly_record[ 'losses' ] );
			}

			print '</tr>';
		}

		print '</table>';
		printf( '<p><a href="javascript:;" id="highlightpicks" onclick="$.fn.highlightPicks( 0, %d );">Highlighting On</a></p>', $week_id );
		print( <<<EOD
			<table cellspacing="5">
				<tr>
					<td><h3>Key</h3></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><b>LAC</b></td>
					<td>Game won</td>
				</tr>
				<tr>
					<td><i>LAC</i></td>
					<td>Game lost</td>
				</tr>
				<tr>
					<td>LAC</td>
					<td>Game not yet final</td>
				</tr>
				<tr>
					<td><u>LAC</u></td>
					<td>Game tied</td>
				</tr>
				<tr>
					<td><span style="color:red;">N/A</td>
					<td>Pick not submitted</td>
				</tr>
				<tr>
					<td><b>11-5</b></td>
					<td>Number of wins and losses (all picks submitted)</td>
				</tr>
				<tr>
					<td><span style="color:red;"><b>11-5</b></style></td>
					<td>Number of wins and losses (with picks missing and the final record has been manually calculated)</td>
				</tr>
				<tr>
					<td><span style="color:red;"><b>9-5 (TBD)</b></style></td>
					<td>Number of wins and losses (with picks missing and final record yet to be manually calculated)</td>
				</tr>
			</table>
			<br />
			<p>Manually calculated records will not happen until ALL games are final for the week.</p>
EOD );

		return true;
	}

	private function _Users_List_Load( &$users )
	{
		return $this->db()->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name, CONCAT( SUBSTRING( fname, 1, 1 ), \'.\', SUBSTRING( lname, 1, 1 ), \'.\' ) AS abbr FROM users ORDER BY abbr, fname, lname, id', $users );
	}
}
