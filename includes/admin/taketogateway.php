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
<!--- #securepay:taketogateways --->
<form name="order" id="securepay_payment" method="post" action="<?php echo esc_url($securepay_payment_url).'/payments'; ?>">
    <input type="hidden" name="order_number" value="<?php echo esc_attr($order_id); ?>">
    <input type="hidden" name="buyer_name" value="<?php echo esc_attr($customer_name); ?>">
    <input type="hidden" name="buyer_email" value="<?php echo esc_attr($customer_email); ?>">
    <input type="hidden" name="buyer_phone" value="<?php echo esc_attr($customer_phone); ?>">
    <input type="hidden" name="transaction_amount" value="<?php echo esc_attr($securepay_amount); ?>">
    <input type="hidden" name="product_description" value="<?php echo esc_attr($job_title); ?>">
    <input type="hidden" name="callback_url" value="<?php echo esc_attr($redirect_url); ?>">
    <input type="hidden" name="redirect_url" value="<?php echo esc_attr($redirect_url); ?>">
    <input type="hidden" name="cancel_url" value="<?php echo esc_attr($cancel_url); ?>">
    <input type="hidden" name="timeout_url" value="<?php echo esc_attr($timeout_url); ?>">
    <input type="hidden" name="token" value="<?php echo esc_attr($securepay_token); ?>">
    <input type="hidden" name="partner_uid" value="<?php echo esc_attr($securepay_partner_uid); ?>">
    <input type="hidden" name="checksum" value="<?php echo esc_attr($securepay_sign); ?>">
</form>
<script>
    document.getElementById( "securepay_payment" ).submit();
</script>
<!--- /#securepay:taketogateways --->