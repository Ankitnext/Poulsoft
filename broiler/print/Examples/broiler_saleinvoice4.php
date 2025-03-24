<?php
//broiler_saleinvoice4.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
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

$t1 = array(); $t1 = explode("@", $_GET['id']);
$atrnum = $t1[0];

$trnum = $link_trnum = $date = $vcode = $billno = $icode = $birds = $snt_qty = $mort_qty = $cull_qty = $rcd_qty = $fre_qty = $rate = $dis_per = $dis_amt = 
$gst_per = $gst_amt = $tcds_per = $tcds_amt = $item_tamt = $freight_type = $freight_amt = $freight_pay_type = $freight_pay_acc = $freight_acc = $round_off = $finl_amt = 
$bal_qty = $bal_amt = $avg_price = $avg_wt = $avg_item_amount = $avg_final_amount = $profit = $remarks = $warehouse = $farm_batch = $supervisor_code = $bag_code = $bag_count = 
$batch_no = $exp_date = $vehicle_code = $driver_code = array();
$sql = "SELECT * FROM `broiler_sales` WHERE `trnum` = '$atrnum' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $i = 0;
while($row = mysqli_fetch_assoc($query)){
    $trnum[$i] = $row['trnum'];
    $link_trnum[$i] = $row['link_trnum'];
    $date[$i] = date("d.m.Y",strtotime($row['date']));
    $vcode[$i] = $row['vcode'];
    $billno[$i] = $row['billno'];
    $icode[$i] = $row['icode'];
    $birds[$i] = $row['birds'];
    $snt_qty[$i] = $row['snt_qty'];
    $mort_qty[$i] = $row['mort_qty'];
    $cull_qty[$i] = $row['cull_qty'];
    $rcd_qty[$i] = $row['rcd_qty'];
    $fre_qty[$i] = $row['fre_qty'];
    $rate[$i] = $row['rate'];
    $dis_per[$i] = $row['dis_per'];
    $dis_amt[$i] = $row['dis_amt'];
    $gst_per[$i] = $row['gst_per'];
    $gst_amt[$i] = $row['gst_amt'];
    $tcds_per[$i] = $row['tcds_per'];
    $tcds_amt[$i] = $row['tcds_amt'];
    $item_tamt[$i] = $row['item_tamt'];
    $freight_type[$i] = $row['freight_type'];
    $freight_amt[$i] = $row['freight_amt'];
    $freight_pay_type[$i] = $row['freight_pay_type'];
    $freight_pay_acc[$i] = $row['freight_pay_acc'];
    $freight_acc[$i] = $row['freight_acc'];
    $round_off[$i] = $row['round_off'];
    $finl_amt[$i] = $row['finl_amt'];
    $bal_qty[$i] = $row['bal_qty'];
    $bal_amt[$i] = $row['bal_amt'];
    $avg_price[$i] = $row['avg_price'];
    $avg_wt[$i] = $row['avg_wt'];
    $avg_item_amount[$i] = $row['avg_item_amount'];
    $avg_final_amount[$i] = $row['avg_final_amount'];
    $profit[$i] = $row['profit'];
    $remarks[$i] = $row['remarks'];
    $warehouse[$i] = $row['warehouse'];
    $farm_batch[$i] = $row['farm_batch'];
    $supervisor_code[$i] = $row['supervisor_code'];
    $bag_code[$i] = $row['bag_code'];
    $bag_count[$i] = $row['bag_count'];
    $batch_no[$i] = $row['batch_no'];
    $exp_date[$i] = $row['exp_date'];
    $vehicle_code[$i] = $row['vehicle_code'];
    $driver_code[$i] = $row['driver_code'];
    $i++;
}


$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' OR `type` = 'all'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
	$bank_name = $row['bank_name'];
    $img_path2 = $row['qr_img_path'];
    $img_path3 = $row['other_image'];
	$bank_branch = $row['bank_branch'];
	$bank_accno = $row['bank_accno'];
	$bank_ifsc = $row['bank_ifsc'];
	$bank_accname = $row['bank_accname'];
	$upi_details = $row['upi_details'];
	$upi_mobile = $row['upi_mobile'];
	$comname = $row['cname'];
}
$bank_flag = 0;
$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bank_flag = $row['bank_flag']; }
if($bank_flag == "" || $bank_flag == NULL || $bank_flag == 0 || $bank_flag == "0"){ $bank_flag = 0; }
$bank_flag = 0;

