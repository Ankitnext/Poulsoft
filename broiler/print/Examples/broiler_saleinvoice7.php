<?php
//broiler_saleinvoice5.php
require_once('tcpdf_include.php');
include "../../config.php";
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$sql='SHOW COLUMNS FROM `main_companyprofile`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("corp_addr", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_companyprofile` ADD `corp_addr` VARCHAR(1500) NULL NULL DEFAULT NULL COMMENT 'Corporate Address' AFTER `logopath`"; mysqli_query($conn,$sql); }


$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $corp_addr = $row['corp_addr']; $for_cname = $row['fullcname']; }
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
    $added_date = $row['addedtime'];
    $pono = $row['sale_pono'];
    $ponodate = $row['sale_podate'];
    $fmake_date[$i] = $row['fmake_date'];
    $fexp_date[$i] = $row['fexp_date'];
    $i++;
}

$vendors =  $vcode[0];
$fdate = date('Y-m-d');
$old_inv = "";
$opening_sales = $opening_receipts = $opening_ccn = $opening_cdn = $opening_cntcr = $opening_cntdr = $opening_returns = $rb_amt = 0;
if ($count65 > 0) {
    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` <= '$fdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
    $query = mysqli_query($conn, $sql_record);
    $transaction_count = 0;
    if (!empty($query)) {
        $transaction_count = mysqli_num_rows($query);
    }
    if ($transaction_count > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            if ($old_inv != $row['trnum']) {
                $opening_sales = $opening_sales + $row['finl_amt'];
                $old_inv = $row['trnum'];
            }
        }
    } else {
        $opening_sales = 0;
    }
}
if ($count63 > 0) {
    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` <= '$fdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
    $query = mysqli_query($conn, $sql_record);
    $transaction_count = 0;
    if (!empty($query)) {
        $transaction_count = mysqli_num_rows($query);
    }
    if ($transaction_count > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $opening_receipts = $opening_receipts + $row['amount'];
        }
    } else {
        $opening_receipts = 0;
    }
}
if ($count54 > 0) {
    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` <= '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
    $query = mysqli_query($conn, $sql_record);
    $transaction_count = 0;
    if (!empty($query)) {
        $transaction_count = mysqli_num_rows($query);
    }
    if ($transaction_count > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $opening_returns = $opening_returns + $row['amount'];
        }
    } else {
        $opening_returns = 0;
    }
}
if ($count17 > 0) {
    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` <= '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
    $query = mysqli_query($conn, $sql_record);
    $transaction_count = 0;
    if (!empty($query)) {
        $transaction_count = mysqli_num_rows($query);
    }
    if ($transaction_count > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            if ($row['crdr'] == "Credit") {
                $opening_ccn = $opening_ccn + $row['amount'];
            } else {
                $opening_cdn = $opening_cdn + $row['amount'];
            }
        }
    } else {
        $opening_ccn = $opening_cdn = 0;
    }
}
if ($count7 > 0) {
    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$fdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
    $query = mysqli_query($conn, $sql_record);
    $transaction_count = 0;
    if (!empty($query)) {
        $transaction_count = mysqli_num_rows($query);
    }
    if ($transaction_count > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $opening_cntcr = $opening_cntcr + $row['amount'];
        }
    } else {
        $opening_cntcr = 0;
    }

    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$fdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
    $query = mysqli_query($conn, $sql_record);
    $transaction_count = 0;
    if (!empty($query)) {
        $transaction_count = mysqli_num_rows($query);
    }
    if ($transaction_count > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $opening_cntdr = $opening_cntdr + $row['amount'];
        }
    } else {
        $opening_cntdr = 0;
    }
}
$ob_cramt = $ob_cramt = 0;
if ($obtype[$vendors] == "Cr") {
    $ob_cramt = $obamt[$vendors];
    $ob_dramt = 0;
} else {
    $ob_dramt = $obamt[$vendors];
    $ob_cramt = 0;
}

$ob_rcv = $opening_sales + $opening_cdn + $opening_cntdr + $ob_dramt;
$ob_pid = $opening_receipts + $opening_returns + $opening_ccn + $opening_cntcr + $ob_cramt;

$ob_bal = $ob_rcv - $ob_pid;

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

