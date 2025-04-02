<?php
//broiler_save_multisales2
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['multisales2'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("mnu_rf_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `mnu_rf_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Manual Round-Off' AFTER `round_off`"; mysqli_query($conn,$sql); }

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SalewithRCTAutoWapp:broiler_display_multisales2.php'";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); $swrct_wapp = 0; $field_value = "1,1,1,1"; /*SWRCT: Sale with Receipt*/
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $swrct_wapp = $row['flag']; $field_value = $row['field_value']; } }
if($swrct_wapp == ""){ $swrct_wapp = 0; }

$sql = "SELECT * FROM `acc_modes`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mode_name[$row['code']] = $row['description']; }

$today = date("Y-m-d");
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

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
$sql = "SELECT * FROM `main_groups`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $control_acc_group[$row['code']] = $row['cus_controller_code']; }
$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `acc_modes` WHERE `description` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mode = $row['code']; }
$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Cash In Hand' AND `ctype` LIKE 'Cash' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cash_code = $row['code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_cmode[$row['code']] = $row['ctype']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('MultipleSale-Receipt2') AND (`user_access` LIKE '%$user_code%' OR `user_access` LIKE 'all') AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $rct2_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Sale transaction' AND `field_function` LIKE 'send WhatsApp to Customer' AND (`user_access` LIKE '%$user_code%' OR `user_access` LIKE 'all')";
$query = mysqli_query($conn,$sql); $send_cuswapp_flag = mysqli_num_rows($query);
if($send_cuswapp_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $send_cuswapp_flag = $row['flag']; } if($send_cuswapp_flag == "" || $send_cuswapp_flag == "0"){ $send_cuswapp_flag = 0; } else{ $send_cuswapp_flag = 1; }
}
else{
    $send_cuswapp_flag = 1;
}

$date=  $vcode= $billno= $warehouse= $birds= $rcd_qty= $rate= $item_tamt= $round_off= $finl_amt= $mnu_rf_flag= $supervisor_code= $vehicle_code= $driver_code= $remarks= $receipt= $avg_wt= array();

$i = 0; foreach($_POST['date'] as $dates){ $date[$i]= date("Y-m-d",strtotime($dates)); $i++; }
if($_POST['sale_type'] == "CusMBSale"){ $i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i]= $vcodes; $i++; } }
$sale_type = $_POST['sale_type']; 
$i = 0; foreach($_POST['billno'] as $billnos){ $billno[$i]= $billnos; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i]= $warehouses; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i]= $birdss; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['avg_wt'] as $avg_wts){ $avg_wt[$i]= $avg_wts; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['round_off'] as $round_offs){ $round_off[$i]= $round_offs; $i++; }
$i = 0; foreach($_POST['finl_amt'] as $finl_amts){ $finl_amt[$i]= $finl_amts; $i++; }
$i = 0; foreach($_POST['mnu_rf_flag'] as $mnu_rf_flags){ $mnu_rf_flag[$i]= $mnu_rf_flags; $i++; }
$i = 0; foreach($_POST['supervisor_code'] as $supervisor_codes){ $supervisor_code[$i]= $supervisor_codes; $i++; }
$i = 0; foreach($_POST['vehicle_code'] as $vehicle_codes){ $vehicle_code[$i]= $vehicle_codes; $i++; }
$i = 0; foreach($_POST['driver_code'] as $driver_codes){ $driver_code[$i]= $driver_codes; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i]= $remarkss; $i++; }
$i = 0; foreach($_POST['receipt'] as $receipts){ $receipt[$i]= $receipts; $i++; }
if($rct2_flag > 0){
    $i = 0; foreach($_POST['coa_code2'] as $coa_code2s){ $coa_code2[$i] = $coa_code2s; $i++; }
    $i = 0; foreach($_POST['receipt2'] as $receipt2s){ $receipt2[$i] = $receipt2s; $i++; }
}
$vtype = "Customer";
$flag = 0;
$active = 1;
$dflag = 0;

