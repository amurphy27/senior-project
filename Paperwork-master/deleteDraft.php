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
    $formID= $parts[1];


    //delete the draft from database when the delete button is clicked
    $stmt = $db->prepare("DELETE FROM forms where formID=:formID");
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();


} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
