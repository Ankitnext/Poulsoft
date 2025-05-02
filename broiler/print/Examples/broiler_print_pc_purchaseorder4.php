<?php
//broiler_sc_saleinvoice1.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql='SHOW COLUMNS FROM `main_companyprofile`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("loa_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_companyprofile` ADD `loa_name` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Letter of Authorization Name' AFTER `cdetails`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Purchase Order' AND `field_function` LIKE 'LOA Name Display Flag' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $loa_nflag = mysqli_num_rows($query);
// function convert_number_to_words1($amount) {
//     $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
//     $amount_parts = explode(".", number_format($amount, 2, ".", ""));
    
//     $rupees = $formatter->format($amount_parts[0]) . " rupees";
//     if (isset($amount_parts[1]) && (int)$amount_parts[1] > 0) {
//         $paisa = $formatter->format($amount_parts[1]) . " paisa";
//         return ucwords($rupees . " and " . $paisa . " only");
//     }
    
//     return ucwords($rupees . " only");
// }

function convert_number_to_words1($amount) {
    // Split the amount into rupees and paisa parts
    $amount_parts = explode(".", number_format($amount, 2, ".", ""));
    
    // Convert the rupees part into words using the Indian numbering format
    $rupees_in_words = format_indian_number($amount_parts[0]) . " rupees";
    
    // Handle paisa (fractional part), if any
    if (isset($amount_parts[1]) && (int)$amount_parts[1] > 0) {
        $paisa_in_words = format_indian_number($amount_parts[1]) . " paisa";
        return ucwords($rupees_in_words . " and " . $paisa_in_words . " only");
    }
    
    return ucwords($rupees_in_words . " only");
}

function format_indian_number($number) {
    // Map numbers to words
    $words = [
        0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen',
        15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
        20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    ];
    
    // Indian numbering units
    $units = ['crore', 'lakh', 'thousand', 'hundred', ''];
    $divisors = [10000000, 100000, 1000, 100, 1]; // Crore, Lakh, Thousand, Hundred, Units
    
    $output = [];
    foreach ($divisors as $index => $divisor) {
        $current_value = intdiv($number, $divisor); // Get the value at the current divisor
        $number %= $divisor; // Update the number to the remainder
        
        if ($current_value > 0) {
            $output[] = convert_two_digit_number($current_value, $words) . " " . $units[$index];
        }
    }
    
    return implode(" ", array_filter($output));
}

function convert_two_digit_number($number, $words) {
    if ($number <= 19) {
        return $words[$number]; // Direct mapping for numbers <= 19
    }
    
    $tens = intval($number / 10) * 10;
    $units = $number % 10;
    
    return $words[$tens] . ($units > 0 ? " " . $words[$units] : "");
}


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
			//$paisa = " and ".$words[$atemp[1]*10]." paisa only";
			$paisa = " and ".convert_number_to_words($atemp[1])." paisa only";
		}
		else{
			$paisa = "";
		}
        $words_string .= $paisa;
    }
    return $words_string;
    
}

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
	$company_gstno = $row['com_gstinno'];
	$comname = $row['cname'];
	$loa_name = $row['loa_name'];
	$shipping_address_print = $row['shipping_address_print'];
}

$item_name = $item_hsn = array();
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_units[$row['code']] = $row['sunits'];
    $item_hsn[$row['code']] = $row['hsn_code']; }

$sql = "SELECT * FROM `main_disclaimer` WHERE `type` = 'Sale Invoice Format-2'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $note = $row['note']; }

$sql = "SELECT * FROM `main_terms` WHERE `type` = 'Sale Invoice Format-2'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $terms = $row['description']; }

$sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description'];$sector_address[$row['code']] = $row['sector_address']; }


