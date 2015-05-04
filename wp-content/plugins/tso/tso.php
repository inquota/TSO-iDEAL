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
	add_submenu_page( 'tso', 'Statistics', 'Statistics', 'manage_options', 'statistics', 'tso_statistics');
	add_submenu_page( 'tso', 'Settings', 'Settings', 'manage_options', 'settings', 'tso_settings');
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