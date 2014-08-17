<?php
function Module_Validate( &$db, &$user, &$validation )
{
	$action = trim( $_GET[ 'action' ] );

	if ( $action == 'changeemail' )
	{
		$email 	= trim( $_POST[ 'email' ] );
		$cemail = trim( $_POST[ 'cemail' ] );
		$pass 	= trim( $_POST[ 'pass' ] );
		$errors	= array();

		if ( !Validation::Email( $email ) )
		{
			array_push( $errors, 'Invalid email address.' );
		}
		else if ( $email != $cemail )
		{
			array_push( $errors, 'Email address\' do not match.' );
		}
		else if ( Functions::EmailExists( $db, $email ) )
		{
			array_push( $errors, 'The email address is already in use.' );
		}

		if ( !Functions::VerifyPassword( $pass, $user->account[ 'password' ] ) )
		{
			array_push( $errors, 'Invalid password.' );
		}

		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}

		$validation[ 'email' ] = $email;
	}

	if ( $action == 'changepassword' )
	{
		$new_password 	= trim( $_POST[ 'new_password' ] );
		$c_new_password = trim( $_POST[ 'c_new_password' ] );
		$old_password	= trim( $_POST[ 'old_password' ] );
		$errors			= array();

		if ( strlen( $new_password ) < 5 )
		{
			array_push( $errors, 'Password must be at least 5 characters.' );
		}
		else if ( $new_password !== $c_new_password )
		{
			array_push( $errors, 'Passwords do not match.' );
		}

		if ( !Functions::VerifyPassword( $old_password, $user->account[ 'password' ] ) )
		{
			array_push( $errors, 'Your old password does not match the password on file.' );
		}

		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}

		$validation[ 'password' ] = Functions::HashPassword( $new_password );
	}

	return true;
}

function Module_Update( &$db, &$user, $validation )
{
	$action = trim( $_GET[ 'action' ] );

	if ( $action == 'changeemail' )
	{
		$user->account[ 'email' ] = $validation[ 'email' ];

		if ( !$user->Update( $user->account ) )
		{
			return false;
		}

		return Functions::Module_Updated( 'Your email address has been updated.' );
	}

	if ( $action == 'changepassword' )
	{
		$user->account[ 'password' ] = $validation[ 'password' ];

		if ( !$user->Update( $user->account ) )
		{
			return false;
		}

		return Functions::Module_Updated( 'Your password has been updated.' );
	}

	return true;
}

function Module_Content( &$db, &$user )
{
	Validation::User( $user->id );

	$action = trim( $_GET[ 'action' ] );

	if ( empty( $action ) )
	{
		return PageLayout();
	}

	if ( $action == 'emailpreferences' )
	{
		return EmailPreferences( $user );
	}

	if ( $action == 'changeemail' )
	{
		return ChangeEmail();
	}

	if ( $action == 'changepassword' )
	{
		return ChangePassword();
	}

	return true;
}

function PageLayout()
{
	print '<h1>Control Panel</h1>';
	print <<<EOT
		<p><a href="?module=makepicks" title="Make Picks">Make Picks</a></p>
		<p><a href="?module=controlpanel&action=changepassword" title="Change Password">Change Password</a></p>
		<p><a href="?module=controlpanel&action=changeemail" title="Change Email">Change Email</a></p>
		<p><a href="?module=contact" title="File Report">Support Help</a></p>
		<p><a href="?module=controlpanel&action=emailpreferences" title="Email Preferences">Email Preferences</a></p>
EOT;

	return true;
}

function EmailPreferences( &$user )
{
	$value = ( $user->account[ 'email_preference' ] ) ? 'Disable Email Notifications' : 'Enable Email Notifications';

	print '<h1>Email Preferences</h1>';
	print '<input type="button" id= "emailpreferences" value="' . $value . '" onclick="$.fn.updateEmailPreferences();" />';

	return true;
}

function ChangeEmail()
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	print <<<EOT
		<form name="email" action="" method="post" id="email">
			<fieldset>
				<legend>Reset Email</legend>
				<label for="email">New Email</label>
				<input type="text" name="email" id="newEmail" />
				<br />
				<label for="cemail">Confirm Email</label>
				<input type="text" name="cemail" id="confirmEmail" />
				<br />
				<label for="pass">Current Password</label>
				<input type="password" name="pass" id="pass" /><br />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="changeEmail" id="changeEmail" value="Change Email Now" />
			</fieldset>
		</form>
EOT;
	return true;
}

function ChangePassword()
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	print <<<EOT
		<form name="change" action="" method="post" id="change">
			<fieldset>
				<legend>Change Password</legend>
				<label for="oldPass">Old Password</label>
				<input type="password" name="old_password" id="oldPass" />
				<br />
				<label for="newPass">New Password</label>
				<input type="password" name="new_password" id="newPass" />
				<br />
				<label for="confirmNewPass">Confirm New Password</label>
				<input type="password" name="c_new_password" id="confirmPass" />
				<br />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="changePass" id="changePass" value="Change Password Now!" />
			</fieldset>
		</form>
EOT;
	return true;
}
?>
