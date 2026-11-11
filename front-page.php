<?php get_header();
?>

	<main id="primary" class="site-main the-wraper">
    	<section class="cover">
			<?php
			$featured_post_id = absint( get_theme_mod( 'selfhelpblog_featured_post', 0 ) );
			$featured_post    = $featured_post_id ? get_post( $featured_post_id ) : null;

			if ( is_front_page() && $featured_post instanceof WP_Post && 'publish' === $featured_post->post_status && 'post' === $featured_post->post_type ) :
				$featured_categories = get_the_category( $featured_post_id );
				
				?>
				<article class="featured-post"<?php echo $featured_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="featured-post-title">
					<div class="featured-post__content">
						<p class="featured-post__label"><?php esc_html_e( 'Featured', 'selfhelpblog' ); ?></p>
						<?php if ( ! empty( $featured_categories ) ) : ?>
							<p class="featured-post__category"><?php echo esc_html( $featured_categories[0]->name ); ?></p>
						<?php endif; ?>
						<h1 id="featured-post-title" class="featured-post__title">
							<a href="<?php echo esc_url( get_permalink( $featured_post_id ) ); ?>"><?php echo esc_html( get_the_title( $featured_post_id ) ); ?></a>
						</h1>
						<p class="featured-post__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $featured_post ), 24 ) ); ?></p>
						<a class="featured-post__link" href="<?php echo esc_url( get_permalink( $featured_post_id ) ); ?>">
							<?php esc_html_e( 'Read article', 'selfhelpblog' ); ?> <span aria-hidden="true">&rarr;</span>
						</a>
					</div>
				</article>			
			<?php endif; ?>

				<div class="circles">
					<div class="circle-no1">

					</div>

					<div class="circle-no2">
						
					</div>

				</div>
		</section>

		<section class="latest-posts">
			<h2 class="section-title"><?php esc_html_e( 'Latest Posts', 'selfhelpblog' ); ?></h2>
			<?php
			$latest_posts = new WP_Query(
				array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => 5,
					'post__not_in'        => $featured_post_id ? array( $featured_post_id ) : array(),
					'orderby'             => array( 'date' => 'DESC', 'ID' => 'DESC' ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			?>
			<?php if ( $latest_posts->have_posts() ) : ?>
				<div class="latest-posts-grid">
					<?php
					while ( $latest_posts->have_posts() ) :
						$latest_posts->the_post();
						$latest_post_number = $latest_posts->current_post + 1;
						$latest_post_image  = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
						$latest_post_style  = $latest_post_image
							? sprintf( 'background-image:linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url("%s");', esc_url( $latest_post_image ) )
							: 'background-image:linear-gradient(135deg, #334155, #0f172a);';
						?>
						<article class="latest-post<?php echo 1 === $latest_post_number ? ' star' : ''; ?> latest-post--<?php echo esc_attr( $latest_post_number ); ?>">
							<div class="post-img" style="<?php echo esc_attr( $latest_post_style ); ?>" aria-hidden="true"></div>
							<?php if ( 1 !== $latest_post_number ) : ?>
								<div class="latest-post-details">
							<?php endif; ?>
							<h3><?php echo esc_html( get_the_title() ); ?></h3>
							<p class="post-exerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?></p>
							<a class="featured-post__link" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php esc_html_e( 'Read article', 'selfhelpblog' ); ?> <span aria-hidden="true">&rarr;</span>
							</a>
							<?php if ( 1 !== $latest_post_number ) : ?>
								
							</div>
							<?php endif; ?>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<p><?php esc_html_e( 'No posts published yet.', 'selfhelpblog' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</section>

		<!-- FEATURED CATEGORIES -->
		<?php
			$featured_categories = array();
			for ( $slot = 1; $slot <= 6; $slot++ ) {
				$category_id = absint( get_theme_mod( 'selfhelpblog_featured_category_' . $slot, 0 ) );
				$category    = $category_id ? get_term( $category_id, 'category' ) : null;
				if ( ! $category instanceof WP_Term ) {
					continue;
				}
				$category_link = get_term_link( $category );
				if ( is_wp_error( $category_link ) ) {
					continue;
				}
				$featured_categories[] = array(
					'name'  => $category->name,
					'url'   => $category_link,
					'image' => wp_get_attachment_image_url( absint( get_theme_mod( 'selfhelpblog_featured_category_' . $slot . '_image', 0 ) ), 'large' ),
				);
			}
		?>
		<?php if ( $featured_categories ) : ?>
		<section class="featured-categories" >
			<h2 class="section-title"><?php esc_html_e( 'Explore Featured Categories', 'selfhelpblog' ); ?></h2>
			<div class="featured-categories-grid">
				<?php foreach ( $featured_categories as $featured_category ) : ?>
					<?php
					$category_style = $featured_category['image']
						? sprintf( 'background-image:linear-gradient(rgba(0,0,0,0.2),rgba(0,0,0,0.2)),url("%s");', esc_url( $featured_category['image'] ) )
						: 'background-image:linear-gradient(135deg, #334155, #0f172a);';
					?>
					<a class="featured-category" href="<?php echo esc_url( $featured_category['url'] ); ?>" style="<?php echo esc_attr( $category_style ); ?>">
						<span class="featured-category__name"><?php echo esc_html( $featured_category['name'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>
				
				

		<?php
		$featured_articles = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => -1,
				'meta_key'            => '_selfhelpblog_featured_article',
				'meta_value'          => '1',
				'orderby'             => array( 'date' => 'DESC', 'ID' => 'DESC' ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		?>
		<?php if ( $featured_articles->have_posts() ) : ?>
		<section class="featured-b-posts">
			<h2 class="section-title"><?php esc_html_e( 'Featured Articles', 'selfhelpblog' ); ?></h2>
			<div class="featured-b-posts-grid">
				<?php
				while ( $featured_articles->have_posts() ) :
					$featured_articles->the_post();
					$is_lead_article = 0 === $featured_articles->current_post;
					$article_image   = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					$article_style   = $article_image
						? sprintf( 'background-image:linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url("%s");', esc_url( $article_image ) )
						: 'background-color:#334155;';
					?>
					<article class="featured-b-post <?php echo $is_lead_article ? 'featured-b-post-1' : 'featured-b-post-2'; ?>"<?php if ( $is_lead_article ) : ?> style="<?php echo esc_attr( $article_style ); ?>"<?php endif; ?>>
						<?php if ( ! $is_lead_article ) : ?>
							<figure class="featured-b-post-img" style="<?php echo esc_attr( $article_style ); ?>" aria-hidden="true"></figure>
						<?php endif; ?>
						<div class="<?php echo $is_lead_article ? 'featured-b-post-1-details' : 'featured-b-post-details'; ?>">
							<h3><?php echo esc_html( get_the_title() ); ?></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							<a class="featured-post__link" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php esc_html_e( 'Read article', 'selfhelpblog' ); ?> <span aria-hidden="true">&rarr;</span>
							</a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		</section>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>

		<?php $popular_posts = selfhelpblog_get_popular_posts(); ?>
		<?php if ( $popular_posts->have_posts() ) : ?>
		<section class="most-popular-posts">
			<h2 class="section-title" style="text-align:center;"><?php esc_html_e( 'Most Popular', 'selfhelpblog' ); ?></h2>
			<div class="most-popular-posts-grid">
				<?php
				while ( $popular_posts->have_posts() ) :
					$popular_posts->the_post();
					$popular_number = $popular_posts->current_post + 1;
					$popular_image  = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					$popular_style  = $popular_image
						? sprintf( 'background-image:linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url("%s");', esc_url( $popular_image ) )
						: 'background-color:#334155;';
					?>
					<article class="popular-post<?php echo 1 === $popular_number ? ' star' : ''; ?> popular-post--<?php echo esc_attr( $popular_number ); ?>">
						<div class="post-img" style="<?php echo esc_attr( $popular_style ); ?>" aria-hidden="true"></div>
						<?php if ( 1 !== $popular_number ) : ?>
							<div class="popular-post-details">
						<?php endif; ?>
							<h3><?php echo esc_html( get_the_title() ); ?></h3>
							<p class="post-exerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?></p>
							<a class="featured-post__link" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php esc_html_e( 'Read article', 'selfhelpblog' ); ?> <span aria-hidden="true">&rarr;</span>
							</a>
						<?php if ( 1 !== $popular_number ) : ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endwhile; ?>
			</div>
		</section>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>

	</main><!-- #main -->

<?php

get_footer();

?>
