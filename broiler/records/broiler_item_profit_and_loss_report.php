<?php
//broiler_item_profit_and_loss_report.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "P &amp; L Report";
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

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_list." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $sector_array_list = array(); $sector_array_list["all"] = "all";
$sector_list = ""; $sec_all_flag = 0; $excel_type = "display"; $sector_filter = $item_filter = "";

if(isset($_GET['icat']) == true){
    $fdate = date("Y-m-d",strtotime($_GET['fdate']));
    $tdate = date("Y-m-d",strtotime($_GET['tdate']));
    $farms = $sectors = $_GET['sector'];

    if($sectors == "all"){ $sector_filter = ""; }
    else{
        $sector_array_list = array();
        $sector1 = explode("@",$sectors);
        foreach($sector1 as $scode){
            if($sector_list == ""){ $sector_list = $scode; } else{ $sector_list = $sector_list."','".$scode; }
            $sector_array_list[$scode] = $scode;
            if($scode == "all"){ $sec_all_flag = 1; } else{ }
        }
        if($sec_all_flag == 1){ $sector_filter = ""; } else{ $sector_filter = " AND `location` IN ('$sector_list')"; }
    }

    $icat = $_GET['icat']; $ils = "";
    if($icat == "all"){ $item_filter = ""; }
    else{
        foreach($item_code as $icode){
            if($item_category[$icode] == $icat){
                if($ils == ""){
                    $ils = $icode;
                }
                else{
                    $ils = $ils."','".$icode;
                }
            }
        }
        $item_filter = " AND `item_code` IN ('$ils')";
    }
}
else if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));

    $sector_array_list = array();
    foreach($_POST["sectors"] as $scode){
        if($sector_list == ""){ $sector_list = $scode; } else{ $sector_list = $sector_list."','".$scode; }
        if($sector_item_list == ""){ $sector_item_list = $scode; } else{ $sector_item_list = $sector_item_list."@".$scode; }
        $sector_array_list[$scode] = $scode;
        if($scode == "all"){ $sec_all_flag = 1; } else{ }
    }
    if($sec_all_flag == 1){ $sector_filter = ""; } else{ $sector_filter = " AND `location` IN ('$sector_list')"; }
    
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
                    <th colspan="4" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>P &amp; L Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_item_profit_and_loss_report.php" method="post" onsubmit="return checkval()">
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
                                    <select name="sectors[]" id="sectors[]" class="form-control select2" style="width:250px;" multiple >
                                        <option value="all" <?php foreach($sector_array_list as $sectors){ if($sectors == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php foreach($sector_array_list as $sectors){ if($sectors == $fcode){ echo "selected"; } } ?>><?php echo $sector_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <!--<div class="m-2 form-group" id="batch_activate" style="visibility:hidden;">
                                    <label>Batch</label>
                                    <select name="batches" id="batches" class="form-control select2" style="width:250px;">
                                        <option value="all" <?php //if($batches == "all"){ echo "selected"; } ?>>-All-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">-->
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
                    <th colspan="3">Expense</th>
                    <th colspan="3">Revenue</th>
                </tr>
                <tr align="center">
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_POST['submit_report']) == true || isset($_GET['icat']) == true){
                $acc_list = "";
                $sql = "SELECT * FROM `acc_types` WHERE `description` LIKE 'Expense'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $exp_type = $row['code']; }
                
                $sql = "SELECT * FROM `acc_coa` WHERE `type` LIKE '$exp_type' AND `description` NOT LIKE '%cogs%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; if($acc_list == ""){ $acc_list = $row['code']; }else{ $acc_list = $acc_list."','".$row['code']; } }
                
                $sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $icat_iac[$row['code']] = $row['iac'];
                    $icat_pvac[$row['code']] = $row['pvac'];
                    $icat_pdac[$row['code']] = $row['pdac'];
                    $icat_cogsac[$row['code']] = $row['cogsac'];
                    $icat_wpac[$row['code']] = $row['wpac'];
                    $icat_sac[$row['code']] = $row['sac'];
                    $icat_srac[$row['code']] = $row['srac'];
                    if($acc_list == ""){ $acc_list = $row['iac']; }else{ $acc_list = $acc_list."','".$row['iac']; }
                    if($acc_list == ""){ $acc_list = $row['sac']; }else{ $acc_list = $acc_list."','".$row['sac']; }
                }
                $sql = "SELECT * FROM `account_summary` WHERE `date` <= '$tdate'".$sector_filter."".$item_filter." AND `item_code` LIKE 'MED-0118' AND `coa_code` IN ('$acc_list') AND `active` = 1 AND `dflag` = 0 ORDER BY `date`,`id` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if(strtotime($row['date']) < strtotime($fdate)){
                        //echo "<br/>".$row['crdr']."@".$row['date']."@".$row['trnum']."@".$row['quantity']."@".$row['amount'];
                        if($row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $opening_cr_item_qty[$row['item_code']] = $opening_cr_item_qty[$row['item_code']] + $row['quantity'];
                            $opening_cr_item_amt[$row['item_code']] = $opening_cr_item_amt[$row['item_code']] + $row['amount'];
                            //if($row['item_code'] == "BF-0001"){ echo "<br/>Cr-".$row['quantity']; }
                        }
                        else if($row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $opening_dr_item_qty[$row['item_code']] = $opening_dr_item_qty[$row['item_code']] + $row['quantity'];
                            $opening_dr_item_amt[$row['item_code']] = $opening_dr_item_amt[$row['item_code']] + $row['amount'];
                            //if($row['item_code'] == "BF-0001"){ echo "<br/>Dr-".$row['quantity']; }
                        }
                        else{ }
                    }
                    else if(strtotime($row['date']) >= strtotime($fdate) && strtotime($row['date']) <= strtotime($tdate)){
                        //Purchase Details
                        if($row['etype'] == "OpeningBalance" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_pur_item_qty[$row['item_code']] = $between_pur_item_qty[$row['item_code']] + $row['quantity'];
                            $between_pur_item_amt[$row['item_code']] = $between_pur_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Purchase Details
                        if($row['etype'] == "Purchase-RcvQty" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_pur_item_qty[$row['item_code']] = $between_pur_item_qty[$row['item_code']] + $row['quantity'];
                            $between_pur_item_amt[$row['item_code']] = $between_pur_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Purchase Details
                        if($row['etype'] == "Purchase-StkQty" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_pur_item_qty[$row['item_code']] = $between_pur_item_qty[$row['item_code']] + $row['quantity'];
                            $between_pur_item_amt[$row['item_code']] = $between_pur_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Purchase Details
                        if($row['etype'] == "Purchase-FreeQty" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_pur_item_qty[$row['item_code']] = $between_pur_item_qty[$row['item_code']] + $row['quantity'];
                            $between_pur_item_amt[$row['item_code']] = $between_pur_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Sale Details
                        if($row['etype'] == "Sales" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_sac[$item_category[$row['item_code']]]){
                            $between_sal_item_qty[$row['item_code']] = $between_sal_item_qty[$row['item_code']] + $row['quantity'];
                            $between_sal_item_amt[$row['item_code']] = $between_sal_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Sale Details
                        if($row['etype'] == "Sales" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_sal_itemavg_qty[$row['item_code']] = $between_sal_itemavg_qty[$row['item_code']] + $row['quantity'];
                            $between_sal_itemavg_amt[$row['item_code']] = $between_sal_itemavg_amt[$row['item_code']] + $row['amount'];
                        }
                        //Stock Transfer In Details
                        if($row['etype'] == "Stock-Transfer In" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_strin_item_qty[$row['item_code']] = $between_strin_item_qty[$row['item_code']] + $row['quantity'];
                            $between_strin_item_amt[$row['item_code']] = $between_strin_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Stock Transfer Out Details
                        if($row['etype'] == "Stock-Transfer Out" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_strout_item_qty[$row['item_code']] = $between_strout_item_qty[$row['item_code']] + $row['quantity'];
                            $between_strout_item_amt[$row['item_code']] = $between_strout_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Daily Entry Details
                        if($row['etype'] == "DayEntryFeed" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_dentry_item_qty[$row['item_code']] = $between_dentry_item_qty[$row['item_code']] + $row['quantity'];
                            $between_dentry_item_amt[$row['item_code']] = $between_dentry_item_amt[$row['item_code']] + $row['amount'];
                            
                            $between_feedconsumed_item_qty[$row['item_code']] = $between_feedconsumed_item_qty[$row['item_code']] + $row['quantity'];
                            $between_feedconsumed_item_amt[$row['item_code']] = $between_feedconsumed_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Daily Entry2 Details
                        if($row['etype'] == "DayEntryFeed2" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_dentry_item_qty[$row['item_code']] = $between_dentry_item_qty[$row['item_code']] + $row['quantity'];
                            $between_dentry_item_amt[$row['item_code']] = $between_dentry_item_amt[$row['item_code']] + $row['amount'];
                            
                            $between_feedconsumed_item_qty[$row['item_code']] = $between_feedconsumed_item_qty[$row['item_code']] + $row['quantity'];
                            $between_feedconsumed_item_amt[$row['item_code']] = $between_feedconsumed_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //MedVac Details
                        if($row['etype'] == "MedVacEntry" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_medvac_item_qty[$row['item_code']] = $between_medvac_item_qty[$row['item_code']] + $row['quantity'];
                            $between_medvac_item_amt[$row['item_code']] = $between_medvac_item_amt[$row['item_code']] + $row['amount'];
                            
                            $between_feedconsumed_item_qty[$row['item_code']] = $between_feedconsumed_item_qty[$row['item_code']] + $row['quantity'];
                            $between_feedconsumed_item_amt[$row['item_code']] = $between_feedconsumed_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Opening Details
                        if($row['etype'] == "OpeningBalance" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_opnbal_item_qty[$row['item_code']] = $between_opnbal_item_qty[$row['item_code']] + $row['quantity'];
                            $between_opnbal_item_amt[$row['item_code']] = $between_opnbal_item_amt[$row['item_code']] + $row['amount'];
                            
                            $between_feedconsumed_item_qty[$row['item_code']] = $between_feedconsumed_item_qty[$row['item_code']] + $row['quantity'];
                            $between_feedconsumed_item_amt[$row['item_code']] = $between_feedconsumed_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Feed Consumption Details
                        if($row['etype'] == "FeedConsumption" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_feedconsumed_item_qty[$row['item_code']] = $between_feedconsumed_item_qty[$row['item_code']] + $row['quantity'];
                            $between_feedconsumed_item_amt[$row['item_code']] = $between_feedconsumed_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Feed Production Details
                        if($row['etype'] == "FeedConsumption" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_feedprod_item_qty[$row['item_code']] = $between_feedprod_item_qty[$row['item_code']] + $row['quantity'];
                            $between_feedprod_item_amt[$row['item_code']] = $between_feedprod_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Egg Grading Cr Details
                        if($row['etype'] == "EggGrading" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_egggrcr_item_qty[$row['item_code']] = $between_egggrcr_item_qty[$row['item_code']] + $row['quantity'];
                            $between_egggrcr_item_amt[$row['item_code']] = $between_egggrcr_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Egg Grading Dr Details
                        if($row['etype'] == "EggGrading" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_egggrdr_item_qty[$row['item_code']] = $between_egggrdr_item_qty[$row['item_code']] + $row['quantity'];
                            $between_egggrdr_item_amt[$row['item_code']] = $between_egggrdr_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Tray Setting Cr Details
                        if($row['etype'] == "TraySetting" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_traysetcr_item_qty[$row['item_code']] = $between_traysetcr_item_qty[$row['item_code']] + $row['quantity'];
                            $between_traysetcr_item_amt[$row['item_code']] = $between_traysetcr_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Tray Setting Dr Details
                        if($row['etype'] == "TraySetting" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_traysetdr_item_qty[$row['item_code']] = $between_traysetdr_item_qty[$row['item_code']] + $row['quantity'];
                            $between_traysetdr_item_amt[$row['item_code']] = $between_traysetdr_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Hatch Entry Cr Details
                        if($row['etype'] == "HatchEntry" && $row['crdr'] == "CR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_hatchcr_item_qty[$row['item_code']] = $between_hatchcr_item_qty[$row['item_code']] + $row['quantity'];
                            $between_hatchcr_item_amt[$row['item_code']] = $between_hatchcr_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Hatch Entry Dr Details
                        if($row['etype'] == "HatchEntry" && $row['crdr'] == "DR" && $row['coa_code'] == $icat_iac[$item_category[$row['item_code']]]){
                            $between_hatchdr_item_qty[$row['item_code']] = $between_hatchdr_item_qty[$row['item_code']] + $row['quantity'];
                            $between_hatchdr_item_amt[$row['item_code']] = $between_hatchdr_item_amt[$row['item_code']] + $row['amount'];
                        }
                        //Expense Details
                        //if($row['etype'] == "PayVoucher" && $row['crdr'] == "DR"){
                            //$exp_amt[$row['coa_code']] = $exp_amt[$row['coa_code']] + $row['amount'];
                        //}
                        if(!empty($coa_code[$row['coa_code']]) && $row['crdr'] == "DR"){
                            $exp_dr_amt[$row['coa_code']] = $exp_dr_amt[$row['coa_code']] + $row['amount'];
                        }
                        if(!empty($coa_code[$row['coa_code']]) && $row['crdr'] == "CR"){
                            $exp_cr_amt[$row['coa_code']] = $exp_cr_amt[$row['coa_code']] + $row['amount'];
                        }
                    }
                    else{ }
                    $item_list[$row['item_code']] = $row['item_code'];
                    $exp_list[$row['coa_code']] = $row['coa_code'];
                }
                $o_flag = $p_flag = $s_flag = $c_flag = $so_flag = $si_flag = $fp_flag = $fc_flag = $exp_flag = 0;
                $html_col1 = $html_col2 = $html_col3 = $html_col4 = array();
                $i = $j = 0;

                $total_opening_qty = $total_opening_amt = 0;
                foreach($item_list as $ilist){
                    //Opening Stock
                    if($ilist == ""){ }
                    else{
                        $item_open_qty[$ilist] = $opening_dr_item_qty[$ilist] - $opening_cr_item_qty[$ilist];
                        $item_open_amt[$ilist] = $opening_dr_item_amt[$ilist] - $opening_cr_item_amt[$ilist];
                        //if($ilist == "BF-0001"){ echo "<br/>".$opening_dr_item_qty[$ilist]."".$opening_cr_item_qty[$ilist]; }
                        if(number_format_ind($item_open_amt[$ilist]) != "0.00"){
                            if($o_flag == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Opening</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $o_flag = 1; $i++; }
                            $html_col1[$i] = "<td>".$item_name[$ilist]."-".$ilist."</td>";
                            $html_col2[$i] = "<td style='text-align:right;'>".number_format_ind($item_open_qty[$ilist])."</td>";
                            $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($item_open_amt[$ilist])."</td>"; $i++;

                            $total_opening_qty = $total_opening_qty + $item_open_qty[$ilist];
                            $total_opening_amt = $total_opening_amt + $item_open_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_opening_amt) != "0.00"){
                    $html_col1[$i] = "<th class='thead4' style='text-align:center;'>Total Opening Balance Amount</th>";
                    $html_col2[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_opening_qty)."</th>";
                    $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_opening_amt)."</th>"; $i++;
                }

                $total_purchase_qty = $total_purchase_amt = 0;
                foreach($item_list as $ilist){
                    //Purchase Stock
                    if($ilist == ""){ }
                    else{
                        if(number_format_ind($between_pur_item_amt[$ilist]) != "0.00"){
                            if($p_flag == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Purchases</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $p_flag = 1; $i++; }
                            $html_col1[$i] = "<td>".$item_name[$ilist]."</td>";
                            $html_col2[$i] = "<td style='text-align:right;'>".number_format_ind($between_pur_item_qty[$ilist])."</td>";
                            $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($between_pur_item_amt[$ilist])."</td>"; $i++;

                            $total_purchase_qty = $total_purchase_qty + $between_pur_item_qty[$ilist];
                            $total_purchase_amt = $total_purchase_amt + $between_pur_item_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_purchase_amt) != "0.00"){
                    $html_col1[$i] = "<th class='thead4' style='text-align:center;'>Total Purchase Amount</th>";
                    $html_col2[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_purchase_qty)."</th>";
                    $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_purchase_amt)."</th>"; $i++;
                }

                $total_transferin_qty = $total_transferin_amt = 0;
                foreach($item_list as $ilist){
                    //Transfer In Stock
                    if($ilist == ""){ }
                    else{
                        if(number_format_ind($between_strin_item_amt[$ilist]) != "0.00"){
                            if($si_flag == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Stock Transfer In</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $si_flag = 1; $i++; }
                            $html_col1[$i] = "<td>".$item_name[$ilist]."</td>";
                            $html_col2[$i] = "<td style='text-align:right;'>".number_format_ind($between_strin_item_qty[$ilist])."</td>";
                            $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($between_strin_item_amt[$ilist])."</td>"; $i++;

                            $total_transferin_qty = $total_transferin_qty + $between_strin_item_qty[$ilist];
                            $total_transferin_amt = $total_transferin_amt + $between_strin_item_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_transferin_amt) != "0.00"){
                    $html_col1[$i] = "<th class='thead4' style='text-align:center;'>Total Transfer In Amount</th>";
                    $html_col2[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_transferin_qty)."</th>";
                    $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_transferin_amt)."</th>"; $i++;
                }

                $total_feedprod_qty = $total_feedprod_amt = 0;
                foreach($item_list as $ilist){
                    //Feed Production Stock
                    if($ilist == ""){ }
                    else{
                        if(number_format_ind($between_feedprod_item_amt[$ilist]) != "0.00"){
                            if($fp_flag == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Feed Production</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $fp_flag = 1; $i++; }
                            $html_col1[$i] = "<td>".$item_name[$ilist]."</td>";
                            $html_col2[$i] = "<td style='text-align:right;'>".number_format_ind($between_feedprod_item_qty[$ilist])."</td>";
                            $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($between_feedprod_item_amt[$ilist])."</td>"; $i++;

                            $total_feedprod_qty = $total_feedprod_qty + $between_feedprod_item_qty[$ilist];
                            $total_feedprod_amt = $total_feedprod_amt + $between_feedprod_item_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_feedprod_amt) != "0.00"){
                    $html_col1[$i] = "<th class='thead4' style='text-align:center;'>Total Feed Production Amount</th>";
                    $html_col2[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_feedprod_qty)."</th>";
                    $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_feedprod_amt)."</th>"; $i++;
                }

                $total_sale_qty = $total_sale_amt = 0;
                foreach($item_list as $ilist){
                    //Sale Stock
                    if($ilist == ""){ }
                    else{
                        if(number_format_ind($between_sal_item_amt[$ilist]) != "0.00"){
                            if($s_flag == 0){ $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Sales</th>"; $html_col5[$i] = $html_col6[$i] = ""; $s_flag = 1; $j++; }
                            $html_col4[$j] = "<td>".$item_name[$ilist]."</td>";
                            $html_col5[$j] = "<td style='text-align:right;'>".number_format_ind($between_sal_item_qty[$ilist])."</td>";
                            $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($between_sal_item_amt[$ilist])."</td>"; $j++;

                            $total_sale_qty = $total_sale_qty + $between_sal_item_qty[$ilist];
                            $total_sale_amt = $total_sale_amt + $between_sal_item_amt[$ilist];
                            $total_saleavg_amt = $total_saleavg_amt + $between_sal_itemavg_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_sale_amt) != "0.00"){
                    $html_col4[$j] = "<th class='thead4' style='text-align:center;'>Total Sales Amount</th>";
                    $html_col5[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_sale_qty)."</th>";
                    $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_sale_amt)."</th>"; $j++;
                }
                
                $total_transferout_qty = $total_transferout_amt = 0;
                foreach($item_list as $ilist){
                    //Transfer Out Stock
                    if($ilist == ""){ }
                    else{
                        if(number_format_ind($between_strout_item_amt[$ilist]) != "0.00"){
                            if($so_flag == 0){ $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Stock Transfer Out</th>"; $html_col5[$i] = $html_col6[$i] = ""; $so_flag = 1; $j++; }
                            $html_col4[$j] = "<td>".$item_name[$ilist]."</td>";
                            $html_col5[$j] = "<td style='text-align:right;'>".number_format_ind($between_strout_item_qty[$ilist])."</td>";
                            $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($between_strout_item_amt[$ilist])."</td>"; $j++;

                            $total_transferout_qty = $total_transferout_qty + $between_strout_item_qty[$ilist];
                            $total_transferout_amt = $total_transferout_amt + $between_strout_item_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_transferout_amt) != "0.00"){
                    $html_col4[$j] = "<th class='thead4' style='text-align:center;'>Total Transfer Out Amount</th>";
                    $html_col5[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_transferout_qty)."</th>";
                    $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_transferout_amt)."</th>"; $j++;
                }
                
                $total_feedcons_qty = $total_feedcons_amt = 0;
                foreach($item_list as $ilist){
                    //Feed Consumed Stock
                    if($ilist == ""){ }
                    else{
                        /*$between_dentry_item_qty[$row['item_code']];
                        $between_dentry_item_amt[$row['item_code']];
                        $between_medvac_item_qty[$row['item_code']];
                        $between_medvac_item_amt[$row['item_code']];*/
                        
                        if(number_format_ind($between_feedconsumed_item_amt[$ilist]) != "0.00"){
                            if($fc_flag == 0){ $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Consumption</th>"; $html_col5[$i] = $html_col6[$i] = ""; $fc_flag = 1; $j++; }
                            $html_col4[$j] = "<td>".$item_name[$ilist]."</td>";
                            $html_col5[$j] = "<td style='text-align:right;'>".number_format_ind($between_feedconsumed_item_qty[$ilist])."</td>";
                            $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($between_feedconsumed_item_amt[$ilist])."</td>"; $j++;

                            $total_feedcons_qty = $total_feedcons_qty + $between_feedconsumed_item_qty[$ilist];
                            $total_feedcons_amt = $total_feedcons_amt + $between_feedconsumed_item_amt[$ilist];
                        }
                    }
                }
                if(number_format_ind($total_feedcons_amt) != "0.00"){
                    $html_col4[$j] = "<th class='thead4' style='text-align:center;'>Total Consumed Amount</th>";
                    $html_col5[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_feedcons_qty)."</th>";
                    $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_feedcons_amt)."</th>"; $j++;
                }
                
                $final_sale_amt = $total_purd_amt = 0;
                $total_closing_qty = $total_closing_amt = 0;
                foreach($item_list as $ilist){
                    //Closing Stock
                    if($ilist == ""){ }
                    else{
                        $item_close_qty[$ilist] = (($item_open_qty[$ilist] + $between_pur_item_qty[$ilist] + $between_strin_item_qty[$ilist] + $between_feedprod_item_qty[$ilist]) - ($between_sal_itemavg_qty[$ilist] + $between_strout_item_qty[$ilist] + $between_feedconsumed_item_qty[$ilist]));
                        $item_close_amt[$ilist] = (($item_open_amt[$ilist] + $between_pur_item_amt[$ilist] + $between_strin_item_amt[$ilist] + $between_feedprod_item_amt[$ilist]) - ($between_sal_itemavg_amt[$ilist] + $between_strout_item_amt[$ilist] + $between_feedconsumed_item_amt[$ilist]));
                        if(number_format_ind($item_close_amt[$ilist]) != "0.00"){
                            if($c_flag == 0){ $html_col4[$j] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Closing</th>"; $html_col5[$i] = $html_col6[$i] = ""; $c_flag = 1; $j++; }
                            $html_col4[$j] = "<td>".$item_name[$ilist]."</td>";
                            $html_col5[$j] = "<td style='text-align:right;'>".number_format_ind($item_close_qty[$ilist])."</td>";
                            $html_col6[$j] = "<td style='text-align:right;'>".number_format_ind($item_close_amt[$ilist])."</td>"; $j++;

                            $total_closing_qty = $total_closing_qty + $item_close_qty[$ilist];
                            $total_closing_amt = $total_closing_amt + $item_close_amt[$ilist];
                        }

                        $final_sale_amt = $final_sale_amt + ($between_sal_item_amt[$ilist] + $between_strout_item_amt[$ilist]);
                        $total_purd_amt = $total_purd_amt + ($item_open_amt[$ilist] + $between_pur_item_amt[$ilist] + $between_strin_item_amt[$ilist]);
                    }
                }
                if(number_format_ind($total_closing_amt) != "0.00"){
                    $html_col4[$j] = "<th class='thead4' style='text-align:center;'>Total Closing Amount</th>";
                    $html_col5[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_closing_qty)."</th>";
                    $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind($total_closing_amt)."</th>"; $j++;
                }
                
                //echo "<br/>".$i."-".$j;
                if($i > $j){ $k = $i - $j; $l = $i; for($a = 0;$a < $k; $a++){ $html_col4[$j] = $html_col5[$j] = $html_col6[$j] = "<td></td>"; $j++; } }
                else if($i < $j){ $k = $j - $i; $l = $j; for($a = 0;$a < $k; $a++){ $html_col1[$i] = $html_col2[$i] = $html_col3[$i] = "<td></td>"; $i++; } }
                else{ $k = 0; $l = $i; }

                //echo "<br/>".$i."-".$j;
                //echo "<br/>".$final_sale_amt."-".$total_purd_amt;
                if($final_sale_amt >= $total_purd_amt){
                    $gross_profit = $final_sale_amt - $total_purd_amt; $gross_loss = 0;
                    $html_col1[$i] = $html_col2[$i] = $html_col3[$i] = "<td></td>"; $i++;
                    $html_col4[$j] = "<th class='thead4'>Gross Profit</th>"; $html_col5[$j] = "<th class='thead4'></th>";
                    $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round(($gross_profit),2))."</th>"; $j++;
                }
                else{
                    $gross_loss = $total_purd_amt - $final_sale_amt; $gross_profit = 0;
                    $html_col4[$j] = $html_col5[$j] = $html_col6[$j] = "<td></td>"; $j++;
                    $html_col1[$i] = "<th class='thead4'>Gross Loss</th>"; $html_col2[$i] = "<th class='thead4'></th>";
                    $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round(($gross_loss),2))."</th>"; $i++;
                }
                //echo "<br/>".$i."-".$j;
                
                $total_expense_amt = 0;
                foreach($exp_list as $elist){
                    //Expenses
                    if($elist == ""){ }
                    else{
                        if($exp_flag == 0){ $html_col1[$i] = "<th colspan='3' style='text-align:center;color:green;font-weight:bold;'>Expense</th>"; $html_col2[$i] = ""; $html_col3[$i] = ""; $exp_flag = 1; $i++; }
                        if(number_format_ind($exp_dr_amt[$elist]) != "0.00" || number_format_ind($exp_cr_amt[$elist]) != "0.00"){
                            $exp_amt[$elist] = $exp_cr_amt[$elist] - $exp_dr_amt[$elist];
                        }
                        else{
                            $exp_amt[$elist] = 0;
                        }
                        if(number_format_ind($exp_amt[$elist]) != "0.00"){
                            $html_col1[$i] = "<td>".$coa_name[$elist]."</td>";
                            $html_col2[$i] = "<td style='text-align:right;'></td>";
                            $html_col3[$i] = "<td style='text-align:right;'>".number_format_ind($exp_amt[$elist])."</td>"; $i++;
                            $total_expense_amt = $total_expense_amt + $exp_amt[$elist];
                        }
                    }
                }

                if($i > $j){ $k = $i - $j; $l = $i; for($a = 0;$a < $k; $a++){ $html_col4[$j] = $html_col5[$j] = $html_col6[$j] = "<td></td>"; $j++; } }
                else if($i < $j){ $k = $j - $i; $l = $j; for($a = 0;$a < $k; $a++){ $html_col1[$i] = $html_col2[$i] = $html_col3[$i] = "<td></td>"; $i++; } }
                else{ $k = 0; $l = $i; }

                if($gross_profit >= $gross_loss){
                    $net_profit = $gross_profit - $total_expense_amt; $net_loss = 0;
                    $html_col4[$j] = $html_col5[$j] = $html_col6[$j] = "<td></td>"; $j++;
                    $html_col1[$i] = "<th class='thead4'>Net Profit</th>"; $html_col2[$i] = "<th class='thead4'></th>";
                    $html_col3[$i] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round(($net_profit),2))."</th>"; $i++;
                }
                else{
                    $net_loss = $gross_loss + $total_expense_amt; $net_profit = 0;
                    $html_col1[$i] = $html_col2[$i] = $html_col3[$i] = "<td></td>"; $i++;
                    $html_col4[$j] = "<th class='thead4'>Net Loss</th>"; $html_col5[$j] = "<th class='thead4'></th>";
                    $html_col6[$j] = "<th class='thead4' style='text-align:right;'>".number_format_ind(round(($net_loss),2))."</th>"; $j++;
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
                <th colspan="1" style="text-align:center;">Total</th>
                <th colspan="1" style="text-align:center;"></th>
                <th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($total_opening_amt + $total_purchase_amt + $total_transferin_amt + $gross_profit),2)); ?></th>
                <th colspan="1" style="text-align:center;"></th>
                <th colspan="1" style="text-align:center;"></th>
                <th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($total_sale_amt + $total_transferout_amt + $gross_loss),2)); ?></th>
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