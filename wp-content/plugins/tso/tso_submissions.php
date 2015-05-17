<?php
global $wpdb;
$table_submissions = $wpdb->prefix . 'tso_submissions';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_children = $wpdb->prefix . 'tso_children';
	
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

# Init the class
$oIdeal = new TargetPayIdeal(null);

$banks = $oIdeal->getBanks();

$limit = 20; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_submissions}" );
$num_of_pages = ceil( $total / $limit );

$oBookings = $wpdb->get_results( 
"
SELECT 
	Submission.id,
	Submission.created_at,
	Submission.groep,
	Submission.card,
	Submission.price,
	Submission.payment_status,
	Submission.bank,
	User.name_father,
	User.name_mother,
	Child.name AS name_child,
	School.name AS name_school
FROM 
	{$table_submissions} as Submission 
	LEFT JOIN {$table_users} AS User ON (Submission.user_id=User.id) 
	LEFT JOIN {$table_schools} AS School ON (Submission.school_id=School.id)
	LEFT JOIN {$table_children} AS Child ON (Submission.child_id=Child.id)
"
);

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_submissions} WHERE id IN (".implode(',', $_POST['id']).")");
	echo '<meta http-equiv="refresh" content="0; URL=/wp-admin/admin.php?page=tso">';
endif;


?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Inzendingen', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	
	<input type="submit" name="action_delete" value="Delete" class="button button-primary button-large" />
	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-cb check-column" scope="col"></th> 
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Ouders / verzorgers</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Kind</th>
			<th class="manage-column column-columnname" scope="col">Strippenkaart</th>
			<th class="manage-column column-columnname" scope="col">Betaald</th>
			<th class="manage-column column-columnname" scope="col">Bank</th>
			<th class="manage-column column-columnname" scope="col">Created</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Ouders / verzorgers</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Kind</th>
			<th class="manage-column column-columnname" scope="col">Strippenkaart</th>
			<th class="manage-column column-columnname" scope="col">Betaald</th>
			<th class="manage-column column-columnname" scope="col">Bank</th>
			<th class="manage-column column-columnname" scope="col">Created</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($oBookings as $booking) : ?>
        <tr class="alternate">
            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $booking->id; ?>" name="id[]" /></th>
            <td class="column-columnname"><?php echo $booking->id; ?></td>
			<td class="column-columnname"><?php echo $booking->name_father; ?> <?php echo $booking->name_mother; ?></td>
			<td class="column-columnname"><?php echo $booking->name_school; ?></td>
			<td class="column-columnname"><?php echo $booking->groep; ?></td>
			<td class="column-columnname"><?php echo $booking->name_child; ?></td>
			<td class="column-columnname"><?php echo $booking->card; ?></td>
			<td class="column-columnname"><?php if($booking->payment_status==1) { echo "<span style='color: green;'>Ja</span>"; }else{ echo "<span style='color: red;'>Nee</span>"; } ?></td>
			<td class="column-columnname"><?php if(!isset($banks[$booking->bank])) { echo 'Onbekend'; }else{ echo $banks[$booking->bank]; } ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($booking->created_at)); ?></td>
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
	
	
<div>
