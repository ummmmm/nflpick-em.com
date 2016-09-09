<?php
session_start();
ob_start();

header( "Content-Type: text/html; charset=utf-8" );

require_once( "includes/classes/functions.php" );
require_once( "includes/classes/Database.php" );
require_once( "includes/classes/Screen.php" );

$db					= new Database();
$users				= new Users( $db );
$settings			= new Settings( $db );
$screen_renderer 	= new ScreenRenderer();
$admin 				= Functions::Get( "view" ) == "admin" ? true : false;
$screen				= Functions::Get( "screen" ) === "" ? "default" : Functions::Get( "screen" );
$update				= Functions::Post_Int( "update" ) ? true : false;

$screen_renderer->build( $admin, $screen, $update );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php printf( '%s', htmlentities( $settings->site_title ) ); ?></title>
	<base href="<?php print $settings->domain_url; ?>" />
	<link rel="icon" type="image/x-icon" href="static/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="static/css/styles.css" media="screen" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
	<script src="static/javascript/jqueryui.js" type="text/javascript"></script>
	<script src="static/javascript/javascript.js" type="text/javascript"></script>
	<script type="text/javascript">
		var token = <?php print json_encode( $users->token ); ?>;
	</script>
	<?php
		if ( $admin )
		{
			print '<script type="text/javascript" src="static/javascript/admin.js"></script>';
		}

		print $screen_renderer->head();
	?>
	<script type="text/javascript">$( document ).ready( function() { $.fn.load_poll(); } );</script>
	<script type="text/javascript">$( document ).ready( function() { <?php print $screen_renderer->jquery_head(); ?> } );</script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-2438906-1', 'auto');
		ga('send', 'pageview');
	</script>
</head>
<body>
	<div class="container">
		<div class="header">
			<div class="title">
				<h1><a href="" title="<?php printf( "%s", htmlentities( $settings->site_title ) ); ?>"><?php printf( "%s", htmlentities( $settings->site_title ) ); ?></a></h1>
			</div>
		</div>
		<div class="navigation">
			<?php print $screen_renderer->topNavigation(); ?>
		</div>
		<div class="main">
			<div class="content">
				<?php print $screen_renderer->content(); ?>
			</div>
			<div class="sidenav">
				<?php print $screen_renderer->sideNavigation(); ?>
				<h1>Poll</h1>
				<div id="loading_polls_nav"></div>
			</div>
			<br clear="all" />
		</div>
		<div class="footer">
			&copy; 2007-2015 <a href="">NFLPick-Em.com</a>. Template design by <a href="http://templates.arcsin.se" target="_blank" title="Designed By Arcsin">Arcsin</a>.
		</div>
	</div>
</body>
</html>
<?php
ob_end_flush();
?>
