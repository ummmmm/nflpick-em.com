<?php

class JSON_LoadNews extends JSONAdmin
{
	public function execute()
	{
		$this->db()->news()->List_Load( $news );

		return $this->setData( $news );
	}
}
