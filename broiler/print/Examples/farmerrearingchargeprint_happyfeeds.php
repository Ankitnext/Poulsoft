<?php
//farmerrearingchargeprint_happyfeeds.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;


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
    $feed_cost_amt = $row['feed_cost_amt'];
    $feed_cost_unit = $row['feed_cost_unit'];
    $admin_cost_amt = $row['admin_cost_amt'];
    $admin_cost_unit = $row['admin_cost_unit'];
    $medicine_cost_amt = $row['medicine_cost_amt'];
    $medicine_cost_unit = $row['medicine_cost_unit'];
    $total_cost_amt = $row['total_cost_amt'];
    $total_cost_unit = $row['total_cost_unit'];
    $actual_prod_cost = $row['actual_prod_cost'];
    $standard_prod_cost = $row['standard_prod_cost'];
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
    $fcr_incentive_prc = $row['fcr_incentive_prc'];
    $fcr_incentive_amt = $row['fcr_incentive_amt'];
    $summer_incentive_prc = $row['summer_incentive_prc'];
    $summer_incentive_amt = $row['summer_incentive_amt'];
    $other_incentive = $row['other_incentive']; if($other_incentive == ""){ $other_incentive = 0; }
    $ifft_charges = $row['ifft_charges']; if($ifft_charges == ""){ $ifft_charges = 0; }
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
    $advance_deduction = $row['advance_deduction'];
    $farmer_payable = $row['farmer_payable'];
    $remarks = $row['remarks'];
    $other_sales = $row['other_sales'];
}
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Farm RC' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
}
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_fcode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description']; $farm_branch[$row['code']] = $row['branch_code']; $farmer_code[$row['code']] = $row['farmer_code']; $supervisor_code[$row['code']] = $row['supervisor_code']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feedcat_name[$row['code']] = $row['description']; $feedcat_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chickcat_name[$row['code']] = $row['description']; $chickcat_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%broiler bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bbcat_name[$row['code']] = $row['description']; $bbcat_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_code[$row['code']] = $row['code']; $item_category[$row['code']] = $row['category']; }

$bh_code = $farm_branch[$farm_code]; $gc_date = date("Y-m-d",strtotime($date));

$sup_code = $supervisor_code[$farm_code];
$sql = "SELECT * FROM `broiler_employee` WHERE `code` = '$sup_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_name = $row['name']; }

$sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mortality = $row['mortality']; $culls = $row['culls']; }

$sql = "SELECT * FROM `broiler_purchases` WHERE `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
$query = mysqli_query($conn,$sql); $total_in_feed_qty = array(); $total_in_chick_qty = $total_placed_chick_qty = 0;
while($row = mysqli_fetch_assoc($query)){
    $cats = $item_category[$row['icode']];
    if(!empty($feedcat_code[$cats])){
        $total_in_feed_qty[$row['icode']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
        $feeditem_array_list[$row['icode']] = $row['icode'];
    }
    if(!empty($chickcat_code[$cats])){
        $total_in_chick_qty += ((float)$row['snt_qty'] + (float)$row['fre_qty']);
        $total_placed_chick_qty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
    }
}

$sql = "SELECT * FROM `broiler_sales` WHERE `vcode` = '' AND `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
$query = mysqli_query($conn,$sql); $farmer_sale_bno = 0;
while($row = mysqli_fetch_assoc($query)){
    $cats = $item_category[$row['icode']];
    if(!empty($bbcat_code[$cats])){
        $farmer_sale_bno += ((float)$row['birds']);
    }
}

$sql = "SELECT * FROM `item_stocktransfers` WHERE `to_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $cats = $item_category[$row['code']];
    if(!empty($feedcat_code[$cats])){
        $total_in_feed_qty[$row['code']] += ((float)$row['quantity']);
        $feeditem_array_list[$row['code']] = $row['code'];
    }
    if(!empty($chickcat_code[$cats])){
        $total_in_chick_qty += ((float)$row['quantity']);
        $total_placed_chick_qty += ((float)$row['quantity']);
    }
}
$sql = "SELECT * FROM `item_stocktransfers` WHERE `from_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $total_out_feed_qty = array();
while($row = mysqli_fetch_assoc($query)){
    $cats = $item_category[$row['code']];
    if(!empty($feedcat_code[$cats])){
        $total_out_feed_qty[$row['code']] += ((float)$row['quantity']);
    }
}

