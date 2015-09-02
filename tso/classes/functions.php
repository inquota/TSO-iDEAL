<?php

require_once 'lib/swiftmailer-5.4.0/lib/swift_required.php';

class Functions {
	
	public function randomPassword() {
	    $alphabet = "abcdefghijkmnopqrstuwxyzABCDEFGHIJKLMNPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	
	public function RandomHash() {
	    $alphabet = "abcdefghijkmnopqrstuwxyz";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i <= 32; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	
	/**
	 * Send an E-mail with SwiftMailer
	 *
	 * @param string $subject
	 * @param string $to
	 * @param string $_message
	 * 
	 * @return boolean true|false
	 */
	public function SendMail($subject, $to, $_message)
	{
		global $wpdb;
		
		$table_settings = $wpdb->prefix . 'wv_reservations_settings';
		$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );
		
		if($to==null || $to == false){
			return false;
		}
		
		$emails=explode(',',$to);
		
		//$username = 'info@websitevorm.nl';
		//$password = 'wLap_h9jdIKooEQ3v33Xug';
		
		// Create the Transport
		/*$transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 25)
		  ->setUsername($username) 
		  ->setPassword($password)
		  ;*/
		// Create the Transport
		$transport = Swift_MailTransport::newInstance();
				
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		
		// Create a message
		$message = Swift_Message::newInstance($subject)
		  ->setFrom(array('admin@'.$_SERVER['HTTP_HOST'] => $settings->email_sender_name))
		  ->setBody($_message)
		  ->addPart($_message, 'text/html')
		  ;
		  
		// if we have multiple receivers use bcc  
		if(count($emails) > 1 && is_array($emails)){
			$message->setBcc($emails);
		}else{
			$message->addTo($to);
		}  
		
		// Send the message
		if($mailer->send($message)){
			return true;	
		}else{
			return false;
		}
		
					
		// To send HTML mail, the Content-type header must be set
		/*$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$settings->email_sender_name.' <admin@'.$_SERVER['HTTP_HOST'] .'>' . "\r\n";
		
		
		// Mail it
		if(mail($to, $subject, $_message, $headers))
		{
			return true;
		}else{
			return false;
		}*/
	}
}
