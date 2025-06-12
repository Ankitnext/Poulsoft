<?php
//farmerrearingchargeprint_moti.php
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
     $actual_medicine_cost = $row['actual_medicine_cost'];
     $actual_chick_price = $row['actual_chick_price'];
     $actual_chick_cost = $row['actual_chick_cost'];
     $actual_feed_price = $row['actual_feed_price'];
     $actual_feed_cost = $row['actual_feed_cost'];
     $standard_gc_prc = $row['standard_gc_prc'];
$standard_gc_amt = $row['standard_gc_amt'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql);
$data = mysqli_fetch_assoc($query);
$lotno = $data['clot_no']; $bat_desc = $data['description'];

$sql = "SELECT * FROM `broiler_sales` WHERE `farm_batch` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $tot_item_amt = 0;
while($row = mysqli_fetch_assoc($query)){
     $tot_item_amt += (float)$row['item_tamt'];
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
    $panno = $row['panno'];
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
$sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batch_code' AND `brood_age` > '0' AND `active` = '1' AND `dflag` = '0'";
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
    if($days > 49 && $days <= 56){ $week_8mortcnt += (float)$day_mort[$present_date."@".$chick_code]; }
}
if((float)$placed_birds != 0){
    $week_1mortper = round((((float)$week_1mortcnt / (float)$placed_birds) * 100),2);
    $week_2mortper = round((((float)$week_2mortcnt / (float)$placed_birds) * 100),2);
    $week_3mortper = round((((float)$week_3mortcnt / (float)$placed_birds) * 100),2);
    $week_4mortper = round((((float)$week_4mortcnt / (float)$placed_birds) * 100),2);
    $week_5mortper = round((((float)$week_5mortcnt / (float)$placed_birds) * 100),2);
    $week_6mortper = round((((float)$week_6mortcnt / (float)$placed_birds) * 100),2);
    $week_7mortper = round((((float)$week_7mortcnt / (float)$placed_birds) * 100),2);
    $week_8mortper = round((((float)$week_8mortcnt / (float)$placed_birds) * 100),2);
    $total_mortper = round((((float)$mort_total / (float)$placed_birds) * 100),2);
}
else{
    $week_1mortper = $week_2mortper = $week_3mortper = $week_4mortper = $week_5mortper = $week_6mortper = $week_7mortper = $week_8mortper = $total_mortper = 0;
}




$sql = "SELECT * FROM `broiler_sales` WHERE `farm_batch` = '$batch_code' AND `warehouse` = '$farm_code'";
$query = mysqli_query($conn,$sql); $lmbird = $lmbwt = $abwt = $lbcnt = $nbcnt = 0;
while($row = mysqli_fetch_assoc($query)){
    if($row['lb_flag'] == '1'){
         $lmbird += (float)$row['birds'];
         $lmbwt += (float)$row['rcd_qty'];
         $lb_amt += (float)$row['item_tamt'];
         $lbcnt = $lbcnt + 1;
    }else{
        $abwt += (float)$row['rcd_qty'];
        $nbcnt += (float)$row['birds'];
        $nb_amt += (float)$row['item_tamt'];
       
    }
   
}

$asppkg = round($nb_amt/$abwt,2);

$html = "";

$html .= '<table width="100%">';
$html .=  '<tr width="40%" align="left"><th>'.$cdetail.'</th>';
$html .=  '<th>';

$html .= '<p style=" font-weight: bold;">Management Payment Sheet</p><br/>';
$html .= '<table width="100%" >
    <tr>
      
        <td width="50%" valign="top">
            <table width="80%">
    <tr>
        <td style="width:60px;"><strong>Farmer</strong></td>
        <td style="width:10px;" >:</td>
        <td style="width:80px;">'.$farm_name[$farm_code].'</td>
    </tr>
    <tr>
        <td><strong>Farmer Code</strong></td>
        <td>:</td>
        <td>'.$farm_code.'</td>
    </tr>
    <tr>
        <td><strong>PAN No.</strong></td>
        <td>:</td>
        <td>'.$panno.'</td>
    </tr>