//Daily Entry
$count = 0;
$sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $total_consumed_feed_qty = array();
while($row = mysqli_fetch_assoc($query)){
    $cats = $item_category[$row['item_code1']]; if(!empty($feedcat_code[$cats])){ $total_consumed_feed_qty[$row['item_code1']] += ((float)$row['kgs1']); }
    $cats = $item_category[$row['item_code2']]; if(!empty($feedcat_code[$cats])){ $total_consumed_feed_qty[$row['item_code2']] += ((float)$row['kgs2']); }

    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['mortality'];
    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['culls'];
    $day_fcons[$row['date']."@".$chick_code] = $day_fcons[$row['date']."@".$chick_code] + $row['kgs1'];
    $day_fcons[$row['date']."@".$chick_code] = $day_fcons[$row['date']."@".$chick_code] + $row['kgs2'];
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
$fdate = strtotime($dstart_date); $tdate = strtotime($dend_date); $days = $sold_mean_total = $bird_sold_amt = 
$week_1mortcnt = $week_2mortcnt = $week_3mortcnt = $week_4mortcnt = $week_5mortcnt = $week_6mortcnt = $week_7mortcnt = 0;
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
    $total_fconscnt = $total_fconscnt + $day_fcons[$present_date."@".$chick_code];

    if($days <= 7){ $week_1mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_1fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
    if($days > 7 && $days <= 14){ $week_2mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_2fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
    if($days > 14 && $days <= 21){ $week_3mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_3fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
    if($days > 21 && $days <= 28){ $week_4mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_4fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
    if($days > 28 && $days <= 35){ $week_5mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_5fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
    if($days > 35 && $days <= 42){ $week_6mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_6fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
    if($days > 42 && $days <= 49){ $week_7mortcnt += (float)$day_mort[$present_date."@".$chick_code]; $week_7fconscnt += (float)$day_fcons[$present_date."@".$chick_code]; }
}
$week_1mortper = round((((float)$week_1mortcnt / (float)$placed_birds) * 100),2);
$week_2mortper = round((((float)$week_2mortcnt / (float)$placed_birds) * 100),2);
$week_3mortper = round((((float)$week_3mortcnt / (float)$placed_birds) * 100),2);
$week_4mortper = round((((float)$week_4mortcnt / (float)$placed_birds) * 100),2);
$week_5mortper = round((((float)$week_5mortcnt / (float)$placed_birds) * 100),2);
$week_6mortper = round((((float)$week_6mortcnt / (float)$placed_birds) * 100),2);
$week_7mortper = round((((float)$week_7mortcnt / (float)$placed_birds) * 100),2);
$total_mortper = round((((float)$mort_total / (float)$placed_birds) * 100),2);

$week_1fconsper = (float)$week_1fconscnt / (float)$placed_birds;
$week_2fconsper = (float)$week_2fconscnt / (float)$placed_birds;
$week_3fconsper = (float)$week_3fconscnt / (float)$placed_birds;
$week_4fconsper = (float)$week_4fconscnt / (float)$placed_birds;
$week_5fconsper = (float)$week_5fconscnt / (float)$placed_birds;
$week_6fconsper = (float)$week_6fconscnt / (float)$placed_birds;
$week_7fconsper = (float)$week_7fconscnt / (float)$placed_birds;
$total_fconsper = (float)$total_fconscnt / (float)$placed_birds;

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

$html = '';
$html .= '<table style="border:1px solid black;">';
$html .= '<tr style="line-height: 0.8;">';
$html .= '<th colspan="2" style="width:90px;text-align:center;"><br/><br/><img src="../../'.$img_path.'" height="50px" /></th>';
$html .= '<th colspan="5" style="width:363px;text-align:center;"><i align="center">'.$cdetail.'</i></th>';
$html .= '<th colspan="1" style="width:80px;text-align:center;"><br/><br/><img src="../../images/farmerprint2ndimg.jpeg" height="50px" /></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 1.7;"><th colspan="1" style="width:80px;text-align:right;">NAME:</th><td colspan="4" style="text-align:left;">'.$fmr_name.'</td><th colspan="1" style="text-align:right;">PAN NO:</th><td colspan="2" style="text-align:left;">'.$fmr_panno.'</td></tr>';
$html .= '<tr style="line-height: 1.7;"><th colspan="1" style="width:80px;text-align:right;">AREA:</th><td colspan="7" style="text-align:left;">'.$line_name[$line_code].", ".$branch_name[$farm_branch[$farm_code]].'</td></tr>';
$html .= '<tr style="line-height: 1.7;"><th colspan="1" style="width:80px;text-align:right;">FARM CODE:</th><td colspan="1" style="width:60px;text-align:left;">'.$farm_fcode[$farm_code].'</td><th colspan="2" style="width:60px;text-align:right;">FLOCK NO:</th><td colspan="2" style="text-align:left;">'.$batch_book[$batch_code].'</td><th colspan="1" style="width:60px;text-align:right;">BATCH NO:</th><td colspan="2" style="width:110px;text-align:left;">'.$batch_name[$batch_code].'</td></tr>';

$html .= '<tr><th colspan="8" style="width:533px;text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>COMMERCIAL BROILER BIRDS - BATCH FINAL RECORD</b></th></tr>';

//Row wise and column wise cell values
$col1 = $col2 = $col3 = $col4 = $col5 = $col6 = $col7 = $col8 = array();
$incr1 = $incr2 = $incr3 = $incr4 = $incr5 = $incr6 = $incr7 = $incr8 = 1;
//$col1[$incr1] = ''; $incr1++; $col2[$incr2] = ''; $incr2++; $col3[$incr3] = ''; $incr3++; $col4[$incr4] = ''; $incr4++;
//$col5[$incr5] = ''; $incr5++; $col6[$incr6] = ''; $incr6++; $col7[$incr7] = ''; $incr7++; $col8[$incr8] = ''; $incr8++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">BROILER CHICKS DISPATCHED</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($total_in_chick_qty)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">TRANSIT MORTALITY</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind((float)$total_in_chick_qty - (float)$total_placed_chick_qty)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">TOTAL INDUCTED CHICKS</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($total_placed_chick_qty)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">BIRDS MORTALITY</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($mortality)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">FARMER SALES</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($farmer_sale_bno)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">WEAK BIRDS / CULLS</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($culls)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">ABNORMAL MORTALITY</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">SHORTAGE /FARMER</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($shortage)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">EXCESS</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($excess)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">BROILER BIRDS OUT</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($sold_birds)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">TOTAL WEIGHT</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($sold_weight)).'</td>'; $incr4++;

$col1[$incr1] = '<th colspan="3" style="text-align:left;">TOTAL VALUE</th>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($sale_amount)).'</td>'; $incr4++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">DATE IN</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.date("d.m.Y",strtotime($start_date)).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">DATE OUT</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.date("d.m.Y",strtotime($liquid_date)).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">REPORT DT</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.date("d.m.Y",strtotime($date)).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">MEAN AGE</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.number_format_ind($mean_age).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Local Sales Rs:</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.number_format_ind($farmer_sale_deduction).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Total Mortality:</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.str_replace(".00","",number_format_ind($mortality + $culls)).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Grade:</th>'; $incr5++; 
$col6[$incr6] = ''; $incr6++; 
$col7[$incr7] = ''; $incr7++; 
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.$grade.'</td>'; $incr8++;

$col5[$incr5] = ''; $incr5++; $col6[$incr6] = ''; $incr6++; $col7[$incr7] = ''; $incr7++; $col8[$incr8] = ''; $incr8++;
//$col5[$incr5] = ''; $incr5++; $col6[$incr6] = ''; $incr6++; $col7[$incr7] = ''; $incr7++; $col8[$incr8] = ''; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Feed received</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.number_format_ind($feed_in_kgs).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Return Feed</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.number_format_ind($feed_out_kgs).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Consumption</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.number_format_ind($feed_consume_kgs).'</td>'; $incr8++;

$col5[$incr5] = '<th colspan="3" style="text-align:left;">Returnfeed %</th>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;">'.number_format_ind((($feed_out_kgs / $feed_in_kgs) * 100)).'</td>'; $incr8++;

$m = 0; $m = max($incr1,$incr2,$incr3,$incr4,$incr5,$incr6,$incr7,$incr8);
for ($i = 1; $i < $m; $i++){
    if($incr1 < $m){ $col1[$incr1] = ''; $incr1++; } if($incr2 < $m){ $col2[$incr2] = ''; $incr2++; } if($incr3 < $m){ $col3[$incr3] = ''; $incr3++; } if($incr4 < $m){ $col4[$incr4] = ''; $incr4++; }
    if($incr5 < $m){ $col5[$incr5] = ''; $incr5++; } if($incr6 < $m){ $col6[$incr6] = ''; $incr6++; } if($incr7 < $m){ $col7[$incr7] = ''; $incr7++; } if($incr8 < $m){ $col8[$incr8] = ''; $incr8++; }
}

$col1[$incr1] = '<th colspan="8" style="text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>FEED CONSUMED</b></th>';
$col2[$incr2] = ''; $col3[$incr3] = ''; $col4[$incr4] = ''; $col5[$incr5] = ''; $col6[$incr6] = ''; $col7[$incr7] = ''; $col8[$incr8] = '';
$incr1++; $incr2++; $incr3++; $incr4++; $incr5++; $incr6++; $incr7++; $incr8++;


//Feed Details
foreach($feeditem_array_list as $items){
    $col1[$incr1] = '<td style="width:130px;">'.$item_name[$items].'</td>'; $incr1++;
    $col2[$incr2] = '<td colspan="1" style="text-align:right;">'.number_format_ind($total_in_feed_qty[$items]).'</td>'; $incr2++;
    $col3[$incr3] = '<td colspan="1" style="text-align:right;">'.number_format_ind($total_out_feed_qty[$items]).'</td>'; $incr3++;
    $col4[$incr4] = '<td colspan="1" style="text-align:right;">'.number_format_ind($total_consumed_feed_qty[$items]).'</td>'; $incr4++;
    $col5[$incr5] = '<td colspan="1" style="width:30px;text-align:left;">Kgs</td>'; $incr5++;
    $col6[$incr6] = '<td colspan="1" style="width:50px;text-align:right;">Average</td>'; $incr6++;
    $col7[$incr7] = '<td colspan="1" style="text-align:right;">'.number_format_ind((float)$total_consumed_feed_qty[$items] / (float)$total_placed_chick_qty).'</td>'; $incr7++;
    $col8[$incr8] = '<td colspan="1" style="text-align:left;">kg/Bird</td>'; $incr8++;
}

$col1[$incr1] = '<td style="width:130px;font-weight:bold;">TOTAL FEED CONSUMED</td>'; $incr1++;
$col2[$incr2] = '<td colspan="1" style="text-align:right;font-weight:bold;">'.number_format_ind($feed_in_kgs).'</td>'; $incr2++;
$col3[$incr3] = '<td colspan="1" style="text-align:right;font-weight:bold;">'.number_format_ind($feed_out_kgs).'</td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;font-weight:bold;">'.number_format_ind($feed_consume_kgs).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:30px;text-align:left;font-weight:bold;">Kgs</td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:50px;text-align:right;font-weight:bold;">Average</td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="text-align:right;font-weight:bold;">'.number_format_ind((float)$feed_consume_kgs / (float)$sold_birds).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:left;font-weight:bold;">kg/Bird</td>'; $incr8++;


$m = 0; $m = max($incr1,$incr2,$incr3,$incr4,$incr5,$incr6,$incr7,$incr8);
for ($i = 1; $i < $m; $i++){
    if($incr1 < $m){ $col1[$incr1] = ''; $incr1++; } if($incr2 < $m){ $col2[$incr2] = ''; $incr2++; } if($incr3 < $m){ $col3[$incr3] = ''; $incr3++; } if($incr4 < $m){ $col4[$incr4] = ''; $incr4++; }
    if($incr5 < $m){ $col5[$incr5] = ''; $incr5++; } if($incr6 < $m){ $col6[$incr6] = ''; $incr6++; } if($incr7 < $m){ $col7[$incr7] = ''; $incr7++; } if($incr8 < $m){ $col8[$incr8] = ''; $incr8++; }
}

$col1[$incr1] = '<th colspan="8" style="width:auto;text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>PERFORMANCE</b></th>';
$col2[$incr2] = ''; $col3[$incr3] = ''; $col4[$incr4] = ''; $col5[$incr5] = ''; $col6[$incr6] = ''; $col7[$incr7] = ''; $col8[$incr8] = '';
$incr1++; $incr2++; $incr3++; $incr4++; $incr5++; $incr6++; $incr7++; $incr8++;

$col1[$incr1] = '<td colspan="1" style="width:100px;">MORTALITY</td>'; $incr1++;
$col2[$incr2] = '<td colspan="1" style="width:65px;"></td>'; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(((100/(float)$total_placed_chick_qty) * ((float)$mortality + (float)$culls))).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:20px;text-align:left;"></td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">AVERAGE BODY WEIGHT</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($avg_wt).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:50px;text-align:left;">Kg./Bird</td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">AVERAGE DAILY WEIGHT GAIN</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind((($avg_wt * 1000) / $mean_age)).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:50px;text-align:left;">Gm./Day</td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">F.C.R. ACTUAL</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($fcr,2)).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:20px;text-align:left;"></td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">F.C.R. ADJUSTED</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($fcr,2)).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:20px;text-align:left;"></td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">F.C.R. CONVERTED</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($cfcr,2)).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:20px;text-align:left;"></td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">E.E.F</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($eef).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:20px;text-align:left;"></td>'; $incr8++;

$col1[$incr1] = '<td colspan="2" style="width:165px;">SELLING PRICE</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td colspan="1" style="width:65px;"></td>'; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:65px;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:65px;"></td>'; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:68px;"></td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($sale_rate).'</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:50px;text-align:left;">Rs./Kg.</td>'; $incr8++;

$col1[$incr1] = '<td colspan="8" style="width:auto;text-align:center;color:green;font-weight:bold;border-top: 1px solid black;">MORTALITY AND FEED CONSUMED WEEK WISE DETAILS</td>';
$col2[$incr2] = ''; $col3[$incr3] = ''; $col4[$incr4] = ''; $col5[$incr5] = ''; $col6[$incr6] = ''; $col7[$incr7] = ''; $col8[$incr8] = '';
$incr1++; $incr2++; $incr3++; $incr4++; $incr5++; $incr6++; $incr7++; $incr8++;
$col9 = array(); $incr9 = $incr8;
$col1[$incr1] = '<td style="width:83px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">Description</td>'; $incr1++;
$col2[$incr2] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">1st Week</td>'; $incr2++;
$col3[$incr3] = '<td style="width:58px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">2nd Week</td>'; $incr3++;
$col4[$incr4] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">3rd Week</td>'; $incr4++;
$col5[$incr5] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">4th Week</td>'; $incr5++;
$col6[$incr6] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">5th Week</td>'; $incr6++;
$col7[$incr7] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">6th Week</td>'; $incr7++;
$col8[$incr8] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;border-right: 1px solid black;">7th Week</td>'; $incr8++;
$col9[$incr9] = '<td style="width:56px;font-size:9px;font-weight:bold;border-top: 1px solid black;">Total</td>'; $incr9++;

$col1[$incr1] = '<td style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">Mortality(%)</td>'; $incr1++;
$col2[$incr2] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_1mortper,2)).'</td>'; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_2mortper,2)).'</td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_3mortper,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_4mortper,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_5mortper,2)).'</td>'; $incr6++;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_6mortper,2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_7mortper,2)).'</td>'; $incr8++;
$col9[$incr9] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($total_mortper,2)).'</td>'; $incr9++;

