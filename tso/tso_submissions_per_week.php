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
		WHERE year(Submission.created_at)= ".date('Y')." AND week(Submission.created_at, 3)= ".date('W').""
		);
	?>
	<hr />
	<table class="widefat fixed" cellspacing="0">
		<thead>
	    <tr>
			<th class="manage-column column-columnname" scope="col">Ouders / verzorgers</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Kind</th>
			<th class="manage-column column-columnname" scope="col">Strippenkaart</th>
			<th class="manage-column column-columnname" scope="col">Betaald</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>
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
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
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
	?>
	
	<?php
	if(!empty($submissions)) :
	?>
	<h2>Week <?php echo $i; ?> <a href="#" class="toggleWeek" id="<?php echo $i; ?>">tonen/verbergen</a></h2>
	<table class="widefat fixed" cellspacing="0" style="display: none;" id="week<?php echo $i; ?>">
		<thead>
	    <tr>
			<th class="manage-column column-columnname" scope="col">Ouders / verzorgers</th>
			<th class="manage-column column-columnname" scope="col">School</th>
			<th class="manage-column column-columnname" scope="col">Groep</th>
			<th class="manage-column column-columnname" scope="col">Kind</th>
			<th class="manage-column column-columnname" scope="col">Strippenkaart</th>
			<th class="manage-column column-columnname" scope="col">Betaald</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>
	    </tr>
	    </thead>
		<tbody>
		<?php foreach($submissions as $result) : ?>
		<tr>
			<td class="column-columnname"><?php echo $result->first_name_mother; ?> <?php echo $result->last_name_mother; ?> -  <?php echo $result->first_name_father; ?> <?php echo $result->last_name_father; ?></td>
			<td class="column-columnname"><?php echo $result->name_school; ?></td>
			<td class="column-columnname"><?php echo $result->groep; ?></td>
			<td class="column-columnname"><?php echo $result->first_name . ' ' . $result->last_name; ?></td>
			<td class="column-columnname"><?php echo $result->card; ?></td>
			<td class="column-columnname"><?php if($result->payment_status==1) { echo "<span style='color: green;'>Ja</span>"; }else{ echo "<span style='color: red;'>Nee</span>"; } ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($result->created_at . "+2 hours")); ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	
	<?php
	endfor; 
	?>
<div>
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
</script>