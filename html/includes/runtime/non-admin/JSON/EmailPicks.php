<?php

require_once( "includes/classes/Mail.php" );

class JSON_EmailPicks extends JSONUserAction
{
	public function execute()
	{
		$db_picks		= $this->db()->picks();
		$db_sent_picks	= $this->db()->sentpicks();
		$db_weeks		= $this->db()->weeks();
		$week			= $this->input()->value_int( 'week_id' );

		if ( !$db_weeks->Load( $week, $null ) )
		{
			throw new NFLPickEmException( 'Week does not exist' );
		}

		$db_picks->List_Load_UserWeek( $this->auth()->getUserID(), $week, $picks );

		if ( count( $picks ) === 0 )
		{
			throw new NFLPickEmException( 'No picks have been selected' );
		}

		$sent = array( 'userid' => $this->auth()->getUserID(), 'week' => $week, 'date' => Functions::Timestamp(), 'picks' => array() );
		$mail = new Mail( $this->auth()->getUser()[ 'email' ], sprintf( "Week %d Picks", $week ) );

		foreach( $picks as $pick )
		{
			array_push( $sent[ 'picks' ], array( 'winner' => $pick[ 'winner' ], 'loser' => $pick[ 'loser' ] ) );
		}

		$mail->message( $this->_confirmationText( $week, $sent[ 'picks' ] ) );

		if ( $mail->send() === false )
		{
			throw new NFLPickEmException( 'The email failed to send. Please try again later.' );
		}

		$insert = array( 'user_id' => $this->auth()->getUserID(), 'picks' => json_encode( $sent ), 'week' => $week, 'active' => 1 );
		$db_sent_picks->Insert( $insert );

		return $this->setData( sprintf( 'Your picks for week %d have been sent.', $week ) );
	}

	private function _confirmationText( &$week, &$picks )
	{
		$output = sprintf( 'Here are your picks for week %d. Please save them for your records.<br /><br />', $week );

		foreach( $picks as $pick )
		{
			$output .= sprintf( 'You have picked the <b>%s</b> to beat the <b>%s</b><br />', htmlentities( $pick[ 'winner' ] ), htmlentities( $pick[ 'loser' ] ) );
		}

		return $output;
	}
}
