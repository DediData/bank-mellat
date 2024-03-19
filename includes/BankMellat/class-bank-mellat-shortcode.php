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
		$options  = $GLOBALS['WPBEGPAY_Options'];
		$settings = get_option( 'WPBEGPAY_settings_fields_arrays', $options );

		echo "
			<style>
			.WPBEGPAY_Success, .WPBEGPAY_Warning, .bank-mellat-connecting {
				direction: rtl;
				border: 1px solid;
				border-radius: 5px;
				padding: 15px 50px 15px 50px;
				background-repeat: no-repeat;
				background-position: calc(100% - 10px)  center;
			}
			.WPBEGPAY_Warning {
				color: #9F6000;
				background-color: #FEEFB3;
				background-image: url('" . esc_url( $this->plugin_url . 'assets/images/warning.png' ) . "');
			}
			.WPBEGPAY_Success {
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
			$default_themes = array( 'formA.html', 'formB.html', 'formC.html' );

			if ( in_array( $settings['form'], $default_themes, true ) ) {
				include_once plugin_dir_path( __FILE__ ) . '/../forms/' . $settings['form'];
			} elseif ( ! in_array( $settings['form'], $default_themes, true ) ) {
				include_once \WP_CONTENT_DIR . '/WPBEGPAY/' . $settings['form'];
			}
			
			$server_req_method = filter_input( \INPUT_SERVER, 'REQUEST_METHOD', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$bank_mellat_price = filter_input( \INPUT_POST, 'bank_mellat_price', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( 'POST' === $server_req_method && null !== $bank_mellat_price ) {
				$order_id   = time() . wp_rand( 100000, 999999 );
				$local_date = gmdate( 'Ymd' );
				$local_time = gmdate( 'His' );
				$client     = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' );
				$namespace  = 'http://interfaces.core.sw.bps.com/';
				
				$client_error = $client->getError();
				if ( ! $client || $client_error ) {
					echo '<div class="error"><p>' . esc_html( $client_error ) . '</p></div>';
				} else {
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
						$wpdb       = $GLOBALS['wpdb'];
						$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
						
						$server_remote_addr = filter_input( \INPUT_SERVER, 'REMOTE_ADDR', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
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
								'order_name_surname' => $_POST['bank_mellat_name_family'],
								'order_phone'        => $_POST['bank_mellat_phone'],
								'order_des'          => $_POST['bank_mellat_description'],
								'order_email'        => $_POST['bank_mellat_email'],
							)
						);
						?>
						<style>.WPBEGPAY-form,.basic-grey,.elegant-aero{display:none;}</style>

						<script language='javascript' type='text/javascript'>
							window.onload = function(){document.forms['Order_Form'].submit()}
						</script>

						<div class="bank-mellat-connecting">

						<?php echo $settings['connecting_msg']; ?>
						
						<form id="Order_Form" name="Order_Form" style="position:absolute;bottom:82px;left:35px;" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
						
							<input type="hidden" name="RefId" value="<?php echo $result_array[1]; ?>" />
							<input name="submit button" type="submit" style="width:100%;" value="ورود به درگاه پرداخت" id="button" />
						</form>
						</div>
						<?php
					} else {
						echo "<script>alert('امکان اتصال به درگاه پرداخت وجود ندارد!\\nکدخطا:$result_code');location.reload();</script>";
					}
					
					if ( $client->fault ) {
					
						echo '<h2>خطا!</h2><pre>';
						print_r( $result );
						echo '</pre>';
						
						die();
					} else {
						$err = $client->getError();
						if ( $err ) {
							// Display the error
							echo '<h2>خطا!</h2><pre>' . $err . '</pre>';
							die();
						}
					}
				}
			}
		} elseif ( isset( $_POST['ResCode'] ) ) {
		
			$client    = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' );
			$namespace = 'http://interfaces.core.sw.bps.com/';
			
			$err = $client->getError();
			
			if ( $err ) {
				
				echo '<div class="warning">' . $settings['invalid_msg'] . '</div>';
				exit;
			}
			
			$result_code           = $_POST['ResCode'];
			$terminalId            = $settings['MellatG_TerminalNumber'];
			$userName              = $settings['MellatG_TerminalUser'];
			$userPassword          = $settings['MellatG_TerminalPass'];
			$refid                 = $_POST['refid'];
			$order_id              = $_POST['SaleOrderId'];
			$verifySaleOrderId     = $_POST['SaleOrderId'];
			$verifySaleReferenceId = $_POST['SaleReferenceId'];
			
			if ( 0 == $result_code ) {
				
				if ( $client->fault ) {
					echo '<div class="warning">' . $settings['error_msg'] . '</div>';
					exit;
				}
				
				$refid = $_POST['refid'];
				
				$parameters = array(
					'terminalId'      => $terminalId,
					'userName'        => $userName,
					'userPassword'    => $userPassword,
					'saleOrderId'     => $order_id,
					'saleOrderId'     => $verifySaleOrderId,
					'saleReferenceId' => $verifySaleReferenceId
				);
				
				$resultpay = $client->call( 'bpVerifyRequest', $parameters, $namespace );
				$Check     = $client->call( 'bpInquiryRequest', $parameters, $namespace );
				if ( '0' == $Check ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
					
					$wpdb->update( 
						$table_name, 
						array( 'order_status' => 'yes' ), 
						array( 'order_orderid' => $order_id ), 
						array( '%s' ), 
						array( '%s' ) 
					);
									
					$settel = $client->call( 'bpSettleRequest', $parameters, $namespace );
					
					$wpdb->update( 
						$table_name, 
						array( 'order_settle' => 'yes' ), 
						array( 'order_orderid' => $order_id ), 
						array( '%s' ), 
						array( '%s' ) 
					);
					
					$wpdb->update( 
						$table_name, 
						array( 'order_referenceId' => $verifySaleReferenceId ), 
						array( 'order_orderid' => $order_id ), 
						array( '%s' ), 
						array( '%s' )
					);

					$getorder = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_orderid = $order_id" );

					foreach ( $getorder as $order ) {
						
						echo'
							<div class="WPBEGPAY_Success">' . $settings['successfull_msg'] . '</div>
							شماره سفارش: ' . $order->order_id . '</br>
							نام و نام خانوادگي: ' . $order->order_name_surname . '</br>
							آدرس ايميل: ' . $order->order_email . '</br>
							شماره تلفن: ' . $order->order_phone . '</br>
							توضيحات: ' . $order->order_des . '</br>
							تاريخ: ' . $order->order_date . '</br>
							آي پي: ' . $order->order_ip . '</br>
							مبلغ(ريال): ' . $order->order_amount . '</br>
							رسيد ديجيتالي سفارش: ' . $order->order_referenceId . '
						';
						
						include_once plugin_dir_path( __FILE__ ) . '/inc/order_mail.php';
						
						if ( 'true' == $settings['SendSmS'] ) {
							
							$AdminMobile   = $settings['adminMobile'];
							$smsUserName   = $settings['Sms_username'];
							$smsPassword   = $settings['Sms_password'];
							$smsLineNumber = $settings['sms_lineNumber'];
							$sms_service   = $settings['sms_service'];
							$sms_text      = $settings['Sms_text'];
							$sms_text      = str_replace( '#', $order->order_id, $sms_text );
							$sms_text      = str_replace( '$', number_format( $order->order_amount ), $sms_text );
							
							include_once plugin_dir_path( __FILE__ ) . '/inc/sms.php';
						}
					}
				}
			} else {
				echo '<div class="WPBEGPAY_Warning">' . $settings['cancel_msg'] . '</div>';
			}
		}
	}

	/**
	 * Dynamically generate and return the current URL of the page being accessed.
	 * 
	 * @return string
	 */
	private function get_current_url() {
		$server_https = filter_input( \INPUT_SERVER, 'HTTPS', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_url  = $server_https === 'on' ? 'https://' : 'http://';
		$server_name  = filter_input( \INPUT_SERVER, 'SERVER_NAME', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_url .= $server_name;
		$server_port  = filter_input( \INPUT_SERVER, 'SERVER_PORT', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $server_port != '80' && $server_port != '443' ) {
			$current_url .= ':' . $server_port;
		}
		$current_url .= filter_input( \INPUT_SERVER, 'REQUEST_URI', \FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		return $current_url;
	}
}
