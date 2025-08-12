<?php

class Screen_Games extends Screen_Admin
{
	public function jquery()
	{
		print "$.fn.load_games();";

		return true;
	}

	public function content()
	{
		$this->db()->teams()->List_Load( $teams );
		$this->db()->weeks()->List_Load( $weeks );
		
		print '<h1>Edit Weeks</h1>';
		print '<div id="weeks_loading">Loading...</div>';
		print '<div id="games_addedit">';
		print '<div id="games_addedit_dialog">Edit Game</div>';
		print '<a href="javascript:;" id="games_addedit_switch">Switch View</a>';
		print '<div id="games">';
		print '<table>';
		print '<tr>';
		print '<td>';
		print '<select id="games_addedit_away" name="away">';
			foreach ( $teams as $team )
			{
				printf( '<option value="%d">%s</option>', $team[ 'id'], $team[ 'team' ] );
			}
		print '</select>';
		print ' <b>vs.</b> ';	
		print '<select id="games_addedit_home" name="home">';
			foreach ( $teams as $team )
			{
				printf( '<option value="%d">%s</option>', $team[ 'id'], $team[ 'team' ] );
			}
		print '</select>';
		print '</td>';
		print '<tr>';
		print '<td>';
		print '<select id="games_addedit_month" name="month">';
			for ( $i = 1; $i <= 12; $i++ )
			{
				printf( '<option value="%d">%s</option>', $i, date( 'F', mktime( 0, 0, 0, $i ) ) );
			}
		print '</select>';
		print '<select id="games_addedit_day" name="day">';
			for ( $i = 1; $i <= 31; $i++ )
			{
				printf( '<option value="%d">%s</option>', $i, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
			}
		print '</select>';
		print '<select id="games_addedit_year" name="year">';
			$year = ( int ) date( 'Y' );

			for ( $i = $year; $i <= $year + 1; $i++ )
			{
				printf( '<option value="%d">%s</option>', $i, $i );
			}
		print '</select>';
		print '<select id="games_addedit_hour" name="hour">';
			for ( $i = 0; $i <= 23; $i++ )
			{
				printf( '<option value="%d">%s</option>', $i, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
			}
		print '</select>';
		print '<select id="games_addedit_minute" name="minute">';
			for ( $i = 0; $i <= 55; $i += 5 )
			{
				printf( '<option value="%d">%s</option>', $i, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
			}
		print '</select>';
		print '</td>';
		print '</tr>';
		print '<tr>';
		print '<td>';
		print '<select id="games_addedit_week" name="week">';
			foreach ( $weeks as $week )
			{
				printf( '<option value="%d">%s</option>', $week[ 'id'], $week[ 'id' ] );
			}
		print '</select>';
		print '</td>';
		print '</tr>';
		print '</table>';
		print '</div>';
		print '<div id="scores">
				<table>
				<tr>
					<td><b></b>:</td>
					<td><input type="text" maxlength="2" size="2" id="games_addedit_away_score" name="awayScore" /></td>
				</tr>
				<tr>
					<td><b></b>:</td>
					<td><input type="text" maxlength="2" size="2" id="games_addedit_home_score" name="homeScore" /></td>
				</tr>
				</table>
				</div>';
		print '<input type="hidden" id="scored" name="name" value="0" />';
		print '<div class="buttons_left">
			<input type="button" id="games_addedit_cancel" value="Cancel" />
		</div>
		<div class="buttons_right">
			<input type="button" id="games_addedit_update" value="Update Game" />
		</div>';
		print '</div>';
		
		return true;
	}
}
