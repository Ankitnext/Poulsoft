<?php
//broiler_modify_purchase2_chicks.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['purchase2_chicks'];
$user_code = $_SESSION['userid'];

$sql='SHOW COLUMNS FROM `master_generator`'; $query= mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("chick_purchases", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `chick_purchases` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `broiler_purchases`'; $query= mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("fre_qper", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `fre_qper` DECIMAL(30,2) NULL DEFAULT '0' COMMENT 'Free Qty Percentage' AFTER `snt_qty`"; mysqli_query($conn,$sql); }
if(in_array("mort", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `mort` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("shortage", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `shortage` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `mort`"; mysqli_query($conn,$sql); }
if(in_array("weeks", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `weeks` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `shortage`"; mysqli_query($conn,$sql); }
if(in_array("excess_qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `excess_qty` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `weeks`"; mysqli_query($conn,$sql); }
if(in_array("chicks_pur", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `chicks_pur` INT(11) NULL DEFAULT '0' COMMENT 'Chick Purchase Flag'"; mysqli_query($conn,$sql); }
if(in_array("gst_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `gst_code` VARCHAR(100) NULL DEFAULT NULL COMMENT 'GST Code'"; mysqli_query($conn,$sql); }
if(in_array("ven_hat_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `ven_hat_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Vendor Hatchery Code' AFTER `vcode`"; mysqli_query($conn,$sql); }

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
while($row = mysqli_fetch_assoc($query)){ $control_acc_group[$row['code']] = $row['sup_controller_code']; }
$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bag_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `tax_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gst_coa[$row['code']] = $row['coa_code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Purchase Discount'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $discount_code = $row['code']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }

$id = $_POST['idvalue'];
$date = date("Y-m-d",strtotime($_POST['date']));
$vcode = $_POST['vcode'];
$ven_hat_code = $_POST['ven_hat_code'];
$billno = $_POST['billno'];
$tcds_per = $_POST['tcds_per'];
$tcds_amt = $_POST['tcds_amount'];
$freight_type = $_POST['freight_type'];
$freight_amt = $_POST['freight_amount'];
$freight_pay_type = $_POST['pay_type'];
$freight_pay_acc = $_POST['freight_pay_acc'];
$freight_acc = $_POST['freight_acc'];
$round_off = $_POST['round_off'];
$finl_amt = $_POST['finl_amt'];
$remarks = $_POST['remarks'];
$farm_batch = $_POST['farm_batch'];
$bag_code = $_POST['bag_code'];
$bag_count = $_POST['bag_count'];
$batch_no = $_POST['batch_no'];
$exp_date = date("Y-m-d",strtotime($_POST['exp_date']));
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$icode = $_POST['icode'];

$tot_rcd_qty = 0;
// $i = 0; foreach($_POST['icode'] as $icodes){ $icode = $icodes; $i++; }
$i = 0; foreach($_POST['snt_qty'] as $snt_qtys){ $snt_qty[$i] = $snt_qtys; $i++; }
$i = 0; foreach($_POST['fre_qper'] as $fre_qpers){ $fre_qper[$i] = $fre_qpers; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i] = $rcd_qtys; $tot_rcd_qty = $tot_rcd_qty + $rcd_qtys; $i++; }
$i = 0; foreach($_POST['fre_qty'] as $fre_qtys){ $fre_qty[$i] = $fre_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i] = $rates; $i++; }
$i = 0; foreach($_POST['dis_per'] as $dis_pers){ $dis_per[$i] = $dis_pers; $i++; }
$i = 0; foreach($_POST['dis_amt'] as $dis_amts){ $dis_amt[$i] = $dis_amts; $i++; }
$i = 0; foreach($_POST['gst_per'] as $gst_pers){ $gst_per[$i] = $gst_pers; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i] = $item_tamts; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }
$i = 0; foreach($_POST['mortality'] as $mortalitys){ $mortality[$i] = $mortalitys; $i++; }
$i = 0; foreach($_POST['shortage'] as $shortages){ $shortage[$i] = $shortages; $i++; }
$i = 0; foreach($_POST['weeks'] as $weekss){ $weeks[$i] = $weekss; $i++; }
$i = 0; foreach($_POST['excess_qty'] as $excess_qtys){ $excess_qty[$i] = $excess_qtys; $i++; }
$flag = 0;
$active = 1;
$dflag = 0;

//Freight Price Calculations
$freight_price = 0; if($freight_amt > 0 && $tot_rcd_qty > 0){ $freight_price = $freight_amt / $tot_rcd_qty; }
//TDS Price Calculations
$tds_price = 0; if($tcds_amt > 0 && $tot_rcd_qty > 0){ $tds_price = $tcds_amt / $tot_rcd_qty; }

//Verify Financial Year
$sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$id' GROUP BY `trnum` ORDER BY `trnum` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $old_date = $row['date']; $old_incr = $row['incr']; $old_prefix = $row['prefix']; $old_trnum = $row['trnum']; }

$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$old_date' AND `tdate` >= '$old_date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $old_pfx = $row['prefix']; }

$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

if($old_pfx == $pfx){
    $incr = $old_incr;
    $prefix = $old_prefix;
    $trnum = $old_trnum;
}
else{
    //Generate Invoice transaction number format
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $purchases = $row['purchases']; } $incr = $purchases + 1;
    
    $sql = "UPDATE `master_generator` SET `purchases` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'purchases' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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
}

$sql = "DELETE FROM `broiler_purchases` WHERE `trnum` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error Deletion 1:-".mysqli_error($conn)); }

$sql = "DELETE FROM `account_summary` WHERE `trnum` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error Deletion 2:-".mysqli_error($conn)); }

$dsize = sizeof($warehouse);
for($i = 0;$i < $dsize;$i++){
    if($snt_qty[$i] == "" || $snt_qty[$i] == NULL || $snt_qty[$i] == 0 || $snt_qty[$i] == "0.00"){ $snt_qty[$i] = 0; }
    if($fre_qper[$i] == "" || $fre_qper[$i] == NULL || $fre_qper[$i] == 0 || $fre_qper[$i] == "0.00"){ $fre_qper[$i] = 0; }
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = 0; }
    if($fre_qty[$i] == "" || $fre_qty[$i] == NULL || $fre_qty[$i] == 0 || $fre_qty[$i] == "0.00"){ $fre_qty[$i] = 0; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = "0.00"; }
    if($dis_per[$i] == "" || $dis_per[$i] == NULL || $dis_per[$i] == 0 || $dis_per[$i] == "0.00"){ $dis_per[$i] = "0.00"; }
    if($dis_amt[$i] == "" || $dis_amt[$i] == NULL || $dis_amt[$i] == 0 || $dis_amt[$i] == "0.00"){ $dis_amt[$i] = "0.00"; }
    if($mortality[$i] == "" || $mortality[$i] == NULL || $mortality[$i] == 0 || $mortality[$i] == "0.00"){ $mortality[$i] = "0.00"; }
    if($shortage[$i] == "" || $shortage[$i] == NULL || $shortage[$i] == 0 || $shortage[$i] == "0.00"){ $shortage[$i] = "0.00"; }
    if($weeks[$i] == "" || $weeks[$i] == NULL || $weeks[$i] == 0 || $weeks[$i] == "0.00"){ $weeks[$i] = "0.00"; }
    if($excess_qty[$i] == "" || $excess_qty[$i] == NULL || $excess_qty[$i] == 0 || $excess_qty[$i] == "0.00"){ $excess_qty[$i] = "0.00"; }
    if($gst_per[$i] == "select"){ $gst_amt[$i] = $gst_per[$i] = "0.00"; }
    else{
        $gst_per1 = explode("@",$gst_per[$i]);
        $gst_code[$i] = $gst_per1[0];
        $gst_value[$i] = $gst_per1[1];
        if($qty_on_sqty_flag == 1 || $qty_on_sqty_flag == "1"){ $gst_amt[$i] = (($gst_value[$i] / 100) * (($snt_qty[$i] * $rate[$i]) - $dis_amt[$i])); }
        else{ $gst_amt[$i] = (($gst_value[$i] / 100) * (($rcd_qty[$i] * $rate[$i]) - $dis_amt[$i])); }
    }
    if($item_tamt[$i] == "" || $item_tamt[$i] == NULL || $item_tamt[$i] == 0 || $item_tamt[$i] == "0.00"){ $item_tamt[$i] = "0.00"; }
    if($tcds_amt <= 0){ $tcds_per = $tcds_amt = "0.00"; }
    if($freight_amt <= 0){ $freight_type = $freight_pay_type = $freight_acc = ""; $freight_amt = "0.00"; }
    if($finl_amt == "" || $finl_amt == NULL || $finl_amt == 0 || $finl_amt == "0.00"){ $finl_amt = "0.00"; }
    if($bag_count == "" || $bag_count == NULL || $bag_count == 0 || $bag_count == "0.00"){ $bag_count = "0.00"; }

    if($fre_qty[$i] > 0){ $item_stock_qty = $rcd_qty[$i] + $fre_qty[$i]; } else{ $item_stock_qty = $rcd_qty[$i]; }
    if($freight_amt > 0){ if($freight_type == "include"){ $item_freight_amt = 0; } if($freight_type == "exclude" || $freight_type == "inbill"){ $item_freight_amt = $freight_price * $rcd_qty[$i]; } else{ $item_freight_amt = 0; } } else{ $item_freight_amt = 0; }
    if($tcds_amt > 0){ $item_tds_amt = $tds_price * $rcd_qty[$i]; } else{ $item_tds_amt = 0; }
    $item_avg_amount = $item_tamt[$i] + $item_freight_amt + $item_tds_amt;
    $item_avg_price = $item_avg_amount / $item_stock_qty;

    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }
    
    $sql = "INSERT INTO `broiler_purchases` (chicks_pur,mort,shortage,weeks,excess_qty,incr,prefix,trnum,date,vcode,ven_hat_code,billno,icode,snt_qty,fre_qper,rcd_qty,fre_qty,rate,dis_per,dis_amt,gst_code,gst_per,gst_amt,tcds_per,tcds_amt,item_tamt,freight_type,freight_amt,freight_pay_type,freight_pay_acc,freight_acc,round_off,finl_amt,bal_qty,bal_amt,remarks,warehouse,farm_batch,bag_code,bag_count,batch_no,exp_date,vehicle_code,driver_code,active,flag,dflag,addedemp,addedtime,updatedtime) VALUES ('1','$mortality[$i]','$shortage[$i]','$weeks[$i]','$excess_qty[$i]','$incr','$prefix','$trnum','$date','$vcode','$ven_hat_code','$billno','$icode','$snt_qty[$i]','$fre_qper[$i]','$rcd_qty[$i]','$fre_qty[$i]','$rate[$i]','$dis_per[$i]','$dis_amt[$i]','$gst_code[$i]','$gst_value[$i]','$gst_amt[$i]','$tcds_per','$tcds_amt','$item_tamt[$i]','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$round_off','$finl_amt','$rcd_qty[$i]','$item_tamt[$i]','$remarks','$warehouse[$i]','$farm_batch','$bag_code','$bag_count','$batch_no','$exp_date','$vehicle_code','$driver_code','$active','$flag','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
    else {
        $coa_Dr = $icat_iac[$icat_code[$icode]]; $coa_Cr = $control_acc_group[$contact_group[$vcode]];
        $amount = $rate[$i] * $rcd_qty[$i];
        $gst_acc = $gst_coa[$gst_code[$i]];

        /* ***** Supplier Quantity ***** */
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$icode','$rcd_qty[$i]','$rate[$i]','$amount','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-RcvQty','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        /* ***** Free Quantity ***** */
        if($fre_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$icode','$fre_qty[$i]','0','0','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 2:-".mysqli_error($conn)); } else{ }
        }

        /* ***** Stock Quantity ***** */
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_Dr','$date','$vcode','$trnum','$icode','$item_stock_qty','$item_avg_price','$item_avg_amount','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-RcvQty','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }

        /* ***** GST ***** */
        if($gst_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$icode','0','$gst_value[$i]','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 1:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$gst_acc','$date','$vcode','$trnum','$icode','0','$gst_value[$i]','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 2:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$gst_acc','$date','$vcode','$trnum','$icode','0','$gst_value[$i]','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 3:-".mysqli_error($conn)); } else{ }
        }

        /* ***** Discount ***** */
        if($dis_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$discount_code','$date','$vcode','$trnum','$icode','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$discount_code','$date','$vcode','$trnum','$icode','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$coa_Cr','$date','$vcode','$trnum','$icode','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
        }
    }
}
/* ***** Freight ***** */
if($freight_amt > 0){
    $coa_Cr = $control_acc_group[$contact_group[$vcode]];
    if($freight_type == "include"){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 1:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Freight Error 2:-".mysqli_error($conn)); } else{ }

            /*Freight Value to Item Account
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Freight Error 3:-".mysqli_error($conn)); } else{ }*/
        }
    }
    else if($freight_type == "exclude"){
        //Payment method From Cash/Bank
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_pay_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 5:-".mysqli_error($conn)); } else{ }
        //Freight Value to Freight Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 4:-".mysqli_error($conn)); } else{ }
        //Freight Value to Item Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 6:-".mysqli_error($conn)); } else{ }
    }
    else if($freight_type == "inbill"){
        //Freight Value from Supplier Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 8:-".mysqli_error($conn)); } else{ }
        //Freight Value to Freight Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 7:-".mysqli_error($conn)); } else{ }
        //Freight Value to Item Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 8:-".mysqli_error($conn)); } else{ }
        
    }
    else{ }
}

/* ***** TDS ***** */
if($tcds_amt > 0){
    $sql = "SELECT * FROM `main_tcds` WHERE `type` = 'TDS' AND `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $tds_code = $row['coa']; }
    $coa_Cr = $control_acc_group[$contact_group[$vcode]];
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Purchase-TDS','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("TDS Error 1:-".mysqli_error($conn)); }
    else{
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$tds_code','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Purchase-TDS','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("TDS Error 2:-".mysqli_error($conn)); } else{ }
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$tds_code','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Purchase-TDS','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("TDS Error 2:-".mysqli_error($conn)); } else{ }
    }
}

header('location:broiler_display_purchase2_chicks.php?ccid='.$ccid);
?>