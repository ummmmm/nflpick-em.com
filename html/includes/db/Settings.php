<?php

class Settings
{
	public $max_news;
	public $poll_options;
	public $email_validation;
	public $registration;
	public $domain_url;
	public $domain_email;
	public $online;
	public $site_title;
	
	private $_db;
	
	public function __construct( Database &$db )
	{
		$this->_db = $db;
		
		if ( $this->Load( $settings ) )
		{
			$this->max_news 		= $settings[ 'max_news' ];
			$this->poll_options 	= $settings[ 'poll_options' ];
			$this->email_validation = $settings[ 'email_validation' ];
			$this->registration 	= $settings[ 'registration' ];
			$this->domain_url 		= $settings[ 'domain_url' ];
			$this->domain_email 	= $settings[ 'domain_email' ];
			$this->online 			= $settings[ 'online' ];
			$this->site_title 		= $settings[ 'site_title' ];
		}
	}

	public function Create()
	{
		$sql = "CREATE TABLE settings
				(
					poll_options 		tinyint( 3 ),
					email_validation 	tinyint( 1 ),
					registration 		tinyint( 1 ),
					max_news 			tinyint( 3 ),
					domain_url 			char( 255 ),
					domain_email 		char( 255 ),
					online 				int( 11 ),
					site_title 			char( 255 ),
					login_sleep 		int( 11 )
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
		return array( 'poll_options' 		=> 10,
					  'email_validation'	=> 0,
					  'registration' 		=> 1,
					  'max_news' 			=> 4,
					  'domain_url' 			=> '',
					  'domain_email' 		=> '',
					  'online' 				=> 30,
					  'site_title' 			=> '',
					  'login_sleep' 		=> 3000 );
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
									poll_options		= ?,
									email_validation	= ?,
									registration		= ?,
									max_news			= ?,
									domain_url			= ?,
									domain_email		= ?,
									online				= ?,
									site_title			= ?,
									login_sleep			= ?',
									$settings[ 'poll_options' ], $settings[ 'email_validation' ], $settings[ 'registration' ], $settings[ 'max_news' ],
									$settings[ 'domain_url' ], $settings[ 'domain_email' ], $settings[ 'online' ], $settings[ 'site_title' ], $settings[ 'login_sleep' ] );

	}
}
