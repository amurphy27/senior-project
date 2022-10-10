<?php
include 'Field.php';
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
$dotenv->load();
$servername = getenv('SERVERNAME');
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

session_start();
session_regenerate_id(true);

function divStringCleaner($divString){
    $openTag = "<div>";
    $closeTag = "</div>";
    
    $openTagCount = 0;
    $closeTagCount = 0;
    
    $offset = 0;
    while (($pos = strpos($divString, "<div>", $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $openTagCount++;
    }
    
    $offset = 0;
    while (($pos = strpos($divString, "</div>", $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $closeTagCount++;
    }   
    
    while($openTagCount > $closeTagCount){
        $closeTagCount++;
        $divString = $divString . '</div>';
    }
    while($closeTagCount > $openTagCount){
        $openTagCount++;
        $divString = '<div>' . $divString;
    }

    return $divString;
}


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
?>
<!DOCTYPE html>
<!-- Designined by CodingLab | www.youtube.com/codinglabyt -->
<html lang="en" dir="ltr">
<head>
<title>Form Editor</title>
<script src="js/EditForm.js"></script>
<link href='https://fonts.googleapis.com/css?family=Cedarville Cursive' rel='stylesheet'>
<link rel="icon" type="image/x-icon" href="images/favicon-32x32.png">
<meta charset="UTF-8">
<!--<title> Responsiive Admin Dashboard | CodingLab </title>-->
<link rel="stylesheet" href="css/style.css">
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

<div class="home-content">

<?php
$formType = $_POST['formName'];
$formID = 1;

//get page count
$mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp']); //$mpdf = new \Mpdf\Mpdf();
//$mpdf = new \Mpdf\Mpdf();
$pageCount = $mpdf->setSourceFile('formPDF/'. $formType .'.pdf');

function percentToNum($percent, $max)
{
    return $max * $percent;
}
function numToPercent($num, $max)
{
    return ($num/ $max) * 100;
}


//gets the user's name from the db
$stmt = $db->prepare("SELECT firstName, lastName FROM users WHERE googleID = :userID");
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$userNamesDB = $stmt->fetch();
$userFullname = $userNamesDB['firstName'] . ' ' . $userNamesDB['lastName'];

$stmt = $db->prepare("SELECT * FROM templatefields WHERE formType = :formType ORDER BY FieldID ASC");
$stmt->bindParam(':formType', $formType);
$stmt->execute();
$fieldTemplatesDB = $stmt->fetchAll();

$fields = array();
foreach($fieldTemplatesDB as $fieldTemplateDB)
{
    $fieldDB = new Field;
    $fieldDB->fieldId = $fieldTemplateDB['FieldID'];
    $fieldDB->name = $fieldTemplateDB['name'];
    $fieldDB->x = $fieldTemplateDB['xPos'];
    $fieldDB->y = $fieldTemplateDB['yPos'];
    $fieldDB->width = $fieldTemplateDB['width'];
    $fieldDB->height = $fieldTemplateDB['height'];
    $fieldDB->type = $fieldTemplateDB['type'];
    array_push($fields, $fieldDB);
}

// Check if new form.
if (strcmp($_POST['formState'], "newForm") == 0){ // check if it is a new form
    $stmt = $db->prepare("INSERT INTO forms (formType, authorID, dateCreated, lastEdited, formState, formTitle) 
    VALUES (:formType, :userID, :dateNow, :dateNow, 'draft', 'generic form')");
    $stmt->bindParam(':formType', $formType);
    $stmt->bindParam(':userID', $userID);
    $dateNow = date("Y/m/d");
    $stmt->bindParam(':dateNow', $dateNow);
    $stmt->execute();

    $formID = $db->lastInsertId();

} else {
    $formID = $_POST['formID'];
}

echo '<div id="formTitle"><label> Form Title : </label> <input type="text" id="formTitleInput" ';
if(strcmp($_POST['formState'],"newForm") != 0 && strcmp($_POST['formState'],"draft") != 0 ){
echo 'readonly';
} if(strcmp($_POST['formState'],"draft") ==0 || strcmp($_POST['formState'],"pending") ==0 || strcmp($_POST['formState'],"completed") ==0){
    $stmt = $db->prepare('SELECT formTitle FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formTitle = $stmt->fetch()[0];
    echo ' value="'. $formTitle .'"';
}
echo ' onchange="formTitle(this.value, '.$formID.') "></div><div style="position: relative; margin: auto; width: 80%;">
<img src="images/'. $formType .'.jpg" alt="uh-oh broken" width="100%"><form id="bigForm" method="post">';

if (strcmp($_POST['formState'],"newForm") == 0){ // check if it is a new form

    //Insert initiators email as 0 index of the email chain.
    $stmt = $db->prepare('INSERT INTO email (formID, email, hasBeenSentTo, sendOrder, hasApproved) VALUES (:formID, :emailString, 1, 0, 1)');
    $stmt->bindParam(':formID', $formID);
    $stmt->bindParam(':emailString', $email);
    $stmt->execute();

    foreach($fields as $field)
    {
        if ($field->type == 'signature')   //case for sigs can change to sig of sign later instead of file
        {
            echo '<button type="button" id="' . $field->fieldId . '" name="' . $field->name . '" onclick="signButton(this.id, \''.$userFullname.'\', '.$formID.')" style="font-size: 100%; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;">Sign</button>';
        }
        else  if ($field->type == 'div')   
        {
            echo'<div class="divText" contenteditable="true" id="' . $field->fieldId . '" oninput="dbUpdate(this.id, this.innerHTML,'.$formID.')" 
            style="border: 1px solid black; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;"></div>';
        }
        else    //all other input types here
        {
            echo '<input type="' . $field->type . '" id="' . $field->fieldId . '" name="' . $field->name . '" onchange="dbUpdate(this.id, this.value,'.$formID.')" style="border: 1px solid black; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;">';
        }
    }

    echo '</form></div>';

    //comments stuff
    $stmt = $db->prepare('SELECT formComments FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $comments = $stmt->fetch()['formComments'];
    echo '<br><h3 style="text-align:center;">Comments</h3><div id="formComments" name="formComments" contenteditable="true" oninput="comment('.$formID.')" style="width:80%; margin:auto; border:1px solid black;">'.$comments.'</div>';

    echo '<br><h3 style="text-align: center;">Email</h3>';
    echo    '<br>
        <input type="text" value="" id="emailChainInput" name="emailChain" onchange="dbEmailUpdate(this.value, '.$formID.')">
        <br>
        <input type="submit" value="Send Form via Email Chain" onclick="sendAsEmailChain('.$formID.')" id="sendAsEmailChain">
        <input type="submit" value="Send Form via Mass Email " onclick="sendAsMassEmail('.$formID.')" id="sendAsMassEmail">
        <p id="message"> </p>'
        ;
}




//Check if the form is pending approval or completed.
else if(strcmp($_POST['formState'],"pending") == 0 || strcmp($_POST['formState'],"completed") == 0){ 

    $stmt = $db->prepare('SELECT * FROM fields WHERE formID=:formID ORDER BY FieldID ASC');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $result = $stmt->fetchAll();


    $stmt = $db->prepare('SELECT formState, authorID FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formDetails = $stmt->fetch();
    $formState = $formDetails['formState'];
    $authorID = $formDetails['authorID'];

    $i = 0;
    $iMax = count($result);
    foreach($fields as $field)
    {
        if ($field->type == 'signature')
        {
            echo '<button type="button" id="' . $field->fieldId . '" name="' . $field->name . '"';
            if (strcmp($_POST['formState'],"completed") != 0)
            {
                echo 'onclick="signButton(this.id, \''.$userFullname.'\', '.$formID.')" ';
            }
            echo 'style="left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;';
            if ($field->fieldId == $result[$i]['FieldID'] && count($result))
            {
                if (strcmp($result[$i]["content"], '') != 0)
                {
                    echo 'font-family: Cedarville Cursive; font-size: 70%;">' . $result[$i]["content"];
                }
                else
                {
                    echo 'font-size: 100%;">Sign';
                }
                if ($i < $iMax - 1)
                {
                    $i++;
                }
            }
            else
            {
                echo 'font-size: 100%;">Sign';
            }
            echo'</button>';
        }
        else if ($field->type == 'div')   
        {
            echo'<div class="divText" contenteditable="false" id="' . $field->fieldId . '" oninput="dbUpdate(this.id, this.innerHTML,'.$formID.')" style="border: 1px solid black; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;">';
            if ($field->fieldId == $result[$i]['FieldID'] && count($result) ){
                
                //echo $result[$i]["content"];
                echo divStringCleaner($result[$i]["content"]);

                if ($i < $iMax - 1)
                {
                    $i++;
                }
            }
            echo' </div>';
            
        }
        else
        {
            echo '<input type="' . $field->type . '" id="' . $field->fieldId . '" name="' . $field->name . '" onchange="dbUpdate(this.id, this.value,'.$formID.')" ';

            if ($field->fieldId == $result[$i]['FieldID'])
            {
                if ($field->type == "checkbox")
                {
                    if($result[$i]["content"] == "true"){
                        echo 'checked onclick="return false;" ';
                    }
                    else{
                        echo 'value="'. $result[$i]["content"] .'" onclick="return false;" ';
                    }
                    
                }
                else if($field->type == "date"){
                    echo 'value="'. $result[$i]["content"] .'" onclick="return false;" ';
                }
                else
                {
                    echo 'readonly value="'. $result[$i]["content"] .'" ';
                }

                if ($i < $iMax - 1)
                {
                    $i++;
                }
            }
            else{
                if ($field->type == "checkbox" ) // || $field->type == "date")
                {
                    echo 'onclick="return false;" ';    
                }
                else if($field->type != "date")
                {
                    echo 'readonly ';
                }
            }
            
        echo 'style="border: 1px solid black; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;">';
        }
    }

    echo '</form></div>';

    //Form comments
    $stmt = $db->prepare('SELECT formComments FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $comments = $stmt->fetch()['formComments'];
    echo '<br><h3 style="text-align:center;">Comments</h3><div id="formComments" name="formComments" contenteditable="true" oninput="comment('.$formID.')" style="width:80%; margin:auto; border:1px solid black;">'.$comments.'</div>';

    //Populate the email chain input box with current emails in db.
    if($userID == $authorID){
        $stmt = $db->prepare('SELECT email FROM email WHERE formID=:formID AND sendOrder != 0 ORDER BY sendOrder ASC');
        $stmt->bindParam(':formID', $formID);
        $stmt->execute();
        $emailChain = $stmt->fetchAll();

        $emailChainString = "";
        foreach($emailChain as $indEmail){
            $emailChainString .= $indEmail[0] . " ";
        }
        echo '<br><h3 style="text-align: center;">Email</h3>';
        echo    '<br>
        <input type="text" value="'.$emailChainString.'" id="emailChainInput" name="emailChain" readonly>
        <br>';
        
    }

    $stmt = $db->prepare('SELECT hasApproved FROM email WHERE formID=:formID AND email=:email'); //TODO: do we need all??
    $stmt->bindParam(':formID', $formID);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $userHasApproved = $stmt->fetchAll();

    //Display approve, edit, disapprove options.
    if (strcmp(($_POST['isCurrentReviewer']),"true")==0 && $userHasApproved[0]["hasApproved"] == 0){ 
    echo    '<br>
        <input type="submit" value="Approve" onclick="approveForm('.$formID.', \''.$email.'\', \'approve\')" id="approveForm">
        <input type="submit" value="Edit" onclick="approveForm('.$formID.', \''.$email.'\', \'edit\')" id="editForm">
        <input type="submit" value="Disapprove" onclick="approveForm('.$formID.', \''.$email.'\', \'disapprove\')" id="disapproveForm">
        <p id="message"> </p>';
    } 


}



//The form is a draft.
else 
{
    //$formID = $_POST['formID'];
    $stmt = $db->prepare('SELECT * FROM fields WHERE formID=:formID ORDER BY FieldID ASC');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $result = $stmt->fetchAll();
    


    $stmt = $db->prepare('SELECT formState, authorID FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formDetails = $stmt->fetch();
    $formState = $formDetails['formState'];
    $authorID = $formDetails['authorID'];

    $i = 0;
    $iMax = count($result);
    foreach($fields as $field)
    {
        if ($field->type == 'signature')
        {   
            echo '<button type="button" id="' . $field->fieldId . '" name="' . $field->name . '" onclick="signButton(this.id, \''.$userFullname.'\', '.$formID.')" style="left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;';
            if ($field->fieldId == $result[$i]['FieldID'])
            {
                if (strcmp($result[$i]["content"], '') != 0)
                {
                    echo 'font-family: Cedarville Cursive; font-size: 70%;">' . $result[$i]["content"];
                }
                else
                {
                    echo 'font-size: 100%;">Sign';
                }
                if ($i < $iMax - 1)
                {
                    $i++;
                }
            }
            else
            {
                echo 'font-size: 100%;">Sign';
            }
            echo'</button>';
        }
        else  if ($field->type == 'div')   
        {
            echo'<div class="divText" contenteditable="true" id="' . $field->fieldId . '" oninput="dbUpdate(this.id, this.innerHTML,'.$formID.')" 
            style="border: 1px solid black; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;">';
            if ($field->fieldId == $result[$i]['FieldID'] && count($result) ){
                //echo $result[$i]["content"];
                echo divStringCleaner($result[$i]["content"]);

                if ($i < $iMax - 1)
                {
                    $i++;
                }
            }
            echo' </div>';
        }
        else
        {
            echo '<input type="' . $field->type . '" id="' . $field->fieldId . '" name="' . $field->name . '" onchange="dbUpdate(this.id, this.value,'.$formID.')" ';

            if ($field->fieldId == $result[$i]['FieldID'])
            {
                if ($field->type == "checkbox" && $result[$i]["content"] == "true")
                {
                    echo 'checked ';
                }
                else
                {
                    echo 'value="'. $result[$i]["content"] .'" ';
                }
                if ($i < $iMax - 1)
                {
                    $i++;
                }
            }
        echo 'style="border: 1px solid black; left: ' . numToPercent($field->x, 215.9) . '%; top: ' . numToPercent($field->y, 279.4*$pageCount) . '%; position: absolute; width: ' . numToPercent($field->width, 215.9) . '%; height: ' . numToPercent($field->height, 279.4*$pageCount) . '%;">';
        }
    }

    echo '</form></div>';

    //Form comments stuff
    $stmt = $db->prepare('SELECT formComments FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $comments = $stmt->fetch()['formComments'];
    echo '<br><h3 style="text-align:center;">Comments</h3><div id="formComments" name="formComments" contenteditable="true" oninput="comment('.$formID.')" style="width:80%; margin:auto; border:1px solid black;">'.$comments.'</div>';

    //Populate the email chain input box with the current emails in the DB
    if($userID == $authorID){ 
        $stmt = $db->prepare('SELECT email FROM email WHERE formID=:formID AND sendOrder != 0 ORDER BY sendOrder ASC');
        $stmt->bindParam(':formID', $formID);
        $stmt->execute();
        $emailChain = $stmt->fetchAll();

        $emailChainString = "";
        foreach($emailChain as $indEmail){
            $emailChainString .= $indEmail[0] . " ";
        }
        echo '<br><h3 style="text-align: center;">Email</h3>';
        echo    '<br>
        <input type="text" value="'.$emailChainString.'" id="emailChainInput" name="emailChain" onchange="dbEmailUpdate(this.value, '.$formID.')">
        <br>
        <input type="submit" value="Send Form via Email Chain" onclick="sendAsEmailChain('.$formID.')" id="sendAsEmailChain">
        <input type="submit" value="Send Form via Mass Email " onclick="sendAsMassEmail('.$formID.')" id="sendAsMassEmail">';
    }


}

?>

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
