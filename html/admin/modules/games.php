<?php
function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	$jquery = "$.fn.load_games();";	
	
	return true;
}

function Module_Content( &$db, &$user )
{
	$db_teams = new Teams( $db );
	$count = $db_teams->List_Load( $teams );
	
	if ( $count === false )
	{
		return false;
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

function EditGame( &$db, $game )
{
	$db_teams	= new Teams( $db );
	$count 		= $db_teams->List_Load( $teams );
	$gamedate 	= new DateTime( $game[ 'date' ] );
	$gamedate->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
	
	if ( $count === false )
	{
		return false;
	}
	
	$now = new DateTime( 'now', new DateTimeZone( 'America/Los_Angeles' ) );
	
	if ( $game[ 'winner' ] === 0 && $gamedate > $now )
	{
		$updateScores	= 'false';
		$style_game 	= 'block';
		$style_score	= 'none';
	} else {
		$updateScores	= 'true';
		$style_game		= 'none';
		$style_score	= 'block';
	}
	
	print '<form id="update_games" method="POST">';
	print '<fieldset>';
	print '<legend>Edit Game</legend>';
	print '<div id="game" style="display: ' . $style_game . ';">';
	print '<p><a href="javascript:;" onclick="$.fn.editGame();">Edit Game Scores</a></p>';
	print '<label for="teams">Away vs. Home</label>';
	print '<select id="away" name="away">';
		Draw::Teams( $teams, $game[ 'away' ] );
	print '</select>';
	print ' <b>vs.</b> ';	
	print '<select id="home" name="home">';
		Draw::Teams( $teams, $game[ 'home' ] );
	print '</select><br />';
	print '<label for="date">What\'s The Date</label>';
	print '<select id="month" name="month">';
		Draw::Months( $gamedate->format( 'm' ) );
	print '</select>';
	print '<select id="day" name="day">';
		Draw::Days( $gamedate->format( 'j' ) );
	print '</select>';
	print '<select id="year" name="year">';
		Draw::Years( $gamedate->format( 'Y' ) );
	print '</select>';
	print '<select id="hour" name="hour">';
		Draw::Hours( $gamedate->format( 'H' ) );
	print '</select>';
	print '<select id="minute" name="minute">';
		Draw::Minutes( $gamedate->format( 'i' ) );
	print '</select><br />';
	print '<label for="week">What Week</label>';
	print '<select id="week" name="week">';
		Draw::Weeks( $game[ 'week' ] );
	print '</select><br />';
	print '</div>';
	print '<div id="scores" style="display: ' . $style_score . ';">';
	print '<p><a href="javascript:;" onclick="$.fn.editGame();">Edit Game Info</a></p>';
	print '<label>What\'s The Score</label>';
	print '<b>' . $game[ 'awayTeam' ] . ':</b><input type="text" size="1" maxlength="2" id="awayScore" name="awayScore" value="' . htmlentities( $game[ 'awayScore' ] ) . '" />';
	print '<b>' . $game[ 'homeTeam' ] . ':</b><input type="text" size="1" maxlength="2" id="homeScore" name="homeScore" value="' . htmlentities( $game[ 'homeScore' ] ) . '" /><br />';
	print '</div>';
	print '<input type="submit" value="Update" />';
	print Draw::Hidden( 'updateScores', $updateScores );
	print Draw::Hidden( 'gameid', $game[ 'id' ] );
	print '</fieldset>';
	print '</form>';
	
	
	return true;
}
?>