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

$id = $_SESSION['login_id'];

$stmt = $db->prepare("SELECT * FROM `users` WHERE `googleID`=:id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$get_user = $stmt;
$stmt = $db->prepare("SELECT FOUND_ROWS()");
$stmt->execute();
$get_user = $stmt;

$rowCount =$get_user->fetchColumn();
if($rowCount > 0){

    $stmt = $db->prepare("SELECT * FROM `users` WHERE `googleID`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $users = $stmt;
}
else{
    header('Location: logout.php');
    exit;
}
?>
<!DOCTYPE html>
<!-- Designined by CodingLab | www.youtube.com/codinglabyt -->
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<!--<title> Responsiive Admin Dashboard | CodingLab </title>-->
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
<script> document.getElementById("chooseForm").className = "active"; </script>

<div class="home-content">
<div class="overview-boxes">
<div class="box">
    <div class="right-side">
    <div class="box-topic">
        <form action="EditForm.php" method="post">
        <input type="hidden" name="formName" value="6080"> 
        <!--new form-->
        <input type="hidden" name="formState" value="newForm"> 
        <input id="newFormButton" type="submit" value="6080">
        </form>
    </div>
    </div>
</div>
<div class="box">
    <div class="right-side">
    <div class="box-topic">
        <form action="EditForm.php" method="post">
        <input type="hidden" name="formName" value="opsPlan">
        <!--new form-->
        <input type="hidden" name="formState" value="newForm"> 
        <input id="newFormButton" type="submit" value="opsPlan">
        </form>
    </div>
    </div>
</div>
<div class="box">
    <div class="right-side">
    <div class="box-topic">
        <form action="EditForm.php" method="post">
        <input type="hidden" name="formName" value="160">
        <!--new form-->
        <input type="hidden" name="formState"  value="newForm"> 
        <input id="newFormButton" type="submit" value="160">
        </form>
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

</body>
</html>


