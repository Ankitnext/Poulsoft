<?php
//layer_check_stocktransfer1.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$trnum = $_GET['trnum'];

$count = 0;
$sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$trnum' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
echo $count;
?>
