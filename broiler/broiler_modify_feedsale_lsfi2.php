<?php
//broiler_modify_feedsale_lsfi2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedsale_lsfi2'];


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
if(in_array("gst_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gst_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `dis_amt`"; mysqli_query($conn,$sql); }
if(in_array("gst_per", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gst_per` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `gst_code`"; mysqli_query($conn,$sql); }
if(in_array("gst_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gst_amt` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `gst_per`"; mysqli_query($conn,$sql); }
if(in_array("tcds_type", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_type` VARCHAR(500) NULL DEFAULT NULL COMMENT 'TCDS Add/Deduct' AFTER `gst_amt`"; mysqli_query($conn,$sql); }
if(in_array("fre_qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fre_qty` decimal(20,2) NOT NULL DEFAULT '0' AFTER `rcd_qty`"; mysqli_query($conn,$sql); }
            
//Fetch Column From Summary Table
$sql='SHOW COLUMNS FROM `account_summary`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $c = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("batch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `batch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Batch No' AFTER `remarks`"; mysqli_query($conn,$sql); }
if(in_array("exp_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `exp_date` DATE NULL DEFAULT NULL COMMENT 'Expiry Date' AFTER `batch_no`"; mysqli_query($conn,$sql); }
if(in_array("company_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `company_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Company Code' AFTER `exp_date`"; mysqli_query($conn,$sql); }
if(in_array("brand_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `brand_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Brand Code' AFTER `exp_date`"; mysqli_query($conn,$sql); }
if(in_array("vmg_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `vmg_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Vendor Master Group Code' AFTER `brand_name`"; mysqli_query($conn,$sql); }

$avg_price = $avg_amount = array();
//Transaction Details
$date = date("Y-m-d",strtotime($_POST['date']));
$billno = $_POST['billno'];
$vcode = $_POST['vcode'];
$warehouse = $_POST['warehouse'];
$vmg_code = $_POST['vmg_code'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i]= $icodes; $i++; }
$i = 0; foreach($_POST['branch'] as $branchs){ $branch[$i]= $branchs; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['fre_qty'] as $fre_qtys){ $fre_qty[$i]= $fre_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['prt_per'] as $prt_pers){ $prt_per[$i]= $prt_pers; $i++; }
$i = 0; foreach($_POST['prt_pamt'] as $prt_pamts){ $prt_pamt[$i]= $prt_pamts; $i++; }
$i = 0; foreach($_POST['chrg_prc'] as $chrg_prcs){ $chrg_prc[$i]= $chrg_prcs; $i++; }
$i = 0; foreach($_POST['gst_val'] as $gst_vals){ $gst_val[$i]= $gst_vals; $i++; }
$i = 0; foreach($_POST['gst_amt'] as $gst_amts){ $gst_amt[$i]= $gst_amts; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['avg_price'] as $avg_prices){ $avg_price[$i]= $avg_prices; $i++; }
$i = 0; foreach($_POST['avg_amount'] as $avg_amounts){ $avg_amount[$i]= $avg_amounts; $i++; }
$i = 0; foreach($_POST['farm_mnu_name'] as $farm_mnu_names){ $farm_mnu_name[$i]= $farm_mnu_names; $i++; }
$i = 0; foreach($_POST['batch_mnu_name'] as $batch_mnu_names){ $batch_mnu_name[$i]= $batch_mnu_names; $i++; }
$freight_type = $_POST['freight_type'];
$freight_pay_type = $_POST['pay_type'];
$freight_pay_acc = $_POST['freight_pay_acc'];
$freight_acc = $_POST['freight_acc'];
$freight_amt = $_POST['freight_amount'];            if($freight_amt == "" || $freight_amt == NULL || $freight_amt == 0 || $freight_amt == "0.00"){ $freight_amt = 0; }
$tcds_type = $_POST['tcds_type'];
$tcds_amt = $_POST['tcds_amt']; if($tcds_amt == ""){ $tcds_amt = 0; }
$round_off = $_POST['round_off']; if($round_off == ""){ $round_off = 0; }
$final_total = $_POST['final_total'];
$final_avg_total = $_POST['final_avg_total'];
$profit_amount = $_POST['profit_amount'];           if($profit_amount == "" || $profit_amount == NULL || $profit_amount == 0 || $profit_amount == "0.00"){ $profit_amount = 0; }
$remarks = $_POST['remarks'];
$batch_no = $_POST['batch_no'];
$exp_date = date("Y-m-d",strtotime($_POST['exp_date']));
$id = $_POST['idvalue'];
$flag = 0;
$active = 1;
$dflag = 0;
$sale_type = "FeedSingleSale";

$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` LIKE 'TCS' AND `active` = 1 AND `dflag` = 0 ORDER BY `fdate`,`id` ASC";
$query = mysqli_query($conn,$sql); $tcds_count = mysqli_num_rows($query);
if($tcds_count > 0){ while($row = mysqli_fetch_assoc($query)){ $tcds_coa_code = $row['coa']; } } else{ $tcds_coa_code = ""; }

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
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

/* Delete Existing Transaction Details */
$fsql = "SELECT * FROM `broiler_sales` WHERE `trnum` = '$id'"; $fquery = mysqli_query($conn,$fsql);
while($frow = mysqli_fetch_assoc($fquery)){ $prefix = $frow['prefix']; $incr = $frow['incr']; $trnum = $frow['trnum']; }

$fsql = "DELETE FROM `broiler_sales` WHERE `trnum` = '$trnum'"; mysqli_query($conn,$fsql);
$fsql = "DELETE FROM `account_summary` WHERE `trnum` = '$trnum'"; mysqli_query($conn,$fsql);

$dsize = sizeof($icode); $item_dlt = "";
for($i = 0;$i < $dsize;$i++){
    //Check Batch Details
    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }

    $birds[$i] = 0;
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = "0.00"; }
    if($fre_qty[$i] == "" || $fre_qty[$i] == NULL || $fre_qty[$i] == 0 || $fre_qty[$i] == "0.00"){ $fre_qty[$i] = "0.00"; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = "0.00"; }
    if($chrg_prc[$i] == "" || $chrg_prc[$i] == NULL || $chrg_prc[$i] == 0 || $chrg_prc[$i] == "0.00"){ $chrg_prc[$i] = "0.00"; }
    if($prt_pamt[$i] == "" || $prt_pamt[$i] == NULL || $prt_pamt[$i] == 0 || $prt_pamt[$i] == "0.00"){ $prt_pamt[$i] = "0.00"; }
    if($prt_per[$i] == "" || $prt_per[$i] == NULL || $prt_per[$i] == 0 || $prt_per[$i] == "0.00"){ $prt_per[$i] = "0.00"; }
    if($item_tamt[$i] == "" || $item_tamt[$i] == NULL || $item_tamt[$i] == 0 || $item_tamt[$i] == "0.00"){ $item_tamt[$i] = "0.00"; }
    if($avg_price[$i] == "" || $avg_price[$i] == NULL || $avg_price[$i] == 0 || $avg_price[$i] == "0.00"){ $avg_price[$i] = "0.00"; }
    if($avg_amount[$i] == "" || $avg_amount[$i] == NULL || $avg_amount[$i] == 0 || $avg_amount[$i] == "0.00"){ $avg_amount[$i] = "0.00"; }

    //GST Calculations
    if($gst_val[$i] == "" || $gst_val[$i] == "select"){
        $gst_code[$i] = $gst_coa[$i] = NULL; $gst_per[$i] = $gst_amt[$i] = 0;
    }
    else{
        $gst_dt1 = array(); $gst_dt1 = explode("@",$gst_val[$i]);
        $gst_code[$i] = $gst_dt1[0]; $gst_per[$i] = $gst_dt1[1]; $gst_coa[$i] = $gst_dt1[2];
        if(empty($gst_amt[$i]) || $gst_amt[$i] == ""){ $gst_amt[$i] = 0; }
    }

    if(!empty($icode[$i]) && !empty($rcd_qty[$i])){
        $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$icode[$i]' AND `active` = '1' AND `dflag` = '0' OR `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
        if($ibag_flag1 > 0 && $bag_access_flag > 0){
            while($brow = mysqli_fetch_assoc($bquery)){
                if($ibag_flag1 > 1){
                    if($brow['code'] != "all"){
                        $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size'];
                        $fre_qty[$i] = $fre_qty[$i] * $brow['bag_size'];
                        $rate[$i] = $rate[$i] / $brow['bag_size'];
                    }
                }
                else{
                    $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size'];
                    $fre_qty[$i] = $fre_qty[$i] * $brow['bag_size'];
                    $rate[$i] = $rate[$i] / $brow['bag_size'];
                }
            }
        }
    }

    //Add Transaction
    $from_post = "INSERT INTO `broiler_sales` (incr,prefix,trnum,date,vcode,billno,icode,birds,rcd_qty,fre_qty,rate,prt,prt_amt,chrg_prc,branch_code,gst_code,gst_per,gst_amt,item_tamt,tcds_type,tcds_amt,freight_type,freight_amt,freight_pay_type,freight_pay_acc,freight_acc,round_off,finl_amt,bal_qty,bal_amt,avg_price,avg_item_amount,avg_final_amount,profit,remarks,warehouse,farm_batch,farm_mnu_name,batch_mnu_name,vehicle_code,driver_code,active,flag,dflag,sale_type,batch_no,exp_date,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$birds[$i]','$rcd_qty[$i]','$fre_qty[$i]','$rate[$i]','$prt_per[$i]','$prt_pamt[$i]','$chrg_prc[$i]','$branch[$i]','$gst_code[$i]','$gst_per[$i]','$gst_amt[$i]','$item_tamt[$i]','$tcds_type','$tcds_amt','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$round_off','$final_total','$rcd_qty[$i]','$final_total','$avg_price[$i]','$avg_amount[$i]','$final_avg_total','$profit_amount','$remarks','$warehouse','$farm_batch','$farm_mnu_name[$i]','$batch_mnu_name[$i]','$vehicle_code','$driver_code','$active','$flag','$dflag','$sale_type','$batch_no','$exp_date','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$icode[$i]]]; $cus_acc = $control_acc_group[$contact_group[$vcode]];
        $cogs_acc = $icat_cogsac[$icat_code[$icode[$i]]];
        $sale_acc = $icat_sac[$icat_code[$icode[$i]]];
        
        //Add Account Summary
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,updatedemp,updatedtime) 
        VALUES ('CR','$item_acc','$date','$billno','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,updatedemp,updatedtime) 
        VALUES ('DR','$cogs_acc','$date','$billno','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,updatedemp,updatedtime) 
        VALUES ('DR','$cus_acc','$date','$billno','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,updatedemp,updatedtime) 
        VALUES ('CR','$sale_acc','$date','$billno','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }

        /* ***** Free Quantity ***** */
        if($fre_qty[$i] > 0){
            $fre_amt = 0;
            $fre_amt = $fre_qty[$i] * $avg_price[$i];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$item_acc','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','$avg_price[$i]','$fre_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sale-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }
    
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$cogs_acc','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','$avg_price[$i]','$fre_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sale-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$sale_acc','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','0','0','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sale-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }
    
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','0','0','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sale-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }
        }

        //GST Summary
        if((float)$gst_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$gst_coa[$i]','$date','$vcode','$trnum','$icode[$i]','0','$gst_per[$i]','$gst_amt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }
        }
    }
}
/* ***** TCS ***** */
if($tcds_amt > 0){
    
    if($tcds_type == "deduct"){
        $coa_cr = $control_acc_group[$contact_group[$vcode]];
        $coa_dr = $tcds_coa_code;
    }
    else{
        $coa_cr = $tcds_coa_code;
        $coa_dr = $control_acc_group[$contact_group[$vcode]];
    }
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$coa_cr','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Sale-TCS','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("TCDS Error 1:-".mysqli_error($conn)); }
    else{
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_dr','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Sale-TCS','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("TCDS Error 2:-".mysqli_error($conn)); } else{ }
    }
}

//Transportation cost Summary
if($freight_amt != "" && $freight_amt > 0){
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,updatedemp,updatedtime) 
    VALUES ('DR','$freight_acc','$date','$billno','$vcode','$trnum','','0','0','$freight_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,updatedemp,updatedtime) 
    VALUES ('CR','$freight_pay_acc','$date','$billno','$vcode','$trnum','','0','0','$freight_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }
}

header('location:broiler_display_feedsale_lsfi2.php?ccid='.$ccid);
?>