<?php
global $wpdb;
$site_url = site_url();
$table_submissions = $wpdb->prefix . 'tso_submissions';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_children = $wpdb->prefix . 'tso_children';
?>


<div class="wrap">
	
	<h1>Huidige week: Week <?php echo date('W'); ?></h1>
	<p>
		<a href="#" class="exportToPDFCurrentWeek" id="exportCurrentWeek<?php echo date('W'); ?>">Export</a>
	</p>
	<?php
		$submissionsCurrentWeek = $wpdb->get_results( 
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
		WHERE year(Submission.created_at)= ".date('Y')." AND week(Submission.created_at, 3)= ".date('W')."
		ORDER BY Submission.created_at ASC, School.name ASC
		"
		);
		
						
			$cards = $wpdb->get_results( 
				"
				SELECT 
					COUNT(`card`) AS CountCard,
					Submission.card
				FROM 
					{$table_submissions} as Submission 
				WHERE year(Submission.created_at)= ".date('Y')." AND week(Submission.created_at, 3)= ".date('W')."
				GROUP BY Submission.card
				"
				);
		$totalPrice = array();
	?>
	<hr />
	<table class="widefat fixed" cellspacing="0">
		<thead>
	    <tr>
			<th class="manage-column column-columnname" scope="col">Ouder/verzorger</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Kind</th>
			<th class="manage-column column-columnname" scope="col">Strippenkaart</th>
			<th class="manage-column column-columnname" scope="col">Betaald</th>
			<th class="manage-column column-columnname" scope="col">Datum</th>
	    </tr>
	    </thead>
		<tbody>
		<?php foreach($submissionsCurrentWeek as $resultCurrentWeek) : ?>
		<tr>
			<td class="column-columnname"><?php echo $resultCurrentWeek->first_name_mother; ?> <?php echo $resultCurrentWeek->last_name_mother; ?> -  <?php echo $resultCurrentWeek->first_name_father; ?> <?php echo $resultCurrentWeek->last_name_father; ?></td>
			<td class="column-columnname"><?php echo $resultCurrentWeek->name_school; ?></td>
			<td class="column-columnname"><?php echo $resultCurrentWeek->groep; ?></td>
			<td class="column-columnname"><?php echo $resultCurrentWeek->first_name . ' ' . $resultCurrentWeek->last_name; ?></td>
			<td class="column-columnname"><?php echo $resultCurrentWeek->card; ?></td>
			<td class="column-columnname"><?php if($resultCurrentWeek->payment_status==1) { echo "<span style='color: green;'>Ja</span>"; }else{ echo "<span style='color: red;'>Nee</span>"; } ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($resultCurrentWeek->created_at . "+2 hours")); ?></td>
			<?php
			if($resultCurrentWeek->payment_status==1) {
				$totalPrice[] = $resultCurrentWeek->price;	
			}
			?>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p>
		<h3>Totaal: € <?php echo (array_sum($totalPrice) / 100); ?> (alleen betaalde)</h3>
	</p>
	<p>
		<table>
			<tr>
				<td><strong>Aantal</strong></td>
				<td><strong>Strippenkaart</strong></td>
			</tr>
			<?php
			foreach($cards as $card){ ?>
			<tr>
				<td><?php echo $card->CountCard; ?></td>
				<td><?php echo html_entity_decode($card->card); ?></td>
			</tr>
			<?php 
			}
		?>
		</table>
	</p>
	<hr />
	<?php
	for($i=1; $i <= 52; $i++) :
	?>
	
	<?php 
	$submissions = $wpdb->get_results( 
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
		WHERE year(Submission.created_at)= ".date('Y')." AND week(Submission.created_at, 3)= ".$i.""
		);
		
		
		$cardsWeeks = $wpdb->get_results( 
				"
				SELECT 
					COUNT(`card`) AS CountCard,
					Submission.card
				FROM 
					{$table_submissions} as Submission 
				WHERE year(Submission.created_at)= ".date('Y')." AND week(Submission.created_at, 3)= ".$i."
				GROUP BY Submission.card
				"
				);
	?>
	
	<?php
	if(!empty($submissions)) :
	?>
	<h2>Week <?php echo $i; ?> <a href="#" class="toggleWeek" id="<?php echo $i; ?>">tonen/verbergen</a> <a href="#" class="exportToPDF" id="export<?php echo $i; ?>">Export</a></h2>
	<div style="display: none;" id="week<?php echo $i; ?>">
	<table class="widefat fixed" cellspacing="0">
		<thead>
	    <tr>
			<th class="manage-column column-columnname" scope="col">Ouder/verzorger</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Kind</th>
			<th class="manage-column column-columnname" scope="col">Strippenkaart</th>
			<th class="manage-column column-columnname" scope="col">Betaald</th>
			<th class="manage-column column-columnname" scope="col">Datum</th>
	    </tr>
	    </thead>
		<tbody>
		<?php 
		$totalPriceWeek = array();
		foreach($submissions as $result) : ?>
		<tr>
			<td class="column-columnname"><?php echo $result->first_name_mother; ?> <?php echo $result->last_name_mother; ?> -  <?php echo $result->first_name_father; ?> <?php echo $result->last_name_father; ?></td>
			<td class="column-columnname"><?php echo $result->name_school; ?></td>
			<td class="column-columnname"><?php echo $result->groep; ?></td>
			<td class="column-columnname"><?php echo $result->first_name . ' ' . $result->last_name; ?></td>
			<td class="column-columnname"><?php echo $result->card; ?></td>
			<td class="column-columnname"><?php if($result->payment_status==1) { echo "<span style='color: green;'>Ja</span>"; }else{ echo "<span style='color: red;'>Nee</span>"; } ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($result->created_at . "+2 hours")); ?></td>
			<?php
			if($result->payment_status==1) {
				$totalPriceWeek[] = $result->price;	
			}
			?>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p>
		Totaal: <?php echo (array_sum($totalPriceWeek) / 100); ?> EURO (alleen betaalde)
	</p>
	<p>
		<table>
			<tr>
				<td><strong>Aantal</strong></td>
				<td><strong>Strippenkaart</strong></td>
			</tr>
			<?php
			foreach($cardsWeeks as $cardWeek){ ?>
			<tr>
				<td><?php echo $cardWeek->CountCard; ?></td>
				<td><?php echo html_entity_decode($cardWeek->card); ?></td>
			</tr>
			<?php 
			}
		?>
		</table>
	</p>
	</div>
	<?php endif; ?>
	
	<?php
	endfor; 
	?>
<div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.1.135/jspdf.min.js"></script>
<script>
jQuery('.toggleWeek').click(function (event) {
event.preventDefault();		
	var id=event.target.id;
	
  if ( jQuery( "#week"+id ).is( ":hidden" ) ) {
    jQuery( "#week"+id ).slideDown( "slow" );
  } else {
    jQuery( "#week"+id ).hide();
  }
});

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
	var d = new Date();
	var year = d.getFullYear(); 
    doc.fromHTML(jQuery('#week'+weeknumber).html(), 15, 15, {
        'width': 170,
            'elementHandlers': specialElementHandlers
    });
    doc.save('Betalingen-week'+weeknumber+'-'+year+'.pdf');
});

jQuery('.exportToPDFCurrentWeek').click(function (event) {
	event.preventDefault();		
	var id=event.target.id;
	var weeknumber = id.replace("exportCurrentWeek", "");
	var d = new Date();
	var year = d.getFullYear(); 
    doc.fromHTML(jQuery('#exportToPDFCurrentWeek').html(), 15, 15, {
        'width': 170,
            'elementHandlers': specialElementHandlers
    });
    doc.save('Betalingen-week'+weeknumber+'-'+year+'.pdf');
});

</script>