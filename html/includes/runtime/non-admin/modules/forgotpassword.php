<?php

require_once( "includes/classes/Mail.php" );

function Module_Validate( &$db, &$user, &$validation )
{
	$action = Functions::Get( 'action' );

	if ( $action === 'changepassword' )
	{
		$password 	= trim( $_POST[ 'password' ] );
		$c_password = trim( $_POST[ 'c_password' ] );
		$token		= trim( $_POST[ 'token' ] );
		$errors		= array();

		if ( !Sessions::Validate( $db, $user->id, $token ) )
		{
			array_push( $errors, 'You do not have a valid token to complete this action.' );
		}

		if ( strlen( $password ) < 5 )
		{
			array_push( $errors, 'Password must be at least 5 characters.' );
		}
		else if ( $password !== $c_password )
		{
			array_push( $errors, 'Passwords do not match.' );
		}

		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}

		$validation[ 'password' ] = $password;

		return true;
	}

	if ( $action === '' )
	{
		$email 	= trim( $_POST[ 'email' ] );

		if ( !$user->Load_Email( $email, $loaded_user ) )
		{
			return Functions::ValidationError( array( 'The email address could not be found.' ) );
		}

		$validation = $loaded_user;

		return true;
	}

	return true;
}

function Module_Update( &$db, &$user, &$validation )
{
	$reset_passwords 	= new Reset_Passwords( $db );
	$action 			= Functions::Get( 'action' );

	if ( $action == 'changepassword' )
	{
		$user->account[ 'password' ]		= Functions::HashPassword( $validation[ 'password' ] );
		$user->account[ 'force_password' ] 	= 0;

		if ( !$user->Update( $user->account ) )
		{
			return false;
		}

		if ( !$reset_passwords->Delete_User( $user->id ) )
		{
			return false;
		}

		$_SESSION[ 'updated' ] = true;

		return Functions::Module_Updated( 'Your password has been updated.' );
	}

	if ( $action === '' )
	{
		$userid							= $validation[ 'id' ];
		$temp_password 					= Functions::Random( 10 );
		$info 							= array( 'userid' => $userid, 'password' => Functions::HashPassword( $temp_password ) );

		$validation[ 'force_password' ] = 1;

		if ( !$reset_passwords->Delete_User( $userid ) )
		{
			return false;
		}

		if ( !$reset_passwords->Insert( $info ) )
		{
			return false;
		}

		if ( !$user->Update( $validation ) )
		{
			return false;
		}

		$email = new Mail( $validation[ 'email' ], 'Forgot Password', 'Your temporary password is <span style="font-weight: bold;">' . $temp_password . '</span>' );

		if ( $email->send() === false )
		{
			return false;
		}

		return Functions::Module_Updated( 'A temporary password has been emailed to you.' );
	}

	return true;
}

function Module_Content( &$db, &$user, &$settings )
{
	if ( $user->logged_in && !$user->account[ 'force_password' ] && !isset( $_SESSION[ 'updated' ] ) )
	{
		header( sprintf( 'location: %s', INDEX ) );
		return false;
	}
	else if ( isset( $_SESSION[ 'updated' ] ) )
	{
		unset( $_SESSION[ 'updated' ] );
	}

	$action = Functions::Get( 'action' );

	if ( $user->logged_in && $action == 'changepassword' )
	{
		return ChangePassword( $user );
	}

	if ( $action === '' )
	{
		return ForgotPassword();
	}

	return true;
}

function ForgotPassword()
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	print <<<EOT
		<form name="forgotPass" action="" method="post" id="forgotPass">
			<fieldset>
				<legend>Forgotten Password</legend>
				<label for="email">Email Address</label>
				<input type="text" name="email" id="email" />
				<br />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="forgotPass" id="forgotPass" value="Get Password Now!" />
			</fieldset>
		</form>
EOT;

	return true;
}

function ChangePassword( &$user )
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	print <<<EOT
		<form name="update" action="" method="post" id="update">
			<fieldset>
				<legend>Update Password</legend>
				<label for="pass">New Password</label>
				<input type="password" name="password" id="newPass" />
				<br />
				<label for="cpass">Confirm Passowrd</label>
				<input type="password" name="c_password" id="cUpdatePass" />
				<br />
				<input type="hidden" name="token" value="{$user->token}" />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="updatePass" id="updatePass" value="Update Password Now!" />
			</fieldset>
		</form>
EOT;

	return true;
}
?>
