<?php

class Screen_FailedLogins extends Screen_Admin
{
	public function content()
	{
		$failed_login_count = $this->db()->failedlogins()->List_Load( $failed_logins );

		print( '<h1>Failed Logins</h1>' );

		if ( $failed_login_count == 0 )
		{
			print( '<p>No failed logins</p>' );
			return true;
		}

		foreach ( $failed_logins as $failed_login )
		{
			printf( '<p>%s - %s (%s)</p>', htmlentities( $failed_login[ 'email' ] ), Functions::FormatDate( $failed_login[ 'date' ] ), htmlentities( $failed_login[ 'ip' ] ) );
		}

		return true;
	}
}
