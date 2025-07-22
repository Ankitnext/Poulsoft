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
    $start_date = date("d.m.Y",strtotime($row['start_date']));
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
    $summer_incentive_amt = $row['summer_incentive_amt'];
}
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Farm RC' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
}
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description']; $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farmer_code[$row['code']] = $row['farmer_code']; $supervisor_code[$row['code']] = $row['supervisor_code']; $area_name[$row['code']] = $row['area_name']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$bh_code = $farm_branch[$farm_code]; $gc_date = date("Y-m-d",strtotime($date));

$sup_code = $supervisor_code[$farm_code];
$sql = "SELECT * FROM `broiler_employee` WHERE `code` = '$sup_code' AND `active` = '1' "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_name = $row['name']; }

$sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mortality = $row['mortality']; $culls = $row['culls']; }

$sql = "SELECT MAX(date) as final_lifting_date FROM `broiler_sales` WHERE `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $final_lifting_date = date("d.m.Y",strtotime($row['final_lifting_date'])); }


$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = ""; $feed_cat = array();
while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; }
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
$fdate = strtotime($dstart_date); $tdate = strtotime($dend_date); $days = $sold_mean_total = $bird_sold_amt = 
$week_1mortcnt = $week_2mortcnt = $week_3mortcnt = $week_4mortcnt = $week_5mortcnt = $week_6mortcnt = $week_7mortcnt = 0;
$week_1cullcnt = $week_2cullcnt = $week_3cullcnt = $week_4cullcnt = $week_5cullcnt = $week_6cullcnt = $week_7cullcnt = 0;
for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
    $days++;
    $present_date = date("Y-m-d",$currentDate);
    $mort_total = $mort_total + $day_mort[$present_date."@".$chick_code] ;
    $cull_total = $cull_total + $day_culls[$present_date."@".$chick_code];

    if($days <= 7){ $week_1mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 7 && $days <= 14){ $week_2mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 14 && $days <= 21){ $week_3mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 21 && $days <= 28){ $week_4mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 28 && $days <= 35){ $week_5mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 35 && $days <= 42){ $week_6mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 42 && $days <= 49){ $week_7mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }

    if($days <= 7){ $week_1cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
    if($days > 7 && $days <= 14){ $week_2cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
    if($days > 14 && $days <= 21){ $week_3cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
    if($days > 21 && $days <= 28){ $week_4cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
    if($days > 28 && $days <= 35){ $week_5cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
    if($days > 35 && $days <= 42){ $week_6cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
    if($days > 42 && $days <= 49){ $week_7cullcnt += (float)$day_culls[$present_date."@".$chick_code]; }
}
if((float)$placed_birds != 0){
    $week_1mortper = round(((((float)$week_1mortcnt + (float)$week_1cullcnt) / (float)$placed_birds) * 100),2);
    $week_2mortper = round(((((float)$week_2mortcnt + (float)$week_2cullcnt) / (float)$placed_birds) * 100),2);
    $week_3mortper = round(((((float)$week_3mortcnt + (float)$week_3cullcnt) / (float)$placed_birds) * 100),2);
    $week_4mortper = round(((((float)$week_4mortcnt + (float)$week_4cullcnt) / (float)$placed_birds) * 100),2);
    $week_5mortper = round(((((float)$week_5mortcnt + (float)$week_5cullcnt) / (float)$placed_birds) * 100),2);
    $week_6mortper = round(((((float)$week_6mortcnt + (float)$week_6cullcnt) / (float)$placed_birds) * 100),2);
    $week_7mortper = round(((((float)$week_7mortcnt + (float)$week_7cullcnt) / (float)$placed_birds) * 100),2);
    $total_mortper = round(((((float)$mort_total + (float)$cull_total ) / (float)$placed_birds) * 100),2);
}
else{
    $week_1mortper = $week_2mortper = $week_3mortper = $week_4mortper = $week_5mortper = $week_6mortper = $week_7mortper = $total_mortper = 0;
}
$html = "";

$html .= '<table align="center" style="border: 1px solid black;">';
$html .= '<tr style="line-height:6px;padding:0;margin:0;">';
$html .= '<th style="padding:0;margin:0;width:560px;text-align:center;border-top: 1px solid black;">';
$html .= '<br/><br/><img src="../../'.$img_path.'" height="70px" />';
$html .= '</th>';
$html .= '</tr>';
//$html .= '<tr style="line-height:10px;padding:0;margin:0;">';
//html .= '<th style="padding:0;margin:0;width:560px;text-align:center;border-bottom: 1px solid black;">';
//$html .= '<p align="center">'.$cdetail.'</p>';
//$html .= '</th>';
//$html .= '</tr>';
$html .= '<tr style="line-height:20px;">';
$html .= '<th style="width:560px;text-align:center;border-top: 1px solid black;border-bottom: 1px solid black;">';
$html .= '<b style="font-size:14px;text-align:center;color:red;">FARMER GROWING CHARGES - GC</b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:186px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="text-align:left;">Branch Name: </b>'.$branch_name[$farm_branch[$farm_code]].'</th>';
$html .= '<th style="width:186px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="text-align:left;">Line: </b>'.$line_name[$farm_line[$farm_code]].'</th>';
$html .= '<th style="width:188px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="text-align:left;">&nbsp;Supervisor : </b>'.$supervisor_name.'</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:186px;text-align:left;border-right: 1px solid black;"><b style="text-align:left;">Farm Code  : </b>'.$farm_ccode[$farm_code].'</th>';
$html .= '<th style="width:186px;text-align:left;border-right: 1px solid black;"><b style="text-align:left;">Flock No.: </b>'.$batch_name[$batch_code].'</th>';
$html .= '<th style="width:188px;text-align:left;border-right: 1px solid black;"><b style="text-align:left;">&nbsp;Grade : </b>'.$grade.'</th>';
$html .= '</tr>';
if($sup_name_flag > 0){
    $html .= '<tr style="line-height:20px;">';
    $html .= '<th style="width:560px;text-align:left;border-top: 1px solid black;border-bottom: 1px solid black;">';
    $html .= '<b style="font-size:11px;text-align:left;color:red;">Hatchery Name: </b><b style="font-size:10px;text-align:left;color:black;">'.$vname.'</b>';
    $html .= '</th>';
    $html .= '</tr>';
}
$html .= '<tr>';
$html .= '<th style="width:267px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;border-top: 1px solid black;"><u>Farm Details</u></b>';
$html .= '</th>';
$html .= '<th style="width:293px;padding-left: 10px;text-align:left;border-left: 1px solid black;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;border-top: 1px solid black;"><u>Farmer Account Details & Kyc Details</u></b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:80px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Farm Name: </b>';
$html .= '</th>';
$html .= '<td style="width:187px;padding-right: 10px;text-align:right;color:green;">'.$farm_name[$farm_code].'</td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Acc Holder Name: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-right: 10px;text-align:right;color:green;">'.$fmr_name.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:95px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Farmer Name: </b>';
$html .= '</th>';
$html .= '<td style="width:172px;padding-right: 10px;text-align:right;color:green;">'.$fmr_name.'</td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Account Number: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-right: 10px;text-align:right;color:green;">'.$fmr_accountno.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:140px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Chicks Placement Date: </b>';
$html .= '</th>';
$html .= '<td style="width:127px;padding-right: 10px;text-align:right;color:green;">'.$start_date.'</td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">IFSC Code: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-right: 10px;text-align:right;color:green;">'.$fmr_ifsc_code.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:140px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Final Lifting Date: </b>';
$html .= '</th>';
$html .= '<td style="width:127px;padding-right: 10px;text-align:right;color:green;">'.$final_lifting_date.'</td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Branch Name: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-right: 10px;text-align:right;color:green;">'.$fmr_branch_code.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:127px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Final Liquidation Date: </b>';
$html .= '</th>';
$html .= '<td style="width:140px;padding-left: 10px;text-align:right;color:green;">'.$liquid_date.'</td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Bank Name: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-left: 10px;text-align:right;color:green;">'.$fmr_bank_name.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td style="padding-right: 10px;text-align:right;color:green;"></td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Pan Card No.: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-left: 10px;text-align:right;color:green;">'.$fmr_panno.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td style="padding-right: 10px;text-align:right;color:green;"></td>';
$html .= '<th style="width:105px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Aadhar No.: </b>';
$html .= '</th>';
$html .= '<td style="width:193px;padding-left: 10px;text-align:right;color:green;">'.$fmr_aadharno.'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:267px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Batch Information</u></b>';
$html .= '</th>';
$html .= '<th style="width:293px;padding-left: 10px;text-align:left;border-top: 1px solid black;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Batch Performance</u></b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:140px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Housed Chicks: </b>';
$html .= '</th>';
$html .= '<td style="width:127px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($placed_birds)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">FCR: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round($fcr,3)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Mortality: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.str_replace(".00","",number_format_ind($mortality + $culls)).'('.$total_mort.'%)</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">CFCR: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round($cfcr,3)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Sold Birds: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.str_replace(".00","",number_format_ind($sold_birds)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Avg Body Weight: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round($avg_wt,3)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Excess Birds: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.$excess.'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Mean Age: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($mean_age,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Shortage Birds: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.$shortage.'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Day Gain: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($day_gain,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Sold Weight (Kgs): </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($sold_weight,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">E E F: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($eef,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Age in Days: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($age,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Avg Sale Rate: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($sale_rate,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:267px;text-align:center;border-right: 1px solid black;"></th>';
$html .= '<th style="width:293px;text-align:center;"></th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:560px;text-align:center;border-top: 1px solid black;"><b style="color:red;font-size:13px;">Week Wise Mortality Details</b></th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:64px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">Week:</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">1st Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">2nd Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">3rd Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">4th Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">5th Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">6th Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">7th Week</b></th>';
$html .= '<th style="width:62px;text-align:center;border-top: 1px solid black;"><b style="color:green;">Total</b></th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:64px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">Mortality:</b></th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_1mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_2mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_3mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_4mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_5mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_6mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_7mortcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;">'.str_replace(".00","",number_format_ind(round($mort_total,2))).'</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:64px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">Culls:</b></th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_1cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_2cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_3cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_4cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_5cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_6cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($week_7cullcnt,2))).'</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;">'.str_replace(".00","",number_format_ind(round($cull_total,2))).'</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:64px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">Mort %:</b></th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_1mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_2mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_3mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_4mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_5mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_6mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.number_format_ind(round($week_7mortper,2)).'%</th>';
$html .= '<th style="width:62px;text-align:right;border-top: 1px solid black;">'.number_format_ind(round($total_mortper,2)).'%</th>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:267px;text-align:center;border-right: 1px solid black;border-top: 1px solid black;"></th>';
$html .= '<th style="width:293px;text-align:center;border-top: 1px solid black;"></th>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Feed Details</u></b>';
$html .= '</th>';
$html .= '<th style="width:53px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Bags</u></b>';
$html .= '</th>';
$html .= '<th style="width:70px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Kgs</u></b>';
$html .= '</th>';
$html .= '<th style="width:185px;padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Calculation of Growing Charge</u></b>';
$html .= '</th>';
$html .= '<th style="width:43px;padding-right: 10px;text-align:right;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Per Kg</u></b>';
$html .= '</th>';
$html .= '<th style="width:71px;padding-right: 10px;text-align:right;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Amount&nbsp;</u></b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Purchased: </b>';
$html .= '</th>';
$html .= '<td style="width:53px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_purchased_qty / 50),2)).'</td>';
$html .= '<td style="width:70px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_purchased_qty,2)).'</td>';
$html .= '<td style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Rearing Charges:</b></td>';
$html .= '<td style="width:38px;padding-right: 10px;text-align:right;color:green;">'.(round(($standard_gc_prc),3)).'</td>';
$html .= '<td style="width:71px;padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round(($standard_gc_amt),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed In: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_transin_qty / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_transin_qty,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Sales Rate Incentive Per Kg: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round(($sales_incentive_prc),3)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($sales_incentive_amt),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Total Feed In: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round((($feed_purchased_qty + $feed_transin_qty) / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_purchased_qty + $feed_transin_qty),2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Medicine Charges: </b></th>';
$html .= '<td style="padding-right: 10px;text-align:right;color:green;">'.$medicine_cost_unit.'</td>';
$html .= '<td style="padding-right: 10px;text-align:right;color:green;">'.round($medicine_cost_amt,3).'&nbsp;</td>';
$html .= '</tr>';


$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Out: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_out_kgs / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_out_kgs,2)).'</td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Feed Charges: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_cost_amt,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Consumed:</b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_consume_kgs / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_consume_kgs,2)).'</td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Chicks Amount: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($chick_cost_amt,2)).'&nbsp;</td>';
$html .= '</tr>';


$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;"></b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Summer Incentive: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>'; //'.number_format_ind(round(($total_incentives / $sold_weight),2)).'
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($summer_incentive_amt,2)).'&nbsp;</td>';
// $html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($total_incentives,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Batch Costing</u></b>';
$html .= '</th>';
$html .= '<th style="width:123px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Std.Cost</u></b>';
$html .= '</th>';
//$html .= '<th style="width:70px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
//$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Amount</u></b>';
//$html .= '</th>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">(Growing Charges)Total: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>'; //'.number_format_ind(round(($total_incentives / $sold_weight),2)).'
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($total_incentives,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Chicks Cost: </b></th>';
if((float)$placed_birds != 0){ $t1 = $chick_cost_amt / $placed_birds; } else{ $t1 = 0; }
$html .= '<td style="width:123px;padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round(($t1),2)).'</td>';
//$html .= '<td style="width:70px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($chick_cost_amt,2)).'</td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Other Charges: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($other_deduction,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Feed Cost: </b></th>';
if((float)$feed_consume_kgs != 0){ $t1 = $feed_cost_amt / $feed_consume_kgs; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round(($t1),2)).'</td>';
//$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_cost_amt,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Other Credits: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($other_incentive,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Admin Cost: </b></th>';
if((float)$placed_birds != 0){ $t1 = $admin_cost_amt / $placed_birds; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round(($t1),2)).'</td>';
//$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round($admin_cost_amt,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Farmer Sales Deductions: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($farmer_sale_deduction,2)).'&nbsp;</td>';
// $html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($birds_shortage,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Medicine Cost: </b></th>';
if((float)$sold_weight != 0){ $t1 = $medicine_cost_amt / $sold_weight; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round(($medicine_cost_unit),2)).'</td>';
//$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round($medicine_cost_amt,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Equipment Deductions: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($equipment_charges,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Actual Production Cost: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round($actual_prod_cost,2)).'</td>';
//$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round(($total_cost_amt),2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">TDS: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($tds_amt,2)).'&nbsp;</td>';
$html .= '</tr>';

if((float)$amount_payable != 0){ $t1 = 0; $t1 = round((($tds_amt /$amount_payable) * 100)); } else{ $t1 = 0; }
$ftotal = $medicine_cost_amt + $standard_gc_amt + $feed_cost_amt + $chick_cost_amt + $sales_incentive_amt + $summer_incentive_amt;
// Final Payemnt
(float)$payment_total = (float)$total_incentives - (float)$other_deduction - (float)$other_incentive - (float)$farmer_sale_deduction - (float)$equipment_charges - (float)$tds_amt; 

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Standard Production Cost: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round($standard_prod_cost,2)).'</td>';
//$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round(($standard_prod_cost * $sold_weight),2)).'</td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Final Payment Amt: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($payment_total,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th rowspan="4" style="width:267px;padding-left: 10px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Remarks: </u></b><br/>'.$remarks;
$html .= '</th>';
if((float)$sold_birds != 0){ $t1 = $farmer_payable / $sold_birds; } else{ $t1 = 0; }
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Per Bird: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($t1,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Per Kg Sold </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($ftotal - $other_deduction - $other_incentive - $farmer_sale_deduction - $equipment_charges - $tds_amt)/$sold_weight,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;"> </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;"> </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
//$html .= '<td style="width:140px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Prepared By</td>';
$html .= '<td style="width:180px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Checked By</td>';
$html .= '<td style="width:180px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Approved By</td>';
$html .= '<td style="width:200px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Paid By</td>';
$html .= '</tr>';
$html .= '</table>';

//echo $html;
$html .= '<div style="page-break-before:always"></div>';

$html .= '<table align="center" style="border: 1px solid black;">';

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
$html .= '<tr style="line-height:20px;">';
$html .= '<th style="width:560px;text-align:left;color:red;font-size:13px;font-weight:bold;border-top: 1px solid black;border-bottom: 1px solid black;">Bird Sales</th>';
$html .= '</tr>';
$html .= '<tr style="line-height:20px;text-align:center;color:green;">';
$html .= '<th style="width:73px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">Date</th>';
$html .= '<th style="width:185px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">Bird Dealer</th>';
$html .= '<th style="width:63px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">Birds</th>';
$html .= '<th style="width:73px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">Weight</th>';
$html .= '<th style="width:63px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">Rate</th>';
$html .= '<th style="width:103px;text-align:center;border-bottom: 1px solid black;">Amount</th>';
$html .= '</tr>';

$a = $c;
for($c = 1;$c <= $a;$c++){
    $html .= '<tr style="line-height:20px;">';
    $html .= '<td style="width:73px;text-align:left;border-right: 1px solid black;border-bottom: 1px solid black;">'.$s_date[$c].'</td>';
    $html .= '<td style="width:185px;text-align:left;border-right: 1px solid black;border-bottom: 1px solid black;">'.$s_vname[$c].'</td>';
    $html .= '<td style="width:63px;text-align:right;border-right: 1px solid black;border-bottom: 1px solid black;">'.$s_birds[$c].'</td>';
    $html .= '<td style="width:73px;text-align:right;border-right: 1px solid black;border-bottom: 1px solid black;">'.$s_weight[$c].'</td>';
    $html .= '<td style="width:63px;text-align:right;border-right: 1px solid black;border-bottom: 1px solid black;">'.$s_rate[$c].'</td>';
    $html .= '<td style="width:103px;text-align:right;border-bottom: 1px solid black;">'.$s_amt[$c].'</td>';
    $html .= '</tr>';
}
$avg_prc = 0; if((float)$ts_weight != 0){ $avg_prc = (float)$ts_amt / (float)$ts_weight; }
$html .= '<tr style="line-height:20px;">';
$html .= '<th style="width:258px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;"><b>Total</b></th>';
$html .= '<th style="width:63px;text-align:right;border-right: 1px solid black;border-bottom: 1px solid black;color:red;">'.number_format_ind(round($ts_birds,2)).'</th>';
$html .= '<th style="width:73px;text-align:right;border-right: 1px solid black;border-bottom: 1px solid black;color:red;">'.number_format_ind(round($ts_weight,2)).'</th>';
$html .= '<th style="width:63px;text-align:right;border-right: 1px solid black;border-bottom: 1px solid black;color:red;">'.number_format_ind(round($avg_prc,2)).'</th>';
$html .= '<th style="width:103px;text-align:right;border-bottom: 1px solid black;color:red;">'.number_format_ind(round($ts_amt,2)).'</th>';
$html .= '</tr>';

//Item CoA Accounts
$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; }
$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$chick_iac = $icat_iac[$icat_code[$chick_code]];
$sql = "SELECT MIN(date) as sdate,MAX(date) as edate FROM `account_summary` WHERE `batch` = '$batch_code' AND `item_code` = '$chick_code' AND `crdr` = 'DR' AND `coa_code` = '$chick_iac' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $sdate = $row['sdate']; $edate = $row['edate']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `code` LIKE '$farm_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code = $row['region_code']; }


$sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$region_code' AND `branch_code` = '$branch_code' AND `from_date` <= '$sdate' AND `to_date` >= '$edate' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $gc_code = $row['code'];
    $chick_cost = $row['chick_cost'];
    $feed_cost = $row['feed_cost'];
    $medicine_cost = $row['medicine_cost'];
    $med_price = $row['med_price'];
    $admin_cost = $row['admin_cost'];
    $standard_prod_cost = $row['standard_prod_cost'];
    $standard_cost = $row['standard_cost'];
    $minimum_cost = $row['minimum_cost'];
    $standard_fcr = $row['standard_fcr'];
    $standard_mortality = $row['standard_mortality'];
    $mgmt_admin_prc = $row['mgmt_admin_cost'];
}
$avg_wt = 0; if((float)$sold_birds != 0){ $avg_wt = round(((float)$sold_weight / (float)$sold_birds),3); }

$avg_wtgrms = 0; if($sold_birds > 0){ $avg_wtgrms = round((((float)$sold_weight / (float)$sold_birds) * 1000),2); }
$sql = "SELECT * FROM `broiler_gc_fcr_standards` WHERE `std_code` = '$gc_code' AND `fcrs_from_val` <= '$avg_wtgrms' AND `fcrs_to_val` >= '$avg_wtgrms'"; $query = mysqli_query($conn,$sql);
$standard_fcr = 0; while($row = mysqli_fetch_assoc($query)){ $standard_fcr = $row['std_rates']; } if($standard_fcr == ""){ $standard_fcr = 0; }

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:50px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">Avg Wt</th>';
$html .= '<th style="width:50px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">'.$avg_wt.'</th>';
$html .= '<th style="width:130px;text-align:center;border-right: 1px solid black;"></th>';
$html .= '<th style="width:50px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">FCR</th>';
$html .= '<th style="width:50px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">'.$fcr.'</th>';
$html .= '<th style="width:130px;text-align:center;border-right: 1px solid black;"></th>';
$html .= '<th style="width:50px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">STD</th>';
$html .= '<th style="width:50px;text-align:center;border-right: 1px solid black;border-bottom: 1px solid black;">'.$standard_fcr.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:100px;text-align:center;"><br/><br/><br/></th>';
$html .= '</tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:180px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($medicine_cost_unit,2)).'<hr style="width:40px;">(Rate)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> x </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.str_replace(".00","",number_format_ind(round($placed_birds,2))).'<hr style="width:40px;">(birds)</th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">Medicine<br/>Charges</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($medicine_cost_amt,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:180px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($standard_gc_prc,2)).'<hr style="width:40px;">(Rate)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> x </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($sold_weight,2)).'<hr style="width:40px;">(Kg Sold)</th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">Rearing<br/>Charges</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($standard_gc_amt,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';

$std_feed_consumed_qty = $fmr_total_feed_consumed_amt = 0;
$std_feed_consumed_qty = round(((float)$standard_fcr * (float)$sold_weight),2);
$diff_feed_cons_qty = ((float)$std_feed_consumed_qty - (float)$total_feed_consumed_qty);
$fmr_total_feed_consumed_amt = (float)$diff_feed_cons_qty * (float)$feed_cost;
$fmr_total_feed_consumed_prc = (float)$feed_cost;

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($std_feed_consumed_qty,2)).'<hr style="width:40px;">(STD Feed)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($feed_consume_kgs,2)).'<hr style="width:40px;">(Act. Feed)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($diff_feed_cons_qty,2)).'<hr style="width:40px;">(Diff)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> x </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($feed_cost,2)).'<hr style="width:40px;">(Act. Feed)</th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">Feed<br/>Charges</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($feed_cost_amt,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';

