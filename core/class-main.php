<?php
class pluginComponents{
	
	public function __construct(){
		
		// Call function pages
		$this->pages();

	}
	
	/**
	 * Load and instantiate plugin pages classes
	 *
	 * @since 1.0
	 */
	public function pages(){
		
		// Load order's class
		include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/class-orders.php' );

		// Load settings's class
		include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/class-settings.php' );
		
		// Load help's class
		include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/class-help.php' );
		
		include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/exportOrders.php' );
		
		include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/widget.php' );
		
		// Make object of class orders
		$orders = new orders();

		// Make object of class settings
		$settings = new settings();
		
		// Make object of class help
		$help = new help();
	}
}
?>