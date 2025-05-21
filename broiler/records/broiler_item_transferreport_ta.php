<?php
//broiler_item_transferreport_ta.php
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

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $region_code = $region_name = array();
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; $branch_code[$row['code']] = $row['code']; $branch_region[$row['code']] = $row['region_code']; }

$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_branch = $farm_line = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT DISTINCT vehicle_code FROM `item_stocktransfers`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ 
    if($row['vehicle_code'] == 'select' || $row['vehicle_code'] == ''){ }
    else{
        if(empty($vehicle_name[$row['vehicle_code']])){
            $vehicle_code[$row['vehicle_code']] = $row['vehicle_code'];
            $vehicle_name[$row['vehicle_code']] = $row['vehicle_code'];
        }
    }
}

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $desig_code = $desig_name = array();
while($row = mysqli_fetch_assoc($query)){ $desig_code[$row['code']] = $row['code']; $desig_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $emp_code = $emp_name = $driver_code = $driver_name = array();
while($row = mysqli_fetch_assoc($query)){
    $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name'];
    if(empty($desig_name[$row['desig_code']]) || $desig_name[$row['desig_code']] == ""){ }
    else{
        $driver_code[$row['code']] = $row['code'];
        $driver_name[$row['code']] = $row['name'];
    }
}

$sql = "SELECT DISTINCT driver_code FROM `item_stocktransfers`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ 
    if($row['driver_code'] == 'select' || $row['driver_code'] == ''){ }
    else{
        if(empty($driver_name[$row['driver_code']])){
            $driver_code[$row['driver_code']] = $row['driver_code'];
            $driver_name[$row['driver_code']] = $row['driver_code'];
        }
    }
}

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code'];  }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }

