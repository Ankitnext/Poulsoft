<?php
//broiler_fetch_rearingcharge_skbf.php
session_start(); include "newConfig.php";
$farm_code = $_GET['farm_code'];
$farm_batch = "";

$fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql);
while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; $batch_name = $frow['description']; }

if(!empty($farm_batch)){
    $pur_qty = $pur_amount = $trin_qty = $trin_amount = $day_qty = $day_amount = $day_ages = $day_mort = $mve_qty = $mve_amount = $sal_qty = $sal_amount = array();
    $trot_qty = $trot_amount = $item_all = $sal_birds = $feed_cat = $medvac_cat = $biosec_cat = array(); $item_all[$chick_code] = $chick_code;

    $sql = "SELECT * FROM `broiler_farm` WHERE `code` LIKE '$farm_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $region_code = $row['region_code']; $branch_code = $row['branch_code']; $line_code = $row['line_code'];  $supervisor_code = $row['supervisor_code'];  $farmer_code = $row['farmer_code']; }

    $sql = "SELECT * FROM `location_line` WHERE `code` LIKE '$line_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $line_name = $row['description']; }

    $sql = "SELECT * FROM `location_branch` WHERE `code` LIKE '$branch_code'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $branch_name = $row['description']; }

    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $feed_cat[$row['code']] = $row['code']; }
    $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $medvac_cat[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $medvac_cat[$row['code']] = $row['code']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bio security%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
    while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $biosec_cat[$row['code']] = $row['code']; }
    $bsi_list = implode("','",$biosec_cat);

    $sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$region_code' AND `branch_code` = '$branch_code'AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); // AND `from_date` >= '$start_date' AND `to_date` >= '$end_date' 
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
        $max_fcr = $row['max_fcr'];
        $max_feed_cost = $row['max_feed_cost'];
    }

    //Purchases
    $count = 0; $start_date = $end_date = $dstart_date = $dend_date = "";
    $sql = "SELECT * FROM `broiler_purchases` WHERE `warehouse` = '$farm_code' AND `farm_batch` = '$farm_batch' AND `icode` NOT IN ('$bsi_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['date']."@".$row['icode'];
        $pur_qty[$key] = $pur_qty[$key] + $row['rcd_qty']+ $row['fre_qty'];
        $pur_amount[$key] = $pur_amount[$key] + $row['item_tamt'];
        $item_in_price[$row['icode']] = $row['rate'];
        $item_all[$row['icode']] = $row['icode'];
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }
    //Transfer In
    $count = 0;
    $sql = "SELECT * FROM `item_stocktransfers` WHERE `towarehouse` = '$farm_code' AND `to_batch` = '$farm_batch' AND `code` NOT IN ('$bsi_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['date']."@".$row['code'];
        $trin_qty[$key] = $trin_qty[$key] + $row['quantity'];
        $trin_amount[$key] = $trin_amount[$key] + $row['amount'];
        $item_in_price[$row['code']] = $row['price'];
        $farmer_price[$key] = $row['farmer_price'];
        $item_all[$row['code']] = $row['code'];
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }
    //Daily Entry
    $count = 0;
    $sql = "SELECT * FROM `broiler_daily_record` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$farm_batch' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql);
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
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
        if($dstart_date == ""){ $dstart_date = $row['date']; } else{ if(strtotime($dstart_date) >= strtotime($row['date'])){ $dstart_date = $row['date']; } }
        if($dend_date == ""){ $dend_date = $row['date']; } else{ if(strtotime($dend_date) <= strtotime($row['date'])){ $dend_date = $row['date']; } }
        $page = $row['brood_age'];
        
    }
    //Medecine & Vaccine Consumptions
    $count = 0;
    $sql = "SELECT * FROM `broiler_medicine_record` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$farm_batch' AND `item_code` NOT IN ('$bsi_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['date']."@".$row['item_code'];
        $mve_qty[$key] = $mve_qty[$key] + $row['quantity'];
        $item_all[$row['item_code']] = $row['item_code'];
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }
    //Sales
    $count = $farmer_sales_amt = 0;
    $sql = "SELECT * FROM `broiler_sales` WHERE `warehouse` = '$farm_code' AND `farm_batch` = '$farm_batch' AND `icode` NOT IN ('$bsi_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['date']."@".$row['icode'];
        $sal_birds[$key] = $sal_birds[$key] + $row['birds'];
        $sal_qty[$key] = $sal_qty[$key] + $row['rcd_qty'];
        $sal_amount[$key] = $sal_amount[$key] + ($row['rcd_qty'] * $row['rate']);
        $item_all[$row['icode']] = $row['icode'];
        if($row['sale_type'] == "FormMBSale"){
            $farmer_sales_amt = $farmer_sales_amt +  ($row['rcd_qty'] * $row['rate']);
        }
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }

    }
    //Transfer Out
    $count = 0;
    $sql = "SELECT * FROM `item_stocktransfers` WHERE `fromwarehouse` = '$farm_code' AND `from_batch` = '$farm_batch' AND `code` NOT IN ('$bsi_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['date']."@".$row['code'];
        $trot_qty[$key] = $trot_qty[$key] + $row['quantity'];
        $trot_amount[$key] = $trot_amount[$key] + $row['amount'];
        $item_all[$row['code']] = $row['code'];
        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
    }

    $fdate = strtotime($start_date); $tdate = strtotime($end_date); $days = $sold_mean_total = $bird_sold_amt = 0;
	for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
        $days++;
        $present_date = date("Y-m-d",$currentDate);
        if($days <= 7){
            $mort_7days = $mort_7days + $day_mort[$present_date."@".$chick_code];
            $mort_30days = $mort_30days + $day_mort[$present_date."@".$chick_code];
        }
        else if($days <= 30){
            $mort_30days = $mort_30days + $day_mort[$present_date."@".$chick_code];
        }
        else if($days > 30){
            $mort_ge31days = $mort_ge31days + $day_mort[$present_date."@".$chick_code];
        }
        else{ }
        $mort_total = $mort_total + $day_mort[$present_date."@".$chick_code];

        $chick_placed = $chick_placed + $pur_qty[$present_date."@".$chick_code];
        $chick_placed = $chick_placed + $trin_qty[$present_date."@".$chick_code];

        if(!empty($sal_birds[$present_date."@".$bird_code])){
            $bird_sold_nos = $bird_sold_nos + $sal_birds[$present_date."@".$bird_code];
        }
        if(!empty($sal_qty[$present_date."@".$bird_code])){
            $bird_sold_qty = $bird_sold_qty + $sal_qty[$present_date."@".$bird_code];
        }
        if(!empty($sal_amount[$present_date."@".$bird_code])){
            $bird_sold_amt = $bird_sold_amt + $sal_amount[$present_date."@".$bird_code];
        }

        //Mean Age Calculations
        if(strtotime($present_date) >= strtotime($dstart_date)){
            $dlist = (INT)((strtotime($present_date) - strtotime($dstart_date)) / 60 / 60 / 24);
            $dlist2 = $dlist + 1;
            if(!empty($sal_birds[$present_date."@".$bird_code])){ $sbirds = $sal_birds[$present_date."@".$bird_code]; } else{ $sbirds = 0; }
            $sold_mean_total = $sold_mean_total + ($dlist2 * $sbirds);
        }

        foreach($item_all as $items){
            if(!empty($feed_cat[$items])){
                $feed_total_in = $feed_total_in + $pur_qty[$present_date."@".$items];
                $feed_total_in = $feed_total_in + $trin_qty[$present_date."@".$items];

                $feed_total_in_amt = $feed_total_in_amt + $pur_amount[$present_date."@".$items];
                $feed_total_in_amt = $feed_total_in_amt + $trin_amount[$present_date."@".$items];

                $feed_total_consumed = $feed_total_consumed + $day_qty[$present_date."@".$items];
                if(round($day_qty[$present_date."@".$items],2) > 0){
                    $item_total_qty[$items] = $item_total_qty[$items] + $day_qty[$present_date."@".$items];
                }
                
                $feed_total_out = $feed_total_out + $sal_qty[$present_date."@".$items];
                $feed_total_out = $feed_total_out + $trot_qty[$present_date."@".$items];

            }
            if(!empty($medvac_cat[$items])){
                $medvac_total_in = $medvac_total_in + $pur_qty[$present_date."@".$items];
                $medvac_total_in = $medvac_total_in + $trin_qty[$present_date."@".$items];

                if(!empty($pur_amount[$present_date."@".$items])){
                    $medvac_total_in_amt = $medvac_total_in_amt + $pur_amount[$present_date."@".$items];
                }
                if(!empty($trin_amount[$present_date."@".$items])){
                    $medvac_total_in_amt = $medvac_total_in_amt + $trin_amount[$present_date."@".$items];
                }
                
                
                $medvac_total_consumed = $medvac_total_consumed + $mve_qty[$present_date."@".$items];
                if(!empty($mve_qty[$present_date."@".$items])){
                    $iprice = 0;
                    if($medicine_cost == "M"){
                        $ficode = $items; $fidate = $present_date;
                        $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                        $queryf = mysqli_query($conn,$sqlf); $m_cnt = mysqli_num_rows($queryf);
                        if((int)$m_cnt > 0){ while($rowf = mysqli_fetch_assoc($queryf)){ $iprice = $rowf['rate']; } }
                        else{
                            if(!empty($farmer_price[$present_date."@".$items])){ $iprice = $farmer_price[$present_date."@".$items]; }
                            else{ $iprice = 0; }
                        }
                        $medvac_total_consumed_amt = $medvac_total_consumed_amt + ($mve_qty[$present_date."@".$items] * $iprice);
                    }
                    else{
                        if(!empty($item_in_price[$items])){ $iprice = $item_in_price[$items]; } else{ $iprice = 0; }
                        $medvac_total_consumed_amt = $medvac_total_consumed_amt + ($mve_qty[$present_date."@".$items] * $iprice);
                    }
                }
                $medvac_total_out = $medvac_total_out + $sal_qty[$present_date."@".$items];
                $medvac_total_out = $medvac_total_out + $trot_qty[$present_date."@".$items];

            }
            else{ }
        }
    }

    //Bio-Security
    $bis_cqty = $bis_cprc = $bis_camt = 0;
    $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$bsi_list') AND `towarehouse` = '$farm_code' AND `to_batch` = '$farm_batch' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn, $sql); $bis_iqty = $bis_iprc = $bis_iamt = 0;
    while($row = mysqli_fetch_assoc($query)){ $bis_iqty += (float)$row['quantity']; $bis_iprc = $row['price']; $bis_iamt += ((float)$bis_iprc * (float)$row['quantity']); }

    $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$bsi_list') AND `fromwarehouse` = '$farm_code' AND `from_batch` = '$farm_batch' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
    $query = mysqli_query($conn, $sql); $bis_oqty = $bis_oprc = $bis_oamt = 0;
    while($row = mysqli_fetch_assoc($query)){ $bis_oqty += (float)$row['quantity']; $bis_oprc = $row['price']; $bis_oamt += ((float)$bis_oprc * (float)$row['quantity']); }
    $bis_cqty = (float)$bis_iqty - (float)$bis_oqty;
    $bis_camt = (float)$bis_iamt - (float)$bis_oamt;
    if((float)$bird_sold_qty != 0){ $bis_cprc = round(((float)$bis_camt / (float)$bird_sold_qty),2); } else{ $bis_cprc = 0; }

    /*  *****Final GC calculations***** */
    $placement_date = date("d.m.Y",strtotime($dstart_date));
    $placed_birds = $chick_placed;
    $mortality = $mort_total;
    $sold_birds = $bird_sold_nos;
    $sold_weight = $bird_sold_qty;
    if($placed_birds - $mortality - $sold_birds >= 0){
        $shortage = $placed_birds - $mortality - $sold_birds; $excess = 0;
    }
    else{
        $excess = $placed_birds - $mortality - $sold_birds; $shortage = 0;
    }
    $liquid_date = date("d.m.Y",strtotime($end_date));
    $sold_amount = $bird_sold_amt;
    if($sold_amount > 0 && $sold_amount > 0){ $sold_rate = round(($sold_amount / $sold_weight),2); } else{ $sold_rate = 0; }
    $age = $page;

    if($mort_7days > 0 && $placed_birds > 0){ $days7_mort_per = round(($mort_7days / $placed_birds) * 100,2); } else{ $days7_mort_per = 0; }
    if($mort_30days > 0 && $placed_birds > 0){ $days30_mort_per = round(($mort_30days / $placed_birds) * 100,2); } else{ $days30_mort_per = 0; }
    if($mort_ge31days > 0 && $placed_birds > 0){ $daysge31_mort_per = round(($mort_ge31days / $placed_birds) * 100,2); } else{ $daysge31_mort_per = 0; }
    if($mort_total > 0 && $placed_birds > 0){ $daysall_mort_per = round(($mort_total / $placed_birds) * 100,2); } else{ $daysall_mort_per = 0; }
    
    if($sold_weight > 0 && $sold_birds > 0){ $avg_weight = round(($sold_weight / $sold_birds),2); } else{ $avg_weight = 0; }
    if($feed_total_consumed > 0 && $sold_weight > 0){ $fcr = round(($feed_total_consumed / $sold_weight),2); } else{ $fcr = 0; }
    $cfcr = round((((2 - ($avg_weight)) / 4) + $fcr),3);

    if($sold_mean_total > 0 && $sold_birds > 0){ $mean_age = round(($sold_mean_total / $sold_birds),2); } else{ $mean_age = 0; }
    if($avg_weight > 0 && $mean_age > 0){ $day_gain = round((($avg_weight * 1000) / $mean_age),2); } else{ $day_gain = 0; }
    
    $t1 = 0; $t1 = ($placed_birds - $mortality);
    $t2 = 0; $t2 = $placed_birds;
    $t3 = 0; $t3 = $avg_weight;
    $t4 = 0; $t4 = ($fcr * $mean_age);

    if($t1 > 0 && $t2 > 0 && $t3 > 0 && $t4 > 0){
        $eef = round((((((($t1) / $t2) * 100) * $t3) * 100) / ($t4)));
    }
    else{
        $eef = 0;
    }

    $feed_in = $feed_total_in;
    $feed_in_bag = round(($feed_in / 50),2);
    $feed_consumed = $feed_total_consumed;
    $feed_consumed_bag = round(($feed_consumed / 50),2);
    $feed_out = $feed_total_out;
    $feed_out_bag = round(($feed_out / 50),2);
    $feed_balance = $feed_in - $feed_consumed - $feed_out;
    $feed_balance_bag = round(($feed_balance / 50),2);

    $medvac_in = $medvac_total_in;
    $medvac_consumed = $medvac_total_consumed;
    $medvac_out = $medvac_total_out;
    $medvac_balance = $medvac_in - $medvac_consumed - $medvac_out;
    if($medvac_total_in_amt > 0 && $medvac_total_in > 0){ $medvac_price = $medvac_total_in_amt / $medvac_total_in; } else{ $medvac_price = 0; }

    $chick_amount = round(($chick_cost * $placed_birds),2);
    if($chick_amount > 0 && $sold_weight > 0){ if($chick_amount > 0 && $sold_weight > 0){ $chick_amount_pno = round(($chick_amount / $sold_weight),2); } else{ $chick_amount_pno = 0; } } else{ $chick_amount_pno = 0; }
    $feed_amount = round(($feed_cost * $feed_consumed),2);
    if($feed_amount > 0 && $sold_weight > 0){ if($feed_amount > 0 && $sold_weight > 0){ $feed_amount_pno = round(($feed_amount / $sold_weight),2); } else{ $feed_amount_pno = 0; } } else{ $feed_amount_pno = 0; }
    $admin_amount = round(($admin_cost * $placed_birds),2);
    if($admin_amount > 0 && $sold_weight > 0){ if($admin_amount > 0 && $sold_weight > 0){ $admin_amount_pno = round(($admin_amount / $sold_weight),2); } else{ $admin_amount_pno = 0; } } else{ $admin_amount_pno = 0; }
    
    if($medicine_cost == "A" || $medicine_cost == "M"){
        $medvac_amount = round(($medvac_total_consumed_amt),2);
        //$medvac_amount = 0;
        if($medvac_amount > 0 && $sold_weight > 0){ if($medvac_amount > 0 && $sold_weight > 0){ $medvac_amount_pno = round(($medvac_amount / $sold_weight),2); } else{ $medvac_amount_pno = 0; } } else{ $medvac_amount_pno = 0; }
    }
    else{
        $medvac_amount = round(($med_price * $placed_birds),2);
        //$medvac_amount = 0;
        if($medvac_amount > 0 && $sold_weight > 0){ if($medvac_amount > 0 && $sold_weight > 0){ $medvac_amount_pno = round(($medvac_amount / $sold_weight),2); } else{ $medvac_amount_pno = 0; } } else{ $medvac_amount_pno = 0; }
    }
    $total_amount = round(($chick_amount + $feed_amount + $admin_amount + $medvac_amount),2);
    if($total_amount > 0 && $sold_weight > 0){ if($total_amount > 0 && $sold_weight > 0){ $total_amount_pno = round(($total_amount / $sold_weight),2); } else{ $total_amount_pno = 0; } } else{ $total_amount_pno = 0; }
    
    if($total_amount > 0 && $sold_weight > 0){ if($total_amount > 0 && $sold_weight > 0){ $act_prod_cost = round(($total_amount / $sold_weight),2); } else{ $act_prod_cost = 0; } } else{ $act_prod_cost = 0; }
    

    /*********** FCR Production Cost Validation ***********/
    //echo "<br/>".$gc_code."<br/>";
    $sql = "SELECT * FROM `broiler_max_values` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $fcr_count = mysqli_num_rows($query);
    if($fcr_count > 0){
        while($row = mysqli_fetch_assoc($query)){
            $max_prod_cost = round((float)$row['max_prod_cost'],2);
            $max_prod_rate = round((float)$row['max_prod_rate'],2);
            $max_prod_on = $row['max_prod_on'];
        }

    }
    else{
        $max_prod_cost = 999;
    }
    if($act_prod_cost >= $max_prod_cost){
        //$act_prod_cost = $max_prod_cost;
        if($max_prod_on == "sold_bird"){
            $actual_gc_per_kg = $max_prod_rate;
            $actual_gc_amount = round(($actual_gc_per_kg * $sold_birds),2);
            $standard_amount = $standard_cost * $sold_birds;
        }
        else if($max_prod_on == "sold_weight"){
            $actual_gc_per_kg = $max_prod_rate;
            $actual_gc_amount = round(($actual_gc_per_kg * $sold_weight),2);
            $standard_amount = $standard_cost * $sold_weight;
        }
        else{ }
        $fcr_diff = $avg_fcr_cal1 = $avg_fcr_cal2 = 0;

        $bird_shortage_prc = $bird_shortage_amount = $sale_inc_amount = 0;

        $fcr_deduct_prc = $fcr_deduct_amount = $mort_deduct_prc = $mort_deduct_amount = 0;

        $mort_inc_prc = $mort_inc_amount = $summer_inc_prc = $summer_inc_amount = $other_inc_amount = $unload_charges = 0;
        //echo "<br/>".$fcr."-".$max_fcr."<br/>";
        if($fcr > $max_fcr){
            $fcr_diff = $fcr - $max_fcr;
            $avg_fcr_cal1 = $fcr_diff * $avg_weight;
            $avg_fcr_cal2 = $sold_birds * $avg_fcr_cal1;
            $fcr_deduct_amount = round(($avg_fcr_cal2 * $max_feed_cost),2);
        }
        //echo "<br/>".$actual_gc_amount."<br/>".$fcr_diff."<br/>".$fcr."<br/>".$max_fcr."<br/>".$avg_weight."<br/>".$avg_fcr_cal1."<br/>".$avg_fcr_cal2."<br/>".$fcr_deduct_amount."<br/>";
        $total_decentives = $bird_shortage_amount + $fcr_deduct_amount + $mort_deduct_amount;

        $total_incentive =  $actual_gc_amount + $sale_inc_amount + $mort_inc_amount + $summer_inc_amount + $other_inc_amount + $unload_charges;

        $amount_payable = round($total_incentive - $total_decentives,2);

        $total_amount_payable = $total_incentive - $total_decentives - $farmer_sales_amt;

        if($tds_flag == 1 || $tds_flag == "1"){
            $tds_amount = round(($amount_payable * 0.01),2);
        }
        else{
            $tds_amount = 0;
        }
    }
    else{
        $standard_amount = $standard_cost * $sold_weight;
        /*********** Grade ***********/
        $sql = "SELECT * FROM `broiler_farmer_classify` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            if($act_prod_cost >= $row['prod_from_classify'] && $act_prod_cost <= $row['prod_to_classify']){ $grade = $row['grade_classify']; }
        }

        /*********** Production Cost Incentive ***********/
        if(round($act_prod_cost,2) <= round($standard_prod_cost,2)){
            $farmer_perform = "incentive";
            $act_prod_cost_diff = $standard_prod_cost - $act_prod_cost;
            
            $sql = "SELECT * FROM `broiler_gc_pc_incentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
            while($row = mysqli_fetch_assoc($query)){
                $i++;
                $prod_from_incs[$i] = $row['prod_from_inc'];
                $prod_to_incs[$i] = $row['prod_to_inc'];
                $rate_incs[$i] = $row['rate_inc'] / 100;
                $counts[$i] = $i;
            }
            foreach($counts as $cn){
                if($act_prod_cost <= $prod_to_incs[$cn]){
                    $rates = round($prod_to_incs[$cn]) - $act_prod_cost;
                    $rate_inc[$cn] = $rates * $rate_incs[$cn];
                }
                else{
                    $rate_inc[$cn] = 0;
                }
            }
            $prod_inc_rate = 0;
            foreach($counts as $cn){
                $prod_inc_rate = $prod_inc_rate + $rate_inc[$cn];
            }
            $prod_inc_rate = round($prod_inc_rate,2);

            $actual_gc_per_kg = $standard_cost + $prod_inc_rate;
            $actual_gc_amount = round(($actual_gc_per_kg * $sold_weight),2);
            //$sale_incentive_flag = 1; 
        }
        else{
            $farmer_perform = "decentive";
            $act_prod_cost_diff = $act_prod_cost - $standard_prod_cost;

            $sql = "SELECT * FROM `broiler_gc_pc_decentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
            while($row = mysqli_fetch_assoc($query)){
                $i++;
                $prod_from_decs[$i] = $row['prod_from_dec'];
                $prod_to_decs[$i] = $row['prod_to_dec'];
                $rate_decs[$i] = $row['prod_rate_dec'] / 100;
                $counts[$i] = $i;
            }
            foreach($counts as $cn){
                if($act_prod_cost > $prod_from_decs[$cn]){
                    $rates = $act_prod_cost - round($prod_from_decs[$cn]);
                    $rate_dec[$cn] = $rates * $rate_decs[$cn];
                }
                else{
                    $rate_dec[$cn] = 0;
                }
            }
            $prod_dec_rate = 0;
            foreach($counts as $cn){
                $prod_dec_rate = $prod_dec_rate + $rate_dec[$cn];
            }
            $prod_dec_rate = round($prod_dec_rate,2);

            $actual_gc_per_kg = $standard_cost - $prod_dec_rate;
            if($actual_gc_per_kg <= $minimum_cost){ $actual_gc_per_kg = $minimum_cost; } else{ $actual_gc_per_kg = round($actual_gc_per_kg,3); $sale_incentive_flag = 1;  }
            $actual_gc_amount = round(($actual_gc_per_kg * $sold_weight),2);
        }
    }
        
    /*********** Sales Incentive ***********/
    $sql = "SELECT * FROM `broiler_gc_si_incentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
    while($row = mysqli_fetch_assoc($query)){
        $i++;
        $sales_from_incs[$i] = $row['sales_from_inc'];
        $sales_to_incs[$i] = $row['sales_to_inc'];
        $sales_rate_incs[$i] = $row['sales_rate_inc'];
        $sales_max_rates = $row['sales_max_rate'];
        $counts[$i] = $i;
    }
    $new_count = array(); $i = 0;
    foreach($counts as $cn){
        if($sold_rate >= $sales_from_incs[$cn]){
            $i++;
            if($sold_rate > $sales_to_incs[$cn]){
                $rates = round($sales_to_incs[$cn]) - round($sales_from_incs[$cn]);
                $rate_inc[$cn] = $rates * $sales_rate_incs[$cn];
            }
            else{
                $rates = $sold_rate - round($sales_from_incs[$cn]);
                $rate_inc[$cn] = $rates * $sales_rate_incs[$cn];
            }
            $new_count[$i] = $i;
        }
    }
    $sale_inc_prc = 0;
    foreach($new_count as $cn){
        $sale_inc_prc = $sale_inc_prc + $rate_inc[$cn];
    }
    //if($sale_incentive_flag == 0){ $sale_inc_prc = 0; } else{ }
    if($sale_inc_prc >= $sales_max_rates){ $sale_inc_prc = $sales_max_rates; } else{$sale_inc_prc = round($sale_inc_prc,2); }
    $sale_inc_amount = round(($sale_inc_prc * $sold_weight),2);

    /*********** Mortality Incentive ***********/
    $mort_inc_prc = $mort_inc_amount = 0;

    $sql = "SELECT * FROM `broiler_gc_mi_incentive` WHERE `std_code` = '$gc_code' AND `mort_from_inc` <= '$daysall_mort_per' AND `mort_to_inc` >= '$daysall_mort_per'"; $query = mysqli_query($conn,$sql); $minc_count = mysqli_num_rows($query);
    if($minc_count > 0){
        while($row = mysqli_fetch_assoc($query)){
            $mort_inc_prc = $row['mort_rate_inc'];
            $mort_inc_amount = round(($row['mort_rate_inc'] * $sold_birds),2);
        }
    }
    else{ }
    
    $summer_inc_prc = $summer_inc_amount = $other_inc_amount = $unload_charges = 0;

    $total_incentive =  $actual_gc_amount + $sale_inc_amount + $mort_inc_amount + $summer_inc_amount + $other_inc_amount + $unload_charges;

    /*********** Shortage Calculation ***********/
    $sql = "SELECT * FROM `broiler_gc_st_decentive` WHERE `std_code` = '$gc_code'"; $query = mysqli_query($conn,$sql); $i = 0;
    while($row = mysqli_fetch_assoc($query)){
        if($row['sprod_flag'] == 1){ $bird_shortage_amount = round($standard_prod_cost * ($shortage * $avg_weight),2); }
        else if($row['prod_flag'] == 1){ $bird_shortage_amount = round($act_prod_cost * ($shortage * $avg_weight),2); }
        else if($row['sale_flag'] == 1){ $bird_shortage_amount = round($sold_rate * ($shortage * $avg_weight),2); }
        else if($row['high_flag'] == 1){ $bird_shortage_amount = round(max($standard_prod_cost,$act_prod_cost,$sold_rate) * ($shortage * $avg_weight),2); }
        else{ $bird_shortage_amount = 0; }
    }
    
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Farmer TDS' AND `field_function` LIKE 'Deduction' AND `flag` = 1"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $tds_flag = $row['flag']; } if($tds_flag == 1 || $tds_flag == "1"){ $tds_flag = 1; } else{ $tds_flag = 0; }

    if($bird_shortage_amount > 0 && $sold_weight > 0){ if($bird_shortage_amount > 0 && $sold_weight > 0){ $bird_shortage_prc = $bird_shortage_amount / $sold_weight; } else{ $bird_shortage_prc = 0; } } else{ $bird_shortage_prc = 0; }
    

    $fcr_deduct_prc = $fcr_deduct_amount = $mort_deduct_prc = $mort_deduct_amount = 0;
    //echo "<br/>".$fcr."-".$max_fcr."<br/>";
    if($fcr > $max_fcr){
        $fcr_diff = $fcr - $max_fcr;
        /*$avg_fcr_cal1 = $fcr_diff * $avg_weight;
        $avg_fcr_cal2 = $sold_birds * $avg_fcr_cal1;
        $fcr_deduct_amount = round(($avg_fcr_cal2 * $max_feed_cost),2);
        */
        $fcr_deduct_amount = round(((float)$fcr_diff * (float)$sold_weight * (float)$max_feed_cost),3);
    }
    $total_decentives = $bird_shortage_amount + $fcr_deduct_amount + $mort_deduct_amount + $bis_camt;

    $amount_payable = round($total_incentive - $total_decentives,2);

    $total_amount_payable = $total_incentive - $total_decentives - $farmer_sales_amt;

    if($tds_flag == 1 || $tds_flag == "1"){
        $tds_amount = round(($amount_payable * 0.01),2);
    }
    else{
        $tds_amount = 0;
    }
    

    $other_deduct_amount = 0;

    $farmer_payable = $total_amount_payable - $tds_amount;


    $farm_value = 
        $placement_date
    ."@".$placed_birds
    ."@".$mortality
    ."@".$sold_birds
    ."@".$sold_weight
    ."@".$excess
    ."@".$shortage
    ."@".$liquid_date
    ."@".$sold_amount
    ."@".$sold_rate
    ."@".$age
    ."@".$days7_mort_per
    ."@".$days30_mort_per
    ."@".$daysge31_mort_per
    ."@".$daysall_mort_per
    ."@".$fcr
    ."@".$cfcr
    ."@".$avg_weight
    ."@".$mean_age
    ."@".$day_gain
    ."@".$eef
    ."@".$grade
    ."@".$feed_in
    ."@".$feed_consumed
    ."@".$feed_out
    ."@".$feed_balance
    ."@".$feed_in_bag
    ."@".$feed_consumed_bag
    ."@".$feed_out_bag
    ."@".$feed_balance_bag
    ."@".$medvac_in
    ."@".$medvac_consumed
    ."@".$medvac_out
    ."@".$medvac_balance
    ."@".$chick_amount
    ."@".$chick_amount_pno
    ."@".$feed_amount
    ."@".$feed_amount_pno
    ."@".$admin_amount
    ."@".$admin_amount_pno // 40
    ."@".$medvac_amount
    ."@".$medvac_amount_pno
    ."@".$total_amount
    ."@".$total_amount_pno
    ."@".$act_prod_cost
    ."@".$standard_cost
    ."@".$standard_amount
    ."@".$actual_gc_per_kg
    ."@".$actual_gc_amount
    ."@".$sale_inc_prc
    ."@".$sale_inc_amount
    ."@".$mort_inc_prc
    ."@".$mort_inc_amount
    ."@".$summer_inc_prc
    ."@".$summer_inc_amount
    ."@".$other_inc_amount
    ."@".$unload_charges//57
    ."@".$total_incentive
    ."@".$bird_shortage_amount
    ."@".$fcr_deduct_amount
    ."@".$mort_deduct_amount
    ."@".$total_decentives
    ."@".$amount_payable
    ."@".$tds_amount
    ."@".$other_deduct_amount
    ."@".$farmer_payable
    ."@".$branch_name
    ."@".$line_name
    ."@".$batch_name
    ."@".$standard_prod_cost
    ."@".$farmer_sales_amt
    ."@".$total_amount_payable
    ."@".round($bird_shortage_prc,3) //72
    /*73*/."@".$bis_cqty
    /*74*/."@".$bis_cprc
    /*75*/."@".$bis_camt
    ;
    
    echo $farm_value;
}