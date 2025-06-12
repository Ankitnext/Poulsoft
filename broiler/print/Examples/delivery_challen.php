<?php
//broiler_saleinvoice.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $fullcname = $row['fullcname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$tran = $_GET['trnum'];

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
            if($number > 0 && $baseUnit > 0){ $numBaseUnits = (int) ($number / $baseUnit); } else{ $numBaseUnits = 0; }
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction) && $fraction != 00 && $fraction != "00") {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}


// Helper function: converts a non-negative number to words (without currency labels).
function convert_number_to_words_helper($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
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
        40                  => 'forty', // corrected spelling from "fourty"
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
    
    if ($number < 0) {
        return $negative . convert_number_to_words_helper(abs($number));
    }
    
    $string = '';
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
            $hundreds  = (int)($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words_helper($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words_helper($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words_helper($remainder);
            }
            break;
    }
    return $string;
}

// Wrapper function: formats the number as currency words with "Rupees" and "Paisa"
function convert_number_to_currency_words($number) {
    // Convert the number to a string so we can check for a decimal part.
    $numberStr = (string)$number;
    $parts = explode('.', $numberStr);

    // Process the integer (Rupees) part.
    $rupeesPart = (int)$parts[0];
    $words = convert_number_to_words_helper($rupeesPart) . ' Rupees';

    // Process the fractional (Paisa) part if it exists and is non-zero.
    if (count($parts) > 1 && (int)$parts[1] > 0) {
        // Optionally, if you need the fraction to represent two digits (e.g. 7 -> 70), you might pad it:
        // $parts[1] = str_pad($parts[1], 2, '0', STR_PAD_RIGHT);
        $paisaPart = (int)$parts[1];
        $words .= ' and ' . convert_number_to_words_helper($paisaPart) . ' Paisa';
    }
    
    // Optionally, you can use ucwords() to capitalize the first letter of each word.
    return ucwords($words);
}

$sql_farm = "SELECT * FROM `broiler_farm` ORDER BY `id` DESC"; $query_farm = mysqli_query($conn,$sql_farm);
while($row_farm = mysqli_fetch_assoc($query_farm)){ $sector_description[$row_farm['code']] = $row_farm['description']; $farm_farmer[$row_farm['code']] = $row_farm['farmer_code']; }

$sql_farm = "SELECT * FROM `broiler_farm` ORDER BY `id` DESC"; $query_farm = mysqli_query($conn,$sql_farm);
while($row_farm = mysqli_fetch_assoc($query_farm)){ $sector_description[$row_farm['code']] = $row_farm['description']; $farm_farmer[$row_farm['code']] = $row_farm['farmer_code']; }

$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $description[$row['code']] = $row['description']; $sunits[$row['code']] = $row['sunits']; }

$sql_sector = "SELECT * FROM `inv_sectors` ORDER BY `id` DESC"; $query_sector = mysqli_query($conn,$sql_sector);
while($row_sector = mysqli_fetch_assoc($query_sector)){ $sector_description[$row_sector['code']] = $row_sector['description']; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' OR `type` = 'all'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
	$bank_name = $row['bank_name'];
	$bank_branch = $row['bank_branch'];
	$bank_accno = $row['bank_accno'];
	$bank_ifsc = $row['bank_ifsc'];
	$bank_accname = $row['bank_accname'];
	$upi_details = $row['upi_details'];
	$upi_mobile = $row['upi_mobile'];
	$comname = $row['cname'];
}


$sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$tran' and dflag = 0 ";
		$query = mysqli_query($conn,$sql); $c = 0;
		while($row = mysqli_fetch_assoc($query)){
			$c = $c + 1;
			$trnum = $row['trnum'];
			$code[$c] =  $description[$row['code']];
			$icode[$c] =  $row['code'];
			$sunit[$c] =  $sunits[$row['code']];
			$dcno = $row['dcno'];
			$fromwarehouse = $sector_description[$row['fromwarehouse']];
			$towarehouse = $sector_description[$row['towarehouse']];
			$farmers = $farm_farmer[$row['towarehouse']];
			$quantity[$c] = $row['quantity'];
			$price = $row['price'];
			$amount = $row['amount'];
			$vehicle_code = $row['vehicle_code'];
			if(!empty($vehicle_name[$vehicle_code])){ $vname = $vehicle_name[$vehicle_code]; } else{ $vname = $vehicle_code; }
			if($vname == 'select'){
				$vname = '';
			}
			$driver_code = $row['driver_code'];
			if(!empty($driver_name[$driver_code])){ $dname = $driver_name[$driver_code]; } else{ $dname = $driver_code; }
			if($dname == 'select'){
				$dname = '';
			}
			$date = $row['date'];
			$addedemp = $employee_description[$row['addedemp']];
		}




