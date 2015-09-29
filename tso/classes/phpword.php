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
	
	public function createWordUserRegistration($userObject, $childObjects, $filename)
	{
		/* Note: any element you append to a document must reside inside of a Section. */
		$section = $this->_phpWord->addSection();
		$header = array('size' => 16, 'bold' => true);
		// 1. Basic table
		$section->addText(htmlspecialchars('Gegevens ouders', ENT_COMPAT, 'UTF-8'), $header);
		$table = $section->addTable();
		
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("E-mail", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->email, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("1ste Ouder / verzorger", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->first_name_mother . ' ' . $userObject->last_name_mother, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("2de Ouder / verzorger", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->first_name_father.' '.$userObject->last_name_father, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->address . ' ' . $userObject->number, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Postcode en woonplaats", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->postalcode . ' ' . $userObject->city, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon bij onbereikbaar", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->phone_unreachable, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Relatie tot kind(eren) ", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->relation_child, ENT_COMPAT, 'UTF-8'));
		
		$section->addText(htmlspecialchars('Gegevens dokter', ENT_COMPAT, 'UTF-8'), $header);
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Naam", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->name_doc, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->phone_doc, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->address_doc, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Woonplaats", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->city_doc, ENT_COMPAT, 'UTF-8'));
		
		$section->addText(htmlspecialchars('Gegevens tandarts', ENT_COMPAT, 'UTF-8'), $header);
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Naam", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->name_dentist, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Telefoon", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->phone_dentist, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Adres", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->address_dentist, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Woonplaats", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->city_dentist, ENT_COMPAT, 'UTF-8'));
		
		$section->addText(htmlspecialchars('Gegevens kinderen', ENT_COMPAT, 'UTF-8'), $header);
		$table = $section->addTable();
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Basisschool", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($schooldObject->name, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(3000)->addText(htmlspecialchars("Dagen opvang", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->days_care, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(5000)->addText(htmlspecialchars("Mijn kind(eren) blijft/blijven niet op vaste dagen over.", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->toelichting1, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(5000)->addText(htmlspecialchars("Mijn kinderen blijven niet op de zelfde dagen over.", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->toelichting2, ENT_COMPAT, 'UTF-8'));
		$table->addRow();
		$table->addCell(5000)->addText(htmlspecialchars("Bijzonderheden kind(eren).", ENT_COMPAT, 'UTF-8'));
		$table->addCell(5000)->addText(htmlspecialchars($userObject->toelichting3, ENT_COMPAT, 'UTF-8'));
		
		foreach($childObjects as $child){
			$table->addRow();
			$table->addCell(5000)->addText(htmlspecialchars("Kind en groep.", ENT_COMPAT, 'UTF-8'));
			$table->addCell(5000)->addText(htmlspecialchars($child->first_name.' ' . $child->last_name. ' (groep: '.$child->groep.')', ENT_COMPAT, 'UTF-8'));
		}
		
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->_phpWord, 'Word2007');
		$objWriter->save($filename);
	}
	
}
