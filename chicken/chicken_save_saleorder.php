<?php
//chicken_save_saleorder.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$addedtime1 = date('Y-m-d');
$client = $_SESSION['client'];

//Payment Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$wcodes = $_POST['wcodes'];
$itemcode = $_POST['scat'];
$cnames = $cus_qty = $snames = $place = $sv_no = $birds = $quantity = $price = $amount = $remarks = array();
// $i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['cnames'] as $cnamess){ $cnames[$i] = $cnamess; $i++; }
$i = 0; foreach($_POST['cus_qty'] as $cus_qtys){ $cus_qty[$i] = $cus_qtys; $i++; }
$i = 0; foreach($_POST['snames'] as $snamess){ $snames[$i] = $snamess; $i++; }
$i = 0; foreach($_POST['place'] as $places){ $place[$i] = $places; $i++; }
$i = 0; foreach($_POST['sv_no'] as $sv_nos){ $sv_no[$i] = $sv_nos; $i++; }
$i = 0; foreach($_POST['v_no'] as $v_nos){ $v_no[$i] = $v_nos; $i++; }
$i = 0; foreach($_POST['narr'] as $narrs){ $narr[$i] = $narrs; $i++; }
$active = 1;
$flag = $dflag = 0;

$trtype = "saleorder";
$trlink = "chicken_display_saleorder.php";



//Save Payments
$dsize = sizeof($cnames);
for($i = 0;$i < $dsize;$i++){

    
    $sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }

    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){  $so = $row['salesorder']; }  $incr = $so + $i+1;

    $sql = "UPDATE `master_generator` SET `salesorder` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    $incr = $so; 

    //Generate Transaction No.
     $incr = $incr + 1; 
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $trnum = "SO-".$fprefix."".$incr;

    if($cus_qty[$i] == ""){ $cus_qty[$i] = 0; } 
  
    $sql = "INSERT INTO `salesorder` (incr,prefix,trnum,date,ccode,itemcode,twt,vehicleno,supplier,place,supervisor,warehouse,remarks,mflag,active,dflag,isDelete,flag,addedemp,addeddate,updatetime) 
    VALUES ('$incr','SO','$trnum','$date','$cnames[$i]','$itemcode','$cus_qty[$i]','$v_no[$i]','$snames[$i]','$place[$i]','$sv_no[$i]','$wcodes','$narr[$i]','0','1','0','0','0','$addedemp','$addedtime1','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:chicken_display_saleorder.php?ccid='.$ccid);


