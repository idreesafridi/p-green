<?php 

	session_start();

	if (isset($_POST['sendmail'])) {
		$rec = $_POST['rec'];
		$subject = $_POST['subject'];
		$mesg = $_POST['mesg'];
		//send email
		$head = "From: Greengen Group SRL <u753951497@srv699.main-hosting.eu> \r\n";
		$head .= "MIME-Version: 1.0\r\n";
        $head .= "Content-type: text/html; charset=utf-8";
		$send = mail($rec,$subject,$mesg, $head);
		if ($send) {
			$_SESSION['msg'] = "e-mail di promemoria inviata";
			header('location:'.$_SERVER['HTTP_REFERER']);
		}else{
		    $_SESSION['warning_msg'] = "error in sending";
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	
	}


 ?>