$col1[$incr1] = '<td style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">Feed/Bird(%)</td>'; $incr1++;
$col2[$incr2] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_1fconsper,2)).'</td>'; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_2fconsper,2)).'</td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_3fconsper,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_4fconsper,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_5fconsper,2)).'</td>'; $incr6++;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_6fconsper,2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_7fconsper,2)).'</td>'; $incr8++;
$col9[$incr9] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($total_fconsper,2)).'</td>'; $incr9++;

$col1[$incr1] = '<th colspan="8" style="width:auto;text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>PRODUCTION COST</b></th>';
$col2[$incr2] = ''; $col3[$incr3] = ''; $col4[$incr4] = ''; $col5[$incr5] = ''; $col6[$incr6] = ''; $col7[$incr7] = ''; $col8[$incr8] = '';
$incr1++; $incr2++; $incr3++; $incr4++; $incr5++; $incr6++; $incr7++; $incr8++; $incr9++;

$col1[$incr1] = '<td colspan="2" style="width:165px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;"><br/>DESCRIPTION</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td style="width:65px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">QUANTITY<br/>(K.gs)</td>'; $incr3++;
$col4[$incr4] = '<td style="width:65px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">PRICE<br/>(Rs)</td>'; $incr4++;
$col5[$incr5] = '<td colspan="2" style="width:100px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">TOTAL<br/>(Rs)</td>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = '<td style="width:73px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">AVERAGE<br/>(Rs./Kg.Bwt.)</td>'; $incr7++;
$col8[$incr8] = '<td style="width:65px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">Growing<br/>Charges</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">BROILER D.O.C.</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind($total_placed_chick_qty)).'</td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($chick_cost_unit,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="2" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($chick_cost_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round(($chick_cost_amt / $sold_weight),2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">FEED</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind($feed_consume_kgs).'</td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($feed_cost_unit,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="2" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($feed_cost_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round(($feed_cost_amt / $sold_weight),2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">MANAGEMENT COST / OVER HEAD</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind($total_placed_chick_qty)).'</td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($admin_cost_unit,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="2" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($admin_cost_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round(($admin_cost_amt / $sold_weight),2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">MEDICINE AND VACCINE COST</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="2" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($medicine_cost_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round(($medicine_cost_amt / $sold_weight),2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">ACTUAL PRODUCTION COST</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr3++;
$col4[$incr4] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="2" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($total_cost_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = ''; $incr6++;
//$actual_gc_prc = round($standard_gc_prc - ((($total_cost_amt / $sold_weight) - $standard_prod_cost) * 0.5),2);
$actual_gc_prc = $grow_charge_exp_prc;
$col7[$incr7] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round(($total_cost_amt / $sold_weight),2)).'</td>'; $incr7++;
$col8[$incr8] = '<td style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind($actual_gc_prc).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<th colspan="8" style="width:auto;text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>PAYMENTS</b></th>';
$col2[$incr2] = ''; $col3[$incr3] = ''; $col4[$incr4] = ''; $col5[$incr5] = ''; $col6[$incr6] = ''; $col7[$incr7] = ''; $col8[$incr8] = '';
$incr1++; $incr2++; $incr3++; $incr4++; $incr5++; $incr6++; $incr7++; $incr8++; $incr9++;

$col1[$incr1] = '<td colspan="5" style="width:305px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">EARNINGS</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = '<td colspan="3" style="width:228px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">DEDUCTIONS</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = ''; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="width:145px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">Particulars</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="width:60px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">Per kg.</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="width:100px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">Amount</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">Particulars</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">Amount</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">1. Growing charges</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$actual_gc_amt = round(($actual_gc_prc * $sold_weight));
$sales_incentive_amt = round(($sales_incentive_amt));
$mortality_incentive_amt = round(($mortality_incentive_amt));
$fcr_incentive_amt = round(($fcr_incentive_amt));

if($sold_weight > 0){$mrt_prc = $mortality_incentive_amt / $sold_weight;} else { $mrt_prc = 0;}

$tds_amt = round(($tds_amt));
$vaccinator_charges = round(($vaccinator_charges));
$farmer_sale_deduction = round(($farmer_sale_deduction));
$birds_shortage = round(($birds_shortage));
$mortality_deduction = round(($mortality_deduction));
$fcr_deduction = round(($fcr_deduction));
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($actual_gc_prc,5)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;" title="">'.number_format_ind(round($actual_gc_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">1. T. D . S</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($tds_amt,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">2. Loading incentives</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'."".'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($summer_incentive_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">2. Vaccines</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($vaccinator_charges,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">3. Additional Incentive</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($mrt_prc,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($mortality_incentive_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">3. Local sale</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($farmer_sale_deduction,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">4. Early Lifting Incentive</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($fcr_incentive_prc,2)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($fcr_incentive_amt,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">4. Shortages</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($birds_shortage,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">5. Legweak Incentive</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($other_incentive,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">5. Penality for Excess Mort</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($mortality_deduction,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">5. IIFT Incentives</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($ifft_charges,2)).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">6. Penality for Excess F C R</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($fcr_deduction,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="5" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">7. Abnormal Mortality </td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round(0,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="5" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">8. Other Deductions</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($other_deduction,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="5" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">9. Other Sales</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($other_sales,2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;



if($advance_deduction == "" || $advance_deduction == 0){ $advance_deduction = 0; }
$total_earnings = round((($actual_gc_amt) +($mortality_incentive_amt) +($fcr_incentive_amt) +($other_incentive) +($ifft_charges) + ($summer_incentive_amt)),2);
$col1[$incr1] = '<td colspan="3" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">TOTAL EARNINGS</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">'.number_format_ind(round(((float)$total_earnings / (float)$sold_weight),2)).'</td>'; $incr4++;
$col5[$incr5] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">'.number_format_ind($total_earnings).'</td>'; $incr5++;
$col6[$incr6] = '<td colspan="2" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">TOTAL DEDUCTIONS</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">'.number_format_ind(round((($tds_amt) + ($vaccinator_charges) + ($farmer_sale_deduction) + ($birds_shortage) + ($fcr_deduction) + ($mortality_deduction) + ($other_deduction) + ($advance_deduction) + ($other_sales)),2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="7" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;"></td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">'.number_format_ind(round((($total_earnings) - (($tds_amt) + ($vaccinator_charges) + ($farmer_sale_deduction) + ($birds_shortage) + ($fcr_deduction) + ($mortality_deduction) + ($other_deduction) + ($advance_deduction))),2)).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="6" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b>Remarks: </b> '.$remarks.'</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = ''; $incr6++;
$col7[$incr7] = '<td colspan="1" style="text-align:left;border-top: 1px solid black;border-right: 1px solid black;">LESS ADVANCE</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind($advance_deduction).'</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="5" style="width:305px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">NET PAYABLE: (NEFT: '.number_format_ind(round((($total_earnings) - (($tds_amt) + ($vaccinator_charges) + ($farmer_sale_deduction) + ($birds_shortage) + ($fcr_deduction) + ($mortality_deduction) + ($other_deduction) + ($advance_deduction))),2)).'/- Dt:           )</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = '<td colspan="3" style="width:228px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;">PAYABLE AMOUNT: '.number_format_ind(round((($total_earnings) - (($tds_amt) + ($vaccinator_charges) + ($farmer_sale_deduction) + ($birds_shortage) + ($fcr_deduction) + ($mortality_deduction) + ($other_deduction) + ($advance_deduction))),2)).'</td>'; $incr6++;
$col7[$incr7] = ''; $incr7++;
$col8[$incr8] = ''; $incr8++;
$col9[$incr9] = ''; $incr9++;

$col1[$incr1] = '<td colspan="5" style="width:205px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;font-size:8px;">'.$fmr_bank_name.', '.$fmr_branch_code.'<br/>Acc No: '.$fmr_accountno.'<br/>IFSC Code: '.$fmr_ifsc_code.'</td>'; $incr1++;
$col2[$incr2] = ''; $incr2++;
$col3[$incr3] = ''; $incr3++;
$col4[$incr4] = ''; $incr4++;
$col5[$incr5] = ''; $incr5++;
$col6[$incr6] = '<td colspan="1" style="width:109px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;"><br/><br/><br/>Prepared By</td>'; $incr6++;
$col7[$incr7] = '<td colspan="1" style="width:109px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;"><br/><br/><br/>Checked By</td>'; $incr7++;
$col8[$incr8] = '<td colspan="1" style="width:110px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;font-weight:bold;"><br/><br/><br/>Passed By</td>'; $incr8++;
$col9[$incr9] = ''; $incr9++;

for ($i = 1; $i < $incr1; $i++){
    $html .= '<tr>';
    $html .= $col1[$i].''.$col2[$i].''.$col3[$i].''.$col4[$i].''.$col5[$i].''.$col6[$i].''.$col7[$i].''.$col8[$i].''.$col9[$i];
    $html .= '</tr>';
}
$html .= '</table>';

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'GC Transaction-Print' AND `field_function` LIKE 'Detailed Mortality List' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $detailed_mort_count = mysqli_num_rows($query);
if($detailed_mort_count == 1 || $detailed_mort_count == "1"){ $detailed_mort_count = 1; } else{ $detailed_mort_count = 0; }

if($detailed_mort_count == 1 || $detailed_mort_count == "1"){
    //Daily Entry
    $count = 0;
    $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        if((int)$row['brood_age'] >= 1 && (int)$row['brood_age'] <= 7){ $w1_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 8 && (int)$row['brood_age'] <= 14){ $w2_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 15 && (int)$row['brood_age'] <= 21){ $w3_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 22 && (int)$row['brood_age'] <= 28){ $w4_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 29 && (int)$row['brood_age'] <= 35){ $w5_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 36 && (int)$row['brood_age'] <= 42){ $w6_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 43 && (int)$row['brood_age'] <= 49){ $w7_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 50 && (int)$row['brood_age'] <= 56){ $w8_mort += ((float)$row['mortality'] + (float)$row['culls']); }
        if((int)$row['brood_age'] >= 57){ $w9_mort += ((float)$row['mortality'] + (float)$row['culls']); }
    }

    $html .= '<div style="page-break-before:always"></div>';
    $html .= '<table align="center" style="border: 1px solid black;">';
    $html .= '<tr>';
    $html .= '<th colspan="2" style="text-align:center;"><br/><br/>';
    $html .= '<img src="../../'.$img_path.'" height="60px" />';
    $html .= '</th>';
    $html .= '<th colspan="6" style="text-align:center;">';
    $html .= '<i align="center">'.$cdetail.'</i>';
    $html .= '</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="8" style="text-align:center;border-top: 1px solid black;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;color:purple;">Batch Mortality Details</b>';
    $html .= '</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Farm Name</b>';
    $html .= '</th>';
    $html .= '<td colspan="4" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.$farm_name[$farm_code].'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Village</b>';
    $html .= '</th>';
    $html .= '<td colspan="4" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.$line_name[$line_code].'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Flock No.</b>';
    $html .= '</th>';
    $html .= '<td colspan="4" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.$batch_name[$batch_code].'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Housed Chicks</b>';
    $html .= '</th>';
    $html .= '<td colspan="4" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($placed_birds))).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Week</b>';
    $html .= '</th>';
    $html .= '<th colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Mortality</b>';
    $html .= '</th>';
    $html .= '<th colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Mortality %</b>';
    $html .= '</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">1st Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w1_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w1_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">2nd Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w2_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w2_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">3rd Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w3_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w3_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">4th Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w4_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w4_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">5th Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w5_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w5_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">6th Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w6_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w6_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">7th Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w7_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w7_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">8th Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w8_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w8_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;">9th Week</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w9_mort))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round((((float)$w9_mort / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th colspan="4" style="text-align:center;border-bottom: 1px solid black;font-weight:bold;">Total</th>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;font-weight:bold;">'.str_replace(".00","",number_format_ind(round((float)$mortality + (float)$culls))).'</td>';
    $html .= '<td colspan="2" style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;font-weight:bold;">'.number_format_ind(round(((((float)$mortality + (float)$culls) / (float)$placed_birds) * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '</table>';
}

$p2bp_flag = 1;
if($p2bp_flag == 1 || $p2bp_flag == "1"){
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
    $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` IN ('$b1no') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $qcount = mysqli_num_rows($query);
    if($qcount > 0){
        $html .= '<div style="page-break-before:always"></div>';
        $html .= '<table align="center" style="border: 1px solid black;">';
        $html .= '<tr>';
        $html .= '<th colspan="2" style="text-align:center;"><br/><br/>';
        $html .= '<img src="../../'.$img_path.'" height="60px" />';
        $html .= '</th>';
        $html .= '<th colspan="6" style="text-align:center;">';
        $html .= '<i align="center">'.$cdetail.'</i>';
        $html .= '</th>';
        $html .= '</tr>';

        if($b1no > 0){
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` IN ('$b1no') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $b1_Batch_code = $row['code']; }
            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` IN ('$b1_Batch_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $b1_batch_code = $row['batch_code'];
                $b1_placed_birds = $row['placed_birds']; if($b1_placed_birds == ""){ $b1_placed_birds = 0; }
                $b1_avg_wt = $row['avg_wt']; if($b1_avg_wt == ""){ $b1_avg_wt = 0; }
                $b1_fcr = $row['fcr']; if($b1_fcr == ""){ $b1_fcr = 0; }
                $b1_cfcr = $row['cfcr']; if($b1_cfcr == ""){ $b1_cfcr = 0; }
                $b1_mean_age = $row['mean_age']; if($b1_mean_age == ""){ $b1_mean_age = 0; }
                $b1_eef = $row['eef']; if($b1_eef == ""){ $b1_eef = 0; }
                $b1_sale_rate = $row['sale_rate']; if($b1_sale_rate == ""){ $b1_sale_rate = 0; }
            }
            $sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls FROM `broiler_daily_record` WHERE `batch_code` = '$b1_batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $b1_mortality = $row['mortality']; $b1_culls = $row['culls']; }

            if(((float)$b1_placed_birds * ((float)$b1_mortality + (float)$b1_culls)) == 0){ $t1 = 0; }
            else{ $t1 = ((100/(float)$b1_placed_birds) * ((float)$b1_mortality + (float)$b1_culls)); }
            $html .= '<tr><th colspan="8" style="width:auto;text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>PREVIOUS BATCH PERFORMANCE: '.$batch_name[$b1_batch_code].'</b></th></tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">MORTALITY</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($t1).'</td>';
            $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">AVERAGE BODY WEIGHT</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($b1_avg_wt).'</td>';
            $html .= '<td colspan="1" style="width:50px;text-align:left;">Kg./Bird</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">AVERAGE DAILY WEIGHT GAIN</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            if((float)$b1_mean_age != 0){
                $t1 = 0; $t1 = (($b1_avg_wt * 1000) / $b1_mean_age);
            }
            else{
                $t1 = 0;
            }
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($t1).'</td>';
            $html .= '<td colspan="1" style="width:50px;text-align:left;">Gm./Day</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">F.C.R. ACTUAL</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b1_fcr,2)).'</td>';
            $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">F.C.R. ADJUSTED</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b1_fcr,2)).'</td>';
            $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">F.C.R. CONVERTED</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b1_cfcr,2)).'</td>';
            $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">E.E.F</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b1_eef,2)).'</td>';
            $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="1" style="width:100px;text-align:left;">SELLING PRICE</td>';
            $html .= '<td colspan="5" style="width:325px;"></td>';
            $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b1_sale_rate,2)).'</td>';
            $html .= '<td colspan="1" style="width:50px;text-align:left;">Rs./Kg.</td>';
            $html .= '</tr>';
            
        }
        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` IN ('$b2no') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC";
        $query = mysqli_query($conn,$sql); $qcount = mysqli_num_rows($query);
        if($qcount > 0){
            if($b2no > 0){
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` IN ('$b2no') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $b2_Batch_code = $row['code']; }
                $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` IN ('$b2_Batch_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $b2_batch_code = $row['batch_code'];
                    $b2_placed_birds = $row['placed_birds']; if($b2_placed_birds == ""){ $b2_placed_birds = 0; }
                    $b2_avg_wt = $row['avg_wt']; if($b2_avg_wt == ""){ $b2_avg_wt = 0; }
                    $b2_fcr = $row['fcr']; if($b2_fcr == ""){ $b2_fcr = 0; }
                    $b2_cfcr = $row['cfcr']; if($b2_cfcr == ""){ $b2_cfcr = 0; }
                    $b2_mean_age = $row['mean_age']; if($b2_mean_age == ""){ $b2_mean_age = 0; }
                    $b2_eef = $row['eef']; if($b2_eef == ""){ $b2_eef = 0; }
                    $b2_sale_rate = $row['sale_rate']; if($b2_sale_rate == ""){ $b2_sale_rate = 0; }
                }
                $sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls FROM `broiler_daily_record` WHERE `batch_code` = '$b2_batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $b2_mortality = $row['mortality']; $b2_culls = $row['culls']; }

                if(((float)$b2_placed_birds * ((float)$b2_mortality + (float)$b2_culls)) == 0){ $t1 = 0; }
                else{ $t1 = ((100/(float)$b2_placed_birds) * ((float)$b2_mortality + (float)$b2_culls)); }
                $html .= '<tr><th colspan="8" style="width:auto;text-align:center;border-top:1px solid black;border-bottom:1px solid black;"><b>PREVIOUS BATCH PERFORMANCE: '.$batch_name[$b2_batch_code].'</b></th></tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">MORTALITY</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($t1).'</td>';
                $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">AVERAGE BODY WEIGHT</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($b2_avg_wt).'</td>';
                $html .= '<td colspan="1" style="width:50px;text-align:left;">Kg./Bird</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">AVERAGE DAILY WEIGHT GAIN</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                if((float)$b2_mean_age != 0){
                    $t1 = 0; $t1 = (($b2_avg_wt * 1000) / $b2_mean_age);
                }
                else{
                    $t1 = 0;
                }
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind($t1).'</td>';
                $html .= '<td colspan="1" style="width:50px;text-align:left;">Gm./Day</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">F.C.R. ACTUAL</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b2_fcr,2)).'</td>';
                $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">F.C.R. ADJUSTED</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b2_fcr,2)).'</td>';
                $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">F.C.R. CONVERTED</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b2_cfcr,2)).'</td>';
                $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">E.E.F</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b2_eef,2)).'</td>';
                $html .= '<td colspan="1" style="width:20px;text-align:left;"></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td colspan="1" style="width:100px;text-align:left;">SELLING PRICE</td>';
                $html .= '<td colspan="5" style="width:325px;"></td>';
                $html .= '<td colspan="1" style="width:50px;text-align:right;">'.number_format_ind(round($b2_sale_rate,2)).'</td>';
                $html .= '<td colspan="1" style="width:50px;text-align:left;">Rs./Kg.</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
    }
}
if($_SERVER['REMOTE_ADDR'] == "49.205.128.344"){
    echo $html;
}
else{
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Mallikarjuna K');
    $pdf->SetTitle('Famrer RC generate');
    $pdf->SetSubject('Famrer RC generate');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    $pdf->SetFont('dejavusans', '', 9, '', true);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->SetMargins(10, 5, 10, true);
    //$pdf->setCellPaddings(0,0,0,0);
    //$pdf->setCellHeightRatio(1.5);
    $pdf->AddPage('P', 'A4');
    
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    
    $pdf->Output('example_028.pdf', 'I');
}
?>