<?php

class DatabaseTableSettings extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE settings
				(
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

		return $this->query( $sql );
	}

	public function Insert( &$settings )
	{
		return $this->query( 'INSERT INTO settings
							  ( registration, max_news, domain_url, domain_email, online, site_title, login_sleep, turnstile_sitekey, turnstile_secretkey )
							  VALUES
							  ( ?, ?, ?, ?, ?, ?, ?, ?, ? )',
							  $settings[ 'registration' ], $settings[ 'max_news' ], $settings[ 'domain_url' ], $settings[ 'domain_email' ], $settings[ 'online' ],
							  $settings[ 'site_title' ], $settings[ 'login_sleep' ], $settings[ 'turnstile_sitekey' ], $settings[ 'turnstile_secretkey' ] );
	}

	public function Load( &$settings )
	{
		return $this->single( 'SELECT * FROM settings', $settings );
	}

	public function Update( &$settings )
	{
		return $this->query( 'UPDATE
								settings
							  SET
								registration		= ?,
								max_news			= ?,
								domain_url			= ?,
								domain_email		= ?,
								online				= ?,
								site_title			= ?,
								login_sleep			= ?,
								turnstile_sitekey	= ?,
								turnstile_secretkey	= ?',
							  $settings[ 'registration' ], $settings[ 'max_news' ], $settings[ 'domain_url' ],
							  $settings[ 'domain_email' ], $settings[ 'online' ], $settings[ 'site_title' ], $settings[ 'login_sleep' ],
							  $settings[ 'turnstile_sitekey' ], $settings[ 'turnstile_secretkey' ] );

	}
}
