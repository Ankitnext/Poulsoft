<?php
//mgmtrearingchargeprint_paras.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;


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
$trnum = $_GET['trnum'];
$sql = "SELECT * FROM `broiler_rearingcharge` WHERE `trnum` = '$trnum'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $date = date("d.m.Y",strtotime($row['date']));
    $branch_code = $row['branch_code'];
    $line_code = $row['line_code'];
    $farm_code = $row['farm_code'];
    $batch_code = $row['batch_code'];
    $start_date = date("d.m.Y",strtotime($row['start_date']));
    $placed_birds = $row['placed_birds'];
    //$mortality = $row['mortality'];
    $sold_birds = $row['sold_birds'];
    $sold_weight = $row['sold_weight'];
    $excess = $row['excess'];
    $shortage = $row['shortage'];
    $liquid_date = date("d.m.Y",strtotime($row['liquid_date']));
    $sale_amount = $row['sale_amount'];
    $sale_rate = $row['sale_rate'];
    $age = $row['age'];
    $high_chickin_secvcode = $row['high_chickin_secvcode'];
    $high_feedin_brand_code = $row['high_feedin_brand_code'];

    $days7_mort = $row['days7_mort'];
    $days7_mort_count = $row['days7_mort_count'];
    $days30_mort = $row['days30_mort'];
    $days30_mort_count = $row['days30_mort_count'];
    $daysge31_mort = $row['daysge31_mort'];
    $days31_mort_count = $row['days31_mort_count'];
    $total_mort = $row['total_mort'];
    $fcr = $row['fcr'];
    $cfcr = $row['cfcr'];
    $avg_wt = $row['avg_wt'];
    $mean_age = $row['mean_age'];
    $day_gain = $row['day_gain'];
    $eef = $row['eef'];
    $grade = $row['grade'];
    $feed_in_kgs = $row['feed_in_kgs'];
    $feed_consume_kgs = $row['feed_consume_kgs'];
    $feed_out_kgs = $row['feed_out_kgs'];
    $feed_bal_kgs = $row['feed_bal_kgs'];
    $feed_in_bag = $row['feed_in_bag'];
    $feed_consume_bag = $row['feed_consume_bag'];
    $feed_out_bag = $row['feed_out_bag'];
    $feed_bal_bag = $row['feed_bal_bag'];
    $transfer_in = $row['transfer_in'];
    $consumption = $row['consumption'];
    $transfer_out = $row['transfer_out'];
    $closing = $row['closing'];
    $chick_cost_amt = $row['chick_cost_amt'];
    $chick_cost_unit = $row['chick_cost_unit'];
    $actual_chick_cost = $row['actual_chick_cost'];
    $feed_cost_amt = $row['feed_cost_amt'];
    $feed_cost_unit = $row['feed_cost_unit'];
    $actual_feed_cost = $row['actual_feed_cost'];
    $admin_cost_amt = $row['admin_cost_amt'];
    $admin_cost_unit = $row['admin_cost_unit'];
    $medicine_cost_amt = $row['medicine_cost_amt'];
    $medicine_cost_unit = $row['medicine_cost_unit'];
    $actual_medicine_cost = $row['actual_medicine_cost'];
    $total_cost_amt = $row['total_cost_amt'];
    $total_cost_unit = $row['total_cost_unit'];
    $actual_prod_cost = $row['actual_prod_cost'];
    $standard_gc_prc = $row['standard_gc_prc'];
    $standard_gc_amt = $row['standard_gc_amt'];
    $grow_charge_exp_prc = $row['grow_charge_exp_prc'];
    $grow_charge_exp_amt = $row['grow_charge_exp_amt'];
    $sales_incentive_prc = $row['sales_incentive_prc'];
    $sales_incentive_amt = $row['sales_incentive_amt'];
    $total_gc_prc = $row['total_gc_prc'];
    $total_gc_amt = $row['total_gc_amt'];
    $mortality_incentive_prc = $row['mortality_incentive_prc'];
    $mortality_incentive_amt = $row['mortality_incentive_amt'];
    $summer_incentive_prc = $row['summer_incentive_prc'];
    $summer_incentive_amt = $row['summer_incentive_amt'];
    $other_incentive = $row['other_incentive'];
    $unloading_charges = $row['unloading_charges'];
    $total_incentives = $row['total_incentives'];
    $birds_shortage = $row['birds_shortage'];
    $fcr_deduction = $row['fcr_deduction'];
    $mortality_deduction = $row['mortality_deduction'];
    $total_deduction = $row['total_deduction'];
    $amount_payable = $row['amount_payable'];
    $farmer_sale_deduction = $row['farmer_sale_deduction'];
    $feed_transfer_charges = $row['feed_transfer_charges'];
    $vaccinator_charges = $row['vaccinator_charges'];
    $transportation_charges = $row['transportation_charges'];
    $total_amount_payable = $row['total_amount_payable'];
    $tds_amt = $row['tds_amt'];
    $other_deduction = $row['other_deduction'];
    $farmer_payable = $row['farmer_payable'];
}