$sql = "SELECT * FROM `broiler_employee` "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $employe_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access` "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $db_emp_Code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $tporter_code[$row['code']] = $row['code']; $tporter_name[$row['code']] = $row['description']; }
    
$view_type = $_GET['view_type'];
$ids = $_GET['trnum'];
$sql = "SELECT * FROM `broiler_pc_purchaseorder` WHERE `trnum` = '$ids' AND `dflag` = '0' ORDER BY `id` ASC"; // AND `active` = '1' AND `aut_flag` = '1'
$query = mysqli_query($conn,$sql); $i = 0; $items = array();
while($row = mysqli_fetch_assoc($query)){
    
    $credit_term = $row['credit_term'];
    $remarks = $row['remarks'];
    $billno = $row['billno'];
    $trnum = $row['trnum'];
    $tdate = $row['date'];
    $date = date("d.m.Y",strtotime($row['date']));
    $vcode = $row['vcode'];
    $addedemp = $employe_name[$db_emp_Code[$row['addedemp']]];
 
    $warehouse_code[$i] = $row['warehouse'];
    $warehouse[$i] = $sector_name[$row['warehouse']];
    if($row['delivery_date'] != '0000-00-00'){
        $delivery_date[$i] = date("d.m.Y",strtotime($row['delivery_date']));
    }else{
        $delivery_date[$i] = "";
    }
   
    $items[$i] = $item_name[$row['item_code']];
    $hsn[$i] = $item_hsn[$row['item_code']];
    $units[$i] = $item_units[$row['item_code']];
    $qty[$i] = number_format_ind($row['rcvd_qty']);
    $free_qty[$i] = number_format_ind($row['free_qty']);
    $rate[$i] = number_format_ind($row['rate']);
    $gross_amt[$i] = number_format_ind($row['amount']);
    $gst_per[$i] = number_format_ind($row['gst_per']);

    $disc_per = number_format_ind($row['disc_per']);
    //$gst_per = number_format_ind($row['gst_per']);
    $dis_amt = number_format_ind($row['disc_amt']);
    $gst_amt = $gst_amt + $row['gst_amt'];
    $inv_amt = $inv_amt + $row['inv_amt'];

    $tot_qty += $row['rcvd_qty'];
    $tot_fqty += $row['free_qty'];
    $tot_amount += $row['amount'];

    $payment_cond[$i] = $row['payment_cond'];
    $delivery_cond[$i] = $row['delivery_cond'];
    $freight_cond[$i] = $row['freight_cond'];
    $quotation_cond[$i] = $row['quotation_cond'];
    $pfCharges_cond[$i] = $row['pfCharges_cond'];
    $other_cond[$i] = $row['other_cond'];

    $i++;
}



$ven_code = $ven_name = $ven_address = $ven_saddress = $ven_mobile = $ven_gstin = $ven_state = "";
$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$vcode'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $ven_code = $row['code'];
    $customer_name = $row['name'];
    $cus_billing_address = $row['baddress'];
    $ven_saddress = $row['saddress'];
    $ven_mobile = $row['mobile1'];
    $cus_gstno = $row['gstinno'];
    $ven_state = $row['state_code'];
    $cus_panno = $row['pan_no'];
    $cus_mobileno = $row['mobile1'];

    $cus_bankacc_no = $row['bank_accno'];
    $cus_bank_details = $row['bank'];
    $cus_bank_ifsc = $row['bank_ifsc_code'];

    $ven_saddress1 = str_replace( ',', ',<br/>', $ven_saddress );
}

$header1 = '';
$header1 .= '<h1 align = "center">Purchase Order</h1>';
$header1 .= '<table style="line-height: 1.5;border-right:1px sold black;border-left:1px sold black;border-top:1px sold black;">';
$header1 .= '<tr style="line-height: 2;">';
//$header1 .= '<th style="width:130px;text-align:left;"><span style="font-size: 8px; "><br/><b>GST In: </b></span>'.$company_gstno.'</th>';
$header1 .= '<th rowspan="1" style="width:430px;text-align:left;"><br/><br/><img src="../../'.$img_path.'" height="100px" /></th>';
$header1 .= '<th style="width:130px;text-align:left;"><span style="font-size: 8px; "><br/><b>Po No: </b></span>'.$billno.'<br/><b>Po Date: </b>'.$date.'</th>';
$header1 .= '</tr>';
//$header1 .= '<tr>';
//$header1 .= '<th style="width:560px;text-align:center;"><span style="font-size: 12px; "><b>Purchase Order</b></span></th>';
//$header1 .= '</tr>';
$header1 .= '<tr>';
$header1 .= '<th style="width:400px;text-align:left;"><span style="font-size: 9px;color:red "><b>Vendor: </b></span></th>';
$header1 .= '<th style="width:160px;text-align:left;"><span style="font-size: 9px;color:red "><b>Ship To: </b></span></th>';
$header1 .= '</tr>';
$header1 .= '<tr>';
$header1 .= '<th style="width:400px;text-align:left;"><span style="font-size: 8px;color:black ">'.$customer_name.'<br/>'.$ven_saddress1.'<br/>GST No: '.$cus_gstno.'<br/><br/></span></th>';
//$header1 .= '<th style="width:160px;text-align:left;"><span style="font-size: 8px;color:black ">'.$warehouse[0].'<br/>'.$sector_address[$warehouse_code[0]].'</span></th>';
$header1 .= '<th style="width:160px;text-align:left;"><span style="font-size: 8px;color:black ">'.$shipping_address_print.'</span></th>';
$header1 .= '</tr>';
$header1 .= '</table>';

$hbody = '';


$hbody .= '<table style="line-height: 1.8;">';
$hbody .= '<tr>';
$hbody .= '<th style="width:25px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-left:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>S.No</b></th>';
$hbody .= '<th style="width:295px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>Product Name</b></th>';
$hbody .= '<th style="width:50px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>QTY</b></th>';
$hbody .= '<th style="width:40px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>Free Qty</b></th>';
$hbody .= '<th style="width:40px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>Units</b></th>';
$hbody .= '<th style="width:40px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>Price</b></th>';
//$hbody .= '<th style="width:40px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>GST</b></th>';
$hbody .= '<th style="width:70px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;"><b>Basic Amount</b></th>';
$hbody .= '</tr>';

$dsize = sizeof($items);
if($dsize < 8){
    $dval = 8;
}else{
    $dval = $dsize + 1;  
}

for($i = 0;$i < $dval;$i++){
    $slno = $i + 1;
    
    if($delivery_date[$i] ==  '1970.01.01'){
        $delivery_date[$i] = "";
    }
    if($i < $dsize){
        $hbody .= '<tr style="line-height: 1.8;">';
        $hbody .= '<td style="padding:5px;width:25px;text-align:center;border-right:1px sold black;border-left:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$slno.'</td>';
        $hbody .= '<td style="padding:5px;width:295px;text-align:left;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$items[$i].'</td>';
        $hbody .= '<td style="padding:5px;width:50px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$qty[$i].'</td>';
        $hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$free_qty[$i].'</td>';
        $hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$units[$i].'</td>';
        $hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$rate[$i].'</td>';
        //$hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$gst_per[$i].' %</td>';
        $hbody .= '<td style="padding:5px;width:70px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;font-size: 8px;">'.$gross_amt[$i].'</td>';
        $hbody .= '</tr>';
    }
    else if($i >= $dsize){
        $hbody .= '<tr style="line-height: 1.8;">';
        $hbody .= '<td style="padding:5px;width:25px;text-align:center;border-right:1px sold black;border-left:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '<td style="padding:5px;width:295px;text-align:center;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '<td style="padding:5px;width:50px;text-align:left;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '<td style="padding:5px;width:40px;text-align:center;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        //$hbody .= '<td style="padding:5px;width:40px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '<td style="padding:5px;width:70px;text-align:right;border-right:1px sold black;border-bottom:1px sold black;"></td>';
        $hbody .= '</tr>';
    }
}

$hbody .= '</table>';

$hbody .= '<table style="line-height: 2.1;">';
$hbody .= '<tr>';
$hbody .= '<th  style="width:370px;padding-left: 10px;text-align:left;border-left:1px sold black;">';
$hbody .= '<p style="padding-left: 10px;text-align:left;color:black;font-weight:bold;"><b>Remarks: </b></p>';
$hbody .= '</th>';
$hbody .= '<th  style="width:120px;padding-left: 10px;text-align:center;border-left: 1px solid black;border-top: 1px solid black;"><p style="padding-left: 10px;text-align:center;"><b>Taxable Amount</b></p></th>';
$hbody .= '<th  style="width:70px;padding-left: 10px;text-align:right;border-left: 1px solid black;;border-top: 1px solid black;border-right: 1px solid black;"><p style="padding-left: 10px;text-align:right;"><b>'.number_format_ind($tot_amount).'</b></p></th>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<td rowspan="3" style="width:370px;padding-left: 10px;text-align:left;border-left:1px sold black;border-bottom:1px sold black;">'.$remarks.'</td>';
$hbody .= '<th style="width:120px;padding-left: 10px;text-align:left;border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;"><p style="padding-left: 10px;text-align:center;"><b>SGST '.number_format_ind($gst_per[0] / 2).' %</b></p></th>';
$hbody .= '<td style="width:70px;padding-right: 10px;text-align:right;color:black;border-top: 1px solid black;border-right: 1px solid black;font-size: 9px;">'.number_format_ind($gst_amt/2).'</td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th  style="padding-left: 10px;text-align:left;border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;"><p style="padding-left: 10px;text-align:center;"><b>CGST '.number_format_ind($gst_per[0] / 2).' %</b></p></th>';
$hbody .= '<td style="padding-right: 10px;text-align:right;color:black;border-top: 1px solid black;border-right: 1px solid black;font-size: 9px;">'.number_format_ind($gst_amt/2).'</td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th  style="padding-left: 10px;text-align:left;border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;"><p style="padding-left: 10px;text-align:center;"><b>Total Amount</b></p></th>';
$hbody .= '<td style="padding-right: 10px;text-align:right;color:black;border-top: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;"><b>'.number_format_ind($inv_amt).'</b></td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th style="width:80px;padding-left: 10px;text-align:left;border-left: 1px solid black;border-bottom: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Amount in words:</b></th>';
$hbody .= '<td style="width:480px;padding-right: 10px;text-align:left;color:black;border-right: 1px solid black;border-bottom: 1px solid black;">'.convert_number_to_words1($inv_amt).'</td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th  style="width:360px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Terms & Conditions:</b></th>';
$hbody .= '<td style="width:200px;padding-right: 10px;text-align:right;color:black;border-right: 1px solid black;"></td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><p style="padding-left: 10px;text-align:left;"><b>Payment: </b>'.$payment_cond[0].'<br/><b>Delivery: </b>'.$delivery_cond[0].'<br/><b>Freight: </b>'.$freight_cond[0].'<br/><b>Qutation Date: </b>'.$quotation_cond[0].'<br/><b>P&F Charges: </b>'.$pfCharges_cond[0].'<br/><b>Others: </b>'.$other_cond[0].'</p></th>';
$hbody .= '<td style="padding-right: 10px;text-align:right;color:black;border-right: 1px solid black;"></td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th  style="width:330px;padding-left: 10px;text-align:left;border-left:1px sold black;"><p style="padding-right: 10px;text-align:left;"><b>Declaration: </b></p></th>';
$hbody .= '<td style="width:230px;padding-right: 10px;text-align:center;color:black;border-right: 1px solid black;"><p style="padding-right: 10px;text-align:center;"><b>'.$comname.',</b></p></td>';
$hbody .= '</tr>';
$hbody .= '<tr>';
$hbody .= '<th  style="width:360px;padding-left: 10px;text-align:left;border-bottom: 1px solid black;border-left:1px sold black;"><p style="padding-right: 10px;text-align:left;">We Declare that this Po shows the actual price of Goods <br/> described and that all perticulars are true and correct. <br/></p></th>';
$hbody .= '<td style="line-height:1.2;width:140px;padding-right: 10px;text-align:center;color:black;border-bottom: 1px solid black;"><br/><br/><br/>Authorised Signatory';
if((int)$loa_nflag == 1){ $hbody .= '<br/><p style="text-align:center;">'.$loa_name.'</p>'; }
$hbody .= '</td>';
$hbody .= '<td style="width:60px;padding-right: 10px;text-align:right;color:black;border-right: 1px solid black;border-bottom: 1px solid black;"></td>';
$hbody .= '</tr>';
$hbody .= '</table>';





$html = '';
$html .= $header1;
$html .= $hbody;
//echo $html;

$paper_mode = "P"; $paper_size = "A4";
$file_name1 =  $customer_name."_purchaseorder_".date("YmdHis");
require_once('tcpdf_include.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Purchase ORder');
$pdf->SetSubject('Indent PDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$fontname = TCPDF_FONTS::addTTFfont('font-family/MAIAN.TTF', 'TrueType', '', 32);
$pdf->SetFont($fontname, '', 9, '', true);
$dt = date("d.m.Y",strtotime($odate));
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(5, 5, 5, true);
$pdf->AddPage($paper_mode, $paper_size);
$file_name = str_replace(" ","_",$file_name1);
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


if($view_type == 'send_pdf'){

    $file = $pdf->Output(__DIR__."/".$file_name.".pdf",'F');
    $filepath = "https://broiler.poulsoft.co.in/print/Examples/".$file_name.".pdf";

    $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'PurchaseOrder' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $url_id = $row['url_id']; $instance_id = $row['sms_key']; $access_token = $row['msg_key']; $numers = $row['numers']; }

    $mobilenos = array(); if($ven_mobile != ""){ $mobilenos = explode(",",$ven_mobile); } else{ $mobilenos = explode(",",$ven_mobile); }

    foreach($mobilenos as $mobile){
        if(strlen($mobile) == 10 || strlen($mobile) == "10"){
            $message = "Purchase Order: ".date("d.m.Y",strtotime($tdate));
            $message = str_replace(" ","+",$message);

            $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $curlopt_url = $row['curlopt_url'];
                $curlopt_returntransfer = $row['curlopt_returntransfer'];
                $curlopt_encoding = $row['curlopt_encoding'];
                $curlopt_maxredirs = $row['curlopt_maxredirs'];
                $curlopt_timeout = $row['curlopt_timeout'];
                $curlopt_followlocation = $row['curlopt_followlocation'];
                $curlopt_http_version = $row['curlopt_http_version'];
                $curlopt_customrequest = $row['curlopt_customrequest'];
            }
            if($url_id == 3){

                

            }else{
                $media_url = $filepath; $filename = $file_name.".pdf"; $number = "91".$mobile; $type = "media";
                $url = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&media_url='.$media_url.'&filename='.$filename.'&instance_id='.$instance_id.'&access_token='.$access_token;
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&media_url='.$media_url.'&filename='.$filename.'&instance_id='.$instance_id.'&access_token='.$access_token,
                CURLOPT_RETURNTRANSFER => $curlopt_returntransfer,
                CURLOPT_ENCODING => $curlopt_encoding,
                CURLOPT_MAXREDIRS => $curlopt_maxredirs,
                CURLOPT_TIMEOUT => $curlopt_timeout,
                CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
                CURLOPT_HTTP_VERSION => $curlopt_http_version,
                CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
                ));
    
                $response = curl_exec($curl);
                curl_close($curl);
                $d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);
            }
            

            if($response != ""){
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$tdate' AND `tdate` >= '$tdate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                
                $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$tdate' AND `tdate` >= '$tdate' AND `type` = 'transactions'";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                
                if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                $wapp_code = "WAPP-".$incr_wapp;
                
                $wsfile_path = $_SERVER['REQUEST_URI'];
                $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime)
                VALUES ('$wapp_code','$vcode','$mobile','$url','$d3[1]','$response','Purchase Order','$wsfile_path','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$sql)) { } else{  }
            }
        }
    }
    unlink(__DIR__."/".$file_name.".pdf");
    ?>
    <script>window.opener = self; window.close();</script>
    <?php
    exit();
}else{

    
    
    $pdf->Output('purchaseorder.pdf', 'I');
}


?>