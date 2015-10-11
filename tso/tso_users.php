<?php
	global $wpdb;
	$site_url = site_url();
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
    						'Huisnummer'=> 'number',
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
    						'Plaats Tandarts'=> 'city_dentist'
						);
	
	$items = $wpdb->get_results( 
	"
	SELECT User.email AS user_email, User.id AS user_id, User.*, User.created_at AS userCreatedAt, School.* 
	FROM {$table_users} AS User LEFT JOIN {$table_schools} AS School ON (User.school_id = School.id) ORDER BY School.name ASC
	"
	);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_users} WHERE id IN (".implode(',', $_POST['id']).")");
	echo'<script>window.location="'.$site_url.'/wp-admin/admin.php?page=users"; </script>';
endif;
?>


<div class="wrap">
	<?php if(isset($_GET['page']) && $_GET['page']=='users'  && isset($_GET['view'])) : ?>
	<?php
	// Load data for view
	$user = $wpdb->get_row( "SELECT * FROM {$table_users}  WHERE id = ".$_GET['view']."", OBJECT);
	$school = $wpdb->get_row( "SELECT * FROM {$table_schools}  WHERE id = ".$user->school_id."", OBJECT);
	$children = $wpdb->get_results( "SELECT Child.* FROM {$table_children} AS Child WHERE user_id = ".$user->id."");
	
	$aChildren=array();
	foreach($children as $child) {
		$aChildren[]= $child->first_name . ' ' . $child->last_name;
	}
	if(count($aChildren) > 1){
		$aChildren = implode('-', $aChildren);
	}else{
		$aChildren = $aChildren[0];
	}
	?>
	<p>
		<a href="#" class="exportToPDF button button-primary button-large" id="export<?php echo $aChildren; ?>">PDF Export</a>
	</p>
	<div id="tables">
		<?php    echo "<h2>" . __( 'Gegevens ouders', 'oscimp_trdom' ) . "</h2>"; ?>
		<table style="padding: 5px; background: #FFF; width: 100%;">
		<?php foreach ($fields as $key => $value) : ?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $user->$value; ?></td>
			</tr>
		<?php endforeach; ?>
	
		</table>
		<hr />
				<?php    echo "<h2>" . __( 'Gegevens kinderen', 'oscimp_trdom' ) . "</h2>"; ?>
		<table style="padding: 5px; background: #FFF; width: 100%;">
			<tr>
				<td>Basisschool</td>
				<td><?php echo $school->name; ?></td>
			</tr>
			<tr>
				<td>Dagen opvang</td>
				<td><?php echo $user->days_care; ?></td>
			</tr>
		<?php foreach ($children as $value) : ?>
			<tr>
				<td>Naam en groep</td>
				<td><?php echo $value->first_name; ?> <?php echo $value->last_name; ?> - Groep <?php echo $value->groep; ?></td>
			</tr>
		<?php endforeach; ?>
			<tr>
				<td>Mijn kind(eren) blijft/blijven niet op vaste dagen over.</td>
				<td><?php echo $user->toelichting1; ?></td>
			</tr>
			<tr>
				<td>Mijn kinderen blijven niet op de zelfde dagen over.</td>
				<td><?php echo $user->toelichting2; ?></td>
			</tr>
			<tr>
				<td>Bijzonderheden kind(eren).</td>
				<td><?php echo $user->toelichting3; ?></td>
			</tr>
		</table>
		
	<?php endif; ?>
	</div>
	
	
	<?php    echo "<h2>" . __( 'Aanmeldingen', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">1e Verzorger</th>
			<th class="manage-column column-columnname" scope="col">2e Verzorger</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Adres</th>
			<th class="manage-column column-columnname" scope="col">Postcode</th>
			<th class="manage-column column-columnname" scope="col">Stad</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>
			    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">1e Verzorger</th>
			<th class="manage-column column-columnname" scope="col">2e Verzorger</th>
			<th class="manage-column column-columnname" scope="col">E-mail</th>
			<th class="manage-column column-columnname" scope="col">Adres</th>
			<th class="manage-column column-columnname" scope="col">Postcode</th>
			<th class="manage-column column-columnname" scope="col">Stad</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>


    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($items as $item) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $item->user_id; ?>" name="id[]" /></th>
            <td class="column-columnname"><a href="?page=users&view=<?php echo $item->user_id; ?>"><?php echo $item->user_id; ?></a></td>
            <td class="column-columnname"><?php echo $item->name; ?></td>
			<td class="column-columnname"><a href="?page=users&view=<?php echo $item->user_id; ?>"><?php echo $item->first_name_mother; ?> <?php echo $item->last_name_mother; ?></a></td>
			<td class="column-columnname"><a href="?page=users&view=<?php echo $item->user_id; ?>"><?php echo $item->first_name_father; ?> <?php echo $item->last_name_father; ?></a></td>
			<td class="column-columnname"><?php echo $item->user_email; ?></td>
			<td class="column-columnname"><?php echo $item->address; ?> <?php echo $item->number; ?></td>
			<td class="column-columnname"><?php echo $item->postalcode; ?></td>
			<td class="column-columnname"><?php echo $item->city; ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i', strtotime($item->userCreatedAt)); ?></td>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.1.135/jspdf.min.js"></script>
<script>

var doc = new jsPDF();
var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
};

jQuery('.exportToPDF').click(function (event) {
	event.preventDefault();		
	var id=event.target.id;
	var weeknumber = id.replace("export", "");
    doc.fromHTML(jQuery('#tables').html(), 15, 15, {
        'width': 170,
            'elementHandlers': specialElementHandlers
    });
    doc.save('Aanmelding-'+weeknumber+'.pdf');
});
</script>
