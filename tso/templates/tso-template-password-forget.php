<?php
global $wpdb;

$table_settings = $wpdb->prefix . 'tso_settings';
$table_users = $wpdb->prefix . 'tso_users';

// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

$functionsClass = new Functions(); 
 
$error= ''; 
$error_flag = true;

if(isset($_POST['submit'])){
	

	unset($_POST['submit']);
	
	if(empty($_POST['email'])){
		$error .= 'U heeft geen e-mail opgegeven.<br />';
		$error_flag = false;
	}elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$error .= 'Geen geldig e-mail.';
		$error_flag = false;
	}else{
		$user = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE email = '".$_POST['email']."'", OBJECT );
		if(!$user){
			$error .= 'Uw e-mail komt niet voor in ons systeem.';
			$error_flag = false;	
		}
		
	}
		
	if($error_flag==true){
		
		$passwordClass = new Password();
		$functionsClass = new Functions();
	
		$password_readable = $functionsClass->randomPassword();
		$password_hash = $passwordClass->create_hash($password_readable);
		
			// save
			$values = 	array( 
							"password" => $password_hash,
				);
				
		$wpdb->update( 
		$table_users, 
			$values, 
				array( 'id' => $user->id )
			);
		
				/**
		 * Compose Mail
		 */
		$message ='Beste,<br /><br />';
		$message .='U heeft een nieuw wachtwoord opgevraagd.<br />';
		$message .='Wachtwoord: '.$password_readable . '<br />';
		
		$blog_title = get_bloginfo(); 
		$functionsClass->SendMail($blog_title, 'Account nieuw wachtwoord', $_POST['email'], $message);
			
		echo'<script>window.location="'.$settings->url_login.'"?email="'.$_POST['email'].'"; </script>';
	}


}

?>
	
	<style>
		table tr td {
			padding: 20px;
		}
	</style>
	
	        	<form method="POST">
	        		<?php if(isset($error)) {
	        			echo $error;
	        		}?>
	        		
	        		
     		
<h2>Wachtwoord vergeten</h2>
<table id="table-parents">
	<tr>
		<td>E-mail</td>
		<td><input type="email" required="required" name="email" /></td>
	</tr>
	
</table>
	        		<button type="submit" name="submit" class="">Nieuw wachtwoord aanvragen</button>
	        	</form>
	        	