$t_header = $l_header =  $v_header =  $d_header =  $sgst_footer =  $cgst_footer =  $tcs_footer =  $frt_footer =  $ft_footer = $roff_footer = $aob_footer = $tax_footer = 
$tcn_footer = $bank_footer = $sign_footer = $t_footer = $fit_footer = '';
$t_header .= '<table style="border:1px solid black;">';
$l_header .= '<tr style="line-height: 2.1;">';
$l_header .= '<th rowspan="3" style="width:265px;text-align:center;border-right:1px sold black;"><br/><br/><img src="../../'.$img_path.'" height="80px" /></th>';
//$header .= '<th rowspan="3" style="width:65px;text-align:left;border-right:1px sold black;line-height: 1.1;"><br/>'.$cdetail.'</th>';
$l_header .= '<th rowspan="1" style="width:152px;text-align:left;border-right:1px sold black;"><br/><b>Invoice No: </b><br/>'.$billno[0].'</th>';
$l_header .= '<th rowspan="1" style="width:145px;text-align:left;"><b>Po No:</b><br/>'.$pono.'</th>'; //<br/>Original&nbsp;&nbsp; -Buyer<br/>Duplicate -transpoter<br/>Triplicate -Supplier
$l_header .= '</tr>';

$l_header .= '<tr style="line-height: 2.1;">';
$l_header .= '<th style="width:152px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Date: </b>'.$date[0].' '.date("h:i A",strtotime($added_date)).'<br/></th>';
$l_header .= '<th style="width:145px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Po Date:</b><br/>'.$ponodate.'</th>'; //<b>Eway Bill No: </b>'.$billno[0].'
$l_header .= '</tr>';

$l_header .= '<tr style="line-height: 2.1;">';
$l_header .= '<th style="width:152px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Vehicle No: </b>'.$vehicle_code[0].'<br/></th>';
$l_header .= '<th style="width:145px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Delivery challan: </b>'.$trnums[0].'<br/></th>';
$l_header .= '</tr>';

$v_header .= '<tr>';
$v_header .= '<th style="width:265px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/>
<b>Consignee:</b> <br/> '.$ven_name.'<br/>
<b>Address: </b>'.$ven_baddress.'<br/>
<b>Contact Person: </b><br/>
<b>Phone No: </b>'.$ven_mobile.'<br/>
<b>GSTIN: </b>'.$ven_gstin.'<br/><br/>
<b>Supply State: </b>'.$state_name.'&ensp;&ensp;<b>State Code: </b>
</th>';
$v_header .= '<th style="width:297px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/>
<b>Shipped To: </b>'.$ven_saddress.'<br/><br/><br/><br/><br/><br/>
<b>Supply State: </b>'.$state_name.'&ensp;&ensp;<b>State Code: </b>
</th>';
$v_header .= '</tr>';


$d_header .= '<tr style="line-height: 0.6;">';
$d_header .= '<th style="width:27px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Sl. No.</b><br/></th>';
//$d_header .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Item Code</b><br/></th>';
$d_header .= '<th style="width:100px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Description</b><br/></th>';

$d_header .= '<th style="width:53px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Mfg Date</b><br/></th>';
$d_header .= '<th style="width:53px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Exp Date</b><br/></th>';


$d_header .= '<th style="width:50px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>HSN</b><br/></th>';
$d_header .= '<th style="width:40px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>UOM</b><br/></th>';
$d_header .= '<th style="width:50px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Qty</b><br/></th>';
//$d_header .= '<th style="width:44px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Free</b><br/></th>';
$d_header .= '<th style="width:43px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Price</b><br/></th>';
$d_header .= '<th style="width:39px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>GST %</b><br/></th>';
$d_header .= '<th style="width:47px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Discount</b><br/></th>';
$d_header .= '<th style="width:60px;text-align:center;border-top:1px sold black;border-right:1px sold black;border-bottom:1px sold black;"><br/><br/><b>Amount</b><br/></th>';
$d_header .= '</tr>';

