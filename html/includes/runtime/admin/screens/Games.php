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
		$db_teams	= $this->db()->games();
		$count		= $db_teams->List_Load( $teams );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
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
			Draw::Teams( $teams );
		print '</select>';
		print ' <b>vs.</b> ';	
		print '<select id="games_addedit_home" name="home">';
			Draw::Teams( $teams );
		print '</select>';
		print '</td>';
		print '<tr>';
		print '<td>';
		print '<select id="games_addedit_month" name="month">';
			Draw::Months();
		print '</select>';
		print '<select id="games_addedit_day" name="day">';
			Draw::Days();
		print '</select>';
		print '<select id="games_addedit_year" name="year">';
			Draw::Years();
		print '</select>';
		print '<select id="games_addedit_hour" name="hour">';
			Draw::Hours();
		print '</select>';
		print '<select id="games_addedit_minute" name="minute">';
			Draw::Minutes();
		print '</select>';
		print '</td>';
		print '</tr>';
		print '<tr>';
		print '<td>';
		print '<select id="games_addedit_week" name="week">';
			Draw::Weeks();
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
		print Draw::Hidden( 'scored', 'false' );
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
