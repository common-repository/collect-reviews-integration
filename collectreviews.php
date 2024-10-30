<?php

require_once("api.class.php");

/*
 *
 * @package     CollectReviews
 * @author      Ivan Timofeev, Andrew Pavlow
 * @copyright   2018 Collect-Reviews.com
 * @license     GPL-2.0+
 *
Plugin Name: Collect reviews integration plugin
Description: Integration with Collect Reviews web service
Plugin URI: https://collect-reviews.com/
Author: Ivan Timofeev
Version: 0.1
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/


register_activation_hook(__FILE__,'collectr_activate');
register_deactivation_hook(__FILE__,'collectr_deactivate');
register_uninstall_hook(__FILE__,'collectr_uninstall');
function collectr_activate()
{
    if ( ! wp_next_scheduled( 'collectr_task_cron' ) ) {
        wp_schedule_event( time(), 'daily', 'collectr_task_cron' );
    }


    $domain = get_option('siteurl'); //or home
    $domain = str_replace('http://', '', $domain);
    $domain = str_replace('https://', '', $domain);


    Collect_Review_Api::activatePlugin($domain);
	

}

function collectr_deactivate()
{
    if (wp_next_scheduled('collectr_task_cron')) {
        $timestamp = wp_next_scheduled('collectr_task_cron');
        wp_unschedule_event( $timestamp, 'collectr_task_cron' );
    }

    $domain = get_option('siteurl'); //or home
    $domain = str_replace('http://', '', $domain);
    $domain = str_replace('https://', '', $domain);

    Collect_Review_Api::deactivatePlugin($domain);
}

function collectr_uninstall(){

    $domain = get_option('siteurl'); //or home
    $domain = str_replace('http://', '', $domain);
    $domain = str_replace('https://', '', $domain);

    Collect_Review_Api::uninstallPlugin($domain);

}

add_action( 'collectr_task_cron', 'collectr_task_cron_func' );

function collectr_task_cron_func() {
    $newdate = (time()-172800)."...".time();
    $options = get_option('collectrs_settings_api');
    $status = mb_strtolower($options['status']);
    if(!empty($options)){
        $orders = wc_get_orders(array("status" => $status, 'date_'.$status => $newdate, "return" => "ids"));

        $result = array();
        $countOrders = 0;
        foreach ($orders as $order) {
            $WC_Order = new WC_Order($order);

            $products = array();
            foreach ($WC_Order->get_items() as $var) {
                $products[] = array("SKU" => $var->get_product_id(), "Name" => $var->get_name(), "Url" =>  $url = get_permalink($var->get_product_id() ) );
            }
            $int_res = array(
                "ID" => $order,
                "BuyerName" => $WC_Order->get_billing_first_name()." ".$WC_Order->get_billing_last_name(),
                "BuyerEmail" => $WC_Order->get_billing_email(),
                "Products" => $products,
            );

            if($WC_Order->get_date_completed()){
                $int_res["DeliveryDate"] = $WC_Order->get_date_completed()->date('Y-m-d');
            }else{
                $int_res["DeliveryDate"] = date('Y-m-d');
            }

            $result[] = $int_res;

            $countOrders++;
        }

        if($countOrders > 0){
	        $apiClass = new Collect_Review_Api($options['clientid'], $options['tokenauth']);
	        $apiClass->sendProducts($result);
        }
    }
}

/****************************************
 *										*
 *      Admin setting Page       		*
 *										*
 *****************************************/

function collectr_add_options_page() {
    add_options_page('Collect Reviews Settings', 'Collect Reviews Settings', 'edit_pages', 'collectr_settings_page', 'collectr_settings_page_cb');
}

function collectr_settings_page_cb(){

    if ( ! current_user_can( 'edit_pages' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'collectr' );
            do_settings_sections( 'collectr_settings_page' );
            submit_button( 'Save' );
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'collectr_add_options_page');

function collectr_settings_init() {

    register_setting( 'collectr', 'collectrs_settings_api', 'collectr_settings_callback');

    add_settings_section(
        'collectr_settings_frontpage',
        'Plugin settings',
        'collectr_settings_frontpage_cb',
        'collectr_settings_page'
    );

    add_settings_field(
        'collectrs_clientid',
        'Client ID (from Collect Reviews)',
        'collectrs_clientid_cb',
        'collectr_settings_page',
        'collectr_settings_frontpage'
    );

    add_settings_field(
        'collectrs_tokenauth',
        'Token Auth (from Collect Reviews)',
        'collectrs_tokenauth_cb',
        'collectr_settings_page',
        'collectr_settings_frontpage'
    );

    add_settings_field(
        'collectrs_status',
        'Upload orders for',
        'collectrs_status_cb',
        'collectr_settings_page',
        'collectr_settings_frontpage'
    );
}

function collectr_settings_frontpage_cb( $args ) {

}

function collectrs_clientid_cb( $args ) {
    $options = get_option( 'collectrs_settings_api' );
    $options = $options ? $options['clientid'] : null;
    ?>
    <input type="text" name="collectrs_settings_api[clientid]" value="<?=$options?>">
    <?php
}

function collectrs_tokenauth_cb( $args ) {
    $options = get_option( 'collectrs_settings_api' );
    $options = $options ? $options['tokenauth'] : null;
    ?>
    <input type="password" name="collectrs_settings_api[tokenauth]" value="<?=$options?>">
    <?php
}

function collectrs_status_cb( $args ) {
    $options = get_option('collectrs_settings_api');
    $option = $options;
    $options = $options ? $options['status'] : "date_completed";
    ?>
    <select name="collectrs_settings_api[status]" >
    	<option value="created" <?=(($options == "created") ? "selected" : "")?>>Created Orders</option>
    	<option value="paid" <?=(($options == "paid") ? "selected" : "")?>>Paid Orders</option>
    	<option value="completed" <?=(($options == "completed") ? "selected" : "")?>>Completed Orders</option>
    </select>
    <?php
}

function collectr_settings_callback( $args ){
    $api = new Collect_Review_Api($args['clientid'], $args['tokenauth']);
    if(!$api->test()){
        add_settings_error( 'collectr_messages', 'test', 'ClientID or Token is incorrect!', 'error');
    }else{
        
        collectr_task_cron_func();
        return $args;
    }
}

add_action( 'admin_init', 'collectr_settings_init' );
