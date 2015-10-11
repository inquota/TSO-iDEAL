<?php
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);
date_default_timezone_set("Europe/Amsterdam");
require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once 'wp-content/plugins/tso/classes/phpword.php';
$table_settings = $wpdb->prefix . 'tso_settings';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

$functionsClass = new Functions();
$PHPWordCustom = new PHPWordCustom();

$week = $_POST['week'];
$year = $_POST['year'];

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();
		
$filename = 'Betalingen-Week-'.$week.'-'.$year.'.docx';
$PHPWordCustom->createWordPaymentsLastWeek($wpdb, $year, $week, $filename, 'https://delunchclub-opo.nl/wp-content/uploads/2015/05/cropped-header_27-51.jpg');

$message = '<h1>Betalingen van week ' . $week. '</h1>';
$message.= 'In de bijlage een Word document van alle betalingen van de door u gekozen week.';
$blog_title = get_bloginfo();  
$functionsClass->SendMail($blog_title, 'Betalingen van week ' . $week, $settings->tso_admin_mail, $message, $filename);
echo 'Saved...' . time();
unlink($filename);		