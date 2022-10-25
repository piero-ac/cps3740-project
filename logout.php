<?php
if(!isset($_COOKIE['userid'])) { 
    echo "Cookie userid does not exist!"; 
    die;
}
else {
    echo "<p>You successfully logged out.</p>";
    unset($_COOKIE['userid']); // remove a cookie
    setcookie('userid', '', time() - 3600); //expire the cookie immediately
    unset($_COOKIE['name']); // remove a cookie
    setcookie('name', '', time() - 3600); //expire the cookie immediately

    echo "<br><br>";
    echo "<a href='index.html'>project home page</a>";

}

?>