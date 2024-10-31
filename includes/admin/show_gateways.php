<?php
/**
 * SecurePay for WPJobster.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */
!\defined('ABSPATH') && exit;
?>
<!--- #securepay:show-gateways --->
<div id="tabs<?php echo esc_html($tab_id); ?>">
    <form method="post" enctype="multipart/form-data" action="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=payment-methods&active_tab=tabs<?php echo esc_html($tab_id); ?>">
        <table width="100%" class="wpj-admin-table">
            <?php do_action('wpj_securepay_add_tab_content'); ?>
            <tr>
                <td></td>
                <td>
                    <h2><?php esc_html_e('General Options', 'securepaywpjobster'); ?></h2>
                </td>
                <td></td>
            </tr>
            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Enable/Disable SecurePay payment gateway', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Enable SecurePay:', 'securepaywpjobster'); ?></td>
                <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enable', 'no'); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Enable/Disable SecurePay test mode', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Enable Test mode:', 'securepaywpjobster'); ?></td>
                <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enabletestmode', 'no'); ?></td>
            </tr>

            <?php
            global $payment_type_enable_arr;
foreach ($payment_type_enable_arr as $payment_type_enable_key => $payment_type_enable) :
    if ('job_purchase' !== $payment_type_enable_key && 'subscription' !== $payment_type_enable_key) :
        ?>
            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet($payment_type_enable['hint_label']); ?></td>
                <td width="250"><?php echo esc_html($payment_type_enable['enable_label']); ?></td>
                <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enable_'.$payment_type_enable_key); ?></td>
            </tr>
            <?php
    endif;
endforeach;
?>
            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Put the SecurePay button caption you want user to see on purchase page', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Button Caption:', 'securepaywpjobster'); ?></td>
                <?php
    $caption_button = get_option('wpjobster_securepay_button_caption', 'Pay with SecurePay');
if ('' === trim($caption_button)) {
    $caption_button = 'SecurePay';
}
?>
                <td><input type="text" size="45" name="wpjobster_securepay_button_caption" value="<?php echo esc_attr($caption_button); ?>" /></td>
            </tr>
            <?php
            /*
            <tr>
            <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Enable/Disable show SecurePay supported banks', 'securepaywpjobster')); ?></td>
            <td width="250"><?php esc_html_e('Enable bank list:', 'securepaywpjobster'); ?></td>
            <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enablebanklist', 'yes'); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Enable/Disable use supported banks logo', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Enable supported banks logo:', 'securepaywpjobster'); ?></td>
                <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enablebanklogo', 'yes'); ?></td>
            </tr>
            */
            ?>
            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Enable/Disable SecurePay plugin Auto-updates', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Enable Auto-updates:', 'securepaywpjobster'); ?></td>
                <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enableautoupdate', 'yes'); ?></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <h2><?php esc_html_e('Live Options', 'securepaywpjobster'); ?></h2>
                </td>
                <td></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Live Token', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Live Token:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_live_token" value="<?php echo esc_attr(get_option('wpjobster_securepay_live_token')); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Live Checksum Token', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Live Checksum Token:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_live_checksum" value="<?php echo esc_attr(get_option('wpjobster_securepay_live_checksum')); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Live UID', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Live UID:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_live_uid" value="<?php echo esc_attr(get_option('wpjobster_securepay_live_uid')); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Live Partner UID', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Live Partner UID:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_live_partner_uid" value="<?php echo esc_attr(get_option('wpjobster_securepay_live_partner_uid')); ?>" /></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <h2><?php esc_html_e('Sandbox Options', 'securepaywpjobster'); ?></h2>
                </td>
                <td></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Enable/Disable SecurePay sandbox mode.', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Enable Sandbox Mode:', 'securepaywpjobster'); ?></td>
                <td><?php echo wpjobster_get_option_drop_down($arr, 'wpjobster_securepay_enablesandbox', 'no'); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Sandbox Token', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Sandbox Token:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_sandbox_token" value="<?php echo esc_attr(get_option('wpjobster_securepay_sandbox_token')); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Sandbox Checksum Token', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Sandbox Checksum Token:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_sandbox_checksum" value="<?php echo esc_attr(get_option('wpjobster_securepay_sandbox_checksum')); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Sandbox UID', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Sandbox UID:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_sandbox_uid" value="<?php echo esc_attr(get_option('wpjobster_securepay_sandbox_uid')); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Your SecurePay Sandbox Partner UID', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('SecurePay Sandbox Partner UID:', 'securepaywpjobster'); ?></td>
                <td><input type="text" size="45" name="wpjobster_securepay_sandbox_partner_uid" value="<?php echo esc_attr(get_option('wpjobster_securepay_sandbox_partner_uid')); ?>" /></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <h2><?php esc_html_e('Redirect Options', 'securepaywpjobster'); ?></h2>
                </td>
                <td></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Please select a page to show when SecurePay payment successful. If empty, it redirects to the transaction page', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Transaction Success Redirect:', 'securepaywpjobster'); ?></td>
                <td>
                    <?php
    echo wpjobster_get_option_drop_down($arr_pages, 'wpjobster_securepay_success_page', '', ' class="select2" ');
?>
                </td>
            </tr>
            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(esc_html__('Please select a page to show when SecurePay payment failed. If empty, it redirects to the transaction page', 'securepaywpjobster')); ?></td>
                <td width="250"><?php esc_html_e('Transaction Failure Redirect:', 'securepaywpjobster'); ?></td>
                <td>
                    <?php
echo wpjobster_get_option_drop_down($arr_pages, 'wpjobster_securepay_failure_page', '', ' class="select2" ');
?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><input type="submit" name="wpjobster_save_securepay" value="<?php esc_html_e('Save Options', 'securepaywpjobster'); ?>" /></td>
            </tr>
        </table>
    </form>
</div>
<!--- /#securepay:show-gateways --->