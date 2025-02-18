<?php
//layer_check_farm1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$code = $_GET['code'];

$count = 0;
$sql = "SELECT * FROM `layer_units` WHERE `farm_code` = '$code' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
if($count > 0){
    echo $count;
}
else{
    $sql = "SELECT * FROM `layer_sheds` WHERE `farm_code` = '$code' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    if($count > 0){
        echo $count;
    }
    else{
        $sql = "SELECT * FROM `layer_batch` WHERE `farm_code` = '$code' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
        if($count > 0){
            echo $count;
        }
        else{
            $sql = "SELECT * FROM `layer_shed_allocation` WHERE `farm_code` = '$code' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
            echo $count;
        }
    }
}
?>
