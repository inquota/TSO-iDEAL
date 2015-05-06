<?php
/*
Template Name: TSO - Login
*/

global $wpdb;

$table_users = $wpdb->prefix . 'tso_users';

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
	
		if($passwordClass->validate_password($password, $user->password)){
			$_SESSION['user'] = $user;
			echo'<script>window.location="'.$settings->url_card_overview.'"; </script>';
		}else{
			echo 'invalid';
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
				
		