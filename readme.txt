=== Plugin Name ===
Contributors: steigenhaus
Tags: customer reviews, review plugin, collect reviews
Requires at least: 4.9
Tested up to: 5.2.3
Requires PHP: 7.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The plugin integrate your WooCommerce shop to [Collect Reviews](https://collect-reviews.com/).

== Description ==
(Collect Reviews)[https://collect-reviews.com/] lets you collect trusted, verified reviews of your service and products and boosts your sales using social proof. As an independent reviews service, Collect Reviews offers solutions to automatically reach out to all of your customers following transactions and collect both Company and Product reviews, as well as the widgets and integrations to market your scores and ratings directly on your website. We collect all reviews, moderate the content for compliance with our review guidelines, and publish them directly on your dedicated Certificate Page.

This plugin offers a lightweight and simple solution to integrate with the services from Collect Reviews. Upon installing the plugin, it automatically send the necessary transaction information for Collect Reviews to request reviews from your customers. The transaction information include details regarding the transaction (Order ID, Products) and the customer who needs to be contacted for a review of that transaction (Customer Name, Customer Email).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/collectreviews` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Collect Reviews Settion screen to configure the plugin
4. Leave a request to create an account in the (Collect Reviews service page)[https://collect-reviews.com/trial/]
5. After receiving an account in the Collect Reviews service, go to your personal account in the [Settings -> Technical section](https://my.collect-reviews.com/Settings/RCSettings.aspx) and copy the ID and Token from there to activate your plugin.
6. Enter the received cridentials on the settings page Settings-> Collect Reviews Settion screen of your WordPress admin console.
7. Also on Settings page, select the status of orders that will be sent to Collect Reviews for further user polling. You can upload newly created orders, paid orders or completed orders to Collect Reviews in accordance with the WooCommerce order status.
8. After you save the settings, the system will automatically upload the current orders for the survey for the last two days. In the future, unloading will occur automatically daily.


== Changelog ==

= 0.1 =

* Upload base transactions info into Collect Reviews service once a day.

