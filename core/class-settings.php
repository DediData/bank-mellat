<?php
class settings{
	
	public function __construct(){

		add_action( 'admin_menu', array( &$this, 'adminMenu' ) );
		add_action( 'admin_init', array( &$this, 'WPBEGPAY_settings_init' ) );
	}
	
	public function adminMenu(){
		
		add_submenu_page( 'bank-mellat', __( 'تنظیمات', 'WPBEGPAY' ), __( 'تنظیمات', 'WPBEGPAY' ), 'manage_options', 'WPBEGPAY-settings', array( &$this, 'settings' ) );
	}

	
	/**
	 * settings page
	 *
	 * @since 1.0.0
	 */
	public function settings() {
?>
		<div class="wrap">
			<?php 
				// Uncomment if this screen isn't added with add_options_page() 
				// settings_errors(); 
			?>
 
			<h2><?php _e('تنظیمات افزونه پرداخت آنلاین', 'WPBEGPAY');?></h2>

			<form method="post" action="options.php">
            
			<?php
				// Output the settings sections.
				do_settings_sections( 'WPBEGPAY_settings' );
 
				// Output the hidden fields, nonce, etc.
				settings_fields( 'WPBEGPAY_settings_group' );
				
				// Default setting's value
				$WPBEGPAY_setting_defualt_values = array(
					'MellatG' => '',
					'MellatG_TerminalNumber' => '',
					'MellatG_TerminalUser' => '',
					'MellatG_TerminalPass' => '',
					'connecting_msg' => 'در حال اتصال به بانک ملت ...</br>لطفا کمی صبر کنید...',
					'cancel_msg' => 'شما از پرداخت هزينه انصراف داديد .',
					'error_msg' => 'در تکمیل انتقال وجه به حساب مشکلی رخ داده است...<br /> مبلغ کسر شده از حساب حداکثر تا 72 دیگر به حساب شما برگشت داده خواهد شد.',
					'invalid_msg' => 'این درخواست از درگاه ملت معتبر شناسایی نشد',
					'successful_msg' => 'پرداخت اینترنتی با موفقیت انجام شد',
					'email_sender' => 'info@yoursite.ir',
					'email_subject' => 'پرداخت وجه با موفقیت انجام شد!',
					'email_logoUrl' => '',
					'email_headerText' => '!خرید شما با موفقیت انجام شد',
					'email_Text' => 'با تشکر از شما پرداخت وجه با موفقیت انجام شد!',
					'email_footerText' => 'این پیغام به صورت خودکار ارسال شده است، لطفا به آن پاسخ ندهید',
					'email_textLink1' => 'صفحه نخست',
					'email_link1' => get_home_url(),
					'email_textLink2' => 'درباره ما',
					'email_link2' => '',
					'email_textLink3' => 'تماس باما',
					'email_link3' => '',
					'sendSms' => 'false',
					'sms_service' => '',
					'adminMobile' => '',
					'Sms_username' => '',
					'Sms_password' => '',
					'sms_lineNumber' => '',
					'Sms_text' => 'پرداخت # با مبلغ $ با موفقیت انجام شد.',
					'form' => 'formA.html'

				);
				
				// retrive setting array from option's.
				$settings = get_option( 'WPBEGPAY_settings_fields_arrays', $WPBEGPAY_setting_defualt_values);
	
				//print settings array.
				//print_r ($settings_gateway);
			?>
			
			<script>
				jQuery(document).ready(function() {
					jQuery(".nav-tab-wrapper a").click(function(event) {
						event.preventDefault();
						jQuery(".nav-tab-wrapper a").removeClass("nav-tab-active");
						jQuery(this).addClass("nav-tab-active");
						var tab = jQuery(this).attr("href");
						jQuery(".tab-content").not(tab).css("display", "none");
						jQuery(tab).fadeIn();
					});
				});
			</script>
			
			<style>
				.grid{
					width: 960px;
					display: block;
					margin: 0 auto;
				}
				#header-container{
					margin-bottom: 30px;
					overflow: hidden
				}
				#body-container{
					clear:both;
					overflow: hidden
					}
				#navigation ul{
					list-style: none;
					margin: 0;
					padding: 0;
				}
				#navigation li{
					float: left;
					list-style: none;
					margin-right: 10px;
				}
				.tab-content {
					display: none;
				}
			</style>
			
			<div id="tabs-container">
			   <h2 class="nav-tab-wrapper">
				  <a class="nav-tab nav-tab-active" href="#General">عمومی</a>
				  <a class="nav-tab" href="#Email">ایمیل</a>
				  <a class="nav-tab" href="#Sms">اس ام اس</a>
				  <a class="nav-tab" href="#Form">فرم</a>
			   </h2>
			   <div id="tab">
				  <div id="General" class="tab-content" style="display:block;">
					 <section id="mellat_gateway">
						<table class="form-table" style="width:650px;">
						   <!-- Grab a hot cup of coffee, yes we're using tables! -->
						   <tr valign="top">
							  <th scope="row"><label for="MellatG_TerminalNumber">شماره ترمینال درگاه </label></th>
							  <td>
								 <input id="MellatG_TerminalNumber" name="WPBEGPAY_settings_fields_arrays[MellatG_TerminalNumber]" type="text" value="<?php esc_attr_e($settings['MellatG_TerminalNumber']); ?>" />
							  </td>
						   </tr>
						   <tr valign="top">
							  <th scope="row"><label for="MellatG_TerminalUser">نام کاربر ترمینال</label></th>
							  <td>
								 <input id="MellatG_TerminalUser" name="WPBEGPAY_settings_fields_arrays[MellatG_TerminalUser]" type="text" value="<?php  esc_attr_e($settings['MellatG_TerminalUser']); ?>" />
							  </td>
						   </tr>
						   <tr valign="top">
							  <th scope="row"><label for="MellatG_TerminalPass">کلمه عبور ترمینال</label></th>
							  <td>
								 <input id="MellatG_TerminalPass" name="WPBEGPAY_settings_fields_arrays[MellatG_TerminalPass]" type="text" value="<?php  esc_attr_e($settings['MellatG_TerminalPass']); ?>" />
							  </td>
						   </tr>
						</table>
					 </section>
					 <table class="form-table" style="width:650px;">
						<tr valign="top">
						   <th scope="row"><label for="connecting_msg">متن انتظار برای اتصال به بانک</label></th>
						   <td>
							  <input id="connecting_msg" name="WPBEGPAY_settings_fields_arrays[connecting_msg]" type="text" value="<?php  esc_attr_e($settings['connecting_msg']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="cancel_msg">متن انصراف از پرداخت</label></th>
						   <td>
							  <input id="cancel_msg" name="WPBEGPAY_settings_fields_arrays[cancel_msg]" type="text" value="<?php  esc_attr_e($settings['cancel_msg']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="error_msg">متن وقوع خطا در پرداخت</label></th>
						   <td>
							  <input id="error_msg" name="WPBEGPAY_settings_fields_arrays[error_msg]" type="text" value="<?php  esc_attr_e($settings['error_msg']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="invalid_msg">پیغام برای پرداخت های غیر معتبر</label></th>
						   <td>
							  <input id="invalid_msg" name="WPBEGPAY_settings_fields_arrays[invalid_msg]" type="text" value="<?php  esc_attr_e($settings['invalid_msg']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="successful_msg">پیغام برای پرداخت های موفق</label></th>
						   <td>
							  <input id="successful_msg" name="WPBEGPAY_settings_fields_arrays[successful_msg]" type="text" value="<?php  esc_attr_e($settings['successful_msg']); ?>" />
						   </td>
						</tr>
					 </table>
				  </div>
				  <div id="Email"  class="tab-content">
					 <table class="form-table">
						<!-- Grab a hot cup of coffee, yes we're using tables! -->
						<tr valign="top">
						   <th scope="row"><label for="email_sender">آدرس ایمیل ارسال کننده</label></th>
						   <td>
							  <input id="email_sender" name="WPBEGPAY_settings_fields_arrays[email_sender]" type="text" value="<?php  esc_attr_e($settings['email_sender']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_subject">موضوع ایمیل</label></th>
						   <td>
							  <input id="email_subject" name="WPBEGPAY_settings_fields_arrays[email_subject]" type="text" value="<?php  esc_attr_e($settings['email_subject']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_logoUrl">آدرس لوگو</label></th>
						   <td>
							  <input id="email_logoUrl" name="WPBEGPAY_settings_fields_arrays[email_logoUrl]" type="text" value="<?php  esc_attr_e($settings['email_logoUrl']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_headerText">سربرگ ایمیل</label></th>
						   <td>
							  <input id="email_headerText" name="WPBEGPAY_settings_fields_arrays[email_headerText]" type="text" value="<?php  esc_attr_e($settings['email_headerText']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_Text">متن ایمیل</label></th>
						   <td>
							  <input id="email_Text" name="WPBEGPAY_settings_fields_arrays[email_Text]" type="text" value="<?php  esc_attr_e($settings['email_Text']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_footerText">پاورقی ایمیل</label></th>
						   <td>
							  <input id="email_footerText" name="WPBEGPAY_settings_fields_arrays[email_footerText]" type="text" value="<?php  esc_attr_e($settings['email_footerText']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_textLink1">نام پیوند 1</label></th>
						   <td>
							  <input id="email_textLink1" name="WPBEGPAY_settings_fields_arrays[email_textLink1]" type="text" value="<?php  esc_attr_e($settings['email_textLink1']); ?>" />
						   </td>
						   <th scope="row"><label for="email_link1">آدرس پیوند 1</label></th>
						   <td>
							  <input id="email_link1" name="WPBEGPAY_settings_fields_arrays[email_link1]" type="text" value="<?php  esc_attr_e($settings['email_link1']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_textLink2">نام پیوند 2</label></th>
						   <td>
							  <input id="email_textLink2" name="WPBEGPAY_settings_fields_arrays[email_textLink2]" type="text" value="<?php  esc_attr_e($settings['email_textLink2']); ?>" />
						   </td>
						   <th scope="row"><label for="email_link2">آدرس پیوند 2</label></th>
						   <td>
							  <input id="email_link2" name="WPBEGPAY_settings_fields_arrays[email_link2]" type="text" value="<?php  esc_attr_e($settings['email_link2']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="email_textLink3">نام پیوند 3</label></th>
						   <td>
							  <input id="email_textLink3" name="WPBEGPAY_settings_fields_arrays[email_textLink3]" type="text" value="<?php  esc_attr_e($settings['email_textLink3']); ?>" />
						   </td>
						   <th scope="row"><label for="email_link3">آدرس پیوند 3</label></th>
						   <td>
							  <input id="email_link3" name="WPBEGPAY_settings_fields_arrays[email_link3]" type="text" value="<?php  esc_attr_e($settings['email_link3']); ?>" />
						   </td>
						</tr>
					 </table>
				  </div>
				  <div id="Sms"  class="tab-content">
					 <table class="form-table" style="width:650px;">
						<tr valign="top">
						   <th scope="row">ارسال پیامک در زمان اتمام پرداخت</th>
						   <td>
							  <select dir="rtl" name="WPBEGPAY_settings_fields_arrays[sendSms]">
								 <option value="true" <?php echo ($settings['sendSms'] == 'true') ?  'selected' : ''; ?>>بله</option>
								 <option value="false" <?php echo ($settings['sendSms'] == 'false') ? 'selected' : '';  ?>>خیر</option>
							  </select>
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row">سرویس دهنده پیامک</th>
						   <td>
							  <select dir="rtl"  name="WPBEGPAY_settings_fields_arrays[sms_service]">
								 <option value="sabapyamak" <?php echo ($settings['sms_service'] == 'sabapyamak') ? 'selected' : '';  ?>>صبا پیامک</option>
								 <option value="iransmspanel" <?php echo ($settings['sms_service'] == 'iransmspanel') ? 'selected' : '';  ?>>ایران اس ام اس پنل</option>
								 <option value="relax" <?php echo ($settings['sms_service'] == 'relax') ? 'selected' : '';  ?>>ریلکس</option>
								 <option value="farapayamak" <?php echo ($settings['sms_service'] == 'farapayamak') ? 'selected' : '';  ?>>فراپیامک</option>
								 <option value="limoosms" <?php echo ($settings['sms_service'] == 'limoosms') ? 'selected' : '';  ?>>لیمو اس ام اس</option>
								 <option value="aminsms" <?php echo ($settings['sms_service'] == 'aminsms') ? 'selected' : '';  ?>>پیامک امین</option>
								 <option value="payamafraz" <?php echo ($settings['sms_service'] == 'payamafraz') ? 'selected' : '';  ?>>پیام افراز</option>
								 <option value="parandsms" <?php echo ($settings['sms_service'] == 'parandsms') ? 'selected' : '';  ?>>پرند پیامک</option>
								 <option value="mizbansms" <?php echo ($settings['sms_service'] == 'mizbansms') ? 'selected' : '';  ?>>میزبان پیامک</option>
								 <option value="shabnam1" <?php echo ($settings['sms_service'] == 'shabnam1') ? 'selected' : '';  ?>>iransms - iransms.cc</option>
								 <option value="shabnam1" <?php echo ($settings['sms_service'] == 'shabnam1') ? 'selected' : '';  ?>>سامانه پیام کوتاه شبنم - shabnam.co</option>
								 <option value="payamresan" <?php echo ($settings['sms_service'] == 'payamresan') ? 'selected' : '';  ?>>سامانه پیامک پیام رسان - payam-resan.com</option>
								 <option value="payamgah" <?php echo ($settings['sms_service'] == 'payamgah') ? 'selected' : '';  ?>>سامانه پیامک پیامگاه - payamgah.net</option>
								 <option value="melipayamak" <?php echo ($settings['sms_service'] == 'melipayamak') ? 'selected' : '';  ?>>ملی پیامک</option>
								 <option value="hostiran" <?php echo ($settings['sms_service'] == 'hostiran') ? 'selected' : '';  ?>>هاست ایران</option>
								 <option value="shabnam" <?php echo ($settings['sms_service'] == 'shabnam') ? 'selected' : '';  ?>>شبنم</option>
								 <option value="mediana" <?php echo ($settings['sms_service'] == 'mediana') ? 'selected' : '';  ?>>مدیانا</option>
								 <option value="fpayamak" <?php echo ($settings['sms_service'] == 'fpayamak') ? 'selected' : '';  ?>>پیامکده</option>
								 <option value="diakosms" <?php echo ($settings['sms_service'] == 'diakosms') ? 'selected' : '';  ?>>دیاکو پیامک</option>
								 <option value="samanpayamak" <?php echo ($settings['sms_service'] == 'samanpayamak') ? 'selected' : '';  ?>>سامان پیامک</option>
								 <option value="idehsms" <?php echo ($settings['sms_service'] == 'idehsms') ? 'selected' : '';  ?>>ایده اس ام اس</option>
								 <option value="banehsms" <?php echo ($settings['sms_service'] == 'banehsms') ? 'selected' : '';  ?>>بانه اس ام اس</option>
								 <option value="irpayamak" <?php echo ($settings['sms_service'] == 'irpayamak') ? 'selected' : '';  ?>>پیامک ایرانیان</option>
								 <option value="freersms" <?php echo ($settings['sms_service'] == 'freersms') ? 'selected' : '';  ?>>فریر اس ام اس</option>
								 <option value="panizsms" <?php echo ($settings['sms_service'] == 'panizsms') ? 'selected' : '';  ?>>پانیز اس ام اس</option>
								 <option value="mcisms" <?php echo ($settings['sms_service'] == 'mcisms') ? 'selected' : '';  ?>>ایران اس ام اس</option>
								 <option value="smsclick" <?php echo ($settings['sms_service'] == 'smsclick') ? 'selected' : '';  ?>>اس ام اس کلیک</option>
								 <option value="textsms" <?php echo ($settings['sms_service'] == 'textsms') ? 'selected' : '';  ?>>Text Sms</option>
								 <option value="sgmsms" <?php echo ($settings['sms_service'] == 'sgmsms') ? 'selected' : '';  ?>>سیگما اس ام اس</option>
								 <option value="persiansms" <?php echo ($settings['sms_service'] == 'persiansms') ? 'selected' : '';  ?>>پرشین اس ام اس</option>
								 <option value="spadsms" <?php echo ($settings['sms_service'] == 'spadsms') ? 'selected' : '';  ?>>اسـپــاد اس ام اس</option>
								 <option value="idehsms3000" <?php echo ($settings['sms_service'] == 'idehsms3000') ? 'selected' : '';  ?>>ایده اس ام اس (3000)</option>
								 <option value="f2usms" <?php echo ($settings['sms_service'] == 'f2usms') ? 'selected' : '';  ?>>f2u</option>
								 <option value="f2usms2" <?php echo ($settings['sms_service'] == 'f2usms2') ? 'selected' : '';  ?>>Panel2u</option>
								 <option value="wstdsms" <?php echo ($settings['sms_service'] == 'wstdsms') ? 'selected' : '';  ?>>WebStudio</option>
								 <option value="hezarnevis" <?php echo ($settings['sms_service'] == 'hezarnevis') ? 'selected' : '';  ?>>hezarnevis</option>
							  </select>
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="adminMobile">شماره موبایل مدیر وبسایت</label></th>
						   <td>
							  <input id="adminMobile" name="WPBEGPAY_settings_fields_arrays[adminMobile]" type="text" value="<?php  esc_attr_e($settings['adminMobile']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="Sms_username">نام کاربری پنل پیامک</label></th>
						   <td>
							  <input id="Sms_username" name="WPBEGPAY_settings_fields_arrays[Sms_username]" type="text" value="<?php  esc_attr_e($settings['Sms_username']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="Sms_password">کلمه عبور پنل پیامک</label></th>
						   <td>
							  <input id="Sms_password" name="WPBEGPAY_settings_fields_arrays[Sms_password]" type="text" value="<?php  esc_attr_e($settings['Sms_password']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="sms_lineNumber">شماره خط اختصاصی</label></th>
						   <td>
							  <input id="sms_lineNumber" name="WPBEGPAY_settings_fields_arrays[sms_lineNumber]" type="text" value="<?php  esc_attr_e($settings['sms_lineNumber']); ?>" />
						   </td>
						</tr>
						<tr valign="top">
						   <th scope="row"><label for="Sms_text">متن پیامک ارسالی</label></th>
						   <td>
							  <input id="Sms_text" name="WPBEGPAY_settings_fields_arrays[Sms_text]" type="text" value="<?php  esc_attr_e($settings['Sms_text']); ?>" />
						   </td>
						</tr>
					 </table>
				  </div>
				  <div id="Form"  class="tab-content">
					 <table class="form-table" style="width:650px;">
						<tr valign="top">
						   <th scope="row">فرم پرداخت</th>
						   <td>
							  <?php 
								 $defualtThemes = scandir(plugin_dir_path( __FILE__ ) . "/../forms");
								 
								 foreach($defualtThemes as $theme){
									if(preg_match('/.html/', $theme)){
								 ?>
							  <input type="radio" name="WPBEGPAY_settings_fields_arrays[form]" value="<?php echo $theme; ?>" <?php if($settings['form'] == $theme) echo 'checked="checked"'; ?> />فرم <?php echo $theme;?><br>
							  <?php
								 }
								 }
								 
								 if(is_dir(WP_CONTENT_DIR . "/WPBEGPAY")){
								 
								 $usersThemes = scandir(WP_CONTENT_DIR . "/WPBEGPAY");
								 
								 foreach($usersThemes as $theme){
									if(preg_match('/.html/', $theme)){
								 ?>
							  <input type="radio" name="WPBEGPAY_settings_fields_arrays[form]" value="<?php echo $theme; ?>" <?php if($settings['form'] == $theme) echo 'checked="checked"'; ?> />فرم <?php echo $theme;?><br>
							  <?php
								 }
								 }
								 }
								 ?>
						   </td>
						</tr>
					 </table>
				  </div>
			   </div>
			</div>
			
			<?php submit_button(); ?>
			
			</form>
			</div>
<?php
	}
	
	public function WPBEGPAY_settings_init() {
 
		 //setting section
		add_settings_section(
			'WPBEGPAY_settings_section',
			null,
			array(&$this ,'WPBEGPAY_settings_section_callback'),
			'WPBEGPAY_settings'
		);
		 
		// setting field
		add_settings_field(
			'WPBEGPAY_settings_field_array',
			null,
			array(&$this ,'WPBEGPAY_settings_array'),
			'WPBEGPAY_settings',
			'WPBEGPAY_settings_section'
		);
	 
		// Register this field with our settings group.
		register_setting( 'WPBEGPAY_settings_group', 'WPBEGPAY_settings_fields_arrays' );
	}

	public function WPBEGPAY_settings_section_callback() {
		
	}

	/** Field 1 Input **/
	public function WPBEGPAY_settings_array() {
		
	}

}
