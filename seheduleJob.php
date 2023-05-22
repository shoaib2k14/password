<!--
<button class="btn btn-primary float-right veiwbutton ml-3" onclick="seheduleJob();">Schedule Job</button>

<script>
function seheduleJob(){
	
	$('#loaderImg').show();
	$.ajax({
		 url:'../seheduleJob.php',
		 type:'POST',
		 success:function(result){
			$('#loaderImg').hide();
			var response = JSON.parse(result);
			
			if (response.code == 200) {
				Swal.fire({
						text: response.text,
						icon: response.msg,
						showCancelButton: true,
						showConfirmButton: false,
						cancelButtonColor: '#d33',
						cancelButtonText: 'Close'
					});
			}
		 }
	 });	
}
</script>
-->



<?php
include('dbConnection.php');

require('phpmailer/PHPMailerAutoload.php');

$flag = 0;

$sql_mail = "SELECT * FROM mails WHERE status = 1";
 $result_mail = $conn->query($sql_mail);
 $row_mail = mysqli_fetch_assoc($result_mail);
 $mailHost = $row_mail['host'];
 $mailPort = $row_mail['port'];
 $mailUser = $row_mail['user_name'];
 $mailPassword = $row_mail['password'];
 $mailSender = $row_mail['sender'];


$Sql = "SELECT users.unix_id, users.f_name, users.l_name, users.email, server_users.user, server_users.expiry_at, servers.host_name FROM users, servers, server_users Where server_users.user_id = users.id AND server_users.server_id = servers.id AND server_users.expiry_at != ''";
  $Result = $conn->query($Sql);
 
  if($Result->num_rows == 0){
	$myObj = new stdClass();
	$myObj->code = 200;
	$myObj->text = "Records is empty";
	$myObj->msg = "success";
	$myJSON = json_encode($myObj);
	echo $myJSON;
	die();
  }
  
  while($Row = $Result->fetch_assoc()){
	  $diff = (strtotime($Row['expiry_at'])-strtotime(date("Y-m-d")))/(24*60*60);
	  $f_name = $Row['f_name'];
	  $l_name = $Row['l_name'];
	  $email = $Row['email'];
	  $user = $Row['user'];
	  $server = $Row['host_name'];
	  $name = $f_name.' '.$l_name;
	  
	  if($diff <= 2){
		  $flag = 1;
		  if($diff == 0){$days = "today";}
		  if($diff == 1){$days = "on next day";}
		  if($diff == 2){$days = "in $diff days";}
		  $message = "Your user $user password of server $server is going to expire $days. Please reset your password.";
		  sendNotification($message, $name, $email, $mailHost, $mailPort, $mailUser, $mailPassword, $mailSender);
	  }	  
  }
  
  if($flag == 0){
	$myObj = new stdClass();
	$myObj->code = 200;
	$myObj->text = "No Records found whose password is going expiry at least 2 days";
	$myObj->msg = "success";
	$myJSON = json_encode($myObj);
	echo $myJSON;
	die();
  }
  
 

