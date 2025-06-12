<?php
//chicken_modify_stocktransfer1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Purchase Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$warehouse = $_POST['warehouse'];
$tcds_per = $_POST['tcds_per'];

$dcno = $_POST['dcno'];
$trnums = $_POST['trnum'];
$pdate = date("Y-m-d", strtotime($_POST['pdate']));
$fsector = $_POST['fsector'];
$icode = $_POST['icode'];
$jalqty = $_POST['jalqty'];
$birdqty = $_POST['birdqty'];
$qty = $_POST['qty'];
$price = $_POST['price'];
$tsector = $_POST['tsector'];
$remarks = $_POST['remarks'];


$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "stocktransfer1";
$trlink = "chicken_display_stocktransfer1.php";
$trip_trnum = $_POST['idvalue'];

    if($jalqty == ""){ $jalqty = 0; }
    if($birdqty == ""){ $birdqty = 0; }
    if($qty == ""){ $qty = 0; }
    if($price == ""){ $price = 0; }

    $sql = "UPDATE `item_stocktransfers` SET `date` = '$pdate',`fromwarehouse` = '$fsector',`code` = '$icode',`jals` = '$jalqty',`birds`='$birdqty',`quantity`='$qty',`price`='$price',`towarehouse` = '$tsector',`dcno`='$dcno',`remarks`='$remarks' WHERE `trnum` = '$trnums' ";

    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }




header('location:chicken_display_stocktransfer1.php?ccid='.$ccid);

