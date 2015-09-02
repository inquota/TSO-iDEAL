<?php
	global $wpdb;
	$table_children = $wpdb->prefix . 'tso_children';
	$table_schools = $wpdb->prefix . 'tso_schools';
	$table_users = $wpdb->prefix . 'tso_users';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_children}" );
	$num_of_pages = ceil( $total / $limit );
	
	$items = $wpdb->get_results( 
		"SELECT 
				Child.id, 
				Child.first_name AS first_name,
				Child.last_name AS last_name,  
				School.name AS name_school, 
				Child.groep, 
				Child.created_at 
		FROM 
			{$table_children} AS Child
		LEFT JOIN {$table_users} AS User ON (Child.user_id=User.id) 
		LEFT JOIN {$table_schools} AS School ON (School.id=User.school_id) 
		ORDER BY name_school ASC, first_name ASC"
	);
	
if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_children} WHERE id IN (".implode(',', $_POST['id']).")");
	echo '<meta http-equiv="refresh" content="0; URL=/wp-admin/admin.php?page=children">';
endif;
?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Kinderen', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Verwijderen" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Naam</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Naam</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $item->id; ?></td>
            <td class="column-columnname"><?php echo $item->name_school; ?></td>
            <td class="column-columnname"><?php echo $item->groep; ?></td>
			<td class="column-columnname"><?php echo $item->first_name; ?> <?php echo $item->last_name; ?></td>
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
