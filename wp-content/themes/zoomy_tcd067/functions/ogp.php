<?php
function og_image( $n ) {
	global $dp_options, $post;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();
	$myArray = array();
	if ( is_singular() ) {
		if ( $post->_main_image ) {
			$myArray[0] = $post->_main_image['url'];
			$myArray[1] = $post->_main_image['width'];
			$myArray[2] = $post->_main_image['height'];
			echo esc_attr( $myArray[$n] );
		} elseif ( $post->main_image ) {
			$myArray[0] = $post->main_image;
			$myArray[1] = '';
			$myArray[2] = '';

			$main_image = str_replace( array( 'https://', 'http://' ), '//' , $post->main_image );
			$site_url = str_replace( array( 'https://', 'http://' ), '//' , site_url( '/' ) );
			$realpath = str_replace( $site_url, ABSPATH, $main_image );
			if ( file_exists( $realpath ) && $imagesize = getimagesize( $realpath ) ) {
				$myArray[1] = $imagesize[0];
				$myArray[2] = $imagesize[1];
			}
			echo esc_attr( $myArray[$n] );
		} elseif ( has_post_thumbnail() && $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ) ) {
			list( $myArray[0], $myArray[1], $myArray[2] ) = $image;
			echo esc_attr( $myArray[$n] );
		} elseif ( $dp_options['ogp_image'] ) {
			$image = wp_get_attachment_image_src( $dp_options['ogp_image'], 'full' );
			list( $myArray[0], $myArray[1], $myArray[2] ) = $image;
			echo esc_attr( $myArray[$n] );
		} else {
			$myArray[0] = get_bloginfo( 'template_url' ) . '/img/no-image-300x300.gif';
			$myArray[1] = 300;
			$myArray[2] = 300;
			echo esc_attr( $myArray[$n] );
		}
	} else {
		if ( $dp_options['ogp_image'] ) {
			$image = wp_get_attachment_image_src( $dp_options['ogp_image'], 'full' );
			list( $myArray[0], $myArray[1], $myArray[2] ) = $image;
			echo esc_attr( $myArray[$n] );
		} else {
			$myArray[0] = get_bloginfo( 'template_url' ) . '/img/no-image-300x300.gif';
			$myArray[1] = 300;
			$myArray[2] = 300;
			echo esc_attr( $myArray[$n] );
		}
	}
}
function twitter_image() {
	global $dp_options, $post;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();
	if ( is_singular() ) {
		if ( ! empty( $post->_main_image['thumbnails']['300x300'] ) ) {
			echo esc_attr( $post->_main_image['thumbnails']['300x300'] );
		} elseif ( $post->main_image ) {
			echo esc_attr( $post->main_image );
		} elseif ( has_post_thumbnail() && $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'size1' ) ) {
			echo esc_attr( $image[0] );
		} elseif ( $dp_options['ogp_image'] && $image = wp_get_attachment_image_src( $dp_options['ogp_image'], 'size1' ) ) {
			echo esc_attr( $image[0] );
		} else {
			echo get_bloginfo( 'template_url' ) . '/img/no-image-300x300.gif';
		}
	} else {
		if ( $dp_options['ogp_image'] && $image = wp_get_attachment_image_src( $dp_options['ogp_image'], 'size1' ) ) {
			echo esc_attr( $image[0] );
		} else {
			echo get_bloginfo( 'template_url' ) . '/img/no-image-300x300.gif';
		}
	}
}
?>
<?php
function ogp() {
	global $dp_options, $post;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	$og_type = (!is_front_page() && is_singular()) ? 'article' : 'website';
	$og_url = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	//$og_title = is_front_page() || is_home() ? get_bloginfo( 'name' ) : strip_tags( get_the_title() );
	$og_title = is_front_page() ? get_bloginfo( 'name' ) : strip_tags( wp_get_document_title() );
	$og_description = is_singular() ? get_seo_description() : get_bloginfo( 'description' );
	$twitter_title = is_singular() ? get_the_title() : get_bloginfo( 'name' );
?>
<meta property="og:type" content="<?php echo $og_type; ?>">
<meta property="og:url" content="<?php echo esc_url( $og_url ); ?>">
<meta property="og:title" content="<?php echo $og_title; ?>">
<meta property="og:description" content="<?php echo $og_description; ?>">
<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>">
<meta property="og:image" content="<?php og_image(0); ?>">
<meta property="og:image:secure_url" content="<?php og_image(0); ?>">
<meta property="og:image:width" content="<?php og_image(1); ?>">
<meta property="og:image:height" content="<?php og_image(2); ?>">
<?php /*if ( $dp_options['fb:app_id'] ) { ?>
<meta property="fb:admins" content="<?php echo esc_attr( $dp_options['fb_admin_id'] ); ?>">
<?php } */ ?>
<?php if ( $dp_options['fb_app_id'] ) { ?>
<meta property="fb:app_id" content="<?php echo esc_attr( $dp_options['fb_app_id'] ); ?>">
<?php } ?>
<?php if ( $dp_options['use_twitter_card'] ) { ?>
<meta name="twitter:card" content="summary">
<?php if ( $dp_options['twitter_account_name'] ) { ?>
<meta name="twitter:site" content="@<?php echo esc_attr( $dp_options['twitter_account_name'] ); ?>">
<meta name="twitter:creator" content="<?php echo esc_attr( $dp_options['twitter_account_name'] ); ?>">
<?php } ?>
<meta name="twitter:title" content="<?php echo $og_title; ?>">
<meta property="twitter:description" content="<?php echo $og_description; ?>">
<?php if ( is_singular() ) { ?>
<meta name="twitter:image:src" content="<?php twitter_image(); ?>">
<?php } }
}