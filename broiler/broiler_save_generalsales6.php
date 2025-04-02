<?php
//broiler_save_generalsales6.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['generalsales6'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

//Check Column Availability
$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("tcds_type", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_type` VARCHAR(300) NULL DEFAULT NULL COMMENT 'TCS/TDS Type' AFTER `gst_amt`"; mysqli_query($conn,$sql); }
if(in_array("tcds_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'TCS/TDS Master Code' AFTER `tcds_type`"; mysqli_query($conn,$sql); }
if(in_array("tcds_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_type1` VARCHAR(300) NULL DEFAULT NULL COMMENT 'TCS/TDS Master Code' AFTER `tcds_code`"; mysqli_query($conn,$sql); }
if(in_array("amount1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `amount1` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `rate`"; mysqli_query($conn,$sql); }
if(in_array("dis_per", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `dis_per` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `amount1`"; mysqli_query($conn,$sql); }
if(in_array("dis_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `dis_amt` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `dis_per`"; mysqli_query($conn,$sql); }
if(in_array("gst_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gst_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `dis_amt`"; mysqli_query($conn,$sql); }
if(in_array("gst_per", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gst_per` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `gst_code`"; mysqli_query($conn,$sql); }
if(in_array("gst_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `gst_amt` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT '' AFTER `gst_per`"; mysqli_query($conn,$sql); }
if(in_array("dmobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `dmobile_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `driver_code`"; mysqli_query($conn,$sql); }
if(in_array("fbatch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fbatch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `batch_no`"; mysqli_query($conn,$sql); }
if(in_array("fmake_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fmake_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `fbatch_no`"; mysqli_query($conn,$sql); }
if(in_array("fexp_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fexp_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `fmake_date`"; mysqli_query($conn,$sql); }
if(in_array("file_path1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `file_path1` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-1' AFTER `remarks`"; mysqli_query($conn,$sql); }
if(in_array("file_path2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `file_path2` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-2' AFTER `file_path1`"; mysqli_query($conn,$sql); }
if(in_array("file_path3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `file_path3` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-3' AFTER `file_path2`"; mysqli_query($conn,$sql); }



//Transaction Details
$date = date("Y-m-d",strtotime($_POST['date']));
$billno = $_POST['billno'];
$vcode = $_POST['vcode'];
// $warehouse = $_POST['warehouse'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$dmobile_no = $_POST['dmobile_no'];
$sale_pono = $_POST['sale_pono'];
$sale_podate = $_POST['sale_podate'];


$icode = $fbatch_no = $fmake_date = $fexp_date = $rcd_qty = $rate = $amount1 = $warehouse = $dis_per = $dis_amt = $gst_val = $gst_amt = $item_tamt = $avg_price = $avg_amount = array();
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i]= $icodes; $i++; }
$i = 0; foreach($_POST['fbatch_no'] as $fbatch_nos){ $fbatch_no[$i]= $fbatch_nos; $i++; }
$i = 0; foreach($_POST['fmake_date'] as $fmake_dates){ $fmake_date[$i]= date("Y-m-d",strtotime($fmake_dates)); $i++; }
$i = 0; foreach($_POST['fexp_date'] as $fexp_dates){ $fexp_date[$i]= date("Y-m-d",strtotime($fexp_dates)); $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['amount1'] as $amount1s){ $amount1[$i]= $amount1s; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i]= $warehouses; $i++; }
$i = 0; foreach($_POST['dis_per'] as $dis_pers){ $dis_per[$i]= $dis_pers; $i++; }
$i = 0; foreach($_POST['dis_amt'] as $dis_amts){ $dis_amt[$i]= $dis_amts; $i++; }
$i = 0; foreach($_POST['gst_val'] as $gst_vals){ $gst_val[$i]= $gst_vals; $i++; }
$i = 0; foreach($_POST['gst_amt'] as $gst_amts){ $gst_amt[$i]= $gst_amts; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['avg_price'] as $avg_prices){ $avg_price[$i]= $avg_prices; $i++; }
$i = 0; foreach($_POST['avg_amount'] as $avg_amounts){ $avg_amount[$i]= $avg_amounts; $i++; }

$tot_rqty = $_POST['tot_rqty']; if($tot_rqty == ""){ $tot_rqty = 0; }
$tot_ramt = $_POST['tot_ramt']; if($tot_ramt == ""){ $tot_ramt = 0; }

$tcds_code = $_POST['tcds_code'];
$tcds_type1 = $_POST['tcds_type1'];
$tcds_per = $tcds_amt = 0; $tcds_type = $tcds_coa = "";
if($tcds_code != "none"){
    $sql = "SELECT * FROM `broiler_tcds_master` WHERE `code` = '$tcds_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $tcds_type = $row['type']; $tcds_per = $row['value']; $tcds_coa = $row['coa_acc']; }
    $tcds_amt = $_POST['tcds_amt']; if($tcds_amt == ""){ $tcds_amt = 0; }
}

$round_off = $_POST['round_off']; if($round_off == ""){ $round_off = 0; }
$finl_amt = $_POST['finl_amt']; if($finl_amt == ""){ $finl_amt = 0; }

$remarks = $_POST['remarks'];
$flag = 0;
$active = 1;
$dflag = 0;
$trtype = "generalsales6";
$trlink = "broiler_display_generalsales6.php";

//Customer and Group Details
$sql = "SELECT * FROM `main_groups` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $control_acc_group = array();
while($row = mysqli_fetch_assoc($query)){ $control_acc_group[$row['code']] = $row['cus_controller_code']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $contact_group = array();
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['groupcode']; }

//Item Details
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

//Item CoA Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_iac = $icat_iac = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; $icat_cogsac[$row['code']] = $row['cogsac']; $icat_sac[$row['code']] = $row['sac']; }

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SaleAutoWapp:broiler_display_generalsales6.php' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $sale_wapp = mysqli_num_rows($query);

//Discount
$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Sales Discount' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $dis_code = "";
while($row = mysqli_fetch_assoc($query)){ $dis_code = $row['code']; }

//GST
$sql = "SELECT * FROM `tax_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gst_coa[$row['code']] = $row['coa_code']; }

$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales6.php' AND `field_function` LIKE 'Batch No manual entry' AND `flag` = '1' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $bnme_flag = mysqli_num_rows($query);	
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales6.php' AND `field_function` LIKE 'Manufacturing Date manual entry' AND `flag` = '1' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $mdme_flag = mysqli_num_rows($query);	
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales6.php' AND `field_function` LIKE 'Expiry Date manual entry' AND `flag` = '1' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $edme_flag = mysqli_num_rows($query);
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales6.php' AND `field_function` LIKE 'PO No manual entry' AND `flag` = '1' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $pnme_flag = mysqli_num_rows($query);
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales6.php' AND `field_function` LIKE 'PO Date manual entry' AND `flag` = '1' AND `user_access` = 'all'";
$query = mysqli_query($conn,$sql); $pdme_flag = mysqli_num_rows($query);

//Generate Transaction No.
$incr = 0; $prefix = $trnum = $fyear = "";
$trno_dt1 = generate_transaction_details($date,"generalsales6","GGS","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

$folder_path = "documents/".$dbname; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }

if(!empty($_FILES["prod_doc_1"]["name"])) {
    $filename = basename($_FILES["prod_doc_1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$trnum."_1.".$filetype;

    $filetmp = $_FILES['prod_doc_1']['tmp_name'];
    $file_path1 = $folder_path."/".$file_name; 
    move_uploaded_file($filetmp,$file_path1);
}
else{ $file_path1 = ""; }

if(!empty($_FILES["prod_doc_2"]["name"])) {
    $filename = basename($_FILES["prod_doc_2"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$trnum."_2.".$filetype;

    $filetmp = $_FILES['prod_doc_2']['tmp_name'];
    $file_path2 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$file_path2);
}
else{ $file_path2 = ""; }

if(!empty($_FILES["prod_doc_3"]["name"])) {
    $filename = basename($_FILES["prod_doc_3"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$trnum."_3.".$filetype;

    $filetmp = $_FILES['prod_doc_3']['tmp_name'];
    $file_path3 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$file_path3);
}
else{ $file_path3 = ""; }

$dsize = sizeof($icode); $item_dlt = "";
for($i = 0;$i < $dsize;$i++){
    //Check Batch Details
    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }

    $birds[$i] = 0;
    if($rcd_qty[$i] == ""){ $rcd_qty[$i] = 0; }
    if($rate[$i] == ""){ $rate[$i] = 0; }
    if($amount1[$i] == ""){ $amount1[$i] = 0; }
    if($dis_per[$i] == ""){ $dis_per[$i] = 0; }
    if($dis_amt[$i] == ""){ $dis_amt[$i] = 0; }
    if($gst_amt[$i] == ""){ $gst_amt[$i] = 0; }
    if($item_tamt[$i] == ""){ $item_tamt[$i] = 0; }
    if($avg_price[$i] == ""){ $avg_price[$i] = 0; }
    if($avg_amount[$i] == ""){ $avg_amount[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt == ""){ $tcds_amt = 0; }
    if($finl_amt == ""){ $finl_amt = 0; }
    
    //GST
    $gst_code = ""; $gst_per = 0;
    if($gst_val[$i] == "" || $gst_val[$i] == "select"){ } else{ $gst_dt2 = explode("@",$gst_val[$i]); $gst_code = $gst_dt2[0]; $gst_per = $gst_dt2[1]; }
    if($gst_per == ""){ $gst_per = 0; }

    //Prepare Item Details for Message
    $iunit = ""; $iunit = $item_cunits[$icode[$i]];
    if($birds[$i] != "" || $birds[$i] != 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; } 
	if($item_dlt == ""){ $item_dlt = $item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."$iunit @ ".$rate[$i]; } else{ $item_dlt = $item_dlt.",%0D%0A".$item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."$iunit @ ".$rate[$i]; }

    $batch_col = $batch_val = ""; // $batch_val = array();
    if((int)$bnme_flag == 1 ){ $batch_col .= ",`fbatch_no`"; $batch_val .= ",'$fbatch_no[$i]'";}
    if((int)$mdme_flag == 1 ){ $batch_col .= ",`fmake_date`"; $batch_val .= ",'$fmake_date[$i]'";}
    if((int)$edme_flag == 1 ){ $batch_col .= ",`fexp_date`"; $batch_val .= ",'$fexp_date[$i]'";}
    $po_col = $po_val = "";
    if((int)$pnme_flag == 1 ){ $po_col .= ",`sale_pono`"; $po_val .= ",'$sale_pono'";}
    if((int)$pdme_flag == 1 ){ $po_col .= ",`sale_podate`"; $po_val .= ",'$sale_podate'";}

    //Add Transaction
    $from_post = "INSERT INTO `broiler_sales` (`incr`,`prefix`,`trnum`,`date`,`vcode`,`billno`,`icode`,`rcd_qty`,`rate`,`amount1`,`dis_per`,`dis_amt`,`gst_code`,`gst_per`,`gst_amt`,`item_tamt`,`tcds_type`,`tcds_code`,`tcds_type1`,`tcds_per`,`tcds_amt`,`round_off`,`finl_amt`,`bal_qty`,`bal_amt`,`avg_price`,`avg_item_amount`,`remarks`,`file_path1`,`file_path2`,`file_path3`,`warehouse`,`farm_batch`".$batch_col."".$po_col.",`vehicle_code`,`driver_code`,`dmobile_no`,`active`,`flag`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$amount1[$i]','$dis_per[$i]','$dis_amt[$i]','$gst_code','$gst_per','$gst_amt[$i]','$item_tamt[$i]','$tcds_type','$tcds_code','$tcds_type1','$tcds_per','$tcds_amt','$round_off','$finl_amt','$rcd_qty[$i]','$finl_amt','$avg_price[$i]','$avg_amount[$i]','$remarks','$file_path1','$file_path2','$file_path3','$warehouse[$i]','$farm_batch'".$batch_val."".$po_val.",'$vehicle_code','$driver_code','$dmobile_no','$active','$flag','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$icode[$i]]]; $cus_acc = $control_acc_group[$contact_group[$vcode]];
        $cogs_acc = $icat_cogsac[$icat_code[$icode[$i]]];
        $sale_acc = $icat_sac[$icat_code[$icode[$i]]];
        
        //Add Account Summary
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$item_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse[$i]','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cogs_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse[$i]','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse[$i]','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$sale_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse[$i]','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }

        /* ***** Discount ***** */
        if((float)$dis_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$dis_code','$date','$vcode','$trnum','$icode[$i]','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Sales-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Sales-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
        }

        /* ***** GST ***** */
        if($gst_amt[$i] > 0){
            $gst_acc = $gst_coa[$gst_code];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$gst_acc','$date','$vcode','$trnum','$icode[$i]','0','$gst_per','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Sales-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 1:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','0','$gst_per','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','Sales-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 2:-".mysqli_error($conn)); } else{ }
        }
    }
}

/* ***** TDS ***** */
if($tcds_code != "none" && (float)$tcds_amt > 0){
    $tcds_cr = $tcds_dr = "";
    if($tcds_type1 == "deduct"){ $tcds_cr = $control_acc_group[$contact_group[$vcode]]; $tcds_dr = $tcds_coa; }
    else{ $tcds_cr = $tcds_coa; $tcds_dr = $control_acc_group[$contact_group[$vcode]]; }
    
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$tcds_dr','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Sales-TCS','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("TDS Error 1:-".mysqli_error($conn)); } else{ }
    
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$tcds_cr','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Sales-TCS','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("TDS Error 2:-".mysqli_error($conn)); } else{ }
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
        $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0ADC No: ".$billno.",%0D%0AVehicle No: ".$vehicle_code.",%0D%0AItems:%0D%0A".$item_dlt.",%0D%0ASale Amount: ".$finl_amt.",%0D%0ABalance: ".number_format_ind($balance_amount).",%0D%0ARemarks: ".$remarks.".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
    }
    else if($sale_wapp == 2){
        $message = "Dear: ".$cname."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0ADC No: ".$billno.",%0D%0AVehicle No: ".$vehicle_code.",%0D%0AItems:%0D%0A".$item_dlt.",%0D%0ASale Amount: ".$finl_amt.",%0D%0ARemarks: ".$remarks.".%0D%0A%0D%0AThank You,%0D%0A".$cdetails;
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
header('location:broiler_display_generalsales6.php?ccid='.$ccid);
?>