<?php
	global $wpdb;
	$table_children = $wpdb->prefix . 'tso_children';
	$table_schools = $wpdb->prefix . 'tso_schools';
	$table_users = $wpdb->prefix . 'tso_users';
	$table_submissions = $wpdb->prefix . 'tso_submissions';
	
	$salesPerChild = $wpdb->get_results("SELECT COUNT(`child_id`) AS CountChild, SUM(`price`) AS Price, Child.*, School.*   FROM {$table_submissions} AS Submission 
	LEFT JOIN {$table_children} AS Child ON (Child.id = Submission.child_id) 
	LEFT JOIN {$table_users} AS User ON (Child.user_id=User.id) 
	LEFT JOIN {$table_schools} AS School ON (School.id=User.school_id) WHERE price != 0 AND payment_status = 1
	GROUP BY `child_id`  ORDER BY School.name ASC, Price ASC");
	
	$salesPerSchool = $wpdb->get_results("SELECT SUM(`price`) AS Price, School.*   FROM {$table_submissions} AS Submission LEFT JOIN {$table_schools} AS School ON (School.id = Submission.school_id) WHERE price != 0  AND payment_status = 1 GROUP BY `school_id` ORDER BY School.name ASC");

	$submissionResult = $wpdb->get_row( "SELECT SUM(`price`) AS Price FROM {$table_submissions}", OBJECT );
	?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Statistieken', 'oscimp_trdom' ) . "</h2>"; ?>
Totaal aantal verkopen: &euro; <?php echo number_format( $submissionResult->Price / 100, 2, ',', '.' ); ?>
<fieldset>
	<legend><strong>Verkopen per kind</strong></legend>
	<table style="background: #FFF; width: 100%;">
		<tr>
			<td><strong>Naam</strong></td>
			<td><strong>Groep</strong></td>
			<td><strong>School</strong></td>
			<td><strong>Aantal</strong></td>
			<td><strong>Bedrag</strong></td>
		</tr>
		<?php foreach($salesPerChild as $child) : ?>
		<tr>
			<td><?php echo $child->first_name; ?> <?php echo $child->last_name; ?></td>
			<td><?php echo $child->groep; ?></td>
			<td><?php echo $child->name; ?></td>
			<td><?php echo $child->CountChild; ?></td>
			<td>&euro; <?php echo number_format( $child->Price / 100, 2, ',', '.' ); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
</fieldset>
<hr />
<fieldset>
	<legend><strong>Verkopen per school</strong></legend>
	<table>
		<tr>
			<td>Naam</td>
			<td>Bedrag</td>
		</tr>
		<?php foreach($salesPerSchool as $school) : ?>
		<tr>
			<td><?php echo $school->name; ?></td>
			<td>&euro; <?php echo number_format( $school->Price / 100, 2, ',', '.' ); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
</fieldset>
</div>