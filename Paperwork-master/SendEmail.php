<?php
use PHPMailer\PHPMailer\PHPMailer;


require_once 'vendor/autoload.php';

function sendEmail($toEmail, $prevRecipientEmail, $action){

    $mail = new PHPMailer();
    $mail->isSendmail();
    $mail->Port = 25;

    $capEmail = 'noreply@pcr-wa-015.org'; 
    
    $mail->setFrom($capEmail, 'no reply');
    $mail->addAddress($toEmail, '');
    $mail->isHTML(true);

    $msg = "";
    if(strcmp($action, "approve") == 0){
        $msg = "Form is awaiting your approval on the CAP Paperwork site at https://pcr-wa-015.org/paper/home.php";
    }
    else if(strcmp($action, "disapprove") == 0){
        $msg = $prevRecipientEmail . " has disapproved your form! https://pcr-wa-015.org/paper/home.php";
    }
    else if(strcmp($action, "edit") == 0){
        $msg = $prevRecipientEmail . " has edited your form! https://pcr-wa-015.org/paper/home.php";
    }
    else if(strcmp($action, "completed") == 0){
        $msg = "Your form has been approved by everyone! https://pcr-wa-015.org/paper/home.php";
    }
    else{
        $msg = "For CAP Paperwork go to https://pcr-wa-015.org/paper/home.php";
    }
    
    $mail->Subject = 'CAP Paperwork';
    $mail->Body = $msg;

    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
    } else {
        echo 'Message sent!';
        return true;
    }

}
?>

