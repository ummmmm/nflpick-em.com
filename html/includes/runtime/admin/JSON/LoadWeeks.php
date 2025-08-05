<?php

class JSON_LoadWeeks extends JSONAdmin
{
	public function execute()
	{
		$this->db()->weeks()->List_Load( $weeks );
		
		foreach( $weeks as &$week )
		{
			$week[ 'formatted_date' ] = Functions::FormatDate( $week[ 'date' ] );
		}
		
		return $this->setData( $weeks );
	}
}
