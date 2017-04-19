<?php

/*
  Plugin Name: My Products
  Plugin URI: http://www.mrabdelrahman10.com
  Description: Plugin for controlling products from an OSCommerce shopping cart
  Author: Abdelrahman Mohamed
  Version: 1.0
  Author URI: http://www.mrabdelrahman10.com
 */

/**
 * Including main plugin's file
 */
function my_products_admin() {
    include('main.php');
}

/**
 * Adding "My Products" to menu
 */
function my_products_admin_actions() {
    add_menu_page("My Products Display", "My Products", 1, "My Products Display", "my_products_admin");
}

add_action('admin_menu', 'my_products_admin_actions');

/**
 * Creating necessary tables
 * @global string $table_prefix
 * @global string $wpdb
 */
function create_plugin_database_table() {
    global $table_prefix, $wpdb;

    $tblname = 'my_product';
    $wp_track_table = $table_prefix . "$tblname";

    #Check to see if the table exists already, if not, then create it
    if ($wpdb->get_var("show tables like '$wp_track_table'") != $wp_track_table) {
        $sql = "CREATE TABLE `{$wp_track_table}` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL,
                `sku` VARCHAR(100) NOT NULL, PRIMARY KEY (`id`)
                ) COLLATE='utf8_general_ci' ENGINE=InnoDB;";
        require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
        dbDelta($sql);
        add_option('myproducts_version', '1.0');
    }
}

register_activation_hook(__FILE__, 'create_plugin_database_table');

/**
 * Uninstalling tables of plugin
 * @global string $table_prefix
 * @global string $wpdb
 */
function uninstall_plugin_database_table() {
    global $table_prefix, $wpdb;

    $tblname = 'my_product';
    $wp_track_table = $table_prefix . "$tblname ";
    $sql = "DROP TABLE IF EXISTS `{$wp_track_table}`";
    require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
    dbDelta($sql);
    delete_option('myproducts_version');
}

register_uninstall_hook(__FILE__, 'uninstall_plugin_database_table');

