<?php
ob_start();

header( "Content-Type: text/html; charset=utf-8" );

require_once( "includes/classes/Exceptions.php" );
require_once( "includes/classes/functions.php" );
require_once( "includes/classes/Database.php" );
require_once( "includes/classes/Screen.php" );

$screen_manager = new ScreenManager();
$admin 			= Functions::Get( "view" ) == "admin" ? true : false;
$screen			= Functions::Get( "screen" ) === "" ? "default" : Functions::Get( "screen" );
$update			= Functions::Post_Int( "update" ) ? true : false;

try
{
	$screen_manager->initialize( $admin, $screen, $update );

	$settings	= $screen_manager->settings();
	$auth		= $screen_manager->auth();
}
catch ( Exception $e )
{
	printf( 'A fatal error occurred: %s', htmlentities( $e->getMessage() ) );
	exit();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php printf( '%s', htmlentities( $settings[ 'site_title' ] ) ); ?></title>
	<base href="<?php print $settings[ 'domain_url' ]; ?>" />
	<link rel="icon" type="image/x-icon" href="static/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="static/css/styles.css" media="screen" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
	<script src="static/javascript/jqueryui.js" type="text/javascript"></script>
	<script src="static/javascript/javascript.js" type="text/javascript"></script>
	<script type="text/javascript">
		var token = <?php print json_encode( $auth->getToken() ); ?>;
	</script>
	<?php
		if ( $admin )
		{
			print '<script type="text/javascript" src="static/javascript/admin.js"></script>';
		}

		print $screen_manager->head();
	?>
	<script type="text/javascript">$( document ).ready( function() { $.fn.load_poll(); } );</script>
	<script type="text/javascript">$( document ).ready( function() { <?php print $screen_manager->jquery_head(); ?> } );</script>
</head>
<body>
	<div class="container">
		<div class="header">
			<div class="title">
				<h1><a href="" title="<?php print( htmlentities( $settings[ 'site_title' ] ) ); ?>"><?php print( htmlentities( $settings[ 'site_title' ] ) ); ?></a></h1>
			</div>
		</div>
		<div class="navigation">
			<?php print $screen_manager->topNavigation(); ?>
		</div>
		<div class="main">
			<div class="content">
				<?php print $screen_manager->content(); ?>
			</div>
			<div class="sidenav">
				<?php print $screen_manager->sideNavigation(); ?>
				<h1>Poll</h1>
				<div id="loading_polls_nav"></div>
			</div>
			<br clear="all" />
		</div>
		<div class="footer">
			&copy; 2007-<?php print date( 'Y' ); ?> <a href="">NFLPick-Em.com</a>. Template design by <a href="http://templates.arcsin.se" target="_blank" title="Designed By Arcsin">Arcsin</a>.
		</div>
	</div>
</body>
</html>
<?php
ob_end_flush();
?>
