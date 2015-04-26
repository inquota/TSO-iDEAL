<?php

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
	
	public function SendMail($subject, $to, $message, $bcc=null){

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'From: TSO <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
		
		if($bcc!=null){
			$headers .= 'Bcc: '.$bcc . "\r\n";
		}
		
		// Mail it
		mail($to, $subject, $message, $headers);
	}
}
