<?php
//chicken_modify_pursale1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
include "cus_outbalfunction.php";
include "pur_outbalfunction.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['pursale1'];
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
if($_POST['submittrans'] == "updatepage"){
	$date = date("Y-m-d",strtotime($_POST['pdate']));
	$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
	$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
	$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
	$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcsper = $row['tcds']; }
				
	$d = date("d",strtotime($date)); $m = date("m",strtotime($date)); $y = date("Y",strtotime($date));
	$id = explode("@",$_POST['idvalue']);
	$warehouse = $_POST['wcodes'];
	$tr_incr = $_POST['incr'];
	$i = 0; foreach($_POST['snames'] as $snames){ $i++; $sdetails = explode("@",$snames); $sup_code[$i] = $sdetails[0]; $sup_names[$i] = $sdetails[1]; }
	$i = 0; foreach($_POST['scat'] as $icats){ $i++; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }
	$i = 0; foreach($_POST['supbrh_code'] as $supbrh_codes){ $i++; $supbrh_code[$i] = $supbrh_codes; }
	$i = 0; foreach($_POST['bno'] as $bno){ $i++; $bnos[$i] = $bno; }
	$i = 0; foreach($_POST['jval'] as $jal){ $i++; $jals[$i] = $jal; }
	$i = 0; foreach($_POST['bval'] as $bird){ $i++; $birds[$i] = $bird; }
	$i = 0; foreach($_POST['wval'] as $weights){ $i++; $totalweight[$i] = $weights; }
	$i = 0; foreach($_POST['ewval'] as $eweights){ $i++; $emptyweight[$i] = $eweights; }
	$i = 0; foreach($_POST['nwval'] as $nweights){ $i++; $netweight[$i] = $nweights; }
	$i = 0; foreach($_POST['sup_iprice'] as $sup_iprices){ $i++; $sup_iprice[$i] = $sup_iprices; }
	$i = 0; foreach($_POST['sup_tamt'] as $sup_tamts){ $i++; if($sup_tamts ==""){ $sup_tamts = 0; } $sup_tamt[$i] = $sup_tamts; }
	$i = 0; foreach($_POST['tds_tamt'] as $tds_tamts){ $i++; if($tds_tamts ==""){ $tds_tamts = 0; } $tds_tamt[$i] = $tds_tamts; }
	$i = 0; foreach($_POST['sup_famt'] as $sup_famts){ $i++; if($sup_famts ==""){ $sup_famts = 0; } $sup_ftotal[$i] = round($sup_famts); }
	
	$i = 0; foreach($_POST['cnames'] as $cnames){ $i++; $cdetails = explode("@",$cnames); $cus_code[$i] = $cdetails[0]; $cus_names[$i] = $cdetails[1]; }
	$i = 0; foreach($_POST['cus_iprice'] as $cus_iprices){ $i++; $cus_iprice[$i] = $cus_iprices; }
	$i = 0; foreach($_POST['cus_tamt'] as $cus_tamts){ $i++; if($cus_tamts ==""){ $cus_tamts = 0; } $cus_tamt[$i] = $cus_tamts; }
	$i = 0; foreach($_POST['tcs_tamt'] as $tcs_tamts){ $i++; if($tcs_tamts ==""){ $tcs_tamts = 0; } $tcs_tamt[$i] = $tcs_tamts; }
	$i = 0; foreach($_POST['cus_famt'] as $cus_famts){ $i++; if($cus_famts ==""){ $cus_famts = 0; } $cus_ftotal[$i] = round($cus_famts); }
	$i = 0; foreach($_POST['vehicle'] as $vehicles){ $i++; $vehicle[$i] = $vehicles; }
	$i = 0; foreach($_POST['driver'] as $drivers){ $i++; $driver[$i] = $drivers; }
	$i = 0; foreach($_POST['narr'] as $narr){ $i++; $remarks[$i] = $narr; }
	
	$tr_size = sizeof($cus_code);
	$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
	
	$incr_sale = $sales; $incr_purchase = $purchases;
	for($j = 1;$j <= $tr_size;$j++){
		$incr_sale = $incr_sale + 1;
		if($incr_sale < 10){ $incr_sale = '000'.$incr_sale; } else if($incr_sale >= 10 && $incr_sale < 100){ $incr_sale = '00'.$incr_sale; } else if($incr_sale >= 100 && $incr_sale < 1000){ $incr_sale = '0'.$incr_sale; } else { }
		$sale_inv = "S".$pfx."-".$incr_sale;
		
		$incr_purchase = $incr_purchase + 1;
		if($incr_purchase < 10){ $incr_purchase = '000'.$incr_purchase; } else if($incr_purchase >= 10 && $incr_purchase < 100){ $incr_purchase = '00'.$incr_purchase; } else if($incr_purchase >= 100 && $incr_purchase < 1000){ $incr_purchase = '0'.$incr_purchase; } else { }
		$pur_inv = "P".$pfx."-".$incr_purchase;
		
		$cus_amtinwds = convert_number_to_words($cus_ftotal[$j]); $cus_amtinwds = $cus_amtinwds." Rupees Only";
		$cus_roundoff = 0; $cus_roundoff = ((float)$cus_ftotal[$j] - ((float)$cus_tamt[$j] + (float)$tcs_tamt[$j]));
		
		$sup_amtinwds = convert_number_to_words($sup_ftotal[$j]); $sup_amtinwds = $sup_amtinwds." Rupees Only";
		$sup_roundoff = 0; $sup_roundoff = ((float)$sup_ftotal[$j] - ((float)$sup_tamt[$j] + (float)$tds_tamt[$j]));
				
		if($jals[$j] == ""){ $jals[$j] = 0; }
		if($totalweight[$j] == ""){ $totalweight[$j] = 0; }
		if($emptyweight[$j] == ""){ $emptyweight[$j] = 0; }
		if($birds[$j] == ""){ $birds[$j] = 0; }
		if($netweight[$j] == ""){ $netweight[$j] = 0; }
		
		$sql = "UPDATE `customer_sales` SET `date` = '$date',`bookinvoice` = '$bnos[$j]',`customercode` = '$cus_code[$j]',`jals` = '$jals[$j]',`totalweight` = '$totalweight[$j]',`emptyweight` = '$emptyweight[$j]',`itemcode` = '$itemcode[$j]',`birds` = '$birds[$j]',`netweight` = '$netweight[$j]',`itemprice` = '$cus_iprice[$j]',`totalamt` = '$cus_tamt[$j]',`tcdsper` = '$tcsper',`tcdsamt` = '$tcs_tamt[$j]',`roundoff` = '$cus_roundoff',`finaltotal` = '$cus_ftotal[$j]',`balance` = '$cus_ftotal[$j]',`amtinwords` = '$cus_amtinwds',`trtype` = 'PST',`warehouse` = '$warehouse',`flag` = '0',`authorization` = '0',`tdflag` = '0',`pdflag` = '0',`drivercode` = '$driver[$j]',`vehiclecode` = '$vehicle[$j]',`discounttype` = '0.00',`discountvalue` = '0.00',`taxtype` = '0.00',`taxvalue` = '0.00',`discountamt` = '0.00',`taxamount` = '0.00',`taxcode` = '0.00',`discountcode` = '0.00',`remarks` = '$remarks[$j]',`sms_sent` = '$sms_code',`updatedemp` = '$addedemp',`updated` = '$addedtime',`client` = '$client' WHERE `invoice` = '$id[0]'";
		if(!mysqli_query($conn,$sql)){ die("Cus-Error:-".mysqli_error($conn)); } else { }
		
		$sql = "UPDATE `pur_purchase` SET `date` = '$date',`bookinvoice` = '$bnos[$j]',`vendorcode` = '$sup_code[$j]',`supbrh_code` = '$supbrh_code[$j]',`jals` = '$jals[$j]',`totalweight` = '$totalweight[$j]',`emptyweight` = '$emptyweight[$j]',`itemcode` = '$itemcode[$j]',`birds` = '$birds[$j]',`netweight` = '$netweight[$j]',`itemprice` = '$sup_iprice[$j]',`totalamt` = '$sup_tamt[$j]',`tcdsper` = '$tdsper',`tcdsamt` = '$tds_tamt[$j]',`roundoff` = '$sup_roundoff',`finaltotal` = '$sup_ftotal[$j]',`balance` = '$sup_ftotal[$j]',`amtinwords` = '$sup_amtinwds',`warehouse` = '$warehouse',`flag` = '0',`authorization` = '0',`tdflag` = '0',`pdflag` = '0',`drivercode` = '$driver[$j]',`vehiclecode` = '$vehicle[$j]',`discounttype` = '0.00',`discountvalue` = '0.00',`taxtype` = '0.00',`taxvalue` = '0.00',`discountamt` = '0.00',`taxamount` = '0.00',`taxcode` = '0.00',`discountcode` = '0.00',`remarks` = '$remarks[$j]',`updatedemp` = '$addedemp',`updated` = '$addedtime',`client` = '$client' WHERE `invoice` = '$id[1]'";
		if(!mysqli_query($conn,$sql)){ die("Sup-Error:-".mysqli_error($conn)); } else { }
		
	}
	header('location:chicken_display_pursale1.php');
}
else{
	header('location:chicken_display_pursale1.php');
}
if($number > 0 && $baseUnit > 0){ $numBaseUnits = (int) ($number / $baseUnit); } else{ $numBaseUnits = 0; }
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
?>