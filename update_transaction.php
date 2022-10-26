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

$customer_id = $_POST['cid'][0]; // all cids will be the same
$notes = array();
$source_ids = array();
$mids_to_delete = array(); // will store all the transactions ids to delete
$mids_to_possibly_update = array(); // will store all the transcation ids to check if note message has changed

// var_dump($_POST);

// traverse all records to check for whether to update or delete
for($i = 0; $i < count($_POST['note']); $i++){
    if(isset($_POST['cdelete'][$i])){
        array_push($mids_to_delete, $_POST['mid'][$i]); 
    } else {
        array_push($source_ids, $_POST['sid'][$i]);
        array_push($notes, $_POST['note'][$i]);
        array_push($mids_to_possibly_update, $_POST['mid'][$i]);
    }
}

$mids_delete_length = count($mids_to_delete);
if($mids_delete_length > 0) {
    echo "<h4>Will delete $mids_delete_length records from the table.</h4>";
    for($i = 0; $i < $mids_delete_length; $i++){
        $mid_to_delete = $mids_to_delete[$i];
        $deletion_sql = "delete from CPS3740_2022F.Money_coronapi where cid='$customer_id' and mid='$mid_to_delete'";
        $deletion_result = mysqli_query($con, $deletion_sql);

        if($deletion_result){
            echo "The Code $mid_to_delete has been deleted. <br>";
        } else {
            echo "Something is wrong with SQL: " . mysqli_error($con);
        }
    }
    echo "$mids_delete_length records have been deleted. <br>";
} else {
    echo "No records will be deleted. <br>";
}

$mids_update_length = count($mids_to_possibly_update);
echo "Will check $mids_update_length records for possible update.";
for($i = 0; $i < $mids_update_length; $i++){
    echo "<br>Record $i - $mids_to_possibly_update[$i] will be checked for possible update.";
}

// var_dump($_POST);


?>