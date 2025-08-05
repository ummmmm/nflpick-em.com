<?php

class JSON_UpdateUser extends JSONAdminAction
{
	public function execute()
	{
		$db_users	= $this->db()->users();
		$user_id	= Functions::Post_Int( 'user_id' );
		$first_name	= Functions::Post( 'first_name' );
		$last_name	= Functions::Post( 'last_name' );
		$message	= Functions::Post( 'message' );

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