// Hatchery
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Farmer GC Print' AND `field_function` LIKE 'Display Chick Supplier Name' AND `user_access` LIKE 'all' AND `flag` = 1";
$query = mysqli_query($conn,$sql); $sup_name_flag = mysqli_num_rows($query);

if($high_chickin_secvcode == ""){
    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vcode = $row['vcode']; }
}
else{
    $vcode = $high_chickin_secvcode;
}
// Feed 
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = ""; $feed_cat = array();
while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; }
$feed_list = implode("','",$feed_cat);

$sql = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` LIKE '%WIP-%' AND `item_code` IN ('$feed_list') AND `batch` LIKE '$batch_code' AND `active` = '1' AND `dflag` = '0' GROUP BY `item_code` ORDER BY `item_code` ASC";
$query = mysqli_query($conn,$sql); $hstkcon_brand_itm = $hstkcon_brand_qty = $hstkcon_brand_amt = array();
while($row = mysqli_fetch_assoc($query)){
    $hstkcon_brand_itm[$row['item_code']] = $row['item_code'];
    $hstkcon_brand_qty[$row['item_code']] += (float)$row['quantity'];
    $hstkcon_brand_amt[$row['item_code']] += (float)$row['amount'];
}

if($high_feedin_brand_code == ""){
    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` like '%BF%' AND `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0' ORDER BY incr ASC  LIMIT 0,1"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vfcode = $row['vcode']; }

    if($vfcode != ""){
        $sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$vfcode' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $vfname = $row['name']; }
    }
    else{
    }
    if(sizeof($hstkcon_brand_qty) > 0){
        if(sizeof($hstkcon_brand_qty) > 1){ $maxValue = 0; $maxValue = max($hstkcon_brand_qty); $itm_code = array_keys($hstkcon_brand_qty, $maxValue)[0]; }
        else{ $itm_code = array_keys($hstkcon_brand_qty)[0]; }
        
        $sql = "SELECT * FROM `broiler_link_itembrand` WHERE `item_code` = '$itm_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bcode = $row['brand_code']; }

        $sql = "SELECT * FROM `broiler_item_brands` WHERE `code` = '$bcode' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $vfname = $row['description']; }
    }
}
else{
    $vfcode = $high_feedin_brand_code;
    $sql = "SELECT * FROM `broiler_item_brands` WHERE `code` = '$vfcode' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vfname = $row['description']; }
}
//fghghjh
$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$vcode' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vname = $row['name']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `code` IN ('$batch_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $bc = 0;
while($row = mysqli_fetch_assoc($query)){
    $batch_no = $row['batch_no'];
}
$b1no = $b2no = 0;
if($batch_no > 1){
    $batch_no--;
    if($batch_no > 0){
        $b1no = $batch_no;
        $batch_no--;
        if($batch_no > 0){
            $b2no = $batch_no;
        }
    }
}
//echo "<br/>".$b1no."--->".$b2no;
if($b1no > 0){
    $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` IN ('$b1no') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $b1_Batch_code = $row['code']; }
    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` IN ('$b1_Batch_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $b1_batch_code = $row['batch_code'];
        $b1_sold_birds = $row['sold_birds'];
        $b1_start_date = $row['start_date'];
        $b1_total_mort = $row['total_mort'];
        $b1_days7_mort = $row['days7_mort'];
        $b1_days30_mort = $row['days30_mort'];
        $b1_daysge31_mort = $row['daysge31_mort'];
        $b1_fcr = $row['fcr'];
        $b1_mean_age = $row['mean_age'];
        $b1_eef = $row['eef'];
        $b1_actual_prod_cost = $row['actual_prod_cost'];
        $b1_amount_payable = $row['amount_payable'];
    }
}
if($b2no > 0){
    $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` IN ('$b2no') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $b2_Batch_code = $row['code']; }
    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` IN ('$b2_Batch_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $b2_batch_code = $row['batch_code'];
        $b2_sold_birds = $row['sold_birds'];
        $b2_start_date = $row['start_date'];
        $b2_total_mort = $row['total_mort'];
        $b2_days7_mort = $row['days7_mort'];
        $b2_days30_mort = $row['days30_mort'];
        $b2_daysge31_mort = $row['daysge31_mort'];
        $b2_fcr = $row['fcr'];
        $b2_mean_age = $row['mean_age'];
        $b2_eef = $row['eef'];
        $b2_actual_prod_cost = $row['actual_prod_cost'];
        $b2_amount_payable = $row['amount_payable'];
    }
}

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Farm RC' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
}
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_name[$row['code']] = $row['description']; $farm_branch[$row['code']] = $row['branch_code']; $farmer_code[$row['code']] = $row['farmer_code']; $supervisor_code[$row['code']] = $row['supervisor_code']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$bh_code = $farm_branch[$farm_code]; $gc_date = date("Y-m-d",strtotime($date));
$sql = "SELECT * FROM `broiler_gc_standard` WHERE `from_date` <= '$gc_date' AND `to_date` >= '$gc_date' AND `branch_code` = '$bh_code' AND `active` = '1'  AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $standard_prod_cost = $row['standard_prod_cost']; }

$sup_code = $supervisor_code[$farm_code];
$sql = "SELECT * FROM `broiler_employee` WHERE `code` = '$sup_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_name = $row['name']; }

$sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mortality = $row['mortality']; $culls = $row['culls']; }

//Daily Entry
$count = 0;
$sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['mortality'];
    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['culls'];
    $day_ages[$row['date']] = $row['brood_age'];
    $key = $row['date']."@".$row['item_code1'];
    $day_qty[$key] = $day_qty[$key] + $row['kgs1'];
    $item_all[$row['item_code1']] = $row['item_code1'];
    $key = $row['date']."@".$row['item_code2'];
    $day_qty[$key] = $day_qty[$key] + $row['kgs2'];
    $item_all[$row['item_code2']] = $row['item_code2'];
    if($dstart_date == ""){ $dstart_date = $row['date']; } else{ if(strtotime($dstart_date) >= strtotime($row['date'])){ $dstart_date = $row['date']; } }
    if($dend_date == ""){ $dend_date = $row['date']; } else{ if(strtotime($dend_date) <= strtotime($row['date'])){ $dend_date = $row['date']; } }

}
$fdate = strtotime($dstart_date); $tdate = strtotime($dend_date); $days = $sold_mean_total = $bird_sold_amt = 0;
for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
    $days++;
    $present_date = date("Y-m-d",$currentDate);
    if($days <= 7){
        $days7_mort_count = $days7_mort_count + $day_mort[$present_date."@".$chick_code];
        $days30_mort_count = $days30_mort_count + $day_mort[$present_date."@".$chick_code];
    }
    else if($days <= 30){
        $days30_mort_count = $days30_mort_count + $day_mort[$present_date."@".$chick_code];
    }
    else if($days > 30){
        $days31_mort_count = $days31_mort_count + $day_mort[$present_date."@".$chick_code];
    }
    else{ }
    $mort_total = $mort_total + $day_mort[$present_date."@".$chick_code];
}

$fm_code = $farmer_code[$farm_code];
$sql = "SELECT * FROM `broiler_farmer` WHERE `code` = '$fm_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $fmr_code = $row['code'];
    $fmr_name = $row['name'];
    $fmr_farmer_group = $row['farmer_group'];
    $fmr_mobile1 = $row['mobile1'];
    $fmr_mobile2 = $row['mobile2'];
    $fmr_panno = $row['panno'];
    $fmr_aadharno = $row['aadharno'];
    $fmr_nationalidno = $row['nationalidno'];
    $fmr_address = $row['address'];
    $fmr_tds_per = $row['tds_per'];
    $fmr_accountno = $row['accountno'];
    $fmr_ifsc_code = $row['ifsc_code'];
    $fmr_bank_name = $row['bank_name'];
    $fmr_branch_code = $row['branch_code'];
}
$bank_flag = 1;
$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $description[$row['code']] = $row['description']; }

$html = "";

$html .= '<table align="center" style="font-size:9px;border: 1px solid black;">';
$html .= '<tr>';
$html .= '<th colspan="2" style="text-align:center;"><br/><br/>';
$html .= '<img src="../../'.$img_path.'" height="100px" />';
$html .= '</th>';
$html .= '<th colspan="6" style="text-align:center;border-left: 1px solid black;">';
$html .= '<i align="center">'.$cdetail.'</i>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="8" style="text-align:center;border-top: 1px solid black;border-bottom: 1px solid black;">';
$html .= '<b style="text-align:center;color:purple;">MANAGEMENT - GC</b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Farm Details</b>';
$html .= '</th>';
$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Farmer Details</b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Farm Name</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$farm_name[$farm_code].'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Farmer Name</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$fmr_name.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Farmer Mobile No.</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$fmr_mobile1.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Batch No.</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$batch_name[$batch_code].'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Supervisor Name: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$supervisor_name.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Account No.</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$fmr_accountno.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Farmer Branch: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$branch_name[$farm_branch[$farm_code]].'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Bank Name: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$fmr_bank_name.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">GC Date: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.$date.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Branch Name: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.$fmr_branch_code.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">PAN No: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-right: 10px;text-align:right;color:green;">'.$fmr_panno.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">IFSC</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.$fmr_ifsc_code.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Batch Information</b>';
$html .= '</th>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-top: 1px solid black;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Batch Performance</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:red;font-weight:bold;">Count</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:red;font-weight:bold;">%</b>';
$html .= '</th>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th colspan="1" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Hatchery </b>';
$html .= '</th>';
$html .= '<td colspan="3" style="padding-left: 1px;text-align:right;color:green;">'.$vname.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">1st week Mort%</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($days7_mort_count)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($days7_mort,2)).'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Supplier</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.$vfname.'</td>';
// $html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
// $html .= '<b style="padding-left: 10px;text-align:left;"></b>';
// $html .= '</th>';
// $html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Upto 30days Mort%</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($days30_mort_count)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($days30_mort,2)).'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Chicks Placement Date: </b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.$start_date.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">After 30days Mort%</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($days31_mort_count)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($daysge31_mort,2)).'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Final Liquidation Date: </b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.$liquid_date.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Total Mortality%</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($mort_total,2)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($total_mort,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Age: </b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.round($age).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:purple;">FCR</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($fcr,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Grade: </b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.$grade.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:purple;">CFCR</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($cfcr,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Housed Chicks: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($placed_birds)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:purple;">Avg.Body Weight</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($avg_wt,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Mortality: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($mortality)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Mean Age</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($mean_age,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Culls: </b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($culls)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Day Gain</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($day_gain,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Sold Birds</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($sold_birds)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">EEF</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($eef,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Sold Weight</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($sold_weight,2)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Avg. Sale Rate</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($sale_rate),2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Excess Birds</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($excess,2)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Shortage Birds</b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($shortage,2)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Feed Details</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:purple;font-weight:bold;">KG&#58;s</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:purple;font-weight:bold;">Bags</b>';
$html .= '</th>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-top: 1px solid black;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Rearing Charges</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:purple;font-weight:bold;">Per Unit</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:purple;font-weight:bold;">Amount</b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Feed Transferred</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_in_kgs,2)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_in_bag,2)).'</td>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Chick Cost</b></td>';
if((float)$placed_birds != 0){ $t1 = $actual_chick_cost / $placed_birds; } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round($actual_chick_cost,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Feed Consumed</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_consume_kgs,2)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_consume_bag,2)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Cost</b>';
$html .= '</th>';
if((float)$feed_consume_kgs != 0){ $t1 = $actual_feed_cost / $feed_consume_kgs; } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.round(($t1),2).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($actual_feed_cost,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Feed Return</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_out_kgs,2)).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_out_bag,2)).'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Medicine Cost</b></th>';
if((float)$placed_birds != 0){ $t1 = $actual_medicine_cost / $placed_birds; } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;">'.round(($t1),2).'</td>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round($actual_medicine_cost,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-weight:bold;">Previous 2 Batches Performance</b>';
$html .= '</th>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Admin Cost</b></th>';
if((float)$placed_birds != 0){ $t1 = $admin_cost_amt / $placed_birds; } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.round(($t1),2).'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($admin_cost_amt,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:purple;font-weight:bold;">Batch</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;font-size:8px;border-left: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:purple;font-weight:bold;font-size:8px;">'.$batch_name[$b2_batch_code].'</b>';
$html .= '</th>';
$html .= '<th colspan="1" style="padding-right: 10px;text-align:right;border-top: 1px solid black;font-size:8px;border-left: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:purple;font-weight:bold;font-size:8px;">'.$batch_name[$b1_batch_code].'</b>';
$html .= '</th>';
$actual_prod_amount = $actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt;
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Production Cost</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($actual_prod_amount,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Housed Date</b></th>';
if(date("d.m.Y",strtotime($b2_start_date)) != "01.01.1970"){
    $html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.date("d.m.Y",strtotime($b2_start_date)).'</td>';
}
else{
    $html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;"></td>';
}
if(date("d.m.Y",strtotime($b1_start_date)) != "01.01.1970"){
    $html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.date("d.m.Y",strtotime($b1_start_date)).'</td>';
}
else{
    $html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;"></td>';
}
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Production Cost/Kg</b></th>';
if((float)$sold_weight != 0){ $t1 = $actual_prod_amount / $sold_weight; } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Mortality %</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_total_mort.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_total_mort.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Farmer Sales Amount</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($farmer_sale_deduction,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">1st week Mort%</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_days7_mort.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_days7_mort.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Total Amount Payable</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'."".'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($total_amount_payable,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Up to 30 Days Mort%</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_days30_mort.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_days30_mort.'</td>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Less TDS%</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($tds_amt,2)).'</td>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;"></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">After 30 Days Mort%</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_daysge31_mort.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_daysge31_mort.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Farmer Payable</b></th>';
//$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;"></td>'; //'.round(($total_gc_amt + $mortality_incentive_amt + $other_incentive) / $sold_weight,3).'
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($farmer_payable,2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">FCR</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_fcr.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_fcr.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;color:purple;">Total Cost</b></th>';
$html .= '<td colspan="2" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $total_amount_payable),2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:purple;font-weight:bold;">Mean Age</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_mean_age.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_mean_age.'</td>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;color:purple;">PC/Kg(Including Farmer Pay)</b></th>';
if((float)$sold_weight != 0){ $t1 = ($actual_prod_amount + $total_amount_payable) / $sold_weight; } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;font-weight:bold;">EEF</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_eef.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_eef.'</td>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;color:purple;">Sale Amount</b></th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($sale_amount),2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;font-weight:bold;">P.Price</b>';
$html .= '</th>';
if($b2_actual_prod_cost > 0 && $b2_sold_birds > 0){
    $b2_pprice = round($b2_actual_prod_cost / $b2_sold_birds);
}
else{
    $b2_pprice = "";
}
if($b1_actual_prod_cost > 0 && $b1_sold_birds > 0){
    $b1_pprice = round($b1_actual_prod_cost / $b1_sold_birds);
}
else{
    $b1_pprice = "";
}
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_pprice.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_pprice.'</td>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;color:purple;">Profit / Loss</b></th>';
if((float)$sold_weight != 0){ $t1 = ((($sale_amount) - ($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $total_amount_payable)) / $sold_weight); } else{ $t1 = 0; }
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round($t1,2)).'</td>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round((($sale_amount) - ($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $total_amount_payable)),2)).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;font-weight:bold;">P.Cost</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_actual_prod_cost.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_actual_prod_cost.'</td>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;"></b></th>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;"></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:purple;font-weight:bold;">Amount Paid</b>';
$html .= '</th>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b2_amount_payable.'</td>';
$html .= '<td colspan="1" style="padding-left: 10px;text-align:right;color:green;border-left: 1px solid black;">'.$b1_amount_payable.'</td>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;"></b></th>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;"></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="2" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;"></b></th>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;border-left: 1px solid black;"></td>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;border-left: 1px solid black;"></td>';
$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;"></b></th>';
$html .= '<td colspan="1" style="padding-right: 10px;text-align:right;color:green;"></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td colspan="1" style="border-top: 1px solid black;"><br/><br/><br/></td>';
$html .= '<td colspan="2" style="text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/><br/>Prepared By</td>';
$html .= '<td colspan="2" style="text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/><br/>Accountant/Manager</td>';
$html .= '<td colspan="2" style="text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/><br/>Authorized Signatory</td>';
$html .= '<td colspan="1" style="border-top: 1px solid black;"><br/><br/><br/></td>';
$html .= '</tr>';
$html .= '</table>';
//echo $html;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 10, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
//$pdf->SetMargins(7, 7, 7, true);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>