</table>
        </td>
        <!-- Right Column: Bank Details -->
        <td width="60%" valign="top">
            <table width="60%" >
                <tr>
                    <td><strong>Bank Name </strong></td>
                    
                    <td>: '.$fmr_bank_name.'</td>
                </tr>
                <tr>
                    <td><strong>A/c No.</strong></td>
                    
                    <td>: '.$fmr_accountno.'</td>
                </tr>
                <tr>
                    <td><strong>IFSC No.</strong></td>
                   
                    <td>: '.$fmr_ifsc_code.'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
';

$html .='</th>';

$html .= '</tr>';
$html .= '</table><br/><hr>';

// section two
$html .= '<br/><table width="100%">';
$html .= '<tr>';

$html .= '<th width="33%"> <table width="100%" >
                <tr>
                    <td><strong>Distribution Center :</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Lot No. :</strong></td>
                    <td>'.$lotno.'</td>
                </tr>
                <tr>
                    <td><strong>Flock No. :</strong></td>
                    <td>'.$bat_desc.'</td>
                </tr>
            </table></th>';

$html .= '<th width="33%">

<table width="90%" >
                <tr>
                    <td><strong>Placement Date :</strong></td>
                    <td>'.$start_date.'</td>
                </tr>
                <tr>
                    <td><strong>Final Lifting Date. :</strong></td>
                    <td>'.$liquid_date.'</td>
                </tr>
                <tr>
                    <td><strong>Supervisor. :</strong></td>
                    <td>'.$supervisor_name.'</td>
                </tr>
            </table>

</th>';
$html .= '<th width="33%">
<table style="border-collapse: collapse;">
    <tr>
        <td style="border:1px solid black; padding: 5px;"><strong>Chicks :</strong></td>
        <td style="border:1px solid black; padding: 5px;">MOTI</td>
    </tr>
    <tr>
        <td style="border:1px solid black; padding: 5px;"><strong>Hatch :</strong></td>
        <td style="border:1px solid black; padding: 5px;"></td>
    </tr>
</table>

</th>';

$html .= '</tr>';
$html .= '</table><br/><hr>';

//section 3

$html .= '
<table style="width: 100%; border-collapse: collapse; text-align: center; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style=";  background-color: #f2f2f2; font-size: 12px;">Summary of Flock Performance</th>
            <th colspan="3" style=";  background-color: #f2f2f2; font-size: 12px;">Calculation of Cost of Production</th>
            <th colspan="5" style=";  background-color: #f2f2f2; font-size: 12px;">Calculation of Cost of Production</th>
        </tr>
        <tr>
           
            <th style=";  font-weight: bold;"></th>
            <th style=";  font-weight: bold;"></th>
            <th style=";  font-weight: bold;"></th>
            <th style=";  font-weight: bold;"></th>

            
            <th style=";  font-weight: bold;"></th>
            <th style=";  font-weight: bold;"></th>
            <th style=";  font-weight: bold;"></th>

           
            <th style=";  font-weight: bold;"></th>
            <th style=";  font-weight: bold;text-align:right;width:50px;">Rate</th>
            <th style=";  font-weight: bold;"></th>
             <th style=";  font-weight: bold;text-align:right;width:50px;">Amount</th>
            <th style=";  font-weight: bold;text-align:right;width:60px;">Amount</th>
        </tr>
    </thead>
    <tbody>';
       
        if((float)$placed_birds != 0){ $t1 = $chick_cost_amt / $placed_birds; } else{ $t1 = 0; }
        $totalqtyplaced = $placed_birds - ($mortality + $culls);
       $html .= ' <tr>
            <td style=";width:140px;text-align:left; ">Chicks Placeed(Pcs)</td>
            <td style=";width:10px; ">:</td>
            <td style=";width:50px;text-align:right; ">'.number_format_ind(round($placed_birds)).'</td>
            <td style=";width:50px; "></td>

            <td style=";width:140px;text-align:left; ">Chicks Cost @ '.number_format_ind(round(($actual_chick_price),2)).' </td>
            <td style=";width:10px; ">:</td>
            <td style=";;width:55px;text-align:right ">'.number_format_ind(round($actual_chick_cost,2)).'</td>

            <td style=";width:140px;text-align:left; ">Basic Growing Charges</td>
            <td style=";;width:50px;text-align:right; ">'.(round(($standard_gc_prc),3)).'</td>
            <td style=";width:10px; ">:</td>
             <td style=";;width:50px; "></td>
            <td style=";;width:60px;text-align:right; ">'.number_format_ind(round(($standard_gc_amt),2)).'</td>
        </tr>

        <tr>
            <td style=";width:140px;text-align:left; ">Total Mortality(Pcs)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.number_format_ind($mortality + $culls).'</td>
            <td style="; ">'.$total_mort.'%</td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; ">Production Cost(Standart)</td>
            <td style=";text-align:right; ">'.number_format_ind(round($standard_prod_cost,2)).'</td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style="; "></td>
        </tr>';
