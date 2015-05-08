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
		if($to==null || $to == false){
			return false;
		}
		
		$emails=explode(',',$to);

		// To send HTML mail, the Content-type header must be set
		/*$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'From: TSO <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
		
		if($bcc!=null){
			$headers .= 'Bcc: '.$bcc . "\r\n";
		}
		
		// Mail it
		mail($to, $subject, $message, $headers);*/
		
		// Create the Transport
		$transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 25)
		  ->setUsername('info@websitevorm.nl')
		  ->setPassword('GXnECZWS5BYZ2yn_CwvrJA')
		  ;
				
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		
		// Create a message
		$message = Swift_Message::newInstance($subject)
		  ->setFrom(array('admin@'.$_SERVER['HTTP_HOST'] => 'De Lunchclub'))
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
		$mailer->send($message);
		
		return true;
	}
}
