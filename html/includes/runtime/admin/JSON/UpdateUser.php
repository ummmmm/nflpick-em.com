<?php

class JSON_UpdateUser extends JSONAdminAction
{
	public function execute()
	{
		$db_users	= $this->db()->users();
		$user_id	= $this->input()->value_int( 'user_id' );
		$first_name	= $this->input()->value_str( 'first_name' );
		$last_name	= $this->input()->value_str( 'last_name' );
		$message	= $this->input()->value_str( 'message' );

		if ( !$db_users->Load( $user_id, $user ) )	throw new NFLPickEmException( 'User does not exist' );
		else if ( $first_name === '' )				throw new NFLPickEmException( 'First name cannot be blank' );
		else if ( $last_name === '' )				throw new NFLPickEmException( 'Last name cannot be blank' );

		$user[ 'fname' ] = $first_name;
		$user[ 'lname' ] = $last_name;

		$user[ 'active' ]	= ( $message === '' ) ? 1 : 0;
		$user[ 'message' ]	= $message;

		$db_users->Update( $user );

		return true;
	}
}
