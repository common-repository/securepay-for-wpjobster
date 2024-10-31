<?php
/**
 * SecurePay for WPJobster.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */

/*
 * @wordpress-plugin
 * Plugin Name:         SecurePay for WPJobster
 * Plugin URI:          https://www.securepay.my/?utm_source=wp-plugins-jobster&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Version:             1.0.6
 * Description:         SecurePay payment platform plugin for Jobster Theme
 * Author:              SecurePay Sdn Bhd
 * Author URI:          https://www.securepay.my/?utm_source=wp-plugins-jobster&utm_campaign=author-uri&utm_medium=wp-dash
 * Requires at least:   5.4
 * Requires PHP:        7.2
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         securepaywpjobster
 * Domain Path:         /languages
 */

if (!\defined('ABSPATH') || \defined('SECUREPAY_WPJOBSTER_FILE')) {
    exit;
}

\define('SECUREPAY_WPJOBSTER_MIN_PHP_VER', '5.4');
\define('SECUREPAY_WPJOBSTER_SLUG', 'securepay-for-wpjobster');
\define('SECUREPAY_WPJOBSTER_ENDPOINT_LIVE', 'https://securepay.my/api/v1/');
\define('SECUREPAY_WPJOBSTER_ENDPOINT_SANDBOX', 'https://sandbox.securepay.my/api/v1/');
\define('SECUREPAY_WPJOBSTER_ENDPOINT_PUBLIC_LIVE', 'https://securepay.my/api/public/v1/');
\define('SECUREPAY_WPJOBSTER_ENDPOINT_PUBLIC_SANDBOX', 'https://sandbox.securepay.my/api/public/v1/');
\define('SECUREPAY_WPJOBSTER_FILE', __FILE__);
\define('SECUREPAY_WPJOBSTER_HOOK', plugin_basename(SECUREPAY_WPJOBSTER_FILE));
\define('SECUREPAY_WPJOBSTER_PATH', realpath(plugin_dir_path(SECUREPAY_WPJOBSTER_FILE)).'/');
\define('SECUREPAY_WPJOBSTER_URL', trailingslashit(plugin_dir_url(SECUREPAY_WPJOBSTER_FILE)));

require __DIR__.'/includes/load.php';
SecurePay_WPJobster::attach();
