<?php
/**
 * Bank Mellat Shortcode Main Class
 * 
 * @package Bank_Mellat
 */

declare(strict_types=1);

namespace BankMellat;

/**
 * Class Bank_Mellat_Shortcode
 */
final class Bank_Mellat_Shortcode extends \DediData\Singleton {

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
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_url    = BANK_MELLAT()->plugin_url;
		$this->plugin_folder = BANK_MELLAT()->plugin_folder;
		if ( ! class_exists( 'nusoap_base' ) ) {
			require_once $this->plugin_folder . '/includes/Composer/autoload.php';
		}
		add_shortcode( 'WPBEGPAY_SC', array( $this, 'shortcode' ) );
	}

	/**
	 * Handles payment processing and order management
	 * 
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function shortcode() {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$settings = get_option( 'WPBEGPAY_settings_fields_arrays' );

		echo "
			<style>
			.bank-mellat-success,
			.bank-mellat-warning,
			.bank-mellat-connecting {
				direction: rtl;
				border: 1px solid;
				border-radius: 5px;
				padding: 15px 50px 15px 50px;
				background-repeat: no-repeat;
				background-position: calc(100% - 10px)  center;
			}
			.bank-mellat-warning {
				color: #9F6000;
				background-color: #FEEFB3;
				background-image: url('" . esc_url( $this->plugin_url . 'assets/images/warning.png' ) . "');
			}
			.bank-mellat-success {
				color: #4F8A10;
				background-color: #DFF2BF;
				background-image: url('" . esc_url( $this->plugin_url . 'assets/images/success.png' ) . "');
			}
			.bank-mellat-connecting {
				color: #4F8A10;
				background-color: #DFF2BF;
				background-image: url('" . esc_url( $this->plugin_url . 'assets/images/loader.gif' ) . "');
			}
			</style>
		";

		$post_res_code = filter_input( \INPUT_POST, 'ResCode', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! isset( $post_res_code ) ) {
			$this->process_callback( $settings );
			return;
		}
		$this->display_form( $settings );
	}

	/**
	 * Dynamically generate and return the current URL of the page being accessed.
	 * 
	 * @return string
	 */
	private function get_current_url() {
		$server_https = filter_input( \INPUT_SERVER, 'HTTPS', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_url  = 'on' === $server_https ? 'https://' : 'http://';
		$server_name  = filter_input( \INPUT_SERVER, 'SERVER_NAME', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_url .= $server_name;
		$server_port  = filter_input( \INPUT_SERVER, 'SERVER_PORT', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( '80' !== $server_port && '443' !== $server_port ) {
			$current_url .= ':' . $server_port;
		}
		$current_url .= filter_input( \INPUT_SERVER, 'REQUEST_URI', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		return $current_url;
	}

	/**
	 * Display Form.
	 * 
	 * @param array<mixed> $settings Settings Array.
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function display_form( $settings ) {
		$default_themes = array( 'formA.php', 'formB.php', 'formC.php' );

		if ( in_array( $settings['form'], $default_themes, true ) ) {
			$settings['form'] = str_replace( '.html', '.php', $settings['form'] );
			include_once plugin_dir_path( __FILE__ ) . '/../forms/' . $settings['form'];
		} elseif ( ! in_array( $settings['form'], $default_themes, true ) ) {
			include_once plugin_dir_path( __FILE__ ) . '/../forms/formA.php';
		}
		
		$server_req_method = filter_input( \INPUT_SERVER, 'REQUEST_METHOD', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$bank_mellat_price = filter_input( \INPUT_POST, 'bank_mellat_price', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( 'POST' !== $server_req_method || null === $bank_mellat_price ) {
			return;
		}

		$order_id   = time() . wp_rand( 100000, 999999 );
		$local_date = gmdate( 'Ymd' );
		$local_time = gmdate( 'His' );
		$client     = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' );
		$namespace  = 'http://interfaces.core.sw.bps.com/';
		
		$client_error = $client->getError();
		if ( ! $client || $client_error ) {
			echo '<div class="error"><p>' . esc_html( $client_error ) . '</p></div>';
			return;
		}

		$parameters = array(
			'terminalId'     => $settings['MellatG_TerminalNumber'],
			'userName'       => $settings['MellatG_TerminalUser'],
			'userPassword'   => $settings['MellatG_TerminalPass'],
			'orderId'        => $order_id,
			'amount'         => $bank_mellat_price,
			'localDate'      => $local_date,
			'localTime'      => $local_time,
			'additionalData' => '',
			'callBackUrl'    => $this->get_current_url(),
			'payerId'        => '0',
		);
				
		$result        = $client->call( 'bpPayRequest', $parameters, $namespace );
		$result_string = $result;        
		$result_array  = explode( ',', $result_string );
		$result_code   = $result_array[0];
		
		if ( '0' === $result_code ) {
			// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
			$wpdb       = $GLOBALS['wpdb'];
			$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
			
			$server_remote_addr = filter_input( \INPUT_SERVER, 'REMOTE_ADDR', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			
			$post_name_family = filter_input( \INPUT_POST, 'bank_mellat_name_family', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$post_phone       = filter_input( \INPUT_POST, 'bank_mellat_phone', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$post_description = filter_input( \INPUT_POST, 'bank_mellat_description', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$post_email       = filter_input( \INPUT_POST, 'bank_mellat_email', \FILTER_SANITIZE_EMAIL );
			$wpdb->insert( 
				$table_name, 
				array(
					'order_status'       => 'no',
					'order_amount'       => $bank_mellat_price,
					'order_date'         => gmdate( 'H:i:s Y/m/d' ),
					'order_ip'           => $server_remote_addr,
					'order_orderid'      => $order_id,
					'order_referenceId'  => '',
					'order_refid'        => $result_array[1],
					'order_settle'       => 'no',
					'order_name_surname' => $post_name_family,
					'order_phone'        => $post_phone,
					'order_des'          => $post_description,
					'order_email'        => $post_email,
				)
			);
			?>
			<style>.bank-mellat-form,.basic-grey,.elegant-aero{display:none;}</style>

			<script language='javascript' type='text/javascript'>
				window.onload = function(){document.forms['Order_Form'].submit()}
			</script>

			<div class="bank-mellat-connecting">

			<?php echo esc_html( $settings['connecting_msg'] ); ?>
			
			<form id="Order_Form" name="Order_Form" style="position:absolute;bottom:82px;left:35px;" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
			
				<input type="hidden" name="RefId" value="<?php echo esc_attr( $result_array[1] ); ?>" />
				<input name="submit button" type="submit" style="width:100%;" value="ورود به درگاه پرداخت" id="button" />
			</form>
			</div>
			<?php
		}//end if
		if ( '0' !== $result_code ) {
			echo wp_kses(
				sprintf(
					"<script>alert('%s\\n%s:%s');location.reload();</script>",
					'امکان اتصال به درگاه پرداخت وجود ندارد!',
					'کد خطا',
					$result_code
				),
				'script'
			);
		}
		
		if ( $client->fault ) {
			return new WP_Error(
				'bank-mellat-client-fault',
				__( 'An error occurred while doing something with bank mellat plugin.', 'bank-mellat' ),
				$result
			);
		}
		$error = $client->getError();
		if ( $error ) {
			return new WP_Error(
				'bank-mellat-client-error',
				__( 'An error occurred while doing something with bank mellat plugin.', 'bank-mellat' ),
				$error
			);
		}
	}

	/**
	 * Process Callback.
	 * 
	 * @param array<mixed> $settings Settings Array.
	 * @return mixed
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function process_callback( $settings ) {
		$client    = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' );
		$namespace = 'http://interfaces.core.sw.bps.com/';
		
		$error = $client->getError();
		
		if ( $error ) {
			return new WP_Error(
				'bank-mellat-client-get-error',
				__( 'An error occurred while doing something with bank mellat plugin.', 'bank-mellat' ),
				$settings['invalid_msg']
			);
		}
		
		$post_res_code = filter_input( \INPUT_POST, 'bank_mellat_email', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		// Unused
		// $post_ref_id        = filter_input( \INPUT_POST, 'bank_mellat_email', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$post_sale_order_id = filter_input( \INPUT_POST, 'bank_mellat_email', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$post_sale_ref_id   = filter_input( \INPUT_POST, 'bank_mellat_email', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$result_code   = $post_res_code;
		$terminal_id   = $settings['MellatG_TerminalNumber'];
		$user_name     = $settings['MellatG_TerminalUser'];
		$user_password = $settings['MellatG_TerminalPass'];
		// Unused
		// $ref_id               = $post_ref_id;
		$order_id             = $post_sale_order_id;
		$verify_sale_order_id = $post_sale_order_id;
		$verify_sale_ref_id   = $post_sale_ref_id;
		
		if ( 0 !== $result_code ) {
			echo '<div class="bank-mellat-warning">' . esc_html( $settings['cancel_msg'] ) . '</div>';
			return;
		}
			
		if ( $client->fault ) {
			return new WP_Error(
				'bank-mellat-client-fault',
				__( 'An error occurred while doing something with bank mellat plugin.', 'bank-mellat' ),
				$settings['error_msg']
			);
		}
		
		// Unused
		// $ref_id = $post_ref_id;
		
		$parameters = array(
			'terminalId'      => $terminal_id,
			'userName'        => $user_name,
			'userPassword'    => $user_password,
			// 'saleOrderId'     => $order_id,
			'saleOrderId'     => $verify_sale_order_id,
			'saleReferenceId' => $verify_sale_ref_id,
		);
		
		// $result_pay = $client->call( 'bpVerifyRequest', $parameters, $namespace );
		$client->call( 'bpVerifyRequest', $parameters, $namespace );
		$check = $client->call( 'bpInquiryRequest', $parameters, $namespace );
		if ( '0' !== $check ) {
			return;
		}
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$wpdb       = $GLOBALS['wpdb'];
		$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
		
		$wpdb->update( 
			$table_name, 
			array( 'order_status' => 'yes' ), 
			array( 'order_orderid' => $order_id ), 
			array( '%s' ), 
			array( '%s' ) 
		);
						
		// $settle = $client->call( 'bpSettleRequest', $parameters, $namespace );
		$client->call( 'bpSettleRequest', $parameters, $namespace );
		
		$wpdb->update( 
			$table_name, 
			array( 'order_settle' => 'yes' ), 
			array( 'order_orderid' => $order_id ), 
			array( '%s' ), 
			array( '%s' ) 
		);
		
		$wpdb->update( 
			$table_name, 
			array( 'order_referenceId' => $verify_sale_ref_id ), 
			array( 'order_orderid' => $order_id ), 
			array( '%s' ), 
			array( '%s' )
		);

		$get_order = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM %s WHERE order_orderid = %d',
				$table_name,
				$order_id
			)
		);

		foreach ( $get_order as $order ) {
			
			echo '
				<div class="bank-mellat-success">' . esc_html( $settings['successful_msg'] ) . '</div>'
				. 'شماره سفارش: ' . esc_html( $order->order_id ) . '<br />'
				. 'نام و نام خانوادگی: ' . esc_html( $order->order_name_surname ) . '<br />'
				. 'آدرس ایمیل: ' . esc_html( $order->order_email ) . '<br />'
				. 'شماره تلفن: ' . esc_html( $order->order_phone ) . '<br />'
				. 'توضیح: ' . esc_html( $order->order_des ) . '<br />'
				. 'تاریخ: ' . esc_html( $order->order_date ) . '<br />'
				. 'آی پی: ' . esc_html( $order->order_ip ) . '<br />'
				. 'مبلغ (ریال): ' . esc_html( $order->order_amount ) . '<br />'
				. 'رسيد ديجيتالي سفارش: ' . esc_html( $order->order_referenceId ) . '
			';
			
			include_once BANK_MELLAT()->plugin_folder . '/includes/core/order-mail.php';
			

			if ( 'true' !== $settings['SendSmS'] ) {
				return;
			}
				
			include_once BANK_MELLAT()->plugin_folder . '/includes/core/sms.php';
		}//end foreach
	}
}
