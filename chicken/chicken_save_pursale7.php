<?php
//chicken_save_pursale7.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "number_format_ind.php";
include "chicken_generate_trnum_details.php";
include "chicken_send_wapp_master2.php";
include "cus_outbalfunction.php";

//SMS and WAPP Sending
//Check and Insert WhatsApp Details
$file_name = "chicken_display_pursale7.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
$sql = "SELECT * FROM `whatsapp_keygenerate_master` WHERE `file_type` = 'Purchase Sale' AND `file_name` = '$file_name' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $w_cnt = mysqli_num_rows($query);
if($w_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $sms_type = $row['sms_type']; $wapp_ptrn = $row['pattern']; } }

//Fetch Company Details
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }

//Check Message Flags
$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'";
$query = mysqli_query($conn,$sql); $sales_sms_flag = 0; $sales_wapp_flag = 0;
while($row = mysqli_fetch_assoc($query)){ $sales_sms_flag = $row['sales_sms']; $sales_wapp_flag = $row['sales_wapp']; }

//Fetch SMS Key Details
$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'sales' AND  `msg_type` = 'SMS' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $sms_user = $row['sms_user'];
    $sms_key = $row['sms_key'];
    $sms_msg_key = $row['msg_key'];
    $sms_accusage = $row['sms_accusage'];
    $sms_senderid = $row['sms_senderid'];
    $sms_entityid = $row['sms_entityid'];
    $sms_tempid = $row['sms_tempid'];
}

$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$itemcode = $_POST['itemcode'];
$warehouse = $_POST['warehouse'];

$vendorcode = $supbrh_code = $bookinvoice = $jals = $birds = $tweight = $eweight = $nweight = $sup_prc = $sup_amt = $tds_per = $tds_amt = $roff_samt = $sup_famt = 
$customercode = $cus_prc = $cus_amt = $tcs_per = $tcs_amt = $roff_camt = $cus_famt = $vehiclecode = $drivercode = $remarks = array();
$i = 0; foreach($_POST['vendorcode'] as $vendorcodes){ $vendorcode[$i] = $vendorcodes; $i++; }
$i = 0; foreach($_POST['supbrh_code'] as $supbrh_codes){ $supbrh_code[$i] = $supbrh_codes; $i++; }
$i = 0; foreach($_POST['bookinvoice'] as $bookinvoices){ $bookinvoice[$i] = $bookinvoices; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['sup_prc'] as $sup_prcs){ $sup_prc[$i] = $sup_prcs; $i++; }
$i = 0; foreach($_POST['sup_amt'] as $sup_amts){ $sup_amt[$i] = $sup_amts; $i++; }
$i = 0; foreach($_POST['tds_per'] as $tds_pers){ $tds_per[$i] = $tds_pers; $i++; }
$i = 0; foreach($_POST['tds_amt'] as $tds_amts){ $tds_amt[$i] = $tds_amts; $i++; }
$i = 0; foreach($_POST['roff_samt'] as $roff_samts){ $roff_samt[$i] = $roff_samts; $i++; }
$i = 0; foreach($_POST['sup_famt'] as $sup_famts){ $sup_famt[$i] = $sup_famts; $i++; }

$i = 0; foreach($_POST['customercode'] as $customercodes){ $customercode[$i] = $customercodes; $i++; }
$i = 0; foreach($_POST['cus_prc'] as $cus_prcs){ $cus_prc[$i] = $cus_prcs; $i++; }
$i = 0; foreach($_POST['cus_amt'] as $cus_amts){ $cus_amt[$i] = $cus_amts; $i++; }
$i = 0; foreach($_POST['tcs_per'] as $tcs_pers){ $tcs_per[$i] = $tcs_pers; $i++; }
$i = 0; foreach($_POST['tcs_amt'] as $tcs_amts){ $tcs_amt[$i] = $tcs_amts; $i++; }
$i = 0; foreach($_POST['roff_camt'] as $roff_camts){ $roff_camt[$i] = $roff_camts; $i++; }
$i = 0; foreach($_POST['cus_famt'] as $cus_famts){ $cus_famt[$i] = $cus_famts; $i++; }

// $i = 0; foreach($_POST['vehiclecode'] as $vehiclecodes){ $vehiclecode[$i] = $vehiclecodes; $i++; }
// $i = 0; foreach($_POST['drivercode'] as $drivercodes){ $drivercode[$i] = $drivercodes; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "pursale7";
$trlink = "chicken_display_pursale7.php";

