<?php
/**
 * Bank Mellat Main Class
 * 
 * @package Bank_Mellat
 */

declare(strict_types=1);

namespace BankMellat;

use BankMellat;

/**
 * Class Bank_Mellat
 */
final class Bank_Mellat extends \DediData\Singleton {
	
	/**
	 * Plugin URL
	 * 
	 * @var string $plugin_url
	 */
	protected $plugin_url;

	/**
	 * Plugin Folder
	 * 
	 * @var string $plugin_folder
	 */
	protected $plugin_folder;

	/**
	 * Plugin Name
	 * 
	 * @var string $plugin_name
	 */
	protected $plugin_name;

	/**
	 * Plugin Version
	 * 
	 * @var string $plugin_version
	 */
	protected $plugin_version;
	
	/**
	 * Plugin Slug
	 * 
	 * @var string $plugin_slug
	 */
	protected $plugin_slug;

	/**
	 * Plugin File
	 * 
	 * @var string $plugin_file
	 */
	protected $plugin_file;

	/**
	 * Orders Table Name
	 *
	 * @var string $orders_table_name
	 */
	protected $orders_table_name;

	/**
	 * Constructor
	 * 
	 * @param mixed $plugin_file Plugin File Name.
	 * @see https://developer.wordpress.org/reference/functions/register_activation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_deactivation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	protected function __construct( $plugin_file = null ) {
		$this->plugin_file = $plugin_file;
		$this->set_plugin_info();
		register_activation_hook( $plugin_file, array( $this, 'activate' ) );
		register_deactivation_hook( $plugin_file, array( $this, 'deactivate' ) );
		register_uninstall_hook( $plugin_file, self::class . '::uninstall' );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 11 );
			$this->admin();
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 11 );
			$this->run();
		}

		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb = $GLOBALS['wpdb'];
		
		// Setup global database table names
		$this->orders_table_name = $wpdb->prefix . 'WPBEGPAY_orders';
		$this->transfer_orders_to_new_table();
		// Load plugin components

		if ( ! class_exists( 'BankMellat\Bank_Mellat_Shortcode' ) ) {
			new BankMellat\Bank_Mellat_Shortcode();
		}

		// Load order's class
		if ( ! class_exists( 'BankMellat\Bank_Mellat_Orders' ) ) {
			new BankMellat\Bank_Mellat_Orders();
		}

		// Load settings's class
		if ( ! class_exists( 'BankMellat\Bank_Mellat_Settings' ) ) {
			new BankMellat\Bank_Mellat_Settings();
		}
		
		// Load help's class
		include_once $this->plugin_folder . 'core/class-help.php';
		new \help();
		
		include_once $this->plugin_folder . 'core/exportOrders.php';
		
		// Adds a Settings link to the Plugins page
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

		// Check the db version and run SQL install, if needed
		add_action( 'plugins_loaded', array( $this, 'update_db_check' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_references' ) );
	}

	/**
	 * The function is used to load frontend scripts and styles in a WordPress plugin, with support for
	 * RTL (right-to-left) languages.
	 * 
	 * @return void
	 */
	public function load_frontend_scripts() {
		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/public/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/public/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/public/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/
	}

	/**
	 * Styles for Admin
	 * 
	 * @return void
	 */
	public function load_admin_scripts() {
		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/admin/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/admin/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/admin/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/
	}

	/**
	 * Activate the plugin
	 * 
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/add_option
	 */
	public function activate() {
		// add_option( $this->plugin_slug );
	}

	/**
	 * Run when plugins deactivated
	 * 
	 * @return void
	 */
	public function deactivate() {
		// Clear any temporary data stored by plugin.
		// Flush Cache/Temp.
		// Flush Permalinks.
	}

	/**
	 * Add Settings link to Plugins page
	 *
	 * @param array<string> $links       Links.
	 * @param string        $plugin_file Plugin File.
	 * @return array<string> array Links to add to plugin name
	 */
	public function plugin_action_links( $links, string $plugin_file ) {
		if ( basename( plugin_dir_path( $this->plugin_file ) ) . '/bank-mellat.php' === $plugin_file ) {
			$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=WPBEGPAY-settings' ) ), __( 'Settings', 'bank-mellat' ) );
		}
		return $links;
	}

	/**
	 * Check database version and run SQL install, if needed
	 *
	 * @return void
	 */
	public function update_db_check() {
		// Add a database version to help with upgrades and run SQL install
		if ( ! get_option( 'WPBEGPAY_db_version' ) ) {
			update_option( 'WPBEGPAY_db_version', $this->plugin_version );
			$this->install_db();
		}

		// If database version doesn't match, update and run SQL install
		if ( ! version_compare( get_option( 'WPBEGPAY_db_version' ), $this->plugin_version, '<' ) ) {
			return;
		}

		update_option( 'WPBEGPAY_db_version', $this->plugin_version );
		$this->install_db();
	}

