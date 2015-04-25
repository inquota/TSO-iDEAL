<?php
/*
Template Name: TSO - Login
*/

get_header(); 

//require 'custom/password.php';

if(isset($_POST['login'])){
	
	//extract($_POST);
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	$user = $wpdb->get_row("SELECT * FROM clients WHERE email = '".$email."'");
	
	if($user){
	
		if(validate_password($password, $user->password)){
			$_SESSION['user'] = $user;
			//echo '<meta http-equiv="refresh" content="0; URL=http://quenchdrinks.nl/?page_id=2321">';
		}else{
			echo 'invalid';
		}	
	}
	
	
}

if ( have_posts() ) : while ( have_posts() ) : the_post();
?>
	<div class="about-us-wrapper" style="background: #FFF;" data-stellar-background-ratio="0.5">
	    <div class="container">
	        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	        	
	        	<?php echo the_content(); ?>
	        	
	        	<form method="POST">
				<table>
					<tr>
						<td>E-mail</td>
						<td><input type="email" name="email" /></td>
					</tr>
					<tr>
						<td>Wachtwoord</td>
						<td><input type="password" name="password" /></td>
					</tr>
				</table>
				
					<button type="submit" name="login" class="">Inloggen</button>
				
			
				</form>
				
				
	        </div>
	    </div>
	</div>
	
	<div style="clear:both;"></div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>