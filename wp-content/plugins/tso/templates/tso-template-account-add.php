<?php
global $wpdb;

$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_settings = $wpdb->prefix . 'tso_settings';
$table_users = $wpdb->prefix . 'tso_users';
$table_schools = $wpdb->prefix . 'tso_schools';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

/**
 * Get Children from user_id
 */ 
$urlparts = explode('/',$_SERVER['REQUEST_URI']);

$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id = 1", OBJECT );
 
// groepen
$groups = array('1','1a','1b','2','2a','2b','3','3a','3b','4','4a','4b','5','5a','5b','6','6a','6b','7','7a','7b','8','8a','8b');

// card / strippenkaart
$cards = $wpdb->get_results("SELECT * FROM {$table_cards}");

$schools = $wpdb->get_results("SELECT * FROM {$table_schools} ORDER by name ASC"); 
 
$error= ''; 
$error_flag = true;

if(isset($_POST['submit'])){
	
	unset($_POST['submit']);
	//echo '<pre>';
	//print_r($_POST['data']);
	
	// Get Child data
	$child_names = $_POST['data']['Child']['chid_name'];
	$child_groups = $_POST['data']['Child']['group'];
		
			
	if($error_flag==true){
		
		$passwordClass = new Password();
		$functionsClass = new Functions();
	
		$password_readable = $functionsClass->randomPassword();
		$password_hash = $passwordClass->create_hash($password_readable);
		$hash = $functionsClass->RandomHash();
		
		// save user
		$wpdb->insert( 
		$table_users, 
				array( 
						'email'=>$_POST['email'],
						"password" => $password_hash,
						'name_father'=>$_POST['name_father'],
						'phone_father'=>$_POST['phone_father'],
						'name_mother'=>$_POST['name_mother'],
						'phone_mother'=>$_POST['phone_mother'],
						'address'=>$_POST['address'],
						'postalcode'=>$_POST['postalcode'],
						'city'=>$_POST['city'],
						'iban'=>$_POST['iban'],
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
						'school_id'=>$_POST['school'], 
						"ip" => $_SERVER['REMOTE_ADDR'],
	   					"created_at" => date('Y-m-d H:i:s'),
	   					"hash" => $hash,
				) 
		);
		
		$user_id=$wpdb->insert_id;
		
		foreach($child_names as $k=>$name){
		
			// save Child
			$wpdb->insert( 
			$table_children, 
					array( 
							'user_id'=>$user_id,
							'name'=>$name,
							'groep'=>$child_groups[$k],
		   					"created_at" => date('Y-m-d H:i:s'),
					) 
			);
		}
		
		/**
		 * Compose Mail
		 */
		$message ='Beste,<br /><br />';
		$message .='Inlog: '.$_POST['email'] . '<br />';
		$message .='Wachtwoord: '.$password_readable . '<br />';
		$message .='Klik hier om uw account te bevestigen: <a href="http://'.$_SERVER['HTTP_HOST'].'/tso-verify.php?hash='.$hash.'">activeren</a><br />';
		
		$functionsClass->SendMail('TSO | Account', $_POST['email'], $message);
			
		echo '<meta http-equiv="refresh" content="0; URL='.$settings->url_profile_created.'">';
	}


}

?>
<style>
	table {
		width: 100%;
	}
</style>

<form method="POST">
<h2>Gegevens ouders</h2>
<table>
	<tr>
		<td>E-mail</td>
		<td><input type="email" required="required" name="email" /></td>
	</tr>
	<tr>
		<td>Naam vader</td>
		<td><input type="text" required="required" name="name_father" /></td>
	</tr>
	<tr>
		<td>Telefoon vader</td>
		<td><input type="text" required="required" name="phone_father" /></td>
	</tr>
	<tr>
		<td>Naam moeder</td>
		<td><input type="text" required="required" name="name_mother" /></td>
	</tr>
	<tr>
		<td>Telefoon moeder</td>
		<td><input type="text" required="required" name="phone_mother" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address" /></td>
	</tr>
	<tr>
		<td>Postcode en woonplaats</td>
		<td><input type="text" required="required" name="postalcode" size="6" maxlength="6" /> <input type="text" required="required" name="city" /></td>
	</tr>
	<tr>
		<td>IBAN</td>
		<td><input type="text" required="required" name="iban" /></td>
	</tr>
	<tr>
		<td>Telefoon bij onbereikbaar</td>
		<td><input type="text" required="required" name="phone_unreachable" /></td>
	</tr>
	<tr>
		<td>Relatie tot kind(eren)</td>
		<td><input type="text" required="required" name="relation_child" /></td>
	</tr>
