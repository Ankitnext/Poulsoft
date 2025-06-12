<?php
//farmerrearingchargeprint_paras.php
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
    $accidental_mort_birds = round($row['accidental_mort_birds'],5); if($accidental_mort_birds == ""){ $accidental_mort_birds = 0; }
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
    $high_feedin_sup_code = $row['high_feedin_sup_code'];
    $high_feedin_brand_code = $row['high_feedin_brand_code'];
    $high_chickin_secvcode = $row['high_chickin_secvcode'];
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
    $birds_shortage_qty = round($row['birds_shortage_qty'],5); if($birds_shortage_qty == ""){ $birds_shortage_qty = 0; }
    $birds_shortage_prc = $row['birds_shortage_prc']; if($birds_shortage_prc == ""){ $birds_shortage_prc = 0; }
    $birds_shortage = $row['birds_shortage']; if($birds_shortage == ""){ $birds_shortage = 0; }
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

if($high_chickin_secvcode == ""){
    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $vcode = $row['vcode']; }
}
else{
    $vcode = $high_chickin_secvcode;
}

$sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$vcode' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vname = $row['name']; }

//Brand Wise Quantity Consumed details
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Feed%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $feed_cat = array(); while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; }

$feed_list = implode("','",$feed_cat);
$sql = "SELECT * FROM `item_details` WHERE `category` IN('$feed_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $feed_code = array(); while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; } $feed_list = implode("','",$feed_code);

$sql = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` LIKE '%WIP-%' AND `item_code` IN ('$feed_list') AND `batch` LIKE '$batch_code' AND `active` = '1' AND `dflag` = '0' GROUP BY `item_code` ORDER BY `item_code` ASC";
$query = mysqli_query($conn,$sql); $hstkcon_brand_itm = $hstkcon_brand_qty = $hstkcon_brand_amt = array();
while($row = mysqli_fetch_assoc($query)){
    $hstkcon_brand_itm[$row['item_code']] = $row['item_code'];
    $hstkcon_brand_qty[$row['item_code']] += (float)$row['quantity'];
    $hstkcon_brand_amt[$row['item_code']] += (float)$row['amount'];
}

//$sql = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` LIKE '%STK-%' AND `item_code` IN ('$feed_list') AND `batch` LIKE '$batch_code' AND `active` = '1' AND `dflag` = '0' GROUP BY `item_code` ORDER BY `item_code` ASC";
//$query = mysqli_query($conn,$sql); $hstkin_brand_qty = 0; while($row = mysqli_fetch_assoc($query)){ $hstkin_brand_qty += (float)$row['quantity']; }

$item_list = ""; $item_list = implode("','",$hstkcon_brand_itm);
$sql = "SELECT * FROM `broiler_link_itembrand` WHERE `item_code` IN ('$item_list') AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $brand_arr_code = array();
while($row = mysqli_fetch_assoc($query)){ $brand_arr_code[$row['item_code']] = $row['brand_code']; }

$brand_list = ""; $brand_list = implode("','",$brand_arr_code);
$sql = "SELECT * FROM `broiler_item_brands` WHERE `code` IN ('$brand_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $brand_code = $brand_name = array();
while($row = mysqli_fetch_assoc($query)){ $brand_code[$row['code']] = $row['code']; $brand_name[$row['code']] = $row['description']; }

$brand_qty = array(); $tbrandcon_qty = 0;
foreach($hstkcon_brand_itm as $icode){
    $bicode = $brand_arr_code[$icode];
    $brand_qty[$bicode] += (float)$hstkcon_brand_qty[$icode];
    $tbrandcon_qty += (float)$hstkcon_brand_qty[$icode];
}

$bsize = sizeof($brand_code) + 1;
if($bsize > 0){
    $pxm = 560 % $bsize;
    if($pxm > 0){
        $pxv = 560 - $pxm;
        $pxb = $pxv / $bsize;
        $pxt = $pxb + $pxm;
    }
    else{
        $pxv = 560;
        $pxb = $pxv / $bsize;
        $pxt = $pxb;
    }
}
else{
    $pxt = 560;
}

