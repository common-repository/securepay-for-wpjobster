<?php
/**
 * SecurePay for WPJobster.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */
final class WPJobster_SecurePay_Loader
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public $notices = [];

    private function __construct()
    {
        add_action('admin_init', [$this, 'check_environment']);
        add_action('admin_notices', [$this, 'admin_notices'], 15);
        add_action('plugins_loaded', [$this, 'init_gateways'], 0);
        add_filter('plugin_action_links_'.SECUREPAY_WPJOBSTER_HOOK, [$this, 'plugin_action_links']);

        add_filter('wpj_payment_gateways_filter', function ($payment_gateways_list) {
            $payment_gateways_list['securepay'] = 'SecurePay';

            return $payment_gateways_list;
        }, 10, 1);

        add_filter('wpj_admin_settings_items_filter', function ($menu) {
            $menu['payment-gateways']['childs']['securepay'] = ['order' => '02a', 'path' => SECUREPAY_WPJOBSTER_PATH.'/includes/src/admin-fields.php'];

            return $menu;
        }, 10, 1);

        // tweaks: wpjobster/lib/gateways/init.php
        add_action('template_redirect', function () {
            if (!empty($_REQUEST['payment_response']) && empty($_GET['payment_response'])) {
                $_GET['payment_response'] = $_REQUEST['payment_response'];
            }

            if (!empty($_REQUEST['payment_response']) && !empty($_REQUEST['payment_type']) && !empty($_REQUEST['oid'])) {
                $_REQUEST['payment_id_securepay'] = $_REQUEST['payment_id'];
                $_REQUEST['payment_id'] = $_REQUEST['oid'];
            }
        }, \PHP_INT_MIN);

        add_action('wpjobster_taketo_securepay_gateway', [$this, 'taketogateway_function'], 10, 2);
        add_action('wpjobster_processafter_securepay_gateway', [$this, 'processgateway_function'], 10, 2);
        add_filter('wpjobster_take_allowed_currency_securepay', [$this, 'get_gateway_currency']);

        if (isset($_POST['wpjobster_save_securepay'])) {
            add_action('wpjobster_payment_methods_action', [$this, 'save_gateway'], 11);
        }

        /*if ( !is_admin() ) {
            add_action('init', function() {

                foreach(['job_purchase', 'feature', 'topup', 'custom_extra', 'tips', 'subscription'] as $payment_type) {
                    add_action('wpjobster_after_securepay_'.$payment_type.'_link', function($pid) {
                        echo "<!-- TEST {$pid} -->\n";
                    });
                }
            });
        }*/
    }

    public static function init()
    {
        $class = __CLASS__;
        new $class();
    }

    private function sanitizein($text)
    {
        return sanitize_text_field(trim($text));
    }

    public function get_gateway_currency($currency)
    {
        $currency = 'MYR';

        return $currency;
    }

    public function init_gateways()
    {
        add_filter('wpjobster_payment_gateways', [$this, 'add_gateways']);
    }

    public function add_gateways($methods)
    {
        $priority = end(array_keys($methods)) + 50000;
        $methods[$priority] = [
            'label' => __('SecurePay', 'securepaywpjobster'),
            'unique_id' => 'securepay',
            'action' => 'wpjobster_taketo_securepay_gateway',
            'response_action' => 'wpjobster_processafter_securepay_gateway',
        ];

        add_action('wpjobster_show_paymentgateway_forms', [$this, 'show_gateways'], $priority, 3);

        return $methods;
    }

    /**
     * Save the gateway settings in admin.
     *
     * @since 1.0.0
     */
    public function save_gateway()
    {
        if (isset($_POST['wpjobster_save_securepay'])) {
            update_option('wpjobster_securepay_enable', $this->sanitizein($_POST['wpjobster_securepay_enable']));
            $caption_button = $this->sanitizein($_POST['wpjobster_securepay_button_caption']);
            if ('' === $caption_button) {
                $caption_button = 'Pay with SecurePay';
            }
            update_option('wpjobster_securepay_button_caption', $caption_button);
            update_option('wpjobster_securepay_enableautoupdate', $this->sanitizein($_POST['wpjobster_securepay_enableautoupdate']));
            // update_option('wpjobster_securepay_enableabanklist', $this->sanitizein($_POST['wpjobster_securepay_enablebanklist']));
            // update_option('wpjobster_securepay_enableabanklogo', $this->sanitizein($_POST['wpjobster_securepay_enablebanklogo']));

            $payment_type_enable_arr = $GLOBALS['payment_type_enable_arr'];
            foreach ($payment_type_enable_arr as $payment_type_enable_key => $payment_type_enable) {
                if ('job_purchase' !== $payment_type_enable_key && 'subscription' != $payment_type_enable_key) {
                    if (isset($_POST['wpjobster_securepay_enable_'.$payment_type_enable_key])) {
                        update_option('wpjobster_securepay_enable_'.$payment_type_enable_key, $this->sanitizein($_POST['wpjobster_securepay_enable_'.$payment_type_enable_key]));
                    }
                }
            }

            update_option('wpjobster_securepay_live_token', $this->sanitizein($_POST['wpjobster_securepay_live_token']));
            update_option('wpjobster_securepay_live_checksum', $this->sanitizein($_POST['wpjobster_securepay_live_checksum']));
            update_option('wpjobster_securepay_live_uid', $this->sanitizein($_POST['wpjobster_securepay_live_uid']));
            update_option('wpjobster_securepay_live_partner_uid', $this->sanitizein($_POST['wpjobster_securepay_live_partner_uid']));

            $testmode = $this->sanitizein($_POST['wpjobster_securepay_enabletestmode']);
            $sandbox = $this->sanitizein($_POST['wpjobster_securepay_enablesandbox']);
            if ('yes' === $testmode) {
                $sandbox = 'no';
            }

            update_option('wpjobster_securepay_enabletestmode', $testmode);
            update_option('wpjobster_securepay_enablesandbox', $sandbox);
            update_option('wpjobster_securepay_sandbox_token', $this->sanitizein($_POST['wpjobster_securepay_sandbox_token']));
            update_option('wpjobster_securepay_sandbox_checksum', $this->sanitizein($_POST['wpjobster_securepay_sandbox_checksum']));
            update_option('wpjobster_securepay_sandbox_uid', $this->sanitizein($_POST['wpjobster_securepay_sandbox_uid']));
            update_option('wpjobster_securepay_sandbox_partner_uid', $this->sanitizein($_POST['wpjobster_securepay_sandbox_partner_uid']));

            update_option('wpjobster_securepay_success_page', $this->sanitizein($_POST['wpjobster_securepay_success_page']));
            update_option('wpjobster_securepay_failure_page', $this->sanitizein($_POST['wpjobster_securepay_failure_page']));

            echo '<div class="updated fade"><p>'.__('Settings saved!', 'securepaywpjobster').'</p></div>';
        }
    }

    /**
     * Display the gateway settings in admin.
     *
     * @since 1.0.0
     */
    public function show_gateways($wpjobster_payment_gateways, $arr, $arr_pages)
    {
        $tab_id = get_tab_id($wpjobster_payment_gateways);
        include_once SECUREPAY_WPJOBSTER_PATH.'/includes/admin/show_gateways.php';
    }

    private function get_option($option, $default = '')
    {
        $value = $default;
        if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3') {
            $value = wpj_get_option($option);
            if ('' === trim($value)) {
                $value = $default;
            }
        } else {
            $value = get_option($option, $default);
        }

        return $value;
    }

    private function user($a, $b)
    {
        if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3' && \function_exists('wpj_user')) {
            return wpj_user($a, $b);
        }

        return user($a, $b);
    }

    private function formats_special($number, $cents = 1)
    {
        if (\function_exists('wpjobster_formats_special')) {
            return wpjobster_formats_special($number, $cents);
        }
        $dec_sep = '.';
        $tho_sep = '';

        if (is_numeric($number)) { // a number
            if (!$number) { // zero
                $money = (2 == $cents ? '0'.$dec_sep.'00' : '0'); // output zero
            } else { // value
                if (floor($number) == $number) { // whole number
                    $money = number_format($number, 2 == $cents ? 2 : 0, $dec_sep, ''); // format
                } else { // cents
                    $money = number_format(round($number, 2), 0 == $cents ? 0 : 2, $dec_sep, ''); // format
                } // integer or decimal
            } // value

            return $money;
        } // numeric
    }

    /**
     * This function is not required, but it helps making the code a bit cleaner.
     *
     * @since 1.0.0
     */
    public function get_gateway_credentials()
    {
        if ('yes' === (string) $this->get_option('wpjobster_securepay_enabletestmode', 'no')) {
            $securepay_payment_url = SECUREPAY_WPJOBSTER_ENDPOINT_SANDBOX;
            $securepay_token = 'GFVnVXHzGEyfzzPk4kY3';
            $securepay_checksum = '3faa7b27f17c3fb01d961c08da2b6816b667e568efb827544a52c62916d4771d';
            $securepay_uid = '4a73a364-6548-4e17-9130-c6e9bffa3081';
            $securepay_partner_uid = '';
        } else {
            if ('no' === (string) $this->get_option('wpjobster_securepay_enablesandbox', 'no')) {
                $securepay_payment_url = SECUREPAY_WPJOBSTER_ENDPOINT_LIVE;
                $securepay_token = $this->get_option('wpjobster_securepay_live_token');
                $securepay_checksum = $this->get_option('wpjobster_securepay_live_checksum');
                $securepay_uid = $this->get_option('wpjobster_securepay_live_uid');
                $securepay_partner_uid = $this->get_option('wpjobster_securepay_live_partner_uid');
            } else {
                $securepay_payment_url = SECUREPAY_WPJOBSTER_ENDPOINT_SANDBOX;
                $securepay_token = $this->get_option('wpjobster_securepay_sandbox_token');
                $securepay_checksum = $this->get_option('wpjobster_securepay_sandbox_checksum');
                $securepay_uid = $this->get_option('wpjobster_securepay_sandbox_uid');
                $securepay_partner_uid = $this->get_option('wpjobster_securepay_sandbox_partner_uid');
            }
        }

        $credentials = [
            'securepay_token' => $securepay_token,
            'securepay_checksum' => $securepay_checksum,
            'securepay_uid' => $securepay_uid,
            'securepay_partner_uid' => $securepay_partner_uid,
            'securepay_payment_url' => $securepay_payment_url,
        ];

        return $credentials;
    }

    private function calculate_sign($checksum, $a, $b, $c, $d, $e, $f, $g, $h, $i)
    {
        $str = $a.'|'.$b.'|'.$c.'|'.$d.'|'.$e.'|'.$f.'|'.$g.'|'.$h.'|'.$i;

        return hash_hmac('sha256', $str, $checksum);
    }

    /**
     * Collect all the info that we need and forward to the gateway.
     *
     * @since 1.0.0
     */
    public function taketogateway_function($payment_type, $common_details)
    {
        $credentials = $this->get_gateway_credentials();
        $securepay_token = $credentials['securepay_token'];
        $securepay_checksum = $credentials['securepay_checksum'];
        $securepay_uid = $credentials['securepay_uid'];
        $securepay_partner_uid = $credentials['securepay_partner_uid'];
        $securepay_payment_url = $credentials['securepay_payment_url'];

        if ('' !== $securepay_token && '' !== $securepay_checksum && '' !== $securepay_uid) {
            $uid = get_current_user_id(); // $common_details['uid'];

            $wpjobster_final_payable_amount = $common_details['wpjobster_final_payable_amount'];
            $currency = $common_details['selected'];
            $order_id = $common_details['order_id'];
            $job_title = $common_details['job_title'];
            $job_id = $common_details['pid'];

            if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3' && \function_exists('wpj_get_payment')) {
                $payment_row = wpj_get_payment(['payment_type_id' => $common_details['id'], 'payment_type' => $payment_type]);
                $wpjobster_final_payable_amount = $payment_row->final_amount_exchanged;
                $order_id = $payment_row->id;
            }

            $securepay_amount = $this->formats_special($wpjobster_final_payable_amount, 2);

            $customer_first_name = $this->user($uid, 'first_name');
            $customer_last_name = $this->user($uid, 'last_name');
            $customer_name = trim($customer_first_name.' '.$customer_last_name);
            if (empty($customer_name)) {
                $customer_name = $this->user($uid, 'display_name');
            }

            $customer_email = $this->user($uid, 'user_email');
            $customer_phone = $this->user($uid, 'cell_number');
            $redirect_url = get_bloginfo('url').'/?payment_response=securepay&url=return&payment_type='.$payment_type.'&oid='.$order_id;

            $cancel_url = get_bloginfo('url').'/?jb_action=purchase_this&jobid='.$job_id;
            $timeout_url = $cancel_url;

            $securepay_sign = $this->calculate_sign($securepay_checksum, $customer_email, $customer_name, $customer_phone, $redirect_url, $order_id, $job_title, $redirect_url, $securepay_amount, $securepay_uid);

            if ($securepay_amount < 2) {
                wp_die(__('Minimum payment amount for SecurePay is RM2. Please choose another payment method.', 'securepaywpjobster'), __('Please choose another payment gateway', 'securepaywpjobster'));
            }

            include_once SECUREPAY_WPJOBSTER_PATH.'/includes/admin/taketogateway.php';
            exit;
        }

        esc_html_e('Please enter SecurePay Token, Checksum and UID', 'securepaywpjobster');
        exit;
    }

    private function sanitize_response()
    {
        $params = [
             'amount',
             'bank',
             'buyer_email',
             'buyer_name',
             'buyer_phone',
             'checksum',
             'client_ip',
             'created_at',
             'created_at_unixtime',
             'currency',
             'exchange_number',
             'fpx_status',
             'fpx_status_message',
             'fpx_transaction_id',
             'fpx_transaction_time',
             'id',
             'interface_name',
             'interface_uid',
             'merchant_reference_number',
             'name',
             'order_number',
             'payment_id',
             'payment_id_securepay', /* for wpjobster >= 6.1 */
             'payment_method',
             'payment_status',
             'receipt_url',
             'retry_url',
             'source',
             'status_url',
             'transaction_amount',
             'transaction_amount_received',
             'uid',
         ];

        $response_params = [];
        if (isset($_REQUEST)) {
            foreach ($params as $k) {
                if (isset($_REQUEST[$k])) {
                    $response_params[$k] = sanitize_text_field($_REQUEST[$k]);
                }
            }
        }

        return $response_params;
    }

    private function response_status($response_params)
    {
        if ((isset($response_params['payment_status']) && 'true' === $response_params['payment_status']) || (isset($response_params['fpx_status']) && 'true' === $response_params['fpx_status'])) {
            return true;
        }

        return false;
    }

    public function processgateway_function($payment_type, $details)
    {
        $response_params = $this->sanitize_response();
        $payment_response = maybe_serialize($response_params);

        if (!empty($response_params) && isset($response_params['order_number'])) {
            $success = $this->response_status($response_params);
            $order_id = $response_params['order_number'];

            if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3') {
                $payment_row = wpj_get_payment(['id' => $order_id]);
                $order_id = $payment_row->payment_type_id;
                $payment_type = $payment_row->payment_type;
                $payment_type = $payment_row->payment_type;
                $payment_status = $payment->payment_status;
            } else {
                $wpdb = $GLOBALS['wpdb'];
                $pref = $wpdb->prefix;
                $select_package = 'select * from '.$pref."job_payment_received where payment_type_id='".$order_id."' order by id DESC";
                $r = $wpdb->get_results($select_package);
                $order_info = isset($r['0']) ? $r['0'] : 0;
                $payment_type = $order_info->payment_type;
                $payment_status = $order_info->payment_status;

                do_action('wpjobster_include_all_common_payment_type_files', $payment_type, 'securepay');
            }

            $response_params['payment_id'] = $response_param['payment_id_securepay'];
            unset($response_param['payment_id_securepay']);

            if ('completed' === $payment_status) {
                $payment_details = 'Payment already completed: '.$response_params['merchant_reference_number'];
                do_action('wpjobster_'.$payment_type.'_payment_success', $order_id, 'securepay', $payment_details, $payment_response);
                exit;
            }

            if ($success) {
                $payment_details = $response_params['merchant_reference_number'];
                do_action('wpjobster_'.$payment_type.'_payment_success', $order_id, 'securepay', $payment_details, $payment_response);
                exit;
            }

            $payment_details = 'SecurePay Failed';
            do_action('wpjobster_'.$payment_type.'_payment_failed', $order_id, 'securepay', $payment_details, $payment_response);
            exit;
        }

        $payment_details = 'SecurePay Failed to capture response';
        $order_id = isset($response_params['order_number']) ? $response_params['order_number'] : -1;
        do_action('wpjobster_'.$payment_type.'_payment_failed', $order_id, 'securepay', $payment_details, $payment_response);
        exit;
    }

    public function add_admin_notice($slug, $class, $message)
    {
        $this->notices[$slug] = [
            'class' => $class,
            'message' => $message,
        ];
    }

    public static function activation_check()
    {
        $environment_warning = self::get_environment_warning(true);
        if ($environment_warning) {
            deactivate_plugins(SECUREPAY_WPJOBSTER_HOOK);
            wp_die($environment_warning);
        }
    }

    public function check_environment()
    {
        $environment_warning = self::get_environment_warning();
        if ($environment_warning && is_plugin_active(SECUREPAY_WPJOBSTER_HOOK)) {
            deactivate_plugins(plugin_basename(SECUREPAY_WPJOBSTER_HOOK));
            $this->add_admin_notice('bad_environment', 'error', $environment_warning);
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
        if (!\function_exists('wpj_get_wpjobster_plugins_list')) {
            if (is_plugin_active(SECUREPAY_WPJOBSTER_HOOK)) {
                deactivate_plugins(SECUREPAY_WPJOBSTER_HOOK);
                $message = esc_html__('The current theme is not compatible with the plugin WPJobster SecurePay Gateway. Activate the WPJobster theme before installing this plugin.', 'securepaywpjobster');
                $this->add_admin_notice('securepay', 'error', $message);
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }

    public static function get_environment_warning($during_activation = false)
    {
        if (version_compare(\PHP_VERSION, SECUREPAY_WPJOBSTER_MIN_PHP_VER, '<')) {
            if ($during_activation) {
                /* translators: %1$s = plugin min php version, %2$s = current php version */
                $message = sprintf(esc_html__('The plugin could not be activated. The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'securepaywpjobster'), SECUREPAY_WPJOBSTER_MIN_PHP_VER, \PHP_VERSION);
            } else {
                /* translators: %1$s = plugin min php version, %2$s = current php version */
                $message = sprintf(esc_html__('The SecurePay Powered by wpjobster plugin has been deactivated. The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'securepaywpjobster'), SECUREPAY_WPJOBSTER_MIN_PHP_VER, \PHP_VERSION);
            }

            return sprintf($message, SECUREPAY_WPJOBSTER_MIN_PHP_VER, \PHP_VERSION);
        }

        return false;
    }

    public function plugin_action_links($links)
    {
        if (\defined('wpjobster_VERSION') && wpjobster_VERSION >= '6.1.7.3') {
            return array_merge([wpj_generate_settings_link('securepay')], $links);
        }

        $setting_link = $this->get_setting_link();
        $plugin_links = [
            '<a href="'.$setting_link.'">'.esc_html__('Settings', 'securepaywpjobster').'</a>',
        ];

        return array_merge($plugin_links, $links);
    }

    public function get_setting_link()
    {
        return admin_url('admin.php?page=payment-methods&active_tab=tabssecurepay');
    }

    public function admin_notices()
    {
        $html = '';
        foreach ((array) $this->notices as $notice_key => $notice) {
            $html .= "<div class='".esc_attr($notice['class'])."'><p>";
            $html .= wp_kses($notice['message'], ['a' => ['href' => []]]);
            $html .= '</p></div>';
        }
        echo $html;
    }
}
