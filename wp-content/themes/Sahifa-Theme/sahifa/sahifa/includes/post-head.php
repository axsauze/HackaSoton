<?php
global $get_meta, $post;;
			
if( $get_meta['tie_post_head'][0] != 'none' ):
	
	$orig_post = $post;
	
	//Get Post Video
	if( $get_meta['tie_post_head'][0] == 'video' ){
		if( isset( $get_meta["tie_video_url"][0] ) && !empty( $get_meta["tie_video_url"][0] ) ){
		
			$video_url = $get_meta["tie_video_url"][0];
			$width  = '100%' ;
			$height = '380';

			$video_link = @parse_url($video_url);
			if ( $video_link['host'] == 'www.youtube.com' || $video_link['host']  == 'youtube.com' ) {
				parse_str( @parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
				$video =  $my_array_of_vars['v'] ;
				$video_code ='<iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video.'?rel=0" frameborder="0" allowfullscreen></iframe>';
			}
			elseif( $video_link['host'] == 'www.youtu.be' || $video_link['host']  == 'youtu.be' ){
				$video = substr(@parse_url($video_url, PHP_URL_PATH), 1);
				$video_code ='<iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video.'?rel=0" frameborder="0" allowfullscreen></iframe>';
			}elseif( $video_link['host'] == 'www.vimeo.com' || $video_link['host']  == 'vimeo.com' ){
				$video = (int) substr(@parse_url($video_url, PHP_URL_PATH), 1);
				$video_code='<iframe src="http://player.vimeo.com/video/'.$video.'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			}
			elseif( $video_link['host'] == 'www.dailymotion.com' || $video_link['host']  == 'dailymotion.com' ){
				$video = substr(@parse_url($video_url, PHP_URL_PATH), 7);
				$video_id = strtok($video, '_');
				$video_code='<iframe frameborder="0" width="'.$width.'" height="'.$height.'" src="http://www.dailymotion.com/embed/video/'.$video_id.'"></iframe>';
			}
		}
		elseif( isset( $get_meta["tie_embed_code"][0] ) ){
			$embed_code = $get_meta["tie_embed_code"][0];
			$embed_code = htmlspecialchars_decode( $embed_code);
			$width = 'width="100%"';
			$height = 'height="450"';
			$embed_code = preg_replace('/width="([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width,$embed_code);
			$video_code = preg_replace( '/height="([0-9]*)"/' , $height , $embed_code );
		}
		if( isset($video_code) ) echo $video_code;
		
	}elseif( $get_meta['tie_post_head'][0] == 'thumb' || ( empty( $get_meta['tie_post_head'][0] ) && tie_get_option( 'post_featured' ) ) ){
		if( $get_meta["tie_sidebar_pos"][0] == 'full' ){
			$width = 995 ;
			$height = 498 ;
		}else{
			$width = 660;
			$height = 330 ; 
		}?>
		<div class="single-post-thumb">
			<?php tie_thumb('', $width , $height ); ?>
		</div>
		
<?php } elseif( $get_meta['tie_post_head'][0] == 'map' && !empty( $get_meta['tie_googlemap_url'][0] ) ){
		if( $get_meta["tie_sidebar_pos"][0] == 'full' ){
			$width = 1003 ;
			$height = 498 ;
		}else{
			$width = 658;
			$height = 330 ; 
		}?>
		<?php echo tie_google_maps( $get_meta['tie_googlemap_url'][0] , $width , $height ); ?>
		
		
<?php }elseif( $get_meta['tie_post_head'][0] == 'slider' && !empty( $get_meta['tie_post_slider'][0] ) ){

	if( $get_meta["tie_sidebar_pos"][0] == 'full' ){
		$width = 995 ;
		$height = 498 ;
	}else{
		$width = 660;
		$height = 330 ; 
	}
			
	$effect = tie_get_option( 'flexi_slider_effect' );
	$speed = tie_get_option( 'flexi_slider_speed' );
	$time = tie_get_option( 'flexi_slider_time' );
	
	if( !$speed || $speed == ' ' || !is_numeric($speed))	$speed = 7000 ;
	if( !$time || $time == ' ' || !is_numeric($time))	$time = 600;
	
	if( $effect == 'slideV' )
			$effect = 'animation: "slide",
					  direction: "vertical",';
	elseif( $effect == 'slideH' )
				$effect = 'animation: "slide",';
	else
		$effect = 'animation: "fade",'; 
		
		$custom_slider_args = array( 'post_type' => 'tie_slider', 'p' => $get_meta['tie_post_slider'][0] );
		$custom_slider = new WP_Query( $custom_slider_args );
	?>
	<div class="flexslider">
		<ul class="slides">
		<?php while ( $custom_slider->have_posts() ) : $custom_slider->the_post();
			$custom = get_post_custom($post->ID);
			$slider = unserialize( $custom["custom_slider"][0] );
			$number = count($slider);
				
			if( $slider ){
			foreach( $slider as $slide ): ?>	
			<li>
				<?php if( !empty( $slide['link'] ) ):?><a href="<?php  echo stripslashes( $slide['link'] )  ?>"><?php endif; ?>
				<img src="<?php echo tie_slider_img_src( $slide['id'] , $width , $height ) ?>" alt="" />
				<?php if( !empty( $slide['link'] ) ):?></a><?php endif; ?>
				<?php if( !empty( $slide['title'] ) || !empty( $slide['caption'] ) ) :?>
				<div class="slider-caption">
					<?php if( !empty( $slide['title'] ) ):?><h2><?php if( !empty( $slide['link'] ) ):?><a href="<?php  echo stripslashes( $slide['link'] )  ?>"><?php endif; ?><?php  echo stripslashes( $slide['title'] )  ?><?php if( !empty( $slide['link'] ) ):?></a><?php endif; ?></h2><?php endif; ?>
					<?php if( !empty( $slide['caption'] ) ):?><p><?php echo stripslashes($slide['caption']) ; ?></p><?php endif; ?>
				</div>
				<?php endif; ?>
			</li>
			<?php endforeach; 
			}?>
		<?php endwhile;?>
		</ul>
	</div>
	<script>
	jQuery(window).load(function() {
	  jQuery('.flexslider').flexslider({
		<?php echo $effect  ?>
		slideshowSpeed: <?php echo $speed ?>,
		animationSpeed: <?php echo $time ?>,
		randomize: false,
		pauseOnHover: true,
		start: function(slider) {
				var slide_control_width = 100/<?php echo $number; ?>;
				jQuery('.flex-control-nav li').css('width', slide_control_width+'%');
			}
	  });
	});
	</script>
<?php }

	$post = $orig_post;
	wp_reset_query();
	
 endif; ?>
