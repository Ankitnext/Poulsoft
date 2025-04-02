<?php
ini_set('display_errors', 0);
ini_set('log_errors', 0);
//ini_set('error_reporting', E_ALL);
error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
if(!isset($_SESSION)){ session_start(); }
global $conn;
global $conns;
if(!isset($_SESSION['dbase'])){
    //header('location: logout.php');
    exit;
}
else{
    if(isset($conn)){ mysqli_close($conn); }
    $conns = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB","poulso6_userlist") or die('No Connection');
    $connc = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB","poulso6_admin_chickenmaster") or die('No Connection');
    $conn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$_SESSION['dbase']) or die('No Connection');
    $sconn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB","poulso6_admin_abroilerstatus") or die('No Connection');
}
if(!$conn){ echo "Connection Error" ; }
date_default_timezone_set("Asia/Kolkata");
?>