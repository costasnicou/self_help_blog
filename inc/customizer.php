<?php
/**
 * selfhelpblog Theme Customizer
 *
 * @package selfhelpblog
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function selfhelpblog_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'selfhelpblog_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'selfhelpblog_customize_partial_blogdescription',
			)
		);
	}

	$wp_customize->add_section(
		'selfhelpblog_featured_post_section',
		array(
			'title'       => esc_html__( 'Featured post', 'selfhelpblog' ),
			'description' => esc_html__( 'Choose the single post displayed in the homepage header.', 'selfhelpblog' ),
			'priority'    => 35,
		)
	);

	$wp_customize->add_setting(
		'selfhelpblog_featured_post',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$featured_post_choices = array(
		0 => esc_html__( 'None', 'selfhelpblog' ),
	);
	$featured_posts = get_posts(
		array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		)
	);

	foreach ( $featured_posts as $featured_post ) {
		$featured_post_choices[ $featured_post->ID ] = $featured_post->post_title;
	}

	$wp_customize->add_control(
		'selfhelpblog_featured_post',
		array(
			'label'       => esc_html__( 'Select a post', 'selfhelpblog' ),
			'description' => esc_html__( 'Select “None” to hide the featured post.', 'selfhelpblog' ),
			'section'     => 'selfhelpblog_featured_post_section',
			'type'        => 'select',
			'choices'     => $featured_post_choices,
		)
	);

	$wp_customize->add_section(
		'selfhelpblog_featured_categories_section',
		array(
			'title'       => esc_html__( 'Featured categories', 'selfhelpblog' ),
			'description' => esc_html__( 'Choose up to six homepage categories in display order and upload or select an image for each. Choose None to hide a slot.', 'selfhelpblog' ),
			'priority'    => 36,
		)
	);

	$category_choices = array( 0 => esc_html__( 'None', 'selfhelpblog' ) );
	$categories       = get_categories( array( 'hide_empty' => false ) );
	foreach ( $categories as $category ) {
		$category_choices[ $category->term_id ] = $category->name;
	}

	for ( $slot = 1; $slot <= 6; $slot++ ) {
		$category_setting = 'selfhelpblog_featured_category_' . $slot;
		$image_setting    = $category_setting . '_image';

		$wp_customize->add_setting(
			$category_setting,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$category_setting,
			array(
				/* translators: %d: Homepage category slot number. */
				'label'   => sprintf( esc_html__( 'Category %d', 'selfhelpblog' ), $slot ),
				'section' => 'selfhelpblog_featured_categories_section',
				'type'    => 'select',
				'choices' => $category_choices,
			)
		);
		$wp_customize->add_setting(
			$image_setting,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$image_setting,
				array(
					/* translators: %d: Homepage category slot number. */
					'label'     => sprintf( esc_html__( 'Category %d image', 'selfhelpblog' ), $slot ),
					'section'   => 'selfhelpblog_featured_categories_section',
					'mime_type' => 'image',
				)
			)
		);
	}

}
add_action( 'customize_register', 'selfhelpblog_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function selfhelpblog_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function selfhelpblog_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function selfhelpblog_customize_preview_js() {
	wp_enqueue_script( 'selfhelpblog-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'selfhelpblog_customize_preview_js' );
