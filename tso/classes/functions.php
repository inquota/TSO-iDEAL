<?php

require_once 'lib/swiftmailer-5.4.0/lib/swift_required.php';
require_once 'lib/php-export-data/php-export-data.class.php';

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
	public function SendMail($subject, $to, $_message, $attachment = null)
	{
		/*global $wpdb;
		
		$table_settings = $wpdb->prefix . 'wv_reservations_settings';
		$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );*/
		$this->SendEmailSWIFT($subject, $to, $_message, $attachment);
	}
	
	private function SendEmailPHP($subject, $to, $_message)
	{
		if($to==null || $to == false){
			return false;
		}
		
		$blog_title = get_bloginfo(); 
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$blog_title.' <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
		
		$emails=explode(',',$to);
		
		if(count($emails) > 1 && is_array($emails)){
			$headers .= 'BCC: '. $to . "\r\n";
			
			// Mail it
			if(mail(null, $subject, $_message, $headers))
			{
				$this->MailLog('multiple', $subject, $_message);
				return true;
			}else{
				return false;
			}
				
		}else{
			// Mail it
			if(mail($to, $subject, $_message, $headers))
			{
				$this->MailLog($to, $subject, $_message);
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function SendEmailSWIFT($subject, $to, $_message, $attachment = null)
	{
		$blog_title = get_bloginfo();
		
		// Create the Transport
		/*$transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 25)
		  ->setUsername('info@websitevorm.nl')
		  ->setPassword('GXnECZWS5BYZ2yn_CwvrJA')
		  ;*/
		$transport = Swift_MailTransport::newInstance();				
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		
		// Create a message
		$message = Swift_Message::newInstance($subject)
		  ->setFrom(array('noreply@'.$_SERVER['HTTP_HOST'] => $blog_title))
		  ->setBody($_message)
		  ->addPart($_message, 'text/html')
		  ;
		
		$emails=explode(',',$to);
		  
		// if we have multiple receivers use bcc  
		if(count($emails) > 1 && is_array($emails)){
			$message->setBcc($emails);
		}else{
			$message->addTo($to);
		}  
		
		if($attachment != null) {
			// Optionally add any attachments
  			$message->attach(Swift_Attachment::fromPath($attachment));
		}
		
		// Send the message
		$mailer->send($message);
		
		$this->MailLog($to, $subject, $_message);
		
		return true;
	}
	/**
	 * Log mail.
	 * 
	 * @author Jarah de Jong
	 * @param string $receiver
	 * @param string $subject
	 * @param string $body
	 * @return bool
	 */
	public function MailLog($receiver, $subject, $body)
	{
		global $wpdb;
		
		$table = $wpdb->prefix . 'tso_maillog';
		
		if($wpdb->insert($table, array(
				   "receiver" => $receiver,
				   "subject" => $subject,
				   "body" => $body,
				   "created_at" => date('Y-m-d H:i:s'),
				)))
		{
			return true;
		}else{
			return false;
		}
	}
}
