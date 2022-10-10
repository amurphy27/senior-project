<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
$dotenv->load();
$servername = getenv('SERVERNAME');
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

try {
    
    $db = new PDO("mysql:host=$servername;dbname=paperwork", $username, $password);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $post = file_get_contents('php://input');
    $parts = explode('=', $post);
    $email= $parts[2];
    $formID= explode('&', $parts[1])[0];


    //delete the form from the emails table where the email equals to the user's email
    $stmt = $db->prepare("DELETE FROM email where formID=:formID AND email =:email ");
    $stmt->bindParam(':formID', $formID);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    //check if there is a form in the emails table with that formID
    $stmt = $db->prepare("SELECT * FROM email where formID=:formID");
    $stmt->bindParam(':formID', $formID);
    //$stmt->bindParam(':email', $email);
    $stmt->execute();
    $rows= $stmt->fetchAll();

    $count = count($rows);

    if($count<1)   {
    
    $stmt = $db->prepare("DELETE FROM forms where formID=:formID");
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();


    $stmt = $db->prepare("DELETE FROM fields where formID=:formID");
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();

    }

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
