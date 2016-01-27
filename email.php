<?php
/*

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

http://www.gnu.org/licenses/ 

@Project YAES - Yet Another Email Script
@Author Gui Yazbek
@Version 1.0

*/

// Uncomment the following if you wish to debug the script by viewing all php errors
// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);

// Change these variables to suit your needs
// You can override the from email by specifying it here
$to      = "youremail@example.com";
$from 	 = null;
$subject = "Message Subject";


if($_SERVER["REQUEST_METHOD"] =="POST"){

	// Initialize error array
	$errors = array();
	if ($from == null && empty($_POST["from"])) {
      $errors[] = 'Email is required';
   	}else{
   		// Filter to remove any illegal characters from email
   		$from = filter_var($_POST["from"],FILTER_SANITIZE_EMAIL);
   	}
   	if (empty($_POST["message"])) {
      $errors[] = 'Message is required';
   	}else{
   		// We strip out any HTML tags, you may remove it if you wish to preserve any HTML that is submitted
   		$message = strip_tags($_POST["message"]);
   	}

   	// If you wish to do any further processing to the variables now is a good time

   	// If there are errors we return them along with the appropriate header response, or else we send the message
   	if (!empty($errors)) {
   		header('HTTP/1.1 400 Bad Request');
   		header('Content-Type: application/json');
   		echo json_encode(array("errors" => $errors));
	}else{
		$headers  = 'From: ' . $from . "\r\n";
		$headers .= 'Reply-To: ' . $from . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";

		// If the message sending fails return the appropriate headers
		if(@mail($to, $subject, $message, $headers)){
			header('HTTP/1.1 201 Created');
		}else{
			header('HTTP/1.0 500 Internal Server Error');
			echo json_encode(array("errors" => array('Unable to send email at this time, please try again later')));
		}

	}
// We only allow POST, so anything else is ignored
}else{
	header('HTTP/1.1 405 Method Not Allowed');
	header('Allow: POST');
	echo "{\"Method Not Allowed\"}";
}
?>