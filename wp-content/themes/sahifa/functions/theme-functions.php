<?php

/*-----------------------------------------------------------------------------------*/
# Get Theme Options
/*-----------------------------------------------------------------------------------*/
function tie_get_option( $name ) {
	$get_options = get_option( 'tie_options' );
	
	if( !empty( $get_options[$name] ))
		return $get_options[$name];
		
	return false ;
}

/*-----------------------------------------------------------------------------------*/
# Setup Theme
/*-----------------------------------------------------------------------------------*/
add_action( 'after_setup_theme', 'tie_setup' );
function tie_setup() {
	global $default_data;
	
	add_theme_support( 'automatic-feed-links' );
	
	add_filter( 'enable_post_format_ui', '__return_false' );

	load_theme_textdomain( 'tie', get_template_directory() . '/languages' );

	register_nav_menus( array(
		'top-menu' => __( 'Top Menu Navigation', 'tie' ),
		'primary' => __( 'Primary Navigation', 'tie' )
	) );
	
}

/*-----------------------------------------------------------------------------------*/
# Post Thumbinals
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'add_theme_support' ) ) 
	add_theme_support( 'post-thumbnails' );


if ( function_exists( 'add_image_size' ) && !tie_get_option( 'timthumb' ) ){
	add_image_size( 'tie-small', 55, 55, true );
	add_image_size( 'tie-medium', 272, 125, true );
	add_image_size( 'tie-large', 290, 195, true );
	add_image_size( 'slider', 660, 330, true );
	add_image_size( 'big-slider', 995, 498, true );
}


/*-----------------------------------------------------------------------------------*/
# If the menu doesn't exist
/*-----------------------------------------------------------------------------------*/
function tie_nav_fallback(){
	echo '<div class="menu-alert">'.__( 'You can use WP menu builder to build menus' , 'tie' ).'</div>';
}


/*-----------------------------------------------------------------------------------*/
# Mobile Menus
/*-----------------------------------------------------------------------------------*/
function tie_alternate_menu( $args = array() ) {			
	$output = '';
		
	@extract($args);						
			
	if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {	
		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );						
		$menu_items = wp_get_nav_menu_items( $menu->term_id );				
		$output = "<select id='". $id. "'>";
		$output .= "<option value='' selected='selected'>" . __('Go to...', 'tie') . "</option>";
		foreach ( (array) $menu_items as $key => $menu_item ) {
		    $title = $menu_item->title;
		    $url = $menu_item->url;
				    
		    if ( $menu_item->menu_item_parent ) {
				$title = ' - ' . $title;
		    }
		    $output .= "<option value='" . $url . "'>" . $title . '</option>';
		}
		$output .= '</select>';
	}
	return $output;							
}
	
	
/*-----------------------------------------------------------------------------------*/
# Custom Dashboard login page logo
/*-----------------------------------------------------------------------------------*/
function tie_login_logo(){
	if( tie_get_option('dashboard_logo') )
    echo '<style  type="text/css"> h1 a {  background-image:url('.tie_get_option('dashboard_logo').')  !important; } </style>';  
}  
add_action('login_head',  'tie_login_logo'); 


/*-----------------------------------------------------------------------------------*/
# Custom Gravatar
/*-----------------------------------------------------------------------------------*/
function tie_custom_gravatar ($avatar) {
	$tie_gravatar = tie_get_option( 'gravatar' );
	if($tie_gravatar){
		$custom_avatar = tie_get_option( 'gravatar' );
		$avatar[$custom_avatar] = "Custom Gravatar";
	}
	return $avatar;
}
add_filter( 'avatar_defaults', 'tie_custom_gravatar' ); 


/*-----------------------------------------------------------------------------------*/
# Custom Favicon
/*-----------------------------------------------------------------------------------*/
function tie_favicon() {
	$default_favicon = get_template_directory_uri()."/favicon.ico";
	$custom_favicon = tie_get_option('favicon');
	$favicon = (empty($custom_favicon)) ? $default_favicon : $custom_favicon;
	echo '<link rel="shortcut icon" href="'.$favicon.'" title="Favicon" />';
}
add_action('wp_head', 'tie_favicon');


/*-----------------------------------------------------------------------------------*/
# Get Home Cats Boxes
/*-----------------------------------------------------------------------------------*/
function tie_get_home_cats($cat_data){

	switch( $cat_data['type'] ){
	
		case 'n':
			get_home_cats( $cat_data );
			break;
		
		case 's':
			get_home_scroll( $cat_data );
			break;
			
		case 'news-pic':
			get_home_news_pic( $cat_data );
			break;	
			
		case 'recent':
			get_home_recent( $cat_data );
			break;	
			
		case 'divider': ?>
			<div class="divider" style="height:<?php echo $cat_data['height'] ?>px"></div>
			<div class="clear"></div>
		<?php
			break;
			
		case 'ads': ?>
			<div class="home-ads"><?php echo do_shortcode( htmlspecialchars_decode(stripslashes ($cat_data['text']) )) ?></div>
			<div class="clear"></div>
		<?php
			break;
	}
}


/*-----------------------------------------------------------------------------------*/
# Exclude pages From Search
/*-----------------------------------------------------------------------------------*/
function tie_Search_Filter($query) {
	if( $query->is_search ){
		if ( tie_get_option( 'search_exclude_pages' ) && !is_admin() )
			$query->set('post_type', 'post');
			
		if ( tie_get_option( 'search_cats' ))
			$query->set( 'cat', tie_get_option( 'search_cats' ) && !is_admin() );
	}
	return $query;
}
add_filter('pre_get_posts','tie_Search_Filter');


/*-----------------------------------------------------------------------------------*/
# Random article
/*-----------------------------------------------------------------------------------*/	
add_action('init', 'tie_random_post');
function tie_random_post(){
	if ( isset($_GET['tierand']) ){

$random = new WP_Query('orderby=rand&showposts=1');
if ($random->have_posts()) {
	while ($random->have_posts()) : $random->the_post();
		$URL = get_permalink();
	endwhile; ?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Refresh" content="0; url=<?php echo $URL; ?>">
</head>
<body>
</body>
</html>
<?php }
		die;
	}
}


