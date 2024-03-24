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
										$wpdb       = $GLOBALS['wpdb'];
										$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
										$query      = $wpdb->prepare( "SELECT * FROM $table_name WHERE order_id = %d", $get_order_id );
										$get_order  = $wpdb->get_results( $query );
										if ( $get_order ) {
											
											foreach ( $get_order as $order ) {
												if ( $order->order_settle == 'yes' ) {
													$settel = "انجام شده است";
												} else {
													$settel = "انجام نشده است";
												}
												if ( $order->order_status == 'yes' ) {
													$status = "انجام شده است";
												} else {
													$status = "انجام نشده است";
												}
												echo ( '
														<style>
															table, th, td {
																border: 1px solid rgb(206, 199, 199);
															} 
														</style>
														<table style="width: 100%; border: 1px solid rgb(206, 199, 199);" dir="rtl" border="1" cellpadding="10">
															<tbody>
																<tr>
																<td>#</td>
																<td>' . $order->order_id . '</td>
																</tr>
																<tr>
																<td>نام و نام خانوادگي</td>
																<td>' . $order->order_name_surname . '</td>
																</tr>
																<tr>
																<td>آدرس ايميل</td>
																<td>' . $order->order_email . '</td>
																</tr>
																<tr>
																<td>شماره تلفن</td>
																<td>' . $order->order_phone . '</td>
																</tr>
																<tr>
																<td>توضيحات</td>
																<td>' . $order->order_des . '</td>
																</tr>
																<tr>
																<td>وضعيت پرداخت</td>
																<td>' . $status . '</td>
																</tr>
																<tr>
																<td>ستل</td>
																<td>' . $settel . '</td>
																</tr>
																<tr>
																<td>تاريخ</td>
																<td>' . $order->order_date . '</td>
																</tr>
																<tr>
																<td>آي پي</td>
																<td>' . $order->order_ip . '</td>
																</tr>
																<tr>
																<td>مبلغ(ريال)</td>
																<td>' . $order->order_amount . '</td>
																</tr>
																<tr>
																<td>رسيد ديجيتالي سفارش</td>
																<td>' . $order->order_referenceId . '</td>
																</tr>
																<tr>
																<td>شماره سفارش</td>
																<td>' . $order->order_orderid . '</td>
																</tr>
																<tr>
																<td>شماره تراکنش</td>
																<td>' . $order->order_refid . '</td>
																</tr>
															</tbody>
														</table>' );
											}
										}
										?>
									</div>
								</div>	
								
								<?php printf( '<a class="button" href="%s">%s</a>', admin_url( 'admin.php?page=bank-mellat', 'http' ), 'بازگشت' ); ?>
								
							<?php } ?>
						</div>
				</div>
				
				<div class="sidebar-container"><a href="http://www.wp-beginner.ir/product/%d8%a7%d9%81%d8%b2%d9%88%d9%86%d9%87-%d8%af%d8%b1%da%af%d8%a7%d9%87-%d9%be%d8%b1%d8%af%d8%a7%d8%ae%d8%aa-%d8%a8%d8%a7%d9%86%da%a9-%d9%87%d8%a7%db%8c-%d8%a7%db%8c%d8%b1%d8%a7%d9%86%db%8c/" target="_blank"><img src="<?php echo plugins_url( 'bank-mellat/images/banner.jpg' ); ?>" /></a></div>
			
			</div>

		</div>

		<?php
	}

	/**
	 * Forms page
	 *
	 * @return void
	 */
	private function orders_widget( $arg ) {
		 
		global $wpdb;
		$orders_table = $this->orders_table_name;
		switch ( $arg ) {
			case 'successful_pay_money':
				$q = $wpdb->get_var( "select SUM(order_amount) AS priceCount from $orders_table where order_status='yes'" );
			break;

			case 'successful_pay':
				$q = $wpdb->get_var( "select count(*) from $orders_table where order_status='yes'" );
			break;

			case 'unsuccessful_pay':
				$q = $wpdb->get_var( "select count(*) from $orders_table where order_status='no'" );
			break;
		}
		echo number_format( $q );
	}
}
