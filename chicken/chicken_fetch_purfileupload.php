<?php
//broiler_fetch_purfileupload.php
if(!isset($_SESSION)){ session_start(); } include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$dbname = $_SESSION['dbase'];
$trnum = $_GET['trnum'];

$file_dt = "";
$folder_path = "documents/".$dbname."/Purchase_Docs"."/";
$sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$trnum' AND `tdflag` = '0' GROUP BY `invoice` ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $file_dt = str_replace($folder_path,"",$row['purchase_image'])."@".$row['remarks'];
}

echo $file_dt;
?>