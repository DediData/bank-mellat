<?php
/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function widget_last_tutorials_wp_beginner() {

	wp_add_dashboard_widget(
                 'last_tutorials_wp_beginner',         // Widget slug.
                 'آخرین آموزش های وردپرس',         // Title.
                 'wp_beginner_dashboard_widget_function' // Display function.
        );	
}
add_action( 'wp_dashboard_setup', 'widget_last_tutorials_wp_beginner' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function wp_beginner_dashboard_widget_function() {

	// Get RSS Feed(s)
	include_once( ABSPATH . WPINC . '/feed.php' );

	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed( 'http://wp-beginner.ir/feed/' );

	$maxitems = 0;

	if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

		// Figure out how many total items there are, but limit it to 5. 
		$maxitems = $rss->get_item_quantity( 5 ); 

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items( 0, $maxitems );

	endif;
	?>
	<div class="rss-widget">

		<ul>
			<?php if ( $maxitems == 0 ) : ?>
				<li><?php _e( 'آموزشی یافت نشد!', 'wp-beginner' ); ?></li>
			<?php else : ?>
				<?php // Loop through each feed item and display each item as a hyperlink. ?>
				<?php foreach ( $rss_items as $item ) : ?>
					<li>
						<a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" title="<?php echo esc_html( $item->get_title() ); ?>">
							<?php echo esc_html( $item->get_title() ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>
<?php
}
?>