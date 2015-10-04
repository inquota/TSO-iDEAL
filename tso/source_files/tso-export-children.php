<?php
/***************DO NOT ALLOW DIRECT ACCESS************************************/
if ( stripos( $_SERVER[ 'REQUEST_URI' ], basename( __FILE__ ) ) !== FALSE ) { // TRUE if the script's file name is found in the URL
  header( 'HTTP/1.0 403 Forbidden' );
  die( "<h2>Forbidden! You don't have permission to access this page.</h2>" );
}
/******/
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);
date_default_timezone_set("Europe/Amsterdam");
require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once 'wp-content/plugins/tso/classes/lib/phpexcel-1.8.0/PHPExcel/IOFactory.php';

$functionsClass = new Functions();

$fileType = 'Excel5';
$fileName = 'TSO-borger-odoorn.xls';
$fileNameNew = 'TSO-borger-odoorn-'.date('d-m-Y-H-i').'.xls';

// Read the file
$objReader = PHPExcel_IOFactory::createReader($fileType);
$objPHPExcel = $objReader->load($fileName);

$table_users = $wpdb->prefix . 'tso_users';
$table_schools = $wpdb->prefix . 'tso_schools';
$table_children = $wpdb->prefix . 'tso_children';

$schools = $wpdb->get_results( 
"
SELECT
	School.id AS SchoolId, 
	School.name AS SchoolName
FROM
	{$table_schools} AS School
	
	ORDER BY School.name ASC
"
);
$children = $wpdb->get_results( 
	"
	SELECT 
		School.name AS SchoolName,
		Child.first_name AS ChildFirstName, 
		Child.last_name AS ChildLastName,
		User.email AS UserEmail, 
		User.days_care AS DaysCare,
		User.school_id AS SchoolId
	FROM
		{$table_children} AS Child
	LEFT JOIN 
		{$table_users} AS User ON (Child.user_id = User.id) 
	LEFT JOIN 
		{$table_schools} AS School ON (User.school_id = School.id)
		ORDER BY School.name ASC
	"
	);
$i = 1;

foreach($schools as $school) {
	
	$sheetNames = $objPHPExcel->getSheetNames();
	if(!in_array($school->SchoolName, $sheetNames)) {
		
		$objWorkSheet = $objPHPExcel->createSheet($i);
		$objWorkSheet->setTitle($school->SchoolName);
	
	}
	
	$i++;
}

foreach($objPHPExcel->getSheetNames() as $key_sheet => $sheet) {
	
	$cstart = 0;
	$rstart = 1;
	$count = 1;
	
	foreach($children as $item) {
		            
	if($sheet == $item->SchoolName) {
				
		$objPHPExcel->setActiveSheetIndex($key_sheet);
		$objWorksheet = $objPHPExcel->getSheet($key_sheet);
		
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart, $rstart, $item->SchoolName);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 1, $rstart, $item->ChildFirstName . ' ' . $item->ChildLastName);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 2, $rstart, $item->UserEmail);
		
			
		$daysParts = explode(',',$item->DaysCare); 
		if(!empty($daysParts[0])){
			foreach($daysParts as $day) {
				if($day == 'Maandag') {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 3, $rstart, 'X');
				}
				if($day == 'Dinsdag') {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 4, $rstart, 'X');
				}
				if($day == 'Donderdag') {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 5, $rstart, 'X');
				}
				if($day == 'Vrijdag') {
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 6, $rstart, 'X');
				}
			}	
		}

		$rstart++;
	}
	}
	
}

// Auto size columns for each worksheet
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

    $sheet = $objPHPExcel->getActiveSheet();
    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(true);
    /** @var PHPExcel_Cell $cell */
    foreach ($cellIterator as $cell) {
        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
    }
}

// Write the file
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $fileType);
$objWriter->save($fileNameNew);
//echo 'Saved...' . time();

// Send mails
$message = '<h2>Excel export aanmeldingen</h2>';
$message .= 'Export van alle kinderen per school.';
$blog_title = get_bloginfo(); 
$functionsClass->SendMail($blog_title, 'Excel export aanmeldingen ', 'jarahdamiel@gmail.com', $message, $fileNameNew);
//$settings->tso_admin_mail
echo "OK";
