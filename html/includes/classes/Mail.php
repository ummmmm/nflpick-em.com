<?php

class Mail
{
	private $to;
	private $subject;
	private $message;
	private $headers;
	private $from 		= 'NFL Pick-Em <contact@nflpick-em.com>';
	private $replyto 	= 'NFL Pick-Em <contact@nflpick-em.com>';
	
	public function __construct( $to = null, $subject = null, $message = null, $from = null, $replyto = null, $headers = null )
	{
		if ( !is_null( $to ) )		$this->to 		= $to;
		if ( !is_null( $subject ) )	$this->subject 	= $subject;		
		if ( !is_null( $message ) )	$this->message 	= $message;
		if ( !is_null( $from ) )	$this->from 	= $from;
		if ( !is_null( $replyto ) )	$this->replyto 	= $replyto;
		if ( !is_null( $headers ) )	$this->headers 	= $headers;
	}
	
	public function to( $to )
	{
		$this->to = $to;
	}
	
	public function subject( $subject )
	{
		$this->subject = $subject;
	}
	
	public function message( $message )
	{
		$this->message = $message;
	}
	
	public function from( $from )
	{
		$this->from = $from;
	}
	
	public function replyto( $replyto )
	{
		$this->replyto = $replyto;
	}
	
	public function headers( $headers )
	{
		$this->headers = $headers;
	}
	
	public function send()
	{
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: ' . $this->from . "\r\n";
		$headers .= 'Reply-To: ' . $this->replyto . "\r\n";
		$headers .= $this->headers;
		
		$message = preg_replace( "/\n/", '<br />', $this->message );
		
		if ( !mail( $this->to, $this->subject, $message, $headers ) )
		{
			return Functions::Error( '#Error#', 'Failed to send mail' );
		}

		return true;
	}
}
