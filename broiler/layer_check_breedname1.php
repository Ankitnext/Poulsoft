<?php
//layer_check_breedname1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$code = $_GET['code'];

$count = 0;
$sql = "SELECT * FROM `layer_batch` WHERE `breed_code` = '$code' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
if($count > 0){
    echo $count;
}
else{
    $sql = "SELECT * FROM `layer_medvac_schedule` WHERE `breed_code` = '$code' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
}
?>
