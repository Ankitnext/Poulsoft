<?php
//chicken_save_generalsales8.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
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
$jali_no = $_POST['jali_no'];
$bookinvoice = $_POST['bookinvoice'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];
$warehouse = $_POST['warehouse'];

$itemcode = $jals = $birds = $tweight = $eweight = $nweight = $price = $amount = array();
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'chicken_display_generalsales8.php' AND `field_function` = 'Group Check' AND `flag` = '1' ORDER BY `id` ASC";
$query = mysqli_query($conn,$sql); $gr_flag = mysqli_num_rows($query);
if($gr_flag > 0){ 
    $i = 0; foreach($_POST['remarks2'] as $remarks2s){ $remarks2[$i] = $remarks2s; $i++; }
}

$tcds_chk = $_POST['tcds_chk'];
$tcds_per = $_POST['tcds_per'];
$tcds_type1 = $_POST['tcds_type1'];
$tcds_type2 = $_POST['tcds_type2'];
$tcds_amt = $_POST['tcds_amt'];
$transporter_code = $_POST['transporter_code'];
$freight_amt = $_POST['freight_amt'];
$dressing_charge = $_POST['dressing_charge'];
$roundoff_type1 = $_POST['roundoff_type1'];
$roundoff_type2 = $_POST['roundoff_type2'];
$roundoff_amt = $_POST['roundoff_amt'];
$finaltotal = $_POST['finaltotal'];
$remarks = $_POST['remarks'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "generalsales8";
$trlink = "chicken_display_generalsales8.php";

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $cdetails = $row['cname']." - ".$row['cnum']; }

//Generate Transaction No.
$incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"generalsales8","CINV","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];

//Save Sales
$dsize = sizeof($itemcode); $item_dlt = "";
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt == ""){ $tcds_amt = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($dressing_charge == ""){ $dressing_charge = 0; }
    if($roundoff_amt == ""){ $roundoff_amt = 0; }
    if($finaltotal == ""){ $finaltotal = 0; }

    if($item_dlt == ""){
        if($birds[$i] != ""){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_name[$itemcode[$i]].": ".$item_birds."".$nweight[$i]."Kgs @ ".$price[$i];
    }
    else{
        if($birds[$i] != ""){ $item_birds = $birds[$i]."No. "; } else{ $item_birds = ""; }
        $item_dlt = $item_dlt.", ".$item_name[$itemcode[$i]].": ".$item_birds."".$nweight[$i]."Kgs @ ".$price[$i];
    }

    $sql = "INSERT INTO `customer_sales` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`jali_no`,`bookinvoice`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`transporter_code`,`freight_amount`,`tcdsper`,`tcds_type1`,`tcds_type2`,`tcdsamt`,`dressing_charge`,`roundoff_type1`,`roundoff_type2`,`roundoff`,`finaltotal`,`balance`,`drivercode`,`vehiclecode`,`warehouse`,`remarks`,`remarks2`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updated`) 
    VALUES ('$incr','$d','$m','$y','$pfx','$date','$invoice','$jali_no','$bookinvoice','$vcode','$itemcode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$amount[$i]','$transporter_code','$freight_amt','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$dressing_charge','$roundoff_type1','$roundoff_type2','$roundoff_amt','$finaltotal','$finaltotal','$driver','$vehicle','$warehouse','$remarks','$remarks2[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//Check and save Cash Receipt
$cash_rcode = $_POST['cash_rcode']; $cash_ramt = $_POST['cash_ramt']; if($cash_ramt == ""){ $cash_ramt = 0; }
if($cash_rcode != "" && $cash_rcode !="select" && (float)$cash_ramt > 0){
    //Fetch Cash Modes
    $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_mode = "";
    while($row = mysqli_fetch_assoc($query)){ $cash_mode = $row['code']; }
    
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"msi_rct8","CRI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
    
    $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag)
    VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode','$bookinvoice','$cash_mode','$cash_rcode','$cash_ramt','C','$warehouse','$remarks','$flag','$active','$addedemp','$addedtime','$tdflag','$pdflag')";
    if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//Check and save Bank Receipt
$bank_rcode = $_POST['bank_rcode']; $bank_ramt = $_POST['bank_ramt']; if($bank_ramt == ""){ $bank_ramt = 0; }
if($bank_rcode != "" && $bank_rcode !="select" && (float)$bank_ramt > 0){
    //Fetch Account Modes
    $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_mode = "";
    while($row = mysqli_fetch_assoc($query)){ $bank_mode = $row['code']; }
    
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"msi_rct8","CRI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
    
    $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag)
    VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode','$bookinvoice','$bank_mode','$bank_rcode','$bank_ramt','C','$warehouse','$remarks','$flag','$active','$addedemp','$addedtime','$tdflag','$pdflag')";
    if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//SMS and WAPP Sending
//Check and Insert WhatsApp Details
$file_name = "chicken_display_generalsales8.php"; $sms_type = "WappKey"; $wapp_ptrn = "Normal";
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
}

if(isset($_POST['sub_pt']) == true){
?>
    <script>
        var invoice = '<?php echo $invoice; ?>';
        window.open('chicken_generate_saleinv_print1.php?trnum='+ invoice,'_blank');
        window.location.href = 'chicken_add_generalsales8.php';
    </script>
<?php
exit;
}
else{
    header('Location: chicken_add_generalsales8.php');
    exit;
}