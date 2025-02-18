<?php
//layer_delete_shedallocate1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['shedallocate1'];

$utype = $_GET['utype'];
$id = $_GET['id'];

$sql = "SELECT * FROM `layer_shed_allocation` WHERE `id` = '$ids' AND `dflag` = '0'";
$query = mysqli_query($conn, $sql); $opn_ftrnum = $opn_mtrnum = $flk_code = "";
while($row = mysqli_fetch_assoc($query)){ $flk_code = $row['code']; $opn_ftrnum = $row['opn_ftrnum']; $opn_mtrnum = $row['opn_mtrnum']; }

if($utype == "delete"){
    $sql = "UPDATE `layer_shed_allocation` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql3 = "UPDATE `account_summary` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` IN ('$opn_ftrnum','$opn_mtrnum') AND `flock_code` = '$flk_code' AND `dflag` = '0'";
        if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    }
}
header('location:layer_display_shedallocate1.php?ccid='.$ccid);
 ?>
