<?php
//cus_daysalesinvoices_hotel.php
require_once('tcpdf_include.php');
include "number_format_ind.php";
include "../../config.php";
$dbname = $_SESSION['dbase'];


function convert_number_to_words($amount) {
	$words = array();
	$words[0] = '';
	$words[1] = 'One';
	$words[2] = 'Two';
	$words[3] = 'Three';
	$words[4] = 'Four';
	$words[5] = 'Five';
	$words[6] = 'Six';
	$words[7] = 'Seven';
	$words[8] = 'Eight';
	$words[9] = 'Nine';
	$words[10] = 'Ten';
	$words[11] = 'Eleven';
	$words[12] = 'Twelve';
	$words[13] = 'Thirteen';
	$words[14] = 'Fourteen';
	$words[15] = 'Fifteen';
	$words[16] = 'Sixteen';
	$words[17] = 'Seventeen';
	$words[18] = 'Eighteen';
	$words[19] = 'Nineteen';
	$words[20] = 'Twenty';
	$words[30] = 'Thirty';
	$words[40] = 'Forty';
	$words[50] = 'Fifty';
	$words[60] = 'Sixty';
	$words[70] = 'Seventy';
	$words[80] = 'Eighty';
	$words[90] = 'Ninety';

	$amount = strval($amount);

	$atemp = explode(".",$amount);
	$number = str_replace(",","",$atemp[0]);
	$n_length = strlen($number);
	$words_string = "";

	if($n_length <= 9){
		$received_n_array = array(); $n_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

		for ($i = 0; $i < $n_length; $i++) {
            $received_n_array[$i] = substr($number,$i, 1);
        }
        for ($i = 9 - $n_length, $j = 0; $i < 9; $i++, $j++) {
            $n_array[$i] = $received_n_array[$j];
        }
        for ($i = 0, $j = 1; $i < 9; $i++, $j++) {
            if ($i == 0 || $i == 2 || $i == 4 || $i == 7) {
                if ($n_array[$i] == 1) {
                    $n_array[$j] = 10 + (int)$n_array[$j];
                    $n_array[$i] = 0;
                }
            }
        }
        $value = "";
        for ($i = 0; $i < 9; $i++) {
            if ($i == 0 || $i == 2 || $i == 4 || $i == 7) {
                $value = $n_array[$i] * 10;
            } else {
                $value = $n_array[$i];
            }
            if ($value != 0) {
                $words_string .= $words[$value]." ";
            }
            if (($i == 1 && $value != 0) || ($i == 0 && $value != 0 && $n_array[$i + 1] == 0)) {
                $words_string .= "Crores ";
            }
            if (($i == 3 && $value != 0) || ($i == 2 && $value != 0 && $n_array[$i + 1] == 0)) {
                $words_string .= "Lakhs ";
            }
            if (($i == 5 && $value != 0) || ($i == 4 && $value != 0 && $n_array[$i + 1] == 0)) {
                $words_string .= "Thousand ";
            }
            if ($i == 6 && $value != 0 && ($n_array[$i + 1] != 0 && $n_array[$i + 2] != 0)) {
                $words_string .= "Hundred and ";
            }
			else if ($i == 6 && $value != 0) {
                $words_string .= "Hundred ";
            }
        }
        $words_string = str_replace("  "," ",$words_string);
		if((int)$atemp[1] > 0){
			$paisa = " and ".$words[$atemp[1]*10]." paisa only";
		}
		else{
			$paisa = "rupees only";
		}
        $words_string .= $paisa;
    }
    return $words_string;
}

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
	$comname = $row['cname'];
}
$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bank_flag = $row['bank_flag']; }
if($bank_flag == "" || $bank_flag == NULL || $bank_flag == 0 || $bank_flag == "0"){ $bank_flag = 0; }
$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $description[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%broiler bird%' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$html = "";
$day_ccode = $day_date = array();
$day_details = explode("@",$_GET['id']);
$day_date[0] = $day_details[0];
$day_ccode[0] = $day_details[1];
$isize = sizeof($day_ccode);
$icount = 0;

foreach($day_ccode as $cuscode){
	$icount++;
	if($cuscode != ""){
		$id = $cuscode; $slno = $iname = $bird = $qty = $price = $amt = $act_wt = array();
		$tot_birds = $tot_actwts = $tot_qty = $post = $camt = $oinv = $ocdn = $obdramt = $orct = $occn = $obcramt = $ob_mortality = $ob_returns = 0;
		$sql = "SELECT * FROM `customer_sales` WHERE `date` = '$day_date[0]' AND `customercode` = '$id' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
		
		$query = mysqli_query($conn,$sql); $c = 0;
		while($row = mysqli_fetch_assoc($query)){
			$c = $c + 1;
			$slno[$c] = $c;
			$iname[$c] = $description[$row['itemcode']];
			$tot_birds += $bird[$c] = $row['birds'];
			$tot_qty += $qty[$c] = $row['netweight'];
			$price[$c] = $row['itemprice'];
			$amt[$c] = $row['totalamt'];
			$tcdsper = $row['tcdsper'];
			$tcdsamt = $row['tcdsamt'];
			$roundoff = $row['roundoff'];
			//$famt = $row['finaltotal'];
			$inv = $row['invoice'];
			$ccode = $row['customercode'];
			$odate = $row['date'];
			$amtwds = $row['amtinwords'];

            $icode = $row['itemcode'];
            $netweight = $row['netweight'];
            $sql1 = "SELECT * FROM `customer_price` WHERE `ccode` = '$ccode' AND `itemcode` = '$icode' AND `active` = '1' AND `dflag` = '0' AND `price_type2` = 'M' AND `id` IN (SELECT MAX(id) as id FROM `customer_price` WHERE `ccode` = '$ccode' AND `itemcode` = '$icode' AND `active` = '1' AND `dflag` = '0' AND `price_type2` = 'M')";
            $query1 = mysqli_query($conn,$sql1); $act_wt_count = mysqli_num_rows($query1);
            if($act_wt_count > 0){
                while($row1 = mysqli_fetch_assoc($query1)){
                    $act_wt[$c] = round(($netweight * ($row1['value2'])),2);
                    $tot_actwts += $act_wt[$c];
                }
            }
            else{
                $act_wt[$c] = round(($netweight * (1)),2);
				$tot_actwts += $act_wt[$c];
            }
        }
		$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` = '$odate' AND `code` = '$bird_code' AND `active` = '1' AND `dflag` = '0'";
		$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $paper_rate = $row['new_price']; }

		$dt = date("d.m.Y",strtotime($odate));
		$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$ccode'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $cname[$row['code']] = $row['name'];$cgst[$row['code']] = $row['gstinno']; if($row['obtype'] == "Cr") { $obcramt = $row['obamt']; $obdramt = 0; } else { $obdramt = $row['obamt']; $obcramt = 0; } }

		$old_inv = "";
		$sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` < '$day_date[0]' AND `customercode` LIKE '$ccode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv = $oinv + $row['finaltotal']; $old_inv = $row['invoice']; } }
		$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` < '$day_date[0]' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $orct = $row['tamt']; }
		$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` = '$day_date[0]' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $current_orct = $row['tamt']; }
		$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` < '$day_date[0]' AND `ccode` LIKE '$ccode' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn = $row['tamt']; } else { $occn = $row['tamt']; } }
		$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$day_date[0]' AND `ccode` = '$ccode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
		$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_mortality = $ob_mortality + $obrow['amount']; }
		$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$day_date[0]' AND `vcode` = '$ccode' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
		$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = $ob_returns + $obrow['amount']; }
		$post = (($oinv + $ocdn + $obdramt) - ($orct + $occn + $obcramt + $ob_returns + $ob_mortality));

		$html .= '<table align="center" style="border: 1px solid black;">';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="text-align:center;"><br/><br/><br/>';
		$html .= '<img src="../../'.$img_path.'" height="60px" />';
		$html .= '</th>';
		$html .= '<th colspan="5" style="text-align:center;">';
		$html .= '<p align="left">'.$cdetail.'</p>';
		$html .= '</th>';
		$html .= '</tr>';
		if($dbname == "poulso6_chicken_ka_bestbroilers"){
			$html .= '<tr>';
			$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
			$html .= '<b align="left">Date: '.$dt.'</b>';
			$html .= '</th>';
			$html .= '<th colspan="3" style="padding-right: 10px;text-align:right;">';
			$html .= '<b align="right">Paper Rate: '.number_format_ind($paper_rate).'</b>';
			$html .= '</th>';
			$html .= '</tr>';
		}
		else{
			$html .= '<tr>';
			$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
			$html .= '<b align="left">Date: '.$dt.'</b>';
			$html .= '</th>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		if($cus_remarks != ''){
			$html .= '<b style="padding-left: 10px;text-align:left;">Billing Name: </b>'.$cname[$ccode].'('.$cus_remarks.') <br/>';
		}else{
			$html .= '<b style="padding-left: 10px;text-align:left;">Billing Name: '.$cname[$ccode].'</b><br/>';
		}
		
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">GST No.:</b>'.$cgst[$ccode].'<br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '</table>';
		$slnos = $inames =  $actwts = $qtys = $prices = $amts = "<br/><br/>";
		for($i = 1;$i <= $c;$i++){
			if($i <$c){
				$slnos = $slnos."".$slno[$i]."<br/><br/>";
				$actwts = $actwts."".number_format_ind(round($act_wt[$i],2))."&nbsp;&nbsp;<br/><br/>";
				$inames = $inames."".$iname[$i]."<br/><br/>";
				$qtys = $qtys."".number_format_ind($qty[$i])."&nbsp;&nbsp;<br/><br/>";
				$prices = $prices."".number_format_ind($price[$i])."&nbsp;&nbsp;<br/><br/>";
				$amts = $amts."".number_format_ind($amt[$i])."&nbsp;&nbsp;<br/><br/>";
			}
			else {
				$br = "";
				//$k = 9 - $c;
				for($j = 4;$j >= $c; $j--){
					$br = $br."<br/><br/>";
				}
				$slnos = $slnos."".$slno[$i]."".$br;
				$actwts = $actwts."".number_format_ind(round($act_wt[$i],2))."&nbsp;&nbsp;".$br;
				$inames = $inames."".$iname[$i]."".$br;
				$qtys = $qtys."".number_format_ind($qty[$i])."&nbsp;&nbsp;".$br;
				$prices = $prices."".number_format_ind($price[$i])."&nbsp;&nbsp;".$br;
				$amts = $amts."".number_format_ind($amt[$i])."&nbsp;&nbsp;".$br;
			}
            $famt = $famt + $amt[$i];
		}
		$camt = (($post + $famt) - $current_orct);
		
		$html .= '<table align="center" height="100%" border="1">';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="border: 1px solid black;">Items</th>';
		$html .= '<th colspan="1" style="border: 1px solid black;">Act Wt</th>';
		$html .= '<th colspan="1" style="border: 1px solid black;">Kgs</th>';
		$html .= '<th colspan="1" style="border: 1px solid black;">Rate</th>';
		$html .= '<th colspan="2" style="border: 1px solid black;">Amount</th>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td colspan="2" style="padding:5px;text-align:center;">'.$inames.'</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$actwts.'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$qtys.'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$prices.'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="2" style="padding:5px;text-align:right;">'.$amts.'&nbsp;&nbsp;</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<th colspan="2"><br/>Invoice Total</th>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/>'.number_format_ind(round($tot_actwts,2)).'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/>'.number_format_ind($tot_qty).'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/></td>';
			$html .= '<td colspan="2" style="padding:5px;text-align:right;"><br/>'.number_format_ind($famt).'&nbsp;&nbsp;</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td colspan="3"><br/><br/><b>Amount in words:</b> '.ucfirst(strtolower(convert_number_to_words($famt))).'.<br/></td>';
            $html .= '<td colspan="4">';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;">&nbsp;Previous Balance:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($post).'&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;">&nbsp;This Bill Amount:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($famt).'&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;">&nbsp;Received Amount:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($current_orct).'&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;border-top:1px dotted black;">&nbsp;<br/>Closing Balance:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;border-top:1px dotted black;"><b><br/>'.number_format_ind($camt).'</b>&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</td>';
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
		$html .= '<td colspan="7" style="text-align:right;font-size:10px;"><br/><br/><b>'.$comname.'&ensp;&ensp;</b></td>';
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