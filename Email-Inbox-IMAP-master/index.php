<?php 
// include Imap.class
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('lib/class.imap.php');
include_once 'database/dbconfig.php';
$email = new Imap();

// $hostname = '{posmab.com:465/ssl}INBOX';  
// $username = 'punithkc9@posmab.com'; 
// $password = '230650@Pu';  

$inbox = null;
$hostname = '{posmab.com:143/notls}INBOX';  // hostname
$username = 'punithkc9@posmab.com';  // username
$password = '230650@Pu';  // password
$imap_connection = imap_open($hostname, $username, $password);
$emailsInbox = imap_search($imap_connection, 'ALL', SE_UID);
$message_numbers = imap_search($imap_connection, 'ALL');
if($email->connect($hostname, $username, $password)){
    $inbox = $email->getMessages('html');
}
foreach($inbox['data'] as $v){
   
    if ((!empty($v['from']['name'] && $v['subject']) && !empty($v['from']['address'])) && !empty($v['date'])) {
    
        $sqlQ = "INSERT INTO email_info (Name,Subject,Email,Recieved_date,created_date) VALUES (?,?,?,?,NOW())";
        $stmt = $db->prepare($sqlQ);
        if (!$stmt) {
            echo "Query failed: " . mysqli_error($mysqli);
        }else{
            $stmt->bind_param("ssss", $v['from']['name'], $v['subject'], $v['from']['address'], $v['date'] );
            $insert = $stmt->execute();
    
            if ($insert) {
                $response['status'] = 1;
                $response['message'] = 'Form data submitted successfully!';
            }
        }
     
    } else {
        $response['message'] = 'Please fill all the mandatory fields (name and email).';
    }
}
if($message_numbers>0){
    foreach ($message_numbers as $message_number) {
        imap_mail_move($imap_connection, $message_number, 'INBOX.Archive');
    }
    imap_expunge($imap_connection);
    imap_close($imap_connection);
}

?>


<!-- Bootstrap -->
<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
<!-- dataTables -->
<link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
<style>
body {
	padding: 20px 10px 20px 10px
}
</style>

<script async defer src="https://buttons.github.io/buttons.js"></script>

<div class="container">
	<div class="row">
		<div class="col-md-12"> 
			<h3 align="center">Email Inbox <a href="mailto:hello@bachors.com">posmab.com</a></h3>
			<hr>
<?php 
if($inbox == null){
    echo '<h4>Not Connect..</h4>';
    exit;
} else {?>
			<table id="myTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>No</th>
						<th>Subject</th>
						<th>Name</th>
						<th>Email</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody id="inbox">

<?php
$html = '';
$no = 1; 
$result = $db->query("SELECT * FROM email_info");

while ($row = $result->fetch_assoc()) {

$html.= '<tr><td>'.$no.'</td>';
$html.= '<td><a href="#"  class="message" data-toggle="modal" data-target="#addModal">'. substr($row['Subject'], 0, 120).'</a></td>';
$html.= '<td>'. (empty($row['Name']) ? '[empty]' : $row['Name']) .'</td>';
$html.= '<td><a href="mailto:'.$row['Email'].'?subject=Re:'.$row['Email'].'">'.$row['Email'] . '</a><td>';
$html.= '<td>'. $row['Recieved_date']. '</td></tr>';
$no++;
}

echo $html;




// Close the mailbox
// $countnum = imap_num_msg($connection);
// $hostnamed = '{posmab.com:143/notls}';  // hostname
// foreach ($emailsInbox as $emailUID) {
//     Move
//     $imapresult=imap_mail_move($connection,'1:'.$hostnamed,'INBOX/Saved');
//     $movingResult = imap_mail_move($connection, $emailUID, $hostnamed, CP_UID);
// }
// imap_close($connection, CL_EXPUNGE);

?>
</tbody>
			</table>
				
		</div>					
	</div>					
</div>

<!-- Modal message -->		
<div id="addModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
     <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Message</h4>
       </div>
       <div class="modal-body" id="message">
         
       </div>
     </div>
   </div>
</div>
<?php }?>
<!-- jQuery -->
<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<!-- Bootstrap -->
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
<!-- dataTables -->
<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<!-- loading-overlay -->
<script src="//cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.6.0/src/loadingoverlay.min.js"></script>
<script>		



