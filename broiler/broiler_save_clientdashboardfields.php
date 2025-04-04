<?php
//broiler_save_clientdashboardfields.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['clientdashboardfields'];

$user_code = $_POST['user_code'];

// $i = 0; foreach($_POST['user_code'] as $desc){ $user_code[$i] = $desc; $i++; }
$i = 0; foreach($_POST['panel_name'] as $desc){ $fval = explode("@",$desc); $field_name[$i] = $fval[0]; $panel_name[$i] = $fval[1]; $i++; }
$i = 0; foreach($_POST['sort_order'] as $desc){ $sort_order[$i] = $desc; $i++; }
$flag = $dflag = 0; $active = '1';

$dsize = sizeof($user_code);
for($i = 0; $i < $dsize;$i++){
    $sql ="SELECT * FROM `master_dashboard_links` WHERE `user_code` = '$user_code AND `field_name` = '$field_name[$i]' AND `panel_name` = '$panel_name[$i]' AND `dflag` = '0' ORDER BY `id` ASC";
    $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ }
    else {
        $sql = "INSERT INTO `master_dashboard_links` (field_name,panel_name,user_code,sort_order,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('$field_name[$i]','$panel_name[$i]','$user_code','$sort_order[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ $error_details .= mysqli_error($conn); } else{ }
    }
}
header('location:broiler_display_clientdashboardfields.php?ccid='.$ccid);
?>
