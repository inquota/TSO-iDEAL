<?php 
    /*
    Plugin Name: TSO
    Plugin URI: http://www.inquota.nl
    Description: Plugin for TSO.
    Author: J. de Jong
    Version: 1.0
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
error_reporting(E_ALL);
ini_set('display_errors', true);

require 'classes/password.php';
require 'classes/functions.php';
require 'classes/TargetPayIdeal.php';

 
function tso_admin_actions() {
	add_menu_page("TSO", "TSO", 'manage_options', "tso", "tso_submissions");
	add_submenu_page( 'tso', 'Submissions', 'Submissions', 'manage_options', 'tso', 'tso_submissions');
	add_submenu_page( 'tso', 'Users', 'Users', 'manage_options', 'users', 'tso_users');
	add_submenu_page( 'tso', 'Schools', 'Schools', 'manage_options', 'schools', 'tso_schools');
	add_submenu_page( 'tso', 'Children', 'Children', 'manage_options', 'children', 'tso_children');
	add_submenu_page( 'tso', 'Cards', 'Cards', 'manage_options', 'cards', 'tso_cards');
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

function tso_submissions() {	  
    include('tso_submissions.php');  
} 

function tso_children() {	  
    include('tso_children.php');  
} 

// Gravityforms hook
add_action('gform_after_submission', 'post_to_third_party', 10, 2);
function post_to_third_party($entry, $form) {
	
	global $wpdb;
	
	$table_users = $wpdb->prefix . 'tso_users';
		
	$passwordClass = new Password();
	$functionsClass = new Functions();
	
	$password_readable = $functionsClass->randomPassword();
	$password_hash = $passwordClass->create_hash($password_readable);
	$hash = $functionsClass->RandomHash();
	
	$email = $entry[2];
	
	/**
	 * Compose Mail
	 */
	$message ='Beste,<br /><br />';
	$message .='Inlog: '.$email . '<br />';
	$message .='Wachtwoord: '.$password_readable . '<br />';
	$message .='Klik hier om uw account te bevestigen: <a href="http://'.$_SERVER['HTTP_HOST'].'/tso-verify.php?hash='.$hash.'">activeren</a><br />';
	
	$functionsClass->SendMail('TSO | Account', $email, $message);
	
	/**
	 * Insert into database
	 */
	$wpdb->insert($table_users, array(
	   "email" => $email,
	   "password" => $password_hash,
	   "name" => 'Jarah de Jong',
	   "address" => 'Saffierstraat 23 A-1',
	   "postalcode" => '3051XT',
	   "city" => 'Rotterdam',
	   "ip" => $_SERVER['REMOTE_ADDR'],
	   "created_at" => date('Y-m-d H:i:s'),
	   "hash" => $hash,
	));
}
	