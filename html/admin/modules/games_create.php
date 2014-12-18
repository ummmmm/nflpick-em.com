<?php

function Module_Head()
{
	return true;
}

function Module_Content( &$db )
{
	$db_games 	= new Games( $db );
	$db_picks	= new Picks( $db );

	print '<h1>Games Create</h1>';

	if ( $db_games->List_Load( $null ) || $db_picks->List_Load( $null ) )
	{
		print '<p>The games and picks tables must be empty</p>';
	}
	else if ( !$db_games->Create_Games() )
	{
		printf( '<p>Failed to create games: %s</p>', htmlentities( $db_games->Get_Error() ) );
	}
	else
	{
		print '<p>Games created</p>';
	}

	return true;
}
