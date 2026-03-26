<?php // This open the php code section

session_start();  # connect back to the session for data in there

require_once "assets/common.php";  # bring in the common functions we need
require_once "assets/dbconn.php"; # get the connection functions for the database

if (!isset($_SESSION['userid'])) {  # If they have managed to get to this page without loggining
    $_SESSION['usermessage'] = "ERROR: You are not logged in!"; // sets error messsge
    header("Location: login.php");  // redirects them
    exit;  // ensures no othetr code executes
} elseif($_SERVER["REQUEST_METHOD"] === "POST") {  // if the user has posted
    if(isset($_POST['delprod'])){  // if they have clicked to delete the appointment
        unset($_SESSION['basket'][$_POST['itemid']]);
        $_SESSION['usermessage'] = "SUCCESS: Product Removed.";
        header('Location: basket.php');  // send them to the alterbooking page
        exit;  // ensure no other code can execute
    } elseif (isset($_POST['updateprod'])) {  // if the change appointment button was use
        $_SESSION['basket'][$_POST['itemid']] = $_POST['quantity']; // capture the appointment id from the from
        $_SESSION['usermessage'] = "SUCCESS: Quantity updated.";
        header('Location: basket.php');  // send them to the alterbooking page
        exit;  // ensure no other code can execute
    }
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



echo "<h2> Carty - Your Basket</h2>";  # sets a h2 heading as a welcome

echo "<br>";

echo usermessage();

echo "<br>";

echo "<p class='content'> Below are the Items in your basket </p>";


try{
    $products = products_getter(dbconnect());
    $ordersubtotal = 0;
} catch(PDOException $e){
    $_SESSION['message'] = "ERROR: ".$e->getMessage();
    header('Location: index.php');  // send them to the alterbooking page
    exit;  // ensure no other code can execute
} catch (Exception $e){
    $_SESSION['message'] = "ERROR: ".$e->getMessage();
    header('Location: index.php');  // send them to the alterbooking page
    exit;  // ensure no other code can execute
}

    echo "<table id='basket'>";

    echo "<thead>";
        echo "<tr>";
            echo "<th></th>";
            echo "<th>Name</th>";
            echo "<th>Category</th>";
            echo "<th>Price</th>";
            echo "<th>Sub</th>";
            echo "<th>Actions</th>";
        echo "</tr>";
    echo "</thead>";

    foreach ($_SESSION['basket'] as $itemid => $quantity) {


        echo "<tr>";

        if ($products[$itemid]['imglink']==""){
            $imglnk = "default.png";
        } else {
            $imglnk = $products[$itemid]['imglink'];
        }

        echo "<td> <img src='assets/prod_img/thumb_" . $imglnk . "'> </td>";
        echo "<td> " . $products[$itemid]['name'] . "</td>";
        echo "<td> " . $products[$itemid]['category'] . "</td>";
        echo "<td> £" . $products[$itemid]['unitprice'] . "</td>";

        echo "<td> ";
            $itemsubtotal = $products[$itemid]['unitprice'] * $quantity;
            $ordersubtotal += $itemsubtotal;
            echo "£ " . $itemsubtotal;
        echo "</td>";

        echo "<td>";
 echo "<form id='ind_item' action='' method='post'>
        <input type='hidden' name='itemid' value='" . $itemid . "'> 
                   <input type='number' name='quantity' min='0' max='" .$products[$itemid]['dailyquantity'] . "' value='" . $quantity . "'/>
                   <input type='submit' name='delprod' value='Delete' />
                   <input type='submit' name='updateprod' value='Update Quantity' />";
        echo "</form>";
        echo "</td>";
        echo "</tr>";


    }
    echo "</table>";

echo "<h2> Your Basket Total</h2>";

echo "<table id='subtotal'>";
    echo "<tr>";
    echo "<td> Your Basket Subtotal: £" . $ordersubtotal . "</td>";
echo "<td>";
echo "<form id='ind_item' action='' method='post'>";
echo "<select id='delorcol' name='delorcol' value=''>";
    echo"<option value='collection'>Collection</option>";
    echo"<option value='delivery'>Delivery</option>";
echo "</select>";
echo " Select delivery or collection date: ";
echo "<input type='date' id='start' name='trip-start' min='2026-01-01' max='2026-12-31'>";
echo "<input type='submit' name='clearorder' value='Delete Basket' />";
echo "<input type='submit' name='comporder' value='Complete Order' />";
echo "</form>";
echo "</td>";
    echo "</tr>";
echo "</table>";

echo "<br>";



echo "</div>";

echo "</div>";

echo "</body>";

echo "</html>";
?>