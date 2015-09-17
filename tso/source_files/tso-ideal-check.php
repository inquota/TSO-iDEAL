<?php
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);
date_default_timezone_set("Europe/Amsterdam");
require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header, $wpdb;

if(!isset($_GET['trxid']) && !isset($_GET['ec'])){
	exit;
}

// tables
$table_submissions = $wpdb->prefix . 'tso_submissions';
$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_settings = $wpdb->prefix . 'tso_settings';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

$functionsClass = new Functions();

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

if(isset($_SESSION['data'])) {
	$session_data = $_SESSION['data'];	
}else{
	mail('jarahzakelijk@gmail.com', 'Debug info', print_r($_SESSION,1));
	header('Location: /');
	exit;
}

$trxid = $_GET['trxid'];
$ec = $_GET['ec'];		

// Init the class
$oIdeal = new TargetPayIdeal($targetpay['rtlo']);
$oIdeal->setDomain($_SERVER['HTTP_HOST']);

$banks = $oIdeal->getBanks();

// check if ec and trxid already exists in DB
$transactionCheck = $wpdb->get_row( "SELECT * FROM {$table_submissions} WHERE ec = '".$ec."' AND trxid ='".$trxid."'", OBJECT );

// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

// get user
$userObject = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id =".$session_data['user_id'], OBJECT );

// get child
$childObject = $wpdb->get_results( "SELECT * FROM {$table_children} WHERE id IN(".implode(',', $session_data['children']).")");

// get school
$schooldObject = $wpdb->get_row( "SELECT * FROM {$table_schools} WHERE id =".$userObject->school_id, OBJECT );

$card = array_filter($session_data['card']);

