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

// get the keywords and customer id + name
$id = $_COOKIE['userid'];
$name = $_COOKIE['name'];
$keywords = mysqli_real_escape_string($con, $_GET['keywords']);

// select appropriate query based on conditions
$search_sql;
if($keywords == "*" ){
    $search_sql = "select m.*, s.name from CPS3740_2022F.Money_coronapi m, CPS3740.Sources s where m.cid = '$id' and m.sid = s.id";
} else {
    $search_sql = "select m.*, s.name from CPS3740_2022F.Money_coronapi m, CPS3740.Sources s where m.cid = '$id' and m.sid = s.id and m.note like '%$keywords%'";
}

// make the query
$search_results = mysqli_query($con, $search_sql);

if($search_results){
   $num_search_results = mysqli_num_rows($search_results);
   if($num_search_results == 0) {
        echo "<p>There are no transactions in customer <strong>$name</strong> records that matched the keyword: $keywords.</p>";
   } else {
        echo "The transactions in customer <strong>$name</strong> records that matched the keyword $keywords are: \n";
        echo "<table border=1>\n"; // opening transactions table tag
        echo "<tbody>"; // opening tbody tag
        echo "<tr><th>ID</th><th>Code</th><th>Type</th><th>Amount</th><th>Date Time</th><th>Note</th><th>Source</th></tr>";
        $total_balance = 0;
        while($transaction_row = mysqli_fetch_array($search_results)){
            
            $transaction_mid = $transaction_row["mid"];
            $transaction_code = $transaction_row["code"];
            $transaction_type = ($transaction_row["type"] == "D") ? "Deposit" : "Withdraw"; // (2.5)
            $transaction_amount = $transaction_row["amount"];
            
            // determine color of number and current total balance
            if($transaction_type == "Deposit"){
                $total_balance += (float)$transaction_amount;
                $transaction_amount = "<font color='blue'> $transaction_amount </font>";
            } else {
                $total_balance -= (float)$transaction_amount;
                $transaction_amount = "<font color='red'> -$transaction_amount </font>";
            }   

            $transaction_source = $transaction_row["name"]; // (2.6)
            $transaction_datetime = $transaction_row["mydatetime"];
            $transaction_note = $transaction_row["note"];
            echo "<tr><td>$transaction_mid</td><td>$transaction_code</td><td>$transaction_type</td><td>$transaction_amount</td><td>$transaction_datetime</td><td>$transaction_note</td><td>$transaction_source</td></tr>";
        }

        // determine color to display for total balance (2.7)
        $total_balance = ($total_balance >= 0) ? "<font color='blue'>$total_balance</font>" : "<font color='red'>$total_balance</font>";

        echo "</tbody>"; // closing tbody tag
        echo "</table>\n"; // closing transactions 
        echo "<p>Total balance: $total_balance</p>"; // display total balance

        mysqli_free_result($search_results); // free the result set
    }
} else {
    echo "Something is wrong with SQL: " . mysqli_error($con);
}

mysqli_close($con);


?>