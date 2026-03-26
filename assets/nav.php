<?php
echo "<div class='navi'>";
echo "<nav>";  #decales

echo "<ul>";  #declares an unordered list


echo "<li class='linkbox'> <a href='index.php'>Home</a></li>"; #open a cell for a link to be housed
if(!isset($_SESSION['userid'])){
    echo "<li class='linkbox'> <a href='login.php'>Login</a></li>";
    echo "<li class='linkbox'> <a href='register.php'>Register</a></li>";
} else {
    echo "<li class='linkbox'> <a href='products.php'>Products</a></li>";
    echo "<li class='linkbox'> <a href='basket.php'>Basket</a></li>";
    echo "<li class='linkbox'> <a href='order history.php'>Order History</a></li>";
    echo "<li class='linkbox'> <a href='logout.php'>Logout</a></li>";
}
echo "</ul>";  # closes the row of the table.

echo "</nav>";

echo "</div>";