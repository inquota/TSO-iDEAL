<link rel='stylesheet' id='jquery-ui-css'  href='//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css?ver=4.2.2' type='text/css' media='all' />
<?php
global $wpdb;
$site_url = site_url();
$table_submissions = $wpdb->prefix . 'tso_submissions';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
	
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

# Init the class
$oIdeal = new TargetPayIdeal(null);

$banks = $oIdeal->getBanks();

$limit = 20; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_submissions}" );
$num_of_pages = ceil( $total / $limit );

// get all children
$children = $wpdb->get_results( 
"
SELECT 
	*
FROM 
	{$table_children}
ORDER BY 
	last_name 
ASC
"
);
// get all cards
$cards = $wpdb->get_results( 
"
SELECT 
	*
FROM 
	{$table_cards}
ORDER BY 
	description 
ASC
"
);

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
	User.first_name_father,
	User.last_name_father,
	User.first_name_mother,
	User.last_name_mother,
	School.name AS name_school,
	Child.first_name AS first_name,
	Child.last_name AS last_name
FROM 
	{$table_submissions} as Submission 
	LEFT JOIN {$table_users} AS User ON (Submission.user_id=User.id) 
	LEFT JOIN {$table_schools} AS School ON (Submission.school_id=School.id)
	LEFT JOIN {$table_children} AS Child ON (Submission.child_id=Child.id)
	ORDER BY Submission.created_at DESC, School.name ASC
"
);

if(isset($_POST['action_add'])) :
	
	$child_id =  $_POST['child_id'];
	$card_id =  $_POST['card_id'];
	$bank =  $_POST['bank'];
	$ec =  $_POST['ec'];
	if(empty($ec)) {
		$ec = 0;
	}
	$trxid =  $_POST['trxid'];
	if(empty($trxid)) {
		$trxid = 0;
	}
	
	
	$payment_status =  $_POST['payment_status'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$created_at =  $_POST['created_at'];

	// get Child
	$child = $wpdb->get_row(
	"
	SELECT 
	Child.*,
	User.* 
	FROM {$table_children} AS Child
	LEFT JOIN {$table_users} AS User ON (Child.user_id=User.id)
	WHERE Child.id = ".$child_id."", OBJECT);
	
	// get Card
	$card = $wpdb->get_row(
	"
	SELECT 
	*
	FROM {$table_cards} AS Card
	WHERE Card.id = ".$card_id."", OBJECT);
		
	$wpdb->insert( 
		$table_submissions, 
			array( 
				'user_id' => $child->user_id,	// string
				'child_id' => $child_id,	// string
				'school_id' => $child->school_id,	// string
				'groep' => $child->groep,	// string
				'card' => $card->description,	// string
				'price' => $card->price,	// string
				'bank' => $bank,	// string
				'ec' => $ec,	// string
				'trxid' => $trxid,	// string
				'ip' => $ip,	// string
				'payment_status' => $payment_status,	// string
				'created_at' => $created_at . ' 00:00:00'	// string
			)
		);

	echo'<script>window.location="'.$site_url.'/wp-admin/admin.php?page=tso"; </script>';
endif;	

if(isset($_POST['action_delete'])) :
	$wpdb->query( "DELETE FROM {$table_submissions} WHERE id IN (".implode(',', $_POST['id']).")");
	echo'<script>window.location="'.$site_url.'/wp-admin/admin.php?page=tso"; </script>';
endif;


?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Betalingen', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	<?php    echo "<h3>" . __( 'Invoeren', 'oscimp_trdom' ) . "</h3>"; ?>
	<p>
		Je kan hier een betaling handmatig invoeren.
	</p>
	<table style="width: 100%; background: #FFFFFF;">
		<tr>
			<td>Kind</td>
			<td>
				<select name="child_id">
					<?php foreach($children as $child) : ?>
						<?php if(!empty($child->first_name) && !empty($child->last_name)) : ?>
							<option value="<?php echo $child->id; ?>"><?php echo $child->last_name; ?>, <?php echo $child->first_name; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Strippenkaart</td>
			<td>
				<select name="card_id">
					<?php foreach($cards as $card) : ?>
						<option value="<?php echo $card->id; ?>"><?php echo $card->description; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Bank</td>
			<td>
				<select name="bank">
					<?php foreach($banks as $key_bank => $bank) : ?>
						<option value="<?php echo $key_bank; ?>"><?php echo $bank; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>EC</td>
			<td><input type="text" name="ec" /> Geen EC? Vul dan een 0 in.</td>
		</tr>
		<tr>
			<td>Transaction id</td>
			<td><input type="text" name="trxid" /> Geen Transaction id? Vul dan een 0 in.</td>
		</tr>
		<tr>
			<td>Betaald</td>
			<td>
				<select name="payment_status">
					<option value="1">Ja</option>
					<option value="0">Nee</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Datum</td>
			<td><input type="text" name="created_at" id="datepicker" readonly="readonly" /></td>
		</tr>
	</table>
	<input type="submit" name="action_add" value="Toevoegen" class="button button-primary button-large" />
	<hr />
	
	<input type="submit" name="action_delete" value="Delete" onClick="return confirm('Weet je het zeker?');" class="button button-primary button-large" />
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
			<td class="column-columnname"><?php echo $booking->first_name_mother; ?> <?php echo $booking->last_name_mother; ?> -  <?php echo $booking->first_name_father; ?> <?php echo $booking->last_name_father; ?></td>
			<td class="column-columnname"><?php echo $booking->name_school; ?></td>
			<td class="column-columnname"><?php echo $booking->groep; ?></td>
			<td class="column-columnname"><?php echo $booking->first_name . ' ' . $booking->last_name; ?></td>
			<td class="column-columnname"><?php echo $booking->card; ?></td>
			<td class="column-columnname"><?php if($booking->payment_status==1) { echo "<span style='color: green;'>Ja</span>"; }else{ echo "<span style='color: red;'>Nee</span>"; } ?></td>
			<td class="column-columnname"><?php if(!isset($banks[$booking->bank])) { echo 'Onbekend'; }else{ echo $banks[$booking->bank]; } ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($booking->created_at . "+2 hours")); ?></td>
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
	
	
	<script type='text/javascript' src='//code.jquery.com/ui/1.11.4/jquery-ui.js?ver=4.2.2'></script>
	<script>	
	var dateToday = new Date();
	
	// display datepicker
	jQuery( "#datepicker" ).datepicker({
		altField: "#dateHidden",
	    // The format you want
	    altFormat: "yy-mm-dd",
	    // The format the user actually sees
	    dateFormat: "yy-mm-dd",
  	});
  
	</script>
