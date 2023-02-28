<?php

// include Imap.Class
include_once('lib/class.imap.php');
include_once 'database/dbconfig.php';
$email = new Imap();
$connect = $email->connect(
	'{posmab.com:143/notls}INBOX', //host
	'punithkc9@posmab.com', //username
	'230650@Pu' //password
);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$result = $db->query("SELECT * FROM email_info");
$row = $result->fetch_assoc();


if($connect){
	if(isset($_POST['inbox'])){
		// inbox array
		$inbox = $email->getMessages('html');
		echo json_encode($inbox, JSON_PRETTY_PRINT);
	}else if(!empty($_POST['uid']) && !empty($_POST['part']) && !empty($_POST['file']) && !empty($_POST['encoding'])){
		// attachments
		$inbox = $email->getFiles($_POST);
		echo json_encode($inbox, JSON_PRETTY_PRINT);
	}else {
		echo json_encode(array("status" => "error", "message" => "Not connect."), JSON_PRETTY_PRINT);
	}
}else{
	echo json_encode(array("status" => "error", "message" => "Not connect."), JSON_PRETTY_PRINT);
}