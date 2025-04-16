<?php
//broiler_modify_clientfieldstatus2.php $dbname = $_SESSION['dbase'];
include "../newConfig.php";
if(!isset($_SESSION)){ session_start(); }
date_default_timezone_set("Asia/Kolkata");

$file_url = $_GET['file_url'];
$user_code = $_GET['user_code'];
$field_name = $_GET['field_name'];

$sql2 = "SELECT * FROM `master_cbr_main_details` WHERE `file_url` LIKE '%$file_url%' AND `user_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2);
while($row2 = mysqli_fetch_assoc($query2)){
    if(empty($row2[$field_name]) || $row2[$field_name] == ""){ }
    else{
        $field_value = $row2[$field_name];
    }
}
$fas_details = explode(":",$field_value);
if($fas_details[1] == 0){
    $cur_field_val = $fas_details[0].":1:".$fas_details[2];
}
else{
    $cur_field_val = $fas_details[0].":0:".$fas_details[2];
}

$sql2 = "UPDATE `master_cbr_main_details` SET `$field_name` = '$cur_field_val' WHERE `file_url` LIKE '%$file_url%' AND `user_code` = '$user_code' AND `active` = '1'";
if(!mysqli_query($conn,$sql2)){ $error_code = 1; } else{ $error_code = 0; }
echo $error_code;
