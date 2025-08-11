<?php

class Screen_Register extends Screen
{
	public function head()
	{
		print( <<<EOF
			<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
EOF );
		return true;
	}

	public function validate()
	{
		$settings	= $this->settings();
		$db_users	= $this->db()->users();

		if ( $settings[ 'registration' ] != 1 )
		{
			return $this->setValidationErrors( array( "Registration is currently disabled." ) );
		}

		$agree						= $this->input()->value_POST_bool( 'agree' );
		$turnstile					= $this->input()->value_POST_str( "cf-turnstile-response" );

		$register[ 'fname' ] 		= $this->input()->value_POST_str( 'fname' );
		$register[ 'lname' ] 		= $this->input()->value_POST_str( 'lname' );
		$register[ 'email' ] 		= $this->input()->value_POST_str( 'email' );
		$register[ 'cemail' ] 		= $this->input()->value_POST_str( 'cemail' );
		$register[ 'password' ] 	= $this->input()->value_POST_str( 'password' );
		$register[ 'cpass' ] 		= $this->input()->value_POST_str( 'cpass' );
		$register[ 'pw_opt_out'	]	= $this->input()->value_POST_bool( 'pw_opt_out', int: true );
		$errors 					= array();

		if ( !$agree )
		{
			array_push( $errors, 'You must agree to the rules.' );
		}

		if ( Functions::Turnstile_Active( $settings ) && !Functions::Turnstile_Validate( $settings, $turnstile ) )
		{
			array_push( $errors, "Invalid validation token" );
		}

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

		if ( ( $db_users->Load_Email( $register[ 'email' ], $null ) ) )
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
			return $this->setValidationErrors( $errors );
		}

		return $this->setValidationData( $register );
	}

	public function update( $data )
	{
		$settings	= $this->settings();
		$db_users	= $this->db()->users();
		$user 		= array( 'fname' 			=> $data[ 'fname' ],
							 'lname' 			=> $data[ 'lname' ],
							 'email' 			=> $data[ 'email' ],
							 'password' 		=> Security::password_hash( $data[ 'password' ] ),
							 'admin' 			=> 0,
							 'sign_up' 			=> time(),
							 'last_on' 			=> time(),
							 'wins' 			=> 0,
							 'losses' 			=> 0,
							 'paid' 			=> 0,
							 'current_place' 	=> 1,
							 'email_preference' => 1,
							 'force_password' 	=> 0,
							 'active'			=> 1,
							 'message'			=> '',
							 'pw_opt_out'		=> $data[ 'pw_opt_out' ] );

		$db_users->Insert( $user );

		$this->auth()->login( $user[ 'id' ] );

		header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );

		return true;
	}

	public function content()
	{
		if ( $this->auth()->getUserID() )
		{
			$settings = $this->settings();

			header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );
			return true;
		}

		$settings = $this->settings();

		if ( $settings[ 'registration' ] != 1 )
		{
			return Functions::Information( "Registration Disabled", "You currently cannot sign up for the NFL Pick-Em League." );
		}

		$pw_opt_out_checked = $this->input()->value_POST_bool( 'pw_opt_out' ) ? ' checked' : '';
?>
<form action="?screen=register" method="post">
  <fieldset>
	  <legend>What's Your Name</legend>
	  <label for="fname">First Name</label>
	  <input type="text" name="fname" id="firstName" title="Please enter your first name" value="<?php print htmlentities( $this->input()->value_POST_str( 'fname' ) ); ?>" />
	  <br />
	  <label for="lname">Last Name</label>
	  <input type="text" name="lname" id="lastName" value="<?php print htmlentities( $this->input()->value_POST_str( 'lname' ) ); ?>" />
  </fieldset>

  <fieldset>
	  <legend>What's Your Email</legend>
	  <label for="email">Email Address</label>
	  <input type="text" name="email" id="email" value="<?php print htmlentities( $this->input()->value_POST_str( 'email' ) ); ?>" />
	  <br />
	  <label for="confirmEmail">Confirm Email Address</label>
	  <input type="text" name="cemail" id="confirmEmail" value="<?php print htmlentities( $this->input()->value_POST_str( 'cemail' ) ); ?>" />
  </fieldset>

  <fieldset>
	  <legend>Choose Your Password</legend>
	  <label for="password">Password</label>
	  <input type="password" name="password" id="password" />
	  <br />
	  <label for="confirmPassword">Confirm Password</label>
	  <input type="password" name="cpass" id="confirmPassword" />
  </fieldset>

  <fieldset>
  	<legend>Additional</legend>
  	<label><input type="checkbox" name="agree" value="1" /> I have read and agree to the <a href="rules.pdf" target="_blank">Rules</a></label>
  	<label><input type="checkbox" name="pw_opt_out" value="1" <?php print $pw_opt_out_checked; ?> /> Opt-out of the perfect week pool</label>
  </fieldset>

  <div class="cf-turnstile" data-sitekey="<?php print htmlentities( $settings[ 'turnstile_sitekey' ] ); ?>" data-appearance="interaction-only"></div>
  <input type="hidden" name="update" value="1" />
  <input type="submit" name="register" id="register" value="Register" />
</form>
<?php
	return true;
	}
}
