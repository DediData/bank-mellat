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
		$this->orders_table_name = $wpdb->prefix . 'bank_mellat_orders';
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
		new BankMellat\Bank_Mellat_Settings();
		
		// Load help's class
		if ( ! class_exists( 'BankMellat\Bank_Mellat_Help' ) ) {
			new BankMellat\Bank_Mellat_Help();
		}
		
		$this->export_orders();
		
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
			$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=bank-mellat-settings' ) ), 'تنظیمات' );
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
		if ( ! get_option( 'bank_mellat_db_version' ) ) {
			update_option( 'bank_mellat_db_version', $this->plugin_version );
			$this->install_db();
		}

		// If database version doesn't match, update and run SQL install
		if ( ! version_compare( get_option( 'bank_mellat_db_version' ), $this->plugin_version, '<' ) ) {
			return;
		}

		update_option( 'bank_mellat_db_version', $this->plugin_version );
		$this->install_db();
	}

	/**
	 * Queue plugin scripts for order page
	 *
	 * @return void
	 */
	public function plugin_references() {
		wp_enqueue_style( 'bank-mellat-orders-css', BANK_MELLAT()->plugin_url . '/assets/css/style.css', array(), BANK_MELLAT()->plugin_version );
		wp_enqueue_script( 'bank-mellat-help-js', BANK_MELLAT()->plugin_url . '/assets/js/jquery.accordion.js', 'jquery', BANK_MELLAT()->plugin_version, true );
	}
	
	/**
	 * Displays a success notice with a button to transfer transactions from an older version of a Bank Mellat payment gateway plugin to a newer version.
	 * 
	 * @return void
	 */
	public function transfer_orders_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php printf( '<a style="float:left" class="button" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=bank-mellat&transferBankMellatOrders=true' ) ), 'انتقال تراکنش ها' ); ?><?php echo 'بنظر می رسد که شما تراکنش هایی را، از نسخه قبلی افزونه درگاه بانک ملت، داشته اید، برای انتقال تراکنش ها به نسخه جدید کلیک نمایید.'; ?></p>
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

		$orders_table_name = $wpdb->prefix . 'bank_mellat_orders';

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
		
		$table_name     = $wpdb->prefix . 'bank_mellat_orders';
		$old_table_name = $wpdb->prefix . 'WPBEGPAY_orders';
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

	/**
	 * Export Orders
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function export_orders() {
		$get_export_orders = filter_input( \INPUT_GET, 'ExportOrders', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( null === $get_export_orders || ! is_admin() ) {
			return;
		}
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb = $GLOBALS['wpdb'];
			
		$table_name = $wpdb->prefix . 'bank_mellat_orders';
		$get_order  = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %s ORDER BY order_id', $table_name ) );
	
		if ( ! $get_order ) {
			return;
		}
		header( 'Content-Type: text/html; charset=utf-8', true, 200 );
		header( 'Content-Disposition: attachment; filename=Orders.html' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="UTF-8">
				<meta name="author" content="plugin bank-mellat">
				<style>
					html{
						direction:rtl;
						text-align:right;
						-ms-text-size-adjust:100%;
						-webkit-text-size-adjust:100%;
					}
					body{
						margin:0;
					}
					article,
					aside,
					details,
					figcaption,
					figure,
					footer,
					header,
					hgroup,
					main,
					menu,
					nav,
					section,
					summary{
						display:block;
					}
					audio,
					canvas,
					progress,
					video{
						display:inline-block;
						vertical-align:baseline;
					}
					audio:not([controls]){
						display:none;
						height:0;
					}
					[hidden],
					template{
						display:none;
					}
					a {
						background-color:transparent;
					}
					a:active,
					a:hover{
						outline:0;
					}
					abbr[title]{
						border-bottom:1px dotted;
					}
					b,
					strong{
						font-weight:700;
					}
					dfn{
						font-style:italic;
					}
					h1{
						font-size:2em;
						margin:.67em 0;
					}
					mark{
						background:#ff0;
						color:#000;
					}
					small{
						font-size:80%;
					}
					sub,
					sup{
						font-size:75%;
						line-height:0;
						position:relative;
						vertical-align:baseline
					}
					sup{
						top:-.5em;
					}
					sub{
						bottom:-.25em;
					}
					img{
						border:0;
					}
					svg:not(:root){
						overflow:hidden;
					}
					figure{
						margin:1em 40px;
					}
					hr{
						-moz-box-sizing:content-box;
						-webkit-box-sizing:content-box;
						box-sizing:content-box;
						height:0;
					}
					pre{
						overflow:auto;
					}
					code,
					kbd,
					pre,
					samp{
						font-family:monospace,monospace;
						font-size:1em
					}
					button,
					input,
					optgroup,
					select,
					textarea{
						color:inherit;
						font:inherit;
						margin:0;
					}
					button{
						overflow:visible;
					}
					button,
					select{
						text-transform:none;
					}
					button,
					html input[type="button"],
					input[type="reset"],
					input[type="submit"]{
						-webkit-appearance:button;
						cursor:pointer;
					}
					button[disabled],
					html input[disabled]{
						cursor:default;
					}
					button::-moz-focus-inner,
					input::-moz-focus-inner{
						border:0;
						padding:0;
					}
					input{
						line-height:normal;
					}
					input[type="checkbox"],
					input[type="radio"]{
						-webkit-box-sizing:border-box;
						-moz-box-sizing:border-box;
						box-sizing:border-box;
						padding:0;
					}
					input[type="number"]::-webkit-inner-spin-button,
					input[type="number"]::-webkit-outer-spin-button{
						height:auto;
					}
					input[type="search"]{
						-webkit-appearance:textfield;
						-moz-box-sizing:content-box;
						-webkit-box-sizing:content-box;
						box-sizing:content-box;
					}
					input[type="search"]::-webkit-search-cancel-button,
					input[type="search"]::-webkit-search-decoration{
						-webkit-appearance:none;
					}
					fieldset{
						border:1px solid silver;
						margin:0 2px;
						padding:.35em .625em .75em;
					}
					legend{
						border:0;
						padding:0;
					}
					textarea{
						overflow:auto;
					}
					optgroup{
						font-weight:700;
					}
					table{
						border-collapse:collapse;
						border-spacing:0;
					}
					td,th{
						padding:0;
					}
					.responstable{
						margin:1em 0;
						width:100%;
						overflow:hidden;
						background:#FFF;
						color:#024457;
						border-radius:10px;
						border:1px solid #167F92;
					}
					.responstable tr{
						border:1px solid #D9E4E6;
					}
					.responstable tr:nth-child(odd){
						background-color:#EAF3F3;
					}
					.responstable th{
						display:none;
						border:1px solid #FFF;
						background-color:#167F92;
						color:#FFF;
						padding:1em;
					}
					.responstable th:first-child{
						display:table-cell;
						text-align:center;
					}
					.responstable th:nth-child(2){
						display:table-cell;
					}
					.responstable th:nth-child(2) span{
						display:none;
					}
					.responstable th:nth-child(2):after{
						content:attr(data-th);
					}
					@media (min-width: 480px){
						.responstable th:nth-child(2) span{
							display:block;
						}
						.responstable th:nth-child(2):after{
							display:none;
						}
					}
					.responstable td{
						display:block;
						word-wrap:break-word;
						max-width:7em;
					}
					.responstable td:first-child{
						display:table-cell;
						text-align:center;
						border-right:1px solid #D9E4E6;
					}
					@media (min-width: 480px){
						.responstable td{
							border:1px solid #D9E4E6;
						}
					}
					.responstable th,.responstable td{
						text-align:right;
						margin:.5em 1em;
					}
					@media (min-width: 480px){
						.responstable th,
						.responstable td{
							display:table-cell;
							padding:1em;
						}
					}
					body{
						padding:0 2em;
						font-family:Arial,sans-serif;
						color:#024457;
						background:#f2f2f2
					}
					h1{
						font-family:Verdana;
						font-weight:400;
						color:#024457
					}
					h1 span{
						color:#167F92
					}
					.right{
						float:right;
					}
					.left{
						float:left;
					}
				</style>
			</head>
			<body>
				<h1>گزارشات تراکنش های انجام شده</span></h1>
				<table class="responstable">
					<tr>
						<th>#</th>
						<th>نام و نام خانوادگي</th>
						<th>آدرس ايميل</th>
						<th>شماره تلفن</th>
						<th>توضيحات</th>
						<th>وضعيت پرداخت</th>
						<th>ستل</th>
						<th>تاريخ</th>
						<th>آي پي</th>
						<th>مبلغ(ريال)</th>
						<th>رسيد ديجيتالي سفارش</th>
						<th>شماره سفارش</th>
						<th>شماره تراکنش</th>
					</tr>
					<?php
					foreach ( $get_order as $order ) {
						?>
					<tr>
						<td><?php echo esc_html( $order->order_id ); ?></td>
						<td><?php echo esc_html( $order->order_name_surname ); ?></td>
						<td><?php echo esc_html( $order->order_email ); ?></td>
						<td><?php echo esc_html( $order->order_phone ); ?></td>
						<td><?php echo esc_html( $order->order_des ); ?></td>
						<td><?php echo esc_html( $order->order_status ); ?></td>
						<td><?php echo esc_html( $order->order_settle ); ?></td>
						<td><?php echo esc_html( $order->order_date ); ?></td>
						<td><?php echo esc_html( $order->order_ip ); ?></td>
						<td><?php echo esc_html( $order->order_amount ); ?></td>
						<td><?php echo esc_html( $order->order_referenceId ); ?></td>
						<td><?php echo esc_html( $order->order_orderid ); ?></td>
						<td><?php echo esc_html( $order->order_refid ); ?></td>
					</tr>
						<?php
					}
					?>
				</table>
				<div>تاریخ گزارش گیری: <?php echo esc_html( gmdate( 'Y-m-d' ) ); ?></div>
			</body>
		</html>
		<?php
	}
}