//$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
//while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
$fdate = $tdate = date("Y-m-d"); $fregions = $fbranches = $tregions = $tbranches = $item_cat = $items = $loc_sector_from = $vehicle = $drivers = $loc_sector_to = "all"; $excel_type = "display";
if(isset($_REQUEST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $item_cat = $_REQUEST['item_cat'];
    $items = $_REQUEST['items'];
    $loc_sector_from = $_REQUEST['loc_sector_from'];
    $loc_sector_to = $_REQUEST['loc_sector_to'];
    $vehicle = $_REQUEST['vehicle'];
    $drivers = $_REQUEST['drivers'];

     
    $fregions = $_POST['fregions'];
    $fbranches = $_POST['fbranches']; 
    $tregions = $_POST['tregions'];
    $tbranches = $_POST['tbranches'];

    $fromfltr = $tofltr = "";
    if($fregions != "all" && $fbranches != "all"){
        $sql = "SELECT * FROM `broiler_farm` WHERE `region_code` = '$fregions' AND `branch_code` = '$fbranches' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $ffarmc = array();
        while($row = mysqli_fetch_assoc($query)){
            $ffarmc[$row['code']] = $row['code'];
        }
        $fromfrmcodelist = implode("','",$ffarmc);
        $fromfltr = " AND `fromwarehouse` IN ('$fromfrmcodelist')";
    }

     if($fregions == "all" && $fbranches != "all"){
       echo $sql = "SELECT * FROM `broiler_farm` WHERE `branch_code` = '$fbranches' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $ffarmcc = array();
        while($row = mysqli_fetch_assoc($query)){
           echo $ffarmcc[$row['code']] = $row['code'];
        }
        $fromfrmcodelistt = implode("','",$ffarmcc);
        $fromfltr = " AND `fromwarehouse` IN ('$fromfrmcodelistt')";
    }

    if($tregions != "all" && $tbranches != "all"){
        $sql = "SELECT * FROM `broiler_farm` WHERE `region_code` = '$tregions' AND `branch_code` = '$tbranches' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $tfarmc = array();
        while($row = mysqli_fetch_assoc($query)){
            $tfarmc[$row['code']] = $row['code'];
        }
        $tofrmcodelist = implode("','",$tfarmc);
        $tofltr = " AND `towarehouse` IN ('$tofrmcodelist')";
    }

    if($tregions == "all" && $tbranches == "all" && $fregions == "all" && $fbranches == "all"){
        if($loc_sector_to == "all"){
            $tofltr = "";
        }else{
            $tofltr = " AND `towarehouse` = '$loc_sector_to'";
        }

        if($loc_sector_from == "all"){
            $fromfltr = "";
        }else{
            $fromfltr = " AND `fromwarehouse` = '$loc_sector_from'";
        }
        
        
    }

    if($vehicle == "all"){ $vehicle_filter = ""; } else{ $vehicle_filter = " AND `vehicle_code` = '$vehicle'"; }
    if($drivers == "all"){ $driver_filter = ""; } else{ $driver_filter = " AND `driver_code` = '$drivers'"; }
    
    if($items != "all"){ $item_filter = " AND `code` IN ('$items')"; }
    else if($item_cat == "all"){ $item_filter = ""; }
    else{
        $icat_list = $item_filter = "";
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
        $item_filter = " AND `code` IN ('$icat_list')";
    }
	$excel_type = $_REQUEST['export'];
	$url = "../PHPExcel/Examples/broiler_item_transferreport_ta-Excel.php?fromdate=".$fdate."&todate=".$tdate."&items=".$items."&itemcat=".$item_cat."&fsector=".$loc_sector_from."&tsector=".$loc_sector_to;
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
                    <th colspan="19" align="center"><?php echo $row['cdetails']; ?><h5>Stock Transfer Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_item_transferreport_ta.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="21">
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
                                    <label>From Region</label>
                                    <select name="fregions" id="fregions" class="form-control select2" >
                                        <option value="all" <?php if($fregions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($fregions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>From Branch</label>
                                    <select name="fbranches" id="fbranches" class="form-control select2"  >
                                        <option value="all" <?php if($fbranches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($fbranches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="m-2 form-group">
                                    <label>To Region</label>
                                    <select name="tregions" id="tregions" class="form-control select2" >
                                        <option value="all" <?php if($tregions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($tregions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Branch</label>
                                    <select name="tbranches" id="tbranches" class="form-control select2"  >
                                        <option value="all" <?php if($tbranches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($tbranches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
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
                                    <label>From Warehouse</label>
                                    <select name="loc_sector_from" id="loc_sector_from" class="form-control select2">
                                        <option value="all" <?php if($loc_sector_from == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($loc_sector_from == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Warehouse</label>
                                    <select name="loc_sector_to" id="loc_sector_to" class="form-control select2">
                                        <option value="all" <?php if($loc_sector_to == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($loc_sector_to == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Vehicle</label>
                                    <select name="vehicle" id="vehicle" class="form-control select2">
                                        <option value="all" <?php if($vehicle == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vehicle_code as $whcode){ if($vehicle_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($vehicle == $whcode){ echo "selected"; } ?>><?php echo $vehicle_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Driver</label>
                                    <select name="drivers" id="drivers" class="form-control select2">
                                        <option value="all" <?php if($drivers == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($driver_code as $dcode){ if($driver_name[$dcode] != ""){ ?>
                                        <option value="<?php echo $dcode; ?>" <?php if($drivers == $dcode){ echo "selected"; } ?>><?php echo $driver_name[$dcode]; ?></option>
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
                    <th>Branch</th>
                    <th>Transaction No.</th>
                    <th>Dc No.</th>
                    <th>From Warehouse</th>
                    <th>From Batch</th>
                    <th>To Warehouse</th>
                    <th>To Batch</th>
                    <th>Item Code</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Bag Qty</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>vehicle</th>
                    <th>Driver</th>
                    <th>Line</th>
                    <th>Bag/Kg Rate</th>
                    <th>Transport Cost</th>
                    <th>Narration</th>
                    <th>User</th>
                </tr>
            </thead>
            <?php
            if(isset($_REQUEST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
               echo $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$tofltr."".$vehicle_filter."".$driver_filter."".$fromfltr."".$item_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_qty = $tot_amt = $tot_bags = 0;
                while($row = mysqli_fetch_assoc($query)){
                    if($vehicle_name[$row['vehicle_code']] != ''){ $vename = $vehicle_name[$row['vehicle_code']]; }
                    else if($row['vehicle_code'] == 'select'){ $vename = ""; }
                    else{ $vename = $row['vehicle_code']; }

                    if($emp_name[$row['driver_code']] != ''){ $dvr_name = $emp_name[$row['driver_code']]; }
                    else if($row['driver_code'] == 'select'){ $dvr_name = ""; }
                    else{ $dvr_name = $row['driver_code']; }

                   echo $brch_name = $branch_name[$farm_branch[$row['towarehouse']]];
                    $lne_name = $line_name[$farm_line[$row['towarehouse']]];
                    $fbch_name = $batch_name[$row['from_batch']];
                    $tbch_name = $batch_name[$row['to_batch']];

                    $t_bags = $row['bags']; if($t_bags == ""){ $t_bags = 0; }
                    $bok_rate = $row['bok_rate']; if($bok_rate == ""){ $bok_rate = 0; }
                    if((float)$bok_rate != 0){
                        $bok_amt = (float)$t_bags * (float)$bok_rate;
                    }
                    else{
                        $bok_amt = $row['transport_cost'];
                    }
                    if($bok_amt == ""){ $bok_amt = 0; }
                ?>
                <tr>
                    <td title="Date"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title="Branch"><?php echo $brch_name; ?></td>
                    <td title="Transaction No."><?php echo $row['trnum']; ?></td>
                    <td title="Dc No."><?php echo $row['dcno']; ?></td>
                    <td title="From Warehouse"><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
                    <td title="From Batch"><?php echo $fbch_name; ?></td>
                    <td title="To Warehouse"><?php echo $sector_name[$row['towarehouse']]; ?></td>
                    <td title="To Batch"><?php echo $tbch_name; ?></td>
                    <td title="Item"><?php echo $row['code']; ?></td>
                    <td title="Item"><?php echo $item_name[$row['code']]; ?></td>
                    <td title="Quantity" style="text-align:right;"><?php echo number_format_ind($row['quantity']); ?></td>
                    <td title="Bag Qty" style="text-align:right;">
                        <?php
                        if(!empty($feed_code[$row['code']])){
                            $bags = 0;
                            echo str_replace(".00","",number_format_ind(round(($row['quantity'] / 50))));
                            $bags = round(($row['quantity'] / 50));
                            $tot_bags = $tot_bags + $bags;
                        }
                        else{
                            echo "0";
                        }
                        ?>
                    </td>
                    <td title="Price" style="text-align:right;"><?php echo number_format_ind($row['price']); ?></td>
                    <td title="Amount" style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
                    <td title="Vehicle"><?php echo $vename; ?></td>
                    <td title="Driver"><?php echo $dvr_name; ?></td>
                    <td title="Line"><?php echo $lne_name; ?></td>
                    <td title="Bag Rate" style="text-align:right;"><?php echo number_format_ind($bok_rate); ?></td>
                    <td title="Transport Cost" style="text-align:right;"><?php echo number_format_ind($bok_amt); ?></td>
                    <td title="Narration"><?php echo $row['remarks']; ?></td>
                    <td title="User"><?php echo $emp_name[$db_emp_code[$row['addedemp']]]; ?></td>
                </tr>
                <?php
                    $tot_qty = $tot_qty + $row['quantity'];
                    $tot_amt = $tot_amt + $row['amount'];
                    $tot_transport_Cost = (float)$tot_transport_Cost + (float)$bok_amt;
                }
                if($tot_amt > 0 && $tot_qty > 0){
                    $avg_price = round(($tot_amt / $tot_qty),2);
                }
                else{
                    $avg_price = 0;
                }
                
                $bag_avgprc = 0;
                if((float)$tot_qty > 0){
                    $bag_avgprc = (float)$tot_transport_Cost / (float)$tot_qty;
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="10" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_qty,2)); ?></th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($tot_bags,2))); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($avg_price,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_amt,2)); ?></th>
                <th colspan="1"></th>
                <th colspan="1"></th>
                <th colspan="1"></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($bag_avgprc,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_transport_Cost,2)); ?></th>
                <th colspan="2"></th>
            </tr>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
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
    </body>
</html>
<?php
include "header_foot.php";
?>