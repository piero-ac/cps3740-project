<?php   

    include "dbconfig.php";

    // connection to database
    $con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());

    // Get the user's username and password
    // Check if they have entered username and password
    if(isset($_POST['username']))
        $user = mysqli_real_escape_string($con, $_POST['username']);
    else
        die("Please enter username first!");
    
    if(isset($_POST['password']))
        $pass = mysqli_real_escape_string($con, $_POST['password']);
    else
        die("Please enter password first!");

    // SQL statement to check user login info
    $sql_customers = "select * from CPS3740.Customers where login = '$user'"; // do not compare passwords
    $sql_customers_results = mysqli_query($con, $sql_customers);

    if($sql_customers_results){ // check if result is false (something wrong with query)
        $num_rows = mysqli_num_rows($sql_customers_results);

        if($num_rows == 0) 
            echo "<h1>Login $user doesn't exist in the database</h1>"; // 1.2
        else if ($num_rows > 1) 
            echo "<h1>More than one user with login $user</h1>";

        else {
            $customer_row = mysqli_fetch_array($sql_customers_results);
            $cpassword = $customer_row['password'];
            if($cpassword == $pass){

                ##### Logout Function #####
                echo "<a href='logout.php'>User Logout</a><br>";

                ##### Display Connection Information #####
                // Get IP of client
                $ip = $_SERVER['REMOTE_ADDR'];
                echo "<p>Your IP: $ip</p>"; // 2.1
                
                // Get Client's Browser Information
                $browser = $_SERVER['HTTP_USER_AGENT'] . "\n\n";
                echo "<p>Your browser and OS: $browser</p>"; // 2.1

                // Display whether the user is from Kean University (2.3)
                $IPv4 = explode(".",$ip);

                if($IPv4[0] == "10" || ($IPv4[0] == "131" )){
                    echo "<p>You are from Kean University</p>\n";
                } else {
                    echo "<p>You are NOT from Kean University</p>\n";
                }
                
                ##### Get user id #####
                $id = $customer_row['id'];

                #####  Set cookie for user #####
                setcookie('userid', $id, time()+600);
               

                ##### Obtain user info #####
                $name = $customer_row['name'];
                $age = convertDOBToAge($customer_row['DOB']);
                $address = $customer_row['street'] . ", " . $customer_row['city'] . ", " . $customer_row['state'] . ", " . $customer_row['zipcode'];
                $img = $customer_row['img'];

                ##### Set cookie for name #####
                setcookie('name', $name, time()+600);

                ##### Display user info (2.1) #####
                echo "<p>Welcome Customer: <strong>$name</strong></p>"; 
                echo "<p>Age: $age</p>";
                echo "<p>Address: $address</p>";
                echo '<img src="data:image/jpeg;base64,'.base64_encode($img).'"/>'; // (2.2)
                echo "<hr />";

                ##### Display Transactions for User (2.4) #####
                $transactions_sql = "select m.*, s.name from CPS3740_2022F.Money_coronapi m, CPS3740.Sources s where m.cid = '$id' and m.sid = s.id";
                $transactions_results = mysqli_query($con, $transactions_sql);

                if($transactions_results){
                    $num_transactions = mysqli_num_rows($transactions_results);
                    if($num_transactions == 0) 
                        echo "<h2>No transaction history.</h2>";
                    else {
                        echo "<p>There are <strong>$num_transactions</strong> transactions for customer <strong>$name</strong>:</p>";
                        echo "<table border=1>\n"; // opening transactions table tag
                        echo "<tbody>"; // opening tbody tag
                        echo "<tr><th>ID</th><th>Code</th><th>Type</th><th>Amount</th><th>Source</th><th>Date Time</th><th>Note</th></tr>";
                        $total_balance = 0;
                        while($transaction_row = mysqli_fetch_array($transactions_results)){
                            
                            $transaction_mid = $transaction_row["mid"];
                            $transaction_code = $transaction_row["code"];
                            $transaction_type = ($transaction_row["type"] == "D") ? "Deposit" : "Withdraw"; // (2.5)
                            $transaction_amount = $transaction_row["amount"];
                            
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
                            echo "<tr><td>$transaction_mid</td><td>$transaction_code</td><td>$transaction_type</td><td>$transaction_amount</td><td>$transaction_source</td><td>$transaction_datetime</td><td>$transaction_note</td></tr>";
                        }

                        // determine color to display for total balance (2.7)
                        $total_balance = ($total_balance >= 0) ? "<font color='blue'>$total_balance</font>" : "<font color='red'>$total_balance</font>";

                        echo "</tbody>"; // closing tbody tag
                        echo "</table>\n"; // closing transactions 
                        echo "<p>Total balance: $total_balance</p>"; // display total balance
                    }

                    mysqli_free_result($transactions_results); // free the result set
                } else {
                    echo "Something is wrong with SQL: " . mysqli_error($con);
                }

                ##### 2.8 #######

                echo <<<HTML
                    <br><br>
                    <table border=0>
                        <tbody>
                            <tr>
                                <td>
                                    <form action='add_transaction.php' method='POST'>
                                        <input type='hidden' name='customer_name' value='$name'>
                                        <input type='submit' value='Add transaction'>
                                    </form>
                                </td>
                                <td><a href='display_transaction.php'>Display and update transaction</a></td>
                                <td><a href='display_stores.php' target='_blank'>Display stores</a></td>
                            </tr>
                            <tr>
                                <td colspan=3>
                                    <form action="search_transaction.php" method='get'>
                                        Keyword:
                                        <input type="text" name="keywords" required='required'>
                                        <input type="submit" value="Search transaction">
                                    </form>
                                </td>
                            </tr>
                        </tbody>   
                    </table>
                HTML;


                ##### Clear result set #####
                mysqli_free_result($sql_customers_results); // free the result set

            } else {
                echo "<h1>Login $user exists, but password does not match</h1>"; // 1.3
            }
        }
    } else {
        echo "Something is wrong with SQL: " . mysqli_error($con);
    }

    // http://obi.kean.edu/~coronapi/CPS3740/login.php
mysqli_close($con);

function convertDOBToAge($dob){
    $today = date("Y-m-d");
    $diff = date_diff(date_create($dob), date_create($today));
    $dob = $diff->format('%y');
    return $dob;
}

?>

