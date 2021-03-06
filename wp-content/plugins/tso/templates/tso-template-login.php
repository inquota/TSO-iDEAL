<?php
/*
Template Name: TSO - Login
*/

global $wpdb;

$table_users = $wpdb->prefix . 'tso_users';
$table_settings = $wpdb->prefix . 'tso_settings';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

// check if user is already logged in
if(isset($_SESSION['user'])){
	echo'<script>window.location="'.$settings->url_card_overview.'"; </script>';
}

if(isset($_POST['login'])){
	
	$passwordClass = new Password();
	
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	$user = $wpdb->get_row("SELECT * FROM {$table_users} WHERE email = '".$email."'");
	
	if($user){
		
		if($user->verified == null) {
			echo '<script>alert("U heeft uw account nog niet bevestigd"); </script>';
			echo'<script>window.location="/"; </script>';
			exit;
		}
		
		if($passwordClass->validate_password($password, $user->password)){
			$_SESSION['user'] = $user;
			echo'<script>window.location="'.$settings->url_card_overview.'"; </script>';
		}else{
			echo 'E-mail / wachtwoord combinatie komen niet overeen';
		}	
	}
}
?>

<form method="POST">
	<table>
		<tr>
			<td>E-mail</td>
			<td><?php if(isset($_GET['email'])) :
			?>
			<input type="email" name="email" value="<?= $_GET['email'] ?>" />
			<?php else :
			?>
			<input type="email" name="email" />
			<?php endif;
			?></td>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td>
			<input type="password" name="password" />
			</td>
		</tr>
	</table>

	<button type="submit" name="login" class="">
		Inloggen
	</button>

</form>
				
		