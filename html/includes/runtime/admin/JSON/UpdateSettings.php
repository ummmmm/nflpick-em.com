<?php

class JSON_UpdateSettings implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_settings = new Settings( $this->_db );
		$db_sessions = new Sessions( $this->_db );

		if ( !$db_settings->Load( $settings ) )
		{
			return $this->_json->DB_Error();
		}

		$settings[ 'email_validation' ]	= Functions::Post_Active( 'email_validation' );
		$settings[ 'registration' ]		= Functions::Post_Active( 'registration' );
		$settings[ 'poll_options' ]		= Functions::Post_Int( 'poll_options' );
		$settings[ 'max_news' ]			= Functions::Post_Int( 'max_news' );
		$settings[ 'online' ]			= Functions::Post_Int( 'online' );
		$settings[ 'login_sleep' ]		= Functions::Post_Int( 'login_sleep' );
		$settings[ 'domain_url' ]		= Functions::Post( 'domain_url' );
		$settings[ 'domain_email' ]		= Functions::Post( 'domain_email' );
		$settings[ 'site_title' ]		= Functions::Post( 'site_title' );

		if ( $settings[ 'poll_options' ] <= 0 )			return $this->_json->setError( array( '#Error#', 'Poll Options must be greater than 0' ) );
		elseif ( $settings[ 'max_news' ] <= 0 )			return $this->_json->setError( array( '#Error#', 'Max News must be greater than 0' ) );
		elseif ( $settings[ 'online' ] <= 0 )			return $this->_json->setError( array( '#Error#', 'Online must be greater than 0' ) );
		elseif ( $settings[ 'login_sleep' ] <= 0 )		return $this->_json->setError( array( '#Error#', 'Login Sleep must be greater than 0' ) );
		elseif ( $settings[ 'domain_url' ] === '' )		return $this->_json->setError( array( '#Error#', 'Domain URL cannot be blank' ) );
		elseif ( $settings[ 'site_title' ] === '' )		return $this->_json->setError( array( '#Error#', 'Site Title cannot be blank' ) );
		
		if ( !$db_settings->Update( $settings ) )
		{
			return $this->_json->DB_Error();
		}
		
		return true;
	}
}