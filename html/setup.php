<?php

require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/Exceptions.php' );
require_once( 'includes/classes/Database.php' );
require_once( 'includes/classes/Authentication.php' );
require_once( 'includes/classes/Setup.php' );
require_once( 'includes/classes/Input.php' );

set_time_limit( 90 );

$setup	= new Setup();
$input	= new RawInput();
$action	= $input->value_str_GET( 'Action' );

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
	$install = $input->value_str_POST( 'install' );

	if ( $install != '' )
	{
		$db_games		= $setup->db()->games();
		$db_settings	= $setup->db()->settings();
		$db_weeks		= $setup->db()->weeks();
		$domain_url 	= $input->value_str_POST( 'domain_url' );
		$domain_email	= $input->value_str_POST( 'domain_email' );

		if ( $domain_url == '' )		throw new NFLPickEmException( 'A domain URL is required' );
		else if ( $domain_email == '' )	throw new NFLPickEmException( 'A domain email is required' );

		$setup->install();
		$setup->configure_defaults( $domain_url, $domain_email );

		header( sprintf( 'location: %s', $domain_url ) );
		exit();
	}

	print '<form method="POST">';
	print '<table>';
	print '<tr>';
	print '<td><b>Domain URL:</b></td>';
	print '<td><input type="text" name="domain_url" value="' . htmlentities( url() ) . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td><b>Domain Email:</b></td>';
	print '<td><input type="text" name="domain_email" value="contact@nflpick-em.com" /></td>';
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
	$uninstall = $input->value_str_POST( 'uninstall' );

	if ( $uninstall != '' )
	{
		$email 		= $input->value_str_POST( 'email' );
		$password 	= $input->value_str_POST( 'password' );

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
