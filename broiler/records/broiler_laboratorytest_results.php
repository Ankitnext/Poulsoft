<?php
//broiler_laboratorytest_results.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Lab Test Report";
include "header_head.php";

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `master_parameters` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $param_code[$row['code']] = $row['code']; $param_name[$row['code']] = $row['description']; }

$sql = "SELECT DISTINCT(vehicle_code) as code FROM `broiler_purchases` WHERE `vehicle_code` NOT IN ('select')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if(!empty($vehicle_code[$row['code']])){ } else{ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['code']; } }
$fdate = $tdate = date("Y-m-d"); $items = $vendors = $sectors = $vehicles = $trtypes = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $items = $_POST['items'];
    $vendors = $_POST['vendors'];
    $sectors = $_POST['sectors'];
    $vehicles = $_POST['vehicles'];
    $trtypes = $_POST['trtypes'];

    if($items == "all"){ $item_filter = $fp_item_filter = ""; } else{ $item_filter = " AND `icode` = '$items'"; $fp_item_filter = " AND `feed_code` = '$items'"; }
    if($vendors == "all"){ $vendor_filter = ""; } else{ $vendor_filter = " AND `vcode` = '$vendors'"; }
    if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `warehouse` = '$sectors'"; }
    if($vehicles == "all"){ $vehicle_filter = ""; } else{ $vehicle_filter = " AND `vehicle_code` = '$vehicles'"; }
    if($trtypes == "all"){ $trtype_filter = ""; } else{ $trtype_filter = " AND `ttype` = '$trtypes'"; }
    
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/SalesReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&items=".$items."&vendors=".$vendors."&branches=".$branches."&lines=".$lines."&sectors=".$sectors;
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
                    <th colspan="18" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Lab Test Report</h5></th>
                    <th colspan="180" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_laboratorytest_results.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="200">
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
                                    <label>Supplier</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Item</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($item_code as $icode){ if($item_name[$icode] != ""){ ?>
                                        <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Vehicle</label>
                                    <select name="vehicles" id="vehicles" class="form-control select2">
                                        <option value="all" <?php if($vehicles == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vehicle_code as $vcode){ if($vehicle_name[$vcode] != ""){ ?>
                                        <option value="<?php echo $vcode; ?>" <?php if($vehicles == $vcode){ echo "selected"; } ?>><?php echo $vehicle_name[$vcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Transaction Type</label>
                                    <select name="trtypes" id="trtypes" class="form-control select2">
                                        <option value="all" <?php if($trtypes == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="PurchaseInv" <?php if($trtypes == "PurchaseInv"){ echo "selected"; } ?>>Purchase Items</option>
                                        <option value="FeedProductionInv" <?php if($trtypes == "FeedProductionInv"){ echo "selected"; } ?>>Feed Production Items</option>
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
                    <th rowspan="3">Date</th>
                    <th rowspan="3">Transaction No.</th>
                    <th rowspan="3">Supplier Name</th>
                    <th rowspan="3">Vehicle No.</th>
                    <th rowspan="3">Quantity</th>
                    <th rowspan="3">Per Kg Cost</th>
                    <?php
                    foreach($param_code as $pcode){
                    ?>
                    <th colspan="6"><?php echo $param_name[$pcode]; ?></th>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach($param_code as $pcode){
                    ?>
                    <th rowspan="2">Std</th>
                    <th rowspan="2">Actual</th>
                    <th colspan="2">Rebate Limits</th>
                    <th colspan="2">Rejections</th>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                <?php
                    foreach($param_code as $pcode){
                    ?>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Min</th>
                    <th>Max</th>
                    <?php
                    }
                    ?>

                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $start_date = $end_date = ""; $inv_list = "";
                if($trtypes == "all" || $trtypes == "PurchaseInv"){
                    $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$item_filter."".$vehicle_filter." AND `active` = '1' AND `dflag` = '0' AND `lqt_flag` = '1' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_index = $row['trnum']."@".$row['icode'];
                        $item_date[$key_index] = $row['date'];
                        $item_qty[$key_index] = $item_qty[$key_index] + ($row['rcd_qty'] + $row['fre_qty']);
                        $item_price[$key_index] = $row['rate'];
                        $item_vcode[$key_index] = $row['vcode'];
                        $item_trnum[$key_index] = $row['trnum'];
                        $item_vehicle[$key_index] = $row['vehicle_code'];

                        $trnums[$row['trnum']] = $row['trnum'];
                        $item_list[$row['icode']] = $row['icode'];
                        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
                        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
                        if($inv_list == ""){ $inv_list = $row['trnum']; } else{ $inv_list = $inv_list."','".$row['trnum']; }
                        if($i_list == ""){ $i_list = $row['icode']; } else{ $i_list = $i_list."','".$row['icode']; }
                    }
                }
                if($trtypes == "all" || $trtypes == "FeedProductionInv"){
                    $sql_record = "SELECT * FROM `broiler_feed_production` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$fp_item_filter."".$vehicle_filter." AND `active` = '1' AND `dflag` = '0' AND `lqt_flag` = '1' ORDER BY `date`,`code` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_index = $row['code']."@".$row['feed_code'];
                        $item_date[$key_index] = $row['date'];
                        $item_qty[$key_index] = $item_qty[$key_index] + ($row['produced_quantity']);
                        $item_price[$key_index] = $row['produced_price'];
                        $item_trnum[$key_index] = $row['code'];

                        $trnums[$row['code']] = $row['code'];
                        $item_list[$row['feed_code']] = $row['feed_code'];
                        if($start_date == ""){ $start_date = $row['date']; } else{ if(strtotime($start_date) >= strtotime($row['date'])){ $start_date = $row['date']; } }
                        if($end_date == ""){ $end_date = $row['date']; } else{ if(strtotime($end_date) <= strtotime($row['date'])){ $end_date = $row['date']; } }
                        if($inv_list == ""){ $inv_list = $row['code']; } else{ $inv_list = $inv_list."','".$row['code']; }
                        if($i_list == ""){ $i_list = $row['feed_code']; } else{ $i_list = $i_list."','".$row['feed_code']; }
                    }
                }
                $sql = "SELECT * FROM `broiler_lab_results` WHERE `link_trnum` IN ('$inv_list') AND  `icode` IN ('$i_list')".$trtype_filter." AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['link_trnum']."@".$row['icode']."@".$row['param_code'];
                    $lab_trnum[$key_index] = $row['trnum'];
                    $std_value[$key_index] = $row['std_value'];
                    $rebate_min_value[$key_index] = $row['rebate_min_value'];
                    $rebate_max_value[$key_index] = $row['rebate_max_value'];
                    $rejection_min_value[$key_index] = $row['rejection_min_value'];
                    $rejection_max_value[$key_index] = $row['rejection_max_value'];
                    $actual_value[$key_index] = $row['actual_value'];
                }
                //for($currentDate = (strtotime($fdate)); $currentDate <= (strtotime($tdate)); $currentDate += (86400)){
                    foreach($trnums as $tcode){
                        foreach($item_list as $icode){
                            $key_index = $tcode."@".$icode;
                            $dcode = date("Y-m-d",$item_date[$key_index]);
                            if(!empty($item_trnum[$key_index])){
                                $trow = 1;
                                echo "<tr>";
                                ?>
                                    <td><?php echo date("d.m.Y",strtotime($dcode)); ?></td>
                                    <td><?php echo $item_trnum[$key_index]; ?></td>
                                    <td><?php echo $vendor_name[$item_vcode[$key_index]]; ?></td>
                                    <td><?php echo $vehicle_name[$item_vehicle[$key_index]]; ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($item_qty[$key_index],2)); ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($item_price[$key_index],2)); ?></td>
                                <?php
                                foreach($param_code as $pcode){
                                    $key2 = $tcode."@".$icode."@".$pcode;
                                    //if(!empty($lab_trnum[$key2])){
                                        //if($trow == 1){ echo "<tr>"; $trow = 2; }
                                    ?>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($std_value[$key2],2)); ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($actual_value[$key2],2)); ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($rebate_min_value[$key2],2)); ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($rebate_max_value[$key2],2)); ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($rejection_min_value[$key2],2)); ?></td>
                                    <td style="text-align:right;"><?php echo number_format_ind(round($rejection_max_value[$key2],2)); ?></td>
                                    <?php
                                    //}
                                }
                                //if($trow == 2){ echo "</tr>"; }
                                echo "</tr>";
                            }
                        }
                    }
                //}
                ?>
            </tbody>
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