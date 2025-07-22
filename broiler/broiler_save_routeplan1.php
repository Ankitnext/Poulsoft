<?php
//broiler_save_routeplan1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['routeplan1'];

$date =  date("Y-m-d",strtotime($_POST['date']));
$so_date =  date("Y-m-d",strtotime($_POST['so_date']));
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];
$route_no = $_POST['route_no'];
$company = $_POST['company'];
$labour1 = $_POST['labour1'];
$labour2 = $_POST['labour2'];
$lifter = $_POST['lifter'];
$lifter_mob = $_POST['lifter_mob'];

$so_trnum = $vcode = $item_code = $boxes = $order_qty = $delivery_date = array();
$i = 0; foreach($_POST['slno'] as $slno){
    $so_trnum[$i] = $_POST['so_trnum'][$slno];
    $vcode[$i] = $_POST['vcode'][$slno];
    $item_code[$i] = $_POST['item_code'][$slno];
    $boxes[$i] = $_POST['boxes'][$slno];
    $order_qty[$i] = $_POST['order_qty'][$slno];
    $sorder_no[$i] = $_POST['sorder_no'][$slno];
    $warehouse[$i] = $_POST['warehouse'][$slno];
    $delivery_date[$i] = date("Y-m-d",strtotime($_POST['delivery_date'][$slno]));
    $i++;
}
$flag = 0;
$active = 1;
$dflag = 0;

$trtype = "routeplan1";
$trlink = "broiler_display_routeplan1.php";

//Generate Transaction No.
$incr = 0; $prefix = $trnum = $fyear = "";
$trno_dt1 = generate_transaction_details($date,"routeplan1","RPS","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

$dsize = sizeof($vcode);
for($i = 0;$i < $dsize;$i++){
    if($boxes[$i] == ""){ $boxes[$i] = 0; }
    if($order_qty[$i] == ""){ $order_qty[$i] = 0; }
    $sql = "INSERT INTO `broiler_routeplan` (`incr`,`prefix`,`trnum`,`date`,`route_no`,`sorder_no`,`warehouse`,`so_date`,`so_trnum`,`vcode`,`item_code`,`boxes`,`order_qty`,`delivery_date`,`driver`,`vehicle`,`company`,`labour1`,`labour2`,`lifter`,`lifter_mob`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$date','$route_no','$sorder_no[$i]','$warehouse[$i]','$so_date','$so_trnum[$i]','$vcode[$i]','$item_code[$i]','$boxes[$i]','$order_qty[$i]','$delivery_date[$i]','$driver','$vehicle','$company','$labour1','$labour2','$lifter','$lifter_mob','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `broiler_sc_saleorder` SET `rp_flag` = '1' WHERE `date` = '$so_date' AND `trnum` = '$so_trnum[$i]' AND `active` = '1' AND `dflag` = '0'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
    }
}
header('location:broiler_display_routeplan1.php?ccid='.$ccid);
?>