<?php
//layer_fetch_shedwise_avlflocks.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$shed_code = $_GET['shed_code'];
$batch_opt = "";
$sql = "SELECT * FROM `layer_shed_allocation` WHERE `shed_code` = '$shed_code' AND `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0'";
$query = mysqli_query($conn,$sql); $s_cnt = mysqli_num_rows($query);
if((int)$s_cnt > 0){
    $batch_opt = '<option value="select">-select-</option>';
    while($row = mysqli_fetch_array($query)){
        $code = $row['code']; $name = $row['description'];
        $batch_opt .= '<option value="'.$code.'">'.$name.'</option>';
    }
}

echo $batch_opt;
?>
