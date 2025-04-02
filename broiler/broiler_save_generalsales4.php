<?php
//broiler_save_generalsales4.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['generalsales4'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SaleAutoWapp:broiler_display_generalsales4.php'";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); /*SWRCT: Sale with Receipt*/
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $sale_wapp = $row['flag']; } } else{ $sale_wapp = 0; }
if($sale_wapp == "" || $sale_wapp == 0 || $sale_wapp == "0.00" || $sale_wapp == NULL){ $sale_wapp = 0; }

$today = date("Y-m-d");

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Feed Sale' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

//Fetch Column From Sales Table
$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $c = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }

//Add Columns to Sales Table
if(in_array("avg_price", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `avg_price` DECIMAL(20,5) NOT NULL DEFAULT '0.00' COMMENT 'Weighted Avg. Price' AFTER `bal_amt`"; mysqli_query($conn,$sql); }
if(in_array("avg_item_amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `avg_item_amount` DECIMAL(20,5) NOT NULL DEFAULT '0.00' COMMENT 'Weighted Avg. Amount' AFTER `avg_price`"; mysqli_query($conn,$sql); }
if(in_array("avg_final_amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `avg_final_amount` DECIMAL(20,5) NOT NULL DEFAULT '0.00' COMMENT 'Weighted Avg. Final Amount' AFTER `avg_item_amount`"; mysqli_query($conn,$sql); }
if(in_array("profit", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `profit` DECIMAL(20,5) NOT NULL DEFAULT '0.00' COMMENT 'Total Profit' AFTER `avg_final_amount`"; mysqli_query($conn,$sql); }
if(in_array("farm_mnu_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `farm_mnu_name` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Farm Manual Name' AFTER `farm_batch`"; mysqli_query($conn,$sql); }
if(in_array("batch_mnu_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `batch_mnu_name` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Batch Manual Name' AFTER `farm_mnu_name`"; mysqli_query($conn,$sql); }
if(in_array("company_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `company_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Company Code' AFTER `exp_date`"; mysqli_query($conn,$sql); }
if(in_array("brand_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `brand_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Brand Code' AFTER `company_name`"; mysqli_query($conn,$sql); }
if(in_array("vmg_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `vmg_code` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Vendor Master Group Code' AFTER `brand_name`"; mysqli_query($conn,$sql); }
if(in_array("driver_price", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `driver_price` VARCHAR(500) NULL DEFAULT NULL COMMENT '' AFTER `driver_code`"; mysqli_query($conn,$sql); }

//Fetch Column From Summary Table
$sql='SHOW COLUMNS FROM `account_summary`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $c = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("batch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `batch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Batch No' AFTER `remarks`"; mysqli_query($conn,$sql); }
if(in_array("exp_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `exp_date` DATE NULL DEFAULT NULL COMMENT 'Expiry Date' AFTER `batch_no`"; mysqli_query($conn,$sql); }
if(in_array("company_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `company_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Company Code' AFTER `exp_date`"; mysqli_query($conn,$sql); }
if(in_array("brand_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `brand_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Brand Code' AFTER `exp_date`"; mysqli_query($conn,$sql); }
if(in_array("vmg_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `vmg_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Vendor Master Group Code' AFTER `brand_name`"; mysqli_query($conn,$sql); }

$avg_price = $avg_amount = array(); $trcvd_qty = 0;
//Transaction Details
$date = date("Y-m-d",strtotime($_POST['date']));
$billno = $_POST['billno'];
$vcode = $_POST['vcode'];
$warehouse = $_POST['warehouse'];
$vmg_code = $_POST['vmg_code'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$driver_price = $_POST['driver_price']; if($driver_price == ""){ $driver_price = 0; }
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i]= $icodes; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; $trcvd_qty += (float)$rcd_qtys; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['avg_price'] as $avg_prices){ $avg_price[$i]= $avg_prices; $i++; }
$i = 0; foreach($_POST['avg_amount'] as $avg_amounts){ $avg_amount[$i]= $avg_amounts; $i++; }
$i = 0; foreach($_POST['farm_mnu_name'] as $farm_mnu_names){ $farm_mnu_name[$i]= $farm_mnu_names; $i++; }
$i = 0; foreach($_POST['batch_mnu_name'] as $batch_mnu_names){ $batch_mnu_name[$i]= $batch_mnu_names; $i++; }
$freight_type = $_POST['freight_type'];
$freight_pay_type = $_POST['pay_type'];
$freight_pay_acc = $_POST['freight_pay_acc'];
$freight_acc = $_POST['freight_acc'];
$freight_amt = $_POST['freight_amount'];
$final_total = $_POST['final_total'];
$final_avg_total = $_POST['final_avg_total'];
$profit_amount = $_POST['profit_amount'];
$remarks = $_POST['remarks'];
$batch_no = $_POST['batch_no'];
$exp_date = date("Y-m-d",strtotime($_POST['exp_date']));
$flag = 0;
$active = 1;
$dflag = 0;
$sale_type = "GeneralSales2";
$trtype = "generalsales4";
$trlink = "broiler_display_generalsales4.php";

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%BAG%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $icode_bag = array();
while($row = mysqli_fetch_assoc($query)){ $icode_bag[$row['code']] = $row['code']; }
$icat_list = implode("','",$icode_bag);

//Item Category
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $bag_code = array();
while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; }

// foreach($bag_code as $bcode){
//     if($bcode == $icode ){
//        $item_amts =  $item_tamt - $rcd_qty;
//        $avg_amts = $avg_price - $rcd_qty;
//     } else {}
// }

//CoA Details
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

//Item Category
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; $item_name[$row['code']] = $row['description']; }

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

$dsize = sizeof($icode); $item_dlt = "";
for($i = 0;$i < $dsize;$i++){
    //Check Batch Details
    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }

    $birds[$i] = 0;
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = "0.00"; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = "0.00"; }
    if($item_tamt[$i] == "" || $item_tamt[$i] == NULL || $item_tamt[$i] == 0 || $item_tamt[$i] == "0.00"){ $item_tamt[$i] = "0.00"; }
    if($avg_price[$i] == "" || $avg_price[$i] == NULL || $avg_price[$i] == 0 || $avg_price[$i] == "0.00"){ $avg_price[$i] = "0.00"; }
    if($avg_amount[$i] == "" || $avg_amount[$i] == NULL || $avg_amount[$i] == 0 || $avg_amount[$i] == "0.00"){ $avg_amount[$i] = "0.00"; }
    if($freight_amt == "" || $freight_amt == NULL || $freight_amt == 0 || $freight_amt == "0.00"){ $freight_amt = "0.00"; }
    if($profit_amount == "" || $profit_amount == NULL || $profit_amount == 0 || $profit_amount == "0.00"){ $profit_amount = "0.00"; }

    if(!empty($icode[$i]) && !empty($rcd_qty[$i])){
        $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$icode[$i]' AND `active` = '1' AND `dflag` = '0' OR `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
        if($ibag_flag1 > 0 && $bag_access_flag > 0){ while($brow = mysqli_fetch_assoc($bquery)){ if($ibag_flag1 > 1){ if($brow['code'] != "all"){ $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size']; } } else{ $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size']; } } }
    }

    //Prepare Item Details for Message
    if($birds[$i] != "" || $birds[$i] != 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
	if($item_dlt == ""){ $item_dlt = $item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."Kgs @ ".$rate[$i]; } else{ $item_dlt = $item_dlt.",%0D%0A".$item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."Kgs @ ".$rate[$i]; }

    $drvprc = 0;
    // excluding driver price if it is BAGS
    if (!empty($bag_code[$icode[$i]]) && $bag_code[$icode[$i]] == $icode[$i]) {
        // contain BAGS
        $drvprc = 0;
    } else {
        // not contain
        $drvprc = $driver_price;
    }

    //Add Transaction
    $from_post = "INSERT INTO `broiler_sales` (incr,prefix,trnum,date,vcode,billno,icode,birds,rcd_qty,rate,item_tamt,freight_type,freight_amt,freight_pay_type,freight_pay_acc,freight_acc,finl_amt,bal_qty,bal_amt,avg_price,avg_item_amount,avg_final_amount,profit,remarks,warehouse,farm_batch,farm_mnu_name,batch_mnu_name,vehicle_code,driver_code,driver_price,active,flag,dflag,sale_type,batch_no,exp_date,trtype,trlink,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$birds[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$final_total','$rcd_qty[$i]','$final_total','$avg_price[$i]','$avg_amount[$i]','$final_avg_total','$profit_amount','$remarks','$warehouse','$farm_batch','$farm_mnu_name[$i]','$batch_mnu_name[$i]','$vehicle_code','$driver_code','$drvprc','$active','$flag','$dflag','$sale_type','$batch_no','$exp_date','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$icode[$i]]]; $cus_acc = $control_acc_group[$contact_group[$vcode]];
        $cogs_acc = $icat_cogsac[$icat_code[$icode[$i]]];
        $sale_acc = $icat_sac[$icat_code[$icode[$i]]];
        
        //Add Account Summary
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$item_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cogs_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$sale_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }
    }
}

//Driver CoA Account Summary
if((float)$driver_price > 0){
    $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Driver Expenses' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $dcoa_code = $row['code']; }

    $driver_amt = (float)$trcvd_qty * (float)$driver_price;
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$driver_code','$date','$driver_code','$trnum',NULL,'$trcvd_qty','$driver_price','$driver_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Driver Commission','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$dcoa_code','$date','$driver_code','$trnum',NULL,'$trcvd_qty','$driver_price','$driver_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Driver Commission','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
}

//Transportation cost Summary
if($freight_amt != "" && $freight_amt > 0){
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$freight_pay_acc','$date','$vcode','$trnum','','0','0','$freight_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }
}

//Send Message
if($sale_wapp > 0){
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
    $balance_amount = 0; $balance_amount = get_customer_balance($vcode);
    if($balance_amount == "" || $balance_amount == 0 || $balance_amount == "0.00"){ $balance_amount = 0; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$vcode'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $m1 = explode(",",$row['mobile1']);
        if(sizeof($m1) > 1){ foreach($m1 as $fm1){ $mobile_count++; $mobile_no_array[$mobile_count] = $fm1; } }
        else{ $mobile_count++; $mobile_no_array[$mobile_count] = $row['mobile1']; }
        $cname = $row['name'];
    }
    $message = "";
    if($sale_wapp == 1){
        $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0ADC No: ".$billno.",%0D%0AVehicle No: ".$vehicle_code.",%0D%0AItems:%0D%0A".$item_dlt.",%0D%0ASale Amount: ".$final_total.",%0D%0ABalance: ".number_format_ind($balance_amount).",%0D%0ARemarks: ".$remarks.".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
    }
    else if($sale_wapp == 2){
        $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0ADC No: ".$billno.",%0D%0AVehicle No: ".$vehicle_code.",%0D%0AItems:%0D%0A".$item_dlt.",%0D%0ASale Amount: ".$final_total.",%0D%0ARemarks: ".$remarks.".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
    }
    else{ }

    if($message != ""){
        $message = str_replace(" ","+",$message);
        for($j = 1;$j <= $mobile_count;$j++){
            if(!empty($mobile_no_array[$j])){
                        
                $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
                while($row = mysqli_fetch_assoc($query)){ $curlopt_url = $row['curlopt_url']; }

                $wapp_date = date("Y-m-d");
                $ccode = $vcode;
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
                $trtype = "BB-AutoSaleWapp";
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
header('location:broiler_display_generalsales4.php?ccid='.$ccid);
?>