//Save Purchase
$dsize = sizeof($vendorcode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($sup_prc[$i] == ""){ $sup_prc[$i] = 0; }
    if($sup_amt[$i] == ""){ $sup_amt[$i] = 0; }
    if($tds_per[$i] == ""){ $tds_per[$i] = 0; }
    if($tds_amt[$i] == ""){ $tds_amt[$i] = 0; }
    if($roff_samt[$i] == ""){ $roff_samt[$i] = 0; }
    if($sup_famt[$i] == ""){ $sup_famt[$i] = 0; }
    if($cus_prc[$i] == ""){ $cus_prc[$i] = 0; }
    if($cus_amt[$i] == ""){ $cus_amt[$i] = 0; }
    if($tcs_per[$i] == ""){ $tcs_per[$i] = 0; }
    if($tcs_amt[$i] == ""){ $tcs_amt[$i] = 0; }
    if($roff_camt[$i] == ""){ $roff_camt[$i] = 0; }
    if($cus_famt[$i] == ""){ $cus_famt[$i] = 0; }

    //Generate Purchase Invoice
    $pincr = 0; $ptrnum = $pfyear = "";
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pfyear = $row['prefix']; }
	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pincr = $row['purchases']; } $pincr++;
	$sql = "UPDATE `master_generator` SET `purchases` = '$pincr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
	if(!mysqli_query($conn,$sql)){ echo "Error:-".mysqli_error($conn); }
    if($pincr < 10){ $pincr = '000'.$pincr; } else if($pincr >= 10 && $pincr < 100){ $pincr = '00'.$pincr; } else if($pincr >= 100 && $pincr < 1000){ $pincr = '0'.$pincr; } else { }
	$ptrnum = "P".$pfyear."-".$pincr;
        
    //Generate Sale Invoice
    $sincr = 0; $strnum = $sfyear = "";
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sfyear = $row['prefix']; }
	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sincr = $row['sales']; } $sincr++;
	$sql = "UPDATE `master_generator` SET `sales` = '$sincr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
	if(!mysqli_query($conn,$sql)){ echo "Error:-".mysqli_error($conn); }
    if($sincr < 10){ $sincr = '000'.$sincr; } else if($sincr >= 10 && $sincr < 100){ $sincr = '00'.$sincr; } else if($sincr >= 100 && $sincr < 1000){ $sincr = '0'.$sincr; } else { }
	$strnum = "S".$sfyear."-".$sincr;
        
    $sql = "INSERT INTO `pur_purchase` (`date`,`incr`,`d`,`m`,`y`,`fy`,`invoice`,`bookinvoice`,`link_trnum`,`vendorcode`,`supbrh_code`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`tcdsper`,`tcdsamt`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updated`) 
    VALUES ('$date','$pincr','$d','$m','$y','$pfyear','$ptrnum','$bookinvoice[$i]','$strnum','$vendorcode[$i]','$supbrh_code[$i]','$itemcode','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$sup_prc[$i]','$sup_amt[$i]','$tds_per[$i]','$tds_amt[$i]','$roff_samt[$i]','$sup_famt[$i]','$sup_famt[$i]','$warehouse','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    $sql = "INSERT INTO `customer_sales` (`date`,`incr`,`d`,`m`,`y`,`fy`,`invoice`,`bookinvoice`,`link_trnum`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`tcdsper`,`tcdsamt`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updated`) 
    VALUES ('$date','$sincr','$d','$m','$y','$sfyear','$strnum','$bookinvoice[$i]','$ptrnum','$customercode[$i]','$itemcode','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$cus_prc[$i]','$cus_amt[$i]','$tcs_per[$i]','$tcs_amt[$i]','$roff_camt[$i]','$cus_famt[$i]','$cus_famt[$i]','$warehouse','$remarks[$i]','$flag','$active','$tdflag','$pdflag','PST','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    //Send Messages
    $item_dlt = "";
    if($item_dlt == ""){
        if((int)$birds[$i] > 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_name[$itemcode].": ".$item_birds."".$nweight[$i]."Kgs @ ".$cus_prc[$i];
    }
    else{
        if((int)$birds[$i] > 0){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_dlt.", ".$item_name[$itemcode].": ".$item_birds."".$nweight[$i]."Kgs @ ".$cus_prc[$i];
    }
    if((int)$sales_sms_flag == 1 || (int)$sales_wapp_flag == 1){
        $cusdet = ""; $cob_dt = array();
        $cusdet = customer_outbalance($customercode[$i]); $cob_dt = explode("@",$cusdet);
        $customer_name = $cob_dt[0]; $customer_mobile = "91".$cob_dt[1]; $cus_obamt = $cob_dt[2];
        $cus_cbamt = (float)$cus_obamt;

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
Sale Amt: Rs. '.number_format_ind($cus_famt[$i]).'/-
Balance: Rs. '.number_format_ind((float)$cus_obamt).'/-
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
                VALUES ('$sms_code','$customercode[$i]','$customer_mobile','$xml_data','$status[1]','SALES','$addedemp','$addedtime','$addedtime','$client')";
                if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); } else{  }
            }
        }
        if((int)$sales_wapp_flag == 1){
            if(!$conn){ }
            else{
                if($wapp_ptrn == "Template"){
                    $msg1 = array("dear"=>$customer_name, "date"=>date("d.m.Y",strtotime($date)), "item_dt1"=>$item_dlt."/-", "samount"=>number_format_ind($cus_famt[$i]), "ramount"=>number_format_ind(0), "balance"=>number_format_ind($cus_cbamt)."/-", "cdetails"=>$cdetails);
                    $message = json_encode($msg1);
                }
                else{
                    $message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".number_format_ind($cus_famt[$i])."/-,%0D%0AReceived Amt: ".number_format_ind(0)."/-%0D%0ABalance: Rs. ".number_format_ind($cus_cbamt)."/-%0D%0AThank You,%0D%0A".$cdetails;
                    $message = str_replace(" ","+",$message);
                }
                
                $wapp_date = date("Y-m-d");
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                $query = mysqli_query($conn,$sql); $wapp = 0; while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $wincr = $wapp + 1;
                $sql = "UPDATE `master_generator` SET `wapp` = '$wincr' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                if($wincr < 10){ $wincr = '000'.$wincr; } else if($wincr >= 10 && $wincr < 100){ $wincr = '00'.$wincr; } else if($wincr >= 100 && $wincr < 1000){ $wincr = '0'.$wincr; } else { }
                
                $database = $_SESSION['dbase'];
                $wapp_type = "Invoice Message";
                $trnum = $strnum;
                $ccode = $customercode[$i];
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
?>
<script>
    var x = confirm("Would you like to save more entries?");
    if(x == true){
        window.location.href = "chicken_add_pursale7.php";
    }
    else if(x == false){
        window.location.href = "chicken_display_pursale7.php";
    }
</script>
<?php
exit;

