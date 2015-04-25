<?php
	global $wpdb;
	$table_children = $wpdb->prefix . 'tso_children';
	$table_schools = $wpdb->prefix . 'tso_schools';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_children}" );
	$num_of_pages = ceil( $total / $limit );
	
	$items = $wpdb->get_results( 
		"SELECT C.id, C.name AS name_child, S.name AS name_school, C.groep, C.created_at FROM {$table_children} AS C LEFT JOIN {$table_schools} AS S ON (S.id=C.school_id) ORDER BY C.id DESC"
	);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_children} WHERE id IN (".implode(',', $_POST['id']).")");
	header('Location: ');
	echo '<meta http-equiv="refresh" content="0; URL=/wp-admin/admin.php?page=schools">';
endif;


if(isset($_POST['insert'])) :

	// Save e-mail templates
	$wpdb->insert( 
	$table_submissions, 
			array( 
				'name' => $_POST['name'],	// string
				'email' => $_POST['email'],	// string
				'created_at' => date('Y-m-d H:i:s'),	// string
			)
		);
	echo '<meta http-equiv="refresh" content="0; URL=/wp-admin/admin.php?page=schools">';
endif;
?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Children', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Group</th>
			<th class="manage-column column-columnname" scope="col">Name</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Group</th>
			<th class="manage-column column-columnname" scope="col">Name</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $item->id; ?></td>
            <td class="column-columnname"><?php echo $item->name_school; ?></td>
            <td class="column-columnname"><?php echo $item->groep; ?></td>
			<td class="column-columnname"><?php echo $item->name_child; ?></td>
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
