<?php
//chicken_save_expense1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";


 $vtype = $_POST['pname'];

$dcno = $fcoa = $icode = $tcoa = $amount = $sector = $qty = $nweight = $price = $tsector = $pdate = $gtamtinwords = $rndoff_chk = $roundoff = $finaltotal = $remarks = array();
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
// foreach($_POST['pdate'] as $pdate){ $pdates[$i] = date("Y-m-d",strtotime($pdate)); $date = date("Y-m-d",strtotime($pdate)); }
$i = 0; foreach($_POST['pdate'] as $pdates){ $pdate[$i] = date("Y-m-d", strtotime($pdates)); $i++; $date = date("Y-m-d",strtotime($pdates)); }
$i = 0; foreach($_POST['fcoa'] as $fcoas){ $fcoa[$i] = $fcoas; $i++; }
$i = 0; foreach($_POST['tcoa'] as $tcoas){ $tcoa[$i] = $tcoas; $i++; }
$i = 0; foreach($_POST['gtamtinwords'] as $gtamtinwordss){ $gtamtinwords[$i] = $gtamtinwordss; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i] = $sectors; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }





$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "expense1";
$trlink = "chicken_display_expense1.php";

// $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
// $trno_dt1 = generate_transaction_details($pdate[$i],"expense1","STI","generate",$_SESSION['dbase']);
// $trno_dt2 = explode("@",$trno_dt1);
// $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

//Save Purchase
$dsize = sizeof($amount);
for($i = 0;$i < $dsize;$i++){

    if($amount[$i] == ""){ $amount[$i] = 0; }

    $sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$pdates[$i]' AND `tdate` >= '$pdates[$i]' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }
    if($vtype == "PV"){
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pvouchers = $row['pvouchers']; } $incr = $pvouchers + 1;
        $sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = "PV-".$fprefix."".$incr; $prefix = "PV";
    }
    else if($vtype == "RV"){
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $rvouchers = $row['rvouchers']; } $incr = $rvouchers + 1;
        $sql = "UPDATE `master_generator` SET `rvouchers` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = "RV-".$fprefix."".$incr; $prefix = "RV";
    }
    else if($vtype == "JV"){
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $jvouchers = $row['jvouchers']; } $incr = $jvouchers + 1;
        $sql = "UPDATE `master_generator` SET `jvouchers` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $code = "JV-".$fprefix."".$incr; $prefix = "JV";
    }
    else {
        $code = "Invalid";
    }
    if($code == "Invalid"){}
    else {
        $sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,date,fcoa,tcoa,amount,amtinwords,warehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
        VALUES ('$incr','$prefix','$code','$pdate[$i]','$fcoa[$i]','$tcoa[$i]','$amount[$i]','$amtinword[$i]','$sector[$i]','$dcno[$i]','$remarks[$i]','0','1','$addedemp','$addedtime','0','0','$client')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    
//     $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
//     $trno_dt1 = generate_transaction_details($pdate[$i],"expense1","EXP","generate",$_SESSION['dbase']);
//     $trno_dt2 = explode("@",$trno_dt1);
//     $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

//    $sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,date,fcoa,tcoa,amount,amtinwords,warehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
//     VALUES ('$incr','$prefix','$trnum','$pdate[$i]','$fcoa[$i]','$tcoa[$i]','$amount[$i]','$gtamtinwords[$i]','$sector[$i]','$dcno[$i]','$remarks[$i]','0','1','$addedemp','$addedtime','0','0','$client')";
//     if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    // echo "Not yet";
    // $sql = "INSERT INTO `acc_vouchers` (incr,prefix,trnum,date,fromwarehouse,code,jals,birds,quantity,price,towarehouse,dcno,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client) 
    // VALUES ('$incr','$prefix','$trnum','$pdate[$i]','$fsector[$i]','$icode[$i]','$jalqty[$i]','$birdqty[$i]','$qty[$i]','$price[$i]','$tsector[$i]','$dcno[$i]','$remarks[$i]','1','1','$addedemp','$addedtime','0','0','$client')";
    // if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//}

header('location:chicken_display_expense1.php?ccid='.$ccid);

