<?php
//broiler_save_hatchentry2.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['hatchentry2'];

//Verify and Add Generator Column
$sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("hatch_entry", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `hatch_entry` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `prate`"; mysqli_query($conn,$sql); }

//Verify and Add Prefix Master
$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` = 'hatch_entry' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $pcount = mysqli_num_rows($query);
if($pcount > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`id`, `format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES (NULL, 'column:flag', 'hatch_entry', 'HTE-', '0', '1:1', '0', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1')"; mysqli_query($conn,$sql); }

//Verify and Add HatchEntry Columns
$sql='SHOW COLUMNS FROM `broiler_hatchentry`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("transfer_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_hatchentry` ADD `transfer_date` DATE NULL DEFAULT NULL COMMENT '' AFTER `setting_date`"; mysqli_query($conn,$sql); }
if(in_array("deathin_hatch_nos", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_hatchentry` ADD `deathin_hatch_nos` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `hatch_per`"; mysqli_query($conn,$sql); }
if(in_array("deathin_hatch_per", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_hatchentry` ADD `deathin_hatch_per` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `deathin_hatch_nos`"; mysqli_query($conn,$sql); }
if(in_array("remarks", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_hatchentry` ADD `remarks` VARCHAR(1500) NULL DEFAULT NULL COMMENT '' AFTER `avg_chick_amount`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql); $icat_iac = $icat_wpac = array();
while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; $icat_wpac[$row['code']] = $row['wpac']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql); $icat_code = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Stock-Wastage'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){  $wastage_coa_code = $row['code']; }

$sector_code = $_POST['warehouse'];
$link_trnum = $_POST['link_trnum'];
$vcode = $_POST['vcode'];
$nof_egg_set = $_POST['nof_egg_set'];
$item_code = $_POST['item_code'];
$avg_price = $_POST['avg_price']; if($avg_price == ""){ $avg_price = 0; }
$avg_amount = $_POST['avg_amount']; if($avg_amount == ""){ $avg_amount = 0; }
$hatch_nos = $_POST['hatch_nos']; if($hatch_nos == ""){ $hatch_nos = 0; }
$hatch_per = $_POST['hatch_per']; if($hatch_per == ""){ $hatch_per = 0; }
$deathin_hatch_nos = $_POST['deathin_hatch_nos']; if($deathin_hatch_nos == ""){ $deathin_hatch_nos = 0; }
$deathin_hatch_per = $_POST['deathin_hatch_per']; if($deathin_hatch_per == ""){ $deathin_hatch_per = 0; }
$culls_nos = $_POST['culls_nos']; if($culls_nos == ""){ $culls_nos = 0; }
$culls_per = $_POST['culls_per']; if($culls_per == ""){ $culls_per = 0; }
$salable_chick_nos = $_POST['salable_chick_nos']; if($salable_chick_nos == ""){ $salable_chick_nos = 0; }
$salable_chick_per = $_POST['salable_chick_per']; if($salable_chick_per == ""){ $salable_chick_per = 0; }
$avg_chick_weight = $_POST['avg_chick_weight']; if($avg_chick_weight == ""){ $avg_chick_weight = 0; }
$total_eggs = $_POST['total_eggs']; if($total_eggs == ""){ $total_eggs = 0; }
$avg_egg_price = $_POST['total_eggs_rate']; if($avg_egg_price == ""){ $avg_egg_price = 0; }
$avg_egg_amount = $_POST['total_eggs_amt']; if($avg_egg_amount == ""){ $avg_egg_amount = 0; }
$total_chicks = $_POST['total_chicks']; if($total_chicks == ""){ $total_chicks = 0; }
$chicks_rate = $_POST['chicks_rate']; if($chicks_rate == ""){ $chicks_rate = 0; }
$chicks_amount = $_POST['chicks_amount']; if($chicks_amount == ""){ $chicks_amount = 0; }
$avg_chick_price = $_POST['avg_chick_price']; if($avg_chick_price == ""){ $avg_chick_price = 0; }
$avg_chick_amount = $_POST['avg_chick_amount']; if($avg_chick_amount == ""){ $avg_chick_amount = 0; }
$date = $setting_date = date("Y-m-d",strtotime($_POST['setting_date']));
$transfer_date = date("Y-m-d",strtotime($_POST['transfer_date']));
$hatch_date = date("Y-m-d",strtotime($_POST['hatch_date']));

$remarks = $_POST['remarks'];
$flag = 0;
$active = 1;
$dflag = 0;

//Generate Invoice transaction number format
$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $hatch_entry = $row['hatch_entry']; } $incr = $hatch_entry + 1;

$sql = "UPDATE `master_generator` SET `hatch_entry` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'hatch_entry' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
$a = 1; $tr_code = $prefix;
for($i = 0;$i <= 16;$i++){
    if($inv_format[$i.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
    else if($inv_format[$i.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
    else if($inv_format[$i.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
    else if($inv_format[$i.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
    else if($inv_format[$i.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
    else if($inv_format[$i.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
    else if($inv_format[$i.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
    else if($inv_format[$i.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
    else if($inv_format[$i.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
    else if($inv_format[$i.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
    else if($inv_format[$i.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
    else if($inv_format[$i.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
    else if($inv_format[$i.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
    else if($inv_format[$i.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
    else if($inv_format[$i.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
    else if($inv_format[$i.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
    else{ }
}
$trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

$sql = "INSERT INTO `broiler_hatchentry` (incr,prefix,trnum,link_trnum,sector_code,setting_date,transfer_date,hatch_date,vcode,setter_no,hatcher_no,item_code,nof_egg_set,hatch,hatch_per,deathin_hatch_nos,deathin_hatch_per,culls,culls_per,saleable_chicks,saleable_chicks_per,avg_chick_weight,avg_egg_price,avg_egg_amount,avg_chick_price,avg_chick_amount,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) 
VALUES ('$incr','$prefix','$trnum','$link_trnum','$sector_code','$setting_date','$transfer_date','$hatch_date','$vcode',NULL,NULL,'$item_code','$nof_egg_set','$hatch_nos','$hatch_per','$deathin_hatch_nos','$deathin_hatch_per','$culls_nos','$culls_per','$salable_chick_nos','$salable_chick_per','$avg_chick_weight','$avg_egg_price','$avg_egg_amount','$avg_chick_price','$avg_chick_amount','$remarks','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
else{
    $sql1 = "UPDATE `broiler_tray_settings` SET `hatch_flag` = '1' WHERE `trnum` = '$link_trnum'"; mysqli_query($conn,$sql1);
    $coa_Cr = $icat_wpac[$icat_code[$item_code]];
    $coa_Dr = $icat_iac[$icat_code[$chick_code]];

    /*Stock Out from WIP to Chick Production */
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,etype,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$coa_Cr','$hatch_date','$trnum','$item_code','$nof_egg_set','$avg_egg_price','$avg_egg_amount','$sector_code','','','','$remarks','HatchEntry','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }

    /* Stock in as Chick Production */
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,etype,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$coa_Dr','$hatch_date','$trnum','$chick_code','$salable_chick_nos','$avg_chick_price','$avg_chick_amount','$sector_code','','','','$remarks','HatchEntry','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); }

    /* Stock Wastage */
    $total_wastage_qty = (float)$deathin_hatch_nos + (float)$culls_nos;
    if((float)$total_wastage_qty > 0){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,etype,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$wastage_coa_code','$hatch_date','$trnum','$item_code','$total_wastage_qty','0','0','$sector_code','','','','$remarks','HatchEntry','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); }
    }
}

$eincr = $_POST['eincr'];
if($eincr >= 0){
    for($i = 0;$i <= $eincr;$i++){
        $exp_type = $_POST['exp_type'.$i];
        $exp_code = $_POST['exp_code'.$i];
        $exp_rate = $_POST['exp_value'.$i];
        $exp_amt = $_POST['exp_amount'.$i];
        if($exp_rate == ""){ $exp_rate = 0; }
        if($exp_amt == ""){ $exp_amt = 0; }
        /*Hatchery Expense */
        if($exp_code != "select"){
            $from_post = "INSERT INTO `broiler_hatchentry_expenses` (link_trnum,date,exp_type,exp_code,exp_rate,exp_amt,sector,flag,active,dflag) 
            VALUES ('$trnum','$hatch_date','$exp_type','$exp_code','$exp_rate','$exp_amt','$sector_code','$flag','$active','$dflag')";
            if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); }

            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,etype,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$exp_code','$hatch_date','$trnum','','0','$exp_rate','$exp_amt','$sector_code','','','','$remarks','HatchEntry','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); }
        }
    }
}
$vincr = $_POST['vincr'];
if($vincr >= 0){
    $i = 0; foreach($_POST['vaccine_code'] as $vcode){ $vaccine_code[$i] = $vcode; $i++; }
    $i = 0; foreach($_POST['vaccine_qty'] as $vqty){ if($vqty == ""){ $vqty = 0; } $vaccine_qty[$i] = $vqty; $i++; }
    $i = 0; foreach($_POST['vaccine_rate'] as $vrate){ if($vrate == ""){ $vrate = 0; } $vaccine_rate[$i] = $vrate; $i++; }
    $i = 0; foreach($_POST['vaccine_amount'] as $vamnt){ if($vamnt == ""){ $vamnt = 0; } $vaccine_amount[$i] = $vamnt; $i++; }
    for($i = 0;$i <= $vincr;$i++){
        /*Vaccine Consumption Expense */
        if($vaccine_code[$i] != "select"){
            $from_post = "INSERT INTO `broiler_hatchery_consumed` (link_trnum,date,sector_code,item_code,quantity,rate,amount,flag,active,dflag) 
            VALUES ('$trnum','$hatch_date','$sector_code','$vaccine_code[$i]','$vaccine_qty[$i]','$vaccine_rate[$i]','$vaccine_amount[$i]','$flag','$active','$dflag')";
            if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); }

            $item_acc = $icat_iac[$icat_code[$vaccine_code[$i]]];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,etype,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$item_acc','$hatch_date','$trnum','$vaccine_code[$i]','$vaccine_qty[$i]','$vaccine_rate[$i]','$vaccine_amount[$i]','$sector_code','','','','$remarks','HatchEntry','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 8:-".mysqli_error($conn)); }
        }
    }
}

$rejection_type = $reject_egg_nos = $reject_egg_per = $reject_egg_stk = array();
$i = 0; foreach($_POST['rejection_type'] as $rejection_types){ $rejection_type[$i] = $rejection_types; $i++; }
$i = 0; foreach($_POST['reject_egg_nos'] as $reject_egg_noss){ $reject_egg_nos[$i] = $reject_egg_noss; $i++; }
$i = 0; foreach($_POST['reject_egg_per'] as $reject_egg_pers){ $reject_egg_per[$i] = $reject_egg_pers; $i++; }
$i = 0; foreach($_POST['stk_val'] as $stk_vals){ $reject_egg_stk[$i] = $stk_vals; $i++; }
$rsize = sizeof($rejection_type);
for($i = 0;$i < $rsize;$i++){
    if($rejection_type[$i] != "select"){
        if($reject_egg_stk[$i] == "1"){ $coa_Dr = $icat_iac[$icat_code[$rejection_type[$i]]]; }
        else{ $coa_Dr = $wastage_coa_code; }
        
        $from_post = "INSERT INTO `broiler_hatchery_rejectitems` (link_trnum,rejection_type,reject_egg_nos,reject_egg_per,reject_egg_stk,flag,active,dflag) 
        VALUES ('$trnum','$rejection_type[$i]','$reject_egg_nos[$i]','$reject_egg_per[$i]','$reject_egg_stk[$i]','$flag','$active','$dflag')";
        if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,etype,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_Dr','$hatch_date','$trnum','$rejection_type[$i]','$reject_egg_nos[$i]','0','0','$sector_code','','','','$remarks','HatchEntry-Rejection Items','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 8:-".mysqli_error($conn)); }
    }
}
header('location:broiler_display_hatchentry2.php?ccid='.$ccid);
?>