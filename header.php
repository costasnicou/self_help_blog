<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package selfhelpblog
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<script src="https://kit.fontawesome.com/d495971c43.js" crossorigin="anonymous"></script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'selfhelpblog' ); ?></a>

	<header id="masthead" class="site-header the-wraper">
		<div class="header-wraper">
			<div class="logo-area">
				<div class="icircle">
					<i class="fa-solid fa-check"></i>
				</div>
				
				<img src="<?php echo get_template_directory_uri(); ?>/assets/imgs/logo.png" alt="" width="210px" height="40px">
			</div>
			<div class="header-wraper-in">
				<nav class="main-menu">
					<li><a href="">Home</a></li>
					<li><a href="">Categories</a></li>
					<li><a href="">About</a></li>

				</nav>

				<div class="subscribe">
					<button>Join Newsletter</button>
				</div>

			</div>
	
		</div>

		
	
	</header><!-- #masthead -->

	<div class="nav-trigger"></div>