if((float)$feed_consume_kgs != 0){ $t1 = $feed_cost_amt / $feed_consume_kgs; } else{ $t1 = 0; }
$feed_costs = $actual_feed_cost / $feed_consume_kgs;
    $html .= '    <tr>
            <td style=";width:140px;text-align:left; ">1st Week Mortality</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.$days7_mort_count.'</td>
            <td style="; ">'.$days7_mort.'%</td>

            <td style=";width:140px;text-align:left; ">Feed Cost @ '.number_format_ind(round(($feed_costs),2)).'</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right ">'.number_format_ind(round($actual_feed_cost,2)).'</td>

            <td style=";width:140px;text-align:left; ">Production Cost(Actual)</td>
            <td style=";text-align:right; "> '.number_format_ind(round($actual_prod_cost,2)).'</td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style="; "></td>
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">After 7 days Mortality</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.($days30_mort_count - $days7_mort_count).'</td>
            <td style="; ">'.($days30_mort-$days7_mort).'%</td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; ">Production Incentive)</td>
            <td style=";text-align:right; ">'.number_format_ind(round($actual_charge_exp_prc,2)).'</td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style="; ">'.number_format_ind(round($actual_charge_exp_amt,2)).'</td>
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">After 30 days Mortality</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.$days31_mort_count.'</td>
            <td style="; ">'.$daysge31_mort.'%</td>

            <td style=";width:140px;text-align:left; ">Medicine,Vaccination @ Actual</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'. $actual_medicine_cost.'</td>

            <td style=";width:140px;text-align:left; ">Obtainable Growing Charges</td>
            <td style=";text-align:right; "></td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style="; "></td>
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">Total Live Birds Sold(Pcs)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.str_replace(".00","",number_format_ind($sold_birds)).'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; ">Market Incentive</td>
            <td style=";text-align:right; ">'.number_format_ind(round($sales_incentive_prc,2)).'</td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style="; ">'.number_format_ind(round($sales_incentive_amt,2)).'</td>
        </tr>';
     $qtykgsold = number_format_ind(round($sold_weight,2));
      $html .= '   <tr>
            <td style=";width:140px;text-align:left; ">Total Live Birds Sold(kg)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.number_format_ind(round($sold_weight,2)).'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; ">Admin,Supervision Cost @ '.round(($admin_cost_unit),2).'</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right ">'.number_format_ind(round($admin_cost_amt,2)).'</td>

            <td style=";width:140px;text-align:left; ">Extra Payment</td>
            <td style=";text-align:right; "></td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style="; "></td>
        </tr>';
        $totalfinal =  round(($standard_gc_amt + $sales_incentive_amt + $actual_charge_exp_amt),2);
        if($totalfinal < 0) { $totalfinals = 0;} else { $totalfinals = $totalfinal;}
     $html .= '    <tr>
            <td style=";width:140px;text-align:left; ">Avg. Birds Wt(kg)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.(round($avg_wt,3)).'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; ">Growing Charges @ Rs '.number_format_ind(round($standard_gc_prc,2)).'</td>
            <td style=";width:10px; "></td>
            <td style="; ">'.number_format_ind(round($totalfinals,2)).'</td>

            <td style=";width:140px;text-align:left; ">Total</td>
            <td style=";text-align:right; "></td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style=";text-align:right;border-top:2px solid black; ">'.$totalfinal.'</td>
        </tr>';
        $percentage = $totalfinal * 0.01;
      $finamt1 = $standard_gc_amt + $sales_incentive_amt + $actual_charge_exp_amt - $percentage;
      $html .='   <tr>
            <td style=";width:140px;text-align:left; ">Total Feed Consumed(kg)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.number_format_ind(round($feed_consume_kgs,2)).'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; "></td>

            <td style=";width:140px;text-align:left; ">Less TDS</td>
            <td style=";text-align:right; ">'.round(1,2).'</td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style=";text-align:right;border-bottom:2px solid black; ">'.round($percentage,2).'</td>
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">Feed Conversion Ratio</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.$fcr.'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "><b>Total Cost Amount</b></td>
            <td style=";width:10px; "></td>
            <td style="; "><b>'.number_format_ind(round(($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $totalfinals ),2)).'</b></td>

            <td style=";width:140px;text-align:left; ">Total Amount</td>
            <td style="; "></td>
            <td style=";width:10px; "></td>
             <td style="; "></td>
            <td style=";text-align:right;border-bottom:2px solid black; ">'.$finamt1.'</td>
        </tr>
        
          <tr>
            <td style=";width:140px;text-align:left; ">Corrected Feed Conversion Ratio</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">'.$cfcr.'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style="; "></td>
            <td style=";width:10px; "></td>
             <td style="; "></td>
            <td style=";text-align:right; "></td>
        </tr>
        
        
        ';

        
