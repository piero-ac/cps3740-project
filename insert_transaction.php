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

##### Get the inputs from the form #####
$source_id = $_POST['source_id'];
$note = $_POST['note'];
$customer_name = $_POST['customer_name'];
$id = $_COOKIE['userid']; 

## Find the transaction codes in the database for comparison
$code = $_POST['code'];
$codes_array = getCodes($con);

## Find current balance
$current_balance = getCurrentBalance($con, $id);

## Find the amount being entered
$amount = (float)$_POST['amount'];

## Check if code already exists in the database
if(in_array($code, $codes_array)){
    echo "<p style='color:red'>Error! The transaction code $code already exists in the database.</p>";
    die;
}

## Check if a radio button was selected;
if(!isset($_POST['type'])){
    echo "Please select deposit or withdraw.";
    die;
}
$type = $_POST['type'];

## Check if the amount entered is valid 
if($amount <= 0){
    echo "Amount must be positive";
    die;
} 
##  Check if we are trying to withdraw more than what's available in account
if($type == "W" && $amount > $current_balance){
    echo "<p style='color:red'>Error! Customer $customer_name has $$current_balance in the bank, and tries to withdraw $$amount. Not enough money!</p>";
    die;
} 

$insert_sql = "insert into CPS3740_2022F.Money_coronapi (code, cid, type, amount, mydatetime, note, sid) values ('$code', '$id', '$type', $amount, NOW(), '$note', '$source_id')";

# Execute the insertion query
$insert_result = mysqli_query($con, $insert_sql);
if($insert_result){
    $new_balance = ($type=="W") ? $current_balance - $amount : $current_balance + $amount; 
    if($type == "W")
        echo "<br>Withdrawal of $$amount successful!";
    else 
        echo "<br>Deposit of $$amount successful!";  
    echo "<br>Transaction ($code) has been added successfully.";
    echo "<br>New balance: $$new_balance";
} else {
    echo "Something is wrong with insertion SQL: " . mysqli_error($con);
}


function getCodes($con){
    $codes_sql = "select code from CPS3740_2022F.Money_coronapi";
    $codes_results = mysqli_query($con, $codes_sql);
    $codes_array = array();

    if($codes_results){
        $num_rows = mysqli_num_rows($codes_results);
        
        if($num_rows == 0){
            echo "No transactions in database.";
        } else {
            while($code_row = mysqli_fetch_array($codes_results)){ 
                $code = $code_row['code'];
                array_push($codes_array, $code);
            }
            mysqli_free_result($codes_results);
        }
    } else {
        echo "Something is wrong with getting codes SQL: " . mysqli_error($con);
    }
    return $codes_array;
}

function getCurrentBalance($con, $id){
    $balance_records_sql  = "select type, amount from CPS3740_2022F.Money_coronapi where cid=$id";
    $balance_results = mysqli_query($con, $balance_records_sql);
    $total_balance = 0;

    if($balance_results){
        $num_rows = mysqli_num_rows($balance_results);

        if($num_rows == 0){
            $total_balance = (float)$_POST['balance'];
        } else {
            while($balance_row = mysqli_fetch_array($balance_results)){
                $balance_type = $balance_row['type'];
                $balance_amount = (float)$balance_row['amount'];
                $total_balance = ($balance_type == 'D') ? $total_balance + $balance_amount : $total_balance - $balance_amount;
            }
            mysqli_free_result($balance_results); // free the result set
        }
    } else {
        echo "Something is wrong with getting balance SQL: " . mysqli_error($con);
    }
    return $total_balance;
}


mysqli_close($con);
?>