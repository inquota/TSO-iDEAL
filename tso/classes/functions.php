<?php
require_once 'lib/swiftmailer-5.4.0/lib/swift_required.php';

class Functions {
	/**
	 * Generate a random password
	 * 
	 * @return string
	 */
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
	/**
	 * Generate a random hash
	 * 
	 * @return string
	 */
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
	public function SendMail($blog_title, $subject, $to, $_message, $attachment = null)
	{
		$this->SendEmailPHP($blog_title, $subject, $to, $_message, $attachment);
	} 
	
	private function SendEmailPHP($blog_title, $subject, $to, $_message, $attachment = null)
	{
		if($to==null || $to == false){
			return false;
		}
				
		$emails=explode(',',$to);
		
		if($attachment != null) {
			$email_from = 'noreply@'.$_SERVER['HTTP_HOST'];
			$email_txt = $_message;
			$path_parts = pathinfo($attachment);
			$fileatt = $attachment; // Path to the file (example)
			$fileatt_type = "application/zip"; // File Type
			$fileatt_name = $path_parts['basename']; // Filename that will be used for the file as the attachment
			$file = fopen($fileatt,'rb');
			$data = fread($file,filesize($fileatt));
			fclose($file);
			
			$semi_rand = md5(time());
			$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
			$headers="From: ".$blog_title." <{$email_from}>"; // Who the email is from (example)
			$headers .= "\nMIME-Version: 1.0\n" .
			"Content-Type: multipart/mixed;\n" .
			" boundary=\"{$mime_boundary}\"";
			$email_message = "This is a multi-part message in MIME format.\n\n" .
			"--{$mime_boundary}\n" .
			"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
			"Content-Transfer-Encoding: 7bit\n\n" . $email_txt;
			$email_message .= "\n\n";
			$data = chunk_split(base64_encode($data));
			$email_message .= "--{$mime_boundary}\n" .
			"Content-Type: {$fileatt_type};\n" .
			" name=\"{$fileatt_name}\"\n" .
			"Content-Transfer-Encoding: base64\n\n" .
			$data . "\n\n" .
			"--{$mime_boundary}--\n";
			
			$_message = $email_message; 
		}else{
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$blog_title.' <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
		}
		
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
	
	private function SendEmailSWIFT($blog_title, $subject, $to, $_message, $attachment = null)
	{
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
