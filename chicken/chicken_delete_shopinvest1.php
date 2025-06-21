<?php
//chicken_delete_shopinvest1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['shopinvest1'];

$utype = $_GET['page'];
$id = $_GET['id'];

$sql = "SELECT * FROM `shop_machine_investment` WHERE `trnum` = '$id' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ids = $row['trnum']; }

if($utype == "pause"){
    $sql = "UPDATE `shop_machine_investment` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
       header('location:chicken_display_shopinvest1.php?ccid='.$cid); 
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `shop_machine_investment` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_shopinvest1.php?ccid='.$cid); 
    }
}
else if($utype == "delete"){
    $sql = "UPDATE `shop_machine_investment` SET `active` = '0',`dflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_shopinvest1.php?ccid='.$cid); 
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `shop_machine_investment` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_shopinvest1.php?ccid='.$cid); 
    }
}

?>
