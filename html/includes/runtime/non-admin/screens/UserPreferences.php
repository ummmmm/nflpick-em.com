<?php

class Screen_UserPreferences extends Screen_User
{
	public function requirements()
	{
		return array( "user" => true, "token" => true );
	}

	public function validate()
	{
		$email_preference = Functions::Post_Int( 'email_preference' );

		return $this->setValidationData( array( 'email_preference' => $email_preference ) );
	}

	public function update( $data )
	{
		$db_users					= new Users( $this->_db );
		$user						= $this->_auth->getUser();
		$user[ 'email_preference' ]	= $data[ 'email_preference' ] ? 1 : 0;

		if ( !$db_users->Update( $user ) )
		{
			return $this->setDBError();
		}

		return $this->setUpdateMessage( "Preferences saved." );
	}

	public function content()
	{
		if ( isset( $_POST[ 'update' ] ) )	$checked = $_POST[ 'email_preference' ] ? 'checked' : '';
		else								$checked = $this->_auth->getUser()[ 'email_preference' ] ? 'checked' : '';

		$token = htmlentities( $this->_auth->getToken() );

		print <<<EOT
		<h1>User Preferences</h1>
		<form method="post">
			<label><input type="checkbox" name="email_preference" value="1" {$checked} /> Email Pick Reminders</label><br />
			<input type="hidden" name="update" value="1" />
			<input type="hidden" name="token" value="{$token}" />
			<input type="submit" value="Update" />
		</form>
EOT;
		return true;
	}
}
