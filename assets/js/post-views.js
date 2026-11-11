/* global selfhelpblogPostViews */
( function () {
	'use strict';

	function recordView() {
		if ( document.visibilityState !== 'visible' ) {
			return;
		}
		document.removeEventListener( 'visibilitychange', recordView );
		const body = new URLSearchParams( {
			action: 'selfhelpblog_record_post_view',
			post_id: selfhelpblogPostViews.postId,
		} );
		fetch( selfhelpblogPostViews.url, {
			method: 'POST',
			credentials: 'same-origin',
			body,
			keepalive: true,
		} ).catch( function () {
			// Tracking failures must not interrupt reading the article.
		} );
	}

	if ( document.visibilityState === 'visible' ) {
		recordView();
	} else {
		document.addEventListener( 'visibilitychange', recordView );
	}
}() );
