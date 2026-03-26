<?php // This open the php code section

session_start();  # connect back to the session for data in there

require_once "assets/common.php";  # bring in the common functions we need
require_once "assets/dbconn.php"; # get the connection functions for the database

if (!isset($_SESSION['userid'])) {  # If they have managed to get to this page without loggining
    $_SESSION['usermessage'] = "ERROR: You are not logged in!"; // sets error messsge
    header("Location: login.php");  // redirects them
    exit;  // ensures no othetr code executes
} elseif($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['quantity']>0) {  // if the user has posted
    try {
        if (in_basket($_POST['itemid'])) {  // only checks for in basket as date order for can be different (see basket page code)
            $_SESSION["basket"][$_POST['itemid']] = $_POST['quantity'];
            $_SESSION['usermessage'] = "SUCCESS: Item added to your Basket!";
            header('Location: products.php');  // redirects them
            exit;  // ensures no other code executes
        } else {
            $_SESSION['usermessage'] = "ERROR: In basket already";
            header('Location: products.php');  // redirects them
            exit;  // ensures no other code executes
        }
    } catch (PDOException $e) {
        $_SESSION['usermessage'] = "ERROR: ".$e->getMessage();
        header('Location: products.php');  // redirects them
        exit;  // ensures no other code executes
    } catch (Exception $e){
        $_SESSION['usermessage'] = "ERROR: ".$e->getMessage();
        header('Location: products.php');  // redirects them
        exit;  // ensures no other code executes
    }
} elseif($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['quantity']==0) {
    $_SESSION['usermessage'] = "ERROR: You need a quantity higher than 1!"; // sets error messsge
    header("Location: products.php");  // redirects them
    exit;  // ensures no othetr code executes
}

echo "<!DOCTYPE html>";  # essential html line to dictate the page type

echo "<html>";  # opens the html content of the page

echo "<head>";  # opens the head section

echo "<title> Carty</title>";  # sets the title of the page (web browser tab)
echo "<link rel='stylesheet' type='text/css' href='assets/css/styles.css' />";  # links to the external style sheet

echo "</head>";  # closes the head section of the page

echo "<body>";  # opens the body for the main content of the page.

echo "<div class='container'>";

require_once "assets/topbar.php";

require_once "assets/nav.php";

echo "<div class='content'>";



echo "<h2> Carty - Products</h2>";  # sets a h2 heading as a welcome

echo "<br>";

echo usermessage();

echo "<br>";

echo "<p class='content'> Below are the products we sell. </p>";
try{
    $products = products_getter(dbconnect());
} catch(PDOException $e){
    $_SESSION['message'] = "ERROR: ".$e->getMessage();
    header('Location: index.php');  // send them to the alterbooking page
    exit;  // ensure no other code can execute
} catch (Exception $e){
    $_SESSION['message'] = "ERROR: ".$e->getMessage();
    header('Location: index.php');  // send them to the alterbooking page
    exit;  // ensure no other code can execute
}

if (!$products) {
    echo "no Products found";
} else {

    echo "<table id='products'>";

    foreach ($products as $id => $prod) {

        echo "<form action='' method='post'>";

        echo "<tr>";
        if ($prod['imglink']==""){
        $imglnk = "default.png";
        } else {
            $imglnk = $prod['imglink'];
        }

        echo "<td> <img src='assets/prod_img/thumb_" . $imglnk . "'> </td>";
        echo "<td> " . $prod['name'] . "</td>";
        echo "<td> " . $prod['category'] . "</td>";
        echo "<td> £" . $prod['unitprice'] . "</td>";

        echo "<td><input type='hidden' name='itemid' value=".$id."> 
                   <input type='number' name='quantity' min='0' max='" .$prod['dailyquantity'] . "' value='0' />
                   <input type='submit' name='addprod' value='Add' /></td>";

        echo "</tr>";
        echo "</form>";

    }


    echo "</table>";
}
echo "<br>";



echo "</div>";

echo "</div>";

echo "</body>";

echo "</html>";
?>