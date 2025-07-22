<?php
//broiler_account_balance_sheet_report2.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Account Balance Sheet Report";
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

//Check for table
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'location_branch';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.location_branch LIKE poulso6_admin_broiler_broilermaster.location_branch;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
 
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Fetching Details for C&S Calculations
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` = 'S&C' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cas_ccode = $cas_gcode = array();
while($row = mysqli_fetch_assoc($query)){ $cas_ccode[$row['code']] = $row['code']; $cas_gcode[$row['groupcode']] = $row['groupcode']; }
$grp_list = implode("','", $cas_gcode);
$sql = "SELECT * FROM `main_groups` WHERE `code` IN ('$grp_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cas_ccac = $cas_scac = array();
while($row = mysqli_fetch_assoc($query)){ $cas_ccac[$row['cus_controller_code']] = $row['cus_controller_code']; $cas_scac[$row['sup_controller_code']] = $row['sup_controller_code']; }

$fdate = $tdate = date("Y-m-d"); $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $farms = $sectors = $_POST['sectors'];
    $sector_array_list = array();
    $scode = $_POST["sectors"];

    if($sector_list == ""){ $sector_list = $scode; } else{ $sector_list = $sector_list . "','" . $scode; }
    if($sector_item_list == ""){ $sector_item_list = $scode; } else { $sector_item_list = $sector_item_list . "@" . $scode; }
    $sector_array_list[$scode] = $scode;
    if($scode == "all"){ $sec_all_flag = 1; } else { }
        
    if($sec_all_flag == 1){
        $sector_filter = "";
        $slist_count = 1;
        $sec_code = "all";
        $acc_link = "https://".$_SERVER['HTTP_HOST']."/records/broiler_account_ledger.php?fdate=".$fdate."&tdate=".$tdate."&sectors=".$sec_code."&coas=";
    }
    else{
        $sector_filter = " AND `location` IN ('$sector_list')";
        
        if($slist_count == 1){
            $sec_code = $sector_list;
            $acc_link = "https://".$_SERVER['HTTP_HOST']."/records/broiler_account_ledger.php?fdate=".$fdate."&tdate=".$tdate."&sectors=".$sec_code."&coas=";
        }
        else{
            $slist_count = 0;
            $acc_link = "javascript:void(0)";
        }
    }

    if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `location` = '$sectors'"; }
    $excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/ItemRerateMaster-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors."&farm=".$farms;
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
        <table class="tbl" style="width:auto;" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="4" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Account Balance Sheet Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_account_balance_sheet_report2.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="6">
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
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group"><br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th colspan="3">Liability/Capital</th>
                    <th colspan="3">Asset</th>
                </tr>
                <tr align="center">
                    <th>Code</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_POST['submit_report']) == true){
                $acc_list = $sch_list = "";
                $asset_code = $asset_name = $liability_code = $liability_name = $capital_code = $capital_name = $coa_code = $coa_name = array();
                
                $sql = "SELECT * FROM `acc_types` WHERE `description` LIKE '%Asset%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $asset_type = $row['code']; }
                $sql = "SELECT * FROM `acc_types` WHERE `description` LIKE '%Liability%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $liability_type = $row['code']; }
                $sql = "SELECT * FROM `acc_types` WHERE `description` LIKE '%Capital%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $capital_type = $row['code']; }

                $sql = "SELECT * FROM `acc_schedules` WHERE `subtype` IN ('$asset_type') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $asset_schedule_code[$row['code']] = $row['code'];
                    $asset_schedule_name[$row['code']] = $row['description'];
                    if($sch_list == ""){ $sch_list = $row['code']; }else{ $sch_list = $sch_list."','".$row['code']; }
                }
                $sql = "SELECT * FROM `acc_schedules` WHERE `subtype` IN ('$liability_type') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $liability_schedule_code[$row['code']] = $row['code'];
                    $liability_schedule_name[$row['code']] = $row['description'];
                    if($sch_list == ""){ $sch_list = $row['code']; }else{ $sch_list = $sch_list."','".$row['code']; }
                }
                $sql = "SELECT * FROM `acc_schedules` WHERE `subtype` IN ('$capital_type') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $capital_schedule_code[$row['code']] = $row['code'];
                    $capital_schedule_name[$row['code']] = $row['description'];
                    if($sch_list == ""){ $sch_list = $row['code']; }else{ $sch_list = $sch_list."','".$row['code']; }
                }

                $sql = "SELECT * FROM `acc_coa` WHERE `type` IN ('$asset_type','$liability_type','$capital_type') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($row['type'] == $asset_type){ $asset_code[$row['code']] = $row['code']; $asset_name[$row['code']] = $row['description']; $asset_schedule[$row['code']] = $row['schedules']; }
                    else if($row['type'] == $liability_type){ $liability_code[$row['code']] = $row['code']; $liability_name[$row['code']] = $row['description']; $liability_schedule[$row['code']] = $row['schedules']; }
                    else if($row['type'] == $capital_type){ $capital_code[$row['code']] = $row['code']; $capital_name[$row['code']] = $row['description']; $capital_schedule[$row['code']] = $row['schedules']; }
                    else{ }
                    $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description'];
                    if($acc_list == ""){ $acc_list = $row['code']; }else{ $acc_list = $acc_list."','".$row['code']; }
                }
                $sql = "SELECT * FROM `acc_coa` WHERE `schedules` IN ('$sch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($row['type'] == $asset_type){ $asset_code[$row['code']] = $row['code']; $asset_name[$row['code']] = $row['description']; $asset_schedule[$row['code']] = $row['schedules']; }
                    else if($row['type'] == $liability_type){ $liability_code[$row['code']] = $row['code']; $liability_name[$row['code']] = $row['description']; $liability_schedule[$row['code']] = $row['schedules']; }
                    else if($row['type'] == $capital_type){ $capital_code[$row['code']] = $row['code']; $capital_name[$row['code']] = $row['description']; $capital_schedule[$row['code']] = $row['schedules']; }
                    else{ }
                    $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description'];
                    if($acc_list == ""){ $acc_list = $row['code']; }else{ $acc_list = $acc_list."','".$row['code']; }
                }

                //Stock Management
                $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $iac_seatch_list = "";
                while($row = mysqli_fetch_assoc($query)){
                    if($iac_seatch_list == ""){ $iac_seatch_list = $row['iac']; } else{ $iac_seatch_list = $iac_seatch_list."','".$row['iac']; }
                    $item_account[$row['code']] = $row['iac'];
                }
                
                $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$tdate' AND `coa_code` IN ('$iac_seatch_list')".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql); $csq = $csp = $csa = $item_list = $sector_list = array();
                while($row = mysqli_fetch_assoc($query)){
                    if($row['item_code'] != ""){
                        $key = $row['location']."@".$row['item_code'];
                        if($row['crdr'] == "CR"){
                            $csq[$key] = (float)$csq[$key] - (float)$row['quantity'];
                            $csa[$key] = (float)$csa[$key] - (float)$row['amount'];
                        }
                        else if($row['crdr'] == "DR"){
                            $csq[$key] = (float)$csq[$key] + (float)$row['quantity'];
                            $csa[$key] = (float)$csa[$key] + (float)$row['amount'];
                            if(!empty($csq[$key]) && $csq[$key] > 0 && !empty($csa[$key]) && $csa[$key] > 0){
                                $csp[$key] = round(((float)$csa[$key] / (float)$csq[$key]),2);
                            }
                            else{
                                $csp[$key] = 0;
                            }
                        }
                        else{ }

                        $item_list[$row['item_code']] = $row['item_code'];
                        $sector_list[$row['location']] = $row['location'];
                    }
                }

                $acc_cr_amount = $acc_dr_amount = array();
                foreach($sector_list as $slist){
                    foreach($item_list as $ilist){
                        $key = $slist."@".$ilist;
                        $cats = $item_category[$item_code[$ilist]]; $key2 = $item_account[$cats];
                        $acc_dr_amount[$key2] = ((float)$acc_dr_amount[$key2] + ((float)$csa[$key]));
                        $acc_cr_amount[$key2] = 0;
                    }
                }

                //Other Accounts management
                $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$tdate'".$sector_filter." AND `coa_code` IN ('$acc_list') AND `coa_code` NOT IN ('$iac_seatch_list') AND `active` = 1 AND `dflag` = 0 ORDER BY `date`,`id` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($row['crdr'] == "CR"){
                        $acc_cr_amount[$row['coa_code']] = $acc_cr_amount[$row['coa_code']] + $row['amount'];
                    }
                    if($row['crdr'] == "DR"){
                        $acc_dr_amount[$row['coa_code']] = $acc_dr_amount[$row['coa_code']] + $row['amount'];
                    }
                    else{}
                }

                $acc_heading_flag = array();
                $html_col1 = $html_col2 = $html_col3 = $html_col4 = $html_col5 = $html_col6 = array();
                $i = $j = $final_lc_total = $final_asset_total = $ng_asset_total = 0;

                foreach($liability_schedule_code as $lslist){
                    if($lslist == ""){ }
                    else{
                        $acc_heading_flag[$lslist] = $total_amt = 0;
                        foreach($liability_code as $llist){
                            if($lslist != "" && $lslist == $liability_schedule[$llist]){
                                $curr_amount = (float)$acc_cr_amount[$llist] - (float)$acc_dr_amount[$llist];
                                if(number_format_ind($curr_amount) != "0.00"){
                                    if($acc_heading_flag[$lslist] == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>".$liability_schedule_name[$lslist]."</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $acc_heading_flag[$lslist] = 1; $i++; }
                                    $html_col1[$i] = "<th style='text-align:enter;'><a href = '".$acc_link."".$liability_code[$llist]."' target='_BLANK'>".$liability_code[$llist]."</a></th>";
                                    if($liability_schedule_name[$lslist] == "Trade Payable"){
                                        if($liability_name[$llist] == "Sundry Creditors - Liability"){
                                            //echo "<br/>".(float)$acc_cr_amount[$llist]."@".(float)$acc_dr_amount[$llist]."@".number_format_ind($curr_amount);
                                            $html_col2[$i] = "<td style='text-align:left;'><a href=/records/broiler_supplier_ledger_all.php?submit_report=true&fdate=". date("d.m.Y",strtotime($fdate)) ."&tdate=". date("d.m.Y",strtotime($tdate))."&vendor_group=". $liability_code[$llist] ." target='_blank'>".$liability_name[$llist]."</a></td>";
                                        }
                                        else{
                                            $html_col2[$i] = "<td style='text-align:left;'>".$liability_name[$llist]."</td>";
                                        }
                                        
                                    
                                    }else{
                                        $html_col2[$i] = "<td style='text-align:left;'>".$liability_name[$llist]."</a></td>";
                                    
                                    }
                                    $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($curr_amount)."</td>"; $i++;
                                    
                                    if((float)$curr_amount >= 0){
                                        $total_amt += (float)$curr_amount;
                                        $final_lc_total += (float)$curr_amount;
                                    }
                                    else{
                                        $t1 = 0; $t1 = ((float)$curr_amount * -1);
                                        $total_amt = $total_amt - (float)$t1;
                                        $final_lc_total -= (float)$t1;
                                    }
                                }
                            }
                        }
                        if($acc_heading_flag[$lslist] == 1){
                            $html_col1[$i] = "<th colspan='2' class='thead4'>".$liability_schedule_name[$lslist]." Total</th>";
                            $html_col2[$i] = "";
                            $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round($total_amt,2))."</th>";
                            $acc_heading_flag[$lslist] = 2;
                            $i++; $total_amt = 0;
                        }
                    }
                }
                foreach($capital_schedule_code as $lslist){
                    if($lslist == ""){ }
                    else{
                        $acc_heading_flag[$lslist] = $total_amt = 0;
                        foreach($capital_code as $llist){
                            if($lslist != "" && $lslist == $capital_schedule[$llist]){
                                $curr_amount = (float)$acc_cr_amount[$llist] - (float)$acc_dr_amount[$llist];
                                if((float)$curr_amount >= 0){
                                if(number_format_ind($curr_amount) != "0.00"){
                                    if($acc_heading_flag[$lslist] == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>".$capital_schedule_name[$lslist]."</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $acc_heading_flag[$lslist] = 1; $i++; }
                                    $html_col1[$i] = "<th style='text-align:enter;'>".$capital_code[$llist]."</th>";
                                    
                                    $html_col2[$i] = "<td style='text-align:left;'>".$capital_name[$llist]."</td>";
                                    $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($curr_amount)."</td>"; $i++;
                                    
                                    if((float)$curr_amount >= 0){
                                        $total_amt += (float)$curr_amount;
                                        $final_lc_total += (float)$curr_amount;
                                    }
                                    else{
                                        //$t1 = 0; $t1 = abs((float)number_format_ind($curr_amount));
                                        $t1 = 0; $t1 = ((float)$curr_amount * -1);
                                        $total_amt = $total_amt - (float)$t1;
                                        $final_lc_total -= (float)$t1;
                                        
                                    }
                                }
                            }
                            }
                        }
                        if($acc_heading_flag[$lslist] == 1){
                            $html_col1[$i] = "<th colspan='2' class='thead4'>".$capital_schedule_name[$lslist]." Total</th>";
                            $html_col2[$i] = "";
                            $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round($total_amt,2))."</th>";
                            $acc_heading_flag[$lslist] = 2;
                            $i++; $total_amt = 0;
                        }
                    }
                }
                $acc_heading_flag = array(); $total_amt = 0;
                foreach($asset_schedule_code as $lslist){
                    $total_amt = 0; $title = "";
                    if($lslist == ""){ }
                    else{
                        $acc_heading_flag[$lslist] = $total_amt = 0;
                        foreach($asset_code as $llist){
                            if($lslist != "" && $lslist == $asset_schedule[$llist]){
                                $curr_amount = round(((float)$acc_dr_amount[$llist] - (float)$acc_cr_amount[$llist]),2);
                                if(number_format_ind($curr_amount) != "0.00"){
                                    if($acc_heading_flag[$lslist] == 0){ $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>".$asset_schedule_name[$lslist]."</th>"; $html_col5[$j] = ""; $html_col6[$j] = ""; $acc_heading_flag[$lslist] = 1; $j++; }
                                    $html_col4[$j] = "<th style='text-align:enter;'><a href = '".$acc_link."".$asset_code[$llist]."' target='_BLANK'>".$asset_code[$llist]."</a></th>"; //."-".$lslist
                                    if($asset_schedule_name[$lslist] == "Trade Receivables"){
                                        if($asset_name[$llist] == "Sundry Debtors - Asset"){
                                            $html_col5[$j] = "<td style='text-align:left;'><a href=/records/broiler_customer_ledger_all.php?submit_report=true&fdate=". date("d.m.Y",strtotime($fdate)) ."&tdate=". date("d.m.Y",strtotime($tdate))."&vendor_group=". $asset_code[$llist] ." target='_blank'>".$asset_name[$llist]."</a></td>";
                                        }
                                        else{
                                            $html_col5[$j] = "<td style='text-align:left;'>".$asset_name[$llist]."</td>";
                                        }
                                        
                                    
                                    }else{
                                        $html_col5[$j] = "<td style='text-align:left;'>".$asset_name[$llist]."</a></td>";
                                    
                                    }
                                    $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($curr_amount)."</td>"; $j++;
                                    $title .= "&#010;".number_format_ind($curr_amount);
                                    if((float)$curr_amount >= 0){
                                        $total_amt += (float)$curr_amount;
                                        $final_asset_total += (float)$curr_amount;
                                    }
                                    else{
                                        //$t1 = 0; $t1 = abs((float)number_format_ind($curr_amount));
                                        //$t1 = 0; $t1 = ((float)$curr_amount * -1);
                                        /*$total_amt = $total_amt + (float)$curr_amount;
                                        $final_asset_total = $final_asset_total + (float)$curr_amount;*/

                                        $t1 = 0; $t1 = abs((float)number_format_ind($curr_amount));
                                        //$t1 = 0; $t1 = ((float)$curr_amount * -1);
                                        $total_amt = $total_amt - (float)$t1;
                                        $final_asset_total -= (float)$t1;
                                    }
                                }
                            }
                        }
                        if(number_format_ind($total_amt) != "0.00"){
                        $html_col4[$j] = "<th colspan='2' class='thead4'>".$asset_schedule_name[$lslist]." Total</th>";
                        $html_col5[$j] = "";
                        $html_col6[$j] = "<th class='thead4' style='text-align:right;' title='".$title."'>".number_format_ind($total_amt)."</th>";
                        $j++; $total_amt = 0;
                        }
                    }
                }
                foreach($capital_schedule_code as $lslist){
                    if($lslist == ""){ }
                    else{
                        $acc_heading_flag[$lslist] = $total_amt = 0;
                        foreach($capital_code as $llist){
                            if($lslist != "" && $lslist == $capital_schedule[$llist]){
                                $curr_amount = (float)$acc_dr_amount[$llist] - (float)$acc_cr_amount[$llist];
                                if((float)$curr_amount >= 0){
                                if(number_format_ind($curr_amount) != "0.00"){
                                    if($acc_heading_flag[$lslist] == 0){ $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>".$capital_schedule_name[$lslist]."</th>"; $html_col5[$j] = ""; $html_col6[$j] = ""; $acc_heading_flag[$lslist] = 1; $j++; }
                                    $html_col4[$j] = "<th style='text-align:enter;'>".$capital_code[$llist]."</th>";
                                    
                                    $html_col5[$j] = "<td style='text-align:left;'>".$capital_name[$llist]."</td>";
                                    $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($curr_amount)."</td>"; $j++;
                                    
                                    if((float)$curr_amount >= 0){
                                        $total_amt += (float)$curr_amount;
                                        $final_asset_total += (float)$curr_amount;
                                    }
                                    else{
                                        //$t1 = 0; $t1 = abs((float)number_format_ind($curr_amount));
                                        $t1 = 0; $t1 = ((float)$curr_amount * -1);
                                        $total_amt = $total_amt - (float)$t1;
                                        $final_asset_total -= (float)$t1;
                                        
                                    }
                                }
                            }
                            }
                        }
                        if($acc_heading_flag[$lslist] == 1){
                            $html_col4[$j] = "<th colspan='2' class='thead4'>".$capital_schedule_name[$lslist]." Total</th>";
                            $html_col5[$j] = "";
                            $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round($total_amt,2))."</th>";
                            $acc_heading_flag[$lslist] = 2;
                            $j++; $total_amt = 0;
                        }
                    }
                }
                //echo $ng_asset_total;
                if($final_lc_total > $final_asset_total){
                    $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Equity</th>"; $j++;

                    $total_retain_earning = $final_lc_total - $final_asset_total;
                    $final_asset_total = $final_asset_total + $total_retain_earning;
                    $html_col4[$j] = "<th style='text-align:enter;'></th>";
                    $html_col5[$j] = "<td style='text-align:left;'>Difference In Opening Balance</td>";
                    $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($total_retain_earning)."</td>"; $j++;
                }
                else if($final_lc_total < $final_asset_total){
                    $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Equity</th>"; $i++;

                    $total_retain_earning = $final_asset_total - $final_lc_total;
                    $final_lc_total = $final_lc_total + $total_retain_earning;
                    $html_col1[$i] = "<th style='text-align:enter;'></th>";
                    $html_col2[$i] = "<td style='text-align:left;'>Difference In Opening Balance</td>";
                    $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($total_retain_earning)."</td>"; $i++;
                }
                
                if($i > $j){ $k = $i - $j; $l = $i; for($a = 0;$a < $k; $a++){ $html_col4[$j] = $html_col5[$j] = $html_col6[$j] = "<td></td>"; $j++; } }
                else if($i < $j){ $k = $j - $i; $l = $j; for($a = 0;$a < $k; $a++){ $html_col1[$i] = $html_col2[$i] = $html_col3[$i] = "<td></td>"; $i++; } }
                else{ $k = 0; $l = $i; }

                $html = "";
                for($m = 0;$m < $l;$m++){
                    $html .= "<tr>";
                    $html .= $html_col1[$m]."".$html_col2[$m]."".$html_col3[$m]."".$html_col4[$m]."".$html_col5[$m]."".$html_col6[$m];
                    $html .= "</tr>";
                }
                echo $html;
                ?>
            </tbody>
            
            <tr class="thead4">
                <th colspan="2" style="text-align:center;">Total</th>
                <th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($final_lc_total),2)); ?></th>
                <th colspan="2" style="text-align:center;">Total</th>
                <th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($final_asset_total),2)); ?></th>
            </tr>
        <?php
            }
        ?>
        </table>
        <script>
            function checkval(){
                var sectors = document.getElementById("sectors").value;
                if(sectors.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("sectors").focus();
                    return false;
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