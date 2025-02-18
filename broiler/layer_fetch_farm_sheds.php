<?php
//layer_fetch_farm_sheds.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";
$farm_code = $_GET['farm_code'];
$unit_code = $_GET['unit_code'];

$inv_list = "";
$sql = "SELECT * FROM `layer_sheds` WHERE `farm_code` IN ('$farm_code') AND `unit_code` IN ('$unit_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $lqt_count = mysqli_num_rows($query); $c = 0;
if($lqt_count > 0){
    $inv_list .= '<option value="select">-select-</option>';
    while($row = mysqli_fetch_assoc($query)){
        $code = $row['code'];
        $name = $row['description'];
        $inv_list .= '<option value="'.$code.'">'.$name.'</option>';
    }
}
else{ }

echo $inv_list;
?>