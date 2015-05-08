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
 
// groepen
$groups = array('1','1a','1b','2','2a','2b','3','3a','3b','4','4a','4b','5','5a','5b','6','6a','6b','7','7a','7b','8','8a','8b');

$schools = $wpdb->get_results("SELECT * FROM {$table_schools} ORDER by name ASC"); 
 
$error= ''; 
$error_flag = true;

if(isset($_POST['submit'])){
	
	unset($_POST['submit']);
	
	// Get Child data
	$child_last_names = $_POST['data']['Child']['child_last_name'];
	$child_first_names = $_POST['data']['Child']['child_first_name'];
	$child_groups = $_POST['data']['Child']['group'];
	
	if(empty($_POST['email'])){
		$error .= 'U heeft geen e-mail opgegeven.<br />';
		$error_flag = false;
	}elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$error .= 'Geen geldig e-mail.';
		$error_flag = false;
	}
	
	if(empty($_POST['address'])){
		$error .= 'U heeft geen adres opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['number'])){
		$error .= 'U heeft geen huisnummer opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['postalcode'])){
		$error .= 'U heeft geen postcode opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['city'])){
		$error .= 'U heeft geen postcode opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['phone_unreachable'])){
		$error .= 'U heeft geen telefoon opgegeven bij onbereikbaar.<br />';
		$error_flag = false;
	}
		
	if(empty($_POST['relation_child'])){
		$error .= 'U heeft geen relatie tot kind opgegeven.<br />';
		$error_flag = false;
	}
	/* Doc */
	if(empty($_POST['name_doc'])){
		$error .= 'U heeft geen dokter opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['phone_doc'])){
		$error .= 'U heeft geen dokter\'s telefoon opgegeven.<br />';
		$error_flag = false;
	}
		
	if(empty($_POST['address_doc'])){
		$error .= 'U heeft geen dokter\'s adres opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['number_doc'])){
		$error .= 'U heeft geen dokter\'s huisnummer opgegeven.<br />';
		$error_flag = false;
	}
			
	if(empty($_POST['city_doc'])){
		$error .= 'U heeft geen dokter\'s plaats opgegeven.<br />';
		$error_flag = false;
	}
	
	/* dentist */
	if(empty($_POST['name_dentist'])){
		$error .= 'U heeft geen tandarts opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['phone_dentist'])){
		$error .= 'U heeft geen tandarts telefoon opgegeven.<br />';
		$error_flag = false;
	}
		
	if(empty($_POST['address_dentist'])){
		$error .= 'U heeft geen tandarts adres opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['number_dentist'])){
		$error .= 'U heeft geen tandarts huisnummer opgegeven.<br />';
		$error_flag = false;
	}
			
	if(empty($_POST['city_dentist'])){
		$error .= 'U heeft geen tandarts plaats opgegeven.<br />';
		$error_flag = false;
	}
	
	if(empty($_POST['t_and_c'])){
		$error .= 'U heeft de algemene voorwaarden niet geaccepteerd.<br />';
		$error_flag = false;
	}
		
			
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
						'days_care'=>implode(',', $_POST['days_care']),
						'school_id'=>$_POST['school'], 
						"ip" => $_SERVER['REMOTE_ADDR'],
	   					"created_at" => date('Y-m-d H:i:s'),
	   					"hash" => $hash,
				) 
		);
		
		$user_id=$wpdb->insert_id;
		
		foreach($child_last_names as $k=>$name){
		
			// save Child
			$wpdb->insert( 
			$table_children, 
					array( 
							'user_id'=>$user_id,
							'first_name'=> $child_first_names[$k],
							'last_name'=>$name,
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
			
		echo'<script>window.location="'.$settings->url_profile_created.'"; </script>';
	}


}

?>
<style>
	table {
		width: 100%;
	}
	
	table tr td {
		padding: 10px;
	}
</style>

<form method="POST">
		<?php if(isset($error)) {
			echo $error;
		}?>
<h2>Gegevens ouders</h2>
<table id="table-parents">
	<tr>
		<td>E-mail</td>
		<td><input type="email" required="required" name="email" /></td>
	</tr>
	<tr>
		<td>Vader</td>
		<td><input type="text" required="required" name="last_name_father" placeholder="Achternaam" /> <input type="text" required="required" name="first_name_father" placeholder="Voornaam" /> Telefoon <input type="text" required="required" name="phone_father" placeholder="0600000000" /></td>
	</tr>
	<tr>
		<td>Moeder</td>
		<td><input type="text" required="required" name="last_name_mother" placeholder="Achternaam" /> <input type="text" required="required" name="first_name_mother" placeholder="Voornaam" /> Telefoon <input type="text" required="required" name="phone_mother" placeholder="0600000000" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address" /> Huisnummer <input type="text" required="required" name="number" size="5" maxlength="5" /></td>
	</tr>
	<tr>
		<td>Postcode en woonplaats</td>
		<td><input type="text" required="required" name="postalcode" size="6" maxlength="6" /> <input type="text" required="required" name="city" /></td>
	</tr>
	<tr>
		<td>Telefoon bij onbereikbaar</td>
		<td><input type="text" required="required" name="phone_unreachable" /> Relatie tot kind(eren) <input type="text" required="required" name="relation_child" /></td>
	</tr>
</table>

<h2>Gegevens Dokter</h2>
<table id="table-doctor">
	<tr>
		<td>Naam</td>
		<td><input type="text" required="required" name="name_doc" /> Telefoon <input type="text" required="required" name="phone_doc" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address_doc" /> Huisnummer <input type="text" required="required" name="number_doc" size="5" maxlength="5" /></td>
	</tr>
	<tr>
		<td>Woonplaats</td>
		<td><input type="text" required="required" name="city_doc" /></td>
	</tr>
</table>


<h2>Gegevens Tandarts</h2>
<table id="table-dentist">
	<tr>
		<td>Naam</td>
		<td><input type="text" required="required" name="name_dentist" /> Telefoon <input type="text" required="required" name="phone_dentist" /></td>
	</tr>
	<tr>
		<td>Adres</td>
		<td><input type="text" required="required" name="address_dentist" />  Huisnummer <input type="text" required="required" name="number_dentist" size="5" maxlength="5" /></td>
	</tr>
	<tr>
		<td>Woonplaats</td>
		<td><input type="text" required="required" name="city_dentist" /></td>
	</tr>
</table>

<h2>Gegevens kinderen</h2>
<table id="table-children">
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
					Kind <input type="text" required="required" name="data[Child][child_last_name][]" placeholder="Achternaam" />
					<input type="text" required="required" name="data[Child][child_first_name][]" placeholder="Voornaam" />
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
<hr />
<p>
	<input type="checkbox" value="agree" name="t_and_c" required="required"  /> Ik ga akkoord met de <a href="https://delunchclub-opo.nl/?p=63" title="algemene voorwaarden">algemene voorwaarden</a>	
</p>

<p>
	<button type="submit" name="submit" class="">Aanmelden</button>
</p>
</form>