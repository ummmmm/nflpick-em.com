<?php

require_once( "includes/classes/Mail.php" );

class JSON_EmailPicks implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'user' => true, 'token' => true );
	}

	public function execute()
	{
		$db_picks		= new Picks( $this->_db );
		$db_sent_picks	= new Sent_Picks( $this->_db );
		$db_weeks		= new Weeks( $this->_db );
		$week			= Functions::Post( 'week' );

		if ( !$db_weeks->Load( $week, $null ) )
		{
			return $this->_json->setError( array( 'NFL-EMAILPICKS-1', sprintf( 'Failed to load week %d', $week ) ) );
		}

		$count = $db_picks->UserWeekList_Load( $this->_auth->userID, $week, $picks );

		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}

		$sent = array( 'userid' => $this->_auth->userID, 'week' => $week, 'date' => Functions::Timestamp(), 'picks' => array() );
		$mail = new Mail( $this->_auth->user[ 'email' ], sprintf( "Week %d Picks", $week ) );

		foreach( $picks as $pick )
		{
			if ( $pick[ 'winner_pick' ] === 0 )
			{
				continue;
			}

			array_push( $sent[ 'picks' ], array( 'winner' => $pick[ 'winner' ], 'loser' => $pick[ 'loser' ] ) );
		}

		$mail->message( $this->_confirmationText( $week, $sent[ 'picks' ] ) );

		if ( $mail->send() === false )
		{
			return $this->_json->setError( array( 'NFL-EMAILPICKS-3', 'The email failed to send. Please try again later.' ) );
		}

		$insert = array( 'user_id' => $this->_auth->userID, 'picks' => json_encode( $sent ), 'week' => $week );

		if ( !$db_sent_picks->Insert( $insert ) )
		{
			return $this->_json->DB_Error();
		}

		return $this->_json->setData( sprintf( 'Your picks for week %d have been sent.', $week ) );
	}

	private function _confirmationText( &$week, &$picks )
	{
		$output = sprintf( 'Here are your picks for week %d Please save them for your records.<br /><br />', $week );

		foreach( $picks as $pick )
		{
			$output .= sprintf( 'You have picked the <b>%s</b> to beat the <b>%s</b><br />', $pick[ 'winner' ], $pick[ 'loser' ] );
		}

		return $output;
	}
}
