<?php

/**
 * Adding row or update by id
 * @global string $table_prefix
 * @global string $wpdb
 * @return boolean
 */
function add_row_plugin_database_table() {
    if ($_POST) {
        global $table_prefix, $wpdb;

        $_title = strip_tags(@$_POST['title']);
        $_sku = strip_tags(@$_POST['sku']);
        $_description = @$_POST['description'];

        $tblname = 'my_product';
        $wp_table = $table_prefix . "$tblname";
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $wpdb->update(
                    $wp_table, array(
                'title' => $_title,
                'description' => $_description,
                'sku' => $_sku
                    ), array('id' => $id)
            );
        } else {
            $wpdb->insert(
                    $wp_table, array(
                'title' => $_title,
                'description' => $_description,
                'sku' => $_sku
                    )
            );
        }
    }
    return true;
}

/**
 * Get single record by id
 * @global string $table_prefix
 * @global string $wpdb
 */
function get_row_by_id() {
    global $table_prefix, $wpdb;
    $tblname = 'my_product';
    $wp_table = $table_prefix . "$tblname";
    $id = intval($_GET['id']);
    return $wpdb->get_row("SELECT * FROM {$wp_table} WHERE id = $id");
}

$_title = @$_POST['title'];
$_sku = @$_POST['sku'];
$_description = @$_POST['description'];
$err = array();
if ($_POST) {
    if (empty($_title)) {
        $err['title'] = 'Title is required';
    }
    if (empty($_sku)) {
        $err['sku'] = 'Sku is required';
    }
    if (empty($_description)) {
        $err['description'] = 'Description  is required';
    }
    if (!$err) {
        add_row_plugin_database_table();
        ?>

        <script>
            window.location = '<?php echo admin_url('admin.php?page=my_products'); ?>';
        </script>
        <?php
        exit;
    }
} elseif (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $row = get_row_by_id();
    if ($row) {
        $_title = $row->title;
        $_sku = $row->sku;
        $_description = $row->description;
    }
}
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="form-area">
            <form role="form" method="POST">
                <br style="clear:both">
                <h3 style="margin-bottom: 25px; text-align: center;"><?php echo isset($_GET['id']) ? 'Edit' : 'Add New'; ?> Product</h3>
                <div class="form-group">
                    <label>Product Name :</label>
                    <input type="text" class="form-control" name="title" placeholder="Product Name" value="<?php echo $_title; ?>" />
                    <span class="text-danger"><?php echo @$err['title']; ?></span>
                </div>
                <div class="form-group">
                    <label>Sku :</label>
                    <input type="text" class="form-control" name="sku" placeholder="Sku" value="<?php echo $_sku; ?>" />
                    <span class="text-danger"><?php echo @$err['sku']; ?></span>
                </div>
                <div class="form-group">
                    <label>Description :</label>
                    <textarea class="form-control" type="textarea" name="description" placeholder="Description" rows="7"><?php echo $_description; ?></textarea>
                    <span class="text-danger"><?php echo @$err['description']; ?></span>
                </div>

                <button type="submit" id="submit" name="submit" class="btn btn-primary pull-right">Save</button>
            </form>
        </div>
    </div>
</div>