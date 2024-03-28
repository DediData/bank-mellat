﻿<style>
.basic-grey {
	direction: rtl;
	margin-left: auto;
	margin-right: auto;
	max-width: 500px;
	background: #F7F7F7;
	padding: 25px 15px 25px 10px;
	font: 12px tahoma, "Times New Roman", Times, serif;
	color: #888;
	text-shadow: 1px 1px 1px #FFF;
	border: 1px solid #E4E4E4
}

.basic-grey h1 {
	font-size: 25px;
	padding: 0 0 10px 40px;
	display: block;
	border-bottom: 1px solid #E4E4E4;
	margin: -10px -15px 30px -10px;
	color: #888
}

.basic-grey h1>span {
	display: block;
	font-size: 11px
}

.basic-grey label {
	display: block;
	margin: 0
}

.basic-grey label>span {
	float: right;
	width: 20%;
	text-align: right;
	padding-right: 10px;
	margin-top: 10px;
	color: #888
}

.basic-grey input[type="text"],
input[type="submit"],
input[type="number"],
.basic-grey input[type="email"],
.basic-grey textarea,
.basic-grey select {
	border: 1px solid #DADADA;
	color: #888;
	height: 30px;
	margin-bottom: 16px;
	margin-right: 6px;
	margin-top: 2px;
	outline: 0 none;
	padding: 3px 5px 3px 3px;
	width: 70%;
	font-size: 12px;
	line-height: 15px;
	box-shadow: inset 0 1px 4px #ECECEC;
	-moz-box-shadow: inset 0 1px 4px #ECECEC;
	-webkit-box-shadow: inset 0 1px 4px #ECECEC
}

.basic-grey textarea {
	padding: 5px 3px 3px 5px;
	height: 100px
}

.basic-grey .button {
	background: #E27575;
	border: none;
	padding: 10px 25px;
	color: #FFF;
	box-shadow: 1px 1px 5px #B6B6B6;
	border-radius: 3px;
	text-shadow: 1px 1px 1px #9E3F3F;
	cursor: pointer
}

.basic-grey .button:hover {
	background: #CF7A7A
}
</style>
<form action="" method="post" class="basic-grey">
	<h1>پرداخت آنلاين وجه</h1>
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