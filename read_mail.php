<!doctype html>
<html>
<head>
<SCRIPT TYPE="text/javascript">
$('#loaded').hide();
$(window).ready(function() {
    $('#loading').hide();
	$('#loaded').show();
});

</SCRIPT>
<meta charset="utf-8">
<title>Read Mail</title>
</head>

<body>


<?php
@$email_id = $_GET['id'];



//get mail info
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

$imap = imap_open("{".$email_imap_addr.":993/imap/ssl}INBOX", $email_username, $email_pass);
$hText = imap_fetchheader($imap,$email_id); 
$header_info = imap_rfc822_parse_headers ($hText);



$email_address = $header_info->from[0]->mailbox . "@" . $header_info->from[0]->host;
$email_subject = $header_info->subject;

$email_body = imap_fetchbody($imap, $email_id, "1.1");

if ($email_body == "") {
    $email_body = imap_fetchbody($imap, $email_id, "1");
}
imap_close($imap);

#################################POPULATE DROPDOWN
     //Create Connection
$conn2 = new mysqli($server_url,$server_login,$server_pass,$server_dbname);
     // Check connection
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}
$sql2 = "SELECT `ticket_status` FROM `ticket_status_types` WHERE 1";
$result2 = $conn->query($sql2);

	
?>

<div id="loading">
Now Loading..
</div>

<div id='loaded'>
<form id="form1" name="form1" method="post">
  <table width="453" border="0">
    <tr>
      <td width="58"><strong>Sender:</strong></td>
      <td width="424"><?php echo $email_address; ?></td>
    </tr>
    <tr>
      <td><strong>Date:</strong></td>
      <td><?php print date("d/n/Y, g.iA", strtotime($header_info->date)) ?></td>
    </tr>
    <tr>
      <td><strong>Subject:</strong></td>
      <td><?php echo $email_subject; ?></td>
    </tr>
    <tr>
    <p>
      <td height="252" align="left" valign="top"><strong>Message / Issue:</strong></td>
      <td valign="top" ><?php echo $email_body; ?></td>
    </tr>
    <tr align="right">
      <td align="left" valign="top"><strong>Reply</strong></td>
      <td align="center">
        <textarea name="textarea" cols="55" rows="5" id="textarea"></textarea>
        
        <p>
          <input type="button" name="button" id="button" value="Reply">
          <input type="button" name="button2" id="button2" value="Clear">
          <input type="button" name="button3" id="button3" value="Close" onclick="self.close()">
        </p>
        <p>
          <label for="select">Ticket Status:</label>
     
<?php
echo "<select>";
    while($row = $result2->fetch_array()){
         echo "<option>";
         echo $row['ticket_status'];
         echo "</option>"; 
    }
    echo "</select>";
          ?>
 
          
          
          
        </p>
    <p>&nbsp; </p></td></tr>
  </table>
  <p>&nbsp;</p>
</form>




</div>
</body>
</html>