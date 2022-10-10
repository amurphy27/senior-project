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
    $formID= $parts[2];
    $emailString= explode('&', $parts[1])[0];


    $stmt = $db->prepare('SELECT * FROM email WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    //Process and validate the given emails before inserting into the DB.
    $emailArray = preg_split('/ +/', $emailString, null, PREG_SPLIT_NO_EMPTY);

    //Email chain already exists, delete the old one and add the new. Leave the author's email as sendOrder 0. 
    if ($stmt->rowCount() > 0)
    {
        $stmt = $db->prepare('DELETE FROM email WHERE formID=:formID AND sendOrder <> 0');
        $stmt->bindParam(':formID', $formID);
        $stmt->execute();
    }
    //Email chain for formID doesn't already exist in the DB so INSERT.
    $i = 1;
    foreach($emailArray as $email){
        $stmt = $db->prepare('INSERT INTO email (formID, email, hasBeenSentTo, sendOrder, hasApproved) VALUES (:formID, :emailString, 0, :i, 0)');
        $stmt->bindParam(':formID', $formID);
        $stmt->bindParam(':emailString', $email);
        $stmt->bindParam(':i', $i);
        $stmt->execute();

        $i++;
    }
        
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$db = null;
?>
