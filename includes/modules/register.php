<?php
function Module_Validate( $db, $user, &$register )
{
	if ( Settings::Load( $db, $settings ) === 1 && array_key_exists( 'registration', $settings ) && $settings[ 'registration' ] !== 1 )
	{
		return Functions::ValidationErrors( array( 'Registration is currently disabled.' ) );
	}

	$register[ 'fname' ] 	= trim( $_POST[ 'fname' ] );
	$register[ 'lname' ] 	= trim( $_POST[ 'lname' ] );
	$register[ 'email' ] 	= trim( $_POST[ 'email' ] );
	$register[ 'cemail' ] 	= trim( $_POST[ 'cemail' ] );
	$register[ 'password' ] = trim( $_POST[ 'password' ] );
	$register[ 'cpass' ] 	= trim( $_POST[ 'cpass' ] );
	$errors 				= array();

	if ( empty( $register[ 'fname' ] ) || ( strlen( $register[ 'fname' ] ) < 3 ) || ( strlen( $register[ 'fname' ] ) > 15 ) )
	{
		array_push( $errors, 'First name must be between 3 and 15 characters.' );
	} else if ( !Validation::IsAlpha( $register[ 'fname' ] ) )
	{
		array_push( $errors, 'First name can only contain letters.' );
	}

	if ( empty( $register[ 'lname' ] ) || ( strlen( $register[ 'lname' ] ) < 3 ) || ( strlen( $register[ 'lname' ] ) > 15 ) )
	{
		array_push( $errors, 'Last name must be between 3 and 15 characters.' );
	} else if ( !Validation::IsAlpha( $register[ 'lname' ] ) )
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
	if ( !$user->Insert( $register_user ) )
	{
		return Functions::Error( 'NFL-REGISTER-0', 'An error has occurred creating your account. Please try again later.' );
	}

	if ( !Picks::Insert_All( $db, $register_user[ 'id' ] ) )
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

function Module_Content( $db, $user, $settings )
{
	if ( $user->id )
	{
		header( sprintf( 'Location: %s', INDEX ) );

		return true;
	}

	if ( !$settings->registration )
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
?>
