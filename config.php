<?php
$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];

require_once( '/home4/dcarver/data/db.php' );
require_once( $document_root . '/includes/classes/functions.php' );
require_once( $document_root . '/includes/classes/database.php' );

$db = new Database( $connection );

if ( Settings::Load( $db, $null ) === 1 )
{
	die( 'NFL Pick-Em site has already been configured' );
}

if ( FailedLogin::Create( $db ) !== true ||
	 Games::Create( $db )		!== true ||
	 Teams::Create( $db )		!== true ||
   Settings::Create( $db ) !== true )
{
	global $error_message;
	die( $error_message );
}

$news = "CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `news` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$picks = "CREATE TABLE `picks` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user_id` int(2) NOT NULL DEFAULT '0',
  `game_id` int(3) NOT NULL DEFAULT '0',
  `winner_pick` int(2) NOT NULL DEFAULT '0',
  `loser_pick` int(2) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `week` int(2) NOT NULL DEFAULT '0',
  `picked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `picks_1` (`user_id`,`game_id`),
  KEY `picks_2` (`user_id`,`week`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$polls = "CREATE TABLE `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  `question` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$poll_answers = "CREATE TABLE `poll_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `answer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$poll_votes = "CREATE TABLE `poll_votes` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `poll_id` int(2) NOT NULL DEFAULT '0',
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_votes_1` (`poll_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$reset_password = "CREATE TABLE `reset_password` (
  `userid` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  UNIQUE KEY `password` (`password`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sent_picks = "CREATE TABLE `sent_picks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(2) NOT NULL DEFAULT '0',
  `picks` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(20) NOT NULL DEFAULT '',
  `week` tinyint(2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$sessions = "CREATE TABLE `sessions` (
  `token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `cookieid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `userid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `last_active` datetime DEFAULT NULL,
  UNIQUE KEY `sessionid` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$users = "CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `lname` varchar(25) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL,
  `sign_up` datetime NOT NULL,
  `last_on` datetime NOT NULL,
  `wins` int(3) NOT NULL DEFAULT '0',
  `losses` int(3) NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `current_place` tinyint(3) NOT NULL DEFAULT '1',
  `email_preference` tinyint(1) NOT NULL DEFAULT '1',
  `force_password` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$weeks = "CREATE TABLE `weeks` (
  `id` tinyint(4) NOT NULL,
  `date` datetime NOT NULL,
  `locked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
?>