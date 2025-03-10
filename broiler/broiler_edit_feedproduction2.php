<?php
//broiler_edit_feedproduction2.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['feedproduction2'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Feed Mill' AND `field_function` = 'Min Wastage per'"; $query = mysqli_query($conn,$sql); $extras_count = mysqli_num_rows($query);
        if($extras_count > 0){ while($row = mysqli_fetch_assoc($query)){ $auto_Wastage_per = $row['flag']; } } else{ $auto_Wastage_per = 0; }
        
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

        $sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `feedmill_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $bag_size_code[$row['code']] = $row['code']; $bag_size_value[$row['code']] = $row['bag_size']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Premix%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Finishing Material%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Finished Goods%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%Raw Materials%' OR `description` LIKE '%Poultry%' OR `description` LIKE '%Aqua%' OR `description` LIKE '%Vitamins Premix%' OR `description` LIKE '%Dairy%' OR `description` LIKE '%Vitamins%' OR `description` LIKE '%Raw Ingredients%' OR `description` LIKE '%Finished Products%')"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bag%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `broiler_feed_formula` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $formula_code[$row['code']] = $row['code']; $formula_name[$row['code']] = $row['description']; $formula_total_qty[$row['code']] = $row['total_qty']; $formula_item[$row['code']] = $row['formula_item_code']; $formula_mill[$row['code']] = $row['mill_code']; }
        $feedmill_type_code = "";
        $sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
        }
        $sql = "SELECT * FROM `broiler_feed_expense` WHERE `id` IN (SELECt MAX(id) as id FROM `broiler_feed_expense` WHERE `active` = '1' AND `dflag` = '0' GROUP BY mill_code,feed_type ORDER BY code ASC)"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $key_code = $row['mill_code']."@".$row['feed_type'];
            $labour_charge[$key_code] = $row['labour_charge'];
            $packing_charge[$key_code] = $row['packing_charge'];
            $transport_charge[$key_code] = $row['transport_charge'];
            $electric_charge[$key_code] = $row['electric_charge'];
            $other_charge[$key_code] = $row['other_charge'];
            $other_charge2[$key_code] = $row['other_charge2'];
        }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Production' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Production' AND `field_function` LIKE 'Auto Avg Price'"; $query = mysqli_query($conn,$sql); $autoavgprice_flag = 0; $aapcount = mysqli_num_rows($query);
        if($aapcount > 0){ while($row = mysqli_fetch_assoc($query)){ $autoavgprice_flag = $row['flag']; } } else{ $autoavgprice_flag = 0; } if($autoavgprice_flag == "" || $autoavgprice_flag == 0){ $autoavgprice_flag = 0; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') AND `active` = '1' ".$sector_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `feedmill_expenses_parameters`"; $query = mysqli_query($conn,$sql); $fep_count = mysqli_num_rows($query);
        if($fep_count > 0){
            while($row = mysqli_fetch_assoc($query)){ $desc_array[$row['name']] = $row['displayname']; }
        }
        else{
            $desc_array['labourcharge'] = "Labour Charge";
            $desc_array['packingcharge'] = "Packing Charge";
            $desc_array['electricalcharge'] = "Electrical Charge";
            $desc_array['transportcharge'] = "Transport Charge";
            $desc_array['othercharge'] = "Other Charge";
            $desc_array['othercharge2'] = "Other Charge2";
        }
        
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <?php
            $code = $_GET['trnum'];
            $sql = "SELECT *  FROM `broiler_feed_production` WHERE `code` LIKE '$code'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $fm_prod_date = $row['date'];
                $fm_prod_dcno = $row['dcno'];
                $fbatch_no = $row['fbatch_no'];
                $make_date = $row['make_date'];
                $exp_date = $row['exp_date'];
                $fm_prod_feed_mill = $row['feed_mill'];
                $fm_prod_feed_code = $row['feed_code'];
                $fm_prod_formula_code = $row['formula_code'];
                $fm_prod_total_tons = $row['total_tons'];
                $fm_prod_silos_type = $row['silos_type'];
                $fm_prod_consumed_quantity = $row['consumed_quantity'];
                $fm_prod_previous_leftover = $row['previous_leftover'];
                $fm_prod_wastage_quantity = $row['wastage_quantity'];
                $fm_prod_wastage_per = $row['wastage_per'];
                $fm_prod_wastage_price = $row['wastage_price'];
                $fm_prod_wastage_amount = $row['wastage_amount'];
                $fm_prod_produced_quantity = $row['produced_quantity'];
                $final_item_prod_price = $row['produced_price'];
                $final_item_prod_amount = $row['produced_amount'];
                $fm_prod_current_leftover = $row['current_leftover'];
                $fm_prod_bag_code = $row['bag_code'];
                $fm_prod_bag_total = $row['bag_total'];
                $fm_prod_bag_code_feed = $row['bag_code_feed'];
                $fm_prod_no_of_bags_feed = $row['no_of_bags_feed'];
                $fm_prod_bag_code_empty = $row['bag_code_empty'];
                $fm_prod_no_of_bags_empty = $row['no_of_bags_empty'];
                $fm_prod_item_cost = $row['item_cost'];
                $fm_prod_labour_cost = $row['labour_cost'];
                $fm_prod_packing_cost = $row['packing_cost'];
                $fm_prod_electricity_cost = $row['electricity_cost'];
                $fm_prod_transport_cost = $row['transport_cost'];
                $fm_prod_bag_amount = $row['bag_amount'];
                $fm_prod_other_cost = $row['other_cost'];
                $fm_prod_other_cost2 = $row['other_cost2'];
                $fm_prod_total_cost = $row['total_cost'];
                $margin_per = $row['margin_value'];
                $margin_amount = $row['margin_amount'];
                $margin_type = $row['margin_type'];
                $fm_prod_remarks = $row['remarks'];
            }
            $sql = "SELECT *  FROM `broiler_feed_consumed` WHERE `link_trnum` LIKE '$code'";
            $query = mysqli_query($conn,$sql); $incr_count = $item_list_size = mysqli_num_rows($query); $item_list = ""; $formula_items = array();
            while($row = mysqli_fetch_assoc($query)){
                if($item_list == ""){
                    $item_list = $row['item_code'];
                }
                else{
                    $item_list = $item_list."','".$row['item_code'];
                }
                $formula_items[$row['item_code']] = $row['item_code'];
            }
            if($code == ""){ $trnum_filter = ""; } else{ $trnum_filter = " AND `trnum` NOT IN ('$code')"; }
            if(sizeof($formula_items) > 0){
                $current_stock = $current_price = $current_amount = array();
        
                $icats = ""; foreach($formula_items as $fitems){ if(!empty($item_category[$fitems])){ if($icats == ""){ $icats = $item_category[$fitems]; } else{ $icats = $icats."','".$item_category[$fitems]; } } }
                $sql = "SELECT * FROM `item_category` WHERE `code` IN ('$icats')"; $query = mysqli_query($conn,$sql); $icat_iac = "";
                while($row = mysqli_fetch_assoc($query)){ if($icat_iac == ""){ $icat_iac = $row['iac']; } else{ $icat_iac = $icat_iac."','".$row['iac']; } }
        
                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$icat_iac') AND `date` <= '$fm_prod_date' AND `item_code` IN ('$item_list')".$trnum_filter." AND `location` IN ('$fm_prod_feed_mill') AND `active` = 1 AND `dflag` = 0 ORDER BY `date`,`id` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($row['crdr'] == "CR"){
                        $current_stock[$row['item_code']] = $current_stock[$row['item_code']] - $row['quantity'];
                        $current_amount[$row['item_code']] = $current_amount[$row['item_code']] - ($current_price[$row['item_code']] * $row['quantity']);
                    }
                    else if($row['crdr'] == "DR"){
                        $current_stock[$row['item_code']] = $current_stock[$row['item_code']] + $row['quantity'];
                        $current_amount[$row['item_code']] = $current_amount[$row['item_code']] + $row['amount'];
                        if($current_stock[$row['item_code']] != 0){
                            $current_price[$row['item_code']] = round(($current_amount[$row['item_code']] / $current_stock[$row['item_code']]),2);
                        }
                        else{
                            $current_price[$row['item_code']] = 0;
                        }
                    }
                    else{ }
                }
            }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Feed Production</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_feedproduction2.php" method="post" role="form" enctype="multipart/form-data" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control datepicker" style="width:100px;" value="<?php echo date('d.m.Y',strtotime($fm_prod_date)); ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Dc No.</label>
							                <input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $fm_prod_dcno; ?>" style="width:90px;" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Feed Mill<b style="color:red;">&nbsp;*</b></label>
							                <select name="feed_mill" id="feed_mill" class="form-control select2" style="width:160px;" onchange="fetch_feed_details()">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($fm_prod_feed_mill == $prod_code){ echo "selected"; } ?>><?php echo $sector_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Feed Name<b style="color:red;">&nbsp;*</b></label>
							                <select name="feed_code" id="feed_code" class="form-control select2" style="width:160px;" onchange="fetch_feed_formula()">
                                                <option value="select">select</option>
                                                <?php foreach($feed_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($fm_prod_feed_code == $prod_code){ echo "selected"; } ?>><?php echo $feed_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Formula<b style="color:red;">&nbsp;*</b></label>
							                <select name="formula_code" id="formula_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php
                                                foreach($formula_code as $fcode){
                                                    $fmill = $formula_mill[$fcode];
                                                    $fitem = $formula_item[$fcode];
                                                    if($fitem == $fm_prod_feed_code){ //$fmill == $fm_prod_feed_mill && 
                                                    ?>
                                                        <option value="<?php echo $formula_code[$fcode]; ?>" <?php if($fm_prod_formula_code == $formula_code[$fcode]){ echo "selected"; } ?>><?php echo $formula_name[$fcode];; ?></option>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>No. of Tons<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="total_tons" id="total_tons" class="form-control" value="<?php echo round($fm_prod_total_tons); ?>" style="width:90px;" />
                                        </div>
                                        <div class="form-group"><br/>
                                            <button type="button" name="fetch_details" id="fetch_details" class="btn btn-sm bg-purple" onclick="calculate_feed_quantity()">Fetch Details</button>
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo round($incr_count); ?>" style="width:90px;" />
                                        </div>
                                    </div>
                                    <div class="row" id="production_info" Style="" align="center">
                                        <div class="col-md-12" align="center">
                                        <table class="w-80" style="width:auto;">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align:center;"><label><b style="color:red;">Output Item</b></label></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th><label>Total Items Consumed<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="consumed_total" id="consumed_total" class="form-control" value="<?php echo round($fm_prod_consumed_quantity,3); ?>" style="width:110px;" readonly /></td>
                                                    <th><label>Out Feed Quantity in Kg's<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="produced_total" id="produced_total" class="form-control" value="<?php echo round($fm_prod_produced_quantity,3); ?>" style="width:110px;" onkeyup="calculate_wastage()" /></td>
                                                </tr>
                                                <tr>
                                                    <th><label>Wastage Kg's</label></th>
                                                    <td><input type="text" name="wastage_kg" id="wastage_kg" class="form-control" value="<?php echo round($fm_prod_wastage_quantity,2); ?>" style="width:110px;" readonly /></td>
                                                    <th style='visibility:hidden;'><label>Bags</label></th>
                                                    <td style='visibility:hidden;'><input type="text" name="input_bags" id="input_bags" class="form-control" value="0" style="width:110px;" /></td>
                                                </tr>
                                                <tr>
                                                    <th><label>Wastage %</label></th>
                                                    <td><input type="text" name="wastage_per" id="wastage_per" class="form-control" value="<?php echo round($fm_prod_wastage_per,2); ?>" style="width:110px;" readonly /></td>
                                                    <th colspan="2" style="background: blue;color:yellow;text-align:center;">Costing Details</th>
                                                </tr>
                                                <tr>
                                                    <th><label>Wastage Cost</label></th>
                                                    <td><input type="text" name="wastage_cost" id="wastage_cost" class="form-control" value="<?php echo round($fm_prod_wastage_amount,2); ?>" style="width:110px;" readonly /></td>
                                                    <th><label>Input Items Cost</label></th>
                                                    <td><input type="text" name="input_cost" id="input_cost" class="form-control" value="<?php echo round($fm_prod_item_cost,2); ?>" style="width:110px;font-weight: bold;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="background: blue;color:yellow;text-align:center;">Bag Details For Feed</th>
                                                    <th><?php echo $desc_array['labourcharge']; ?></th>
                                                    <td><input type="text" name="labour_charge" id="labour_charge" class="form-control" value="<?php echo round($fm_prod_labour_cost,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <th>Feed Bag Name</th>
                                                    <th>No.of Bags</th>
                                                    <th><?php echo $desc_array['packingcharge']; ?></th>
                                                    <td><input type="text" name="packing_charge" id="packing_charge" class="form-control" value="<?php echo round($fm_prod_packing_cost,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <td><select name="bag_code_feed" id="bag_code_feed" class="form-control select2" onchange="fetch_bag_count();calculate_expenses_bagtype();"><option>-select-</option><?php foreach($bag_code as $bcode){ ?><option value="<?php echo $bag_code[$bcode]; ?>" <?php if($fm_prod_bag_code_feed == $bag_code[$bcode]){ echo "selected"; } ?>><?php echo $bag_name[$bcode]; ?></option> <?php } ?></select></td>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td><input type="text" name="no_of_bags_feed" id="no_of_bags_feed" class="form-control" value="<?php echo round($fm_prod_no_of_bags_feed,2); ?>" style="width:60px;" onkeyup="validatenum(this.id);calculate_wastage();calculate_expenses_bagtype();" /></td>
                                                                <td style='visibility:hidden;'><input type="text" name="price_of_bags_feed" id="price_of_bags_feed" class="form-control" style="width:50px;" readonly /></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <th><?php echo $desc_array['electricalcharge']; ?></th>
                                                    <td><input type="text" name="electric_charge" id="electric_charge" class="form-control" value="<?php echo round($fm_prod_electricity_cost,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th><?php echo $desc_array['transportcharge']; ?></th>
                                                    <td><input type="text" name="transport_charge" id="transport_charge" class="form-control" value="<?php echo round($fm_prod_transport_cost,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th>Bag Cost</th>
                                                    <td><input type="text" name="bag_amount" id="bag_amount" class="form-control" value="<?php echo round($fm_prod_bag_amount,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th><?php echo $desc_array['othercharge']; ?></th>
                                                    <td><input type="text" name="other_charge" id="other_charge" class="form-control" value="<?php echo round($fm_prod_other_cost,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th><?php echo $desc_array['othercharge2']; ?></th>
                                                    <td><input type="text" name="other_charge2" id="other_charge2" class="form-control" value="<?php echo round($fm_prod_other_cost2,2); ?>" style="width:110px;" onkeyup="validatenum(this.id);calculate_total_cost();" /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th>Total Cost</th>
                                                    <td><input type="text" name="total_cost" id="total_cost" class="form-control" value="<?php echo round($fm_prod_total_cost,2); ?>" style="width:110px;" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th>Per Bag Cost</th>
                                                    <?php
                                                        if($fm_prod_no_of_bags_feed != 0){
                                                            $bag_cost = $fm_prod_total_cost / $fm_prod_no_of_bags_feed;
                                                        }else{
                                                            $bag_cost = 0;
                                                        }
                                                        if($fm_prod_produced_quantity != 0){
                                                            $fm_prod_price = $fm_prod_total_cost / $fm_prod_produced_quantity;
                                                        }
                                                        else{
                                                            $fm_prod_price = 0;
                                                        }
                                                        
                                                    ?>
                                                    <td><input type="text" name="bag_cost" id="bag_cost" class="form-control" value="<?php echo round(($bag_cost),2); ?>" style="width:110px;" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th>Cost/Kg</th>
                                                    <td><input type="text" name="cpkg_cost" id="cpkg_cost" class="form-control" value="<?php echo round(($fm_prod_price),2); ?>" style="width:110px;" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th>Fixed Cost</th>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td><input type="text" name="margin_per" id="margin_per" class="form-control" value="<?php echo round(($margin_per),2); ?>" style="width:50px;" onkeyup="calculate_total_cost();"/></td>
                                                                <td><input type="text" name="margin_amount" id="margin_amount" class="form-control" value="<?php echo round(($margin_amount),2); ?>" style="width:110px;" onkeyup="calculate_margin_cost();" onchange="calculate_total_cost();" /></td>
                                                                <td style="visibility:hidden;"><input type="text" name="margin_type" id="margin_type" class="form-control" value="<?php echo $margin_type; ?>" style="width:10px;" /></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th>Fixed Cost/Kg</th>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td><input type="text" name="final_item_prod_price" id="final_item_prod_price" class="form-control" value="<?php echo round(($final_item_prod_price),2); ?>" style="width:50px;" readonly /></td>
                                                                <td><input type="text" name="final_item_prod_amount" id="final_item_prod_amount" class="form-control" value="<?php echo round(($final_item_prod_amount),2); ?>" style="width:110px;" readonly /></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table><br/>
                                            <table class="w-80" style="width:auto;">
                                                <tr>
                                                    <th colspan="7" style="background: blue;color:yellow;text-align:center;">Input Items</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="background: blue;color:yellow;text-align:center;">Item Name</th>
                                                    <th colspan="1" style="background: blue;color:yellow;text-align:center;">Quantity</th>
                                                    <th colspan="1" style="background: blue;color:yellow;text-align:center;">Price</th>
                                                    <th colspan="1" style="background: blue;color:yellow;text-align:center;">Amount</th>
                                                    <th colspan="1" style="background: blue;color:yellow;text-align:center;">Stock</th>
                                                    <th colspan="1" style="background: blue;color:yellow;text-align:center;"></th>
                                                </tr>
                                                <tbody id="row_body">
                                                <?php
                                                    $sql = "SELECT *  FROM `broiler_feed_consumed` WHERE `link_trnum` LIKE '$code'"; $query = mysqli_query($conn,$sql); $c = 0; $row_values = "";
                                                    while($row = mysqli_fetch_assoc($query)){
                                                        $icds = $row['item_code']; $c++;
                                                        $total_item_qty = $current_stock[$icds];
                                                        if($row['quantity'] > 0){
                                                            $price = round(($row['amount'] / $row['quantity']),3);
                                                        }
                                                        else{
                                                            $price = 0;
                                                        }
                                                        //echo "<tr><td colspan='1'></td><td colspan='1'><input type='text' name='itm_names[]' id='itm_names[]' class='form-control' value='".$item_name[$icode]."' style='border:none;' readonly /></td><td><input type='text' name='itm_qtys[]' id='itm_qtys[]' class='form-control' value='".round($row['quantity'],2)."' style='padding-right:10px;text-align:right;border:none;' readonly /></td><td colspan='1' style='visibility:hidden;'><input type='text' name='itm_amt[]' id='itm_amt[]' class='form-control' value='".$row['amount']."' readonly /></td></tr>";

                                                        $row_values .= "<tr id='row_no[".$c."]' style='padding:1px;'><td colspan='1'></td><td colspan='1'>";
                                                        $row_values .= "<select name='itm_names[]' id='itm_names[".$c."]' class='form-control select2' style='padding:0;width:180px;' onchange='fetch_item_avg_price(this.id)'><option value='select'>select</option>";
                                                        foreach($item_code as $itsc){
                                                            if($itsc == $icds){
                                                                $row_values .= "<option value='".$itsc."' selected >".$item_name[$itsc]."</option>";
                                                            }
                                                            else{
                                                                $row_values .= "<option value='".$itsc."'>".$item_name[$itsc]."</option>";
                                                            }
                                                            
                                                        }
                                                        $row_values .= "</select></td>";
                                                        $row_values .= "<td style='width:90px;'><input type='text' name='itm_qtys[]' id='itm_qtys[".$c."]' class='form-control' value='".round($row['quantity'],3)."' style='padding-right:10px;text-align:right;' onkeyup='calculate_final_feed_amount()' /></td>";
                                                        
                                                        $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='itm_prc[]' id='itm_prc[".$c."]' class='form-control' value='".$price."' style='padding-right:10px;text-align:right;' readonly /></td>";
                                                        $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='itm_amt[]' id='itm_amt[".$c."]' class='form-control' value='".$row['amount']."' style='padding-right:10px;text-align:right;' readonly /></td>";
                                                        if($row['quantity'] > $total_item_qty){
                                                            $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='available_stock[]' id='available_stock[".$c."]' class='form-control' value='".$total_item_qty."' style='padding-right:10px;text-align:right;color:red;' readonly /></td>";
                                                        }
                                                        else{
                                                            $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='available_stock[]' id='available_stock[".$c."]' class='form-control' value='".$total_item_qty."' style='padding-right:10px;text-align:right;' readonly /></td>";
                                                        }
                                                        
                                                        
                                                        if($item_list_size == 1 && $c == 1){
                                                            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 5px;'><br class='labelrow' style='display:none;' /><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;</div>";
                                                        }
                                                        else if($c == 1){
                                                            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 5px;visibility:hidden;'><br class='labelrow' style='display:none;' /><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;</div>";
                                                        }
                                                        else if($item_list_size == $c){
                                                            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 5px;'><br class='labelrow' style='display:none;' /><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;<a href='javascript:void(0);' id='deductrow[".$c."]' onclick='destroy_row(this.id)'><i class='fa fa-minus' style='color:red;'></i></a></div>";
                                                        }
                                                        else{
                                                            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 5px;visibility:hidden;'><br class='labelrow' style='display:none;' /><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;<a href='javascript:void(0);' id='deductrow[".$c."]' onclick='destroy_row(this.id)'><i class='fa fa-minus' style='color:red;'></i></a></div>";
                                                        }
                                                        $row_values .= "</tr>";
                                                    }
                                                    echo $row_values;
                                                ?>
                                                </tbody>
                                                <tbody id="final_values">
                                                    <?php
                                                    if($fm_prod_consumed_quantity > 0){
                                                        $final_total_price = $fm_prod_item_cost / $fm_prod_consumed_quantity;
                                                    }
                                                    else{
                                                        $final_total_price = 0;
                                                    }
                                                    
                                                    echo "<tr><td colspan='1'></td><td colspan='1'><b>Total</b></td><td><input type='text' name='final_total_qty' id='final_total_qty' class='form-control' value='".round($fm_prod_consumed_quantity,3)."' style='padding-right:10px;text-align:right;border:none;' readonly /></td><td colspan='1' style='visibility:visible;'><input type='text' name='final_avg_price' id='final_avg_price' class='form-control' value='".$final_total_price."'  style='padding-right:10px;text-align:right;visibility:visible;' readonly /></td><td colspan='1' style='visibility:visible;'><input type='text' name='final_total_amt' id='final_total_amt' class='form-control' value='".$fm_prod_item_cost."' style='visibility:visible;' readonly /></td></tr>";
                                                    ?>
                                                </tbody>
                                                <tr>
                                                    <th colspan="7" style="background: blue;color:yellow;text-align:center;">Bag Details For Empty</th>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <th>Bag Name</th>
                                                    <th>No.of Bags</th>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><select name="bag_code_empty" id="bag_code_empty" class="form-control select2"><option>-select-</option><?php foreach($bag_code as $bcode){ ?><option value="<?php echo $bag_code[$bcode]; ?>" <?php if($fm_prod_bag_code_empty == $bag_code[$bcode]){ echo "selected"; } ?>><?php echo $bag_name[$bcode]; ?></option> <?php } ?></select></td>
                                                    <td><input type="text" name="no_of_bags_empty" id="no_of_bags_empty" class="form-control" value="<?php echo round($fm_prod_no_of_bags_empty,2); ?>" style="width:110px;" /></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table>
                                                <tr>
                                                    <th colspan="3" style="background: blue;color:yellow;text-align:center;">Batch Details</th>
                                                </tr>
                                                <tr>
                                                    <th>Batch No.</th>
                                                    <th>Manufacture Date</th>
                                                    <th>Expiry Date</th>
                                                </tr>
                                                <tr>
                                                    <td><input type="text" name="fbatch_no" id="fbatch_no" class="form-control" value="<?php echo $fbatch_no; ?>" style="width:200px;" /></td>
                                                    <td><input type="text" name="make_date" id="make_date" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($make_date)); ?>" style="width:200px;" readonly /></td>
                                                    <td><input type="text" name="exp_date" id="exp_date" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($exp_date)); ?>" style="width:200px;" readonly /></td>
                                                </tr>
                                        </table>
                                        </div>
                                    </div><br/>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Reference Document-1</label>
                                                <input type="file" name="prod_doc_1" id="prod_doc_1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Reference Document-2</label>
                                                <input type="file" name="prod_doc_2" id="prod_doc_2" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Reference Document-3</label>
                                                <input type="file" name="prod_doc_3" id="prod_doc_3" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $code; ?>">
                                        </div>
                                        <div class="form-group" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:90px;">
                                        </div>
                                    </div>
                                    <div class="col-12"><br/><br/>
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_feedproduction2.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var l = true;
                var feed_mill =document.getElementById("feed_mill"). value;
                var feed_code =document.getElementById("feed_code"). value;
                var formula_code =document.getElementById("formula_code"). value;
                var total_tons =document.getElementById("total_tons"). value;
                var final_item_prod_amount =document.getElementById("final_item_prod_amount"). value; if(final_item_prod_amount == ""){ final_item_prod_amount = 0; }

                if(feed_mill.match("select")){
                    alert("select Feemill");
                    document.getElementById("feed_mill").focus();
                    l = false;
                }
                else if(feed_code.match("select")){
                    alert("select Feed Name");
                    document.getElementById("feed_code").focus();
                    l = false;
                }
                else if(formula_code.match("select")){
                    alert("select Formula");
                    document.getElementById("formula_code").focus();
                    l = false;
                }
                else if(total_tons == "" || total_tons.length == 0){
                    alert("Enter Total Tons");
                    document.getElementById("total_tons").focus();
                    l = false;
                }
                else if(parseFloat(final_item_prod_amount) == 0){
                    alert("Enter production details");
                    document.getElementById("total_tons").focus();
                    l = false;
                }
                else{
                    if(l == true){
                        //Stock Check
                        var incrs = document.getElementById("incr").value; var d = qty = stock = 0;
                        var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                        if(stockcheck_flag == 1){
                            for(d = 1;d <= incrs;d++){
                                if(l == true){
                                    c = d;
                                    qty = document.getElementById("itm_qtys["+d+"]").value;
                                    stock = document.getElementById("available_stock["+d+"]").value;
                                    if(parseFloat(qty) > parseFloat(stock)){
                                        alert("Stock not Available in row: "+c);
                                        document.getElementById("itm_qtys["+d+"]").focus();
                                        l = false;
                                    }
                                }
                            }
                        }
                    }
                }
                if(l == true){
                    var x = confirm("Would you like to save the transaction?");
                    if(x === true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']" style="padding:1px;"><td colspan="1"></td><td colspan="1">';
                html += '<select name="itm_names[]" id="itm_names['+d+']" class="form-control itm_select2" style="padding:0;width:180px;" onchange="fetch_item_avg_price(this.id)"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>';
                html += '<td style=""><input type="text" name="itm_qtys[]" id="itm_qtys['+d+']" class="form-control" value="".round($item_quantity[$icds],2)."" style="padding-right:10px;text-align:right;" onkeyup="calculate_final_feed_amount()" /></td>';
                html += '<td colspan="1" style="visibility:visible;"><input type="text" name="itm_prc[]" id="itm_prc['+d+']" class="form-control" value="".$avg_price."" style="padding-right:10px;text-align:right;" readonly /></td>';
                html += '<td colspan="1" style="visibility:visible;"><input type="text" name="itm_amt[]" id="itm_amt['+d+']" class="form-control" value="".$total_item_amt."" style="padding-right:10px;text-align:right;" readonly /></td>';
                html += '<td><div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '</tr>';
                $('#row_body').append(html); $('.itm_select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_feed_amount();
            }
            function calculate_feed_quantity(){
                var feed_mill = document.getElementById("feed_mill").value;
                var feed_code = document.getElementById("feed_code").value;
                var formula_code = document.getElementById("formula_code").value;
                var total_tons = document.getElementById("total_tons").value;
                var date = document.getElementById("date").value;
                var auto_Wastage_per = parseFloat('<?php echo $auto_Wastage_per; ?>');

                $('#row_body tr').remove(); $('#final_values tr').remove();
                var auto_wastage_kgs = auto_wastage_amount = no_of_bags_feed = 0;

                //Fetch Feed Expenses
                var total_qty = labour_charge = packing_charge = transport_charge = electric_charge = other_charge = other_charge2 = 0; var exp_type = "";
                if(!feed_code.match("select") && feed_code.length > 0 && !feed_mill.match("select") && feed_mill.length > 0 && !formula_code.match("select") && formula_code.length > 0){
                    var fetch_expenses = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_feedmill_expenses.php?feed_mill="+feed_mill+"&feed_code="+feed_code+"&formula_code="+formula_code+"&total_tons="+total_tons+"&date="+date;
                    //window.open(url);
                    var asynchronous = true;
                    fetch_expenses.open(method, url, asynchronous);
                    fetch_expenses.send();
                    fetch_expenses.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var elist = this.responseText;
                            var exp_lists = elist.split("@");
                            total_qty = exp_lists[0];
                            labour_charge = exp_lists[1];
                            packing_charge = exp_lists[2];
                            transport_charge = exp_lists[3];
                            electric_charge = exp_lists[4];
                            other_charge = exp_lists[5];
                            other_charge2 = exp_lists[6];
                            exp_type = exp_lists[7];

                            if(labour_charge == ""){ labour_charge = 0; }
                            if(packing_charge == ""){ packing_charge = 0; }
                            if(transport_charge == ""){ transport_charge = 0; }
                            if(electric_charge == ""){ electric_charge = 0; }
                            if(other_charge == ""){ other_charge = 0; }
                            if(other_charge2 == ""){ other_charge2 = 0; }
                            
                            if(exp_type == "ton"){
                                labour_charge = parseFloat(total_tons) * parseFloat(labour_charge);
                                packing_charge = parseFloat(total_tons) * parseFloat(packing_charge);
                                transport_charge = parseFloat(total_tons) * parseFloat(transport_charge);
                                electric_charge = parseFloat(total_tons) * parseFloat(electric_charge);
                                other_charge = parseFloat(total_tons) * parseFloat(other_charge);
                                other_charge2 = parseFloat(total_tons) * parseFloat(other_charge2);
                            }
                            else if(exp_type == "bag"){
                                no_of_bags_feed = document.getElementById("no_of_bags_feed").value;
                                if(no_of_bags_feed == ""){ no_of_bags_feed = 0; }
                                labour_charge = parseFloat(no_of_bags_feed) * parseFloat(labour_charge);
                                packing_charge = parseFloat(no_of_bags_feed) * parseFloat(packing_charge);
                                transport_charge = parseFloat(no_of_bags_feed) * parseFloat(transport_charge);
                                electric_charge = parseFloat(no_of_bags_feed) * parseFloat(electric_charge);
                                other_charge = parseFloat(no_of_bags_feed) * parseFloat(other_charge);
                                other_charge2 = parseFloat(no_of_bags_feed) * parseFloat(other_charge2);
                            }
                            else{ }
                            document.getElementById("labour_charge").value = parseFloat(labour_charge).toFixed(2);
                            document.getElementById("packing_charge").value = parseFloat(packing_charge).toFixed(2);
                            document.getElementById("transport_charge").value = parseFloat(transport_charge).toFixed(2);
                            document.getElementById("electric_charge").value = parseFloat(electric_charge).toFixed(2);
                            document.getElementById("other_charge").value = parseFloat(other_charge).toFixed(2);
                            document.getElementById("other_charge2").value = parseFloat(other_charge2).toFixed(2);

                            var produced_total = consumed_total = parseFloat(total_tons) * parseFloat(total_qty);
                            var total_cost = parseFloat(labour_charge) + parseFloat(packing_charge) + parseFloat(transport_charge) + parseFloat(electric_charge) + parseFloat(other_charge) + parseFloat(other_charge2);
                            produced_total = parseFloat(produced_total) - (parseFloat(produced_total) * (auto_Wastage_per / 100));
                            auto_wastage_kgs = (parseFloat(produced_total) * (auto_Wastage_per / 100));

                            document.getElementById("wastage_kg").value = parseFloat(auto_wastage_kgs).toFixed(2);
                            document.getElementById("wastage_per").value = parseFloat(auto_Wastage_per).toFixed(2);

                            document.getElementById("consumed_total").value = parseFloat(consumed_total).toFixed(2);
                            document.getElementById("produced_total").value = parseFloat(produced_total).toFixed(2);
                            document.getElementById("total_cost").value = parseFloat(total_cost).toFixed(2);
                        }
                    }
                }
                var trnum = document.getElementById("idvalue").value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_feedmill_items2.php?feed_mill="+feed_mill+"&feed_code="+feed_code+"&formula_code="+formula_code+"&total_tons="+total_tons+"&date="+date+"&trnum="+trnum;
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length != 0){
                            var idetails = item_list.split("@");
                            $('#row_body').append(idetails[0]); $('#final_values').append(idetails[2]); $('.itm_select2').select2();
                            document.getElementById("incr").value = idetails[3];
                            document.getElementById("input_cost").value = idetails[1];
                            if(idetails[1] > 0){
                                var tcost = document.getElementById("total_cost").value;
                                var final_cost = parseFloat(tcost) + parseFloat(idetails[1]);
                                document.getElementById("total_cost").value = final_cost.toFixed(2);

                                input_cost = document.getElementById("input_cost").value;
                                auto_wastage_kgs = document.getElementById("wastage_kg").value;
                                auto_wastage_amount = ((parseFloat(input_cost) / parseFloat(consumed_total)) * parseFloat(auto_wastage_kgs));
                                document.getElementById("wastage_cost").value = auto_wastage_amount.toFixed(2);

                                total_cost = document.getElementById("total_cost").value;
                                no_of_bags_feed = document.getElementById("no_of_bags_feed").value;
                                if(no_of_bags_feed == ""){ no_of_bags_feed = 0; var bag_cost = 0; }
                                else{
                                    var bag_cost = (parseFloat(total_cost) / parseFloat(no_of_bags_feed));
                                }
                                document.getElementById("bag_cost").value = bag_cost.toFixed(2);
                                produced_total = document.getElementById("produced_total").value;
                                var cpkg_cost = (parseFloat(total_cost) / parseFloat(produced_total));
                                document.getElementById("cpkg_cost").value = cpkg_cost.toFixed(2);

                                var margin_type = idetails[4];
                                var margin_per = idetails[5];
                                var margin_amount = 0;
                                if(margin_type.match("Per")){
                                    margin_amount = (((parseFloat(margin_per) / 100)) * (parseFloat(total_cost)));
                                }
                                else{
                                    margin_amount = (((parseFloat(margin_per) / 100)) * (parseFloat(total_cost)));
                                    //margin_amount = parseFloat(final_cost) + parseFloat(margin_per) ;
                                }
                                
                                document.getElementById("margin_type").value = margin_type;
                                document.getElementById("margin_per").value = parseFloat(margin_per).toFixed(2);
                                document.getElementById("margin_amount").value = parseFloat(margin_amount).toFixed(2);

                                var final_item_prod_amount = parseFloat(final_cost) + parseFloat(margin_amount);
                                var final_item_prod_price = parseFloat(final_item_prod_amount) / parseFloat(produced_total);

                                document.getElementById("final_item_prod_price").value = parseFloat(final_item_prod_price).toFixed(2);
                                document.getElementById("final_item_prod_amount").value = parseFloat(final_item_prod_amount).toFixed(2);
                            }
                        }
                        else{

                        }
                    }
                }
            }
            function calculate_total_cost(){
                var produced_total = parseFloat(document.getElementById("produced_total").value);
                var input_cost = document.getElementById("input_cost").value;
                var labour_charge = document.getElementById("labour_charge").value;
                var packing_charge = document.getElementById("packing_charge").value;
                var transport_charge = document.getElementById("transport_charge").value;
                var electric_charge = document.getElementById("electric_charge").value;
                var bag_amount = document.getElementById("bag_amount").value;
                var other_charge = document.getElementById("other_charge").value;
                var other_charge2 = document.getElementById("other_charge2").value;
                if(labour_charge == "" || labour_charge == "0.00"){ labour_charge = 0; }
                if(packing_charge == "" || packing_charge == "0.00"){ packing_charge = 0; }
                if(transport_charge == "" || transport_charge == "0.00"){ transport_charge = 0; }
                if(electric_charge == "" || electric_charge == "0.00"){ electric_charge = 0; }
                if(bag_amount == "" || bag_amount == "0.00"){ bag_amount = 0; }
                if(other_charge == "" || other_charge == "0.00"){ other_charge = 0; }
                if(other_charge2 == "" || other_charge2 == "0.00"){ other_charge2 = 0; }
                var total_cost = parseFloat(input_cost) + parseFloat(labour_charge) + parseFloat(packing_charge) + parseFloat(transport_charge) + parseFloat(electric_charge) + parseFloat(bag_amount) + parseFloat(other_charge) + parseFloat(other_charge2);
                document.getElementById("total_cost").value = total_cost.toFixed(2);

                total_cost = document.getElementById("total_cost").value;
                var margin_type = document.getElementById("margin_type").value;
                var margin_per = document.getElementById("margin_per").value;
                var margin_amount = 0;
                if(margin_type.match("Per")){
                    margin_amount = (((parseFloat(margin_per) / 100)) * (parseFloat(total_cost)));
                }
                else if(margin_type.match("Amt")){
                    margin_amount = parseFloat(total_cost) + parseFloat(margin_per);
                }
                else{
                    margin_amount = 0;
                }
                
                document.getElementById("margin_amount").value = parseFloat(margin_amount).toFixed(2);

                var final_item_prod_amount = parseFloat(total_cost) + parseFloat(margin_amount);
                var final_item_prod_price = parseFloat(final_item_prod_amount) / parseFloat(produced_total);
                document.getElementById("final_item_prod_price").value = parseFloat(final_item_prod_price).toFixed(2);
                document.getElementById("final_item_prod_amount").value = parseFloat(final_item_prod_amount).toFixed(2);

                calculate_wastage();
            }
            function calculate_margin_cost(){
                var margin_amount = document.getElementById("margin_amount").value;
                var total_cost = document.getElementById("total_cost").value;
                var margin_per = ((parseFloat(margin_amount) * 100) / parseFloat(total_cost));
                document.getElementById("margin_per").value =  parseFloat(margin_per).toFixed(2);
                //calculate_total_cost();
            }
            function fetch_feed_formula(){
                var feed_mill = document.getElementById("feed_mill").value;
                var feed_code = document.getElementById("feed_code").value;
                var feed_mill_all = "all";
				removeAllOptions(document.getElementById("formula_code"));
                myselect = document.getElementById("formula_code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(!feed_code.match("select") && feed_code.length > 0 && !feed_mill.match("select") && feed_mill.length > 0){
                    <?php
                    foreach($formula_code as $fcode){
                        $fmill = $formula_mill[$fcode];
                        $fitem = $formula_item[$fcode];
                        echo "if(feed_mill == '$fmill' && feed_code == '$fitem'){";
                    ?>
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $formula_name[$fcode]; ?>"); theOption1.value = "<?php echo $formula_code[$fcode]; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                        echo "}";
                        echo "else if(feed_mill_all == '$fmill' && feed_code == '$fitem'){";
                    ?>
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $formula_name[$fcode]; ?>"); theOption1.value = "<?php echo $formula_code[$fcode]; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                        echo "}";
                    }
                    ?>
                }
            }
            function fetch_feed_details(){
                var feed_mill = document.getElementById("feed_mill").value;
                var feed_mill_all = "all";
				removeAllOptions(document.getElementById("feed_code"));
                myselect = document.getElementById("feed_code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(!feed_mill.match("select") && feed_mill.length > 0){
                    <?php
                    foreach($formula_code as $fcode){
                        $fmill = $formula_mill[$fcode];
                        echo "if(feed_mill == '$fmill' || feed_mill_all == '$fmill'){";
                            $feeds[$formula_item[$fcode]] = $formula_item[$fcode];
                        echo "}";
                    }
                    foreach($feeds as $fcode){
                    ?>
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$fcode]; ?>"); theOption1.value = "<?php echo $item_code[$fcode]; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                    }
                    ?>
                }
            }
            function calculate_wastage(){
                var consumed_total = parseFloat(document.getElementById("consumed_total").value);
                var produced_total = parseFloat(document.getElementById("produced_total").value);
                var wastage_kg = (parseFloat(consumed_total) - parseFloat(produced_total));
                var total_cost = parseFloat(document.getElementById("total_cost").value);
                var input_cost = parseFloat(document.getElementById("input_cost").value);
                var avg_price = input_cost / consumed_total;
                var wastage_cost = wastage_kg * parseFloat(avg_price);
                var wastage_per = ((wastage_kg / consumed_total) * 100);
                document.getElementById("wastage_kg").value = wastage_kg.toFixed(2);
                document.getElementById("wastage_per").value = wastage_per.toFixed(2);
                document.getElementById("wastage_cost").value = wastage_cost.toFixed(2);
                
                var no_of_bags_feed = document.getElementById("no_of_bags_feed").value;
                if(no_of_bags_feed == "" || no_of_bags_feed == "0"){ no_of_bags_feed = 0; var bag_cost = 0; }
                else{
                    var bag_cost = (parseFloat(total_cost) / parseFloat(no_of_bags_feed));
                }
                
                document.getElementById("bag_cost").value = bag_cost.toFixed(2);

                var cpkg_cost = (parseFloat(total_cost) / parseFloat(produced_total));
                document.getElementById("cpkg_cost").value = cpkg_cost.toFixed(2);

            }
            function fetch_bag_count(){
                var date = document.getElementById("date").value;
                var feed_mill = document.getElementById("feed_mill").value;
                var bag_code = document.getElementById("bag_code_feed").value;
                var produced_kgs = parseFloat(document.getElementById("produced_total").value);
                if(!bag_code.match("select")){
                    var bag_size = total_bags = 0; var a1 = "";
                    <?php
                        foreach($bag_size_code as $bgc){
                            echo "if(bag_code =='$bgc'){";
                    ?>
                            bag_size = '<?php echo $bag_size_value[$bgc]; ?>';
                    <?php
                            echo "}";
                        }
                    ?>
                    if(bag_size == "" || bag_size == 0 || bag_size == "NaN"){ bag_size = 0; }
                    if(bag_size > 0){
                        total_bags = produced_kgs / bag_size;
                    }
                    else{
                        total_bags = 0;
                    }
                    document.getElementById("no_of_bags_feed").value = total_bags.toFixed(0);

                    var item_price = 0;
                    var fetch_avgprice = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_item_pricemaster.php?dates="+date+"&feedmills="+feed_mill+"&items="+bag_code;
                    //window.open(url);
                    var asynchronous = true;
                    fetch_avgprice.open(method, url, asynchronous);
                    fetch_avgprice.send();
                    fetch_avgprice.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            item_price = this.responseText;
                            if(item_price == "" || item_price == 0 || item_price == "NaN"){ item_price = 0; }
                            document.getElementById("price_of_bags_feed").value = parseFloat(item_price).toFixed(2);
                            if(parseFloat(item_price) > 0 && parseFloat(total_bags) > 0){
                                var bag_amount = parseFloat(total_bags.toFixed(0)) * parseFloat(item_price);
                                if(bag_amount == "" || bag_amount == 0 || bag_amount == "NaN"){ bag_amount = 0; }
                                //alert(parseFloat(total_bags.toFixed(0))+"---"+parseFloat(item_price));
                                document.getElementById("bag_amount").value = parseFloat(bag_amount).toFixed(2);
                                calculate_total_cost();
                            }
                            else{
                                document.getElementById("bag_amount").value = 0;
                                calculate_total_cost();
                            }
                        }
                    }
                }
                else{
                    document.getElementById("no_of_bags_feed").value = 0;
                    document.getElementById("price_of_bags_feed").value = 0;
                    document.getElementById("bag_amount").value = 0;
                    calculate_total_cost();
                }
            }
            function calculate_expenses_bagtype(){
                var feed_mill = document.getElementById("feed_mill").value;
                var feed_code = document.getElementById("feed_code").value;
                var formula_code = document.getElementById("formula_code").value;
                var total_tons = document.getElementById("total_tons").value;
                var no_of_bags_feed = document.getElementById("no_of_bags_feed").value; if(no_of_bags_feed == ""){ no_of_bags_feed = 0; }

                var price_of_bags_feed = document.getElementById("price_of_bags_feed").value; if(price_of_bags_feed == ""){ price_of_bags_feed = 0; }
                var bag_amount = parseFloat(no_of_bags_feed) * parseFloat(price_of_bags_feed);
                document.getElementById("bag_amount").value = parseFloat(bag_amount).toFixed(2);
                calculate_total_cost();
            }
            function fetch_item_avg_price(a){
                var feed_mill = document.getElementById("feed_mill").value;
                var date = document.getElementById("date").value;
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var items = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+feed_mill+"&item_code="+items+"&date="+date+"&row_count="+d+"&trtype=FeedProduction";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        var item_details = item_price.split("@");

                        if(parseFloat(item_details[0]) <= 0 || parseFloat(item_details[1]) <= 0){
                            document.getElementById("available_stock["+item_details[3]+"]").value = item_details[0];
                            document.getElementById("itm_prc["+item_details[3]+"]").value = item_details[1];
                        }
                        else{
                            document.getElementById("available_stock["+item_details[3]+"]").value = item_details[0];
                            document.getElementById("itm_prc["+item_details[3]+"]").value = item_details[1];
                        }
                        calculate_final_feed_amount();
                    }
                }
            }
            function calculate_final_feed_amount(){
                var incr = document.getElementById("incr").value;
                var price = quantity = item_amount = final_quantity = final_amount = 0;
                for(var i = 1;i <= incr;i++){
                    quantity = document.getElementById("itm_qtys["+i+"]").value;
                    price = document.getElementById("itm_prc["+i+"]").value;
                    if(quantity == ""){ quantity = 0; }
                    if(price == ""){ price = 0; }
                    item_amount = parseFloat(quantity) * parseFloat(price);
                    final_quantity += parseFloat(quantity);
                    final_amount += parseFloat(item_amount);
                    document.getElementById("itm_amt["+i+"]").value = item_amount.toFixed(2);
                }
                document.getElementById("consumed_total").value = final_quantity.toFixed(2);
                document.getElementById("final_total_qty").value = final_quantity.toFixed(2);
                document.getElementById("final_total_amt").value = final_amount.toFixed(2);
                document.getElementById("input_cost").value = final_amount.toFixed(2);
                calculate_total_cost(); calculate_wastage();
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>