<?php
//chicken_modify_expense1.php
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

// $dcno = $fsector = $icode = $jalqty = $birds = $icode = $qty = $nweight = $price = $tsector = $pdate = $old_trnum = $rndoff_chk = $roundoff = $finaltotal = $remarks = array();
// $i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
// $i = 0; foreach($_POST['trnum'] as $trnums){ $old_trnum[$i] = $trnums; $i++; }
// $i = 0; foreach($_POST['pdate'] as $pdates){ $pdate[$i] = date("Y-m-d", strtotime($pdates)); $i++; }
// $i = 0; foreach($_POST['fsector'] as $fsectors){ $fsector[$i] = $fsectors; $i++; }
// $i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
// $i = 0; foreach($_POST['jalqty'] as $jalqtys){ $jalqty[$i] = $jalqtys; $i++; }
// $i = 0; foreach($_POST['birdqty'] as $birdqtys){ $birdqty[$i] = $birdqtys; $i++; }
// $i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
// $i = 0; foreach($_POST['qty'] as $qtys){ $qty[$i] = $qtys; $i++; }
// $i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
// $i = 0; foreach($_POST['tsector'] as $tsectors){ $tsector[$i] = $tsectors; $i++; }
// $i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$dcno = $_POST['dcno'];
$trnums = $_POST['trnum'];
$pdate = date("Y-m-d", strtotime($_POST['pdate']));
$sector = $_POST['sector'];
$fcoa = $_POST['fcoa'];
$tcoa = $_POST['tcoa'];
$amount = $_POST['amount'];
$remarks = $_POST['remarks'];


$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "expense1";
$trlink = "chicken_display_expense1.php";
$trip_trnum = $_POST['idvalue'];
//echo $trip_trnum;
//Update deletion flag via Trip No.
// $trno_list = implode("','",$old_trnum);
// $sql = "UPDATE `item_stocktransfers` SET `flag` = '0',`active` = '0',`tdflag` = '2',`pdflag` = '2' WHERE `trnum` NOT IN ('$trno_list') AND `trnum` = '$trip_trnum' AND `flag` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
// mysqli_query($conn,$sql);

//Save Purchase


    if($amount == ""){ $amount = 0; }
   
    $sql = "UPDATE `acc_vouchers` SET `date` = '$pdate',`warehouse` = '$sector',`fcoa` = '$fcoa',`tcoa` = '$tcoa',`amount`='$amount',`dcno`='$dcno',`remarks`='$remarks' WHERE `trnum` = '$trnums' ";

    //   $sql = "INSERT INTO `item_stocktransfers` (incr,prefix,trnum,date,fromwarehouse,code,jals,birds,quantity,price,towarehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client) 
    // VALUES ('$incr','$prefix','$trnum','$pdate[$i]','$fsector[$i]','$icode[$i]','$jalqty[$i]','$birdqty[$i]','$qty[$i]','$price[$i]','$tsector[$i]','$dcno[$i]','$remarks[$i]','0','1','$addedemp','$addedtime','0','0','$client')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }




header('location:chicken_display_expense1.php?ccid='.$ccid);