$brand_stock = '';
$brand_stock .= '<tr>';
$brand_stock .= '<th style="width:560px;text-align:center;border-top: 1px solid black;"><b style="color:red;font-size:13px;">Brand Wise Stock-In Details</b></th>';
$brand_stock .= '</tr>';
$brand_stock .= '<tr>';
foreach($brand_code as $bcode){
    $brand_stock .= '<th style="width:'.$pxb.'px;text-align:center;border-top: 1px solid black;border-right: 1px solid black;"><b style="color:green;">'.$brand_name[$bcode].' ('.round((($brand_qty[$bcode] / $tbrandcon_qty) * 100),2).'%)</b></th>';
}
$brand_stock .= '<th style="width:'.$pxt.'px;text-align:center;border-top: 1px solid black;"><b style="color:green;">Total ('.round((($tbrandcon_qty / $tbrandcon_qty) * 100),2).'%)</b></th>';
$brand_stock .= '</tr>';
$brand_stock .= '<tr>';
foreach($brand_code as $bcode){
    $brand_stock .= '<th style="width:'.$pxb.'px;text-align:right;border-top: 1px solid black;border-right: 1px solid black;">'.str_replace(".00","",number_format_ind(round($brand_qty[$bcode],2))).' - ('.str_replace(".00","",number_format_ind(round(($brand_qty[$bcode] / 50),2))).' Bags)</th>';
}
$brand_stock .= '<th style="width:'.$pxt.'px;text-align:right;border-top: 1px solid black;">'.str_replace(".00","",number_format_ind(round($tbrandcon_qty,2))).' - ('.str_replace(".00","",number_format_ind(round(($tbrandcon_qty / 50),2))).' Bags)</th>';
$brand_stock .= '</tr>';

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

$sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $total_consumed_feed_qty = array();
while($row = mysqli_fetch_assoc($query)){
    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['mortality'];
    $day_mort[$row['date']."@".$chick_code] = $day_mort[$row['date']."@".$chick_code] + $row['culls'];
    if($dstart_date == ""){ $dstart_date = $row['date']; } else{ if(strtotime($dstart_date) >= strtotime($row['date'])){ $dstart_date = $row['date']; } }
    if($dend_date == ""){ $dend_date = $row['date']; } else{ if(strtotime($dend_date) <= strtotime($row['date'])){ $dend_date = $row['date']; } }

}
$fdate = strtotime($dstart_date); $tdate = strtotime($dend_date); $days = $sold_mean_total = $bird_sold_amt = 
$week_1mortcnt = $week_2mortcnt = $week_3mortcnt = $week_4mortcnt = $week_5mortcnt = $week_6mortcnt = $week_7mortcnt = 0;
for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
    $days++;
    $present_date = date("Y-m-d",$currentDate);
    $mort_total = $mort_total + $day_mort[$present_date."@".$chick_code];

    if($days <= 7){ $week_1mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 7 && $days <= 14){ $week_2mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 14 && $days <= 21){ $week_3mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 21 && $days <= 28){ $week_4mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 28 && $days <= 35){ $week_5mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 35 && $days <= 42){ $week_6mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
    if($days > 42 && $days <= 49){ $week_7mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
}
if((float)$placed_birds != 0){
    $week_1mortper = round((((float)$week_1mortcnt / (float)$placed_birds) * 100),2);
    $week_2mortper = round((((float)$week_2mortcnt / (float)$placed_birds) * 100),2);
    $week_3mortper = round((((float)$week_3mortcnt / (float)$placed_birds) * 100),2);
    $week_4mortper = round((((float)$week_4mortcnt / (float)$placed_birds) * 100),2);
    $week_5mortper = round((((float)$week_5mortcnt / (float)$placed_birds) * 100),2);
    $week_6mortper = round((((float)$week_6mortcnt / (float)$placed_birds) * 100),2);
    $week_7mortper = round((((float)$week_7mortcnt / (float)$placed_birds) * 100),2);
    $total_mortper = round((((float)$mort_total / (float)$placed_birds) * 100),2);
}
else{
    $week_1mortper = $week_2mortper = $week_3mortper = $week_4mortper = $week_5mortper = $week_6mortper = $week_7mortper = $total_mortper = 0;
}


$supplier = mysqli_fetch_assoc(mysqli_query($conn,"SELECT vcode  FROM `broiler_purchases` WHERE `icode` LIKE '%BC%' AND farm_batch = '$batch_code' AND active = 1 AND dflag = 0"))['vcode'];

$html = "";

$html .= '<table align="center" style="border: 1px solid black;">';
$html .= '<tr style="line-height:1;">';
$html .= '<th style="padding:0;margin:0;width:200px;text-align:center;border-top: 1px solid black;">';
$html .= '<br/><br/><img src="../../'.$img_path.'" height="70px" />';
$html .= '</th>';
//$html .= '</tr>';
//$html .= '<tr style="line-height:6px;padding:0;margin:0;">';
$html .= '<th style="padding:0;margin:0;width:360px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">';
$html .= '<span align="center">'.$cdetail.'</span>';
$html .= '</th>';
$html .= '</tr>';
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
$html .= '<th style="width:127px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">GC Date: </b>';
$html .= '</th>';
$html .= '<td style="width:140px;padding-left: 10px;text-align:right;color:green;">'.$date.'</td>';
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

$html .= '<th style="width:110px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Hatchery : </b>';
$html .= '</th>';
$html .= '<td style="width:157px;padding-left: 10px;text-align:right;color:green;">'.$vname.'</td>';

$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Avg Body Weight: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round($avg_wt,3)).'&nbsp;</td>';

