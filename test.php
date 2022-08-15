<?php 
$con = mysqli_connect("192.168.0.114", "root", "", "uthman_test");

if ($con != null or $con != false) {
    echo "Connected";
} else {
    echo mysqli_connect_error();
}
?>