$html  .= '';
$html .= '<table style="border:1px solid black;">';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:560px;text-align:center;border-top:1px solid black;"><b>Delivery Challan</b></th>';
$html .= '</tr>';
$html .= '<tr style="line-height: 1;">';
$html .= '<th style="width:180px;border-top:1px solid black;border-right:1px solid black;margin-top:10px;text-align:center;"><br/><br/><img src="../../'.$img_path.'" height="50px" /></th>';
$html .= '<th style="width:380px;border-top:1px solid black;border-right:1px solid black;text-align:center;height:100px;font-size:10vw"><br/>'.$cdetail.'</th>';
$html .= '</tr>';



$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;">From</th>';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;">To</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;">'.$fromwarehouse.'</th>';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;">'.$towarehouse.'</th>';
$html .= '</tr>';


$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Delivery Note </th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">ML|INT|DC|'.$dcno.''.$date.' </th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Vehicle No </th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-right:1px solid black;">Time </th>';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '<th style="width:140px;border-right:1px solid black;">WeighBridge No</th>';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-right:1px solid black;">Company GST </th>';
$html .= '<th style="width:140px;border-right:1px solid black;"> </th>';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '</tr>';


$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:40px;border-top:1px solid black;border-right:1px solid black;">SNo</th>';
$html .= '<th style="width:180px;border-top:1px solid black;border-right:1px solid black;">Item</th>';

$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">HSN Code</th>';
$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">Bags</th>';

$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">Quantity</th>';
$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">UOM</th>';

$html .= '<th style="width:50px;border-top:1px solid black;border-right:1px solid black;">Rate</th>';
$html .= '<th style="width:50px;border-top:1px solid black;border-right:1px solid black;">Amount</th>';
$html .= '</tr>';

$cbags = $cQunatity = $cAmt = 0;
for($i = 1;$i <= $c;$i++){

    if($i <= $c){
            $codes = $codes."".$code[$i]."<br/>";
            $sunits = $sunits."".$sunit[$i]."<br/>";
            if(!empty($feed_name[$icode[$i]])){
                $qty  =  $quantity[$i] ;
                $quantitys = $quantitys."".number_format_ind($quantity[$i])."(".round($quantity[$i] / 50).") Bags<br/>";
            }
            else{
                $qty = $quantity[$i] ;
                $quantitys = $quantitys."".number_format_ind($quantity[$i])."<br/>";
            }
            

            $html .= '<tr style="line-height: 2.8;">';
            $html .= '<th style="width:40px;border-top:1px solid black;border-right:1px solid black;">'.$i.'</th>';
            $html .= '<th style="width:180px;border-top:1px solid black;border-right:1px solid black;">'.$codes.'</th>';

            $html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">HSN Code</th>';
            $html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">'.($qty/50).'</th>';

            $html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">'.$quantitys.'</th>';
            $html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;">'.$sunit[$i].'</th>';

            $html .= '<th style="width:50px;border-top:1px solid black;border-right:1px solid black;">'.$price.'</th>';
            $html .= '<th style="width:50px;border-top:1px solid black;border-right:1px solid black;">'.$amount.'</th>';
            $html .= '</tr>';
            $cbags += ($qty/50);
            $cQunatity += $qty;
            $cAmt += $amount;

			}
			
}
$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:40px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"></th>';
$html .= '<th style="width:180px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">Total</th>';

$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"></th>';
$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">'.$cbags.'</th>';

$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">'.$cQunatity.'</th>';
$html .= '<th style="width:60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"></th>';

$html .= '<th style="width:50px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"></th>';
$html .= '<th style="width:50px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">'.$cAmt.'</th>';
$html .= '</tr>';


for($k=0;$k<8;$k++){
    $html .= '<tr style="line-height: 2.8;">';
    $html .= '<th style="width:40px;"></th>';
    $html .= '<th style="width:180px;"></th>';

    $html .= '<th style="width:60px;"></th>';
    $html .= '<th style="width:60px;"></th>';

    $html .= '<th style="width:60px;"></th>';
    $html .= '<th style="width:60px;"></th>';

    $html .= '<th style="width:50px;"></th>';
    $html .= '<th style="width:50px;"></th>';
    $html .= '</tr>';
}


$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:560px;text-align:left;border-top:1px solid black;"><b>'.convert_number_to_currency_words($cAmt).'</b></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;">Not For Sale Own Use Only</th>';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;">Note</th>';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;text-align:center;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-right:1px solid black;"></th>';
$html .= '<th style="width:280px;border-right:1px solid black;text-align:center;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-right:1px solid black;"></th>';
$html .= '<th style="width:280px;border-right:1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-right:1px solid black;border-top:1px solid black;"></th>';
$html .= '<th style="width:140px;border-right:1px solid black;border-top:1px solid black;"></th>';
$html .= '<th style="width:140px;"></th>';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '<th style="width:140px;border-right:1px solid black;"></th>';
$html .= '<th style="width:280px;text-align:center;">'.$fullcname.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-right:1px solid black;">Customer Sign</th>';
$html .= '<th style="width:140px;border-right:1px solid black;">Driver Sign</th>';
$html .= '<th style="width:280px;text-align:center;">Authorized Signature</th>';
$html .= '</tr>';

$html .= '</table>';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 9, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(5, 5, 10, true);
//$pdf->setCellHeightRatio(1.5);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>