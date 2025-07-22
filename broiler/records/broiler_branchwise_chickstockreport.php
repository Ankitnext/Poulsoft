<?php
//broiler_branchwise_chickstockreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    global $page_title; $page_title = "Branch Wise Chick Stock";
    include "header_head.php";
    $form_path = "broiler_branchwise_chickstockreport.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    global $page_title; $page_title = "Branch Wise Chick Stock";
    include "header_head.php";
    $form_path = "broiler_branchwise_chickstockreport.php?db=$db&userid=".$user_code;
}

$file_name = "Branch Wise Chick Stock";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'  ".$branch_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $line_code = $line_name = $line_branch = array();
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_officetypes` WHERE `description` NOT LIKE '%plant%' AND `active` = 1 AND `dflag` = 0";
$query = mysqli_query($conn,$sql); $office_alist = array();
while($row = mysqli_fetch_assoc($query)){ $office_alist[$row["code"]] = $row["code"]; }

$office_list = implode("','", $office_alist);
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$office_list') AND `active` = '1'".$sector_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = $sectors = $loc_type = "all"; $fetch_type = "branch_wise"; /*$batch_type = "Live";*/ $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $sectors = $_POST['sectors'];
    $loc_type = $_POST['loc_type'];
    $fetch_type = $_POST['fetch_type'];
    //$batch_type = $_POST['batch_type'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
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
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != "" && !empty($farm_svr[$scode])){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Warehouse</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="none" <?php if($sectors == "none"){ echo "selected"; } ?>>-None-</option>
                                        <?php foreach($sector_code as $wcode){ if(!empty($sector_name[$wcode])){ ?>
                                        <option value="<?php echo $wcode; ?>" <?php if($sectors == $wcode){ echo "selected"; } ?>><?php echo $sector_name[$wcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Fetch Type</label>
                                    <select name="fetch_type" id="fetch_type" class="form-control select2">
                                        <option value="branch_wise" <?php if($fetch_type == "branch_wise"){ echo "selected"; } ?>>Branch</option>
                                        <option value="line_wise" <?php if($fetch_type == "line_wise"){ echo "selected"; } ?>>Line</option>
                                        <option value="supvr_wise" <?php if($fetch_type == "supvr_wise"){ echo "selected"; } ?>>Supervisor</option>
                                        <option value="farm_wise" <?php if($fetch_type == "farm_wise"){ echo "selected"; } ?>>Farm</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Location Type</label>
                                    <select name="loc_type" id="loc_type" class="form-control select2">
                                        <option value="all" <?php if($loc_type == "all"){ echo "selected"; } ?>>Both</option>
                                        <option value="only_farms" <?php if($loc_type == "only_farms"){ echo "selected"; } ?>>Only Farms</option>
                                        <option value="only_sectors" <?php if($loc_type == "only_sectors"){ echo "selected"; } ?>>Only Sectors</option>
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
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
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
        <table id="main_table" class="tbl" align="center">
            <?php
            if(isset($_POST['submit_report']) == true){
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $chick_code = $chick_name = array();
                while($row = mysqli_fetch_assoc($query)){ $chick_code[$row['code']] = $row['code']; $chick_name[$row['code']] = $row['description']; }

                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $chick_code[$row['code']] = $row['code']; $chick_name[$row['code']] = $row['description']; }
            
                $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = "";
                if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                if($supervisors != "all"){ $sup_fltr = " AND `line_code` = '$supervisors'"; }
                if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }

                $farm_list = ""; $farm_list = implode("','", $farm_code);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                $farm_list = ""; $farm_list = implode("','", $farm_alist);
                $gc_fltr = ""; //if($loc_type == "all" || $loc_type == "only_farms"){ $gc_fltr = " AND `gc_flag` = '0'"; }
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')".$gc_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $batch_alist = $batch_afarm = $batch_gcflag = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; $batch_afarm[$row['code']] = $row['farm_code']; $batch_gcflag[$row['code']] = $row['gc_flag']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    //Fetch Item Details
                    $item_list = ""; $item_list = implode("','", $chick_code);
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);

                    $sec_list = "";
                    if($sectors == "all"){ foreach($sector_code as $wcode){ if($sec_list == ""){ $sec_list = $wcode; } else{ $sec_list = $sec_list."','".$wcode; } } }
                    else if($sectors != "all" && $sectors != "none"){ $sec_list = $sectors; } else{ }
                    
                    $p_fltr = $ti_fltr = $s_fltr = $to_fltr = $pp_fltr = $d_fltr = "";
                    if($loc_type == "all"){
                        if($sec_list != ""){
                            $p_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $ti_fltr = " AND (`towarehouse` IN ('$sec_list') OR `to_batch` IN ('$batch_list'))";
                            $s_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $to_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                            $pp_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                            $pr_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $d_fltr = " AND `batch_code` IN ('$batch_list')";
                        }
                        else{
                            $p_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $ti_fltr = " AND `to_batch` IN ('$batch_list')";
                            $s_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $to_fltr = " AND `from_batch` IN ('$batch_list')";
                            $pp_fltr = " AND `from_batch` IN ('$batch_list')";
                            $pr_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $d_fltr = " AND `batch_code` IN ('$batch_list')";
                        }
                    }
                    else if($loc_type == "only_farms"){
                        $p_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $ti_fltr = " AND `to_batch` IN ('$batch_list')";
                        $s_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $to_fltr = " AND `from_batch` IN ('$batch_list')";
                        $pp_fltr = " AND `from_batch` IN ('$batch_list')";
                        $pr_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $d_fltr = " AND `batch_code` IN ('$batch_list')";
                    }
                    else if($loc_type == "only_sectors"){
                        $p_fltr = " AND `warehouse` IN ('$sec_list')";
                        $ti_fltr = " AND `towarehouse` IN ('$sec_list')";
                        $s_fltr = " AND `warehouse` IN ('$sec_list')";
                        $to_fltr = " AND `fromwarehouse` IN ('$sec_list')";
                        $pp_fltr = " AND `fromwarehouse` IN ('$sec_list')";
                        $pr_fltr = " AND `warehouse` IN ('$sec_list')";
                        $d_fltr = " AND `batch_code` IN ('none')";
                    }
                    else{
                        $p_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $ti_fltr = " AND (`towarehouse` IN ('$sec_list') OR `to_batch` IN ('$batch_list'))";
                        $s_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $to_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                        $pp_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                        $pr_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $d_fltr = " AND `batch_code` IN ('$batch_list')";
                    }
                    
                    //Purchase
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list')".$p_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_pur_cqty = $opn_pur_camt = $btw_pur_cqty = $btw_pur_camt = array();
                    while($row = mysqli_fetch_array($query)){
                        if($row['farm_batch'] != ""){ $key = $row['farm_batch']; } else{ $key = $row['warehouse']; }
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_pur_cqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $opn_pur_camt[$key] += (float)$row['item_tamt'];
                        }
                        else{
                            $btw_pur_cqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $btw_pur_camt[$key] += (float)$row['item_tamt'];
                        }
                    }
                    //Stock-In
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list')".$ti_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_tin_cqty = $opn_tin_camt = $btw_tin_cqty = $btw_tin_camt = array();
                    while($row = mysqli_fetch_array($query)){
                        if($row['to_batch'] != ""){ $key = $row['to_batch']; } else{ $key = $row['towarehouse']; }
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_tin_cqty[$key] += (float)$row['quantity'];
                            $opn_tin_camt[$key] += (float)$row['amount'];
                        }
                        else{
                            $btw_tin_cqty[$key] += (float)$row['quantity'];
                            $btw_tin_camt[$key] += (float)$row['amount'];
                        }
                    }
                    //Sale
                    $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list')".$s_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_sale_cqty = $opn_sale_camt = $btw_sale_cqty = $btw_sale_cwht = $btw_sale_camt = array();
                    while($row = mysqli_fetch_array($query)){
                        if($row['farm_batch'] != ""){ $key = $row['farm_batch']; } else{ $key = $row['warehouse']; }
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_sale_cqty[$key] += (float)$row['birds'];
                            $opn_sale_camt[$key] += (float)$row['item_tamt'];
                        }
                        else{
                            $btw_sale_cqty[$key] += (float)$row['birds'];
                            $btw_sale_cwht[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $btw_sale_camt[$key] += (float)$row['item_tamt'];
                        }
                    }
                    //Stock-Out
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list')".$to_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_tout_cqty = $opn_tout_camt = $btw_tout_cqty = $btw_tout_camt = array();
                    while($row = mysqli_fetch_array($query)){
                        if($row['from_batch'] != ""){ $key = $row['from_batch']; } else{ $key = $row['fromwarehouse']; }
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_tout_cqty[$key] += (float)$row['quantity'];
                            $opn_tout_camt[$key] += (float)$row['amount'];
                        }
                        else{
                            $btw_tout_cqty[$key] += (float)$row['quantity'];
                            $btw_tout_camt[$key] += (float)$row['amount'];
                        }
                        //if($row['from_batch'] == ""){ echo "<br/>".$row['date']."@".$row['trnum']."@".$row['quantity']; }
                    }
                    //Purchase Return-Out
                    $sql = "SELECT * FROM `broiler_itemreturns` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list')".$pr_fltr." AND `type` = 'Supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_array($query)){
                        if($row['farm_batch'] != ""){ $key = $row['farm_batch']; } else{ $key = $row['warehouse']; }
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_tout_cqty[$key] += (float)$row['quantity'];
                            $opn_tout_camt[$key] += (float)$row['amount'];
                        }
                        else{
                            $btw_tout_cqty[$key] += (float)$row['quantity'];
                            $btw_tout_camt[$key] += (float)$row['amount'];
                        }
                        //if($row['from_batch'] == ""){ echo "<br/>".$row['date']."@".$row['trnum']."@".$row['quantity']; }
                    }
                    //In-House: Transfer-Out
                    $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `date` <= '$tdate' AND `item_code` IN ('$item_list')".$pp_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_prsout_cqty = $opn_prsout_camt = $btw_prsout_cqty = $btw_prsout_cwht = $btw_prsout_camt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        if($row['from_batch'] != ""){ $key = $row['from_batch']; } else{ $key = $row['fromwarehouse']; }
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_prsout_cqty[$key] += (float)$row['birds'];
                            $opn_prsout_camt[$key] += (float)$row['avg_amount'];
                        }
                        else{
                            $btw_prsout_cqty[$key] += (float)$row['birds'];
                            $btw_prsout_cwht[$key] += (float)$row['weight'];
                            $btw_prsout_camt[$key] += (float)$row['avg_amount'];
                        }
                    }
                    //Day Record
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate'".$d_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $opn_mort_qty = $opn_cull_qty  = $btw_mort_qty = $btw_cull_qty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['batch_code'];
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_mort_qty[$key] += (float)$row['mortality'];
                            $opn_cull_qty[$key] += (float)$row['culls'];
                        }
                        else{
                            $btw_mort_qty[$key] += (float)$row['mortality'];
                            $btw_cull_qty[$key] += (float)$row['culls'];
                        }
                    }
                    //Rearing Record
                    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `date` <= '$tdate'".$d_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $opn_excs_qty = $opn_shrt_qty  = $btw_excs_qty = $btw_shrt_qty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['batch_code'];
                        if(strtotime($row['date']) < strtotime($fdate)){
                            if((float)$row['excess'] < 0){ $opn_excs_qty[$key] += (-1 * (float)$row['excess']); } else{ $opn_excs_qty[$key] += (float)$row['excess']; }
                            if((float)$row['shortage'] < 0){ $opn_shrt_qty[$key] += (-1 * (float)$row['shortage']); } else{ $opn_shrt_qty[$key] += (float)$row['shortage']; }
                        }
                        else{
                            if((float)$row['excess'] < 0){ $btw_excs_qty[$key] += (-1 * (float)$row['excess']); } else{ $btw_excs_qty[$key] += (float)$row['excess']; }
                            if((float)$row['shortage'] < 0){ $btw_shrt_qty[$key] += (-1 * (float)$row['shortage']); } else{ $btw_shrt_qty[$key] += (float)$row['shortage']; }
                        }
                    }

                    $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_book = $batch_farm = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_farm[$row['farm_code']] .= $row['code'].","; }
                    
                    $html = '';
                    $html .= '<thead class="thead3">';
                    $html .= '<tr style="text-align:center;" align="center">';
                    if($fetch_type == "farm_wise"){ $html .= '<th></th><th></th><th></th><th></th><th></th><th></th>'; } else{ $html .= '<th></th>'; }
                    $html .= '<th colspan="3">Opening</th>';
                    $html .= '<th colspan="3">Purchase</th>';
                    $html .= '<th colspan="3">Transfer-In</th>';
                    $html .= '<th colspan="1">Mortality</th>';
                    $html .= '<th colspan="4">Sales</th>';
                    $html .= '<th colspan="3">Transfer-Out</th>';
                    $html .= '<th colspan="4">In-House Processing</th>';
                    $html .= '<th colspan="1">Excess</th>';
                    $html .= '<th colspan="1">Short</th>';
                    $html .= '<th colspan="1">Closing</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<thead class="thead3" id="head_names">';
                    $html .= '<tr style="text-align:center;" align="center">';
                    if($fetch_type == "branch_wise"){ $html .= '<th id="order">Branch</th>'; }
                    else if($fetch_type == "line_wise"){ $html .= '<th id="order">Line</th>'; }
                    else if($fetch_type == "supvr_wise"){ $html .= '<th id="order">Supervisor</th>'; }
                    else{ $html .= '<th id="order">Branch</th><th id="order">Line</th><th id="order">Supervisor</th><th id="order">Farm Code</th><th id="order">Farm</th><th id="order">Batch</th>'; }
                    //Opening
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Purchase
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Transfer-In
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Mortality
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    //$html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    //$html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Sale
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Weight</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Transfer-Out
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //In-House Processing
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Weight</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Excess
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    //Short
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    //Closing
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    //$html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    //$html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody class="tbody1" id="tbody1">';
                    
                    //Check Fetch Type
                    $opn_cqty = $opn_camt = $btw_pqty = $btw_pamt = $btw_tiqty = $btw_tiamt = $btw_sqty = $btw_swht = $btw_samt = $btw_toqty = $btw_toamt = $btw_ppqty = $btw_ppwht = $btw_ppamt = $btw_exqty = $btw_stqty = $btw_mqty = $btw_mamt = $btw_cqty = $btw_camt = array();
                    $topn_qty = $topn_amt = $tpur_qty = $tpur_amt = $ttin_qty = $ttin_amt = $tsale_qty = $tsale_wht = $tsale_amt = $ttout_qty = $ttout_amt = $tpout_qty = $texs_qty = $tpout_wht = $tpout_amt = $tmort_qty = $tmort_amt = $tclose_qty = $tclose_amt = 0;

                    foreach($farm_alist as $fcode){
                        $blist = array(); $blist = explode(",",$batch_farm[$fcode]);
                        foreach($blist as $bcode){
                            if($bcode == ""){ }
                            else{
                                $key = $bcode;
                                $fcode = $batch_afarm[$key]; $bcode = $farm_branch[$fcode]; $lcode = $farm_line[$fcode]; $scode = $farm_supervisor[$fcode];
                                if($fetch_type == "branch_wise"){ $key2 = $bcode; } else if($fetch_type == "line_wise"){ $key2 = $lcode; }
                                else if($fetch_type == "supvr_wise"){ $key2 = $scode; } else{ $key2 = $key; }

                                //Initialization
                                if(empty($opn_pur_cqty[$key]) || $opn_pur_cqty[$key] == ""){ $opn_pur_cqty[$key] = 0; }
                                if(empty($opn_pur_camt[$key]) || $opn_pur_camt[$key] == ""){ $opn_pur_camt[$key] = 0; }
                                if(empty($btw_pur_cqty[$key]) || $btw_pur_cqty[$key] == ""){ $btw_pur_cqty[$key] = 0; }
                                if(empty($btw_pur_camt[$key]) || $btw_pur_camt[$key] == ""){ $btw_pur_camt[$key] = 0; }
                                if(empty($opn_tin_cqty[$key]) || $opn_tin_cqty[$key] == ""){ $opn_tin_cqty[$key] = 0; }
                                if(empty($opn_tin_camt[$key]) || $opn_tin_camt[$key] == ""){ $opn_tin_camt[$key] = 0; }
                                if(empty($btw_tin_cqty[$key]) || $btw_tin_cqty[$key] == ""){ $btw_tin_cqty[$key] = 0; }
                                if(empty($btw_tin_camt[$key]) || $btw_tin_camt[$key] == ""){ $btw_tin_camt[$key] = 0; }
                                if(empty($opn_sale_cqty[$key]) || $opn_sale_cqty[$key] == ""){ $opn_sale_cqty[$key] = 0; }
                                if(empty($opn_sale_camt[$key]) || $opn_sale_camt[$key] == ""){ $opn_sale_camt[$key] = 0; }
                                if(empty($btw_sale_cqty[$key]) || $btw_sale_cqty[$key] == ""){ $btw_sale_cqty[$key] = 0; }
                                if(empty($btw_sale_cwht[$key]) || $btw_sale_cwht[$key] == ""){ $btw_sale_cwht[$key] = 0; }
                                if(empty($btw_sale_camt[$key]) || $btw_sale_camt[$key] == ""){ $btw_sale_camt[$key] = 0; }
                                if(empty($opn_tout_cqty[$key]) || $opn_tout_cqty[$key] == ""){ $opn_tout_cqty[$key] = 0; }
                                if(empty($opn_tout_camt[$key]) || $opn_tout_camt[$key] == ""){ $opn_tout_camt[$key] = 0; }
                                if(empty($btw_tout_cqty[$key]) || $btw_tout_cqty[$key] == ""){ $btw_tout_cqty[$key] = 0; }
                                if(empty($btw_tout_camt[$key]) || $btw_tout_camt[$key] == ""){ $btw_tout_camt[$key] = 0; }
                                if(empty($opn_prsout_cqty[$key]) || $opn_prsout_cqty[$key] == ""){ $opn_prsout_cqty[$key] = 0; }
                                if(empty($btw_prsout_cwht[$key]) || $btw_prsout_cwht[$key] == ""){ $btw_prsout_cwht[$key] = 0; }
                                if(empty($btw_prsout_cqty[$key]) || $btw_prsout_cqty[$key] == ""){ $btw_prsout_cqty[$key] = 0; }
                                if(empty($opn_mort_qty[$key]) || $opn_mort_qty[$key] == ""){ $opn_mort_qty[$key] = 0; }
                                if(empty($opn_cull_qty[$key]) || $opn_cull_qty[$key] == ""){ $opn_cull_qty[$key] = 0; }
                                if(empty($btw_mort_qty[$key]) || $btw_mort_qty[$key] == ""){ $btw_mort_qty[$key] = 0; }
                                if(empty($btw_cull_qty[$key]) || $btw_cull_qty[$key] == ""){ $btw_cull_qty[$key] = 0; }
                                if(empty($opn_mort_amt[$key]) || $opn_mort_amt[$key] == ""){ $opn_mort_amt[$key] = 0; }
                                if(empty($opn_cull_amt[$key]) || $opn_cull_amt[$key] == ""){ $opn_cull_amt[$key] = 0; }
                                if(empty($btw_mort_amt[$key]) || $btw_mort_amt[$key] == ""){ $btw_mort_amt[$key] = 0; }
                                if(empty($btw_cull_amt[$key]) || $btw_cull_amt[$key] == ""){ $btw_cull_amt[$key] = 0; }
                                if(empty($opn_excs_qty[$key]) || $opn_excs_qty[$key] == ""){ $opn_excs_qty[$key] = 0; }
                                if(empty($opn_shrt_qty[$key]) || $opn_shrt_qty[$key] == ""){ $opn_shrt_qty[$key] = 0; }
                                if(empty($btw_excs_qty[$key]) || $btw_excs_qty[$key] == ""){ $btw_excs_qty[$key] = 0; }
                                if(empty($btw_shrt_qty[$key]) || $btw_shrt_qty[$key] == ""){ $btw_shrt_qty[$key] = 0; }

                                //Opening
                                $oqty = (((float)$opn_pur_cqty[$key] + (float)$opn_tin_cqty[$key] + (float)$opn_excs_qty[$key]) - ((float)$opn_sale_cqty[$key] + (float)$opn_tout_cqty[$key] + (float)$opn_prsout_cqty[$key] + (float)$opn_mort_qty[$key] + (float)$opn_cull_qty[$key] + (float)$opn_shrt_qty[$key]));
                                
                                //Excess/Shortage Adjustment if(!empty($batch_gcflag[$key]) && (float)$batch_gcflag[$key] == 1 && (float)$oqty <= 400){ $oqty = 0; }

                                $oamt = (((float)$opn_pur_camt[$key] + (float)$opn_tin_camt[$key]) - ((float)$opn_sale_camt[$key] + (float)$opn_tout_camt[$key] + (float)$opn_prsout_camt[$key] + (float)$opn_mort_amt[$key] + (float)$opn_cull_amt[$key]));
                                if((float)$oamt < 0){
                                    $t1 = ((float)$opn_pur_cqty[$key] + (float)$opn_tin_cqty[$key]);
                                    $t2 = ((float)$opn_pur_camt[$key] + (float)$opn_tin_camt[$key]);
                                    $t3 = 0;
                                    if((float)$t1 != 0){
                                        $t3 = ((float)$t2 / (float)$t1);
                                    }
                                    $oamt = (float)$t3 * (float)$oqty;
                                }
                                $opn_cqty[$key2] += (float)$oqty;
                                $opn_camt[$key2] += (float)$oamt;
                                //Purchase
                                $btw_pqty[$key2] += (float)$btw_pur_cqty[$key];
                                $btw_pamt[$key2] += (float)$btw_pur_camt[$key];
                                //Transfer-In
                                $btw_tiqty[$key2] += (float)$btw_tin_cqty[$key];
                                $btw_tiamt[$key2] += (float)$btw_tin_camt[$key];

                                //Batch Wise Chick Avg Price
                                $bin_qty = $bavg_prc = $bin_prc = 0; 
                                $bin_qty = ((float)$oqty + (float)$btw_pur_cqty[$key] + (float)$btw_tin_cqty[$key]);
                                $bin_amt = ((float)$oamt + (float)$btw_pur_camt[$key] + (float)$btw_tin_camt[$key]);
                                if((float)$bin_qty != 0){ $bin_prc = round(((float)$bin_amt / (float)$bin_qty),10); }
                                
                                //Sale
                                $btw_sqty[$key2] += (float)$btw_sale_cqty[$key];
                                $btw_swht[$key2] += (float)$btw_sale_cwht[$key];
                                //$btw_sale_camt[$key] = (float)$btw_sale_cqty[$key] * (float)$bin_prc;
                                $btw_samt[$key2] += (float)$btw_sale_camt[$key];

                                //Transfer-Out
                                $btw_toqty[$key2] += (float)$btw_tout_cqty[$key];
                                //$btw_tout_camt[$key] = (float)$btw_tout_cqty[$key] * (float)$bin_prc;
                                //$btw_toamt[$key2] += (float)$btw_tout_camt[$key];
                                $tout_amt1 = 0;
                                $tout_amt1 = (float)$btw_tout_cqty[$key] * (float)$bin_prc;
                                $btw_toamt[$key2] += (float)$tout_amt1;

                                //In-House Processing
                                $btw_ppqty[$key2] += (float)$btw_prsout_cqty[$key];
                                $btw_ppwht[$key2] += (float)$btw_prsout_cwht[$key];
                                //$btw_prsout_camt[$key] = (float)$btw_prsout_cqty[$key] * (float)$bin_prc;
                                $btw_ppamt[$key2] += (float)$btw_prsout_camt[$key];

                                //Excess
                                $btw_exqty[$key2] += (float)$btw_excs_qty[$key];

                                //Short
                                $btw_stqty[$key2] += (float)$btw_shrt_qty[$key];

                                //Mortality + Culls
                                $btw_mqty[$key2] += ((float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]);
                                //$btw_mamt[$key2] += (((float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]) * (float)$bin_prc);
                                $btw_mamt[$key2] += 0;
                                
                                $bcqty = (((Float)$oqty + (float)$btw_pur_cqty[$key] + (float)$btw_tin_cqty[$key] + (float)$btw_excs_qty[$key]) - ((float)$btw_sale_cqty[$key] + (float)$btw_tout_cqty[$key] + (float)$btw_prsout_cqty[$key] + (float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key] + (float)$btw_shrt_qty[$key]));
                                //Excess/Shortage Adjustment if(!empty($batch_gcflag[$key]) && (float)$batch_gcflag[$key] == 1 && (float)$bcqty <= 400){ $bcqty = 0; }
                                
                                $bcamt = (((Float)$oamt + (float)$btw_pur_camt[$key] + (float)$btw_tin_camt[$key]) - ((float)$btw_sale_camt[$key] + (float)$tout_amt1 + (float)$btw_prsout_camt[$key] + (float)$btw_mort_amt[$key] + (float)$btw_cull_amt[$key]));

                                $btw_cqty[$key2] += (float)$bcqty;
                                $btw_camt[$key2] += (float)$bcamt;

                                //Calculating Totals
                                $topn_qty += (float)$oqty;
                                $topn_amt += (float)$oamt;
                                $tpur_qty += (float)$btw_pur_cqty[$key];
                                $tpur_amt += (float)$btw_pur_camt[$key];
                                $ttin_qty += (float)$btw_tin_cqty[$key];
                                $ttin_amt += (float)$btw_tin_camt[$key];
                                $tsale_qty += (float)$btw_sale_cqty[$key];
                                $tsale_wht += (float)$btw_sale_cwht[$key];
                                $tsale_amt += (float)$btw_sale_camt[$key];
                                $ttout_qty += (float)$btw_tout_cqty[$key];
                                $ttout_amt += (float)$tout_amt1;
                                $tpout_qty += (float)$btw_prsout_cqty[$key];
                                $tpout_wht += (float)$btw_prsout_cwht[$key];
                                $tpout_amt += (float)$btw_prsout_camt[$key];
                                $texs_qty += (float)$btw_excs_qty[$key];
                                $tsrt_qty += (float)$btw_shrt_qty[$key];
                                $tmort_qty += ((float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]);
                                $tmort_amt += (((float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]) * (float)$bin_prc);
                                $tclose_qty += (float)$bcqty;
                                $tclose_amt += (float)$bcamt;

                                //echo "<br/>$key@(((float)$opn_pur_cqty[$key] + (float)$opn_tin_cqty[$key]) - ((float)$opn_sale_cqty[$key] + (float)$opn_tout_cqty[$key] + (float)$opn_prsout_cqty[$key] + (float)$opn_mort_qty[$key] + (float)$opn_cull_qty[$key]));";
                                //echo "<br/>$key@(((float)$btw_pur_cqty[$key] + (float)$btw_tin_cqty[$key]) - ((float)$btw_sale_cqty[$key] + (float)$btw_tout_cqty[$key] + (float)$btw_prsout_cqty[$key] + (float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]));";
                            }
                        }
                    }
                    //Sector Wise Calculations
                    if($sec_list != ""){
                        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `code` IN ('$sec_list')".$sector_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
                        $query = mysqli_query($conn,$sql); $sector_acode = array();
                        while($row = mysqli_fetch_assoc($query)){ $sector_acode[$row['code']] = $row['code']; }
                        
                        foreach($sector_acode as $wcode){
                            if($wcode == "" || $wcode == "select"){ }
                            else{
                                $key = $key2 = $wcode;

                                //Initialization
                                if(empty($opn_pur_cqty[$key]) || $opn_pur_cqty[$key] == ""){ $opn_pur_cqty[$key] = 0; }
                                if(empty($opn_pur_camt[$key]) || $opn_pur_camt[$key] == ""){ $opn_pur_camt[$key] = 0; }
                                if(empty($btw_pur_cqty[$key]) || $btw_pur_cqty[$key] == ""){ $btw_pur_cqty[$key] = 0; }
                                if(empty($btw_pur_camt[$key]) || $btw_pur_camt[$key] == ""){ $btw_pur_camt[$key] = 0; }
                                if(empty($opn_tin_cqty[$key]) || $opn_tin_cqty[$key] == ""){ $opn_tin_cqty[$key] = 0; }
                                if(empty($opn_tin_camt[$key]) || $opn_tin_camt[$key] == ""){ $opn_tin_camt[$key] = 0; }
                                if(empty($btw_tin_cqty[$key]) || $btw_tin_cqty[$key] == ""){ $btw_tin_cqty[$key] = 0; }
                                if(empty($btw_tin_camt[$key]) || $btw_tin_camt[$key] == ""){ $btw_tin_camt[$key] = 0; }
                                if(empty($opn_sale_cqty[$key]) || $opn_sale_cqty[$key] == ""){ $opn_sale_cqty[$key] = 0; }
                                if(empty($opn_sale_camt[$key]) || $opn_sale_camt[$key] == ""){ $opn_sale_camt[$key] = 0; }
                                if(empty($btw_sale_cqty[$key]) || $btw_sale_cqty[$key] == ""){ $btw_sale_cqty[$key] = 0; }
                                if(empty($btw_sale_cwht[$key]) || $btw_sale_cwht[$key] == ""){ $btw_sale_cwht[$key] = 0; }
                                if(empty($btw_sale_camt[$key]) || $btw_sale_camt[$key] == ""){ $btw_sale_camt[$key] = 0; }
                                if(empty($opn_tout_cqty[$key]) || $opn_tout_cqty[$key] == ""){ $opn_tout_cqty[$key] = 0; }
                                if(empty($opn_tout_camt[$key]) || $opn_tout_camt[$key] == ""){ $opn_tout_camt[$key] = 0; }
                                if(empty($btw_tout_cqty[$key]) || $btw_tout_cqty[$key] == ""){ $btw_tout_cqty[$key] = 0; }
                                if(empty($btw_tout_camt[$key]) || $btw_tout_camt[$key] == ""){ $btw_tout_camt[$key] = 0; }
                                if(empty($opn_prsout_cqty[$key]) || $opn_prsout_cqty[$key] == ""){ $opn_prsout_cqty[$key] = 0; }
                                if(empty($btw_prsout_cwht[$key]) || $btw_prsout_cwht[$key] == ""){ $btw_prsout_cwht[$key] = 0; }
                                if(empty($btw_prsout_cqty[$key]) || $btw_prsout_cqty[$key] == ""){ $btw_prsout_cqty[$key] = 0; }
                                if(empty($opn_mort_qty[$key]) || $opn_mort_qty[$key] == ""){ $opn_mort_qty[$key] = 0; }
                                if(empty($opn_cull_qty[$key]) || $opn_cull_qty[$key] == ""){ $opn_cull_qty[$key] = 0; }
                                if(empty($btw_mort_qty[$key]) || $btw_mort_qty[$key] == ""){ $btw_mort_qty[$key] = 0; }
                                if(empty($btw_cull_qty[$key]) || $btw_cull_qty[$key] == ""){ $btw_cull_qty[$key] = 0; }
                                if(empty($opn_mort_amt[$key]) || $opn_mort_amt[$key] == ""){ $opn_mort_amt[$key] = 0; }
                                if(empty($opn_cull_amt[$key]) || $opn_cull_amt[$key] == ""){ $opn_cull_amt[$key] = 0; }
                                if(empty($btw_mort_amt[$key]) || $btw_mort_amt[$key] == ""){ $btw_mort_amt[$key] = 0; }
                                if(empty($btw_cull_amt[$key]) || $btw_cull_amt[$key] == ""){ $btw_cull_amt[$key] = 0; }

                                //Opening
                                $oqty = (((float)$opn_pur_cqty[$key] + (float)$opn_tin_cqty[$key]) - ((float)$opn_sale_cqty[$key] + (float)$opn_tout_cqty[$key] + (float)$opn_prsout_cqty[$key] + (float)$opn_mort_qty[$key] + (float)$opn_cull_qty[$key]));
                                $oamt = (((float)$opn_pur_camt[$key] + (float)$opn_tin_camt[$key]) - ((float)$opn_sale_camt[$key] + (float)$opn_tout_camt[$key] + (float)$opn_prsout_camt[$key] + (float)$opn_mort_amt[$key] + (float)$opn_cull_amt[$key]));
                                if((float)$oamt < 0){
                                    $t1 = ((float)$opn_pur_cqty[$key] + (float)$opn_tin_cqty[$key]);
                                    $t2 = ((float)$opn_pur_camt[$key] + (float)$opn_tin_camt[$key]);
                                    $t3 = 0;
                                    if((float)$t1 != 0){
                                        $t3 = ((float)$t2 / (float)$t1);
                                    }
                                    $oamt = (float)$t3 * (float)$oqty;
                                }
                                $opn_cqty[$key2] += (float)$oqty;
                                $opn_camt[$key2] += (float)$oamt;
                                //Purchase
                                $btw_pqty[$key2] += (float)$btw_pur_cqty[$key];
                                $btw_pamt[$key2] += (float)$btw_pur_camt[$key];
                                //Transfer-In
                                $btw_tiqty[$key2] += (float)$btw_tin_cqty[$key];
                                $btw_tiamt[$key2] += (float)$btw_tin_camt[$key];

                                //Chick Avg Price
                                $bin_qty = $bavg_prc = $bin_prc = 0; 
                                $bin_qty = ((float)$oqty + (float)$btw_pur_cqty[$key] + (float)$btw_tin_cqty[$key]);
                                $bin_amt = ((float)$oamt + (float)$btw_pur_camt[$key] + (float)$btw_tin_camt[$key]);
                                if((float)$bin_qty != 0){ $bin_prc = round(((float)$bin_amt / (float)$bin_qty),10); }
                                
                                //Sale
                                $btw_sqty[$key2] += (float)$btw_sale_cqty[$key];
                                $btw_swht[$key2] += (float)$btw_sale_cwht[$key];
                                $btw_samt[$key2] += (float)$btw_sale_camt[$key];

                                //Transfer-Out
                                $btw_toqty[$key2] += (float)$btw_tout_cqty[$key];
                                $tout_amt1 = 0;
                                $tout_amt1 = (float)$btw_tout_cqty[$key] * (float)$bin_prc;
                                //$btw_toamt[$key2] += (float)$btw_tout_camt[$key];
                                $btw_toamt[$key2] += (float)$tout_amt1;

                                //In-House Processing
                                $btw_ppqty[$key2] += (float)$btw_prsout_cqty[$key];
                                $btw_ppwht[$key2] += (float)$btw_prsout_cwht[$key];
                                $btw_ppamt[$key2] += (float)$btw_prsout_camt[$key];

                                //Mortality + Culls
                                $btw_mqty[$key2] += 0;
                                $btw_mamt[$key2] += 0;

                                $bcqty = (((Float)$oqty + (float)$btw_pur_cqty[$key] + (float)$btw_tin_cqty[$key]) - ((float)$btw_sale_cqty[$key] + (float)$btw_tout_cqty[$key] + (float)$btw_prsout_cqty[$key] + (float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]));
                                $bcamt = (((Float)$oamt + (float)$btw_pur_camt[$key] + (float)$btw_tin_camt[$key]) - ((float)$btw_sale_camt[$key] + (float)$tout_amt1 + (float)$btw_prsout_camt[$key] + (float)$btw_mort_amt[$key] + (float)$btw_cull_amt[$key]));

                                $btw_cqty[$key2] += (float)$bcqty;
                                $btw_camt[$key2] += (float)$bcamt;

                                //Calculating Totals
                                $topn_qty += (float)$oqty;
                                $topn_amt += (float)$oamt;
                                $tpur_qty += (float)$btw_pur_cqty[$key];
                                $tpur_amt += (float)$btw_pur_camt[$key];
                                $ttin_qty += (float)$btw_tin_cqty[$key];
                                $ttin_amt += (float)$btw_tin_camt[$key];
                                $tsale_qty += (float)$btw_sale_cqty[$key];
                                $tsale_wht += (float)$btw_sale_cwht[$key];
                                $tsale_amt += (float)$btw_sale_camt[$key];
                                $ttout_qty += (float)$btw_tout_cqty[$key];
                                $ttout_amt += (float)$tout_amt1;
                                $tpout_qty += (float)$btw_prsout_cqty[$key];
                                $tpout_wht += (float)$btw_prsout_cwht[$key];
                                $tpout_amt += (float)$btw_prsout_camt[$key];
                                $tmort_qty += ((float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]);
                                $tmort_amt += (((float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key]) * (float)$bin_prc);
                                $tclose_qty += (float)$bcqty;
                                $tclose_amt += (float)$bcamt;
                            }
                        }
                    }

                    if($fetch_type == "branch_wise"){
                        foreach($branch_code as $key2){
                            if($key2 == ""){ }
                            else{
                                if(empty($opn_cqty[$key2]) || $opn_cqty[$key2] == ""){ $opn_cqty[$key2] = 0; }
                                if(empty($opn_camt[$key2]) || $opn_camt[$key2] == ""){ $opn_camt[$key2] = 0; }
                                if(empty($btw_pqty[$key2]) || $btw_pqty[$key2] == ""){ $btw_pqty[$key2] = 0; }
                                if(empty($btw_pamt[$key2]) || $btw_pamt[$key2] == ""){ $btw_pamt[$key2] = 0; }
                                if(empty($btw_tiqty[$key2]) || $btw_tiqty[$key2] == ""){ $btw_tiqty[$key2] = 0; }
                                if(empty($btw_tiamt[$key2]) || $btw_tiamt[$key2] == ""){ $btw_tiamt[$key2] = 0; }
                                if(empty($btw_sqty[$key2]) || $btw_sqty[$key2] == ""){ $btw_sqty[$key2] = 0; }
                                if(empty($btw_swht[$key2]) || $btw_swht[$key2] == ""){ $btw_swht[$key2] = 0; }
                                if(empty($btw_samt[$key2]) || $btw_samt[$key2] == ""){ $btw_samt[$key2] = 0; }
                                if(empty($btw_toqty[$key2]) || $btw_toqty[$key2] == ""){ $btw_toqty[$key2] = 0; }
                                if(empty($btw_toamt[$key2]) || $btw_toamt[$key2] == ""){ $btw_toamt[$key2] = 0; }
                                if(empty($btw_ppqty[$key2]) || $btw_ppqty[$key2] == ""){ $btw_ppqty[$key2] = 0; }
                                if(empty($btw_ppwht[$key2]) || $btw_ppwht[$key2] == ""){ $btw_ppwht[$key2] = 0; }
                                if(empty($btw_ppamt[$key2]) || $btw_ppamt[$key2] == ""){ $btw_ppamt[$key2] = 0; }
                                if(empty($btw_exqty[$key2]) || $btw_exqty[$key2] == ""){ $btw_exqty[$key2] = 0; }
                                if(empty($btw_stqty[$key2]) || $btw_stqty[$key2] == ""){ $btw_stqty[$key2] = 0; }
                                if(empty($btw_mqty[$key2]) || $btw_mqty[$key2] == ""){ $btw_mqty[$key2] = 0; }
                                if(empty($btw_mamt[$key2]) || $btw_mamt[$key2] == ""){ $btw_mamt[$key2] = 0; }
                                if(empty($btw_cqty[$key2]) || $btw_cqty[$key2] == ""){ $btw_cqty[$key2] = 0; }
                                if(empty($btw_camt[$key2]) || $btw_camt[$key2] == ""){ $btw_camt[$key2] = 0; }
                                if((float)$opn_cqty[$key2] == 0 && (float)$btw_pqty[$key2] == 0 && (float)$btw_tiqty[$key2] == 0 && (float)$btw_sqty[$key2] == 0 && (float)$btw_toqty[$key2] == 0 && (float)$btw_ppqty[$key2] == 0 && (float)$btw_mqty[$key2] == 0 && (float)$btw_cqty[$key2] == 0){ }
                                else{
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$branch_name[$key2].'</td>';

                                    //Opening
                                    $avg_prc = 0; if((float)$opn_cqty[$key2] != 0){ $avg_prc = round(((float)$opn_camt[$key2] / (float)$opn_cqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_cqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_camt[$key2])).'</td>';
                                   
                                    //Purchase
                                    $avg_prc = 0; if((float)$btw_pqty[$key2] != 0){ $avg_prc = round(((float)$btw_pamt[$key2] / (float)$btw_pqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pamt[$key2])).'</td>';
                                    
                                    //Transfer-In
                                    $avg_prc = 0; if((float)$btw_tiqty[$key2] != 0){ $avg_prc = round(((float)$btw_tiamt[$key2] / (float)$btw_tiqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiamt[$key2])).'</td>';
                                    
                                    //Mortality
                                    $avg_prc = 0; if((float)$btw_mqty[$key2] != 0){ $avg_prc = round(((float)$btw_mamt[$key2] / (float)$btw_mqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mqty[$key2])).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mamt[$key2])).'</td>';
                                    
                                    //Sale
                                    $avg_prc = 0; if((float)$btw_sqty[$key2] != 0){ $avg_prc = round(((float)$btw_samt[$key2] / (float)$btw_sqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_sqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_swht[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_samt[$key2])).'</td>';
                                    
                                    //Transfer-Out
                                    $avg_prc = 0; if((float)$btw_toqty[$key2] != 0){ $avg_prc = round(((float)$btw_toamt[$key2] / (float)$btw_toqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toamt[$key2])).'</td>';
                                    
                                    //In-House Processing
                                    $avg_prc = 0; if((float)$btw_ppqty[$key2] != 0){ $avg_prc = round(((float)$btw_ppamt[$key2] / (float)$btw_ppqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppwht[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppamt[$key2])).'</td>';

                                    //Excess
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_exqty[$key2])).'</td>';
                                    
                                    //Short
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_stqty[$key2])).'</td>';
                                    
                                    //Closing
                                    $avg_prc = 0; if((float)$btw_cqty[$key2] != 0){ $avg_prc = round(((float)$btw_camt[$key2] / (float)$btw_cqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_cqty[$key2])).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_camt[$key2])).'</td>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    else if($fetch_type == "line_wise"){
                        foreach($line_code as $key2){
                            if($key2 == ""){ }
                            else{
                                if(empty($opn_cqty[$key2]) || $opn_cqty[$key2] == ""){ $opn_cqty[$key2] = 0; }
                                if(empty($opn_camt[$key2]) || $opn_camt[$key2] == ""){ $opn_camt[$key2] = 0; }
                                if(empty($btw_pqty[$key2]) || $btw_pqty[$key2] == ""){ $btw_pqty[$key2] = 0; }
                                if(empty($btw_pamt[$key2]) || $btw_pamt[$key2] == ""){ $btw_pamt[$key2] = 0; }
                                if(empty($btw_tiqty[$key2]) || $btw_tiqty[$key2] == ""){ $btw_tiqty[$key2] = 0; }
                                if(empty($btw_tiamt[$key2]) || $btw_tiamt[$key2] == ""){ $btw_tiamt[$key2] = 0; }
                                if(empty($btw_sqty[$key2]) || $btw_sqty[$key2] == ""){ $btw_sqty[$key2] = 0; }
                                if(empty($btw_swht[$key2]) || $btw_swht[$key2] == ""){ $btw_swht[$key2] = 0; }
                                if(empty($btw_samt[$key2]) || $btw_samt[$key2] == ""){ $btw_samt[$key2] = 0; }
                                if(empty($btw_toqty[$key2]) || $btw_toqty[$key2] == ""){ $btw_toqty[$key2] = 0; }
                                if(empty($btw_toamt[$key2]) || $btw_toamt[$key2] == ""){ $btw_toamt[$key2] = 0; }
                                if(empty($btw_ppqty[$key2]) || $btw_ppqty[$key2] == ""){ $btw_ppqty[$key2] = 0; }
                                if(empty($btw_ppwht[$key2]) || $btw_ppwht[$key2] == ""){ $btw_ppwht[$key2] = 0; }
                                if(empty($btw_ppamt[$key2]) || $btw_ppamt[$key2] == ""){ $btw_ppamt[$key2] = 0; }
                                if(empty($btw_exqty[$key2]) || $btw_exqty[$key2] == ""){ $btw_exqty[$key2] = 0; }
                                if(empty($btw_stqty[$key2]) || $btw_stqty[$key2] == ""){ $btw_stqty[$key2] = 0; }
                                if(empty($btw_mqty[$key2]) || $btw_mqty[$key2] == ""){ $btw_mqty[$key2] = 0; }
                                if(empty($btw_mamt[$key2]) || $btw_mamt[$key2] == ""){ $btw_mamt[$key2] = 0; }
                                if(empty($btw_cqty[$key2]) || $btw_cqty[$key2] == ""){ $btw_cqty[$key2] = 0; }
                                if(empty($btw_camt[$key2]) || $btw_camt[$key2] == ""){ $btw_camt[$key2] = 0; }
                                if((float)$opn_cqty[$key2] == 0 && (float)$btw_pqty[$key2] == 0 && (float)$btw_tiqty[$key2] == 0 && (float)$btw_sqty[$key2] == 0 && (float)$btw_toqty[$key2] == 0 && (float)$btw_ppqty[$key2] == 0 && (float)$btw_mqty[$key2] == 0 && (float)$btw_cqty[$key2] == 0){ }
                                else{
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$line_name[$key2].'</td>';

                                    //Opening
                                    $avg_prc = 0; if((float)$opn_cqty[$key2] != 0){ $avg_prc = round(((float)$opn_camt[$key2] / (float)$opn_cqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_cqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_camt[$key2])).'</td>';
                                   
                                    //Purchase
                                    $avg_prc = 0; if((float)$btw_pqty[$key2] != 0){ $avg_prc = round(((float)$btw_pamt[$key2] / (float)$btw_pqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pamt[$key2])).'</td>';
                                    
                                    //Transfer-In
                                    $avg_prc = 0; if((float)$btw_tiqty[$key2] != 0){ $avg_prc = round(((float)$btw_tiamt[$key2] / (float)$btw_tiqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiamt[$key2])).'</td>';
                                    
                                    //Mortality
                                    $avg_prc = 0; if((float)$btw_mqty[$key2] != 0){ $avg_prc = round(((float)$btw_mamt[$key2] / (float)$btw_mqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mqty[$key2])).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mamt[$key2])).'</td>';
                                    
                                    //Sale
                                    $avg_prc = 0; if((float)$btw_sqty[$key2] != 0){ $avg_prc = round(((float)$btw_samt[$key2] / (float)$btw_sqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_sqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_swht[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_samt[$key2])).'</td>';
                                    
                                    //Transfer-Out
                                    $avg_prc = 0; if((float)$btw_toqty[$key2] != 0){ $avg_prc = round(((float)$btw_toamt[$key2] / (float)$btw_toqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toamt[$key2])).'</td>';
                                    
                                    //In-House Processing
                                    $avg_prc = 0; if((float)$btw_ppqty[$key2] != 0){ $avg_prc = round(((float)$btw_ppamt[$key2] / (float)$btw_ppqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppwht[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppamt[$key2])).'</td>';

                                    //Excess
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_exqty[$key2])).'</td>';
                                    
                                    //Short
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_stqty[$key2])).'</td>';
                                    
                                    //Closing
                                    $avg_prc = 0; if((float)$btw_cqty[$key2] != 0){ $avg_prc = round(((float)$btw_camt[$key2] / (float)$btw_cqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_cqty[$key2])).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_camt[$key2])).'</td>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    else if($fetch_type == "supvr_wise"){
                        foreach($supervisor_code as $key2){
                            if($key2 == ""){ }
                            else{
                                if(empty($opn_cqty[$key2]) || $opn_cqty[$key2] == ""){ $opn_cqty[$key2] = 0; }
                                if(empty($opn_camt[$key2]) || $opn_camt[$key2] == ""){ $opn_camt[$key2] = 0; }
                                if(empty($btw_pqty[$key2]) || $btw_pqty[$key2] == ""){ $btw_pqty[$key2] = 0; }
                                if(empty($btw_pamt[$key2]) || $btw_pamt[$key2] == ""){ $btw_pamt[$key2] = 0; }
                                if(empty($btw_tiqty[$key2]) || $btw_tiqty[$key2] == ""){ $btw_tiqty[$key2] = 0; }
                                if(empty($btw_tiamt[$key2]) || $btw_tiamt[$key2] == ""){ $btw_tiamt[$key2] = 0; }
                                if(empty($btw_sqty[$key2]) || $btw_sqty[$key2] == ""){ $btw_sqty[$key2] = 0; }
                                if(empty($btw_swht[$key2]) || $btw_swht[$key2] == ""){ $btw_swht[$key2] = 0; }
                                if(empty($btw_samt[$key2]) || $btw_samt[$key2] == ""){ $btw_samt[$key2] = 0; }
                                if(empty($btw_toqty[$key2]) || $btw_toqty[$key2] == ""){ $btw_toqty[$key2] = 0; }
                                if(empty($btw_toamt[$key2]) || $btw_toamt[$key2] == ""){ $btw_toamt[$key2] = 0; }
                                if(empty($btw_ppqty[$key2]) || $btw_ppqty[$key2] == ""){ $btw_ppqty[$key2] = 0; }
                                if(empty($btw_ppwht[$key2]) || $btw_ppwht[$key2] == ""){ $btw_ppwht[$key2] = 0; }
                                if(empty($btw_ppamt[$key2]) || $btw_ppamt[$key2] == ""){ $btw_ppamt[$key2] = 0; }
                                if(empty($btw_exqty[$key2]) || $btw_exqty[$key2] == ""){ $btw_exqty[$key2] = 0; }
                                if(empty($btw_stqty[$key2]) || $btw_stqty[$key2] == ""){ $btw_stqty[$key2] = 0; }
                                if(empty($btw_mqty[$key2]) || $btw_mqty[$key2] == ""){ $btw_mqty[$key2] = 0; }
                                if(empty($btw_mamt[$key2]) || $btw_mamt[$key2] == ""){ $btw_mamt[$key2] = 0; }
                                if(empty($btw_cqty[$key2]) || $btw_cqty[$key2] == ""){ $btw_cqty[$key2] = 0; }
                                if(empty($btw_camt[$key2]) || $btw_camt[$key2] == ""){ $btw_camt[$key2] = 0; }
                                if((float)$opn_cqty[$key2] == 0 && (float)$btw_pqty[$key2] == 0 && (float)$btw_tiqty[$key2] == 0 && (float)$btw_sqty[$key2] == 0 && (float)$btw_toqty[$key2] == 0 && (float)$btw_ppqty[$key2] == 0 && (float)$btw_mqty[$key2] == 0 && (float)$btw_cqty[$key2] == 0){ }
                                else{
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$supervisor_name[$key2].'</td>';

                                    //Opening
                                    $avg_prc = 0; if((float)$opn_cqty[$key2] != 0){ $avg_prc = round(((float)$opn_camt[$key2] / (float)$opn_cqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_cqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_camt[$key2])).'</td>';
                                   
                                    //Purchase
                                    $avg_prc = 0; if((float)$btw_pqty[$key2] != 0){ $avg_prc = round(((float)$btw_pamt[$key2] / (float)$btw_pqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pamt[$key2])).'</td>';
                                    
                                    //Transfer-In
                                    $avg_prc = 0; if((float)$btw_tiqty[$key2] != 0){ $avg_prc = round(((float)$btw_tiamt[$key2] / (float)$btw_tiqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiamt[$key2])).'</td>';
                                    
                                    //Mortality
                                    $avg_prc = 0; if((float)$btw_mqty[$key2] != 0){ $avg_prc = round(((float)$btw_mamt[$key2] / (float)$btw_mqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mqty[$key2])).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mamt[$key2])).'</td>';
                                    
                                    //Sale
                                    $avg_prc = 0; if((float)$btw_sqty[$key2] != 0){ $avg_prc = round(((float)$btw_samt[$key2] / (float)$btw_sqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_sqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_swht[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_samt[$key2])).'</td>';
                                    
                                    //Transfer-Out
                                    $avg_prc = 0; if((float)$btw_toqty[$key2] != 0){ $avg_prc = round(((float)$btw_toamt[$key2] / (float)$btw_toqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toamt[$key2])).'</td>';
                                    
                                    //In-House Processing
                                    $avg_prc = 0; if((float)$btw_ppqty[$key2] != 0){ $avg_prc = round(((float)$btw_ppamt[$key2] / (float)$btw_ppqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppqty[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppwht[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppamt[$key2])).'</td>';

                                    //Excess
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_exqty[$key2])).'</td>';
                                    
                                    //Short
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_stqty[$key2])).'</td>';
                                    
                                    //Closing
                                    $avg_prc = 0; if((float)$btw_cqty[$key2] != 0){ $avg_prc = round(((float)$btw_camt[$key2] / (float)$btw_cqty[$key2]),2); }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_cqty[$key2])).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                    //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_camt[$key2])).'</td>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    else{
                        foreach($farm_alist as $fcode){
                            $blist = array(); $blist = explode(",",$batch_farm[$fcode]);
                            foreach($blist as $key2){
                                if($key2 == ""){ }
                                else{
                                    $brch = $farm_branch[$fcode]; $line = $farm_line[$fcode]; $supr = $farm_supervisor[$fcode];

                                    if(empty($opn_cqty[$key2]) || $opn_cqty[$key2] == ""){ $opn_cqty[$key2] = 0; }
                                    if(empty($opn_camt[$key2]) || $opn_camt[$key2] == ""){ $opn_camt[$key2] = 0; }
                                    if(empty($btw_pqty[$key2]) || $btw_pqty[$key2] == ""){ $btw_pqty[$key2] = 0; }
                                    if(empty($btw_pamt[$key2]) || $btw_pamt[$key2] == ""){ $btw_pamt[$key2] = 0; }
                                    if(empty($btw_tiqty[$key2]) || $btw_tiqty[$key2] == ""){ $btw_tiqty[$key2] = 0; }
                                    if(empty($btw_tiamt[$key2]) || $btw_tiamt[$key2] == ""){ $btw_tiamt[$key2] = 0; }
                                    if(empty($btw_sqty[$key2]) || $btw_sqty[$key2] == ""){ $btw_sqty[$key2] = 0; }
                                    if(empty($btw_swht[$key2]) || $btw_swht[$key2] == ""){ $btw_swht[$key2] = 0; }
                                    if(empty($btw_samt[$key2]) || $btw_samt[$key2] == ""){ $btw_samt[$key2] = 0; }
                                    if(empty($btw_toqty[$key2]) || $btw_toqty[$key2] == ""){ $btw_toqty[$key2] = 0; }
                                    if(empty($btw_toamt[$key2]) || $btw_toamt[$key2] == ""){ $btw_toamt[$key2] = 0; }
                                    if(empty($btw_ppqty[$key2]) || $btw_ppqty[$key2] == ""){ $btw_ppqty[$key2] = 0; }
                                    if(empty($btw_ppwht[$key2]) || $btw_ppwht[$key2] == ""){ $btw_ppwht[$key2] = 0; }
                                    if(empty($btw_ppamt[$key2]) || $btw_ppamt[$key2] == ""){ $btw_ppamt[$key2] = 0; }
                                    if(empty($btw_exqty[$key2]) || $btw_exqty[$key2] == ""){ $btw_exqty[$key2] = 0; }
                                    if(empty($btw_stqty[$key2]) || $btw_stqty[$key2] == ""){ $btw_stqty[$key2] = 0; }
                                    if(empty($btw_mqty[$key2]) || $btw_mqty[$key2] == ""){ $btw_mqty[$key2] = 0; }
                                    if(empty($btw_mamt[$key2]) || $btw_mamt[$key2] == ""){ $btw_mamt[$key2] = 0; }
                                    if(empty($btw_cqty[$key2]) || $btw_cqty[$key2] == ""){ $btw_cqty[$key2] = 0; }
                                    if(empty($btw_camt[$key2]) || $btw_camt[$key2] == ""){ $btw_camt[$key2] = 0; }
                                    if((float)$opn_cqty[$key2] == 0 && (float)$btw_pqty[$key2] == 0 && (float)$btw_tiqty[$key2] == 0 && (float)$btw_sqty[$key2] == 0 && (float)$btw_toqty[$key2] == 0 && (float)$btw_ppqty[$key2] == 0 && (float)$btw_mqty[$key2] == 0 && (float)$btw_cqty[$key2] == 0){ }
                                    else{
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$branch_name[$brch].'</td>';
                                        $html .= '<td style="text-align:left;">'.$line_name[$line].'</td>';
                                        $html .= '<td style="text-align:left;">'.$supervisor_name[$supr].'</td>';
                                        $html .= '<td style="text-align:left;">'.$farm_ccode[$fcode].'</td>';
                                        $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                        $html .= '<td style="text-align:left;">'.$batch_name[$key2].'</td>';
    
                                        //Opening
                                        $avg_prc = 0; if((float)$opn_cqty[$key2] != 0){ $avg_prc = round(((float)$opn_camt[$key2] / (float)$opn_cqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_cqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_camt[$key2])).'</td>';
                                       
                                        //Purchase
                                        $avg_prc = 0; if((float)$btw_pqty[$key2] != 0){ $avg_prc = round(((float)$btw_pamt[$key2] / (float)$btw_pqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pamt[$key2])).'</td>';
                                        
                                        //Transfer-In
                                        $avg_prc = 0; if((float)$btw_tiqty[$key2] != 0){ $avg_prc = round(((float)$btw_tiamt[$key2] / (float)$btw_tiqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiamt[$key2])).'</td>';
                                        
                                        //Mortality
                                        $avg_prc = 0; if((float)$btw_mqty[$key2] != 0){ $avg_prc = round(((float)$btw_mamt[$key2] / (float)$btw_mqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mqty[$key2])).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mamt[$key2])).'</td>';
                                        
                                        //Sale
                                        $avg_prc = 0; if((float)$btw_sqty[$key2] != 0){ $avg_prc = round(((float)$btw_samt[$key2] / (float)$btw_sqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_sqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_swht[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_samt[$key2])).'</td>';
                                        
                                        //Transfer-Out
                                        $avg_prc = 0; if((float)$btw_toqty[$key2] != 0){ $avg_prc = round(((float)$btw_toamt[$key2] / (float)$btw_toqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toamt[$key2])).'</td>';
                                        
                                        //In-House Processing
                                        $avg_prc = 0; if((float)$btw_ppqty[$key2] != 0){ $avg_prc = round(((float)$btw_ppamt[$key2] / (float)$btw_ppqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppwht[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppamt[$key2])).'</td>';

                                        //Excess
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_exqty[$key2])).'</td>';
                                        
                                        //Short
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_stqty[$key2])).'</td>';
                                        
                                        //Closing
                                        $avg_prc = 0; if((float)$btw_cqty[$key2] != 0){ $avg_prc = round(((float)$btw_camt[$key2] / (float)$btw_cqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_cqty[$key2])).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_camt[$key2])).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                            }
                        }
                    }
                    if($loc_type == "all" || $loc_type == "only_sectors"){
                        if($sec_list != ""){
                            foreach($sector_acode as $key2){
                                if($key2 == ""){ }
                                else{
                                    if(empty($opn_cqty[$key2]) || $opn_cqty[$key2] == ""){ $opn_cqty[$key2] = 0; }
                                    if(empty($opn_camt[$key2]) || $opn_camt[$key2] == ""){ $opn_camt[$key2] = 0; }
                                    if(empty($btw_pqty[$key2]) || $btw_pqty[$key2] == ""){ $btw_pqty[$key2] = 0; }
                                    if(empty($btw_pamt[$key2]) || $btw_pamt[$key2] == ""){ $btw_pamt[$key2] = 0; }
                                    if(empty($btw_tiqty[$key2]) || $btw_tiqty[$key2] == ""){ $btw_tiqty[$key2] = 0; }
                                    if(empty($btw_tiamt[$key2]) || $btw_tiamt[$key2] == ""){ $btw_tiamt[$key2] = 0; }
                                    if(empty($btw_sqty[$key2]) || $btw_sqty[$key2] == ""){ $btw_sqty[$key2] = 0; }
                                    if(empty($btw_swht[$key2]) || $btw_swht[$key2] == ""){ $btw_swht[$key2] = 0; }
                                    if(empty($btw_samt[$key2]) || $btw_samt[$key2] == ""){ $btw_samt[$key2] = 0; }
                                    if(empty($btw_toqty[$key2]) || $btw_toqty[$key2] == ""){ $btw_toqty[$key2] = 0; }
                                    if(empty($btw_toamt[$key2]) || $btw_toamt[$key2] == ""){ $btw_toamt[$key2] = 0; }
                                    if(empty($btw_ppqty[$key2]) || $btw_ppqty[$key2] == ""){ $btw_ppqty[$key2] = 0; }
                                    if(empty($btw_ppwht[$key2]) || $btw_ppwht[$key2] == ""){ $btw_ppwht[$key2] = 0; }
                                    if(empty($btw_ppamt[$key2]) || $btw_ppamt[$key2] == ""){ $btw_ppamt[$key2] = 0; }
                                    if(empty($btw_exqty[$key2]) || $btw_exqty[$key2] == ""){ $btw_exqty[$key2] = 0; }
                                    if(empty($btw_stqty[$key2]) || $btw_stqty[$key2] == ""){ $btw_stqty[$key2] = 0; }
                                    if(empty($btw_mqty[$key2]) || $btw_mqty[$key2] == ""){ $btw_mqty[$key2] = 0; }
                                    if(empty($btw_mamt[$key2]) || $btw_mamt[$key2] == ""){ $btw_mamt[$key2] = 0; }
                                    if(empty($btw_cqty[$key2]) || $btw_cqty[$key2] == ""){ $btw_cqty[$key2] = 0; }
                                    if(empty($btw_camt[$key2]) || $btw_camt[$key2] == ""){ $btw_camt[$key2] = 0; }
                                    if((float)$opn_cqty[$key2] == 0 && (float)$btw_pqty[$key2] == 0 && (float)$btw_tiqty[$key2] == 0 && (float)$btw_sqty[$key2] == 0 && (float)$btw_toqty[$key2] == 0 && (float)$btw_ppqty[$key2] == 0 && (float)$btw_mqty[$key2] == 0 && (float)$btw_cqty[$key2] == 0){ }
                                    else{
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$sector_name[$key2].'</td>';
        
                                        //Opening
                                        $avg_prc = 0; if((float)$opn_cqty[$key2] != 0){ $avg_prc = round(((float)$opn_camt[$key2] / (float)$opn_cqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_cqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_camt[$key2])).'</td>';
                                       
                                        //Purchase
                                        $avg_prc = 0; if((float)$btw_pqty[$key2] != 0){ $avg_prc = round(((float)$btw_pamt[$key2] / (float)$btw_pqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_pamt[$key2])).'</td>';
                                        
                                        //Transfer-In
                                        $avg_prc = 0; if((float)$btw_tiqty[$key2] != 0){ $avg_prc = round(((float)$btw_tiamt[$key2] / (float)$btw_tiqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_tiamt[$key2])).'</td>';
                                        
                                        //Mortality
                                        $avg_prc = 0; if((float)$btw_mqty[$key2] != 0){ $avg_prc = round(((float)$btw_mamt[$key2] / (float)$btw_mqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mqty[$key2])).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mamt[$key2])).'</td>';
                                        
                                        //Sale
                                        $avg_prc = 0; if((float)$btw_sqty[$key2] != 0){ $avg_prc = round(((float)$btw_samt[$key2] / (float)$btw_sqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_sqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_swht[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_samt[$key2])).'</td>';
                                        
                                        //Transfer-Out
                                        $avg_prc = 0; if((float)$btw_toqty[$key2] != 0){ $avg_prc = round(((float)$btw_toamt[$key2] / (float)$btw_toqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_toamt[$key2])).'</td>';
                                        
                                        //In-House Processing
                                        $avg_prc = 0; if((float)$btw_ppqty[$key2] != 0){ $avg_prc = round(((float)$btw_ppamt[$key2] / (float)$btw_ppqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppqty[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppwht[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_ppamt[$key2])).'</td>';

                                        //Excess
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_exqty[$key2])).'</td>';
                                        
                                        //Short
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_stqty[$key2])).'</td>';
                                        
                                        //Closing
                                        $avg_prc = 0; if((float)$btw_cqty[$key2] != 0){ $avg_prc = round(((float)$btw_camt[$key2] / (float)$btw_cqty[$key2]),2); }
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_cqty[$key2])).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                        //$html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_camt[$key2])).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                            }
                        }
                    }
                    
                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    if($fetch_type == "farm_wise"){ $html .= '<th style="text-align:left;" colspan="6">Total</th>'; } else{ $html .= '<th style="text-align:left;">Total</th>'; }

                    //Opening
                    $avg_prc = 0; if((float)$topn_qty != 0){ $avg_prc = round(((float)$topn_amt / (float)$topn_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($topn_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($topn_amt)).'</th>';
                   
                    //Purchase
                    $avg_prc = 0; if((float)$tpur_qty != 0){ $avg_prc = round(((float)$tpur_amt / (float)$tpur_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpur_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpur_amt)).'</th>';
                    
                    //Transfer-In
                    $avg_prc = 0; if((float)$ttin_qty != 0){ $avg_prc = round(((float)$ttin_amt / (float)$ttin_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttin_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttin_amt)).'</th>';
                    
                    //Mortality
                    $avg_prc = 0; if((float)$tmort_qty != 0){ $avg_prc = round(((float)$tmort_amt / (float)$tmort_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tmort_qty)).'</th>';
                    //$html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    //$html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tmort_amt)).'</th>';
                    
                    //Sale
                    $avg_prc = 0; if((float)$tsale_qty != 0){ $avg_prc = round(((float)$tsale_amt / (float)$tsale_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tsale_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tsale_wht)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tsale_amt)).'</th>';
                    
                    //Transfer-Out
                    $avg_prc = 0; if((float)$ttout_qty != 0){ $avg_prc = round(((float)$ttout_amt / (float)$ttout_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttout_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttout_amt)).'</th>';
                    
                    //In-House Processing
                    $avg_prc = 0; if((float)$tpout_qty != 0){ $avg_prc = round(((float)$tpout_amt / (float)$tpout_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpout_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpout_wht)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpout_amt)).'</th>';

                    //Excess
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($texs_qty)).'</th>';
                    
                    //Short
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tsrt_qty)).'</th>';
                    
                    //Closing
                    $avg_prc = 0; if((float)$tclose_qty != 0){ $avg_prc = round(((float)$tclose_amt / (float)$tclose_qty),2); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tclose_qty)).'</th>';
                    //$html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    //$html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tclose_amt)).'</th>';
                    $html .= '</tr>';
                    $html .= '</tfoot>';
                    
                    echo $html;
                }
            }
        ?>
        </table><br/><br/><br/>
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_line[$fcode])){ $l_code = $farm_line[$fcode]; } else{ $l_code = ""; }
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
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
                    slnos();
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
                    slnos();
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
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search_table');
                const table = document.getElementById('main_table');
                const tableBody = table.querySelector('tbody');

                searchInput.addEventListener('input', () => {
                    const filter = searchInput.value.toLowerCase();
                    const rows = tableBody.querySelectorAll('tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    var fetch_type = '<?php echo $fetch_type; ?>';
                    html +='<tr style="text-align:center;" align="center">';
                    if(fetch_type == "branch_wise"){ html +='<th>Branch</th>'; }
                    else if(fetch_type == "line_wise"){ html +='<th>Line</th>'; }
                    else if(fetch_type == "supvr_wise"){ html +='<th>Supervisor</th>'; }
                    else{ html +='<th>Branch</th><th>Line</th><th>Supervisor</th><th>Farm Code</th><th>Farm</th><th>Batch</th>'; }
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='</tr>';
                    $('#head_names').append(html);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html +='<tr style="text-align:center;" align="center">';
                    if(fetch_type == "branch_wise"){ html +='<th id="order">Branch</th>'; }
                    else if(fetch_type == "line_wise"){ html +='<th id="order">Line</th>'; }
                    else if(fetch_type == "supvr_wise"){ html +='<th id="order">Supervisor</th>'; }
                    else{ html +='<th id="order">Branch</th><th id="order">Line</th><th id="order">Supervisor</th><th id="order">Farm Code</th><th id="order">Farm</th><th id="order">Batch</th>'; }
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='</tr>';
                    //$('#head_names').append(html);
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
        </script>
        <script>
            function fetch_row_height(){
                var table_elements = document.querySelector("table>tbody");
                var i; var max_height = 0;
                for(i = 1; i <= table_elements.rows.length; i++){
                    var row_selector = "table>tbody>tr:nth-child(" + [i] + ")";
                    var table_row = document.querySelector(row_selector);
                    var vertical_spacing = window.getComputedStyle(table_row).getPropertyValue("-webkit-border-vertical-spacing");
                    var margin_top = window.getComputedStyle(table_row).getPropertyValue("margin-top");
                    var margin_bottom = window.getComputedStyle(table_row).getPropertyValue("margin-bottom");
                    var row_height= parseInt(vertical_spacing, 10)+parseInt(margin_top, 10)+parseInt(margin_bottom, 10)+table_row.offsetHeight;
                    if(max_height <= row_height){
                        max_height = row_height;
                    }
                }
                //alert("The height is: "+max_height+"px");
                document.getElementById("thead2_empty_row").style.height = max_height+"px";
            }
            fetch_row_height();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>