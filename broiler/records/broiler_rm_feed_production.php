<?php
//broiler_rm_feed_production.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

$file_name = "Feed Production Report";

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
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') ".$sector_access_filter1." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_feed_formula` GROUP BY `code` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $formula_code[$row['code']] = $row['code']; $formula_name[$row['code']] = $row['description']; }

$sql = "SELECT DISTINCT(item_code) as item_code FROM `broiler_feed_consumed` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $mi_codes = "";
while($row = mysqli_fetch_assoc($query)){ if($mi_codes == ""){ $mi_codes = $row['item_code']; } else{ $mi_codes = $mi_codes."','".$row['item_code']; } }

$sql = "SELECT * FROM `item_details` WHERE `code` IN ('$mi_codes') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $mi_cat = "";
while($row = mysqli_fetch_assoc($query)){
    $mill_item_code[$row['code']] = $row['code']; $mill_item_name[$row['code']] = $row['description']; $mill_item_category[$row['code']] = $row['category'];
    if($mi_cat == ""){ $mi_cat = $row['category']; } else{ $mi_cat = $mi_cat."','".$row['category']; }
}
$sql = "SELECT * FROM `item_category` WHERE `code` IN ('$mi_cat') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mill_item_cat_code[$row['code']] = $row['code']; $mill_item_cat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $sectors = $item_categories = $items = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $sectors = $_POST['sectors'];
    $item_categories = $_POST['item_categories'];
    $items = $_POST['items'];
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sector_list = "";
    if($sectors == "all"){
        foreach($sector_code as $prod_code){ if($sector_list == ""){ $sector_list = $prod_code; } else{ $sector_list = $sector_list."','".$prod_code; } }
        $inv_mill_filter = " AND `warehouse` IN ('$sector_list')";
        $inv_mill_filter1 = " AND `sector_code` IN ('$sector_list')";
        $str_in_mill_filter = " AND `towarehouse` IN ('$sector_list')";
        $str_out_mill_filter = " AND `fromwarehouse` IN ('$sector_list')";
        $consumed_mill_filter = " AND `feed_mill` IN ('$sector_list')";
    }
    else{
        $inv_mill_filter = " AND `warehouse` IN ('$sectors')";
        $inv_mill_filter1 = " AND `sector_code` IN ('$sectors')";
        $str_in_mill_filter = " AND `towarehouse` IN ('$sectors')";
        $str_out_mill_filter = " AND `fromwarehouse` IN ('$sectors')";
        $consumed_mill_filter = " AND `feed_mill` IN ('$sectors')";
    }

    $item_list = "";
    if($items != "all"){
        $item_list = $items;
    }
    else if($item_categories != "all"){
        foreach($mill_item_code as $micode){
            if($item_categories == $mill_item_category[$micode]){
                if($item_list == ""){ $item_list = $micode; }
                else{$item_list = $item_list."','".$micode; }
            }
        }
    }
    else{
        foreach($mill_item_code as $micode){
            if($item_list == ""){ $item_list = $micode; }
            else{$item_list = $item_list."','".$micode; }
        }
    }
    if($item_list == ""){
        $pur_item_filter = "";
        $pur_item_filter1 = "";
        $str_item_filter = "";
        $consumed_item_filter = "";
    }
    else{
        $pur_item_filter = " AND `icode` IN ('$item_list')";
        $pur_item_filter1 = " AND `type_code` IN ('$item_list')";
        $str_item_filter = " AND `code` IN ('$item_list')";
        $consumed_item_filter = " AND `item_code` IN ('$item_list')";
    }
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
        <table id="main_table" class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="48" align="center"><?php echo $row['cdetails']; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_rm_feed_production.php" method="post">
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
                                    <label>Item Category</label>
                                    <select name="item_categories" id="item_categories" class="form-control select2" onchange="fetch_item_list()">
                                        <option value="all" <?php if($item_categories == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($mill_item_cat_code as $mic_code){ ?><option value="<?php echo $mic_code; ?>" <?php if($item_categories == $mic_code){ echo "selected"; } ?>><?php echo $mill_item_cat_name[$mic_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Description</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($mill_item_code as $mi_codes){ ?><option value="<?php echo $mi_codes; ?>" <?php if($items == $mi_codes){ echo "selected"; } ?>><?php echo $mill_item_name[$mi_codes]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel('main_table','<?php echo $file_name; ?>');">
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
                $opn_pur_feed_item_qty = $opn_pur_feed_item_amt = $opn_only_qty = $opn_only_amt = $opn_trin_feed_item_qty = $opn_trin_feed_item_amt = $formula_items = $feed_items = array();
                $sql_record = "SELECT SUM(quantity) as rcd_qty,SUM(amount) as amount,type_code as itm_code FROM `broiler_openings` WHERE `date` < '$fdate'".$pur_item_filter1."".$inv_mill_filter1." AND `active` = '1' AND `dflag` = '0' GROUP BY `type_code` ORDER BY `type_code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($opn_only_qty[$row['itm_code']])){
                        $opn_only_qty[$row['itm_code']] = $opn_only_qty[$row['itm_code']] + $row['rcd_qty'];
                        $opn_only_amt[$row['itm_code']] = $opn_only_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $opn_only_qty[$row['itm_code']] = $row['rcd_qty'];
                        $opn_only_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                 $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as amount,icode as itm_code FROM `broiler_purchases` WHERE `date` < '$fdate'".$pur_item_filter."".$inv_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `icode` ORDER BY `icode` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($opn_pur_feed_item_qty[$row['itm_code']])){
                        $opn_pur_feed_item_qty[$row['itm_code']] = $opn_pur_feed_item_qty[$row['itm_code']] + ($row['rcd_qty'] + $row['fre_qty']);
                        $opn_pur_feed_item_amt[$row['itm_code']] = $opn_pur_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $opn_pur_feed_item_qty[$row['itm_code']] = ($row['rcd_qty'] + $row['fre_qty']);
                        $opn_pur_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as itm_code FROM `item_stocktransfers` WHERE `date` < '$fdate'".$str_item_filter."".$str_in_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `code` ORDER BY `code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($opn_trin_feed_item_qty[$row['itm_code']])){
                        $opn_trin_feed_item_qty[$row['itm_code']] = $opn_trin_feed_item_qty[$row['itm_code']] + ($row['quantity']);
                        $opn_trin_feed_item_amt[$row['itm_code']] = $opn_trin_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $opn_trin_feed_item_qty[$row['itm_code']] = ($row['quantity']);
                        $opn_trin_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $opn_consumed_feed_item_qty = $opn_consumed_feed_item_amt = array();
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as itm_code FROM `broiler_feed_consumed` WHERE `date` < '$fdate'".$consumed_item_filter."".$consumed_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `item_code` ORDER BY `item_code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($opn_consumed_feed_item_qty[$row['itm_code']])){
                        $opn_consumed_feed_item_qty[$row['itm_code']] = $opn_consumed_feed_item_qty[$row['itm_code']] + ($row['quantity']);
                        $opn_consumed_feed_item_amt[$row['itm_code']] = $opn_consumed_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $opn_consumed_feed_item_qty[$row['itm_code']] = ($row['quantity']);
                        $opn_consumed_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $opn_sale_feed_item_qty = $opn_sale_feed_item_amt = $opn_trout_feed_item_qty = $opn_trout_feed_item_amt = array();
                $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as amount,icode as itm_code FROM `broiler_sales` WHERE `date` < '$fdate'".$pur_item_filter."".$inv_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `icode` ORDER BY `icode` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($opn_sale_feed_item_qty[$row['itm_code']])){
                        $opn_sale_feed_item_qty[$row['itm_code']] = $opn_sale_feed_item_qty[$row['itm_code']] + ($row['rcd_qty'] + $row['fre_qty']);
                        $opn_sale_feed_item_amt[$row['itm_code']] = $opn_sale_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $opn_sale_feed_item_qty[$row['itm_code']] = ($row['rcd_qty'] + $row['fre_qty']);
                        $opn_sale_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as itm_code FROM `item_stocktransfers` WHERE `date` < '$fdate'".$item_filter."".$str_item_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `code` ORDER BY `code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($opn_trout_feed_item_qty[$row['itm_code']])){
                        $opn_trout_feed_item_qty[$row['itm_code']] = $opn_trout_feed_item_qty[$row['itm_code']] + ($row['quantity']);
                        $opn_trout_feed_item_amt[$row['itm_code']] = $opn_trout_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $opn_trout_feed_item_qty[$row['itm_code']] = ($row['quantity']);
                        $opn_trout_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }

                /* *****Between Date Transactions***** */
                
                $btw_pur_feed_item_qty = $btw_pur_feed_item_amt = $btw_only_qty = $btw_only_amt = $btw_trin_feed_item_qty = $btw_trin_feed_item_amt = array();
                $sql_record = "SELECT SUM(quantity) as rcd_qty,SUM(amount) as amount,type_code as itm_code FROM `broiler_openings` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pur_item_filter1."".$inv_mill_filter1." AND `active` = '1' AND `dflag` = '0' GROUP BY `type_code` ORDER BY `type_code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($btw_only_qty[$row['itm_code']])){
                        $btw_only_qty[$row['itm_code']] = $btw_only_qty[$row['itm_code']] + ($row['rcd_qty']);
                        $btw_only_amt[$row['itm_code']] = $btw_only_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $btw_only_qty[$row['itm_code']] = ($row['rcd_qty']);
                        $btw_only_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as amount,icode as itm_code FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pur_item_filter."".$inv_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `icode` ORDER BY `icode` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($btw_pur_feed_item_qty[$row['itm_code']])){
                        $btw_pur_feed_item_qty[$row['itm_code']] = $btw_pur_feed_item_qty[$row['itm_code']] + ($row['rcd_qty'] + $row['fre_qty']);
                        $btw_pur_feed_item_amt[$row['itm_code']] = $btw_pur_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $btw_pur_feed_item_qty[$row['itm_code']] = ($row['rcd_qty'] + $row['fre_qty']);
                        $btw_pur_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as itm_code FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$str_item_filter."".$str_in_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `code` ORDER BY `code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($btw_trin_feed_item_qty[$row['itm_code']])){
                        $btw_trin_feed_item_qty[$row['itm_code']] = $btw_trin_feed_item_qty[$row['itm_code']] + ($row['quantity']);
                        $btw_trin_feed_item_amt[$row['itm_code']] = $btw_trin_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $btw_trin_feed_item_qty[$row['itm_code']] = ($row['quantity']);
                        $btw_trin_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $btw_consumed_feed_item_qty = $btw_consumed_feed_item_amt = array();
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,feed_code as prod_itm_code,item_code as itm_code FROM `broiler_feed_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$consumed_item_filter."".$consumed_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `formula_code`,`item_code` ORDER BY `item_code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($btw_consumed_feed_item_qty[$row['itm_code']."@".$row['prod_itm_code']])){
                        $btw_consumed_feed_item_qty[$row['itm_code']."@".$row['prod_itm_code']] = $btw_consumed_feed_item_qty[$row['itm_code']."@".$row['prod_itm_code']] + ($row['quantity']);
                        $btw_consumed_feed_item_amt[$row['itm_code']."@".$row['prod_itm_code']] = $btw_consumed_feed_item_amt[$row['itm_code']."@".$row['prod_itm_code']] + ($row['amount']);
                    }
                    else{
                        $btw_consumed_feed_item_qty[$row['itm_code']."@".$row['prod_itm_code']] = ($row['quantity']);
                        $btw_consumed_feed_item_amt[$row['itm_code']."@".$row['prod_itm_code']] = ($row['amount']);
                    }
                    $formula_items[$row['prod_itm_code']] = $row['prod_itm_code'];
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $btw_sale_feed_item_qty = $btw_sale_feed_item_amt = $btw_trout_feed_item_qty = $btw_trout_feed_item_amt = array();
                $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as amount,icode as itm_code FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pur_item_filter."".$inv_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `icode` ORDER BY `icode` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($btw_sale_feed_item_qty[$row['itm_code']])){
                        $btw_sale_feed_item_qty[$row['itm_code']] = $btw_sale_feed_item_qty[$row['itm_code']] + ($row['rcd_qty'] + $row['fre_qty']);
                        $btw_sale_feed_item_amt[$row['itm_code']] = $btw_sale_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $btw_sale_feed_item_qty[$row['itm_code']] = ($row['rcd_qty'] + $row['fre_qty']);
                        $btw_sale_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as itm_code FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$str_item_filter."".$str_out_mill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `code` ORDER BY `code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    if(!empty($btw_trout_feed_item_qty[$row['itm_code']])){
                        $btw_trout_feed_item_qty[$row['itm_code']] = $btw_trout_feed_item_qty[$row['itm_code']] + ($row['quantity']);
                        $btw_trout_feed_item_amt[$row['itm_code']] = $btw_trout_feed_item_amt[$row['itm_code']] + ($row['amount']);
                    }
                    else{
                        $btw_trout_feed_item_qty[$row['itm_code']] = ($row['quantity']);
                        $btw_trout_feed_item_amt[$row['itm_code']] = ($row['amount']);
                    }
                    $feed_items[$row['itm_code']] = $row['itm_code'];
                }

                $pitem_count = sizeof($formula_items) * 2;
                $html = $nhtml = $fhtml = '';
                $html .= '<thead class="thead3" id="head_names">';

                $nhtml .= '<tr style="text-align:center;" align="center">';
                $fhtml .= '<tr style="text-align:center;" align="center">';

                $nhtml .= '<thcolspan="8">Avaialble Stock</th>'; $fhtml .= '<th colspan="8">Avaialble Stock</th>';
                if($pitem_count > 0){ $nhtml .= '<th colspan="'.$pitem_count.'">Hatchery</th>'; $fhtml .= '<th colspan="'.$pitem_count.'">Hatchery</th>'; }
                $nhtml .= '<thcolspan="3">Closing Stock</th>'; $fhtml .= '<th colspan="3">Closing Stock</th>';

                $nhtml .= '</tr>';
                $fhtml .= '</tr>';
                $nhtml .= '<tr style="text-align:center;" align="center">';
                $fhtml .= '<tr style="text-align:center;" align="center">';

                $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
                $nhtml .= '<th>Item</th>'; $fhtml .= '<th id="order">Item</th>';
                $nhtml .= '<th>Opening</th>'; $fhtml .= '<th id="order_num">Opening</th>';
                $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
                $nhtml .= '<th>Purchase</th>'; $fhtml .= '<th id="order_num">Purchase</th>';
                $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
                $nhtml .= '<th>Total Opening</th>'; $fhtml .= '<th id="order_num">Total Opening</th>';
                $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
                foreach($formula_items as $fitems){ $nhtml .= '<th>'.$item_name[$fitems].'</th><th>Rate</th>'; $fhtml .= '<th id="order">'.$item_name[$fitems].'</th><th id="order_num">Rate</th>'; }
                $nhtml .= '<th>Closing Stock</th>'; $fhtml .= '<th id="order_num">Closing Stock</th>';
                $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
                $nhtml .= '<th>Stock Value</th>'; $fhtml .= '<th id="order_num">Stock Value</th>';
                $nhtml .= '</tr>';
                $fhtml .= '</tr>';
                $html .= $fhtml;
                $html .= '</thead>';
                echo $html;
            ?>
            <tbody class="tbody1">
                <?php
                $slno = $final_opening_stock = $final_opening_rate = $final_purchased_stock = $final_purchased_rate = $final_total_opening_stock = $final_total_opening_rate = $final_total_consumed_stock = $final_total_consumed_rate = $final_total_closing_stock = $final_total_closing_rate = $final_total_closing_amount = 0;
                $opening_stock = $opening_rate = $purchased_stock = $purchased_rate = $total_opening_stock = $total_opening_rate = $total_closing_stock = $total_closing_rate = $total_closing_amount = 0;
                $total_consumed_stock = $total_consumed_rate = $total_consumed_amount = array();
                foreach($feed_items as $itm_list){
                    $slno++;
                    $opening_stock = ($opn_pur_feed_item_qty[$itm_list] + $opn_trin_feed_item_qty[$itm_list] + $opn_only_qty[$itm_list]) - ($opn_consumed_feed_item_qty[$itm_list] + $opn_sale_feed_item_qty[$itm_list] + $opn_trout_feed_item_qty[$itm_list]);
                    $opening_amount = ($opn_pur_feed_item_amt[$itm_list] + $opn_trin_feed_item_amt[$itm_list] + $opn_only_amt[$itm_list]) - ($opn_consumed_feed_item_amt[$itm_list] + $opn_sale_feed_item_amt[$itm_list] + $opn_trout_feed_item_amt[$itm_list]);
                    
                    if($opening_amount > 0 && $opening_stock > 0){
                        $opening_rate = $opening_amount / $opening_stock;
                    }
                    else{
                        $opening_rate = 0;
                    }

                    $bw_qty_stock = $btw_only_qty[$itm_list];
                    $bw_amt_stock = $btw_only_amt[$itm_list];
                    $purchased_stock = $btw_pur_feed_item_qty[$itm_list];
                    $purchased_amount = $btw_pur_feed_item_amt[$itm_list];
                    if($purchased_amount > 0 && $purchased_stock > 0){
                        $purchased_rate = $purchased_amount / $purchased_stock;
                    }
                    else{
                        $purchased_rate = 0;
                    }
                    

                    $total_opening_stock = ($opening_stock + $purchased_stock + $bw_qty_stock);
                    $total_opening_amount = ($opening_amount + $purchased_amount + $bw_amt_stock);
                    if($total_opening_amount > 0 && $total_opening_stock > 0){
                        $total_opening_rate = $total_opening_amount / $total_opening_stock;
                    }
                    else{
                        $total_opening_rate = 0;
                    }
                    
                ?>
                <tr>
                    <td title='Sl.No' style="text-align:center;"><?php echo $slno; ?></td>
                    <td title='Item'><?php echo $item_name[$itm_list]; ?></td>
                    <td title='Opening' style="text-align:right;"><?php echo number_format_ind(round($opening_stock,2)); ?></td>
                    <td title='Rate' style="text-align:right;"><?php echo number_format_ind(round($opening_rate,2)); ?></td>
                    <td title='Purchase' style="text-align:right;"><?php echo number_format_ind(round($purchased_stock,2)); ?></td>
                    <td title='Rate' style="text-align:right;"><?php echo number_format_ind(round($purchased_rate,2)); ?></td>
                    <td title='Total Opening' style="text-align:right;"><?php echo number_format_ind(round($total_opening_stock,2)); ?></td>
                    <td title='Rate' style="text-align:right;"><?php echo number_format_ind(round($total_opening_rate,2)); ?></td>
                    <?php
                    $present_consumed_stock = $present_consumed_amount = 0;
                    foreach($formula_items as $fitems){
                        $total_consumed_stock[$fitems] = $btw_consumed_feed_item_qty[$itm_list."@".$fitems];
                        $total_consumed_amount[$fitems] = $btw_consumed_feed_item_amt[$itm_list."@".$fitems];
                        if(!empty($total_consumed_amount[$fitems]) && $total_consumed_amount[$fitems] > 0 && !empty($total_consumed_stock[$fitems]) && $total_consumed_stock[$fitems] > 0){
                            $total_consumed_rate[$fitems] = $total_consumed_amount[$fitems] / $total_consumed_stock[$fitems];
                        }
                        else{
                            $total_consumed_rate[$fitems] = 0;
                        }
                        

                        $present_consumed_stock = $present_consumed_stock + $total_consumed_stock[$fitems];
                        $present_consumed_amount = $present_consumed_amount + $total_consumed_amount[$fitems];
                        echo "<td style='text-align:right;'>".number_format_ind(round($total_consumed_stock[$fitems],2))."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind(round($total_consumed_rate[$fitems],2))."</td>";
                    }
                    $total_closing_stock = $total_opening_stock - $present_consumed_stock;
                    $total_closing_amount = $total_opening_amount - $present_consumed_amount;
                    if($total_closing_amount > 0 && $total_closing_stock > 0){
                        $total_closing_rate = $total_closing_amount / $total_closing_stock;
                    }
                    else{
                        $total_closing_rate = 0;
                    }
                    
                    ?>
                    <td title='Closing Stock' style="text-align:right;"><?php echo number_format_ind(round($total_closing_stock,2)); ?></td>
                    <td title='Rate' style="text-align:right;"><?php echo number_format_ind(round($total_closing_rate,2)); ?></td>
                    <td title='Stock Value' style="text-align:right;"><?php echo number_format_ind(round($total_closing_amount,2)); ?></td>
                </tr>
                <?php
                    $final_opening_stock = $final_opening_stock + $opening_stock;
                    $final_opening_rate = $final_opening_rate + $opening_rate;
                    $final_purchased_stock = $final_purchased_stock + $purchased_stock;
                    $final_purchased_rate = $final_purchased_rate + $purchased_rate;
                    $final_total_opening_stock = $final_total_opening_stock + $total_opening_stock;
                    $final_total_opening_rate = $final_total_opening_rate + $total_opening_rate;
                    $final_total_consumed_stock = $final_total_consumed_stock + $total_consumed_stock[$fitems];
                    $final_total_consumed_rate = $final_total_consumed_rate + $total_consumed_rate[$fitems];
                    $final_total_closing_stock = $final_total_closing_stock + $total_closing_stock;
                    $final_total_closing_rate = $final_total_closing_rate + $total_closing_rate;
                    $final_total_closing_amount = $final_total_closing_amount + $total_closing_amount;
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="2" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($final_opening_stock,2)); ?></th>
                <th style="text-align:right;"><?php //echo number_format_ind(round($final_opening_rate,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($final_purchased_stock,2)); ?></th>
                <th style="text-align:right;"><?php //echo number_format_ind(round($final_purchased_rate,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($final_total_opening_stock,2)); ?></th>
                <th style="text-align:right;"><?php //echo number_format_ind(round($final_total_opening_rate,2)); ?></th>
                <?php
                    foreach($formula_items as $fitems){
                ?>
                <th style="text-align:right;"><?php echo number_format_ind(round($final_total_consumed_stock,2)); ?></th>
                <th style="text-align:right;"><?php //echo number_format_ind(round($final_total_consumed_rate,2)); ?></th>
                <?php
                    }
                ?>
                <th style="text-align:right;"><?php echo number_format_ind(round($final_total_closing_stock,2)); ?></th>
                <th style="text-align:right;"><?php //echo number_format_ind(round($final_total_closing_rate,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($final_total_closing_amount,2)); ?></th>
            </tr>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script type="text/javascript" src="table_sorting_wauto_slno.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_download_excel.js"></script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $fhtml; ?>';
                document.getElementById("head_names").innerHTML = html;
                    
                $('#export').select2();
                document.getElementById("export").value = "display";
                $('#export').select2();
                table_sort();
                table_sort2();
                table_sort3();
            }
        </script>
        <script>
            function fetch_item_list(){
                var item_categories = document.getElementById("item_categories").value;
                var items = document.getElementById("items").value;
                var cat_value = '<?php echo $item_categories; ?>';
                var item_value = '<?php echo $items; ?>';
				removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(item_categories.match("all")){
                    <?php
                    foreach($mill_item_code as $micode){
                        //echo "if(fcode == '$micode'){";
                    ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $mill_item_name[$micode]; ?>"); theOption1.value = "<?php echo $micode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        //echo "}";
                    }
                    ?>
                }
                else{
                    <?php
                    foreach($mill_item_code as $micode){
                        echo "if(item_categories == '$mill_item_category[$micode]'){";
                    ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $mill_item_name[$micode]; ?>"); theOption1.value = "<?php echo $micode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        echo "}";
                    }
                    ?>
                }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            //fetch_item_list();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>