<?php
//broiler_fetch_purfileupload.php
if(!isset($_SESSION)){ session_start(); } include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$dbname = $_SESSION['dbase'];
$trnum = $_GET['trnum'];

$file_dt = "";
$folder_path = "documents/".$dbname."/Purchase_Docs"."/";
$sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$trnum' AND `dflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $file_dt = str_replace($folder_path,"",$row['file_url1'])."@".str_replace($folder_path,"",$row['file_url2'])."@".str_replace($folder_path,"",$row['file_url3'])."@".$row['file_remarks'];
}

echo $file_dt;
?>