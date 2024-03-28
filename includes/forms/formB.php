﻿<style>
.elegant-aero {
	direction: rtl;
	margin-left: auto;
	margin-right: auto;
	max-width: 500px;
	background: #D2E9FF;
	padding: 20px;
	font: 12px tahoma, Arial, Helvetica, sans-serif;
	color: #666
}

.elegant-aero h1 {
	font: 24px tahoma, Arial, Helvetica, sans-serif;
	padding: 10px 10px 10px 20px;
	display: block;
	background: #C0E1FF;
	border-bottom: 1px solid #B8DDFF;
	margin: -20px -20px 15px
}

.elegant-aero h1>span {
	display: block;
	font-size: 11px
}

.elegant-aero label>span {
	float: right;
	margin-top: 10px;
	color: #5E5E5E
}

.elegant-aero label {
	display: block;
	margin: 0 0 5px
}

.elegant-aero label>span {
	float: right;
	width: 20%;
	text-align: right;
	padding-right: 15px;
	margin-top: 10px;
	font-weight: 700
}

.elegant-aero input[type="text"],
input[type="number"],
input[type="submit"],
.elegant-aero input[type="email"],
.elegant-aero textarea,
.elegant-aero select {
	color: #888;
	width: 70%;
	padding: 0 5px 0 0;
	border: 1px solid #C5E2FF;
	background: #FBFBFB;
	outline: 0;
	-webkit-box-shadow: inset 0 1px 6px #ECF3F5;
	box-shadow: inset 0 1px 6px #ECF3F5;
	font: 200 12px/25px tahoma, Arial, Helvetica, sans-serif;
	height: 30px;
	line-height: 15px;
	margin: 2px 6px 16px 0
}

.elegant-aero textarea {
	height: 100px;
	padding: 5px 0 0 5px;
	width: 70%
}

.elegant-aero select {
	background: #fbfbfb url(down-arrow.png) no-repeat right;
	background: #fbfbfb url(down-arrow.png) no-repeat right;
	appearance: none;
	-webkit-appearance: none;
	-moz-appearance: none;
	text-indent: .01px;
	width: 70%
}

.elegant-aero .button {
	padding: 10px 30px;
	background: #66C1E4;
	border: none;
	color: #FFF;
	box-shadow: 1px 1px 1px #4C6E91;
	-webkit-box-shadow: 1px 1px 1px #4C6E91;
	-moz-box-shadow: 1px 1px 1px #4C6E91;
	text-shadow: 1px 1px 1px #5079A3
}

.elegant-aero .button:hover {
	background: #3EB1DD
}
</style>
<form dir="rtl" action="" method="post" class="elegant-aero">
	<h1>پرداخت آنلاین وجه</h1>
	<p>
		<label><span>نام&nbsp;و&nbsp;نام&nbsp;خانوادگی</span><input type="text" name="bank_mellat_name_family"
				required /></label>
		<label><span>شماره&nbsp;تلفن</span><input type="number" name="bank_mellat_phone" required /></label>
		<label><span>ايميل:</span><input type="email" name="bank_mellat_email" required /></label>
		<label><span>مبلغ(ریال):</span><input type="number" name="bank_mellat_price" required /></label>
		<label><span>توضيحات:</span><input type="text" name="bank_mellat_description" required /></label>
		<label><span>&nbsp;</span><input type="submit" class="button" value="پرداخت"></label>
	</p>
</form>