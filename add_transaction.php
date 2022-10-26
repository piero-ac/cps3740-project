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

// Form 
echo <<<HTML
    <form name="input" action="insert_transaction.php" method="POST" required="required">
        <input type="hidden" name="customer_name" value='$name'>
        Transaction Code:
        <input type="text" name="code" required="required">
        <br>
        <input type="radio" name="type" value="D">
        Deposit 
        <input type="radio" name="type" value="W">
        Withdrawal 
        <br>
        Amount:
        <input type="text" name="amount" required="required">
        <input type="hidden" name="balance" value='$total_balance'>
        <br>
        Select a Source:
        <select name="source_id">
HTML;
?>

<?php
// Get sources from Sources table
$sources_sql = "select * from CPS3740.Sources";
$sources_results = mysqli_query($con, $sources_sql);

if($sources_results){
    while($sources_row = mysqli_fetch_array($sources_results)){
        $value = $sources_row['id'];
        $name = $sources_row['name'];
        echo "<option value=$value>$name</option>";
    }
    mysqli_free_result($sources_results); // free the result set
} else {
    echo "Something is wrong with SQL: " . mysqli_error($con);
}

echo <<<HTML
        </select> 
        <br> 
        Note: 
        <input type="text" name="note">
        <br>
        <input type="submit" value="Submit">
    </form>

HTML;


mysqli_close($con);
?>