<?php
// check if user id exists
if(!isset($_COOKIE['userid'])) { 
    echo "Please login first!"; 
    die;
}

include "dbconfig.php";

// connection to database
$con = mysqli_connect($host, $username, $password, $dbname) 
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());


##### Logout Function #####
echo "<a href='logout.php'>User Logout</a><br>";


##### Get the necessary inputs from the form and cookie array

#### inputs that do not require validation
$source_id = $_POST['source_id'];
$note = $_POST['note'];
$customer_name = $_POST['customer_name'];
$id = $_COOKIE['userid']; // attained from cookie

#### inputs that require validation
// Check if a radio button was selected
$type = "";
if(!isset($_POST['type'])){
    echo "Please select deposit or withdraw.";
    die;
} else {
    $type = $_POST['type'];
}

// Check if the amount entered is valid
$amount = (int)$_POST['amount'];
if($amount <= 0){
    echo "Amount must be positive";
    die;
}

// Check if code does not already exist in transactions for user
$code = $_POST['code'];

// Display entered information (only get here after validating input except for code)
echo "Customer_name: $customer_name \n";
echo "Customer_id: $id \n";
echo "Code: $code \n";
echo "Type: $type \n";
echo "Source_Id: $source_id \n";
echo "Amount: $amount \n";
echo "Note: $note \n";






?>