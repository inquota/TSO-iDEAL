<?php
	global $wpdb;
	$table_schools = $wpdb->prefix . 'tso_schools';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_schools}" );
	$num_of_pages = ceil( $total / $limit );
	
	$items = $wpdb->get_results( 
	"
	SELECT * 
	FROM {$table_schools} ORDER BY id DESC
	"
	);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_schools} WHERE id IN (".implode(',', $_POST['id']).")");
	header('Location: ');
	echo'<script>window.location="/wp-admin/admin.php?page=schools"; </script>';
endif;


if(isset($_POST['insert'])) :

	// Save e-mail templates
	$wpdb->insert( 
	$table_schools, 
			array( 
				'name' => $_POST['name'],	// string
				'email' => $_POST['email'],	// string
				'created_at' => date('Y-m-d H:i:s'),	// string
			)
		);
	echo'<script>window.location="/wp-admin/admin.php?page=schools"; </script>';
endif;

if(isset($_POST['edit'])) :

	// Save e-mail templates
	$wpdb->update( 
	$table_schools, 
			array( 
				'name' => $_POST['name'],	// string
				'email' => $_POST['email'],	// string
				'created_at' => date('Y-m-d H:i:s'),	// string
			),
			array( 'id' => $_POST['edit_id'] )
		);
	echo'<script>window.location="/wp-admin/admin.php?page=schools"; </script>';
endif;
?>


<div class="wrap">
	<?php if(isset($_GET['page']) && $_GET['page']=='schools'  && isset($_GET['edit'])) : ?>

	<?php
	// Load data for edit
	$school = $wpdb->get_row( "SELECT * FROM {$table_schools} WHERE id = ".$_GET['edit']."", OBJECT);
	?>
			<h2>Edit school</h2>
	<form method="POST">
		
		<p>
			Name: <input type="text" name="name" required="required" value="<?php echo $school->name; ?>" />
		</p>
		
		<p>
			E-mail: <input type="text" name="email" value="<?php echo $school->email; ?>" required="required" /> use multiple e-mails with comma. e.g. email1@email.com,email2@email.com
		</p>
		
		<input type="hidden" name="edit_id" value="<?php echo $school->id; ?>" />
		
		<p>
			<input type="submit" name="edit" value="Save" class="button button-primary button-large" />
		</p>
	</form>	
	<?php endif; ?>
	
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
			<td class="column-columnname"><a href="?page=schools&edit=<?php echo $item->id; ?>">Edit</a></td>
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
			E-mail: <input type="text" name="email" required="required" /> use multiple e-mails with comma. e.g. email1@email.com,email2@email.com
		</p>
		
		<p>
			<input type="submit" name="insert" value="Save" class="button button-primary button-large" />
		</p>
	</form>
	
	
</div>
