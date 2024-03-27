<?php
/**
 * Bank Mellat SMS Class
 *
 * @package Bank_Mellat
 */

declare(strict_types=1);

namespace BankMellat;

/**
 * Class Bank_Mellat_SMS
 */
final class Bank_Mellat_Sms extends \DediData\Singleton {

	/**
	 * Constructor
	 *
	 * @param mixed $order    Order.
	 * @param mixed $settings Settings.
	 * @return void
	 */
	public function __construct( $order, $settings ) {
		$admin_mobile    = $settings['adminMobile'];
		$sms_user_name   = $settings['Sms_username'];
		$sms_password    = $settings['Sms_password'];
		$sms_line_number = $settings['sms_lineNumber'];
		$sms_service     = $settings['sms_service'];
		$customer_mob    = $order->order_phone;
		$sms_text        = $settings['Sms_text'];
		$sms_text        = str_replace( '#', $order->order_id, $sms_text );
		$sms_text        = str_replace( '$', number_format( $order->order_amount ), $sms_text );
		?>
		<div style="display:none">
			<?php
			if ( 'diakosms' === $sms_service ) {
				$f = wp_remote_get( 'http://www.diakosms.ir/WsSms.asmx/sendsms?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'f2usms' === $sms_service ) {
				$f = wp_remote_get( 'http://sms.f2u.ir/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'mediana' === $sms_service ) {
				$f = wp_remote_get( 'http://185.4.28.180/class/sms/webservice/send_url.php?from=' . $sms_line_number . '&to=' . $admin_mobile . '&msg=' . urlencode( $sms_text ) . '&uname=' . $sms_user_name . '&pass=' . $sms_password );
				if ( '' !== $customer_mob ) {
					$ff = wp_remote_get( 'http://185.4.28.180/class/sms/webservice/send_url.php?from=' . $sms_line_number . '&to=' . $customer_mob . '&msg=' . urlencode( $sms_text ) . '&uname=' . $sms_user_name . '&pass=' . $sms_password );
				}
			} elseif ( 'aminsms' === $sms_service ) {
				$f = wp_remote_get( 'http://37.130.202.188/class/sms/webservice/send_url.php?from=' . $sms_line_number . '&to=' . $admin_mobile . '&msg=' . urlencode( $sms_text ) . '&uname=' . $sms_user_name . '&pass=' . $sms_password );
				if ( '' !== $customer_mob ) {
					$ff = wp_remote_get( 'http://37.130.202.188/class/sms/webservice/send_url.php?from=' . $sms_line_number . '&to=' . $customer_mob . '&msg=' . urlencode( $sms_text ) . '&uname=' . $sms_user_name . '&pass=' . $sms_password );
				}
			} elseif ( 'iransms' === $sms_service ) {
				$f = wp_remote_get( 'http://panel.iransms.cc/url.php?from=' . $sms_line_number . '&to=' . $admin_mobile . '&text=' . urlencode( $sms_text ) . '&username=' . $sms_user_name . '&password=' . $sms_password );
				if ( '' !== $customer_mob ) {
					$ff = wp_remote_get( 'http://panel.iransms.cc/url.php?from=' . $sms_line_number . '&to=' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&username=' . $sms_user_name . '&password=' . $sms_password );
				}
			} elseif ( 'melipayamak' === $sms_service ) {
				// turn off the WSDL cache
				ini_set( 'soap.wsdl_cache_enabled', '0' );
				try {
					$client                 = new SoapClient( 'http://87.107.121.54/post/send.asmx?wsdl' );
					$parameters             = array();
					$parameters['username'] = $sms_user_name;
					$parameters['password'] = $sms_password;
					$parameters['from']     = $sms_line_number;
					$parameters['to']       = array(
						$admin_mobile,
						$customer_mob,
					);
					$parameters['text']     = iconv( $encoding, 'UTF-8//TRANSLIT', $sms_text );
					$parameters['isflash']  = true;
					$parameters['udh']      = '';
					$parameters['recId']    = array( 0 );
					$parameters['status']   = 0x0;
					echo esc_html( $client->SendSms( $parameters )->SendSmsResult );
				} catch ( SoapFault $ex ) {
					// empty
				}
			} elseif ( 'payamgah' === $sms_service ) {
				// turn off the WSDL cache
				ini_set( 'soap.wsdl_cache_enabled', '0' );
				try {
					$client                 = new SoapClient( 'http://sms.payamgah.net/API/send.asmx?wsdl' );
					$parameters['username'] = $sms_user_name;
					$parameters['password'] = $sms_password;
					$parameters['from']     = $sms_line_number;
					$parameters['to']       = array(
						$admin_mobile,
						$customer_mob,
					);
					$parameters['text']     = iconv( $encoding, 'UTF-8//TRANSLIT', $sms_text );
					$parameters['isflash']  = true;
					$parameters['udh']      = '';
					$parameters['recId']    = array( 0 );
					$parameters['status']   = 0x0;
					echo esc_html( $client->SendSms( $parameters )->SendSmsResult );
				} catch ( SoapFault $ex ) {
					// empty
				}
			} elseif ( 'limoosms' === $sms_service ) {
				// turn off the WSDL cache
				ini_set( 'soap.wsdl_cache_enabled', '0' );
				try {
					$client                 = new SoapClient( 'http://panel.limoosms.com/post/send.asmx?wsdl' );
					$parameters['username'] = $sms_user_name;
					$parameters['password'] = $sms_password;
					$parameters['from']     = $sms_line_number;
					$parameters['to']       = array(
						$admin_mobile,
						$customer_mob,
					);
					$parameters['text']     = iconv( $encoding, 'UTF-8//TRANSLIT', $sms_text );
					$parameters['isflash']  = true;
					$parameters['udh']      = '';
					$parameters['recId']    = array( 0 );
					$parameters['status']   = 0x0;
					echo $client->SendSms( $parameters )->SendSmsResult;
				} catch ( SoapFault $ex ) {
					// empty
				}
			} elseif ( 'f2usms2' === $sms_service ) {
				$f = file_get_contents( 'http://sms.panel2u.ir/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'fpayamak' === $sms_service ) {
				$f = file_get_contents( 'http://login.payamakde.com/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'freersms' === $sms_service ) {
				if ( ! class_exists( 'nusoap_base' ) ) {
					require_once $this->plugin_folder . '/includes/Composer/autoload.php';
				}
				$client = new nusoap_client( 'http://sms.freer.ir/gateway/index.php?wsdl', 'wsdl' );
				$res    = $client->call(
					'SendSMS',
					array(
						$sms_user_name,
						$sms_password,
						$sms_line_number,
						$admin_mobile . ',' . $customer_mob,
						$sms_text,
					)
				);
			} elseif ( 'hezarnevis' === $sms_service ) {
				$url    = 'http://panel.hezarnevis.com/API/SendSms.ashx?username=' . $sms_user_name . '&password=' . $sms_password . '&from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text );
				$result = file_get_contents( $url );
			} elseif ( 'idehsms' === $sms_service ) {
				$url                      = 'http://sms.idehsms.ir/remote.php';
				$parameters['Number']     = "$sms_line_number";
				$parameters['RemoteCode'] = "$data[RemoteCode]";
				$parameters['Message']    = "$sms_text";
				$parameters['Farsi']      = '1';
				$parameters['To']         = "$admin_mobile . ','.$customer_mob";
				$ch                       = curl_init();
				curl_setopt( $ch, \CURLOPT_URL, $url );
				curl_setopt( $ch, \CURLOPT_POST, 1 );
				curl_setopt( $ch, \CURLOPT_TIMEOUT, 310000 );
				curl_setopt( $ch, \CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, \CURLOPT_POSTFIELDS, $parameters );
				$data1 = curl_exec( $ch );
				curl_close( $ch );
			} elseif ( 'idehsms3000' === $sms_service ) {
				if ( ! class_exists( 'nusoap_base' ) ) {
					require_once $this->plugin_folder . '/includes/Composer/autoload.php';
				}
				$username                      = $sms_user_name;
				$password                      = $sms_password;
				$sender                        = $sms_line_number;
				$reciever                      = $admin_mobile . ',' . $customer_mob;
				$text                          = $sms_text;
				$soap_client                   = new nusoap_client( 'http://ws.idehsms.ir/index.php?wsdl', 'wsdl' );
				$soap_client->soap_defencoding = 'UTF-8';
				$soap_proxy                    = $soap_client->getProxy();
				$res                           = $soap_proxy->SendSMS( $username, $password, $reciever, $text, $sender );
			} elseif ( 'irpayamak' === $sms_service ) {
				$username = "$sms_user_name";
				$password = "$sms_password";
				$from     = "$sms_line_number";
				$to_phone = "$admin_mobile . ','.$customer_mob";
				$text     = "$sms_text";
				$isflash;
				$url    = 'http://ir-payamak.com/sendsms.php';
				$fields = array(
					'programmer' => '4',
					'username'   => "$username",
					'password'   => "$password",
					'from'       => $from,
					'to'         => $to_phone,
					'text'       => ( "$text" ),
					'isflash'    => "$isflash",
					'udh'        => '',
				);
				foreach ( $fields as $key => $value ) {
					$fields_string .= $key . '=' . $value . '&';
				}

				rtrim( $fields_string, '&' );
				$ch = curl_init();
				curl_setopt( $ch, \CURLOPT_URL, $url );
				curl_setopt( $ch, \CURLOPT_POST, count( $fields ) );
				curl_setopt( $ch, \CURLOPT_POSTFIELDS, $fields_string );
				$result = curl_exec( $ch );
				curl_close( $ch );
			} elseif ( 'panizsms' === $sms_service ) {
				$f = file_get_contents( 'http://panel.panizsms.com/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'parandsms' === $sms_service ) {
				$f = file_get_contents( 'http://parandsms.ir/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'persiansms' === $sms_service ) {
				$my_class = new SoapClient(
					'http://www.persiansms.info/webservice/smsService.php?wsdl',
					array(
						'trace' => 1,
					)
				);
				$smsid    = $my_class->send_sms( $sms_user_name, $sms_password, $sms_line_number, $admin_mobile, $sms_text );
				if ( '' !== $customer_mob ) {
					$smsids = $my_class->send_sms( $sms_user_name, $sms_password, $sms_line_number, $customer_mob, $sms_text );
				}
			} elseif ( 'mcisms' === $sms_service ) {
				$f = file_get_contents( 'http://www.p.mcisms.net/send_via_get/send_sms.php?username=' . $sms_user_name . '&password=' . $sms_password . '&sender_number=' . $sms_line_number . '&receiver_number=' . $admin_mobile . '&note=' . urlencode( $sms_text ) );
				if ( '' !== $customer_mob ) {
					echo $f = file_get_contents( 'http://www.p.mcisms.net/send_via_get/send_sms.php?username=' . $sms_user_name . '&password=' . $sms_password . '&sender_number=' . $sms_line_number . '&receiver_number=' . $customer_mob . '&note=' . urlencode( $sms_text ) );
				}
			} elseif ( 'textsms' === $sms_service ) {
				$f = file_get_contents( 'http://textsms.ir/send_via_get/send_sms.php?username=' . $sms_user_name . '&password=' . $sms_password . '&sender_number=' . $sms_line_number . '&receiver_number=' . $admin_mobile . ',' . $customer_mob . '&note=' . urlencode( $sms_text ) );
			} elseif ( 'payamresan' === $sms_service ) {
				$f = file_get_contents( 'http://www.payam-resan.com/APISend.aspx?Username=' . $sms_user_name . '&password=' . $sms_password . '&from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) );
			} elseif ( 'samanpayamak' === $sms_service ) {
				$get['username'] = $sms_user_name;
				$get['password'] = $sms_password;
				$get['from']     = $sms_line_number;
				$get['To']       = $admin_mobile . ',' . $customer_mob;
				$get['text']     = $sms_text;
				$baseURL         = 'http://samanpayamak.ir/API/SendSms.ashx';
				$filename        = $baseURL . '?' . http_build_query( $get );
				$res             = file_get_contents( $filename );
			} elseif ( 'shabnam1' === $sms_service ) {
				$url = 'http://37.130.202.188/services.jspd';

				$rcpt_nm = array( $admin_mobile, $customer_mob );
				$param   = array(
					'uname'   => $sms_user_name,
					'pass'    => $sms_password,
					'from'    => $sms_line_number,
					'message' => $sms_text,
					'to'      => json_encode( $rcpt_nm ),
					'op'      => 'send',
				);

				$handler = curl_init( $url );
				curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
				curl_setopt( $handler, CURLOPT_POSTFIELDS, $param );
				curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
				$response2 = curl_exec( $handler );
			} elseif ( 'sgmsms' === $sms_service ) {
				$f = file_get_contents( 'http://panel.sigmasms.ir/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'banehsms' === $sms_service ) {
				$f = file_get_contents( 'http://banehsms.ir/post/SendWithDelivery.ashx?from' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'shabnam' === $sms_service ) {
				$f = file_get_contents( 'http://shabnam-sms.ir/API/SendSms.ashx?username=' . $sms_user_name . '&password=' . $sms_password . '&from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) );
			} elseif ( 'smsclick' === $sms_service ) {
				try {
					$client                 = new SoapClient( 'http://sms.dorbid.ir/post/send.asmx?wsdl', array( 'encoding' => 'UTF-8' ) );
					$parameters['username'] = "$sms_user_name";
					$parameters['password'] = "$sms_password";
					$parameters['from']     = "$sms_line_number";
					$parameters['to']       = array( "$admin_mobile . ',' . $customer_mob" );
					$parameters['text']     = "$sms_text";
					$parameters['isflash']  = false;
					$parameters['udh']      = '';
					$parameters['recId']    = array( 0 );
					$parameters['status']   = 0x0;
					echo $client->GetCredit(
						array(
							'username' => 'wsdemo',
							'password' => 'wsdemo',
						)
					)->GetCreditResult;
					echo $client->SendSms( $parameters )->SendSmsResult;
					echo $status;
				} catch ( SoapFault $ex ) {
					echo $ex->faultstring;
				}
			} elseif ( 'spadsms' === $sms_service ) {
				$f = file_get_contents( 'http://spadsms.net/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'wstdsms' === $sms_service ) {
				$f = file_get_contents( 'http://sms.webstudio.ir/post/sendSMS.ashx?from=' . $sms_line_number . '&to=' . $admin_mobile . ',' . $customer_mob . '&text=' . urlencode( $sms_text ) . '&password=' . $sms_password . '&username=' . $sms_user_name );
			} elseif ( 'hostiran' === $sms_service ) {
				$options = array(
					'login'    => $sms_user_name,
					'password' => $sms_password,
				);
				$client  = new SoapClient( 'http://sms.hostiran.net/webservice/?WSDL', $options );
				try {
					$messageId = $client->send( $admin_mobile, $sms_text );
					sleep( 3 );
					print( $client->deliveryStatus( $messageId ) );
					var_dump( $client->accountInfo() );
				} catch ( SoapFault $sf ) {
					print $sf->faultcode . "\n";
					print $sf->faultstring . "\n";
				}

				if ( '' !== $customer_mob ) {
					try {
						$messageIds = $client->send( $customer_mob, $sms_text );
						sleep( 3 );
						print( $client->deliveryStatus( $messageIds ) );
						var_dump( $client->accountInfo() );
					} catch ( SoapFault $sfs ) {
					}
				}
			}

			?>
		</div>
		<?php
	}
}
