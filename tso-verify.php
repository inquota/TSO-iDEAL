<?php
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);

require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header, $wpdb;

if(!isset($_GET['hash'])){
	exit;
}

$functionsClass = new Functions();

// tables
$table_users = $wpdb->prefix . 'tso_users';
$table_settings = $wpdb->prefix . 'tso_settings';
$table_children = $wpdb->prefix . 'tso_children';
$table_schools = $wpdb->prefix . 'tso_schools';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

$user = $wpdb->get_row("SELECT * FROM {$table_users} WHERE hash = '".$_GET['hash']."' AND verified IS NULL");

if($user != null){
	
	// load data
	
	// get user
	$userObject = $wpdb->get_row( "SELECT * FROM {$table_users} WHERE hash ='".$_GET['hash']."' ", OBJECT );
	
	// get school
	$schooldObject = $wpdb->get_row( "SELECT * FROM {$table_schools} WHERE id =".$userObject->school_id, OBJECT );
	
	// get children
	$childObjects = $wpdb->get_results( "SELECT * FROM {$table_children} WHERE user_id =".$userObject->id );
	
	// Save
	$wpdb->update( 
	$table_users, 
			array( 
				'verified' => date('c'),	// string
			), 
			array( 'id' => $user->id )
		);
		
		
		/**
		 * Compose Mail for School 
		 */
		$message ='<h2>Gegevens aanmelding</h2>';
		$message .='Groep: '.$childObject->groep.'<br />';
		$message .='School: '.$schooldObject->name.'<br />';
		$message .='Dagen voor opvang: '.$userObject->days_care.'<br />';
		
		foreach($childObjects as $child){
			$message .='Kind en groep: '.$child->name.' (groep: '.$child->groep.')<br />';	
		}
		
		$message .='<h2>Gegevens ouders</h2>';
		
		if($userObject->name_father !=null){
			$message .='Naam vader: '.$userObject->name_father.'<br />';
			$message .='Telefoon vader: '.$userObject->phone_father.'<br />';	
		}
		
		if($userObject->name_mother !=null){
			$message .='Naam moeder: '.$userObject->name_mother.'<br />';
			$message .='Telefoon moeder: '.$userObject->phone_mother.'<br />';	
		}
		$message .='Adres: '.$userObject->address.'<br />';
		$message .='Postcode en woonplaats: '.$userObject->postalcode . ' ' . $userObject->city .'<br />';
		$message .='Telefoon bij onbereikbaar: '.$userObject->phone_unreachable.'<br />';
		$message .='Relatie tot kind(eren): '.$userObject->relation_child.'<br /><br />';
		
		$message .='<h3>Dokter</h3>';
		$message .='Naam dokter: '.$userObject->name_doc.'<br />';
		$message .='Telefoon dokter: '.$userObject->phone_doc.'<br />';
		$message .='Adres dokter: '.$userObject->address_doc.'<br />';
		$message .='Woonplaats dokter: '.$userObject->city_doc.'<br />';
		$message .='<h3>Tandarts</h3>';
		$message .='Naam tandarts: '.$userObject->name_dentist.'<br />';
		$message .='Telefoon tandarts: '.$userObject->phone_dentist.'<br />';
		$message .='Adres tandarts: '.$userObject->address_dentist.'<br />';
		$message .='Woonplaats tandarts: '.$userObject->city_dentist.'<br /><br />';
		
		// Send mails
		$functionsClass->SendMail('Aanmelding', get_option('admin_email'), $message);
		
		if($schooldObject->email!=null){
			$emails2=explode(',',$schooldObject->email);

			// if school has multiple emails use bcc
			if(count($emails2) > 1 && is_array($emails2)){
				$functionsClass->SendMail('Aanmelding', $schooldObject->email, $message, $schooldObject->email);
			}else{
				$functionsClass->SendMail('Aanmelding', $schooldObject->email, $message);	
			}
		}	
		
	header('Location: '.$settings->url_login.'?email='.$user->email);
}else{
	echo 'User doesnt exists or is already activated';
	exit;	
}