if((float)$sold_weight != 0){ $t1 = $actual_prod_amount / $sold_weight; } else{ $t1 = 0; }
      $html .= '   <tr>
            <td style=";width:140px;text-align:left; ">Production efficiency Factor</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; ">Cost of Production / kg</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right;border:1px solid black; ">'.number_format_ind(round(($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $totalfinal) / $sold_weight,2)).'</td>

            <td style=";width:140px;text-align:left; ">Deduction</td>
            <td style="; "></td>
            <td style=";width:10px; "></td>
             <td style="; "></td>
            <td style="text-align:right;border-bottom:2px solid black; ">'.number_format_ind(round($farmer_sale_deduction,2)).'</td>
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">Own Use/Shortage of Birds(Pcs)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left;text-decoration: underline; ">Net Payment</td>
            <td style="; "></td>
            <td style=";width:10px; ">:</td>
             <td style="; "></td>
            <td style=" text-align:right;border-bottom:2px solid black;">'.number_format_ind(round($finamt1-$farmer_sale_deduction,2)).'</td>
        </tr>

        <tr>
            <td style=";width:140px;text-align:left; ">Birds Lifted During in age</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; ">39</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style="; "></td>
            <td style=";width:10px; "></td>
             <td style="; "></td>
            <td style="; "></td>
        </tr>';
      $totavgqty = $sale_rate;
      $html .= '  <tr>
            <td style=";width:140px;text-align:left; ">Avg. Sales Price /kg</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; "> '.$asppkg.'</td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style=";width:146px;text-align:left;border-top:1px solid black;border-left:1px solid black; "></td>
            <td style="border-top:1px solid black; "></td>
            <td style="width:14px;border-top:1px solid black; "></td>
             <td style="border-top:1px solid black; "></td>
            <td style="border-top:1px solid black;border-right:1px solid black; "></td>
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">Chicks Fine(Pcs)</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td colspan="5" style=";width:320px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-right; ">Details of Sales</td>
           
        </tr>

         <tr>
            <td style=";width:140px;text-align:left; ">Chicks Fine Rate</td>
            <td style=";width:10px; ">:</td>
            <td style=";text-align:right; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style="width:80px;text-align:left;border-left:1px solid black; "></td>
            <td style="width:80px;"></td>
            <td style="width:80px;"></td>
             <td style="width:80px;"></td>
            <td style="width:50px;border-left:1px solid black;border-right:1px solid black;border-right; "></td>
        </tr>

        <tr>
            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style=";text-align:right; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style="width:80px;text-align:left;border-left:1px solid black; "><b>Date</b></td>
            <td style="width:80px; ">Qty(Pcs)</td>
            <td style="width:50px; ">Qty(kg)</td>
             <td style="width:50px; ">Rate</td>
            <td style="width:60px;border-right:1px solid black;border-right; ">Amount</td>
        </tr>';
       $qtyplaced = $placed_birds - ($mortality + $culls) ;
       if($lmbwt != 0 || $lmbwt != '0'){  $amt = (float)$lb_amt/$lmbwt; } else { $amt = 0; }

       $html .= '<tr>
            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style=";text-align:right; "></td>
            <td style="; "></td>

            <td style=";width:140px;text-align:left; "></td>
            <td style=";width:10px; "></td>
            <td style="; "></td>

            <td style="width:80px;text-align:left;border-left:1px solid black; "></td>
            <td style="width:80px; "></td>
            <td style="width:50px; "></td>
             <td style="width:50px; "></td>
            <td style="width:60px;border-right:1px solid black;border-right; "></td>
        </tr>



