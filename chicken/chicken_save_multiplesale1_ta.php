<?php
//chicken_save_multiplesale1_ta.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "number_format_ind.php";
include "chicken_generate_trnum_details.php";
include "chicken_send_wapp_master2.php";
include "cus_outbalfunction.php";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `sms_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("file_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `sms_details` ADD `file_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'File Name' AFTER `smsto`"; mysqli_query($conn,$sql); }

//Check and Insert WhatsApp Details
$file_name = "chicken_display_multiplesale1_ta.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
$sql = "SELECT * FROM `whatsapp_keygenerate_master` WHERE `file_type` = 'Multiple Sale' AND `file_name` = '$file_name' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $w_cnt = mysqli_num_rows($query);
if($w_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $sms_type = $row['sms_type']; $wapp_ptrn = $row['pattern']; } }

//Fetch Company Details
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }

//Check Message Flags
$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'";
$query = mysqli_query($conn,$sql); $sales_sms_flag = 0; $sales_wapp_flag = 0;
while($row = mysqli_fetch_assoc($query)){ $sales_sms_flag = $row['sales_sms']; $sales_wapp_flag = $row['sales_wapp']; }

//Item Names
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

//Fetch Account Modes
$sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }

//Receipt Cash Method
$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Cash In Hand'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cash_code = $row['code']; }

//Fetch SMS Key Details
$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'MSLSWRCT2' AND  `msg_type` = 'SMS' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $sms_user = $row['sms_user'];
    $sms_key = $row['sms_key'];
    $sms_msg_key = $row['msg_key'];
    $sms_accusage = $row['sms_accusage'];
    $sms_senderid = $row['sms_senderid'];
    $sms_entityid = $row['sms_entityid'];
    $sms_tempid = $row['sms_tempid'];
}

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$itemcode = $_POST['itemcode'];
$warehouse = $_POST['warehouse'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];
$tcds_per = $_POST['tcds_per'];

