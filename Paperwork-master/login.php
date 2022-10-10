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

if(isset($_SESSION['login_id'])){
    header('Location: home.php');
    exit;
}

// Creating new google client instance
$client = new Google_Client();

// Enter your Client ID
$client->setClientId('258739609433-g99eqeo39l6lot00us8em6j9a7icvf23.apps.googleusercontent.com');
// Enter your Client Secrect
$client->setClientSecret('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');
// Enter the Redirect URL
$client->setRedirectUri('https://pcr-wa-015.org/paper/login.php');

// Adding those scopes which we want to get (email & profile Information)
$client->addScope("email");
$client->addScope("profile");

if(isset($_GET['code'])):

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if(!isset($token["error"])){

        $client->setAccessToken($token['access_token']);

        // getting profile information
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
    
        // Storing data into database
        $id =  $google_account_info->id;
        $fname =trim($google_account_info->givenName);
        $lname =trim($google_account_info->familyName);
        $email =$google_account_info->email;

        // checking user already exists or not

        $stmt = $db->prepare("SELECT * FROM `users` WHERE `googleID`=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $get_user = $stmt;
        $stmt = $db->prepare("SELECT FOUND_ROWS()");
        $stmt->execute();
        $get_user = $stmt;

        $rowCount =$get_user->fetchColumn();
        if($rowCount > 0){
            $_SESSION['login_id'] = $id; 
            header('Location: home.php');
            exit;
        }
        else{
            // if user not exists we will insert the user
            try{

            $stmt = $db->prepare("INSERT INTO `users`(`googleID`,`email`,`firstName`,`lastName`) VALUES(:id,:email,:fname,:lname)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':fname', $fname);
            $stmt->bindParam(':lname', $lname);
            $stmt->execute();
            $insert = $stmt;
            } catch (PDOException $e) {
                if ($e->getCode() == 1062) {
                    echo "Something went wrong";
                } else {
                    throw $e;
                }
            }
            $_SESSION['login_id'] = $id; 
            header('Location: home.php');
            exit;

        }
    }
    else{
        header('Location: login.php');
        exit;
    }
    
else: 
    // Google Login Url = $client->createAuthUrl(); 
?>
<html>
<head>
    <link rel="icon" type="image/x-icon" href="images/favicon-32x32.png">
</head>
<body>
    <div width="100%" style="height: 100%; text-align: center; padding-top: 10%;">
        <h1 style="font-family: Verdana, sans-serif;">CAP Login</h1>
        <a href="<?php echo $client->createAuthUrl(); ?>"><button type="button" style="background:#4285f4;color:white; border:none; width:100px; height:40px; border-radius:3%; cursor: pointer;   box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1); text-align: center; "><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" style="width:30px; background:white; float:left;" ><b style="top: 5px;  position: relative">Login</b></button></a>
    </div>
</body>
</html>
    <!-- <a class="login-btn" href="<?php echo $client->createAuthUrl(); ?>">Login</a> -->

<?php endif; ?>



