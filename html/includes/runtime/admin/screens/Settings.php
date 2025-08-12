<?php

class Screen_Settings extends Screen_Admin
{
	public function content()
	{
		$settings	= $this->settings();
		$reg_on		= $settings[ 'registration' ] ? 'checked' : '';
		$reg_off	= !$settings[ 'registration' ] ? 'checked' : '';

		print '<div id="settings_addedit">';
		print '<table>';
		print '<tr>';
		print '<td valign="top">Registration: </td>';
		print '<td>';
		print '<input type="radio" name="registration" value="1" ' . $reg_on . '/> On<br />';
		print '<input type="radio" name="registration" value="0" ' . $reg_off . '/> Off';
		print '</td>';
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
		print '<td>Turnstile Site Key: </td>';
		print '<td><input type="text" name="turnstile_sitekey" value="' . htmlentities( $settings[ 'turnstile_sitekey' ] ) . '" /></td>';
		print '</tr>';
		print '<tr>';
		print '<td>Turnstile Secret Key: </td>';
		print '<td><input type="text" name="turnstile_secretkey" value="' . htmlentities( $settings[ 'turnstile_secretkey' ] ) . '" /></td>';
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
}
