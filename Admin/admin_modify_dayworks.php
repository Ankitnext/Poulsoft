<?php
//admin_update_dayworks.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['dayworks'];


// $gdate = $_POST['gdate'];
$date = date("Y-m-d",strtotime($_POST['date']));
 $idk = $_POST['idvalue'];

$tic_no = $_POST['tic_no'];
$mod_type = $_POST['mod_type'];
$cl_name = $_POST['cl_name'];
$wok_type = $_POST['wok_type'];
$gdate = date("Y-m-d",strtotime($_POST['gdate']));
$fl_type = $_POST['fl_type'];
$fl_link = $_POST['fl_link'];
$wdate = date("Y-m-d",strtotime($_POST['wdate']));
$t_taken = $_POST['t_taken'];
$status = $_POST['status'];
$remarks = $_POST['remarks'];

// Make sure to identify the record uniquely (e.g., using an ID or another unique column)
//  echo "<br>".$id = $_POST['record_id'];

 "<br>".$sql = "UPDATE `emp_daily_works` SET date='$date', `tic_no` = '$tic_no', `mod_type` ='$mod_type', `cl_name` ='$cl_name', `wok_type`='$wok_type',`gdate`='$gdate',`fl_type`='$fl_type',`fl_link`='$fl_link',`wdate`='$wdate',`t_taken`='$t_taken',`statuses`='$status',`remarks`='$remarks',`addedemp`='$addedemp',`addedtime` ='$addedtime' WHERE `id` = '$idk'";

if(!mysqli_query($conn,$sql)){
    die("Error:-".mysqli_error($conn));
} else {
    // mysqli_query($conn,$sql);
    // echo "data updated";
    header('location:admin_display_dayworks.php?ccid='.$ccid);
}
?>

