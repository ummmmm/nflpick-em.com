<?php
require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/database.php' );
require_once( 'includes/classes/Setup.php' );

$action	= Functions::Get( 'Action' );

if ( $action === 'INSTALL' )
{
	die( install() );
}
else if ( $action === 'UNINSTALL' )
{
	die( uninstall() );
}

die( 'Nothing to see here' );

function install()
{
	$setup		= new Setup();
	$install 	= Functions::Post( 'install' );

	if ( $setup->Configured() )
	{
		die( $setup->Get_Error() );
	}

	if ( $install != '' )
	{
		$database		= new Database();
		$db_settings	= new Settings( $db );
		$site_title		= Functions::Post( 'site_title' );
		$domain_url 	= Functions::Post( 'domain_url' );
		$domain_email	= Functions::Post( 'domain_email' );

		if ( $site_title == '' )		die( 'A site title is required' );
		else if ( $domain_url == '' )	die( 'A domain URL is required' );
		else if ( $domain_email == '' )	die( 'A domain email is required' );

		if ( !$setup->Install() )
		{
			die( $setup->Get_Error() );
		}

		if ( !$db_settings->Load( $settings ) )
		{
			print 'Failed to load settings';
		}
		else
		{
			$settings[ 'site_title' ] 	= $site_title;
			$settings[ 'domain_url' ] 	= $domain_url;
			$settings[ 'domain_email' ]	= $domain_email;

			if ( !$db_settings->Update( $settings ) )
			{
				print 'Failed to update default settings';
			}
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
	print '<td><input type="text" name="domain_url" value="" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td><b>Domain Email:</b></td>';
	print '<td><input type="text" name="domain_email" value="" /></td>';
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
	$uninstall = Functions::Post( 'uninstall' );

	if ( $uninstall != '' )
	{
		$setup		= new Setup();
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
