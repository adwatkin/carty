<?php

//
//function
//
//function basket_adder($conn, $orderid, $prodid, $quant, $idontknowwhat){
//    $sql = "INSERT into BASKET  VALUES(?,?,?,?<)";
//
//    $stmt = $conn->prepare($sql);
//    $stmt->bind_param("ssi", $orderid, $prodid, $quant);
//}

function usermessage(){  # function to check for a user message and return echoable string
    if(isset($_SESSION['usermessage'])){  # checks to see if it is set
        if(str_contains($_SESSION['usermessage'],"ERROR")){  # if it's an error
            $msg = "<div id='usererror'>".$_SESSION['usermessage']."</div>";  # formats string appropriately
        } else {  # if it's not an error
            $msg = "<div id='usermessage'>".$_SESSION['usermessage']."</div>";  # positive message given
        }
        unset($_SESSION['usermessage']);  # unsets the user message so it doesn't keep being displayed
    } else {
        $msg = "";  # if no message has been set, returns empty string.
    }
    return $msg;
}

function onlyuser($conn, $email){  # At registration checks to make sure that no user already matches
    $sql = "SELECT email FROM user WHERE email = ?"; //set up the sql statement
    $stmt = $conn->prepare($sql); //prepares
    $stmt->bindParam(1, $email);
    $stmt->execute(); //run the sql code
    $result = $stmt->fetch(PDO::FETCH_ASSOC);  //brings back results
    if ($result) {  # if a user is returned
        return false; # return false so y
    } else {
        return true;
    }
}

function reg_user($conn){

    // Prepare and execute the SQL query
    $sql = "INSERT INTO user (email, password, fname, sname, addrln1, addrln2) VALUES (?, ?, ?, ?, ?, ?)";  //prepare the sql to be sent
    $stmt = $conn->prepare($sql); //prepare to sql

    $stmt->bindParam(1, $_POST['email']);  //bind parameters for security
    // Hash the password
    $stmt->bindParam(2, password_hash($_POST['password'], PASSWORD_DEFAULT));
    $stmt->bindParam(3, $_POST['fname']);
    $stmt->bindParam(4, $_POST['sname']);
//    $stmt->bindParam(5, $_POST['dob']);
//    $stmt->bindParam(5, date('Y-m-d'));
    $stmt->bindParam(5, $_POST['addressln1']);
    $stmt->bindParam(6, $_POST['addressln2']);
//    $stmt->bindParam(9, $_POST['postcode']);
//    $stmt->bindParam(10, $_POST['county']);
//    $stmt->bindParam(11, $_POST['phone']);

    $stmt->execute();  //run the query to insert
    $conn = null;  // closes the connection so cant be abused.
    return true; // Registration successful
}

function getnewuserid($conn, $email){  # upon registering, retrieves the userid from the system to audit.
    $sql = "SELECT userid FROM user WHERE email = ?"; //set up the sql statement
    $stmt = $conn->prepare($sql); //prepares
    $stmt->bindParam(1, $email);
    $stmt->execute(); //run the sql code
    $result = $stmt->fetch(PDO::FETCH_ASSOC);  //brings back results
    return $result["userid"];
}

function login($conn, $email){
    $sql = "SELECT userid, password FROM user WHERE email = ?"; //set up the sql statement
    $stmt = $conn->prepare($sql); //prepares
    $stmt->bindParam(1,$email);  //binds the parameters to execute
    $stmt->execute(); //run the sql code
    $result = $stmt->fetch(PDO::FETCH_ASSOC);  //brings back results
    $conn = null;  // nulls off the connection so cant be abused.

    if($result){  // if there is a result returned
        return $result;

    } else {
        return false;
    }

}

// New function to collect all of the products from the database to see all of the ones in stock
function products_getter($conn){
    $sql = "SELECT * FROM item"; //set up the sql statement
    $stmt = $conn->prepare($sql); //prepares
    //$stmt->bindParam(1, $email);
    $stmt->execute(); //run the sql code
    $result = $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);  // ***** THIS LINE AND ITS FORMAT IS IMPORTANT ******
    return $result;
}

function in_basket($itemid){
    if(array_key_exists($itemid, $_SESSION['basket'])){
        return false;  // because they already have in their basket
    } else {
        return true; // its not in the basket, so its ok.
    }

}

function stock_check($conn, $datewanted){

    // 1. Get already sold totals for that date
    // Note: Use backticks ` or no quotes for table names, not single quotes '
    $sql = "SELECT b.itemid, SUM(b.quantity) as total_sold 
            FROM custorder AS o 
            JOIN basket AS b ON o.orderid = b.orderid 
            WHERE o.datefor = ? 
            GROUP BY b.itemid";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$datewanted]);
    // Use FETCH_KEY_PAIR to get [itemid => total_sold] for easy math
    $alreadySold = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 2. Get the master daily limits for items
    $sql = "SELECT itemid, dailyquantity FROM item";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $dailyLimits = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $unavailableItems = [];

    // 3. Loop through the current user's basket
    foreach ($_SESSION['basket'] as $itemid => $requestedQuant) {
        #These lines use the Null Coalescing Operator (??), which is a shorthand way of
        # saying: "Try to use this value, but if it doesn't exist, use this default instead."

        $limit = $dailyLimits[$itemid] ?? 0;
        $sold = $alreadySold[$itemid] ?? 0;

        $remainingStock = $limit - $sold;

        if ($requestedQuant > $remainingStock) {
            // This item is short on stock! Store the ID (or name)
            $unavailableItems[] = $itemid;
        }
    }

    // Return the list of items that failed the check
    return $unavailableItems;

}

function new_order($conn,$datewanted, $userId, $delorcol){
    $sql = "INSERT INTO custorder (datemade, datefor, userid, delORcol, orderstatus) VALUES (NOW(), ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

// 2. Execute with your data
    $stmt->execute([$datewanted, $userId, $delorcol, "Complete"]);

// 3. Capture the newly generated OrderID
    $newOrderId = $conn->lastInsertId();
    $conn = null;
    return $newOrderId;
}

function sell_basket($conn, $basket, $orderid){
    $itemStmt = $conn->prepare("INSERT INTO basket (orderid, itemid, quantity) VALUES (?, ?, ?)");

    foreach ($basket as $itemid => $quantity) {
        $itemStmt->execute([$orderid, $itemid, $quantity]);
    }
    $conn = null;
    return true;
}