$dsize = sizeof($icode); $hsn_list = $gst_per_list = $tax_amt_list = $cgst_amt_list = $sgst_amt_list = $igst_amt_list = "";
for($i = 0; $i < $dsize; $i++){
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
$fit_footer .= '<tr style="line-height: 0.6;">';
$fit_footer .= '<th style="width:323px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Final Total</b><br/></th>';
$fit_footer .= '<th style="width:50px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_rqty).'</b></th>';
//$fit_footer .= '<th style="width:44px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_fqty).'</b></th>';
$fit_footer .= '<th style="width:43px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_price).'</b></th>';
$fit_footer .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><br/></th>';
$fit_footer .= '<th style="width:47px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_damt).'</b></th>';
$fit_footer .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($tot_tamt).'</b></th>';
$fit_footer .= '</tr>';

$frt_footer .= '<tr style="line-height: 0.6;">';
$frt_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Freight</b><br/></th>';
$frt_footer .= '<th style="width:119px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($freight_amt[0]).'<br/></th>';
$frt_footer .= '</tr>';

$tcs_footer .= '<tr style="line-height: 0.6;">';
$tcs_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>TCS Amount</b><br/></th>';
$tcs_footer .= '<th style="width:119px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($tcds_amt[0]).'<br/></th>';
$tcs_footer .= '</tr>';

$cgst_footer .= '<tr style="line-height: 0.6;">';
$cgst_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>CGST</b><br/></th>';
$cgst_footer .= '<th style="width:119px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind(round(($gst_amt[0] / 2),2)).'<br/></th>';
$cgst_footer .= '</tr>';

$sgst_footer .= '<tr style="line-height: 0.6;">';
$sgst_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>SGST</b><br/></th>';
$sgst_footer .= '<th style="width:119px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind(round(($gst_amt[0] / 2),2)).'<br/></th>';
$sgst_footer .= '</tr>';

$roff_footer .= '<tr style="line-height: 0.6;">';
if($round_off[0] >= 0){
    $roff_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Round Off(add)</b><br/></th>';
}
else{
    $roff_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Round Off(deduct)</b><br/></th>';
}
$roff_footer .= '<th style="width:119px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($round_off[0]).'<br/></th>';
$roff_footer .= '</tr>';

$ft_footer .= '<tr style="line-height: 0.6;">';
$ft_footer .= '<th style="width:443px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Total</b><br/></th>';
$ft_footer .= '<th style="width:119px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($finl_amt[0]).'</b><br/></th>';
$ft_footer .= '</tr>';

$op_bal = 0;
if((float)$finl_amt[0] < (float)$ob_bal){
    $op_bal = (float)$ob_bal - (float)$finl_amt[0];
}
$aob_footer .= '<tr style="line-height: 1.2;">';
$aob_footer .= '<th style="width:362px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Rs.(in Words): '.convert_number_to_words($finl_amt[0]).'</b><br/></th>';
$aob_footer .= '<th style="width:200px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b></b><br/></th>';
$aob_footer .= '</tr>';

$tax_footer .= '<tr style="line-height: 0.5;">';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>HSN</b><br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>GST %</b><br/></th>';
$tax_footer .= '<th style="width:97px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Taxable Amount</b><br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>CGST</b><br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>SGST</b><br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>IGST</b><br/></th>';
$tax_footer .= '</tr>';

$tax_footer .= '<tr style="line-height: 0.9;">';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$hsn_list.'<br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$gst_per_list.'<br/></th>';
$tax_footer .= '<th style="width:97px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$tax_amt_list.'<br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$cgst_amt_list.'<br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$sgst_amt_list.'<br/></th>';
$tax_footer .= '<th style="width:93px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/>'.$igst_amt_list.'<br/></th>';
$tax_footer .= '</tr>';

$tcn_footer .= '<tr style="line-height: 1.1;">';
$tcn_footer .= '<th style="width:350px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Terms & Conditions: </b><div style="font-size:9px;">'.$note.'</div><br/><br/></th>';
$tcn_footer .= '<th style="width:212px;text-align:left;border-top:1px sold black;border-right:1px sold black;"><br/><b>Narration: </b><br/>'.$remarks[0].'<br/><br/><br/><br/></th>';
$tcn_footer .= '</tr>';

if($bank_flag == 1){
    $bank_footer .= '<tr style="line-height: 1.5;">';
        $bank_footer .= '<td style="width:562px;text-align:left;border-top:1px sold black;border-right:1px sold black;">';
        $bank_footer .= '&nbsp;<b>Acc. Holder name : '.$bank_accname.'</b><br/>';
        $bank_footer .= '&nbsp;<b>Account Number : '.$bank_accno.'</b><br/>';
            $bank_footer .= '&nbsp;<b>Bank Name : '.$bank_name.'</b><br/>';
            $bank_footer .= '&nbsp;<b>Branch : '.$bank_branch.'</b><br/>';
            $bank_footer .= '&nbsp;<b>IFSC Code: '.$bank_ifsc.'</b><br/>';
            if($upi_details != "" || $upi_details != NULL){
            $bank_footer .= '&nbsp;<b>'.$upi_details.': '.$upi_mobile.'</b><br/>';
            }
        $bank_footer .= '</td>';
    $bank_footer .= '</tr>';
}

$sign_footer .= '<tr style="line-height: 0.7;">';
$sign_footer .= '<th style="width:350px;text-align:left;border-top:1px sold black;"><br/><br/><b>Received Signature: </b><div style="font-size:10px;"></div><br/><br/></th>';
$sign_footer .= '<th style="width:212px;text-align:left;border-top:1px sold black;border-right:0px sold black;"><br/><br/><b>Authorized Signature: </b><br/><br/><br/><br/><br/></th>';
$sign_footer .= '</tr>';
$sign_footer .= '<tr style="line-height: 1;">';
//$sign_footer .= '<th style="width:162px;text-align:left;"><br/><br/><br/>CC-Chilled Chicken<br/>FC-Frozen Chicken</th>';
//$sign_footer .= '<th style="width:400px;text-align:right;"><br/><br/><br/><b>FOR '.$for_cname.'</b></th>';
$sign_footer .= '</tr>';
$t_footer .= '</table>';

$dsize = sizeof($icode); $html = ''; $icount = $h_cnt = $b_cnt = $t_rqty = $t_dqty = $t_iqty = $t_prc = 0;
//$minrows = 5;
for($i = 0; $i < max($dsize,15); $i++){
    $j = $i + 1;
    $icount++;

    if($icount == 1 && $h_cnt == 0){
        $html .= $t_header."".$l_header."".$v_header."".$d_header;
    }
    else if($icount == 1){
        $html .= $t_header."".$d_header;
    }
    $h_cnt++;
    if($i < $dsize){
    $html .= '<tr style="line-height: 1.0;">';
    $html .= '<th style="width:27px;text-align:center;border-right:1px sold black;"><br/><br/>'.$j.'<br/></th>';
    //$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.$item_code[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:100px;text-align:center;border-right:1px sold black;"><br/><br/>'.$item_name[$icode[$i]].'<br/></th>';
//date("d.m.Y",strtotime($row['date']))
    $html .= '<th style="width:53px;text-align:center;border-right:1px sold black;"><br/><br/>'.date("d.m.Y",strtotime($fmake_date[$i])).'<br/></th>';
    $html .= '<th style="width:53px;text-align:center;border-right:1px sold black;"><br/><br/>'.date("d.m.Y",strtotime($fexp_date[$i])).'<br/></th>';

    $html .= '<th style="width:50px;text-align:center;border-right:1px sold black;"><br/><br/>'.$item_hsn[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:40px;text-align:center;border-right:1px sold black;"><br/><br/>'.$item_uom[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:50px;text-align:right;border-right:1px sold black;"><br/><br/>'.number_format_ind($rcd_qty[$i]).'<br/></th>';
    //$html .= '<th style="width:44px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($fre_qty[$i]).'<br/></th>';
    $html .= '<th style="width:43px;text-align:right;border-right:1px sold black;"><br/><br/>'.number_format_ind($rate[$i]).'<br/></th>';
    $html .= '<th style="width:39px;text-align:right;border-right:1px sold black;"><br/><br/>'.number_format_ind($gst_per[$i]).'<br/></th>';
    $html .= '<th style="width:47px;text-align:right;border-right:1px sold black;"><br/><br/>'.number_format_ind($dis_amt[$i]).'<br/></th>';
    $html .= '<th style="width:60px;text-align:right;border-right:1px sold black;"><br/><br/>'.number_format_ind($item_tamt[$i]).'<br/></th>';
    $html .= '</tr>';
    }
    else{
         $html .= '<tr style="line-height: 0.4;">';
    $html .= '<th style="width:27px;text-align:center;border-right:1px sold black"><br/><br/><br/></th>';
    //$html .= '<th style="width:55px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.$item_code[$icode[$i]].'<br/></th>';
    $html .= '<th style="width:100px;text-align:center;border-right:1px sold black"><br/><br/></th>';

    $html .= '<th style="width:53px;text-align:center;border-right:1px sold black;"><br/><br/><br/></th>';
    $html .= '<th style="width:53px;text-align:center;border-right:1px sold black;"><br/><br/><br/></th>';

    $html .= '<th style="width:50px;text-align:center;border-right:1px sold black"></th>';
    $html .= '<th style="width:40px;text-align:center;border-right:1px sold black"></th>';
    $html .= '<th style="width:50px;text-align:right;border-right:1px sold black"></th>';
    //$html .= '<th style="width:44px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/>'.number_format_ind($fre_qty[$i]).'<br/></th>';
    $html .= '<th style="width:43px;text-align:right;border-right:1px sold black"></th>';
    $html .= '<th style="width:39px;text-align:right;border-right:1px sold black"></th>';
    $html .= '<th style="width:47px;text-align:right;border-right:1px sold black"></th>';
    $html .= '<th style="width:60px;text-align:right;border-right:1px sold black"></th>';
    $html .= '</tr>';

   }
   
    if($i < $dsize){
    $t_rqty += (float)$rcd_qty[$i];
    $t_dqty += (float)$dis_amt[$i];
    $t_iqty += (float)$item_tamt[$i];
    }
    if($icount == 15 && $j < max($dsize,15)){
        $t_prc = 0; if((float)$t_rqty != 0){ $t_prc = (float)$t_iqty / (float)$t_rqty; }
        $html .= '<tr style="line-height: 0.6;">';
        $html .= '<th style="width:269px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Page Total</b><br/></th>';
        $html .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_rqty).'</b></th>';
        $html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_prc).'</b></th>';
        $html .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><br/></th>';
        $html .= '<th style="width:55px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_dqty).'</b></th>';
        $html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_iqty).'</b></th>';
        $html .= '</tr>';
        $t_rqty = $t_dqty = $t_iqty = $t_prc = 0;
        
        if($j == max($dsize,15)){
            $html .= $fit_footer."".$frt_footer."".$tcs_footer."".$cgst_footer."".$sgst_footer."".$roff_footer."".$ft_footer."".$aob_footer."".$tax_footer."".$tcn_footer."".$bank_footer."".$sign_footer."".$t_footer;
        }
        else{
            $html .= $frt_footer."".$tcs_footer."".$cgst_footer."".$sgst_footer."".$roff_footer."".$ft_footer."".$aob_footer."".$tax_footer."".$tcn_footer."".$bank_footer."".$sign_footer."".$t_footer;
        }
        // if($icount < max($dsize,15)){
        //     $html .= '<div style="page-break-before:always"></div>';
        //     $b_cnt = 1;
        // }
       // $icount = 0;
    }
    else if($j == max($dsize,15)){
        // if($b_cnt == 1){
        //     $t_prc = 0; if((float)$t_rqty != 0){ $t_prc = (float)$t_iqty / (float)$t_rqty; }
        //     $html .= '<tr style="line-height: 0.6;">';
        //     $html .= '<th style="width:269px;text-align:center;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>Page Total</b><br/></th>';
        //     $html .= '<th style="width:60px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_rqty).'</b></th>';
        //     $html .= '<th style="width:49px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_prc).'</b></th>';
        //     $html .= '<th style="width:39px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><br/></th>';
        //     $html .= '<th style="width:55px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_dqty).'</b></th>';
        //     $html .= '<th style="width:90px;text-align:right;border-top:1px sold black;border-right:1px sold black;"><br/><br/><b>'.number_format_ind($t_iqty).'</b></th>';
        //     $html .= '</tr>';
        //     $t_rqty = $t_dqty = $t_iqty = $t_prc = 0;
        // }
        $html .= $fit_footer."".$frt_footer."".$tcs_footer."".$cgst_footer."".$sgst_footer."".$roff_footer."".$ft_footer."".$aob_footer."".$tax_footer."".$tcn_footer."".$bank_footer."".$sign_footer."".$t_footer;
    }
}



//echo $html;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    /*//Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    */

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('dejavusans', '', 8, '', true);
        // Page number
        $this->writeHTML('<p><strong>Corporate Address:</strong> Peddarangapuram,
Sy No. 56 & 30,
Rachumarripalle, Pulivendula
YSR Kadapa Dist.
AndhraPradesh - 516391.
GSTIN: 37AAECN8917E1ZA <br /> </p>', false, false, false, false, 'C');
    }
}
//echo $html;

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set header and footer fonts
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetFont('dejavusans', '', 8, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetMargins(5, 5, 10, true);
//$pdf->setCellHeightRatio(1.5);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>