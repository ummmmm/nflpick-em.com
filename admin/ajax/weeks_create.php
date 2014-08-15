<?php
function Module_JSON( &$db, &$user )
{
	$token 	= Functions::Get( 'token' );
	
	$count = Weeks::List_Load( $db, $weeks );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count !== 0 )
	{
		return JSON_Response_Error( 'NFL-WEEKS_CREATE-0', 'The weeks table must be empty' );
	}
	
	$date = new DateTime( '9/7/2014 10:00am', new DateTimeZone( 'America/Los_Angeles' ) ); //starting sunday for week 1
	
	for( $i = 1; $i <= 17; $i++ )
	{
		$week[ 'id' ] 		= $i;
		$week[ 'date' ] 	= $date->format( DATE_ISO8601 );
		$week[ 'locked' ] 	= 0;
		
		Weeks::Insert( $db, $week );

		$date->modify( '+1 week ');
	}

	return JSON_Response_Success();
}
?>
