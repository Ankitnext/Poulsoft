<?php
//chicken_save_vehexp1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$dbname = $_SESSION['dbase'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "number_format_ind.php";
include "chicken_generate_trnum_details.php";
include "chicken_send_wapp_master2.php";
include "cus_outbalfunction.php";

//Transaction Details
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$code = $_POST['code'];
$vehicle = $_POST['vehicle'];
$mode = $_POST['mode'];
//$descs = $_POST['descs'];
$warehouse = $_POST['warehouse'];

//Generate Transaction No.
$incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"vehexp1","GSIN","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];

if(isset($_POST['submit']) == "addpage"){
    //Document Upload
    $folder_path = "documents/".$dbname."/vehicle_exp"; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }
    if(!empty($_FILES["doc1"]["name"])) {
        $filename = basename($_FILES["doc1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        $directory = $folder_path."/";
        $filecount = count(glob($directory . "*")); $filecount++;
        $file_name = $dbname."_".$invoice."-".$incr."-".$filecount.".".$filetype;

        $filetmp = $_FILES['doc1']['tmp_name'];
        $doc1_path = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$doc1_path);
    }
    else{ $doc1_path = ""; }

    if(!empty($_FILES["doc2"]["name"])) {
        $filename = basename($_FILES["doc2"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        $directory = $folder_path."/";
        $filecount = count(glob($directory . "*")); $filecount++;
        $file_name = $dbname."_".$invoice."-".$incr."-".$filecount.".".$filetype;

        $filetmp = $_FILES['doc2']['tmp_name'];
        $doc2_path = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$doc2_path);
    }
    else{ $doc2_path = ""; }

    if(!empty($_FILES["doc3"]["name"])) {
        $filename = basename($_FILES["doc3"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        $directory = $folder_path."/";
        $filecount = count(glob($directory . "*")); $filecount++;
        $file_name = $dbname."_".$invoice."-".$incr."-".$filecount.".".$filetype;

        $filetmp = $_FILES['doc3']['tmp_name'];
        $doc3_path = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$doc3_path);
    }
    else{ $doc3_path = ""; }
}
$descs = $remark = $amount = array();
$i = 0; foreach($_POST['descs'] as $descss){ $descs[$i] = $descss; $i++; }
$i = 0; foreach($_POST['remark'] as $remarks){ $remark[$i] = $remarks; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "vehexp1";
$trlink = "chicken_display_vehexp1.php";

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }



//Save Sales
$dsize = sizeof($amount); 
for($i = 0;$i < $dsize;$i++){
   
    if($amount[$i] == ""){ $amount[$i] = 0; }
   
    $sql = "INSERT INTO `acc_vouchers` (`incr`,`prefix`,`trnum`,`date`,`fcoa`,`tcoa`,`mode`,`doc1_path`,`doc2_path`,`doc3_path`,`amount`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$invoice','$date','$code','$descs[$i]','$mode','$doc1_path','$doc2_path','$doc3_path','$amount[$i]','$warehouse','$remark[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {  }
}
// die();
//SMS and WAPP Sending
//Check and Insert WhatsApp Details
$file_name = "chicken_display_vehexp1.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
$sql = "SELECT * FROM `whatsapp_keygenerate_master` WHERE `file_type` = 'General Sale' AND `file_name` = '$file_name' AND `active` = '1' AND `dflag` = '0'";
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
//Send Messages
if((int)$sales_sms_flag == 1 || (int)$sales_wapp_flag == 1){
    $cusdet = customer_outbalance($vcode); $cob_dt = explode("@",$cusdet);
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
Sale Amt: Rs. '.number_format_ind($finaltotal).'/-
Balance: Rs. '.number_format_ind((float)$cus_obamt).'/-
Received: Rs. '.number_format_ind(0).'/-
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
            VALUES ('$sms_code','$vcode','$customer_mobile','$xml_data','$status[1]','SALES','$addedemp','$addedtime','$addedtime','$client')";
            if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); } else{  }
        }
    }
    if((int)$sales_wapp_flag == 1){
        if(!$conn){ }
        else{
            if($wapp_ptrn == "Template"){
                $msg1 = array("dear"=>$customer_name, "date"=>date("d.m.Y",strtotime($date)), "item_dt1"=>$item_dlt."/-", "samount"=>number_format_ind($finaltotal), "ramount"=>number_format_ind(0), "balance"=>number_format_ind($cus_cbamt)."/-", "cdetails"=>$cdetails);
                $message = json_encode($msg1);
            }
            else{
                $message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".number_format_ind($finaltotal)."/-,%0D%0AReceived Amt: ".number_format_ind(0)."/-%0D%0ABalance: Rs. ".number_format_ind($cus_cbamt)."/-%0D%0AThank You,%0D%0A".$cdetails;
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
            $ccode = $vcode;
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

if((int)$sales_notify_flag == 1){
    include "NotificationSending_ct_web.php";
    $message = "Dear: ".$out_nme."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
    $message = str_replace(" ","+",$message);

    $db = $_SESSION['dbase'];
    $sql = "SELECT * FROM `firebase_device_details` where db = '$db' and mobile = '$customer_mobile1'";
    $q3=mysqli_query($conns,$sql);$fb_count = mysqli_num_rows($q3);
    if($fb_count > 0){
        $row = mysqli_fetch_assoc($q3);
        send_notification("Sales","Sales Confirmation From ".$company_name,$row['device_token']);
        save_notification($customer_mobile1,$invoice,"cus_save_sales1.php","Sales","Sales Confirmation From ".$company_name." (".$invoice.")",$message);
    }
} else{ }

if(isset($_POST['sub_pt']) == true){
    $url = "chicken_generate_saleinv_print1.php?trnum=".$invoice;
    echo "<script>window.open('$url', '_blank');</script>";
    echo "<script>window.location.href = 'chicken_display_vehexp1.php';</script>";
}
else{
    echo "<script>window.location.href = 'chicken_display_vehexp1.php';</script>";
}

