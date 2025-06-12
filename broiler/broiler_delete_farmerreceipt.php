<?php
//broiler_delete_farmerreceipt.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['farmerreceipt'];
$table_session = $ccid."tbl_access";
$table_name = $_SESSION[$table_session];
$utype = $_GET['utype'];
$trnum = $_GET['trnum'];

if($utype == "delete"){
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Farmer Receipt' AND `field_function` LIKE 'Farmer Discount: Pass Credit Note' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $fmr_dflag = mysqli_num_rows($query);
    if((int)$fmr_dflag == 1){
        $sql = "SELECT * FROM `".$table_name."` WHERE `trnum` = '$trnum'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $ccn_trnum = $row['ccn_trnum'];
        }
    
        if($ccn_trnum != ""){
            $sql = "UPDATE `broiler_crdrnote` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ccn_trnum'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
            else{
                $sql = "UPDATE `account_summary` SET `active` = '0',`dflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ccn_trnum'";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
                else { header('location:broiler_display_farmerreceipt.php?ccid='.$ccid); }
            }
        }
    }
    
    $sql = "UPDATE `".$table_name."` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '0',`dflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else { header('location:broiler_display_farmerreceipt.php?ccid='.$ccid); }
    }
}

?>
