<?php
//broiler_chick_placement_ta.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();

$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    $sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
    if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
    include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}


/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
    $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description'];
    $farm_capacity[$row['code']] = $row['farm_capacity'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }

$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $supplier_code[$row['code']] = $row['code']; $supplier_name[$row['code']] = $row['name']; $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = $vsectors = "all"; $excel_type = "display"; $url = "";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $vsectors = $_POST['vsectors'];
     $regions = $_POST['regions'];

    $farm_query = "";
    if($regions != "all"){
        $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
        $rbrh_list = implode("','",$rbrh_alist);
        $farm_query .= " AND `branch_code` IN ('$rbrh_list')";
    }

      $farm_list = ""; $farm_list = implode("','", $farm_code);
    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_query." AND `dflag` = '0' ORDER BY `description` ASC";

    $query = mysqli_query($conn,$sql); $farm_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }
    
    
    $farm_list = implode("','",$farm_alist);
    if($farms != "all"){
        $farm_list = $farms;
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else if($supervisors != "all" && $lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors && $farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else if($supervisors != "all" && $branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else if($supervisors != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else if($lines != "all" && $branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else if($lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else if($branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list;
                }
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    else{
        foreach($farm_code as $fcode){
            if($farm_list == ""){
                $farm_list = $fcode;
            }
            else{
                $farm_list = $farm_list;
            }
        }
        $farm_list_pur = " AND `warehouse` IN ('$farm_list')";
        $farm_list_trs = " AND `towarehouse` IN ('$farm_list')";
    }
    //Hatchery/Supplier Filter
    if($vsectors == "all"){
        $purin_filter = "";
        $stktrin_filter = "";
    }
    else{
        $purin_filter = " AND (`vcode` IN ('$vsectors') OR `warehouse` IN ('$vsectors'))";
        $stktrin_filter = " AND `towarehouse` IN ('$vsectors')";
    }
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/ChickPlacementReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&farm=".$farms."&submit_report=true";
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
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="21" align="center"><?php echo $row['cdetails']; ?></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_chick_placement_ta.php" method="post">
                <?php } else { ?>
                <form action="broiler_chick_placement_ta.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="23">
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
                                    <label>Region</label>
                                    <select name="regions" id="regions" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($regions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != "" && !empty($farm_svr[$scode])){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" style="width:200px;">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Hatchery/Supplier</label>
                                    <select name="vsectors" id="vsectors" class="form-control select2" style="width:200px;">
                                        <option value="all" <?php if($vsectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($vsectors == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option>
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
                    <th id='order_date'>Date</th>
                    <th id='order'>Delivery Note</th>
                    <th id='order'>Branch</th>
                    <th id='order'>Line</th>
                    <th id='order'>Supervisor</th>
                    <th id='order'>Farm Code</th>
                    <th id='order'>Farm Name</th>
                    <th id='order'>Batch</th>
                    <th id='order'>Hatchery</th>
                    <th id='order'>Supplier</th>
                    <th id='order_num'>Quantity Sent</th>
                    <th id='order_num'>Mortality</th>
                    <th id='order_num'>Mort%</th>
                    <th id='order_num'>Shortage</th>
                    <th id='order_num'>Weeks</th>
                    <th id='order_num'>Weeks%</th>
                    <th id='order_num'>Leg Weeks</th>
                    <th id='order_num'>Excess</th>
                    <th id='order_num'>Quantity Received</th>
                    <th id='order_num'>Free Quantity</th>
                    <th id='order_num'>Total Chicks Placed</th>
                    <th id='order_num'>Purchase Price</th>
                    <th id='order_num'>Farm Capacity</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1" id="tbody1">
                <?php
                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `icode` = '$chick_code'".$farm_list_pur."".$purin_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1; $pur_chicks = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['date']."@".$i;
                    $tot_qty = 0; $tot_qty = $row['rcd_qty'] + $row['fre_qty'];
                    $pur_chicks[$key_code] = $row['date']."@".$row['trnum']."@".$row['billno']."@".$row['vcode']."@".$row['rcd_qty']."@".$row['fre_qty']."@".$tot_qty."@".$row['warehouse']."@".$row['farm_batch']."@".$row['snt_qty']."@".$row['mort']."@".$row['shortage']."@".$row['weeks']."@".$row['rate'];
                    $i++;
                }
                $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `code` = '$chick_code'".$farm_list_trs."".$stktrin_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1; $trin_chicks = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['date']."@".$i;
                    $tot_qty = 0; $tot_qty = $row['quantity'] + $row['fre_qty'];
                    $trin_chicks[$key_code] = $row['date']."@".$row['trnum']."@".$row['dcno']."@".$row['fromwarehouse']."@".$row['quantity']."@".$row['fre_qty']."@".$tot_qty."@".$row['towarehouse']."@".$row['to_batch']."@".$row['sent_qty']."@".$row['mort_qty']."@".$row['weak_qty']."@".$row['legw_qty']."@".$row['cull_qty']."@".$row['price']."@".$row['excess_qty']."@".$row['short_qty']."@".$row['vcode'];
                    $i++;
                }
                $hfdate = date("Y-m-d",strtotime($fdate. '-3 days'));

                $sector_list = implode("','",$sector_code);
                $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$hfdate' AND `date` <= '$tdate' AND `icode` = '$chick_code' AND `warehouse` IN ('$sector_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $pur_vcode =  $pur_keyset = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['date']."@".$row['warehouse']."@".$i;
                    $pur_vcode[$key_code] = $row['vcode'];
                    $pur_keyset[$key_code] = $key_code;
                    $i++;
                } $pur_count = sizeof($pur_vcode);

                $sql_record = "SELECT * FROM `broiler_hatchentry` WHERE `hatch_date` >= '$hfdate' AND `hatch_date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `hatch_date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 0; $hatch_vcode = $hatch_keyset = array(); $hatch_count = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['hatch_date']."@".$row['sector_code']."@".$i;
                    $hatch_vcode[$key_code] = $row['vcode'];
                    $hatch_keyset[$key_code] = $key_code;
                    $i++;
                } $hatch_count = sizeof($hatch_vcode);

                $tot_trin_qty = $tot_pur_fqty = $tot_pur_gqty = $tot_pur_qty = $tot_trin_fqty = $tot_trin_gqty = 0;
                $tot_sent_qty = $tot_mort_qty = $tot_shortage_qty = $tot_weeks_qty = $tot_lweeks_qty = $tot_excess_qty = $tot_pur_qty = $tot_pur_fqty = $tot_pur_gqty = $tot_capacity = 0;
                $start_date = strtotime($fdate); $end_date = strtotime($tdate); $pur_count = sizeof($pur_chicks); $trin_count = sizeof($trin_chicks);
                for ($currentDate = $start_date; $currentDate <= $end_date; $currentDate += (86400)) {
                    $prev_date = date("Y-m-d",$currentDate);

                    for($i = 1;$i <= $pur_count;$i++){
                        if($pur_chicks[$prev_date."@".$i] != ""){
                            $pur_details = explode("@",$pur_chicks[$prev_date."@".$i]);
                            if($pur_details[9] != 0 && $pur_details[9] != ""){ $mortp = ($pur_details[10]/$pur_details[9]) * 100;} else { $mortp = 0;}
                            if($pur_details[9] != 0 && $pur_details[9] != ""){ $weeksp = ($pur_details[12]/$pur_details[9]) * 100; } else { $weeksp = 0;}
                            ?>
                            <tr>
                                <td title="Date" style="text-align:left;"><?php echo date("d.m.Y",strtotime($pur_details[0])); ?></td>
                                <td title="Delivery Note" style="text-align:left;"><?php echo $pur_details[2]; ?></td>
                                <td title="Branch" style="text-align:left;"><?php echo $branch_name[$farm_branch[$pur_details[7]]]; ?></td>
                                <td title="Line" style="text-align:left;"><?php echo $line_name[$farm_line[$pur_details[7]]]; ?></td>
                                <td title="Supervisor" style="text-align:left;"><?php echo $supervisor_name[$farm_supervisor[$pur_details[7]]]; ?></td>
                                <td title="Farm Code" style="text-align:left;"><?php echo $farm_ccode[$pur_details[7]]; ?></td>
                                <td title="Farm Name" style="text-align:left;"><?php echo $farm_name[$pur_details[7]]; ?></td>
                                <td title="Batch" style="text-align:left;"><?php echo $batch_name[$pur_details[8]]; ?></td>
                                <td title="Hatchery" style="text-align:left;"></td>
                                <td title="Supplier" style="text-align:left;"><?php echo $sector_name[$pur_details[3]]; ?></td>
                                <td title="Quantity Sent" style="text-align:right;"><?php echo number_format_ind($pur_details[9]); ?></td>
                                <td title="Mortality" style="text-align:right;"><?php echo number_format_ind($pur_details[10] ); ?></td>

                                <td title="Mort%" style="text-align:right;"><?php echo number_format_ind($mortp); ?></td>

                                <td title="Shortage" style="text-align:right;"><?php echo number_format_ind($pur_details[11] ); ?></td>
                                <td title="weeks" style="text-align:right;"><?php echo number_format_ind($pur_details[12]); ?></td>

                                <td title="weeks%" style="text-align:right;"><?php echo number_format_ind($weeksp); ?></td>

                                <td title="Leg weeks" style="text-align:right;"><?php echo number_format_ind(0); ?></td>
                                <td title="Excess" style="text-align:right;"><?php echo number_format_ind(0); ?></td>
                                <td title="Quantity Received" style="text-align:right;"><?php echo number_format_ind($pur_details[4]); ?></td>
                                <td title="Free Quantity" style="text-align:right;"><?php echo number_format_ind($pur_details[5]); ?></td>
                                <td title="Total Chicks Placed" style="text-align:right;"><?php echo number_format_ind($pur_details[6]); ?></td>
                                <td title="Purchase Price" style="text-align:right;"><?php echo number_format_ind($pur_details[14]); ?></td>
                                <td title="Farm Capacity" style="text-align:right;"><?php echo $farm_capacity[$pur_details[7]]; ?></td>
                            </tr>
                            <?php
                            $tot_sent_qty = $tot_sent_qty + (float)$pur_details[9];
                            $tot_mort_qty = $tot_mort_qty + (float)$pur_details[10];
                            $tot_shortage_qty = $tot_shortage_qty + (float)$pur_details[11];
                            $tot_weeks_qty = $tot_weeks_qty + (float)$pur_details[12];
                            $tot_pur_qty = $tot_pur_qty + (float)$pur_details[4];
                            $tot_pur_fqty = $tot_pur_fqty + (float)$pur_details[5];
                            $tot_pur_gqty = $tot_pur_gqty + (float)$pur_details[6];
							$tot_capacity = $tot_capacity + (float)$farm_capacity[$pur_details[7]];
							
                        }
                    }
                
                    for($i = 1;$i <= $trin_count;$i++){
                        if($trin_chicks[$prev_date."@".$i] != ""){
                            $trin_details = explode("@",$trin_chicks[$prev_date."@".$i]);

                            /*$ldate = $lsector = $lincr = "";
                            if($hatch_count > 0){
                                foreach($hatch_keyset as $key1){
                                    $key2 = explode("@",$key1);
                                    $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                    if($hsector == $trin_details[3] && strtotime($hdate) <= strtotime($trin_details[0])){
                                        if($ldate == ""){
                                            $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                        }
                                        else if(strtotime($ldate) < strtotime($hdate)){
                                            $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                        }
                                    }
                                }
                                if($ldate == "" && $lsector == "" && $lincr == ""){ }
                                else{
                                    $hkey = $ldate."@".$lsector."@".$lincr;
                                    if(empty($hatch_vcode[$hkey]) || $hatch_vcode[$hkey] == ""){ $sname = ""; }
                                    else{
                                        $sname = $hatch_vcode[$hkey];
                                    }
                                }
                            }

                            if($sname == ""){
                                if($pur_count > 0){
                                    $ldate = $lsector = $lincr = "";
                                    foreach($pur_keyset as $key1){
                                        $key2 = explode("@",$key1);
                                        $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                        if($hsector == $trin_details[3] && strtotime($hdate) <= strtotime($trin_details[0])){
                                            if($ldate == ""){
                                                $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                            }
                                            else if(strtotime($ldate) < strtotime($hdate)){
                                                $ldate = $hdate; $lsector = $hsector; $lincr = $hicr;
                                            }
                                        }
                                    }
                                    if($ldate == "" && $lsector == "" && $lincr == ""){ }
                                    else{
                                        $hkey = $ldate."@".$lsector."@".$lincr;
                                        if(empty($pur_vcode[$hkey]) || $pur_vcode[$hkey] == ""){ $sname = ""; }
                                        else{
                                            $sname = $pur_vcode[$hkey];
                                        }
                                    }
                                }
                            }*/
                            
                            
                            ?>
                            <tr>
                                <td title="Date" style="text-align:left;"><?php echo date("d.m.Y",strtotime($trin_details[0])); ?></td>
                                <td title="Delivery Note" style="text-align:left;"><?php echo $trin_details[2]; ?></td>
                                <td title="Branch" style="text-align:left;"><?php echo $branch_name[$farm_branch[$trin_details[7]]]; ?></td>
                                <td title="Line" style="text-align:left;"><?php echo $line_name[$farm_line[$trin_details[7]]]; ?></td>
                                <td title="Supervisor" style="text-align:left;"><?php echo $supervisor_name[$farm_supervisor[$trin_details[7]]]; ?></td>
                                <td title="Farm Code" style="text-align:right;"><?php echo $farm_ccode[$trin_details[7]]; ?></td>
                                <td title="Farm Name" style="text-align:left;"><?php echo $farm_name[$trin_details[7]]; ?></td>
                                <td title="Batch" style="text-align:left;"><?php echo $batch_name[$trin_details[8]]; ?></td>
                                <td title="Hatchery" style="text-align:left;"><?php echo $sector_name[$trin_details[3]]; ?></td>
                                <td title="Supplier" style="text-align:left;"><?php echo $sector_name[$trin_details[17]]; ?></td>
                                <td title="Quantity Sent" style="text-align:right;"><?php echo number_format_ind($trin_details[9]); ?></td>
                                <td title="Mortality" style="text-align:right;"><?php echo number_format_ind((float)$trin_details[10]+(float)$trin_details[13]); ?></td>
                                <td title="Mortality" style="text-align:right;"><?php echo number_format_ind((float)$trin_details[10]+(float)$trin_details[13]); ?></td>
                                <td title="Shortage" style="text-align:right;"><?php echo number_format_ind($trin_details[16]); ?></td>
                                <td title="Weeks" style="text-align:right;"><?php echo number_format_ind($trin_details[11]); ?></td>
                                <td title="Leg weeks" style="text-align:right;"><?php echo number_format_ind($trin_details[12]); ?></td>
                                <td title="Excess" style="text-align:right;"><?php echo number_format_ind($trin_details[15]); ?></td>
                                <td title="Quantity Received" style="text-align:right;"><?php echo number_format_ind($trin_details[4]); ?></td>
                                <td title="Free Quantity" style="text-align:right;"><?php echo number_format_ind($trin_details[5]); ?></td>
                                <td title="Total Chicks Placed" style="text-align:right;"><?php echo number_format_ind($trin_details[6]); ?></td>
                                <td title="Purchase Price" style="text-align:right;"><?php echo number_format_ind($trin_details[1]); ?></td>
                                <td title="Farm Capacity" style="text-align:right;"><?php echo number_format_ind($farm_capacity[$trin_details[7]]); ?></td>
                            </tr>
                            <?php
                            $tot_sent_qty = $tot_sent_qty + (float)$trin_details[9];
                            $tot_mort_qty = $tot_mort_qty + (float)$trin_details[10]+(float)$trin_details[13];
                            $tot_shortage_qty = $tot_shortage_qty + (float)$trin_details[16];
                            $tot_weeks_qty = $tot_weeks_qty + (float)$trin_details[11];
                            $tot_lweeks_qty = $tot_lweeks_qty + (float)$trin_details[12];
                            $tot_excess_qty = $tot_excess_qty + (float)$trin_details[15];
                            $tot_trin_qty = $tot_trin_qty + (float)$trin_details[4];
                            $tot_trin_fqty = $tot_trin_fqty + (float)$trin_details[5];
                            $tot_trin_gqty = $tot_trin_gqty + (float)$trin_details[6];
                            $tot_capacity = $tot_capacity + (float)$farm_capacity[$trin_details[7]];
                        }
                    }
                }
                ?>
            </tbody>
            <thead class="thead4">
            <tr >
                <th colspan="10" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_sent_qty),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_mort_qty),2)); ?></th>
                <?php if($tot_sent_qty != 0 && $tot_sent_qty != ""){ $fmortp = ($tot_mort_qty/$tot_sent_qty) * 100; } else { $fmortp = 0;} ?>
                <th style="text-align:right;"><?php echo number_format_ind(round(($fmortp),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_shortage_qty),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_weeks_qty),2)); ?></th>
                <?php if($tot_sent_qty != 0 && $tot_sent_qty != ""){ $fweekp = ($tot_weeks_qty/$tot_sent_qty) * 100; } else { $fweekp = 0;} ?>
                <th style="text-align:right;"><?php echo number_format_ind(round(($fweekp),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_lweeks_qty),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_excess_qty),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_pur_qty + $tot_trin_qty),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_pur_fqty + $tot_trin_fqty),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_pur_gqty + $tot_trin_gqty),2)); ?></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tot_capacity),2)); ?></th>
            </tr>
            </thead>
        <?php
            }
        ?>
        </table>
        <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $l_code = $farm_line[$f_code];
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    //slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    //slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                   // slnos();
                    asc = !asc;
                    })
                });
                
            }
            /*function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }*/

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>