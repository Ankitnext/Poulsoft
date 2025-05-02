<?php
//broiler_save_feedsale5.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedsale5'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SaleAutoWapp:broiler_display_feedsale5.php'";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); /*SWRCT: Sale with Receipt*/
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $sale_wapp = $row['flag']; } } else{ $sale_wapp = 0; }
if($sale_wapp == "" || $sale_wapp == 0 || $sale_wapp == "0.00" || $sale_wapp == NULL){ $sale_wapp = 0; }

//Fetch Column From Sales Table
$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $c = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
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

$icode = $nof_bags = $rcd_qty = $mrp_prc = $dis_pper = $dis_pamt = $bag_rate = $rate = $item_tamt = $avl_stk_qty = $avl_stk_prc = $farm_mnu_name = $batch_mnu_name = $gst_amt = $bag_size = array();
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i]= $icodes; $i++; }
$i = 0; foreach($_POST['nof_bags'] as $nof_bagss){ $nof_bags[$i]= $nof_bagss; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['mrp_prc'] as $mrp_prcs){ $mrp_prc[$i]= $mrp_prcs; $i++; }
$i = 0; foreach($_POST['dis_pper'] as $dis_ppers){ $dis_pper[$i]= $dis_ppers; $i++; }
$i = 0; foreach($_POST['dis_pamt'] as $dis_pamts){ $dis_pamt[$i]= $dis_pamts; $i++; }
$i = 0; foreach($_POST['bag_rate'] as $bag_rates){ $bag_rate[$i]= $bag_rates; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['gst_val'] as $gst_vals){ $gst_val[$i]= $gst_vals; $i++; }
$i = 0; foreach($_POST['gst_amt'] as $gst_amts){ $gst_amt[$i]= $gst_amts; $i++; }
$i = 0; foreach($_POST['avl_stk_qty'] as $avl_stk_qtys){ $avl_stk_qty[$i]= $avl_stk_qtys; $i++; }
$i = 0; foreach($_POST['avl_stk_prc'] as $avl_stk_prcs){ $avl_stk_prc[$i]= $avl_stk_prcs; $i++; }
$i = 0; foreach($_POST['farm_mnu_name'] as $farm_mnu_names){ $farm_mnu_name[$i]= $farm_mnu_names; $i++; }
$i = 0; foreach($_POST['batch_mnu_name'] as $batch_mnu_names){ $batch_mnu_name[$i]= $batch_mnu_names; $i++; }
$i = 0; foreach($_POST['bag_size'] as $bag_sizes){ $bag_size[$i]= $bag_sizes; $i++; }

$freight_type = $_POST['freight_type'];
$freight_pay_type = $_POST['pay_type'];
$freight_pay_acc = $_POST['freight_pay_acc'];
$freight_acc = $_POST['freight_acc'];
$freight_amt = $_POST['freight_amount']; if($freight_amt == ""){ $freight_amt = 0; }
$tcds_type = $_POST['tcds_type'];
$tcds_amt = $_POST['tcds_amt']; if($tcds_amt == ""){ $tcds_amt = 0; }
$round_off = $_POST['round_off']; if($round_off == ""){ $round_off = 0; }
$final_total = $_POST['final_total']; if($final_total == ""){ $final_total = 0; }
$remarks = $_POST['remarks'];
$batch_no = $_POST['batch_no'];
$exp_date = date("Y-m-d",strtotime($_POST['exp_date']));

$flag = $dflag = 0;
$active = 1;
$trtype = "feedsale5";
$trlink = "broiler_display_feedsale5.php";

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
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; $item_name[$row['code']] = $row['description']; }

