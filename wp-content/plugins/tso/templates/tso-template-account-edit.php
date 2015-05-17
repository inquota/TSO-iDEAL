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

$user = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id = ".$sessionUser->id, OBJECT );

// groepen
$groups = array('1','1a','1b','2','2a','2b','3','3a','3b','4','4a','4b','5','5a','5b','6','6a','6b','7','7a','7b','8','8a','8b');

// card / strippenkaart
$cards = $wpdb->get_results("SELECT * FROM {$table_cards}");

$functionsClass = new Functions(); 
 
$error= ''; 
$error_flag = true;

if(isset($_POST['submit'])){
	
	
	unset($_POST['submit']);
			
	if($error_flag==true){
			// save
			$values = 	array( 
						'email'=>$_POST['email'],
						'first_name_father'=>$_POST['first_name_father'],
						'last_name_father'=>$_POST['last_name_father'],
						'phone_father'=>$_POST['phone_father'],
						'first_name_mother'=>$_POST['first_name_mother'],
						'last_name_mother'=>$_POST['last_name_mother'],
						'phone_mother'=>$_POST['phone_mother'],
						'address'=>$_POST['address'],
						'number'=>$_POST['number'],
						'postalcode'=>$_POST['postalcode'],
						'city'=>$_POST['city'],
						'phone_unreachable'=>$_POST['phone_unreachable'],
						'relation_child'=>$_POST['relation_child'],
						'name_doc'=>$_POST['name_doc'],
						'phone_doc'=>$_POST['phone_doc'],
						'address_doc'=>$_POST['address_doc'],
						'number_doc'=>$_POST['number_doc'],
						'city_doc'=>$_POST['city_doc'],
						'name_dentist'=>$_POST['name_dentist'],
						'phone_dentist'=>$_POST['phone_dentist'],
						'address_dentist'=>$_POST['address_dentist'],
						'number_dentist'=>$_POST['number_dentist'],
						'city_dentist'=>$_POST['city_dentist'],  
				);
				
		$wpdb->update( 
		$table_users, 
			$values, 
				array( 'id' => $sessionUser->id )
			);
			
			
		$userObject = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id = ".$sessionUser->id, OBJECT );	
		
		$post_data=array();
		foreach($values as $key=>$value){
			$post_data[] = ($value != $user->$key) ? '<span style="color:green;">'.$value.'</span>' : $user->$key;
		}	
		$message ='Er zijn een aantal wijzigingen in een account. De wijzigingen worden in het groen weergegeven.<br /><br />';
		$message .='<h1>Nieuwe gegevens</h1>';	
		$message .='<h2>Gegevens ouders</h2>';
		$message .='E-mail:' .$post_data[0] . '<br />';
		
		$message .='1ste Ouder / verzorger: '.$post_data[4] . ' ' . $post_data[5] .'<br />';
		$message .='1ste Ouder / verzorger telefoon: '.$post_data[6].'<br />';
		
		$message .='2e Ouder / verzorger: '. $post_data[1]  . ' ' . $post_data[2] .'<br />';
		$message .='2e Ouder / verzorger telefoon: '.$post_data[3].'<br />';	
		
		
		$message .='Adres: '.$post_data[7].'<br />';
		$message .='Huisnummer: '.$post_data[8].'<br />';
		$message .='Postcode en woonplaats: '.$post_data[9] . ' ' . $post_data[10] .'<br />';
		$message .='Telefoon bij onbereikbaar: '.$post_data[11].'<br />';
		$message .='Relatie tot kind(eren): '.$post_data[12].'<br /><br />';
		
		$message .='<h3>Dokter</h3>';
		$message .='Naam: '.$post_data[13].'<br />';
		$message .='Telefoon: '.$post_data[14].'<br />';
		$message .='Adres: '.$post_data[15].'<br />';
		$message .='Huisnummer: '.$post_data[16].'<br />';
		$message .='Woonplaats: '.$post_data[17].'<br />';
		
		$message .='<h3>Tandarts</h3>';
		$message .='Naam: '.$post_data[18].'<br />';
		$message .='Telefoon: '.$post_data[19].'<br />';
		$message .='Adres: '.$post_data[20].'<br />';
		$message .='Huisnummer: '.$post_data[21].'<br />';
		$message .='Woonplaats: '.$post_data[22].'<br /><br />';
		$message .='<hr />';	
		$message .='<h1>Oude gegevens</h1>';	
		$message .='<h2>Gegevens ouders</h2>';
		$message .='1ste Ouder / verzorger: '.$user->first_name_mother . ' ' . $user->last_name_mother .'<br />';
		$message .='1ste Ouder / verzorger telefoon: '.$user->phone_mother.'<br />';
		
		$message .='2e Ouder / verzorger: '. $user->first_name_father . ' ' . $user->last_name_father .'<br />';
		$message .='2e Ouder / verzorger telefoon: '.$user->phone_father.'<br />';	
		
		
		$message .='Adres: '.$user->address.'<br />';
		$message .='Huisnummer: '.$user->number.'<br />';
		$message .='Postcode en woonplaats: '.$user->postalcode . ' ' . $user->city .'<br />';
		$message .='Telefoon bij onbereikbaar: '.$user->phone_unreachable.'<br />';
		$message .='Relatie tot kind(eren): '.$user->relation_child.'<br /><br />';
		
		$message .='<h3>Dokter</h3>';
		$message .='Naam: '.$user->name_doc.'<br />';
		$message .='Telefoon: '.$user->phone_doc.'<br />';
		$message .='Adres: '.$user->address_doc.'<br />';
		$message .='Huisnummer: '.$user->number_doc.'<br />';
		$message .='Woonplaats: '.$user->city_doc.'<br />';
		
		$message .='<h3>Tandarts</h3>';
		$message .='Naam: '.$user->name_dentist.'<br />';
		$message .='Telefoon: '.$user->phone_dentist.'<br />';
		$message .='Adres: '.$user->address_dentist.'<br />';
		$message .='Huisnummer: '.$user->number_dentist.'<br />';
		$message .='Woonplaats: '.$user->city_dentist.'<br /><br />';
		
		// Send mails
		$functionsClass->SendMail('Account gewijzigd van aanmelding', $settings->tso_admin_mail, $message);
		echo'<script>window.location="'.$settings->url_profile_edit_done.'"; </script>';
	}


}

