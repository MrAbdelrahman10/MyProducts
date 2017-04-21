<?php
/*
  Plugin Name: My Products
  Plugin URI: http://www.mrabdelrahman10.com
  Description: Plugin for controlling products from an OSCommerce shopping cart
  Author: Abdelrahman Mohamed
  Version: 2.0
  Author URI: http://www.mrabdelrahman10.com
 */

/**
 * Uninstalling tables of plugin
 * @global string $table_prefix
 * @global string $wpdb
 */
function uninstall_plugin_database_table() {
    global $table_prefix, $wpdb;

    $tblname = 'postmeta';
    $wp_track_table = $table_prefix . "$tblname ";
    $sql = "Delete From `{$wp_track_table}` Where IN ('my_description', 'my_sku')";
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
    global $wpdb;
    $sql = "
            SELECT DISTINCT wposts.*, wpostmeta.meta_value AS my_sku
        FROM $wpdb->posts wposts
	INNER JOIN $wpdb->postmeta wpostmeta ON wposts.ID = wpostmeta.post_id
        WHERE wpostmeta.meta_key = 'my_sku'
        ";
    $rows = $wpdb->get_results($sql);
    if($rows){
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
                    <td><?php echo $row->ID; ?></td>
                    <td><a href="<?php echo get_permalink($row->ID); ?>"><?php echo $row->post_title; ?></a></td>
                    <td><?php echo $row->my_sku; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
} else {
    echo 'No Products';
}
}
/**
 * Call it to active Shortcode
 */
add_shortcode('myproduct', 'render_myproduct_shortcode');


/**
 * Function to define product as Custom Post Type
 */
function product_register() {

    $labels = array(
        'name' => _x('My Products', 'Post type name'),
        'singular_name' => _x('Product Item', 'Post type singular name'),
        'add_new' => _x('Add New', 'Product item'),
        'add_new_item' => __('Add New Product Item'),
        'edit_item' => __('Edit Product Item'),
        'new_item' => __('New Product Item'),
        'view_item' => __('View Product Item'),
        'search_items' => __('Search Product'),
        'not_found' => __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('product', $args);
}
/**
 * Register Product plugin as Custom Post Type
 */
add_action('init', 'product_register');


/**
 * Function to define product meta
 */
function admin_init() {
    add_meta_box("product_meta", "Product Details", "product_meta", "product", "normal", "low");
}
/**
 * Add it to admin
 */
add_action("admin_init", "admin_init");

/**
 * Render elements for Product
 */
function product_meta() {
    global $post;
    $custom = get_post_custom($post->ID);
    $sku = $custom["my_sku"][0];
    $description = $custom["my_description"][0];
    ?>
    <style>
        #product-form input, #product-form textarea{
            width: 100%;
        }
    </style>
    <div id="product-form">
        <p>
            <label>Sku :</label><br />
            <input type="text" name="sku" value="<?php echo $sku; ?>" />
        </p>
        <p>
            <label>Description :</label><br />
            <textarea cols="50" rows="5" name="description"><?php echo $description; ?></textarea>
        </p>
    </div>
    <?php
}

/**
 * Save product meta data
 */
function save_product() {
    global $post;

    update_post_meta($post->ID, "my_description", $_POST["description"]);
    update_post_meta($post->ID, "my_sku", $_POST["sku"]);
}

/**
 * Call it to save product meta data
 */
add_action('save_post', 'save_product');
