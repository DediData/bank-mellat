<?php
/**
 * Bank Mellat Settings Main Class
 * 
 * @package Bank_Mellat
 */

declare(strict_types=1);

namespace BankMellat;

/**
 * Class Bank_Mellat_Settings
 */
final class Bank_Mellat_Settings extends \DediData\Singleton {

	/**
	 * Plugin Folder
	 * 
	 * @var string $plugin_folder
	 */
	protected $plugin_folder;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_folder = BANK_MELLAT()->get( 'plugin_folder' );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/**
	 * Admin Menu
	 * 
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'bank-mellat', 'تنظیمات', 'تنظیمات', 'manage_options', 'bank-mellat-settings', array( $this, 'settings' ) );
	}

	/**
	 * Settings page
	 * 
	 * @return void
	 */
	public function settings() {
		?>
		<div class="wrap">
			<?php
				// Uncomment if this screen isn't added with add_options_page()
				// settings_errors();
			?>
			<h2>تنظیمات افزونه پرداخت آنلاین</h2>
			<form method="post" action="options.php">
			<?php
				// Output the settings sections.
				do_settings_sections( 'bank_mellat_settings' );
				// Output the hidden fields, nonce, etc.
				settings_fields( 'bank_mellat_settings_group' );
				// Default setting's value
				$default_settings = array(
					'MellatG'                => '',
					'MellatG_TerminalNumber' => '',
					'MellatG_TerminalUser'   => '',
					'MellatG_TerminalPass'   => '',
					'connecting_msg'         => 'در حال اتصال به بانک ملت ...<br />لطفا کمی صبر کنید...',
					'cancel_msg'             => 'شما از پرداخت هزينه انصراف داديد.',
					'error_msg'              => 'در تکمیل انتقال وجه به حساب مشکلی رخ داده است...<br /> مبلغ کسر شده از حساب حداکثر تا 72 دیگر به حساب شما برگشت داده خواهد شد.',
					'invalid_msg'            => 'این درخواست از درگاه ملت معتبر شناسایی نشد',
					'successful_msg'         => 'پرداخت اینترنتی با موفقیت انجام شد',
					'email_sender'           => 'info@yoursite.ir',
					'email_subject'          => 'پرداخت وجه با موفقیت انجام شد!',
					'email_logoUrl'          => '',
					'email_headerText'       => 'خرید شما با موفقیت انجام شد!',
					'email_Text'             => 'با تشکر از شما پرداخت وجه با موفقیت انجام شد!',
					'email_footerText'       => 'این پیغام به صورت خودکار ارسال شده است، لطفا به آن پاسخ ندهید',
					'email_textLink1'        => 'صفحه نخست',
					'email_link1'            => get_home_url(),
					'email_textLink2'        => 'درباره ما',
					'email_link2'            => '',
					'email_textLink3'        => 'تماس با ما',
					'email_link3'            => '',
					'sendSms'                => 'false',
					'sms_service'            => '',
					'adminMobile'            => '',
					'Sms_username'           => '',
					'Sms_password'           => '',
					'sms_lineNumber'         => '',
					'Sms_text'               => 'پرداخت # با مبلغ $ با موفقیت انجام شد.',
					'form'                   => 'formA.php',
				);
				
				// retrieve setting array from option's.
				$settings = get_option( 'bank_mellat_settings_fields_arrays', $default_settings );
	
				// print settings array.
				// print_r ( $settings_gateway );
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
					#header-container {
						margin-bottom: 30px;
						overflow: hidden
					}
					#body-container {
						clear:both;
						overflow: hidden
						}
					#navigation ul {
						list-style: none;
						margin: 0;
						padding: 0;
					}
					#navigation li {
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
									<tr valign="top">
										<th scope="row"><label for="MellatG_TerminalNumber">شماره ترمینال درگاه </label></th>
										<td>
											<input id="MellatG_TerminalNumber" name="bank_mellat_settings_fields_arrays[MellatG_TerminalNumber]" type="text" value="<?php esc_attr( $settings['MellatG_TerminalNumber'] ); ?>" />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><label for="MellatG_TerminalUser">نام کاربر ترمینال</label></th>
										<td><input id="MellatG_TerminalUser" name="bank_mellat_settings_fields_arrays[MellatG_TerminalUser]" type="text" value="<?php esc_attr( $settings['MellatG_TerminalUser'] ); ?>" /></td>
									</tr>
									<tr valign="top">
										<th scope="row"><label for="MellatG_TerminalPass">کلمه عبور ترمینال</label></th>
										<td><input id="MellatG_TerminalPass" name="bank_mellat_settings_fields_arrays[MellatG_TerminalPass]" type="text" value="<?php esc_attr( $settings['MellatG_TerminalPass'] ); ?>" /></td>
									</tr>
								</table>
							</section>
							<table class="form-table" style="width:650px;">
								<tr valign="top">
									<th scope="row"><label for="connecting_msg">متن انتظار برای اتصال به بانک</label></th>
									<td><input id="connecting_msg" name="bank_mellat_settings_fields_arrays[connecting_msg]" type="text" value="<?php esc_attr( $settings['connecting_msg'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="cancel_msg">متن انصراف از پرداخت</label></th>
									<td><input id="cancel_msg" name="bank_mellat_settings_fields_arrays[cancel_msg]" type="text" value="<?php esc_attr( $settings['cancel_msg'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="error_msg">متن وقوع خطا در پرداخت</label></th>
									<td><input id="error_msg" name="bank_mellat_settings_fields_arrays[error_msg]" type="text" value="<?php esc_attr( $settings['error_msg'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="invalid_msg">پیغام برای پرداخت های غیر معتبر</label></th>
									<td><input id="invalid_msg" name="bank_mellat_settings_fields_arrays[invalid_msg]" type="text" value="<?php esc_attr( $settings['invalid_msg'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="successful_msg">پیغام برای پرداخت های موفق</label></th>
									<td><input id="successful_msg" name="bank_mellat_settings_fields_arrays[successful_msg]" type="text" value="<?php esc_attr( $settings['successful_msg'] ); ?>" /></td>
								</tr>
							</table>
						</div>
						<div id="Email" class="tab-content">
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><label for="email_sender">آدرس ایمیل ارسال کننده</label></th>
									<td><input id="email_sender" name="bank_mellat_settings_fields_arrays[email_sender]" type="text" value="<?php esc_attr( $settings['email_sender'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_subject">موضوع ایمیل</label></th>
									<td><input id="email_subject" name="bank_mellat_settings_fields_arrays[email_subject]" type="text" value="<?php esc_attr( $settings['email_subject'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_logoUrl">آدرس لوگو</label></th>
									<td><input id="email_logoUrl" name="bank_mellat_settings_fields_arrays[email_logoUrl]" type="text" value="<?php esc_attr( $settings['email_logoUrl'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_headerText">سربرگ ایمیل</label></th>
									<td><input id="email_headerText" name="bank_mellat_settings_fields_arrays[email_headerText]" type="text" value="<?php esc_attr( $settings['email_headerText'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_Text">متن ایمیل</label></th>
									<td><input id="email_Text" name="bank_mellat_settings_fields_arrays[email_Text]" type="text" value="<?php esc_attr( $settings['email_Text'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_footerText">پاورقی ایمیل</label></th>
									<td><input id="email_footerText" name="bank_mellat_settings_fields_arrays[email_footerText]" type="text" value="<?php esc_attr( $settings['email_footerText'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_textLink1">نام پیوند 1</label></th>
									<td><input id="email_textLink1" name="bank_mellat_settings_fields_arrays[email_textLink1]" type="text" value="<?php esc_attr( $settings['email_textLink1'] ); ?>" /></td>
									<th scope="row"><label for="email_link1">آدرس پیوند 1</label></th>
									<td><input id="email_link1" name="bank_mellat_settings_fields_arrays[email_link1]" type="text" value="<?php esc_attr( $settings['email_link1'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_textLink2">نام پیوند 2</label></th>
									<td><input id="email_textLink2" name="bank_mellat_settings_fields_arrays[email_textLink2]" type="text" value="<?php esc_attr( $settings['email_textLink2'] ); ?>" /></td>
									<th scope="row"><label for="email_link2">آدرس پیوند 2</label></th>
									<td><input id="email_link2" name="bank_mellat_settings_fields_arrays[email_link2]" type="text" value="<?php esc_attr( $settings['email_link2'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="email_textLink3">نام پیوند 3</label></th>
									<td><input id="email_textLink3" name="bank_mellat_settings_fields_arrays[email_textLink3]" type="text" value="<?php esc_attr( $settings['email_textLink3'] ); ?>" /></td>
									<th scope="row"><label for="email_link3">آدرس پیوند 3</label></th>
									<td><input id="email_link3" name="bank_mellat_settings_fields_arrays[email_link3]" type="text" value="<?php esc_attr( $settings['email_link3'] ); ?>" /></td>
								</tr>
							</table>
						</div>
						<div id="Sms" class="tab-content">
							<table class="form-table" style="width:650px;">
								<tr valign="top">
									<th scope="row">ارسال پیامک در زمان اتمام پرداخت</th>
									<td>
										<select name="bank_mellat_settings_fields_arrays[sendSms]">
											<option value="true" <?php echo 'true' === $settings['sendSms'] ? 'selected' : ''; ?>>بله</option>
											<option value="false" <?php echo 'false' === $settings['sendSms'] ? 'selected' : ''; ?>>خیر</option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">سرویس دهنده پیامک</th>
									<td>
										<select dir="rtl"  name="bank_mellat_settings_fields_arrays[sms_service]">
											<option value="sabapyamak" <?php echo 'sabapyamak' === $settings['sms_service'] ? 'selected' : ''; ?>>صبا پیامک</option>
											<option value="iransmspanel" <?php echo 'iransmspanel' === $settings['sms_service'] ? 'selected' : ''; ?>>ایران اس ام اس پنل</option>
											<option value="relax" <?php echo 'relax' === $settings['sms_service'] ? 'selected' : ''; ?>>ریلکس</option>
											<option value="farapayamak" <?php echo 'farapayamak' === $settings['sms_service'] ? 'selected' : ''; ?>>فراپیامک</option>
											<option value="limoosms" <?php echo 'limoosms' === $settings['sms_service'] ? 'selected' : ''; ?>>لیمو اس ام اس</option>
											<option value="aminsms" <?php echo 'aminsms' === $settings['sms_service'] ? 'selected' : ''; ?>>پیامک امین</option>
											<option value="payamafraz" <?php echo 'payamafraz' === $settings['sms_service'] ? 'selected' : ''; ?>>پیام افراز</option>
											<option value="parandsms" <?php echo 'parandsms' === $settings['sms_service'] ? 'selected' : ''; ?>>پرند پیامک</option>
											<option value="mizbansms" <?php echo 'mizbansms' === $settings['sms_service'] ? 'selected' : ''; ?>>میزبان پیامک</option>
											<option value="shabnam1" <?php echo 'shabnam1' === $settings['sms_service'] ? 'selected' : ''; ?>>iransms - iransms.cc</option>
											<option value="shabnam1" <?php echo 'shabnam1' === $settings['sms_service'] ? 'selected' : ''; ?>>سامانه پیام کوتاه شبنم - shabnam.co</option>
											<option value="payamresan" <?php echo 'payamresan' === $settings['sms_service'] ? 'selected' : ''; ?>>سامانه پیامک پیام رسان - payam-resan.com</option>
											<option value="payamgah" <?php echo 'payamgah' === $settings['sms_service'] ? 'selected' : ''; ?>>سامانه پیامک پیامگاه - payamgah.net</option>
											<option value="melipayamak" <?php echo 'melipayamak' === $settings['sms_service'] ? 'selected' : ''; ?>>ملی پیامک</option>
											<option value="hostiran" <?php echo 'hostiran' === $settings['sms_service'] ? 'selected' : ''; ?>>هاست ایران</option>
											<option value="shabnam" <?php echo 'shabnam' === $settings['sms_service'] ? 'selected' : ''; ?>>شبنم</option>
											<option value="mediana" <?php echo 'mediana' === $settings['sms_service'] ? 'selected' : ''; ?>>مدیانا</option>
											<option value="fpayamak" <?php echo 'fpayamak' === $settings['sms_service'] ? 'selected' : ''; ?>>پیامکده</option>
											<option value="diakosms" <?php echo 'diakosms' === $settings['sms_service'] ? 'selected' : ''; ?>>دیاکو پیامک</option>
											<option value="samanpayamak" <?php echo 'samanpayamak' === $settings['sms_service'] ? 'selected' : ''; ?>>سامان پیامک</option>
											<option value="idehsms" <?php echo 'idehsms' === $settings['sms_service'] ? 'selected' : ''; ?>>ایده اس ام اس</option>
											<option value="banehsms" <?php echo 'banehsms' === $settings['sms_service'] ? 'selected' : ''; ?>>بانه اس ام اس</option>
											<option value="irpayamak" <?php echo 'irpayamak' === $settings['sms_service'] ? 'selected' : ''; ?>>پیامک ایرانیان</option>
											<option value="freersms" <?php echo 'freersms' === $settings['sms_service'] ? 'selected' : ''; ?>>فریر اس ام اس</option>
											<option value="panizsms" <?php echo 'panizsms' === $settings['sms_service'] ? 'selected' : ''; ?>>پانیز اس ام اس</option>
											<option value="mcisms" <?php echo 'mcisms' === $settings['sms_service'] ? 'selected' : ''; ?>>ایران اس ام اس</option>
											<option value="smsclick" <?php echo 'smsclick' === $settings['sms_service'] ? 'selected' : ''; ?>>اس ام اس کلیک</option>
											<option value="textsms" <?php echo 'textsms' === $settings['sms_service'] ? 'selected' : ''; ?>>Text Sms</option>
											<option value="sgmsms" <?php echo 'sgmsms' === $settings['sms_service'] ? 'selected' : ''; ?>>سیگما اس ام اس</option>
											<option value="persiansms" <?php echo 'persiansms' === $settings['sms_service'] ? 'selected' : ''; ?>>پرشین اس ام اس</option>
											<option value="spadsms" <?php echo 'spadsms' === $settings['sms_service'] ? 'selected' : ''; ?>>اسـپــاد اس ام اس</option>
											<option value="idehsms3000" <?php echo 'idehsms3000' === $settings['sms_service'] ? 'selected' : ''; ?>>ایده اس ام اس (3000)</option>
											<option value="f2usms" <?php echo 'f2usms' === $settings['sms_service'] ? 'selected' : ''; ?>>f2u</option>
											<option value="f2usms2" <?php echo 'f2usms2' === $settings['sms_service'] ? 'selected' : ''; ?>>Panel2u</option>
											<option value="wstdsms" <?php echo 'wstdsms' === $settings['sms_service'] ? 'selected' : ''; ?>>WebStudio</option>
											<option value="hezarnevis" <?php echo 'hezarnevis' === $settings['sms_service'] ? 'selected' : ''; ?>>hezarnevis</option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="adminMobile">شماره موبایل مدیر وبسایت</label></th>
									<td><input id="adminMobile" name="bank_mellat_settings_fields_arrays[adminMobile]" type="text" value="<?php esc_attr( $settings['adminMobile'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="Sms_username">نام کاربری پنل پیامک</label></th>
									<td><input id="Sms_username" name="bank_mellat_settings_fields_arrays[Sms_username]" type="text" value="<?php esc_attr( $settings['Sms_username'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="Sms_password">کلمه عبور پنل پیامک</label></th>
									<td><input id="Sms_password" name="bank_mellat_settings_fields_arrays[Sms_password]" type="text" value="<?php esc_attr( $settings['Sms_password'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="sms_lineNumber">شماره خط اختصاصی</label></th>
									<td><input id="sms_lineNumber" name="bank_mellat_settings_fields_arrays[sms_lineNumber]" type="text" value="<?php esc_attr( $settings['sms_lineNumber'] ); ?>" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="Sms_text">متن پیامک ارسالی</label></th>
									<td><input id="Sms_text" name="bank_mellat_settings_fields_arrays[Sms_text]" type="text" value="<?php esc_attr( $settings['Sms_text'] ); ?>" /></td>
								</tr>
							</table>
						</div>
						<div id="Form"  class="tab-content">
							<table class="form-table" style="width:650px;">
								<tr valign="top">
									<th scope="row">فرم پرداخت</th>
									<td>
									<?php 
									$defualt_themes = scandir( $this->plugin_folder . 'includes/forms' );
									foreach ( $defualt_themes as $theme ) {
										if ( ! preg_match( '/.php/', $theme ) ) {
											continue;
										}
										$settings_form_theme = '';
										if ( $settings['form'] === $theme ) {
											$settings_form_theme = 'checked="checked"';
										}
										?>
										<input type="radio" name="bank_mellat_settings_fields_arrays[form]" value="<?php echo esc_attr( $theme ); ?>" <?php echo esc_html( $settings_form_theme ); ?> />فرم <?php echo esc_html( $theme ); ?><br />
										<?php
									}
									if ( is_dir( \WP_CONTENT_DIR . '/bank-mellat' ) ) {
										$users_themes = scandir( \WP_CONTENT_DIR . '/bank-mellat' );
										foreach ( $users_themes as $theme ) {
											if ( ! preg_match( '/.html/', $theme ) ) {
												continue;
											}
											$settings_form_theme = '';
											if ( $settings['form'] === $theme ) {
												$settings_form_theme = 'checked="checked"';
											}
											?>
											<input type="radio" name="bank_mellat_settings_fields_arrays[form]" value="<?php echo esc_attr( $theme ); ?>" <?php echo esc_html( $settings_form_theme ); ?> />فرم <?php echo esc_html( $theme ); ?><br />
											<?php
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

	/**
	 * Settings Init
	 * 
	 * @return void
	 */
	public function settings_init() {
 
		// setting section
		add_settings_section(
			'bank_mellat_settings_section',
			null,
			array( $this, 'settings_section_callback' ),
			'bank_mellat_settings'
		);
		 
		// setting field
		add_settings_field(
			'bank_mellat_settings_field_array',
			null,
			array( $this, 'settings_array' ),
			'bank_mellat_settings',
			'bank_mellat_settings_section'
		);
	 
		// Register this field with our settings group.
		register_setting( 'bank_mellat_settings_group', 'bank_mellat_settings_fields_arrays' );
	}

	/**
	 * Settings section callback
	 * 
	 * @return void
	 */
	public function settings_section_callback() {
		// empty for now
	}

	/**
	 * Settings array
	 * 
	 * @return void
	 */
	public function settings_array() {
		// empty for now
	}
}
