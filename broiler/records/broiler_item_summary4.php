<?php
//broiler_item_summary4.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Item Summary Report";
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

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; $item_cunits[$row['code']] = $row['cunits']; }

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; $icat_iac[$row['code']] = $row['iac']; }

$fdate = $tdate = date("Y-m-d"); $item_cat = $items = $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate'])); $tdate = date("Y-m-d",strtotime($_POST['tdate'])); $item_cat = $_POST['item_cat']; $items = $_POST['items']; $sectors = $_POST['sectors'];

    $coa_list = $icat_list = $item_filter = $item_filter2 = $sector_filter = $sector_filter2 = "";
    if($sectors == "all"){ $sector_filter = $sector_filter2 = ""; } else{ $sector_filter = " AND `location` = '$sectors'"; $sector_filter2 = " AND `warehouse` = '$sectors'"; }

    if($items != "all"){
        $item_filter = " AND `item_code` IN ('$items')";
        $item_filter2 = " AND `itemcode` IN ('$items')";

        $coa_list = $icat_iac[$item_category[$items]];
        $iacoa_filter = " AND `coa_code` IN ('$coa_list')";
    }
    else if($item_cat != "all"){
        foreach($item_code as $icodes){
            if($item_category[$icodes] == $item_cat){
                if($icat_list == ""){
                    $icat_list = $icodes;
                }
                else{
                    $icat_list = $icat_list."','".$icodes;
                }
                if(!empty($icat_iac[$item_category[$icodes]])){
                    if($coa_list == ""){
                        $coa_list = $icat_iac[$item_category[$icodes]];
                    }
                    else{
                        $coa_list = $coa_list."','".$icat_iac[$item_category[$icodes]];
                    }
                }
            }
        }
        $item_filter = " AND `item_code` IN ('$icat_list')";
        $item_filter2 = " AND `itemcode` IN ('$icat_list')";
        $iacoa_filter = " AND `coa_code` IN ('$coa_list')";
    }
    else{
        $item_filter = $item_filter2 = "";
        foreach($item_code as $icodes){
            if(!empty($icat_iac[$item_category[$icodes]])){
                $coa_list = ""; $coa_list = $icat_iac[$item_category[$icodes]]; $coa_array_list[$coa_list] = $coa_list;
            }
            $item_array_list[$icodes] = $icodes;
        }
        $coa_list = ""; $coa_list = implode("','",$coa_array_list);
        $icat_list = ""; $icat_list = implode("','",$item_array_list);
        $iacoa_filter = " AND `coa_code` IN ('$coa_list')";
        $item_filter = " AND `item_code` IN ('$icat_list')";
        $item_filter2 = " AND `itemcode` IN ('$icat_list')";
    }

    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_item_cat =$icat_name[$_POST['item_cat']]; if ($export_item_cat == "") { $export_item_cat = "All"; }
    $export_items = $item_name[$_POST['items']]; if ($export_items == "") { $export_items = "All"; }
    $export_sectors = $sector_name[$_POST['sectors']]; if ( $export_sectors == "") {  $export_sectors = "All"; }

    if ($export_fdate == $export_tdate)
    {$filename = "Item Summary_".$export_tdate; }
     else {
    $filename = "Item Summary_".$export_fdate."_to_".$export_tdate; }

    $excel_type = $_POST['export'];
    //$url = "";
	//$url = "../PHPExcel/Examples/ItemRerateMaster-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors;
}
/*else{
    $url = "";
}*/
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
       
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
        <table class="tbl" style="width:auto;" align="center" style="width:1300px;" >
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Item Summary Report</h5></th>
                    <th colspan="20" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_item_summary4.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="29">
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
                                    <label>Category</label>
                                    <select name="item_cat" id="item_cat" class="form-control select2" onchange="fetch_item_list();">
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Items</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php if($item_cat == "all"){ ?>
                                        <?php foreach($item_code as $icodes){ if($item_name[$icodes] != ""){ ?>
                                        <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                        <?php } } }
                                        else{
                                            foreach($item_code as $icodes){
                                                if($item_cat == $item_category[$icodes]){
                                                ?>
                                                <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                                <?php
                                                }
                                            }
                                        }
                                            ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Sector</label>
                                    <select name="sectors" id="sectors" class="form-control select2" style="width:250px;">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($sectors == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Item Summary','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
            </table>
            </form>
            <table id="main_body" class="tbl" align="center"  style="width:1300px;">
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                   
                    <th colspan="28" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Item Summary Report</h5></th>
                    
                </tr>
            <?php } ?>
            
  
                <tr>
                       
                <th colspan="28">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Category: <?php echo $export_item_cat; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Items: <?php echo $export_items; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Sector: <?php echo $export_sectors; ?></label>
            
                                </div>
                                <div class="m-2 form-group">
                                    <label><br/></label>
            
                                </div>
                                
                        </th>
                    
                </tr>
               
            </thead>
            <thead class="thead3" align="center">
            <br/>
            <input style="left:50px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
            <br/>
            
            <tr align="center">
                    <th rowspan="2">Item Category</th>
                    <th rowspan="2">Item Code</th>
                    <th rowspan="2">Item Description</th>
                    <th rowspan="2">Item Unit</th>
                    <th colspan="3">Opening</th>
                    <th colspan="3">Purchase/Transferred IN</th>
                    <th colspan="3">Consume/Sale/Transferred OUT</th>
                    <th colspan="3">Purchase Return</th>
                    <th colspan="3">Closing</th>
                </tr>
                <tr align="center">
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
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
            <?php
            if(isset($_POST['submit_report']) == true){
                //Purchase Return
                $sql = "SELECT * FROM `broiler_itemreturns` WHERE `type` LIKE 'Supplier' AND `date` >= '$fdate' AND `date` <= '$tdate'".$item_filter2."".$sector_filter2." AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $prtn_trno = array();
                while($row = mysqli_fetch_assoc($query)){ $prtn_trno[$row['trnum']] = $row['trnum']; }

                $current_stock = $current_price = $current_amount = $item_list = $icat_arr_list = $opening_stk_qty = $opening_stk_amt = $between_in_qty = $between_in_amt = $between_out_qty = $between_out_amt = 
                $between_prtn_qty = $between_prtn_amt = array();
                $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$tdate'".$iacoa_filter."".$item_filter."".$sector_filter." AND `active` = 1 AND `dflag` = 0 ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($row['item_code'] != ""){
                        $key = $item_category[$row['item_code']]."@".$row['item_code'];
                        if($row['crdr'] == "CR"){
                            $current_stock[$key] = (float)$current_stock[$key] - (float)$row['quantity'];
                            $current_amount[$key] = (float)$current_amount[$key] - ((float)$current_price[$key] * (float)$row['quantity']);

                            if(strtotime($row['date']) >= strtotime($fdate)){
                                if(empty($prtn_trno[$row['trnum']]) || $prtn_trno[$row['trnum']] == ""){
                                    $between_out_qty[$key] = (float)$between_out_qty[$key] + (float)$row['quantity'];
                                    $between_out_amt[$key] = (float)$between_out_amt[$key] + ((float)$current_price[$key] * (float)$row['quantity']);
                                }
                                else{
                                    $between_prtn_qty[$key] = (float)$between_prtn_qty[$key] + (float)$row['quantity'];
                                    $between_prtn_amt[$key] = (float)$between_prtn_amt[$key] + ((float)$row['price'] * (float)$row['quantity']);
                                }
                            }
                        }
                        else if($row['crdr'] == "DR"){
                            $current_stock[$key] = (float)$current_stock[$key] + (float)$row['quantity'];
                            $current_amount[$key] = (float)$current_amount[$key] + (float)$row['amount'];
                            if((float)$current_stock[$key] != 0){
                                $current_price[$key] = round(((float)$current_amount[$key] / (float)$current_stock[$key]),15);
                            }
                            else{
                                $current_price[$key] = 0;
                            }
                            
                            if(strtotime($row['date']) >= strtotime($fdate)){
                                $between_in_qty[$key] = (float)$between_in_qty[$key] + (float)$row['quantity'];
                                $between_in_amt[$key] = (float)$between_in_amt[$key] + (float)$row['amount'];
                            }
                        }
                        else{ }

                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opening_stk_qty[$key] = (float)$current_stock[$key];
                            $opening_stk_amt[$key] = (float)$current_amount[$key];
                        }
                        
                        if(number_format_ind($current_stock[$key]) == "0.00"){ $current_stock[$key] = $current_price[$key] = $current_amount[$key] = 0; }
                        $item_list[$row['item_code']] = $row['item_code'];
                        $icat_arr_list[$item_category[$row['item_code']]] = $item_category[$row['item_code']];
                        
                    }
                }
                /* Sector and item wise filter*/
                $loc_icat_opening_qty = $loc_icat_opening_amt = $loc_icat_btwout_qty = $loc_icat_btwout_amt = $loc_icat_btwprtn_qty = $loc_icat_btwprtn_amt = $loc_icat_btwin_qty = $loc_icat_btwin_amt = $icat_list = array();
                foreach($icat_arr_list as $slist){
                    foreach($item_list as $ilist){
                        $key = $slist."@".$ilist;
                        $ccode = $item_code[$ilist]; $key2 = $slist."@".$ccode; $icat_list[$ccode] = $ccode;
                        
                        if(!empty($opening_stk_qty[$key])){ $loc_icat_opening_qty[$key2] = $loc_icat_opening_qty[$key2] + $opening_stk_qty[$key]; }
                        if(!empty($opening_stk_amt[$key])){ $loc_icat_opening_amt[$key2] = $loc_icat_opening_amt[$key2] + $opening_stk_amt[$key]; }

                        if(!empty($between_in_qty[$key])){$loc_icat_btwin_qty[$key2] = $loc_icat_btwin_qty[$key2] + $between_in_qty[$key]; }
                        if(!empty($between_in_amt[$key])){ $loc_icat_btwin_amt[$key2] = $loc_icat_btwin_amt[$key2] + $between_in_amt[$key]; }

                        if(!empty($between_out_qty[$key])){ $loc_icat_btwout_qty[$key2] = $loc_icat_btwout_qty[$key2] + $between_out_qty[$key]; }
                        if(!empty($between_out_amt[$key])){ $loc_icat_btwout_amt[$key2] = $loc_icat_btwout_amt[$key2] + $between_out_amt[$key]; }

                        if(!empty($between_prtn_qty[$key])){ $loc_icat_btwprtn_qty[$key2] = $loc_icat_btwprtn_qty[$key2] + $between_prtn_qty[$key]; }
                        if(!empty($between_prtn_amt[$key])){ $loc_icat_btwprtn_amt[$key2] = $loc_icat_btwprtn_amt[$key2] + $between_prtn_amt[$key]; }

                        if(!empty($current_stock[$key])){ $loc_icat_btwclose_qty[$key2] = $loc_icat_btwclose_qty[$key2] + $current_stock[$key]; }
                        if(!empty($current_amount[$key])){ $loc_icat_btwclose_amt[$key2] = $loc_icat_btwclose_amt[$key2] + $current_amount[$key]; }
                        
                        if(number_format_ind(round($loc_icat_btwclose_qty[$key],2)) == "0.00"){ $loc_icat_btwclose_qty[$key2] = $loc_icat_btwclose_amt[$key2] = 0; }
                    }
                }
                ?>
            <tbody>
            <?php
            $total_opening_qty = $total_opening_amt = $total_in_qty = $total_in_amt = $total_out_qty = $total_out_amt = $total_prtn_qty = $total_prtn_amt = $total_close_qty = $total_close_amt = 0;
            foreach($icat_arr_list as $slist){
                $sector_header[$slist] = 0; $sectorwise_total_open_qty = $sectorwise_total_open_amt = $sectorwise_total_in_qty = $sectorwise_total_in_amt = $sectorwise_total_out_qty = $sectorwise_total_out_amt = 
                $sectorwise_total_prtn_qty = $sectorwise_total_prtn_amt = $sectorwise_total_close_qty = $sectorwise_total_close_amt = 0;
                foreach($icat_list as $ilist){
                    $key = $slist."@".$ilist;
                    if(number_format_ind(round($loc_icat_opening_qty[$key],2)) == "0.00" && number_format_ind(round($loc_icat_btwin_qty[$key],2)) == "0.00" && number_format_ind(round($loc_icat_btwout_qty[$key],2)) == "0.00" && number_format_ind(round($loc_icat_btwclose_qty[$key],2)) == "0.00"){ }
                    else if($item_name[$ilist] == ""){ }
                    else{
                        echo "<tr>";
                        if($sector_header[$slist] == 0){ echo "<td>".$icat_name[$slist]."</td>"; } else{ echo "<td></td>"; }
                        echo "<td>".$ilist."</td>";
                        echo "<td>".$item_name[$ilist]."</td>";
                        echo "<td>".$item_cunits[$ilist]."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_opening_qty[$key],2))."</td>";
                        if((float)$loc_icat_opening_qty[$key] != 0){ $loc_icat_opening_prc[$key] = (float)$loc_icat_opening_amt[$key] / (float)$loc_icat_opening_qty[$key]; } else{ $loc_icat_opening_prc[$key] = 0; }
                        echo "<td style='text-align:right;'>".number_format_ind(round(($loc_icat_opening_prc[$key]),2))."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_opening_amt[$key],2))."</td>";
    
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_btwin_qty[$key],2))."</td>";
                        if((float)$loc_icat_btwin_qty[$key] != 0){ $loc_icat_btwin_prc[$key] = (float)$loc_icat_btwin_amt[$key] / (float)$loc_icat_btwin_qty[$key]; } else{ $loc_icat_btwin_prc[$key] = 0; }
                        echo "<td style='text-align:right;'>".number_format_ind(round(($loc_icat_btwin_prc[$key]),2))."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_btwin_amt[$key],2))."</td>";
    
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_btwout_qty[$key],2))."</td>";
                        if((float)$loc_icat_btwout_qty[$key] != 0){ $loc_icat_btwout_prc[$key] = (float)$loc_icat_btwout_amt[$key] / (float)$loc_icat_btwout_qty[$key]; } else{ $loc_icat_btwout_prc[$key] = 0; }
                        echo "<td style='text-align:right;'>".number_format_ind(round(($loc_icat_btwout_prc[$key]),2))."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($loc_icat_btwout_amt[$key])."</td>";
                        
                        //Purchase Return
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_btwprtn_qty[$key],2))."</td>";
                        if((float)$loc_icat_btwprtn_qty[$key] != 0){ $loc_icat_btwprtn_prc[$key] = (float)$loc_icat_btwprtn_amt[$key] / (float)$loc_icat_btwprtn_qty[$key]; } else{ $loc_icat_btwprtn_prc[$key] = 0; }
                        echo "<td style='text-align:right;'>".number_format_ind(round(($loc_icat_btwprtn_prc[$key]),2))."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_btwprtn_amt[$key],2))."</td>";
    
                        echo "<td style='text-align:right;'>".number_format_ind(round($loc_icat_btwclose_qty[$key],2))."</td>";
                        if((float)$loc_icat_btwclose_qty[$key] != 0){ $loc_icat_btwclose_prc[$key] = (float)$loc_icat_btwclose_amt[$key] / (float)$loc_icat_btwclose_qty[$key]; } else{ $loc_icat_btwclose_prc[$key] = 0; }
                        echo "<td style='text-align:right;'>".number_format_ind(round(($loc_icat_btwclose_prc[$key]),2))."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($loc_icat_btwclose_amt[$key])."</td>";
                        echo "</tr>";
                        $sector_header[$slist] = 1;
                        
                        $total_opening_qty = $total_opening_qty + $loc_icat_opening_qty[$key];
                        $total_opening_amt = $total_opening_amt + $loc_icat_opening_amt[$key];

                        $total_in_qty = $total_in_qty + $loc_icat_btwin_qty[$key];
                        $total_in_amt = $total_in_amt + $loc_icat_btwin_amt[$key];
                        
                        $total_out_qty = $total_out_qty + $loc_icat_btwout_qty[$key];
                        $total_out_amt = $total_out_amt + $loc_icat_btwout_amt[$key];
                        
                        $total_prtn_qty = $total_prtn_qty + $loc_icat_btwprtn_qty[$key];
                        $total_prtn_amt = $total_prtn_amt + $loc_icat_btwprtn_amt[$key];
                        
                        $total_close_qty = $total_close_qty + $loc_icat_btwclose_qty[$key];
                        $total_close_amt = $total_close_amt + $loc_icat_btwclose_amt[$key];

                        $sectorwise_total_open_qty = $sectorwise_total_open_qty + $loc_icat_opening_qty[$key];
                        $sectorwise_total_open_amt = $sectorwise_total_open_amt + $loc_icat_opening_amt[$key];
                        $sectorwise_total_in_qty = $sectorwise_total_in_qty + $loc_icat_btwin_qty[$key];
                        $sectorwise_total_in_amt = $sectorwise_total_in_amt + $loc_icat_btwin_amt[$key];
                        $sectorwise_total_out_qty = $sectorwise_total_out_qty + $loc_icat_btwout_qty[$key];
                        $sectorwise_total_out_amt = $sectorwise_total_out_amt + $loc_icat_btwout_amt[$key];
                        $sectorwise_total_prtn_qty = $sectorwise_total_prtn_qty + $loc_icat_btwprtn_qty[$key];
                        $sectorwise_total_prtn_amt = $sectorwise_total_prtn_amt + $loc_icat_btwprtn_amt[$key];
                        $sectorwise_total_close_qty = $sectorwise_total_close_qty + $loc_icat_btwclose_qty[$key];
                        $sectorwise_total_close_amt = $sectorwise_total_close_amt + $loc_icat_btwclose_amt[$key];
                        if(number_format_ind($sectorwise_total_close_qty) == "0.00"){ $sectorwise_total_close_qty = $sectorwise_total_close_amt = 0; }
                    }
                }
                ?>
                <tr class="thead4">
                <th colspan="4" style="text-align:center;">Total</th>

				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_open_qty); ?></th>
                <?php if((float)$sectorwise_total_open_qty != 0){ $sectorwise_total_open_prc = (float)$sectorwise_total_open_amt / (float)$sectorwise_total_open_qty; } else{ $sectorwise_total_open_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($sectorwise_total_open_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_open_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_in_qty); ?></th>
                <?php if((float)$sectorwise_total_in_qty != 0){ $sectorwise_total_in_prc = (float)$sectorwise_total_in_amt / (float)$sectorwise_total_in_qty; } else{ $sectorwise_total_in_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($sectorwise_total_in_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_in_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_out_qty); ?></th>
                <?php if((float)$sectorwise_total_out_qty != 0){ $sectorwise_total_out_prc = (float)$sectorwise_total_out_amt / (float)$sectorwise_total_out_qty; } else{ $sectorwise_total_out_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($sectorwise_total_out_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_out_amt); ?></th>
                <!--Purchase Return-->
				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_prtn_qty); ?></th>
                <?php if((float)$sectorwise_total_prtn_qty != 0){ $sectorwise_total_prtn_prc = (float)$sectorwise_total_prtn_amt / (float)$sectorwise_total_prtn_qty; } else{ $sectorwise_total_prtn_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($sectorwise_total_prtn_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_prtn_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_close_qty); ?></th>
                <?php if((float)$sectorwise_total_close_qty != 0){ $sectorwise_total_close_prc = (float)$sectorwise_total_close_amt / (float)$sectorwise_total_close_qty; } else{ $sectorwise_total_close_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($sectorwise_total_close_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($sectorwise_total_close_amt); ?></th>
            </tr>
                <?php
            }
            ?>
            </tbody>
            <tr class="thead4">
                <th colspan="4" style="text-align:center;">Grand Total</th>

				<th style="text-align:right;"><?php echo number_format_ind($total_opening_qty); ?></th>
                <?php if((float)$total_opening_qty != 0){ $total_opening_prc = (float)$total_opening_amt / (float)$total_opening_qty; } else{ $total_opening_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($total_opening_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_opening_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($total_in_qty); ?></th>
                <?php if((float)$total_in_qty != 0){ $total_in_prc = (float)$total_in_amt / (float)$total_in_qty; } else{ $total_in_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($total_in_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_in_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($total_out_qty); ?></th>
                <?php if((float)$total_out_qty != 0){ $total_out_prc = (float)$total_out_amt / (float)$total_out_qty; } else{ $total_out_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($total_out_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_out_amt); ?></th>
                <!--Purchase Return-->
				<th style="text-align:right;"><?php echo number_format_ind($total_prtn_qty); ?></th>
                <?php if((float)$total_prtn_qty != 0){ $total_prtn_prc = (float)$total_prtn_amt / (float)$total_prtn_qty; } else{ $total_prtn_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($total_prtn_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_prtn_amt); ?></th>

				<th style="text-align:right;"><?php echo number_format_ind($total_close_qty); ?></th>
                <?php if((float)$total_close_qty != 0){ $total_close_prc = (float)$total_close_amt / (float)$total_close_qty; } else{ $total_close_prc = 0; } ?>
				<th style="text-align:right;"><?php echo number_format_ind(round(($total_close_prc),2)); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_close_amt); ?></th>
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
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icodes){
                        $icats = $item_category[$icodes];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icodes){
                            $icats = $item_category[$icodes];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        
        <script src="../table_search_filter/Search_Script.js"></script>
        <script type="text/javascript">
var tableToExcel = (function() {
    
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
   // if (selectedValue === 'excel') {  
  return function(table, name, filename, chosen) {
    if (chosen === 'excel') { 
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    //window.location.href = uri + base64(format(template, ctx))
    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
    }
  }

})()
</script>


    </body>
</html>
<?php
include "header_foot.php";
?>