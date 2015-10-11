<?php
require_once 'lib/PHPWord-develop-0.12.1/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();

class PHPWordCustom {
	
	private $_phpWord;
	
	public function __construct()
	{
		// Creating the new document...
		$this->_phpWord = new \PhpOffice\PhpWord\PhpWord();
	}
	/**
	 * Create a Word document with data of user Registration.
	 * 
	 * @author Jarah de Jong
	 * @param object $userObject
	 * @param object $childObjects
	 * @param object $schooldObject
	 * @param string $filename
	 * @param string $imageHeader
	 */
	public function createWordUserRegistration($userObject, $childObjects, $schooldObject, $filename, $imageHeader)
	{
		/* Note: any element you append to a document must reside inside of a Section. */
		//$section = $this->_phpWord->createSection(array('orientation'=>'landscape'));
		$section = $this->_phpWord->addSection();
		$section->addImage($imageHeader,     
		array(
	        'width' => 459,
	        'height' => 94,
    	));
		$header = array('size' => 14, 'bold' => true);
		
		$textParams = array('size' => 10);
		// This is used to remove "padding" below text-lines
		$noSpace = array('spaceAfter' => 0, 'spaceBefore' => 0);
		
		$section->addText(htmlspecialchars('Gegevens kinderen', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Basisschool", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($schooldObject->name, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Dagen opvang", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->days_care, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		
		foreach($childObjects as $child){
			$table->addRow();
			$table->addCell(5000)->addText(htmlspecialchars("Kind en groep", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
			$table->addCell(5000)->addText(htmlspecialchars($child->first_name.' ' . $child->last_name. ' (groep: '.$child->groep.')', ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		}
		
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Mijn kind(eren) blijft/blijven niet op vaste dagen over", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->toelichting1, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Mijn kinderen blijven niet op de zelfde dagen over", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->toelichting2, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Bijzonderheden kind(eren)", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->toelichting3, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		
		$section->addTextRun();
		$section->addText(htmlspecialchars('Gegevens ouders', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("E-mail", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->email, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("1ste Ouder / verzorger", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->first_name_mother . ' ' . $userObject->last_name_mother, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("2de Ouder / verzorger", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->first_name_father.' '.$userObject->last_name_father, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->address . ' ' . $userObject->number, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Postcode en woonplaats", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->postalcode . ' ' . $userObject->city, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon bij onbereikbaar", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->phone_unreachable, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Relatie tot kind(eren) ", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->relation_child, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		
		$section->addTextRun();
		$section->addText(htmlspecialchars('Gegevens dokter', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Naam", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->name_doc, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->phone_doc, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->address_doc, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Huisnummer", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->number_doc, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Woonplaats", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->city_doc, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
				
		$section->addTextRun();				
		$section->addText(htmlspecialchars('Gegevens tandarts', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Naam", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->name_dentist, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->phone_dentist, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->address_dentist, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Huisnummer", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->number_dentist, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Woonplaats", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($userObject->city_dentist, ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);		
		
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->_phpWord, 'Word2007');
		$objWriter->save($filename);
	}
	/**
	 * Create a Word document with data of user registration.
	 * 
	 * @author Jarah de Jong
	 * @param object $userObject
	 * @param object $childObjects
	 * @param string $filename
	 * @param string $imageHeader
	 */
	public function createWordUserEdit($post, $children, $filename, $imageHeader)
	{
		/* Note: any element you append to a document must reside inside of a Section. */
		$section = $this->_phpWord->addSection();
		$section->addImage($imageHeader,     
		array(
	        'width' => 459,
	        'height' => 94,
    	));
		$header = array('size' => 16, 'bold' => true);
		
		$textParams = array('size' => 10);
		// This is used to remove "padding" below text-lines
		$noSpace = array('spaceAfter' => 0, 'spaceBefore' => 0);
		
		$section->addText(htmlspecialchars('Gegevens kinderen', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Dagen opvang", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[26], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		
		foreach($children as $child){
			$table->addRow();
			$table->addCell(5000)->addText(htmlspecialchars("Kind en groep", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
			$table->addCell(5000)->addText(htmlspecialchars($child->first_name.' ' . $child->last_name. ' (groep: '.$child->groep.')', ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		}
		
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Mijn kind(eren) blijft/blijven niet op vaste dagen over", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[23], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Mijn kinderen blijven niet op de zelfde dagen over", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[24], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Bijzonderheden kind(eren)", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[25], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		
		$section->addTextRun();				
		$section->addText(htmlspecialchars('Gegevens ouders', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("E-mail", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[0], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("1ste Ouder / verzorger", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[4] . ' ' . $post[5], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("1ste Ouder / verzorger telefoon", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[6], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("2de Ouder / verzorger", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[1].' '.$post[2], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("2e Ouder / verzorger telefoon", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[3], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres en huisnummer", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[7] . ' ' . $post[8], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Postcode en woonplaats", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[9] . ' ' . $post[10], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon bij onbereikbaar", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[11], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Relatie tot kind(eren) ", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[12], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);

		$section->addTextRun();		
		$section->addText(htmlspecialchars('Gegevens dokter', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Naam", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[13], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[14], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres en huisnummer", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[15] . ' ' . $post[16], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Woonplaats", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[17], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);

		$section->addTextRun();				
		$section->addText(htmlspecialchars('Gegevens tandarts', ENT_COMPAT, 'UTF-8'), $header, $noSpace);
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Naam", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[18], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[19], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres en huisnummer", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[20] . ' ' . $post[21], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Woonplaats", ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		$table->addCell(5000)->addText(htmlspecialchars($post[22], ENT_COMPAT, 'UTF-8'), $textParams, $noSpace);
		
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->_phpWord, 'Word2007');
		
		$objWriter->save($filename);
	}
	/**
	 * 
	 */
	public function createWordPaymentsLastWeek($wpdb, $year, $week, $filename, $imageHeader)
	{
		$table_submissions = $wpdb->prefix . 'tso_submissions';
		$table_schools = $wpdb->prefix . 'tso_schools';
		$table_users = $wpdb->prefix . 'tso_users';
		$table_children = $wpdb->prefix . 'tso_children';
			
			$submissionsCurrentWeek = $wpdb->get_results( 
				"
				SELECT 
					Submission.id,
					Submission.created_at,
					Submission.groep,
					Submission.card,
					Submission.price,
					Submission.payment_status,
					Submission.bank,
					User.first_name_father,
					User.last_name_father,
					User.first_name_mother,
					User.last_name_mother,
					School.name AS name_school,
					Child.first_name AS first_name,
					Child.last_name AS last_name
				FROM 
					{$table_submissions} as Submission 
				LEFT JOIN {$table_users} AS User ON (Submission.user_id=User.id) 
				LEFT JOIN {$table_schools} AS School ON (Submission.school_id=School.id)
				LEFT JOIN {$table_children} AS Child ON (Submission.child_id=Child.id)
				WHERE year(Submission.created_at)= ".$year." AND week(Submission.created_at, 3)= ".$week."
				ORDER BY Submission.created_at ASC, School.name ASC
				"
				);
				
			$cards = $wpdb->get_results( 
				"
				SELECT 
					COUNT(`card`) AS CountCard,
					Submission.card
				FROM 
					{$table_submissions} as Submission 
				WHERE year(Submission.created_at)= ".$year." AND week(Submission.created_at, 3)= ".$week."
				GROUP BY Submission.card
				"
				);
		
		$section = $this->_phpWord->createSection(array('orientation'=>'landscape'));
		$section->addImage($imageHeader,     
		array(
	        'width' => 459,
	        'height' => 94,
    	));
		$header = array('size' => 15, 'bold' => true);
		// 1. Basic table
		$section->addText(htmlspecialchars('Betalingen van week ' . $week . ' - ' . date('Y'), ENT_COMPAT, 'UTF-8'), $header);
		$section->addTextRun();
		$table = $section->addTable();
		
		$table->addRow();
		$table->addCell(2200)->addText(htmlspecialchars("School", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(4000)->addText(htmlspecialchars("Ouders / verzorgers", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(1000)->addText(htmlspecialchars("Groep", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(2500)->addText(htmlspecialchars("Kind", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(2200)->addText(htmlspecialchars("Strippenkaart", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(1400)->addText(htmlspecialchars("Betaald", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(1400)->addText(htmlspecialchars("Datum", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		
		$totalPrice = array();
		foreach($submissionsCurrentWeek as $resultCurrentWeek){
			$table->addRow();
			$table->addCell(2200)->addText(htmlspecialchars($resultCurrentWeek->name_school, ENT_COMPAT, 'UTF-8'));
			$table->addCell(4000)->addText(htmlspecialchars($resultCurrentWeek->first_name_mother . ' ' . $resultCurrentWeek->last_name_mother . ' ' .  $resultCurrentWeek->first_name_father . ' ' . $resultCurrentWeek->last_name_father, ENT_COMPAT, 'UTF-8'));
			$table->addCell(1000)->addText(htmlspecialchars($resultCurrentWeek->groep, ENT_COMPAT, 'UTF-8'));
			$table->addCell(2500)->addText(htmlspecialchars($resultCurrentWeek->first_name . ' ' . $resultCurrentWeek->last_name, ENT_COMPAT, 'UTF-8'));
			$table->addCell(2200)->addText(htmlspecialchars(html_entity_decode($resultCurrentWeek->card), ENT_COMPAT, 'UTF-8'));
			if($resultCurrentWeek->payment_status==1) {
				$table->addCell(1400)->addText('Ja', ENT_COMPAT, 'UTF-8');	
			}else{
				$table->addCell(1400)->addText('Nee', ENT_COMPAT, 'UTF-8');	
			}
			$table->addCell(1400)->addText(htmlspecialchars(date('d-m H:i', strtotime($resultCurrentWeek->created_at . "+2 hours")), ENT_COMPAT, 'UTF-8'));
			$totalPrice[] = $resultCurrentWeek->price;
		}
		$section->addTextRun();
		$section->addText(htmlspecialchars('Totaal: € ' . (array_sum($totalPrice) / 100), ENT_COMPAT, 'UTF-8'),   array(
	      'size' => 14,
	      'bold' => true,
	    ));
		
		$section->addTextRun();
		$section->addTextRun();
		
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(2500)->addText(htmlspecialchars("Aantal", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		$table->addCell(2500)->addText(htmlspecialchars("Strippenkaart", ENT_COMPAT, 'UTF-8'),  array(
	      'size' => 13,
	      'bold' => true,
	    ));
		foreach($cards as $card){
			$table->addRow();
			$table->addCell(2500)->addText(htmlspecialchars($card->CountCard, ENT_COMPAT, 'UTF-8'));
			$table->addCell(2500)->addText(htmlspecialchars(html_entity_decode($card->card), ENT_COMPAT, 'UTF-8'));
		}
				
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->_phpWord, 'Word2007');
		$objWriter->save($filename);
	}
	
}
