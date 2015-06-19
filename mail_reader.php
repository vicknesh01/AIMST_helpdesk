<html>
<head><title>Processing..</title>
<meta http-equiv="refresh" content="30" />
<script src="sorttable.js"></script>
<SCRIPT TYPE="text/javascript">
<!--
function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=600,height=600,scrollbars=yes');
return false;
}
//-->
</SCRIPT>

</head>
<body>

<?php
header('Content-type: text/html');



################  pull server information from SERVER_DETAILS.cfg
$myfile = fopen("SERVER_DETAILS.cfg", "r") or die("Unable To Obtain Server Information, Please check SERVER_DETAILS.cfg!");$server_url = preg_replace('/\s+/', '', fgets($myfile));
$server_login = preg_replace('/\s+/', '', fgets($myfile));
$server_pass = preg_replace('/\s+/', '', fgets($myfile));
$server_dbname = preg_replace('/\s+/', '', fgets($myfile));
fclose($myfile);


#################  pull email info from SQL Server
     //Create Connection
$conn = new mysqli($server_url,$server_login,$server_pass,$server_dbname);
     // Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

	//pull data
	$sql = "SELECT * FROM `email_info`";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
       
	   $email_username = $row["username"];
	   $email_pass = $row["pass"];
	   $email_imap_addr = $row["server_imap_add"];
    }


################  get all email details

function mail_list($email_imap_addr,$email_pass,$email_username) {
	
	$imap = imap_open("{".$email_imap_addr.":993/imap/ssl}INBOX", $email_username, $email_pass);
	$headers = imap_headers($imap);
	
	$lala1 =  explode("\n", imap_fetchheader($imap, 3));
	$unread_counter = 0;
	$message_counter = 0;
	$message_list_counter = 1;
	?> <table border="1" align="center" class="sortable">
    
    <tr><th>Num.</th><th>Date & Time Recieved</th><th>From Address</th><th width="400">Subject</th><th>Ticket Status</th></tr>
    
    
    
     <?php
	
	
	foreach ($headers as $mail) {
				
				
                $flags = substr($mail, 0, 4);
				
				$message_counter++;
                $isunr = (strpos($flags, "U ") !== false);
                if ($isunr) {
                $unread_counter++;
				$hText = imap_fetchheader($imap,$message_counter); 
				$header_info = imap_rfc822_parse_headers ($hText);
				
				?>
				
                
               
<tr><td><strong> <?php print $message_list_counter; $message_list_counter++; ?></strong></td><td><strong> <?php print date("d/n/Y, g.ia", strtotime($header_info->date)); ?></strong></td><td><strong> <?php print $header_info->from[0]->mailbox . "@" . $header_info->from[0]->host; ?></strong></td><td><strong> <a href="read_mail.php?id=<?php echo $message_counter; ?>" onClick="return popup(this, 'Read Message')" style="text-decoration : none; color : #000000;"><?php print $header_info->subject; ?></a></strong></td><td><strong> <input type="button" name="Create Ticket" id="Create Ticket" value="Create Ticket"></strong></td></tr>


<?php
			
				} else {
					
					//if message has been read already
					$hText = imap_fetchheader($imap,$message_counter); 
				    $header_info = imap_rfc822_parse_headers ($hText);
					
									?>
				
                
                
<tr><td><?php print $message_list_counter; $message_list_counter++; ?></td><td><?php print date("d/n/Y, g.ia", strtotime($header_info->date)); ?></td><td><?php print $header_info->from[0]->mailbox . "@" . $header_info->from[0]->host; ?></td><td><a href="read_mail.php?id=<?php echo $message_counter; ?>" onClick="return popup(this, 'Read Message')" style="text-decoration : none; color : #000000;"><?php print $header_info->subject; ?></a></td><td><input type="button" name="Create Ticket" id="Create Ticket" value="Create Ticket"></td></tr>


<?php
				}
				
				
           }
		   
	?> </table> <?php

	imap_close($imap);
	
}
################  get all email details  ---- END OF FUNCTION






//all functions
mail_list($email_imap_addr,$email_pass,$email_username);
?>

</body>
</html>