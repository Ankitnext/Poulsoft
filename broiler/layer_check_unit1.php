<?php
//layer_check_unit1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$code = $_GET['code'];

$count = 0;
$sql = "SELECT * FROM `layer_sheds` WHERE `unit_code` = '$code' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
if($count > 0){
    echo $count;
}
else{
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `unit_code` = '$code' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    echo $count;
}
?>
