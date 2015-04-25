<?php
	global $wpdb;
	$table_submissions = $wpdb->prefix . 'tso_users';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_submissions}" );
	$num_of_pages = ceil( $total / $limit );
	
	$items = $wpdb->get_results( 
	"
	SELECT * 
	FROM {$table_submissions} ORDER BY id DESC
	"
	);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_submissions} WHERE id IN (".implode(',', $_POST['id']).")");
	header('Location: /wp-admin/admin.php?page=schools');
endif;
?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Users', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Father</th>
			<th class="manage-column column-columnname" scope="col">Mother</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Address</th>
			<th class="manage-column column-columnname" scope="col">Postalcode</th>
			<th class="manage-column column-columnname" scope="col">City</th>
			<th class="manage-column column-columnname" scope="col">Created on</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Father</th>
			<th class="manage-column column-columnname" scope="col">Mother</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Address</th>
			<th class="manage-column column-columnname" scope="col">Postalcode</th>
			<th class="manage-column column-columnname" scope="col">City</th>
			<th class="manage-column column-columnname" scope="col">Created on</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $item->id; ?></td>
			<td class="column-columnname"><?php echo $item->name_father; ?></td>
			<td class="column-columnname"><?php echo $item->name_mother; ?></td>
			<td class="column-columnname"><?php echo $item->email; ?></td>
			<td class="column-columnname"><?php echo $item->address; ?></td>
			<td class="column-columnname"><?php echo $item->postalcode; ?></td>
			<td class="column-columnname"><?php echo $item->city; ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($item->created_at)); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php

$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '&laquo;', 'text-domain' ),
    'next_text' => __( '&raquo;', 'text-domain' ),
    'total' => $num_of_pages,
    'current' => $pagenum
) );

if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}

?>
</form>
		
</div>
