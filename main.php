<?php


/**
 * Deleting row from table by id
 * @global string $table_prefix
 * @global string $wpdb
 * @return boolean
 */
function delete_row_plugin_database_table() {
    global $table_prefix, $wpdb;

    $tblname = 'my_product';
    $wp_table = $table_prefix . "$tblname";
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        $wpdb->delete( $wp_table, array( 'id' => $id ) );
        return true;
    }
    return false;
}
$deleted = delete_row_plugin_database_table();

/**
 *  Rendering all records
 * @global string $table_prefix
 * @global string $wpdb
 */
function render_rows() {
    global $table_prefix, $wpdb;
    $sql = "SELECT * FROM {$table_prefix}my_product Order By id DESC";
    $rows = $wpdb->get_results($sql) or die(mysql_error());

    foreach ($rows as $row) {
        ?>
        <tr>
            <td><?php echo $row->id; ?></td>
            <td><?php echo $row->title; ?></td>
            <td><?php echo $row->sku; ?></td>
            <td class="text-center">
                <a class="btn btn-info btn-xs" href="<?php echo admin_url("admin.php?page=myproducts_add&id={$row->id}"); ?>">
                    <span class="glyphicon glyphicon-edit"></span>
                    Edit
                </a>
                <a href="javascript:void(0)" class="btn btn-danger btn-xs btn-delete" data-id="<?php echo $row->id; ?>">
                    <span class="glyphicon glyphicon-remove"></span>
                    Delete
                </a>
            </td>
        </tr>
        <?php
    }
}
?>
<div id="my_products" class="container">
    <div class="row my_data">
        <table class="table table-striped custab">
            <thead>
            <a href="<?php echo admin_url('admin.php?page=myproducts_add'); ?>" class="btn btn-primary btn-xs pull-right"><b>+</b> Add new Product</a>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Sku</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <?php render_rows(); ?>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.btn-delete').click(function () {
            var _id = $(this).attr('data-id');
            if (confirm("Are you sure you want to delete? ... It will be deleted all related data on selected elements") == true) {

                window.location = '<?php echo admin_url('admin.php?page=my_products'); ?>&delete&id=' + _id;


            }
        });
    });
</script>