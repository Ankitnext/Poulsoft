<?php
//chicken_save_stocktransfer1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Purchase Information
// $date = date("Y-m-d", strtotime($_POST['date']));
// $d = date("d",strtotime($date));
// $m = date("m",strtotime($date));
// $y = date("Y-m-d",strtotime($date));
$warehouse = $_POST['warehouse'];
$tcds_per = $_POST['tcds_per'];

$dcno = $fsector = $icode = $jalqty = $birds = $icode = $qty = $nweight = $price = $tsector = $pdate = $tcds_amt = $rndoff_chk = $roundoff = $finaltotal = $remarks = array();
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
$i = 0; foreach($_POST['pdate'] as $pdates){ $pdate[$i] = date("Y-m-d", strtotime($pdates)); $i++; }
$i = 0; foreach($_POST['fsector'] as $fsectors){ $fsector[$i] = $fsectors; $i++; }
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['jalqty'] as $jalqtys){ $jalqty[$i] = $jalqtys; $i++; }
$i = 0; foreach($_POST['birdqty'] as $birdqtys){ $birdqty[$i] = $birdqtys; $i++; }
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['qty'] as $qtys){ $qty[$i] = $qtys; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['tsector'] as $tsectors){ $tsector[$i] = $tsectors; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }





$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "stocktransfer1";
$trlink = "chicken_display_stocktransfer1.php";

// $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
// $trno_dt1 = generate_transaction_details($pdate[$i],"stocktransfer1","STI","generate",$_SESSION['dbase']);
// $trno_dt2 = explode("@",$trno_dt1);
// $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

//Save Purchase
$dsize = sizeof($icode);
for($i = 0;$i < $dsize;$i++){
    if($jalqty[$i] == ""){ $jalqty[$i] = 0; }
    if($birdqty[$i] == ""){ $birdqty[$i] = 0; }
    if($qty[$i] == ""){ $qty[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
  
   
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($pdate[$i],"stocktransfer1","STI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    // $sql = "INSERT INTO `item_stocktransfers` (date,incr,d,m,y,fy,invoice,link_trnum,bookinvoice,vendorcode,itemcode,jals,birds,totalweight,emptyweight,netweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,client,trtype,trlink) 
    // VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$trip_trnum','$bookinvoice[$i]','$vcode[$i]','$icode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt[$i]','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff[$i]','$finaltotal[$i]','$finaltotal[$i]',NULL,'$warehouse','$flag','$active','$authorization','$tdflag','$pdflag','$driver','$vehicle',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks[$i]','$addedemp','$addedtime','$client','$trtype','$trlink')";
    // if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    $sql = "INSERT INTO `item_stocktransfers` (incr,prefix,trnum,date,fromwarehouse,code,jals,birds,quantity,price,towarehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client) 
    VALUES ('$incr','$prefix','$trnum','$pdate[$i]','$fsector[$i]','$icode[$i]','$jalqty[$i]','$birdqty[$i]','$qty[$i]','$price[$i]','$tsector[$i]','$dcno[$i]','$remarks[$i]','1','1','$addedemp','$addedtime','0','0','$client')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//}

header('location:chicken_display_stocktransfer1.php?ccid='.$ccid);