function sendNotification($message, $name, $email, $mailHost, $mailPort, $mailUser, $mailPassword, $mailSender){
	
	$htmlbody = '
			  <html lang="en">
				 <head>
				   <title>Password-Engine</title>
				</head>
                <body style="margin:0; padding:0; background:#f4f4f4">
				  <div class="content" id="wrapper">
					<div class="nw_layout_LAYOUT1">
					  <table align="center" border="0" cellpadding="0" cellspacing="0" class="CoverPage" id="CoverPage" style="" width="600">
						<tr>
						  <td id="header" valign="top" width="600" style="width: 100%;
							padding: 0px 0px 8px 0px;">
							<table id="nw_masthead_wrapper" class="nw_component_wrapper" cellpadding="0" cellspacing="0">
							   <tr>
								<td class="nw-componentSpacerMainCell">
									<table cellpadding="0" cellspacing="0" class="ContentBlock" id="masthead" style="margin-top:0px;margin-bottom:0px;margin-left:0px;margin-right:0px;" width="100%">
									  <tr>
										<td class="nw-componentMainCell">
										   <img alt="" src="vois.jpg" width="600" height="301" /></a>
										</td>
									  </tr>
									</table>
								</td>
							  </tr>
							</table>
						  </td> 
						</tr>
						
						<tr>
						  <td id="main" valign="top" width="600" style="width: 100%; padding-left: 0px; padding-right: 0px; background-color: #ffffff;">
							<table id="nw_maintitle_wrapper" class="nw_component_wrapper" cellpadding="0" cellspacing="0" width="100%">
							  <tr>
								<td class="nw-componentSpacerMainCell">
								   <table cellpadding="0" cellspacing="0" class="ContentBlock" id="maintitle" style="margin-top:0px;margin-bottom:0px;margin-left:0px;margin-right:0px;" width="100%">
									  <tr>
										 <td class="nw-componentMainCell">
											<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
											   <tbody>
												 <tr>
													<td class="main_title" style="font-family: sans-serif; font-size: 16px; color: #FFFFFF; font-weight:300; font-style: normal; text-decoration: none; background-color: #000000; padding: 8px; border: 1px solid #FFFFFF;">Password Engine<br/></td>
												 </tr>
												</tbody>
											</table>
										  </td>
									  </tr>
									</table>
								</td>
							  </tr>
							</table>

							<table id="nw_maincontent_wrapper" class="nw_component_wrapper" cellpadding="0" cellspacing="0" width="100%">
								<tr>
								  <td class="nw-componentSpacerMainCell">
									 <table cellpadding="0" cellspacing="0" class="ContentBlock" id="maincontent" style="margin-top:0px;margin-bottom:0px;margin-left:0px;margin-right:0px;" width="100%">
										<tr>
										  <td class="nw-componentMainCell">
											 <table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
												<tbody>
												   <tr>
													  <td class="main_content" style="font-size: 16px; color: #000000; font-weight: normal; font-style: normal; text-decoration: none; padding: 10px;">
														<p style="margin: 1.3rem 0rem; margin-top:12px; font-size: 9pt; font-family: Calibri, sans-serif;">
														  <span style="font-family: sans-serif; color: black;">Hello '.$name.'</span>
														</p>
														
														<p style="margin: 1.3rem 0rem; font-size: 9pt; font-family: Calibri, sans-serif;">
															<span style="font-family: sans-serif; color: black;">'.$message.'</span>
														</p>
														
														<p style="margin: 1.3rem 0rem; font-size: 9pt; font-family: Calibri, sans-serif;">
															<span style="font-family: sans-serif; color: black;">
															  Login Credentials: <br/>
															  User Name : '.$email.' <br/>
															  Password : **Windows AD account Password** <br/>
															  URL to access portal : <a href="http://139.47.169.69/password-engine/">www.password-engine-vodafone.com</a>
															</span>
														</p>
														
														<p style="margin: 1.3rem 0rem; font-size: 9pt; font-family: Calibri, sans-serif;">
															<span style="font-family: sans-serif; color: black;">In case of any issue, please reach out to <a href="#" style="color: #000000c7">pe@vodafone.com</a>
															</span>
														</p>
														
														<p style="margin-top: 50px; font-size: 11pt; font-family: Calibri, sans-serif;">
															<strong>Thank you!</strong>
														</p>
														
														<p style="margin-top: 30px; font-size: 11pt; font-family: Calibri, sans-serif;">
															<strong>Kind Regards<br/>ACC</strong>
														</p>
													  </td>
													</tr>
												</tbody>
											 </table>
										   </td>
										</tr>
									 </table>	
								   </td>
								</tr>
							</table>				 
					</body>
				</html> 
			 ';
			 
	$subject = "Gentle notification of your user's password expire.";
	
	sendMail($mailHost, $mailPort, $mailUser, $mailPassword, $mailSender, $email, $subject, $htmlbody);
}


function sendMail($host, $port, $userName, $password, $sender, $email, $subject, $htmlbody){
	
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Host = $host;
	$mail->Port = (int)$port;
	
	if(isset($userName) && isset($password)){
	
		$mail->SMTPSecure = 'tls';
	    $mail->SMTPAuth = true;
	    $mail->Username = $userName;
	    $mail->Password = $password;
	}
	
	$mail->setFrom($sender);
	$mail->addAddress($email);
    $mail->Subject = $subject;
	$mail->msgHTML($htmlbody);
	$mail->send();
	
	if(!$mail->send()) {
		$myObj = new stdClass();
		$myObj->code = 200;
		$myObj->text = "Somthing Went Wrong";
		$myObj->msg = "info";
		$myJSON = json_encode($myObj);
		echo $myJSON;
		die();
	} else {
		$myObj = new stdClass();
		$myObj->code = 200;
		$myObj->text = "Send Notification Successfully";
		$myObj->msg = "success";
		$myJSON = json_encode($myObj);
		echo $myJSON;
		die();
	}
	/*
	if (!$mail->send()) {
		echo "Mailer Error: ".$mail->ErrorInfo;
	}*/
}





?>