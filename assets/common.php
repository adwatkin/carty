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
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  //brings back results
    return $result;
}

function in_basket($itemid){
    if(array_key_exists($itemid, $_SESSION['basket'])){
        return false;  // because they already have in their basket
    } else {
        return true; // its not in the basket, so its ok.
    }

}

function stock_check($conn, $itemid, $quant){

    // THIS IS MISSING THE DATE CHECK AS WELL.. AS IT HAS NOT BEEN ADDED

    $sql = "SELECT quantity FROM basket WHERE itemid = ?"; //set up the sql statement
    $stmt = $conn->prepare($sql); //prepares
    $stmt->bindParam(1,$itemid);  //binds the parameters to execute
    $stmt->execute(); //run the sql code
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  //brings back results

    $sql = "SELECT dailyquantity FROM item WHERE itemid = ?"; //set up the sql statement
    $stmt = $conn->prepare($sql); //prepares
    $stmt->bindParam(1,$itemid);  //binds the parameters to execute
    $stmt->execute(); //run the sql code
    $result1 = $stmt->fetch(PDO::FETCH_ASSOC);  //brings back results
    $conn = null;  // nulls off the connection so cant be abused.

    if($result){  // if there is a result returned
        $total = 0;
        foreach($result as $row){
            $total += $row["quantity"];
        }
        if($total+$quant > $result1['dailyquantity']){
            return false; // meaning you cant order that many
        } else {
            return true;  // meaning you can order that many
        }
    } elseif($result1['dailyquantity']>$quant) {
        return true;  // no orders made for that date but enough stock
    }
    else  {
          return false;
    }
}