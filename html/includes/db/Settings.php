<?php

class Settings
{
	public $max_news;
	public $email_validation;
	public $registration;
	public $domain_url;
	public $domain_email;
	public $online;
	public $site_title;
	public $turnstile_sitekey;
	public $turnstile_secretkey;
	
	private $_db;
	
	public function __construct( Database &$db )
	{
		$this->_db = $db;
		
		if ( !$this->Load( $settings ) )
		{
			$settings = $this->Defaults();
		}

		$this->max_news 			= $settings[ 'max_news' ];
		$this->email_validation 	= $settings[ 'email_validation' ];
		$this->registration 		= $settings[ 'registration' ];
		$this->domain_url 			= $settings[ 'domain_url' ];
		$this->domain_email 		= $settings[ 'domain_email' ];
		$this->online 				= $settings[ 'online' ];
		$this->site_title 			= $settings[ 'site_title' ];
		$this->turnstile_sitekey 	= $settings[ 'turnstile_sitekey' ];
		$this->turnstile_secretkey 	= $settings[ 'turnstile_secretkey' ];
	}

	public function Create()
	{
		$sql = "CREATE TABLE settings
				(
					email_validation 	tinyint( 1 ),
					registration 		tinyint( 1 ),
					max_news 			tinyint( 3 ),
					domain_url 			char( 255 ),
					domain_email 		char( 255 ),
					online 				int( 11 ),
					site_title 			char( 255 ),
					login_sleep 		int( 11 ),
					turnstile_sitekey	char( 100 ),
					turnstile_secretkey	char( 100 )
				)";

		if ( $this->_db->query( $sql ) === false )
		{
			return false;
		}

		$default_settings = $this->Defaults();

		return $this->_db->insert( 'settings', $default_settings );
	}

	private function Defaults()
	{
		return array( 'email_validation'	=> 0,
					  'registration' 		=> 1,
					  'max_news' 			=> 4,
					  'domain_url' 			=> sprintf( 'https://%s/', $_SERVER[ 'HTTP_HOST' ] ),
					  'domain_email' 		=> '',
					  'online' 				=> 30,
					  'site_title' 			=> sprintf( 'NFL Pick-Em %d', date( 'Y' ) ),
					  'login_sleep' 		=> 3000,
					  'turnstile_sitekey'	=> '',
					  'turnstile_secretkey'	=> '' );
	}

	public function Load( &$settings )
	{
		return $this->_db->single( 'SELECT * FROM settings', $settings );
	}

	public function Update( &$settings )
	{
		return $this->_db->query( 'UPDATE
									settings
								   SET
									email_validation	= ?,
									registration		= ?,
									max_news			= ?,
									domain_url			= ?,
									domain_email		= ?,
									online				= ?,
									site_title			= ?,
									login_sleep			= ?,
									turnstile_sitekey	= ?,
									turnstile_secretkey	= ?',
									$settings[ 'email_validation' ], $settings[ 'registration' ], $settings[ 'max_news' ], $settings[ 'domain_url' ],
									$settings[ 'domain_email' ], $settings[ 'online' ], $settings[ 'site_title' ], $settings[ 'login_sleep' ],
									$settings[ 'turnstile_sitekey' ], $settings[ 'turnstile_secretkey' ] );

	}
}
