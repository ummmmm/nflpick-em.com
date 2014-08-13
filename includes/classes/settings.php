<?php
class Settings1
{
	public $max_news;
	public $poll_options;
	public $email_validation;
	public $registration;
	public $domain_url;
	public $domain_email;
	public $online;
	public $site_title;
	
	private $db;
	
	public function __construct( &$db )
	{
		$this->db = $db;
		
		$this->Settings_Load( $settings );
		
		$this->max_news 		= $settings[ 'max_news' ];
		$this->poll_options 	= $settings[ 'poll_options' ];
		$this->email_validation = $settings[ 'email_validation' ];
		$this->registration 	= $settings[ 'registration' ];
		$this->domain_url 		= $settings[ 'domain_url' ];
		$this->domain_email 	= $settings[ 'domain_email' ];
		$this->online 			= $settings[ 'online' ];
		$this->site_title 		= $settings[ 'site_title' ];
	}
	
	private function Settings_Load( &$settings )
	{
		return $this->db->single( 'SELECT * FROM settings', $settings );
	}
}
?>