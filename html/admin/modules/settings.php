<?php

function Module_Content( &$db, &$user )
{
	$db_settings = new Settings( $db );
	
	if ( !$db_settings->Load( $settings ) )
	{
		return false;
	}
	
	print '<div id="settings_addedit">';
	print '<table>';
	print '<tr>';
	print '<td valign="top">Email Validation: </td>';
	print '<td>';
	print Draw::Radio( 'email_validation', 1, $settings[ 'email_validation' ], 'On' ) . '<br />';
	print Draw::Radio( 'email_validation', 0, $settings[ 'email_validation' ], 'Off' );
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td valign="top">Registration: </td>';
	print '<td>';
	print Draw::Radio( 'registration', 1, $settings[ 'registration' ], 'On' ) . '<br />';
	print Draw::Radio( 'registration', 0, $settings[ 'registration' ], 'Off' );
	print '</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Poll Options: </td>';
	print '<td><input type="text" name="poll_options" size="1" value="' . htmlentities( $settings[ 'poll_options' ] ) . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td>Max News: </td>';
	print '<td><input type="text" name="max_news" size="1" value="' . htmlentities( $settings[ 'max_news' ] ) . '" /> articles</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Online: </td>';
	print '<td><input type="text" name="online" size="1" value="' . htmlentities( $settings[ 'online' ] ) . '" /> minutes</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Login Sleep: </td>';
	print '<td><input type="text" name="login_sleep" size="1" value="' . htmlentities( $settings[ 'login_sleep' ] ) . '" /> milliseconds</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Domain URL: </td>';
	print '<td><input type="text" name="domain_url" value="' . htmlentities( $settings[ 'domain_url' ] ) . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td>Domain Email: </td>';
	print '<td><input type="text" name="domain_email" value="' . htmlentities( $settings[ 'domain_email' ] ) . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td>Site Title: </td>';
	print '<td><input type="text" name="site_title" value="' . htmlentities( $settings[ 'site_title' ] ) . '" /></td>';
	print '</tr>';
	print '<tr>';
	print '<td>&nbsp;</td>';
	print '<td>';
	print '<input type="submit" name="update" value="Update" onclick="$.fn.update_settings();" />';
	print '</td>';
	print '</tr>';
	print '</table>';
	print '</div>';

	return true;
}