//Generate Transaction No.
$incr = 0; $prefix = $trnum = $fyear = "";
$trno_dt1 = generate_transaction_details($date,"feedsale5","FSIN","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

$dsize = sizeof($icode); $item_dlt = "";
for($i = 0;$i < $dsize;$i++){
    //Check Batch Details
    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }

    $birds[$i] = 0;
    if($nof_bags[$i] == ""){ $nof_bags[$i] = "0.00"; }
    if($rcd_qty[$i] == ""){ $rcd_qty[$i] = "0.00"; }
    if($mrp_prc[$i] == ""){ $mrp_prc[$i] = "0.00"; }
    if($dis_pper[$i] == ""){ $dis_pper[$i] = "0.00"; }
    if($dis_pamt[$i] == ""){ $dis_pamt[$i] = "0.00"; }
    if($rate[$i] == ""){ $rate[$i] = "0.00"; }
    if($bag_rate[$i] == ""){ $bag_rate[$i] = "0.00"; }
    if($item_tamt[$i] == ""){ $item_tamt[$i] = "0.00"; }
    if($avl_stk_qty[$i] == ""){ $avl_stk_qty[$i] = "0.00"; }
    if($avl_stk_prc[$i] == ""){ $avl_stk_prc[$i] = "0.00"; }
    if($gst_amt[$i] == ""){ $gst_amt[$i] = "0.00"; }
    if($bag_size[$i] == ""){ $bag_size[$i] = "0.00"; }

    //GST Calculations
    if($gst_val[$i] == "" || $gst_val[$i] == "select"){
        $gst_code[$i] = $gst_coa[$i] = NULL; $gst_per[$i] = $gst_amt[$i] = 0;
    }
    else{
        $gst_dt1 = array(); $gst_dt1 = explode("@",$gst_val[$i]);
        $gst_code[$i] = $gst_dt1[0]; $gst_per[$i] = $gst_dt1[1]; $gst_coa[$i] = $gst_dt1[2];
        if(empty($gst_amt[$i]) || $gst_amt[$i] == ""){ $gst_amt[$i] = 0; }
    }

    //Prepare Item Details for Message
    if($birds[$i] != "" || $birds[$i] != 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
	if($item_dlt == ""){ $item_dlt = $item_name[$icode[$i]].": ".$item_birds."".$nof_bags[$i]."Kgs @ ".$bag_rate[$i]; } else{ $item_dlt = $item_dlt.",%0D%0A".$item_name[$icode[$i]].": ".$item_birds."".$nof_bags[$i]."Kgs @ ".$bag_rate[$i]; }

    //Add Transaction
    $from_post = "INSERT INTO `broiler_sales` (incr,prefix,trnum,date,vcode,billno,icode,nof_bags,birds,rcd_qty,mrp_prc,dis_pper,dis_pamt,bag_rate,rate,gst_code,gst_per,gst_amt,item_tamt,tcds_type,tcds_amt,freight_type,freight_amt,freight_pay_type,freight_pay_acc,freight_acc,round_off,finl_amt,bal_qty,bal_amt,remarks,warehouse,farm_batch,farm_mnu_name,batch_mnu_name,vehicle_code,driver_code,active,flag,dflag,trtype,trlink,batch_no,exp_date,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$nof_bags[$i]','$birds[$i]','$rcd_qty[$i]','$mrp_prc[$i]','$dis_pper[$i]','$dis_pamt[$i]','$bag_rate[$i]','$rate[$i]','$gst_code[$i]','$gst_per[$i]','$gst_amt[$i]','$item_tamt[$i]','$tcds_type','$tcds_amt','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$round_off','$final_total','$rcd_qty[$i]','$final_total','$remarks','$warehouse','$farm_batch','$farm_mnu_name[$i]','$batch_mnu_name[$i]','$vehicle_code','$driver_code','$active','$flag','$dflag','$trtype','$trlink','$batch_no','$exp_date','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$icode[$i]]]; $cus_acc = $control_acc_group[$contact_group[$vcode]];
        $cogs_acc = $icat_cogsac[$icat_code[$icode[$i]]];
        $sale_acc = $icat_sac[$icat_code[$icode[$i]]];
        
        //Add Account Summary
        $amount1 = ((float)$rcd_qty[$i] * (float)$avl_stk_prc[$i]);
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$item_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avl_stk_prc[$i]','$amount1','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cogs_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avl_stk_prc[$i]','$amount1','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$sale_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }

        /* ***** Free Quantity ***** */
        if($fre_qty[$i] > 0){
            $fre_amt = 0;
            $fre_amt = $fre_qty[$i] * $avl_stk_prc[$i];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$item_acc','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','$avl_stk_prc[$i]','$fre_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sale-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }
    
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,batch_no,exp_date,vmg_code,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$cogs_acc','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','$avl_stk_prc[$i]','$fre_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','$batch_no','$exp_date','$vmg_code','0','Sale-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
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
header('location:broiler_display_feedsale5.php?ccid='.$ccid);
?>