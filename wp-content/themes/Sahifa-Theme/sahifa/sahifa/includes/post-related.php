<?php
global $get_meta , $post;

if( ( tie_get_option('related') && empty( $get_meta["tie_hide_related"][0] ) ) || ( isset( $get_meta["tie_hide_related"][0] ) && $get_meta["tie_hide_related"][0] == 'no' ) ):
	$related_no = tie_get_option('related_number') ? tie_get_option('related_number') : 3;
	
	global $post;
	$orig_post = $post;
	
	$query_type = tie_get_option('related_query') ;
	if( $query_type == 'author' ){
		$args=array('post__not_in' => array($post->ID),'posts_per_page'=> $related_no , 'author'=> get_the_author_meta( 'ID' ));
	}elseif( $query_type == 'tag' ){
		$tags = wp_get_post_tags($post->ID);
		$tags_ids = array();
		foreach($tags as $individual_tag) $tags_ids[] = $individual_tag->term_id;
		$args=array('post__not_in' => array($post->ID),'posts_per_page'=> $related_no , 'tag__in'=> $tags_ids );
	}
	else{
		$categories = get_the_category($post->ID);
		$category_ids = array();
		foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
		$args=array('post__not_in' => array($post->ID),'posts_per_page'=> $related_no , 'category__in'=> $category_ids );
	}		
	$related_query = new wp_query( $args );
	if( $related_query->have_posts() ) : $count=0;?>
	<section id="related_posts">
		<div class="block-head">
			<h3><?php _e( 'Related Articles' , 'tie' ); ?></h3><div class="stripe-line"></div>
		</div>
		<div class="post-listing">
			<?php while ( $related_query->have_posts() ) : $related_query->the_post()?>
			<div class="related-item">
				<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
				<div class="post-thumbnail">
					<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
						<?php tie_thumb('', 272 ,125); ?>
						<span class="overlay-icon"></span>
					</a>
				</div><!-- post-thumbnail /-->
				<?php endif; ?>			
				<h3><a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
				<p class="post-meta"><?php the_time(get_option('date_format')); ?></p>
			</div>
			<?php endwhile;?>
			<div class="clear"></div>
		</div>
	</section>
	<?php	endif;
	$post = $orig_post;
	wp_reset_query();
endif; ?>