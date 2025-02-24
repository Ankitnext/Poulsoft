<?php
//layer_fetch_flock_details.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$today = date("Y-m-d");
$flock_code = $_GET['flock_code'];
$incr = $_GET['incr'];
$start_date = ""; $start_age = $max_eflag = 0;

$sql = "SELECT * FROM `layer_dayentry_consumed` WHERE `flock_code` = '$flock_code' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `layer_dayentry_consumed` WHERE `flock_code` = '$flock_code' AND `active` = '1' AND `dflag` = '0');";
$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query);
if((int)$d_cnt > 0){
    while($row = mysqli_fetch_array($query)){
        $start_date = date("d.m.Y",strtotime($row['date']."+".($incr + 1)." days"));
        $start_age += (int)$row['breed_age'] + (int)$incr + 1;
    }
    if(strtotime($start_date) > strtotime($today)){ $max_eflag = 1; }
}
else{
    $sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_array($query)){
        $start_date = date("d.m.Y",strtotime($row['start_date']."+".$incr." days"));
        $start_age += (int)$row['start_age'] + (int)$incr;
    }
    if(strtotime($start_date) > strtotime($today)){ $max_eflag = 1; }
}
echo $incr."[@$&]".$start_date."[@$&]".$start_age."[@$&]".$max_eflag;
?>
