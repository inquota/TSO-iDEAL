<?php
/*
Template Name: TSO - Card - Add (Strippentkaart afnemen)
*/
global $wpdb;

$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_settings = $wpdb->prefix . 'tso_settings';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

// check if user is logged in
if(empty($_SESSION['user'])){
	echo'<script>window.location="'.$settings->url_login.'"; </script>';
}

$sessionUser = $_SESSION['user'];

/**
 * Get Children from user_id
 */ 
$urlparts = explode('/',$_SERVER['REQUEST_URI']);

if(empty($urlparts[3])){
	echo '<meta http-equiv="refresh" content="0; URL='.$settings->url_card_overview.'">';
} 
 
$result = $wpdb->get_row( "SELECT * FROM {$table_children} WHERE id = ".$urlparts[3], OBJECT );

// card / strippenkaart
$cards = $wpdb->get_results("SELECT * FROM {$table_cards}"); 
 
$error= ''; 
$error_flag = true;

if(isset($_SESSION['data'])){
	unset($_SESSION['data']);
}

if(isset($_POST['submit'])){
	
	//extract($_POST);
	$card = $_POST['card'];
	$bank = $_POST['bank'];
	
	$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE id = ".$card, OBJECT );
	
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
			'rtlo'				=> 	$settings->targetpay_rtlo, 
			
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
		$oIdeal->setIdealDescription($cardObject->description_short);
		
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
		
		$data=array('bank'=>$bank, 'card'=>$card, 'child_id'=> $urlparts[3], 'user_id'=>$sessionUser->id);
		
		$_SESSION['data'] = $data;
		
		/**
		* This header function will redirect the browser to the bank
		*/
		echo'<script>window.location="'.$strBankURL.'"; </script>';
	}


}
?>
	        	
	        	<form method="POST">
	        		<?php if(isset($error)) {
	        			echo $error;
	        		}?>
	        		<table>
	        		
	        			<tr>
	        				<td>Naam</td>
	        				<td><?php echo $result->first_name; ?> <?php echo $result->last_name; ?></td>
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