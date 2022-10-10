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

$userID = $_SESSION['login_id'];

$stmt = $db->prepare("SELECT * FROM `users` WHERE `googleID`=:userID");
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$get_user = $stmt;
$stmt = $db->prepare("SELECT FOUND_ROWS()");
$stmt->execute();
$get_user = $stmt;

$rowCount =$get_user->fetchColumn();
if($rowCount > 0){

    $stmt = $db->prepare("SELECT * FROM `users` WHERE `googleID`=:userID");
    $stmt->bindParam(':userID', $userID);
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

        $stmt = $db->prepare('SELECT * FROM email WHERE email=:userEmail AND hasBeenSentTo=1 AND hasApproved=0');
        $stmt->bindParam(':userEmail', $email);
        $stmt->execute();
        $formsForReviewByUser = $stmt->fetchAll();
        
        $formsToReview = [];
        foreach($formsForReviewByUser as $emailDetails){
            $stmt = $db->prepare('SELECT * FROM forms, users WHERE formID=:formID AND forms.authorID = users.googleID ');
            $stmt->bindParam(':formID', $emailDetails['formID']);
            $stmt->execute();
            $form = $stmt->fetchAll();

            $formsToReview[] = $form;
        }



        echo $fname; 
        
        ?>
    </title>
</head>
<body>

<?php include "Navigation.php"; ?>
<script> document.getElementById("home").className = "active"; </script>

<div class="home-content">

<div class="sales-boxes">
<div class="recent-sales box">  <!--main drop down menu-->
    <div class="title">
    <span class="menu"> Pending Approval Forms </span>
    <!-- <i class='bx bx-chevron-down' ></i> -->
    </div>
    <div class="sales-details">
    <ul class="details">
        <li class="topic">Date Created</li>
        <?php foreach($formsToReview as $form) {
            echo '<li>'. $form[0]["dateCreated"] . '</li>';
        }

            ?>
    
    </ul>

    <ul class="details">
    
    <li class="topic">From</li>
    <?php foreach($formsToReview as $form) {
        echo '<li>'. $form[0]["email"] . '</li>';
    } ?>

    </ul>
    <ul class="details">
    
    <li class="topic">Form Title</li>
    <?php 
    foreach($formsToReview as $form) {
    echo'
            <form  action="EditForm.php" method="post">
            <input type="hidden" name="formID" value="'. $form[0]["formID"] .'">
            <input type="hidden" name="formName" value="'. $form[0]["formType"] .'">
            <input type="hidden" name="formState" value="'. $form[0]["formState"] .'">
            <input type="hidden" name="isCurrentReviewer" value="true"> 
            <li> <input id="editFormButton" type="submit" value="'. $form[0]["formTitle"] .'"></li>
            </form>';
    }
    ?>

    </ul>
    <ul class="details">
    <li class="topic">Form Type</li>
    <?php foreach($formsToReview as $form) {
        echo '<li>'. $form[0]["formType"] . '</li>';
    } ?>
    </ul>
    </div>
    
</div>
<?php 
    $stmt = $db->prepare("SELECT * FROM forms WHERE authorID = :userID AND formState = 'pending'");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $sentForms= $stmt->fetchAll();
?>

<div class="top-sales box">
    <div class="title">Sent Forms</div>
    <ul class="top-sales-details">
    
    <?php 
    foreach($sentForms as $sentForm){
        echo 
        '<form  action="EditForm.php" method="post">
        <input type="hidden" name="formID" value="'. $sentForm["formID"] .'">
        <input type="hidden" name="formName" value="'. $sentForm["formType"] .'">
        <input type="hidden" name="formState" value="'. $sentForm["formState"] .'">
        <input type="hidden" name="isCurrentReviewer" value="false"> 
        <li><span class="form"><input id="editFormButton" type="submit" value="'. $sentForm["formTitle"] . '"></span>
        <span class="date">'. $sentForm["dateCreated"] . '</span></li> </form>';
    }
    ?>


    </ul>
</div>
<?php 
    //display all the completed forms that have been approved by everyone 
    $stmt = $db->prepare("SELECT * FROM email NATURAL JOIN forms WHERE email=:userEmail AND
    hasBeenSentTo=1 AND hasApproved=1 AND formState='completed'"); 
    $stmt->bindParam(':userEmail', $email);
    $stmt->execute();
    $approvedForms = $stmt->fetchAll();
?>


<div class="recent-sales box">  
    <div class="title">
    <span class="menu"> Completed Forms </span>
    </div>
    <div class="sales-details">
    <ul class="details">
        <li class="topic">Date Created</li>
        <?php foreach($approvedForms as $approvedForm) {
            echo '<li>'. $approvedForm["dateCreated"] . '</li>';
        }

            ?>
    
    </ul>

    </ul>
    
    <ul class="details">
    <li class="topic">Form Type</li>
    <?php foreach($approvedForms as $approvedForm) {
        echo '<li>'. $approvedForm["formType"] . '</li>';
    } ?>
    </ul>

    <ul class="details">
    
    <li class="topic">Form Title </li>
    <?php 
    foreach($approvedForms as $approvedForm) {
        echo'
            <form  action="EditForm.php" method="post">
            <input type="hidden" name="formID" value="'. $approvedForm["formID"] .'">
            <input type="hidden" name="formName" value="'. $approvedForm["formType"] .'">
            <input type="hidden" name="formState" value="'. $approvedForm["formState"] .'">
            <input type="hidden" name="isCurrentReviewer" value="false"> 
            <input type="hidden" name="approvedForm" value="true"> 
            <li><input id="editFormButton" type="submit" value="'.$approvedForm["formTitle"] . '"></li>
            </form>';
    }
    ?>

    </ul>

    <ul class="details">
    <li class="topic">Save</li>
    <?php foreach($approvedForms as $approvedForm) {
        echo'
        <form  action="savePDF.php" method="post">
        <input type="hidden" name="formID" value="'. $approvedForm["formID"] .'">
        <input type="hidden" name="formName" value="'.$approvedForm["formType"] .'">
        <li> <input id="saveButton" type="submit" value="Save as PDF"></li>
        </form>';
    } ?>
    </ul>

    <ul class="details">
    <li class="topic"> Delete</li>
    <?php 

    foreach($approvedForms as $approvedForm) { 
    echo'
    <li> <input id="deleteButton" onClick="return confirmDelete('.$approvedForm['formID'].', \''.$email.'\')" type="submit" name="deleteButton" value="Delete "></li>';
    }
?>
</ul>
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
<script src="js/deleteForm.js"></script>



</body>
</html>
