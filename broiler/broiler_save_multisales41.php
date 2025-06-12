<?php
//broiler_save_multisales4
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['multisales4'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SalewithRCTAutoWapp:broiler_display_multisales4.php'";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); /*SWRCT: Sale with Receipt*/
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $swrct_wapp = $row['flag']; } } else{ $swrct_wapp = 0; }
if($swrct_wapp == "" || $swrct_wapp == 0 || $swrct_wapp == "0.00" || $swrct_wapp == NULL){ $swrct_wapp = 0; }

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
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$date= date("Y-m-d",strtotime($_POST['date']));

$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` LIKE 'TCS' AND `active` = 1 AND `dflag` = 0 ORDER BY `fdate`,`id` ASC";
$query = mysqli_query($conn,$sql); $tcds_count = mysqli_num_rows($query);
if($tcds_count > 0){ while($row = mysqli_fetch_assoc($query)){ $tcds_coa_code = $row['coa']; } } else{ $tcds_coa_code = ""; }

$vcode= $billno= $warehouse= $birds= $rcd_qty= $rate= $item_tamt= $supervisor_code= $vehicle_code= $driver_code= $remarks= $receipt= $avg_wt= array();

if($_POST['sale_type'] == "CusMBSale"){ $i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i]= $vcodes; $i++; } }
$sale_type = $_POST['sale_type']; 
$i = 0; foreach($_POST['billno'] as $billnos){ $billno[$i]= $billnos; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i]= $warehouses; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i]= $birdss; $i++; }
$i = 0; foreach($_POST['gross_weight'] as $gross_weights){ $gross_weight[$i] = $gross_weights; $i++; }
$i = 0; foreach($_POST['tare_weight'] as $tare_weights){ $tare_weight[$i] = $tare_weights; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['avg_wt'] as $avg_wts){ $avg_wt[$i]= $avg_wts; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['tcds_value'] as $tcds_values){ $tcds_per[$i]= $tcds_values; $i++; }
$i = 0; foreach($_POST['tcds_amount'] as $tcds_amounts){ $tcds_amt[$i]= $tcds_amounts; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['supervisor_code'] as $supervisor_codes){ $supervisor_code[$i]= $supervisor_codes; $i++; }
$i = 0; foreach($_POST['vehicle_code'] as $vehicle_codes){ $vehicle_code[$i]= $vehicle_codes; $i++; }
$i = 0; foreach($_POST['driver_code'] as $driver_codes){ $driver_code[$i]= $driver_codes; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i]= $remarkss; $i++; }
$i = 0; foreach($_POST['receipt'] as $receipts){ $receipt[$i]= $receipts; $i++; }
$vtype = "Customer";
$flag = 0;
$active = 1;
$dflag = 0;

$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("gross_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gross_weight` DOUBLE(20,5) NOT NULL DEFAULT '0' COMMENT 'total weight' AFTER `birds`"; mysqli_query($conn,$sql); }
if(in_array("tare_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tare_weight` DOUBLE(20,5) NOT NULL DEFAULT '0' COMMENT 'empty weight' AFTER `gross_weight`"; mysqli_query($conn,$sql); }


$dsize = sizeof($warehouse);
for($i = 0;$i < $dsize;$i++){
    //Generate Invoice transaction number format
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
    
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; } $incr = $sales + 1;
    
    $sql = "UPDATE `master_generator` SET `sales` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
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
    if($gross_weight[$i] == "" || $gross_weight[$i] == NULL || $gross_weight[$i] == 0 || $gross_weight[$i] == "0.00"){ $gross_weight[$i] = "0.00"; }
    if($tare_weight[$i] == "" || $tare_weight[$i] == NULL || $tare_weight[$i] == 0 || $tare_weight[$i] == "0.00"){ $tare_weight[$i] = "0.00"; }
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = "0.00"; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = "0.00"; }
    if($tcds_per[$i] == "" || $tcds_per[$i] == NULL || $tcds_per[$i] == 0 || $tcds_per[$i] == "0.00"){ $tcds_per[$i] = "0.00"; }
    if($tcds_amt[$i] == "" || $tcds_amt[$i] == NULL || $tcds_amt[$i] == 0 || $tcds_amt[$i] == "0.00"){ $tcds_amt[$i] = "0.00"; }

    $from_post = "INSERT INTO `broiler_sales` (incr,prefix,trnum,date,vcode,billno,icode,birds,gross_weight,tare_weight,rcd_qty,rate,tcds_per,tcds_amt,item_tamt,finl_amt,bal_qty,bal_amt,remarks,warehouse,farm_batch,supervisor_code,vehicle_code,driver_code,active,flag,dflag,sale_type,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode[$i]','$billno[$i]','$bird_code','$birds[$i]','$gross_weight[$i]','$tare_weight[$i]','$rcd_qty[$i]','$rate[$i]','$tcds_per[$i]','$tcds_amt[$i]','$item_tamt[$i]','$item_tamt[$i]','$rcd_qty[$i]','$item_tamt[$i]','$remarks[$i]','$warehouse[$i]','$farm_batch','$supervisor_code[$i]','$vehicle_code[$i]','$driver_code[$i]','$active','$flag','$dflag','$sale_type','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$bird_code]]; $cus_acc = $control_acc_group[$contact_group[$vcode[$i]]];
        $cogs_acc = $icat_cogsac[$icat_code[$bird_code]];
        $sale_acc = $icat_sac[$icat_code[$bird_code]];
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$item_acc','$date','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','0','0','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cogs_acc','$date','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','0','0','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cus_acc','$date','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$sale_acc','$date','$vcode[$i]','$trnum','$bird_code','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse[$i]','$farm_batch','$remarks[$i]','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }

        /* ***** TCS ***** */
        if($tcds_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$cus_acc','$date','$vcode[$i]','$trnum','','0','0','$tcds_amt[$i]','','','$remarks[$i]','0','Sale-TCS','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("TCDS Error 1:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('CR','$tcds_coa_code','$date','$vcode[$i]','$trnum','','0','0','$tcds_amt[$i]','','','$remarks[$i]','0','Sale-TCS','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("TCDS Error 2:-".mysqli_error($conn)); } else{ }
            }
        }

    }

    if($receipt[$i] == "" || $receipt[$i] == NULL || $receipt[$i] == 0 || $receipt[$i] == "0.00" || $receipt[$i] == "0" || $receipt[$i] == 0.00){ $receipt[$i] = "0.00"; }
    else{
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
    
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cus_receipts = $row['cus_receipts']; } $incr = $cus_receipts + 1;
    
        $sql = "UPDATE `master_generator` SET `cus_receipts` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
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
    
        $sql = "INSERT INTO `broiler_receipts` (incr,prefix,trnum,date,ccode,docno,mode,method,amount,amtinwords,vtype,warehouse,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$trnum','$date','$vcode[$i]','$billno[$i]','$mode','$cash_code','$receipt[$i]','','$vtype','$warehouse[$i]','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error 6:-".mysqli_error($conn)); }
        else {
            $coa_Cr = $control_acc_group[$contact_group[$vcode[$i]]];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode[$i]','$billno[$i]','$trnum','0.00','0.00','$receipt[$i]','$warehouse[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); }
            else{
                $to_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('DR','$cash_code','$date','$vcode[$i]','$billno[$i]','$trnum','0.00','0.00','$receipt[$i]','$warehouse[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$to_post)){ die("Error 8:-".mysqli_error($conn)); } else{ }
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
            if(sizeof($m1) > 1){ foreach($m1 as $fm1){ $mobile_count++; $mobile_no_array[$mobile_count] = $fm1; } }
            else{ $mobile_count++; $mobile_no_array[$mobile_count] = $row['mobile1']; }
            $cname = $row['name'];
        }
        
        $message = "";
        if($swrct_wapp == 1){
            $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0ADC No: ".$billno[$i].",%0D%0AVehicle No: ".$vehicle_code[$i].",%0D%0ABirds: ".$birds[$i].",%0D%0AWeight: ".number_format_ind($rcd_qty[$i]).",%0D%0AAvg Wt: ".number_format_ind($avg_wt[$i]).",%0D%0APrice: ".number_format_ind($rate[$i]).",%0D%0ASale Amount: ".number_format_ind($item_tamt[$i]).",%0D%0AReceipt Amount: ".number_format_ind($receipt[$i]).",%0D%0AVia: ".$mode_name[$mode].",%0D%0ABalance: ".number_format_ind($balance_amount).",%0D%0ARemarks: ".$remarks[$i].".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
        }
        else if($swrct_wapp == 2){
            $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0ADC No: ".$billno[$i].",%0D%0AVehicle No: ".$vehicle_code[$i].",%0D%0ABirds: ".$birds[$i].",%0D%0AWeight: ".number_format_ind($rcd_qty[$i]).",%0D%0AAvg Wt: ".number_format_ind($avg_wt[$i]).",%0D%0APrice: ".number_format_ind($rate[$i]).",%0D%0ASale Amount: ".number_format_ind($item_tamt[$i]).",%0D%0AReceipt Amount: ".number_format_ind($receipt[$i]).",%0D%0AVia: ".$mode_name[$mode].",%0D%0ARemarks: ".$remarks[$i].".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
        }
        else{ }

        if($message != ""){
            $message = str_replace(" ","+",$message);
            for($j = 1;$j <= $mobile_count;$j++){
                if(!empty($mobile_no_array[$j])){
                    $mobile = "91".$mobile_no_array[$j]; $type = "text";
                    
                    $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $curlopt_url = $row['curlopt_url'];
                        $curlopt_returntransfer = $row['curlopt_returntransfer'];
                        $curlopt_encoding = $row['curlopt_encoding'];
                        $curlopt_maxredirs = $row['curlopt_maxredirs'];
                        $curlopt_timeout = $row['curlopt_timeout'];
                        $curlopt_followlocation = $row['curlopt_followlocation'];
                        $curlopt_http_version = $row['curlopt_http_version'];
                        $curlopt_customrequest = $row['curlopt_customrequest'];
                    }

                    $xml_data = $curlopt_url.'number='.$mobile.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token;
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $curlopt_url.'number='.$mobile.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token,
                    CURLOPT_RETURNTRANSFER => $curlopt_returntransfer,
                    CURLOPT_ENCODING => $curlopt_encoding,
                    CURLOPT_MAXREDIRS => $curlopt_maxredirs,
                    CURLOPT_TIMEOUT => $curlopt_timeout,
                    CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
                    CURLOPT_HTTP_VERSION => $curlopt_http_version,
                    CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    
                    if($response != ""){
                        $d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);
                        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$today' AND `tdate` >= '$today' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                            
                        $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$today' AND `tdate` >= '$today' AND `type` = 'transactions'";
                        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                        
                        if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                        $wapp_code = "WAPP-".$incr_wapp;
                        
                        $wsfile_path = $_SERVER['REQUEST_URI'];
                        $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime)
                        VALUES ('$wapp_code','$vcode[$i]','$mobile','$xml_data','$d3[1]','$response','BB-AutoSWRCTWapp','$wsfile_path','$addedemp','$addedtime','$addedtime')";
                        if(!mysqli_query($conn,$sql)) { die("Error:- WhApp sending error 1: ".mysqli_error($conn)); } else{  }
                    }
                }
            }
        }
    }
}
header('location:broiler_display_multisales41.php?ccid='.$ccid);
?>