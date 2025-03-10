<?php
//broiler_sales2.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code'];
    $farm_ccode[$row['code']] = $row['farm_code'];
    $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code'];
    $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `broiler_farmer` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Bird%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_code') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code = $row['code']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name']; }

//$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
//while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
$fdate = $tdate = date("Y-m-d"); $branches = $lines = $vendors = $sectors = $supervisors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $sectors = $_POST['sectors'];

    if($sectors != "all"){
        $sector_filter = " AND `warehouse` = '$sectors'";
    }
    else{
        $farm_filter = "";
        if($branches != "all"){ $farm_filter .= " AND `branch_code` = '$branches'"; }
        if($lines != "all"){ $farm_filter .= " AND `line_code` = '$lines'"; }
        if($supervisors != "all"){ $farm_filter .= " AND `supervisor_code` = '$supervisors'"; }

        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'".$farm_filter." ORDER BY `farm_code` ASC"; $query = mysqli_query($conn,$sql); $farm_list = "";
        while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['code']; } else{ $farm_list = $farm_list."','".$row['code']; } }

        if($branches == "all" && $lines == "all" && $supervisors == "all"){
            $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['code']; } else{ $farm_list = $farm_list."','".$row['code']; } }
        }
        $sector_filter = " AND `warehouse` IN ('$farm_list')";
    }

    if($vendors == "all"){ $vendor_filter = ""; } else{ $vendor_filter = " AND `vcode` = '$vendors'"; }

    $item_filter = " AND `icode` IN ('$item_code')";

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/SalesReport2-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors."&branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&sectors=".$sectors;
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="20" align="center"><?php echo $row['cdetails']; ?><h5>Sales Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_sales2.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="22">
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
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Location</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
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
                    <th>Date</th>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Invoice</th>
                    <th>Dc No.</th>
                    <th>Birds</th>
                    <th>Weight</th>
                    <th>Avg Wt</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>TCS Amount</th>
                    <th>Total Amount</th>
                    <th>Receipt Amount</th>
                    <th>Branch</th>
                    <th>Line</th>
                    <th>Supervisor</th>
                    <th>Farm</th>
                    <th>Batch</th>
                    <th>Mean Age</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql_record = "SELECT date,ccode,SUM(amount) as amount FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vtype` = 'Customer'  AND `active` = '1' AND `dflag` = '0' GROUP BY `date`,`ccode` ORDER BY `date`,`ccode` ASC";
                $query = mysqli_query($conn,$sql_record); $receipt_details = $displayed_rcts = $start_date = $end_date = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['date']."@".$row['ccode'];
                    $receipt_details[$key_index] = $row['amount'];
                }
                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$item_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $batch_list = "";
                while($row = mysqli_fetch_assoc($query)){
                    $key_index1 = $row['farm_batch'];
                    if($row['farm_batch'] == "" || $row['farm_batch'] == "select"){ }
                    else{
                        if($batch_list == ""){ $batch_list = $row['farm_batch']; } else{ $batch_list = $batch_list."','".$row['farm_batch']; }
                        $batches[$key_index1] = $key_index1;
                        if($start_date[$key_index1] == ""){ $start_date[$key_index1] = $row['date']; } else{ if(strtotime($start_date[$key_index1]) >= strtotime($row['date'])){ $start_date[$key_index1] = $row['date']; } }
                        if($end_date[$key_index1] == ""){ $end_date[$key_index1] = $row['date']; } else{ if(strtotime($end_date[$key_index1]) <= strtotime($row['date'])){ $end_date[$key_index1] = $row['date']; } }
                    }
                }

                $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,date,farm_batch FROM `broiler_sales` WHERE `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `date`,`farm_batch` ORDER BY `date`,`farm_batch` ASC";
                $query = mysqli_query($conn,$sql_record); $datewise_sale = $cum_sale = $tilldate_sale = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['date']."@".$row['farm_batch']; $key_index1 = $row['farm_batch'];
                    if(!empty($datewise_sale[$key_index])){ $datewise_sale[$key_index] = $datewise_sale[$key_index] + ($row['birds']); }
                    else{ $datewise_sale[$key_index] = ($row['birds']); }

                    if(!empty($cum_sale[$key_index1])){ $cum_sale[$key_index1] = $cum_sale[$key_index1] + ($row['birds']); }
                    else{ $cum_sale[$key_index1] = ($row['birds']); }

                    $tilldate_sale[$key_index] = $cum_sale[$key_index1];

                }
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['date']."@".$row['batch_code']; $key_index1 = $row['batch_code'];
                    $day_ages[$key_index] = $row['brood_age'];
                    $batches[$key_index1] = $key_index1;

                    if($start_date[$key_index1] == ""){ $start_date[$key_index1] = $row['date']; } else{ if(strtotime($start_date[$key_index1]) >= strtotime($row['date'])){ $start_date[$key_index1] = $row['date']; } }
                    if($end_date[$key_index1] == ""){ $end_date[$key_index1] = $row['date']; } else{ if(strtotime($end_date[$key_index1]) <= strtotime($row['date'])){ $end_date[$key_index1] = $row['date']; } }
                    if($dend_date[$key_index1] == ""){ $dend_date[$key_index1] = $row['date']; } else{ if(strtotime($dend_date[$key_index1]) <= strtotime($row['date'])){ $dend_date[$key_index1] = $row['date']; } }
                }
                //Mean Age Calculation
                foreach($batches as $bhcode){
                    $cum_age = 0;
                    for($currentDate = (strtotime($start_date[$bhcode])); $currentDate <= (strtotime($end_date[$bhcode])); $currentDate += (86400)){
                        $active_date = date("Y-m-d",((int)$currentDate)); $key_index = $active_date."@".$bhcode;
                        if(number_format_ind($day_ages[$key_index]) == "0.00"){
                            if(strtotime($end_date[$bhcode]) > strtotime($dend_date[$bhcode])){
                                $datediff = round((strtotime($end_date[$bhcode]) - strtotime($dend_date[$bhcode]))/ (60 * 60 * 24));
                                $day_ages[$key_index] = $day_ages[$dend_date[$bhcode]."@".$bhcode] + $datediff;
                            }
                        }
                        if(!empty($datewise_sale[$key_index]) && !empty($tilldate_sale[$key_index]) && !empty($day_ages[$key_index])){
                            if(number_format_ind($datewise_sale[$key_index]) != "0.00" && number_format_ind($tilldate_sale[$key_index]) != "0.00" && number_format_ind($day_ages[$key_index]) != "0.00"){
                                $cum_age = $cum_age + ($day_ages[$key_index] * $datewise_sale[$key_index]);
                                if($cum_age > 0 && $tilldate_sale[$key_index] > 0){
                                    $mean_ages[$key_index] = $cum_age / $tilldate_sale[$key_index];
                                }
                                else{
                                    $mean_ages[$key_index] = 0;
                                }
                            }
                            else{ $mean_ages[$key_index] = 0; }
                        }
                        else{ $mean_ages[$key_index] = 0; }
                    }
                }
                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$item_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = $tot_rct_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Code"><?php echo $vendor_ccode[$row['vcode']]; ?></td>
                    <td title="Customer"><?php if(!empty($vendor_name[$row['vcode']])){ echo $vendor_name[$row['vcode']]; } else{ echo $farmer_name[$farm_farmer[$row['warehouse']]]; } ?></td>
                    <td title="Invoice"><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['billno']; ?></td>
                    <td title="Birds" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($row['birds'])); ?></td>
                    <td title="Weight" style="text-align:right;"><?php echo number_format_ind($row['rcd_qty'] + $row['fre_qty']); ?></td>
                    <td title="Weight" style="text-align:right;">
                    <?php
                        if(($row['rcd_qty'] + $row['fre_qty']) > 0 && $row['birds'] > 0){
                            echo number_format_ind(($row['rcd_qty'] + $row['fre_qty']) / $row['birds']);
                        }
                        else{
                            echo number_format_ind(0);
                        }
                    
                    ?>
                    </td>
                    <td title="Rate" style="text-align:right;"><?php echo number_format_ind($row['rate']); ?></td>
                    <td title="Amount" style="text-align:right;"><?php echo number_format_ind($row['rcd_qty'] * $row['rate']); ?></td>
                    <td title="TCS Amount" style="text-align:right;"><?php echo number_format_ind($row['tcds_amt']); ?></td>
                    <td title="Total Amount" style="text-align:right;"><?php echo number_format_ind($row['item_tamt']); ?></td>
                    <?php
                    $key_index = $row['date']."@".$row['vcode'];
                    if(!empty($receipt_details[$key_index]) || number_format_ind($receipt_details[$key_index]) != "0.00"){
                        if(!empty($displayed_rcts[$key_index])){ echo "<td>0.00</td>"; }
                        else{
                            $displayed_rcts[$key_index] = $receipt_details[$key_index];
                            $tot_rct_amt = $tot_rct_amt + $receipt_details[$key_index];
                        ?>
                        <td title="Receipt Amount" style="text-align:right;"><?php echo number_format_ind($receipt_details[$key_index]); ?></td>
                        <?php
                        }
                    }
                    else{ echo "<td>0.00</td>"; }
                    ?>
                    
                    <td title="Branch"><?php echo $branch_name[$farm_branch[$row['warehouse']]]; ?></td>
                    <td title="Line"><?php echo $line_name[$farm_line[$row['warehouse']]]; ?></td>
                    <td title="Supervisor"><?php echo $supervisor_name[$farm_supervisor[$row['warehouse']]]; ?></td>
                    <td title="Farm"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Batch"><?php echo $batch_name[$row['farm_batch']]; ?></td>
                    <td title="Mean Age"><?php echo number_format_ind($mean_ages[$row['date']."@".$row['farm_batch']]); ?></td>
                    
                    <td title="Vehicle"><?php if(!empty($vehicle_name[$row['vehicle_code']])){ echo $vehicle_name[$row['vehicle_code']]; } else{ echo $row['vehicle_code']; } ?></td>
                    <td title="Driver"><?php if(!empty($supervisor_name[$row['driver_code']])){ echo $supervisor_name[$row['driver_code']]; } else if($row['driver_code'] == "select"){ echo ""; } else{ echo $row['driver_code']; } ?></td>
                    <td title="Remakrs"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                    $tot_bds = $tot_bds + $row['birds'];
                    $tot_qty = $tot_qty + $row['rcd_qty'] + $row['fre_qty'];
                    $tot_amt = $tot_amt + $row['item_tamt'];
                }
                if($tot_amt > 0 && $tot_qty > 0){
                    $avg_price = round(($tot_amt / $tot_qty),2);
                }
                else{
                    $avg_price = 0;
                }
                if($tot_qty > 0 && $tot_bds > 0){
                    $avg_wt = round(($tot_qty / $tot_bds),2);
                }
                else{
                    $avg_wt = 0;
                }
                
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="5" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($tot_bds))); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_qty,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($avg_wt,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($avg_price,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_rct_amt,2)); ?></th>
                <th colspan="9"></th>
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