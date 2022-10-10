<?php

include __DIR__ . "/SendEmail.php";

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
$dotenv->load();
$servername = getenv('SERVERNAME');
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

function ValidEmail($address) {
    $pattern = "^[\w!#$%*_\-/?|\^\{\}'~\.]+@(\w+\.)+\w{2,4}^";
    return preg_match($pattern, $address);
}


try {
    $db = new PDO("mysql:host=$servername;dbname=paperwork", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $post = file_get_contents('php://input');
    $parts = explode('=', $post);
    $formID= $parts[1];

    $stmt = $db->prepare('SELECT formState FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formState = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    //Get email chain from DB.
    $stmt = $db->prepare('SELECT * FROM email WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $emailChain = $stmt->fetchAll();


    //Check if form is a draft or pending so user can no longer edit or start an email chain.
    if($formState == "draft" && count($emailChain) > 1){
        $stmt = $db->prepare('UPDATE forms SET formState="pending" WHERE formID=:formID');
        $stmt->bindParam(':formID', $formID);
        $stmt->execute();

        $stmt = $db->prepare('UPDATE email SET hasBeenSentTo=1 WHERE formID=:formID AND sendOrder=1');
        $stmt->bindParam(':formID', $formID);
        $stmt->execute();

        $sendRes = sendEmail($emailChain[1]['email'], "", "approve");
            
    }
        
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$db = null;
?>
