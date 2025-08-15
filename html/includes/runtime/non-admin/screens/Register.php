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
			$this->addValidationError( 'Registration is currently disabled.' );
			return true;
		}

		$agree						= $this->input()->value_bool_POST( 'agree' );
		$turnstile					= $this->input()->value_str_POST( "cf-turnstile-response" );

		$register[ 'fname' ] 		= $this->input()->value_str_POST( 'fname' );
		$register[ 'lname' ] 		= $this->input()->value_str_POST( 'lname' );
		$register[ 'email' ] 		= $this->input()->value_str_POST( 'email' );
		$register[ 'cemail' ] 		= $this->input()->value_str_POST( 'cemail' );
		$register[ 'password' ] 	= $this->input()->value_str_POST( 'password' );
		$register[ 'cpass' ] 		= $this->input()->value_str_POST( 'cpass' );
		$register[ 'pw_opt_out'	]	= $this->input()->value_bool_POST( 'pw_opt_out', int: true );

		if ( !$agree )
		{
			$this->addValidationError( 'You must agree to the rules.' );
		}

		if ( Functions::Turnstile_Active( $settings ) && !Functions::Turnstile_Validate( $settings, $turnstile ) )
		{
			$this->addValidationError( "Invalid validation token" );
		}

		if ( empty( $register[ 'fname' ] ) || ( strlen( $register[ 'fname' ] ) < 3 ) || ( strlen( $register[ 'fname' ] ) > 15 ) )
		{
			$this->addValidationError( 'First name must be between 3 and 15 characters.' );
		}
		else if ( !Validation::IsAlpha( $register[ 'fname' ] ) )
		{
			$this->addValidationError( 'First name can only contain letters.' );
		}

		if ( empty( $register[ 'lname' ] ) || ( strlen( $register[ 'lname' ] ) < 3 ) || ( strlen( $register[ 'lname' ] ) > 15 ) )
		{
			$this->addValidationError( 'Last name must be between 3 and 15 characters.' );
		}
		else if ( !Validation::IsAlpha( $register[ 'lname' ] ) )
		{
			$this->addValidationError( 'Last name can only contain letters.' );
		}

		if ( !Validation::Email( $register[ 'email' ] ) )
		{
			$this->addValidationError( 'Please enter a valid email address.' );
		}

		if ( ( $db_users->Load_Email( $register[ 'email' ], $null ) ) )
		{
			$this->addValidationError( 'The email address is already in use.' );
		}

		if ( $register[ 'email' ] !== $register[ 'cemail' ] )
		{
			$this->addValidationError( 'Please make sure email address\' match.' );
		}

		if ( strlen( $register[ 'password' ] ) < 5 )
		{
			$this->addValidationError( 'Password must be at least 5 characters.' );
		}

		if ( $register[ 'password' ] !== $register[ 'cpass' ] )
		{
			$this->addValidationError( 'Passwords do not match.' );
		}

		if ( !$this->hasValidationErrors() )
		{
			 $this->setValidationData( $register );
		}

		return true;
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
			return $this->outputInformation( "Registration Disabled", "You currently cannot sign up for the NFL Pick-Em League." );
		}

		$pw_opt_out_checked = $this->input()->value_bool_POST( 'pw_opt_out' ) ? ' checked' : '';
?>
<form action="?screen=register" method="post">
  <fieldset>
	  <legend>What's Your Name</legend>
	  <label for="fname">First Name</label>
	  <input type="text" name="fname" id="firstName" title="Please enter your first name" value="<?php print htmlentities( $this->input()->value_str_POST( 'fname' ) ); ?>" />
	  <br />
	  <label for="lname">Last Name</label>
	  <input type="text" name="lname" id="lastName" value="<?php print htmlentities( $this->input()->value_str_POST( 'lname' ) ); ?>" />
  </fieldset>

  <fieldset>
	  <legend>What's Your Email</legend>
	  <label for="email">Email Address</label>
	  <input type="text" name="email" id="email" value="<?php print htmlentities( $this->input()->value_str_POST( 'email' ) ); ?>" />
	  <br />
	  <label for="confirmEmail">Confirm Email Address</label>
	  <input type="text" name="cemail" id="confirmEmail" value="<?php print htmlentities( $this->input()->value_str_POST( 'cemail' ) ); ?>" />
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