if ($oIdeal->validatePayment($trxid, 1,$settings->targetpay_testmode) == true) {
	
	if(count($childObject) > 1){
			
		// get card
		$cardObject = $wpdb->get_results( "SELECT * FROM {$table_cards} WHERE price IN(".implode(',', $card).")");
		$description = $cardObject;
				
		foreach($childObject as $k=>$child){
			
			if(count($cardObject) == 1){
				$card_desc = $cardObject[0]->description;
				$card_price = $cardObject[0]->price;
			}else{
				$card_desc = $cardObject[$k]->description;
				$card_price = $cardObject[$k]->price;
			}
			
			if($transactionCheck==null){
				
				$wpdb->insert($table_submissions, array(
				   "user_id" => $session_data['user_id'],
				   "child_id" => $child->id,
				   "school_id" => $userObject->school_id,
				   "groep" => $child->groep,
				   "card" => $card_desc,
				   "price" => $card_price,
				   "bank" => $session_data['bank'],
				   "ec" => $ec,
				   "trxid" => $trxid,
				   "ip" => $_SERVER['REMOTE_ADDR'],
				   "payment_status" => 1,
				   "created_at" => date('Y-m-d H:i:s'),
				));	
			}
		}
		
	}else{
			
		$single_card= '';
		foreach($card as $key_single_card=>$single_card){
			if(isset($key_single_card)){
				$single_card= $single_card;
			}
		}
		// get card
		$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE price = ".$single_card."", OBJECT);
		$description = $cardObject->description;
		
	if($transactionCheck==null){
		// save
		$wpdb->insert($table_submissions, array(
		   "user_id" => $session_data['user_id'],
		   "child_id" => $childObject[0]->id,
		   "school_id" => $userObject->school_id,
		   "groep" => $childObject[0]->groep,
		   "card" => $description,
		   "price" => $cardObject->price,
		   "bank" => $session_data['bank'],
		   "ec" => $ec,
		   "trxid" => $trxid,
		   "ip" => $_SERVER['REMOTE_ADDR'],
		   "payment_status" => 1,
		   "created_at" => date('Y-m-d H:i:s'),
			));
			
		
		}
	}
			
		/**
		 * Compose Mail for Admin 
		 */ 
		$message_admin ='<h2>Strippenkaart</h2>';
		$message_admin .='Er is een strippenkaart afgenomen:<br /><br />';
	
		if($userObject->first_name_mother !=null){
			$message_admin .='1ste Ouder / verzorger: '.$userObject->first_name_mother.' '.$userObject->last_name_mother.'<br />';
			$message_admin .='1ste Ouder / verzorger telefoon: '.$userObject->phone_mother.' <br />';
		}
				
		if($userObject->first_name_father !=null){
			$message_admin .='2de Ouder / verzorger: '.$userObject->first_name_father.' '.$userObject->last_name_father.'<br />';
			$message_admin .='2de Ouder / verzorger telefoon: '.$userObject->phone_father.' <br />';	
		}
		
		$message_admin .='School: '.$schooldObject->name.'<br />';
		
		foreach($childObject as $k=>$child){
			if(count($childObject) == 1)
			{
				$message_admin .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description.'<br />';	
			}else{
				if(count($description) == 1) {
					$message_admin .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[0]->description.'<br />';
				}else{
					$message_admin .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[$k]->description.'<br />';
				}
			}
		}
		
		$message_admin .='<h2>Betaalgegevens</h2>';
		$message_admin .='Betaald: Ja<br />';
		$message_admin .='Bank: '.$banks[$session_data['bank']].'<br />';
		$message_admin .='EC: '.$ec.'<br />';
		$message_admin .='Transactie nummer: '.$trxid.'<br />';
	
		/**
		 * Compose Mail for Client 
		 */
		$message_client ='<h2>Strippenkaart</h2>';
		$message_client .='Bedankt voor het afnemen van een strippenkaart. Hieronder vind u een overzicht van uw gegevens:<br /><br />';
		
		$message_client .='School: '.$schooldObject->name.'<br />';
		foreach($childObject as $k=>$child){
			if(count($childObject) == 1)
			{
				$message_client .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description.'<br />';	
			}else{
				if(count($description) == 1) {
					$message_client .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[0]->description.'<br />';
				}else{
					$message_client .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[$k]->description.'<br />';
				}
			}	
		}
		$message_client .='<h2>Betaalgegevens</h2>';
		$message_client .='Betaald: Ja<br />';
		$message_client .='Bank: '.$banks[$session_data['bank']].'<br />';
		$message_client .='EC: '.$ec.'<br />';
		$message_client .='Transactie nummer: '.$trxid.'<br />';
		
		/**
		 * Compose Mail for School 
		 */
		$message_school ='<h2>Strippenkaart</h2>';
		$message_school .='Er is een strippenkaart afgenomen:<br /><br />';
		
		if($userObject->first_name_mother !=null){
			$message_school .='1ste Ouder / verzorger: '.$userObject->first_name_mother.' '.$userObject->last_name_mother.'<br />';
			$message_school .='1ste Ouder / verzorger telefoon: '.$userObject->phone_mother.' <br />';
		}
		
		if($userObject->first_name_father !=null){
			$message_school .='2de Ouder / verzorger: '.$userObject->first_name_father.' '.$userObject->last_name_father.'<br />';
			$message_school .='2de Ouder / verzorger telefoon: '.$userObject->phone_father.' <br />';	
		}


		foreach($childObject as $k=>$child){
			if(count($childObject) == 1)
			{
				$message_school .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description.'<br />';	
			}else{
				if(count($description) == 1) {
					$message_school .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[0]->description.'<br />';
				}else{
					$message_school .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[$k]->description.'<br />';
				}
			}	
		}
		
		// Send mails
		$functionsClass->SendMail('Strippenkaart afgenomen', $settings->tso_admin_mail, $message_admin);
		$functionsClass->SendMail('Strippenkaart afgenomen', $userObject->email, $message_client);
		$functionsClass->SendMail('Strippenkaart afgenomen', $schooldObject->email, $message_school);
	
}else{

	if(count($childObject) > 1){
			
		// get card
		$cardObject = $wpdb->get_results( "SELECT * FROM {$table_cards} WHERE price IN(".implode(',', $card).")");
		$description = $cardObject;
				
		foreach($childObject as $k=>$child){
			
			if(count($cardObject) == 1){
				$card_desc = $cardObject[0]->description;
				$card_price = $cardObject[0]->price;
			}else{
				$card_desc = $cardObject[$k]->description;
				$card_price = $cardObject[$k]->price;
			}
			
			if($transactionCheck==null){
				
				$wpdb->insert($table_submissions, array(
				   "user_id" => $session_data['user_id'],
				   "child_id" => $child->id,
				   "school_id" => $userObject->school_id,
				   "groep" => $child->groep,
				   "card" => $card_desc,
				   "price" => $card_price,
				   "bank" => $session_data['bank'],
				   "ec" => $ec,
				   "trxid" => $trxid,
				   "ip" => $_SERVER['REMOTE_ADDR'],
				   "payment_status" => 0,
				   "created_at" => date('Y-m-d H:i:s'),
				));
			
			}
		}
		
	}else{
			
		$single_card= '';
		foreach($card as $key_single_card=>$single_card){
			if(isset($key_single_card)){
				$single_card= $single_card;
			}
		}
		// get card
		$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE price = ".$single_card."", OBJECT);
		$description = $cardObject->description;
		
			if($transactionCheck==null){
		// save
		$wpdb->insert($table_submissions, array(
		   "user_id" => $session_data['user_id'],
		   "child_id" => $childObject[0]->id,
		   "school_id" => $userObject->school_id,
		   "groep" => $childObject[0]->groep,
		   "card" => $description,
		   "price" => $cardObject->price,
		   "bank" => $session_data['bank'],
		   "ec" => $ec,
		   "trxid" => $trxid,
		   "ip" => $_SERVER['REMOTE_ADDR'],
		   "payment_status" => 0,
		   "created_at" => date('Y-m-d H:i:s'),
			));
		
		}
	}
		
	$message_client ='<h2>Strippenkaart</h2>';
	$message_client .='Helaas, het afnemen van een strippenkaart is helaas niet gelukt. Probeert u het nogmaals. Hieronder vind u een overzicht van uw gegevens:<br /><br />';
	$message_client .='School: '.$schooldObject->name.'<br />';
	$message_client .='<h2>Betaalgegevens</h2>';
	$message_client .='Betaald: <strong>Nee</strong><br />';
	$message_client .='Bank: '.$banks[$session_data['bank']].'<br />';
	$message_client .='EC: '.$ec.'<br />';
	$message_client .='Transactie nummer: '.$trxid.'<br />';

	$message_client .='School: '.$schooldObject->name.'<br />';
	
	foreach($childObject as $k=>$child){
		if(count($childObject) == 1)
		{
			$message_client .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description.'<br />';	
		}else{
			if(count($description) == 1) {
				$message_client .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[0]->description.'<br />';
			}else{
				$message_client .='Kind en groep: '.$child->first_name.' '.$child->last_name.' (groep: '.$child->groep.' ) -  '.$description[$k]->description.'<br />';
			}
			
		}	
	}
	$message_client .='<h2>Betaalgegevens</h2>';
	$message_client .='Betaald: Ja<br />';
	$message_client .='Bank: '.$banks[$session_data['bank']].'<br />';
	$message_client .='EC: '.$ec.'<br />';
	$message_client .='Transactie nummer: '.$trxid.'<br />';
}	

$_SESSION['message'] = $message_client;

header('Location: '.$settings->url_payment_done);