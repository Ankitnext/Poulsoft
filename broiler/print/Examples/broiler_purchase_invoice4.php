<?php
//broiler_saleinvoice.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;


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

$t1 = array(); $t1 = explode("@", $_GET['trnum']);
$atrnum = $t1[0];
$sector = $_GET['sector'];

$trnum = $link_trnum = $date = $vcode = $billno = $icode = $birds = $snt_qty = $mort_qty = $cull_qty = $rcd_qty = $fre_qty = $rate = $dis_per = $dis_amt = 
$gst_per = $gst_amt = $tcds_per = $tcds_amt = $item_tamt = $freight_type = $freight_amt = $freight_pay_type = $freight_pay_acc = $freight_acc = $round_off = $finl_amt = 
$bal_qty = $bal_amt = $avg_price = $avg_wt = $avg_item_amount = $avg_final_amount = $profit = $remarks = $warehouse = $farm_batch = $supervisor_code = $bag_code = $bag_count = 
$batch_no = $exp_date = $vehicle_code = $driver_code = array();
$sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$atrnum' AND `active` = '1' AND `warehouse` = '$sector' AND `dflag` = '0'";
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
    $item_tamt[$i] = (float)$row['item_tamt'] - (float)$row['gst_amt'];
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


$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' OR `type` = 'all'"; $query = mysqli_query($conn,$sql);
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
if($bank_flag == "" || $bank_flag == NULL || $bank_flag == 0 || $bank_flag == "0"){ $bank_flag = 0; }

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

$html = '';
$html .= '<table style="border:1px solid black;">';
$html .= '<tr style="line-height: 2.1;">';
$html .= '<th rowspan="3" style="width:65px;text-align:center;"><br/><br/><img src="../../'.$img_path.'" height="50px" /></th>';
$html .= '<th rowspan="3" style="width:200px;text-align:left;border-right:1px sold black;line-height: 1.1;"><br/>'.$cdetail.'</th>';
$html .= '<th rowspan="1" style="width:152px;text-align:left;border-right:1px sold black;"><br/><b>Invoice No: </b><br/>'.$trnum[0].'</th>';
$html .= '<th rowspan="1" style="width:145px;text-align:left;"></th>'; //<br/>Original&nbsp;&nbsp; -Buyer<br/>Duplicate -transpoter<br/>Triplicate -Supplier
$html .= '</tr>';

$html .= '<tr style="line-height: 2.1;">';
$html .= '<th style="width:152px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Date: </b>'.$date[0].'<br/></th>';
$html .= '<th style="width:145px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><br/></th>'; //<b>Eway Bill No: </b>'.$billno[0].'
$html .= '</tr>';

$html .= '<tr style="line-height: 2.1;">';
$html .= '<th style="width:152px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Vehicle No: </b>'.$vehicle_code[0].'<br/></th>';
$html .= '<th style="width:145px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Delivery challan: </b>'.$trnums[0].'<br/></th>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:265px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/>
<b>consignee: '.$ven_name.'</b><br/>
<b>Address: </b>'.$ven_baddress.'<br/>
<b>Contact Person: </b><br/>
<b>Phone No: </b>'.$ven_mobile.'<br/>
<b>GSTIN: </b>'.$ven_gstin.'<br/><br/>
<b>Supply State: </b>'.$state_name.'&ensp;&ensp;<b>State Code: </b>
</th>';
$html .= '<th style="width:297px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/>
<b>Shipping Address: </b>'.$ven_saddress.'<br/><br/><br/><br/><br/><br/>
<b>Supply State: </b>'.$state_name.'&ensp;&ensp;<b>State Code: </b>
</th>';
$html .= '</tr>';


$html .= '<tr style="line-height: 2.1;">';
$html .= '<th style="width:37px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Sl. No.</b><br/></th>';
//$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Item Code</b><br/></th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Description</b><br/></th>';
$html .= '<th style="width:59px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>HSN</b><br/></th>';
$html .= '<th style="width:49px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>UOM</b><br/></th>';
$html .= '<th style="width:60px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Qty</b><br/></th>';
$html .= '<th style="width:44px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Free</b><br/></th>';
$html .= '<th style="width:49px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Price</b><br/></th>';
$html .= '<th style="width:39px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>GST %</b><br/></th>';
$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Discount</b><br/></th>';
$html .= '<th style="width:90px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Amount</b><br/></th>';
$html .= '</tr>';

