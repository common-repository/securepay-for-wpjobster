<?php
/**
 * SecurePay for WPJobster.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */
final class SecurePay_WPJobster
{
    private static function register_locale()
    {
        add_action(
            'plugins_loaded',
            function () {
                load_plugin_textdomain(
                    'securepaywpjobster',
                    false,
                    SECUREPAY_WPJOBSTER_PATH.'languages/'
                );
            },
            0
        );
    }

    private static function register_autoupdates()
    {
        add_filter(
            'auto_update_plugin',
            function ($update, $item) {
                if (SECUREPAY_WPJOBSTER_SLUG === $item->slug) {
                    if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3') {
                        $option = wpj_get_option('wpjobster_securepay_enableautoupdate');
                    } else {
                        $option = get_option('wpjobster_securepay_enableautoupdate', 'yes');
                    }

                    return 'yes' === $option ? true : false;
                }

                return $update;
            },
            \PHP_INT_MAX,
            2
        );
    }

    public static function activate()
    {
        WPJobster_SecurePay_Loader::get_instance()->activation_check();

        return true;
    }

    public static function deactivate()
    {
        return true;
    }

    public static function uninstall()
    {
        return true;
    }

    public static function register_plugin_hooks()
    {
        register_activation_hook(SECUREPAY_WPJOBSTER_HOOK, [__CLASS__, 'activate']);
        register_deactivation_hook(SECUREPAY_WPJOBSTER_HOOK, [__CLASS__, 'deactivate']);
        register_uninstall_hook(SECUREPAY_WPJOBSTER_HOOK, [__CLASS__, 'uninstall']);
    }

    public static function attach()
    {
        self::register_locale();
        self::register_plugin_hooks();
        self::register_autoupdates();

        if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3') {
            add_action('after_setup_theme', ['WPJobster_SecurePay_Loader', 'init']);
        } else {
            if (!isset($GLOBALS['WPJobster_SecurePay_Loader']) || !\is_object($GLOBALS['WPJobster_SecurePay_Loader'])) {
                $GLOBALS['WPJobster_SecurePay_Loader'] = WPJobster_SecurePay_Loader::get_instance();
            }
        }
    }
}
