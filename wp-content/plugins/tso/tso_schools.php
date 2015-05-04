<?php
	global $wpdb;
	$table_submissions = $wpdb->prefix . 'tso_schools';
		
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
	<?php    echo "<h2>" . __( 'Schools', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Name</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
			<th class="manage-column column-columnname" scope="col">Actions</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Name</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
			<th class="manage-column column-columnname" scope="col">Actions</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $item->id; ?></td>
			<td class="column-columnname"><?php echo $item->name; ?></td>
			<td class="column-columnname"><?php echo $item->email; ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($item->created_at)); ?></td>
			<td class="column-columnname"><a href="#">Edit</a></td>
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
	
	
	<hr />
	
	<h2>Insert school</h2>
	<form method="POST">
		
		<p>
			Name: <input type="text" name="name" required="required" />
		</p>
		
		<p>
			E-mail: <input type="email" name="email" required="required" /> use multiple e-mails with comma. e.g. email1@email.com,email2@email.com
		</p>
		
		<p>
			<input type="submit" name="insert" value="Save" class="button button-primary button-large" />
		</p>
	</form>
	
	
</div>