?>
		
	<style>
	table {
		width: 100%;
	}
	
	table tr td {
		padding: 10px;
		font-size: 12px;
	}
</style>
	        	<a href="<?php echo $settings->url_card_overview; ?>">Strippenkaart</a> <a href="<?php echo $settings->url_profile_edit; ?>">Gegevens wijzigen</a> - <a href="?action=logout">Uitloggen</a>
	        	<form method="POST">
	        		<?php if(isset($error)) {
	        			echo $error;
	        		}?>
	        		
	        		
<?php
if($user==null){
	echo "Gebruiker bestaat niet.";
}else{
?>	        		
<h2>Gegevens ouders</h2>
<table id="table-parents">
	<tr>
		<td>E-mail</td>
		<td><input type="email" required="required" name="email" value="<?php echo $user->email; ?>" style="width: 293px;" /></td>
	</tr>
		<tr>
		<td>1ste Ouder / verzorger</td>
		<td><input type="text" required="required" name="last_name_mother" placeholder="Achternaam" value="<?php echo $user->last_name_mother; ?>" /> <input type="text" required="required" name="first_name_mother" placeholder="Voornaam" value="<?php echo $user->first_name_mother; ?>" /> <input type="text" required="required" name="phone_mother" placeholder="0600000000" value="<?php echo $user->phone_mother; ?>" /></td>
	</tr>
	<tr>
		<td>2e Ouder / verzorger</td>
		<td><input type="text" name="last_name_father" placeholder="Achternaam" value="<?php echo $user->last_name_father; ?>" /> <input type="text" name="first_name_father" placeholder="Voornaam" value="<?php echo $user->first_name_father; ?>" /> <input type="text" name="phone_father" placeholder="0600000000" value="<?php echo $user->phone_father; ?>" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address" value="<?php echo $user->address; ?>" /> Huisnummer <input type="text" required="required" name="number" size="5" maxlength="5" value="<?php echo $user->number; ?>" /></td>
	</tr>
	<tr>
		<td>Postcode en woonplaats</td>
		<td><input type="text" required="required" name="postalcode" size="6" maxlength="6" value="<?php echo $user->postalcode; ?>" /> <input type="text" required="required" name="city"value="<?php echo $user->city; ?>" /></td>
	</tr>
	<tr>
		<td>Telefoon bij onbereikbaar</td>
		<td><input type="text" required="required" name="phone_unreachable" value="<?php echo $user->phone_unreachable; ?>" /> Relatie tot kind(eren) <input type="text" required="required" name="relation_child" value="<?php echo $user->relation_child; ?>" /></td>
	</tr>
</table>

<h2>Gegevens Dokter</h2>
<table id="table-doctor">
	<tr>
		<td>Naam</td>
		<td><input type="text" required="required" name="name_doc" value="<?php echo $user->name_doc; ?>" /> Telefoon <input type="text" required="required" name="phone_doc" value="<?php echo $user->phone_doc; ?>" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address_doc" value="<?php echo $user->address_doc; ?>" /> Huisnummer <input type="text" required="required" name="number_doc" size="5" maxlength="5" value="<?php echo $user->number_doc; ?>" /></td>
	</tr>
	<tr>
		<td>Woonplaats</td>
		<td><input type="text" required="required" name="city_doc" value="<?php echo $user->city_doc; ?>" /></td>
	</tr>
</table>


<h2>Gegevens Tandarts</h2>
<table id="table-dentist">
	<tr>
		<td>Naam</td>
		<td><input type="text" required="required" name="name_dentist" value="<?php echo $user->name_dentist; ?>" /> Telefoon <input type="text" required="required" name="phone_dentist" value="<?php echo $user->phone_dentist; ?>" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address_dentist" value="<?php echo $user->address_dentist; ?>" />  Huisnummer <input type="text" required="required" name="number_dentist" size="5" maxlength="5" value="<?php echo $user->number_dentist; ?>" /></td>
	</tr>
	<tr>
		<td>Woonplaats</td>
		<td><input type="text" required="required" name="city_dentist" value="<?php echo $user->city_dentist; ?>" /></td>
	</tr>
</table>
	        		<button type="submit" name="submit" class="">Profiel aanpassen</button>
	        	</form>
	        	
	<?php } ?> 