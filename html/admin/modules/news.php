<?php
function Module_Validate( &$db, &$user, &$validation )
{
	$action = Functions::Get( 'action' );
	
	if ( $action === 'add' )
	{
		$title 	= Functions::Post( 'title' );
		$body	= Functions::Post( 'body' );
		$token	= Functions::Post( 'token' );
		$errors	= array();
		
		if ( !Sessions::Validate( $db, $user->id, $token ) )
		{
			return Functions::ValidationError( array( 'Invalid token.' ) );
		}
		
		if ( $title === '' || strlen( $title ) > 255 )
		{
			array_push( $errors, 'Title cannot be blank and must be less than 255 characters.' );
		}
		
		if ( $body === '' )
		{
			array_push( $errors, 'News article cannot be blank.' );
		}
		
		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}
		
		$validation[ 'user_id' ]	= $user->id;
		$validation[ 'title' ] 		= $title;
		$validation[ 'body' ] 		= $body;
		$validation[ 'active' ] 	= Functions::Post( 'active' );
		$validation[ 'ip' ] 		= $_SERVER[ 'REMOTE_ADDR' ];
		
		return true;
	}
	
	if ( $action === 'edit' )
	{
		$title 	= Functions::Post( 'title' );
		$body	= Functions::Post( 'body' );
		$token	= Functions::Post( 'token' );
		$errors	= array();
		
		if ( !Sessions::Validate( $db, $user->id, $token ) )
		{
			return Functions::ValidationError( array( 'Invalid token.' ) );
		}
		
		if ( $title === '' || strlen( $title ) > 255 )
		{
			array_push( $errors, 'Title cannot be blank and must be less than 255 characters.' );
		}
		
		if ( $body === '' )
		{
			array_push( $errors, 'News article cannot be blank.' );
		}
		
		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}
		
		$validation[ 'id' ] 	= Functions::Get( 'id' );
		$validation[ 'title' ] 	= $title;
		$validation[ 'body' ] 	= $body;
		$validation[ 'active' ] = Functions::Post( 'active' );
		
		return true;
	}
}

function Module_Update( &$db, &$user, &$validation )
{
	$action = Functions::Get( 'action' );
	
	if ( $action === 'add' )
	{
		if ( !News_Insert( $db, $validation ) )
		{
			return false;
		}
		
		return Functions::Module_Updated( 'News has been added.' );
	}
	
	if ( $action === 'edit' )
	{
		if ( !News_Update( $db, $validation ) )
		{
			return false;
		}
		
		return Functions::Module_Updated( 'News has been updated.' );
	}
}

function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	$jquery = '$.fn.load_news();';
	
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<a href="javascript:;" onclick="$.fn.add_news();">Add News</a>';
	print '<h1>News</h1>';
	print '<div id="news_loading">Loading...</div>';
	print <<<EOT
			<div id="news_addedit">
				<div id="news_addedit_dialog">Add/Edit News</div>
				<table>
					<tr>
						<td><b>Title:</b></td>
						<td><input type="text" name="title" id="news_addedit_title" /></td>
					</tr>
					<tr>
						<td valign="top"><b>Message:</b></td>
						<td><textarea name="message" id="news_addedit_message" cols="50" rows="10"></textarea></td>
					</tr>
					<tr>
						<td valign="top"><b>Status:</b></td>
						<td><input type="radio" name="active" value="1" id="news_addedit_active" /> Active<br />
							<input type="radio" name="active" value="0" id="news_addedit_inactive" /> Inactive
						</td>
					</tr>
				</table>
				
				<div class="buttons_left">
					<input type="button" id="news_addedit_cancel" value="Cancel" />
					<input type="button" id="news_addedit_delete" value="Delete" />
				</div>
				<div class="buttons_right">
					<input type="button" id="news_addedit_update" value="Update News" />
				</div>
			</div>
EOT;

	return true;
}
?>