/*-----------------------------------------------------------------------------------*/
#Author Box
/*-----------------------------------------------------------------------------------*/
function tie_author_box($avatar = true , $social = true ){
	if( $avatar ) : ?>
	<div class="author-avatar">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'MFW_author_bio_avatar_size', 60 ) ); ?>
	</div><!-- #author-avatar -->
	<?php endif; ?>
		<div class="author-description">
			<?php the_author_meta( 'description' ); ?>
		</div><!-- #author-description -->
	<?php  if( $social ) :	?>	
		<div class="author-social">
			<?php if ( get_the_author_meta( 'url' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'url' ); ?>" title="<?php the_author_meta( 'display_name' ); ?><?php _e( " 's site", 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/site.png" alt="" /></a>
			<?php endif ?>	
			<?php if ( get_the_author_meta( 'twitter' ) ) : ?>
			<a class="ttip" href="http://twitter.com/<?php the_author_meta( 'twitter' ); ?>" title="<?php the_author_meta( 'display_name' ); ?><?php _e( '  on Twitter', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/twitter.png" alt="" /></a>
			<?php endif ?>	
			<?php if ( get_the_author_meta( 'facebook' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'facebook' ); ?>" title="<?php the_author_meta( 'display_name' ); ?> <?php _e( '  on Facebook', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/facebook.png" alt="" /></a>
			<?php endif ?>
			<?php if ( get_the_author_meta( 'google' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'google' ); ?>" title="<?php the_author_meta( 'display_name' ); ?> <?php _e( '  on Google+', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/google_plus.png" alt="" /></a>
			<?php endif ?>	
			<?php if ( get_the_author_meta( 'linkedin' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'linkedin' ); ?>" title="<?php the_author_meta( 'display_name' ); ?> <?php _e( '  on Linkedin', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/linkedin.png" alt="" /></a>
			<?php endif ?>				
			<?php if ( get_the_author_meta( 'flickr' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'flickr' ); ?>" title="<?php the_author_meta( 'display_name' ); ?><?php _e( '  on Flickr', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/flickr.png" alt="" /></a>
			<?php endif ?>	
			<?php if ( get_the_author_meta( 'youtube' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'youtube' ); ?>" title="<?php the_author_meta( 'display_name' ); ?><?php _e( '  on YouTube', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/youtube.png" alt="" /></a>
			<?php endif ?>
			<?php if ( get_the_author_meta( 'pinterest' ) ) : ?>
			<a class="ttip" href="<?php the_author_meta( 'pinterest' ); ?>" title="<?php the_author_meta( 'display_name' ); ?><?php _e( '  on Pinterest', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/pinterest.png" alt="" /></a>
			<?php endif ?>

		</div>
	<?php endif; ?>
	<div class="clear"></div>
	<?php
}

/*-----------------------------------------------------------------------------------*/
# Social 
/*-----------------------------------------------------------------------------------*/
function tie_get_social($newtab='yes', $icon_size='32', $tooltip='ttip'){
	$social = tie_get_option('social');
	@extract($social);
		
	if ($newtab == 'yes') $newtab = "target=\"_blank\"";
	else $newtab = '';
		
	$icons_path =  get_template_directory_uri().'/images/socialicons';
		
		?>
		<div class="social-icons icon_<?php echo $icon_size; ?>">
		<?php
		// RSS
		if ( !tie_get_option('rss_icon') ){
		if ( tie_get_option('rss_url') != '' && tie_get_option('rss_url') != ' ' ) $rss = tie_get_option('rss_url') ;
		else $rss = get_bloginfo('rss2_url'); 
			?><a class="<?php echo $tooltip; ?>" title="Rss" href="<?php echo $rss ; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/rss.png" alt="RSS"  /></a><?php 
		}
		// Google+
		if ( !empty($google_plus) && $google_plus != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Google+" href="<?php echo $google_plus; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/google_plus.png" alt="Google+"  /></a><?php 
		}
		// Facebook
		if ( !empty($facebook) && $facebook != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Facebook" href="<?php echo $facebook; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/facebook.png" alt="Facebook"  /></a><?php 
		}
		// Twitter
		if ( !empty($twitter) && $twitter != ' ') {
			?><a class="<?php echo $tooltip; ?>" title="Twitter" href="<?php echo $twitter; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/twitter.png" alt="Twitter"  /></a><?php
		}		
		// Pinterest
		if ( !empty($Pinterest) && $Pinterest != ' ') {
			?><a class="<?php echo $tooltip; ?>" title="Pinterest" href="<?php echo $Pinterest; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/pinterest.png" alt="MySpace"  /></a><?php
		}
		// MySpace
		if ( !empty($myspace) && $myspace != ' ') {
			?><a class="<?php echo $tooltip; ?>" title="MySpace" href="<?php echo $myspace; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/myspace.png" alt="MySpace"  /></a><?php
		}
		// FriendFeed
		if ( !empty($friendfeed) && $friendfeed != ' ') {
			?><a class="<?php echo $tooltip; ?>" title="FriendFeed" href="<?php echo $friendfeed; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/friendfeed.png" alt="FriendFeed"  /></a><?php
		}
		// Orkut
		if ( !empty($orkut) && $orkut != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Orkut" href="<?php echo $orkut; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/orkut.png" alt="Orkut"  /></a><?php
		}
		// dribbble
		if ( !empty($dribbble) && $dribbble != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Dribbble" href="<?php echo $dribbble; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/dribbble.png" alt="dribbble"  /></a><?php
		}
		// LinkedIN
		if ( !empty($linkedin) && $linkedin != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="LinkedIn" href="<?php echo $linkedin; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/linkedin.png" alt="LinkedIn"  /></a><?php
		}
		// evernote
		if ( !empty($evernote) && $evernote != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Evernote" href="<?php echo $evernote; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/evernote.png" alt="evernote"  /></a><?php
		}
		// Flickr
		if ( !empty($flickr) && $flickr != ' ') {
			?><a class="<?php echo $tooltip; ?>" title="Flickr" href="<?php echo $flickr; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/flickr.png" alt="Flickr"  /></a><?php
		}
		// Picasa
		if ( !empty($picasa) && $picasa != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Picasa" href="<?php echo $picasa; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/picasa.png" alt="Picasa"  /></a><?php
		}
		// YouTube
		if ( !empty($youtube) && $youtube != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Youtube" href="<?php echo $youtube; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/youtube.png" alt="YouTube"  /></a><?php
		}
		// Skype
		if ( !empty($skype) && $skype != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Skype" href="<?php echo $skype; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/skype.png" alt="Skype"  /></a><?php
		}
		// Digg
		if ( !empty($digg) && $digg != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Digg" href="<?php echo $digg; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/digg.png" alt="Digg"  /></a><?php
		}
		// Reddit 
		if ( !empty($reddit) && $reddit != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Reddit" href="<?php echo $reddit; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/reddit.png" alt="Reddit"  /></a><?php
		}
		// Delicious 
		if ( !empty($delicious) && $delicious != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Delicious" href="<?php echo $delicious; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/delicious.png" alt="Delicious"  /></a><?php
		}
		// stumbleuponUpon 
		if ( !empty($stumbleupon) && $stumbleupon != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="StumbleUpon" href="<?php echo $stumbleupon; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/stumbleupon.png" alt="stumbleuponUpon"  /></a><?php
		}
		// Tumblr 
		if ( !empty($tumblr) && $tumblr != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Tumblr" href="<?php echo $tumblr; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/tumblr.png" alt="Tumblr"  /></a><?php
		}
		// Google googletalk
		if ( !empty($googletalk) && $googletalk != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Google Talk" href="<?php echo $googletalk; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/googletalk.png" alt="Google Talk" Google googletalk"  /></a><?php
		}
		// Vimeo
		if ( !empty($vimeo) && $vimeo != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Vimeo" href="<?php echo $vimeo; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/vimeo.png" alt="Vimeo"  /></a><?php
		}
		// Blogger
		if ( !empty($blogger) && $blogger != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Blogger" href="<?php echo $blogger; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/blogger.png" alt="Blogger"  /></a><?php
		}
		// Wordpress
		if ( !empty($wordpress) && $wordpress != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="WordPress" href="<?php echo $wordpress; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/wordpress.png" alt="Wordpress"  /></a><?php
		}
		// Yelp
		if ( !empty($yelp) && $yelp != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Yelp" href="<?php echo $yelp; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/yelp.png" alt="Yelp"  /></a><?php
		}
		// Last.fm
		if ( !empty($lastfm) && $lastfm != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Last.fm" href="<?php echo $lastfm; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/lastfm.png" alt="Last.fm"  /></a><?php
		}
		// Pandora
		if ( !empty($pandora) && $pandora != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Pandora" href="<?php echo $pandora; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/pandora.png" alt="Pandora"  /></a><?php
		}
		// grooveshark
		if ( !empty($grooveshark) && $grooveshark != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Grooveshark" href="<?php echo $grooveshark; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/grooveshark.png" alt="grooveshark"  /></a><?php
		}
		// yahoobuzz
		if ( !empty($yahoobuzz) && $yahoobuzz != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="YahooBuzz" href="<?php echo $yahoobuzz; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/yahoobuzz.png" alt="yahoobuzz"  /></a><?php
		}
		// technorati
		if ( !empty($technorati) && $technorati != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Technorati" href="<?php echo $technorati; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/technorati.png" alt="technorati"  /></a><?php
		}
		// sharethis
		if ( !empty($sharethis) && $sharethis != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="ShareThis" href="<?php echo $sharethis; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/sharethis.png" alt="sharethis"  /></a><?php
		}
		// dopplr
		if ( !empty($dopplr) && $dopplr != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Dopplr" href="<?php echo $dopplr; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/dopplr.png" alt="dopplr"  /></a><?php
		}
		// ember
		if ( !empty($ember) && $ember != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Ember" href="<?php echo $ember; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/ember.png" alt="ember"  /></a><?php
		}
		// xing.me
		if ( !empty($xing) && $xing != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Xing" href="<?php echo $xing; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/xing.png" alt="xing"  /></a><?php
		}
		// dribbble
		if ( !empty($posterous) && $posterous != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Posterous" href="<?php echo $posterous; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/posterous.png" alt="posterous"  /></a><?php
		}
		// DeviantArt
		if ( !empty($deviantart) && $deviantart != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="DeviantArt" href="<?php echo $deviantart; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/deviantart.png" alt="DeviantArt"  /></a><?php
		}
		// Apple
		if ( !empty($apple) && $apple != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Apple" href="<?php echo $apple; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/apple.png" alt="Apple"  /></a><?php
		}
		// mixx
		if ( !empty($mixx) && $mixx != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Mixx" href="<?php echo $mixx; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/mixx.png" alt="mixx"  /></a><?php
		}
		// Newsvine
		if ( !empty($newsvine) && $newsvine != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Newsvine" href="<?php echo $newsvine; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/newsvine.png" alt="Newsvine"  /></a><?php
		}
		// openid
		if ( !empty($openid) && $openid != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="OpenID" href="<?php echo $openid; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/openid.png" alt="openid"  /></a><?php
		}
		// readernaut
		if ( !empty($readernaut) && $readernaut != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Readernaut" href="<?php echo $readernaut; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/readernaut.png" alt="readernaut"  /></a><?php
		}		
		// Design Moo
		if ( !empty($designmoo) && $designmoo != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Design Moo" href="<?php echo $designmoo; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/designmoo.png" alt="Design Moo"  /></a><?php
		}
		// Bebo
		if ( !empty($bebo) && $bebo != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Bebo" href="<?php echo $bebo; ?>" <?php echo $newtab; ?>><img src="<?php echo $icons_path; ?>/bebo.png" alt="Bebo"  /></a><?php
		}
		// virb
		if ( !empty($virb) && $virb != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Virb" href="<?php echo $virb; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/virb.png" alt="virb"  /></a><?php
		}
		// behance
		if ( !empty( $behance ) && $behance != '' && $behance != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Behance" href="<?php echo $behance; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/behance.png" alt="behance"  /></a><?php
		}
		// instagram
		if ( !empty( $instagram ) && $instagram != '' && $instagram != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="instagram" href="<?php echo $instagram; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/instagram.png" alt="instagram"  /></a><?php
		}
		// paypal
		if ( !empty( $paypal ) && $paypal != '' && $paypal != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="paypal" href="<?php echo $paypal; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/paypal.png" alt="paypal"  /></a><?php
		}
		// spotify
		if ( !empty( $spotify ) && $spotify != '' && $spotify != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="spotify" href="<?php echo $spotify; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/spotify.png" alt="spotify"  /></a><?php
		}
		// viadeo
		if ( !empty( $viadeo ) && $viadeo != '' && $viadeo != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="viadeo" href="<?php echo $viadeo; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/viadeo.png" alt="viadeo"  /></a><?php
		}
		// Google Play
		if ( !empty( $google_play ) && $google_play != '' && $google_play != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Google Play" href="<?php echo $google_play; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/google_play.png" alt="Google Play"  /></a><?php
		}
		// viddler
		if ( !empty($viddler) && $viddler != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Viddler" href="<?php echo $viddler; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/viddler.png" alt="viddler"  /></a><?php
		}
		// Forrst
		if ( !empty($forrst) && $forrst != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="Forrst" href="<?php echo $forrst; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/forrst.png" alt="Forrst"  /></a><?php
		}
		// VK
		if ( !empty($vk) && $vk != ' ' ) {
			?><a class="<?php echo $tooltip; ?>" title="vk.com" href="<?php echo $vk; ?>" <?php echo $newtab; ?>><img  src="<?php echo $icons_path; ?>/vk.png" alt="vk.com"  /></a><?php
		} ?>
	</div>

<?php
}


/*-----------------------------------------------------------------------------------*/
# Change The Default WordPress Excerpt Length
/*-----------------------------------------------------------------------------------*/
function tie_excerpt_global_length( $length ) {
	if( tie_get_option( 'exc_length' ) )
		return tie_get_option( 'exc_length' );
	else return 60;
}

function tie_excerpt_home_length( $length ) {
	if( tie_get_option( 'home_exc_length' ) )
		return tie_get_option( 'home_exc_length' );
	else return 15;
}

function tie_excerpt(){
	add_filter( 'excerpt_length', 'tie_excerpt_global_length', 999 );
	echo get_the_excerpt();
}

function tie_excerpt_home(){
	add_filter( 'excerpt_length', 'tie_excerpt_home_length', 999 );
	echo get_the_excerpt();
}


/*-----------------------------------------------------------------------------------*/
# Read More Functions
/*-----------------------------------------------------------------------------------*/
function tie_remove_excerpt( $more ) {
	return ' ...';
}
add_filter('excerpt_more', 'tie_remove_excerpt');


/*-----------------------------------------------------------------------------------*/
# Page Navigation
/*-----------------------------------------------------------------------------------*/
function tie_pagenavi( $query = false, $num = false ){
	?>
	<div class="pagination">
		<?php tie_get_pagenavi( $query, $num ) ?>
	</div>
	<?php
}


/*-----------------------------------------------------------------------------------*/
# Get Post Audio  
/*-----------------------------------------------------------------------------------*/
function tie_audio(){
	global $post;
	$get_meta = get_post_custom($post->ID);
	wp_enqueue_script( 'tie-jplayer' );
	?>
  <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery("#jquery_jplayer_<?php echo $post->ID ?>").jPlayer({
        ready: function () {
          jQuery(this).jPlayer("setMedia", {
            mp3: "<?php echo $get_meta["tie_audio_mp3"][0] ?>",
            m4a: "<?php echo $get_meta["tie_audio_m4a"][0] ?>",
            oga: "<?php echo $get_meta["tie_audio_oga"][0] ?>"
          });
        },
		cssSelectorAncestor: "#jp_container_<?php echo $post->ID ?>",
        supplied: "m4a, oga, mp3"
      });
    });
  </script>
<div id="jquery_jplayer_<?php echo $post->ID ?>" class="jp-jplayer"></div>
	<div id="jp_container_<?php echo $post->ID ?>" class="jp-audio">
		<div class="jp-type-single">
			<div class="jp-gui jp-interface">
	  			<div class="jp-progress">
				  <div class="jp-seek-bar">
					<div class="jp-play-bar"><span></span></div>
				  </div>
				</div>
				
				<a href="javascript:;" class="jp-play" tabindex="1">play</a>
				<a href="javascript:;" class="jp-pause" tabindex="1">pause</a>
				
				<div class="jp-volume-bar">
				  <div class="jp-volume-bar-value"><span class="handle"></span></div>
				</div>
		
				<a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a>
				<a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a>
			</div>

			<div class="jp-no-solution">
				<span>Update Required</span>
				To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			</div>
		</div>
</div>
  <?php
}

/*-----------------------------------------------------------------------------------*/
# Tie Excerpt
/*-----------------------------------------------------------------------------------*/
function tie_content_limit($text, $chars = 120) {
	$text = $text." ";
	$text = mb_substr( $text , 0 , $chars , 'UTF-8');
	$text = $text."...";
	return $text;
}


/*-----------------------------------------------------------------------------------*/
# Queue Comments reply js
/*-----------------------------------------------------------------------------------*/
function comments_queue_js(){
if ( (!is_admin()) && is_singular() && comments_open() && get_option('thread_comments') )
  wp_enqueue_script( 'comment-reply' );
}
add_action('wp_print_scripts', 'comments_queue_js');


/*-----------------------------------------------------------------------------------*/
# Remove recent comments_ style
/*-----------------------------------------------------------------------------------*/
function tie_remove_recent_comments_style() {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'widgets_init', 'tie_remove_recent_comments_style' );


/*-----------------------------------------------------------------------------------*/
# Get the thumbnail
/*-----------------------------------------------------------------------------------*/
function get_post_thumb(){
	global $post ;
	if ( has_post_thumbnail($post->ID) ){
		$image_id = get_post_thumbnail_id($post->ID);  
		$image_url = wp_get_attachment_image_src($image_id,'large');  
		$image_url = $image_url[0];
		return $ap_image_url = str_replace(get_option('siteurl'),'', $image_url);
	}
}


/*-----------------------------------------------------------------------------------*/
# tie Thumb
/*-----------------------------------------------------------------------------------*/
function tie_thumb($img='' , $width='' , $height=''){
	global $post;
	
	if( tie_get_option( 'timthumb') ){
		if( empty( $img ) ) $img = get_post_thumb();
		if( !empty($img) ){
		?>
			<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo $img ?>&amp;h=<?php echo $height ?>&amp;w=<?php echo $width ?>&amp;a=c" alt="<?php the_title(); ?>" />
	<?php }
	}else{
		$image_id = get_post_thumbnail_id($post->ID);  
		$image_url = wp_get_attachment_image($image_id, array($width,$height), false, array( 'alt'   => get_the_title() ,'title' =>  get_the_title()  ));  
		echo $image_url;
	}
}


/*-----------------------------------------------------------------------------------*/
# tie Thumb SRC
/*-----------------------------------------------------------------------------------*/
function tie_thumb_src($img='' , $width='' , $height=''){
	global $post;

	if( tie_get_option( 'timthumb') ){
		if(!$img) $img = get_post_thumb();
		if( !empty($img) ){
			return $img_src = get_template_directory_uri()."/timthumb.php?src=". $img ."&amp;h=". $height ."&amp;w=". $width ."amp;a=c";
		}
	}else{
		$image_id = get_post_thumbnail_id($post->ID);  
		$image_url = wp_get_attachment_image_src($image_id, array($width,$height) );  
		return $image_url[0];
	}
}


/*-----------------------------------------------------------------------------------*/
# tie Thumb
/*-----------------------------------------------------------------------------------*/
function tie_slider_img_src($image_id , $width='' , $height=''){
	global $post;
	if( tie_get_option( 'timthumb') ){
		$img =  wp_get_attachment_image_src( $image_id , 'full' );
		if( !empty($img) ){
			return $img_src = get_template_directory_uri()."/timthumb.php?src=". $img[0] ."&amp;h=".$height ."&amp;w=". $width ."&amp;a=c";
		}
	}else{
		$image_url = wp_get_attachment_image_src($image_id, array($width,$height) );  
		return $image_url[0];
	}
}

/*-----------------------------------------------------------------------------------*/
# Add user's social accounts
/*-----------------------------------------------------------------------------------*/
add_action( 'show_user_profile', 'tie_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'tie_show_extra_profile_fields' );
function tie_show_extra_profile_fields( $user ) { ?>
	<h3>Custom Author widget</h3>
	<table class="form-table">
		<tr>
			<th><label for="author_widget_content">Custom Author widget content</label></th>
			<td>
				<textarea name="author_widget_content" id="author_widget_content" rows="5" cols="30"><?php echo esc_attr( get_the_author_meta( 'author_widget_content', $user->ID ) ); ?></textarea>
				<br /><span class="description">Supports HTML and Shortcodes .</span>
			</td>
		</tr>
	</table>
	<h3><?php _e( 'Social Networking', 'tie' ) ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="google">Google + URL</label></th>
			<td>
				<input type="text" name="google" id="google" value="<?php echo esc_attr( get_the_author_meta( 'google', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="twitter">Twitter Username</label></th>
			<td>
				<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="facebook">FaceBook URL</label></th>
			<td>
				<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="linkedin">linkedIn URL</label></th>
			<td>
				<input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_the_author_meta( 'linkedin', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="flickr">Flickr URL</label></th>
			<td>
				<input type="text" name="flickr" id="flickr" value="<?php echo esc_attr( get_the_author_meta( 'flickr', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="youtube">YouTube URL</label></th>
			<td>
				<input type="text" name="youtube" id="youtube" value="<?php echo esc_attr( get_the_author_meta( 'youtube', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="pinterest">Pinterest URL</label></th>
			<td>
				<input type="text" name="pinterest" id="pinterest" value="<?php echo esc_attr( get_the_author_meta( 'pinterest', $user->ID ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>

	</table>
<?php }

## Save user's social accounts
add_action( 'personal_options_update', 'tie_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'tie_save_extra_profile_fields' );
function tie_save_extra_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) return false;
	update_user_meta( $user_id, 'author_widget_content', $_POST['author_widget_content'] );
	update_user_meta( $user_id, 'google', $_POST['google'] );
	update_user_meta( $user_id, 'pinterest', $_POST['pinterest'] );
	update_user_meta( $user_id, 'twitter', $_POST['twitter'] );
	update_user_meta( $user_id, 'facebook', $_POST['facebook'] );
	update_user_meta( $user_id, 'linkedin', $_POST['linkedin'] );
	update_user_meta( $user_id, 'flickr', $_POST['flickr'] );
	update_user_meta( $user_id, 'youtube', $_POST['youtube'] );
}


/*-----------------------------------------------------------------------------------*/
# Get templates 
/*-----------------------------------------------------------------------------------*/
function tie_include($template){
	include ( get_template_directory() . '/includes/'.$template.'.php' );
}


/*-----------------------------------------------------------------------------------*/
# Get Feeds 
/*-----------------------------------------------------------------------------------*/

function tie_get_feeds( $feed , $number = 10 ){
	include_once(ABSPATH . WPINC . '/feed.php');

	$rss = @fetch_feed( $feed );
	if (!is_wp_error( $rss ) ){
		$maxitems = $rss->get_item_quantity($number); 
		$rss_items = $rss->get_items(0, $maxitems); 
	}
	if ($maxitems == 0) {
		$out = "<ul><li>". __( 'No items.', 'tie' )."</li></ul>";
	}else{
		$out = "<ul>";
		
		foreach ( $rss_items as $item ) : 
			$out .= '<li><a href="'. esc_url( $item->get_permalink() ) .'" title="'.  __( "Posted ", "tie" ).$item->get_date("j F Y | g:i a").'">'. esc_html( $item->get_title() ) .'</a></li>';
		endforeach;
		$out .='</ul>';
	}
	
	return $out;
}


/*-----------------------------------------------------------------------------------*/
# Tie Wp Footer
/*-----------------------------------------------------------------------------------*/
add_action('wp_footer', 'tie_wp_footer');
function tie_wp_footer() { 
	if ( tie_get_option('footer_code')) echo htmlspecialchars_decode( stripslashes(tie_get_option('footer_code') )); 
} 


/*-----------------------------------------------------------------------------------*/
# News In Picture
/*-----------------------------------------------------------------------------------*/
function tie_last_news_pic($order , $numberOfPosts = 12 , $cats = 1 ){
	global $post;
	$orig_post = $post;
	
	if( $order == 'random')
		$lastPosts = get_posts(	$args = array('numberposts' => $numberOfPosts, 'orderby' => 'rand', 'category' => $cats ));
	else
		$lastPosts = get_posts(	$args = array('numberposts' => $numberOfPosts, 'category' => $cats ));
		get_posts('category='.$cats.'&numberposts='.$numberOfPosts);
	
		foreach($lastPosts as $post): setup_postdata($post); ?>

		<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>
			<div class="post-thumbnail">
				<a class="ttip" title="<?php the_title();?>" href="<?php the_permalink(); ?>" ><?php tie_thumb('',50,50); ?></a>
			</div><!-- post-thumbnail /-->
		<?php endif; ?>

	<?php endforeach;
	$post = $orig_post;
}


/*-----------------------------------------------------------------------------------*/
# Get Most Racent posts
/*-----------------------------------------------------------------------------------*/
function tie_last_posts($numberOfPosts = 5 , $thumb = true){
	global $post;
	$orig_post = $post;
	
	$lastPosts = get_posts('numberposts='.$numberOfPosts);
	foreach($lastPosts as $post): setup_postdata($post);
?>
<li>
	<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>			
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php tie_thumb('',50,50); ?></a>
		</div><!-- post-thumbnail /-->
	<?php endif; ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h3>
	<?php tie_get_score(); ?> <span class="date"><?php tie_get_time(); ?></span>
</li>
<?php endforeach; 
	$post = $orig_post;
}


/*-----------------------------------------------------------------------------------*/
# Get Most Racent posts from Category
/*-----------------------------------------------------------------------------------*/
function tie_last_posts_cat($numberOfPosts = 5 , $thumb = true , $cats = 1){
	global $post;
	$orig_post = $post;

	$lastPosts = get_posts('category='.$cats.'&numberposts='.$numberOfPosts);
	foreach($lastPosts as $post): setup_postdata($post);
?>
<li>
	<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>			
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php tie_thumb('',50,50); ?></a>
		</div><!-- post-thumbnail /-->
	<?php endif; ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h3>
	<?php tie_get_score(); ?> <span class="date"><?php tie_get_time() ?></span>
</li>
<?php endforeach;
	$post = $orig_post;
}

/*-----------------------------------------------------------------------------------*/
# Get Most Racent posts from Category with Authors
/*-----------------------------------------------------------------------------------*/
function tie_last_posts_cat_authors($numberOfPosts = 5 , $thumb = true , $cats = 1){
	global $post;
	$orig_post = $post;

	$lastPosts = get_posts('category='.$cats.'&numberposts='.$numberOfPosts);
	foreach($lastPosts as $post): setup_postdata($post);
?>
<li>
	<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>			
		<div class="post-thumbnail">
			<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?>" title="<?php sprintf( esc_attr__( 'View all posts by %s', 'tie' ), get_the_author() ) ?>"><?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'MFW_author_bio_avatar_size', 50 ) ); ?></a>
		</div><!-- post-thumbnail /-->
	<?php endif; ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h3>
	<strong><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?>" title="<?php sprintf( esc_attr__( 'View all posts by %s', 'tie' ), get_the_author() ) ?>"><?php echo get_the_author() ?> </a></strong>
</li>
<?php endforeach;
	$post = $orig_post;
}


/*-----------------------------------------------------------------------------------*/
# Get Random posts 
/*-----------------------------------------------------------------------------------*/
function tie_random_posts($numberOfPosts = 5 , $thumb = true){
	global $post;
	$orig_post = $post;

	$lastPosts = get_posts('orderby=rand&numberposts='.$numberOfPosts);
	foreach($lastPosts as $post): setup_postdata($post);
?>
<li>
	<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>			
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php tie_thumb('',50,50); ?></a>
		</div><!-- post-thumbnail /-->
	<?php endif; ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php tie_get_score(); ?> <span class="date"><?php tie_get_time(); ?></span>
</li>
<?php endforeach;
	$post = $orig_post;
}


/*-----------------------------------------------------------------------------------*/
# Get Popular posts 
/*-----------------------------------------------------------------------------------*/
function tie_popular_posts($pop_posts = 5 , $thumb = true){
	global $wpdb , $post;
	$orig_post = $post;
	
	$popularposts = "SELECT ID,post_title,post_date,post_author,post_content,post_type FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY comment_count DESC LIMIT 0,".$pop_posts;
	$posts = $wpdb->get_results($popularposts);
	if($posts){
		global $post;
		foreach($posts as $post){
		setup_postdata($post);?>
			<li>
			<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>			
				<div class="post-thumbnail">
					<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php tie_thumb('',50,50); ?></a>
				</div><!-- post-thumbnail /-->
			<?php endif; ?>
				<h3><a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php echo the_title(); ?>"><?php echo the_title(); ?></a></h3>
				<?php tie_get_score(); ?> <span class="date"><?php tie_get_time(); ?></span>
			</li>
	<?php 
		}
	}
	$post = $orig_post;
}


/*-----------------------------------------------------------------------------------*/
# Get Most commented posts 
/*-----------------------------------------------------------------------------------*/
function tie_most_commented($comment_posts = 5 , $avatar_size = 50){
$comments = get_comments('status=approve&number='.$comment_posts);
foreach ($comments as $comment) { ?>
	<li>
		<div class="post-thumbnail">
			<?php echo get_avatar( $comment, $avatar_size ); ?>
		</div>
		<a href="<?php echo get_permalink($comment->comment_post_ID ); ?>#comment-<?php echo $comment->comment_ID; ?>">
		<?php echo strip_tags($comment->comment_author); ?>: <?php echo wp_html_excerpt( $comment->comment_content, 60 ); ?>... </a>
	</li>
<?php } 
}

/*-----------------------------------------------------------------------------------*/
# Get Best Reviews posts 
/*-----------------------------------------------------------------------------------*/
function tie_best_reviews_posts($pop_posts = 5 , $thumb = true){
	global $post;
	$orig_post = $post;

	$cat_query1 = new WP_Query(array('posts_per_page' => $pop_posts, 'orderby' => 'meta_value_num' ,  'meta_key' => 'tie_review_score', 'post_status' => 'publish'));
	while ( $cat_query1->have_posts() ) : $cat_query1->the_post()?>
<li>
	<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>			
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php tie_thumb('',60,60); ?></a>
		</div><!-- post-thumbnail /-->
	<?php endif; ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php tie_get_score(); ?> <span class="date"><?php tie_get_time(); ?></span>
</li>
<?php endwhile;
	$post = $orig_post;
}


/*-----------------------------------------------------------------------------------*/
# Get Social Counter
/*-----------------------------------------------------------------------------------*/
function tie_curl_subscribers_text_counter( $xml_url ) {
	if( function_exists('curl_version') ){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $xml_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}else echo '<p style="display:block; padding: 5px; font-weight:bold; clear:both; background: rgb(255, 157, 157);">CURL function is disabled. Please check your php configuration ... Ask your webhosting support to enable cURL</p>';
}

function tie_rss_count( $fb_id ) {
	$feedburner['rss_count'] = get_option( 'rss_count');
	return $feedburner;
}

function tie_followers_count() {
	$twitter_username 		= tie_get_option('twitter_username');
	$twitter['page_url'] = 'http://www.twitter.com/'.$twitter_username;
	$twitter['followers_count'] = get_transient('twitter_count');
	if( empty( $twitter['followers_count']) ){
		try {
		
			$data = @json_decode(tie_curl_subscribers_text_counter("https://twitter.com/users/$twitter_username.json") , true);
			$twitter['followers_count'] = (int) $data['followers_count'];	
			
			if( empty($twitter['followers_count']) ){
				$consumer_key 			= tie_get_option('twitter_consumer_key');
				$consumer_secret		= tie_get_option('twitter_consumer_secret');
				$access_token 			= tie_get_option('twitter_access_token');
				$access_token_secret 	= tie_get_option('twitter_access_token_secret');
				
				$twitterConnection = new TwitterOAuth( $consumer_key , $consumer_secret , $access_token , $access_token_secret	);
				$twitterData = $twitterConnection->get('users/show', array('screen_name' => $twitter_username));
				$twitter['followers_count'] = $twitterData->followers_count;
			}
			
		} catch (Exception $e) {
			$twitter['followers_count'] = 0;
		}
		if( !empty( $twitter['followers_count'] ) ){
			set_transient( 'twitter_count' , $twitter['followers_count'] , 1200);
			if( get_option( 'followers_count') != $twitter['followers_count'] ) 
				update_option( 'followers_count' , $twitter['followers_count'] );
		}
			
		if( $twitter['followers_count'] == 0 && get_option( 'followers_count') )
			$twitter['followers_count'] = get_option( 'followers_count');
				
		elseif( $twitter['followers_count'] == 0 && !get_option( 'followers_count') )
			$twitter['followers_count'] = 0;
	}
	return $twitter;
}

function tie_facebook_fans( $page_link ){
	$face_link = @parse_url($page_link);

	if( $face_link['host'] == 'www.facebook.com' || $face_link['host']  == 'facebook.com' ){
		$fans = get_transient('fans_count');
		if( empty( $fans ) ){
			try {
				$page_name = substr(@parse_url($page_link, PHP_URL_PATH), 1);
				$data = @json_decode(tie_curl_subscribers_text_counter("http://graph.facebook.com/".$page_name));
				$fans = $data->likes;
			} catch (Exception $e) {
				$fans = 0;
			}
				
			if( !empty($fans) ){
				set_transient( 'fans_count' , $fans , 1200);
				if ( get_option( 'fans_count') != $fans )
					update_option( 'fans_count' , $fans );
			}
				
			if( $fans == 0 && get_option( 'fans_count') )
				$fans = get_option( 'fans_count');
					
			elseif( $fans == 0 && !get_option( 'fans_count') )
				$fans = 0;
		}	
		return $fans;
	}
}


function tie_youtube_subs( $channel_link ){
	$youtube_link = @parse_url($channel_link);

	if( $youtube_link['host'] == 'www.youtube.com' || $youtube_link['host']  == 'youtube.com' ){
		$subs = get_transient('youtube_count');
		if( empty( $subs ) ){
			try {
				$youtube_name = substr(@parse_url($channel_link, PHP_URL_PATH), 6);
				$json = @tie_curl_subscribers_text_counter("http://gdata.youtube.com/feeds/api/users/".$youtube_name."?alt=json");
				$data = json_decode($json, true); 
				$subs = $data['entry']['yt$statistics']['subscriberCount']; 
			} catch (Exception $e) {
				$subs = 0;
			}
			
			if( !empty($subs) ){
				set_transient( 'youtube_count' , $subs , 1200);
				if( get_option( 'youtube_count') != $subs )
					update_option( 'youtube_count' , $subs );
			}
				
			if( $subs == 0 && get_option( 'youtube_count') )
				$subs = get_option( 'youtube_count');
					
			elseif( $subs == 0 && !get_option( 'youtube_count') )
				$subs = 0;
		}
		return $subs;
	}
}


function tie_vimeo_count( $page_link ) {
	$vimeo_link = @parse_url($page_link);

	if( $vimeo_link['host'] == 'www.vimeo.com' || $vimeo_link['host']  == 'vimeo.com' ){
		$vimeo = get_transient('vimeo_count');
		if( empty( $vimeo ) ){
			try {
				$page_name = substr(@parse_url($page_link, PHP_URL_PATH), 10);
				@$data = @json_decode(tie_curl_subscribers_text_counter( 'http://vimeo.com/api/v2/channel/' . $page_name  .'/info.json'));
			
				$vimeo = $data->total_subscribers;
			} catch (Exception $e) {
				$vimeo = 0;
			}

			if( !empty($vimeo) ){
				set_transient( 'vimeo_count' , $vimeo , 1200);
				if( get_option( 'vimeo_count') != $vimeo )
					update_option( 'vimeo_count' , $vimeo );
			}
				
			if( $vimeo == 0 && get_option( 'vimeo_count') )
				$vimeo = get_option( 'vimeo_count');
					
			elseif( $vimeo == 0 && !get_option( 'vimeo_count') )
				$vimeo = 0;
		}
		return $vimeo;
	}

}

function tie_dribbble_count( $page_link ) {
	$dribbble_link = @parse_url($page_link);

	if( $dribbble_link['host'] == 'www.dribbble.com' || $dribbble_link['host']  == 'dribbble.com' ){
		$dribbble = get_transient('dribbble_count');
		if( empty( $dribbble ) ){
			try {
				$page_name = substr(@parse_url($page_link, PHP_URL_PATH), 1);
				@$data = @json_decode(tie_curl_subscribers_text_counter( 'http://api.dribbble.com/' . $page_name));
			
				$dribbble = $data->followers_count;
			} catch (Exception $e) {
				$dribbble = 0;
			}

			if( !empty($dribbble) ){
				set_transient( 'dribbble_count' , $dribbble , 1200);
				if( get_option( 'dribbble_count') != $dribbble )
					update_option( 'dribbble_count' , $dribbble );
			}
				
			if( $dribbble == 0 && get_option( 'dribbble_count') )
				$dribbble = get_option( 'dribbble_count');
					
			elseif( $dribbble == 0 && !get_option( 'dribbble_count') )
				$dribbble = 0;
		}
		return $dribbble;
	}

}

/*-----------------------------------------------------------------------------------*/
# Google Map Function
/*-----------------------------------------------------------------------------------*/
function tie_google_maps($src , $width = 610 , $height = 440 , $class="") {
	return '<div class="google-map '.$class.'"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe></div>';
}

/*-----------------------------------------------------------------------------------*/
# Google Map Function
/*-----------------------------------------------------------------------------------*/
function tie_soundcloud($url , $autoplay = 'false' ) {
	return '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url='.$url.'&amp;auto_play='.$autoplay.'&amp;show_artwork=true"></iframe>';
}			

/*-----------------------------------------------------------------------------------*/
# Login Form
/*-----------------------------------------------------------------------------------*/
function tie_login_form( $login_only  = 0 ) {
	global $user_ID, $user_identity, $user_level;
	
	if ( $user_ID ) : ?>
		<?php if( empty( $login_only ) ): ?>
		<div id="user-login">
			<p class="welcome-text"><?php _e( 'Welcome' , 'tie' ) ?> <strong><?php echo $user_identity ?></strong> .</p>
			<span class="author-avatar"><?php echo get_avatar( $user_ID, $size = '85'); ?></span>
			<ul>
				<li><a href="<?php echo home_url() ?>/wp-admin/"><?php _e( 'Dashboard' , 'tie' ) ?> </a></li>
				<li><a href="<?php echo home_url() ?>/wp-admin/profile.php"><?php _e( 'Your Profile' , 'tie' ) ?> </a></li>
				<li><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Logout' , 'tie' ) ?> </a></li>
			</ul>
			<div class="author-social">
				<?php if ( get_the_author_meta( 'url' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'url' , $user_ID); ?>" title="<?php echo $user_identity ?> <?php _e( " 's site", 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/site.png" alt="" /></a>
				<?php endif ?>	
				<?php if ( get_the_author_meta( 'twitter' , $user_ID) ) : ?>
				<a class="ttip" href="http://twitter.com/<?php the_author_meta( 'twitter' ); ?>" title="<?php echo $user_identity ?><?php _e( '  on Twitter', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/twitter.png" alt="" /></a>
				<?php endif ?>	
				<?php if ( get_the_author_meta( 'facebook' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'facebook' ); ?>" title="<?php echo $user_identity ?><?php _e( '  on Facebook', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/facebook.png" alt="" /></a>
				<?php endif ?>
				<?php if ( get_the_author_meta( 'google' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'google' ); ?>" title="<?php echo $user_identity ?><?php _e( '  on Google+', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/google_plus.png" alt="" /></a>
				<?php endif ?>	
				<?php if ( get_the_author_meta( 'linkedin' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'linkedin' , $user_ID); ?>" title="<?php echo $user_identity ?><?php _e( '  on Linkedin', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/linkedin.png" alt="" /></a>
				<?php endif ?>				
				<?php if ( get_the_author_meta( 'flickr' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'flickr' , $user_ID); ?>" title="<?php echo $user_identity ?><?php _e( '  on Flickr', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/flickr.png" alt="" /></a>
				<?php endif ?>	
				<?php if ( get_the_author_meta( 'youtube' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'youtube' , $user_ID); ?>" title="<?php echo $user_identity ?><?php _e( '  on YouTube', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/youtube.png" alt="" /></a>
				<?php endif ?>
				<?php if ( get_the_author_meta( 'pinterest' , $user_ID) ) : ?>
				<a class="ttip" href="<?php the_author_meta( 'pinterest' , $user_ID); ?>" title="<?php echo $user_identity ?><?php _e( '  on Pinterest', 'tie' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/socialicons/pinterest.png" alt="" /></a>
				<?php endif ?>	
			</div>
			<div class="clear"></div>
		</div>
		<?php endif; ?>
	<?php else: ?>
		<div id="login-form">
			<form action="<?php echo home_url() ?>/wp-login.php" method="post">
				<p id="log-username"><input type="text" name="log" id="log" value="<?php _e( 'Username' , 'tie' ) ?>" onfocus="if (this.value == '<?php _e( 'Username' , 'tie' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username' , 'tie' ) ?>';}"  size="33" /></p>
				<p id="log-pass"><input type="password" name="pwd" id="pwd" value="<?php _e( 'Password' , 'tie' ) ?>" onfocus="if (this.value == '<?php _e( 'Password' , 'tie' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Password' , 'tie' ) ?>';}" size="33" /></p>
				<input type="submit" name="submit" value="<?php _e( 'Log in' , 'tie' ) ?>" class="login-button" />
				<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> <?php _e( 'Remember Me' , 'tie' ) ?></label>
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
			</form>
			<ul class="login-links">
				<?php if ( get_option('users_can_register') ) : ?><?php echo wp_register() ?><?php endif; ?>
				<li><a href="<?php echo home_url() ?>/wp-login.php?action=lostpassword"><?php _e( 'Lost your password?' , 'tie' ) ?></a></li>
			</ul>
		</div>
	<?php endif;
}


/*-----------------------------------------------------------------------------------*/
# Get Og Image of post
/*-----------------------------------------------------------------------------------*/
function tie_og_image() {
	global $post ;
	
	if ( function_exists("has_post_thumbnail") && has_post_thumbnail() )
		$post_thumb = tie_thumb_src('', 660 ,330) ;
	else{
		$get_meta = get_post_custom($post->ID);
		if( !empty( $get_meta["tie_video_url"][0] ) ){
			$video_url = $get_meta["tie_video_url"][0];
			$video_link = @parse_url($video_url);
			if ( $video_link['host'] == 'www.youtube.com' || $video_link['host']  == 'youtube.com' ) {
				parse_str( @parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
				$video =  $my_array_of_vars['v'] ;
				$post_thumb ='http://img.youtube.com/vi/'.$video.'/0.jpg';
			}
			elseif( $video_link['host'] == 'www.vimeo.com' || $video_link['host']  == 'vimeo.com' ){
				$video = (int) substr(@parse_url($video_url, PHP_URL_PATH), 1);
				$url = 'http://vimeo.com/api/v2/video/'.$video.'.php';;
				$contents = @file_get_contents($url);
				$thumb = @unserialize(trim($contents));
				$post_thumb = $thumb[0][thumbnail_large];
			}
		}
	}
	
	if( isset($post_thumb) )
		echo '<meta property="og:image" content="'. $post_thumb .'" />';
}


/*-----------------------------------------------------------------------------------*/
# For Empty Widgets Titles 
/*-----------------------------------------------------------------------------------*/
function tie_widget_title($title){
	if( empty( $title ) )
		return ' ';
	else return $title;
}
add_filter('widget_title', 'tie_widget_title');


/*-----------------------------------------------------------------------------------*/
# Get Reviews Box Function 
/*-----------------------------------------------------------------------------------*/
$tie_reviews_attr = array(
	'review'		=>		'itemprop="review" itemscope itemtype="http://schema.org/Review" ',
	'name'			=>		'itemprop="name"'
);
function tie_get_review( $position = "review-top" ){
	global $post ;
	$get_meta = get_post_custom($post->ID);
	$criterias = unserialize( $get_meta['tie_review_criteria'][0] );
	$summary = $get_meta['tie_review_summary'][0] ;
	$short_summary = $get_meta['tie_review_total'][0] ;
	$style = $get_meta['tie_review_style'][0];
	$total_counter = $score = 0;
	?>
	<span style="display:none" class="entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></span>
	<meta itemprop="datePublished" content="<?php the_time( 'Y-m-d' ); ?>" />
	<div style="display:none" itemprop="reviewBody"><?php  the_excerpt(); ?></div>
	<div class="review-box <?php echo $position; if( $style == 'percentage' ) echo ' review-percentage'; elseif( $style == 'points' ) echo ' review-percentage'; else echo ' review-stars'?>">
		<h2 class="review-box-header"><?php _e( 'Review Overview' , 'tie' ) ?></h2>
		<?php foreach( $criterias as $criteria){ 
			if( $criteria['name'] && $criteria['score'] && is_numeric( $criteria['score'] )){
				if( $criteria['score'] > 100 ) $criteria['score'] = 100;
				if( $criteria['score'] < 0 ) $criteria['score'] = 1;
				
			$score += $criteria['score'];
			$total_counter ++;
		?>
		<?php if( $style == 'percentage' ): ?>
		<div class="review-item">
			<h5><?php echo $criteria['name'] ?> - <?php echo $criteria['score'] ?>%</h5>
			<span><span style="width:<?php echo $criteria['score'] ?>%"></span></span>
		</div>
		<?php elseif( $style == 'points' ):   $point =  $criteria['score']/10; ?>
		<div class="review-item">
			<h5><?php echo $criteria['name'] ?> - <?php echo $point ?></h5>
			<span><span style="width:<?php echo $criteria['score'] ?>%"></span></span>
		</div>
		<?php else: ?>
		<div class="review-item">
			<h5><?php echo $criteria['name'] ?></h5>
			<span class="stars-large"><span style="width:<?php echo $criteria['score'] ?>%"></span></span>
		</div>
		<?php endif; ?>
		<?php }
		}
		if( !empty( $score ) && !empty( $total_counter ) )
			$total_score =  $score / $total_counter ;
		?>
		<div class="review-summary" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
		<meta itemprop="worstRating" content = "1" />
		<meta itemprop="bestRating" content = "100" />
		<span class="rating points" style="display:none"><span class="rating points" itemprop="ratingValue"><?php echo round($total_score) ?></span></span>
		<?php if( $style == 'percentage' ): ?>
			<div class="review-final-score">
				<h3><?php echo round($total_score) ?><span>%</span></h3>
				<h4><?php echo $short_summary; ?></h4>
			</div>
		<?php elseif( $style == 'points' ): $total_score = $total_score/10 ; ?>
			<div class="review-final-score">
				<h3><?php echo round($total_score,1) ?></h3>
				<h4><?php echo $short_summary; ?></h4>
			</div>
		<?php else: ?>
			<div class="review-final-score">
				<span title="<?php echo $short_summary ?>" class="stars-large"><span style="width:<?php echo $total_score ?>%"></span></span>
				<h4><?php echo $short_summary; ?></h4>
			</div>
		<?php endif; ?>
			<?php if( !empty( $summary ) ){ ?>
			<div class="review-short-summary" itemprop="description">
				<p><strong><?php _e( 'Summary :' , 'tie' ) ?> </strong> <?php echo $summary; ?></p>
			</div>
			<?php } ?>
		</div>
		<?php echo tie_get_user_rate(); ?>
		<span style="display:none" itemprop="reviewRating"><?php echo round($total_score) ?></span>
	</div>
	<?php 
	// To store the total score in the DB
	$total_score_new = $get_meta['tie_review_score'][0];
	if( empty($total_score_new) || $total_score_new = ' ' )	
		update_post_meta($post->ID, 'tie_review_score', $total_score);
}


/*-----------------------------------------------------------------------------------*/
# Get Totla Reviews Score
/*-----------------------------------------------------------------------------------*/
function tie_get_score(){
	global $post ;
	$summary = 0;
	$get_meta = get_post_custom($post->ID);
	if( !empty( $get_meta['tie_review_position'][0] ) ){
	$criterias = unserialize( $get_meta['tie_review_criteria'][0] );
	$short_summary = $get_meta['tie_review_total'][0] ;
	$total_score = $get_meta['tie_review_score'][0];
	$style = $get_meta['tie_review_style'][0];

	if( empty($total_score) || $total_score = ' ' )	tie_update_score();
	
	$total_score = $get_meta['tie_review_score'][0];
	if( $style == 'points' )  $total_score =  $total_score *10 ;
	
	?>
	<span title="<?php echo $short_summary ?>" class="stars-small"><span style="width:<?php echo $total_score ?>%"></span></span>
	<?php 
	}
}

/*-----------------------------------------------------------------------------------*/
# update Total Reviews Score
/*-----------------------------------------------------------------------------------*/
function tie_update_score(){
	global $post ;
	$get_meta = get_post_custom($post->ID);

	$total_counter = $score = 0;
	$criterias = unserialize( $get_meta['tie_review_criteria'][0] );
	if( !empty($criterias) ){
		foreach( $criterias as $criteria){ 
			if( $criteria['name'] && $criteria['score'] && is_numeric( $criteria['score'] )){
				if( $criteria['score'] > 100 ) $criteria['score'] = 100;
				if( $criteria['score'] < 0 ) $criteria['score'] = 1;
					
			$score += $criteria['score'];
			$total_counter ++;
			}
		}
		if( !empty( $score ) && !empty( $total_counter ) )
			$total_score =  $score / $total_counter ;
		
		if( !empty($total_score) )
			update_post_meta($post->ID, 'tie_review_score', $total_score);
	}
}



/*-----------------------------------------------------------------------------------*/
# Users rate posts function
/*-----------------------------------------------------------------------------------*/
add_action('wp_ajax_nopriv_tie_rate_post', 'tie_rate_post');
add_action('wp_ajax_tie_rate_post', 'tie_rate_post');
function tie_rate_post(){
	global $user_ID;
	
	if( tie_get_option('allowtorate') == 'none' || ( !empty($user_ID) && tie_get_option('allowtorate') == 'guests' ) ||	( empty($user_ID) && tie_get_option('allowtorate') == 'users' ) ){ 
		return false ;
	}else{	
		$count = $rating = $rate = 0;
		$postID = $_REQUEST['post'];
		$rate = abs($_REQUEST['value']);
		if($rate > 5 ) $rate = 5;
		
		if( is_numeric( $postID ) ){
			$rating = get_post_meta($postID, 'tie_user_rate' , true);
			$count = get_post_meta($postID, 'tie_users_num' , true);
			if( empty($count) || $count == '' ) $count = 0;
			
			$count++;
			$total_rate = $rating + $rate;
			$total = round($total_rate/$count , 2);
			if ( $user_ID ) {
				$user_rated = get_the_author_meta( 'tie_rated', $user_ID  );

				if( empty($user_rated) ){
					$user_rated[$postID] = $rate;
					
					update_user_meta( $user_ID, 'tie_rated', $user_rated );
					update_post_meta( $postID, 'tie_user_rate', $total_rate );
					update_post_meta( $postID, 'tie_users_num', $count );
					
					echo $total;
				}
				else{
					if( !array_key_exists($postID , $user_rated) ){
						$user_rated[$postID] = $rate;
						update_user_meta( $user_ID, 'tie_rated', $user_rated );
						update_post_meta( $postID, 'tie_user_rate', $total_rate );
						update_post_meta( $postID, 'tie_users_num', $count );
						
						echo $total;
					}
				}
			}else{
				$user_rated = $_COOKIE["tie_rate_".$postID];
				if( empty($user_rated) ){
					setcookie( 'tie_rate_'.$postID , $rate , time()+31104000 , '/');
					update_post_meta( $postID, 'tie_user_rate', $total_rate );
					update_post_meta( $postID, 'tie_users_num', $count );
				}
			}
		}
	}
    die;
}


/*-----------------------------------------------------------------------------------*/
# Get user rate result
/*-----------------------------------------------------------------------------------*/
function tie_get_user_rate(){
	global $post , $user_ID; 
	$disable_rate = false ;

	if( tie_get_option('allowtorate') == 'none' )
		return false;
		
	if( ( !empty($user_ID) && tie_get_option('allowtorate') == 'guests' ) || ( empty($user_ID) && tie_get_option('allowtorate') == 'users' ) ) 
		$disable_rate = true ;
		
	if( !empty($disable_rate) ){
		$no_rate_text = __( 'No Ratings Yet !' , 'tie' );
		$rate_active = false ;
	}
	else{
		$no_rate_text = __( 'Be the first one !' , 'tie' );
		$rate_active = ' user-rate-active' ;
	}
		
	$image_style ='stars';
	
	$rate = get_post_meta( $post->ID , 'tie_user_rate', true );
	$count = get_post_meta( $post->ID , 'tie_users_num', true );
	if( !empty($rate) && !empty($count)){
		$total = (($rate/$count)/5)*100;
		$totla_users_score = round($rate/$count,2);
	}else{
		$totla_users_score = $total = $count = 0;
	}
	
	if ( $user_ID ) {
		$user_rated = get_the_author_meta( 'tie_rated' , $user_ID ) ;
		if( !empty($user_rated) && is_array($user_rated) && array_key_exists($post->ID , $user_rated) ){
			$user_rate = round( ($user_rated[$post->ID]*100)/5 , 2);
			return $output = '<div class="user-rate-wrap"><span class="user-rating-text"><strong>'.__( "Your Rating:" , "tie" ) .' </strong> <span class="taq-score">'.$user_rated[$post->ID].'</span> <small>( <span class="taq-count">'.$count.'</span> '.__( "votes" , "tie" ) .')</small> </span><div data-rate="'. $user_rate .'" class="rate-post-'.$post->ID.' user-rate rated-done" title=""><span class="user-rate-image post-large-rate '.$image_style.'-large"><span style="width:'. $user_rate .'%"></span></span></div><div class="taq-clear"></div></div>';
		}
	}else{
		$user_rate = $_COOKIE["tie_rate_".$post->ID] ;
		
		if( !empty($user_rate) ){
			return $output = '<div class="user-rate-wrap"><span class="user-rating-text"><strong>'.__( "Your Rating:" , "tie" ) .' </strong> <span class="taq-score">'.$user_rate.'</span> <small>( <span class="taq-count">'.$count.'</span> '.__( "votes" , "tie" ) .')</small> </span><div class="rate-post-'.$post->ID.' user-rate rated-done" title=""><span class="user-rate-image post-large-rate '.$image_style.'-large"><span style="width:'. (($user_rate*100)/5) .'%"></span></span></div><div class="taq-clear"></div></div>';
		}
		
	}
	if( $total == 0 && $count == 0)
		return $output = '<div class="user-rate-wrap"><span class="user-rating-text"><strong>'.__( "User Rating:" , "tie" ) .' </strong> <span class="taq-score"></span> <small>'.$no_rate_text.'</small> </span><div data-rate="'. $total .'" data-id="'.$post->ID.'" class="rate-post-'.$post->ID.' user-rate'.$rate_active.'"><span class="user-rate-image post-large-rate '.$image_style.'-large"><span style="width:'. $total .'%"></span></span></div><div class="taq-clear"></div></div>';
	else
		return $output = '<div class="user-rate-wrap"><span class="user-rating-text"><strong>'.__( "User Rating:" , "tie" ) .' </strong> <span class="taq-score">'.$totla_users_score.'</span> <small>( <span class="taq-count">'.$count.'</span> '.__( "votes" , "tie" ) .')</small> </span><div data-rate="'. $total .'" data-id="'.$post->ID.'" class="rate-post-'.$post->ID.' user-rate'.$rate_active.'"><span class="user-rate-image post-large-rate '.$image_style.'-large"><span style="width:'. $total .'%"></span></span></div><div class="taq-clear"></div></div>';
}




/*-----------------------------------------------------------------------------------*/
# Get the post time
/*-----------------------------------------------------------------------------------*/
function tie_get_time(){
	global $post ;
	if( tie_get_option( 'time_format' ) == 'none' ){
		return false;
	}elseif( tie_get_option( 'time_format' ) == 'modern' ){	
		$to = current_time('timestamp'); //time();
		$from = get_the_time('U') ;
		
		$diff = (int) abs($to - $from);
		if ($diff <= 3600) {
			$mins = round($diff / 60);
			if ($mins <= 1) {
				$mins = 1;
			}
			$since = sprintf(_n('%s min', '%s mins', $mins), $mins) .' '. __( 'ago' , 'tie' );
		}
		else if (($diff <= 86400) && ($diff > 3600)) {
			$hours = round($diff / 3600);
			if ($hours <= 1) {
				$hours = 1;
			}
			$since = sprintf(_n('%s hour', '%s hours', $hours), $hours) .' '. __( 'ago' , 'tie' );
		}
		elseif ($diff >= 86400) {
			$days = round($diff / 86400);
			if ($days <= 1) {
				$days = 1;
				$since = sprintf(_n('%s day', '%s days', $days), $days) .' '. __( 'ago' , 'tie' );
			}
			elseif( $days > 29){
				$since = get_the_time(get_option('date_format'));
			}
			else{
				$since = sprintf(_n('%s day', '%s days', $days), $days) .' '. __( 'ago' , 'tie' );
			}
		}
	}else{
		$since = get_the_time(get_option('date_format'));
	}
	echo '<span>'.$since.'</span>';
}



/*-----------------------------------------------------------------------------------*/
# Add "dark-skin" for body
/*-----------------------------------------------------------------------------------*/
add_filter('body_class','tie_body_class_dark');
function tie_body_class_dark($classes) {
	if( tie_get_option('dark_skin') )
		$classes[] = 'dark-skin';
	return $classes;
}

/*-----------------------------------------------------------------------------------*/
# Add Class to Gallery shortcode for lightbox
/*-----------------------------------------------------------------------------------*/
if( !tie_get_option( 'disable_gallery_shortcode' ) )
add_filter( 'post_gallery', 'tie_post_gallery', 10, 2 );

function tie_post_gallery( $output, $attr) {
    global $post, $wp_locale;

    static $instance = 0;
    $instance++;

    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 3,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";
	
	$images_class ='';
	if( isset($attr['link']) && 'file' == $attr['link'] )
		$images_class = "gallery-images";
	
    $output = apply_filters('gallery_style', "
        <style type='text/css'>
            #{$selector} {
                margin: auto;
            }
            #{$selector} .gallery-item {
                float: {$float};
                margin-top: 10px;
                text-align: center;
                width: {$itemwidth}%;           }
            #{$selector} img {
                border: 2px solid #cfcfcf;
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
        </style>
        <!-- see gallery_shortcode() in wp-includes/media.php -->
        <div id='$selector' class='$images_class gallery galleryid-{$id}'>");

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-icon'>
                $link
            </{$icontag}>";
        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <{$captiontag} class='gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
        if ( $columns > 0 && ++$i % $columns == 0 )
            $output .= '<br style="clear: both" />';
    }

    $output .= "
            <br style='clear: both;' />
        </div>\n";

    return $output;
}
	

/**
 * Creates a nicely formatted and more specific title element text for output
 */
function tie_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'tie' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'tie_wp_title', 10, 2 );


/*-----------------------------------------------------------------------------------*/
/*-----------------------------------------------------------------------------------*/
function tie_fix_shortcodes($content){   
    $array = array (
        '[raw]' => '', 
        '[/raw]' => '', 
        '<p>[raw]' => '', 
        '[/raw]</p>' => '', 
        '[/raw]<br />' => '', 
        '<p>[' => '[', 
        ']</p>' => ']', 
        ']<br />' => ']'
    );

    $content = strtr($content, $array);
    return $content;
}
add_filter('the_content', 'tie_fix_shortcodes');


/*-----------------------------------------------------------------------------------*/
# Check if the current page is wp-login.php or wp-register.php
/*-----------------------------------------------------------------------------------*/
function tie_is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}


/*-----------------------------------------------------------------------------------*/
# WP 3.6.0
/*-----------------------------------------------------------------------------------*/
// For old theme versions Video shortcode
function tie_video_fix_shortcodes($content){   
	$v = '/(\[(video)\s?.*?\])(.+?)(\[(\/video)\])/';
	$content = preg_replace( $v , '$3' , $content);
    return $content;
}
add_filter('the_content', 'tie_video_fix_shortcodes', 0);

//To prevent wordpress from importing mediaelement css file
function tie_audio_video_shortcode(){
	if( !is_admin()){
		wp_enqueue_script( 'wp-mediaelement' );
		return false;
	}
}
add_filter('wp_audio_shortcode_library', 'tie_audio_video_shortcode');
add_filter('wp_video_shortcode_library', 'tie_audio_video_shortcode');

//Responsive Videos
function tie_video_width_shortcode( $html ){
	$width1 = 'width: 100%';
	return preg_replace('/width: ([0-9]*)px/',$width1,$html);
}
add_filter('wp_video_shortcode', 'tie_video_width_shortcode');
?>