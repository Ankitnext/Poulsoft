<?php
//chicken_save_shopinvest1.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$addedtime1 = date('Y-m-d');
$client = $_SESSION['client'];

//Payment Information
$date = date("Y-m-d", strtotime($_POST['pdate']));

$cnames = $cus_amt = $inames = $place = $sv_no = $birds = $quantity = $price = $amount = $remarks = array();
// $i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['cnames'] as $cnamess){ $cnames[$i] = $cnamess; $i++; }
$i = 0; foreach($_POST['cus_amt'] as $cus_amts){ $cus_amt[$i] = $cus_amts; $i++; }
$i = 0; foreach($_POST['inames'] as $inamess){ $inames[$i] = $inamess; $i++; }
$i = 0; foreach($_POST['narr'] as $narrs){ $narr[$i] = $narrs; $i++; }
$active = 1;
$flag = $dflag = 0;

$trtype = "shopinvest1";
$trlink = "chicken_display_shopinvest1.php";



//Save Payments
$dsize = sizeof($cnames);
for($i = 0;$i < $dsize;$i++){

    
    

    //Generate Transaction No.
     $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"shopinvest1","VSSI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    if($cus_amt[$i] == ""){ $cus_amt[$i] = 0; } 
  
    $sql = "INSERT INTO `shop_machine_investment` (incr,prefix,trnum,date,vcode,itemcode,amount,remarks,active,dflag,flag,trtype,trlink,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$cnames[$i]','$inames[$i]','$cus_amt[$i]','$narr[$i]','1','0','0','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:chicken_display_shopinvest1.php?ccid='.$ccid);