$dsize = sizeof($icode); $hsn_list = $gst_per_list = $tax_amt_list = $cgst_amt_list = $sgst_amt_list = $igst_amt_list = "";
for($i = 0; $i < $dsize; $i++){
    $j = $i + 1;
    $html .= '<tr style="line-height: 2.1;">';
    $html .= '<th style="width:37px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$j.'<br/></th>';
    //$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$item_code[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:80px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$item_name[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:59px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$item_hsn[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:49px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$item_uom[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/>'.number_format_ind($rcd_qty[$i]).'<br/></th>';
    $html .= '<th style="width:44px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/>'.number_format_ind($fre_qty[$i]).'<br/></th>';
    $html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/>'.number_format_ind($rate[$i]).'<br/></th>';
    $html .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/>'.number_format_ind($gst_per[$i]).'<br/></th>';
    $html .= '<th style="width:55px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/>'.number_format_ind($dis_amt[$i]).'<br/></th>';
    $html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/>'.number_format_ind($item_tamt[$i]).'<br/></th>';
    $html .= '</tr>';

    $tot_rqty += (float)$rcd_qty[$i];
    $tot_fqty += (float)$fre_qty[$i];
    $tot_damt += (float)$dis_amt[$i];
    $tot_tamt += (float)$item_tamt[$i];

    if(!empty($item_hsn[$icode[$i]]) && $item_hsn[$icode[$i]] != "" && number_format_ind($gst_per[$i]) != "0.00"){
        $hsn_list .= "<br/>".$item_hsn[$icode[$i]];
        $gst_per_list .= "<br/>".number_format_ind($gst_per[$i]);
        $tax_amt_list .= "<br/>".number_format_ind(round(($rcd_qty[$i] * $rate[$i]),2));
        $cgst_amt_list .= "<br/>".number_format_ind(round(($gst_amt[$i] / 2),2));
        $sgst_amt_list .= "<br/>".number_format_ind(round(($gst_amt[$i] / 2),2));
        $igst_amt_list .= "<br/>";
    }
}
if($tot_rqty != "" || $tot_rqty != 0){$tot_price = (float)$tot_tamt / (float)$tot_rqty;} else { $tot_rqty = 0;}
$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:225px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Total</b><br/></th>';
$html .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_rqty).'</b></th>';
$html .= '<th style="width:44px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_fqty).'</b></th>';
$html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_price).'</b></th>';
$html .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><br/></th>';
$html .= '<th style="width:55px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_damt).'</b></th>';
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_tamt).'</b></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Freight</b><br/></th>';
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($freight_amt[0]).'<br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>TCS Amount</b><br/></th>';
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($tcds_amt[0]).'<br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>CGST</b><br/></th>';
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind(round(($gst_amt[0] / 2),2)).'<br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>SGST</b><br/></th>';
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind(round(($gst_amt[0] / 2),2)).'<br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
if($round_off[0] >= 0){
    $html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Round Off(add)</b><br/></th>';
}
else{
    $html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Round Off(deduct)</b><br/></th>';
}
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($round_off[0]).'<br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:472px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Total</b><br/></th>';
$html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($finl_amt[0]).'</b><br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 0.8;">';
$html .= '<th style="width:562px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Rs.(in Words): '.convert_number_to_words($finl_amt[0]).'</b><br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 1.5;">';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>HSN</b><br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>GST %</b><br/></th>';
$html .= '<th style="width:97px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>Taxable Amount</b><br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>CGST</b><br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>SGST</b><br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><b>IGST</b><br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 1.5;">';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$hsn_list.'<br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$gst_per_list.'<br/></th>';
$html .= '<th style="width:97px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$tax_amt_list.'<br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$cgst_amt_list.'<br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$sgst_amt_list.'<br/></th>';
$html .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$igst_amt_list.'<br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 1.5;">';
$html .= '<th style="width:350px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Terms & Conditions: </b><div style="font-size:9px;">'.$note.'</div><br/><br/></th>';
$html .= '<th style="width:212px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Narration: </b><br/>'.$remarks[0].'<br/><br/><br/><br/></th>';
$html .= '</tr>';

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
}

$html .= '<tr style="line-height: 1;">';
$html .= '<th style="width:350px;text-align:left;border-top:1px sold black;"><br/><b>Received Signature: </b><div style="font-size:10px;"></div><br/><br/></th>';
$html .= '<th style="width:212px;text-align:left;border-top:1px sold black;border-right:0px sold black;"><br/><b>Authorized Signature: </b><br/><br/><br/><br/><br/></th>';
$html .= '</tr>';

/*if($bank_flag == 1){
    $html .= '<tr>';
        $html .= '<td colspan="4" style="text-align:left;">';
            $html .= '&nbsp;<b>Bank Name : </b>'.$bank_name.'<br/>';
            $html .= '&nbsp;<b>Branch : </b>'.$bank_branch.'<br/>';
            $html .= '&nbsp;<b>IFSC Code: </b>'.$bank_ifsc.'<br/>';
        $html .= '</td>';
        $html .= '<td colspan="6" style="text-align:left;">';
            $html .= '&nbsp;<b>Acc. Holder name : </b>'.$bank_accname.'<br/>';
            $html .= '&nbsp;<b>Account Number : </b>'.$bank_accno.'<br/>';
            if($upi_details != "" || $upi_details != NULL){
            $html .= '&nbsp;<b>'.$upi_details.': </b>'.$upi_mobile.'<br/>';
            }
        $html .= '</td>';
    $html .= '</tr>';
}
if($disclaimer != ""){
    $html .= '<tr>';
    $html .= '<td colspan="10" style="padding:10px;text-align:left;font-size:8px;"><br/><br/>&nbsp;'.$disclaimer.'&ensp;&ensp;</td>';
    $html .= '</tr>';
}
$html .= '<tr>';
$html .= '<td colspan="10" style="padding:10px;text-align:right;font-size:11px;"><br/><br/><br/><br/>&nbsp;Authorised Signature&ensp;&ensp;</td>';
$html .= '</tr>';*/

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
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>