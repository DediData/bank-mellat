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
	 */
	public function __construct( $order, $settings ) {

		$admin_mobile    = $settings['adminMobile'];
		$sms_user_name   = $settings['Sms_username'];
		$sms_password    = $settings['Sms_password'];
		$sms_line_number = $settings['sms_lineNumber'];
		$sms_service     = $settings['sms_service'];
		$sms_text        = $settings['Sms_text'];
		$sms_text        = str_replace( '#', $order->order_id, $sms_text );
		$sms_text        = str_replace( '$', number_format( $order->order_amount ), $sms_text );

		?>
		<div style="display:none;">
		<?php

		if ($sms_service == 'diakosms') {
			$f = @file_get_contents("http://www.diakosms.ir/WsSms.asmx/sendsms?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'f2usms') {
			$f = @file_get_contents("http://sms.f2u.ir/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'mediana') {
			include_once ('../core/lib/nusoap.php');

			$f = @file_get_contents("http://185.4.28.180/class/sms/webservice/send_url.php?from=" . $sms_line_number . "&to=" . $admin_mobile . "&msg=" . urlencode($sms_text) . "&uname=" . $sms_user_name . "&pass=" . $sms_password);
			if (!empty($customer_mob)) {
				$ff = @file_get_contents("http://185.4.28.180/class/sms/webservice/send_url.php?from=" . $sms_line_number . "&to=" . $customer_mob . "&msg=" . urlencode($sms_text) . "&uname=" . $sms_user_name . "&pass=" . $sms_password);
			}
		}
		elseif ($sms_service == 'aminsms') {

			$f = @file_get_contents("http://37.130.202.188/class/sms/webservice/send_url.php?from=" . $sms_line_number . "&to=" . $admin_mobile . "&msg=" . urlencode($sms_text) . "&uname=" . $sms_user_name . "&pass=" . $sms_password);
			if (!empty($customer_mob)) {
				$ff = @file_get_contents("http://37.130.202.188/class/sms/webservice/send_url.php?from=" . $sms_line_number . "&to=" . $customer_mob . "&msg=" . urlencode($sms_text) . "&uname=" . $sms_user_name . "&pass=" . $sms_password);
			}
		}
		elseif ($sms_service == 'iransms') {
			$f = @file_get_contents("http://panel.iransms.cc/url.php?from=" . $sms_line_number . "&to=" . $admin_mobile . "&text=" . urlencode($sms_text) . "&username=" . $sms_user_name . "&password=" . $sms_password);
			if (!empty($customer_mob)) {
				$ff = @file_get_contents("http://panel.iransms.cc/url.php?from=" . $sms_line_number . "&to=" . $customer_mob . "&text=" . urlencode($sms_text) . "&username=" . $sms_user_name . "&password=" . $sms_password);
			}
		}
		elseif ($sms_service == 'melipayamak') {

			// turn off the WSDL cache

			ini_set("soap.wsdl_cache_enabled", "0");
			try {
				$client = new SoapClient("http://87.107.121.54/post/send.asmx?wsdl");
				$parameters['username'] = $sms_user_name;
				$parameters['password'] = $sms_password;
				$parameters['from'] = $sms_line_number;
				$parameters['to'] = array(
					$admin_mobile,
					$customer_mob
				);
				$parameters['text'] = iconv($encoding, 'UTF-8//TRANSLIT', $sms_text);
				$parameters['isflash'] = true;
				$parameters['udh'] = "";
				$parameters['recId'] = array(
					0
				);
				$parameters['status'] = 0x0;
				echo $client->SendSms($parameters)->SendSmsResult;
			}

			catch(SoapFault $ex) {
			}
		}
		elseif ($sms_service == 'payamgah') {

			// turn off the WSDL cache

			ini_set("soap.wsdl_cache_enabled", "0");
			try {
				$client = new SoapClient("http://sms.payamgah.net/API/send.asmx?wsdl");
				$parameters['username'] = $sms_user_name;
				$parameters['password'] = $sms_password;
				$parameters['from'] = $sms_line_number;
				$parameters['to'] = array(
					$admin_mobile,
					$customer_mob
				);
				$parameters['text'] = iconv($encoding, 'UTF-8//TRANSLIT', $sms_text);
				$parameters['isflash'] = true;
				$parameters['udh'] = "";
				$parameters['recId'] = array(
					0
				);
				$parameters['status'] = 0x0;
				echo $client->SendSms($parameters)->SendSmsResult;
			}

			catch(SoapFault $ex) {
			}
		}
		elseif ($sms_service == 'limoosms') {

			// turn off the WSDL cache

			ini_set("soap.wsdl_cache_enabled", "0");
			try {
				$client = new SoapClient("http://panel.limoosms.com/post/send.asmx?wsdl");
				$parameters['username'] = $sms_user_name;
				$parameters['password'] = $sms_password;
				$parameters['from'] = $sms_line_number;
				$parameters['to'] = array(
					$admin_mobile,
					$customer_mob
				);
				$parameters['text'] = iconv($encoding, 'UTF-8//TRANSLIT', $sms_text);
				$parameters['isflash'] = true;
				$parameters['udh'] = "";
				$parameters['recId'] = array(
					0
				);
				$parameters['status'] = 0x0;
				echo $client->SendSms($parameters)->SendSmsResult;
			}

			catch(SoapFault $ex) {
			}
		}
		elseif ($sms_service == 'f2usms2') {
			$f = @file_get_contents("http://sms.panel2u.ir/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'fpayamak') {
			$f = @file_get_contents("http://login.payamakde.com/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'freersms') {
			$client = new nusoap_client('http://sms.freer.ir/gateway/index.php?wsdl', 'wsdl');
			$res = $client->call('SendSMS', array(
				$sms_user_name,
				$sms_password,
				$sms_line_number,
				$admin_mobile . ',' . $customer_mob,
				$sms_text
			));
		}
		elseif ($sms_service == 'hezarnevis') {
			$url = "http://panel.hezarnevis.com/API/SendSms.ashx?username=" . $sms_user_name . "&password=" . $sms_password . "&from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text);
			$result = @file_get_contents($url);
		}
		elseif ($sms_service == 'idehsms') {
			$url = "http://sms.idehsms.ir/remote.php";
			$parameters["Number"] = "$sms_line_number";
			$parameters["RemoteCode"] = "$data[RemoteCode]";
			$parameters["Message"] = "$sms_text";
			$parameters["Farsi"] = "1";
			$parameters["To"] = "$admin_mobile . ','.$customer_mob";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 310000);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
			$data1 = curl_exec($ch);
			curl_close($ch);
		}
		elseif ($sms_service == 'idehsms3000') {
			$username = $sms_user_name;
			$password = $sms_password;
			$sender = $sms_line_number;
			$reciever = $admin_mobile . ',' . $customer_mob;
			$text = $sms_text;
			$soapclient = new nusoap_client('http://ws.idehsms.ir/index.php?wsdl', 'wsdl');
			$soapclient->soap_defencoding = 'UTF-8';
			$soapProxy = $soapclient->getProxy();
			$res = $soapProxy->SendSMS($username, $password, $reciever, $text, $sender);
		}
		elseif ($sms_service == 'irpayamak') {
			$username = "$sms_user_name";
			$password = "$sms_password";
			$from = "$sms_line_number";
			$to = "$admin_mobile . ','.$customer_mob";
			$text = "$sms_text";
			$isflash;
			$url = 'http://ir-payamak.com/sendsms.php';
			$fields = array(
				'programmer' => "4",
				'username' => "$username",
				'password' => "$password",
				'from' => $from,
				'to' => $to,
				'text' => ("$text") ,
				'isflash' => "$isflash",
				'udh' => ""
			);
			foreach($fields as $key => $value) {
				$fields_string.= $key . '=' . $value . '&';
			}

			rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		elseif ($sms_service == 'panizsms') {
			$f = @file_get_contents("http://panel.panizsms.com/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'parandsms') {
			$f = @file_get_contents("http://parandsms.ir/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'persiansms') {
			$my_class = new SoapClient('http://www.persiansms.info/webservice/smsService.php?wsdl', array(
				'trace' => 1
			));
			$smsid = $my_class->send_sms($sms_user_name, $sms_password, $sms_line_number, $admin_mobile, $sms_text);
			if (!empty($customer_mob)) {
				$smsids = $my_class->send_sms($sms_user_name, $sms_password, $sms_line_number, $customer_mob, $sms_text);
			}
		}
		elseif ($sms_service == 'mcisms') {
			$f = @file_get_contents("http://www.p.mcisms.net/send_via_get/send_sms.php?username=" . $sms_user_name . "&password=" . $sms_password . "&sender_number=" . $sms_line_number . "&receiver_number=" . $admin_mobile . "&note=" . urlencode($sms_text));
			if (!empty($customer_mob)) {
				echo $f = @file_get_contents("http://www.p.mcisms.net/send_via_get/send_sms.php?username=" . $sms_user_name . "&password=" . $sms_password . "&sender_number=" . $sms_line_number . "&receiver_number=" . $customer_mob . "&note=" . urlencode($sms_text));
			}
		}
		elseif ($sms_service == 'textsms') {
			$f = @file_get_contents("http://textsms.ir/send_via_get/send_sms.php?username=" . $sms_user_name . "&password=" . $sms_password . "&sender_number=" . $sms_line_number . "&receiver_number=" . $admin_mobile . ',' . $customer_mob . "&note=" . urlencode($sms_text));
		}
		elseif ($sms_service == 'payamresan') {
			$f = @file_get_contents("http://www.payam-resan.com/APISend.aspx?Username=" . $sms_user_name . "&Password=" . $sms_password . "&From=" . $sms_line_number . "&To=" . $admin_mobile . ',' . $customer_mob . "&Text=" . urlencode($sms_text));
		}
		elseif ($sms_service == 'samanpayamak') {
			$get["username"] = $sms_user_name;
			$get["password"] = $sms_password;
			$get["from"] = $sms_line_number;
			$get["To"] = $admin_mobile . ',' . $customer_mob;
			$get["text"] = $sms_text;
			$baseURL = 'http://samanpayamak.ir/API/SendSms.ashx';
			$filename = $baseURL . '?' . http_build_query($get);
			$res = file_get_contents($filename);
		}elseif ($sms_service == 'shabnam1') {
			$url = "http://37.130.202.188/services.jspd";
			
			$rcpt_nm = array($admin_mobile,$customer_mob);
			$param = array
						(
							'uname'=>$sms_user_name,
							'pass'=>$sms_password,
							'from'=>$sms_line_number,
							'message'=>$sms_text,
							'to'=>json_encode($rcpt_nm),
							'op'=>'send'
						);
						
			$handler = curl_init($url);             
			curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($handler, CURLOPT_POSTFIELDS, $param);                       
			curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
			$response2 = curl_exec($handler);
		}
		elseif ($sms_service == 'sgmsms') {
			$f = @file_get_contents("http://panel.sigmasms.ir/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'banehsms') {
			$f = @file_get_contents("http://banehsms.ir/post/SendWithDelivery.ashx?from" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'shabnam') {
			$f = @file_get_contents("http://shabnam-sms.ir/API/SendSms.ashx?username=" . $sms_user_name . "&password=" . $sms_password . "&from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text));
		}
		elseif ($sms_service == 'smsclick') {
			try {
				$client = new SoapClient("http://sms.dorbid.ir/post/send.asmx?wsdl", array(
					'encoding' => 'UTF-8'
				));
				$parameters['username'] = "$sms_user_name";
				$parameters['password'] = "$sms_password";
				$parameters['from'] = "$sms_line_number";
				$parameters['to'] = array(
					"$admin_mobile . ','.$customer_mob"
				);
				$parameters['text'] = "$sms_text";
				$parameters['isflash'] = false;
				$parameters['udh'] = "";
				$parameters['recId'] = array(
					0
				);
				$parameters['status'] = 0x0;
				echo $client->GetCredit(array(
					"username" => "wsdemo",
					"password" => "wsdemo"
				))->GetCreditResult;
				echo $client->SendSms($parameters)->SendSmsResult;
				echo $status;
			}

			catch(SoapFault $ex) {
				echo $ex->faultstring;
			}
		}
		elseif ($sms_service == 'spadsms') {
			$f = @file_get_contents("http://spadsms.net/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'wstdsms') {
			$f = @file_get_contents("http://sms.webstudio.ir/post/sendSMS.ashx?from=" . $sms_line_number . "&to=" . $admin_mobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $sms_password . "&username=" . $sms_user_name);
		}
		elseif ($sms_service == 'hostiran') {
			$options = array(
				'login' => $sms_user_name,
				'password' => $sms_password
			);
			$client = new SoapClient('http://sms.hostiran.net/webservice/?WSDL', $options);
			try {
				$messageId = $client->send($admin_mobile, $sms_text);
				sleep(3);
				print ($client->deliveryStatus($messageId));
				var_dump($client->accountInfo());
			}

			catch(SoapFault $sf) {
				print $sf->faultcode . "\n";
				print $sf->faultstring . "\n";
			}

			if (!empty($customer_mob)) {
				try {
					$messageIds = $client->send($customer_mob, $sms_text);
					sleep(3);
					print ($client->deliveryStatus($messageIds));
					var_dump($client->accountInfo());
				}

				catch(SoapFault $sfs) {
				}
			}
		}

		?>
		</div>
	}
}