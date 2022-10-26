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

// $customer_ids = array();
// $notes = array();
// $source_ids = array();
// $mids = array();

// for($i = 0; $i < count($_POST['cdelete']); $i++){
//     if($_POST['cdelete'][$i] == 'Y'){

//         $customer_id = $_POST['cid']
//         echo ""
//     }
// }

var_dump($_POST);
?>