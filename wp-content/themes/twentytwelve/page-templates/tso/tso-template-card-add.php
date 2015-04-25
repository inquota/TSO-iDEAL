<?php
/*
Template Name: TSO - Card - Add (Strippentkaart afnemen)
*/
global $wpdb;
$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_schools = $wpdb->prefix . 'tso_schools';

// check if user is logged in
if($_SESSION['user']==null){
	echo '<meta http-equiv="refresh" content="0; URL=/inloggen/">';
}

$sessionUser = $_SESSION['user'];

/**
 * Get Children from user_id
 */ 
$urlparts = explode('/',$_SERVER['REQUEST_URI']);

if(empty($urlparts[3])){
	echo '<meta http-equiv="refresh" content="0; URL=/strippenkaart/">';
} 
 
$result = $wpdb->get_row( "SELECT * FROM {$table_children} WHERE id = ".$urlparts[3], OBJECT );

// groepen
$groups = array('1','1a','1b','2','2a','2b','3','3a','3b','4','4a','4b','5','5a','5b','6','6a','6b','7','7a','7b','8','8a','8b');

// card / strippenkaart
$cards = $wpdb->get_results("SELECT * FROM {$table_cards}"); 

// get schools
$schools = $wpdb->get_results("SELECT * FROM {$table_schools} ORDER BY name ASC"); 
 
$error= ''; 
$error_flag = true;

if(isset($_SESSION['data'])){
	unset($_SESSION['data']);
}

if(isset($_POST['submit'])){
	
	//extract($_POST);
	$group = $_POST['group'];
	$card = $_POST['card'];
	$bank = $_POST['bank'];
	$school = $_POST['school'];
	
	$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE id = ".$card, OBJECT );
	
	if(empty($school)){
		$error .= '<strong>U heeft geen school gekozen</strong><br />';
		$error_flag = false;
	}
	
	if(empty($group)){
		$error .= '<strong>U heeft geen groep gekozen</strong><br />';
		$error_flag = false;
	}
	
	if(empty($card)){
		$error .= '<strong>U heeft geen strippenkaart gekozen</strong><br />';
		$error_flag = false;
	}

	if(empty($bank)){
		$error .= '<strong>U heeft geen bank gekozen</strong><br />';
		$error_flag = false;
	}
	
	if($error_flag==true){
		/**
		 * TargetPay settings
		 */
		$targetpay= array(
		
			// RTLO / Layout code
			'rtlo'				=> 	75941, 
			
			// Set return url from your website. Make sure this route exists in your routes.php
			'return_url'		=>	'/tso-ideal-check.php',
			
			// Set report url to recieve the status of transactions not retreived by the returnurl
			'report_url'		=>	'/tso-ideal-report.php',
			
		);
		
		// Init TargetPay class
		$oIdeal = new TargetPayIdeal( $targetpay['rtlo'] );
			
		# Set ideal amount in cents so 500 cent will be 5 euro
		$oIdeal->setIdealAmount($cardObject->price);
		
		# Set ideal issuer
		$oIdeal->setIdealissuer($bank);
		
		# Set ideal description
		$oIdeal->setIdealDescription($cardObject->description);
		
		# Set return url, wich should return on succes
		$oIdeal->setIdealReturnUrl('http://' . $_SERVER['HTTP_HOST'] . $targetpay['return_url']);
		
		# Set report url 
		$oIdeal->setIdealReportUrl('http://' . $_SERVER['HTTP_HOST'] . $targetpay['report_url']);
		
		# Now we can initiate the payment
		$aReturn = $oIdeal->startPayment();
		
		# This is the transaction id
		$intTrxId = $aReturn[0];
		
		# this will be the bank url that will rederect to the bank.
		$strBankURL = $aReturn[1];
		
		$data=array('groep'=>$group, 'bank'=>$bank, 'card'=>$card, 'school'=> $school, 'child_id'=> $urlparts[3], 'user_id'=>$sessionUser->id);
		
		$_SESSION['data'] = $data;
		
		/**
		* This haader function will redirect the browser to the bank
		*/
		echo '<meta http-equiv="refresh" content="0; URL='.$strBankURL.'">';
	}


}

if ( have_posts() ) : while ( have_posts() ) : the_post();
?>
	<div class="about-us-wrapper" style="background: #FFF;" data-stellar-background-ratio="0.5">
	    <div class="container">
	        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	        	
	        	<?php echo the_content(); ?>
	        	
	        	<form method="POST">
	        		<?php if(isset($error)) {
	        			echo $error;
	        		}?>
	        		<table>
	        		
	        			<tr>
	        				<td>Naam</td>
	        				<td><?php echo $result->name; ?></td>
	        			</tr>
	        			
						<tr>
	        				<td>School</td>
	        				<td>
							<?php if(!isset($schools) || $schools == null ) : ?>
								<span style="color: #FF0000;">Er zijn geen scholen beschikbaar. Neem contact op met de beheerder.</span>
							<?php else : ?>
	        				<select name="school">
	        					<option selected="selected" value="">--- Maak een keuze ----</option>
	        					<?php foreach($schools as $school) : ?>
	        						<option value="<?php echo $school->id; ?>"><?php echo $school->name; ?></option>
	        					<?php endforeach; ?>
	        				</select>
	        				<?php endif; ?>
	        				</td>
	        			</tr>
	        			
	        			<tr>
	        				<td>Groep</td>
	        				<td>
							<?php if(!isset($groups) || $groups == null ) : ?>
								<span style="color: #FF0000;">Er zijn geen groepen beschikbaar. Neem contact op met de beheerder.</span>
							<?php else : ?>
	        				<select name="group">
	        					<option selected="selected" value="">--- Maak een keuze ----</option>
	        					<?php foreach($groups as $group) : ?>
	        						<option value="<?php echo $group; ?>"><?php echo $group; ?></option>
	        					<?php endforeach; ?>
	        				</select>
	        				<?php endif; ?>
	        				</td>
	        			</tr>
	        			
						<tr>
	        				<td>Strippenkaart</td>
	        				<td>
							<?php if(!isset($cards) || $cards == null ) : ?>
								<span style="color: #FF0000;">Er is geen strippenkaart beschikbaar. Neem contact op met de beheerder.</span>
							<?php else : ?>
	        				<select name="card">
	        					<option selected="selected" value="">--- Maak een keuze ----</option>
	        					<?php foreach($cards as $key=>$card) : ?>
	        						<option value="<?php echo $card->id; ?>"><?php echo $card->description; ?></option>
	        					<?php endforeach; ?>
	        				</select>
	        				<?php endif; ?>
	        				</td>
	        			</tr>
	        			
	        			<tr>
	        				<td>Kies een bank</td>
	        				<td><select name="bank" id="bank"><script src="http://www.targetpay.com/ideal/issuers-nl.js"></script></select></td>
	        			</tr>
	        		
	        		</table>
	        		<button type="submit" name="submit" class="">Strippenkaart afnemen</button>
	        	</form>
	        	
	        
	        </div>
	    </div>
	</div>
	
	<div style="clear:both;"></div>

<?php endwhile; endif; ?>


<?php get_footer('customer-area'); ?>