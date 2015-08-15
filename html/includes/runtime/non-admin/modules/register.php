<?php
function Module_Validate( $db, $user, &$register )
{
	$db_settings = new Settings( $db );

	if ( !$db_settings->Load( $settings ) )
	{
		return false;
	}

	if ( $settings[ 'registration' ] !== 1 )
	{
		return Functions::ValidationErrors( array( 'Registration is currently disabled.' ) );
	}

	$register[ 'fname' ] 	= Functions::Post( 'fname' );
	$register[ 'lname' ] 	= Functions::Post( 'lname' );
	$register[ 'email' ] 	= Functions::Post( 'email' );
	$register[ 'cemail' ] 	= Functions::Post( 'cemail' );
	$register[ 'password' ] = Functions::Post( 'password' );
	$register[ 'cpass' ] 	= Functions::Post( 'cpass' );
	$errors 				= array();

	if ( empty( $register[ 'fname' ] ) || ( strlen( $register[ 'fname' ] ) < 3 ) || ( strlen( $register[ 'fname' ] ) > 15 ) )
	{
		array_push( $errors, 'First name must be between 3 and 15 characters.' );
	}
	else if ( !Validation::IsAlpha( $register[ 'fname' ] ) )
	{
		array_push( $errors, 'First name can only contain letters.' );
	}

	if ( empty( $register[ 'lname' ] ) || ( strlen( $register[ 'lname' ] ) < 3 ) || ( strlen( $register[ 'lname' ] ) > 15 ) )
	{
		array_push( $errors, 'Last name must be between 3 and 15 characters.' );
	}
	else if ( !Validation::IsAlpha( $register[ 'lname' ] ) )
	{
		array_push( $errors, 'Last name can only contain letters.' );
	}

	if ( !Validation::Email( $register[ 'email' ] ) )
	{
		array_push( $errors, 'Please enter a valid email address.' );
	}

	if ( Functions::EmailExists ( $db, $register[ 'email' ] ) )
	{
		array_push( $errors, 'The email address is already in use.' );
	}

	if ( $register[ 'email' ] !== $register[ 'cemail' ] )
	{
		array_push( $errors, 'Please make sure email address\' match.' );
	}

	if ( strlen( $register[ 'password' ] ) < 5 )
	{
		array_push( $errors, 'Password must be at least 5 characters.' );
	}

	if ( $register[ 'password' ] !== $register[ 'cpass' ] )
	{
		array_push ( $errors, 'Passwords do not match.' );
	}

	if ( !empty( $errors ) )
	{
		return Functions::ValidationError( $errors );
	}

	return true;
}

function Module_Update( $db, $user, $register_user )
{
	$ruser 		= array( 'fname' 			=> $register_user[ 'fname' ],
						 'lname' 			=> $register_user[ 'lname' ],
						 'email' 			=> $register_user[ 'email' ],
						 'password' 		=> $register_user[ 'password' ],
						 'admin' 			=> 0,
						 'sign_up' 			=> time(),
						 'last_on' 			=> time(),
						 'wins' 			=> 0,
						 'losses' 			=> 0,
						 'paid' 			=> 0,
						 'current_place' 	=> 1,
						 'email_preference' => 1,
						 'force_password' 	=> 0 );

	if ( !$user->Insert( $ruser ) )
	{
		return false;
	}

	if ( !$user->CreateSession() )
	{
		return false;
	}

	header( sprintf( 'Location: %s', INDEX ) );

	return true;
}

function Module_Content( $db, $user )
{
	$db_settings = new Settings( $db );

	$db_settings->Load( $settings );

	if ( $user->id )
	{
		header( sprintf( 'Location: %s', INDEX ) );

		return true;
	}

	if ( $settings[ 'registration' ] !== 1 )
	{
		print '<h1>Registration Off</h1>';
		print '<p>You cannot currently sign up for the NFL Pick-Em League.</p>';

		return true;
	}

	Functions::HandleModuleErrors();
?>
<form action="?module=register" method="post">
  <fieldset>
	  <legend>What's Your Name</legend>
	  <label for="fname">First Name</label>
	  <input type="text" name="fname" id="firstName" title="Please enter your first name" value="<?php print htmlentities( Functions::Post( 'fname' ) ); ?>" />
	  <br />
	  <label for="lname">Last Name</label>
	  <input type="text" name="lname" id="lastName" value="<?php print htmlentities( Functions::Post( 'lname' ) ); ?>" />
  </fieldset>

  <fieldset>
	  <legend>What's Your Email</legend>
	  <label for="email">Email Address</label>
	  <input type="text" name="email" id="email" value="<?php print htmlentities( Functions::Post( 'email' ) ); ?>" />
	  <br />
	  <label for="confirmEmail">Confirm Email Address</label>
	  <input type="text" name="cemail" id="confirmEmail" value="<?php print htmlentities( Functions::Post( 'cemail' ) ); ?>" />
  </fieldset>

  <fieldset>
	  <legend>Choose Your Password</legend>
	  <label for="password">Password</label>
	  <input type="password" name="password" id="password" autocomplete="off" />
	  <br />
	  <label for="confirmPassword">Confirm Password</label>
	  <input type="password" name="cpass" id="confirmPassword" autocomplete="off" />
  </fieldset>

  <input type="hidden" name="action" value="update" />
  <input type="submit" name="register" id="register" value="Register Now!" />
</form>
<?php
	return true;
}
