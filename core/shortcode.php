<?php
defined('ABSPATH') or die("-1");

if(!class_exists('nusoap_base')){

    require_once(  plugin_dir_path( __FILE__ ) .'/lib/nusoap.php' );
}

function WPBEGPAY_ShortCode() {
	
	global $WPBEGPAY_Options;
	$settings = get_option('WPBEGPAY_settings_fields_arrays', $WPBEGPAY_Options);

	echo"
	<style>
	.WPBEGPAY_Success, .WPBEGPAY_Warning, .WPBEGPAY_Connecting {
		direction:rtl;
		border: 1px solid;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		padding:15px 50px 15px 50px;
		background-repeat: no-repeat;
		background-position: calc(100% - 10px)  center;
	}

	.WPBEGPAY_Warning {
		color: #9F6000;
		background-color: #FEEFB3;
		background-image: url('".plugins_url( '../images/warning.png', __FILE__ )."');
	}

	.WPBEGPAY_Success {
		color: #4F8A10;
		background-color: #DFF2BF;
		background-image: url('".plugins_url( '../images/success.png', __FILE__ )."');
	}
	.WPBEGPAY_Connecting {
		color: #4F8A10;
		background-color: #DFF2BF;
		background-image: url('".plugins_url( '../images/Loader.GIF', __FILE__ )."');
	}
	</style>";

	if (!isset($_POST['ResCode'])){
				
		$defaultThemes = array("formA.html", "formB.html", "formC.html");
		
		if(in_array($settings['form'], $defaultThemes))

			include_once(plugin_dir_path( __FILE__ ) .'/../forms/' . $settings['form']);
		else
			include_once(WP_CONTENT_DIR . "/WPBEGPAY/" . $settings['form']);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' And $_POST['WPBEGPAY_price'] != null){
			
			$orderId = time().rand(100000,999999);
			$ldate = date ('Ymd');
			$ltime = date ('His');
			$client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
			$namespace='http://interfaces.core.sw.bps.com/';
				
			
			if ( (!$client) OR ($err = $client->getError()) ) {
				
				$error .= $err . "<br/>" ;
				echo $error ;
			} else {
				
				function getCurrentURL(){
				
					$currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
					$currentURL .= $_SERVER["SERVER_NAME"];
				 
					if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
					{
						$currentURL .= ":".$_SERVER["SERVER_PORT"];
					} 
				 
						$currentURL .= $_SERVER["REQUEST_URI"];
					return $currentURL;
				}

				$par = array(
						'terminalId' => $settings['MellatG_TerminalNumber'] ,
						'userName' => $settings['MellatG_TerminalUser'] ,
						'userPassword' => $settings['MellatG_TerminalPass'],
						'orderId' => $orderId,
						'amount' => $_POST['WPBEGPAY_price'],
						'localDate' => $ldate,
						'localTime' => $ltime,
						'additionalData' => '',
						'callBackUrl' => getCurrentURL(),
						'payerId' => '0'
						);
						
				$result = $client->call('bpPayRequest', $par, $namespace);
				$resultStr  = $result;        
				$res = explode (',',$resultStr);      
				$ResCode = $res[0];          
				
				if($ResCode == "0"){
					
					global $wpdb;
					$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
					
					$wpdb->insert( 
						$table_name, 
						array(
							'order_status' => 'no',
							'order_amount' => $_POST['WPBEGPAY_price'],
							'order_date' => date("H:i:s Y/m/d"),
							'order_ip' => $_SERVER['REMOTE_ADDR'],
							'order_orderid' => $orderId,
							'order_referenceId' => '',
							'order_refid' => $res[1],
							'order_settle' => 'no',
							'order_name_surname' => $_POST['WPBEGPAY_namefamily'],
							'order_phone' => $_POST['WPBEGPAY_phone'],
							'order_des' => $_POST['WPBEGPAY_des'],
							'order_email' => $_POST['WPBEGPAY_email']
						));
?>
				<style>.WPBEGPAY-form,.basic-grey,.elegant-aero{display:none;}</style>

				<script language='javascript' type='text/javascript'>
				   window.onload = function(){document.forms['Order_Form'].submit()}
				</script>

				<div class="WPBEGPAY_Connecting">

				  <?php echo $settings['connecting_msg'];?>
				  
				   <form id="Order_Form" name="Order_Form" style="position:absolute;bottom:82px;left:35px;" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
				   
					  <input type="hidden" name="RefId" value="<?php echo $res[1]?>" />
					  <input name="submit button" type="submit" style="width:100%;" value="ورود به درگاه پرداخت" id="button" />
				   </form>
				</div>
	 <?php
				} else {      
					
					echo "<script>alert('امکان اتصال به درگاه پرداخت وجود ندارد!\\nکدخطا:$ResCode');location.reload();</script>";
				}
				
				if ($client->fault) {
				
					echo '<h2>خطا!</h2><pre>';
					print_r($result);
					echo '</pre>';
					
					die();
				} else {	
				
					$err = $client->getError();
			
					if ($err) {
						// Display the error
						echo '<h2>خطا!</h2><pre>' . $err . '</pre>';
						die();
					} 
				}	
			}
		}
	} else if(isset($_POST['ResCode'])){

	if($_POST['ResCode'] == null)
			die("Are you kidding me!");
	
		$client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
		$namespace = 'http://interfaces.core.sw.bps.com/';
		
		$err = $client->getError();
		
		if ($err) {
			
			echo '<div class="warning">'.$settings['invalid_msg'].'</div>';
			exit;
		}
		
		$ResCode = $_POST['ResCode'];
		$terminalId = $settings['MellatG_TerminalNumber'];
		$userName = $settings['MellatG_TerminalUser'];
		$userPassword = $settings['MellatG_TerminalPass'];
		$refid = $_POST['refid'];
		$orderId = $_POST['SaleOrderId'];
		$verifySaleOrderId = $_POST['SaleOrderId'];
		$verifySaleReferenceId = $_POST['SaleReferenceId'];
		
		if ($ResCode == 0){
			
			if ($client->fault){
				
				echo '<div class="warning">'.$settings['error_msg'].'</div>';
				exit;
			}
			
			$refid = $_POST['refid'];
			
			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'saleOrderId' => $orderId,
				'saleOrderId' => $verifySaleOrderId,
				'saleReferenceId' => $verifySaleReferenceId
			);
			
			$resultpay = $client->call('bpVerifyRequest', $parameters, $namespace);	
			$Check = $client->call('bpInquiryRequest', $parameters, $namespace); 
			if($Check == '0'){
				
				global $wpdb;
				$table_name = $wpdb->prefix . 'WPBEGPAY_orders';
				
				$wpdb->update( 
					$table_name, 
					array( 'order_status' => 'yes' ), 
					array( 'order_orderid' => $orderId ), 
					array( '%s' ), 
					array( '%s' ) 
				);
								
				$settel = $client->call('bpSettleRequest', $parameters, $namespace);
				
				$wpdb->update( 
					$table_name, 
					array( 'order_settle' => 'yes' ), 
					array( 'order_orderid' => $orderId ), 
					array( '%s' ), 
					array( '%s' ) 
				);
				
				$wpdb->update( 
					$table_name, 
					array( 'order_referenceId' => $verifySaleReferenceId ), 
					array( 'order_orderid' => $orderId ), 
					array( '%s' ), 
					array( '%s' )
				);

				$getorder = $wpdb->get_results("SELECT * FROM $table_name WHERE order_orderid = $orderId");

				foreach ($getorder as $order) {
					
					echo'
						<div class="WPBEGPAY_Success">'.$settings['successfull_msg'].'</div>
						شماره سفارش: '. $order->order_id.'</br>
						نام و نام خانوادگي: '.$order->order_name_surname.'</br>
						آدرس ايميل: '.$order->order_email.'</br>
						شماره تلفن: '. $order->order_phone.'</br>
						توضيحات: '.$order->order_des.'</br>
						تاريخ: '.$order->order_date.'</br>
						آي پي: '.$order->order_ip.'</br>
						مبلغ(ريال): '.$order->order_amount.'</br>
						رسيد ديجيتالي سفارش: '.$order->order_referenceId.'
					';
					
					include_once( plugin_dir_path( __FILE__ ) . '/inc/order_mail.php');
					
					if($settings['SendSmS'] == 'true'){
						
						$AdminMobile = $settings['adminMobile'];
						$smsUserName = $settings['Sms_username'];
						$smsPassword = $settings['Sms_password'];
						$smsLineNumber = $settings['sms_lineNumber'];
						$sms_service = $settings['sms_service'];
						$sms_text = $settings['Sms_text'];
						$sms_text = str_replace('#', $order->order_id, $sms_text);
						$sms_text = str_replace('$',  number_format($order->order_amount), $sms_text);
						
						include_once(plugin_dir_path( __FILE__ ) .'/inc/sms.php');
					}
				}
			}
		} else{

			echo'<div class="WPBEGPAY_Warning">'.$settings['cancel_msg'].'</div>';
		}
	}

}

add_shortcode('WPBEGPAY_SC', 'WPBEGPAY_ShortCode');
?>