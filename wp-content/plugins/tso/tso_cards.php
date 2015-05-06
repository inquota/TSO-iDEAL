<?php
	global $wpdb;
	$table_cards = $wpdb->prefix . 'tso_cards';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_cards}" );
	$num_of_pages = ceil( $total / $limit );
	
	$items = $wpdb->get_results( 
	"
	SELECT * 
	FROM {$table_cards} ORDER BY id DESC
	"
	);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_cards} WHERE id IN (".implode(',', $_POST['id']).")");
	echo'<script>window.location="/wp-admin/admin.php?page=cards"; </script>';
endif;


if(isset($_POST['insert'])) :

	// Save e-mail templates
	$wpdb->insert( 
	$table_cards, 
			array( 
				'description' => $_POST['description'],	// string
				'description_short' => $_POST['description_short'],	// string
				'price' => $_POST['price'],	// string
				'created_at' => date('Y-m-d H:i:s'),	// string
			)
		);
	echo'<script>window.location="/wp-admin/admin.php?page=cards"; </script>';
endif;

if(isset($_POST['edit'])) :

	// Save e-mail templates
	$wpdb->insert( 
	$table_cards, 
			array( 
				'description' => $_POST['description'],	// string
				'description_short' => $_POST['description_short'],	// string
				'price' => $_POST['price'],	// string
				'created_at' => date('Y-m-d H:i:s'),	// string
			),
			array('id' => $_POST['edit_id'])
		);
	echo'<script>window.location="/wp-admin/admin.php?page=cards"; </script>';
endif;
?>


<div class="wrap">
		<?php if(isset($_GET['page']) && $_GET['page']=='cards'  && isset($_GET['edit'])) : ?>

	<?php
	// Load data for edit
	$card = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE id = ".$_GET['edit']."", OBJECT);
	?>
			<?php    echo "<h2>" . __( 'Edit Card', 'oscimp_trdom' ) . "</h2>"; ?>
	<form method="POST">
		
		<p>
			Description: <input type="text" name="description" required="required" value="<?php echo $card->description; ?>" />
		</p>
		
		<p>
			Description short (will be used in iDEAL screen): <input type="text" name="description_short" required="required" value="<?php echo $card->description_short; ?>" />
		</p>
		
		<p>
			Price (in cents): <input type="text" name="price" required="required" value="<?php echo $card->price; ?>" /> &euro; 1,00 = 100
		</p>
		
		<input type="hidden" name="edit_id" value="<?php echo $card->id; ?>" />
		
		<p>
			<input type="submit" name="edit" value="Save" class="button button-primary button-large" />
		</p>
	</form>	
	<?php endif; ?>
	
	
	<?php    echo "<h2>" . __( 'Cards', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Description</th>
			<th class="manage-column column-columnname" scope="col">Description short</th>
			<th class="manage-column column-columnname" scope="col">Price</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
			<th class="manage-column column-columnname" scope="col">Actions</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Description</th>
			<th class="manage-column column-columnname" scope="col">Description short</th>
			<th class="manage-column column-columnname" scope="col">Price</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
			<th class="manage-column column-columnname" scope="col">Actions</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $item->id; ?></td>
			<td class="column-columnname"><?php echo $item->description; ?></td>
			<td class="column-columnname"><?php echo $item->description_short; ?></td>
			<td class="column-columnname">&euro; <?php echo number_format( ($item->price / 100), 2, ',', '.' ); ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($item->created_at)); ?></td>
			<td class="column-columnname"><a href="?page=cards&edit=<?php echo $item->id; ?>">Edit</a></td>
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
	
	<h2>Insert card</h2>
	<form method="POST">
		
		<p>
			Description: <input type="text" name="description" required="required" />
		</p>
		
		<p>
			Description short (will be used in iDEAL screen): <input type="text" name="description_short" required="required" />
		</p>
		
		<p>
			Price (in cents): <input type="text" name="price" required="required" />
		</p>
		
		
		<p>
			<input type="submit" name="insert" value="Save" class="button button-primary button-large" />
		</p>
	</form>
	
	
</div>
