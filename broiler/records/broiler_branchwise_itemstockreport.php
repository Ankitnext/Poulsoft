<?php
//broiler_branchwise_itemstockreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Branch Wise Feed Stock";
    include "header_head.php";
    $form_path = "broiler_branchwise_itemstockreport.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Branch Wise Feed Stock";
    include "header_head.php";
    $form_path = "broiler_branchwise_itemstockreport.php?db=$db&userid=".$user_code;
}

$file_name = "Branch Wise Feed Stock";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("item_stocktransfers", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stocktransfers LIKE poulso6_admin_broiler_broilermaster.item_stocktransfers;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_sales", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sales LIKE poulso6_admin_broiler_broilermaster.broiler_sales;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_daily_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_daily_record LIKE poulso6_admin_broiler_broilermaster.broiler_daily_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_medicine_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_medicine_record LIKE poulso6_admin_broiler_broilermaster.broiler_medicine_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_itemreturns", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_itemreturns LIKE poulso6_admin_broiler_broilermaster.broiler_itemreturns;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_inv_intermediate_issued", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_inv_intermediate_issued LIKE poulso6_admin_broiler_broilermaster.broiler_inv_intermediate_issued;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_inv_intermediate_received", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_inv_intermediate_received LIKE poulso6_admin_broiler_broilermaster.broiler_inv_intermediate_received;"; mysqli_query($conn,$sql1); }

/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

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

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND (`description` LIKE '%feed%' OR `description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$item_list = implode("','", $icat_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_category = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `main_officetypes` WHERE `description` NOT LIKE '%plant%' AND `active` = 1 AND `dflag` = 0";
$query = mysqli_query($conn,$sql); $office_alist = array();
while($row = mysqli_fetch_assoc($query)){ $office_alist[$row["code"]] = $row["code"]; }

$office_list = implode("','", $office_alist);
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$office_list') AND `active` = '1'".$sector_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = $item_cat = $items = $sectors = $loc_type = "all"; $fetch_type = "branch_wise"; /*$batch_type = "Live";*/ $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $fetch_type = $_POST['fetch_type'];
    $sectors = $_POST['sectors'];
    $loc_type = $_POST['loc_type'];
    $item_cat = $_POST['item_cat'];
    $items = $_POST['items'];
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
                            </div>
                            <div class="row">
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
                                    <label>Locatioon Type</label>
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
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; // AND (`gc_flag` = '0' OR `start_date` <= '$tdate' AND `end_date` >= '$fdate' AND `gc_flag` = '1')
                $query = mysqli_query($conn,$sql); $batch_alist = $batch_afarm = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; $batch_afarm[$row['code']] = $row['farm_code']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    //Fetch Item Details
                    $item_list = "";
                    if($items != "all"){ $item_list = $items; }
                    else if($item_cat != "all"){ foreach($item_code as $icode){ if(!empty($item_category[$icode]) && $item_category[$icode] == $item_cat){ if($item_list == ""){ $item_list = $icode; } else{ $item_list = $item_list."','".$icode; } } } }
                    else{ foreach($item_code as $icode){ if($item_list == ""){ $item_list = $icode; } else{ $item_list = $item_list."','".$icode; } } }
    
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    
                    $sec_list = "";
                    if($sectors == "all"){ foreach($sector_code as $wcode){ if($sec_list == ""){ $sec_list = $wcode; } else{ $sec_list = $sec_list."','".$wcode; } } }
                    else if($sectors != "all" && $sectors != "none"){ $sec_list = $sectors; } else{ }
                    
                    $op_fltr = $p_fltr = $ti_fltr = $s_fltr = $to_fltr = $pp_fltr = $d_fltr = $mv_fltr = $ii_fltr = $ir_fltr = "";
                    if($loc_type == "all"){
                        if($sec_list != ""){
                            $p_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $ti_fltr = " AND (`towarehouse` IN ('$sec_list') OR `to_batch` IN ('$batch_list'))";
                            $op_fltr = " AND (`sector_code` IN ('$sec_list'))";
                            $s_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $to_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                            $pp_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                            $pr_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $d_fltr = " AND `batch_code` IN ('$batch_list')";
                            $mv_fltr = " AND `batch_code` IN ('$batch_list')";
                            $ii_fltr = " AND (`sector` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                            $ir_fltr = " AND (`sector` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        }
                        else{
                            $p_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $ti_fltr = " AND `to_batch` IN ('$batch_list')";
                            $op_fltr = " AND (`sector_code` IN ('none'))";
                            $s_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $to_fltr = " AND `from_batch` IN ('$batch_list')";
                            $pp_fltr = " AND `from_batch` IN ('$batch_list')";
                            $pr_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $d_fltr = " AND `batch_code` IN ('$batch_list')";
                            $mv_fltr = " AND `batch_code` IN ('$batch_list')";
                            $ii_fltr = " AND `farm_batch` IN ('$batch_list')";
                            $ir_fltr = " AND `farm_batch` IN ('$batch_list')";
                        }
                    }
                    else if($loc_type == "only_farms"){
                        $p_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $ti_fltr = " AND `to_batch` IN ('$batch_list')";
                        $op_fltr = " AND (`sector_code` IN ('none'))";
                        $s_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $to_fltr = " AND `from_batch` IN ('$batch_list')";
                        $pp_fltr = " AND `from_batch` IN ('$batch_list')";
                        $pr_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $d_fltr = " AND `batch_code` IN ('$batch_list')";
                        $mv_fltr = " AND `batch_code` IN ('$batch_list')";
                        $ii_fltr = " AND `farm_batch` IN ('$batch_list')";
                        $ir_fltr = " AND `farm_batch` IN ('$batch_list')";
                    }
                    else if($loc_type == "only_sectors"){
                        $p_fltr = " AND `warehouse` IN ('$sec_list')";
                        $ti_fltr = " AND `towarehouse` IN ('$sec_list')";
                        $op_fltr = " AND (`sector_code` IN ('$sec_list'))";
                        $s_fltr = " AND `warehouse` IN ('$sec_list')";
                        $to_fltr = " AND `fromwarehouse` IN ('$sec_list')";
                        $pp_fltr = " AND `fromwarehouse` IN ('$sec_list')";
                        $pr_fltr = " AND `warehouse` IN ('$sec_list')";
                        $d_fltr = " AND `batch_code` IN ('none')";
                        $mv_fltr = " AND `batch_code` IN ('none')";
                        $ii_fltr = " AND `sector` IN ('$sec_list')";
                        $ir_fltr = " AND `sector` IN ('$sec_list')";
                    }
                    else{
                        $p_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $ti_fltr = " AND (`towarehouse` IN ('$sec_list') OR `to_batch` IN ('$batch_list'))";
                        $op_fltr = " AND (`sector_code` IN ('$sec_list'))";
                        $s_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $to_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                        $pp_fltr = " AND (`fromwarehouse` IN ('$sec_list') OR `from_batch` IN ('$batch_list'))";
                        $pr_fltr = " AND (`warehouse` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $d_fltr = " AND `batch_code` IN ('$batch_list')";
                        $mv_fltr = " AND `batch_code` IN ('$batch_list')";
                        $ii_fltr = " AND (`sector` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                        $ir_fltr = " AND (`sector` IN ('$sec_list') OR `farm_batch` IN ('$batch_list'))";
                    }
                    
                    $act_icode = array();
                    //Purchase
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list')".$p_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $pur_sqty = $pur_samt = $act_batch = array();
                    while($row = mysqli_fetch_array($query)){
                        $idate = $row['date']; $fitem = $row['icode']; if($row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['warehouse']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $pur_sqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $pur_samt[$key] += (float)$row['item_tamt'];
                        //echo "<br/>".$row['date']."@".$row['trnum']."@".$row['icode']."@".((float)$row['rcd_qty'] + (float)$row['fre_qty'])."@".(float)$row['item_tamt'];
                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }
                    //Stock-In
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list')".$ti_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                    $query = mysqli_query($conn,$sql); $tin_sqty = $tin_samt = array();
                    while($row = mysqli_fetch_array($query)){
                        $idate = $row['date']; $fitem = $row['code']; if($row['to_batch'] != ""){ $ibatch = $row['to_batch']; } else{ $ibatch = $row['towarehouse']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $tin_sqty[$key] += (float)$row['quantity'];
                        $tin_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }
                    //openings
                    $sql = "SELECT * FROM `broiler_openings` WHERE `date` <= '$tdate' AND `type_code` IN ('$item_list')".$op_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`sector_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_array($query)){
                        $idate = $row['date']; $fitem = $row['type_code']; $ibatch = $row['sector_code'];
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $tin_sqty[$key] += (float)$row['quantity'];
                        $tin_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }
                    //Sale
                    $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list')".$s_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $sale_sqty = $sale_samt = array();
                    while($row = mysqli_fetch_array($query)){
                        $idate = $row['date']; $fitem = $row['icode']; if(!empty($farm_code[$row['warehouse']]) && $farm_code[$row['warehouse']] == $row['warehouse'] && $row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['warehouse']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $sale_sqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $sale_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }
                    //Stock-Out
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list')".$to_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $tout_sqty = $tout_samt = array();
                    while($row = mysqli_fetch_array($query)){
                        $idate = $row['date']; $fitem = $row['code']; if($row['from_batch'] != ""){ $ibatch = $row['from_batch']; } else{ $ibatch = $row['fromwarehouse']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        //echo "<br/>".$row['trnum']."@".$row['from_batch']."@".$row['fromwarehouse']."@".$row['quantity'];
                        
                        $tout_sqty[$key] += (float)$row['quantity'];
                        $tout_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }
                    //Day Record
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate'".$d_fltr." AND (`item_code1` IN ('$item_list') OR `item_code2` IN ('$item_list')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $dcon_sqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['item_code1']; $ibatch = $row['batch_code']; $key = $idate."@".$fitem."@".$ibatch;
                        if(str_contains($item_list, $fitem)){
                            $dcon_sqty[$key] += (float)$row['kgs1'];
                            $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        }
                        $idate = $row['date']; $fitem = $row['item_code2']; $ibatch = $row['batch_code']; $key = $idate."@".$fitem."@".$ibatch;
                        if(str_contains($item_list, $fitem)){
                            $dcon_sqty[$key] += (float)$row['kgs2'];
                            $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        }
                        
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    //MedVac Record
                    $sql = "SELECT * FROM `broiler_medicine_record` WHERE `date` <= '$tdate'".$mv_fltr." AND `item_code` IN ('$item_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $mcon_sqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['item_code']; $ibatch = $row['batch_code'];
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $mcon_sqty[$key] += (float)$row['quantity'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    //Returns
                    $sql = "SELECT * FROM `broiler_itemreturns` WHERE `date` <= '$tdate'".$pr_fltr." AND `itemcode` IN ('$item_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $prtn_sqty = $prtn_samt = $srtn_sqty = $srtn_samt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['itemcode']; if($row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['warehouse']; }
                        $key = $idate."@".$fitem."@".$ibatch;

                        if($row['type'] == "Supplier"){
                            $prtn_sqty[$key] += (float)$row['quantity'];
                            $prtn_samt[$key] += (float)$row['amount'];
                        }
                        else{
                            $srtn_sqty[$key] += (float)$row['quantity'];
                            $srtn_samt[$key] += (float)$row['amount'];
                        }

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    //Issue
                    $sql = "SELECT * FROM `broiler_inv_intermediate_issued` WHERE `date` <= '$tdate'".$ii_fltr." AND `item_code` IN ('$item_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $isu_sqty = $isu_samt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['item_code']; if($row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['sector']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $isu_sqty[$key] += (float)$row['quantity'];
                        $isu_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    //Adjust Deduct
                    $sql = "SELECT * FROM `broiler_inv_adjustment` WHERE `date` <= '$tdate'".$ii_fltr." AND `item_code` IN ('$item_list') AND `a_type` LIKE 'deduct' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); //$isu_sqty = $isu_samt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['item_code']; if($row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['sector']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $isu_sqty[$key] += (float)$row['quantity'];
                        $isu_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    //Received
                    $sql = "SELECT * FROM `broiler_inv_intermediate_received` WHERE `date` <= '$tdate'".$ir_fltr." AND `item_code` IN ('$item_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $rcd_sqty = $rcd_samt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['item_code']; if($row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['sector']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $rcd_sqty[$key] += (float)$row['quantity'];
                        $rcd_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    //Adjust Add
                    $sql = "SELECT * FROM `broiler_inv_adjustment` WHERE `date` <= '$tdate'".$ir_fltr." AND `item_code` IN ('$item_list') AND `a_type` LIKE 'add' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $rcd_sqty = $rcd_samt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $idate = $row['date']; $fitem = $row['item_code']; if($row['farm_batch'] != ""){ $ibatch = $row['farm_batch']; } else{ $ibatch = $row['sector']; }
                        $key = $idate."@".$fitem."@".$ibatch;
                        
                        $rcd_sqty[$key] += (float)$row['quantity'];
                        $rcd_samt[$key] += (float)$row['amount'];

                        $act_icode[$fitem] = $fitem; $act_batch[$ibatch] = $ibatch;
                        if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                        if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                    }

                    $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_book = $batch_farm = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_farm[$row['farm_code']] .= $row['code'].","; }
                    
                    $stk_qty = $stk_prc = $stk_amt = array(); $tpur_qty = 0;
                    for($cdate = strtotime($sdate); $cdate <= strtotime($edate); $cdate += (86400)){
                        $adate = date("Y-m-d",$cdate);
                        foreach($act_icode as $icode){
                            if($icode != ""){
                                foreach($act_batch as $bcode){
                                    if(empty($batch_code[$bcode]) || $batch_code[$bcode] == ""){ }
                                    else{
                                        $key = $adate."@".$icode."@".$bcode;
        
                                        $fcode = $batch_afarm[$bcode]; $bhcode = $farm_branch[$fcode]; $lcode = $farm_line[$fcode]; $scode = $farm_supervisor[$fcode];
                                        if($fetch_type == "branch_wise"){ $key2 = $icode."@".$bhcode; } else if($fetch_type == "line_wise"){ $key2 = $icode."@".$lcode; }
                                        else if($fetch_type == "supvr_wise"){ $key2 = $icode."@".$scode; } else{ $key2 = $icode."@".$bcode; }
        
                                        
        
                                        //Initialization
                                        if(empty($pur_sqty[$key]) || $pur_sqty[$key] == ""){ $pur_sqty[$key] = 0; }
                                        if(empty($pur_samt[$key]) || $pur_samt[$key] == ""){ $pur_samt[$key] = 0; }
        
                                        if(empty($tin_sqty[$key]) || $tin_sqty[$key] == ""){ $tin_sqty[$key] = 0; }
                                        if(empty($tin_samt[$key]) || $tin_samt[$key] == ""){ $tin_samt[$key] = 0; }
        
                                        if(empty($sale_sqty[$key]) || $sale_sqty[$key] == ""){ $sale_sqty[$key] = 0; }
                                        if(empty($sale_samt[$key]) || $sale_samt[$key] == ""){ $sale_samt[$key] = 0; }
        
                                        if(empty($tout_sqty[$key]) || $tout_sqty[$key] == ""){ $tout_sqty[$key] = 0; }
                                        if(empty($tout_samt[$key]) || $tout_samt[$key] == ""){ $tout_samt[$key] = 0; }
        
                                        if(empty($dcon_sqty[$key]) || $dcon_sqty[$key] == ""){ $dcon_sqty[$key] = 0; }
                                        if(empty($mcon_sqty[$key]) || $mcon_sqty[$key] == ""){ $mcon_sqty[$key] = 0; }
        
                                        if(empty($prtn_sqty[$key]) || $prtn_sqty[$key] == ""){ $prtn_sqty[$key] = 0; }
                                        if(empty($prtn_samt[$key]) || $prtn_samt[$key] == ""){ $prtn_samt[$key] = 0; }
        
                                        if(empty($srtn_sqty[$key]) || $srtn_sqty[$key] == ""){ $srtn_sqty[$key] = 0; }
                                        if(empty($srtn_samt[$key]) || $srtn_samt[$key] == ""){ $srtn_samt[$key] = 0; }
        
                                        if(empty($isu_sqty[$key]) || $isu_sqty[$key] == ""){ $isu_sqty[$key] = 0; }
                                        if(empty($isu_samt[$key]) || $isu_samt[$key] == ""){ $isu_samt[$key] = 0; }
        
                                        if(empty($rcd_sqty[$key]) || $rcd_sqty[$key] == ""){ $rcd_sqty[$key] = 0; }
                                        if(empty($rcd_samt[$key]) || $rcd_samt[$key] == ""){ $rcd_samt[$key] = 0; }
        
                                        if(empty($stk_qty[$key2]) || $stk_qty[$key2] == ""){ $stk_qty[$key2] = 0; }
                                        if(empty($stk_prc[$key2]) || $stk_prc[$key2] == ""){ $stk_prc[$key2] = 0; }
                                        if(empty($stk_amt[$key2]) || $stk_amt[$key2] == ""){ $stk_amt[$key2] = 0; }
        
                                        //Purchase
                                        $stk_qty[$key2] += (float)$pur_sqty[$key]; $stk_amt[$key2] += (float)$pur_samt[$key];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } else{ $stk_prc[$key2] = 0; }
                                        //Transfer-In
                                        $stk_qty[$key2] += (float)$tin_sqty[$key]; $stk_amt[$key2] += (float)$tin_samt[$key];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } else{ $stk_prc[$key2] = 0; }
                                        //Sale Return
                                        $stk_qty[$key2] += (float)$srtn_sqty[$key]; $stk_amt[$key2] += (float)$srtn_samt[$key];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } else{ $stk_prc[$key2] = 0; }
                                        //Intermediate-Received
                                        $stk_qty[$key2] += (float)$rcd_sqty[$key]; $stk_amt[$key2] += (float)$rcd_samt[$key2];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } else{ $stk_prc[$key2] = 0; }
        
                                        //Sale
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$sale_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$sale_sqty[$key]);
                                        //Transfer-Out
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$tout_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$tout_sqty[$key]);
                                        //Daily Consumed
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$dcon_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$dcon_sqty[$key]);
                                        //MedVac Consumed
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$mcon_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$mcon_sqty[$key]);
                                        //Purchase Return
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$prtn_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$prtn_sqty[$key]);
                                        //Intermediate-Issue
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$isu_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$isu_sqty[$key]);
        
                                        //echo "<br/>".date("d.m.Y",strtotime($adate))."@".$icode."@".$stk_qty[$key2]."@".$stk_prc[$key2]."@".$stk_amt[$key2];
                                        if(strtotime($adate) < strtotime($fdate)){
                                            if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                            if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
                                            $opn_sqty[$key2] = $stk_qty[$key2]; $opn_samt[$key2] = $stk_amt[$key2];
                                        }
                                        else{
                                            if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                            if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
                                            if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                            if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
                                            if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                            if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
                                            if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                            if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
        
                                            //Purchase
                                            $purin_sqty[$key2] += (float)$pur_sqty[$key]; //$purin_samt[$key2] += (float)$pur_samt[$key];
                                            //$purin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$pur_sqty[$key]);
                                            $purin_samt[$key2] += ((float)$pur_samt[$key]);
        
                                            //Transfer-In,Sale Return, Intermediate Received
                                            $stkin_sqty[$key2] += (float)$tin_sqty[$key]; //$stkin_samt[$key2] += (float)$tin_samt[$key];
                                            $stkin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$tin_sqty[$key]);
                                            $stkin_sqty[$key2] += (float)$srtn_sqty[$key]; //$stkin_samt[$key2] += (float)$srtn_samt[$key];
                                            $stkin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$srtn_sqty[$key]);
                                            $stkin_sqty[$key2] += (float)$rcd_sqty[$key]; //$stkin_samt[$key2] += (float)$rcd_samt[$key];
                                            $stkin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$rcd_sqty[$key]);
        
                                            //Avg. Price
                                            $sqty = 0; $sqty = (float)$opn_sqty[$key2] + (float)$purin_sqty[$key2] + (float)$stkin_sqty[$key2];
                                            $samt = 0; $samt = (float)$opn_samt[$key2] + (float)$purin_samt[$key2] + (float)$stkin_samt[$key2];
                                            //$avg_prc = 0; if((float)$sqty != 0){ $avg_prc = (float)$samt / (float)$sqty; }
                                            $avg_prc = (float)$stk_prc[$key2];
        
                                            //Daily Consumed, MedVac Consumed
                                            $stkcon_sqty[$key2] += (float)$dcon_sqty[$key];
                                            $stkcon_samt[$key2] += ((float)$avg_prc * (float)$dcon_sqty[$key]);
                                            $stkcon_sqty[$key2] += (float)$mcon_sqty[$key];
                                            $stkcon_samt[$key2] += ((float)$avg_prc * (float)$mcon_sqty[$key]);
        
                                            //Sale, Transfer-Out, Purchase Return, Intermediate Issue
                                            $stkout_sqty[$key2] += (float)$sale_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$sale_sqty[$key]);
                                            $stkout_sqty[$key2] += (float)$tout_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$tout_sqty[$key]);
                                            $stkout_sqty[$key2] += (float)$prtn_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$prtn_sqty[$key]);
                                            $stkout_sqty[$key2] += (float)$isu_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$isu_sqty[$key]);
        
                                            //Closing
                                            $cls_sqty[$key2] = (float)$stk_qty[$key2];
                                            $cls_samt[$key2] = ((float)$stk_prc[$key2] * (float)$stk_qty[$key2]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Sector Wise Calculations
                    if($sec_list != ""){
                        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `code` IN ('$sec_list')".$sector_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC";
                        $query = mysqli_query($conn,$sql); $sector_acode = array();
                        while($row = mysqli_fetch_assoc($query)){ $sector_acode[$row['code']] = $row['code']; }
                        $stk_qty = $stk_prc = $stk_amt = array(); $tpur_qty = 0;
                        for($cdate = strtotime($sdate); $cdate <= strtotime($edate); $cdate += (86400)){
                            $adate = date("Y-m-d",$cdate);
                            foreach($act_icode as $icode){
                                foreach($sector_acode as $bcode){
                                    if($wcode == "" || $wcode == "select"){ }
                                    else{
                                        $key = $adate."@".$icode."@".$bcode;
                                        $key2 = $icode."@".$bcode;
                                        
                                        
                                        //Initialization
                                        if(empty($pur_sqty[$key]) || $pur_sqty[$key] == ""){ $pur_sqty[$key] = 0; }
                                        if(empty($pur_samt[$key]) || $pur_samt[$key] == ""){ $pur_samt[$key] = 0; }
        
                                        if(empty($tin_sqty[$key]) || $tin_sqty[$key] == ""){ $tin_sqty[$key] = 0; }
                                        if(empty($tin_samt[$key]) || $tin_samt[$key] == ""){ $tin_samt[$key] = 0; }
        
                                        if(empty($sale_sqty[$key]) || $sale_sqty[$key] == ""){ $sale_sqty[$key] = 0; }
                                        if(empty($sale_samt[$key]) || $sale_samt[$key] == ""){ $sale_samt[$key] = 0; }
        
                                        if(empty($tout_sqty[$key]) || $tout_sqty[$key] == ""){ $tout_sqty[$key] = 0; }
                                        if(empty($tout_samt[$key]) || $tout_samt[$key] == ""){ $tout_samt[$key] = 0; }
        
                                        if(empty($dcon_sqty[$key]) || $dcon_sqty[$key] == ""){ $dcon_sqty[$key] = 0; }
                                        if(empty($mcon_sqty[$key]) || $mcon_sqty[$key] == ""){ $mcon_sqty[$key] = 0; }
        
                                        if(empty($prtn_sqty[$key]) || $prtn_sqty[$key] == ""){ $prtn_sqty[$key] = 0; }
                                        if(empty($prtn_samt[$key]) || $prtn_samt[$key] == ""){ $prtn_samt[$key] = 0; }
        
                                        if(empty($srtn_sqty[$key]) || $srtn_sqty[$key] == ""){ $srtn_sqty[$key] = 0; }
                                        if(empty($srtn_samt[$key]) || $srtn_samt[$key] == ""){ $srtn_samt[$key] = 0; }
        
                                        if(empty($isu_sqty[$key]) || $isu_sqty[$key] == ""){ $isu_sqty[$key] = 0; }
                                        if(empty($isu_samt[$key]) || $isu_samt[$key] == ""){ $isu_samt[$key] = 0; }
        
                                        if(empty($rcd_sqty[$key]) || $rcd_sqty[$key] == ""){ $rcd_sqty[$key] = 0; }
                                        if(empty($rcd_samt[$key]) || $rcd_samt[$key] == ""){ $rcd_samt[$key] = 0; }
        
                                        if(empty($stk_qty[$key2]) || $stk_qty[$key2] == ""){ $stk_qty[$key2] = 0; }
                                        if(empty($stk_prc[$key2]) || $stk_prc[$key2] == ""){ $stk_prc[$key2] = 0; }
                                        if(empty($stk_amt[$key2]) || $stk_amt[$key2] == ""){ $stk_amt[$key2] = 0; }
        
                                        //Purchase
                                        $stk_qty[$key2] += (float)$pur_sqty[$key]; $stk_amt[$key2] += (float)$pur_samt[$key];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } //else{ $stk_prc[$key2] = 0; }
                                        //Transfer-In
                                        $stk_qty[$key2] += (float)$tin_sqty[$key]; $stk_amt[$key2] += (float)$tin_samt[$key];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } //else{ $stk_prc[$key2] = 0; }
                                        //Sale Return
                                        $stk_qty[$key2] += (float)$srtn_sqty[$key]; $stk_amt[$key2] += (float)$srtn_samt[$key];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } //else{ $stk_prc[$key2] = 0; }
                                        //Intermediate-Received
                                        $stk_qty[$key2] += (float)$rcd_sqty[$key]; $stk_amt[$key2] += (float)$rcd_samt[$key2];
                                        if((float)$stk_qty[$key2] != 0){ $stk_prc[$key2] = (float)$stk_amt[$key2] / (float)$stk_qty[$key2]; } //else{ $stk_prc[$key2] = 0; }
        
                                        //Sale
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$sale_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$sale_sqty[$key]);
                                        //Transfer-Out
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$tout_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$tout_sqty[$key]);
                                        //Daily Consumed
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$dcon_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$dcon_sqty[$key]);
                                        //MedVac Consumed
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$mcon_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$mcon_sqty[$key]);
                                        //Purchase Return
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$prtn_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$prtn_sqty[$key]);
                                        //Intermediate-Issue
                                        $stk_qty[$key2] = (float)$stk_qty[$key2] - (float)$isu_sqty[$key];
                                        $stk_amt[$key2] = $stk_amt[$key2] - ((float)$stk_prc[$key2] * (float)$isu_sqty[$key]);
        
                                        if(strtotime($adate) < strtotime($fdate)){
                                            if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                            if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
                                            $opn_sqty[$key2] = $stk_qty[$key2]; $opn_samt[$key2] = $stk_amt[$key2];
                                        }
                                        else{
                                            if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                            if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
                                            if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                            if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
                                            if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                            if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
                                            if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                            if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
        
                                            //Purchase
                                            $purin_sqty[$key2] += (float)$pur_sqty[$key]; //$purin_samt[$key2] += (float)$pur_samt[$key];
                                            //$purin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$pur_sqty[$key]);
                                            $purin_samt[$key2] += ((float)$pur_samt[$key]);
        
                                            //Transfer-In,Sale Return, Intermediate Received
                                            $stkin_sqty[$key2] += (float)$tin_sqty[$key]; //$stkin_samt[$key2] += (float)$tin_samt[$key];
                                            $stkin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$tin_sqty[$key]);
                                            $stkin_sqty[$key2] += (float)$srtn_sqty[$key]; //$stkin_samt[$key2] += (float)$srtn_samt[$key];
                                            $stkin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$srtn_sqty[$key]);
                                            $stkin_sqty[$key2] += (float)$rcd_sqty[$key]; //$stkin_samt[$key2] += (float)$rcd_samt[$key];
                                            $stkin_samt[$key2] += ((float)$stk_prc[$key2] * (float)$rcd_sqty[$key]);
        
                                            //Avg. Price
                                            $sqty = 0; $sqty = (float)$opn_sqty[$key2] + (float)$purin_sqty[$key2] + (float)$stkin_sqty[$key2];
                                            $samt = 0; $samt = (float)$opn_samt[$key2] + (float)$purin_samt[$key2] + (float)$stkin_samt[$key2];
                                            //$avg_prc = 0; if((float)$sqty != 0){ $avg_prc = (float)$samt / (float)$sqty; }
                                            $avg_prc = (float)$stk_prc[$key2];
        
                                            //Daily Consumed, MedVac Consumed
                                            $stkcon_sqty[$key2] += (float)$dcon_sqty[$key];
                                            $stkcon_samt[$key2] += ((float)$avg_prc * (float)$dcon_sqty[$key]);
                                            $stkcon_sqty[$key2] += (float)$mcon_sqty[$key];
                                            $stkcon_samt[$key2] += ((float)$avg_prc * (float)$mcon_sqty[$key]);
        
                                            //Sale, Transfer-Out, Purchase Return, Intermediate Issue
                                            $stkout_sqty[$key2] += (float)$sale_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$sale_sqty[$key]);
                                            $stkout_sqty[$key2] += (float)$tout_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$tout_sqty[$key]);
                                            $stkout_sqty[$key2] += (float)$prtn_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$prtn_sqty[$key]);
                                            $stkout_sqty[$key2] += (float)$isu_sqty[$key];
                                            $stkout_samt[$key2] += ((float)$avg_prc * (float)$isu_sqty[$key]);
                                            
                                            //Closing
                                            $cls_sqty[$key2] = (float)$stk_qty[$key2];
                                            $cls_samt[$key2] = ((float)$stk_prc[$key2] * (float)$stk_qty[$key2]);
                                        }    
                                    }
                                }
                            }
                        }
                    }

                    $html = '';
                    $html .= '<thead class="thead3">';
                    $html .= '<tr style="text-align:center;" align="center">';
                    if($fetch_type == "farm_wise"){ $html .= '<th></th><th></th><th></th><th></th><th></th><th></th>'; } else{ $html .= '<th></th>'; }
                    $html .= '<th></th>';
                    $html .= '<th colspan="3">Opening</th>';
                    $html .= '<th colspan="3">Purchase</th>';
                    $html .= '<th colspan="3">Transfer-In</th>';
                    $html .= '<th colspan="3">Consumption</th>';
                    $html .= '<th colspan="3">Transfer-Out</th>';
                    $html .= '<th colspan="3">Closing</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<thead class="thead3" id="head_names">';
                    $html .= '<tr style="text-align:center;" align="center">';
                    if($fetch_type == "branch_wise"){ $html .= '<th id="order">Branch</th>'; }
                    else if($fetch_type == "line_wise"){ $html .= '<th id="order">Line</th>'; }
                    else if($fetch_type == "supvr_wise"){ $html .= '<th id="order">Supervisor</th>'; }
                    else{ $html .= '<th id="order">Branch</th><th id="order">Line</th><th id="order">Supervisor</th><th id="order">Farm Code</th><th id="order">Farm</th><th id="order">Batch</th>'; }
                    $html .= '<th style="text-align:center;" id="order_num">Item</th>';
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
                    //Consumption
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Transfer-Out
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    //Closing
                    $html .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Price</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Amount</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody class="tbody1" id="tbody1">';
                    
                    if($fetch_type == "branch_wise"){
                        foreach($branch_code as $bcode){
                            if($bcode == "" || $bcode == "select"){ }
                            else{
                                foreach($act_icode as $icode){
                                    if($icode == "" || $icode == "select"){ }
                                    else{
                                        $key2 = $icode."@".$bcode;
                                        if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                        if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
        
                                        if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                        if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
        
                                        if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                        if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
        
                                        if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                        if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
        
                                        if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                        if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
        
                                        if(empty($cls_sqty[$key2]) || $cls_sqty[$key2] == ""){ $cls_sqty[$key2] = 0; }
                                        if(empty($cls_samt[$key2]) || $cls_samt[$key2] == ""){ $cls_samt[$key2] = 0; }
        
                                        if((float)$opn_sqty[$key2] == 0 && (float)$purin_sqty[$key2] == 0 && (float)$stkin_sqty[$key2] == 0 && (float)$stkout_sqty[$key2] == 0 && (float)$stkcon_sqty[$key2] == 0 && (float)$cls_sqty[$key2] == 0){ }
                                        else{
                                            $html .= '<tr>';
                                            $html .= '<td style="text-align:left;">'.$branch_name[$bcode].'</td>';
                                            $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
        
                                            //Opening
                                            $avg_prc = 0; if((float)$opn_sqty[$key2] != 0){ $avg_prc = round(((float)$opn_samt[$key2] / (float)$opn_sqty[$key2]),2); } else{ $opn_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_samt[$key2])).'</td>';
                                           
                                            //Purchase
                                            $avg_prc = 0; if((float)$purin_sqty[$key2] != 0){ $avg_prc = round(((float)$purin_samt[$key2] / (float)$purin_sqty[$key2]),2); } else{ $purin_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_samt[$key2])).'</td>';
                                            
                                            //Transfer-In
                                            $avg_prc = 0; if((float)$stkin_sqty[$key2] != 0){ $avg_prc = round(((float)$stkin_samt[$key2] / (float)$stkin_sqty[$key2]),2); } else{ $stkin_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_samt[$key2])).'</td>';
                                            
                                            //Consumption
                                            $avg_prc = 0; if((float)$stkcon_sqty[$key2] != 0){ $avg_prc = round(((float)$stkcon_samt[$key2] / (float)$stkcon_sqty[$key2]),2); } else{ $stkcon_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_samt[$key2])).'</td>';
                                            
                                            //Transfer-Out
                                            $avg_prc = 0; if((float)$stkout_sqty[$key2] != 0){ $avg_prc = round(((float)$stkout_samt[$key2] / (float)$stkout_sqty[$key2]),2); } else{ $stkout_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_samt[$key2])).'</td>';
                                            
                                            //Closing
                                            $avg_prc = 0; if((float)$cls_sqty[$key2] != 0){ $avg_prc = round(((float)$cls_samt[$key2] / (float)$cls_sqty[$key2]),2); } else{ $cls_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_samt[$key2])).'</td>';
                                            $html .= '</tr>';

                                            //Calculating Totals
                                            $topn_qty += (float)$opn_sqty[$key2];
                                            $topn_amt += (float)$opn_samt[$key2];
                                            $tpur_qty += (float)$purin_sqty[$key2];
                                            $tpur_amt += (float)$purin_samt[$key2];
                                            $ttin_qty += (float)$stkin_sqty[$key2];
                                            $ttin_amt += (float)$stkin_samt[$key2];
                                            $tcon_qty += (float)$stkcon_sqty[$key2];
                                            $tcon_amt += (float)$stkcon_samt[$key2];
                                            $ttout_qty += (float)$stkout_sqty[$key2];
                                            $ttout_amt += (float)$stkout_samt[$key2];
                                            $tclose_qty += (float)$cls_sqty[$key2];
                                            $tclose_amt += (float)$cls_samt[$key2];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($fetch_type == "line_wise"){
                        foreach($line_code as $bcode){
                            if($bcode == "" || $bcode == "select"){ }
                            else{
                                foreach($act_icode as $icode){
                                    if($icode == "" || $icode == "select"){ }
                                    else{
                                        $key2 = $icode."@".$bcode;
                                        if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                        if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
        
                                        if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                        if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
        
                                        if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                        if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
        
                                        if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                        if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
        
                                        if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                        if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
        
                                        if(empty($cls_sqty[$key2]) || $cls_sqty[$key2] == ""){ $cls_sqty[$key2] = 0; }
                                        if(empty($cls_samt[$key2]) || $cls_samt[$key2] == ""){ $cls_samt[$key2] = 0; }
        
                                        if((float)$opn_sqty[$key2] == 0 && (float)$purin_sqty[$key2] == 0 && (float)$stkin_sqty[$key2] == 0 && (float)$stkout_sqty[$key2] == 0 && (float)$stkcon_sqty[$key2] == 0 && (float)$cls_sqty[$key2] == 0){ }
                                        else{
                                            $html .= '<tr>';
                                            $html .= '<td style="text-align:left;">'.$line_name[$bcode].'</td>';
                                            $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
        
                                            //Opening
                                            $avg_prc = 0; if((float)$opn_sqty[$key2] != 0){ $avg_prc = round(((float)$opn_samt[$key2] / (float)$opn_sqty[$key2]),2); } else{ $opn_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_samt[$key2])).'</td>';
                                           
                                            //Purchase
                                            $avg_prc = 0; if((float)$purin_sqty[$key2] != 0){ $avg_prc = round(((float)$purin_samt[$key2] / (float)$purin_sqty[$key2]),2); } else{ $purin_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_samt[$key2])).'</td>';
                                            
                                            //Transfer-In
                                            $avg_prc = 0; if((float)$stkin_sqty[$key2] != 0){ $avg_prc = round(((float)$stkin_samt[$key2] / (float)$stkin_sqty[$key2]),2); } else{ $stkin_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_samt[$key2])).'</td>';
                                            
                                            //Consumption
                                            $avg_prc = 0; if((float)$stkcon_sqty[$key2] != 0){ $avg_prc = round(((float)$stkcon_samt[$key2] / (float)$stkcon_sqty[$key2]),2); } else{ $stkcon_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_samt[$key2])).'</td>';
                                            
                                            //Transfer-Out
                                            $avg_prc = 0; if((float)$stkout_sqty[$key2] != 0){ $avg_prc = round(((float)$stkout_samt[$key2] / (float)$stkout_sqty[$key2]),2); } else{ $stkout_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_samt[$key2])).'</td>';
                                            
                                            //Closing
                                            $avg_prc = 0; if((float)$cls_sqty[$key2] != 0){ $avg_prc = round(((float)$cls_samt[$key2] / (float)$cls_sqty[$key2]),2); } else{ $cls_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_samt[$key2])).'</td>';
                                            $html .= '</tr>';

                                            //Calculating Totals
                                            $topn_qty += (float)$opn_sqty[$key2];
                                            $topn_amt += (float)$opn_samt[$key2];
                                            $tpur_qty += (float)$purin_sqty[$key2];
                                            $tpur_amt += (float)$purin_samt[$key2];
                                            $ttin_qty += (float)$stkin_sqty[$key2];
                                            $ttin_amt += (float)$stkin_samt[$key2];
                                            $tcon_qty += (float)$stkcon_sqty[$key2];
                                            $tcon_amt += (float)$stkcon_samt[$key2];
                                            $ttout_qty += (float)$stkout_sqty[$key2];
                                            $ttout_amt += (float)$stkout_samt[$key2];
                                            $tclose_qty += (float)$cls_sqty[$key2];
                                            $tclose_amt += (float)$cls_samt[$key2];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($fetch_type == "supvr_wise"){
                        foreach($supervisor_code as $bcode){
                            if($bcode == "" || $bcode == "select"){ }
                            else{
                                foreach($act_icode as $icode){
                                    if($icode == "" || $icode == "select"){ }
                                    else{
                                        $key2 = $icode."@".$bcode;
                                        if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                        if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
        
                                        if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                        if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
        
                                        if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                        if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
        
                                        if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                        if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
        
                                        if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                        if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
        
                                        if(empty($cls_sqty[$key2]) || $cls_sqty[$key2] == ""){ $cls_sqty[$key2] = 0; }
                                        if(empty($cls_samt[$key2]) || $cls_samt[$key2] == ""){ $cls_samt[$key2] = 0; }
        
                                        if((float)$opn_sqty[$key2] == 0 && (float)$purin_sqty[$key2] == 0 && (float)$stkin_sqty[$key2] == 0 && (float)$stkout_sqty[$key2] == 0 && (float)$stkcon_sqty[$key2] == 0 && (float)$cls_sqty[$key2] == 0){ }
                                        else{
                                            $html .= '<tr>';
                                            $html .= '<td style="text-align:left;">'.$supervisor_name[$bcode].'</td>';
                                            $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
        
                                            //Opening
                                            $avg_prc = 0; if((float)$opn_sqty[$key2] != 0){ $avg_prc = round(((float)$opn_samt[$key2] / (float)$opn_sqty[$key2]),2); } else{ $opn_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_samt[$key2])).'</td>';
                                           
                                            //Purchase
                                            $avg_prc = 0; if((float)$purin_sqty[$key2] != 0){ $avg_prc = round(((float)$purin_samt[$key2] / (float)$purin_sqty[$key2]),2); } else{ $purin_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_samt[$key2])).'</td>';
                                            
                                            //Transfer-In
                                            $avg_prc = 0; if((float)$stkin_sqty[$key2] != 0){ $avg_prc = round(((float)$stkin_samt[$key2] / (float)$stkin_sqty[$key2]),2); } else{ $stkin_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_samt[$key2])).'</td>';
                                            
                                            //Consumption
                                            $avg_prc = 0; if((float)$stkcon_sqty[$key2] != 0){ $avg_prc = round(((float)$stkcon_samt[$key2] / (float)$stkcon_sqty[$key2]),2); } else{ $stkcon_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_samt[$key2])).'</td>';
                                            
                                            //Transfer-Out
                                            $avg_prc = 0; if((float)$stkout_sqty[$key2] != 0){ $avg_prc = round(((float)$stkout_samt[$key2] / (float)$stkout_sqty[$key2]),2); } else{ $stkout_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_samt[$key2])).'</td>';
                                            
                                            //Closing
                                            $avg_prc = 0; if((float)$cls_sqty[$key2] != 0){ $avg_prc = round(((float)$cls_samt[$key2] / (float)$cls_sqty[$key2]),2); } else{ $cls_samt[$key2] = 0; }
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_sqty[$key2])).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_samt[$key2])).'</td>';
                                            $html .= '</tr>';

                                            //Calculating Totals
                                            $topn_qty += (float)$opn_sqty[$key2];
                                            $topn_amt += (float)$opn_samt[$key2];
                                            $tpur_qty += (float)$purin_sqty[$key2];
                                            $tpur_amt += (float)$purin_samt[$key2];
                                            $ttin_qty += (float)$stkin_sqty[$key2];
                                            $ttin_amt += (float)$stkin_samt[$key2];
                                            $tcon_qty += (float)$stkcon_sqty[$key2];
                                            $tcon_amt += (float)$stkcon_samt[$key2];
                                            $ttout_qty += (float)$stkout_sqty[$key2];
                                            $ttout_amt += (float)$stkout_samt[$key2];
                                            $tclose_qty += (float)$cls_sqty[$key2];
                                            $tclose_amt += (float)$cls_samt[$key2];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else{
                        foreach($farm_alist as $fcode){
                            $blist = array(); $blist = explode(",",$batch_farm[$fcode]);
                            foreach($blist as $bcode){
                                if($bcode == ""){ }
                                else{
                                    foreach($act_icode as $icode){
                                        if($icode == "" || $icode == "select"){ }
                                        else{
                                            $key2 = $icode."@".$bcode;
                                            if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                            if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
            
                                            if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                            if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
            
                                            if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                            if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
            
                                            if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                            if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
            
                                            if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                            if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
            
                                            if(empty($cls_sqty[$key2]) || $cls_sqty[$key2] == ""){ $cls_sqty[$key2] = 0; }
                                            if(empty($cls_samt[$key2]) || $cls_samt[$key2] == ""){ $cls_samt[$key2] = 0; }
                                            
                                            if((float)$opn_sqty[$key2] == 0 && (float)$purin_sqty[$key2] == 0 && (float)$stkin_sqty[$key2] == 0 && (float)$stkout_sqty[$key2] == 0 && (float)$stkcon_sqty[$key2] == 0 && (float)$cls_sqty[$key2] == 0){ }
                                            else{
                                                $brch = $farm_branch[$fcode]; $line = $farm_line[$fcode]; $supr = $farm_supervisor[$fcode];
                                                $html .= '<tr>';
                                                $html .= '<td style="text-align:left;">'.$branch_name[$brch].'</td>';
                                                $html .= '<td style="text-align:left;">'.$line_name[$line].'</td>';
                                                $html .= '<td style="text-align:left;">'.$supervisor_name[$supr].'</td>';
                                                $html .= '<td style="text-align:left;">'.$farm_ccode[$fcode].'</td>';
                                                $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                                $html .= '<td style="text-align:left;">'.$batch_name[$bcode].'</td>';
                                                $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
            
                                                //Opening
                                                $avg_prc = 0; if((float)$opn_sqty[$key2] != 0){ $avg_prc = round(((float)$opn_samt[$key2] / (float)$opn_sqty[$key2]),2); } else{ $opn_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_samt[$key2])).'</td>';
                                            
                                                //Purchase
                                                $avg_prc = 0; if((float)$purin_sqty[$key2] != 0){ $avg_prc = round(((float)$purin_samt[$key2] / (float)$purin_sqty[$key2]),2); } else{ $purin_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_samt[$key2])).'</td>';
                                                
                                                //Transfer-In
                                                $avg_prc = 0; if((float)$stkin_sqty[$key2] != 0){ $avg_prc = round(((float)$stkin_samt[$key2] / (float)$stkin_sqty[$key2]),2); } else{ $stkin_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_samt[$key2])).'</td>';
                                                
                                                //Consumption
                                                $avg_prc = 0; if((float)$stkcon_sqty[$key2] != 0){ $avg_prc = round(((float)$stkcon_samt[$key2] / (float)$stkcon_sqty[$key2]),2); } else{ $stkcon_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_samt[$key2])).'</td>';
                                                
                                                //Transfer-Out
                                                $avg_prc = 0; if((float)$stkout_sqty[$key2] != 0){ $avg_prc = round(((float)$stkout_samt[$key2] / (float)$stkout_sqty[$key2]),2); } else{ $stkout_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_samt[$key2])).'</td>';
                                                
                                                //Closing
                                                $avg_prc = 0; if((float)$cls_sqty[$key2] != 0){ $avg_prc = round(((float)$cls_samt[$key2] / (float)$cls_sqty[$key2]),2); } else{ $cls_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_samt[$key2])).'</td>';
                                                $html .= '</tr>';

                                                //Calculating Totals
                                                $topn_qty += (float)$opn_sqty[$key2];
                                                $topn_amt += (float)$opn_samt[$key2];
                                                $tpur_qty += (float)$purin_sqty[$key2];
                                                $tpur_amt += (float)$purin_samt[$key2];
                                                $ttin_qty += (float)$stkin_sqty[$key2];
                                                $ttin_amt += (float)$stkin_samt[$key2];
                                                $tcon_qty += (float)$stkcon_sqty[$key2];
                                                $tcon_amt += (float)$stkcon_samt[$key2];
                                                $ttout_qty += (float)$stkout_sqty[$key2];
                                                $ttout_amt += (float)$stkout_samt[$key2];
                                                $tclose_qty += (float)$cls_sqty[$key2];
                                                $tclose_amt += (float)$cls_samt[$key2];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($loc_type == "all" || $loc_type == "only_sectors"){
                        $ssc = 0;
                        if($sec_list != ""){
                            foreach($sector_acode as $bcode){
                                if($bcode == "" || $bcode == "select"){ }
                                else{
                                    foreach($act_icode as $icode){
                                        if($icode == "" || $icode == "select"){ }
                                        else{
                                            $ssc++;
                                            //echo "<br/>".$ssc."@".$bcode."@".$icode;
                                            $key2 = $icode."@".$bcode;
                                            if(empty($opn_sqty[$key2]) || $opn_sqty[$key2] == ""){ $opn_sqty[$key2] = 0; }
                                            if(empty($opn_samt[$key2]) || $opn_samt[$key2] == ""){ $opn_samt[$key2] = 0; }
            
                                            if(empty($purin_sqty[$key2]) || $purin_sqty[$key2] == ""){ $purin_sqty[$key2] = 0; }
                                            if(empty($purin_samt[$key2]) || $purin_samt[$key2] == ""){ $purin_samt[$key2] = 0; }
            
                                            if(empty($stkin_sqty[$key2]) || $stkin_sqty[$key2] == ""){ $stkin_sqty[$key2] = 0; }
                                            if(empty($stkin_samt[$key2]) || $stkin_samt[$key2] == ""){ $stkin_samt[$key2] = 0; }
            
                                            if(empty($stkcon_sqty[$key2]) || $stkcon_sqty[$key2] == ""){ $stkcon_sqty[$key2] = 0; }
                                            if(empty($stkcon_samt[$key2]) || $stkcon_samt[$key2] == ""){ $stkcon_samt[$key2] = 0; }
            
                                            if(empty($stkout_sqty[$key2]) || $stkout_sqty[$key2] == ""){ $stkout_sqty[$key2] = 0; }
                                            if(empty($stkout_samt[$key2]) || $stkout_samt[$key2] == ""){ $stkout_samt[$key2] = 0; }
            
                                            if(empty($cls_sqty[$key2]) || $cls_sqty[$key2] == ""){ $cls_sqty[$key2] = 0; }
                                            if(empty($cls_samt[$key2]) || $cls_samt[$key2] == ""){ $cls_samt[$key2] = 0; }
            
                                            if((float)$opn_sqty[$key2] == 0 && (float)$purin_sqty[$key2] == 0 && (float)$stkin_sqty[$key2] == 0 && (float)$stkout_sqty[$key2] == 0 && (float)$stkcon_sqty[$key2] == 0 && (float)$cls_sqty[$key2] == 0){ }
                                            else{
                                                if($fetch_type == "farm_wise"){
                                                    $html .= '<tr>';
                                                    $html .= '<td style="text-align:left;">'.$sector_name[$bcode].'</td>';
                                                    $html .= '<td style="text-align:left;"></td>';
                                                    $html .= '<td style="text-align:left;"></td>';
                                                    $html .= '<td style="text-align:left;"></td>';
                                                    $html .= '<td style="text-align:left;"></td>';
                                                    $html .= '<td style="text-align:left;"></td>';
                                                    $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
                                                }
                                                else{
                                                    $html .= '<tr>';
                                                    $html .= '<td style="text-align:left;">'.$sector_name[$bcode].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
                                                }
                                                
            
                                                //Opening
                                                $avg_prc = 0; if((float)$opn_sqty[$key2] != 0){ $avg_prc = round(((float)$opn_samt[$key2] / (float)$opn_sqty[$key2]),2); } else{ $opn_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_samt[$key2])).'</td>';
                                               
                                                //Purchase
                                                $avg_prc = 0; if((float)$purin_sqty[$key2] != 0){ $avg_prc = round(((float)$purin_samt[$key2] / (float)$purin_sqty[$key2]),2); } else{ $purin_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($purin_samt[$key2])).'</td>';
                                                
                                                //Transfer-In
                                                $avg_prc = 0; if((float)$stkin_sqty[$key2] != 0){ $avg_prc = round(((float)$stkin_samt[$key2] / (float)$stkin_sqty[$key2]),2); } else{ $stkin_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkin_samt[$key2])).'</td>';
                                                
                                                //Consumption
                                                $avg_prc = 0; if((float)$stkcon_sqty[$key2] != 0){ $avg_prc = round(((float)$stkcon_samt[$key2] / (float)$stkcon_sqty[$key2]),2); } else{ $stkcon_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkcon_samt[$key2])).'</td>';
                                                
                                                //Transfer-Out
                                                $avg_prc = 0; if((float)$stkout_sqty[$key2] != 0){ $avg_prc = round(((float)$stkout_samt[$key2] / (float)$stkout_sqty[$key2]),2); } else{ $stkout_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($stkout_samt[$key2])).'</td>';
                                                
                                                //Closing
                                                $avg_prc = 0; if((float)$cls_sqty[$key2] != 0){ $avg_prc = round(((float)$cls_samt[$key2] / (float)$cls_sqty[$key2]),2); } else{ $cls_samt[$key2] = 0; }
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_sqty[$key2])).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_samt[$key2])).'</td>';
                                                $html .= '</tr>';
    
                                                //Calculating Totals
                                                $topn_qty += (float)$opn_sqty[$key2];
                                                $topn_amt += (float)$opn_samt[$key2];
                                                $tpur_qty += (float)$purin_sqty[$key2];
                                                $tpur_amt += (float)$purin_samt[$key2];
                                                $ttin_qty += (float)$stkin_sqty[$key2];
                                                $ttin_amt += (float)$stkin_samt[$key2];
                                                $tcon_qty += (float)$stkcon_sqty[$key2];
                                                $tcon_amt += (float)$stkcon_samt[$key2];
                                                $ttout_qty += (float)$stkout_sqty[$key2];
                                                $ttout_amt += (float)$stkout_samt[$key2];
                                                $tclose_qty += (float)$cls_sqty[$key2];
                                                $tclose_amt += (float)$cls_samt[$key2];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    if($fetch_type == "farm_wise"){ $html .= '<th style="text-align:left;" colspan="7">Total</th>'; } else{ $html .= '<th style="text-align:left;" colspan="2">Total</th>'; }

                    //Opening
                    $avg_prc = 0; if((float)$topn_qty != 0){ $avg_prc = round(((float)$topn_amt / (float)$topn_qty),2); } else{ $topn_amt = 0; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($topn_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($topn_amt)).'</th>';
                   
                    //Purchase
                    $avg_prc = 0; if((float)$tpur_qty != 0){ $avg_prc = round(((float)$tpur_amt / (float)$tpur_qty),2); } else{ $tpur_amt = 0; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpur_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tpur_amt)).'</th>';
                    
                    //Transfer-In
                    $avg_prc = 0; if((float)$ttin_qty != 0){ $avg_prc = round(((float)$ttin_amt / (float)$ttin_qty),2); } else{ $ttin_amt = 0; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttin_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttin_amt)).'</th>';
                    
                    //Consumption
                    $avg_prc = 0; if((float)$tcon_qty != 0){ $avg_prc = round(((float)$tcon_amt / (float)$tcon_qty),2); } else{ $tcon_amt = 0; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tcon_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tcon_amt)).'</th>';
                    
                    //Transfer-Out
                    $avg_prc = 0; if((float)$ttout_qty != 0){ $avg_prc = round(((float)$ttout_amt / (float)$ttout_qty),2); } else{ $ttout_amt = 0; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttout_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttout_amt)).'</th>';
                    
                    //Closing
                    $avg_prc = 0; if((float)$tclose_qty != 0){ $avg_prc = round(((float)$tclose_amt / (float)$tclose_qty),2); } else{ $tclose_amt = 0; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tclose_qty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($avg_prc)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tclose_amt)).'</th>';
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
                    html +='<th style="text-align:center;">Item</th>';
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
                    html +='<th style="text-align:center;">Item</th>';
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