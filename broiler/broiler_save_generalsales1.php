<?php
//broiler_save_generalsales1.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['generalsales1'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

//Create Generator Column for Transaction No.
$sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sales", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `sales` INT(100) NOT NULL DEFAULT '0' COMMENT 'General Sales-1' AFTER `wapp`"; mysqli_query($conn,$sql); }

//Create Prefix Row for Transaction No.
$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'sales' AND `active` = '1'";
$query = mysqli_query($conn,$sql); $prx_entry_count = mysqli_num_rows($query);
if($prx_entry_count > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES ('column:flag', 'sales', 'SIN-', '0', '1:1', '1', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1');"; mysqli_query($conn,$sql); }

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SaleAutoWapp:broiler_display_generalsales1.php' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $sale_wapp = mysqli_num_rows($query);

//Check Column Availability
$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("tcds_type", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_type` VARCHAR(300) NULL DEFAULT NULL COMMENT 'TCS/TDS Type' AFTER `gst_amt`"; mysqli_query($conn,$sql); }
if(in_array("tcds_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'TCS/TDS Master Code' AFTER `tcds_type`"; mysqli_query($conn,$sql); }
if(in_array("tcds_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tcds_type1` VARCHAR(300) NULL DEFAULT NULL COMMENT 'TCS/TDS Master Code' AFTER `tcds_code`"; mysqli_query($conn,$sql); }

//Transaction Details
$date = date("Y-m-d",strtotime($_POST['date']));
$billno = $_POST['billno'];
$vcode = $_POST['vcode'];
$warehouse = $_POST['warehouse'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];

$icode = $rcd_qty = $rate = $item_tamt = $avg_price = $avg_amount = array();
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i]= $icodes; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
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
$trtype = "generalsales1";
$trlink = "broiler_display_generalsales1.php";

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
    if($rcd_qty[$i] == ""){ $rcd_qty[$i] = 0; }
    if($rate[$i] == ""){ $rate[$i] = 0; }
    if($item_tamt[$i] == ""){ $item_tamt[$i] = 0; }
    if($avg_price[$i] == ""){ $avg_price[$i] = 0; }
    if($avg_amount[$i] == ""){ $avg_amount[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt == ""){ $tcds_amt = 0; }
    if($finl_amt == ""){ $finl_amt = 0; }
    
    //Prepare Item Details for Message
    $iunit = ""; $iunit = $item_cunits[$icode[$i]];
    if($birds[$i] != "" || $birds[$i] != 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
	if($item_dlt == ""){ $item_dlt = $item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."$iunit @ ".$rate[$i]; } else{ $item_dlt = $item_dlt.",%0D%0A".$item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."$iunit @ ".$rate[$i]; }

    //Add Transaction
    $from_post = "INSERT INTO `broiler_sales` (`incr`,`prefix`,`trnum`,`date`,`vcode`,`billno`,`icode`,`rcd_qty`,`rate`,`tcds_type`,`tcds_code`,`tcds_type1`,`tcds_per`,`tcds_amt`,`item_tamt`,`round_off`,`finl_amt`,`bal_qty`,`bal_amt`,`avg_price`,`avg_item_amount`,`remarks`,`warehouse`,`farm_batch`,`vehicle_code`,`driver_code`,`active`,`flag`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$tcds_type','$tcds_code','$tcds_type1','$tcds_per','$tcds_amt','$item_tamt[$i]','$round_off','$finl_amt','$rcd_qty[$i]','$finl_amt','$avg_price[$i]','$avg_amount[$i]','$remarks','$warehouse','$farm_batch','$vehicle_code','$driver_code','$active','$flag','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        $item_acc = $icat_iac[$icat_code[$icode[$i]]]; $cus_acc = $control_acc_group[$contact_group[$vcode]];
        $cogs_acc = $icat_cogsac[$icat_code[$icode[$i]]];
        $sale_acc = $icat_sac[$icat_code[$icode[$i]]];
        
        //Add Account Summary
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$item_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cogs_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$avg_price[$i]','$avg_amount[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$cus_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$sale_acc','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }
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
header('location:broiler_display_generalsales1.php?ccid='.$ccid);
?>