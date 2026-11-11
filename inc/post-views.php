<?php
/**
 * Public article view tracking and popularity queries.
 *
 * @package selfhelpblog
 */

/** Load the tracker on public articles, including cached article pages. */
function selfhelpblog_enqueue_post_views() {
	if ( ! is_singular( 'post' ) || is_preview() || post_password_required() || current_user_can( 'edit_post', get_queried_object_id() ) ) {
		return;
	}

	wp_enqueue_script( 'selfhelpblog-post-views', get_template_directory_uri() . '/assets/js/post-views.js', array(), _S_VERSION, true );
	wp_localize_script(
		'selfhelpblog-post-views',
		'selfhelpblogPostViews',
		array(
			'url'     => admin_url( 'admin-ajax.php' ),
			'postId'  => get_queried_object_id(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'selfhelpblog_enqueue_post_views' );

/**
 * Record one page view. This intentionally public counter uses no expiring
 * nonce so it also works on cached pages; it grants no access to post editing.
 */
function selfhelpblog_record_post_view() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
		wp_send_json_error( null, 405 );
	}

	$post_id = isset( $_POST['post_id'] ) && is_scalar( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$post    = get_post( $post_id );
	if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password ) {
		wp_send_json_error( null, 404 );
	}
	if ( current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_success();
	}

	global $wpdb;
	$meta_key = '_selfhelpblog_post_views';
	add_post_meta( $post_id, $meta_key, 0, true );
	// Increment in the database to avoid losing simultaneous view updates.
	$updated = $wpdb->query(
		$wpdb->prepare(
			"UPDATE {$wpdb->postmeta} SET meta_value = CAST(meta_value AS UNSIGNED) + 1 WHERE post_id = %d AND meta_key = %s",
			$post_id,
			$meta_key
		)
	);
	if ( false === $updated ) {
		wp_send_json_error( null, 500 );
	}
	wp_cache_delete( $post_id, 'post_meta' );
	clean_post_cache( $post_id );
	wp_send_json_success();
}
add_action( 'wp_ajax_selfhelpblog_record_post_view', 'selfhelpblog_record_post_view' );
add_action( 'wp_ajax_nopriv_selfhelpblog_record_post_view', 'selfhelpblog_record_post_view' );

/**
 * Get five public posts, including posts that have not received any views yet.
 *
 * @return WP_Query Popular posts, ordered by numeric view count then date.
 */
function selfhelpblog_get_popular_posts() {
	return new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'has_password'        => false,
			'posts_per_page'      => 5,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'selfhelpblog_popular' => true,
		)
	);
}

/**
 * Sort only the popularity query, treating missing counters as zero.
 *
 * @param string   $orderby Existing SQL order.
 * @param WP_Query $query   Current query.
 * @return string SQL order.
 */
function selfhelpblog_popular_posts_orderby( $orderby, $query ) {
	if ( ! $query->get( 'selfhelpblog_popular' ) ) {
		return $orderby;
	}
	global $wpdb;
	return $wpdb->prepare(
		"COALESCE((SELECT MAX(CAST(views.meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} AS views WHERE views.post_id = {$wpdb->posts}.ID AND views.meta_key = %s), 0) DESC, {$wpdb->posts}.post_date DESC, {$wpdb->posts}.ID DESC",
		'_selfhelpblog_post_views'
	);
}
add_filter( 'posts_orderby', 'selfhelpblog_popular_posts_orderby', 10, 2 );