$ven_code = $ven_name = $ven_address = $ven_saddress = $ven_mobile = $ven_gstin = $ven_state = "";
$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$vcode[0]'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $ven_code = $row['code'];
    $ven_name = $row['name'];
    $ven_baddress = $row['baddress'];
    $ven_saddress = $row['saddress'];
    $ven_mobile = $row['mobile1'];
    $ven_gstin = $row['gstinno'];
    $ven_state = $row['state_code'];
}

$state_name = "";
$sql = "SELECT * FROM `country_states` WHERE `code` = '$ven_state'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $state_name = $row['name']; }

$sql = "SELECT * FROM `main_disclaimer` WHERE `type` = 'Sale Invoice Format-1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $note = $row['note']; }

$item_name = array();
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $item_code[$row['code']] = $row['code'];
    $item_name[$row['code']] = $row['description'];
    $item_hsn[$row['code']] = $row['hsn_code'];
    $item_uom[$row['code']] = $row['cunits'];
}

// Add CSS for Footer
$footer_css = '<style>
    @page {
        margin: 10mm;
    }
    body {
        margin: 0;
        padding-bottom: 100px; /* Adjust space for footer */
    }
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: left;
        border-top: 1px solid black;
        background-color: white; /* Optional background for visibility */
    }
</style>';



$header1  .= '';
$header1 .= '<table style="border:1px solid black;">';
$header1 .= '<tr style="line-height: 2.1;">';
$header1 .= '<th rowspan="3" style="width:65px;text-align:center;"><br/><br/><img src="../../'.$img_path.'" height="50px" /></th>';
$header1 .= '<th rowspan="3" style="width:200px;text-align:left;border-right:1px sold black;line-height: 1.1;"><br/>'.$cdetail.'</th>';
$header1 .= '<th rowspan="1" style="width:152px;text-align:left;border-right:1px sold black;font-size: 9px;"><br/><b>Invoice No: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date:</b><br/>'.$trnum[0].'&nbsp;&nbsp;&nbsp;&nbsp;'.$date[0].'  <b></b></th>';
$header1 .= '<th rowspan="1" style="width:145px;text-align:left; font-size: 9px;"><br/><b>Phone Pay No:</b><br/>8653729989</th>'; //<br/>Original&nbsp;&nbsp; -Buyer<br/>Duplicate -transpoter<br/>Triplicate -Supplier
$header1 .= '</tr>';

$header1 .= '<tr style="line-height: 2.1;">';
$header1 .= '<th style="width:152px;text-align:left;border-top:1px sold black;border-right:1px sold black; font-size: 9px;"><br/><b>ORDER:</b><br/><b>PALTU:</b> 7584855006<br/><b>SAMIRAN:</b> 9932693395</th>';
$header1 .= '<th style="width:145px;text-align:left;border-top:1px sold black;border-right:1px sold black; font-size: 9px;"><br/><b>Vehicle No: </b>'.$vehicle_code[0].'<br/><b>Driver No:</b> 9735554450<br/><b>Salesman No:</b> 8348531275</th>'; //<b>Eway Bill No: </b>'.$billno[0].'
$header1 .= '</tr>';

$header1 .= '<tr style="line-height: 2.1;">';
$header1 .= '<th style="width:152px;text-align:left;border-right:1px sold black;"></th>';
$header1 .= '<th style="width:145px;text-align:left;border-right:1px sold black;"></th>';
$header1 .= '</tr>';

$header1 .= '<tr>';
$header1 .= '<th rowspan="3" style="width:200px;text-align:left;border-top:1px sold black;"><br/>
<b>Consignee: '.$ven_name.'</b><br/>
<b>Address: </b>'.$ven_baddress.'<br/>
<b>Contact Person: </b><br/>
<b>Phone No: </b>'.$ven_mobile.'
<b>GSTIN: </b>'.$ven_gstin.'<br/><br/> 
<b>Supply State: </b>'.$state_name.'&ensp;&ensp;<b>State Code: </b>
</th>';
$header1 .= '<th rowspan="1" style="width:65px;text-align:right; border-top:1px sold black;border-right:1px sold black; "><br/><br/><img src="../../'.$img_path2.'" height="50px" /></th>';
$header1 .= '<th style="width:297px;text-align:center;border-top:1px sold black;border-right:1px sold black; font-size: 9px;"><br/><br/><img src="../../'.$img_path3.'" height="50px" /></th>';
// $header1 .= '<th style="width:297px;text-align:left;border-top:1px sold black;border-right:1px sold black; font-size: 9px;"><br/>
// <b>Shipping Address: </b>'.$ven_saddress.'<br/><br/><br/><br/><br/><br/>
// <b>Supply State: </b>'.$state_name.'&ensp;&ensp;<b>State Code: </b>
// </th>';

