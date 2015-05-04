<?php
/*
Template Name: TSO - Card - Add (Strippentkaart afnemen)
*/
global $wpdb;

$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_settings = $wpdb->prefix . 'tso_settings';
$table_users = $wpdb->prefix . 'tso_users';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

// check if user is logged in
if($_SESSION['user']==null){
	echo'<script>window.location="'.$settings->url_login.'"; </script>';
}

$sessionUser = $_SESSION['user'];

/**
 * Get Children from user_id
 */ 
$urlparts = explode('/',$_SERVER['REQUEST_URI']);

$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id = 1", OBJECT );
 
$user = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id = ".$sessionUser->id, OBJECT );

// groepen
$groups = array('1','1a','1b','2','2a','2b','3','3a','3b','4','4a','4b','5','5a','5b','6','6a','6b','7','7a','7b','8','8a','8b');

// card / strippenkaart
$cards = $wpdb->get_results("SELECT * FROM {$table_cards}"); 
 
$error= ''; 
$error_flag = true;

$fields = array(
    						'E-mail'=>'email', 
    						'Naam vader'=> 'name_father',
    						'Telefoon vader'=> 'phone_father',
    						'Naam moeder'=> 'name_mother',
    						'Telefoon moeder'=> 'phone_mother',
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

if(isset($_POST['submit'])){
	
	
	unset($_POST['submit']);
			
	if($error_flag==true){
			// save
		$wpdb->update( 
		$table_users, 
				array( 
						'email'=>$_POST['email'],
						'name_father'=>$_POST['name_father'],
						'phone_father'=>$_POST['phone_father'],
						'name_mother'=>$_POST['name_mother'],
						'phone_mother'=>$_POST['phone_mother'],
						'address'=>$_POST['address'],
						'postalcode'=>$_POST['postalcode'],
						'city'=>$_POST['city'],
						'phone_unreachable'=>$_POST['phone_unreachable'],
						'relation_child'=>$_POST['relation_child'],
						'name_doc'=>$_POST['name_doc'],
						'phone_doc'=>$_POST['phone_doc'],
						'address_doc'=>$_POST['address_doc'],
						'city_doc'=>$_POST['city_doc'],
						'name_dentist'=>$_POST['name_dentist'],
						'phone_dentist'=>$_POST['phone_dentist'],
						'address_dentist'=>$_POST['address_dentist'],
						'city_dentist'=>$_POST['city_dentist'],
						'days_care'=>$_POST['days_care'], 
				), 
				array( 'id' => $sessionUser->id )
			);
			
			echo'<script>window.location="'.$settings->url_profile_edit.'"; </script>';
	}


}

?>
	
	<style>
		input[type="text"]{
			width: 200px;
		}
	</style>
	
	<div id="tso-menu">
		<a href="<?php echo $settings->url_card_overview; ?>">Strippenkaart</a> <a href="<?php echo $settings->url_profile_edit; ?>">Gegevens wijzigen</a> - <a href="?action=logout">Uitloggen</a>
	</div>
	
	<form method="POST">
		<?php if(isset($error)) {
			echo $error;
		}?>
		<table style="padding: 5px;" id="account_edit">
		

		<?php foreach ($fields as $key => $value) : ?>
		
			<tr>
				<td><?php echo $key; ?></td>
				<td><input type="text" name="<?php echo $value; ?>" required="required" value="<?php echo $user->$value; ?>" /></td>
			</tr>
			
		<?php endforeach; ?>
	
		</table>
		<button type="submit" name="submit" class="">Profiel aanpassen</button>
	</form>
	        	
	 