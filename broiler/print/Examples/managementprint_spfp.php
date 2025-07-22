<?php
//farmerrearingchargeprint_spfp.php
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
    $start_date = date("Y-m-d",strtotime($row['start_date']));
    $placed_birds = $row['placed_birds'];
    $actual_birds = $row['actual_birds'];
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
    $actual_charge_exp_prc = $row['actual_charge_exp_prc'];
    $actual_charge_exp_amt = $row['actual_charge_exp_amt'];
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
    $ifft_charges = $row['ifft_charges'];
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
    $equipment_charges = $row['equipment_charges'];
    $other_deduction = $row['other_deduction'];
    $farmer_payable = $row['farmer_payable'];
    $remarks = $row['remarks'];
    $actual_chick_cost = $row['actual_chick_cost'];
    $actual_feed_cost = $row['actual_feed_cost'];
}
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Farm RC' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
}
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description']; $farm_region[$row['code']] = $row['region_code']; $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farmer_code[$row['code']] = $row['farmer_code']; $supervisor_code[$row['code']] = $row['supervisor_code']; $area_name[$row['code']] = $row['area_name']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$region_code = $farm_region[$farm_code];
$bh_code = $farm_branch[$farm_code];
$gc_date = date("Y-m-d",strtotime($date));

$sup_code = $supervisor_code[$farm_code];
$sql = "SELECT * FROM `broiler_employee` WHERE `code` = '$sup_code' AND `active` = '1' "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_name = $row['name']; }

$sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mortality = $row['mortality']; $culls = $row['culls']; }

$sql = "SELECT MAX(date) as final_lifting_date FROM `broiler_sales` WHERE `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $final_lifting_date = date("d.m.Y",strtotime($row['final_lifting_date'])); }

//Sales Details
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql); $bird_code = "";
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql); $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_sales` WHERE `warehouse` = '$farm_code' AND `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
$query = mysqli_query($conn,$sql); $c = $ts_birds = $ts_weight = $ts_amt = 0; $s_date = $s_date = $s_vname = $s_weight = $s_rate = $s_amt = array();
while($row = mysqli_fetch_assoc($query)){
    if($row['icode'] == $bird_code){
        $c++;
        $s_date[$c] = date("d.m.Y",strtotime($row['date']));
        $s_vname[$c] = $ven_name[$row['vcode']];
        $s_birds[$c] = number_format_ind(round((float)$row['birds'],2));
        $s_weight[$c] = number_format_ind(round(((float)$row['rcd_qty'] + (float)$row['fre_qty']),2));
        $s_rate[$c] = number_format_ind(round((float)$row['rate'],2));
        $s_amt[$c] = number_format_ind(round((float)$row['item_tamt'],2));

        $ts_birds += (float)$row['birds'];
        $ts_weight += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
        $ts_amt += (float)$row['item_tamt'];
    }
}

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = ""; $feed_cat = array();
while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; $feename[$row['code']] = $row['description']; }
$feed_list = implode("','",$feed_cat); $feed_purchased_qty = $feed_transin_qty = 0;
$sql = "SELECT sum(rcd_qty) as rcd_qty,sum(fre_qty) as fre_qty FROM `broiler_purchases` WHERE `farm_batch` = '$batch_code' AND `icode` IN ('$feed_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_purchased_qty = $row['rcd_qty'] + $row['fre_qty']; }

$sql = "SELECT sum(quantity) as quantity FROM `item_stocktransfers` WHERE `to_batch` = '$batch_code' AND `code` IN ('$feed_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_transin_qty = $row['quantity']; }

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

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Farmer GC Print' AND `field_function` LIKE 'Display Chick Supplier Name' AND `user_access` LIKE 'all' AND `flag` = 1";
$query = mysqli_query($conn,$sql); $sup_name_flag = mysqli_num_rows($query);

