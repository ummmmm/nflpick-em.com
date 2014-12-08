<?php
function Module_Validate( &$db, &$user, &$validation )
{
	$validation[ 'email' ] 		= Functions::Post( 'email' );
	$validation[ 'password'] 	= Functions::Post( 'password' );

	if ( !$user->LoginValidate( $validation[ 'email' ], $validation[ 'password' ] ) )
	{
		if ( Settings::Load( $db, $settings ) === 1 && array_key_exists( 'login_sleep', $settings ) )
		{
			usleep( $settings[ 'login_sleep' ] * 1000 ); // usleep uses microseconds, not milliseconds
		}

		FailedLogin::Insert( $db, $validation[ 'email' ] );

		return Functions::ValidationError( array( 'Invalid email or password' ) );
	}

	return true;
}

function Module_Update( &$db, &$user, &$validation )
{
	if ( !$user->CreateSession() )
	{
		return false;
	}

	header( sprintf( 'Location: %s', INDEX ) );

	return true;
}

function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	$jquery = "\$( '#loginEmail' ).focus();";

	return true;
}

function Module_Content( $db, $user, $settings )
{
	if ( $user->id )
	{
		header( sprintf( 'Location: %s', INDEX ) );

		return true;
	}

	Functions::HandleModuleErrors();

	$email = Functions::Post( 'email' );

?>
	<form action="?module=login" method="post">
	  <fieldset>
			<legend>Enter Your Login Info</legend>
			<label for="email">Email Address</label>
			<input type="text" name="email" id="loginEmail" value="<?php print $email; ?>" />
			<br />
			<label for="password">Password</label>
			<input type="password" name="password" id="loginPassword" value="" autocomplete="off" />
			<br />
			<input type="hidden" name="action" value="update" />
			<input type="submit" name="login" id="login" value="Login Now!" /><br />
			<a href="?module=forgotpassword" title="Forgotten Password?">Forgotten Password?</a>
	  </fieldset>
	</form>
<?php
	return true;
}
?>
