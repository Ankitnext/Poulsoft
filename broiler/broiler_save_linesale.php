<?php
//broiler_save_feedsale_lsfi.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['linesale'];
$user_code = $_SESSION['userid'];
include "number_format_ind.php";
include "broiler_fetch_customerbalance.php";

/*Check send message flag*/
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SaleAutoWapp:broiler_display_linesales.php'";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); /*SWRCT: Sale with Receipt*/
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $sale_wapp = $row['flag']; } } else{ $sale_wapp = 0; }
if($sale_wapp == "" || $sale_wapp == 0 || $sale_wapp == "0.00" || $sale_wapp == NULL){ $sale_wapp = 0; }

$today = date("Y-m-d");

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


$avg_price = $avg_amount = array();
//Transaction Details
$date = date("Y-m-d",strtotime($_POST['date']));
$billno = $_POST['billno'];
$vcode = $_POST['vcode'];
$warehouse = $_POST['warehouse'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i]= $icodes; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i]= $birdss; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i]= $rcd_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i]= $rates; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i]= $item_tamts; $i++; }
$i = 0; foreach($_POST['avg_wt'] as $avg_wts){ $avg_wt1[$i]= $avg_wts; $i++; }
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
$flag = 0;
$active = 1;
$dflag = 0;
$sale_type = "FeedSingleSale";

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

    //$birds[$i] = 0;
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = "0.00"; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = "0.00"; }
    if($item_tamt[$i] == "" || $item_tamt[$i] == NULL || $item_tamt[$i] == 0 || $item_tamt[$i] == "0.00"){ $item_tamt[$i] = "0.00"; }
    if($avg_price[$i] == "" || $avg_price[$i] == NULL || $avg_price[$i] == 0 || $avg_price[$i] == "0.00"){ $avg_price[$i] = "0.00"; }
    if($avg_amount[$i] == "" || $avg_amount[$i] == NULL || $avg_amount[$i] == 0 || $avg_amount[$i] == "0.00"){ $avg_amount[$i] = "0.00"; }
    if($freight_amt == "" || $freight_amt == NULL || $freight_amt == 0 || $freight_amt == "0.00"){ $freight_amt = "0.00"; }
    if($profit_amount == "" || $profit_amount == NULL || $profit_amount == 0 || $profit_amount == "0.00"){ $profit_amount = "0.00"; }


    $cust_date = mysqli_fetch_assoc(mysqli_query($conn,"SELECT date FROM `customer_price_new` WHERE ccode = '$vcode ' AND itemcode = '$icode[$i]' AND price_type = 'A' AND dflag = 0 AND active = 1 ORDER BY date DESC LIMIT 0,1"))['date'];
    if($cust_date != ''){
        $sql = "SELECT * FROM `customer_price_new` WHERE ccode = '$vcode' AND itemcode = '$icode[$i]' AND price_type = 'A' AND date = '$cust_date'  AND dflag = 0 AND active = 1 ";

        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){

            if($row['transction_type'] == 'Commission'){
                if($row['per_type'] == 'Kgs'){

                    $commission_amount =  $rcd_qty[$i] * $row['value'];

                }elseif($row['per_type'] == 'Numbers'){

                    $commission_amount =  $birds[$i] * $row['value'];

                }
                

            }else if($row['transction_type'] == 'Freight'){

                if($row['per_type'] == 'Kgs'){

                    $cust_freight_amount =  $rcd_qty[$i] * $row['value'];

                }elseif($row['per_type'] == 'Numbers'){

                    $cust_freight_amount =  $birds[$i] * $row['value'];

                }


            }
            
        }
    }else{
        $commission_amount = 0;
        $cust_freight_amount = 0;
    }


    //Prepare Item Details for Message
    if($birds[$i] != "" || $birds[$i] != 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
	if($item_dlt == ""){ $item_dlt = $item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."Kgs @ ".$rate[$i]; } else{ $item_dlt = $item_dlt.",%0D%0A".$item_name[$icode[$i]].": ".$item_birds."".$rcd_qty[$i]."Kgs @ ".$rate[$i]; }

    //Add Transaction
    $from_post = "INSERT INTO `broiler_sales` (customer_freight_amt,customer_commision_amt,incr,prefix,trnum,date,vcode,billno,icode,birds,rcd_qty,rate,item_tamt,freight_type,freight_amt,freight_pay_type,freight_pay_acc,freight_acc,finl_amt,bal_qty,bal_amt,avg_price,avg_item_amount,avg_final_amount,profit,remarks,warehouse,farm_batch,farm_mnu_name,batch_mnu_name,vehicle_code,driver_code,active,flag,dflag,sale_type,addedemp,addedtime,updatedtime) 
    VALUES ('$cust_freight_amount','$commission_amount','$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$birds[$i]','$rcd_qty[$i]','$rate[$i]','$item_tamt[$i]','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$final_total','$rcd_qty[$i]','$final_total','$avg_price[$i]','$avg_amount[$i]','$final_avg_total','$profit_amount','$remarks','$warehouse','$farm_batch','$farm_mnu_name[$i]','$batch_mnu_name[$i]','$vehicle_code','$driver_code','$active','$flag','$dflag','$sale_type','$addedemp','$addedtime','$addedtime')";
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
//Transportation cost Summary
if($freight_amt != "" && $freight_amt > 0){
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); } else{ }

    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$freight_pay_acc','$date','$vcode','$trnum','','0','0','$freight_amt','$warehouse','$farm_batch','$vehicle_code','$driver_code','$remarks','0','Sales','0','1','0','$addedemp','$addedtime','$addedtime')";
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
header('location:broiler_display_linesales.php?ccid='.$ccid);
?>