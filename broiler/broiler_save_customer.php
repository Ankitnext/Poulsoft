<?php
//broiler_save_customer.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['customer'];

$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("opn_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `opn_trnum` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Opening Balance Trnum' AFTER `saddress`"; mysqli_query($conn,$sql); }
if(in_array("cus_prefix", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cus_prefix` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Bird Receiving Prefix' AFTER `code`"; mysqli_query($conn,$sql); }
if(in_array("processing_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `processing_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Plant Access Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("cus_ccode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cus_ccode` VARCHAR(100) NOT NULL DEFAULT '0' COMMENT 'Customer Code' AFTER `processing_flag`"; mysqli_query($conn,$sql); }
if(in_array("branch_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `branch_code` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Branch' AFTER `groupcode`"; mysqli_query($conn,$sql); }
if(in_array("vdoc_path1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `vdoc_path1` VARCHAR(1500) NULL DEFAULT NULL AFTER `bank_ifsc_code`"; mysqli_query($conn,$sql); }
if(in_array("vdoc_path2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `vdoc_path2` VARCHAR(1500) NULL DEFAULT NULL AFTER `vdoc_path1`"; mysqli_query($conn,$sql); }
if(in_array("vdoc_path3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `vdoc_path3` VARCHAR(1500) NULL DEFAULT NULL AFTER `vdoc_path2`"; mysqli_query($conn,$sql); }
if(in_array("vdoc_path4", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `vdoc_path4` VARCHAR(1500) NULL DEFAULT NULL AFTER `vdoc_path3`"; mysqli_query($conn,$sql); }
if(in_array("vdoc_path5", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `vdoc_path5` VARCHAR(1500) NULL DEFAULT NULL AFTER `vdoc_path4`"; mysqli_query($conn,$sql); }
if(in_array("aadhar_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `aadhar_no` VARCHAR(1500) NULL DEFAULT NULL AFTER `pan_no`"; mysqli_query($conn,$sql); }
      
$sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("vendor_openings", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `vendor_openings` INT(100) NOT NULL DEFAULT '0' COMMENT 'Vendor Openings' AFTER `wapp`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'vendor_openings' AND `active` = '1'";
$query = mysqli_query($conn,$sql); $prx_entry_count = mysqli_num_rows($query);
if($prx_entry_count > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES ('column:flag', 'vendor_openings', 'CSO-', '0', '1:1', '1', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1');"; mysqli_query($conn,$sql); }

$cus_ccode = $_POST['cus_ccode'];
$name = $_POST['cname'];
$cus_prefix = $_POST['cus_prefix'];
$mobile1 = $_POST['mobile'];
$mobile2 = $_POST['mobile2'];
$emails = $_POST['emails'];
$pan_no = $_POST['pan'];
$aadhar_no = $_POST['aadhar_no'];
$birth_date = date("Y-m-d",strtotime($_POST['bday']));

$contacttype = $_POST['stype'];
$groupcode = $_POST['sgrp'];
$gstinno = $_POST['cgstin'];
if($_POST['processing_flag'] == "on" || $_POST['processing_flag'] == true || $_POST['processing_flag'] == 1){ $processing_flag = 1; } else{ $processing_flag = 0; }

$company_details = $_POST['company'];
$state_code = $_POST['state'];
$branch_code = $_POST['branch_code'];
$baddress = $_POST['baddress'];
$saddress = $_POST['saddress'];

if(!empty($_POST['obamount'])){
    $obamt = $_POST['obamount'];
}
else{
    $obamt = "0.00";
}

$obtype = $_POST['obtype'];
$obdate = date("Y-m-d",strtotime($_POST['obdate']));
$obremarks = $_POST['obremarks'];
if(!empty($_POST['ctime'])){
    $creditdays = $_POST['ctime'];
}
else{
    $creditdays = 0;
}

if(!empty($_POST['camount'])){
    $creditamt = $_POST['camount'];
}
else{
    $creditamt = 0;
}

$bank_accno = $_POST['accno'];
$ifsc = $_POST['ifsc'];
$bank = $_POST['bank_details'];

$sql ="SELECT MAX(incr) as incr FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "SC";
if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;

//Uploading Documents
$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Customer Master' AND `field_function` LIKE 'Upload documents' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $updoc_flag = mysqli_num_rows($query); $link_path1 = $link_path2 = $link_path3 = $link_path4 = $link_path5 = "";
if($updoc_flag > 0){
    $dbname = $_SESSION['dbase'];
    //check folder exist or create a folder
    $folder_path = "documents/".$dbname."/customer_documents"; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }
    if(!empty($_FILES["vdoc_link1"]["name"])) {
        $filename = basename($_FILES["vdoc_link1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name = $dbname."_cus_".$code."doc_1.".$filetype; $filetmp = $_FILES['vdoc_link1']['tmp_name']; $link_path1 = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$link_path1);
    }
    if(!empty($_FILES["vdoc_link2"]["name"])) {
        $filename = basename($_FILES["vdoc_link2"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name = $dbname."_cus_".$code."doc_2.".$filetype; $filetmp = $_FILES['vdoc_link2']['tmp_name']; $link_path2 = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$link_path2);
    }
    if(!empty($_FILES["vdoc_link3"]["name"])) {
        $filename = basename($_FILES["vdoc_link3"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name = $dbname."_cus_".$code."doc_3.".$filetype; $filetmp = $_FILES['vdoc_link3']['tmp_name']; $link_path3 = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$link_path3);
    }
    if(!empty($_FILES["vdoc_link4"]["name"])) {
        $filename = basename($_FILES["vdoc_link4"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name = $dbname."_cus_".$code."doc_4.".$filetype; $filetmp = $_FILES['vdoc_link4']['tmp_name']; $link_path4 = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$link_path4);
    }
    if(!empty($_FILES["vdoc_link5"]["name"])) {
        $filename = basename($_FILES["vdoc_link5"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name = $dbname."_cus_".$code."doc_5.".$filetype; $filetmp = $_FILES['vdoc_link5']['tmp_name']; $link_path5 = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$link_path5);
    }
}
$sql = "INSERT INTO `main_contactdetails` (incr,prefix,code,cus_ccode,cus_prefix,name,mobile1,mobile2,emails,pan_no,aadhar_no,birth_date,contacttype,groupcode,gstinno,company_details,state_code,branch_code,baddress,saddress,obamt,obtype,obdate,obremarks,creditdays,creditamt,bank_accno,bank,vdoc_path1,vdoc_path2,vdoc_path3,vdoc_path4,vdoc_path5,flag,active,dflag,processing_flag,addedemp,addedtime,updatedtime) 
value('$incr','$prefix','$code','$cus_ccode','$cus_prefix','$name','$mobile1','$mobile2','$emails','$pan_no','$aadhar_no','$birth_date','$contacttype','$groupcode','$gstinno','$company_details','$state_code','$branch_code','$baddress','$saddress','$obamt','$obtype','$obdate','$obremarks','$creditdays','$creditamt','$bank_accno','$bank','$link_path1','$link_path2','$link_path3','$link_path4','$link_path5','0','1','0','$processing_flag','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
else {
    if($obamt != "" && (float)$obamt != 0){
        $sql = "SELECT * FROM `main_groups` WHERE `code` = '$groupcode'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $control_acc_group = $row['cus_controller_code']; }

        $sql = "SELECT *  FROM `acc_coa` WHERE `description` LIKE 'Opening Balance Equity' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        $ccount = mysqli_num_rows($query);
        if($ccount > 0){
            while($row = mysqli_fetch_assoc($query)){
                $opn_code = $row['code'];
            }
        }
        
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$obdate' AND `tdate` >= '$obdate'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$obdate' AND `tdate` >= '$obdate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $vendor_openings = $row['vendor_openings']; } $incr = $vendor_openings + 1;

        $sql = "UPDATE `master_generator` SET `vendor_openings` = '$incr' WHERE `fdate` <='$obdate' AND `tdate` >= '$obdate' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'vendor_openings' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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

        $sql = "UPDATE `main_contactdetails` SET `opn_trnum` = '$trnum' WHERE `code` = '$code'";
        if(!mysqli_query($conn, $sql)){ die("Error-1".mysqli_error($conn)); }
        else{
            if($obtype == "Cr"){
                $coa_Cr = $control_acc_group; $coa_Dr = $opn_code;
            }
            if($obtype == "Dr"){
                $coa_Dr = $control_acc_group; $coa_Cr = $opn_code;
            }
            
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,amount,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$obdate','$code','','$trnum','$obamt','$obremarks','0','Customer Openings','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
            else{
                $to_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,dc_no,trnum,amount,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('DR','$coa_Dr','$obdate','$code','','$trnum','$obamt','$obremarks','0','Customer Openings','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$to_post)){ die("Error 3:-".mysqli_error($conn)); }
                else{ }
            }
        }
    }
    header('location:broiler_display_customer.php?ccid='.$ccid);
}

?>