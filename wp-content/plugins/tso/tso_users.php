<?php
	global $wpdb;
	$table_users = $wpdb->prefix . 'tso_users';
	$table_schools = $wpdb->prefix . 'tso_schools';
	$table_children = $wpdb->prefix . 'tso_children';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_users}" );
	$num_of_pages = ceil( $total / $limit );
	
	$fields = array(
    						'E-mail'=>'email', 
    						'Voornaam 1ste Ouder / verzorger'=> 'first_name_mother',
    						'Achternaam 1ste Ouder / verzorger'=> 'last_name_mother',
    						'1ste Ouder / verzorger telefoon'=> 'phone_mother',
    						'Voornaam 2de Ouder / verzorger'=> 'first_name_father',
    						'Achternaam 2de Ouder / verzorger'=> 'last_name_father',
    						'2de Ouder / verzorger telefoon'=> 'phone_father',
    						'Adres'=> 'address',
    						'Postcode'=> 'postalcode',
    						'Plaats'=> 'city',
    						'Telefoon bij onbereikbaar'=> 'phone_unreachable',
    						'Relatie tot kind(eren)'=> 'relation_child',
    						'Naam Dokter'=> 'name_doc',
    						'Telefoon Dokter'=> 'phone_doc',
    						'Adres Dokter'=> 'address_doc',
    						'Plaats Dokter'=> 'city_doc',
							'Naam Tandarts'=> 'name_dentist',
    						'Telefoon Tandarts'=> 'phone_dentist',
    						'Adres Tandarts'=> 'address_dentist',
    						'Plaats Tandarts'=> 'city_dentist',
    						'Dagen opvang'=> 'days_care',
						);
	
	$items = $wpdb->get_results( 
	"
	SELECT User.email AS user_email, User.id AS user_id, User.*, School.* 
	FROM {$table_users} AS User LEFT JOIN {$table_schools} AS School ON (User.school_id = School.id) ORDER BY School.name ASC
	"
	);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_users} WHERE id IN (".implode(',', $_POST['id']).")");
	echo'<script>window.location="/wp-admin/admin.php?page=users"; </script>';
endif;
?>


<div class="wrap">
	<?php if(isset($_GET['page']) && $_GET['page']=='users'  && isset($_GET['view'])) : ?>

	<?php
	// Load data for view
	$user = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id = ".$_GET['view']."", OBJECT);
	$children = $wpdb->get_results( "SELECT Child.* FROM {$table_children} AS Child WHERE user_id = ".$user->id."");
	?>
		<?php    echo "<h2>" . __( 'View User', 'oscimp_trdom' ) . "</h2>"; ?>
		<table style="padding: 5px;">
		<?php foreach ($fields as $key => $value) : ?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $user->$value; ?></td>
			</tr>
		<?php endforeach; ?>
	
		</table>
		<hr />
				<?php    echo "<h2>" . __( 'Children', 'oscimp_trdom' ) . "</h2>"; ?>
		<table style="padding: 5px;">
			<tr>
				<td>Naam</td>
				<td>Groep</td>
			</tr>
		<?php foreach ($children as $value) : ?>
			<tr>
				<td><?php echo $value->first_name; ?> <?php echo $value->last_name; ?></td>
				<td><?php echo $value->groep; ?></td>
			</tr>
		<?php endforeach; ?>
	
		</table>
		
	<?php endif; ?>
	
	
	
	<?php    echo "<h2>" . __( 'Users', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">1st Parent</th>
			<th class="manage-column column-columnname" scope="col">2nd Parent</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Address</th>
			<th class="manage-column column-columnname" scope="col">Postalcode</th>
			<th class="manage-column column-columnname" scope="col">City</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
			<th class="manage-column column-columnname" scope="col">Actions</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">1st Parent</th>
			<th class="manage-column column-columnname" scope="col">2nd Parent</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Address</th>
			<th class="manage-column column-columnname" scope="col">Postalcode</th>
			<th class="manage-column column-columnname" scope="col">City</th>
			<th class="manage-column column-columnname" scope="col">Created at</th>
			<th class="manage-column column-columnname" scope="col">Actions</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->user_id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $item->user_id; ?></td>
            <td class="column-columnname"><?php echo $item->name; ?></td>
			<td class="column-columnname"><?php echo $item->first_name_mother; ?> <?php echo $item->last_name_mother; ?></td>
			<td class="column-columnname"><?php echo $item->first_name_father; ?> <?php echo $item->last_name_father; ?></td>
			<td class="column-columnname"><?php echo $item->user_email; ?></td>
			<td class="column-columnname"><?php echo $item->address; ?></td>
			<td class="column-columnname"><?php echo $item->postalcode; ?></td>
			<td class="column-columnname"><?php echo $item->city; ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($item->created_at)); ?></td>
			<td class="column-columnname"><a href="?page=users&view=<?php echo $item->user_id; ?>">View</a></td>
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
