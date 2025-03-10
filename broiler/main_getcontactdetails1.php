<?php
//main_getcontactdetails.php
if(!isset($_SESSION)){ session_start(); }
$dbname = $_SESSION['dbase']; include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$cus_ccode = $_GET['cus_ccode'];
$type = $_GET['type'];
$id = $_GET['id'];
if($type == "add"){
    $id_filter = "";
}
else if($type == "edit"){
    $id_filter = " AND `id` NOT IN ('$id')";
}
$sql = "SELECT * FROM `main_contactdetails` WHERE `cus_ccode` LIKE '$cus_ccode' AND `dflag` = '0'".$id_filter." ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $dup_count = mysqli_num_rows($query);
if($dup_count > 0){ echo "Error"; } else{ echo "ok"; }

?>