</table>

<h2>Gegevens Dokter</h2>
<table>
	<tr>
		<td>Naam</td>
		<td><input type="text" required="required" name="name_doc" /></td>
	</tr>
	<tr>
		<td>Telefoon</td>
		<td><input type="text" required="required" name="phone_doc" /></td>
	</tr>
	<tr>
		<td>Adres en woonplaats</td>
		<td><input type="text" required="required" name="address_doc" /> <input type="text" required="required" name="city_doc" /></td>
	</tr>
</table>


<h2>Gegevens Tandarts</h2>
<table>
	<tr>
		<td>Naam</td>
		<td><input type="text" required="required" name="name_dentist" /></td>
	</tr>
	<tr>
		<td>Telefoon</td>
		<td><input type="text" required="required" name="phone_dentist" /></td>
	</tr>
	<tr>
		<td>Adres en woonplaats</td>
		<td><input type="text" required="required" name="address_dentist" /> <input type="text" required="required" name="city_dentist" /></td>
	</tr>
</table>

<h2>Gegevens kinderen</h2>
<table>
	<?php if(!isset($schools) || $schools == null ) : ?>
		<span style="color: #FF0000;">Er zijn geen scholen beschikbaar. Neem contact op met de beheerder.</span>
	<?php else : ?>
	<tr>
		<td style="width: 120px;">Basisschool</td>
		<td>
			<select name="school">
			<option selected="selected" value="">--- Maak een keuze ----</option>
			<?php foreach($schools as $school) : ?>
				<option value="<?php echo $school->id; ?>"><?php echo $school->name; ?></option>
			<?php endforeach; ?>
		</select></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td>Dagen opvang</td>
		<td>
			<input type="checkbox"  name="days_care[]" value="Maandag" /> Maandag
			<input type="checkbox"  name="days_care[]" value="Dinsdag" /> Dinsdag
			<input type="checkbox"  name="days_care[]" value="Woensdag" /> Woensdag
			<input type="checkbox"  name="days_care[]" value="Donderdag" /> Donderdag
			<input type="checkbox"  name="days_care[]" value="Vrijdag" /> Vrijdag
		</td>
	</tr>
	
	<?php if(!isset($groups) || $groups == null ) : ?>
		<span style="color: #FF0000;">Er zijn geen groepen beschikbaar. Neem contact op met de beheerder.</span>
	<?php else : ?>
	<tr>
		<td>Kinderen</td>
		<td>
			<ul style="list-style-type: none;" id="children">
				<li>
					Naam <input type="text" required="required" name="data[Child][chid_name][]" />
					Groep 

	        				<select name="data[Child][group][]">
	        					<option selected="selected" value="">--- Maak een keuze ----</option>
	        					<?php foreach($groups as $group) : ?>
	        						<option value="<?php echo $group; ?>"><?php echo $group; ?></option>
	        					<?php endforeach; ?>
	        				</select>
	        		<?php /*		
	        		Geslacht 
	        			<input type="radio" name="data[Child][child_gender][]" value="jongen" /> Jongen 
	        			<input type="radio" name="data[Child][child_gender][]" value="meisje" /> Meisje*/ ?>
				</li>
			</ul>
			<br />
			 <a href="#" id="child_add">Kind toevoegen</a>
			</td>
	</tr>
	<?php endif; ?>
</table>

<button type="submit" name="submit" class="">Aanmelden</button>
</form>