$vcode = $bookinvoice = $jals = $birds = $tweight = $eweight = $nweight = $price = $item_amt = $tcds_amt = $rct_amt1 = $bank_method1 = $rct_amt2 = $roundoff = $finaltotal = $remarks = array();
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['bookinvoice'] as $bookinvoices){ $bookinvoice[$i] = $bookinvoices; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['tcds_amt'] as $tcds_amts){ $tcds_amt[$i] = $tcds_amts; $i++; }
$i = 0; foreach($_POST['rct_amt1'] as $rct_amt1s){ $rct_amt1[$i] = $rct_amt1s; $i++; }
$i = 0; foreach($_POST['bank_method1'] as $bank_method1s){ $bank_method1[$i] = $bank_method1s; $i++; }
$i = 0; foreach($_POST['rct_amt2'] as $rct_amt2s){ $rct_amt2[$i] = $rct_amt2s; $i++; }
$i = 0; foreach($_POST['roundoff'] as $roundoffs){ $roundoff[$i] = $roundoffs; $i++; }
$i = 0; foreach($_POST['finaltotal'] as $finaltotals){ $finaltotal[$i] = $finaltotals; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$tcds_type1 = $roundoff_type1 = "auto";
$tcds_type2 = "add";
$transporter_code = "";
$freight_amt = 0;

$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "multiplesale1_ta";
$trlink = "chicken_display_multiplesale1_ta.php";
$sale_type = "";

$dsize = sizeof($vcode);
for($i = 0;$i < $dsize;$i++){
    //Save Sale Transaction
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($rct_amt1[$i] == ""){ $rct_amt1[$i] = 0; }
    if($rct_amt2[$i] == ""){ $rct_amt2[$i] = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($finaltotal[$i] == ""){ $finaltotal[$i] = 0; }
    if($roundoff[$i] == ""){ $roundoff[$i] = 0; }

    $net_amt1 = (float)$item_amt[$i] + (float)$tcds_amt[$i];
    if((float)$roundoff[$i] >= 0){ $roundoff_type2 = "add"; } else{ $roundoff_type2 = "deduct"; }

    //Generate Transaction No.
    $incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"multiplesale1_ta","MSII","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];

    $sql = "INSERT INTO `customer_sales` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`bookinvoice`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`freight_amt`,`tcdsper`,`tcds_type1`,`tcds_type2`,`tcdsamt`,`roundoff_type1`,`roundoff_type2`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`drivercode`,`vehiclecode`,`flag`,`active`,`authorization`,`tdflag`,`pdflag`,`trtype`,`trlink`,`sale_type`,`addedemp`,`addedtime`,`updated`,`client`) 
    VALUES ('$incr','$d','$m','$y','$fy','$date','$invoice','$bookinvoice[$i]','$vcode[$i]','$itemcode','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$freight_amt','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt[$i]','$roundoff_type1','$roundoff_type2','$roundoff[$i]','$finaltotal[$i]','$finaltotal[$i]','$warehouse','$remarks[$i]','$driver','$vehicle','$flag','$active','$authorization','$tdflag','$pdflag','$trtype','$trlink','$sale_type','$addedemp','$addedtime','$addedtime','$client')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    //Save Receipt-1 Transaction
    if((float)$rct_amt1[$i] > 0){
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
    
        $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,cdate,cno,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
        VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode[$i]','$bookinvoice[$i]','$cash_mode','$cash_code',NULL,NULL,'$rct_amt1[$i]','C','$warehouse','$remarks[$i]','$flag','$active','$addedemp','$addedtime','$tdflag','$pdflag','$client')";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
        $date_sms = date("d.m.Y",strtotime($date));    
    }
    //Save Receipt-2 Transaction
    if((float)$rct_amt2[$i] > 0){
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
    
        $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,cdate,cno,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag,client)
        VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode[$i]','$bookinvoice[$i]','$bank_mode','$bank_method1[$i]',NULL,NULL,'$rct_amt2[$i]','C','$warehouse','$remarks[$i]','$flag','$active','$addedemp','$addedtime','$tdflag','$pdflag','$client')";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
        $date_sms = date("d.m.Y",strtotime($date));    
    }

    //Send Messages
    if((int)$sales_sms_flag == 1 || (int)$sales_wapp_flag == 1){
        $item_dlt = $cusdet = $customer_name = $customer_mobile = ""; $cob_dt = array(); $cus_obamt = $cus_cbamt = 0;
        if($birds[$i] != ""){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_name[$itemcode].": ".$item_birds."".$nweight[$i]."Kgs @ ".$price[$i];

        $cusdet = customer_outbalance($vcode[$i]); $cob_dt = explode("@",$cusdet);
        $customer_name = $cob_dt[0]; $customer_mobile = "91".$cob_dt[1]; $cus_obamt = $cob_dt[2];
        $cus_cbamt = (((float)$cus_obamt + (float)$finaltotal[$i]) - ((float)$rct_amt1[$i] + (float)$rct_amt2[$i]));

        if((int)$sales_sms_flag == 1){
            if(!$conn){ }
            else{
$xml_data ='<?xml version="1.0"?>
<parent>
<child>
<user>'.$sms_user.'</user>
<key>'.$sms_key.'</key>
<mobile>'.$customer_mobile.'</mobile>
<message>
Dear: '.$customer_name.'
Date: '.date("d.m.Y",strtotime($date)).',
'.$item_dlt.',
Sale Amt: Rs. '.number_format_ind($finaltotal[$i]).'/-
Balance: Rs. '.number_format_ind(((float)$cus_obamt + (float)$finaltotal[$i])).'/-
Received: Rs. '.number_format_ind(((float)$rct_amt1[$i] + (float)$rct_amt2[$i])).'/-
Closing Bal: Rs. '.number_format_ind($cus_cbamt).'/-
Thank You,
'.$cdetails.'
'.$sms_msg_key.'</message>
<accusage>'.$sms_accusage.'</accusage>
<senderid>'.$sms_senderid.'</senderid>
<entityid>'.$sms_entityid.'</entityid>
<tempid>'.$sms_tempid.'</tempid>
</child></parent>';
                $URL = "http://mobicomm.dove-sms.com//submitsms.jsp?";
                $ch = curl_init($URL);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($ch);
                curl_close($ch);
                $status = explode(",",$output);

                $sms_date = date("Y-m-d");
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$sms_date' AND `tdate` >= '$sms_date' AND `type` = 'transactions'";
                $query = mysqli_query($conn,$sql); $sms = 0; while($row = mysqli_fetch_assoc($query)){ $sms = $row['sms']; } $sincr = $sms + 1;
                $sql = "UPDATE `master_generator` SET `sms` = '$sincr' WHERE `fdate` <='$sms_date' AND `tdate` >= '$sms_date' AND `type` = 'transactions'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                if($sincr < 10){ $sincr = '000'.$sincr; } else if($sincr >= 10 && $sincr < 100){ $sincr = '00'.$sincr; } else if($sincr >= 100 && $sincr < 1000){ $sincr = '0'.$sincr; } else { }
                $sms_code = "SMS-".$sincr;

                $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,addedemp,addedtime,updatedtime,client) 
                VALUES ('$sms_code','$vcode[$i]','$customer_mobile','$xml_data','$status[1]','SALES','$addedemp','$addedtime','$addedtime','$client')";
                if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); } else{  }
            }
        }
        if((int)$sales_wapp_flag == 1){
            if(!$conn){ }
            else{
                if($wapp_ptrn == "Template"){
                    $msg1 = array("dear"=>$customer_name, "date"=>date("d.m.Y",strtotime($date)), "item_dt1"=>$item_dlt."/-", "samount"=>number_format_ind($finaltotal[$i]), "ramount"=>number_format_ind(((float)$rct_amt1[$i] + (float)$rct_amt2[$i])), "balance"=>number_format_ind($cus_cbamt)."/-", "cdetails"=>$cdetails);
                    $message = json_encode($msg1);
                }
                else{
                    $message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".number_format_ind($finaltotal[$i])."/-,%0D%0AReceived Amt: ".number_format_ind(((float)$rct_amt1[$i] + (float)$rct_amt2[$i]))."/-%0D%0ABalance: Rs. ".number_format_ind($cus_cbamt)."/-%0D%0AThank You,%0D%0A".$cdetails;
                    $message = str_replace(" ","+",$message);
                }
                
                $wapp_date = date("Y-m-d");
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                $query = mysqli_query($conn,$sql); $wapp = 0; while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $wincr = $wapp + 1;
                $sql = "UPDATE `master_generator` SET `wapp` = '$wincr' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                if($wincr < 10){ $wincr = '000'.$wincr; } else if($wincr >= 10 && $wincr < 100){ $wincr = '00'.$wincr; } else if($wincr >= 100 && $wincr < 1000){ $wincr = '0'.$wincr; } else { }
                
                $database = $_SESSION['dbase'];
                $wapp_type = "Invoice Message";
                $trnum = $invoice;
                $ccode = $vcode[$i];
                $number = $customer_mobile;
                $wapp_code = "WAPP-".$wincr;
                $msg_type = "WAPP";
                $msg_project = "CTS";
                $status = "CREATED";
                $wapp_link = $_SERVER['REQUEST_URI'];
                $wapp_msg = $message;
                $send_type = "text";
                chicken_send_wapp_text($database,$wapp_type,$trnum,$ccode,$number,$wapp_code,$sms_type,$msg_type,$msg_project,$status,$wapp_link,$wapp_msg,$send_type,$wapp_ptrn);
            }
        }
    }
}

//Store Latest Date to Session
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'chicken_display_multiplesale1.php' AND `field_function` = 'Auto Select Previously Changed Date' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $date_asflag = mysqli_num_rows($query);
if((int)$date_asflag > 0){ $_SESSION['multiplesale1_asdate'] = date("Y-m-d",strtotime($date)); }


header('location:chicken_display_multiplesale1_ta.php?ccid='.$ccid);

