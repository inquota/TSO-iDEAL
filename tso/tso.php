<?php 
    /*
    Plugin Name: TSO
    Plugin URI: http://www.inquota.nl
    Description: Plugin for TSO.
    Author: J. de Jong
    Version: 0.1
    Author URI: http://www.inquota.nl
    */

/*
    Copyright (c) 2015 Jarah de Jong (jarahzakelijk@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
date_default_timezone_set("Europe/Amsterdam");
require 'classes/password.php';
require 'classes/functions.php';
require 'classes/TargetPayIdeal.php';
 
function tso_admin_actions() {
	add_menu_page("TSO", "TSO", 'manage_options', "tso", "tso_submissions");
	add_submenu_page( 'tso', 'Betalingen', 'Betalingen', 'manage_options', 'tso', 'tso_submissions');
	add_submenu_page( 'tso', 'Betalingen per week', 'Betalingen per week', 'manage_options', 'tso', 'tso_submissions_per_week');
	add_submenu_page( 'tso', 'Aanmeldingen', 'Aanmeldingen', 'manage_options', 'users', 'tso_users');
	add_submenu_page( 'tso', 'Scholen', 'Scholen', 'manage_options', 'schools', 'tso_schools');
	add_submenu_page( 'tso', 'Kinderen', 'Kinderen', 'manage_options', 'children', 'tso_children');
	add_submenu_page( 'tso', 'Strippenkaarten', 'Strippenkaarten', 'manage_options', 'cards', 'tso_cards');
	add_submenu_page( 'tso', 'Statistieken', 'Statistieken', 'manage_options', 'statistics', 'tso_statistics');
	add_submenu_page( 'tso', 'Instellingen', 'Instellingen', 'manage_options', 'settings', 'tso_settings');
}

add_action('admin_menu', 'tso_admin_actions');

function tso_schools(){
	include('tso_schools.php');  
}
function tso_cards(){
	include('tso_cards.php');  
}

function tso_users(){
	include('tso_users.php');  
}
function tso_statistics(){
	include('tso_statistics.php');  
}

function tso_submissions() {	  
    include('tso_submissions.php');  
} 
function tso_payments_per_week() {
	include('tso_submissions_per_week.php');  
}

function tso_children() {	  
    include('tso_children.php');  
}
function tso_settings() {	  
    include('tso_settings.php');  
}  

function load_scripts() {
	wp_enqueue_script(
		'tso-script',
		plugins_url() . '/tso/js/script.js',
		array( 'jquery' )
	);
}

add_action( 'wp_enqueue_scripts', 'load_scripts' );

function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');

function createPages(){

	// create Pages
	if (the_slug_exists('inloggen')) {
	    $my_post = array(
		  'post_title'    => 'Inloggen',
		  'post_content'  => 'Welkom op strippenkaart pagina, bent u nieuw dan dient u zicht eerst aan te melden als nieuwe gebruiker. Heeft u alles al ingevuld kunt u gelijk op gebruiker klikken en wordt u gebruikersnaam en wachtwoord gevraagd. [some_random_code_sc file="template-login"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
	}
	
	if (the_slug_exists('inschrijven')) {
	    $my_post = array(
		  'post_title'    => 'Inschrijven',
		  'post_content'  => '[some_random_code_sc file="template-account-add"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
	}

	if (the_slug_exists('strippenkaart')) {
	    $my_post = array(
		  'post_title'    => 'Strippenkaart',
		  'post_content'  => '[some_random_code_sc file="template-card"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
	}
	
	if (the_slug_exists('strippenkaart-toevoegen')) {
	    $my_post = array(
		  'post_title'    => 'Strippenkaart toevoegen',
		  'post_content'  => '[some_random_code_sc file="template-card-add"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
	}
	
	if (the_slug_exists('payment-done')) {
	    $my_post = array(
		  'post_title'    => 'Betaling afgerond',
		  'post_content'  => '[some_random_code_sc file="template-payment-done"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
	}
	
	if (the_slug_exists('profiel-bewerken')) {
	    $my_post = array(
		  'post_title'    => 'Profiel bewerken',
		  'post_content'  => '[some_random_code_sc file="template-account-edit"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
		}
		
		if (the_slug_exists('bedankt-aanmelden')) {
	    $my_post = array(
		  'post_title'    => 'Bedankt aanmelden',
		  'post_content'  => 'Bedankt voor het aanmelden',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
		}
		
		if (the_slug_exists('wachtwoord-instellen')) {
	    $my_post = array(
		  'post_title'    => 'Wachtwoord opnieuw instellen',
		  'post_content'  => '[some_random_code_sc file="template-password-change"]',
		  'post_status'   => 'publish',
		  'post_author'   => 1
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
		}
		
		if (the_slug_exists('profiel-aangepast')) {
		    $my_post = array(
			  'post_title'    => 'Profiel aangepast',
			  'post_content'  => 'Profiel aangepast',
			  'post_status'   => 'publish',
			  'post_author'   => 1
			);
			// Insert the post into the database
			wp_insert_post( $my_post );
		}		
		
		if (the_slug_exists('wachtwoord-vergeten')) {
		    $my_post = array(
			  'post_title'    => 'Wachtwoord vergeten',
			  'post_content'  => '[some_random_code_sc file="template-password-forget"]',
			  'post_status'   => 'publish',
			  'post_author'   => 1
			);
			
			// Insert the post into the database
			wp_insert_post( $my_post );

		}
}
		
function the_slug_exists($post_name) {
    global $wpdb;
    if($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

register_activation_hook(__FILE__, 'my_activation');
add_action('my_specific_event', 'do_this_specific');

function my_activation() {
	global $wpdb;
	
		// create tables
		$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_cards` (
	  `id` int(11) NOT NULL,
	  `description` varchar(90) NOT NULL,
	  `description_short` varchar(90) NOT NULL,
	  `price` int(11) NOT NULL,
	  `created_at` datetime NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
		
		$wpdb->query('
CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_children` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `groep` varchar(2) DEFAULT NULL,
  `card` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');


		$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_schools` (
		  `id` int(11) NOT NULL,
		  `name` varchar(80) NOT NULL,
		  `email` varchar(60) DEFAULT NULL,
		  `created_at` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
		);
	
		
	$wpdb->query('
				CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_settings` (
				  `id` int(11) NOT NULL,
				  `targetpay_rtlo` int(11) DEFAULT NULL COMMENT \'TargetPay RTLO\',
				  `targetpay_testmode` tinyint(1) NOT NULL DEFAULT \'1\',
				  `tso_admin_mail` varchar(40) NOT NULL,
				  `form_id` int(11) DEFAULT NULL COMMENT \'Load schools for form id\',
				  `field_id` int(11) DEFAULT NULL COMMENT \'Load schools for field id\',
				  `url_login` varchar(90) DEFAULT NULL,
				  `url_register` varchar(90) DEFAULT NULL,
				  `url_card_overview` varchar(90) DEFAULT NULL,
				  `url_card_add` varchar(90) DEFAULT NULL,
				  `url_payment_done` varchar(90) DEFAULT NULL,
				  `url_profile_edit` varchar(90) DEFAULT NULL,
				  `url_profile_created` varchar(90) NOT NULL,
				  `url_password_change` varchar(255) NOT NULL,
				  `url_profile_edit_done` varchar(90) NOT NULL,
				  `url_password_forget` varchar(90) NOT NULL,
				  `url_terms` varchar(90) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
	);
	
	// get settings
		$settings = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."wv_reservations_settings WHERE id=1", OBJECT );
		
		if($settings==null){
			$wpdb->query("INSERT INTO `".$wpdb->prefix."tso_settings` (`id`, `targetpay_rtlo`, `targetpay_testmode`, `tso_admin_mail`, `url_login`, `url_register`, `url_card_overview`, `url_card_add`, `url_payment_done`, `url_profile_edit`, `url_profile_created`, `url_password_change`, `url_profile_edit_done`, `url_password_forget`, `url_terms`) VALUES
			(1, 000000, 1, 'email@email.com', '/inloggen/', '/inschrijven/', '/strippenkaart/', '/strippenkaart-toevoegen/', '/payment-done/', '/profiel-bewerken/', '/bedankt-aanmelden/', '/wachtwoord-instellen/', '/profiel-aangepast/', '/wachtwoord-vergeten/', '/algemene-voorwaarden/');");
		}	
		
	$wpdb->query('
		CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_submissions` (
		  `id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `child_id` int(11) NOT NULL,
		  `school_id` int(11) NOT NULL,
		  `groep` char(4) NOT NULL,
		  `card` varchar(60) NOT NULL,
		  `price` int(5) NOT NULL,
		  `bank` varchar(20) NOT NULL,
		  `ec` varchar(64) NOT NULL,
		  `trxid` varchar(64) NOT NULL,
		  `ip` varchar(32) NOT NULL,
		  `payment_status` tinyint(1) NOT NULL,
		  `created_at` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
	);
	
	$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_users` (
		  `id` int(11) NOT NULL,
		  `email` varchar(50) DEFAULT NULL,
		  `password` varchar(90) DEFAULT NULL,
		  `first_name_father` varchar(40) DEFAULT NULL,
		  `last_name_father` varchar(40) DEFAULT NULL,
		  `phone_father` varchar(12) DEFAULT NULL,
		  `first_name_mother` varchar(40) DEFAULT NULL,
		  `last_name_mother` varchar(40) DEFAULT NULL,
		  `phone_mother` varchar(12) DEFAULT NULL,
		  `address` varchar(60) DEFAULT NULL,
		  `number` varchar(6) DEFAULT NULL,
		  `postalcode` varchar(8) DEFAULT NULL,
		  `city` varchar(70) DEFAULT NULL,
		  `phone_unreachable` varchar(40) DEFAULT NULL,
		  `relation_child` varchar(50) DEFAULT NULL,
		  `name_doc` varchar(40) DEFAULT NULL,
		  `phone_doc` varchar(12) DEFAULT NULL,
		  `address_doc` varchar(40) DEFAULT NULL,
		  `number_doc` varchar(6) DEFAULT NULL,
		  `city_doc` varchar(40) DEFAULT NULL,
		  `name_dentist` varchar(40) DEFAULT NULL,
		  `phone_dentist` varchar(20) DEFAULT NULL,
		  `address_dentist` varchar(40) DEFAULT NULL,
		  `number_dentist` varchar(6) DEFAULT NULL,
		  `city_dentist` varchar(40) DEFAULT NULL,
		  `days_care` varchar(255) DEFAULT NULL,
		  `school_id` int(11) DEFAULT NULL,
		  `toelichting1` text,
		  `toelichting2` text,
		  `toelichting3` text,
		  `ip` varchar(32) DEFAULT NULL,
		  `verified` datetime DEFAULT NULL,
		  `created_at` datetime DEFAULT NULL,
		  `hash` varchar(90) DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
');

$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'tso_maillog` (
  `id` int(11) NOT NULL,
  `receiver` varchar(90) NOT NULL,
  `subject` varchar(90) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');




	$wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_cards`
  ADD PRIMARY KEY (`id`);');
  
	$wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_children`
  ADD PRIMARY KEY (`id`);');
  
	$wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_schools`
  ADD PRIMARY KEY (`id`);');
  
	$wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_submissions`
  ADD PRIMARY KEY (`id`);');
  
	$wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_users`
  ADD PRIMARY KEY (`id`);'); 
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_maillog`
  ADD PRIMARY KEY (`id`);');
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
  
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'tso_maillog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
  
	createPages();
	
	$path = get_home_path();
	
	// copy source files to their target location
	$source_files=array(
		0 => array('source' => 'tso-ideal-check.php', 'target' => $path, 'copy_from' => $path . 'wp-content/plugins/tso/source_files'),
		1 => array('source' => 'tso-verify.php', 'target' => $path, 'copy_from' => $path . 'wp-content/plugins/tso/source_files'),
		2 => array('source' => 'tso-export-children.php', 'target' => $path, 'copy_from' => $path . 'wp-content/plugins/tso/source_files'),
		3 => array('source' => 'tso-template-account-add.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		4 => array('source' => 'tso-template-account-edit.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		5 => array('source' => 'tso-template-card.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		6 => array('source' => 'tso-template-card-add.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		7 => array('source' => 'tso-template-login.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		8 => array('source' => 'tso-template-password-change.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		9 => array('source' => 'tso-template-password-forget.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
		10 => array('source' => 'tso-template-payment-done.php', 'target' => get_template_directory(), 'copy_from' => $path . 'wp-content/plugins/tso/templates'),
	);
	
	foreach($source_files as $k=>$v){
		if(!file_exists($v['target'].'/'.$v['source'])){
			copy($v['copy_from'].'/'.$v['source'], $v['target'].'/'.$v['source']);
		}
	}
}

function my_deactivation() {
	
}

function some_random_code($atts){
	$a = shortcode_atts( array(
        'file' => 'FILE_',
    ), $atts );

   global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header, $wpdb;

   include_once get_template_directory() . "/tso-{$a['file']}.php";
   
}
add_shortcode( 'some_random_code_sc', 'some_random_code' );