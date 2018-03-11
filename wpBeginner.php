<?php
/**
 * Plugin Name: پرداخت آنلاین بانک ملت
 * Description: افزونه پرداخت آنلاین بانک ملت | برای استفاده از افزونه کد میانبر [WPBEGPAY_SC] را در پست یا برگه مورد نظر خود وارد کنید.
 * Version: 1.3.6
 * Author: Zanyar Abdolahzadeh <wp-beginner.ir>
 * Author URI: http://www.wp-beginner.ir
 * License: تمامی حقوق این افزونه برای گروه Wp-Beginner محفوظ می باشد.
 */

define( 'WPBEGPAY_VERSION', '1.0.0' );

define( 'WPBEGPAY_PATH', plugin_dir_path( __FILE__ ) );

$wpBeginnerMellatBankGateway = new wpBeginner();

class wpBeginner{
	
	/**
	 * The DB version. Used for SQL install and upgrades.
	 *
	 * Should only be changed when needing to change SQL
	 * structure or custom capabilities.
	 *
	 * @since 1.1.0
	 * @var string
	 * @access protected
	 */
	protected $WPBEGPAY_db_version = '1.1.0';
	
	/**
	 * order_table_name
	 *
	 * @var mixed
	 * @access public
	 */
	public $orders_table_name;

	public function __construct(){
		
		// Wordpress database
		global $wpdb;
		
		// Setup global database table names
		$this->orders_table_name 	= $wpdb->prefix . 'WPBEGPAY_orders';
		
		$this->transferOrdersToNewTable();
		
		// Load plugin components
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/core/class-main.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/core/shortcode.php' );

		// Make object of class components
		$pluginComponents = new pluginComponents();

		// Adds a Settings link to the Plugins page
		add_filter( 'plugin_action_links_'. plugin_basename(__FILE__), array( &$this, 'plugin_action_links' ));

		// Check the db version and run SQL install, if needed
		add_action( 'plugins_loaded', array( &$this, 'update_db_check' ) );
		
		add_action( "admin_enqueue_scripts", array( &$this, 'plugin_refrences' ) );

	}

	/**
	 * Add Settings link to Plugins page
	 *
	 * @since 1.0
	 * @return $links array Links to add to plugin name
	 */
	public function plugin_action_links( $links ) {
			$links[] = '<a href="admin.php?page=WPBEGPAY-settings">' . __( 'تنظیمات' , 'WPBEGPAY') . '</a>';
			$links[] = '<a href="http://www.wp-beginner.ir">' . __( 'آموزش وردپرس' , 'WPBEGPAY') . '</a>';

		return $links;
	}


	/**
	 * Check database version and run SQL install, if needed
	 *
	 * @since 2.1
	 */
	public function update_db_check() {
		// Add a database version to help with upgrades and run SQL install
		if ( !get_option( 'WPBEGPAY_db_version' ) ) {
			update_option( 'WPBEGPAY_db_version', $this->WPBEGPAY_db_version );
			$this->install_db();
		}

		// If database version doesn't match, update and run SQL install
		if ( version_compare( get_option( 'WPBEGPAY_db_version' ), $this->WPBEGPAY_db_version, '<' ) ) {
			update_option( 'WPBEGPAY_db_version', $this->WPBEGPAY_db_version );
			$this->install_db();
		}
	}

	/**
	 * Install database tables
	 *
	 * @since 1.0
	 */
	static function install_db() {
		global $wpdb;

		$orders_table_name      = $wpdb->prefix . 'WPBEGPAY_orders';

		// Explicitly set the character set and collation when creating the tables
		$charset = ( defined( 'DB_CHARSET' && '' !== DB_CHARSET ) ) ? DB_CHARSET : 'utf8';
		$collate = ( defined( 'DB_COLLATE' && '' !== DB_COLLATE ) ) ? DB_COLLATE : 'utf8_general_ci';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

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
	 * Queue plugin scripts for order page
	 *
	 * @since 1.0
	 */
	public function plugin_refrences() {
	
		wp_enqueue_style( 'WPBEGPAY-orders-css', plugins_url("css/Bank-Mellat-Plugin.css", __FILE__ ), array(), '1.0.0' );
		wp_enqueue_script( 'WPBEGPAY-help-js', plugins_url("js/jquery.accordion.js", __FILE__ ), "jquery", '1.0.0' );

		
	}
	
	/**
	 * Transfer orders from last version.
	 *
	 * @since 1.3
	 */
	private function transferOrdersToNewTable() {
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
		$oldTableName = $wpdb->prefix . 'M_B_P_Orders';
		
		if($wpdb->get_var("SHOW TABLES LIKE '$oldTableName'") == $oldTableName) {
			
			$oldOrder = $wpdb->get_results( "SELECT * FROM $oldTableName" );
		
			if(count($oldOrder) > 0){
				add_action( 'admin_notices', array(&$this, 'transferOrdersNotice') );
				
				if(isset($_GET['transferBankMellatOrders'])){
					
					if($_GET['transferBankMellatOrders'] == 'true'){
						
						foreach($oldOrder as $order){
							
									$wpdb->insert( 
										$table_name, 
										array(
											'order_status' => $order->status,
											'order_amount' => $order->price,
											'order_date' => $order->date,
											'order_ip' => $order->ip,
											'order_orderid' => $order->orderid,
											'order_referenceId' => $order->referenceId,
											'order_refid' => $order->refid,
											'order_settle' => $order->settle,
											'order_name_surname' => $order->namefamily,
											'order_phone' => $order->phone,
											'order_des' => $order->des,
											'order_email' => $order->email
										));
						}
						
						$wpdb->query("DELETE FROM $oldTableName where 1");		

					}
				}
			}
		}
	}
	
	public function transferOrdersNotice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo sprintf('<a style="float:left" class="button" href="%s">%s</a>', admin_url( 'admin.php?page=bank-mellat&transferBankMellatOrders=true', 'http' ), 'انتقال تراکنش ها'); ?><?php _e( 'بنظر می رسد که شما از نسخه قبلی افزونه درگاه بانک ملت تراکنش هایی داشته اید، برای انتقال تراکنش ها به نسخه جدید کلیک نمایید.', 'sample-text-domain' ); ?></p>
    </div>
    <?php
	}

}
?>