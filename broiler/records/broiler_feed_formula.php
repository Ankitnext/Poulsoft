<?php
//broiler_feed_formula.php
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
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code')  ".$sector_access_filter1."  AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_feed_formula` GROUP BY `description` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $formula_code[$row['code']] = $row['code']; $formula_name[$row['code']] = $row['description']; $formula_item[$row['code']] = $row['formula_item_code']; $formula_item_list[$row['item_code']] = $row['item_code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$items = $formulas = $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $sectors = $_POST['sectors'];
    $formulas = $_POST['formulas'];
    $items = $_POST['items'];

    if($sectors == "all"){ $mill_filter = ""; } else{ $mill_filter = " AND `mill_code` IN ('$sectors')"; }
    if($formulas == "all"){ $formula_filter = ""; } else{ $formula_filter = " AND `code` IN ('$formulas')"; }
    if($items == "all"){ $item_filter = ""; } else{ $item_filter = " AND `formula_item_code` IN ('$items')"; }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/FeedFormulaReport-Excel.php?sectors=".$sectors."&formulas=".$formulas."&items=".$items;
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
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Feed Formula Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_feed_formula.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Feed Mill</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($sectors == $prod_code){ echo "selected"; } ?>><?php echo $sector_name[$prod_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Formula</label>
                                    <select name="formulas" id="formulas" class="form-control select2">
                                        <option value="all" <?php if($formulas == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($formula_code as $fcode){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($formulas == $fcode){ echo "selected"; } ?>><?php echo $formula_name[$fcode]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Item</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($formula_item_list as $fcode){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($items == $fcode){ echo "selected"; } ?>><?php echo $item_name[$fcode]; ?></option>
                                        <?php } ?>
                                    </select>
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
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Feed Mill</th>
                    <th>Formula</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $iprice_type = "AvgPrc";
                //$iprice_type = "LatestPrc";
                $sql_record = "SELECT * FROM `broiler_feed_formula` WHERE `active` = '1'".$mill_filter."".$formula_filter."".$item_filter." AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql_record); $item_arr_list = $item_cats = $icat_iac = array();
                while($row = mysqli_fetch_assoc($query)){
                    $item_arr_list[$row['item_code']] = $row['item_code'];
                }
                if($sectors == "all"){ $mill_list = implode("','",$sector_code); } else{ $mill_list = $sectors; }
                $item_list = implode("','",$item_arr_list);

                if($iprice_type == "AvgPrc"){
                    $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$item_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $item_cats[$row['code']] = $row['category']; }

                    $icat_list = implode("','",$item_cats);
                    $sql = "SELECT * FROM `item_category` WHERE `code` IN ('$icat_list')"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['iac']] = $row['iac']; }
                    
                    $icoa_list = implode("','",$icat_iac);
                    $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$icoa_list') AND `item_code` IN ('$item_list') AND `location` IN ('$mill_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                    $query = mysqli_query($conn,$sql); $c = 0; $current_stock = $item_price = $current_amount = array();
                    while($row = mysqli_fetch_assoc($query)){
                        if($row['crdr'] == "CR"){
                            $current_stock[$row['item_code']] = $current_stock[$row['item_code']] - (float)$row['quantity'];
                            $current_amount[$row['item_code']] = $current_amount[$row['item_code']] - ($item_price[$row['item_code']] * (float)$row['quantity']);
                        }
                        else if($row['crdr'] == "DR"){
                            $current_stock[$row['item_code']] = $current_stock[$row['item_code']] + (float)$row['quantity'];
                            $current_amount[$row['item_code']] = $current_amount[$row['item_code']] + (float)$row['amount'];
                            if($current_stock[$row['item_code']] != 0){
                                $item_price[$row['item_code']] = round(($current_amount[$row['item_code']] / $current_stock[$row['item_code']]),2);
                            }
                            else{
                                $item_price[$row['item_code']] = 0;
                            }
                        }
                        else{ }
                        if(strtotime($row['date']) <= strtotime($actual_date)){ $opening_stk_qty = $current_stock[$row['item_code']]; $opening_stk_amt = $current_amount[$row['item_code']]; }
                        if(number_format_ind($current_stock[$row['item_code']]) == "0.00"){ $current_stock[$row['item_code']] = $item_price[$row['item_code']] = $current_amount[$row['item_code']] = 0; }
                    }
                }
                else if($iprice_type == "LatestPrc"){
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` IN ('$item_list') AND `warehouse` IN ('$mill_list') AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `broiler_purchases` WHERE `icode` IN ('$item_list') AND `warehouse` IN ('$mill_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `icode` ORDER BY `icode` ASC)";
                    $query = mysqli_query($conn,$sql); $item_parr_list = $item_price = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $item_parr_list[$row['icode']] = $row['icode'];
                        $item_price[$row['icode']] = $row['rate'];
                    }

                    $item_plist = implode("','",$item_parr_list);
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$item_list') AND `code` NOT IN ('$item_plist') AND `towarehouse` IN ('$mill_list') AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `item_stocktransfers` WHERE `code` IN ('$item_list') AND `code` NOT IN ('$item_plist') AND `towarehouse` IN ('$mill_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `code` ORDER BY `code` ASC)";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $item_price[$row['icode']] = $row['rate'];
                    }
                }

                $sql_record = "SELECT * FROM `broiler_feed_formula` WHERE `active` = '1'".$mill_filter."".$formula_filter."".$item_filter." AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                    if((float)$item_price != 0){ $iprc = round($item_price[$row['item_code']],2); } else{ $iprc = 0; }
                    $iamt = round(((float)$row['item_qty'] * (float)$iprc),2);
                ?>
                <tr>
                    <td title="Date"><?php echo $sector_name[$row['mill_code']]; ?></td>
                    <td title="Invoice"><?php echo $row['description']; ?></td>
                    <td title="Item"><?php echo $item_name[$row['item_code']]; ?></td>
                    <td title="Birds" style="text-align:right;"><?php echo number_format_ind($row['item_qty']); ?></td>
                    <td title="Birds" style="text-align:right;"><?php echo number_format_ind($iprc); ?></td>
                    <td title="Birds" style="text-align:right;"><?php echo number_format_ind($iamt); ?></td>
                </tr>
                <?php
                    $tot_qty = $tot_qty + $row['item_qty'];
                    $tot_amt = $tot_amt + (float)$iamt;
                }
                if((float)$tot_qty != 0){ $avg_prc = round(((float)$tot_amt / (float)$tot_qty),2); } else{ $avg_prc = 0; }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="3" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_qty,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($avg_prc,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
            </tr>
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