$dsize = sizeof($warehouse);
for($i = 0;$i < $dsize;$i++){
    //Generate Invoice transaction number format
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
    
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; } $incr = $sales + 1;
    
    $sql = "UPDATE `master_generator` SET `sales` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'sales' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
    $a = 1; $tr_code = $prefix;
    for($j = 0;$j <= 16;$j++){
        if(!empty($inv_format[$j.":".$a])){
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
    }
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }

    if($birds[$i] == "" || $birds[$i] == NULL || $birds[$i] == 0 || $birds[$i] == "0.00"){ $birds[$i] = "0.00"; }
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = "0.00"; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = "0.00"; }
    if($item_tamt[$i] == "" || $item_tamt[$i] == NULL || $item_tamt[$i] == 0 || $item_tamt[$i] == "0.00"){ $item_tamt[$i] = "0.00"; }
    if($round_off[$i] == "" || $round_off[$i] == NULL || $round_off[$i] == 0 || $round_off[$i] == "0.00"){ $round_off[$i] = "0.00"; }
    if($finl_amt[$i] == "" || $finl_amt[$i] == NULL || $finl_amt[$i] == 0 || $finl_amt[$i] == "0.00"){ $finl_amt[$i] = "0.00"; }

    $from_post = "INSERT INTO `broiler_sales` (incr,prefix,trnum,date,vcode,billno,icode,birds,rcd_qty,rate,item_tamt,round_off,mnu_rf_flag,finl_amt,bal_qty,bal_amt,remarks,warehouse,farm_batch,supervisor_code,vehicle_code,driver_code,active,flag,dflag,sale_type,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date[$i]','$vcode[$i]','$billno[$i]','$bird_code','$birds[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$round_off[$i]','$mnu_rf_flag[$i]','$finl_amt[$i]','$rcd_qty[$i]','$finl_amt[$i]','$remarks[$i]','$warehouse[$i]','$farm_batch','$supervisor_code[$i]','$vehicle_code[$i]','$driver_code[$i]','$active','$flag','$dflag','$sale_type','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$bird_code]]; $cus_acc = $control_acc_group[$contact_group[$vcode[$i]]];
        $cogs_acc = $icat_cogsac[$icat_code[$bird_code]];
        $sale_acc = $icat_sac[$icat_code[$bird_code]];
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$item_acc','$date[$i]','$billno[$i]','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','0','0','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cogs_acc','$date[$i]','$billno[$i]','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','0','0','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cus_acc','$date[$i]','$billno[$i]','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','$rate[$i]','$finl_amt[$i]','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$sale_acc','$date[$i]','$billno[$i]','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','$rate[$i]','$finl_amt[$i]','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 5:-".mysqli_error($conn)); } else{ }
    }

    
    if($receipt[$i] == "" || $receipt[$i] == NULL || $receipt[$i] == 0 || $receipt[$i] == "0.00" || $receipt[$i] == "0" || $receipt[$i] == 0.00){ $receipt[$i] = "0.00"; }
    else{
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
    
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cus_receipts = $row['cus_receipts']; } $incr = $cus_receipts + 1;
    
        $sql = "UPDATE `master_generator` SET `cus_receipts` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'cus_receipts' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
        $a = 1; $tr_code = $prefix;
        for($j = 0;$j <= 16;$j++){
            if(!empty($inv_format[$j.":".$a])){
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
        }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }
    
        $sql = "INSERT INTO `broiler_receipts` (incr,prefix,trnum,date,ccode,docno,mode,method,amount,amtinwords,vtype,warehouse,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$trnum','$date[$i]','$vcode[$i]','$billno[$i]','$mode','$cash_code','$receipt[$i]','$amtinwords','$vtype','$warehouse[$i]','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error 6:-".mysqli_error($conn)); }
        else {
            $coa_Cr = $control_acc_group[$contact_group[$vcode[$i]]];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date[$i]','$vcode[$i]','$billno[$i]','$trnum','0.00','0.00','$receipt[$i]','$warehouse[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); }
            else{
                $to_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('DR','$cash_code','$date[$i]','$vcode[$i]','$billno[$i]','$trnum','0.00','0.00','$receipt[$i]','$warehouse[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$to_post)){ die("Error 8:-".mysqli_error($conn)); } else{ }
            }
        }
    }
    if($rct2_flag > 0){
        if($receipt2[$i] == "" || $receipt2[$i] == NULL || $receipt2[$i] == 0 || $receipt2[$i] == "0.00" || $receipt2[$i] == "0" || $receipt2[$i] == 0.00){ $receipt2[$i] = 0; }
        else{
            //Generate Invoice transaction number format
            $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
        
            $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $cus_receipts = $row['cus_receipts']; } $incr = $cus_receipts + 1;
        
            $sql = "UPDATE `master_generator` SET `cus_receipts` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
        
            $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'cus_receipts' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
            $a = 1; $tr_code = $prefix;
            for($j = 0;$j <= 16;$j++){
                if(!empty($inv_format[$j.":".$a])){
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
            }
            if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
            $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }
        
            if($coa_cmode[$coa_code2[$i]] == "Cash"){ $mode2 = $mode; } else{ $mode2 = "MOD-002"; }
            $sql = "INSERT INTO `broiler_receipts` (incr,prefix,trnum,date,ccode,docno,mode,method,amount,amtinwords,vtype,warehouse,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$trnum','$date[$i]','$vcode[$i]','$billno[$i]','$mode2','$coa_code2[$i]','$receipt2[$i]','$amtinwords','$vtype','$warehouse[$i]','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$sql)){ die("Error 6:-".mysqli_error($conn)); }
            else {
                $coa_Cr = $control_acc_group[$contact_group[$vcode[$i]]];
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('CR','$coa_Cr','$date[$i]','$vcode[$i]','$billno[$i]','$trnum','0.00','0.00','$receipt2[$i]','$warehouse[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); }
                else{
                    $to_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
                    VALUES ('DR','$cash_code','$date[$i]','$vcode[$i]','$billno[$i]','$trnum','0.00','0.00','$receipt2[$i]','$warehouse[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$to_post)){ die("Error 8:-".mysqli_error($conn)); } else{ }
                }
            }
        }
    }

    if($swrct_wapp > 0 && $_POST['sale_type'] == "CusMBSale"){
        $mobile_count = 0; $mobile_no_array = array();
        $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-Sales' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $instance_id = $row['sms_key'];
            $access_token = $row['msg_key'];
            $url_id = $row['url_id'];
            if(!empty($row['numers'])){
                $m1 = explode(",",$row['numers']);
                if(sizeof($m1) > 1){ foreach($m1 as $fm1){ $mobile_count++; $mobile_no_array[$mobile_count] = $fm1; } }
                else{ $mobile_count++; $mobile_no_array[$mobile_count] = $row['numers']; }
            }
            else{ }
        }
        
        $sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }

        //Customer Balance Fetch
        $balance_amount = 0; $balance_amount = get_customer_balance($vcode[$i]);
        if($balance_amount == "" || $balance_amount == 0 || $balance_amount == "0.00"){ $balance_amount = 0; }

        $sql = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$vcode[$i]'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $m1 = explode(",",$row['mobile1']);
            if($send_cuswapp_flag == 1){
                if(sizeof($m1) > 1){ foreach($m1 as $fm1){ $mobile_count++; $mobile_no_array[$mobile_count] = $fm1; } }
                else{ $mobile_count++; $mobile_no_array[$mobile_count] = $row['mobile1']; }
            }
            $cname = $row['name'];
        }

        //Field Values
        $fval_list = array();
        if($field_value != ""){ $fval_list = explode(",",$field_value); }
        $wapp_birds_flag = $fval_list[0]; if($wapp_birds_flag == ""){ $wapp_birds_flag = 0; }
        $wapp_weigt_flag = $fval_list[1]; if($wapp_weigt_flag == ""){ $wapp_weigt_flag = 0; }
        $wapp_avgwt_flag = $fval_list[2]; if($wapp_avgwt_flag == ""){ $wapp_avgwt_flag = 0; }
        $wapp_price_flag = $fval_list[3]; if($wapp_price_flag == ""){ $wapp_price_flag = 0; }

        $item_details = "";
        if((int)$wapp_birds_flag == 1){
            $item_details .= ",%0D%0ABirds: ".$birds[$i];
        }
        if((int)$wapp_weigt_flag == 1){
            $item_details .= ",%0D%0AWeight: ".number_format_ind($rcd_qty[$i]);
        }
        if((int)$wapp_avgwt_flag == 1){
            $item_details .= ",%0D%0AAvg Wt: ".number_format_ind($avg_wt[$i]);
        }
        if((int)$wapp_price_flag == 1){
            $item_details .= ",%0D%0APrice: ".number_format_ind($rate[$i]);
        }
        $message = "";
        if($rct2_flag > 0 && $receipt2[$i] > 0){
            if($swrct_wapp == 1){
                $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date[$i])).",%0D%0ADC No: ".$billno[$i].",%0D%0AVehicle No: ".$vehicle_code[$i]."".$item_details.",%0D%0ASale Amount: ".number_format_ind($finl_amt[$i]).",%0D%0AReceipt Amount: ".number_format_ind($receipt[$i]).",%0D%0AVia: ".$mode_name[$mode].",%0D%0AReceipt Amount: ".number_format_ind($receipt2[$i]).",%0D%0AVia: ".$mode_name[$mode2].",%0D%0ABalance: ".number_format_ind($balance_amount).",%0D%0ARemarks: ".$remarks[$i].".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
            }
            else if($swrct_wapp == 2){
                $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date[$i])).",%0D%0ADC No: ".$billno[$i].",%0D%0AVehicle No: ".$vehicle_code[$i]."".$item_details.",%0D%0ASale Amount: ".number_format_ind($finl_amt[$i]).",%0D%0AReceipt Amount: ".number_format_ind($receipt[$i]).",%0D%0AVia: ".$mode_name[$mode].",%0D%0AReceipt Amount: ".number_format_ind($receipt2[$i]).",%0D%0AVia: ".$mode_name[$mode2].",%0D%0ARemarks: ".$remarks[$i].".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
            }
            else{ }
        }
        else{
            if($swrct_wapp == 1){
                $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date[$i])).",%0D%0ADC No: ".$billno[$i].",%0D%0AVehicle No: ".$vehicle_code[$i]."".$item_details.",%0D%0ASale Amount: ".number_format_ind($finl_amt[$i]).",%0D%0AReceipt Amount: ".number_format_ind($receipt[$i]).",%0D%0AVia: ".$mode_name[$mode].",%0D%0ABalance: ".number_format_ind($balance_amount).",%0D%0ARemarks: ".$remarks[$i].".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
            }
            else if($swrct_wapp == 2){
                $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date[$i])).",%0D%0ADC No: ".$billno[$i].",%0D%0AVehicle No: ".$vehicle_code[$i]."".$item_details.",%0D%0ASale Amount: ".number_format_ind($finl_amt[$i]).",%0D%0AReceipt Amount: ".number_format_ind($receipt[$i]).",%0D%0AVia: ".$mode_name[$mode].",%0D%0ARemarks: ".$remarks[$i].".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
            }
            else{ }
        }
        

        if($message != ""){
            $message = str_replace(" ","+",$message);
            for($j = 1;$j <= $mobile_count;$j++){
                if(!empty($mobile_no_array[$j])){
                        
                    $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $curlopt_url = $row['curlopt_url']; }

                    $wapp_date = date("Y-m-d");
                    $ccode = $vcode[$i];
                    $number = "91".$mobile_no_array[$j]; $type = "text";
        
                    if((int)$url_id == 3){ $msg_info = $curlopt_url.''.$instance_id.'/messages/chat?token='.$access_token.'&to='.$number.'&body='.$message; }
                    else{ $msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token; }
                            
                    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                    $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                    if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                    $wapp_code = "WAPP-".$incr_wapp;

                    $database = $_SESSION['dbase'];
                    $trtype = "BB-AutoSWRCTWapp";
                    //$trnum = "";
                    $vendor = $ccode;
                    $mobile = $number;
                    $msg_trnum = $wapp_code;
                    $msg_type = "WAPP";
                    $msg_project = "BTS";
                    $status = "CREATED";
                    $trlink = $_SERVER['REQUEST_URI'];
                    $sql = "INSERT INTO `master_broiler_pendingmessages` (`database`,`url_id`,`trtype`,`trnum`,`vendor`,`mobile`,`msg_trnum`,`msg_type`,`msg_info`,`msg_project`,`status`,`trlink`,`addedemp`,`addedtime`,`updatedtime`)
                    VALUES ('$database','$url_id','$trtype','$trnum','$vendor','$mobile','$msg_trnum','$msg_type','$msg_info','$msg_project','$status','$trlink','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conns,$sql)) { } else{ }
                }
            }
        }
    }
}

/*
if($_SERVER['REMOTE_ADDR'] == "49.205.132.149"){ }
else{
    header('location:broiler_display_multisales2.php?ccid='.$ccid);
}*/
header('location:broiler_display_multisales2.php?ccid='.$ccid);
?>