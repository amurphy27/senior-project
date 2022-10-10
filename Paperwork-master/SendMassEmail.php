<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once 'vendor/autoload.php';
require_once 'MakePDF.php';


function sendMassEmail($toEmail,$formID){

    $dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
    $dotenv->load();
    $servername = getenv('SERVERNAME');
    $username = getenv('USERNAME');
    $password = getenv('PASSWORD');

    try {
        $database = new PDO("mysql:host=$servername;dbname=paperwork", $username, $password);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $mail = new PHPMailer();
    $mail->isSendmail();
    $mail->Port = 25;

    $capEmail = 'noreply@pcr-wa-015.org'; 
    
    $mail->setFrom($capEmail, 'no reply');
    $mail->addAddress($toEmail, '');
    $mail->isHTML(true);


    $mail->Subject = 'CAP Paperwork';
    $mail->Body = 'CAP sent you a form!';

    $pdf=sendPDF($formID); 

    //get the formTitle
    $stmt = $database->prepare('SELECT formTitle FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formTitle = $stmt->fetch()['formTitle'];

    $mail->addStringAttachment($pdf, $formTitle.'.pdf');

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
