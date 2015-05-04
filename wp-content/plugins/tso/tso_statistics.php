<?php
	global $wpdb;
	$table_children = $wpdb->prefix . 'tso_children';
	$table_schools = $wpdb->prefix . 'tso_schools';
	$table_users = $wpdb->prefix . 'tso_users';
	$table_submissions = $wpdb->prefix . 'tso_submissions';
		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

	$limit = 20; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_children}" );
	$num_of_pages = ceil( $total / $limit );
	
	$salesPerChild = $wpdb->get_results("SELECT SUM(`price`) AS Price, Child.*   FROM {$table_submissions} AS Submission LEFT JOIN {$table_children} AS Child ON (Child.id = Submission.child_id) GROUP BY `child_id`");
	$salesPerSchool = $wpdb->get_results("SELECT SUM(`price`) AS Price, School.*   FROM {$table_submissions} AS Submission LEFT JOIN {$table_schools} AS School ON (School.id = Submission.school_id) GROUP BY `school_id`");

	$submissionResult = $wpdb->get_row( "SELECT SUM(`price`) AS Price FROM {$table_submissions}", OBJECT );
	?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Statistics', 'oscimp_trdom' ) . "</h2>"; ?>
Totaal aantal verkopen: &euro; <?php echo number_format( $submissionResult->Price / 100, 2, ',', '.' ); ?>
<fieldset>
	<legend><strong>Verkopen per kind</strong></legend>
	<table>
		<tr>
			<td>Naam</td>
			<td>Bedrag</td>
		</tr>
		<?php foreach($salesPerChild as $child) : ?>
		<tr>
			<td><?php echo $child->name; ?></td>
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
