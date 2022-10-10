<?php
session_start();
session_regenerate_id(true);

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
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if(!isset($_SESSION['login_id'])){
    header('Location: login.php');
    exit;
}

//CHANGE TO PDO STATEMENT
$userID = $_SESSION['login_id'];
$get_user =$db->query( "SELECT * FROM `users` WHERE `googleID`='$userID'");
$get_user= $db->query("SELECT FOUND_ROWS()"); 
$rowCount =$get_user->fetchColumn();
if($rowCount > 0){
    $users =$db->query( "SELECT * FROM `users` WHERE `googleID`='$userID'");
}
else{
    header('Location: logout.php');
    exit;
}


$stmt = $db->prepare('SELECT * FROM forms WHERE authorID=:userID AND formState="draft"');
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$result = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<title> Draft </title>
<link rel="stylesheet" href="css/style.css">
<link rel="icon" type="image/x-icon" href="images/favicon-32x32.png">
<!-- Boxicons CDN Link -->
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        $userArray = $users->fetchAll();
        foreach($userArray as $user)
        {     
            $fname = $user['firstName'];
            $lname= $user['lastName'];
            $email=$user['email']; 
        }
            echo $fname; 
        
        ?>
    </title>
</head>
<body>

<?php include "Navigation.php" ?>
<script> document.getElementById("displayDrafts").className = "active"; </script>

<div class="home-content">
<div class="sales-boxes">
<div class="recent-sales box">
<div class="title">
    <span class="menu"> Drafts </span>
</div>
<div class="sales-details">
    <ul class="details">
    <li class="topic">Date Created</li>
    <?php foreach($result as $form) {
    echo '<li>'. $form["dateCreated"] . '</li>';
    } ?></ul> 

    <ul class="details">
    <li class="topic">Form Type </li>
    <?php foreach($result as $form) {
    echo '<li>'. $form["formType"] .' </li>';
} ?> </ul>

    <ul class="details">
    <li class="topic">Form Title </li>
    <?php foreach($result as $form) {
    echo'
        <form  action="EditForm.php" method="post">
        <input type="hidden" name="formID" value="'. $form["formID"] .'">
        <input type="hidden" name="formName" value="'. $form["formType"] .'">
        <input type="hidden" name="formState" value="'. $form["formState"] .'"> 
        <li> <input id="editFormButton" type="submit" value="'. $form["formTitle"] .'"></li>
        </form>';
    }
    ?>
</ul>
<ul class="details">
    <li class="topic"> Save</li>
    <?php foreach($result as $form) { 
echo'
    <form  action="savePDF.php" method="post">
    <input type="hidden" name="formID" value="'. $form["formID"] .'">
    <input type="hidden" name="formName" value="'. $form["formType"] .'">
    <li> <input id="saveButton" type="submit" value="Save as PDF"></li>
    </form>';
}
?>
</ul>

<ul class="details">
    <li class="topic"> Delete</li>
    <?php 

    foreach($result as $form) { 
    echo'
    <li> <input id="deleteButton" onClick="return confirmDelete('.$form['formID'].')" type="submit" name="deleteButton" value="Delete "></li>';
    }
?>
</ul>

<?php 
?>

</div>
</div>
</div>
</div>
</section>

<script>
let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
sidebar.classList.toggle("active");
if(sidebar.classList.contains("active")){
sidebarBtn.classList.replace("bx-menu" ,"bx-menu-alt-right");
}else
sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
}


</script>
<script src="js/deleteDraft.js"></script>

</body>
</html>