if($sup_name_flag > 0){
    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vcode = $row['vcode']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$vcode' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vname = $row['name']; }
}
$sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $total_consumed_feed_qty = array();
while($row = mysqli_fetch_assoc($query)){
    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['mortality'];
    //$day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['culls'];
    $day_culls[$row['date']."@".$chick_code] = $day_culls[$row['date']."@".$chick_code] + $row['culls'];
    if($dstart_date == ""){ $dstart_date = $row['date']; } else{ if(strtotime($dstart_date) >= strtotime($row['date'])){ $dstart_date = $row['date']; } }
    if($dend_date == ""){ $dend_date = $row['date']; } else{ if(strtotime($dend_date) <= strtotime($row['date'])){ $dend_date = $row['date']; } }

}

$sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$region_code' AND `branch_code` = '$branch_code' AND `from_date` <= '$start_date' AND `to_date` >= '$start_date' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $madmin_prc = $tport_prc = $supr_sal_prc = $tada_prc = $ssb_prc = $oexp_prc = $ocredit_prc = 0;
while($row = mysqli_fetch_assoc($query)){
    $madmin_prc = round($row['mgmt_admin_cost'],5);
    $tport_prc = round($row['tport_prc'],5);
    $supr_sal_prc = round($row['supr_sal_prc'],5);
    $tada_prc = round($row['tada_prc'],5);
    $ssb_prc = round($row['ssb_prc'],5);
    $oexp_prc = round($row['oexp_prc'],5);
    $ocredit_prc = round($row['ocredit_prc'],5);
}
$madmin_amt = round(((float)$madmin_prc * (float)$sold_weight),2);
$tport_amt = round(((float)$tport_prc * (float)$sold_weight),2);
$supr_sal_amt = round(((float)$supr_sal_prc * (float)$sold_weight),2);
$tada_amt = round(((float)$tada_prc * (float)$sold_weight),2);
$ssb_amt = round(((float)$ssb_prc * (float)$sold_weight),2);
$oexp_amt = round(((float)$oexp_prc * (float)$sold_weight),2);
$ocredit_amt = round(((float)$ocredit_prc * (float)$sold_weight),2);

$grow_charge_exp_prc = $tport_bprc = $supr_sal_bprc = $tada_bprc = $ssb_bprc = $oexp_bprc = $ocredit_bprc = 0;
if((float)$sold_birds != 0){
    $madmin_bprc = round(((float)$madmin_amt / (float)$sold_birds),2);
    $grow_charge_exp_bprc = round(((float)$grow_charge_exp_amt / (float)$sold_birds),2);
    $tport_bprc = round(((float)$tport_amt / (float)$sold_birds),2);
    $supr_sal_bprc = round(((float)$supr_sal_amt / (float)$sold_birds),2);
    $tada_bprc = round(((float)$tada_amt / (float)$sold_birds),2);
    $ssb_bprc = round(((float)$ssb_amt / (float)$sold_birds),2);
    $oexp_bprc = round(((float)$oexp_amt / (float)$sold_birds),2);
    $ocredit_bprc = round(((float)$ocredit_amt / (float)$sold_birds),2);
}

// $sql = "SELECT * FROM `item_stocktransfers` WHERE `to_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"
// $query = mysqli_query($conn,$sql);
// while($row = mysqli_fetch_assoc($query)){
//     $key = $row['date']."@".$row['code']."@".$row['quantity']."@".$row['']
//     $trfin[]
// }

$html = "";
$html .= '<table style="border:1px solid black;">';
$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:560px;text-align:center;border-top:1px solid black;color:green;"><b>MANAGEMENT GC</b></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:560px;text-align:left;border-top:1px solid black;color:red;">FEED DISPATCH</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Date</th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Transaction</th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Feed</th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Qty</th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Rate</th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Amount</th>';
$html .= '<th style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">DcNo</th>';
$html .= '</tr>';

