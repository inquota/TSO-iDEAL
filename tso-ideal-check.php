<?php
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);

require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header, $wpdb;

if(!isset($_GET['trxid']) && !isset($_GET['ec'])){
	exit;
}

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

$session_data = $_SESSION['data'];

// tables
$table_submissions = $wpdb->prefix . 'tso_submissions';
$table_children = $wpdb->prefix . 'tso_children';
$table_cards = $wpdb->prefix . 'tso_cards';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_users = $wpdb->prefix . 'tso_users';
$table_settings = $wpdb->prefix . 'tso_settings';

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

// get child
$childObject = $wpdb->get_row( "SELECT * FROM {$table_children} WHERE id =".$session_data['child_id'], OBJECT );
	
// get card
$cardObject = $wpdb->get_row( "SELECT * FROM {$table_cards} WHERE id =".$session_data['card'], OBJECT );

// get user
$userObject = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE id =".$session_data['user_id'], OBJECT );

// get school
$schooldObject = $wpdb->get_row( "SELECT * FROM {$table_schools} WHERE id =".$userObject->school_id, OBJECT );

if ($oIdeal->validatePayment($trxid, 1,$settings->targetpay_testmode) == true) {
		
	// check if child has a group
	if($childObject->groep == null){
		// save
		$wpdb->update( 
		$table_children, 
				array( 
					'groep' => $session_data['groep'],
				), 
				array( 'id' => $session_data['child_id'] )
			);
	}elseif($childObject->groep != $session_data['school']){
		// save
		$wpdb->update( 
		$table_children, 
				array( 
					'groep' => $session_data['groep'],
				), 
				array( 'id' => $session_data['child_id'] )
			);
	}
		// check if child has a group
	if($childObject->card == null){
		// save
		$wpdb->update( 
		$table_children, 
				array( 
					'card' => $session_data['card'],
				), 
				array( 'id' => $session_data['child_id'] )
			);
	}	
	
	if($transactionCheck==null){
		// save
		$wpdb->insert($table_submissions, array(
		   "user_id" => $session_data['user_id'],
		   "child_id" => $session_data['child_id'],
		   "school_id" => $userObject->school_id,
		   "groep" => $childObject->groep,
		   "card" => $cardObject->description,
		   "price" => $cardObject->price,
		   "bank" => $session_data['bank'],
		   "ec" => $ec,
		   "trxid" => $trxid,
		   "ip" => $_SERVER['REMOTE_ADDR'],
		   "payment_status" => 1,
		   "created_at" => date('Y-m-d H:i:s'),
			));
		
		// get last id
		 if($wpdb->insert_id==0){
		 	$last_insert_id = 'Onbekend';
		 }else{
		 	$last_insert_id = $wpdb->insert_id;
		 }
		
		/**
		 * Compose Mail for Admin 
		 */ 
		$message_admin ='<h2>Strippenkaart</h2>';
		$message_admin .='Er is een strippenkaart afgenomen:<br /><br />';
		$message_admin .='Strippenkaart nummer: '.$last_insert_id.'<br />';
		
		if($userObject->name_father !=null){
			$message_admin .='Naam vader: '.$userObject->name_father.'<br />';	
		}

		if($userObject->name_mother !=null){
			$message_admin .='Naam moeder: '.$userObject->name_mother.'<br />';	
		}
		
		$message_admin .='School: '.$schooldObject->name.'<br />';
		$message_admin .='Kind: '.$childObject->name.'<br />';
		$message_admin .='Groep: '.$session_data['groep'].'<br />';
		$message_admin .='Strippenkaart: '.$cardObject->description.'<br /><br />';
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
		$message_client .='Strippenkaart nummer: '.$last_insert_id.'<br />';
		$message_client .='School: '.$schooldObject->name.'<br />';
		$message_client .='Kind: '.$childObject->name.'<br />';
		$message_client .='Groep: '.$childObject->groep.'<br />';
		$message_client .='Strippenkaart: '.$cardObject->description.'<br /><br />';
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
		$message_school .='Strippenkaart nummer: '.$last_insert_id.'<br />';
		$message_school .='Kind: '.$childObject->name.'<br />';
		$message_school .='Groep: '.$childObject->groep.'<br />';
		$message_school .='Strippenkaart: '.$cardObject->description.'<br /><br />';
		
		// Send mails
		$functionsClass->SendMail('Strippenkaart afgenomen', get_option('admin_email'), $message_admin);
		$functionsClass->SendMail('Strippenkaart afgenomen', $userObject->email, $message_client);
		
		if($schooldObject->email!=null){
			$emails2=explode(',',$schooldObject->email);

			// if school has multiple emails use bcc
			if(count($emails2) > 1 && is_array($emails2)){
				$functionsClass->SendMail('Strippenkaart afgenomen', $schooldObject->email, $message_school, $schooldObject->email);
			}else{
				$functionsClass->SendMail('Strippenkaart afgenomen', $schooldObject->email, $message_school);	
			}
		}
		
	}
	
}else{


	if($transactionCheck==null){
		// save
		$wpdb->insert($table_submissions, array(
		   "user_id" => $session_data['user_id'],
		   "child_id" => $session_data['child_id'],
		   "school_id" => $userObject->school_id,
		   "groep" => $childObject->groep,
		   "card" => $cardObject->description,
		   "price" => $cardObject->price,
		   "bank" => $session_data['bank'],
		   "ec" => $ec,
		   "trxid" => $trxid,
		   "ip" => $_SERVER['REMOTE_ADDR'],
		   "payment_status" => 0,
		   "created_at" => date('Y-m-d H:i:s'),
			));
		
		$message_client ='<h2>Strippenkaart</h2>';
		$message_client .='Helaas, het afnemen van een strippenkaart is helaas niet gelukt. Probeert u het nogmaals. Hieronder vind u een overzicht van uw gegevens:<br /><br />';
		$message_client .='School: '.$schooldObject->name.'<br />';
		$message_client .='Kind: '.$childObject->name.'<br />';
		$message_client .='Groep: '.$childObject->groep.'<br />';
		$message_client .='Strippenkaart: '.$cardObject->description.'<br /><br />';
		$message_client .='<h2>Betaalgegevens</h2>';
		$message_client .='Betaald: <strong>Nee</strong><br />';
		$message_client .='Bank: '.$banks[$session_data['bank']].'<br />';
		$message_client .='EC: '.$ec.'<br />';
		$message_client .='Transactie nummer: '.$trxid.'<br />';
	}
}	

$_SESSION['message'] = $message_client;

header('Location: /payment-done/');