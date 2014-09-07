<?php
class Draw
{
	public static function Hidden( $name, $value )
	{
		print '<input type="hidden" id="' . htmlentities( $name ) . '" name="' . htmlentities( $name ) . '" value="' . htmlentities( $value ) . '" />';
	}

	public static function Radio( $name, $value, $default, $prompt = '' )
	{
		if ( $value === $default )
		{
			print '<input type="radio" name="' . htmlentities( $name ) . '" value="' . htmlentities( $value ) . '" checked /> '. $prompt;
		} else {
			print '<input type="radio" name="' . htmlentities( $name ) . '" value="' . htmlentities( $value ) . '" /> '. $prompt;
		}
	}

	public static function Option( $value, $default, $text )
	{
		$output = '<option value="' . htmlentities( $value ) . '"';
		if ( $value == $default )
		{
			$output .= ' selected';
		}

		print $output . '>' . $text . '</option>';
	}

	public static function Months( $default = null )
	{
		for( $i = 1; $i <= 12; $i++ )
		{
			Draw::Option( $i, $default, date( 'F', mktime( 0, 0, 0, $i ) ) );
		}
	}

	public static function Days( $default = null )
	{
		for( $i = 1; $i <= 31; $i++ )
		{
			Draw::Option( $i, $default, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
		}
	}

	public static function Years( $default = null )
	{
		$year = ( int ) date( 'Y' );

		for( $i = $year; $i <= $year + 1; $i++ )
		{
			Draw::Option( $i, $default, $i );
		}
	}

	public static function Hours( $default = null )
	{
		for( $i = 0; $i <= 23; $i++ )
		{
			Draw::Option( $i, $default, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
		}
	}

	public static function Minutes( $default = null )
	{
		for( $i = 0; $i <= 59; $i++ )
		{
			Draw::Option( $i, $default, str_pad( $i, 2, '0', STR_PAD_LEFT ) );
		}
	}

	public static function Teams( $teams, $default = null )
	{
		foreach( $teams as $team )
		{
			Draw::Option( $team[ 'id' ], $default, $team[ 'team' ] );
		}
	}

	public static function Weeks( $default = null )
	{
		for( $i = 1; $i <= 17; $i++ )
		{
			Draw::Option( $i, $default, $i );
		}
	}
}

class Users
{
	public static function List_Load( &$db, &$users )
	{
		return $db->select( 'SELECT * FROM users', $users );
	}

	public static function Recalculate_Records( &$db )
	{
		$query = $db->query( 'UPDATE
								users u
							  SET
								u.wins		= ( SELECT COUNT( p.id ) FROM picks p, games g WHERE p.game_id = g.id AND p.winner_pick = g.winner AND p.user_id = u.id AND g.winner != 0 ),
								u.losses	= ( SELECT COUNT( g.id ) FROM picks p, games g WHERE p.game_id = g.id AND p.loser_pick = g.winner AND p.user_id = u.id AND g.winner != 0 )' );

		if ( !$query )
		{
			return false;
		}

		if ( Users::List_Load( $db, $users ) === false )
		{
			return false;
		}

		if ( !Functions::Fix_User_Records( $db, $users ) )
		{
			return false;
		}

		foreach( $users as $user )
		{
			if ( !$db->single( 'SELECT COUNT( id ) + 1 AS place FROM users WHERE wins > ?', $current, $user[ 'wins' ] ) )
			{
				return false;
			}

			if ( !$db->query( 'UPDATE users SET current_place = ? WHERE id = ?', $current[ 'place' ], $user[ 'id' ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public static function Update_Record( &$db, $userid, $wins, $losses )
	{
		return $db->query( 'UPDATE
								users
							SET
								wins 	= ?,
								losses 	= ?
							WHERE
								id = ?', $wins, $losses, $userid );
	}
}

class Settings
{
	public static function Create( &$db )
	{
		$sql = "CREATE TABLE settings
				(
					poll_options 		tinyint( 3 ),
					email_validation 	tinyint( 1 ),
					registration 		tinyint( 1 ),
					max_news 			tinyint(3),
					domain_url 			char( 255 ),
					domain_email 		char( 255 ),
					online 				int( 11 ),
					site_title 			char( 255 ),
					login_sleep 		int( 11 ),
					PRIMARY KEY ( registration )
				)";

		if ( $db->query( $sql ) === false )
		{
			return false;
		}

		return $db->query( 'INSERT INTO settings
							( poll_options, email_validation, registration, max_news, domain_url, domain_email, online, site_title, login_sleep )
							VALUES
							( ?, ?, ?, ?, ?, ?, ?, ?, ? )',
							10, 0, 0, 4, 'http://www.nflpick-em.com', 'davidcarver88@gmail.com', 30, 'NFL Pick-Em 2014', 3000 );
	}

	public static function Load( &$db, &$settings )
	{
		return $db->single( 'SELECT * FROM settings', $settings );
	}

	public static function Update( &$db, &$settings )
	{
		return $db->query( 'UPDATE
								settings
							SET
								poll_options		= ?,
								email_validation	= ?,
								registration		= ?,
								max_news			= ?,
								domain_url			= ?,
								domain_email		= ?,
								online				= ?,
								site_title			= ?,
								login_sleep			= ?',
							$settings[ 'poll_options' ], $settings[ 'email_validatdion' ], $settings[ 'registration' ], $settings[ 'max_news' ],
							$settings[ 'domain_url' ], $settings[ 'domain_email' ], $settings[ 'online' ], $settings[ 'site_title' ], $settings[ 'login_sleep' ] );

	}
}

class Polls
{
	public static function Votes_Total_Poll( &$db, $poll_id )
	{
		return $db->select( 'SELECT id FROM poll_votes WHERE poll_id = ?', $null, $poll_id );
	}

	public static function Votes_Total_Answer( &$db, $answer_id )
	{
		return $db->select( 'SELECT id FROM poll_votes WHERE answer_id = ?', $null, $answer_id );
	}

	public static function Insert( &$db, &$poll )
	{
		$poll[ 'date' ] = Functions::Timestamp();

		return $db->insert( 'polls', $poll );
	}

	public static function Delete( &$db, $poll_id )
	{
		if ( !Polls::Votes_Delete_Poll( $db, $poll_id ) 	||
			 !Polls::Answers_Delete_Poll( $db, $poll_id ) 	||
			 !Polls::Delete_LowLevel( $db, $poll_id ) )
		{
			return false;
		}

		return true;
	}

	public static function Delete_LowLevel( &$db, $poll_id )
	{
		return $db->query( 'DELETE FROM polls WHERE id = ?', $poll_id );
	}

	public static function Delete_User( &$db, $userid )
	{
		return $db->query( 'DELETE FROM poll_votes WHERE user_id = ?', $userid );
	}
	public static function Answer_Insert( &$db, $answer )
	{
		return $db->insert( 'poll_answers', $answer );
	}

	public static function Vote_Insert( &$db, &$vote )
	{
		$vote[ 'date' ] = Functions::Timestamp();
		$vote[ 'ip' ] 	= $_SERVER[ 'REMOTE_ADDR' ];

		return $db->insert( 'poll_votes', $vote );
	}

	public static function Update( &$db, $poll )
	{
		return $db->query( 'UPDATE polls SET active = ?, question = ? WHERE id = ?', $poll[ 'active' ], $poll[ 'question' ], $poll[ 'id' ] );
	}

	public static function Answer_Update( &$db, $answer )
	{
		return $db->query( 'UPDATE poll_answers SET answer = ? WHERE id = ?', $answer[ 'answer' ], $answer[ 'id' ] );
	}

	public static function Load( &$db, $poll_id, &$poll )
	{
		return $db->single( 'SELECT * FROM polls WHERE id = ?', $poll, $poll_id );
	}

	public static function List_Load( &$db, &$polls )
	{
		return $db->select( 'SELECT * FROM polls ORDER BY id DESC', $polls );
	}

	public static function AnswersList_Load_Poll( &$db, $poll_id, &$answers )
	{
		return $db->select( 'SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY id DESC', $answers, $poll_id );
	}

	public static function Answer_Load( &$db, $answer_id, &$answer )
	{
		return $db->single( 'SELECT * FROM poll_answers WHERE id = ?', $answer, $answer_id );
	}

	public static function Answer_Load_Poll( &$db, $answer_id, $poll_id, &$answer )
	{
		return $db->single( 'SELECT * FROM poll_answers WHERE id = ? AND poll_id = ?', $answer, $answer_id, $poll_id );
	}

	public static function VotesList_Load_Poll( &$db, $poll_id, &$votes )
	{
		return $db->select( 'SELECT * FROM poll_votes WHERE poll_id = ?', $votes, $poll_id );
	}

	public static function Latest( &$db, &$poll )
	{
		return $db->select( 'SELECT * FROM polls WHERE active = 1 ORDER BY id DESC LIMIT 1', $poll );
	}

	public static function Answer_Delete( &$db, $answer_id )
	{
		return $db->query( 'DELETE FROM poll_answers WHERE id = ?', $answer_id );
	}

	public static function Votes_Delete_Answer( &$db, $answer_id )
	{
		return $db->query( 'DELETE FROM poll_votes WHERE answer_id = ?', $answer_id );
	}

	public static function Votes_Delete_Poll( &$db, $poll_id )
	{
		return $db->query( 'DELETE FROM poll_votes WHERE poll_id = ?', $poll_id );
	}

	public static function Answers_Delete_Poll( &$db, $poll_id )
	{
		return $db->query( 'DELETE FROM poll_answers WHERE poll_id = ?', $poll_id );
	}
}

class ResetPassword
{
	public static function Insert( &$db, &$insert )
	{
		$insert[ 'date' ] = Functions::Timestamp();

		return $db->insert( 'reset_password', $insert );
	}

	public static function Delete_User( &$db, $userid )
	{
		return $db->query( 'DELETE FROM reset_password WHERE userid = ?', $userid );
	}
}

class SentPicks
{
	public static function Insert( &$db, &$picks )
	{
		$picks[ 'ip' ]		= $_SERVER[ 'REMOTE_ADDR' ];
		$picks[ 'date' ]	= Functions::Timestamp();

		return $db->insert( 'sent_picks', $picks );
	}

	public static function Delete_User( &$db, $picks )
	{
		return $db->query( 'DELETE FROM sent_picks WHERE user_id = ?', $userid );
	}
}

class News
{
	public static function Insert( &$db, &$news )
	{
		$news[ 'ip' ]	= $_SERVER[ 'REMOTE_ADDR' ];
		$news[ 'date' ]	= Functions::Timestamp();

		return $db->insert( 'news', $news );
	}

	public static function Update( &$db, $news )
	{
		return $db->query( 'UPDATE news SET title = ?, news = ?, active = ? WHERE id = ?', $news[ 'title' ], $news[ 'news' ], $news[ 'active' ], $news[ 'id' ] );
	}

	public static function Load( &$db, $newsid, &$news )
	{
		return $db->single( 'SELECT * FROM news WHERE id = ?', $news, $newsid );
	}

	public static function List_Load( &$db, &$news )
	{
		return $db->select( 'SELECT * FROM news ORDER BY id DESC', $news );
	}

	public static function Delete( &$db, $news_id )
	{
		return $db->query( 'DELETE FROM news WHERE id = ?', $news_id );
	}
}

class Games
{
	public static function Create( &$db )
	{
		$sql = "CREATE TABLE `games` (
					id 			int(3) AUTO_INCREMENT,
					away 		int(2),
					home 		int(2),
					date 		datetime,
					week 		int(2),
					winner 		int(2),
					loser 		int(2),
					homeScore 	int(2),
					awayScore 	int(2),
					PRIMARY KEY ( id )
				)";

		return $db->query( $sql );
	}

	public static function List_Load( &$db, $week, &$games )
	{
		return $db->select( 	'SELECT
									s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, homeTeam.stadium AS stadium,
									awayTeam.team AS awayTeam, awayTeam.wins AS awayWins, awayTeam.losses AS awayLosses, awayTeam.abbr AS awayAbbr,
									homeTeam.team AS homeTeam, homeTeam.wins AS homeWins, homeTeam.losses AS homeLosses, homeTeam.abbr AS homeAbbr
								FROM
									games s
								LEFT JOIN ( SELECT * FROM teams ) awayTeam ON
									s.away = awayTeam.id
								LEFT JOIN ( SELECT * FROM teams ) homeTeam ON
									s.home = homeTeam.id
								WHERE
									s.week = ?
								ORDER BY
									s.date, s.id',
								$games,
								$week );
	}

	public static function Insert( &$db, &$game )
	{
		return $db->insert( 'games', $game );
	}

	public static function Update( &$db, $game )
	{
		return $db->query( 'UPDATE
								games
							SET
								away 		= ?,
								home		= ?,
								date		= ?,
								week		= ?,
								winner		= ?,
								loser		= ?,
								homeScore	= ?,
								awayScore	= ?
							WHERE
								id = ?',
							$game[ 'away' ], $game[ 'home' ], $game[ 'date' ], $game[ 'week' ], $game[ 'winner' ], $game[ 'loser' ], $game[ 'homeScore' ], $game[ 'awayScore' ], $game[ 'id' ] );
	}

	public static function Delete( &$db, $gameid )
	{
		if ( !Picks::Delete_Game( $db, $gameid ) ||
			 !Games::Delete_LowLevel( $db, $gameid ) )
		{
			return false;
		}

		return true;
	}

	public static function Delete_LowLevel( &$db, $gameid )
	{
		return $db->query( 'DELETE FROM games WHERE id = ?', $gameid );
	}

	public static function Load( &$db, $gameid, &$game )
	{
		return $db->single( 'SELECT
									s.id, s.away, s.home, s.date, s.week, s.winner, s.loser, s.homeScore, s.awayScore, homeTeam.stadium AS stadium,
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

	public static function Load_Week_Teams( &$db, $week, $away, $home, &$game )
	{
		return $db->single( 'SELECT * FROM games WHERE week = ? AND away = ? AND home = ?', $game, $week, $away, $home );
	}

	public static function Exists( &$db, $gameid, $weekid, $home, $away )
	{
		$count = $db->single( 'SELECT id FROM games WHERE id = ? AND week = ? AND ( ( away = ? AND home = ? ) OR ( away = ? AND home = ? ) )', $null, $gameid, $weekid, $home, $away, $away, $home );

		if ( !$count )
		{
			return false;
		}

		return true;
	}

	public static function Exists_Week_Teams( &$db, $weekid, $homeid, $awayid, &$game )
	{
		$count = $db->single( 'SELECT id FROM games WHERE week = ? AND home = ? AND away = ?', $game, $weekid, $homeid, $awayid );

		return ( $count ) ? true : false;
	}
}

class Picks
{
	public static function Insert_All( &$db, $user_id )
	{
		$ip = $_SERVER[ 'REMOTE_ADDR' ];

		return $db->query( 'INSERT INTO picks ( user_id, game_id, winner_pick, loser_pick, ip, week )
							SELECT ?, id, 0, 0, ?, week FROM games', $user_id, $ip );
	}

	public static function Update( &$db, $pick )
	{
		$ip 	= $_SERVER[ 'REMOTE_ADDR' ];
		$time 	= Functions::Timestamp();

		return $db->query( 'UPDATE
								picks
						    SET
								winner_pick	= ?,
								loser_pick	= ?,
								ip			= ?,
								updated		= ?,
								picked		= ?
						    WHERE
								user_id		= ? AND
								game_id		= ?',
							$pick[ 'winner_pick' ], $pick[ 'loser_pick' ], $ip, $time, $pick[ 'picked' ],
							$pick[ 'user_id' ], $pick[ 'game_id' ] );
	}

	public static function Delete( &$db, $pick_id )
	{
		return $db->query( 'DELETE FROM picks WHERE id = ?', $pick_id );
	}

	public static function Delete_User( &$db, $userid )
	{
		return $db->query( 'DELETE FROM picks WHERE user_id = ?', $userid );
	}

	public static function Delete_Game( &$db, $gameid )
	{
		return $db->query( 'DELETE FROM picks WHERE game_id = ?', $gameid );
	}

	public static function Remaining( &$db, $userid, $weekid )
	{
		$date	= Functions::Timestamp();
		$count 	= $db->single( 'SELECT
									COUNT( p.id ) AS remaining
								FROM
									picks p, games g
								WHERE
									p.user_id 		= ? 	AND
									p.week 			= ? 	AND
									p.picked	 	= 0 	AND
									p.game_id 		= g.id 	AND
									g.date 			> ?',
								$remaining, $userid, $weekid, $date );

		if ( $count === false )
		{
			return false;
		}

		return $remaining[ 'remaining' ];
	}

	public static function Missing( &$db, $userid, $weekid )
	{
		$count = $db->single( 'SELECT COUNT( id ) AS count FROM picks WHERE user_id = ? AND week = ? AND picked = 0', $missing, $userid, $weekid );

		if ( $count === false )
		{
			return false;
		}

		return $missing[ 'count' ];
	}

	public static function UserWeekList_Load( &$db, $userid, $week, &$picks )
	{
		return $db->select( 'SELECT p.*, ( SELECT t.team FROM teams t WHERE t.id = p.winner_pick ) AS winner, ( SELECT t.team FROM teams t WHERE t.id = p.loser_pick ) AS loser FROM picks p WHERE user_id = ? AND week = ?', $picks, $userid, $week );
	}

	public static function Load_User_Game( &$db, $userid, $gameid, &$pick )
	{
		return $db->single( 'SELECT * FROM picks WHERE user_id = ? AND game_id = ?', $pick, $userid, $gameid );
	}

	public static function List_Load_User_Week( &$db, $userid, $week, &$picks )
	{
		return $db->select( 'SELECT * FROM picks WHERE user_id = ? AND week = ?', $picks, $userid, $week );
	}
}

class Weeks
{
	public static function Load( &$db, $weekid, &$week )
	{
		return $db->single( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w WHERE id = ?', $week, $weekid );
	}

	public static function List_Load( &$db, &$weeks )
	{
		return $db->select( 'SELECT w.*, ( SELECT COUNT( id ) FROM games g WHERE g.week = w.id ) AS total_games FROM weeks w ORDER BY id', $weeks );
	}

	public static function Insert( &$db, &$week )
	{
		return $db->insert( 'weeks', $week );
	}

	public static function IsLocked( &$db, $weekid )
	{
		$count = Weeks::Load( $db, $weekid, $week );

		if ( !$count || $week[ 'locked' ] === 0 )
		{
			return false;
		}

		return true;
	}

	public static function Update( &$db, $week )
	{
		return $db->query( 'UPDATE weeks SET date = ?, locked = ? WHERE id = ?', $week[ 'date' ], $week[ 'locked' ], $week[ 'id' ] );
	}

	public static function Current( &$db )
	{
		$count = $db->single( 'SELECT id FROM weeks WHERE locked = 0 ORDER BY id', $week );

		if ( $count === false )
		{
			return false;
		}

		if ( $count === 0 )
		{
			return 1;
		}

		return $week[ 'id' ];
	}

	public static function Previous( &$db )
	{
		$count = $db->single( 'SELECT id FROM weeks WHERE locked = 1 ORDER BY id DESC', $week );

		if ( $count === false )
		{
			return false;
		}

		if ( $count === 0 )
		{
			return 1;
		}

		return $week[ 'id' ];
	}

	public static function Total_Games( &$db, $week )
	{
		$count = $db->single( 'SELECT COUNT( id ) AS total FROM games WHERE week = ?', $games, $week );

		if ( $count === false )
		{
			return false;
		}

		return $games[ 'total' ];
	}
}

class Teams
{
	public static function Create( &$db )
	{
		$sql = "CREATE TABLE teams
				(
					id 		int( 11 ) AUTO_INCREMENT,
					team 	varchar( 255 ),
					picture varchar( 20 ),
					conf 	varchar( 15 ),
					stadium varchar( 255 ),
					wins 	int( 2 ),
					losses 	int( 2 ),
					abbr 	varchar( 10 ),
					PRIMARY KEY ( id )
				)";

		return $db->query( $sql );
	}

	public static function Update_Wins( &$db, $teamid )
	{
		return $db->query( 'UPDATE teams SET wins = wins + 1 WHERE id = ?', $teamid );
	}

	public static function Update_Losses( &$db, $teamid )
	{
		return $db->query( 'UPDATE teams SET losses = losses + 1 WHERE id = ?', $teamid );
	}

	public static function Load( &$db, $teamid, &$team )
	{
		return $db->single( 'SELECT * FROM teams WHERE id = ?', $team, $teamid );
	}

	public static function Load_Name( &$db, $name, &$team )
	{
		return $db->single( 'SELECT * FROM teams WHERE team LIKE CONCAT( \'%\', ?, \'%\' )', $team, $name );
	}

	public static function List_Load( &$db, &$teams )
	{
		return $db->select( 'SELECT * FROM teams', $teams );
	}

	public static function Delete( &$db, $teamid )
	{
		return $db->query( 'DELETE FROM teams WHERE id = ?', $teamid );
	}

	public static function Update( &$db, $team )
	{
		return $db->query( 'UPDATE
								teams
							SET
								team = ?,
								picture = ?,
								conf	= ?,
								stadium	= ?,
								wins	= ?,
								losses	= ?,
								abbr	= ?
							WHERE
								id = ?',
							$team[ 'team' ], $team[ 'picture' ], $team[ 'conf' ], $team[ 'stadium' ], $team[ 'wins' ], $team[ 'losses' ], $team[ 'abbr' ],
							$team[ 'id' ] );
	}

	public static function Byes( &$db, $week_id, &$bye_teams )
	{
		return $db->single( 'SELECT GROUP_CONCAT( team ORDER BY team SEPARATOR \', \' ) AS bye_teams FROM teams t WHERE NOT EXISTS( SELECT g.id FROM games g WHERE ( g.away = t.id OR g.home = t.id ) AND g.week = ? )', $bye_teams, $week_id );
	}

	public static function Load_Abbr( &$db, $abbr, &$team )
	{
		return $db->single( 'SELECT * FROM teams WHERE REPLACE( abbr, ".", "" ) LIKE CONCAT ( UPPER( ? ), \'%\' )', $team, $abbr );
	}

	public static function Recalculate_Records( &$db )
	{
		return $db->query( 'UPDATE
								teams t
							SET
								t.wins 		= ( SELECT COUNT( g.id ) FROM games g WHERE g.winner 	= t.id ),
								t.losses	= ( SELECT COUNT( g.id ) FROM games g WHERE g.loser		= t.id )' );
	}
}

class FailedLogin
{
	public static function Create( &$db )
	{
		$sql = "CREATE TABLE failed_logins
				(
			  		id 		int(11) AUTO_INCREMENT,
			  		email 	varchar(50),
			  		date 	datetime,
			  		ip 		varchar(255),
			  		PRIMARY KEY ( id )
			  	)";

		return $db->query( $sql );
	}

	public static function Insert( &$db, $email )
	{
		$values = array( 'email' => $email, 'date' => Functions::Timestamp(), 'ip' => $_SERVER[ 'REMOTE_ADDR' ] );

		return $db->insert( 'failed_logins', $values );
	}
}

class Functions
{
	public static function Module_Updated( $message )
	{
		global $updated_message;

		$updated_message = $message;

		return true;
	}

	public static function HandleModuleUpdate()
	{
		global $updated_message;

		if ( !is_null( $updated_message ) && is_string( $updated_message ) && trim( $updated_message ) != '' )
		{
			print "<p><b>{$updated_message}</b></p>";
			unset( $updated_message );
		}

		return true;
	}

	public static function HandleModuleErrors()
	{
		global $error_validation;

		if ( !is_array( $error_validation ) )
		{
			return false;
		}

		$count = count( $error_validation );

		if ( $count > 0 )
		{
			$output 	= '';
			$message	= '';
			$title 		= ( $count === 1 ) ? 'Error Has' : $i . ' Errors Have';

			foreach( $error_validation as $error )
			{
				$message .= "- {$error}<br />";
			}

			print '<div class="error">';
			printf( '<span class="error_text_top">The Following %s Ocurred!</span><br />', htmlentities( $title ) );
			printf( '<span class="error_text">%s</span>', $message );
			print '</div>';

			unset( $error_validation );
		}

		return true;
	}

	public static function OutputError()
	{
		global $error_code;
		global $error_message;

		$error_code 	= ( is_null( $error_code ) ) ? 'NFL-FUNCTIONS-0' : $error_code;
		$error_message 	= ( is_null( $error_message ) ) ? 'An unknown error has occurred.' : $error_message;
		print '<h1>An error has occurred</h1>';
		print '<div>Error Code: ' . htmlentities( $error_code ) . '</div>';
		print '<div>Error Message: ' . htmlentities( $error_message ) . '</div>';

		return true;
	}

	public static function Error( $code, $message )
	{
		global $error_code;
		global $error_message;

		$error_code 	= $code;
		$error_message 	= $message;

		return false;
	}

	public static function ValidationError( $errors )
	{
		global $error_validation;

		$error_validation = $errors;

		return false;
	}

	public static function EmailExists( $db, $email )
	{
		$count = $db->single( 'SELECT id FROM users WHERE email = ?', $null, $email );

		if ( $count === 1 )
		{
			return true;
		}

		return false;
	}

	public static function Random( $length )
	{
		$string 	= '';
		$charset 	= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
		$count 		= strlen( $charset );

		while( $length-- )
		{
			$string .= $charset[ mt_rand( 0, $count - 1 ) ];
		}

		return $string;
	}

	public static function Place( $number )
	{
		$mod10	= $number % 10;
		$mod100 = $number % 100;

		if ( $mod100 >= 11 && $mod100 <= 13 )
		{
			$s = "th";
		}
		else if ( $mod10 === 1 )
		{
			$s = "st";
		}
		else if ( $mod10 === 2 )
		{
			$s = "nd";
		}
		else if ( $mod10 === 3 )
		{
			$s = "rd";
		} else {
			$s = "th";
		}

		return "{$number}<sup>{$s}</sup>";
	}

	public static function Trim_Boolean( $value )
	{
		return $value ? 1 : 0;
	}

	public static function Post_Int( $value )
	{
		return isset( $_POST[ $value ] ) ? (int)$_POST[ $value ] : 0;
	}

	public static function Post_Boolean( $value )
	{
		if ( isset( $_POST[ $value ] ) )
		{
			if ( $_POST[ $value ] === 'true' )
			{
				return true;
			}
		}

		return false;
	}

	public static function Post_Active( $value )
	{
		if ( isset( $_POST[ $value ] ) )
		{
			if ( $_POST[ $value ] )
			{
				return 1;
			}
		}

		return 0;
	}

	public static function Post_Array( $value )
	{
		return isset( $_POST[ $value ] ) ? array_map( 'trim', $_POST[ $value ] ) : array();
	}

	public static function Get( $value )
	{
		return isset( $_GET[ $value ] ) ? trim( $_GET[ $value ] ) : '';
	}

	public static function Post( $value )
	{
		return isset( $_POST[ $value ] ) ? trim( $_POST[ $value ] ) : '';
	}

	public static function Cookie( $value )
	{
		return isset( $_COOKIE[ $value ] ) ? trim( $_COOKIE[ $value ] ) : '';
	}

	public static function Information( $h1, $p )
	{
		print "<h1>{$h1}</h1>";
		print "<p>{$p}</p>";

		return true;
	}

	public static function FormatDate( $date )
	{
		$date = new DateTime( $date );
		$date->setTimezone( new DateTimezone( 'America/Los_Angeles' ) );

		return $date->format( 'm/d/y' ) . ' at '. $date->format( 'h:i a' );
	}

	public static function TimeUntil( $time )
	{
		$now	 	= new DateTime();
		$then		= new DateTime( $time );
		$interval	= $now->diff( $then );

		return $interval->format( '%d days %h hours %i minutes %s seconds' );
	}

	public static function VerifyPassword( $plaintext, $hashed )
	{
		if ( crypt( $plaintext, $hashed ) !== $hashed )
		{
			return false;
		}

		return true;
	}

	public static function HashPassword( $password )
	{
		return crypt( $password, '$6$rounds=10000$' . Functions::GenerateSalt() . '$' );
	}

	public static function GenerateSalt()
	{
		$hex_salt		= '';
		$binary_salt 	= openssl_random_pseudo_bytes( 16 );

		for( $i = 0; $i < 16; $i++ )
		{
			$hex_salt .= bin2hex( substr( $binary_salt, $i, 1 ) );
		}

		return strtoupper( $hex_salt );
	}

	public static function TopNavigation( &$db, &$user )
	{
		if ( !$user->logged_in )
		{
			print '<span>Welcome, Guest. Please login to start making your picks for week ' . Weeks::Current( $db ) . '.</span>';
		} else {
			print '<span>Welcome, ' . htmlentities( $user->account[ 'name' ] ) . '! You have ' . htmlentities( $user->account[ 'wins' ] ) . ' wins and ' . htmlentities( $user->account[ 'losses' ] ) . ' losses and currently in ' . Functions::Place( $user->account[ 'current_place' ] ) . ' place.</span>';
		}
	}

	public static function UserNavigation( &$db, &$user )
	{
		if ( $user->logged_in )
		{
			$admin 	= ( $user->account[ 'admin' ] ) ? '<li><a href="?view=admin" title="Admin Control Panel">Admin Control Panel</a></li>' : '';
			$weekid = Weeks::Previous( $db );

			print <<<EOT
				<h1>User Links</h1>
				<ul>
				  <li><a href="" title="Home">Home Page</a></li>
				  <li><a href="?module=controlpanel" title="User Control Panel">User Control Panel</a></li>
				  {$admin}
				  <li><a href="?module=makepicks" title="Make Picks">Make Picks</a></li>
				  <li><a href="?module=viewpicks&week={$weekid}" title="User Picks">View Other's Picks</a></li>
				  <li><a href="?module=weeklyrecords" title="Weekly User Records">View Weekly Records</a></li>
				  <li><a href="?module=leaderboard" title="Leader Board">View Leader Board</a></li>
				  <li><a href="?module=logout" title="Logout">Logout</a></li>
				</ul>
EOT;
		}
	}

	public static function ValidateScreen( &$db, &$user, &$extra_screen_content )
	{
		$view	= Functions::Get( 'view' );
		$module = Functions::Get( 'module' );

		if ( empty( $module ) )
		{
			$module = 'index';
		}

		if ( $user->logged_in )
		{
			$action = Functions::Get( 'action' );

			if ( $user->account[ 'force_password' ] && ( $module != 'forgotpassword' || ( $module == 'forgotpassword' &&  $action != 'changepassword' ) ) )
			{
				header( sprintf( 'location: %s?module=forgotpassword&action=changepassword', INDEX ) );
				return false;
			}
		}

		if ( !Validation::Filename( $module ) )
		{
			return Functions::Error( 'NFL-FUNCTIONS-1', 'Invalid module name' );
		}

		if ( $view === 'admin' && $user->logged_in && $user->account[ 'admin' ] )
		{
			$path = "admin/modules/{$module}.php";
		} else {
			$path = "includes/modules/{$module}.php";
		}

		if ( !file_exists( $path ) )
		{
			return Functions::Error( 'NFL-FUNCTIONS-2', "Module '{$module}' does not exist." );
		}

		ob_start();
		require_once( $path );
		$extra_screen_content = ob_get_contents();
		ob_clean();

		if ( !function_exists( 'Module_Content' ) )
		{
			return Functions::Error( 'NFL-FUNCTIONS-3', "The module '{$module}' is missing the Module_Content function." );
		}

		$validation = array();
		$action 	= Functions::Post( 'action' );

		if ( $action === 'update' )
		{
			if ( !function_exists( 'Module_Validate' ) )
			{
				return Functions::Error( 'NFL-FUNCTIONS-4', "The module '{$module}' is missing the Module_Validate function." );
			}

			if ( !function_exists( 'Module_Update' ) )
			{
				return Functions::Error( 'NFL-FUNCTIONS-5', "The module '{$module}' is missing the Module_Update function." );
			}

			if ( !Module_Validate( $db, $user, $validation ) )
			{
				return true;
			}

			if ( !Module_Update( $db, $user, $validation ) )
			{
				return false;
			}
		}

		return true;
	}

	public static function PrintR( $data )
	{
		print '<pre>';
		print_r( $data );
		print '</pre>';
	}

	public static function Worst_Record( &$db, $week_id, &$record )
	{
		return $db->single( 'SELECT
								MIN( (
										SELECT
											NULLIF( COUNT( p.id ), 0 )
										FROM
											picks p,
											games g
										WHERE
											p.game_id 		= g.id 		AND
											p.winner_pick 	= g.winner 	AND
											p.user_id 		= u.id 		AND
											g.week 			= ? ) ) AS wins,
								MAX( (
										SELECT
											COUNT( p.id )
										FROM
											picks p,
											games g
										WHERE
											p.game_id 		= g.id 		AND
											p.winner_pick 	= g.loser 	AND
											p.user_id 		= u.id		AND
											g.week 			= ? ) ) AS losses
						     FROM
								users u', $record, $week_id, $week_id );
	}

	public static function Worst_Record_Calculated( &$db, $week_id, &$record )
	{
		if ( Functions::Worst_Record( $db, $week_id, $record ) === false )
		{
			return false;
		}

		$record[ 'total' ]	= $record[ 'wins' ] + $record[ 'losses' ];

		if ( $record[ 'wins' ] < 2 )
		{
			$record[ 'wins' ] 	= 0;
			$record[ 'losses' ] = $record[ 'total' ];
		}
		else
		{
			$record[ 'wins' ] 	-= 2;
			$record[ 'losses' ] += 2;
		}

		return true;
	}

	public static function Timestamp()
	{
		$date = new DateTime();
		return $date->format( DATE_ISO8601 );
	}

	public static function Fix_User_Records( &$db, &$users )
	{
		$weeks_count = Weeks::List_Load( $db, $weeks );

		if ( $weeks_count === false )
		{
			return false;
		}

		foreach( $weeks as $week )
		{
			if ( $week[ 'locked' ] !== 1 )
			{
				continue;
			}

			$total_games = Weeks::Total_Games( $db, $week[ 'id' ] );

			if ( $total_games === false )
			{
				return false;
			}

			foreach( $users as &$user )
			{
				$missing_count = Picks::Missing( $db, $user[ 'id' ], $week[ 'id' ] );

				if ( $missing_count === false )
				{
					return false;
				}

				if ( $missing_count === 0 )
				{
					continue;
				}

				if ( $missing_count !== $total_games )
				{
					$user[ 'losses' ] += $missing_count;
				}
				else
				{
					if ( !Functions::Worst_Record_Calculated( $db, $week[ 'id' ], $worst_record ) )
					{
						return false;
					}

					if ( $worst_record[ 'total' ] !== $total_games )
					{
						continue;
					}

					$user[ 'wins' ] 	+= $worst_record[ 'wins' ];
					$user[ 'losses' ] 	+= $worst_record[ 'losses' ];
				}

				if ( !Users::Update_Record( $db, $user[ 'id' ], $user[ 'wins' ], $user[ 'losses' ] ) )
				{
					return false;
				}
			}
		}

		return true;
	}
}
?>
