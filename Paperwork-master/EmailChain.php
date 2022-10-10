<?php

include __DIR__ . "/SendEmail.php";

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
$dotenv->load();
$servername = getenv('SERVERNAME');
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

function SendToNextRecipient($formID, $prevRecipientEmail, $action, $db) {
    $stmt = $db->prepare('SELECT * FROM email WHERE formID=:formID ORDER BY sendOrder ASC'); 
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $emailChainDB = $stmt->fetchAll();

    $wasSent = 0;
    foreach($emailChainDB as $recipient){
        if($recipient['hasBeenSentTo'] == 0){
            $stmt = $db->prepare('UPDATE email SET hasBeenSentTo = 1 WHERE formID=:formID AND email = :email');
            $stmt->bindParam(':formID', $formID);
            $stmt->bindParam(':email', $recipient['email']);
            $stmt->execute();

            sendEmail($recipient['email'], $prevRecipientEmail, $action);
            $wasSent = 1;
            break;
        }
    }

    if(!$wasSent){
        //Form has been approved by everyone.
        $stmt = $db->prepare('UPDATE forms SET formState = "completed" WHERE formID=:formID');
        $stmt->bindParam(':formID', $formID);
        $stmt->execute();

        //Send email to initiator that form is approved by all.
        sendEmail($emailChainDB[0]['email'], $prevRecipientEmail, "completed");
    }

}

function ValidEmail($address) {
    $pattern = "^[\w!#$%*_\-/?|\^\{\}'~\.]+@(\w+\.)+\w{2,4}^";
    return preg_match($pattern, $address);
}


try {
    $db = new PDO("mysql:host=$servername;dbname=paperwork", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $post = file_get_contents('php://input');
    $parts = explode('=', $post);
    $formID= explode('&', $parts[1])[0];
    $userEmail= explode('&', $parts[2])[0];
    $action= $parts[3];

    if($action == "approve"){
        //Set recipient as approved in DB.
        $stmt = $db->prepare('UPDATE email SET hasApproved = 1 WHERE formID=:formID AND email = :email AND hasBeenSentTo=1');
        $stmt->bindParam(':formID', $formID);
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();

        SendToNextRecipient($formID, $userEmail, "approve", $db);
    }

    else if($action == "disapprove"){
        $stmt = $db->prepare('UPDATE email SET hasApproved = -1, hasBeenSentTo = 0 WHERE formID=:formID AND email = :email AND hasBeenSentTo=1');
        $stmt->bindParam(':formID', $formID);
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();
        
        //Setup so initiator is next recipient.
        $stmt1 = $db->prepare('UPDATE email SET hasBeenSentTo = 0, hasApproved = 0 WHERE formID=:formID AND sendOrder=0');
        $stmt1->bindParam(':formID', $formID);
        $stmt1->execute();

        //send initiator an email about user's disapproval
        SendToNextRecipient($formID, $userEmail, "disapprove", $db);
    }
    else if($action == "edit"){
        $stmt = $db->prepare('UPDATE email SET hasApproved = 0, hasBeenSentTo = 0 WHERE formID=:formID AND email <>:email');
        $stmt->bindParam(':formID', $formID);
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();


    }

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


?>