$header1 .= '</tr>';
$header1 .= '</table>';

$header2 .= '<table style="border:1px solid black;">';
$header2 .= '<tr style="line-height: 1.0;">';
$header2 .= '<th style="width:37px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Sl. No.</b><br/></th>';
//$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Item Code</b><br/></th>';
$header2 .= '<th style="width:228px;;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Description</b><br/></th>';
//$html .= '<th style="width:59px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>HSN</b><br/></th>';
$header2 .= '<th style="width:60px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Qty</b><br/></th>';
$header2 .= '<th style="width:49px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>UOM</b><br/></th>';
//$html .= '<th style="width:44px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Free</b><br/></th>';
$header2 .= '<th style="width:49px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Price</b><br/></th>';
$header2 .= '<th style="width:39px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>GST %</b><br/></th>';
//$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Discount</b><br/></th>';
$header2 .= '<th style="width:100px;;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Amount</b><br/></th>';
$header2 .= '</tr>';
$header2 .= '</table>';

// $footer_last .= '<table style="border:1px solid black;">';


// $footer_last .= '<tr style="line-height: 1;">';
// $footer_last .= '<th style="width:350px;text-align:left;"><br/><br/><b>Received Signature: </b><div style="font-size:10px;"></div><br/><br/></th>';
// $footer_last .= '<th style="width:212px;text-align:left;"><br/><br/><b>Authorized Signature: </b><br/><br/><br/><br/><br/></th>';
// $footer_last .= '</tr>';
// $footer_last .= '</table>';


$r_cnt = 0; $html = '';$maxcnt =
$html .= '<table style="border:1px solid black;">';
$dsize = sizeof($icode); $hsn_list = $gst_per_list = $tax_amt_list = $cgst_amt_list = $sgst_amt_list = $igst_amt_list = "";
for($i = 0; $i < $dsize; $i++){
    if($r_cnt == 0){ $html .= $header1."".$header2; }
    $j = $i + 1;
    $html .= '<tr style="line-height: 0.7;">';
    $html .= '<th style="width:37px;text-align:center;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.$j.'<br/></th>';
    $html .= '<th style="width:228px;text-align:center;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.$item_name[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.number_format_ind($rcd_qty[$i]).'<br/></th>';
    $html .= '<th style="width:49px;text-align:center;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.$item_uom[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.number_format_ind($rate[$i]).'<br/></th>';
    $html .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.number_format_ind($gst_per[$i]).'<br/></th>';
    $html .= '<th style="width:100px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/>'.number_format_ind($item_tamt[$i]).'<br/></th>';
    $html .= '</tr>';

    $r_cnt++;
    if($r_cnt == 29){
        $r_cnt = 0;
        $html .= $footer_last;
        $html .= '</table>';
        $html .= '<div style="page-break-before:always"></div>';
        $html .= '<table style="border:1px solid black;">';
    }

    $tot_rqty += (float)$rcd_qty[$i];
    $tot_fqty += (float)$fre_qty[$i];
    $tot_damt += (float)$dis_amt[$i];
    $tot_tamt += (float)$item_tamt[$i];

    if(!empty($item_hsn[$icode[$i]]) && $item_hsn[$icode[$i]] != "" && number_format_ind($gst_per[$i]) != "0.00"){
        $hsn_list .= "<br/>".$item_hsn[$icode[$i]];
        $gst_per_list .= "<br/>".number_format_ind($gst_per[$i]);
        $tax_amt_list .= "<br/>".number_format_ind(round(($rcd_qty[$i] * $rate[$i]),2));
        $cgst_amt_list .= "<br/>".number_format_ind(round(($gst_per[$i] / 2),2));
        $sgst_amt_list .= "<br/>".number_format_ind(round(($gst_per[$i] / 2),2));
        $igst_amt_list .= "<br/>";
    }
}


