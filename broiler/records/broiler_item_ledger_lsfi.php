<?php
//broiler_item_ledger_lsfi.php
include "../newConfig.php";
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Item Ledger Report";
include "header_head.php";
$user_code = $_SESSION['userid'];

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_broiler_broilermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_broiler_broilermaster.breeder_units;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_broiler_broilermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_batch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_batch LIKE poulso6_admin_broiler_broilermaster.breeder_batch;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_shed_allocation LIKE poulso6_admin_broiler_broilermaster.breeder_shed_allocation;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_dayentry_consumed", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_consumed LIKE poulso6_admin_broiler_broilermaster.breeder_dayentry_consumed;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_dayentry_produced", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_produced LIKE poulso6_admin_broiler_broilermaster.breeder_dayentry_produced;"; mysqli_query($conn,$sql1); }
if(in_array("item_stocktransfers", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stocktransfers LIKE poulso6_admin_broiler_broilermaster.item_stocktransfers;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_extra_access LIKE poulso6_admin_broiler_broilermaster.breeder_extra_access;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_broiler_broilermaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `inv_sectors`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("brd_sflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `inv_sectors` ADD `brd_sflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Sectors' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `item_category`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("main_category", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `main_category` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `description`"; mysqli_query($conn,$sql); }
if(in_array("bfeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bfeed_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Feed' AFTER `main_category`"; mysqli_query($conn,$sql); }
if(in_array("begg_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `begg_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder Eggs' AFTER `bfeed_flag`"; mysqli_query($conn,$sql); }
if(in_array("bmv_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bmv_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Breeder MedVac' AFTER `begg_flag`"; mysqli_query($conn,$sql); }


$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

//Breeder
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query); $sector_code = $sector_name = array();
if((int)$bfeed_scnt > 0){
    $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
    if($bfeed_stkon == "FARM"){
        $bsql = "SELECT * FROM `breeder_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
    }
    else if($bfeed_stkon == "UNIT"){
        $bsql = "SELECT * FROM `breeder_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
    }
    else if($bfeed_stkon == "SHED"){
        $bsql = "SELECT * FROM `breeder_sheds` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
    }
    else if($bfeed_stkon == "BATCH"){
        $bsql = "SELECT * FROM `breeder_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
    }
    else if($bfeed_stkon == "FLOCK"){
        $bsql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
    }
    else{ }
}
if((int)$bsec_sflag > 0){
    $bsql = "SELECT * FROM `inv_sectors` WHERE `brd_sflag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
    while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
}

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql); $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $items = $sectors = "select"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $items = $_POST['items'];
    $farms = $sectors = $_POST['sectors'];
    $excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_item_ledger_lsfi-Excel.php?fromdate=".$fdate."&todate=".$tdate."&items=".$items."&sectors=".$farms;
}
else{
    $url = "";
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
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
         
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                    .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead2_empty_row { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" style="width:auto;" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Item Ledger Report</h5></th>
                    <th colspan="17" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_item_ledger_lsfi.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="26">
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
                                    <label>items</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="select" <?php if($items == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($item_code as $icode){ if($item_name[$icode] != ""){ ?>
                                        <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Sector</label>
                                    <select name="sectors" id="sectors" class="form-control select2" style="width:250px;">
                                        <option value="select" <?php if($sectors == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($sectors == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option>
                                        <?php } } ?>
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
                    <th colspan="6">CheckAll</th>
                    <th colspan="3">Purchase/Transferred IN</th>
                    <th colspan="3">Consume/Sale/Transferred OUT</th>
                    <th colspan="3">Closing</th>
                </tr>
                <tr align="center">
                    <!--<th><input type="checkbox" name="checkall" id="checkall" class="form-control" style='transform: scale(.5);text-align:center;' /></th>-->
                    <th>Sl. No.</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Trnum</th>
                    <th>Location</th>
                    <th>Remarks</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_POST['submit_report']) == true){
                $start_date = $end_date = $dend_date = $dstart_date = "";
                $icat = $item_category[$items];
                $sql = "SELECT * FROM `item_category` WHERE `code` = '$icat'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $icat_iac = $row['iac'];
                    $icat_pvac = $row['pvac'];
                    $icat_pdac = $row['pdac'];
                    $icat_cogsac = $row['cogsac'];
                    $icat_wpac = $row['wpac'];
                    $icat_sac = $row['sac'];
                    $icat_srac = $row['srac'];
                }
                $cr_stock = $cr_amount = $dr_stock = 0;
                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` LIKE '$icat_iac' AND `date` < '$fdate' AND `item_code` LIKE '$items' AND `location` = '$sectors' AND `active` = 1 AND `dflag` = 0 ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($row['crdr'] == "CR"){
                        $current_stock = $current_stock - (float)$row['quantity'];
                        $current_amount = $current_amount - ($current_price * (float)$row['quantity']);

                        /*Stock Account and COGS account Modifications
                        $stk_price = $stk_amount = 0; $stk_trnum = $stk_icode = $stk_sector = $stk_iac = $stk_cogs = $usql = "";
                        $stk_price = $current_price; $stk_amount = ($current_price * (float)$row['quantity']);; $stk_trnum = $row['trnum']; $stk_icode = $row['item_code']; $stk_sector = $row['location']; $stk_iac = $icat_iac; $stk_cogs = $icat_cogsac;
                        $usql = "UPDATE `account_summary` SET `price` = '$stk_price',`amount` = '$stk_amount' WHERE `trnum` = '$stk_trnum' AND `coa_code` IN ('$stk_iac','$stk_cogs') AND `item_code` LIKE '$stk_icode' AND `location` = '$stk_sector';"; mysqli_query($conn,$usql);
                        */
                        if(number_format_ind(round($current_stock,2)) == "0.00"){ $current_stock = $current_amount = $current_price = 0; }
                    }
                    else if($row['crdr'] == "DR"){
                        $current_stock = $current_stock + (float)$row['quantity'];
                        $current_amount = $current_amount + (float)$row['amount'];
                        if($current_amount > 0 && $current_stock > 0){
                            $current_price = $current_amount / $current_stock;
                        }
                        else{
                            $current_price = 0;
                        }
                        
                        if(number_format_ind(round($current_stock,2)) == "0.00"){ $current_stock = $current_amount = $current_price = 0; }
                    }
                    else{ }
                }
                //echo "<br/>".$current_stock."-".$current_price."-".$current_amount;
                //Opening Stock
                echo "<tr>";
                echo "<td>".$c."</td>";
                echo "<td colspan='5' style='text-align:center;'>Opening Stock</td>";
                if($dr_stock < $cr_stock){
                    $total_con_qty = $total_con_qty + $current_stock;
                    $total_con_amt = $total_con_amt + $current_amount;
                    echo "<td></td><td></td><td></td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_stock)."</td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round($current_price,2))."</td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_amount)."</td>";
                }
                else if($dr_stock >= $cr_stock){
                    $total_pur_qty = $total_pur_qty + $current_stock;
                    $total_pur_amt = $total_pur_amt + $current_amount;
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_stock)."</td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round($current_price,2))."</td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_amount)."</td>";
                    echo "<td></td><td></td><td></td>";
                }
                else{ }
                echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_stock)."</td>";
                echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round($current_price,2))."</td>";
                echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_amount)."</td>";
                echo "</tr>";

                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` LIKE '$icat_iac' AND `date` >= '$fdate' AND `date` <= '$tdate' AND `item_code` LIKE '$items' AND `location` = '$sectors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql); $c = 0; $trnums = array(); while($row = mysqli_fetch_assoc($query)){ $trnums[$row['trnum']] = $row['trnum']; }
                $tr_list = implode("','", $trnums);

                /*sale Warehouse
                $sql = "SELECT * FROM `broiler_sales` WHERE `trnum` IN ('$tr_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $key = $row['date']."@".$row['trnum']."@".$row['icode']."@".$sale_sec; }
                */
                $sql = "SELECT *  FROM `item_stocktransfers` WHERE  `trnum`IN ('$tr_list') AND `code` LIKE '$items'  AND `active` = 1 AND `dflag` = 0";
                $query = mysqli_query($conn,$sql); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['date']."@".$row['trnum']."@".$row['towarehouse'];
                    $to_location_mort[$key] = (float)$row['mort_qty'] + (float)$row['weak_qty'] + (float)$row['legw_qty'] + (float)$row['cull_qty'] ;
                }
                
                $sql = "SELECT * FROM `account_summary` WHERE `trnum` IN ('$tr_list') AND `item_code` LIKE '$items' AND `location` != '$sectors' AND `location` != '' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                    if($old_trnm == $row['trnum']){
                        $ac += 1; 
                    }else{
                        $ac = 0; 
                    }
                    $key1 = $row['date']."@".$row['trnum']."@".$row['location'];
                    $tot_qty = round(($row['quantity'] + $to_location_mort[$key1]),5);
                    $key = $row['date']."@".$row['trnum']."@". $tot_qty."@".$ac;
                    $to_location[$key] = $row['location'];
                    $old_trnm = $row['trnum'];
                }


                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` LIKE '$icat_iac' AND `date` >= '$fdate' AND `date` <= '$tdate' AND `item_code` LIKE '$items' AND `location` = '$sectors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                    if($old_trnm == $row['trnum']){
                        $ac += 1; 
                    }else{
                        $ac = 0; 
                    }
                    $old_trnm = $row['trnum'];
                    $key = $row['date']."@".$row['trnum']."@".round($row['quantity'],5)."@".$ac;
                    if(str_contains(strtolower($row['etype']),strtolower("sales"))){
                       // $sname = $ven_name[$row['vendor']]; // $sector_name
                        $sname = $sector_name[$row['vendor']];
                    }
                    else if(empty($to_location[$key]) || $to_location[$key] == ""){ $sname = $sector_name[$row['location']]; }
                    else{ $sname = $sector_name[$to_location[$key]]; }
                    $c++;
                   
                    echo "<tr>";
                    echo "<td>".$c."</td>";
                    echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
                    echo "<td>".$row['etype']."</td>";
                    echo "<td>".$row['trnum']."</td>";
                    echo "<td>".$sname."</td>";
                    echo "<td>".$row['remarks']."</td>";
                    if($row['crdr'] == "CR"){
                        //echo $row['trnum']."//".$row['amount']."</br>";
                        $total_con_qty = $total_con_qty + (float)$row['quantity'];
                        $total_con_amt = $total_con_amt + ($current_price * (float)$row['quantity']);

                        $current_stock = $current_stock - (float)$row['quantity'];
                        $current_amount = $current_amount - ($current_price * (float)$row['quantity']);

                        /*Stock Account and COGS account Modifications
                        $stk_price = $stk_amount = 0; $stk_trnum = $stk_icode = $stk_sector = $stk_iac = $stk_cogs = $usql = "";
                        $stk_price = $current_price; $stk_amount = ($current_price * (float)$row['quantity']);; $stk_trnum = $row['trnum']; $stk_icode = $row['item_code']; $stk_sector = $row['location']; $stk_iac = $icat_iac; $stk_cogs = $icat_cogsac;
                        $usql = "UPDATE `account_summary` SET `price` = '$stk_price',`amount` = '$stk_amount' WHERE `trnum` = '$stk_trnum' AND `coa_code` IN ('$stk_iac','$stk_cogs') AND `item_code` LIKE '$stk_icode' AND `location` = '$stk_sector';"; mysqli_query($conn,$usql);
                        */
                        
                        echo "<td></td><td></td><td></td>";
                        echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind((float)$row['quantity'])."</td>";
                        echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round($current_price,2))."</td>";
                        echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round(($current_price * (float)$row['quantity']),2))."</td>";
                    }
                    else if($row['crdr'] == "DR"){
                        $total_pur_qty = $total_pur_qty + (float)$row['quantity'];
                        $total_pur_amt = $total_pur_amt + (float)$row['amount'];

                        $current_stock = $current_stock + (float)$row['quantity'];
                        $current_amount = $current_amount + (float)$row['amount'];
                        if($current_amount > 0 && $current_stock > 0){
                            $current_price = round(($current_amount / $current_stock),15);
                        }
                        else{
                            $current_price = 0;
                        }
                        
                        echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind((float)$row['quantity'])."</td>";
                        echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round($row['price'],2))."</td>";
                        echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind((float)$row['amount'])."</td>";
                        echo "<td></td><td></td><td></td>";
                    }
                    else{ }
                    if(number_format_ind($total_con_qty) == number_format_ind($total_pur_qty)){
                        $current_stock = $current_price = $current_amount = 0;
                    }
                    //if($_SERVER['REMOTE_ADDR'] == "49.205.130.10"){ echo "<br/>".$current_stock."=".$current_price."=".$current_amount; }
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_stock)."</td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind(round($current_price,2))."</td>";
                    echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($current_amount)."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            
            <tr class="thead4">
                <th colspan="6" style="text-align:center;">Total</th>
				<th style="text-align:right;"><?php echo number_format_ind($total_pur_qty); ?></th>
				<th style="text-align:right;">
                <?php
                if($total_pur_amt > 0 && $total_pur_qty > 0){
                    echo number_format_ind(round(($total_pur_amt / $total_pur_qty),2));
                }
                else{
                    echo number_format_ind(0);
                }
                ?>
                </th>
				<th style="text-align:right;"><?php echo number_format_ind($total_pur_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($total_con_qty); ?></th>
				<th style="text-align:right;">
                <?php
                if($total_con_amt > 0 && $total_con_qty > 0){
                    echo number_format_ind(round(($total_con_amt / $total_con_qty),2));
                }
                else{
                    echo number_format_ind(0);
                }
                
                ?>
                </th>
				<th style="text-align:right;"><?php echo number_format_ind($total_con_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind(($current_stock)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind((round($current_price,2))); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($current_amount); ?></th>
            </tr>
        <?php
            }
        ?>
        </table>
        <script>
            function checkval(){
                var items = document.getElementById("items").value;
                var sectors = document.getElementById("sectors").value;
                if(items.match("select")){
                    alert("Please select Item");
                    document.getElementById("items").focus();
                    return true;
                }
                else if(sectors.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("sectors").focus();
                    return true;
                }
                else{
                    return true;
                }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>