$totalQty = $rate = $tamount = 0;
$sql = "SELECT * FROM `item_stocktransfers` WHERE `to_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
   // $key = $row['date']."@".$row['code']."@".$row['quantity']."@".$row['']
   $totalQty += (float)$row['quantity'];
   $tamount += (float)$row['amount'];
    $html .= '<tr style="line-height: 2;">';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$row['date'].'</th>';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">Tfr IN</th>';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$feename[$row['code']].'</th>';
    $html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($row['quantity'],2)).'</th>';
    $html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($row['price'],2)).'</th>';
    $html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($row['amount'],2)).'</th>';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$row['dcno'].'</th>';
    $html .= '</tr>';
    
}
$sql = "SELECT * FROM `item_stocktransfers` WHERE `from_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $totalQty -= (float)$row['quantity'];
    $tamount -= (float)$row['amount'];
   // $key = $row['date']."@".$row['code']."@".$row['quantity']."@".$row['']
    $html .= '<tr style="line-height: 2;">';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$row['date'].'</th>';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">Tfr OUT</th>';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$row['code'].'</th>';
    $html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($row['quantity'],2)).'</th>';
    $html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($row['price'],2)).'</th>';
    $html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($row['amount'],2)).'</th>';
    $html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$row['dcno'].'</th>';
    $html .= '</tr>';
    
}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:80px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($totalQty,2)).'</th>';
$html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '<th style="width:80px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tamount,2)).'</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:560px;text-align:left;border-top:1px solid black;color:red;">INTERNAL COST SHEET</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2;">';
$html .= '<th rowspan="2" style="width:80px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Cost Center</th>';
$html .= '<th rowspan="2" style="width:115px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Qty</th>';
$html .= '<th rowspan="2" style="width:45px;text-align:center;border-top:1px solid black;border-right:1px solid black;">@</th>';
$html .= '<th colspan="3" style="width:320px;text-align:center;border-top:1px solid black;border-right:1px solid black;">COMPANY</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:106px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Amt</th>';
$html .= '<th style="width:106px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Per Kg</th>';
$html .= '<th style="width:108px;text-align:center;border-top:1px solid black;border-right:1px solid black;">Per Bird</th>';
$html .= '</tr>';

if((float)$placed_birds != 0){ $t1 = (float)$actual_chick_cost / (float)$placed_birds; } else{ $t1 = 0; }
$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">Input Chick Cost</th>';
$html .= '<th style="width:115px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.str_replace(".00","",number_format_ind(round($placed_birds,2))).'</th>';
$html .= '<th style="width:45px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($t1,2)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($actual_chick_cost,2)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($actual_chick_cost / $sold_weight,2)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($t1,2)).'</th>';
$html .= '</tr>';

