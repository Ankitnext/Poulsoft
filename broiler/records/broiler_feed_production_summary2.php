<?php
//broiler_feed_production_summary2.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$feedmill_type_code = "";
$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
}
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code')  ".$sector_access_filter1." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
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

$date = date("Y-m-d"); $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $date = date("Y-m-d",strtotime($_POST['date']));

    $sectors = $_POST['sectors']; $sector_list = "";
    if($sectors == "all"){ foreach($sector_code as $scode){ if($sector_list == ""){ $sector_list = $scode; } else{ $sector_list = $sector_list."','".$scode; } } } else{ $sector_list = $sectors; }
    $sector_filter = " AND `feed_mill` IN ('$sector_list')";

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/FeedFormulaReport-Excel.php?sectors=".$sectors."&fdate=".$fdate."&tdate=".$tdate;
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
                    <th colspan="48" align="center"><?php echo $row['cdetails']; ?><h5>Feed Production summary Report 2</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_feed_production_summary2.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="50">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Feed Mill</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($sectors == $prod_code){ echo "selected"; } ?>><?php echo $sector_name[$prod_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Production Date</label>
                                    <input type="text" name="date" id="date" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($date)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
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
                
                $sql = "SELECT * FROM `broiler_feed_consumed` WHERE `date` = '$date'".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`link_trnum` ASC";
                $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); $total_quantity = $formula_array = $total_amount = $feed_array = $item_array = $quantity = $amount = $key_indexes = array();
                if($ccount > 0){
                    $old_inv = "";
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['formula_code']."@".$row['feed_code'];
                        $key2 = $row['formula_code']."@".$row['feed_code']."@".$row['item_code'];
                        $formula_array[$key] = $row['formula_code'];
                        $feed_array[$key] = $row['feed_code'];
                        $item_array[$row['item_code']] = $row['item_code'];
                        $quantity[$key2] += $row['quantity'];
                        $amount[$key2] += $row['amount'];
                        $keys[$key] = $key;
                        $keys2[$key2] = $key2;
                        if($old_inv != $row['link_trnum']){
                            $old_inv = $row['link_trnum'];
                            $total_quantity[$key] += $row['total_quantity'];
                            $total_amount[$key] += $row['total_amount'];
                        }
                    }
                }
                $sql = "SELECT SUM(labour_cost) as labour_cost,SUM(packing_cost) as packing_cost,SUM(electricity_cost) as electricity_cost,SUM(transport_cost) as transport_cost,SUM(other_cost) as other_cost,SUM(bag_amount) as bag_amount,SUM(produced_quantity) as produced_quantity,SUM(produced_amount) as produced_amount,SUM(margin_amount) as margin_amount,SUM(total_tons) as total_tons,feed_code,formula_code FROM `broiler_feed_production` WHERE `date` = '$date'".$sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `formula_code`,`feed_code` ORDER BY `feed_code` ASC";
                $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query); $labour_cost = $packing_cost = $electricity_cost = $transport_cost = $other_cost = $bag_amount = $produced_quantity = $produced_amount = array();
                if($ccount > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['formula_code']."@".$row['feed_code'];
                        $total_tons[$row['feed_code']] = $row['total_tons'];
                        $labour_cost[$key] = $row['labour_cost'];
                        $packing_cost[$key] = $row['packing_cost'];
                        $electricity_cost[$key] = $row['electricity_cost'];
                        $transport_cost[$key] = $row['transport_cost'];
                        $other_cost[$key] = $row['other_cost'];
                        $bag_amount[$key] = $row['bag_amount'];
                        $produced_quantity[$key] = $row['produced_quantity'];
                        $produced_amount[$key] = $row['produced_amount'];
                        $margin_amount[$key] = $row['margin_amount'];
                        $total_expenses[$key] = (float)$labour_cost[$key] + (float)$packing_cost[$key] + (float)$electricity_cost[$key] + (float)$transport_cost[$key] + (float)$other_cost[$key] + (float)$bag_amount[$key]; // + (float)$margin_amount[$key];
                    }
                }
                $formula_list = implode("','",$formula_array);
                $sql = "SELECT * FROM `broiler_feed_formula` WHERE `code` IN ('$formula_list') AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $fcount = mysqli_num_rows($query);
                if($fcount > 0){ while($row = mysqli_fetch_assoc($query)){ $formula_name[$row['code']] = $row['description']; } }
                //$item_list = implode("','",$item_array); $item_list .= implode("','",$feed_array);
                $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $icount = mysqli_num_rows($query);
                if($icount > 0){ while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; } }
            ?>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Feed Formula</th>
                    <?php $td_count = 0; foreach($keys as $key){ $formula_code = $formula_array[$key]; echo "<th colspan='1'>".$formula_name[$formula_code]."</th>"; $td_count = $td_count + 3; } ?>
                </tr>
                <tr align="center">
                    <th>Feed</th>
                    <?php foreach($keys as $key){ $feed_code = $feed_array[$key]; echo "<th colspan='1'>".$item_name[$feed_code]."</th>"; } ?>
                </tr>
                <tr align="center">
                    <th>No. of Batches/Tons</th>
                    <?php foreach($keys as $key){ $feed_code = $feed_array[$key]; echo "<th colspan='1'>".number_format_ind($total_tons[$feed_code])."</th>"; } ?>
                </tr>
                <tr align="center">
                    <th>Item</th>
                    <?php foreach($keys as $key){ echo "<th colspan='1'>Item Consumed</th>"; } ?>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                foreach($item_array as $id2){
                ?>
                <tr align="left">
                    <th><?php echo $item_name[$id2]; ?></th>
                <?php
                    foreach($keys as $key){
                        $key3 = $key."@".$id2;
                        $price3 = 0; if((float)$quantity[$key3] != 0){ $price3 = round(((float)$amount[$key3] / (float)$quantity[$key3]),2); }
                        
                        echo "<td style='text-align:right;'>".number_format_ind($quantity[$key3])."</td>";
                         
                    }
                ?>
                </tr>
                <?php
                }
                ?>
            </tbody>
            <thead class="thead4">
                <tr align="center">
                    <th>Total</th>
                    <?php
                    foreach($keys as $key){
                        $avg_prc = 0; if((float)$total_quantity[$key] != 0){ $avg_prc = round(((float)$total_amount[$key] / (float)$total_quantity[$key]),2); }
                        echo "<th colspan='1' style='text-align:right;'>".number_format_ind($total_quantity[$key])."</th>";
                        // echo "<th style='text-align:right;'>".number_format_ind($avg_prc)."</th>";
                        // echo "<th style='text-align:right;'>".number_format_ind($total_amount[$key])."</th>";
                    }
                    ?>
                </tr>
               
            </thead>
        <?php
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