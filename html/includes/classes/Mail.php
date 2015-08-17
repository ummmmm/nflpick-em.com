<?php

class Mail
{
	private $_to;
	private $_subject;
	private $_message;
	private $_headers;
	private $_from;
	private $_replyto;

	public function __construct( $to = null, $subject = null, $message = null, $from = null, $replyto = null, $headers = null )
	{
		$this->_from 	= "NFL Pick-Em <contact@nflpick-em.com>";
		$this->_replyto	= "NFL Pick-Em <contact@nflpick-em.com>";

		if ( !is_null( $to ) )		$this->_to 		= $to;
		if ( !is_null( $subject ) )	$this->_subject = $subject;
		if ( !is_null( $message ) )	$this->_message = $message;
		if ( !is_null( $from ) )	$this->_from 	= $from;
		if ( !is_null( $replyto ) )	$this->_replyto = $replyto;
		if ( !is_null( $headers ) )	$this->_headers = $headers;
	}

	public function to( $to )
	{
		$this->_to = $to;
	}

	public function subject( $subject )
	{
		$this->_subject = $subject;
	}

	public function message( $message )
	{
		$this->_message = $message;
	}

	public function from( $from )
	{
		$this->_from = $from;
	}

	public function replyto( $replyto )
	{
		$this->_replyto = $replyto;
	}

	public function headers( $headers )
	{
		$this->_headers = $headers;
	}

	public function send()
	{
		$headers = array();

		array_push( $headers, "MIME-Version: 1.0" );
		array_push( $headers, "Content-type: text/html; charset=utf-8" );
		array_push( $headers, sprintf( "From: %s", $this->_from ) );
		array_push( $headers, sprintf( "Reply-To: %s", $this->_replyto ) );
		array_push( $headers, $this->_headers );

		$headers = implode( "\r\n", $headers );
		$message = preg_replace( "/\n/", '<br />', $this->_message );

		if ( !mail( $this->_to, $this->_subject, $message, $headers ) )
		{
			return Functions::Error( '#Error#', 'Failed to send mail' );
		}

		return true;
	}
}
