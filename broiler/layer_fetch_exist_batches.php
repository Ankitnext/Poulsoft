<?php
//layer_fetch_exist_batches.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$date = date("Y-m-d",strtotime($_GET['date']));
$batch_code = $_GET['batch_code'];
$type = $_GET['type'];
$ids = $_GET['id'];
$id_fltr = ""; if($type == "edit"){ $id_fltr = " AND `id` NOT IN('$ids')"; }

$sql = "SELECT * FROM `layer_shed_allocation` WHERE `batch_code` = '$batch_code'".$id_fltr." AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $c_cnt = mysqli_num_rows($query); $code = "";
if((int)$c_cnt > 0){
    while($row = mysqli_fetch_array($query)){
        $start_date = $row['start_date'];
        $start_age = (int)$row['start_age']; if($start_age == ""){ $start_age = 0; }
    }
    if($start_date == "" && $start_age == 0){
        $start_date = ""; $start_age = 0;
    }
    else{
        if(strtotime($date) < strtotime($start_date)){
            $start_date = ""; $start_age = 0;
        }
        else{
            $gdays = 0; $gdays = (INT)((strtotime($date) - strtotime($start_date)) / 60 / 60 / 24);
            $start_date = date("d.m.Y",strtotime($start_date."+".$gdays." days"));
            $start_age = (int)$start_age + (int)$gdays;
        }
    }
}

echo $c_cnt."@$&".$start_date."@$&".$start_age;
?>