//Actual Chicks after 5% Deductions
$base_birds = 0; $base_birds = round(((float)$placed_birds / 105 * 100));
$diff_birds = ((float)$sold_birds - (float)$base_birds);
$fmr_stkin_chick_amt = (float)$diff_birds * (float)$chick_cost;
$fmr_stkin_chick_prc = (float)$chick_cost;

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($base_birds,2)).'<hr style="width:40px;">(Birds - 5%)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($sold_birds,2)).'<hr style="width:40px;">(Bird Sold)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($diff_birds,2)).'<hr style="width:40px;">(Diff)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> x </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($chick_cost,2)).'<hr style="width:40px;">(Rate)</th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">Chicks<br/>Amount</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($chick_cost_amt,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:180px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($sales_incentive_prc,2)).'<hr style="width:40px;">(Rate Inc)</th>';
$html .= '<th style="width:20px;text-align:center;border-bottom: 1px solid gray;"> x </th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">'.number_format_ind(round($sale_rate,2)).'<hr style="width:40px;">(Rate Recd)</th>';
$html .= '<th style="width:60px;text-align:center;border-bottom: 1px solid gray;">Rate<br/>Incentive</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($sales_incentive_amt,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:150px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:40px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:15px;text-align:center;border-bottom: 1px solid gray;">  </th>';
$html .= '<th style="width:55px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:120px;text-align:center;border-bottom: 1px solid gray;">Summer Incentive</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($summer_incentive_amt,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';
// $ftotal = $medicine_cost_amt + $standard_gc_amt + $feed_cost_amt + $chick_cost_amt + $sales_incentive_amt + $summer_incentive_amt;
$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:150px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:40px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:15px;text-align:center;border-bottom: 1px solid gray;">  </th>';
$html .= '<th style="width:55px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:120px;text-align:center;border-bottom: 1px solid gray;">(Growing Charges)Total</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round(($ftotal),2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';



