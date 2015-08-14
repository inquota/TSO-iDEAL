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
		$message .='School: '.$schooldObject->name.'<br />';
		$message .='Dagen voor opvang: '.$userObject->days_care.'<br />';
		
		foreach($childObjects as $child){
			$message .='Kind en groep: '.$child->first_name.' ' . $child->last_name. ' (groep: '.$child->groep.')<br />';	
		}
		
		$message .='<h2>Gegevens ouders</h2>';
		
		if($userObject->first_name_mother !=null){
			$message .='1ste Ouder / verzorger: '.$userObject->first_name_mother.' '.$userObject->last_name_mother.'<br />';
			$message .='1ste Ouder / verzorger telefoon: '.$userObject->phone_mother.' <br />';
		}
		
		if($userObject->first_name_father !=null){
			$message .='2de Ouder / verzorger: '.$userObject->first_name_father.' '.$userObject->last_name_father.'<br />';
			$message .='2de Ouder / verzorger telefoon: '.$userObject->phone_father.' <br />';	
		}
		$message .='Adres: '.$userObject->address.'<br />';
		$message .='Huisnummer: '.$userObject->number.'<br />';
		$message .='Postcode en woonplaats: '.$userObject->postalcode . ' ' . $userObject->city .'<br />';
		$message .='Telefoon bij onbereikbaar: '.$userObject->phone_unreachable.'<br />';
		$message .='Relatie tot kind(eren): '.$userObject->relation_child.'<br /><br />';
		
		$message .='<h3>Dokter</h3>';
		$message .='Naam: '.$userObject->name_doc.'<br />';
		$message .='Telefoon: '.$userObject->phone_doc.'<br />';
		$message .='Adres: '.$userObject->address_doc.'<br />';
		$message .='Huisnummer: '.$userObject->number_doc.'<br />';
		$message .='Woonplaats: '.$userObject->city_doc.'<br />';
		
		$message .='<h3>Tandarts</h3>';
		$message .='Naam: '.$userObject->name_dentist.'<br />';
		$message .='Telefoon: '.$userObject->phone_dentist.'<br />';
		$message .='Adres: '.$userObject->address_dentist.'<br />';
		$message .='Huisnummer: '.$userObject->number_dentist.'<br />';
		$message .='Woonplaats: '.$userObject->city_dentist.'<br /><br />';
		
		$message .='<h3>Toelichting</h3>';
		$message .='Mijn kind(eren) blijft/blijven niet op vaste dagen over: '.$userObject->toelichting1.'<br />';
		$message .='Mijn kinderen blijven niet op de zelfde dagen over: '.$userObject->toelichting2.'<br />';
		$message .='Bijzonderheden kind(eren): '.$userObject->toelichting3.'<br />';
		
		// Send mails
		$functionsClass->SendMail('Aanmelding', $settings->tso_admin_mail, $message);
		
		$functionsClass->SendMail('Aanmelding', $schooldObject->email, $message);	
		
	header('Location: '.$settings->url_login.'?email='.$user->email);
}else{
	echo 'User doesnt exists or is already activated';
	exit;	
}
