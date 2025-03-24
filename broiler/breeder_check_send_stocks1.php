<?php
//breeder_check_send_stocks1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$trnum = $_GET['trnum'];

$count = 0;
$sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$trnum' AND `active` = '1' AND `quantity` != '0'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
 