$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:150px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:40px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:15px;text-align:center;border-bottom: 1px solid gray;">  </th>';
$html .= '<th style="width:55px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:120px;text-align:center;border-bottom: 1px solid gray;">Other Charges</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($other_deduction,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:150px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:40px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:15px;text-align:center;border-bottom: 1px solid gray;">  </th>';
$html .= '<th style="width:55px;text-align:center;border-bottom: 1px solid gray;"></th>';
$html .= '<th style="width:120px;text-align:center;border-bottom: 1px solid gray;">Other Credits</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($other_incentive,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"></th></tr>';
//$remarks = "This is Poulsoft, for testing Software working or not. Looks like tesgting is working fine.";
$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:280px;text-align:center;" rowspan="3">Charges & Credit Details<br/>
<table style="border:1px solid gray;">
<tr style="line-height:15px;">
<td style="height:50px; vertical-align: top;">'.$remarks.'<br/></td>
</tr>
</table>
</th>';
$html .= '<th style="width:100px;text-align:right;border-bottom: 1px solid gray;">Farmer Sale Deduction</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($farmer_sale_deduction,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:100px;text-align:right;border-bottom: 1px solid gray;">Equipment Deduction</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($equipment_charges,2)).'</th>';
$html .= '</tr>';

// $html .= '<tr style="line-height:15px;">';
// $html .= '<th style="width:100px;text-align:right;border-bottom: 1px solid gray;">Total</th>';
// $html .= '<th style="width:20px;text-align:center;"> = </th>';
// $html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($total_amount_payable,2)).'</th>';
// $html .= '</tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:100px;text-align:right;border-bottom: 1px solid gray;">TDS</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;">'.number_format_ind(round($tds_amt,2)).'</th>';
$html .= '</tr>';


