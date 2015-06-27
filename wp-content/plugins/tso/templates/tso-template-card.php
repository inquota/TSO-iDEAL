<?php
/*
Template Name: TSO - Card (Strippentkaart)
*/

global $wpdb;

$table_children = $wpdb->prefix . 'tso_children';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_settings = $wpdb->prefix . 'tso_settings';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

// check if user is logged in
if($_SESSION['user']==null){
	echo'<script>window.location="'.$settings->url_login.'"; </script>';
}

$sessionUser = $_SESSION['user'];
$user = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id = ".$sessionUser->id, OBJECT );

/**
 * Get Children from user_id
 */ 
$results = $wpdb->get_results( "SELECT 
									Child.first_name AS first_name,
									Child.last_name AS last_name, 
									Child.groep, 
									Child.card, 
									Child.id,
									School.name AS name_school,
									Card.description 
							FROM 
								{$table_children} AS Child 
							LEFT JOIN 
								{$table_users} AS User ON (Child.user_id=User.id)
							LEFT JOIN 
								{$table_schools} AS School ON (User.school_id=School.id)
							LEFT JOIN 
								{$table_cards} AS Card ON (Child.card=Card.id)	
							WHERE 
								user_id = ".$sessionUser->id . "", OBJECT );

if(isset($_GET['action']) && $_GET['action']=='logout'){
	unset($_SESSION['user']);
	session_destroy();
	echo'<script>window.location="'.$settings->url_login.'"; </script>';
	exit;
}		

// card / strippenkaart
$cards = $wpdb->get_results("SELECT * FROM {$table_cards}"); 
 
$error= ''; 
$error_flag = true;

if(isset($_SESSION['data'])){
	unset($_SESSION['data']);
}

if(isset($_POST['submit'])){
	
	$card = $_POST['card'];
	$bank = $_POST['bank'];
	$children = @$_POST['child'];
	
	$card = array_filter($card);
		
	if(count($card) > 1){
		$description_short=array();
		$price_array=array();		
		foreach($card as $single_card){
			$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE price = '".$single_card."'", OBJECT );
			$description_short_array[]= $cardObject->description_short;	
			$price_array[]= $cardObject->price;	
		}
		$description_short = implode(', ', $description_short_array);
		$price= (array_sum($price_array));	
	}elseif(count($card) == 1){
		
		$single_card= '';
		foreach($card as $key_single_card=>$single_card){
			if(isset($key_single_card)){
				$single_card= $single_card;
			}
		}
		
		$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE price = '".$single_card."'", OBJECT );
		$description_short = $cardObject->description_short;
		$price= $cardObject->price;
	}else{
		$error .= '<strong>U dient tenminste 1 strippenkaart te kiezen</strong><br />';
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

	if($children==null){
		$error .= '<strong>U dient tenminste 1 kind aan te vinken</strong><br />';
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
		$oIdeal->setIdealAmount($price);
		
		# Set ideal issuer
		$oIdeal->setIdealissuer($bank);
		
		# Set ideal description
		$oIdeal->setIdealDescription($description_short);
		
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
		
		$data=array('bank'=>$bank, 'card'=>$card,'user_id'=>$sessionUser->id, 'children'=> $children);
		
		$_SESSION['data'] = $data;
		
		/**
		* This header function will redirect the browser to the bank
		*/
		echo'<script>window.location="'.$strBankURL.'"; </script>';
	}
}						
?>
<a href="<?php echo $settings->url_card_overview; ?>">Strippenkaart</a> - 
<a href="<?php echo $settings->url_profile_edit; ?>">Gegevens wijzigen</a> -
<a href="<?php echo $settings->url_password_change; ?>">Wachtwoord opnieuw instellen</a> - 
<a href="?action=logout">Uitloggen</a>
<hr />
<?php
if($user==null){
	echo "Gebruiker bestaat niet";
}else{
?>
	        	
<?php if(!$results) : ?>
	U heeft geen kinderen toegevoegd	
<?php else : ?>
	<form method="POST">
	<table>
		<tr>
			<th style="padding: 5px;">School</th>
			<th style="padding: 5px;">Naam</th>
			<th style="padding: 5px;">Groep</th>
			<th style="padding: 5px;">Acties</th>
		</tr>
	<?php foreach($results as $item) : ?>
		<tr>
			<td style="padding: 5px;"><?php echo $item->name_school; ?></td>
			<td style="padding: 5px;"><?php echo $item->first_name; ?> <?php echo $item->last_name; ?></td>
			<td style="padding: 5px;"><?php echo $item->groep; ?></td>
			<td style="padding: 5px;">
				<?php if(!isset($cards) || $cards == null ) : ?>
					<span style="color: #FF0000;">Er is geen strippenkaart beschikbaar. Neem contact op met de beheerder.</span>
				<?php else : ?>
				<select name="card[]">
					<option selected="selected" value="">--- Maak een keuze ----</option>
					<?php foreach($cards as $key=>$card) : ?>
						<option value="<?php echo $card->price; ?>"><?php echo $card->description; ?></option>
					<?php endforeach; ?>
				</select>
				<?php endif; ?>
				<input type="checkbox" name="child[]" value="<?php echo $item->id; ?>" />
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<hr />
	<p>
		<?php if($_SERVER['REMOTE_ADDR'] != '92.111.180.150') { ?>
		Betaling onder constructie.
		<?php }else { ?>
		
	Kies een bank: <select name="bank" id="bank"><option selected value="">Kies uw bank...</option>
<option value="0031">ABN Amro</option>
<option value="0721">ING</option>
<option value="0021">Rabobank</option>
<option value="0751">SNS Bank</option>
<option value="0761">ASN Bank</option>
<option value="0801">Knab</option>
<option value="0771">RegioBank</option>
<option value="0511">Triodos Bank</option>
<option value="0161">Van Lanschot Bankiers</option></select>	
	</p>
	
	<p>
		<button type="submit" name="submit" class="">Afrekenen</button>
	</p><?php } ?>
	</form>
	
<?php endif; ?>	
<?php } ?>	        