$tot_price = (float)$tot_tamt / (float)$tot_rqty;
$html .= '</table>';
$html .= '<table style="border:1px solid black;">';
$html .= '<tr style="line-height: 1.1;">';
$html .= '<th style="width: 265px;;text-align:center;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/><b>Total</b><br/></th>';
$html .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/><b>'.number_format_ind($tot_rqty).'</b></th>';
$html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><br/></th>';
$html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/><b>'.number_format_ind($tot_price).'</b></th>';
$html .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><br/></th>';
$html .= '<th style="width:100px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;"><br/><br/><b>'.number_format_ind($tot_tamt).'</b></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:462px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px;border-bottom:1px sold black;"><br/><br/><b style="font-size: 9px;">( Rs.(in Words): '.convert_number_to_words($finl_amt[0]).' )</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total</b><br/></th>';
$html .= '<th style="width:100px;text-align:right;border-top:1px sold black;border-right:1px sold black;font-size: 10px; border-bottom:1px sold black;"><br/><br/><b>'.number_format_ind($finl_amt[0]).'</b><br/></th>';
$html .= '</tr>';

// $html .= '<tr style="line-height: 0.8;">';
// $html .= '<th style="width:562px;text-align:left;border-top:1px sold black;border-right:1px sold black;font-size: 10px;border-bottom:1px sold black;"><br/><br/><b>Rs.(in Words): '.convert_number_to_words($finl_amt[0]).'</b><br/></th>';
// $html .= '</tr>';

// $html .= '<tr style="line-height: 0.8;">';
// $html .= '<th style="width:350px;text-align:left;"><br/><br/><b>Received Signature: </b><div style="font-size:10px;"></div><br/><br/></th>';
// $html .= '<th style="width:212px;text-align:left;"><br/><br/><b>Authorized Signature: </b><br/><br/><br/><br/><br/></th>';
// $html .= '</tr>';


if($bank_flag == 1){
    $html .= '<tr style="line-height: 1.5;">';
        $html .= '<td style="width:350px;text-align:left;border-top:1px sold black;border-right:1px sold black;">';
            $html .= '&nbsp;<b>Bank Name : '.$bank_name.'</b><br/>';
            $html .= '&nbsp;<b>Branch : '.$bank_branch.'</b><br/>';
            $html .= '&nbsp;<b>IFSC Code: '.$bank_ifsc.'</b><br/>';
        $html .= '</td>';
        $html .= '<td style="width:212px;text-align:left;border-top:1px sold black;border-right:1px sold black;">';
            $html .= '&nbsp;<b>Acc. Holder name : '.$bank_accname.'</b><br/>';
            $html .= '&nbsp;<b>Account Number : '.$bank_accno.'</b><br/>';
            if($upi_details != "" || $upi_details != NULL){
            $html .= '&nbsp;<b>'.$upi_details.': '.$upi_mobile.'</b><br/>';
            }
        $html .= '</td>';
    $html .= '</tr>';

    // $html .= '<tr>';
    // $html .= '<td>hlo</td>';
    // $html .= '</tr>';
}

$maxc = 9 - $dsize;
for($a=0;$a<$maxc;$a++){

    $footer_last1 .= '<tr style="line-height: 0;">';
$footer_last1 .= '<th style="width:30px;text-align:left;"></th>';
$footer_last1 .= '<th style="width:212px;text-align:left;border-right:0px sold black;"><br/><br/><br/><br/><br/><br/><br/></th>';
$footer_last1 .= '</tr>';
    

}

$html .=  $footer_last1.$footer_last;

// $html .= '<tr style="line-height: 1.5;">';
// $html .= '<th style="width:562px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Terms & Conditions: </b><div style="font-size:9px;">'.$note.'</div><br/><br/></th>';
// $html .= '</tr>';
$html .= '</table>';

     
    $html .= '<table>';
    $html .= '<tr style="line-height: 0.7; border-right:1px sold black;">';
    $html .= '<th style="width:350px;text-align:Left; padding-left:5px;"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><b>Received Signature: </b><div style="font-size:10px;"></div><br/><br/></th>';
    $html .= '<th style="width:212px;text-align:center;"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><b>Authorized Signature: </b><br/><br/><br/><br/><br/></th>';
    $html .= '</tr>';
    $html .= '</table>';


//echo $html;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(5, 5, 10, true);
//$pdf->setCellHeightRatio(1.5);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>