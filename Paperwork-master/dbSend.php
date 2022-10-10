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
    $formID= $parts[3];
    $id= explode('&', $parts[1])[0];
    $value= explode('&', $parts[2])[0];


    $stmt = $db->prepare('SELECT * FROM fields WHERE formID=:formID AND FieldID = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $result = $stmt->fetchAll(); //$stmt->setFetchMode(PDO::FETCH_ASSOC);

    //data does already exist so we UPDATE  
    if ($stmt->rowCount() > 0)
    {
        $stmt = $db->prepare('UPDATE fields SET content = :value WHERE formID=:formID AND FieldID = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':formID', $formID);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    }
    else //data doesn't already exist in the DB so INSERT
    {
        $stmt = $db->prepare('INSERT INTO fields (FieldID, FormID, content) VALUES (:id, :formID, :value)');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':formID', $formID);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    }

    $stmt = $db->prepare('UPDATE forms SET lastEdited = :dateNow WHERE formID = :formID');
    $dateNow = date("Y/m/d");
    $stmt->bindParam(':dateNow', $dateNow);
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$db = null;
?>
