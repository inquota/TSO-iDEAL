<?php
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);

require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header, $wpdb;

if(!isset($_GET['hash'])){
	exit;
}

$table_users = $wpdb->prefix . 'tso_users';

$user = $wpdb->get_row("SELECT * FROM {$table_users} WHERE hash = '".$_GET['hash']."' AND verified IS NULL");

if($user != null){
	
	// Save e-mail templates
	$wpdb->update( 
	$table_users, 
			array( 
				'verified' => date('c'),	// string
			), 
			array( 'id' => $user->id )
		);
	header('Location: /inloggen/?email='.$user->email);
}else{
	echo 'User doesnt exists or is already activated';
	exit;	
}
