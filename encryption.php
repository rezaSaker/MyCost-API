<?php
/*
	*Encrypt a string
*/

function Encrypt($string, $key)
{
    $method    = "AES-256-CBC";
    $secret_iv = 'secret iv';
    $key       = hash('sha256', $key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	
	$encryptedString = openssl_encrypt($string, $method, $key, 0, $iv);
    $encryptedString = base64_encode($encryptedString);
	
	return $encryptedString;
}

function Decrypt($string, $key)
{
	$method    = "AES-256-CBC";
    $secret_iv = 'secret iv';
    $key       = hash('sha256', $key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	
	$encryptedString = openssl_decrypt(base64_decode($string), $method, $key, 0, $iv);
	
	return $encryptedString;
}


?>