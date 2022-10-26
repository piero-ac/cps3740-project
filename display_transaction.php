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


##### Get user id #####
$id = $_COOKIE['userid'];

##### Logout Function #####
echo "<a href='logout.php'>User Logout</a><br>";
echo "<p>You can only update the <strong>Note</strong> column.";
echo "<form action='update_transaction.php' method='post'>";

$transactions_sql = "select m.*, s.name from CPS3740_2022F.Money_coronapi m, CPS3740.Sources s where m.cid = '$id' and m.sid = s.id";
$transactions_results = mysqli_query($con, $transactions_sql);

if($transactions_results){
    $num_transactions = mysqli_num_rows($transactions_results);
    if($num_transactions == 0) 
        echo "<h2>No transaction history.</h2>";
    else {
        echo "<table border=1>\n"; // opening transactions table tag
        echo "<tbody>"; // opening tbody tag
        echo "<tr><th>ID</th><th>Code</th><th>Amount</th><th>Type</th><th>Source</th><th>Date Time</th><th>Note</th><th>Delete</th></tr>";
        $total_balance = 0;
        $row = 0;
        while($transaction_row = mysqli_fetch_array($transactions_results)){
            $transaction_mid = $transaction_row["mid"];
            $transaction_code = $transaction_row["code"];
            $transaction_type = ($transaction_row["type"] == "D") ? "Deposit" : "Withdraw"; // (2.5)
            $transaction_amount = $transaction_row["amount"];
            $transaction_source_id = $transaction_row["sid"];
            
            // determine color of number and current total balance
            if($transaction_type == "Deposit"){
                $total_balance += (int)$transaction_amount;
                $transaction_amount = "<font color='blue'> $transaction_amount </font>";
            } else {
                $total_balance -= (int)$transaction_amount;
                $transaction_amount = "<font color='red'> -$transaction_amount </font>";
            } 

            $transaction_source = $transaction_row["name"]; // (2.6)
            $transaction_datetime = $transaction_row["mydatetime"];
            $transaction_note = $transaction_row["note"];
            echo <<<HTML
            <tr>
                <td>$transaction_mid</td>
                <td>$transaction_code</td>
                <td>$transaction_amount</td>
                <td>$transaction_type</td>
                <td>$transaction_source</td>
                <td>$transaction_datetime</td>
                <td bgcolor='yellow'>
                    <input type="text" value='$transaction_note' name="note[$row]" style='background-color:yellow;'>
                </td>
                <td>
                    <input type="checkbox" name="cdelete[$row]" value='Y'>
                    <input type="hidden" name="cid[$row]" value='$id'>
                    <input type="hidden" name="sid[$row]" value='$transaction_source_id'>
                    <input type="hidden" name="mid[$row]" value='$transaction_mid'>
                </td>
                
            </tr>
            HTML;
            $row++;
        }
        echo "</tbody>";
        echo "</table>";
        echo "<p>Total balance: $total_balance</p>";
        echo "<br>";
        echo "<input type='submit' value='Update Transaction'>";
    }  
    mysqli_free_result($transactions_results); // free the result set 
} else {
echo "Something is wrong with SQL: " . mysqli_error($con);
}

mysqli_close($con);
?>
