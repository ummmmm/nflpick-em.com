<?php

require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/Exceptions.php' );
require_once( 'includes/classes/Database.php' );
require_once( 'includes/classes/Authentication.php' );
require_once( 'includes/classes/Setup.php' );
require_once( 'includes/classes/Input.php' );


$setup	= new Setup();
$input	= new RawInput();
$action	= $input->value_GET_str( 'Action' );

try
{
	$setup->initialize();

	if ( $action === 'INSTALL' )		install( $setup, $input );
	else if ( $action === 'UNINSTALL' )	uninstall( $setup, $input );
}
catch ( NFLPickEmException $e )
{
	print( $e->getMessage() );
}
catch ( Exception $e )
{
	printf( 'A fatal error occurred: %s', htmlentities( $e->getMessage() ) );
}

function install( Setup &$setup, RawInput &$input )
{
	$install = $input->value_POST_str( 'install' );

	if ( $install != '' )
	{
		$db_games		= $setup->db()->games();
		$db_settings	= $setup->db()->settings();
		$db_weeks		= $setup->db()->weeks();
		$site_title		= $input->value_POST_str( 'site_title' );
		$domain_url 	= $input->value_POST_str( 'domain_url' );
		$domain_email	= $input->value_POST_str( 'domain_email' );
		$start_date		= $input->value_POST_str( 'start_date' );

		if ( $site_title == '' )		throw new NFLPickEmException( 'A site title is required' );
		else if ( $domain_url == '' )	throw new NFLPickEmException( 'A domain URL is required' );
		else if ( $domain_email == '' )	throw new NFLPickEmException( 'A domain email is required' );
		else if ( $start_date == '' )	throw new NFLPickEmException( 'Start date is required' );

		if ( !preg_match( "/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/20\d{2}$/", $start_date ) )
		{
			throw new NFLPickEmException( "Start date must be in format mm/dd/yyyy" );
		}

		$timestamp = strtotime( $start_date . ' 10 A.M.' );

		if ( $timestamp === false )				throw new NFLPickEmException( 'Invalid start date' );
		elseif ( date( 'w', $timestamp ) != 0 )	throw new NFLPickEmException( 'Start date is expected to be a Sunday' );

		$setup->install();

		$db_settings->Load( $settings );

		$settings[ 'site_title' ] 	= $site_title;
		$settings[ 'domain_url' ] 	= $domain_url;
		$settings[ 'domain_email' ]	= $domain_email;

		$db_settings->Update( $settings );
		$db_weeks->Create_Weeks( $timestamp );
		$db_games->Create_Games();

		header( sprintf( 'location: %s', $settings[ 'domain_url' ] ) );
		exit();
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

function uninstall( Setup &$setup, RawInput &$input )
{
	$uninstall = $input->value_POST_str( 'uninstall' );

	if ( $uninstall != '' )
	{
		$email 		= $input->value_POST_str( 'email' );
		$password 	= $input->value_POST_str( 'password' );

		if ( !$setup->auth()->validate_login( $email, $password, $user ) )	throw new NFLPickEmException( 'Invalid email / password' );
		else if ( $user[ 'admin' ] != 1 )									throw new NFLPickEmException( 'You must be an admin to uninstall the site' );

		$setup->uninstall();

		print( 'The NFL Pick-Em site has successfully been uninstalled' );
		exit();
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
