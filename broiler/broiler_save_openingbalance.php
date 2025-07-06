<?php
//broiler_save_openingbalance.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['openingbalance'];

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_pvac[$row['code']] = $row['pvac'];
    $icat_pdac[$row['code']] = $row['pdac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE '%Opening Balance Equity%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $opn_bal_code = $row['code']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$amount = array();
$date = date("Y-m-d",strtotime($_POST['date']));
$type = $_POST['type'];
$sector_code = $_POST['sector_code'];
if($type == "Item"){
    $i = 0; foreach($_POST['type_code'] as $type_codes){ $type_code[$i] = $type_codes; $i++; }
    $i = 0; foreach($_POST['quantity'] as $quantitys){ if($quantitys == 0 || $quantitys == "" || $quantitys == "0.00" || $quantitys <= 0){ $quantitys = "0.00"; } $quantity[$i] = $quantitys; $i++; }
    $i = 0; foreach($_POST['rate'] as $rates){ if($rates == 0 || $rates == "" || $rates == "0.00" || $rates <= 0){ $ratea = "0.00"; } $rate[$i] = $rates; $i++; }
    $i = 0; foreach($_POST['amount'] as $amounts){ if($amounts == 0 || $amounts == "" || $amounts == "0.00" || $amounts <= 0){ $amounts = "0.00"; } $amount[$i] = $amounts; $i++; }
  //  $i = 0; foreach($_POST['sector_code'] as $sector_codes){ $sector_code[$i] = $sector_codes; $i++; }
    $i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
    $flag = 0;
    $active = 1;
    $dflag = 0;

    $dsize = sizeof($type_code);
    for($i = 0;$i < $dsize;$i++){
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $openbal = $row['openbal']; } $incr = $openbal + 1;

        $sql = "UPDATE `master_generator` SET `openbal` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'openbal' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
        $a = 1; $tr_code = $prefix;
        for($j = 0;$j <= 16;$j++){
            if($inv_format[$j.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
            else if($inv_format[$j.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
            else if($inv_format[$j.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
            else if($inv_format[$j.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
            else if($inv_format[$j.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
            else if($inv_format[$j.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
            else if($inv_format[$j.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
            else if($inv_format[$j.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
            else if($inv_format[$j.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
            else if($inv_format[$j.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
            else if($inv_format[$j.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
            else if($inv_format[$j.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
            else if($inv_format[$j.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
            else if($inv_format[$j.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
            else if($inv_format[$j.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
            else if($inv_format[$j.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
            else{ }
        }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

        $sql = "INSERT INTO `broiler_openings` (incr,prefix,trnum,type,date,type_code,quantity,rate,amount,sector_code,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$trnum','$type','$date','$type_code[$i]','$quantity[$i]','$rate[$i]','$amount[$i]','$sector_code','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
        else {
            $coa_Cr = $opn_bal_code;
            $coa_Dr = $icat_iac[$icat_code[$type_code[$i]]];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$trnum','$type_code[$i]','$quantity[$i]','$rate[$i]','$amount[$i]','$sector_code','','$remarks[$i]','0','OpeningBalance','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('DR','$coa_Dr','$date','$trnum','$type_code[$i]','$quantity[$i]','$rate[$i]','$amount[$i]','$sector_code','','$remarks[$i]','0','OpeningBalance','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
    }
    header('location:broiler_display_openingbalance.php?ccid='.$ccid);
}
else{
    header('location:broiler_display_openingbalance.php?ccid='.$ccid);
}
?>