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

##### Heading Information ######
echo "<br><br>";
echo "<h2>Add transaction</h2>";

// Get the name from cookies
$name = $_POST['customer_name'];

// Find the current total balance
$id = $_COOKIE['userid'];
$balance_records_sql  = "select type, amount from CPS3740_2022F.Money_coronapi where cid=$id";
$balance_results = mysqli_query($con, $balance_records_sql);
$total_balance = 0;

// If statement to determine total balance
if($balance_records_sql){
    $num_rows = mysqli_num_rows($balance_results);

    if($num_rows == 0){
        $total_balance = 0;
    } else {
        while($balance_row = mysqli_fetch_array($balance_results)){
            $type = $balance_row['type'];
            $amount = (int)$balance_row['amount'];
            $total_balance = ($type == 'D') ? $total_balance + $amount : $total_balance - $amount;
        }
        mysqli_free_result($balance_results); // free the result set
    }

    echo "<p><strong>$name</strong> current balance is $total_balance.";
} else {
    echo "Something is wrong with SQL: " . mysqli_error($con);
}

mysqli_close($con);
?>