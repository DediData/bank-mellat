<?php
/**
 * Bank Mellat Orders Main Class
 * 
 * @package Bank_Mellat
 */

declare(strict_types=1);

namespace BankMellat;

/**
 * Class Bank_Mellat_Orders
 */
final class Bank_Mellat_Orders extends \DediData\Singleton {

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
	 * Orders Table Name
	 *
	 * @var string
	 */
	protected $orders_table_name;

	/**
	 * Constructor
	 * 
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function __construct() {
		$this->plugin_url    = BANK_MELLAT()->plugin_url;
		$this->plugin_folder = BANK_MELLAT()->plugin_folder;
		// WordPress database
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb = $GLOBALS['wpdb'];
		// Setup global database table names
		$this->orders_table_name = $wpdb->prefix . 'WPBEGPAY_orders';
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		require_once $this->plugin_folder . '/inc/class-orders-list.php';
	}

	/**
	 * Admin Menu
	 * 
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page( __( 'Bank Mellat Payment Gateway', 'bank-mellat' ), __( 'Bank Mellat Gateway', 'bank-mellat' ), 'manage_options', 'bank-mellat', array( $this, 'orders' ), $this->plugin_url . 'assets/images/bank-mellat.png' );
		add_submenu_page( 'bank-mellat', __( 'Reports', 'bank-mellat' ), __( 'Payments', 'bank-mellat' ), 'manage_options', 'bank-mellat', array( $this, 'orders' ) );
	}
	
	/**
	 * Orders page
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function orders() {
		?>
		<div class="wrap">
			<h2>
			<?php _e( 'Transactions', 'bank-mellat' ); ?>
			<?php
			// If searched, output the query
			$get_message = filter_input( \INPUT_GET, 'message', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_id      = filter_input( \INPUT_GET, 'id', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( null !== $get_message && 'del' === $get_message ) {
				// translators: replace %s with id
				echo '<div id="message" class="updated notice is-dismissible below-h2"><p>' . sprintf( esc_html__( 'The form "%s" successfully deleted.', 'bank-mellat' ), esc_html( $get_id ) ) . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Close this notification.', 'bank-mellat' ) . '</span></button></div>';
			}
			?>
			</h2>
			<div class="stat-container">
				<!-- /stat-holder -->
				<div class="stat-holder">
					<div class="stat">							
					<span id="total"><?php $this->orders_widget( 'successful_pay_money' ); ?></span>
					<?php _e( 'The total successful payments', 'bank-mellat' ); ?>
					</div>
				<!-- /stat -->
				</div>
				<!-- /stat-holder -->
				<div class="stat-holder">
					<div class="stat">							
					<span id="total"><?php $this->orders_widget( 'successful_pay' ); ?></span>
					<?php _e( 'Successful payments', 'bank-mellat' ); ?>
					</div>
				<!-- /stat -->
				</div>
				<!-- /stat-holder -->
				<div class="stat-holder">
					<div class="stat">							
					<span id="total"><?php $this->orders_widget( 'unsuccessful_pay' ); ?></span>
					<?php _e( 'Unsuccessful payments', 'bank-mellat' ); ?>
					</div>
				<!-- /stat -->
				</div>
			</div>
			<div id="post-stuff">
				<div id="post-body" class="meta-box-holder columns-2">
					<div id="post-body-content">
					<?php
					$get_order_id = filter_input( \INPUT_GET, 'orderId', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					$get_page     = filter_input( \INPUT_GET, 'page', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					if ( null === $get_order_id ) {
						?>
						<div class="meta-box-sortables ui-sortable">
							<form id="persons-table" method="get">
							<input type="hidden" name="page" value="<?php echo esc_attr( $get_page ); ?>"/>
							<?php
								$table = new Orders_list();
								$table->prepare_items();
								$table->display();
							?>
							</form>
						</div>
							<?php
					}
					if ( null !== $get_order_id ) {
						?>
						<div style="display: block;" id="order-<?php esc_attr( $get_order_id ); ?>" class="postbox">
							<h2 class="handle ui-sortable-handle"><span><?php /* Translators: replace %s with order id */  printf( esc_html__( 'Transaction details %s', 'bank-mellat' ), esc_html( $get_order_id ) ); ?></span></h2>
							<div class="inside">
							<?php
							// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
							$wpdb       = $GLOBALS['wpdb'];
							$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
							$cache_key  = 'bank_mellat_get_order_' . $get_order_id;
							$get_order  = wp_cache_get( $cache_key, 'bank_mellat_orders' );
							// If the data is not cached, fetch it from the database and cache it
							if ( false === $get_order ) {
								$get_order = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %s WHERE order_id = %d', $table_name, $get_order_id ) );
								// Cache the result for future use
								wp_cache_set( $cache_key, $get_order, 'bank_mellat_orders' );
							}
							if ( $get_order ) {
								foreach ( $get_order as $order ) {
									$settle = 'yes' === $order->order_settle ? esc_html__( 'It has been done', 'bank-mellat' ) : esc_html__( 'It has not been done', 'bank-mellat' );
									$status = 'yes' === $order->order_status ? esc_html__( 'It has been done', 'bank-mellat' ) : esc_html__( 'It has not been done', 'bank-mellat' );
									echo '
										<style>
										table, th, td {
											border: 1px solid rgb(206, 199, 199);
										} 
										</style>
										<table style="width: 100%; border: 1px solid rgb(206, 199, 199);" dir="rtl" border="1" cellpadding="10">
											<tbody>
												<tr>
													<td>#</td>
													<td>' . esc_html( $order->order_id ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'First and last name', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_name_surname ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Email Address', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_email ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Phone Number', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_phone ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Description', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_des ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Payment Status', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $status ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Settle', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $settle ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Date', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_date ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'IP', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_ip ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Order Amount', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_amount ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Order Reference ID', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_referenceId ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Order ID', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_orderid ) . '</td>
												</tr>
												<tr>
													<td>' . esc_html__( 'Transaction Number', 'bank-mellat' ) . '</td>
													<td>' . esc_html( $order->order_refid ) . '</td>
												</tr>
											</tbody>
										</table>
									';
								}//end foreach
							}//end if
							?>
							</div>
						</div>
						<?php
						printf( '<a class="button" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=bank-mellat', 'admin' ) ), esc_html__( 'Return', 'bank-mellat' ) );
					}//end if
					?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Forms page
	 *
	 * @param string $arg Tag.
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function orders_widget( string $arg ) {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb         = $GLOBALS['wpdb'];
		$orders_table = $this->orders_table_name;
		switch ( $arg ) {
			case 'successful_pay_money':
				$cache_key = 'sum_order_amount_yes';
				$query     = wp_cache_get( $cache_key, 'bank_mellat_orders' );
				if ( false === $query ) {
					$query = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(order_amount) AS priceCount FROM %s WHERE order_status = 'yes'", $orders_table ) );
					// Cache the result for future use
					wp_cache_set( $cache_key, $query, 'bank_mellat_orders' );
				}
				break;
			case 'successful_pay':
				$query = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %s WHERE order_status = 'yes'", $orders_table ) );
				break;
			case 'unsuccessful_pay':
				$query = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %s WHERE order_status = 'no'", $orders_table ) );
				break;
			default:
				// nothing for default
				break;
		}
		echo number_format( $query );
	}
}
