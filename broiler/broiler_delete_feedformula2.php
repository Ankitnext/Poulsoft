<?php
//broiler_delete_feedformula2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedformula2'];

$utype = $_GET['utype'];
$id = $_GET['id'];
$sql ="SELECT * FROM `broiler_feed_formula` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){
    while($row = mysqli_fetch_assoc($query)){ $code = $row['code']; }
}
if($utype == "delete"){
    $sql = "UPDATE `broiler_feed_formula` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$code'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_feedformula2.php?ccid='.$ccid); }
}

 ?>
