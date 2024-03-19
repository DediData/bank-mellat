<?php
if(isset($_GET['ExportOrders'])){
		
	if($_GET['ExportOrders'] == null)
		exit;
		
	if (is_admin()) {
			
		header('Content-Type: text/html; charset=utf-8', true, 200);
		header("Content-Disposition: attachment; filename=Orders.html");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		global $wpdb;
		
		$tablename = $wpdb->prefix . "WPBEGPAY_orders";

		$getorder = $wpdb->get_results("SELECT * FROM $tablename order by order_id");
	
		if($getorder){

			echo <<<HTML

				<!DOCTYPE html>
				<html>
				   <head>
					  <meta charset="UTF-8">
					  <meta name="author" content="plugin bank-mellat">
					  <style>html{direction:rtl;text-align:right;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block;vertical-align:baseline}audio:not([controls]){display:none;height:0}[hidden],template{display:none}a{background-color:transparent}a:active,a:hover{outline:0}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:700}dfn{font-style:italic}h1{font-size:2em;margin:.67em 0}mark{background:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-.5em}sub{bottom:-.25em}img{border:0}svg:not(:root){overflow:hidden}figure{margin:1em 40px}hr{-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;height:0}pre{overflow:auto}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}button,input,optgroup,select,textarea{color:inherit;font:inherit;margin:0}button{overflow:visible}button,select{text-transform:none}button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer}button[disabled],html input[disabled]{cursor:default}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}input{line-height:normal}input[type="checkbox"],input[type="radio"]{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:0}input[type="number"]::-webkit-inner-spin-button,input[type="number"]::-webkit-outer-spin-button{height:auto}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend{border:0;padding:0}textarea{overflow:auto}optgroup{font-weight:700}table{border-collapse:collapse;border-spacing:0}td,th{padding:0}.responstable{margin:1em 0;width:100%;overflow:hidden;background:#FFF;color:#024457;border-radius:10px;border:1px solid #167F92}.responstable tr{border:1px solid #D9E4E6}.responstable tr:nth-child(odd){background-color:#EAF3F3}.responstable th{display:none;border:1px solid #FFF;background-color:#167F92;color:#FFF;padding:1em}.responstable th:first-child{display:table-cell;text-align:center}.responstable th:nth-child(2){display:table-cell}.responstable th:nth-child(2) span{display:none}.responstable th:nth-child(2):after{content:attr(data-th)}@media (min-width: 480px){.responstable th:nth-child(2) span{display:block}.responstable th:nth-child(2):after{display:none}}.responstable td{display:block;word-wrap:break-word;max-width:7em}.responstable td:first-child{display:table-cell;text-align:center;border-right:1px solid #D9E4E6}@media (min-width: 480px){.responstable td{border:1px solid #D9E4E6}}.responstable th,.responstable td{text-align:right;margin:.5em 1em}@media (min-width: 480px){.responstable th,.responstable td{display:table-cell;padding:1em}}body{padding:0 2em;font-family:Arial,sans-serif;color:#024457;background:#f2f2f2}h1{font-family:Verdana;font-weight:400;color:#024457}h1 span{color:#167F92}.right{float:right}.left{float:left}</style>
				   </head>
				   <body>
					  <h1>گزارشات تراکنش های انجام شده</span></h1>
					  <table class="responstable">
						 <tr>
							<th>#</th>
							<th>نام و نام خانوادگي</th>
							<th>آدرس ايميل</th>
							<th>شماره تلفن</th>
							<th>توضيحات</th>
							<th>وضعيت پرداخت</th>
							<th>ستل</th>
							<th>تاريخ</th>
							<th>آي پي</th>
							<th>مبلغ(ريال)</th>
							<th>رسيد ديجيتالي سفارش</th>
							<th>شماره سفارش</th>
							<th>شماره تراکنش</th>
						 </tr>
HTML;

			foreach ($getorder as $order){

				  echo  $actions='
				   <tr>
						<td>'.$order->order_id.'</td>
						<td>'.$order->order_name_surname.'</td>
						<td>'.$order->order_email.'</td>
						<td>'.$order->order_phone.'</td>
						<td>'.$order->order_des.'</td>
						<td>'.$order->order_status.'</td>
						<td>'.$order->order_settle.'</td>
						<td>'.$order->order_date.'</td>
						<td>'.$order->order_ip.'</td>
						<td>'.$order->order_amount.'</td>
						<td>'.$order->order_referenceId.'</td>
						<td>'.$order->order_orderid.'</td>
						<td>'.$order->order_refid.'</td>
					 </tr>
					';
			}
			echo '
				</table>
				<div class="right">تاریخ گزارش گیری: '.date('Y-m-d').'</div>
				<div class="left">تهیه شده توسط <b>افزونه درگاه بانک ملت</b> وردپرس؛ توسعه داده شده توسط گروه <b><a href="http://wp-beginner.ir" target="_blank">Wp Beginner</a></b></div>

			</body>
			</html>';
		}
		
		exit();

	} else {
			echo '-1';echo "<script>window.close();</script>";
	}
}