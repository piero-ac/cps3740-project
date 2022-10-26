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
$total_balance = 0;


#### inputs that require validation
$code = $_POST['code'];

// Check if a radio button was selected
$type = "";
if(!isset($_POST['type'])){
    echo "Please select deposit or withdraw.";
    die;
} else {
    $type = $_POST['type'];
}

// Check if the amount entered is valid and code is unique
$amount = (int)$_POST['amount'];

if($amount <= 0){
    echo "Amount must be positive";
    die;

// Amount entered is valid
} else {

    // Find current balance and the transaction codes
    $balance_records_sql  = "select code, type, amount from CPS3740_2022F.Money_coronapi where cid=$id";
    $balance_results = mysqli_query($con, $balance_records_sql);
    $balance_codes = array(); // array's values will be compared with user entered code to check for uniqueness

    if($balance_records_sql){
        $num_rows = mysqli_num_rows($balance_results);

        if($num_rows == 0){
            $total_balance = (int)$_POST['balance'];
        } else {
            while($balance_row = mysqli_fetch_array($balance_results)){
                $balance_type = $balance_row['type'];
                $balance_amount = (int)$balance_row['amount'];
                $balance_code = $balance_row['code'];
                array_push($balance_codes, $balance_code); // push the codes into the balance_codes array
                $total_balance = ($balance_type == 'D') ? $total_balance + $balance_amount : $total_balance - $balance_amount;
            }
            mysqli_free_result($balance_results); // free the result set
        }
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }

    // Check if code does not already exist in transactions for user
    if(in_array($code, $balance_codes)){
        echo "<p style='color:red'>Error! The transaction code $code already exists in the database.</p>";
        die;
    }

    // Check if type of transaction is withdrawal
    if($type == "W"){
        if($amount > $total_balance){
            echo "<p style='color:red'>Error! Customer $name has $$total_balance in the bank, and tries to withdraw $$amount. Not enough money!</p>";
            die;
        } else {
            echo "Withdrawal has occurred";
        }
        
    }
}

// Display entered information (only get here after validating input except for code)
// echo "Customer_name: $customer_name \n";
// echo "Customer_id: $id \n";
// echo "Code: $code \n";
// echo "Type: $type \n";
// echo "Source_Id: $source_id \n";
// echo "Amount: $amount \n";
// echo "Note: $note \n";
// echo "Balance Before Insertion: $current_total_balance";


##### Get Transactions for User





?>