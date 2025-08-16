<?php

class JSON_UpdateUser extends JSONAdminAction
{
	public function execute()
	{
		$db_users			= $this->db()->users();
		$db_reset_passwords = $this->db()->resetpasswords();
		$user_id			= $this->input()->value_int( 'user_id' );
		$first_name			= $this->input()->value_str( 'first_name' );
		$last_name			= $this->input()->value_str( 'last_name' );
		$password			= $this->input()->value_str( 'password' );
		$verify_password	= $this->input()->value_str( 'verify_password' );
		$message			= $this->input()->value_str( 'message' );

		if ( !$db_users->Load( $user_id, $user ) )	throw new NFLPickEmException( 'User does not exist' );
		else if ( $first_name === '' )				throw new NFLPickEmException( 'First name cannot be blank' );
		else if ( $last_name === '' )				throw new NFLPickEmException( 'Last name cannot be blank' );

		$user[ 'fname' ] = $first_name;
		$user[ 'lname' ] = $last_name;

		$user[ 'active' ]	= ( $message === '' ) ? 1 : 0;
		$user[ 'message' ]	= $message;

		if ( $password != '' )
		{
			if ( strlen( $password ) < 5 )				throw new NFLPickEmException( 'Password must be at least 5 characters' );
			else if ( $password !== $verify_password )	throw new NFLPickEmException( 'Passwords do not match' );

			$record	= array( 'userid' => $user[ 'id' ], 'password' => Security::password_hash( $password ) );

			$db_reset_passwords->Delete_User( $user[ 'id' ] );
			$db_reset_passwords->Insert( $record );

			$user[ 'force_password' ] = 1;
		}

		$db_users->Update( $user );

		return true;
	}
}
