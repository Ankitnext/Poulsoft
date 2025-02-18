<?php
//layer_check_shedallocate1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$shed_code = $_GET['shed_code'];
$batch_code = $_GET['batch_code'];

$count = 0;
$sql = "SELECT * FROM `layer_dayentry_consumed` WHERE `shed_code` = '$shed_code' AND `batch_code` = '$batch_code' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
if($count > 0){
    echo $count;
}
else{
    $sql = "SELECT * FROM `layer_dayentry_produced` WHERE `shed_code` = '$shed_code' AND `batch_code` = '$batch_code' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    echo $count;
}
?>
