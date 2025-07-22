<?php
//chicken_save_multiplesales.php
session_start();
include "newConfig.php";
include "number_format_ind.php";
include "cus_outbalfunction.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');

global $sms_type; $sms_type = "WappKey"; include "chicken_wapp_connectionmaster.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }

$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Authorization' AND `field_function` = 'chicken_add_multiplesales.php' AND `user_access` = 'all'";
$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $aut_flag = 0;
if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $aut_flag = $row1['flag']; } }
else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','chicken_add_multiplesales.php','all','0');"; mysqli_query($conn,$sql1); }
if((int)$aut_flag == 1){ $active = 0; } else{ $active = 1; }

if((int)$wapp_error_flag == 0){
	$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Send WhatsApp Timer' AND `field_function` = 'chicken_add_multiplesales.php' AND `user_access` = 'all'";
	$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $wapp_timer_flag = 0;
	if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_timer_flag = $row1['flag']; } }
	else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','chicken_add_multiplesales.php','all','0');"; mysqli_query($conn,$sql1); }
}
$client = $_SESSION['client'];

$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `farm_weight` double(20,2) NULL DEFAULT NULL COMMENT 'Farm Weight' AFTER `netweight`"; mysqli_query($conn,$sql); }
if(in_array("farm_wt_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `farm_wt_flag` INT(100) NULL DEFAULT '0' COMMENT 'Farm Weight Flag'"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Add Multiple Sales' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_flag = $row['vehicle_flag']; $vehicle_row_flag = $row['vehicle_row_flag']; }
if($vehicle_flag == "" || $vehicle_flag == 0){ $vehicle_flag = 0; }
if($vehicle_row_flag == "" || $vehicle_row_flag == 0){ $vehicle_row_flag = 0; }



if($_POST['submittrans'] == "addpage"){
	$vendorcode = $cus_code = $cus_names = $itemcode = $jals = $birds = $totalweight = $emptyweight = $netweight = 
	$farm_weight = $farm_wt_flag = $itemprice = $totalamt = $finaltotal = $vehiclerno = $remarks = array();

	$date = date("Y-m-d",strtotime($_POST['pdate']));
	$warehouse = $_POST['wcodes'];
	$bookinvoice = $_POST['binv'];
	if($vehicle_flag == 1){ $vehicle_code = $_POST['vehicleno']; } else{ $vehicle_code = ""; }
	$i = 0; foreach($_POST['cnames'] as $cnames){ $i = $i + 1; $cdetails = explode("@",$cnames); $vendorcode[$i] = $cus_code[$i] = $cdetails[0]; $cus_names[$i] = $cdetails[1]; }
	$i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
	$i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; }
	$i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }
	$i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; }
	$i = 0; foreach($_POST['ewval'] as $eweights){ $i = $i + 1; $emptyweight[$i] = $eweights; }
	$i = 0; foreach($_POST['nwval'] as $nweights){ $i = $i + 1; $netweight[$i] = $nweights; }
	$i = 0; foreach($_POST['farm_weight'] as $farm_weights){ $i = $i + 1; $farm_weight[$i] = $farm_weights; if($farm_weights != ""){ $farm_wt_flag[$k] = 1; } else{ $farm_wt_flag[$k] = 0; } }
	$i = 0; foreach($_POST['iprice'] as $iprices){ $i = $i + 1; $itemprice[$i] = $iprices; }
	$i = 0; foreach($_POST['tamt'] as $tamts){ $i = $i + 1; if($tamts == ""){ $tamts = 0; } $totalamt[$i] = $tamts; $finaltotal[$i] = round($tamts); }
	if($vehicle_row_flag == 1){ $i = 0; foreach($_POST['vehiclerno'] as $vnor){ $i = $i + 1; $vehiclerno[$i] = $vnor; } }
	$i = 0; foreach($_POST['narr'] as $narr){ $i = $i + 1; $remarks[$i] = $narr; }
    //$a = sizeof($cus_code); $k = 0; for($j = 0; $j < $a;$j++){ $k = $k + 1; if($_POST['farm_wt_flag'][$j] == "on"){ $farm_wt_flag[$k] = 1; } else{ $farm_wt_flag[$k] = 0; } }
    
    
	$d = date("d",strtotime($date)); $m = date("m",strtotime($date)); $y = date("Y",strtotime($date));
	$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ 
		while($row = mysqli_fetch_assoc($query)){
			$sales_sms_flag = $row['sales_sms'];
			$sales_wapp_flag = $row['sales_wapp'];
		}
	}
	else {
		$sales_sms_flag = 0;
		$sales_wapp_flag = 0;
	}
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; $sms = $row['sms']; $wapp = $row['wapp']; } $incr = $sales + $i; $incr_sms = $sms + $i; $incr_wapp = $wapp + $i;
	if($sales_sms_flag == 1){ $sales_incr = ",`sms` = '$incr_sms'"; } else{ $sales_incr = ""; }
	if($sales_wapp_flag == 1){ $whapp_incr = ",`wapp` = '$incr_wapp'"; } else{ $whapp_incr = ""; }
	$sql = "UPDATE `master_generator` SET `sales` = '$incr'".$sales_incr." WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
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
	$incr = $sales; $incr_sms = $sms; $incr_wapp = $wapp; $item_birds = "";
	for($j = 1;$j <= $i;$j++){
		if($warehouse == "select" || $itemcode[$j] == "select") {
		}
		else {
			$cusdet = $customer_name = $customer_mobile = ""; $ftotal = 0; $obdetails = array();
			$cusdet = customer_outbalance($cus_code[$j]); $obdetails = explode("@",$cusdet);
			$customer_name = $obdetails[0]; $customer_mobile = "91".$obdetails[1];
			$ftotal = $obdetails[2]; $bals = 0;
			$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }
			$out_amt = $ftotal; $totalamount = number_format_ind($finaltotal[$j]); $bals = $out_amt + $finaltotal[$j]; $bal = number_format_ind($bals);
			$item_dlt = "";
			if($item_dlt == ""){
				if(!empty($farm_weight[$j]) && $farm_weight[$j] > 0){
					if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
					$item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$farm_weight[$j]."Kgs @ ".$itemprice[$j];
				}
				else{
					if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
					$item_dlt = $item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
				}
			}
			else{
				if(!empty($farm_weight[$j]) && $farm_weight[$j] > 0){
					if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
					$item_dlt = $item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$farm_weight[$j]."Kgs @ ".$itemprice[$j];
				}
				else{
					if($birds[$j] != ""){ $item_birds = $birds[$j]."No. "; } else{ $item_birds = ""; }
					$item_dlt = $item_dlt.", ".$item_name[$itemcode[$j]].": ".$item_birds."".$netweight[$j]."Kgs @ ".$itemprice[$j];
				}
			}
			if($sales_sms_flag == 1){
				$incr_sms = $incr_sms + 1;
				if($incr_sms < 10){ $incr_sms = '000'.$incr_sms; } else if($incr_sms >= 10 && $incr_sms < 100){ $incr_sms = '00'.$incr_sms; } else if($incr_sms >= 100 && $incr_sms < 1000){ $incr_sms = '0'.$incr_sms; } else { }
				$sms_code = "SMS-".$incr_sms;
			}
			else{
				$sms_code = NULL;
			}
			if($sales_wapp_flag == 1){
				$incr_wapp = $incr_wapp + 1;
				if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
				$wapp_code = "WAPP-".$incr_wapp;
			}
			else{
				$wapp_code = NULL;
			}
			$incr = $incr + 1;
			if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
			$invoice = "S".$pfx."-".$incr;
			$amtinwds = convert_number_to_words($finaltotal[$j]);
			$amtinwds = $amtinwds." Rupees Only";
			$roundoff = "";
			$roundoff = $finaltotal[$j] - $totalamt[$j];
			if($jals[$j] == "" || $jals[$j] == NULL){ $jals[$j] = "0.00"; }
			if($birds[$j] == "" || $birds[$j] == NULL){ $birds[$j] = "0.00"; }
			if($totalweight[$j] == "" || $totalweight[$j] == NULL){ $totalweight[$j] = "0.00"; }
			if($emptyweight[$j] == "" || $emptyweight[$j] == NULL){ $emptyweight[$j] = "0.00"; }
			if($netweight[$j] == "" || $netweight[$j] == NULL){ $netweight[$j] = "0.00"; }
			if($farm_weight[$j] == "" || $farm_weight[$j] == NULL){ $farm_weight[$j] = "0.00"; }
			if($itemprice[$j] == "" || $itemprice[$j] == NULL){ $itemprice[$j] = "0.00"; }
			if($vehicle_row_flag == 1){ $vehiclecode = $vehiclerno[$j]; }
			else if($vehicle_flag == 1){ $vehiclecode = $vehicle_code; }
			else{ $vehiclecode = ""; }
			if($farm_wt_flag[$j] == ""){ $farm_wt_flag[$j] = 0; }
			$sql = "INSERT INTO `customer_sales` (date,incr,d,m,y,fy,invoice,bookinvoice,customercode,jals,totalweight,emptyweight,itemcode,birds,netweight,farm_weight,itemprice,totalamt,tcdsper,tcdsamt,roundoff,finaltotal,balance,amtinwords,trtype,warehouse,farm_wt_flag,flag,authorization,active,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,sms_sent,addedemp,addedtime,client) 
			VALUES ('$date','$incr','$d','$m','$y','$pfx','$invoice','$bookinvoice','$vendorcode[$j]','$jals[$j]','$totalweight[$j]','$emptyweight[$j]','$itemcode[$j]','$birds[$j]','$netweight[$j]','$farm_weight[$j]','$itemprice[$j]','$totalamt[$j]','0.00','0.00','$roundoff','$finaltotal[$j]','$finaltotal[$j]','$amtinwds','M','$warehouse','$farm_wt_flag[$j]','0','0','$active','0','0','','$vehiclecode','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','$remarks[$j]','$sms_code','$addedemp','$addedtime','$client')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
			else {
				if($sales_sms_flag == 1){
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
Sale Amt: Rs. '.$totalamount.'/-
Balance: Rs. '.$bal.'/-
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
					$status[1];
					$wsfile_path = $_SERVER['REQUEST_URI'];
					$sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,smsto,file_name,addedemp,addedtime,updatedtime,client)
					VALUES ('$sms_code','$vendorcode[$j]','$customer_mobile','$xml_data','$status[1]','SALES','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
					if(!mysqli_query($conn,$sql)) { die("Error:- SMS sending error: ".mysqli_error($conn)); }
					else{  } 
					}
				}
				if($sales_wapp_flag == 1){
                    if(!$conn){ }
                    else{
                        $message = "Dear: ".$customer_name."%0D%0ADate: ".date("d.m.Y",strtotime($date)).",%0D%0A".$item_dlt.",%0D%0ASale Amt: ".$totalamount."/-%0D%0ABalance: Rs. ".$bal."/-%0D%0AThank You,%0D%0A".$cdetails;
                        $message = str_replace(" ","+",$message);
						$number = $customer_mobile; $type = "text";
						$ccode = $vendorcode[$j];
						$wapp_date = date("Y-m-d");

						if((int)$url_id == 3){
							$msg_info = $curlopt_url.''.$instance_id.'/messages/chat?token='.$access_token.'&to='.$number.'&body='.$message;
						}
						else{
							$msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token;
						}
						
						if($wapp_error_flag == 0){
							$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
							while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
							
							$sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
							if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
							
							if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
							$wapp_code = "WAPP-".$incr_wapp;
							$wsfile_path = $_SERVER['REQUEST_URI'];
			
							$database = $_SESSION['dbase'];
							$trtype = "Invoice Message";
							$trnum = $invoice;
							$vendor = $ccode;
							$mobile = $number;
							$msg_trnum = $wapp_code;
							$msg_type = "WAPP";
							$msg_project = "CTS";
							$status = "CREATED";
							$trlink = $_SERVER['REQUEST_URI'];
							$sql = "INSERT INTO `master_pendingmessages` (`database`,`url_id`,`trtype`,`trnum`,`vendor`,`mobile`,`msg_trnum`,`msg_type`,`msg_info`,`msg_project`,`status`,`trlink`,`addedemp`,`addedtime`,`updatedtime`)
							VALUES ('$database','$url_id','$trtype','$trnum','$vendor','$mobile','$msg_trnum','$msg_type','$msg_info','$msg_project','$status','$trlink','$addedemp','$addedtime','$addedtime')";
							if(!mysqli_query($conns,$sql)) { } else{ }
						}

                    }
				}
			}
		}
	}
	?>
	<script>
		var x = confirm("Would you like to save more entries?");
		if(x == true){
			window.location.href = "chicken_add_multiplesales.php";
		}
		else if(x == false){
			window.location.href = "chicken_display_multiplesales.php";
		}
	</script>
	<?php
    
}
function convert_number_to_words($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    if (!is_numeric($number)) {
        return false;
    }
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }
    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    $string = $fraction = null;
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    return $string;
}