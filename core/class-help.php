<?php
class help{
	
	public function __construct(){

		add_action( 'admin_menu', array( &$this, 'adminMenu' ) );
		
	}
	
	public function adminMenu(){
		
		add_submenu_page( 'bank-mellat', __( 'راهنما افزونه پرداخت آنلاین', 'bank-mellat' ), __( 'راهنما', 'bank-mellat' ), 'manage_options', 'WPBEGPAY-help', array( &$this, 'help' ) );
	}
	
	/**
	 * Orders page
	 *
	 * @since 2.7
	 */
	public function help() { ?>
		<div class="wrap">
			<h2>
				<?php _e( 'راهنما افزونه پرداخت آنلاین', 'bank-mellat' ); ?>
			</h2>
			<div id="bank-mellat-help">
				<div class="accordion">
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-1">آدرس وبسایت سرویس دهنده های پیامک</a>
						<div id="accordion-1" class="accordion-section-content">
							<p>برای ورود به وبسایت سرویس دهنده کلیک کنید</br></br>
								<a href="http://www.diakosms.ir" target="_blank">دیاکو اس ام اس</a></br>
								<a href="http://sms.f2u.ir" target="_blank">f2usms</a></br>
								<a href="http://sms.panel2u.ir" target="_blank">f2usms2</a></br>
								<a href="http://login.payamakde.com" target="_blank">پیامکده</a></br>
								<a href="http://sms.freer.ir" target="_blank">فریر اس ام اس</a></br>
								<a href="http://panel.hezarnevis.com" target="_blank">hezarnevis</a></br>
								<a href="http://sms.idehsms.ir" target="_blank">ایده اس ام اس</a></br>
								<a href="http://ws.idehsms.ir" target="_blank">ایده اس ام اس (3000)</a></br>
								<a href="http://ir-payamak.com" target="_blank">پیامک ایرانیان</a></br>
								<a href="http://panel.panizsms.com" target="_blank">پانیز اس ام اس</a></br>
								<a href="http://www.persiansms.info" target="_blank">پرشین اس ام اس</a></br>
								<a href="http://www.p.mcisms.net" target="_blank">ایران اس ام اس</a></br>
								<a href="http://samanpayamak.ir" target="_blank">سامان پیامک</a></br>
								<a href="http://panel.sigmasms.ir" target="_blank">سیگما اس ام اس</a></br>
								<a href="http://banehsms.ir" target="_blank">بانه اس ام اس</a></br>
								<a href="http://shabnam-sms.ir" target="_blank">شبنم</a></br>
								<a href="http://sms.dorbid.ir" target="_blank">اس ام اس کلیک</a></br>
								<a href="http://spadsms.net/" target="_blank">اسـپــاد اس ام اس</a></br>
								<a href="http://sms.webstudio.ir/" target="_blank">wstdsms</a>
							</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-2">انجام تنظیمات ایمیل</a>
						<div id="accordion-2" class="accordion-section-content">
							<p>
							<p>در تصویر زیر قسمت های ایمیل توضیح داده شده است</p>
							</br>
							<img src="http://help.pay-system.ir/wp-content/uploads/2016/03/Email_Help.jpg" alt="" width="629px" height="811px" /></p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-3">شرح خطا های احتمالی در هنگام انجام پرداخت (بانک ملت)</a>
						<div id="accordion-3" class="accordion-section-content">
							<p>
							<table dir="" style="text-align: right;" border="1" width="416">
								<tbody>
									<tr>
										<td width="40">ردیف</td>
										<td width="308">توضیحات</td>
										<td width="46">کد خطا</td>
									</tr>
									<tr>
										<td>1</td>
										<td>تراکنش باموفقیت انجام شد</td>
										<td>0</td>
									</tr>
									<tr>
										<td>2</td>
										<td>شماره كارت نامعتبر است</td>
										<td>11</td>
									</tr>
									<tr>
										<td>3</td>
										<td>تراکنش refund یافت نشد.</td>
										<td>12</td>
									</tr>
									<tr>
										<td>4</td>
										<td>رمز نادرست است</td>
										<td>13</td>
									</tr>
									<tr>
										<td>5</td>
										<td>تعداد دفعات وارد كردن رمز بیش از حد مجاز است</td>
										<td>14</td>
									</tr>
									<tr>
										<td>6</td>
										<td>كارت نامعتبر است</td>
										<td>15</td>
									</tr>
									<tr>
										<td>7</td>
										<td>كاربر از انجام تراكنش منصرف شده است</td>
										<td>17</td>
									</tr>
									<tr>
										<td>8</td>
										<td>تاریخ انقضای كارت گذشته است</td>
										<td>18</td>
									</tr>
									<tr>
										<td>9</td>
										<td>صادر كننده كارت نامعتبر است</td>
										<td>111</td>
									</tr>
									<tr>
										<td>10</td>
										<td>خطای سوییچ صادر كننده كارت</td>
										<td>112</td>
									</tr>
									<tr>
										<td>11</td>
										<td>پاسخی از صادر كننده كارت دریافت نشد</td>
										<td>113</td>
									</tr>
									<tr>
										<td>12</td>
										<td>دارنده كارت مجاز به انجام این تراكنش نیست</td>
										<td>114</td>
									</tr>
									<tr>
										<td>13</td>
										<td>پذیرنده نامعتبر است</td>
										<td>21</td>
									</tr>
									<tr>
										<td>14</td>
										<td>ترمینال مجوز ارایه سرویس درخواستی را ندارد.</td>
										<td>22</td>
									</tr>
									<tr>
										<td>15</td>
										<td>خطای امنیتی رخ داده است</td>
										<td>23</td>
									</tr>
									<tr>
										<td>16</td>
										<td>اطلاعات كاربری پذیرنده نامعتبر است</td>
										<td>24</td>
									</tr>
									<tr>
										<td>17</td>
										<td>مبلغ نامعتبر است</td>
										<td>25</td>
									</tr>
									<tr>
										<td>18</td>
										<td>پاسخ نامعتبر است</td>
										<td>31</td>
									</tr>
									<tr>
										<td>19</td>
										<td>فرمت اطلاعات وارد شده صحیح نمی باشد</td>
										<td>32</td>
									</tr>
									<tr>
										<td>20</td>
										<td>حساب نامعتبر است</td>
										<td>33</td>
									</tr>
									<tr>
										<td>21</td>
										<td>خطای سیستمی</td>
										<td>34</td>
									</tr>
									<tr>
										<td>22</td>
										<td>تاریخ نامعتبر است</td>
										<td>35</td>
									</tr>
									<tr>
										<td>23</td>
										<td>شماره درخواست تكراری است</td>
										<td>41</td>
									</tr>
									<tr>
										<td>24</td>
										<td>تراکنش sale یافت نشد.</td>
										<td>42</td>
									</tr>
									<tr>
										<td>25</td>
										<td>قبلا درخواست verify داده شده است.</td>
										<td>43</td>
									</tr>
									<tr>
										<td>26</td>
										<td>درخواست verify یافت نشد.</td>
										<td>44</td>
									</tr>
									<tr>
										<td>27</td>
										<td>تراکنش settle شده است.</td>
										<td>45</td>
									</tr>
									<tr>
										<td>28</td>
										<td>تراکنش settle نشده است.</td>
										<td>46</td>
									</tr>
									<tr>
										<td>29</td>
										<td>تراکنش settle یافت نشد.</td>
										<td>47</td>
									</tr>
									<tr>
										<td>30</td>
										<td>تراکنش reverse شده است.</td>
										<td>48</td>
									</tr>
									<tr>
										<td>31</td>
										<td>تراکنش refund یافت نشد.</td>
										<td>49</td>
									</tr>
									<tr>
										<td>32</td>
										<td>شناسه قبض نادرست است</td>
										<td>412</td>
									</tr>
									<tr>
										<td>33</td>
										<td>شناسه پرداخت نادرست است</td>
										<td>413</td>
									</tr>
									<tr>
										<td>34</td>
										<td>سازمان صادر كننده قبض نامعتبر است</td>
										<td>414</td>
									</tr>
									<tr>
										<td>35</td>
										<td>زمان جلسه كاری به پایان رسیده است</td>
										<td>415</td>
									</tr>
									<tr>
										<td>36</td>
										<td>خطا در ثبت اطلاعات</td>
										<td>416</td>
									</tr>
									<tr>
										<td>37</td>
										<td>شناسه پرداخت كننده نامعتبر است</td>
										<td>417</td>
									</tr>
									<tr>
										<td>38</td>
										<td>اشكال در تعریف اطلاعات مشتری</td>
										<td>418</td>
									</tr>
									<tr>
										<td>39</td>
										<td>تعداد دفعات ورود اطلاعات از حد مجاز گذشته است</td>
										<td>419</td>
									</tr>
									<tr>
										<td>40</td>
										<td>آی پی نامعتبر است</td>
										<td>421</td>
									</tr>
									<tr>
										<td>41</td>
										<td>تراكنش تكراری است</td>
										<td>51</td>
									</tr>
									<tr>
										<td>42</td>
										<td>سرویس درخواستی موجود نمی باشد</td>
										<td>52</td>
									</tr>
									<tr>
										<td>43</td>
										<td>تراكنش مرجع موجود نیست</td>
										<td>54</td>
									</tr>
									<tr>
										<td>44</td>
										<td>تراكنش نامعتبر است</td>
										<td>55</td>
									</tr>
									<tr>
										<td>45</td>
										<td>خطا در واریز</td>
										<td>61</td>
									</tr>
								</tbody>
							</table>
							</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-4">ستل(settel) چیست؟</a>
						<div id="accordion-4" class="accordion-section-content">
							<p>ستل در واقع یک تابع است که بعد از واریز وجه توسط کاربر و برگشت به سایت فروشنده ، این تابع اجرا میشه و دستوری به سرور بانک ارسال میکنه ، این دستور به سرور بانک اعلام میکنه که وجه پرداخت شده توسط مشتری رو به حساب مدیر سایت(فروشنده) واریز کنه بنابراین به محض اجرای تابع ستل وجه به صورت آنی به حساب مدیر سایت واریز میشه و قابل برداشت خواهد بود.
							<div class="alert alert-error">توجه! فقط تراکنش های پرداختی توسط بانک ملت نیازمند ستل کردن هستند و دیگر درگاه ها نیاز به ستل کردن ندارند</div>
							<div class="alert alert-error">توجه! اگر تراکنش موفقی ستل نشود به حساب پذیرنده واریز نمی شود!</div>
							</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-5">چگونه از این افزونه استفاده کنم؟</a>
						<div id="accordion-5" class="accordion-section-content">
							<p>برای استفاده از افزونه بانک ملت کافی است کد میانبر [WPBEGPAY_SC] را در نوشته ها و یا برگه های خود قرار دهید.</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-6">مشکل در افزونه</a>
						<div id="accordion-6" class="accordion-section-content">
							<p>در صورت وجود هرگونه مشکل در افزونه آن را در <a href="http://wp-beginner.ir/%d8%a7%d9%81%d8%b2%d9%88%d9%86%d9%87-%d8%af%d8%b1%da%af%d8%a7%d9%87-%d9%be%d8%b1%d8%af%d8%a7%d8%ae%d8%aa-%d8%a8%d8%a7%d9%86%da%a9-%d9%85%d9%84%d8%aa-%d9%88%d8%b1%d8%af%d9%be%d8%b1%d8%b3/">صفحه افزونه</a> در وبسایت <a href="http://wp-beginner.ir">آموزش وردپرس</a> در بخش دیدگاه ها اعلام نمایید.</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-7">استفاده از فرم سفارشی</a>
						<div id="accordion-7" class="accordion-section-content">
							<p>برای استفاده از فرم سفارشی خودتان براساس یکی از فرم های موجود افزونه که در پوشه forms موجود هستند، فرم خود را ویرایش کرده و سپس در پوشه wp-content وردپرس پوشه ای به نام WPBEGPAY ایجاد کرده و فرم خود را درون آن قرار دهید.
								هم اکنون در تنظیمات افزونه می توانید فرم را فعال نمایید.
							</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion-8">افزودن سرویس دهنده پیامک</a>
						<div id="accordion-8" class="accordion-section-content">
							<p>برای افزودن سرویس دهنده پیامک مورد نظر شما به افزونه لطفا مستندات مربوط به سرویس دهنده پیامک را به ایمیل info@wp-beginner.ir ارسال نمایید.</p>
						</div>
						<!--end .accordion-section-content-->
					</div>
					<!--end .accordion-section-->									
				</div>
			</div>
		</div><?php
	}
	
}
