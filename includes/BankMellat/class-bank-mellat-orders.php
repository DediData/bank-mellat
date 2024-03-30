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
		$this->plugin_url    = BANK_MELLAT()->get( 'plugin_url' );
		$this->plugin_folder = BANK_MELLAT()->get( 'plugin_folder' );
		// WordPress database
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb = $GLOBALS['wpdb'];
		// Setup global database table names
		$this->orders_table_name = $wpdb->prefix . 'bank_mellat_orders';
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}

	/**
	 * Admin Menu
	 * 
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page( 'دروازه پرداخت بانک ملت', 'درگاه بانک ملت', 'manage_options', 'bank-mellat', array( $this, 'orders' ), $this->plugin_url . '/assets/images/bank-mellat.png' );
		add_submenu_page( 'bank-mellat', 'گزارش‌ها', 'پرداخت‌ها', 'manage_options', 'bank-mellat', array( $this, 'orders' ) );
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
			تراکنش ها
			<?php
			// If searched, output the query
			$get_message = filter_input( \INPUT_GET, 'message', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_id      = filter_input( \INPUT_GET, 'id', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( null !== $get_message && 'del' === $get_message ) {
				// translators: replace %s with id
				echo '<div id="message" class="updated notice is-dismissible below-h2"><p>' . sprintf( 'فرم "%s" با موفقیت حذف شد.', esc_html( $get_id ) ) . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">بستن این اعلان.</span></button></div>';
			}
			?>
			</h2>
			<div class="stat-container">
				<div class="stat-holder">
					<div class="stat">							
					<span id="total"><?php $this->orders_widget( 'successful_pay_money' ); ?></span>
					مجموع پرداخت های موفق
					</div>
				</div>
				<div class="stat-holder">
					<div class="stat">							
					<span id="total"><?php $this->orders_widget( 'successful_pay' ); ?></span>
					پرداخت های موفق
					</div>
				</div>
				<div class="stat-holder">
					<div class="stat">							
					<span id="total"><?php $this->orders_widget( 'unsuccessful_pay' ); ?></span>
					پرداخت های ناموفق
					</div>
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
							if ( ! class_exists( 'WP_List_Table' ) ) {
								require_once \ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
							}
							$table = new Bank_Mellat_Orders_List();
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
							$table_name = $wpdb->prefix . 'bank_mellat_orders';
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
									$settle = 'yes' === $order->order_settle ? 'انجام شده است' : 'انجام نشده است';
									$status = 'yes' === $order->order_status ? 'انجام شده است' : 'انجام نشده است';
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
													<td>نام و نام خانوادگی</td>
													<td>' . esc_html( $order->order_name_surname ) . '</td>
												</tr>
												<tr>
													<td>آدرس ایمیل</td>
													<td>' . esc_html( $order->order_email ) . '</td>
												</tr>
												<tr>
													<td>شماره تلفن</td>
													<td>' . esc_html( $order->order_phone ) . '</td>
												</tr>
												<tr>
													<td>توضیح</td>
													<td>' . esc_html( $order->order_des ) . '</td>
												</tr>
												<tr>
													<td>وضعیت پرداخت</td>
													<td>' . esc_html( $status ) . '</td>
												</tr>
												<tr>
													<td>ستل</td>
													<td>' . esc_html( $settle ) . '</td>
												</tr>
												<tr>
													<td>تاریخ</td>
													<td>' . esc_html( $order->order_date ) . '</td>
												</tr>
												<tr>
													<td>آی پی</td>
													<td>' . esc_html( $order->order_ip ) . '</td>
												</tr>
												<tr>
													<td>مبلغ سفارش</td>
													<td>' . esc_html( $order->order_amount ) . '</td>
												</tr>
												<tr>
													<td>شناسه مرجع سفارش</td>
													<td>' . esc_html( $order->order_referenceId ) . '</td>
												</tr>
												<tr>
													<td>شناسه سفارش</td>
													<td>' . esc_html( $order->order_orderid ) . '</td>
												</tr>
												<tr>
													<td>شماره تراکنش</td>
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
						printf( '<a class="button" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=bank-mellat', 'admin' ) ), 'بازگشت' );
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
				$query = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(order_amount) AS priceCount FROM %s WHERE order_status = 'yes'", $orders_table ) );
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
		if ( null !== $query ) {
			echo number_format( $query );
		}
	}
}
