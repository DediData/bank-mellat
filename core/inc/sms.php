<?php

defined('ABSPATH') or die("-1");

?>
<div style="display:none;">
<?php

if ($sms_service == 'diakosms') {
	$f = @file_get_contents("http://www.diakosms.ir/WsSms.asmx/sendsms?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'f2usms') {
	$f = @file_get_contents("http://sms.f2u.ir/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'mediana') {
	include_once ('../core/lib/nusoap.php');

	$f = @file_get_contents("http://185.4.28.180/class/sms/webservice/send_url.php?from=" . $smsLineNumber . "&to=" . $AdminMobile . "&msg=" . urlencode($sms_text) . "&uname=" . $smsUserName . "&pass=" . $smsPassword);
	if (!empty($customer_mob)) {
		$ff = @file_get_contents("http://185.4.28.180/class/sms/webservice/send_url.php?from=" . $smsLineNumber . "&to=" . $customer_mob . "&msg=" . urlencode($sms_text) . "&uname=" . $smsUserName . "&pass=" . $smsPassword);
	}
}
elseif ($sms_service == 'aminsms') {

	$f = @file_get_contents("http://37.130.202.188/class/sms/webservice/send_url.php?from=" . $smsLineNumber . "&to=" . $AdminMobile . "&msg=" . urlencode($sms_text) . "&uname=" . $smsUserName . "&pass=" . $smsPassword);
	if (!empty($customer_mob)) {
		$ff = @file_get_contents("http://37.130.202.188/class/sms/webservice/send_url.php?from=" . $smsLineNumber . "&to=" . $customer_mob . "&msg=" . urlencode($sms_text) . "&uname=" . $smsUserName . "&pass=" . $smsPassword);
	}
}
elseif ($sms_service == 'iransms') {
	$f = @file_get_contents("http://panel.iransms.cc/url.php?from=" . $smsLineNumber . "&to=" . $AdminMobile . "&text=" . urlencode($sms_text) . "&username=" . $smsUserName . "&password=" . $smsPassword);
	if (!empty($customer_mob)) {
		$ff = @file_get_contents("http://panel.iransms.cc/url.php?from=" . $smsLineNumber . "&to=" . $customer_mob . "&text=" . urlencode($sms_text) . "&username=" . $smsUserName . "&password=" . $smsPassword);
	}
}
elseif ($sms_service == 'melipayamak') {

	// turn off the WSDL cache

	ini_set("soap.wsdl_cache_enabled", "0");
	try {
		$client = new SoapClient("http://87.107.121.54/post/send.asmx?wsdl");
		$parameters['username'] = $smsUserName;
		$parameters['password'] = $smsPassword;
		$parameters['from'] = $smsLineNumber;
		$parameters['to'] = array(
			$AdminMobile,
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
		$parameters['username'] = $smsUserName;
		$parameters['password'] = $smsPassword;
		$parameters['from'] = $smsLineNumber;
		$parameters['to'] = array(
			$AdminMobile,
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
		$parameters['username'] = $smsUserName;
		$parameters['password'] = $smsPassword;
		$parameters['from'] = $smsLineNumber;
		$parameters['to'] = array(
			$AdminMobile,
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
	$f = @file_get_contents("http://sms.panel2u.ir/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'fpayamak') {
	$f = @file_get_contents("http://login.payamakde.com/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'freersms') {
	$client = new nusoap_client('http://sms.freer.ir/gateway/index.php?wsdl', 'wsdl');
	$res = $client->call('SendSMS', array(
		$smsUserName,
		$smsPassword,
		$smsLineNumber,
		$AdminMobile . ',' . $customer_mob,
		$sms_text
	));
}
elseif ($sms_service == 'hezarnevis') {
	$url = "http://panel.hezarnevis.com/API/SendSms.ashx?username=" . $smsUserName . "&password=" . $smsPassword . "&from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text);
	$result = @file_get_contents($url);
}
elseif ($sms_service == 'idehsms') {
	$url = "http://sms.idehsms.ir/remote.php";
	$parameters["Number"] = "$smsLineNumber";
	$parameters["RemoteCode"] = "$data[RemoteCode]";
	$parameters["Message"] = "$sms_text";
	$parameters["Farsi"] = "1";
	$parameters["To"] = "$AdminMobile . ','.$customer_mob";
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
	$username = $smsUserName;
	$password = $smsPassword;
	$sender = $smsLineNumber;
	$reciever = $AdminMobile . ',' . $customer_mob;
	$text = $sms_text;
	$soapclient = new nusoap_client('http://ws.idehsms.ir/index.php?wsdl', 'wsdl');
	$soapclient->soap_defencoding = 'UTF-8';
	$soapProxy = $soapclient->getProxy();
	$res = $soapProxy->SendSMS($username, $password, $reciever, $text, $sender);
}
elseif ($sms_service == 'irpayamak') {
	$username = "$smsUserName";
	$password = "$smsPassword";
	$from = "$smsLineNumber";
	$to = "$AdminMobile . ','.$customer_mob";
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
	$f = @file_get_contents("http://panel.panizsms.com/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'parandsms') {
	$f = @file_get_contents("http://parandsms.ir/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'persiansms') {
	$my_class = new SoapClient('http://www.persiansms.info/webservice/smsService.php?wsdl', array(
		'trace' => 1
	));
	$smsid = $my_class->send_sms($smsUserName, $smsPassword, $smsLineNumber, $AdminMobile, $sms_text);
	if (!empty($customer_mob)) {
		$smsids = $my_class->send_sms($smsUserName, $smsPassword, $smsLineNumber, $customer_mob, $sms_text);
	}
}
elseif ($sms_service == 'mcisms') {
	$f = @file_get_contents("http://www.p.mcisms.net/send_via_get/send_sms.php?username=" . $smsUserName . "&password=" . $smsPassword . "&sender_number=" . $smsLineNumber . "&receiver_number=" . $AdminMobile . "&note=" . urlencode($sms_text));
	if (!empty($customer_mob)) {
		echo $f = @file_get_contents("http://www.p.mcisms.net/send_via_get/send_sms.php?username=" . $smsUserName . "&password=" . $smsPassword . "&sender_number=" . $smsLineNumber . "&receiver_number=" . $customer_mob . "&note=" . urlencode($sms_text));
	}
}
elseif ($sms_service == 'textsms') {
	$f = @file_get_contents("http://textsms.ir/send_via_get/send_sms.php?username=" . $smsUserName . "&password=" . $smsPassword . "&sender_number=" . $smsLineNumber . "&receiver_number=" . $AdminMobile . ',' . $customer_mob . "&note=" . urlencode($sms_text));
}
elseif ($sms_service == 'payamresan') {
	$f = @file_get_contents("http://www.payam-resan.com/APISend.aspx?Username=" . $smsUserName . "&Password=" . $smsPassword . "&From=" . $smsLineNumber . "&To=" . $AdminMobile . ',' . $customer_mob . "&Text=" . urlencode($sms_text));
}
elseif ($sms_service == 'samanpayamak') {
	$get["username"] = $smsUserName;
	$get["password"] = $smsPassword;
	$get["from"] = $smsLineNumber;
	$get["To"] = $AdminMobile . ',' . $customer_mob;
	$get["text"] = $sms_text;
	$baseURL = 'http://samanpayamak.ir/API/SendSms.ashx';
	$filename = $baseURL . '?' . http_build_query($get);
	$res = file_get_contents($filename);
}elseif ($sms_service == 'shabnam1') {
	$url = "http://37.130.202.188/services.jspd";
	
	$rcpt_nm = array($AdminMobile,$customer_mob);
	$param = array
				(
					'uname'=>$smsUserName,
					'pass'=>$smsPassword,
					'from'=>$smsLineNumber,
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
	$f = @file_get_contents("http://panel.sigmasms.ir/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'banehsms') {
	$f = @file_get_contents("http://banehsms.ir/post/SendWithDelivery.ashx?from" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'shabnam') {
	$f = @file_get_contents("http://shabnam-sms.ir/API/SendSms.ashx?username=" . $smsUserName . "&password=" . $smsPassword . "&from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text));
}
elseif ($sms_service == 'smsclick') {
	try {
		$client = new SoapClient("http://sms.dorbid.ir/post/send.asmx?wsdl", array(
			'encoding' => 'UTF-8'
		));
		$parameters['username'] = "$smsUserName";
		$parameters['password'] = "$smsPassword";
		$parameters['from'] = "$smsLineNumber";
		$parameters['to'] = array(
			"$AdminMobile . ','.$customer_mob"
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
	$f = @file_get_contents("http://spadsms.net/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'wstdsms') {
	$f = @file_get_contents("http://sms.webstudio.ir/post/sendSMS.ashx?from=" . $smsLineNumber . "&to=" . $AdminMobile . ',' . $customer_mob . "&text=" . urlencode($sms_text) . "&password=" . $smsPassword . "&username=" . $smsUserName);
}
elseif ($sms_service == 'hostiran') {
	$options = array(
		'login' => $smsUserName,
		'password' => $smsPassword
	);
	$client = new SoapClient('http://sms.hostiran.net/webservice/?WSDL', $options);
	try {
		$messageId = $client->send($AdminMobile, $sms_text);
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