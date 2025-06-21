<?php
//broiler_stock_report7.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";

include "../broiler_check_tableavailability.php";
$file_name = "Raw Ingredients Report";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_pc_goodsreceipt", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_pc_goodsreceipt LIKE poulso6_admin_broiler_broilermaster.broiler_pc_goodsreceipt;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `broiler_itemreturns`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `farm_batch` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("vehicle_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `vehicle_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `farm_batch`"; mysqli_query($conn,$sql); }
if(in_array("driver_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `driver_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `vehicle_code`"; mysqli_query($conn,$sql); }
if(in_array("stk_status", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `stk_status` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `driver_code`"; mysqli_query($conn,$sql); }

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

$sector_code = $sector_name = $batch_name = $vehicle_code = $vehicle_name = $emp_code = $emp_name = $icat_code = $icat_name = 
$item_code = $item_name = $item_category = $item_unit = array();
if($count86 > 0){
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
}
if($count26 > 0){
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
}
if($count12 > 0){
$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }
}
if($count68 > 0){
$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
}
if($count25 > 0){
$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
}
$icat_code = array();
if($count87 > 0){
$sql = "SELECT * FROM `item_category` WHERE `rawing_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }
}
$icat_fglist = implode("','",$icat_code);
if($count89 > 0){
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; $item_unit[$row['code']] = $row['cunits']; $lstk_flag[$row['code']] = $row['lsflag']; $lstk_qty[$row['code']] = $row['lsqty']; }


$sql = "SELECT * FROM `item_details` WHERE `description` LIKE 'Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

}
$fdate = $tdate = date("Y-m-d"); $item_cat = $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $item_cat = $_POST['item_cat'];
    $sectors = $_POST['sectors'];
    if($sectors == "all"){
        $opening_sector_filter = "";
        $pursal_sector_filter = "";
        $grn_sector_filter = "";
        $trasin_sector_filter = "";
        $trasout_sector_filter = "";
        $consume_sector_filter = "";
        $conprod_feedmill_filter = "";
        $hprod_sector_filter = "";
        $stkadjust_sector_filter = "";
        $trayset_sector_filter = "";
    }
    else{
        $opening_sector_filter = " AND `sector_code` IN ('$sectors')";
        $pursal_sector_filter = " AND `warehouse` IN ('$sectors')";
        $grn_sector_filter = " AND `warehouse` IN ('$sectors')";
        $trasin_sector_filter = " AND `towarehouse` IN ('$sectors')";
        $trasout_sector_filter = " AND `fromwarehouse` IN ('$sectors')";
        $consume_sector_filter = " AND `farm_code` IN ('$sectors')";
        $conprod_feedmill_filter = " AND `feed_mill` IN ('$sectors')";
        $hprod_sector_filter = " AND `sector_code` IN ('$sectors')";
        $stkadjust_sector_filter = " AND `sector` IN ('$sectors')";
        $trayset_sector_filter = " AND `hathery_code` IN ('$sectors')";
    }
    if($item_cat == "all"){
        $opening_item_filter = $pursal_item_filter = $grn_item_filter = $trasin_item_filter = $stkadjust_item_filter = $trasout_item_filter = $consume_item_filter = $fmconsume_item_filter = 
        $hprod_item_filter = $fmconsume_bagitem_filter = $fmproduce_item_filter = $trayset_item_filter = "";
    }
    else{
        $icat_list = $item_list = "";
        foreach($item_code as $icode){
            $item_category[$icode];
            if($item_category[$icode] == $item_cat){
                if($icat_list == ""){
                    $icat_list = $icode;
                }
                else{
                    $icat_list = $icat_list."','".$icode;
                }
            }
        }
        $opening_item_filter = " AND `type_code` IN ('$icat_list')";
        $pursal_item_filter = " AND `icode` IN ('$icat_list')";
        $grn_item_filter = " AND `item_code` IN ('$icat_list')";
        $return_item_filter = " AND `itemcode` IN ('$icat_list')";
        $trasin_item_filter = " AND `code` IN ('$icat_list')";
        $trasout_item_filter = " AND `code` IN ('$icat_list')";
        $consume_item_filter = " AND `item_code` IN ('$icat_list')";
        $fmconsume_item_filter = " AND `item_code` IN ('$icat_list')";
        $hprod_item_filter = "";
        $fmconsume_bagitem_filter = " AND `bag_code_feed` IN ('$icat_list')";
        $fmproduce_item_filter = " AND `feed_code` IN ('$icat_list')";
        $stkadjust_item_filter = " AND `item_code` IN ('$icat_list')";
        $trayset_item_filter = " AND `item_code` IN ('$icat_list')";
    }
	$excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/broiler_stock_report7-Excel.php?fdate=".$fdate."&tdate=".$tdate."&item_cat=".$item_cat."&sectors=".$sectors;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <!--<script>
            var exptype = '<?php //echo $excel_type; ?>';
            var url = '<?php //echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>-->
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <style>
            /*.thead3 th { top: 0; position: sticky; background-color: #9cc2d5; }*/
            #head_row1{
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
            }
            #head_row2{
                top: 21px;
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="26" align="center"><?php echo $row['cdetails']; ?><h3><?php echo $file_name; ?></th>
                </tr>
                
            </thead>
            <?php } ?>
            <form action="broiler_stock_report7.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="28">
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
                                    <select name="item_cat" id="item_cat" class="form-control select2">
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Warehouse</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <!--<option value="all" <?php //if($sectors == "all"){ echo "selected"; } ?>>-All-</option>-->
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
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
        </table>
        <table class="tbl" align="center" id="main_table">
            <thead class="thead3" id="head_row1" align="center">
                <tr align="center">
                    <th>Category</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Unit</th>
                    <th >Opening</th>
                    <th >Purchase</th>  
                    <th >Consumption</th>     
                    <th colspan="3">Closing</th>
                </tr>
            </thead>
            <thead class="thead3" id="head_row2" align="center">
                <tr>
                    <th colspan="4"></th>
                    <th>Quantity</th>
                    <th>Quantity</th>
                    <th>Quantity</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    $key = "";
                    $opening_stock_qty = $opening_stock_amt = $opening_sales_qty = $opening_sales_amt = $opening_sreturns_qty = $opening_sreturns_amt = $opening_purchases_qty = $opening_purchases_amt = 
                    $opening_preturns_qty = $opening_preturns_amt = $opening_to_sector_qty = $opening_to_sector_amt = $opening_from_sector_qty = $opening_from_sector_amt = $opening_medvac_qty = 
                    $opening_dentry_qty = $opening_fmproduce_qty = $opening_fmproduce_amt = $opening_fmconsume_qty = $opening_fmconsume_amt = $opening_trayset_qty = $opening_trayset_amt = array();
                    if($count58 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,type_code as icode,date FROM `broiler_openings` WHERE `date` < '$fdate'".$opening_item_filter."".$opening_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,type_code ORDER BY `type_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_stock_qty[$key])){ $opening_stock_qty[$key] = $opening_stock_qty[$key] + $row['quantity']; } else{ $opening_stock_qty[$key] = $row['quantity']; }
                                if(!empty($opening_stock_amt[$key])){ $opening_stock_amt[$key] = $opening_stock_amt[$key] + $row['amount']; } else{ $opening_stock_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_stock_qty = $opening_stock_amt = array(); }
                    }
                    $opening_purchases = $opening_purchases_amt = $grn_trnums = array(); $grn_fltr = ""; 
                    $sql = "SELECT * FROM `broiler_pc_goodsreceipt` WHERE `date` < '$fdate'".$grn_item_filter."".$grn_sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`item_code` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['item_code']."@".$row['date']; $active_items[$row['item_code']] = $row['item_code'];
                            if(!empty($start_date[$row['item_code']])){ if(strtotime($row['date']) <= $start_date[$row['item_code']]){ $start_date[$row['item_code']] = strtotime($row['date']); } } else{ $start_date[$row['item_code']] = strtotime($row['date']); }
                            if(!empty($end_date[$row['item_code']])){ if(strtotime($row['date']) >= $end_date[$row['item_code']]){ $end_date[$row['item_code']] = strtotime($row['date']); } } else{ $end_date[$row['item_code']] = strtotime($row['date']); }
                            if(!empty($opening_purchases_qty[$key])){ $opening_purchases_qty[$key] = $opening_purchases_qty[$key] + $row['rcvd_qty']; } else{ $opening_purchases_qty[$key] = $row['rcvd_qty']; }
                            if(!empty($opening_purchases_amt[$key])){ $opening_purchases_amt[$key] = $opening_purchases_amt[$key] + $row['item_amt']; } else{ $opening_purchases_amt[$key] = $row['item_amt']; }
                            $grn_trnums[$row['trnum']] = $row['trnum'];
                        }
                    }
                    if(sizeof($grn_trnums) > 0){
                        $grtrno_list = "";
                        $grtrno_list = implode("','",$grn_trnums);
                        $grn_fltr = " AND `gr_trnum` NOT IN ('$grtrno_list')";
                    }

                    if($count61 > 0){
                        $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` < '$fdate'".$pursal_item_filter."".$pursal_sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `icode` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                if(empty($grn_trnums[$row['gr_trnum']]) || $grn_trnums[$row['gr_trnum']] == ""){
                                    $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                    if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                    if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                    if(!empty($opening_purchases_qty[$key])){ $opening_purchases_qty[$key] = $opening_purchases_qty[$key] + $row['rcd_qty'] + $row['fre_qty']; } else{ $opening_purchases_qty[$key] = $row['rcd_qty'] + $row['fre_qty']; }
                                    if(!empty($opening_purchases_amt[$key])){ $opening_purchases_amt[$key] = $opening_purchases_amt[$key] + $row['item_tamt']; } else{ $opening_purchases_amt[$key] = $row['item_tamt']; }
                                }
                            }
                        }
                    }

                    if($count54 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,itemcode as icode,date FROM `broiler_itemreturns` WHERE `date` < '$fdate'".$return_item_filter."".$pursal_sector_filter." AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' GROUP BY date,itemcode ORDER BY `itemcode` ASC"; // AND `stk_status` NOT LIKE '%wast%'
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_preturns_qty[$key])){ $opening_preturns_qty[$key] = $opening_preturns_qty[$key] + $row['quantity']; } else{ $opening_preturns_qty[$key] = $row['quantity']; }
                                if(!empty($opening_preturns_amt[$key])){ $opening_preturns_amt[$key] = $opening_preturns_amt[$key] + $row['amount']; } else{ $opening_preturns_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_preturns_qty = $opening_preturns_amt = array(); }
                    }
                    
                    $opening_stkadj_add_qty = $opening_stkadj_add_amt = array();
                    $opening_stkadj_deduct_qty = $opening_stkadj_deduct_amt = array();
                    if($count51 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_adjustment` WHERE `date` < '$fdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `a_type` = 'add' AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_stkadj_add_qty[$key])){ $opening_stkadj_add_qty[$key] = $opening_stkadj_add_qty[$key] + $row['quantity']; } else{ $opening_stkadj_add_qty[$key] = $row['quantity']; }
                                if(!empty($opening_stkadj_add_amt[$key])){ $opening_stkadj_add_amt[$key] = $opening_stkadj_add_amt[$key] + $row['amount']; } else{ $opening_stkadj_add_amt[$key] = $row['amount']; }
                            }
                        }
                    
                        else{ $opening_stkadj_add_qty = $opening_stkadj_add_amt = array(); }
                    }
                    if($count51 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_adjustment` WHERE `date` < '$fdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `a_type` = 'deduct' AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_stkadj_deduct_qty[$key])){ $opening_stkadj_deduct_qty[$key] = $opening_stkadj_deduct_qty[$key] + $row['quantity']; } else{ $opening_stkadj_deduct_qty[$key] = $row['quantity']; }
                                if(!empty($opening_stkadj_deduct_amt[$key])){ $opening_stkadj_deduct_amt[$key] = $opening_stkadj_deduct_amt[$key] + $row['amount']; } else{ $opening_stkadj_deduct_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_stkadj_deduct_qty = $opening_stkadj_deduct_amt = array(); }
                    }
                    $opening_stk_rcvd_qty = $opening_stk_rcvd_amt = array();
                    if($count53 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_intermediate_received` WHERE `date` < '$fdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_stk_rcvd_qty[$key])){ $opening_stk_rcvd_qty[$key] = $opening_stk_rcvd_qty[$key] + $row['quantity']; } else{ $opening_stk_rcvd_qty[$key] = $row['quantity']; }
                                if(!empty($opening_stk_rcvd_amt[$key])){ $opening_stk_rcvd_amt[$key] = $opening_stk_rcvd_amt[$key] + $row['amount']; } else{ $opening_stk_rcvd_amt[$key] = $row['amount']; }
                            }
                        }
                    
                        else{ $opening_stk_rcvd_qty = $opening_stk_rcvd_amt = array(); }
                    }
                    
                    $opening_stk_isud_qty = $opening_stk_isud_amt = array();
                    if($count52 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_intermediate_issued` WHERE `date` < '$fdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_stk_isud_qty[$key])){ $opening_stk_isud_qty[$key] = $opening_stk_isud_qty[$key] + $row['quantity']; } else{ $opening_stk_isud_qty[$key] = $row['quantity']; }
                                if(!empty($opening_stk_isud_amt[$key])){ $opening_stk_isud_amt[$key] = $opening_stk_isud_amt[$key] + $row['amount']; } else{ $opening_stk_isud_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_stk_isud_qty = $opening_stk_isud_amt = array(); }
                    }
                    if($count91 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as icode,date FROM `item_stocktransfers` WHERE `date` < '$fdate'".$trasin_item_filter."".$trasin_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,code ORDER BY `code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_to_sector_qty[$key])){ $opening_to_sector_qty[$key] = $opening_to_sector_qty[$key] + $row['quantity']; } else{ $opening_to_sector_qty[$key] = $row['quantity']; }
                                if(!empty($opening_to_sector_amt[$key])){ $opening_to_sector_amt[$key] = $opening_to_sector_amt[$key] + $row['amount']; } else{ $opening_to_sector_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_to_sector_qty = $opening_to_sector_amt = array(); }
                    }
                    if($count91 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as icode,date FROM `item_stocktransfers` WHERE `date` < '$fdate'".$trasout_item_filter."".$trasout_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,code ORDER BY `code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_from_sector_qty[$key])){ $opening_from_sector_qty[$key] = $opening_from_sector_qty[$key] + $row['quantity']; } else{ $opening_from_sector_qty[$key] = $row['quantity']; }
                                if(!empty($opening_from_sector_amt[$key])){ $opening_from_sector_amt[$key] = $opening_from_sector_amt[$key] + $row['amount']; } else{ $opening_from_sector_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_from_sector_qty = $opening_from_sector_amt = array(); }
                    }
                    if($count65 > 0){
                        $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as amount, icode,date FROM `broiler_sales` WHERE `date` < '$fdate'".$pursal_item_filter."".$pursal_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,icode ORDER BY `icode` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_sales_qty[$key])){ $opening_sales_qty[$key] = $opening_sales_qty[$key] + $row['rcd_qty'] + $row['fre_qty']; } else{ $opening_sales_qty[$key] = $row['rcd_qty'] + $row['fre_qty']; }
                                if(!empty($opening_sales_amt[$key])){ $opening_sales_amt[$key] = $opening_sales_amt[$key] + $row['amount']; } else{ $opening_sales_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_sales_qty = $opening_sales_amt = array(); }
                    }
                    if($count54 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,itemcode as icode,date FROM `broiler_itemreturns` WHERE `date` < '$fdate'".$return_item_filter."".$pursal_sector_filter." AND `type` IN ('Customer') AND `stk_status` NOT LIKE '%wast%' AND `active` = '1' AND `dflag` = '0' GROUP BY date,itemcode ORDER BY `itemcode` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_sreturns_qty[$key])){ $opening_sreturns_qty[$key] = $opening_sreturns_qty[$key] + $row['quantity']; } else{ $opening_sreturns_qty[$key] = $row['quantity']; }
                                if(!empty($opening_sreturns_amt[$key])){ $opening_sreturns_amt[$key] = $opening_sreturns_amt[$key] + $row['amount']; } else{ $opening_sreturns_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_sreturns_qty = $opening_sreturns_amt = array(); }
                    }
                    if($count18 > 0){
                        $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `date` < '$fdate'".$consume_sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key1 = $row['item_code1']."@".$row['date'];
                                $key2 = $row['item_code2']."@".$row['date'];
                                if(!empty($opening_dentry_qty[$key1])){ $opening_dentry_qty[$key1] = $opening_dentry_qty[$key1] + $row['kgs1']; } else{ $opening_dentry_qty[$key1] = $row['kgs1']; }
                                if(!empty($opening_dentry_qty[$key2])){ $opening_dentry_qty[$key2] = $opening_dentry_qty[$key2] + $row['kgs2']; } else{ $opening_dentry_qty[$key2] = $row['kgs2']; }
                            }
                        }
                        else{ $opening_dentry_qty = array(); }
                    }
                    if($count57 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,item_code as icode,date FROM `broiler_medicine_record` WHERE `date` < '$fdate'".$consume_item_filter."".$consume_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_medvac_qty[$key])){ $opening_medvac_qty[$key] = $opening_medvac_qty[$key] + $row['quantity']; } else{ $opening_medvac_qty[$key] = $row['quantity']; }
                            }
                        }
                        else{ $opening_medvac_qty = array(); }
                    }
                    if($count30 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_feed_consumed` WHERE `date` < '$fdate'".$fmconsume_item_filter."".$conprod_feedmill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_fmconsume_qty[$key])){ $opening_fmconsume_qty[$key] = $opening_fmconsume_qty[$key] + $row['quantity']; } else{ $opening_fmconsume_qty[$key] = $row['quantity']; }
                                if(!empty($opening_fmconsume_amt[$key])){ $opening_fmconsume_amt[$key] = $opening_fmconsume_amt[$key] + $row['amount']; } else{ $opening_fmconsume_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_fmconsume_qty = $opening_fmconsume_amt = array(); }
                    }
                    if($count33 > 0){
                        $sql_record = "SELECT SUM(produced_quantity) as quantity,SUM(produced_amount) as amount,feed_code as icode,date FROM `broiler_feed_production` WHERE `date` < '$fdate'".$fmproduce_item_filter."".$conprod_feedmill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,feed_code ORDER BY `feed_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_fmproduce_qty[$key])){ $opening_fmproduce_qty[$key] = $opening_fmproduce_qty[$key] + $row['quantity']; } else{ $opening_fmproduce_qty[$key] = $row['quantity']; }
                                if(!empty($opening_fmproduce_amt[$key])){ $opening_fmproduce_amt[$key] = $opening_fmproduce_amt[$key] + $row['amount']; } else{ $opening_fmproduce_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_fmproduce_qty = $opening_fmproduce_amt = array(); }
                    }
                    if($count33 > 0){
                        $sql_record = "SELECT SUM(no_of_bags_feed) as quantity,SUM(bag_amount) as amount,bag_code_feed as icode,date FROM `broiler_feed_production` WHERE `date` < '$fdate'".$fmconsume_bagitem_filter."".$conprod_feedmill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,bag_code_feed ORDER BY `bag_code_feed` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_fmconsume_qty[$key])){ $opening_fmconsume_qty[$key] = $opening_fmconsume_qty[$key] + $row['quantity']; } else{ $opening_fmconsume_qty[$key] = $row['quantity']; }
                                if(!empty($opening_fmconsume_amt[$key])){ $opening_fmconsume_amt[$key] = $opening_fmconsume_amt[$key] + $row['amount']; } else{ $opening_fmconsume_amt[$key] = $row['amount']; }
                                //echo "<br/>".$opening_fmconsume_qty[$key];
                            }
                        }
                        else{ }
                    }
                    if($count45 > 0){
                        $sql_record = "SELECT SUM(saleable_chicks) as quantity,SUM(avg_chick_amount) as amount,hatch_date as date FROM `broiler_hatchentry` WHERE `hatch_date` < '$fdate'".$hprod_item_filter."".$hprod_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `hatch_date` ORDER BY `date` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $chick_code."@".$row['date']; $active_items[$chick_code] = $chick_code;
                                if(!empty($start_date[$chick_code])){ if(strtotime($row['date']) <= $start_date[$chick_code]){ $start_date[$chick_code] = strtotime($row['date']); } } else{ $start_date[$chick_code] = strtotime($row['date']); }
                                if(!empty($end_date[$chick_code])){ if(strtotime($row['date']) >= $end_date[$chick_code]){ $end_date[$chick_code] = strtotime($row['date']); } } else{ $end_date[$chick_code] = strtotime($row['date']); }
                                if(!empty($opening_hprod_qty[$key])){ $opening_hprod_qty[$key] = $opening_hprod_qty[$key] + $row['quantity']; } else{ $opening_hprod_qty[$key] = $row['quantity']; }
                                if(!empty($opening_hprod_amt[$key])){ $opening_hprod_amt[$key] = $opening_hprod_amt[$key] + $row['amount']; } else{ $opening_hprod_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $opening_hprod_qty = $opening_hprod_amt = array(); }
                    }
                    
                    if($count66 > 0){
                        $sql_record = "SELECT SUM(total_eggs) as quantity,SUM(avg_amount) as amount,setting_date as date,item_code as icode FROM `broiler_tray_settings` WHERE `setting_date` < '$fdate'".$trayset_item_filter."".$trayset_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY setting_date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($opening_trayset_qty[$key])){ $opening_trayset_qty[$key] = $opening_trayset_qty[$key] + $row['quantity']; } else{ $opening_trayset_qty[$key] = $row['quantity']; }
                                if(!empty($opening_trayset_amt[$key])){ $opening_trayset_amt[$key] = $opening_trayset_amt[$key] + $row['amount']; } else{ $opening_trayset_amt[$key] = $row['amount']; }
                                //echo "<br/>".$opening_trayset_qty[$key];
                            }
                        }
                        else{ }
                    }
                    
                    //Between Days Transactions
                    $between_stock_qty = $between_stock_amt = $between_sales_qty = $between_sales_amt = $between_sreturns_qty = $between_sreturns_amt = $between_purchases_qty = $between_purchases_amt = 
                    $between_preturns_qty  = $between_preturns_amt = $between_to_sector_qty = $between_to_sector_amt = $between_from_sector_qty = $between_from_sector_amt = $between_medvac_qty = $between_dentry_qty = 
                    $between_fmproduce_qty = $between_fmproduce_amt = $between_fmconsume_qty = $between_fmconsume_amt = $between_trayset_qty = $between_trayset_amt = array();
                    if($count58 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,type_code as icode,date FROM `broiler_openings` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$opening_item_filter."".$opening_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,type_code ORDER BY `type_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_stock_qty[$key])){ $between_stock_qty[$key] = $between_stock_qty[$key] + $row['quantity']; } else{ $between_stock_qty[$key] = $row['quantity']; }
                                if(!empty($between_stock_amt[$key])){ $between_stock_amt[$key] = $between_stock_amt[$key] + $row['amount']; } else{ $between_stock_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_stock_qty = $between_stock_amt = array(); }
                    }
                    $between_purchases_qty = $between_purchases_amt = $grn_trnums = array(); $grn_fltr = ""; 
                    $sql = "SELECT * FROM `broiler_pc_goodsreceipt` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$grn_item_filter."".$grn_sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`item_code` ASC";
                    $query = mysqli_query($conn,$sql); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['item_code']."@".$row['date']; $active_items[$row['item_code']] = $row['item_code'];
                            if(!empty($start_date[$row['item_code']])){ if(strtotime($row['date']) <= $start_date[$row['item_code']]){ $start_date[$row['item_code']] = strtotime($row['date']); } } else{ $start_date[$row['item_code']] = strtotime($row['date']); }
                            if(!empty($end_date[$row['item_code']])){ if(strtotime($row['date']) >= $end_date[$row['item_code']]){ $end_date[$row['item_code']] = strtotime($row['date']); } } else{ $end_date[$row['item_code']] = strtotime($row['date']); }
                            if(!empty($between_purchases_qty[$key])){ $between_purchases_qty[$key] = $between_purchases_qty[$key] + $row['rcvd_qty']; } else{ $between_purchases_qty[$key] = $row['rcvd_qty']; }
                            if(!empty($between_purchases_amt[$key])){ $between_purchases_amt[$key] = $between_purchases_amt[$key] + $row['item_amt']; } else{ $between_purchases_amt[$key] = $row['item_amt']; }
                            $grn_trnums[$row['trnum']] = $row['trnum'];
                        }
                    }
                    if(sizeof($grn_trnums) > 0){
                        $grtrno_list = "";
                        $grtrno_list = implode("','",$grn_trnums);
                        $grn_fltr = " AND `gr_trnum` NOT IN ('$grtrno_list')";
                    }

                    if($count61 > 0){
                        $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pursal_item_filter."".$pursal_sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                if(empty($grn_trnums[$row['gr_trnum']]) || $grn_trnums[$row['gr_trnum']] == ""){
                                    $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                    if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                    if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                    if(!empty($between_purchases_qty[$key])){ $between_purchases_qty[$key] = $between_purchases_qty[$key] + $row['rcd_qty'] + $row['fre_qty']; } else{ $between_purchases_qty[$key] = $row['rcd_qty'] + $row['fre_qty']; }
                                    if(!empty($between_purchases_amt[$key])){ $between_purchases_amt[$key] = $between_purchases_amt[$key] + $row['item_tamt']; } else{ $between_purchases_amt[$key] = $row['item_tamt']; }
                                }
                            }
                        }
                    }
                    if($count54 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,itemcode as icode,date FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$return_item_filter."".$pursal_sector_filter." AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' GROUP BY date,itemcode ORDER BY `itemcode` ASC"; // AND `stk_status` NOT LIKE '%wast%'
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_preturns_qty[$key])){ $between_preturns_qty[$key] = $between_preturns_qty[$key] + $row['quantity']; } else{ $between_preturns_qty[$key] = $row['quantity']; }
                                if(!empty($between_preturns_amt[$key])){ $between_preturns_amt[$key] = $between_preturns_amt[$key] + $row['amount']; } else{ $between_preturns_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_preturns_qty = $between_preturns_amt = array(); }
                    }
                    $between_stkadj_add_qty = $between_stkadj_add_amt = array();
                    $between_stkadj_deduct_qty = $between_stkadj_deduct_amt = array();
                    if($count51 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_adjustment` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `a_type` = 'add' AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_stkadj_add_qty[$key])){ $between_stkadj_add_qty[$key] = $between_stkadj_add_qty[$key] + $row['quantity']; } else{ $between_stkadj_add_qty[$key] = $row['quantity']; }
                                if(!empty($between_stkadj_add_amt[$key])){ $between_stkadj_add_amt[$key] = $between_stkadj_add_amt[$key] + $row['amount']; } else{ $between_stkadj_add_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_stkadj_add_qty = $between_stkadj_add_amt = array(); }

                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_adjustment` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `a_type` = 'deduct' AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_stkadj_deduct_qty[$key])){ $between_stkadj_deduct_qty[$key] = $between_stkadj_deduct_qty[$key] + $row['quantity']; } else{ $between_stkadj_deduct_qty[$key] = $row['quantity']; }
                                if(!empty($between_stkadj_deduct_amt[$key])){ $between_stkadj_deduct_amt[$key] = $between_stkadj_deduct_amt[$key] + $row['amount']; } else{ $between_stkadj_deduct_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_stkadj_deduct_qty = $between_stkadj_deduct_amt = array(); }
                    }
                    $between_stk_rcvd_qty = $between_stk_rcvd_amt = array();
                    if($count53 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_intermediate_received` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_stk_rcvd_qty[$key])){ $between_stk_rcvd_qty[$key] = $between_stk_rcvd_qty[$key] + $row['quantity']; } else{ $between_stk_rcvd_qty[$key] = $row['quantity']; }
                                if(!empty($between_stk_rcvd_amt[$key])){ $between_stk_rcvd_amt[$key] = $between_stk_rcvd_amt[$key] + $row['amount']; } else{ $between_stk_rcvd_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_stk_rcvd_qty = $between_stk_rcvd_amt = array(); }
                    }
                    $between_stk_isud_qty = $between_stk_isud_amt = array();
                    if($count52 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_inv_intermediate_issued` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$stkadjust_item_filter."".$stkadjust_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_stk_isud_qty[$key])){ $between_stk_isud_qty[$key] = $between_stk_isud_qty[$key] + $row['quantity']; } else{ $between_stk_isud_qty[$key] = $row['quantity']; }
                                if(!empty($between_stk_isud_amt[$key])){ $between_stk_isud_amt[$key] = $between_stk_isud_amt[$key] + $row['amount']; } else{ $between_stk_isud_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_stk_isud_qty = $between_stk_isud_amt = array(); }
                    }
                    if($count91 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as icode,date FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$trasin_item_filter."".$trasin_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,code ORDER BY `code` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_to_sector_qty[$key])){ $between_to_sector_qty[$key] = $between_to_sector_qty[$key] + $row['quantity']; } else{ $between_to_sector_qty[$key] = $row['quantity']; }
                                if(!empty($between_to_sector_amt[$key])){ $between_to_sector_amt[$key] = $between_to_sector_amt[$key] + $row['amount']; } else{ $between_to_sector_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_to_sector_qty = $between_to_sector_amt = array(); }

                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,code as icode,date FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$trasin_item_filter."".$trasout_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,code ORDER BY `code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_from_sector_qty[$key])){ $between_from_sector_qty[$key] = $between_from_sector_qty[$key] + $row['quantity']; } else{ $between_from_sector_qty[$key] = $row['quantity']; }
                                if(!empty($between_from_sector_amt[$key])){ $between_from_sector_amt[$key] = $between_from_sector_amt[$key] + $row['amount']; } else{ $between_from_sector_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_from_sector_qty = $between_from_sector_amt = array(); }
                    }
                    if($count65 > 0){
                        $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as amount,icode,date FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pursal_item_filter."".$pursal_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,icode ORDER BY `icode` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_sales_qty[$key])){ $between_sales_qty[$key] = $between_sales_qty[$key] + $row['rcd_qty'] + $row['fre_qty']; } else{ $between_sales_qty[$key] = $row['rcd_qty'] + $row['fre_qty']; }
                                if(!empty($between_sales_amt[$key])){ $between_sales_amt[$key] = $between_sales_amt[$key] + $row['amount']; } else{ $between_sales_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_sales_qty = $between_sales_amt = array(); }
                    }
                    if($count54 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,itemcode as icode,date FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$return_item_filter."".$pursal_sector_filter." AND `type` IN ('Customer') AND `stk_status` NOT LIKE '%wast%' AND `active` = '1' AND `dflag` = '0' GROUP BY date,itemcode ORDER BY `itemcode` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_sreturns_qty[$key])){ $between_sreturns_qty[$key] = $between_sreturns_qty[$key] + $row['quantity']; } else{ $between_sreturns_qty[$key] = $row['quantity']; }
                                if(!empty($between_sreturns_amt[$key])){ $between_sreturns_amt[$key] = $between_sreturns_amt[$key] + $row['amount']; } else{ $between_sreturns_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_sreturns_qty = $between_sreturns_amt = array(); }
                    }
                    if($count18 > 0){
                        $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$consume_sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key1 = $row['item_code1']."@".$row['date'];
                                $key2 = $row['item_code2']."@".$row['date'];
                                if(!empty($between_dentry_qty[$key])){ $between_dentry_qty[$key] = $between_dentry_qty[$key] + $row['kgs1']; } else{ $between_dentry_qty[$key] = $row['kgs1']; }
                                if(!empty($between_dentry_qty[$key2])){ $between_dentry_qty[$key2] = $between_dentry_qty[$key2] + $row['kgs2']; } else{ $between_dentry_qty[$key2] = $row['kgs2']; }
                            }
                        }
                        else{ $between_dentry_qty = array(); }
                    }
                    if($count57 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,item_code as icode,date FROM `broiler_medicine_record` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$consume_item_filter."".$consume_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_medvac_qty[$key])){ $between_medvac_qty[$key] = $between_medvac_qty[$key] + $row['quantity']; } else{ $between_medvac_qty[$key] = $row['quantity']; }
                            }
                        }
                        else{ $between_medvac_qty = array(); }
                    }
                    if($count30 > 0){
                        $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,item_code as icode,date FROM `broiler_feed_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$fmconsume_item_filter."".$conprod_feedmill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_fmconsume_qty[$key])){ $between_fmconsume_qty[$key] = $between_fmconsume_qty[$key] + $row['quantity']; } else{ $between_fmconsume_qty[$key] = $row['quantity']; }
                                if(!empty($between_fmconsume_amt[$key])){ $between_fmconsume_amt[$key] = $between_fmconsume_amt[$key] + $row['amount']; } else{ $between_fmconsume_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_fmconsume_qty = $between_fmconsume_amt = array(); }
                    }
                    if($count33 > 0){
                        $sql_record = "SELECT SUM(produced_quantity) as quantity,SUM(produced_amount) as amount,feed_code as icode,date FROM `broiler_feed_production` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$fmproduce_item_filter."".$conprod_feedmill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,feed_code ORDER BY `feed_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_fmproduce_qty[$key])){ $between_fmproduce_qty[$key] = $between_fmproduce_qty[$key] + $row['quantity']; } else{ $between_fmproduce_qty[$key] = $row['quantity']; }
                                if(!empty($between_fmproduce_amt[$key])){ $between_fmproduce_amt[$key] = $between_fmproduce_amt[$key] + $row['amount']; } else{ $between_fmproduce_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_fmproduce_qty = $between_fmproduce_amt = array(); }
                        
                        $sql_record = "SELECT SUM(no_of_bags_feed) as quantity,SUM(bag_amount) as amount,bag_code_feed as icode,date FROM `broiler_feed_production` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$fmconsume_bagitem_filter."".$conprod_feedmill_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY date,bag_code_feed ORDER BY `bag_code_feed` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_fmconsume_qty[$key])){ $between_fmconsume_qty[$key] = $between_fmconsume_qty[$key] + $row['quantity']; } else{ $between_fmconsume_qty[$key] = $row['quantity']; }
                                if(!empty($between_fmconsume_amt[$key])){ $between_fmconsume_amt[$key] = $between_fmconsume_amt[$key] + $row['amount']; } else{ $between_fmconsume_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ }
                    }
                    if($count45 > 0){
                        $sql_record = "SELECT SUM(saleable_chicks) as quantity,SUM(avg_chick_amount) as amount,hatch_date as date FROM `broiler_hatchentry` WHERE `hatch_date` >= '$fdate' AND `hatch_date` <= '$tdate'".$hprod_item_filter."".$hprod_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `hatch_date` ORDER BY `date` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $chick_code."@".$row['date']; $active_items[$chick_code] = $chick_code;
                                if(!empty($start_date[$chick_code])){ if(strtotime($row['date']) <= $start_date[$chick_code]){ $start_date[$chick_code] = strtotime($row['date']); } } else{ $start_date[$chick_code] = strtotime($row['date']); }
                                if(!empty($end_date[$chick_code])){ if(strtotime($row['date']) >= $end_date[$chick_code]){ $end_date[$chick_code] = strtotime($row['date']); } } else{ $end_date[$chick_code] = strtotime($row['date']); }
                                if(!empty($between_hprod_qty[$key])){ $between_hprod_qty[$key] = $between_hprod_qty[$key] + $row['quantity']; } else{ $between_hprod_qty[$key] = $row['quantity']; }
                                if(!empty($between_hprod_amt[$key])){ $between_hprod_amt[$key] = $between_hprod_amt[$key] + $row['amount']; } else{ $between_hprod_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ $between_hprod_qty = $between_hprod_amt = array(); }
                    }
                    
                    if($count66 > 0){
                        $sql_record = "SELECT SUM(total_eggs) as quantity,SUM(avg_amount) as amount,setting_date as date,item_code as icode FROM `broiler_tray_settings` WHERE `setting_date` >= '$fdate' AND `setting_date` <= '$tdate'".$trayset_item_filter."".$trayset_sector_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY setting_date,item_code ORDER BY `item_code` ASC";
                        $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                        if($transaction_count > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['icode']."@".$row['date']; $active_items[$row['icode']] = $row['icode'];
                                if(!empty($start_date[$row['icode']])){ if(strtotime($row['date']) <= $start_date[$row['icode']]){ $start_date[$row['icode']] = strtotime($row['date']); } } else{ $start_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($end_date[$row['icode']])){ if(strtotime($row['date']) >= $end_date[$row['icode']]){ $end_date[$row['icode']] = strtotime($row['date']); } } else{ $end_date[$row['icode']] = strtotime($row['date']); }
                                if(!empty($between_trayset_qty[$key])){ $between_trayset_qty[$key] = $between_trayset_qty[$key] + $row['quantity']; } else{ $between_trayset_qty[$key] = $row['quantity']; }
                                if(!empty($between_trayset_amt[$key])){ $between_trayset_amt[$key] = $between_trayset_amt[$key] + $row['amount']; } else{ $between_trayset_amt[$key] = $row['amount']; }
                            }
                        }
                        else{ }
                    }
                    
                    $aitem_list = ""; foreach($active_items as $aitems){ if($aitem_list == ""){ $aitem_list = $aitems; } else{ $aitem_list = $aitem_list."','".$aitems; }}
                    
                    $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$aitem_list') AND `category` IN ('$icat_fglist')".$trasin_item_filter." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $aitem_code = array();
                    while($row = mysqli_fetch_assoc($query)){ $aitem_code[$row['code']] = $row['code']; }

                    //echo "<br/>".sizeof($aitem_code);
                    $open_added_item_quantity = $open_deducted_item_quantity = $open_item_amounts = $opening_item_prices = $final_opening_stock = $final_opening_prices = $final_opening_amounts = 
                    $total_purin_qty = $total_purin_amount = $total_consumed_qty = $total_consumed_amount = $total_sold_qty = $total_sold_amount = $total_salereturn_qty = $total_salereturn_amount = array();
                    $final_in_quantity = $final_in_amounts = $final_consumed_quantity = $final_consumed_amounts = $final_sold_quantity = $final_sold_amounts = $final_salereturn_quantity = 
                    $final_salereturn_amounts = $final_purreturn_quantity = $final_purreturn_amounts = $final_closing_stock = $final_closing_amounts = $final_closing_prices = $final_purin_qty = $final_purin_amt = array();
                    foreach($aitem_code as $ail){
                        $final_opening_stock[$ail] = $final_opening_prices[$ail] = $final_opening_amounts[$ail] = $total_purin_qty[$ail] = 
                        $avg_stock_qty = $avg_stock_amt = $avg_stock_prc = $tot_ded_qty = $tot_ded_amt = 0;
                        for($currentDate = ((int)$start_date[$ail]);$currentDate <= ((int)$end_date[$ail]);$currentDate += (86400)){
                            $akey = ""; $akey = $ail."@".date("Y-m-d",$currentDate); $ikey = ""; $ikey = $ail;
                            
                            if($currentDate < strtotime($fdate)){
                                if(!empty($opening_stock_qty[$akey])){ } else{ $opening_stock_qty[$akey] = 0; }
                                if(!empty($opening_purchases_qty[$akey])){ } else{ $opening_purchases_qty[$akey] = 0; }
                                if(!empty($opening_to_sector_qty[$akey])){ } else{ $opening_to_sector_qty[$akey] = 0; }
                                if(!empty($opening_fmproduce_qty[$akey])){ } else{ $opening_fmproduce_qty[$akey] = 0; }
                                if(!empty($opening_sreturns_qty[$akey])){ } else{ $opening_sreturns_qty[$akey] = 0; }
                                if(!empty($opening_stkadj_add_qty[$akey])){ } else{ $opening_stkadj_add_qty[$akey] = 0; }
                                if(!empty($opening_hprod_qty[$akey])){ } else{ $opening_hprod_qty[$akey] = 0; }
                                if(!empty($opening_stk_rcvd_qty[$akey])){ } else{ $opening_stk_rcvd_qty[$akey] = 0; }

                                $avg_stock_qty += ((float)$opening_stock_qty[$akey] + (float)$opening_purchases_qty[$akey] + (float)$opening_hprod_qty[$akey] + (float)$opening_to_sector_qty[$akey] + (float)$opening_fmproduce_qty[$akey] + (float)$opening_sreturns_qty[$akey] + (float)$opening_stkadj_add_qty[$akey] + (float)$opening_stk_rcvd_qty[$akey]);
                                
                                if(!empty($opening_stock_amt[$akey])){ } else{ $opening_stock_amt[$akey] = 0; }
                                if(!empty($opening_purchases_amt[$akey])){ } else{ $opening_purchases_amt[$akey] = 0; }
                                if(!empty($opening_to_sector_amt[$akey])){ } else{ $opening_to_sector_amt[$akey] = 0; }
                                if(!empty($opening_fmproduce_amt[$akey])){ } else{ $opening_fmproduce_amt[$akey] = 0; }
                                if(!empty($opening_sreturns_amt[$akey])){ } else{ $opening_sreturns_amt[$akey] = 0; }
                                if(!empty($opening_stkadj_add_amt[$akey])){ } else{ $opening_stkadj_add_amt[$akey] = 0; }
                                if(!empty($opening_stk_rcvd_amt[$akey])){ } else{ $opening_stk_rcvd_amt[$akey] = 0; }

                                $avg_stock_amt += ((float)$opening_stock_amt[$akey] + (float)$opening_purchases_amt[$akey] + (float)$opening_hprod_amt[$akey] + (float)$opening_to_sector_amt[$akey] + (float)$opening_fmproduce_amt[$akey] + (float)$opening_sreturns_amt[$akey] + (float)$opening_stkadj_add_amt[$akey] + (float)$opening_stk_rcvd_amt[$akey]);
                                
                                if($avg_stock_amt == "" || $avg_stock_amt == "0" || $avg_stock_amt == NULL || $avg_stock_amt == "0.00" || $avg_stock_amt == "NAN" || $avg_stock_amt == 0){ $avg_stock_amt = 0; }
                                if($avg_stock_qty == "" || $avg_stock_qty == "0" || $avg_stock_qty == NULL || $avg_stock_qty == "0.00" || $avg_stock_qty == "NAN" || $avg_stock_qty == 0){ $avg_stock_qty = 0; }
                                
                                if($avg_stock_amt == 0 || $avg_stock_qty == 0){
                                    $avg_stock_prc = 0;
                                }
                                else{
                                    if((float)$avg_stock_qty != 0){
                                        $avg_stock_prc = (float)$avg_stock_amt / (float)$avg_stock_qty;
                                    }
                                    else{
                                        $avg_stock_prc = 0;
                                    }
                                    
                                }

                                if(!empty($opening_dentry_qty[$akey])){ } else{ $opening_dentry_qty[$akey] = 0; }
                                if(!empty($opening_medvac_qty[$akey])){ } else{ $opening_medvac_qty[$akey] = 0; }
                                if(!empty($opening_fmconsume_qty[$akey])){ } else{ $opening_fmconsume_qty[$akey] = 0; }
                                if(!empty($opening_trayset_qty[$akey])){ } else{ $opening_trayset_qty[$akey] = 0; }
                                if(!empty($opening_sales_qty[$akey])){ } else{ $opening_sales_qty[$akey] = 0; }
                                if(!empty($opening_preturns_qty[$akey])){ } else{ $opening_preturns_qty[$akey] = 0; }
                                if(!empty($opening_from_sector_qty[$akey])){ } else{ $opening_from_sector_qty[$akey] = 0; }
                                if(!empty($opening_stkadj_deduct_qty[$akey])){ } else{ $opening_stkadj_deduct_qty[$akey] = 0; }
                                if(!empty($opening_stk_isud_qty[$akey])){ } else{ $opening_stk_isud_qty[$akey] = 0; }

                                $tot_ded_qty = ((float)$opening_dentry_qty[$akey] + (float)$opening_medvac_qty[$akey] + (float)$opening_fmconsume_qty[$akey] + (float)$opening_trayset_qty[$akey] + (float)$opening_sales_qty[$akey] + (float)$opening_preturns_qty[$akey] + (float)$opening_from_sector_qty[$akey] + (float)$opening_stkadj_deduct_qty[$akey] + (float)$opening_stk_isud_qty[$akey]);
                                //echo "<br/>$tot_ded_qty = ((float)$opening_dentry_qty[$akey] + (float)$opening_medvac_qty[$akey] + (float)$opening_fmconsume_qty[$akey] + (float)$opening_trayset_qty[$akey] + (float)$opening_sales_qty[$akey] + (float)$opening_preturns_qty[$akey] + (float)$opening_from_sector_qty[$akey] + (float)$opening_stkadj_deduct_qty[$akey] + (float)$opening_stk_isud_qty[$akey])";
                                
                                if($avg_stock_prc == "" || $avg_stock_prc == "0" || $avg_stock_prc == NULL || $avg_stock_prc == "0.00" || $avg_stock_prc == "NAN" || $avg_stock_prc == 0){ $avg_stock_prc = 0; }
                                if($tot_ded_qty == "" || $tot_ded_qty == "0" || $tot_ded_qty == NULL || $tot_ded_qty == "0.00" || $tot_ded_qty == "NAN" || $tot_ded_qty == 0){ $tot_ded_qty = 0; }
                                
                                if($avg_stock_prc == 0 || $tot_ded_qty == 0){
                                    $tot_ded_amt = 0;
                                }
                                else{
                                    $tot_ded_amt = (float)$avg_stock_prc * (float)$tot_ded_qty;
                                }
                                
                                $avg_stock_qty = (float)round($avg_stock_qty,10) - (float)$tot_ded_qty;
                                
                                $avg_stock_amt = (float)$avg_stock_amt - (float)$tot_ded_amt;

                                //Final Opening Details
                                $final_opening_stock[$ikey] = (float)$avg_stock_qty;
                                $final_opening_amounts[$ikey] = (float)$avg_stock_amt;
                            }
                            else if($currentDate >= strtotime($fdate) && $currentDate <= strtotime($tdate)){
                                
                                if(!empty($between_stock_qty[$akey])){ } else{ $between_stock_qty[$akey] = 0; }
                                if(!empty($between_purchases_qty[$akey])){ } else{ $between_purchases_qty[$akey] = 0; }
                                if(!empty($between_hprod_qty[$akey])){ } else{ $between_hprod_qty[$akey] = 0; }
                                if(!empty($between_to_sector_qty[$akey])){ } else{ $between_to_sector_qty[$akey] = 0; }
                                if(!empty($between_fmproduce_qty[$akey])){ } else{ $between_fmproduce_qty[$akey] = 0; }
                                if(!empty($between_sreturns_qty[$akey])){ } else{ $between_sreturns_qty[$akey] = 0; }
                                if(!empty($between_stkadj_add_qty[$akey])){ } else{ $between_stkadj_add_qty[$akey] = 0; }
                                if(!empty($between_stk_rcvd_qty[$akey])){ } else{ $between_stk_rcvd_qty[$akey] = 0; }

                                $avg_stock_qty = $avg_stock_qty + ((float)$between_stock_qty[$akey] + (float)$between_purchases_qty[$akey] + (float)$between_hprod_qty[$akey] + (float)$between_to_sector_qty[$akey] + (float)$between_fmproduce_qty[$akey] + (float)$between_sreturns_qty[$akey] + (float)$between_stkadj_add_qty[$akey] + (float)$between_stk_rcvd_qty[$akey]);
                                
                                if(!empty($between_stock_amt[$akey])){ } else{ $between_stock_amt[$akey] = 0; }
                                if(!empty($between_purchases_amt[$akey])){ } else{ $between_purchases_amt[$akey] = 0; }
                                if(!empty($between_hprod_amt[$akey])){ } else{ $between_hprod_amt[$akey] = 0; }
                                if(!empty($between_to_sector_amt[$akey])){ } else{ $between_to_sector_amt[$akey] = 0; }
                                if(!empty($between_fmproduce_amt[$akey])){ } else{ $between_fmproduce_amt[$akey] = 0; }
                                if(!empty($between_sreturns_amt[$akey])){ } else{ $between_sreturns_amt[$akey] = 0; }
                                if(!empty($between_stkadj_add_amt[$akey])){ } else{ $between_stkadj_add_amt[$akey] = 0; }
                                if(!empty($between_stk_rcvd_amt[$akey])){ } else{ $between_stk_rcvd_amt[$akey] = 0; }

                                $avg_stock_amt += ((float)$between_stock_amt[$akey] + (float)$between_purchases_amt[$akey] + (float)$between_hprod_amt[$akey] + (float)$between_to_sector_amt[$akey] + (float)$between_fmproduce_amt[$akey] + (float)$between_sreturns_amt[$akey] + (float)$between_stkadj_add_amt[$akey] + (float)$between_stk_rcvd_amt[$akey]);
                                
                                if($avg_stock_amt == "" || $avg_stock_amt == "0" || $avg_stock_amt == NULL || $avg_stock_amt == "0.00" || $avg_stock_amt == "NAN" || $avg_stock_amt == 0){ $avg_stock_amt = 0; }
                                if($avg_stock_qty == "" || $avg_stock_qty == "0" || $avg_stock_qty == NULL || $avg_stock_qty == "0.00" || $avg_stock_qty == "NAN" || $avg_stock_qty == 0){ $avg_stock_qty = 0; }
                                
                                if($avg_stock_amt == 0 || $avg_stock_qty == 0){
                                    $avg_stock_prc = 0;
                                }
                                else{
                                    if((float)$avg_stock_qty != 0){
                                        $avg_stock_prc = (float)$avg_stock_amt / (float)$avg_stock_qty;
                                    }
                                    else{
                                        $avg_stock_prc = 0;
                                    }
                                }
                                

                                if(!empty($between_dentry_qty[$akey])){ } else{ $between_dentry_qty[$akey] = 0; }
                                if(!empty($between_medvac_qty[$akey])){ } else{ $between_medvac_qty[$akey] = 0; }
                                if(!empty($between_fmconsume_qty[$akey])){ } else{ $between_fmconsume_qty[$akey] = 0; }
                                if(!empty($between_trayset_qty[$akey])){ } else{ $between_trayset_qty[$akey] = 0; }
                                if(!empty($between_sales_qty[$akey]) || $between_sales_qty[$akey] != ""){ } else{ $between_sales_qty[$akey] = 0; }
                                if(!empty($between_preturns_qty[$akey])){ } else{ $between_preturns_qty[$akey] = 0; }
                                if(!empty($between_preturns_amt[$akey])){ } else{ $between_preturns_amt[$akey] = 0; }
                                if(!empty($between_from_sector_qty[$akey])){ } else{ $between_from_sector_qty[$akey] = 0; }
                                if(!empty($between_stkadj_deduct_qty[$akey])){ } else{ $between_stkadj_deduct_qty[$akey] = 0; }
                                if(!empty($between_stk_isud_qty[$akey])){ } else{ $between_stk_isud_qty[$akey] = 0; }

                                $tot_ded_qty = ((float)$between_dentry_qty[$akey] + (float)$between_medvac_qty[$akey] + (float)$between_fmconsume_qty[$akey] + (float)$between_trayset_qty[$akey] + (float)$between_sales_qty[$akey] + (float)$between_preturns_qty[$akey] + (float)$between_from_sector_qty[$akey] + (float)$between_stkadj_deduct_qty[$akey] + (float)$between_stk_isud_qty[$akey]);
                                
                                if($avg_stock_amt == "" || $avg_stock_amt == "0" || $avg_stock_amt == NULL || $avg_stock_amt == "0.00" || $avg_stock_amt == "NAN" || $avg_stock_amt == 0){ $avg_stock_amt = 0; }
                                if($tot_ded_qty == "" || $tot_ded_qty == "0" || $tot_ded_qty == NULL || $tot_ded_qty == "0.00" || $tot_ded_qty == "NAN" || $tot_ded_qty == 0){ $tot_ded_qty = 0; }
                                
                                if($avg_stock_prc == 0 || $tot_ded_qty == 0){
                                    $tot_ded_amt = 0;
                                }
                                else{
                                    $tot_ded_amt = (float)$avg_stock_prc * (float)$tot_ded_qty;
                                }

                                
                                
                                $avg_stock_qty = (float)$avg_stock_qty - (float)$tot_ded_qty;
                                $avg_stock_amt = (float)$avg_stock_amt - (float)$tot_ded_amt;
                                
                                //Between Purchases
                                $final_purin_qty[$ikey] += (float)$between_purchases_qty[$akey];
                                $final_purin_amt[$ikey] += (float)$between_purchases_amt[$akey];

                                /*Opening + TransferIn + Feedmill Production + Stock Adj add Qty + Stock Received*/
                                $final_in_quantity[$ikey] += ((float)$between_stock_qty[$akey] + (float)$between_hprod_qty[$akey] + (float)$between_to_sector_qty[$akey] + (float)$between_fmproduce_qty[$akey] + (float)$between_stk_rcvd_qty[$akey] + (float)$between_stkadj_add_qty[$akey]);
                                //echo "<br/>$final_in_quantity[$ikey] += ((float)$between_stock_qty[$akey] + (float)$between_hprod_qty[$akey] + (float)$between_to_sector_qty[$akey] + (float)$between_fmproduce_qty[$akey] + (float)$between_stk_rcvd_qty[$akey] + (float)$between_stkadj_add_qty[$akey]);";
                                $final_in_amounts[$ikey] += ((float)$between_stock_amt[$akey] + (float)$between_hprod_amt[$akey] + (float)$between_to_sector_amt[$akey] + (float)$between_fmproduce_amt[$akey] + (float)$between_stk_rcvd_amt[$akey] + (float)$between_stkadj_add_amt[$akey]);

                                /*Daily Entry + Transfer Out + MedVac Entry + Feedmill Consumption + Stock Adj deduct Qty + Stock Issued*/
                                $cqty = ((float)$between_dentry_qty[$akey] + (float)$between_from_sector_qty[$akey] + (float)$between_medvac_qty[$akey] + (float)$between_fmconsume_qty[$akey] + (float)$between_trayset_qty[$akey] + (float)$between_stk_isud_qty[$akey] + (float)$between_stkadj_deduct_qty[$akey]);
                                $camt = $cqty * $avg_stock_prc;
                                $final_consumed_quantity[$ikey] += (float)$cqty;
                                $final_consumed_amounts[$ikey] += (float)$camt;

                                /*Sold*/
                                $final_sold_quantity[$ikey] += (float)$between_sales_qty[$akey];
                                $final_sold_amounts[$ikey] += ((float)$avg_stock_prc * (float)$between_sales_qty[$akey]);

                                /*Purchase Return */
                                $final_purreturn_quantity[$ikey] += (float)$between_preturns_qty[$akey];
                                //$final_purreturn_amounts[$ikey] += ((float)$avg_stock_prc * (float)$between_preturns_qty[$akey]);
                                $final_purreturn_amounts[$ikey] += ((float)$between_preturns_amt[$akey]);

                                /*Sales Return */
                                $final_salereturn_quantity[$ikey] += (float)$between_sreturns_qty[$akey];
                                $final_salereturn_amounts[$ikey] += ((float)$avg_stock_prc * (float)$between_sreturns_qty[$akey]);
                                /*
                                if(((float)$final_opening_stock[$ikey] + (float)$final_in_quantity[$ikey] + (float)$final_salereturn_quantity[$ikey]) == ((float)$final_consumed_quantity[$ikey] + (float)$final_sold_quantity[$ikey])){
                                    $avg_stock_qty = 0;
                                }*/
                            }
                            else{ }
                            //Final Closing Details
                            
                            $final_closing_stock[$ikey] = (float)$avg_stock_qty;
                            $final_closing_amounts[$ikey] = (float)$avg_stock_amt;
                            if(!empty($final_closing_stock[$ikey]) && $final_closing_stock[$ikey] != 0){
                                $final_closing_prices[$ikey] =  (float)$final_closing_amounts[$ikey] / ((float)$final_closing_stock[$ikey]);
                            }
                            else{
                                $final_closing_prices[$ikey] =  0;
                            }
                            
                        }
                        //echo "<br/>".$item_name[$ail]."-".$ail;
                    }
                    //echo "<br/>".sizeof($aitem_code);
                    foreach($aitem_code as $ikey){
                        if(number_format_ind($final_opening_stock[$ikey]) != "0.00" || number_format_ind($final_purin_qty[$ikey]) != "0.00" || number_format_ind($final_in_quantity[$ikey]) != "0.00" || number_format_ind($final_consumed_quantity[$ikey]) != "0.00" || number_format_ind($final_sold_quantity[$ikey]) != "0.00" || number_format_ind($final_salereturn_quantity[$ikey]) != "0.00" || number_format_ind($final_closing_stock[$ikey]) != "0.00"){
                            $total_opening_qty = $total_opening_qty + $final_opening_stock[$ikey]; if($total_opening_qty == ""){ $total_opening_qty = 0; }
                            $total_opening_amt = $total_opening_amt + $final_opening_amounts[$ikey]; if($total_opening_amt == ""){ $total_opening_amt = 0; }
                            $total_purtrin_qty = $total_purtrin_qty + $final_purin_qty[$ikey]; if($total_purtrin_qty == ""){ $total_purtrin_qty = 0; }
                            $total_stkin_qty = $total_stkin_qty + $final_in_quantity[$ikey]; if($total_stkin_qty == ""){ $total_stkin_qty = 0; }
                            $total_purtrin_amt = $total_purtrin_amt + $final_purin_amt[$ikey]; if($total_purtrin_amt == ""){ $total_purtrin_amt = 0; }
                            $total_stkin_amt = $total_stkin_amt + $final_in_amounts[$ikey]; if($total_stkin_amt == ""){ $total_stkin_amt = 0; }
                            $total_consumd_qty = $total_consumd_qty + $final_consumed_quantity[$ikey]; if($total_consumd_qty == ""){ $total_consumd_qty = 0; }
                            $total_consumd_amt = $total_consumd_amt + $final_consumed_amounts[$ikey]; if($total_consumd_amt == ""){ $total_consumd_amt = 0; }
                            $total_sales_qty = $total_sales_qty + $final_sold_quantity[$ikey]; if($total_sales_qty == ""){ $total_sales_qty = 0; }
                            $total_sales_amt = $total_sales_amt + $final_sold_amounts[$ikey]; if($total_sales_amt == ""){ $total_sales_amt = 0; }
                            $total_preturn_qty = $total_preturn_qty + $final_purreturn_quantity[$ikey]; if($total_preturn_qty == ""){ $total_preturn_qty = 0; }
                            $total_preturn_amt = $total_preturn_amt + $final_purreturn_amounts[$ikey]; if($total_preturn_amt == ""){ $total_preturn_amt = 0; }
                            $total_sreturn_qty = $total_sreturn_qty + $final_salereturn_quantity[$ikey]; if($total_sreturn_qty == ""){ $total_sreturn_qty = 0; }
                            $total_sreturn_amt = $total_sreturn_amt + $final_salereturn_amounts[$ikey]; if($total_sreturn_amt == ""){ $total_sreturn_amt = 0; }
                            echo "<tr>";
                            echo "<td title='Category'>".$icat_name[$item_category[$ikey]]."</td>";
                            echo "<td title='Item Code'>".$ikey."</td>";
                            echo "<td title='Item Name'>".$item_name[$ikey]."</td>";
                            echo "<td title='Unit'>".$item_unit[$ikey]."</td>";

                            echo "<td title='Opening' style='text-align:right;'>".number_format_ind($final_opening_stock[$ikey])."</td>";
                            //Purchases
                            echo "<td title='Purchase/Transfer In' style='text-align:right;'>".number_format_ind($final_purin_qty[$ikey])."</td>";

                            echo "<td title='Consumption/Transferout' style='text-align:right;'>".number_format_ind($final_consumed_quantity[$ikey])."</td>";
                            

                            $in_qty = ((float)$final_opening_stock[$ikey] + (float)$final_purin_qty[$ikey] + (float)$final_in_quantity[$ikey] + (float)$final_salereturn_quantity[$ikey]);
                            $out_qty = ((float)$final_consumed_quantity[$ikey] + (float)$final_sold_quantity[$ikey]);
                            
                            if(number_format_ind($in_qty) == number_format_ind($out_qty)){
                                $final_closing_stock[$ikey] = $final_closing_prices[$ikey] = $final_closing_amounts[$ikey] = 0;
                            }
                            $total_closed_qty = $total_closed_qty + $final_closing_stock[$ikey]; if($total_closed_qty == ""){ $total_closed_qty = 0; }
                            $total_closed_amt = (($total_opening_amt + $total_purtrin_amt + $total_stkin_amt + $total_sreturn_amt) - ($total_consumd_amt + $total_sales_amt));
                            
                            if((int)$lstk_flag[$ikey] == 1 && (float)$final_closing_stock[$ikey] <= (float)$lstk_qty[$ikey]){
                                echo "<td title='Closing' style='text-align:right;color:red;font-weight:bold;'>".number_format_ind($final_closing_stock[$ikey])."</td>";
                            }
                            else{
                                echo "<td title='Closing' style='text-align:right;'>".number_format_ind($final_closing_stock[$ikey])."</td>";
                            }
                          
                            echo "</tr>";
                        }
                    }
                    echo "<tr class='thead4'>";
                    echo "<td colspan='4' style='text-align:center;font-weight:bold;'>Total</td>";
                    echo "<td title='Opening' style='text-align:right;font-weight:bold;'>".number_format_ind($total_opening_qty)."</td>";
                    //Purchases
                    echo "<td title='Purchase/Transfer In' style='text-align:right;font-weight:bold;'>".number_format_ind($total_purtrin_qty)."</td>";
                     echo "<td title='Consumption/Transferout' style='text-align:right;font-weight:bold;'>".number_format_ind($total_consumd_qty)."</td>";
                   
                     
                    
                    echo "<td title='Closing' style='text-align:right;font-weight:bold;'>".number_format_ind($total_closed_qty)."</td>";
                   
                    echo "</tr>";
                ?>
            </tbody>
        <?php
            }
        ?>
        </table>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
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