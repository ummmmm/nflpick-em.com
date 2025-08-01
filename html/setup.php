<?php

require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/Database.php' );
require_once( 'includes/classes/Authentication.php' );
require_once( 'includes/classes/Setup.php' );


$action = Functions::Get( 'Action' );

if ( $action === 'INSTALL' )
{
	install();
	exit();
}
else if ( $action === 'UNINSTALL' )
{
	uninstall();
	exit();
}

die( 'Nothing to see here' );

function install()
{
	$install 	= Functions::Post( 'install' );
	$db_manager	= new DatabaseManager();
	$auth		= new Authentication( $db_manager );

	if ( !$db_manager->initialize() )
	{
		print( $db_manager->Get_Error() );
		exit();
	}

	$setup = new Setup( $db_manager, $auth );

	if ( $setup->Configured() )
	{
		$setup->Get_Error();
		exit();
	}

	if ( $install != '' )
	{
		$db_games		= $db_manager->games();
		$db_settings	= $db_manager->settings();
		$db_weeks		= $db_manager->weeks();
		$site_title		= Functions::Post( 'site_title' );
		$domain_url 	= Functions::Post( 'domain_url' );
		$domain_email	= Functions::Post( 'domain_email' );
		$start_date		= Functions::Post( 'start_date' );

		if ( $site_title == '' )		die( 'A site title is required' );
		else if ( $domain_url == '' )	die( 'A domain URL is required' );
		else if ( $domain_email == '' )	die( 'A domain email is required' );
		else if ( $start_date == '' )	die( 'Start date is required' );

		if ( !preg_match( "/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/20\d{2}$/", $start_date ) )
		{
			die( "Start date must be in format mm/dd/yyyy" );
		}

		$timestamp = strtotime( $start_date . ' 10 A.M.' );

		if ( $timestamp === false )				die( 'Invalid start date' );
		elseif ( date( 'w', $timestamp ) != 0 )	die( 'Start date is expected to be a Sunday' );

		if ( !$setup->Install() )
		{
			print( $setup->Get_Error() );
			exit();
		}
		if ( !$db_settings->Load( $settings ) )
		{
			printf( 'Failed to load settings<br />' );
		}
		else
		{
			$settings[ 'site_title' ] 	= $site_title;
			$settings[ 'domain_url' ] 	= $domain_url;
			$settings[ 'domain_email' ]	= $domain_email;

			if ( !$db_settings->Update( $settings ) )
			{
				printf( 'Failed to update default settings<br />' );
			}
		}

		if ( !$db_weeks->Create_Weeks( $timestamp ) )
		{
			print( $db_weeks->Get_Error() );
			exit();
		}

		if ( !$db_games->Create_Games() )
		{
			printf( 'Failed to create games: %s<br />', htmlentities( $db_games->Get_Error() ) );
		}

		die( 'The NFL Pick-Em site has successfully been installed' );
	}

	print '<form method="POST">';
	print '<table>';
	print '<tr>';
	print '<td><b>Site Title:</b></td>';
	print '<td><input type="text" name="site_title" value="NFL Pick-Em ' . date( 'Y' ) . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td><b>Domain URL:</b></td>';
	print '<td><input type="text" name="domain_url" value="' . url() . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td><b>Domain Email:</b></td>';
	print '<td><input type="text" name="domain_email" value="" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td><b>Start Date:</b></td>';
	print '<td><input type="text" name="start_date" value="' . first_sunday() . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td>&nbsp;</td>';
	print '<td><input type="submit" name="install" value="Install" /></td>';
	print '</tr>';
	print '</table>';
	print '</form>';
}

function uninstall()
{
	$uninstall	= Functions::Post( 'uninstall' );
	$db_manager	= new DatabaseManager();
	$auth		= new Authentication( $db_manager );

	if ( !$db_manager->initialize() )
	{
		print( $db_manager->Get_Error() );
		exit();
	}

	if ( $uninstall != '' )
	{
		$setup		= new Setup( $db_manager, $auth );
		$email 		= Functions::Post( 'email' );
		$password 	= Functions::Post( 'password' );

		if ( !$setup->Uninstall( $email, $password ) )
		{
			die( $setup->Get_Error() );
		}

		die( 'The NFL Pick-Em site has successfully been uninstalled' );
	}

	print '<form method="POST" autocomplete="off">';
	print '<table>';
	print '<tr>';
	print '<td><b>Email:</b></td>';
	print '<td><input type="text" name="email" value="" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td><b>Password:</b></td>';
	print '<td><input type="password" name="password" value="" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td>&nbsp;</td>';
	print '<td><input type="submit" name="uninstall" value="Uninstall" /></td>';
	print '</tr>';
	print '</table>';
	print '</form>';
}

function url()
{
	return sprintf( 'https://%s/', $_SERVER[ 'HTTP_HOST' ] );
}

function first_sunday()
{
	$url 	= 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?week=0';
	$data	= json_decode( file_get_contents( $url ) );

	foreach ( $data->leagues[ 0 ]->calendar as $entry )
	{
		if ( $entry->label == 'Regular Season' )
		{
			$date = new DateTime( $week_1 = $entry->entries[ 0 ]->startDate );

			if ( date( 'w', $date->getTimestamp() ) == 0 )	$timestamp = $date->getTimestamp();
			else											$timestamp = strtotime( 'Next Sunday', $date->getTimestamp() );

			return date( 'm/d/Y', $timestamp );
		}
	}

	return 'Estimated for ' . date( 'm/d/Y', strtotime( 'First Sunday of September ' . date( 'Y' ) ) );
}
