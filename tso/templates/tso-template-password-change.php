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

$user = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id = ".$sessionUser->id, OBJECT );

$functionsClass = new Functions(); 
 
$error= ''; 
$error_flag = true;

if(isset($_POST['submit'])){
	
	
	unset($_POST['submit']);
	
	if(empty($_POST['password_new']))
	{
		$error .= 'U heeft geen nieuw wachtwoord opgegeven.<br />';
		$error_flag = false;	
	}
	
	if(empty($_POST['password_repeat']))
	{
		$error .= 'U heeft wachtwoord herhalen niet opgegeven.<br />';
		$error_flag = false;	
	}
	
	if(!empty($_POST['password_new']) && !empty($_POST['password_repeat']) && $_POST['password_new'] != $_POST['password_repeat'])
	{
		$error .= 'Beide wachtwoorden zijn niet gelijk aan elkaar.<br />';
		$error_flag = false;	
	}
	
			
	if($error_flag==true){
		
			
		$passwordClass = new Password();
		$functionsClass = new Functions();
	

		$password_hash = $passwordClass->create_hash($_POST['password_repeat']);
		
			// save
			$values = 	array( 
							"password" => $password_hash,
				);
				
		$wpdb->update( 
		$table_users, 
			$values, 
				array( 'id' => $sessionUser->id )
			);
			
		echo'<script>window.location="'.$settings->url_card_overview.'"; </script>';
	}


}

?>
	
	<style>
		input[type="text"]{
			width: 200px;
		}
	</style>
	        	<a href="<?php echo $settings->url_card_overview; ?>">Strippenkaart</a> - 
<a href="<?php echo $settings->url_profile_edit; ?>">Gegevens wijzigen</a> -
<a href="<?php echo $settings->url_password_change; ?>">Wachtwoord opnieuw instellen</a> - 
<a href="?action=logout">Uitloggen</a>
	        	<form method="POST">
	        		<?php if(isset($error)) {
	        			echo $error;
	        		}?>
	        		
	        		
<?php
if($user==null){
	echo "Gebruiker bestaat niet.";
}else{
?>	        		
<h2>Wachtwoord opnieuw instellen</h2>
<table id="table-parents">
	<tr>
		<td>Nieuw wachtwoord</td>
		<td><input type="password" required="required" name="password_new" /></td>
	</tr>
	<tr>
		<td>Nieuw wachtwoord herhalen</td>
		<td><input type="password" required="required" name="password_repeat" /></td>
	</tr>
	
</table>
	        		<button type="submit" name="submit" class="">Wachtwoord aanpassen</button>
	        	</form>
	        	
	<?php } ?> 