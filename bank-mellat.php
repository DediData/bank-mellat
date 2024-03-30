<?php
/**
 * Plugin Name: Bank Mellat
 * Description: Bank Mellat Online Payment
 * Plugin URI: https://dedidata.com
 * Author: DediData & Zanyar
 * Author URI: https://dedidata.com
 * Version: 2.0.0
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 7.0
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: bank-mellat
 *
 * @package Bank_Mellat
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( '\DediData\Plugin_Autoloader' ) ) {
	require 'includes/DediData/class-plugin-autoloader.php';
}
// Set name spaces we use in this plugin
new \DediData\Plugin_Autoloader( array( 'DediData', 'BankMellat' ) );
/**
 * The function BANK_MELLAT returns an instance of the Bank_Mellat class.
 *
 * @return object an instance of the \BankMellat\Bank_Mellat_Init class.
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function BANK_MELLAT() { // phpcs:ignore Squiz.Functions.GlobalFunction.Found, WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return \BankMellat\Bank_Mellat_Init::get_instance( __FILE__ );
}
BANK_MELLAT();
\BankMellat\Bank_Mellat::get_instance();
