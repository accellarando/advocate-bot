<?php
include("env.php");

//Verify payload
$timestamp = $_SERVER['HTTP_X_SLACK_REQUEST_TIMESTAMP'];
$body = http_build_query($_POST);
$verificationString = "v0:".$timestamp.":".$body; 
$verificationHash = hash_hmac("sha256",$verificationString,SECRET);
$providedHash = $_SERVER['HTTP_X_SLACK_SIGNATURE'];
if(!hash_equals("v0=".$verificationHash,$providedHash)){
	die("\nHashes don't match!");
}

$error=false;
//Send confirmation to the user
if($_POST['command'] == "/advocate"){
	$response = "Message sent.";
}
else{
	$response = "Error! Unrecognized command.";
	$error = true;
}
if($error)
	die();

//Send message to the bot channel via cURL
$url = "https://slack.com/api/chat.postMessage";
$request = ['token' => BOT_TOKEN,
	'channel' => 'bot-playground',
	'text' => 'In the channel '.$_POST['channel_name'].", someone sent the message:\n\n".$_POST['text']];
$request = http_build_query($request);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_VERBOSE, 0);
curl_setopt($ch,CURLOPT_POSTFIELDS,$request);
if(!curl_exec($ch)){
	$error = true;
	$response = "Error! Unable to POST command.";
}
echo $response;
