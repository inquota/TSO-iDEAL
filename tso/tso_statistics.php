<?php
global $wpdb;
$table_children = $wpdb->prefix . 'tso_children';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_submissions = $wpdb->prefix . 'tso_submissions';

$salesPerChild = $wpdb->get_results("SELECT COUNT(`child_id`) AS CountChild, SUM(`price`) AS Price, Child.*, Child.id AS childId, School.*   FROM {$table_submissions} AS Submission 
LEFT JOIN {$table_children} AS Child ON (Child.id = Submission.child_id) 
LEFT JOIN {$table_users} AS User ON (Child.user_id=User.id) 
LEFT JOIN {$table_schools} AS School ON (School.id=User.school_id) WHERE price != 0 AND payment_status = 1
GROUP BY `child_id`  ORDER BY School.name ASC, Price ASC");

$salesPerSchool = $wpdb->get_results("SELECT SUM(`price`) AS Price, School.*   FROM {$table_submissions} AS Submission LEFT JOIN {$table_schools} AS School ON (School.id = Submission.school_id) WHERE price != 0  AND payment_status = 1 GROUP BY `school_id` ORDER BY School.name ASC");

$submissionResult = $wpdb->get_row( "SELECT SUM(`price`) AS Price FROM {$table_submissions}", OBJECT );

$cards = $wpdb->get_results( 
			"
			SELECT 
				COUNT(`card`) AS CountCard,
				Submission.card
			FROM 
				{$table_submissions} as Submission 
			GROUP BY Submission.card
			"
			);
?>


<div class="wrap">
<?php echo "<h2>" . __( 'Statistieken', 'oscimp_trdom' ) . "</h2>"; ?>
<p>
	Totaal aantal verkopen: &euro; <?php echo number_format( $submissionResult->Price / 100, 2, ',', '.' ); ?>
</p>
<p>
	<table style="background: #FFF; width: 100%;">
		<tr>
			<td><strong>Strippenkaart</strong></td>
			<td><strong>Aantal</strong></td>
		</tr>
		<?php
		foreach($cards as $card){ ?>
		<tr>
			<td><?php echo html_entity_decode($card->card); ?></td>
			<td><?php echo $card->CountCard; ?></td>
		</tr>
		<?php 
		}
	?>
	</table>
</p>
<hr />
<h3>Verkopen per school</h3>
<table style="background: #FFF; width: 100%;">
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
<hr />
<h3>Verkopen per kind</h3>
<table style="background: #FFF; width: 100%;">
<tr>
	<td><strong>Naam</strong></td>
	<td><strong>Groep</strong></td>
	<td><strong>School</strong></td>
	<td><strong>Aantal</strong></td>
	<td><strong>Datum</strong></td>
	<td><strong>Bedrag</strong></td>
</tr>
<?php foreach($salesPerChild as $child) : ?>
<tr>
	<td valign="top" style="border-bottom: 1px solid #000; padding-top: 2px; padding-bottom: 2px;"><?php echo $child->first_name; ?> <?php echo $child->last_name; ?></td>
	<td valign="top" style="border-bottom: 1px solid #000; padding-top: 2px; padding-bottom: 2px;"><?php echo $child->groep; ?></td>
	<td valign="top" style="border-bottom: 1px solid #000; padding-top: 2px; padding-bottom: 2px;"><?php echo $child->name; ?></td>
	<td valign="top" style="border-bottom: 1px solid #000; padding-top: 2px; padding-bottom: 2px;"><?php 
	$stripCards = $wpdb->get_results("SELECT *  FROM {$table_submissions}
			 WHERE price != 0 AND payment_status = 1 AND child_id = ".$child->childId."
			");
			foreach($stripCards as $card) {
				echo $card->card . '<br />';
			}
		 ?></td>
		 <td valign="top" style="border-bottom: 1px solid #000; padding-top: 2px; padding-bottom: 2px;"><?php 
	$stripCards = $wpdb->get_results("SELECT *  FROM {$table_submissions}
			 WHERE price != 0 AND payment_status = 1 AND child_id = ".$child->childId."
			");
			foreach($stripCards as $card) {
				echo date('d-m-Y H:i', strtotime($card->created_at . "+2 hours")) . '<br />';
			}
		 ?></td>
	<td valign="top" style="border-bottom: 1px solid #000;">&euro; <?php echo number_format( $child->Price / 100, 2, ',', '.' ); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>