<?php
defined('ABSPATH') or die("-1");

$img_url = plugins_url( '../../images/mail/', __FILE__ );

$fields ='<p>شماره سفارش: '. $order->order_id.'</p><p>نام و نام خانوادگي: '.$order->order_name_surname.'</p><p>آدرس ايميل: '.$order->order_email.'</p><p>شماره تلفن: '. $order->order_phone.'</p><p>توضيحات: '.$order->order_des.'</p><p>تاريخ: '.$order->order_date.'</p><p>آي پي: '.$order->order_ip.'</p><p>مبلغ(ريال): '.$order->order_amount.'</p><p>رسيد ديجيتالي سفارش: '.$order->order_referenceId.'</p>';

$message = file_get_contents('wp-content/plugins/bank-mellat/core/inc/order_mail_template.html');
$message = str_replace('%mail_header%', $settings['email_headerText'], $message);
$message = str_replace('%mail_text%', $settings['email_Text'], $message);
$message = str_replace('%mail_logo%', $settings['email_logoUrl'], $message);
$message = str_replace('%mail_link1%', $settings['email_link1'], $message);
$message = str_replace('%mail_link2%', $settings['email_link2'], $message);
$message = str_replace('%mail_link3%', $settings['email_link3'], $message);
$message = str_replace('%mail_adress1%', $settings['email_textLink1'], $message);
$message = str_replace('%mail_adress2%', $settings['email_textLink2'], $message);
$message = str_replace('%mail_adress3%', $settings['email_textLink3'], $message);
$message = str_replace('%mail_footer%', $settings['email_footerText'], $message);
$message = str_replace('%img_url%', $img_url, $message);
$message = str_replace('%fields%', $fields, $message);

$to = get_option( 'admin_email' ) . "," . $order->order_email;

$subject = $settings['email_subject'];

$headers = "From:" . $settings['email_sender'] . "\r\n";
$headers.= "MIME-Version: 1.0\r\n";
$headers.= "Content-Type: text/html; charset=UTF-8\r\n";
$headers.= "FromName: " . $settings['EmailText']. "\r\n";

wp_mail($to, $subject, $message, $headers);
?>