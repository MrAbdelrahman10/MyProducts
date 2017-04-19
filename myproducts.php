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
    include('inc.php');
    include('main.php');
}

/**
 * Including form plugin's file to add and edit
 */
function myproducts_add() {
    include('inc.php');
    include('myproducts_add.php');
}

/**
 * Adding "My Products" to menu
 */
function my_products_admin_actions() {
    add_menu_page("My Products Display", "My Products", 1, "my_products", "my_products_admin");

    //this is a submenu
    add_submenu_page('my_products', 'Add New Product', 'Add New', 'manage_options', 'myproducts_add', 'myproducts_add');
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

/**
 * ShortCode to render all products
 * @global string $table_prefix
 * @global string $wpdb
 */
function render_myproduct_shortcode() {
    global $table_prefix, $wpdb;
    $sql = "SELECT * FROM {$table_prefix}my_product Order By id DESC";
    $rows = $wpdb->get_results($sql) or die(mysql_error());
    ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Sku</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($rows as $row) {
        ?>
            <tr>
                <td><?php echo $row->id; ?></td>
                <td><?php echo $row->title; ?></td>
                <td><?php echo $row->sku; ?></td>
            </tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    <?php
}
add_shortcode('myproduct', 'render_myproduct_shortcode');