if((float)$feed_consume_kgs != 0){ $t1 = (float)$actual_feed_cost / (float)$feed_consume_kgs; } else{ $t1 = 0; }
if((float)$sold_weight != 0){ $t2 = (float)$actual_feed_cost / (float)$sold_weight; } else{ $t2 = 0; }
if((float)$sold_birds != 0){ $t3 = (float)$actual_feed_cost / (float)$sold_birds; } else{ $t3 = 0; }
$html .= '<tr style="line-height: 2;">';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">Input Feed Cost</th>';
$html .= '<th style="width:115px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($feed_consume_kgs,2)).')</th>';
$html .= '<th style="width:45px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($t1,precision: 2)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($actual_feed_cost,2)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($t2,2)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($t3,2)).'</th>';
$html .= '</tr>';
if($tport_prc == "" || $tport_prc == 0){ $tport_prc1 = 1.5;} else { $tport_prc1 = $tport_prc;}
if($tport_amt == "" || $tport_amt == 0){ $tport_amt1 = 1.5 * (float)$ts_weight;} else { $tport_amt1 = $tport_amt;}
if($ts_birds != "" || $ts_birds != 0){ $tport_bprc1 = (float)$tport_amt1 / (float)$ts_birds;} else { $tport_bprc1 = $tport_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;">Transport (default @ '.number_format_ind(round($tport_prc1,5)).')</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tport_amt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tport_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tport_bprc1,5)).'</th>';
$html .= '</tr>';
if($grow_charge_exp_prc == "" || $grow_charge_exp_prc == 0){ $grow_charge_exp_prc1 = 8.88;} else { $grow_charge_exp_prc1 = $grow_charge_exp_prc;}
$ftotal = $medicine_cost_amt + $standard_gc_amt + $feed_cost_amt + $chick_cost_amt + $sales_incentive_amt + $summer_incentive_amt;
if($ts_birds != "" || $ts_birds != 0){ $grow_charge_exp_bprc1 = (float)$ftotal / (float)$ts_birds;} else { $grow_charge_exp_bprc1 = $grow_charge_exp_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;">Input Growing Charges @ '.number_format_ind(round($grow_charge_exp_prc1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ftotal,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($grow_charge_exp_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($grow_charge_exp_bprc1,5)).'</th>';
$html .= '</tr>';
if($supr_sal_prc == "" || $supr_sal_prc == 0){ $supr_sal_prc1 = 0.4;} else { $supr_sal_prc1 = $supr_sal_prc;}
if($supr_sal_amt == "" || $supr_sal_amt == 0){ $supr_sal_amt1 = 0.4 * (float)$ts_weight;} else { $supr_sal_amt1 = $supr_sal_amt;}
if($ts_birds != "" || $ts_birds != 0){ $supr_sal_bprc1 = (float)$supr_sal_amt1 / (float)$ts_birds;} else { $supr_sal_bprc1 = $supr_sal_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;">Supervisor Salary @ '.number_format_ind(round($supr_sal_prc1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($supr_sal_amt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($supr_sal_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($supr_sal_bprc1,5)).'</th>';
$html .= '</tr>';
if($tada_prc == "" || $tada_prc == 0){ $tada_prc1 = 0.3;} else { $tada_prc1 = $tada_prc;}
if($tada_amt == "" || $tada_amt == 0){ $tada_amt1 = 0.3 * (float)$ts_weight;} else { $tada_amt1 = $tada_amt;}
if($ts_birds != "" || $ts_birds != 0){ $tada_bprc1 = (float)$tada_amt1 / (float)$ts_birds;} else { $tada_bprc1 = $tada_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;">TADA @ '.number_format_ind(round($tada_prc1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tada_amt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tada_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tada_bprc1,5)).'</th>';
$html .= '</tr>';
if($ssb_prc == "" || $ssb_prc == 0){ $ssb_prc1 = 1.5;} else { $ssb_prc1 = $ssb_prc;}
if($ssb_amt == "" || $ssb_amt == 0){ $ssb_amt1 = 1.5 * (float)$ts_weight;} else { $ssb_amt1 = $ssb_amt;}
if($ts_birds != "" || $ts_birds != 0){ $ssb_bprc1 = (float)$ssb_amt1 / (float)$ts_birds;} else { $ssb_bprc1 = $ssb_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;">SSB Charges @ '.number_format_ind(round($ssb_prc1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ssb_amt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ssb_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ssb_bprc1,5)).'</th>';
$html .= '</tr>';
if($madmin_prc == "" || $madmin_prc == 0){ $madmin_prc1 = 2.2;} else { $madmin_prc1 = $madmin_prc;}
if($madmin_amt == "" || $madmin_amt == 0){ $madmin_amt1 = 2.2 * (float)$ts_weight;} else { $madmin_amt1 = $madmin_amt;}
if($ts_birds != "" || $ts_birds != 0){ $madmin_bprc1 = (float)$madmin_amt1 / (float)$ts_birds;} else { $madmin_bprc1 = $madmin_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;">Admin & Other Charges @ '.number_format_ind(round($madmin_prc1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($madmin_amt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($madmin_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($madmin_bprc1,5)).'</th>';
$html .= '</tr>';

// $html .= '<tr style="line-height: 2;">';
// $html .= '<th colspan="3" style="width:240px;border-top:1px solid black;border-right:1px solid black;">Input Other Expenses </th>';
// $html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($oexp_amt,5)).'</th>';
// $html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($oexp_prc,5)).'</th>';
// $html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($oexp_bprc,5)).'</th>';
// $html .= '</tr>';

// $html .= '<tr style="line-height: 2;">';
// $html .= '<th colspan="3" style="width:240px;border-top:1px solid black;border-right:1px solid black;">Input Other Credits </th>';
// $html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ocredit_amt,5)).'</th>';
// $html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ocredit_prc,5)).'</th>';
// $html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($ocredit_bprc,5)).'</th>';
// $html .= '</tr>';

$tot_input_amt = 0;
$tot_input_amt = (float)$actual_chick_cost + (float)$actual_feed_cost + (float)$tport_amt + (float)$grow_charge_exp_amt + (float)$supr_sal_amt + (float)$tada_amt + (float)$ssb_amt + (float)$madmin_amt + (float)$oexp_amt + (float)$ocredit_amt;

if((float)$sold_weight != 0){ $tot_input_prc = (float)$tot_input_amt / (float)$sold_weight; } else{ $t1 = 0; }
if((float)$sold_birds != 0){ $tot_input_bprc = (float)$tot_input_amt / (float)$sold_birds; } else{ $t1 = 0; }

 $tot_input_prc1 = 89.3;
 $tot_input_amt1 = $actual_chick_cost + $actual_feed_cost + $tport_amt1 + $ftotal + $supr_sal_amt1 + $tada_amt1 + $ssb_amt1 + $madmin_amt1 ;
if($ts_birds != "" || $ts_birds != 0){ $tot_input_bprc1 = (float)$tot_input_amt1 / (float)$ts_birds;} else { $tot_input_bprc1 = $tot_input_bprc;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;"><b>TOTAL INPUT COST</b> </th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tot_input_amt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tot_input_prc1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($tot_input_bprc1,5)).'</th>';
$html .= '</tr>';

if((float)$sold_weight != 0){ $sale_wrate = (float)$sale_amount / (float)$sold_weight; } else{ $t1 = 0; }
if((float)$sold_birds != 0){ $sale_brate = (float)$sale_amount / (float)$sold_birds; } else{ $t1 = 0; }

 $sale_wrate1 = 77.59;
 $sale_amount1 = $ts_amt;
if($ts_birds != "" || $ts_birds != 0){ $sale_brate1 = (float)$sale_amount1 / (float)$ts_birds;} else { $sale_brate1 = $sale_brate;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;"><b>Sales Amt</b> </th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round(($sale_amount1),2)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($sale_wrate1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($sale_brate1,5)).'</th>';
$html .= '</tr>';

$pal_wamt = ((float)$sale_amount - (float)$tot_input_amt);
if((float)$sold_weight != 0){ $pal_wrate = (float)$pal_wamt / (float)$sold_weight; } else{ $t1 = 0; }
if((float)$sold_birds != 0){ $pal_brate = (float)$pal_wamt / (float)$sold_birds; } else{ $t1 = 0; }

 $pal_wrate1 = -11.72;
 $pal_wamt1 = $sale_amount1 - $tot_input_amt1;
if($ts_birds != "" || $ts_birds != 0){ $pal_brate1 = (float)$pal_wamt1 / (float)$ts_birds;} else { $pal_brate1 = $pal_brate;}
$html .= '<tr style="line-height: 2;">';
$html .= '<th colspan="3" style="width:240px;text-align:right;border-top:1px solid black;border-right:1px solid black;"><b>P&L</b> </th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($pal_wamt1,5)).'</th>';
$html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($pal_wrate1,5)).'</th>';
$html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($pal_brate1,5)).'</th>';
$html .= '</tr>';

$oth_iwoc_amt = (float)$tot_input_amt - (float)$ocredit_amt;
if((float)$sold_weight != 0){ $oiwoc_wrate = (float)$oth_iwoc_amt / (float)$sold_weight; } else{ $t1 = 0; }
if((float)$sold_birds != 0){ $oiwoc_brate = (float)$oth_iwoc_amt / (float)$sold_birds; } else{ $t1 = 0; }
// $html .= '<tr style="line-height: 2;">';
// $html .= '<th colspan="3" style="width:346px;border-top:1px solid black;border-right:1px solid black;"><b>INPUT COST W/O CREDIT AMT</b> </th>';
// $html .= '<th style="width:106px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($oiwoc_wrate,5)).'</th>';
// $html .= '<th style="width:108px;text-align:right;border-top:1px solid black;border-right:1px solid black;">'.number_format_ind(round($oiwoc_brate,5)).'</th>';
// $html .= '</tr>';




$html .= '</table>';



//echo $html;
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
//$fontname = $this->pdf->addTTFfont('font-family/MAIAN.TTF', 'TrueTypeUnicode', '', 32);
$fontname = TCPDF_FONTS::addTTFfont('font-family/MAIAN.TTF', 'TrueType', '', 32);
$pdf->SetFont($fontname, '', 10, '', true); //dejavusans
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(5, 5, 5, true);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>