<?php
//broiler_save_vouchers.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['vouchers'];

$sql = "SELECT * FROM `acc_coa` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; $coa_mobile[$row['code']] = $row['mobile_no']; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
	
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Salary Voucher' AND `field_function` LIKE 'Send Salary Wapp Message' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $empsal_wapp = mysqli_num_rows($query);
			
//Transaction Details
$voucher_type = $_POST['voucher_type'];
$i = 0; foreach($_POST['date'] as $dates){ $date[$i]= date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i]= $dcnos; $i++; }
$i = 0; foreach($_POST['fcoa'] as $fcoas){ $fcoa[$i]= $fcoas; $i++; }
$i = 0; foreach($_POST['tcoa'] as $tcoas){ $tcoa[$i]= $tcoas; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i]= $amounts; $i++; }
$i = 0; foreach($_POST['cheque_no'] as $cheque_nos){ $cheque_no[$i]= $cheque_nos; $i++; }
$i = 0; foreach($_POST['sector'] as $sectors){ $sector[$i]= $sectors; $i++; }
$i = 0; foreach($_POST['remark'] as $remarks){ $remark[$i]= $remarks; $i++; }
$flag = 0;
$active = 1;
$dflag = 0;

if($voucher_type == "PayVoucher"){ $prefix_type = " AND `transaction_type` LIKE 'PaymentVoucher'"; }
else if($voucher_type == "RctVoucher"){ $prefix_type = " AND `transaction_type` LIKE 'ReceiptVoucher'"; }
else if($voucher_type == "JorVoucher"){ $prefix_type = " AND `transaction_type` LIKE 'JournalVoucher'"; }
else{ $prefix_type = " AND `transaction_type` LIKE 'PaymentVoucher'"; }
$dsize = sizeof($fcoa);
for($i = 0;$i < $dsize;$i++){
    //Send Employee Salary Message
    if($empsal_wapp > 0){
        $from_coa = $to_coa = ""; $from_coa = $fcoa[$i]; $to_coa = $tcoa[$i];
        if(!empty($coa_mobile[$from_coa]) && $coa_mobile[$from_coa] != "" && strlen($coa_mobile[$from_coa]) == 10){
            $cr_amount = $dr_amount = 0;
            $sql1 = "SELECT SUM(amount) as amount,crdr FROM `account_summary` WHERE `coa_code` = '$from_coa' AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
            $query1 = mysqli_query($conn,$sql1); $fcount = mysqli_num_rows($query1);
            if($fcount > 0){
                while($row1 = mysqli_fetch_assoc($query1)){
                    if($row1['crdr'] == "CR"){
                        if(number_format_ind($row1['amount']) != "0.00"){ $cr_amount = $row1['amount']; } else{ $cr_amount = 0; }
                    }
                    else if($row1['crdr'] == "DR"){
                        if(number_format_ind($row1['amount']) != "0.00"){ $dr_amount = $row1['amount']; } else{ $dr_amount = 0; }
                    }
                    else{ }
                }
            }
            else{ $cr_amount = $dr_amount = 0; }
            
            $cur_cramt = $opening_bal_amount = $closing_bal_amount = 0; $cur_cramt = (float)$amount[$i];
            $opening_bal_amount = ((float)$cr_amount - (float)$dr_amount);
            $closing_bal_amount = (((float)$cr_amount + (float)$cur_cramt) - (float)$dr_amount);
            
            $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-Salary' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $instance_id = $row['sms_key']; $access_token = $row['msg_key']; $url_id = $row['url_id']; }

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

            $message = "";
            $message = "Dear: ".$coa_name[$from_coa]."%0D%0ADate: ".date('d.m.Y',strtotime($date[$i]))."%0D%0AOpening Balance: ".number_format_ind($opening_bal_amount)."/-%0D%0ASalary: ".number_format_ind($cur_cramt)."/-%0D%0AClosing Balance: ".number_format_ind($closing_bal_amount)."/-%0D%0ARemarks: ".$remark[$i]."%0D%0AThank You,%0D%0A".$cdetails;
            $message = str_replace(" ","+",$message);

            $wapp_date = date("Y-m-d");
            $ccode = $from_coa;
            $number = "91".$coa_mobile[$from_coa]; $type = "text";

            if((int)$url_id == 3){ $msg_info = $curlopt_url.''.$instance_id.'/messages/chat?token='.$access_token.'&to='.$number.'&body='.$message; }
            else{ $msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token; }
            
            $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
            $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
            $wapp_code = "WAPP-".$incr_wapp;
            $wsfile_path = $_SERVER['REQUEST_URI'];

            $database = $_SESSION['dbase'];
            $trtype = "BB-Salary";
            $trnum = "";
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
        else if(!empty($coa_mobile[$to_coa]) && $coa_mobile[$to_coa] != "" && strlen($coa_mobile[$to_coa]) == 10){
            $cr_amount = $dr_amount = 0;
            $sql1 = "SELECT SUM(amount) as amount,crdr FROM `account_summary` WHERE `coa_code` = '$to_coa' AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
            $query1 = mysqli_query($conn,$sql1); $fcount = mysqli_num_rows($query1);
            if($fcount > 0){
                while($row1 = mysqli_fetch_assoc($query1)){
                    if($row1['crdr'] == "CR"){
                        if(number_format_ind($row1['amount']) != "0.00"){ $cr_amount = $row1['amount']; } else{ $cr_amount = 0; }
                    }
                    else if($row1['crdr'] == "DR"){
                        if(number_format_ind($row1['amount']) != "0.00"){ $dr_amount = $row1['amount']; } else{ $dr_amount = 0; }
                    }
                    else{ }
                }
            }
            else{ $cr_amount = $dr_amount = 0; }
            
            $cur_cramt = $opening_bal_amount = $closing_bal_amount = 0; $cur_cramt = (float)$amount[$i];
            $opening_bal_amount = ((float)$cr_amount - (float)$dr_amount);
            $closing_bal_amount = (((float)$cr_amount) - ((float)$dr_amount + (float)$cur_cramt));
            
            $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-Salary' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $instance_id = $row['sms_key']; $access_token = $row['msg_key']; $url_id = $row['url_id']; }

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
            
            $message = "";
            $message = "Dear: ".$coa_name[$to_coa]."%0D%0ADate: ".date('d.m.Y',strtotime($date[$i]))."%0D%0AOpening Balance: ".number_format_ind($opening_bal_amount)."/-%0D%0AReceived Amount: ".number_format_ind($cur_cramt)."/-%0D%0AClosing Balance: ".number_format_ind($closing_bal_amount)."/-%0D%0ARemarks: ".$remark[$i]."%0D%0AThank You,%0D%0A".$cdetails;
            $message = str_replace(" ","+",$message);

            $wapp_date = date("Y-m-d");
            $ccode = $to_coa;
            $number = "91".$coa_mobile[$to_coa]; $type = "text";

            if((int)$url_id == 3){ $msg_info = $curlopt_url.''.$instance_id.'/messages/chat?token='.$access_token.'&to='.$number.'&body='.$message; }
            else{ $msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token; }
            
            $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
            $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
            if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
            $wapp_code = "WAPP-".$incr_wapp;
            $wsfile_path = $_SERVER['REQUEST_URI'];

            $database = $_SESSION['dbase'];
            $trtype = "BB-Salary";
            $trnum = "";
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
        else{ }
    }
    //Generate Invoice transaction number format
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($voucher_type == "PayVoucher"){ $voucher_incr = $row['pvouchers']; } else if($voucher_type == "RctVoucher"){ $voucher_incr = $row['rvouchers']; } else if($voucher_type == "JorVoucher"){ $voucher_incr = $row['jvouchers']; } else{ $voucher_incr = $row['pvouchers']; } }

    $incr = $voucher_incr + 1;
    if($voucher_type == "PayVoucher"){ 
        $sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    else if($voucher_type == "RctVoucher"){
        $sql = "UPDATE `master_generator` SET `rvouchers` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    else if($voucher_type == "JorVoucher"){
        $sql = "UPDATE `master_generator` SET `jvouchers` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    else{
        $sql = "UPDATE `master_generator` SET `pvouchers` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }

    $sql = "SELECT * FROM `prefix_master` WHERE `active` = '1'".$prefix_type; $query = mysqli_query($conn,$sql);
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
    $from_post = "INSERT INTO `account_vouchers` (incr,prefix,trnum,type,date,fcoa,tcoa,amount,amtinwords,warehouse,dcno,cheque_no,remarks,flag,active,dflag,addedemp,addedtime,updatedtime)
	VALUES ('$incr','$prefix','$trnum','$voucher_type','$date[$i]','$fcoa[$i]','$tcoa[$i]','$amount[$i]','','$sector[$i]','$dcno[$i]','$cheque_no[$i]','$remark[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
	if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
    else{
        //Add Account Summary
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,cheque_no,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$tcoa[$i]','$date[$i]','$dcno[$i]','$cheque_no[$i]','','$trnum','','0.00','0','$amount[$i]','$sector[$i]','','$remark[$i]','0','$voucher_type','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,cheque_no,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$fcoa[$i]','$date[$i]','$dcno[$i]','$cheque_no[$i]','','$trnum','','0.00','0','$amount[$i]','$sector[$i]','','$remark[$i]','0','$voucher_type','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
    }

}
header('location:broiler_display_vouchers.php?ccid='.$ccid);
?>