$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:280px;text-align:center;"></th>';
$html .= '<th style="width:100px;text-align:right;border-bottom: 1px solid gray;">Final Payment Amt</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;border-top: 1px solid gray;">'.number_format_ind(round($ftotal - $other_deduction - $other_incentive - $farmer_sale_deduction - $equipment_charges - $tds_amt ,2)).'</th>';
$html .= '</tr>';

$per_bird_prc = 0; if((float)$sold_birds != 0){ $per_bird_prc = (float)$farmer_payable / (float)$sold_birds; }
$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:280px;text-align:center;"></th>';
$html .= '<th style="width:100px;text-align:right;">Per Bird</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;border-top: 1px solid gray;">'.number_format_ind(round($per_bird_prc,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:15px;">';
$html .= '<th style="width:280px;text-align:center;"></th>';
$html .= '<th style="width:100px;text-align:right;">Per Kg Sold</th>';
$html .= '<th style="width:20px;text-align:center;"> = </th>';
$html .= '<th style="width:60px;text-align:right;border-top: 1px solid gray;">'.number_format_ind(round(($ftotal - $other_deduction - $other_incentive - $farmer_sale_deduction - $equipment_charges - $tds_amt)/$sold_weight,2)).'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height:10px;"><th style="width:100px;text-align:center;"><br/><br/><br/></th></tr>';

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