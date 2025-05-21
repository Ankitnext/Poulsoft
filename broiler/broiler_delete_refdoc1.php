<?php
//broiler_delete_refdoc1.php
session_start(); include "newConfig.php";

$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'image_deletion_details';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.image_deletion_details LIKE poulso6_admin_broiler_broilermaster.image_deletion_details;"; mysqli_query($conn,$sql1); }

$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['purchase2'];
$trnum = $_GET['trnum'];
$column = $_GET['colm'];
$type = $_GET['type'];
if($type == "purchase"){
    //fetching file url
    $sql = "SELECT `$column` FROM `broiler_purchases` WHERE `trnum` = '$trnum'";
    $query = mysqli_query($conn,$sql); $file_url = "";
    while($row = mysqli_fetch_assoc($query)){ $file_url = $row[$column]; }
    //inserting to table
    $sql = "INSERT INTO `image_deletion_details`(trnum,type,path_type,file_path,addedemp,addedtime) VALUES ('$trnum','$type','$column','$file_url','$addedemp','$addedtime')";
    mysqli_query($conn,$sql);
    //updating
    $sql = "UPDATE `broiler_purchases` SET `$column` = NULL WHERE `trnum` = '$trnum'";
    mysqli_query($conn,$sql);
}

