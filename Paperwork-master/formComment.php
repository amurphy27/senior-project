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
    $formComments= explode('&', $parts[1])[0];


    $stmt = $db->prepare("UPDATE forms SET formComments =:formComments WHERE formID=:formID");
    $stmt->bindParam(':formComments', $formComments);
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>