<?php

class JSON_LoadWeeks extends JSONAdmin
{
	public function execute()
	{
		$db_weeks 	= $this->db()->weeks();
		$count 		= $db_weeks->List_Load( $weeks );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		foreach( $weeks as &$week )
		{
			$week[ 'formatted_date' ] = Functions::FormatDate( $week[ 'date' ] );
		}
		
		return $this->setData( $weeks );
	}
}