$html .= '</tr>';
$html .= '<tr>';

$html .= '<th style="width:110px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Supplier : </b>';
$html .= '</th>';
$html .= '<td style="width:157px;padding-left: 10px;text-align:right;color:green;">'.$vfname.'</td>';

$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">FCR: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round($fcr,3)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="width:140px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Housed Chicks: </b>';
$html .= '</th>';
$html .= '<td style="width:127px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($placed_birds)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;">CFCR: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.(round($cfcr,3)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Mortality: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.str_replace(".00","",number_format_ind($mortality + $culls)).'('.$total_mort.'%)</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Mean Age: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($mean_age,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Accidental Mort. Birds: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.$accidental_mort_birds.'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Day Gain: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($day_gain,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Total Mortality: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.str_replace(".00","",number_format_ind(round($mortality + $culls + $accidental_mort_birds,2))).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">E E F: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($eef,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Sold Birds: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.str_replace(".00","",number_format_ind($sold_birds)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Age in Days: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($age,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Excess Birds: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.$excess.'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Avg Sale Rate: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($sale_rate,2)).'&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Shortage Birds: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.($birds_shortage_qty).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;"></b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Sold Weight (Kgs): </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($sold_weight,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;"></b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">&nbsp;</td>';
$html .= '</tr>';

//if($_SERVER['REMOTE_ADDR'] == "106.197.193.64"){
$html .= $brand_stock;
//}
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

/*$html .= '<tr>';
$html .= '<th style="width:267px;text-align:center;border-right: 1px solid black;border-top: 1px solid black;"></th>';
$html .= '<th style="width:293px;text-align:center;border-top: 1px solid black;"></th>';
$html .= '</tr>';*/

$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Feed Details</u></b>';
$html .= '</th>';
$html .= '<th style="width:53px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Bags</u></b>';
$html .= '</th>';
$html .= '<th style="width:70px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Kgs</u></b>';
$html .= '</th>';
$html .= '<th style="width:185px;padding-left: 10px;text-align:left;border-left: 1px solid black;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Calculation of Growing Charge</u></b>';
$html .= '</th>';
$html .= '<th style="width:43px;padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Per Kg</u></b>';
$html .= '</th>';
$html .= '<th style="width:71px;padding-right: 10px;text-align:right;border-top: 1px solid black;">';
$html .= '<b style="padding-right: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Amount&nbsp;</u></b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Purchased: </b>';
$html .= '</th>';
$html .= '<td style="width:53px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_purchased_qty / 50),2)).'</td>';
$html .= '<td style="width:70px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_purchased_qty,2)).'</td>';
$html .= '<td style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Std. Growing Charge:</b></td>';
$html .= '<td style="width:38px;padding-right: 10px;text-align:right;color:green;">'.(round(($standard_gc_prc),3)).'</td>';
$html .= '<td style="width:71px;padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round(($standard_gc_amt),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed In: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_transin_qty / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_transin_qty,2)).'</td>';
$html .= '<td style="width:180px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Prod. Inc/Dec:</b></td>';
$html .= '<td style="width:48px;padding-right: 10px;text-align:right;color:green;">'.(round(($actual_charge_exp_prc),3)).'</td>';
$html .= '<td style="width:71px;padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round(($actual_charge_exp_amt),3)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Total Feed In: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round((($feed_purchased_qty + $feed_transin_qty) / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_purchased_qty + $feed_transin_qty),2)).'</td>';
$html .= '<td style="width:180px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Actual Growing Charge Paid Per Kg:</b></td>';
$html .= '<td style="width:48px;padding-right: 10px;text-align:right;color:green;">'.(round(($grow_charge_exp_prc),3)).'</td>';
$html .= '<td style="width:71px;padding-right: 10px;text-align:right;color:green;">'.number_format_ind(round(($grow_charge_exp_amt),2)).'&nbsp;</td>';
$html .= '</tr>';


