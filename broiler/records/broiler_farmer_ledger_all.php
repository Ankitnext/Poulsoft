<?php
//broiler_farmer_ledger_all.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Farmer Balance Report";
include "header_head.php";
$user_code = $_SESSION['userid'];

$sql='SHOW COLUMNS FROM `broiler_receipts`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("ccn_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_receipts` ADD `ccn_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Credit Note Transaction No.' AFTER `trnum`"; mysqli_query($conn,$sql); }
if(in_array("ccn_amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_receipts` ADD `ccn_amount` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT 'Credit Note Amount' AFTER `ccn_trnum`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];$farmer_to_farm_code[$row['farmer_code']] = $row['code'];
}
$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$line_code = $line_name = $line_branch = array();
$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0'  ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$farmer_code = $farmer_name = $farmer_mobile = array();
$sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['code']; $farmer_name[$row['code']] = $row['name']; $farmer_mobile[$row['code']] = $row['mobile1']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = $farmers = "all";
if(isset($_POST['fdate'])){
  $fdate = date("Y-m-d",strtotime($_POST['fdate']));
  $tdate = date("Y-m-d",strtotime($_POST['tdate']));

  $branches = $_POST['branches'];
  $lines = $_POST['lines'];
  $supervisors = $_POST['supervisors'];
  $farms = $_POST['farms'];
  $farmers = $_POST['farmers'];
}

$farm_list = $farmer_list = "";
if($farmers != "all"){
    foreach($farm_code as $fcode){ if($farm_farmer[$fcode] == $farmers){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    foreach($farm_code as $fcode){ if($farm_farmer[$fcode] == $farmers){ if($farmer_list == ""){ $farmer_list = $farm_farmer[$fcode]; } else{ $farmer_list = $farmer_list."','".$farm_farmer[$fcode]; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
    $farmer_filter1 = " AND vcode IN ('$farmer_list')";
    $farmer_filter2 = " AND ccode IN ('$farmer_list')";
    $farmer_filter3 = " AND warehouse IN ('$farm_list')";
    $farmer_filter4 = " AND (`vcode` IN ('$farm_list') OR `warehouse` IN ('$farm_list'))";
}
else if($farms != "all"){
    $farm_filter = " AND farm_code = '$farms'";
    $farmer_filter1 = " AND vcode IN ('$farm_farmer[$farms]')";
    $farmer_filter2 = " AND ccode IN ('$farm_farmer[$farms]')";
    $farmer_filter3 = " AND warehouse IN ('$farms')";
    $farmer_filter4 = " AND (`vcode` IN ('$farms') OR `warehouse` IN ('$farms'))";
}
else if($supervisors != "all"){
    foreach($farm_code as $fcode){ if($farm_supervisor[$fcode] == $supervisors){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    foreach($farm_code as $fcode){ if($farm_supervisor[$fcode] == $supervisors){ if($farmer_list == ""){ $farmer_list = $farm_farmer[$fcode]; } else{ $farmer_list = $farmer_list."','".$farm_farmer[$fcode]; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
    $farmer_filter1 = " AND vcode IN ('$farmer_list')";
    $farmer_filter2 = " AND ccode IN ('$farmer_list')";
    $farmer_filter3 = " AND warehouse IN ('$farm_list')";
    $farmer_filter4 = " AND (`vcode` IN ('$farm_list') OR `warehouse` IN ('$farm_list'))";
}
else if($lines != "all"){
    foreach($farm_code as $fcode){ if($farm_line[$fcode] == $lines){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    foreach($farm_code as $fcode){ if($farm_line[$fcode] == $lines){ if($farmer_list == ""){ $farmer_list = $farm_farmer[$fcode]; } else{ $farmer_list = $farmer_list."','".$farm_farmer[$fcode]; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
    $farmer_filter1 = " AND vcode IN ('$farmer_list')";
    $farmer_filter2 = " AND ccode IN ('$farmer_list')";
    $farmer_filter3 = " AND warehouse IN ('$farm_list')";
    $farmer_filter4 = " AND (`vcode` IN ('$farm_list') OR `warehouse` IN ('$farm_list'))";
}
else if($branches != "all"){
    foreach($farm_code as $fcode){ if($farm_branch[$fcode] == $branches){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    foreach($farm_code as $fcode){ if($farm_branch[$fcode] == $branches){ if($farmer_list == ""){ $farmer_list = $farm_farmer[$fcode]; } else{ $farmer_list = $farmer_list."','".$farm_farmer[$fcode]; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
    $farmer_filter1 = " AND vcode IN ('$farmer_list')";
    $farmer_filter2 = " AND ccode IN ('$farmer_list')";
    $farmer_filter3 = " AND warehouse IN ('$farm_list')";
    $farmer_filter4 = " AND (`vcode` IN ('$farm_list') OR `warehouse` IN ('$farm_list'))";
}
else{
    foreach($farm_code as $fcode){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } }
    foreach($farm_code as $fcode){
        if($farmer_list == ""){ $farmer_list = $farm_farmer[$fcode]; } else{ $farmer_list = $farmer_list."','".$farm_farmer[$fcode]; } 
    }
    $farm_filter = " AND farm_code IN ('$farm_list')";
    $farmer_filter1 = " AND vcode IN ('$farmer_list')";
    $farmer_filter2 = " AND ccode IN ('$farmer_list')";
    $farmer_filter3 = " AND warehouse IN ('$farm_list')";
    $farmer_filter4 = " AND (`vcode` IN ('$farm_list') OR `warehouse` IN ('$farm_list'))";
}

if(isset($_POST['submit_report']) == true){
   
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_branch = $branch_name[$_POST['branches']]; if ( $export_branch == "") {  $export_branch = "All"; }
    $export_line = $line_name[$_POST['lines']]; if ( $export_line == "") {  $export_line = "All"; }
    $export_supervisor = $supervisor_name[$_POST['supervisors']]; if ( $export_supervisor == "") {  $export_supervisor = "All"; }
    $export_farm = $farm_name[$_POST['farms']]; if ( $export_farm == "") {  $export_farm = "All"; }
    $export_farmer = $farmer_name[$_POST['farmers']]; if ( $export_farmer == "") {  $export_farmer = "All"; }

	if ($export_fdate == $export_tdate)
    {$filename = "Farmer Balance_report_".$export_tdate; }
    else {
    $filename = "Farmer Balance_report_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];

}

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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Farmer Balance Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_farmer_ledger_all.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="form-group" style="width:190px;">
                                    <label for="branches">Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:190px;">
                                    <label for="lines">Line</label>
                                    <select name="lines" id="lines" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:190px;">
                                    <label for="supervisors">Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:300px;">
                                    <label for="farms">Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" style="width:290px;">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:300px;">
                                    <label for="farmers">Farmer</label>
                                    <select name="farmers" id="farmers" class="form-control select2" style="width:290px;">
                                        <option value="all" <?php if($farmers == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farmer_code as $fcode){ if($farmer_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farmers == $fcode){ echo "selected"; } ?>><?php echo $farmer_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Farmer Balance_report_','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
            <table>
            <table id="main_body" class="tbl" align="center"  style="width:1300px;">
            
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                    <th colspan="7" align="center"><?php echo $row['cdetails']; ?><h5>Farmer History Report</h5></th>
                </tr>
            <?php } ?>

            
            <tr>
                       
                       <th colspan="7">
                                   <div class="row">
                                       <div class="m-2 form-group">
                                           <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Branch: <?php echo $export_branch; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Line: <?php echo $export_line; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Supervisor: <?php echo $export_supervisor; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Farm: <?php echo $export_farm; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Farmer: <?php echo $export_farmer; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label><br/></label>
                   
                                       </div>
                                       
                               </th>
                           
                       </tr>
       
                       </thead>
            
            <br/>
            <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                <br/>
                </div>
            
            </div>
            
            <thead class="thead3" align="center">
                <tr align="center">
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Mobile No</th>
                    <th rowspan="2">Opening Balance</th>
                    <th colspan="3">Selected Period</th>
                    <th rowspan="2">Balance</th>
                </tr>
                <tr align="center">
                    <th>Cr Amount</th>
                    <th>Dr Amount</th>
                    <th>B/w days balance</th>
                </tr>
            </thead>
            <?php
                        if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    $old_inv = "";
                    $opn_farmergc = $opn_farmer_tds = $opening_oded = $opn_sale_amt = $opn_rtn_amt = $opn_receipts = $opn_payments = $opn_ccn = $opn_cdn = array();
                    $btw_farmergc = $btw_farmer_tds = $between_oded = $btw_sale_amt = $btw_rtn_amt = $btw_receipts = $btw_payments = $btw_ccn = $btw_cdn = $fmr_acode = array();

                    $sql_record = "SELECT SUM(amount_payable) as pamt,SUM(tds_amt) as tds_amt,SUM(other_deduction) as other_deduction,farm_code as fcode FROM `broiler_rearingcharge` WHERE `date` < '$fdate'".$farm_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_code` ORDER BY `farm_code` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $fmr_key = ""; $fmr_key = $row['fcode']; $fmr_acode[$fmr_key] = $fmr_key;
                            $opn_farmergc[$fmr_key] += (float)$row['pamt'];
                            $opn_farmer_tds[$fmr_key] += (float)$row['tds_amt'];
                            $opening_oded[$fmr_key] += (float)$row['other_deduction'];
                        }
                    }
                    
                    $sql_record = "SELECT SUM(amount) as pamt,warehouse as fcode FROM `broiler_payments` WHERE `date` < '$fdate'".$farmer_filter2."".$farmer_filter3." AND `vtype` IN ('FarmerPay') AND `active` = '1' AND `dflag` = '0' GROUP BY `ccode`,`warehouse` ORDER BY `ccode` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opn_payments[$row['fcode']] += (float)$row['pamt']; $fmr_acode[$row['fcode']] = $row['fcode'];
                        }
                    }
                    
                    $sql_record = "SELECT SUM(amount) as pamt,warehouse as fcode FROM `broiler_receipts` WHERE `date` < '$fdate'".$farmer_filter2." AND `vtype` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' GROUP BY `ccode`,`warehouse` ORDER BY `ccode` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opn_receipts[$row['fcode']] += (float)$row['pamt']; $fmr_acode[$row['fcode']] = $row['fcode'];
                        }
                    }
                    
                    $sql_record = "SELECT SUM(amount) as pamt,warehouse as fcode,crdr FROM `broiler_crdrnote` WHERE `date` < '$fdate'".$farmer_filter1."".$farmer_filter3." AND `type` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' GROUP BY `vcode`,`warehouse`,`crdr` ORDER BY `vcode` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            //if($row['crdr'] == "Credit"){ $opn_ccn[$farmer_to_farm_code[$row['fcode']]] += (float)$row['pamt']; } else{ $opn_cdn[$farmer_to_farm_code[$row['fcode']]] += (float)$row['pamt']; } $fmr_acode[$farmer_to_farm_code[$row['fcode']]] = $row['fcode'];
                            if($row['crdr'] == "Credit"){ $opn_ccn[$row['fcode']] += (float)$row['pamt']; } else{ $opn_cdn[$row['fcode']] += (float)$row['pamt']; } $fmr_acode[$row['fcode']] = $row['fcode'];
                        }
                    }

                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` < '$fdate'".$farmer_filter4." AND (`sale_type` IN ('FarmerSale') OR `sale_type` IN ('FormMBSale')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                if(empty($farm_code[$row['warehouse']]) || $farm_code[$row['warehouse']] == ""){
                                    $key = $row['vcode'];
                                }
                                else{
                                    $key = $row['warehouse'];
                                }
                                $opn_sale_amt[$key] += (float)$row['finl_amt'];
                                $old_inv = $row['trnum'];
                                $fmr_acode[$key] = $key;
                            }
                        }
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate'".$farmer_filter3." AND `type` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $opn_rtn_amt[$row['warehouse']] += (float)$row['amount'];
                            $fmr_acode[$row['warehouse']] = $row['warehouse'];
                        }
                    }
                    
                    //Between Dates
                    $sql_record = "SELECT SUM(amount_payable) as pamt,SUM(tds_amt) as tds_amt,SUM(other_deduction) as other_deduction,farm_code as fcode FROM `broiler_rearingcharge` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farm_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_code` ORDER BY `farm_code` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $fmr_key = ""; $fmr_key = $row['fcode']; $fmr_acode[$fmr_key] = $fmr_key;
                            $btw_farmergc[$fmr_key] += (float)$row['pamt'];
                            $btw_farmer_tds[$fmr_key] += (float)$row['tds_amt'];
                            $between_oded[$fmr_key] += (float)$row['other_deduction'];
                        }
                    }
                    
                    $sql_record = "SELECT SUM(amount) as pamt,warehouse as fcode FROM `broiler_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farmer_filter2."".$farmer_filter3." AND `vtype` IN ('FarmerPay') AND `active` = '1' AND `dflag` = '0' GROUP BY `ccode`,`warehouse` ORDER BY `ccode` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $btw_payments[$row['fcode']] += (float)$row['pamt']; $fmr_acode[$row['fcode']] = $row['fcode'];
                        }
                    }
                    
                    $sql_record = "SELECT SUM(amount) as pamt,warehouse as fcode FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farmer_filter2." AND `vtype` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' GROUP BY `ccode`,`warehouse` ORDER BY `ccode`,`warehouse` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $btw_receipts[$row['fcode']] += (float)$row['pamt']; $fmr_acode[$row['fcode']] = $row['fcode'];
                        }
                    }
                    
                    $sql_record = "SELECT SUM(amount) as pamt,warehouse as fcode,crdr FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farmer_filter1."".$farmer_filter3." AND `type` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' GROUP BY `vcode`,`warehouse`,`crdr` ORDER BY `vcode` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            //if($row['crdr'] == "Credit"){ $btw_ccn[$farmer_to_farm_code[$row['fcode']]] += (float)$row['pamt']; } else{ $btw_cdn[$farmer_to_farm_code[$row['fcode']]] += (float)$row['pamt']; } $fmr_acode[$farmer_to_farm_code[$row['fcode']]] = $row['fcode'];
                            if($row['crdr'] == "Credit"){ $btw_ccn[$row['fcode']] += (float)$row['pamt']; } else{ $btw_cdn[$row['fcode']] += (float)$row['pamt']; } $fmr_acode[$row['fcode']] = $row['fcode'];
                        }
                    }



                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farmer_filter4." AND (`sale_type` IN ('FarmerSale') OR `sale_type` IN ('FormMBSale')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_inv != $row['trnum']){
                                if(empty($farm_code[$row['warehouse']]) || $farm_code[$row['warehouse']] == ""){
                                    $key = $row['vcode'];
                                }
                                else{
                                    $key = $row['warehouse'];
                                }
                                $btw_sale_amt[$key] += (float)$row['finl_amt'];
                                $old_inv = $row['trnum'];
                                $fmr_acode[$key] = $key;
                            }
                        }
                    }
                    
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farmer_filter3." AND `type` IN ('Farmer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                    if($transaction_count > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            $btw_rtn_amt[$row['warehouse']] += (float)$row['amount'];
                            $fmr_acode[$row['warehouse']] = $row['warehouse'];
                        }
                    }

                    $frm_list = implode("','", $fmr_acode); $frm_code = array();
                    $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$frm_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $frm_code[$row['code']] = $row['code']; }

                    //Display Calculations
                    foreach($frm_code as $vcode){
                        if(empty($opn_farmergc[$vcode]) || $opn_farmergc[$vcode] == 0 || $opn_farmergc[$vcode] == "0.00" || $opn_farmergc[$vcode] == ""){ $opn_farmergc[$vcode] = 0; }
                        if(empty($opn_farmer_tds[$vcode]) || $opn_farmer_tds[$vcode] == 0 || $opn_farmer_tds[$vcode] == "0.00" || $opn_farmer_tds[$vcode] == ""){ $opn_farmer_tds[$vcode] = 0; }
                        if(empty($opening_oded[$vcode]) || $opening_oded[$vcode] == 0 || $opening_oded[$vcode] == "0.00" || $opening_oded[$vcode] == ""){ $opening_oded[$vcode] = 0; }
                        if(empty($opn_receipts[$vcode]) || $opn_receipts[$vcode] == 0 || $opn_receipts[$vcode] == "0.00" || $opn_receipts[$vcode] == ""){ $opn_receipts[$vcode] = 0; }
                        if(empty($opn_payments[$vcode]) || $opn_payments[$vcode] == 0 || $opn_payments[$vcode] == "0.00" || $opn_payments[$vcode] == ""){ $opn_payments[$vcode] = 0; }
                        if(empty($opn_ccn[$vcode]) || $opn_ccn[$vcode] == 0 || $opn_ccn[$vcode] == "0.00" || $opn_ccn[$vcode] == ""){ $opn_ccn[$vcode] = 0; }
                        if(empty($opn_cdn[$vcode]) || $opn_cdn[$vcode] == 0 || $opn_cdn[$vcode] == "0.00" || $opn_cdn[$vcode] == ""){ $opn_cdn[$vcode] = 0; }
                        if(empty($opn_sale_amt[$vcode]) || $opn_sale_amt[$vcode] == 0 || $opn_sale_amt[$vcode] == "0.00" || $opn_sale_amt[$vcode] == ""){ $opn_sale_amt[$vcode] = 0; }
                        if(empty($opn_rtn_amt[$vcode]) || $opn_rtn_amt[$vcode] == 0 || $opn_rtn_amt[$vcode] == "0.00" || $opn_rtn_amt[$vcode] == ""){ $opn_rtn_amt[$vcode] = 0; }

                        if(empty($btw_farmergc[$vcode]) || $btw_farmergc[$vcode] == 0 || $btw_farmergc[$vcode] == "0.00" || $btw_farmergc[$vcode] == ""){ $btw_farmergc[$vcode] = 0; }
                        if(empty($btw_farmer_tds[$vcode]) || $btw_farmer_tds[$vcode] == 0 || $btw_farmer_tds[$vcode] == "0.00" || $btw_farmer_tds[$vcode] == ""){ $btw_farmer_tds[$vcode] = 0; }
                        if(empty($between_oded[$vcode]) || $between_oded[$vcode] == 0 || $between_oded[$vcode] == "0.00" || $between_oded[$vcode] == ""){ $between_oded[$vcode] = 0; }
                        if(empty($btw_receipts[$vcode]) || $btw_receipts[$vcode] == 0 || $btw_receipts[$vcode] == "0.00" || $btw_receipts[$vcode] == ""){ $btw_receipts[$vcode] = 0; }
                        if(empty($btw_payments[$vcode]) || $btw_payments[$vcode] == 0 || $btw_payments[$vcode] == "0.00" || $btw_payments[$vcode] == ""){ $btw_payments[$vcode] = 0; }
                        if(empty($btw_ccn[$vcode]) || $btw_ccn[$vcode] == 0 || $btw_ccn[$vcode] == "0.00" || $btw_ccn[$vcode] == ""){ $btw_ccn[$vcode] = 0; }
                        if(empty($btw_cdn[$vcode]) || $btw_cdn[$vcode] == 0 || $btw_cdn[$vcode] == "0.00" || $btw_cdn[$vcode] == ""){ $btw_cdn[$vcode] = 0; }
                        if(empty($btw_sale_amt[$vcode]) || $btw_sale_amt[$vcode] == 0 || $btw_sale_amt[$vcode] == "0.00" || $btw_sale_amt[$vcode] == ""){ $btw_sale_amt[$vcode] = 0; }
                        if(empty($btw_rtn_amt[$vcode]) || $btw_rtn_amt[$vcode] == 0 || $btw_rtn_amt[$vcode] == "0.00" || $btw_rtn_amt[$vcode] == ""){ $btw_rtn_amt[$vcode] = 0; }
                      
                        /*Opening Balance Calculations */
                        $current_opening_amt = 0;
                        $current_opening_amt = (($opn_farmergc[$vcode] + $opn_receipts[$vcode] + $opn_ccn[$vcode] + $opn_rtn_amt[$vcode]) - ($opn_farmer_tds[$vcode] + $opening_oded[$vcode] + $opn_payments[$vcode] + $opn_sale_amt[$vcode] + $opn_cdn[$vcode]));
                        
                        /*Between days Total Amount */
                        $current_sale_amt = 0;
                        $current_sale_amt = $btw_farmergc[$vcode] + $btw_receipts[$vcode] + $btw_ccn[$vcode] + $btw_rtn_amt[$vcode];
                        //echo "<br/>$current_sale_amt = $btw_farmergc[$vcode] + $btw_receipts[$vcode] + $btw_ccn[$vcode] + $btw_rtn_amt[$vcode];$vcode<br/>";

                        /*Between days Total Receipt */
                        $current_rct_amt = 0;
                        $current_rct_amt = $btw_farmer_tds[$vcode] + $between_oded[$vcode] + $btw_payments[$vcode] + $btw_sale_amt[$vcode] + $btw_cdn[$vcode];
                        //echo "<br/>$current_rct_amt = $btw_farmer_tds[$vcode] + $btw_payments[$vcode] + $btw_sale_amt[$vcode] + $btw_cdn[$vcode];";

                       
                        /*Between Days Balance Amount */
                        $current_Balance_amt = 0;
                        $current_Balance_amt = $current_sale_amt - $current_rct_amt;

                        /*Final Customer Balance */
                        $final_customer_balance_amt = 0;
                        $final_customer_balance_amt = $current_opening_amt + $current_Balance_amt; 

                        echo "<tr>";
                        echo "<td>".$farm_name[$vcode]."</td>";
                        echo "<td>".$farmer_mobile[$farm_farmer[$vcode]]."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($current_opening_amt)."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($current_sale_amt)."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($current_rct_amt)."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($current_Balance_amt)."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($final_customer_balance_amt)."</td>";
                        echo "</tr>";

                        /*Final Total */
                        $final_opening_amt = $final_opening_amt + $current_opening_amt;
                        $final_between_sale_amt = $final_between_sale_amt + $current_sale_amt;
                        $final_between_rct_amt = $final_between_rct_amt + $current_rct_amt;
                        $final_between_balance_amt = $final_between_balance_amt + $current_Balance_amt;
                        $final_all_customer_balance_amt = $final_all_customer_balance_amt + $final_customer_balance_amt;
                    }
                    echo "<tr class='thead3'>";
					echo "<td style='text-align:center;font-weight:bold;' colspan='2'>Total</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_opening_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_sale_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_rct_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_between_balance_amt)."</td>";
                    echo "<td style='text-align:right;font-weight:bold;'>".number_format_ind($final_all_customer_balance_amt)."</td>";
                    echo "</tr>";
                ?>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
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