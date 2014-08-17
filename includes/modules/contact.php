<?php
function Module_Validate( $db, $user, &$validation )
{
	$validation[ 'name' ] 		= Functions::Post( 'name' );
	$validation[ 'email' ] 		= Functions::Post( 'email' );
	$validation[ 'subject' ] 	= Functions::Post( 'subject' );
	$validation[ 'message' ] 	= Functions::Post( 'message' );

	$errors = array();

	if ( $validation[ 'name' ] == '')
	{
		array_push( $errors, 'Please enter your name.' );
	}

	if ( !Validation::Email( $validation[ 'email' ] ) )
	{
		array_push( $errors, 'Please enter a valid email address.' );
	}

	if ( $validation[ 'subject' ] == '' )
	{
		$validation[ 'subject' ] = 'No Subject';
	}

	if ( $validation[ 'message' ] == '' )
	{
		array_push( $errors, 'Please enter a message.' );
	}

	if ( !empty( $errors ) )
	{
		return Functions::ValidationError( $errors );
	}

	return true;
}

function Module_Update( $db, $user, $validation )
{
	if ( !Settings::Load( $db, $settings ) )
	{
		return false;
	}

	$mail = new Mail( $settings[ 'domain_email' ], $validation[ 'subject' ], "Name: {$validation[ 'name' ] }<br />Email: {$validation[ 'email' ]}<br /><br />Message: {$validation[ 'message' ]}" );

	if ( $mail->send() === false )
	{
		return false;
	}

	return Functions::Module_Updated( 'Your message has been sent and you should receive a response within the next 24 hours.' );
}

function Module_Content( $db, $user )
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	$name 		= Functions::Post( 'name' );
	$email		= Functions::Post( 'email' );
	$subject	= Functions::Post( 'subject' );
	$message	= Functions::Post( 'message' );

	if ( $user->logged_in )
	{
		$name 	= ( !$name ) ? $user->account[ 'name' ] : $name;
		$email	= ( !$email ) ? $user->account[ 'email' ] : $email;
	}
?>
	<form name="contact" action="?module=contact" method="post" id="contact">
		<fieldset>
			<legend>Contact Form</legend>
			<label for="name">Your Name</label>
			<input type="text" name="name" id="name" value="<?php print htmlentities( $name ); ?>" />
			<br />
			<label for="email">Your Email</label>
			<input type="text" name="email" id="email" value="<?php print htmlentities( $email ); ?>" />
			<br />
			<label for="subject">Subject</label>
			<input type="text" name="subject" id="subject" value="<?php print htmlentities( $subject ); ?>" />
			<br />
			<label for="message">Message</label>
			<textarea name="message" cols="50" rows="10" id="message"><?php print htmlentities( $message ); ?></textarea>
			<br />
			<input type="hidden" name="action" value="update" />
			<input type="submit" name="contact" id="contact" value="Send Me Now!" />
		</fieldset>
	</form>
<?php
	return true;
}
?>
