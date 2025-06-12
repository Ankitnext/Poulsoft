<?php
//chicken_modify_pursale5.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
include "cus_outbalfunction.php";
include "pur_outbalfunction.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['pursale5'];
$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
if($_POST['submittrans'] == "updatepage"){
	$date = date("Y-m-d",strtotime($_POST['pdate']));
				
	$d = date("d",strtotime($date)); $m = date("m",strtotime($date)); $y = date("Y",strtotime($date));
	$id = explode("@",$_POST['idvalue']);
	$warehouse = $_POST['wcodes'];
	$tr_incr = $_POST['incr'];
	$sdetails = explode("@", $_POST['snames']);
    $sup_code = $sdetails[0];
    $sup_names = $sdetails[1];

    $itemdetails = explode("@", $_POST['scat']);
    $itemcode = $itemdetails[0];

    $supbrh_code = $_POST['supbrh_code'];
    $bno = $_POST['bno'];
    $jal = $_POST['jval'];
    $cjal = $_POST['cjval'];
    $bird = $_POST['bval'];
    $cbird = $_POST['cbval'];
    $totalweight = $_POST['wval'];
    $ctotalweight = $_POST['cwval'];
    $emptyweight = $_POST['ewval'];
    $cemptyweight = $_POST['cewval'];
    $netweight = $_POST['nwval'];
    $cus_qty = $_POST['cus_qty'];

    $sup_iprice = $_POST['sup_iprice'];
    $sup_tamt = $_POST['sup_tamt'] == "" ? 0 : $_POST['sup_tamt'];
    $tds_tamt = $_POST['tds_tamt'] == "" ? 0 : $_POST['tds_tamt'];
    $sup_ftotal = round($_POST['sup_famt'] == "" ? 0 : $_POST['sup_famt']);

    $cdetails = explode("@", $_POST['cnames']);
    $cus_code = $cdetails[0];
    $cus_names = $cdetails[1];

    $cus_iprice = $_POST['cus_iprice'];
    $cus_tamt = $_POST['cus_tamt'] == "" ? 0 : $_POST['cus_tamt'];
    $tcs_tamt = $_POST['tcs_tamt'] == "" ? 0 : $_POST['tcs_tamt'];
    $cus_ftotal = round($_POST['cus_famt'] == "" ? 0 : $_POST['cus_famt']);

    $vehicle = $_POST['vehicle'];
    $driver = $_POST['driver'];
    $remarks = $_POST['narr'];

// Set current date if date is empty
// if (empty($date)) {
//     $date = date("Y-m-d");
// }

// // Ensure $cus_code is a single value
// if (is_array($cus_code)) {
//     $cus_code = implode(", ", $cus_code);
// }

// Retrieve the prefix for financial year
$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'";
$query = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($query)){ 
    $pfx = $row['prefix']; 
}

// Increment sale and purchase counters
$incr_sale = $sales + 1;
$incr_purchase = $purchases + 1;

// Format the incremented sale and purchase values
$sale_inv = "S" . $pfx . "-" . str_pad($incr_sale, 4, '0', STR_PAD_LEFT);
$pur_inv = "P" . $pfx . "-" . str_pad($incr_purchase, 4, '0', STR_PAD_LEFT);

// Calculate amounts and set default values if necessary
$cus_amtinwds = convert_number_to_words($cus_ftotal) . " Rupees Only";
$cus_roundoff = $cus_ftotal - $cus_tamt;

$sup_amtinwds = convert_number_to_words($sup_ftotal) . " Rupees Only";
$sup_roundoff = $sup_ftotal - $sup_tamt;

$jals = empty($jals) ? "0.00" : $jals;
$totalweight = empty($totalweight) ? "0.00" : $totalweight;
$emptyweight = empty($emptyweight) ? "0.00" : $emptyweight;
$birds = empty($birds) ? "0.00" : $birds;
$netweight = empty($netweight) ? "0.00" : $netweight;

// Update customer sales
$sql_customer_sales = "UPDATE `customer_sales` SET `date` = '$date', `bookinvoice` = '$bno', `customercode` = '$cus_code', `jals` = '$cjals', `totalweight` = '$ctotalweight', `emptyweight` = '$cemptyweight', `itemcode` = '$itemcode', `birds` = '$cbirds', `netweight` = '$cus_qty', `itemprice` = '$cus_iprice', `totalamt` = '$cus_tamt', `roundoff` = '$cus_roundoff', `finaltotal` = '$cus_ftotal', `balance` = '$cus_ftotal', `amtinwords` = '$cus_amtinwds', `trtype` = 'PST', `warehouse` = '$warehouse', `flag` = '0', `authorization` = '0', `tdflag` = '0', `pdflag` = '0', `drivercode` = '$driver', `vehiclecode` = '$vehicle', `discounttype` = '0.00', `discountvalue` = '0.00', `taxtype` = '0.00', `taxvalue` = '0.00', `discountamt` = '0.00', `taxamount` = '0.00', `taxcode` = '0.00', `discountcode` = '0.00', `remarks` = '$remarks', `sms_sent` = '$sms_code', `updatedemp` = '$addedemp', `updated` = '$addedtime', `client` = '$client' WHERE `invoice` = '$id[0]'";

if(!mysqli_query($conn, $sql_customer_sales)) {
    die("Cus-Error: " . mysqli_error($conn));
}

// Update purchase
$sql_pur_purchase = "UPDATE `pur_purchase` SET `date` = '$date', `bookinvoice` = '$bno', `vendorcode` = '$sup_code', `supbrh_code` = '$supbrh_code', `jals` = '$jals', `totalweight` = '$totalweight', `emptyweight` = '$emptyweight', `itemcode` = '$itemcode', `birds` = '$birds', `netweight` = '$netweight', `itemprice` = '$sup_iprice', `totalamt` = '$sup_tamt', `roundoff` = '$sup_roundoff', `finaltotal` = '$sup_ftotal', `balance` = '$sup_ftotal', `amtinwords` = '$sup_amtinwds', `warehouse` = '$warehouse', `flag` = '0', `authorization` = '0', `tdflag` = '0', `pdflag` = '0', `drivercode` = '$driver', `vehiclecode` = '$vehicle', `discounttype` = '0.00', `discountvalue` = '0.00', `taxtype` = '0.00', `taxvalue` = '0.00', `discountamt` = '0.00', `taxamount` = '0.00', `taxcode` = '0.00', `discountcode` = '0.00', `remarks` = '$remarks', `updatedemp` = '$addedemp', `updated` = '$addedtime', `client` = '$client' WHERE `invoice` = '$id[1]'";

if(!mysqli_query($conn, $sql_pur_purchase)) {
    die("Sup-Error: " . mysqli_error($conn));
}

	header('location:chicken_display_pursale5.php');
}
else{
	header('location:chicken_display_pursale5.php');
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