<?php // This open the php code section

session_start();

require_once "assets/common.php";
require_once "assets/dbconn.php";

if (isset($_SESSION['userid'])) {  // checks to see if already logged in
    $_SESSION['usermessage'] = "ERROR: You have already logged in!";
    header("Location: index.php");
    exit;  // this is needed to ensure that no other code executes.
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $usr = login(dbconnect(), $_POST["email"]);

        if ($usr && password_verify($_POST["password"], $usr["password"])) {
            //audtitor(dbconnect_insert(),$usr["userid"],"LGI", "User has successfully logged in");  // audit logs the login
            $_SESSION["userid"] = $usr["userid"];
            $_SESSION["basket"] = [];  // This is the initial point, after logging in, that a basket is made!
            $_SESSION['usermessage'] = "SUCCESS: User Successfully Logged In";
            header("location:index.php");
            exit;
        } elseif(!$usr) {
            $_SESSION['usermessage'] = "ERROR: User not found";
            header("Location: login.php");
            exit;
        }else {
            $_SESSION['usermessage'] = "ERROR: User login passwords not match";
            //if($usr["user_id"]){
                //audtitor(dbconnect_insert(),$usr["user_id"],"flo", "User has unsuccessfully logged in");
            //}
            header("Location: login.php");
            exit;

        }
    } catch(PDOException $e) {
        $_SESSION['usermessage'] = "ERROR: " . $e->getMessage();
        header("Location: login.php");
        exit;
    } catch(Exception $e) {
        $_SESSION['usermessage'] = "ERROR: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
}

echo "<!DOCTYPE html>";

echo "<html>";

echo "<head>";

echo "<title>Carty</title>";
echo "<link rel='stylesheet' type='text/css' href='assets/css/styles.css' />";

echo "</head>";

echo "<body>";

echo "<div class='container'>";

require_once "assets/topbar.php";

require_once "assets/nav.php";

echo "<div class='content'>";
echo "<br>";

echo "<h2> Carty - User Login System</h2>";  # sets a h2 heading as a welcome

echo "<br>";

echo usermessage();

echo "<br>";

echo "<p class='content'> Please Enter the needed credentials below! </p>";

echo "<form action='' method='post'>";
echo"<br>";
echo "<input type='email' name='email' placeholder='E-mail Address' required/>";
echo"<br>";
echo "<input type='password' name='password' placeholder='Password' required/>";
echo"<br>";

echo "<input type='submit' name='submit' value='Login' />";

echo "</form>";



echo "</div>";

echo "</div>";

echo "</body>";

echo "</html>";
?>