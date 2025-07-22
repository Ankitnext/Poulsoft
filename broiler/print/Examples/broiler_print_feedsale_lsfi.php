<?php
//cus_daysalesinvoices.php
include "../../number_format_ind.php";
include "newConfig.php";
$tblchk_dbname = $_SESSION['dbase'];
$tblchk_tblname = "Tables_in_".$tblchk_dbname;
$sqlt = "SHOW TABLES;"; $queryt = mysqli_query($conn,$sqlt);
while($rowt = mysqli_fetch_array($queryt)){
    if($rowt[$tblchk_tblname] == "acc_category"){$count1 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_coa"){$count2 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_controltype"){$count3 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_modes"){$count4 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_schedules"){$count5 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_types"){$count6 = 1; }
    else if($rowt[$tblchk_tblname] == "account_contranotes"){$count7 = 1; }
    else if($rowt[$tblchk_tblname] == "account_summary"){$count8 = 1; }
    else if($rowt[$tblchk_tblname] == "account_vouchers"){$count9 = 1; }
    else if($rowt[$tblchk_tblname] == "app_permissions"){$count10 = 1; }
    else if($rowt[$tblchk_tblname] == "authorize"){$count11 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch"){$count12 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch_bkp"){$count13 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch_bkp1"){$count14 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_breed"){$count15 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_breedstandard"){$count16 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_crdrnote"){$count17 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_daily_record"){$count18 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_daily_record_unsaved"){$count19 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_designation"){$count20 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_diseases"){$count21 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_doctorvisit"){$count22 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_egg_grading_consume"){$count23 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_egg_grading_produce"){$count24 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_employee"){$count25 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farm"){$count26 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmer"){$count27 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmer_classify"){$count28 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmergroup"){$count29 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_consumed"){$count30 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_expense"){$count31 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_formula"){$count32 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_production"){$count33 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_silos"){$count34 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_decentive"){$count35 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_incentive"){$count36 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_production"){$count37 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_mi_decentive"){$count38 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_mi_incentive"){$count39 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_pc_decentive"){$count40 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_pc_incentive"){$count41 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_si_incentive"){$count42 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_st_decentive"){$count43 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_standard"){$count44 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchentry"){$count45 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery"){$count46 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_consumed"){$count47 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_expense"){$count48 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_hatcher"){$count49 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_setter"){$count50 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_adjustment"){$count51 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_intermediate_issued"){$count52 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_intermediate_received"){$count53 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_itemreturns"){$count54 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_lab_results"){$count55 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_max_values"){$count56 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_medicine_record"){$count57 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_openings"){$count58 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_payments"){$count59 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_placementplan"){$count60 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_purchases"){$count61 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_rearingcharge"){$count62 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_receipts"){$count63 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_reportfields"){$count64 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_sales"){$count65 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_tray_settings"){$count66 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_vaccineschedule"){$count67 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_vehicle"){$count68 = 1; }
    else if($rowt[$tblchk_tblname] == "company_names"){$count69 = 1; }
    else if($rowt[$tblchk_tblname] == "company_price_list"){$count70 = 1; }
    else if($rowt[$tblchk_tblname] == "country_states"){$count71 = 1; }
    else if($rowt[$tblchk_tblname] == "customer_price"){$count72 = 1; }
    else if($rowt[$tblchk_tblname] == "customer_sales"){$count73 = 1; }
    else if($rowt[$tblchk_tblname] == "dataentry_daterange"){$count74 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_sal_generator"){$count75 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_sal_payment"){$count76 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_salary_components"){$count77 = 1; }
    else if($rowt[$tblchk_tblname] == "extra_access"){$count78 = 1; }
    else if($rowt[$tblchk_tblname] == "farm_check_list_record"){$count79 = 1; }
    else if($rowt[$tblchk_tblname] == "farmer_item_price"){$count80 = 1; }
    else if($rowt[$tblchk_tblname] == "feed_bagcapacity"){$count81 = 1; }
    else if($rowt[$tblchk_tblname] == "feedindent"){$count82 = 1; }
    else if($rowt[$tblchk_tblname] == "feedmill_expenses_parameters"){$count83 = 1; }
    else if($rowt[$tblchk_tblname] == "gateway_masters"){$count84 = 1; }
    else if($rowt[$tblchk_tblname] == "gateway_paymentlinks"){$count85 = 1; }
    else if($rowt[$tblchk_tblname] == "inv_sectors"){$count86 = 1; }
    else if($rowt[$tblchk_tblname] == "item_category"){$count87 = 1; }
    else if($rowt[$tblchk_tblname] == "item_closingstock"){$count88 = 1; }
    else if($rowt[$tblchk_tblname] == "item_details"){$count89 = 1; }
    else if($rowt[$tblchk_tblname] == "item_qty_conversion"){$count90 = 1; }
    else if($rowt[$tblchk_tblname] == "item_stocktransfers"){$count91 = 1; }
    else if($rowt[$tblchk_tblname] == "item_units"){$count92 = 1; }
    else if($rowt[$tblchk_tblname] == "location_branch"){$count93 = 1; }
    else if($rowt[$tblchk_tblname] == "location_line"){$count94 = 1; }
    else if($rowt[$tblchk_tblname] == "location_region"){$count95 = 1; }
    else if($rowt[$tblchk_tblname] == "main_access"){$count96 = 1; }
    else if($rowt[$tblchk_tblname] == "main_companyprofile"){$count97 = 1; }
    else if($rowt[$tblchk_tblname] == "main_contactdetails"){$count98 = 1; }
    else if($rowt[$tblchk_tblname] == "main_dailypaperrate"){$count99 = 1; }
    else if($rowt[$tblchk_tblname] == "main_disclaimer"){$count100 = 1; }
    else if($rowt[$tblchk_tblname] == "Tables_in_poulso6_admin_broiler_broilermaster"){$count101 = 1; }
    else if($rowt[$tblchk_tblname] == "main_financialyear"){$count102 = 1; }
    else if($rowt[$tblchk_tblname] == "main_groups"){$count103 = 1; }
    else if($rowt[$tblchk_tblname] == "main_jals"){$count104 = 1; }
    else if($rowt[$tblchk_tblname] == "main_linkdetails"){$count105 = 1; }
    else if($rowt[$tblchk_tblname] == "main_mortality"){$count106 = 1; }
    else if($rowt[$tblchk_tblname] == "main_officetypes"){$count107 = 1; }
    else if($rowt[$tblchk_tblname] == "main_tcds"){$count108 = 1; }
    else if($rowt[$tblchk_tblname] == "main_terms"){$count109 = 1; }
    else if($rowt[$tblchk_tblname] == "master_dashboard_links"){$count110 = 1; }
    else if($rowt[$tblchk_tblname] == "master_farm_checklist"){$count111 = 1; }
    else if($rowt[$tblchk_tblname] == "master_formfields"){$count112 = 1; }
    else if($rowt[$tblchk_tblname] == "master_generator"){$count113 = 1; }
    else if($rowt[$tblchk_tblname] == "master_item_parameter"){$count114 = 1; }
    else if($rowt[$tblchk_tblname] == "master_itemfields"){$count115 = 1; }
    else if($rowt[$tblchk_tblname] == "master_parameters"){$count116 = 1; }
    else if($rowt[$tblchk_tblname] == "master_reportfields"){$count117 = 1; }
    else if($rowt[$tblchk_tblname] == "message_master"){$count118 = 1; }
    else if($rowt[$tblchk_tblname] == "mobile_user_rights"){$count119 = 1; }
    else if($rowt[$tblchk_tblname] == "prefix_master"){$count120 = 1; }
    else if($rowt[$tblchk_tblname] == "price_master"){$count121 = 1; }
    else if($rowt[$tblchk_tblname] == "pur_purchase"){$count122 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_count"){$count123 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_details"){$count124 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_master"){$count125 = 1; }
    else if($rowt[$tblchk_tblname] == "tax_details"){$count126 = 1; }
    else if($rowt[$tblchk_tblname] == "trip_sheet"){$count127 = 1; }
    else if($rowt[$tblchk_tblname] == "upi_types"){$count128 = 1; }
    else{ }
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
			$paisa = " and ".$words[$atemp[1]*10]." paisa only";
		}
		else{
			$paisa = "rupees only";
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
	$comname = $row['cname'];
}
$bank_flag = 0;
if($count115 > 0){
	$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $bank_flag = $row['bank_flag']; }
}
if($count89 > 0){
if($bank_flag == "" || $bank_flag == NULL || $bank_flag == 0 || $bank_flag == "0"){ $bank_flag = 0; }
$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $description[$row['code']] = $row['description']; }
}
$html = "";
$day_ccode = $day_date = array();
$isize = $icount = 0;
$trnum = $_GET['trnum'];

	$icount++;
	if($trnum != ""){
		$id = $trnum; $slno = $iname = $bird = $qty = $price = $amt = array();
		$tot_birds = $tot_qty = $post = $camt = $oinv = $ocdn = $obdramt = $orct = $occn = $obcramt = $ob_mortality = $ob_returns = 0;
		$sql = "SELECT * FROM `broiler_sales` WHERE `trnum` = '$id' AND `active` = '1' AND `dflag` = '0' ";
		$query = mysqli_query($conn,$sql); $c = 0;
		while($row = mysqli_fetch_assoc($query)){
			$c = $c + 1;
			$slno[$c] = $c;
			$iname[$c] = $description[$row['icode']];
			$tot_birds += $bird[$c] = $row['birds'];
			$tot_qty += $qty[$c] = (float)$row['rcd_qty'] / 50; // in bags if divide by 50
			$price[$c] = $row['rate'] * 50; // in Bags if Multiply by 50 else Kgs 
			$amt[$c] = $row['item_tamt'];
			$tcdsper = $row['tcdsper'];
			$tcdsamt = $row['tcdsamt'];
			$roundoff = $row['roundoff'];
			//$famt = $row['finaltotal'];
			$inv = $row['trnum'];
			$ccode = $row['vcode'];
			$odate = $row['date'];
			$amtwds = $row['amtinwords'];
			$billno = $row['billno'];
			$vehicle_code = $row['vehicle_code'];
		}
		$dt = date("d.m.Y",strtotime($odate));
		$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$ccode'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $cname[$row['code']] = $row['name']; if($row['obtype'] == "Cr") { $obcramt = $row['obamt']; $obdramt = 0; } else { $obdramt = $row['obamt']; $obcramt = 0; } }

		$old_inv = "";
		$oinv = $orct = $current_orct = $ocdn = $occn = $ob_mortality = $ob_returns = 0;
		if($count65 > 0){
		$sql = "SELECT trnum,finl_amt FROM `broiler_sales` WHERE `date` < '$odate' AND `vcode` LIKE '$ccode' AND `active` = '1' AND `dflag` = '0' ORDER BY `trnum` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $oinv = $oinv + $row['finl_amt']; $old_inv = $row['trnum']; } }
		}
		if($count63 > 0){
		$sql = "SELECT SUM(amount) as tamt FROM `broiler_receipts` WHERE  `date` <= '$odate' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $orct = $row['tamt']; }
		}
		if($count63 > 0){
		$sql = "SELECT SUM(amount) as tamt FROM `broiler_receipts` WHERE  `date` = '$odate' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $current_orct = $row['tamt']; }
		}
		if($count17 > 0){
		$sql = "SELECT SUM(amount) as tamt,crdr FROM `broiler_crdrnote` WHERE  `date` <= '$odate' AND `vcode` LIKE '$ccode' AND `crdr` IN ('Credit','Debit') AND `type` = 'Customer' AND `active` = '1' GROUP BY `crdr` ORDER BY `crdr` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Debit") { $ocdn = $row['tamt']; } else { $occn = $row['tamt']; } }
		}
		if($count106 > 0){
		$obsql = "SELECT * FROM `main_mortality` WHERE `date` <= '$odate' AND `ccode` = '$ccode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
		$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_mortality = $ob_mortality + $obrow['amount']; }
		}
		if($count54 > 0){
		$obsql = "SELECT * FROM `broiler_itemreturns` WHERE `date` <= '$odate' AND `vcode` = '$ccode' AND `type` = 'customer' AND `active` = '1' AND `dflag` = '0'";
		$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = $ob_returns + $obrow['amount']; }
		}
		$post = $oinv + $ocdn + $obdramt - $orct - $occn - $obcramt - $ob_returns - $ob_mortality;

		$html .= '<table align="center" style="border: 1px solid black;">';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="text-align:center;"><br/><br/><br/>';
		$html .= '<img src="../../'.$img_path.'" height="60px" />';
		$html .= '</th>';
		$html .= '<th colspan="5" style="text-align:center;">';
		$html .= '<p align="left">'.$cdetail.'</p>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b align="left">Date: '.$dt.'</b>';
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:right;">';
		$html .= '<b align="right">DC No: '.$billno.'</b>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		if($cus_remarks != ''){
			$html .= '<b style="padding-left: 10px;text-align:left;">Billing Name: </b>'.$cname[$ccode].'('.$cus_remarks.') <br/>';
		}else{
			$html .= '<b style="padding-left: 10px;text-align:left;">Billing Name: '.$cname[$ccode].'</b><br/>';
		}
		
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:right;">';
		$html .= '<b style="padding-left: 10px;text-align:right;">Vehicle : '.$vehicle_code.'</b><br/>';
		$html .= '</th>';
	
		$html .= '</tr>';
		$html .= '</table>';
		$slnos = $inames =  $birds = $qtys = $prices = $amts = "<br/><br/>";
		for($i = 1;$i <= $c;$i++){
			if($i <$c){
				$slnos = $slnos."".$slno[$i]."<br/><br/>";
				$birds = $birds."".round($bird[$i])."&nbsp;&nbsp;<br/><br/>";
				$inames = $inames."".$iname[$i]."<br/><br/>";
				$qtys = $qtys."".number_format_ind($qty[$i])."&nbsp;&nbsp;<br/><br/>";
				$prices = $prices."".number_format_ind($price[$i])."&nbsp;&nbsp;<br/><br/>";
				$amts = $amts."".number_format_ind($amt[$i])."&nbsp;&nbsp;<br/><br/>";
			}
			else {
				$br = "";
				//$k = 9 - $c;
				for($j = 9;$j >= $c; $j--){
					$br = $br."<br/><br/>";
				}
				$slnos = $slnos."".$slno[$i]."".$br;
				$birds = $birds."".round($bird[$i])."&nbsp;&nbsp;".$br;
				$inames = $inames."".$iname[$i]."".$br;
				$qtys = $qtys."".number_format_ind($qty[$i])."&nbsp;&nbsp;".$br;
				$prices = $prices."".number_format_ind($price[$i])."&nbsp;&nbsp;".$br;
				$amts = $amts."".number_format_ind($amt[$i])."&nbsp;&nbsp;".$br;
			}
            $famt = $famt + $amt[$i];
		}
		$camt = $post + $famt;
		
		$html .= '<table align="center" height="100%" border="1">';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="border: 1px solid black;">Items</th>';
		//$html .= '<th colspan="1" style="border: 1px solid black;">Birds</th>';
		$html .= '<th colspan="1" style="border: 1px solid black;">Kgs</th>';
		$html .= '<th colspan="1" style="border: 1px solid black;">Rate</th>';
		$html .= '<th colspan="2" style="border: 1px solid black;">Amount</th>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td colspan="2" style="padding:5px;text-align:center;">'.$inames.'</td>';
		//	$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$birds.'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$qtys.'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$prices.'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="2" style="padding:5px;text-align:right;">'.$amts.'&nbsp;&nbsp;</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<th colspan="2"><br/>Invoice Total</th>';
			//$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/>'.round($tot_birds).'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/>'.number_format_ind($tot_qty).'&nbsp;&nbsp;</td>';
			$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/></td>';
			$html .= '<td colspan="2" style="padding:5px;text-align:right;"><br/>'.number_format_ind($famt).'&nbsp;&nbsp;</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td colspan="2"><br/><br/><b>Amount in words:</b> '.ucfirst(strtolower(convert_number_to_words($famt))).'.<br/></td>';
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
				$html .= '<td colspan="2" style="text-align:left;font-size:8px;"><br/><br/>';
					$html .= '&nbsp;<b>Bank Name : </b>'.$bank_name.'<br/>';
					$html .= '&nbsp;<b>Branch : </b>'.$bank_branch.'<br/>';
					$html .= '&nbsp;<b>IFSC Code: </b>'.$bank_ifsc.'<br/>';
				$html .= '</td>';
				$html .= '<td colspan="4" style="text-align:left;font-size:8px;"><br/><br/>';
					$html .= '&nbsp;<b>Acc. Holder name : </b>'.$bank_accname.'<br/>';
					$html .= '&nbsp;<b>Account Number : </b>'.$bank_accno.'<br/>';
					if($upi_details != "" || $upi_details != NULL){
					$html .= '&nbsp;<b>'.$upi_details.': </b>'.$upi_mobile.'<br/>';
					}
				$html .= '</td>';
			$html .= '</tr>';
		}

		$html .= '<tr>';
		$html .= '<td colspan="6" style="text-align:right;font-size:10px;"><br/><br/><b>'.$comname.'&ensp;&ensp;</b></td>';
		$html .= '</tr>';
		$html .= '</table>';
		//if($icount != $isize){ $html .= '<div style="page-break-before:always"></div>'; }
	}

//echo $html;

require_once('tcpdf_include.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Sales Invoice');
$pdf->SetSubject('Sales Invoice');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 10, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
//$pdf->SetMargins(7, 7, 7, true);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);



$pdf->Output($id.'.pdf', 'I');

?>