<tr>
   
    <td colspan="6" style="; width: 455px; text-align: center;">Bird Weight Reduction Details</td>
    <td style="width:80px;text-align:left;border-left:1px solid black;"></td>
    <td style="width:80px; ">'.$nbcnt.'</td>
    <td  style="width:50px; ">'.$abwt.'</td>
    <td style="width: 50px;">'.$totavgqty.'</td>
    <td style="width: 60px;border-right:1px solid black;border-right; ">'.$nb_amt.'</td>
</tr>



<tr>
   
    <td colspan="6" style="; width: 455px; text-align: center;"></td>
    <td style="width:80px;text-align:left;border-left:1px solid black;"></td>
    <td style="width:80px; ">'.$lmbird.'</td>
    <td  style="width:50px; ">'.$lmbwt.'</td>
    <td style="width: 50px;">'.$amt.'</td>
    <td style="width: 60px;border-right:1px solid black;border-right; ">'.$lb_amt.'</td>
</tr>






 <tr>
            <td style=";width:81pxt; ">Item</td>
            <td style=";width:81px; ">Actual Wt</td>
             <td style=";width:81px; ">Reduction%</td>
            <td style=";width:81px; ">Reduction</td>
            <td style=";width:81px; ">Converted Wt</td>
            <td style=";width:50px;text-align:left; "></td>

             <td style="width:80px;text-align:left;border-left:1px solid black;"></td>
    <td style="width:80px; "></td>
    <td  style="width:50px; "></td>
    <td style="width: 50px;"></td>
    <td style="width: 60px;border-right:1px solid black;border-right; "></td>
        </tr>';

        $prolessamt = (float)$tot_item_amt - (float)$total_cost_amt;

      $html .='  <tr>
            <td style=";width:91pxt; "></td>
            <td style=";width:91px; "></td>
            <td style=";width:91px; "></td>
            <td style=";width:91px; "></td>
            <td style=";width:91px;text-align:left; "></td>
            <td style=";width:80px;text-align:left;border-left:1px solid black;  "></td>
            <td style="width:80px;border-top:1px solid black;border-bottom:1px solid black; ">'.$totalqtyplaced.'</td>
            <td style="width:50px;border-top:1px solid black;border-bottom:1px solid black; ">'.$qtykgsold.'</td>
             <td style="width:50px;border-top:1px solid black;border-bottom:1px solid black; ">'.($totavgqty + $amt).'</td>
            <td style="width:60px;border-right:1px solid black;border-right;border-top:1px solid black;border-bottom:1px solid black; ">'.number_format_ind($tot_item_amt).'</td>
        </tr>

           <tr>
            <td style=";width:81pxt; ">Fresh</td>
            <td style=";width:81px; ">'.$abwt.'</td>
            <td style=";width:81px; "></td>
            <td style=";width:81px; "></td>
            <td style=";width:81px; ">'.$abwt.'</td>

            <td style=";width:50px;text-align:left; "></td>
           

             <td style="; width: 80px; text-align: left;border-left:1px solid black;">LESS</td>
            <td style=" width: 80px;">Total Cost</td>
            <td style=" width: 50px;"></td>
            <td style=" width: 50px;"></td>
            <td style=" width: 60px;border-right:1px solid black;border-right;">'.number_format_ind(round(($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $totalfinals),2)).'</td>
        </tr>
      

        <tr>
            <td style="width:80px; ">Small</td>
            <td style="width:91px; "></td>
            <td style="width:91px; "></td>
            <td style="width:91px; "></td>

            <td style="width:102px;text-align:left; "></td>
           

            <td style="width:80px;text-align:left;border-left:1px solid black; "></td>
            <td style="width:80px;"><b>Profit/Loss</b></td>
            <td style="width:50px; "></td>
             <td style="width:50px; "></td>
            <td style="width:60px;border-right:1px solid black;border-right;border-top:1px solid black;border-bottom:1px solid black ">'.number_format_ind($tot_item_amt - ($actual_chick_cost + $actual_feed_cost + $actual_medicine_cost + $admin_cost_amt + $totalfinals)).'</td>
        </tr>';
        $denominator = $avg_wt * $lmbird;
        $percentage = 0;
        if ($denominator != 0) {
            $percentage = ((($denominator - $lmbwt) / $denominator) * 100);
        }
         $html .= '<tr>
           <td style="width:82pxt; ">Lame</td>
            <td style="width:82px; ">'.($avg_wt * $lmbird).'</td>
            <td style="width:82px; ">'.number_format_ind(round($percentage, 2)).'</td>
            <td style="width:82px; ">'.number_format_ind(round((($avg_wt * $lmbird)-$lmbwt),2)).'</td>
            <td style="width:82px; ">'.$lmbwt.'</td>

            <td style="width:45px;text-align:left; "></td>
           

            <td style="width:80px;text-align:left;border-left:1px solid black; "></td>
            <td style="width:80px;"></td>
            <td style="width:50px; "></td>
             <td style="width:50px; "></td>
            <td style="width:60px;border-right:1px solid black;border-right; "></td>
        </tr>

        <tr>
            <td style="width:82pxt;border-top:1px solid black; "></td>
            <td style="width:82px;border-top:1px solid black; ">'.($abwt + ($avg_wt * $lmbird)).'</td>
            <td style="width:82px;border-top:1px solid black; ">'.number_format_ind(round($percentage, 2)).'</td>
            <td style="width:82px;border-top:1px solid black; ">'.number_format_ind(round((($avg_wt * $lmbird)-$lmbwt),2)).'</td>
            <td style="width:82px;border-top:1px solid black; ">'.($abwt + $lmbwt).'</td>
            <td style="width:45px;text-align:left; "></td>
           

            <td style="width:80px;text-align:left;border-top:1px solid black; "></td>
            <td style="width:80px;border-top:1px solid black; "></td>
            <td style="width:50px;border-top:1px solid black; "></td>
             <td style="width:50px;border-top:1px solid black; "></td>
            <td style="width:60px;border-top:1px solid black;  "></td>
        </tr>

         <tr>
            <td style="width:92pxt;border-top:1px solid black; "></td>
            <td style="width:92px;border-top:1px solid black; "></td>
            <td style="width:92px;border-top:1px solid black; "></td>
            <td style="width:135px;border-top:1px solid black; "></td>

            <td style="width:92px;text-align:left; "></td>
           

            <td style="width:140px;text-align:left; "></td>
            <td style=" "></td>
            <td style="width:10px; "></td>
             <td style=" "></td>
            <td style=" "></td>
        </tr>
      
 


        
       
    </tbody></table>
';
$html .= '<br/><br/><br/><br/>';
$html .= '<table style="width: 100%; text-align: center; margin-top: 50px; border-collapse: collapse;">
    <tr>
        <td style="width: 55%; text-align: center; padding-top: 20px;">
           
        </td>
        <td style="width: 50%; text-align: left; padding-top: 20px;">
            <hr style="width: 50%; border: 2px solid black;">
            <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Authorized Signatory</strong> <br><br><br>
            <strong>MOTI POULTRY DEVELOPERS PVT LTD</strong>
        </td>
    </tr>
</table>';

//echo $html;
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
//$fontname = $this->pdf->addTTFfont('font-family/MAIAN.TTF', 'TrueTypeUnicode', '', 32);
$fontname = TCPDF_FONTS::addTTFfont('font-family/MAIAN.TTF', 'TrueType', '', 32);
$pdf->SetFont($fontname, '', 9, '', true); //dejavusans
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(5, 5, 5, true);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->AddPage('L', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>