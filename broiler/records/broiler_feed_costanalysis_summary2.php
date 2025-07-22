<?php
//broiler_feed_costanalysis_summary2.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";

$feedmill_type_code = "";
$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
}
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
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
}
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $cat_list = "";
while($row = mysqli_fetch_assoc($query)){ if($cat_list == ""){ $cat_list = $row['code']; } else{ $cat_list = $cat_list."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$cat_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_array_code[$row['code']] = $row['code']; $feed_array_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $sectors = $feeds = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    //$date = date("Y-m-d",strtotime($_POST['date']));
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $feeds = $_POST['feeds']; $feed_list = ""; $feed_list = "all";
    if($feeds == "all"){ foreach($feed_array_code as $scode){ if($feed_list == ""){ $feed_list = $scode; } else{ $feed_list = $feed_list."','".$scode; } } } else{ $feed_list = "','".$feeds; }
    $feed_filter1 = " AND `formula_item_code` IN ('$feed_list')";
    $feed_filter2 = " AND `feed_type` IN ('$feed_list')";

    $sectors = $_POST['sectors']; $sector_list = ""; $sector_list = "all";
    if($sectors == "all"){ foreach($sector_code as $scode){ if($sector_list == ""){ $sector_list = $scode; } else{ $sector_list = $sector_list."','".$scode; } } } else{ $sector_list = "all','".$sectors; }
    $sector_filter = " AND `mill_code` IN ('$sector_list')";

	//$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/FeedFormulaReport-Excel.php?sectors=".$sectors."&fdate=".$fdate."&tdate=".$tdate;

    $sql = "SELECT * FROM `broiler_feed_formula` WHERE `active` = '1' AND `dflag` = '0'".$feed_filter1." GROUP BY `code` ORDER BY `id` ASC";
    $query = mysqli_query($conn,$sql); $fcount = mysqli_num_rows($query); $fcount = $fcount * 2;
}
else{
    $fcount = 96;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead2_empty_row { display:none; }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="<?php echo $fcount; ?>" align="center"><?php echo $row['cdetails']; ?><h5>Formula Costing Analysis Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_feed_costanalysis_summary2.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="<?php echo $fcount + 2; ?>">
                            <div class="row">
                            <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Item</label>
                                    <select name="feeds" id="feeds" class="form-control select2">
                                        <option value="all" <?php if($feeds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($feed_array_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($feeds == $fcode){ echo "selected"; } ?>><?php echo $feed_array_name[$fcode]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Mill</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($sectors == $prod_code){ echo "selected"; } ?>><?php echo $sector_name[$prod_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            if(isset($_POST['submit_report']) == true){
                $sql = "SELECT * FROM `broiler_feed_formula` WHERE `active` = '1' AND `dflag` = '0'".$feed_filter1." GROUP BY `code` ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); $i = 0; $ff_list = $ffi_list = "";
                while($row = mysqli_fetch_assoc($query)){
                    if($ff_list == ""){ $ff_list = $row['code']; } else{ $ff_list = $ff_list."','".$row['code']; }
                    if($ffi_list == ""){ $ffi_list = $row['code']; } else{ $ffi_list = $ffi_list."','".$row['formula_item_code']; }
                }
                $sql = "SELECT * FROM `broiler_feed_formula` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `code` IN ('$ff_list')".$sector_filter."".$feed_filter1." AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $item_array[$row['item_code']] = $row['item_code'];
                    $formula_name[$row['code']] = $row['description'];

                    if($row['mill_code'] == "all"){
                        foreach($sector_code as $scode){
                            if($sectors == "all" || $sectors == $scode){
                                $key = $row['code']."@".$row['formula_item_code']."@".$scode;
                                $key2 = $row['code']."@".$row['formula_item_code']."@".$scode."@".$row['item_code'];
                                $total_quantity[$key] = $row['total_qty'];
                                $formula_array[$key] = $row['code'];
                                $feed_array[$key] = $row['formula_item_code'];
                                $quantity[$key2] = $row['item_qty'];
                                $keys[$key] = $key;


                                $mill_key = $row['formula_item_code']."@".$scode;
                                if(empty($exp_key[$mill_key])){ $exp_key[$mill_key] = $key; } else if(str_contains($exp_key[$mill_key], $key)){ } else{ $exp_key[$mill_key] = $exp_key[$mill_key]."&".$key;}
                                $icode = ""; $icode = $row['item_code']; $lrate1 = 0;

                                $sql2 = "SELECT * FROM `broiler_purchases` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `icode` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_purchases` WHERE `icode` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                                $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['rate']; $ldate1 = $row2['date']; }

                                if((float)$lrate1 == 0){
                                    $sql2 = "SELECT * FROM `item_stocktransfers` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `code` = '$icode' AND `towarehouse` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `item_stocktransfers` WHERE `code` = '$icode' AND `towarehouse` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                                    $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['price']; $ldate1 = $row2['date']; }
                                }
                                if((float)$lrate1 == 0){
                                    $sql2 = "SELECT * FROM `broiler_feed_production` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `feed_code` = '$icode' AND `feed_mill` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_feed_production` WHERE `feed_code` = '$icode' AND `feed_mill` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                                    $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['produced_price']; $ldate1 = $row2['date']; }
                                }
                                if((float)$lrate1 == 0){
                                    $sql2 = "SELECT * FROM `broiler_openings` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `type` = 'item' AND `type_code` = '$icode' AND `sector_code` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_openings` WHERE `type` = 'item' AND `type_code` = '$icode' AND `sector_code` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                                    $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['rate']; $ldate1 = $row2['date']; }
                                }
                                if((float)$lrate1 == 0){
                                    $sql2 = "SELECT * FROM `broiler_pc_goodsreceipt` WHERE `item_code` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `broiler_pc_goodsreceipt` WHERE `item_code` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                                    $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['rate']; $ldate1 = $row2['date']; }
                                    //if($icode == "IN-0001"){ echo "<br/>".$sql2; }
                                }

                                $amount[$key2] = (float)$quantity[$key2] * (float)$lrate1;
                                if(empty($price[$icode])){ $price[$icode] = (float)$lrate1; }
                            }
                        }
                    }
                    else{
                        
                        $key = $row['code']."@".$row['formula_item_code']."@".$row['mill_code'];
                        $key2 = $row['code']."@".$row['formula_item_code']."@".$row['mill_code']."@".$row['item_code'];
                        $total_quantity[$key] = $row['total_qty'];
                        $formula_array[$key] = $row['code'];
                        $feed_array[$key] = $row['formula_item_code'];
                        $quantity[$key2] = $row['item_qty'];
                        $keys[$key] = $key;

                        $mill_key = $row['formula_item_code']."@".$row['mill_code'];
                        if(empty($exp_key[$mill_key])){ $exp_key[$mill_key] = $key; } else if(str_contains($exp_key[$mill_key], $key)){ } else{ $exp_key[$mill_key] = $exp_key[$mill_key]."&".$key;}
                        $icode = $scode = ""; $icode = $row['item_code']; $scode = $row['mill_code']; $lrate1 = 0;

                        $sql2 = "SELECT * FROM `broiler_purchases` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `icode` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_purchases` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `icode` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                        $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['rate']; $ldate1 = $row2['date']; }
                        
                        if((float)$lrate1 == 0){
                            $sql2 = "SELECT * FROM `item_stocktransfers` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `code` = '$icode' AND `towarehouse` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `item_stocktransfers` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `code` = '$icode' AND `towarehouse` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                            $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['price']; $ldate1 = $row2['date']; }
                        }
                        if((float)$lrate1 == 0){
                            $sql2 = "SELECT * FROM `broiler_feed_production` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `feed_code` = '$icode' AND `feed_mill` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_feed_production` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `feed_code` = '$icode' AND `feed_mill` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                            $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['produced_price']; $ldate1 = $row2['date']; }
                        }
                        if((float)$lrate1 == 0){
                            $sql2 = "SELECT * FROM `broiler_openings` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `type` = 'item' AND `type_code` = '$icode' AND `sector_code` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_openings` WHERE `date`>= '$fdate' AND `date`<='$tdate' AND `type` = 'item' AND `type_code` = '$icode' AND `sector_code` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                            $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['rate']; $ldate1 = $row2['date']; }
                        }
                        if((float)$lrate1 == 0){
                            $sql2 = "SELECT * FROM `broiler_pc_goodsreceipt` WHERE `item_code` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `broiler_pc_goodsreceipt` WHERE `item_code` = '$icode' AND `warehouse` = '$scode' AND `active` = '1' AND `dflag` = '0')";
                            $query2 = mysqli_query($conn,$sql2); while($row2 = mysqli_fetch_array($query2)){ $lrate1 = $row2['rate']; $ldate1 = $row2['date']; }
                            //if($icode == "IN-0001"){ echo "<br/>".$sql2; }
                        }

                        $amount[$key2] = (float)$quantity[$key2] * (float)$lrate1;
                        if(empty($price[$icode])){ $price[$icode] = (float)$lrate1; }

                    }
                }
                
                $sql = "SELECT * FROM `broiler_feed_expense` WHERE `date`<='$tdate' AND `active` = '1'".$sector_filter."".$feed_filter2." AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) FROM `broiler_feed_expense` WHERE `date`<='$tdate' AND `active` = '1'".$sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `mill_code`,`feed_type` ORDER BY id ASC)"; //`feed_type` IN ('$ffi_list')
                $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); $labour_cost = $packing_cost = $electricity_cost = $transport_cost = $other_cost = $bag_amount = $produced_quantity = $produced_amount = array();
                if($ccount > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $mill_key = $row['feed_type']."@".$row['mill_code'];
                        $k1 = array();
                        $k1 = explode("&",$exp_key[$mill_key]);
                        foreach($k1 as $key){
                            $labour_cost[$key] = $row['labour_charge'];
                            $packing_cost[$key] = $row['packing_charge'];
                            $electricity_cost[$key] = $row['electric_charge'];
                            $transport_cost[$key] = $row['transport_charge'];
                            $other_cost[$key] = $row['other_charge'];
                            //echo "<br/>".$mill_key."-->".$key."-->".$labour_cost[$key]."-".$packing_cost[$key]."-".$electricity_cost[$key]."-".$transport_cost[$key]."-".$other_cost[$key];
                            $total_expenses[$key] = (float)$labour_cost[$key] + (float)$packing_cost[$key] + (float)$electricity_cost[$key] + (float)$transport_cost[$key] + (float)$other_cost[$key];
                        }
                    }
                }
            ?>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Feed Formula</th>
                    <th rowspan="3">Item Price</th>
                    <?php $td_count = 0; foreach($keys as $key){ $formula_code = $formula_array[$key]; echo "<th colspan='2'>".$formula_name[$formula_code]."</th>"; $td_count = $td_count + 2; } ?>
                </tr>
                <tr align="center">
                    <th>Feed</th>
                    <?php foreach($keys as $key){ $feed_code = $feed_array[$key]; echo "<th colspan='2'>".$item_name[$feed_code]."</th>"; } ?>
                </tr>
                <tr align="center">
                    <th>Item</th>
                    <?php foreach($keys as $key){ echo "<th>Item Consumed</th><th>Total Cost</th>"; } ?>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                if($item_array != NULL){
                $ilist = ""; $ilist = implode("','",$item_array); $iclist = $item_cats = $item_codes = $icat_code = array();
                $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$ilist') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $iclist[$row['category']] = $row['category']; $item_cats[$row['code']] = $row['category']; $item_codes[$row['code']] = $row['code']; }

                $ilist = ""; $ilist = implode("','",$iclist);
                $sql = "SELECT * FROM `item_category` WHERE `code` IN ('$ilist') AND `dflag` = '0' ORDER BY `sort_order` ASC,`description` ASC"; $query = mysqli_query($conn,$sql); $cat_list = "";
                while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

                $isize = (sizeof($item_array) * 2);
                foreach($icat_code as $ic1){
                    $tot_iqty = $tot_iamt = array();
                    foreach($item_codes as $id2){
                        if(!empty($item_cats[$id2]) && $item_cats[$id2] == $ic1){
                        ?>
                        <tr align="left">
                            <th><?php echo $item_name[$id2]; ?></th>
                        <?php
                            $i = 0;
                            foreach($keys as $key){
                                $i++;
                                $key3 = $key."@".$id2;
                                if($i == 1){
                                    echo "<td style='text-align:right;'>".number_format_ind($price[$id2])."</td>";
                                }
                                echo "<td style='text-align:right;'>".number_format_ind($quantity[$key3])."</td>";
                                echo "<td style='text-align:right;'>".number_format_ind($amount[$key3])."</td>";
                                $produced_quantity[$key] += (float)$quantity[$key3];
                                $total_amount[$key] += (float)$amount[$key3];
                                $tot_iqty[$key] += (float)$quantity[$key3];
                                $tot_iamt[$key] += (float)$amount[$key3];
                            }
                        ?>
                        </tr>
                        <?php
                        }
                    }
                    echo "<tr class='thead4'>";
                    echo "<th style='text-align:center;'>Total</th>";
                    echo "<th></th>";
                    foreach($keys as $key){
                        echo "<th style='text-align:right;'>".number_format_ind($tot_iqty[$key])."</th>";
                        echo "<th style='text-align:right;'>".number_format_ind($tot_iamt[$key])."</th>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
            <thead class="thead3">
                <tr align="center">
                    <th>Final Total</th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th style='text-align:right;'>".number_format_ind($total_quantity[$key])."</th>"; echo "<th style='text-align:right;'>".number_format_ind($total_amount[$key])."</th>"; } ?>
                </tr>
                <tr align="center">
                    <th>Feed Cost per Kg</th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th colspan='2' style='color:green;text-align:right;'>".number_format_ind(round(($total_amount[$key] / $total_quantity[$key]),2))."</th>"; } ?>
                </tr>
                <tr align="center">
                    <th>Expenses</th>
                    <th></th>
                    <th colspan="<?php echo $td_count; ?>"></th>
                </tr>
            </thead>
            <thead>
                <tr align="left">
                    <th><?php echo $desc_array['labourcharge']; ?></th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='text-align:right;'>".number_format_ind(round(($labour_cost[$key]),2))."</th>"; } ?>
                </tr>
                <tr align="left">
                    <th><?php echo $desc_array['packingcharge']; ?></th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='text-align:right;'>".number_format_ind(round(($packing_cost[$key]),2))."</th>"; } ?>
                </tr>
                <tr align="left">
                    <th><?php echo $desc_array['electricalcharge']; ?></th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='text-align:right;'>".number_format_ind(round(($electricity_cost[$key]),2))."</th>"; } ?>
                </tr>
                <tr align="left">
                    <th><?php echo $desc_array['transportcharge']; ?></th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='text-align:right;'>".number_format_ind(round(($transport_cost[$key]),2))."</th>"; } ?>
                </tr>
                <tr align="left">
                    <th>Bag Cost</th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='text-align:right;'>".number_format_ind(round(($bag_amount[$key]),2))."</th>"; } ?>
                </tr>
                <tr align="left">
                    <th><?php echo $desc_array['othercharge']; ?></th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='text-align:right;'>".number_format_ind(round(($other_cost[$key]),2))."</th>"; } ?>
                </tr>
            </thead>
            <thead class="thead4">
                <tr align="center">
                    <th>Total Expenses</th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='color:red;text-align:right;'>".number_format_ind(round(($total_expenses[$key]),2))."</th>"; } ?>
                </tr>
            </thead>
            <thead class="thead4">
                <tr align="center">
                    <th>Net Production Cost/Kg</th>
                    <th></th>
                    <?php foreach($keys as $key){ echo "<th></th><th style='color:green;text-align:right;'>".number_format_ind(round((($total_amount[$key] + $total_expenses[$key]) / $produced_quantity[$key]),2))."</th>"; } ?>
                </tr>
            </thead>
        <?php
                }
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>