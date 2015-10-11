<?php
define('WP_USE_THEMES', false);
define('DONOTCACHEPAGE', true);
date_default_timezone_set("Europe/Amsterdam");
require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once 'wp-content/plugins/tso/classes/lib/phpexcel-1.8.0/PHPExcel/IOFactory.php';
$table_settings = $wpdb->prefix . 'tso_settings';
// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

$functionsClass = new Functions();

$fileType = 'Excel5';
$fileName = 'TSO-overblijvers-borger-odoorn-template.xls';
$fileNameNew = 'TSO-overblijvers-borger-odoorn.xls';

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
		
		//$objWorkSheet = $objPHPExcel->createSheet($i);
		//$objWorkSheet->setTitle($school->SchoolName);
		
		//  Get the current sheet with all its newly-set style properties
		$objWorkSheetBase = $objPHPExcel->getSheet();
		
		//  Create a clone of the current sheet, with all its style properties
		$objWorkSheet = clone $objWorkSheetBase;
		//  Set the newly-cloned sheet title
		$objWorkSheet->setTitle($school->SchoolName);
		//  Attach the newly-cloned sheet to the $objPHPExcel workbook
		$objPHPExcel->addSheet($objWorkSheet);
	
	}
	
	$i++;
}

foreach($objPHPExcel->getSheetNames() as $key_sheet => $sheet) {
	
	$cstart = 1;
	$rstart = 15;
	$count = 1;
	
	foreach($children as $item) {
		            
		if($sheet == $item->SchoolName) {
					
			$objPHPExcel->setActiveSheetIndex($key_sheet);
			$objWorksheet = $objPHPExcel->getSheet($key_sheet);
			
	        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart, $rstart, $item->SchoolName);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 8, $rstart, $item->ChildFirstName . ' ' . $item->ChildLastName);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 9, $rstart, $item->UserEmail);
			
				
			$daysParts = explode(',',$item->DaysCare); 
			if(!empty($daysParts[0])){
				foreach($daysParts as $day) {
					if($day == 'Maandag') {
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 10, $rstart, 'X'); // L
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 15, $rstart, 'X'); //Q
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 20, $rstart, 'X'); //V
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 25, $rstart, 'X'); //AA
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 30, $rstart, 'X'); //AF
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 35, $rstart, 'X'); //AK
					}
					if($day == 'Dinsdag') {
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 11, $rstart, 'X');
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 16, $rstart, 'X'); //R
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 21, $rstart, 'X'); //W
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 26, $rstart, 'X'); //AB
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 31, $rstart, 'X'); //AG
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 36, $rstart, 'X'); //AL
					}
					if($day == 'Donderdag') {
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 12, $rstart, 'X');
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 17, $rstart, 'X'); //S
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 22, $rstart, 'X'); //X
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 27, $rstart, 'X'); //AC
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 32, $rstart, 'X'); //AH
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 37, $rstart, 'X'); //AM
					}
					if($day == 'Vrijdag') {
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 13, $rstart, 'X');
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 18, $rstart, 'X'); //T
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 23, $rstart, 'X'); //Y
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 28, $rstart, 'X'); //AD
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 33, $rstart, 'X'); //AI
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cstart + 38, $rstart, 'X'); //AN
					}
				}	
			}
			
			$objPHPExcel->getActiveSheet()
			    ->setCellValue('R2', '=Q2+1')
				->setCellValue('S2', '=R2+2')
				->setCellValue('T2', '=S2+1')
				->setCellValue('V2', '=Q2+7') // new week
				->setCellValue('W2', '=V2+1')
				->setCellValue('X2', '=W2+2')
				->setCellValue('Y2', '=X2+1')
				->setCellValue('AA2', '=v2+7') // new week
				->setCellValue('AB2', '=AA2+1')
				->setCellValue('AC2', '=AB2+2')
				->setCellValue('AD2', '=AC2+1')
				->setCellValue('AF2', '=AA2+7') // new week
				->setCellValue('AG2', '=AF2+1')
				->setCellValue('AH2', '=AG2+2')
				->setCellValue('AI2', '=AH2+1')
				->setCellValue('AK2', '=AF2+7') // new week
				->setCellValue('AL2', '=AK2+1')
				->setCellValue('AM2', '=AL2+2')
				->setCellValue('AN2', '=AM2+1')
			;
	
			$rstart++;
		}
	}
	
}

// Auto size columns for each worksheet
/*foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

    $sheet = $objPHPExcel->getActiveSheet();
    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(true);
    /** @var PHPExcel_Cell $cell */
    /*foreach ($cellIterator as $cell) {
        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
    }*/
//}

// Write the file
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $fileType);
$objWriter->save($fileNameNew);

// Send mails
$message = '<h2>Excel export aanmeldingen</h2>';
$message .= 'Export van alle kinderen per school.';
$blog_title = get_bloginfo(); 
$functionsClass->SendMail($blog_title, 'Excel export aanmeldingen ', $settings->tso_admin_mail, $message, $fileNameNew);
echo 'Saved...' . time();
unlink($fileNameNew);