$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Out: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_out_kgs / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_out_kgs,2)).'</td>';
$html .= '<td style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Sales Rate Incentive Per Kg: </b></td>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;">'.(round(($sales_incentive_prc),3)).'</td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($sales_incentive_amt),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;">';
$html .= '<b style="padding-left: 10px;text-align:left;">Feed Consumed: </b>';
$html .= '</th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($feed_consume_kgs / 50),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($feed_consume_kgs,2)).'</td>';
$html .= '<td style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Mortality Incentive: </b></td>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;">'.(round(($mortality_incentive_prc),3)).'</td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($mortality_incentive_amt),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Batch Costing</u></b>';
$html .= '</th>';
$html .= '<th style="width:53px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Std.Cost</u></b>';
$html .= '</th>';
$html .= '<th style="width:70px;padding-left: 10px;text-align:left;border-top: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:right;color:red;font-size:13px;font-weight:bold;"><u>Amount</u></b>';
$html .= '</th>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Other Incentive Charges: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($other_incentive,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:144px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Chicks Cost: </b></th>';

if((float)$placed_birds != 0){ $t1 = $chick_cost_amt / $placed_birds; } else{ $t1 = 0; }
$html .= '<td style="width:53px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '<td style="width:70px;padding-left: 10px;text-align:right;color:green;">'."".'</td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">GC Payable Before Deduction: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>'; //'.number_format_ind(round(($total_incentives / $sold_weight),2)).'
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($total_incentives,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Feed Cost: </b></th>';

if((float)$feed_consume_kgs != 0){ $t1 = $feed_cost_amt / $feed_consume_kgs; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'."".'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Deductions</u></b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Admin Cost: </b></th>';

if((float)$placed_birds != 0){ $t1 = $admin_cost_amt / $placed_birds; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'."".'</td>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">FCR Deductions: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($fcr_deduction,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Medicine Cost: </b></th>';
if((float)$sold_weight != 0){ $t1 = $medicine_cost_amt / $sold_weight; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'.number_format_ind(round($medicine_cost_amt,2)).'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;border-left: 1px solid black;"><b style="padding-left: 10px;text-align:left;">Mortality Deductions: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($mortality_deduction,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Actual Production Cost: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($actual_prod_cost,2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'."".'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Birds Shortage Deductions: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($birds_shortage,2)).'&nbsp;</td>';
$html .= '</tr>';
if((float)$amount_payable != 0){ $t1 = 0; $t1 = round((($tds_amt /$amount_payable) * 100)); } else{ $t1 = 0; }
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Standard Production Cost: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($standard_prod_cost,2)).'</td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;border-right: 1px solid black;">'."".'</td>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Farmer Sales Deductions: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($farmer_sale_deduction,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th rowspan="6" style="width:267px;padding-left: 10px;text-align:left;border-top: 1px solid black;border-right: 1px solid black;">';
$html .= '<b style="padding-left: 10px;text-align:left;color:red;font-size:13px;font-weight:bold;"><u>Remarks: </u></b>';
$html .= '</th>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Other Deductions: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($other_deduction,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Growing Charge Payable: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($total_amount_payable,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width:190px;padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">TDS @'.$t1.'%: </b></th>';
$html .= '<td style="width:38px;padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="width:71px;padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($tds_amt,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Net Payable After Deductions: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round($farmer_payable,2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Payable Per Bird: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
if((float)$sold_birds != 0){ $t1 = $farmer_payable / $sold_birds; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Payable Per Kg: </b></th>';
$html .= '<td style="padding-left: 10px;text-align:right;color:green;"></td>';
if((float)$sold_weight != 0){ $t1 = $farmer_payable / $sold_weight; } else{ $t1 = 0; }
$html .= '<td style="padding-left: 10px;text-align:right;color:green;">'.number_format_ind(round(($t1),2)).'&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td style="width:140px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Prepared By</td>';
$html .= '<td style="width:140px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Checked By</td>';
$html .= '<td style="width:140px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Approved By</td>';
$html .= '<td style="width:140px;text-align:center;border-top: 1px solid black;"><br/><br/><br/><br/>Authorized By</td>';
$html .= '</tr>';
$html .= '</table>';

//echo $html;

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
    $html .= '<th style="text-align:center;"><br/><br/>';
    $html .= '<img src="../../'.$img_path.'" height="60px" />';
    $html .= '</th>';
    $html .= '<th colspan="6" style="text-align:center;">';
    $html .= '<i align="center">'.$cdetail.'</i>';
    $html .= '</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="width:560px;text-align:center;border-top: 1px solid black;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;color:purple;">Batch Mortality Details</b>';
    $html .= '</th>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="width:280px;text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Farm Name</b>';
    $html .= '</th>';
    $html .= '<td style="width:280px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.$farm_name[$farm_code].'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="width:280px;text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Village</b>';
    $html .= '</th>';
    $html .= '<td style="width:280px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.$line_name[$line_code].'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="width:280px;text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Flock No.</b>';
    $html .= '</th>';
    $html .= '<td style="width:280px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.$batch_name[$batch_code].'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="width:280px;text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Housed Chicks</b>';
    $html .= '</th>';
    $html .= '<td style="width:280px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($placed_birds))).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="width:280px;text-align:center;border-bottom: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Week</b>';
    $html .= '</th>';
    $html .= '<th style="width:140px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Mortality</b>';
    $html .= '</th>';
    $html .= '<th style="width:140px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">';
    $html .= '<b style="text-align:center;font-weight:bold;">Mortality %</b>';
    $html .= '</th>';
    $html .= '</tr>';
    if((float)$placed_birds != 0){
        $t1 = ((float)$w1_mort / (float)$placed_birds);
        $t2 = ((float)$w2_mort / (float)$placed_birds);
        $t3 = ((float)$w3_mort / (float)$placed_birds);
        $t4 = ((float)$w4_mort / (float)$placed_birds);
        $t5 = ((float)$w5_mort / (float)$placed_birds);
        $t6 = ((float)$w6_mort / (float)$placed_birds);
        $t7 = ((float)$w7_mort / (float)$placed_birds);
        $t8 = ((float)$w8_mort / (float)$placed_birds);
        $t9 = ((float)$w9_mort / (float)$placed_birds);
        $tt = (((float)$mortality + (float)$culls) / (float)$placed_birds);
    }
    else{
        $t1 = $t2 = $t3 = $t4 = $t5 = $t6 = $t7 = $t8 = $tt = 0;
    }
    $html .= '<tr>';
    $html .= '<th style="width:280px;text-align:center;border-bottom: 1px solid black;">1st Week</th>';
    $html .= '<td style="width:140px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w1_mort))).'</td>';
    $html .= '<td style="width:140px;text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t1 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">2nd Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w2_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t2 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">3rd Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w3_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t3 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">4th Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w4_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t4 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">5th Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w5_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t5 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">6th Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w6_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t6 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">7th Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w7_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t7 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">8th Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w8_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t8 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;">9th Week</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.str_replace(".00","",number_format_ind(round($w9_mort))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;">'.number_format_ind(round(($t9 * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th style="text-align:center;border-bottom: 1px solid black;font-weight:bold;">Total</th>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;font-weight:bold;">'.str_replace(".00","",number_format_ind(round((float)$mortality + (float)$culls))).'</td>';
    $html .= '<td style="text-align:center;border-bottom: 1px solid black;border-left: 1px solid black;font-weight:bold;">'.number_format_ind(round(($tt * 100),2)).'</td>';
    $html .= '</tr>';
    $html .= '</table>';
}
if($_SERVER['REMOTE_ADDR'] == "106.197.193.64"){
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
    $pdf->SetMargins(5, 3, 5, true);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_028.pdf', 'I');
}
else{
    //echo $_SERVER['REMOTE_ADDR'];
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
    $pdf->SetMargins(5, 3, 5, true);
    $pdf->SetAutoPageBreak(TRUE, 0);
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_028.pdf', 'I');
}

?>