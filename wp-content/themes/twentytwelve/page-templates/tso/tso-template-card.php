<?php
/*
Template Name: TSO - Card (Strippentkaart)
*/

global $wpdb;

$table_children = $wpdb->prefix . 'tso_children';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_cards = $wpdb->prefix . 'tso_cards';

// check if user is logged in
if($_SESSION['user']==null){
	echo '<meta http-equiv="refresh" content="0; URL=/inloggen/">';
}

$sessionUser = $_SESSION['user'];

/**
 * Get Children from user_id
 */ 
$results = $wpdb->get_results( "SELECT 
									Child.name AS name_child, 
									Child.groep, 
									Child.card, 
									Child.id,
									School.name AS name_school,
									Card.description 
							FROM 
								{$table_children} AS Child 
							LEFT JOIN 
								{$table_schools} AS School ON (Child.school_id=School.id)
							LEFT JOIN 
								{$table_cards} AS Card ON (Child.card=Card.id)	
							WHERE 
								user_id = ".$sessionUser->id . "", OBJECT );
if(isset($_GET['action']) && $_GET['action']=='action'){
	unset($_SESSION['user']);
	session_destroy();
	echo '<meta http-equiv="refresh" content="0; URL=/inloggen/">';
}								
?>
<a href="#">Gegevens wijzigen</a> - <a href="?action=logout">Uitloggen</a>
<hr />	        	
<?php if(!$results) : ?>
	U heeft geen kinderen toegevoegd	
<?php else : ?>
	<table>
		<tr>
			<th style="padding: 5px;">School</th>
			<th style="padding: 5px;">Naam</th>
			<th style="padding: 5px;">Groep</th>
			<th style="padding: 5px;">Huidige strippenkaart</th>
			<th style="padding: 5px;">Acties</th>
		</tr>
	<?php foreach($results as $item) : ?>
		<tr>
			<td style="padding: 5px;"><?php echo $item->name_school; ?></td>
			<td style="padding: 5px;"><?php echo $item->name_child; ?></td>
			<td style="padding: 5px;"><?php if($item->groep==null){ echo 'Onbekend'; }else{ echo $item->groep; }; ?></td>
			<td style="padding: 5px;"><?php echo $item->description; ?></td>
			<td style="padding: 5px;"><a href="/strippenkaart/strippenkaart-toevoegen/<?php echo $item->id; ?>">Nieuwe strippenkaart afnemen</a></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>	
	        