	/**
	 * Queue plugin scripts for order page
	 *
	 * @return void
	 */
	public function plugin_references() {
		wp_enqueue_style( 'WPBEGPAY-orders-css', plugins_url( 'css/Bank-Mellat-Plugin.css', __FILE__ ), array(), '1.0.0' );
		wp_enqueue_script( 'WPBEGPAY-help-js', plugins_url( 'js/jquery.accordion.js', __FILE__ ), 'jquery', '1.0.0', true );
	}
	
	/**
	 * Displays a success notice with a button to transfer transactions from an older version of a Bank Mellat payment gateway plugin to a newer version.
	 * 
	 * @return void
	 */
	public function transfer_orders_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php printf( '<a style="float:left" class="button" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=bank-mellat&transferBankMellatOrders=true' ) ), esc_html__( 'Transfer transactions', 'bank-mellat' ) ); ?><?php _e( 'It seems that you have some transactions from the previous version of the Bank Mellat plugin. To transfer these transactions to the new version, please click here.', 'bank-mellat' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Uninstall plugin
	 * 
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/delete_option
	 */
	public static function uninstall() {
		// delete_option( 'aparat-feed' );
		// Remove Tables from wpdb
		// global $wpdb;
		// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aparat-feed");
		// Clear any cached data that has been removed.
		wp_cache_flush();
	}

	/**
	 * Set Plugin Info
	 * 
	 * @return void
	 */
	private function set_plugin_info() {
		$this->plugin_slug = basename( $this->plugin_file, '.php' );
		$this->plugin_url  = plugins_url( '', $this->plugin_file );

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_folder  = plugin_dir_path( $this->plugin_file );
		$plugin_info          = get_plugins( '/' . plugin_basename( $this->plugin_folder ) );
		$plugin_file_name     = basename( $this->plugin_file );
		$this->plugin_version = $plugin_info[ $plugin_file_name ]['Version'];
		$this->plugin_name    = $plugin_info[ $plugin_file_name ]['Name'];
	}

	/**
	 * The function "run" is a placeholder function in PHP with no code inside.
	 * 
	 * @return void
	 */
	private function run() {
		// nothing for now
	}

	/**
	 * The admin function includes the options.php file and registers the admin menu.
	 * 
	 * @return void
	 */
	private function admin() {
		// add_action( 'admin_menu', 'AparatFeed\Admin_Menus::register_admin_menu' );
	}

	/**
	 * Install database tables
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function install_db() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb = $GLOBALS['wpdb'];

		$orders_table_name = $wpdb->prefix . 'WPBEGPAY_orders';

		// Explicitly set the character set and collation when creating the tables
		$charset = defined( 'DB_CHARSET' ) && '' !== \DB_CHARSET ? \DB_CHARSET : 'utf8';
		$collate = defined( 'DB_COLLATE' ) && '' !== \DB_COLLATE ? \DB_COLLATE : 'utf8_general_ci';

		require_once \ABSPATH . 'wp-admin/includes/upgrade.php';

		$orders_sql = "CREATE TABLE $orders_table_name (
				order_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				order_status VARCHAR(5) NOT NULL,
				order_amount BIGINT(100) NOT NULL,
				order_date TEXT,
				order_ip TEXT,
				order_orderid VARCHAR(20) NOT NULL,
				order_referenceId BIGINT(80) DEFAULT '0',
				order_refid VARCHAR(80) DEFAULT '0',
				order_settle VARCHAR(5),
				order_name_surname VARCHAR(50),
				order_phone BIGINT(15),
				order_email TEXT,
				order_des TEXT,
				PRIMARY KEY  (order_id)
			) DEFAULT CHARACTER SET $charset COLLATE $collate;";

		// Create or Update database tables
		dbDelta( $orders_sql );
	}

	/**
	 * Transfer orders from last version.
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function transfer_orders_to_new_table() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb = $GLOBALS['wpdb'];
		
		$table_name     = $wpdb->prefix . 'WPBEGPAY_orders';
		$old_table_name = $wpdb->prefix . 'M_B_P_Orders';
		$table_exists   = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $old_table_name ) );
		if ( $table_exists !== $old_table_name ) {
			return;
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$old_order = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %s', $old_table_name ) );

		if ( ! count( $old_order ) > 0 ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'transfer_orders_notice' ) );
		
		$transfer_orders = filter_input( \INPUT_GET, 'transferBankMellatOrders', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( 'true' !== $transfer_orders ) {
			return;
		}
			
		foreach ( $old_order as $order ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert( 
				$table_name, 
				array(
					'order_status'       => $order->status,
					'order_amount'       => $order->price,
					'order_date'         => $order->date,
					'order_ip'           => $order->ip,
					'order_orderid'      => $order->orderid,
					'order_referenceId'  => $order->referenceId,
					'order_refid'        => $order->refid,
					'order_settle'       => $order->settle,
					'order_name_surname' => $order->namefamily,
					'order_phone'        => $order->phone,
					'order_des'          => $order->des,
					'order_email'        => $order->email,
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( 'DELETE FROM %s where 1', $old_table_name ) );
	}
}
