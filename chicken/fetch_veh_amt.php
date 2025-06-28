<?php
include "newConfig.php";

$wcode = $_GET['wcode'];

$sql = "SELECT rate FROM `chicken_vehicle_kmw_rate` WHERE `warehouse` = '$wcode' ORDER BY date DESC LIMIT 1;";
$query = mysqli_query($conn,$sql);
$fetchrate = mysqli_fetch_assoc($query);
$rate = $fetchrate['rate'];

$sql = "SELECT cur_reading FROM `vehicle_fuelfilling` WHERE `vno` = '$wcode' ORDER BY addedtime DESC LIMIT 1;";
$query = mysqli_query($conn,$sql);
$fetchrate = mysqli_fetch_assoc($query);
$tokms = $fetchrate['cur_reading'];
$rnd = round($tokms);
echo $rate."@".$rnd;