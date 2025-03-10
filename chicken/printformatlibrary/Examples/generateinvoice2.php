<?php
//generateinvoice
require_once('tcpdf_include.php');
include "../../config.php";
include "number_format_ind.php";

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
$fetch_date = date("Y-m-d",strtotime($_POST['pdates']));
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
	$bank_name = $row['bank_name'];
	$bank_branch = $row['bank_branch'];
	$bank_accno = $row['bank_accno'];
	$bank_ifsc = $row['bank_ifsc'];
	$bank_accname = $row['bank_accname'];
	$upi_details = $row['upi_details'];
	$upi_mobile = $row['upi_mobile'];
}
$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bank_flag = $row['bank_flag']; }
if($bank_flag == "" || $bank_flag == NULL || $bank_flag == 0 || $bank_flag == "0"){ $bank_flag = 0; }
$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $description[$row['code']] = $row['description']; }

$html = "";
$inv_nos = array();
$inum = explode("@",$_GET['id']);
$inv_nos[0] = $inum[0];
$isize = sizeof($inv_nos);
$icount = 0;

foreach($inv_nos as $inv_no){
	$icount++;
	if($inv_no != ""){
		$id = $inv_no;
		$post = $camt = $oinv = $ocdn = $obdramt = $orct = $occn = $obcramt = $ob_mortality = $ob_returns = 0;
		$sql = "SELECT * FROM `customer_sales` WHERE `invoice` LIKE '$id'";
		$query = mysqli_query($conn,$sql); $c = 0;
		while($row = mysqli_fetch_assoc($query)){
			$c = $c + 1;
			$slno[$c] = $c;
			$iname[$c] =  $description[$row['itemcode']];
			$qty[$c] = $row['netweight'];
			$price[$c] = $row['itemprice'];
			$amt[$c] = $row['totalamt'];
			$tcdsper = $row['tcdsper'];
			$tcdsamt = $row['tcdsamt'];
			$roundoff = $row['roundoff'];
			$famt = $row['finaltotal'];
			$inv = $row['invoice'];
			$ccode = $row['customercode'];
			$odate = $row['date'];
			$amtwds = $row['amtinwords'];
		}
		$dt = date("d.m.Y",strtotime($odate));
		$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$ccode'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $cname[$row['code']] = $row['name'];$cgst[$row['code']] = $row['gstinno']; if($row['obtype'] == "Cr") { $obcramt = $row['obamt']; $obdramt = 0; } else { $obdramt = $row['obamt']; $obcramt = 0; } }

		$old_inv = "";
		$sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` <= '$odate' AND `invoice` NOT IN ('$id') AND `customercode` LIKE '$ccode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv = $oinv + $row['finaltotal']; $old_inv = $row['invoice']; } }
		$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` <= '$odate' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $orct = $row['tamt']; }
		$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` <= '$odate' AND `ccode` LIKE '$ccode' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn = $row['tamt']; } else { $occn = $row['tamt']; } }
		$obsql = "SELECT * FROM `main_mortality` WHERE `date` <= '$odate' AND `ccode` = '$ccode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
		$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_mortality = $ob_mortality + $obrow['amount']; }
		$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$odate' AND `vcode` = '$ccode' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
		$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = $ob_returns + $obrow['amount']; }
		$post = $oinv + $ocdn + $obdramt - $orct - $occn - $obcramt - $ob_returns - $ob_mortality;
			
		$html .= '<table align="center" style="border: 1px solid black;">';
		//$html .= '<tr>';
		//$html .= '<td colspan="7" style="text-align:center;">Cash Bill</td>';
		//$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="text-align:center;"><br/><br/><br/>';
		$html .= '<img src="../../'.$img_path.'" height="60px" />';
		$html .= '</th>';
		$html .= '<th colspan="5" style="text-align:center;">';
		$html .= '<i align="center">'.$cdetail.'</i><br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b align="left">Invoice No.:</b>'.$inv.'<br/>';
		$html .= '</th>';
		$html .= '<th colspan="4" style="padding-right: 10px;text-align:right;">';
		$html .= '<b align="right">Date:</b>'.$dt.'<br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Billing Name:</b>'.$cname[$ccode].'<br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">GST No.:</b>'.$cgst[$ccode].'<br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th style="border: 1px solid black;">Sl.No.</th>';
		$html .= '<th colspan="3" style="border: 1px solid black;">Particulars</th>';
		$html .= '<th style="border: 1px solid black;">Quantity</th>';
		$html .= '<th style="border: 1px solid black;">Rate</th>';
		$html .= '<th style="border: 1px solid black;">Amount</th>';
		$html .= '</tr>';
		$html .= '</table>';
		$slnos = $inames = $qtys = $prices = $amts = "<br/><br/>";
		for($i = 1;$i <= $c;$i++){
			if($i <$c){
				$slnos = $slnos."".$slno[$i]."<br/><br/>";
				$inames = $inames."".$iname[$i]."<br/><br/>";
				$qtys = $qtys."".number_format_ind($qty[$i])."<br/><br/>";
				$prices = $prices."".number_format_ind($price[$i])."<br/><br/>";
				$amts = $amts."".number_format_ind($amt[$i])."<br/><br/>";
			}
			else {
				$br = "";
				//$k = 9 - $c;
				for($j = 4;$j >= $c; $j--){
					$br = $br."<br/><br/>";
				}
				$slnos = $slnos."".$slno[$i]."".$br;
				$inames = $inames."".$iname[$i]."".$br;
				$qtys = $qtys."".number_format_ind($qty[$i])." ".$br;
				$prices = $prices."".number_format_ind($price[$i])." ".$br;
				$amts = $amts."".number_format_ind($amt[$i])." ".$br;
			}
		}
		$camt = $post + $famt;
		
		$html .= '<table align="center" height="100%" border="1">';
		$html .= '<tr>';
			$html .= '<td rowspan="1">'.$slnos.'</td>';
			$html .= '<td rowspan="1" colspan="3">'.$inames.'</td>';
			$html .= '<td rowspan="1" style="padding:5px;text-align:right;">'.$qtys.'</td>';
			$html .= '<td style="padding:5px;text-align:right;">'.$prices.'</td>';
			$html .= '<td style="padding:5px;text-align:right;">'.$amts.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<th colspan="6">TCS @ '.$tcdsper.' %</th>';
			$html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($tcdsamt).'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<th colspan="6">Round Off</th>';
			$html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($roundoff).'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<th colspan="6"><br/>Invoice Total</th>';
			$html .= '<td style="padding:5px;text-align:right;"><br/>'.number_format_ind($famt).'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			//$html .= '<td colspan="4"><br/><br/><b>Amount in words:</b> '.$amtwds.'.<br/></td>';
			$html .= '<td colspan="4"><br/><br/><b>Amount in words:</b> '.convert_number_to_words($famt).' Rupees Only.<br/></td>';
			$html .= '<th colspan="3"><br/><br/>Previous Balance : '.number_format_ind($post).'<br/>Closing Balance : '.number_format_ind($camt).'</th>';
		$html .= '</tr>';
		
		if($bank_flag == 1){
			$html .= '<tr>';
				$html .= '<td colspan="3" style="text-align:left;font-size:8px;">';
					$html .= '&nbsp;<b>Bank Name : </b>'.$bank_name.'<br/>';
					$html .= '&nbsp;<b>Branch : </b>'.$bank_branch.'<br/>';
					$html .= '&nbsp;<b>IFSC Code: </b>'.$bank_ifsc.'<br/>';
				$html .= '</td>';
				$html .= '<td colspan="4" style="text-align:left;font-size:8px;">';
					$html .= '&nbsp;<b>Acc. Holder name : </b>'.$bank_accname.'<br/>';
					$html .= '&nbsp;<b>Account Number : </b>'.$bank_accno.'<br/>';
					if($upi_details != "" || $upi_details != NULL){
					$html .= '&nbsp;<b>'.$upi_details.': </b>'.$upi_mobile.'<br/>';
					}
				$html .= '</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<td colspan="7">Declaration: This is a computer generated invoice,<br/>Seal (or) Signature is not required.</td>';
		$html .= '</tr>';
		$html .= '</table>';
		if($icount != $isize){
			$html .= '<div style="page-break-before:always"></div>';
		}
	}
}


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Sales Invoice');
$pdf->SetSubject('PrintInvoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 10, '', true);
$pdf->SetPrintHeader(false);
//$pdf->SetPrintFooter(false);
//$pdf->SetMargins(7, 7, 7, true);
$pdf->AddPage('P', 'A5');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output($id.'.pdf', 'I');

?>