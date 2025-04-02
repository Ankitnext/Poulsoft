<?php
//broiler_save_contraaccount2.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['contraaccount2'];

//Fetch Columns from table
$columns = $new_column = array();
$sql = "SHOW COLUMNS FROM `master_generator`"; $query = mysqli_query($conn,$sql); $i = 0; while($row = mysqli_fetch_assoc($query)){ $columns[$i]=$row['Field']; $i++; }
//Create Columns in table
$new_column = array('contra_note'); $diff_array = array_diff($new_column,$columns);
if(in_array('contra_note',$diff_array)){ $sql = "ALTER TABLE `master_generator` ADD `contra_note` int(100) NULL DEFAULT 0 AFTER `prate`"; mysqli_query($conn,$sql); }

//Verify and Add Prefix Master
$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` = 'contra_note' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $pcount = mysqli_num_rows($query);
if($pcount > 0){ }
else{
    $sql = "INSERT INTO `prefix_master` (`id`, `format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES (NULL, 'column:flag', 'contra_note', 'CONT-', '0', '1:1', '0', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1')";
    mysqli_query($conn,$sql);
}

//Check Column Availability
$sql='SHOW COLUMNS FROM `account_contranotes`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("to_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_contranotes` ADD `to_batch` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `tcoa`"; mysqli_query($conn,$sql); }

//Customer & Supplier Accounts
$sql = "SELECT * FROM `main_groups`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cus_control_code[$row['code']] = $row['cus_controller_code']; $sup_control_code[$row['code']] = $row['sup_controller_code']; }

$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['groupcode']; $contact_type[$row['code']] = $row['contacttype']; }

$sql = "SELECT * FROM `acc_coa` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; $coa_mobile[$row['code']] = $row['mobile_no']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['name']; $coa_mobile[$row['code']] = $row['mobile1']; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
	
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Contra Note' AND `field_function` LIKE 'Send ContraNote Wapp Message' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $contranote_wapp = mysqli_num_rows($query);

//Farmer Accounts
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['farmer_code']; $contact_type[$row['code']] = "F"; }
$sql = "SELECT * FROM `broiler_farmergroup`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $payable_acc_group[$row['code']] = $row['pay_acc_code']; }
$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['farmer_group']; }

//Transaction Details
$date = $dcno = $fcoa = $tcoa = $to_batch = $amount = $sector = $remark = array();

$i = 0; foreach($_POST['date'] as $dates){ $date[$i]= date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i]= $dcnos; $i++; }
$i = 0; foreach($_POST['fcoa'] as $fcoas){ $fcoa[$i]= $fcoas; $i++; }
$i = 0; foreach($_POST['tcoa'] as $tcoas){ $tcoa[$i]= $tcoas; $i++; }
$i = 0; foreach($_POST['to_batch'] as $to_batchs){ $to_batch[$i]= $to_batchs; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i]= $amounts; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i]= $sectors; $i++; }
$i = 0; foreach($_POST['remark'] as $remarks){ $remark[$i]= $remarks; $i++; }
$flag = 0;
$active = 1;
$dflag = 0;

$dsize = sizeof($fcoa);
for($i = 0;$i < $dsize;$i++){
    //Generate Invoice transaction number format
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $contra_note = $row['contra_note']; } $incr = $contra_note + 1;

    $sql = "UPDATE `master_generator` SET `contra_note` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` = 'contra_note' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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

    if($amount[$i] == "" || $amount[$i] == NULL || $amount[$i] == 0 || $amount[$i] == "0.00"){ $amount[$i] = "0.00"; }

    //Add Transaction
    $from_post = "INSERT INTO `account_contranotes` (incr,prefix,trnum,type,date,fcoa,tcoa,to_batch,amount,warehouse,dcno,remarks,flag,active,dflag,addedemp,addedtime,updatedtime)
	VALUES ('$incr','$prefix','$trnum','ContraNote','$date[$i]','$fcoa[$i]','$tcoa[$i]','$to_batch[$i]','$amount[$i]','$sector[$i]','$dcno[$i]','$remark[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
	if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        //Add Account Summary
        if(!empty($contact_group[$tcoa[$i]])){
            $gcode = $to_coa = $to_vendor = ""; $gcode = $contact_group[$tcoa[$i]];
            if($contact_type[$tcoa[$i]] == "S"){ $to_coa = $sup_control_code[$gcode]; }
            else if($contact_type[$tcoa[$i]] == "F"){
                $fmr_code = $farmer_code[$tcoa[$i]];
                $gcode = $contact_group[$fmr_code];
                $to_coa = $payable_acc_group[$gcode];
            }
            else{ $to_coa = $cus_control_code[$gcode]; }
            $to_vendor = $tcoa[$i];
        }
        else{ $to_vendor = $to_coa = ""; $to_coa = $tcoa[$i]; }

        if(!empty($contact_group[$fcoa[$i]])){
            $gcode = $from_coa = $from_vendor = ""; $gcode = $contact_group[$fcoa[$i]];
            if($contact_type[$fcoa[$i]] == "S"){ $from_coa = $sup_control_code[$gcode]; }
            else if($contact_type[$fcoa[$i]] == "F"){
                $fmr_code = $farmer_code[$fcoa[$i]];
                $gcode = $contact_group[$fmr_code];
                $from_coa = $payable_acc_group[$gcode];
            }
            else{ $from_coa = $cus_control_code[$gcode]; }
            $from_vendor = $fcoa[$i];
        }
        else{ $from_vendor = $from_coa = ""; $from_coa = $fcoa[$i]; }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$to_coa','$date[$i]','$to_vendor','$trnum','','0.00','0','$amount[$i]','$sector[$i]','','$remark[$i]','0','ContraNote','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$from_coa','$date[$i]','$from_vendor','$trnum','','0.00','0','$amount[$i]','$sector[$i]','','$remark[$i]','0','ContraNote','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
    }

}
header('location:broiler_display_contraaccount2.php?ccid='.$ccid);
?>