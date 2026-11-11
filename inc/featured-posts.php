<?php
/**
 * Post editor controls for the homepage Featured Articles section.
 *
 * @package selfhelpblog
 */

/** Register the control in both the block and classic post editors. */
function selfhelpblog_add_featured_post_meta_box() {
	add_meta_box(
		'selfhelpblog-featured-article',
		__( 'Featured Articles', 'selfhelpblog' ),
		'selfhelpblog_render_featured_post_meta_box',
		'post',
		'side'
	);
}
add_action( 'add_meta_boxes_post', 'selfhelpblog_add_featured_post_meta_box' );

/**
 * Render the featured article checkbox.
 *
 * @param WP_Post $post Current post.
 */
function selfhelpblog_render_featured_post_meta_box( $post ) {
	wp_nonce_field( 'selfhelpblog_save_featured_article', 'selfhelpblog_featured_article_nonce' );
	?>
	<p>
		<label for="selfhelpblog-featured-article-checkbox">
			<input type="checkbox" id="selfhelpblog-featured-article-checkbox" name="selfhelpblog_featured_article" value="1" <?php checked( get_post_meta( $post->ID, '_selfhelpblog_featured_article', true ), '1' ); ?> />
			<?php esc_html_e( 'Show in Featured Articles', 'selfhelpblog' ); ?>
		</label>
	</p>
	<p class="description"><?php esc_html_e( 'Display this post in the homepage Featured Articles section once published.', 'selfhelpblog' ); ?></p>
	<?php
}

/**
 * Save the selection only when the editor control was submitted.
 *
 * @param int $post_id Saved post ID.
 */
function selfhelpblog_save_featured_article( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['selfhelpblog_featured_article_nonce'] ) || ! is_string( $_POST['selfhelpblog_featured_article_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['selfhelpblog_featured_article_nonce'] ) ), 'selfhelpblog_save_featured_article' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['selfhelpblog_featured_article'] ) && '1' === $_POST['selfhelpblog_featured_article'] ) {
		update_post_meta( $post_id, '_selfhelpblog_featured_article', '1' );
	} else {
		delete_post_meta( $post_id, '_selfhelpblog_featured_article' );
	}
}
add_action( 'save_post_post', 'selfhelpblog_save_featured_article' );
