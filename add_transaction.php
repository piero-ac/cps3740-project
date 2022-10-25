<?php
// check if user id exists
if(!isset($_COOKIE['userid'])) { 
    echo "Please login first!"; 
    die;
}

##### Logout Function #####
echo "<a href='logout.php'>User Logout</a><br>";

##### Heading Information ######
echo "<br><br>";
echo "<h2>Add transaction</h2>";

$name = $_POST['customer_name'];
echo "<p><strong>$name</strong> current balance is X";


?>