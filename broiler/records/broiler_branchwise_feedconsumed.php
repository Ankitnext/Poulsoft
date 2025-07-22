<?php
//broiler_branchwise_feedconsumed.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Feed Consumed Report";
    include "header_head.php";
    $form_path = "broiler_branchwise_feedconsumed.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Feed Consumed Report";
    include "header_head.php";
    $form_path = "broiler_branchwise_feedconsumed.php?db=$db&userid=".$user_code;
}

$file_name = "Feed Consumed Report";
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

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'broiler_branchwise_feedconsumed.php' AND `field_function` = 'ShowFeed'";
$query = mysqli_query($conn,$sql); $showfeedflag = mysqli_num_rows($query);

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

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $fetch_type = "farm_wise"; /*$batch_type = "Live";*/ $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $fetch_type = $_POST['fetch_type'];
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
                                    <label>Fetch Type</label>
                                    <select name="fetch_type" id="fetch_type" class="form-control select2">
                                        <option value="branch_wise" <?php if($fetch_type == "branch_wise"){ echo "selected"; } ?>>Branch</option>
                                        <option value="line_wise" <?php if($fetch_type == "line_wise"){ echo "selected"; } ?>>Line</option>
                                        <option value="supvr_wise" <?php if($fetch_type == "supvr_wise"){ echo "selected"; } ?>>Supervisor</option>
                                        <option value="farm_wise" <?php if($fetch_type == "farm_wise"){ echo "selected"; } ?>>Farm</option>
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
                //Feed
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $fcat_code = $fcat_name = array();
                while($row = mysqli_fetch_assoc($query)){ $fcat_code[$row['code']] = $row['code']; $fcat_name[$row['code']] = $row['description']; }
                
                $feed_list = implode("','", $fcat_code);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_code = $feed_name = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }
                
                //Chick
                $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%Broiler Chick%' OR `description` LIKE '%Broiler Bird%') AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $ccat_code = $ccat_name = array();
                while($row = mysqli_fetch_assoc($query)){ $ccat_code[$row['code']] = $row['code']; $ccat_name[$row['code']] = $row['description']; }
                
                $chick_list = implode("','", $ccat_code);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$chick_list') AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $chick_code = $chick_name = array();
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
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; // AND (`gc_flag` = '0' OR `start_date` <= '$tdate' AND `end_date` >= '$fdate' AND `gc_flag` = '1')
                $query = mysqli_query($conn,$sql); $batch_alist = $batch_afarm = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; $batch_afarm[$row['code']] = $row['farm_code']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    //Fetch Item Details
                    $item_list = "";
                    $item_list = implode("','", $chick_code);
                    $item_list .= "','".implode("','", $feed_code);
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    //Purchase
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $placed_date = $placed_birds = $opur_fqty = $bpur_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['farm_batch']; $items = $row['icode'];
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){
                            $placed_date[$key] = $row['date'];
                            $placed_birds[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        }
                        else{
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $opur_fqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                            else{
                                $bpur_fqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                        }
                        $act_batch[$key] = $key;
                    }
                    //Stock-In
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                    $query = mysqli_query($conn,$sql); $otrin_fqty = $btrin_wfqty = $btrin_ffqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['to_batch']; $items = $row['code'];
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){
                            $placed_date[$key] = $row['date'];
                            $placed_birds[$key] += (float)$row['quantity'];
                        }
                        else{
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $otrin_fqty[$key] += (float)$row['quantity'];
                            }
                            else{
                                if($row['from_batch'] == ""){
                                    $btrin_wfqty[$key] += (float)$row['quantity'];
                                }
                                else{
                                    $btrin_ffqty[$key] += (float)$row['quantity'];
                                }
                            }
                        }
                        $act_batch[$key] = $key;
                    }
                    //Sale
                    $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $osale_fqty = $bsale_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['farm_batch']; $items = $row['icode'];
                        if(!empty($farm_alist[$row['warehouse']]) && $farm_alist[$row['warehouse']] != ""){
                            if(empty($feed_code[$items]) || $feed_code[$items] == ""){ }
                            else{
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $osale_fqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                }
                                else{
                                    $bsale_fqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                }
                            }
                            $act_batch[$key] = $key;
                        }
                    }
                    //Stock-Out
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $otrout_fqty = $btrout_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['from_batch']; $items = $row['code'];
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){ }
                        else{
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $otrout_fqty[$key] += (float)$row['quantity'];
                            }
                            else{
                                $btrout_fqty[$key] += (float)$row['quantity'];
                            }
                        }
                        $act_batch[$key] = $key;
                    }
                    //Stock-Out
                    $sql = "SELECT * FROM `broiler_itemreturns` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['farm_batch']; $items = $row['itemcode'];
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){ }
                        else{
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $otrout_fqty[$key] += (float)$row['quantity'];
                            }
                            else{
                                $btrout_fqty[$key] += (float)$row['quantity'];
                            }
                        }
                        $act_batch[$key] = $key;
                    }
                    //Day Record
                    $feeddata = [];
                    $feed1data = [];
                    $feed1kgs = [];
                    $feed2data = [];
                    $feed2kgs = [];
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $ocon_fqty = $bcon_fqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['batch_code']; $key2 = $row['date']."@".$row['batch_code'];
                        if (!isset($feed1data[$key2]) || !isset($feed2data[$key2])) {

                            if($row['item_code1'] != ""){
                                $feed1data[$key2] = $feed_name[$row['item_code1']];
                                $feed1kgs[$key2] = $row['kgs1'];
                            }
                            if($row['item_code2'] != ""){
                                
                                $feed2data[$key2] = $feed_name[$row['item_code2']];
                                $feed2kgs[$key2] = $row['kgs2'];
                            }        
                        }

                        if(strtotime($row['date']) < strtotime($fdate)){
                            $ocon_fqty[$key1] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                        }
                        else{
                            $bcon_fqty[$key2] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                        }
                        if(empty($brood_age[$key1]) || $brood_age[$key1] == ""){ $brood_age[$key1] = $row['brood_age']; } else if((int)$brood_age[$key1] < $row['brood_age']){ $brood_age[$key1] = $row['brood_age']; } else{ }
                        $act_batch[$key1] = $key1;
                    }

                    $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_book = $batch_farm = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_farm[$row['farm_code']] .= $row['code'].","; }
                    
                    $cls_birds = array();
                    foreach($act_batch as $bcode){
                        $key = $bcode;

                        $fcode = $batch_afarm[$bcode]; $bhcode = $farm_branch[$fcode]; $lcode = $farm_line[$fcode]; $scode = $farm_supervisor[$fcode];
                        if($fetch_type == "branch_wise"){ $key2 = $bhcode; }
                        else if($fetch_type == "line_wise"){ $key2 = $lcode; }
                        else if($fetch_type == "supvr_wise"){ $key2 = $scode; }
                        else{ $key2 = $bcode; }

                        //Initialization
                        if(empty($placed_birds[$key]) || $placed_birds[$key] == ""){ $placed_birds[$key] = 0; }
                        if(empty($ocon_fqty[$key]) || $ocon_fqty[$key] == ""){ $ocon_fqty[$key] = 0; }
                        if(empty($otrout_fqty[$key]) || $otrout_fqty[$key] == ""){ $otrout_fqty[$key] = 0; }
                        if(empty($btrout_fqty[$key]) || $btrout_fqty[$key] == ""){ $btrout_fqty[$key] = 0; }
                        if(empty($osale_fqty[$key]) || $osale_fqty[$key] == ""){ $osale_fqty[$key] = 0; }
                        if(empty($bsale_fqty[$key]) || $bsale_fqty[$key] == ""){ $bsale_fqty[$key] = 0; }
                        if(empty($otrin_fqty[$key]) || $otrin_fqty[$key] == ""){ $otrin_fqty[$key] = 0; }
                        if(empty($btrin_ffqty[$key]) || $btrin_ffqty[$key] == ""){ $btrin_ffqty[$key] = 0; }
                        if(empty($btrin_wfqty[$key]) || $btrin_wfqty[$key] == ""){ $btrin_wfqty[$key] = 0; }
                        if(empty($opur_fqty[$key]) || $opur_fqty[$key] == ""){ $opur_fqty[$key] = 0; }
                        if(empty($bpur_fqty[$key]) || $bpur_fqty[$key] == ""){ $bpur_fqty[$key] = 0; }

                        //Total Placed
                        $tot_pbirds[$key2] += (float)$placed_birds[$key];
                        
                        //Opening Feed
                        $ofeeds = (((float)$opur_fqty[$key] +(float)$otrin_fqty[$key]) - ((float)$osale_fqty[$key] + (float)$otrout_fqty[$key] + (float)$ocon_fqty[$key]));
                        //echo "<br/>$ofeeds = (((float)$opur_fqty[$key] +(float)$otrin_fqty[$key]) - ((float)$osale_fqty[$key] + (float)$otrout_fqty[$key] + (float)$ocon_fqty[$key]));";
                        $opn_feed[$key2] += (float)$ofeeds;

                        //Feed Purchase
                        $bpur_feed[$key2] += (float)$bpur_fqty[$key];
                        
                        //Feed-In: Warehouse
                        $btrin_wfeed[$key2] += (float)$btrin_wfqty[$key];
                        
                        //Feed-In: Farm
                        $btrin_ffeed[$key2] += (float)$btrin_ffqty[$key];
                        
                        //Feed-Out
                        $btout_feed[$key2] += ((float)$bsale_fqty[$key] + (float)$btrout_fqty[$key]);
                        
                        //Total Feed
                        $afeeds = (($ofeeds + (float)$bpur_fqty[$key] + (float)$btrin_wfqty[$key] + (float)$btrin_ffqty[$key]) - ((float)$bsale_fqty[$key] + (float)$btrout_fqty[$key]));
                        $tavl_feed[$key2] += (float)$afeeds;
                        
                        //Between Days Feed Consumption
                        for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                            $adate = date("Y-m-d",$cdate);
                            $key1 = $adate."@".$bcode; $key3 = $adate."@".$key2;
                            if(empty($bcon_fqty[$key1]) || $bcon_fqty[$key1] == ""){ $bcon_fqty[$key1] = 0; }
                            $bday_feed[$key3] += (float)$bcon_fqty[$key1];
                            $bday_cfeed[$adate] += (float)$bcon_fqty[$key1];
                        }
                        //Totals
                        $tp_birds += (float)$placed_birds[$key];
                        $to_feed += (float)$ofeeds;
                        $tp_feed += (float)$bpur_fqty[$key];
                        $ttiw_feed += (float)$btrin_wfqty[$key];
                        $ttif_feed += (float)$btrin_ffqty[$key];
                        $tto_feed += ((float)$bsale_fqty[$key] + (float)$btrout_fqty[$key]);
                        $tta_feed += (float)$afeeds;
                    }
                    $html = $nhead_html = $fhead_html = '';
                    $html .= '<thead class="thead3" id="head_names">';
                    $nhead_html .= '<tr style="text-align:center;" align="center">';
                    $fhead_html .= '<tr style="text-align:center;" align="center">';
                    if($fetch_type == "branch_wise"){ $nhead_html .= '<th>Branch</th>'; $fhead_html .= '<th id="order">Branch</th>'; }
                    else if($fetch_type == "line_wise"){ $nhead_html .= '<th>Line</th>'; $fhead_html .= '<th id="order">Line</th>'; }
                    else if($fetch_type == "supvr_wise"){ $nhead_html .= '<th>Supervisor</th>'; $fhead_html .= '<th id="order">Supervisor</th>'; }
                    else{
                        $nhead_html .= '<th>Branch</th><th>Line</th><th>Supervisor</th><th>Farm Code</th><th>Farm</th><th>Batch</th>';
                        $fhead_html .= '<th id="order">Branch</th><th id="order">Line</th><th id="order">Supervisor</th><th id="order">Farm Code</th><th id="order">Farm</th><th id="order">Batch</th>';
                        $nhead_html .= '<th style="text-align:center;">Date of Placement</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_date">Date of Placement</th>';
                        $nhead_html .= '<th style="text-align:center;">Age</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_num">Age</th>';
                    }
                    $nhead_html .= '<th style="text-align:center;">Placed Birds</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Placed Birds</th>';
                    $nhead_html .= '<th style="text-align:center;">Feed Opening</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Feed Opening</th>';
                    $nhead_html .= '<th style="text-align:center;">Feed Purchased</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Feed Purchased</th>';
                    $nhead_html .= '<th style="text-align:center;">Feed-In: Godown</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Feed-In: Godown</th>';
                    $nhead_html .= '<th style="text-align:center;">Feed-In: Farm</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Feed-In: Farm</th>';
                    $nhead_html .= '<th style="text-align:center;">Feed-Out</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Feed-Out</th>';
                    $nhead_html .= '<th style="text-align:center;">Total Feed</th>';
                    $fhead_html .= '<th style="text-align:center;" id="order_num">Total Feed</th>';
                    
                    for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                        $nhead_html .= '<th style="text-align:center;">'.date("d.m.Y",$cdate).'</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_num">'.date("d.m.Y",$cdate).'</th>';
                        if($showfeedflag > 0){
                        $nhead_html .= '<th style="text-align:center;">feed1 '.date("d.m.Y",$cdate).'</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_num">feed1 '.date("d.m.Y",$cdate).'</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_num">feed1 qty '.date("d.m.Y",$cdate).'</th>';
                        $nhead_html .= '<th style="text-align:center;">feed2 '.date("d.m.Y",$cdate).'</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_num">feed2 '.date("d.m.Y",$cdate).'</th>';
                        $fhead_html .= '<th style="text-align:center;" id="order_num">feed2 qty '.date("d.m.Y",$cdate).'</th>';
                        }
                    }
                    $nhead_html .= '<th style="text-align:center;">Total Consumption</th>';
                    $nhead_html .= '<th style="text-align:center;">Closing Feed</th>';
                    $nhead_html .= '</tr>';
                    $fhead_html .= '<th style="text-align:center;">Total Consumption</th>';
                    $fhead_html .= '<th style="text-align:center;">Closing Feed</th>';
                    $fhead_html .= '</tr>';
                    $html .= $fhead_html;
                    $html .= '</thead>';
                    $html .= '<tbody class="tbody1" id="tbody1">';
                    
                    if($fetch_type == "branch_wise"){
                        foreach($branch_code as $bcode){
                            if($bcode == "" || $bcode == "select"){ }
                            else{
                                $key2 = $bcode;
                                if(empty($tot_pbirds[$key2]) || $tot_pbirds[$key2] == ""){ $tot_pbirds[$key2] = 0; }
                                if(empty($opn_feed[$key2]) || $opn_feed[$key2] == ""){ $opn_feed[$key2] = 0; }
                                if(empty($bpur_feed[$key2]) || $bpur_feed[$key2] == ""){ $bpur_feed[$key2] = 0; }
                                if(empty($btrin_wfeed[$key2]) || $btrin_wfeed[$key2] == ""){ $btrin_wfeed[$key2] = 0; }
                                if(empty($btrin_ffeed[$key2]) || $btrin_ffeed[$key2] == ""){ $btrin_ffeed[$key2] = 0; }
                                if(empty($btout_feed[$key2]) || $btout_feed[$key2] == ""){ $btout_feed[$key2] = 0; }
                                if(empty($tavl_feed[$key2]) || $tavl_feed[$key2] == ""){ $tavl_feed[$key2] = 0; }

                                if((float)$opn_feed[$key2] == 0 && (float)$bpur_feed[$key2] == 0 && (float)$btrin_wfeed[$key2] == 0 && (float)$btrin_ffeed[$key2] == 0 && (float)$btout_feed[$key2] == 0 && (float)$tavl_feed[$key2] == 0){ }
                                else{
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$branch_name[$bcode].'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_pbirds[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($opn_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($bpur_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btrin_wfeed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btrin_ffeed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btout_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tavl_feed[$key2]).'</td>';
                                    $row_cons = 0;
                                    for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                                        $key3 = date("Y-m-d",$cdate)."@".$bcode;
                                        if(empty($bday_feed[$key3]) || $bday_feed[$key3] == ""){ $bday_feed[$key3] = 0; }
                                        $html .= '<td style="text-align:right;">'.number_format_ind($bday_feed[$key3]).'</td>';
                                        $row_cons += (float)$bday_feed[$key3];
                                        if($showfeedflag > 0){      
                                            $fedata1 = $feed1data[$key3];
                                            $fedata2 = $feed2data[$key3];
                                            $feed1qty = $feed1kgs[$key3];
                                            $feed2qty = $feed2kgs[$key3];
                                            $html .= '<td style="text-align:right;">'.$fedata1.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed1qty).'</td>';
                                            $html .= '<td style="text-align:right;">'.$fedata2.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed2qty).'</td>';
                                        }
                                    }
                                    $cls_feed = (float)$tavl_feed[$key2] - (float)$row_cons;
                                    $html .= '<td style="text-align:right;">'.number_format_ind($row_cons).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_feed).'</td>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    else if($fetch_type == "line_wise"){
                        foreach($line_code as $bcode){
                            if($bcode == "" || $bcode == "select"){ }
                            else{
                                $key2 = $bcode;
                                if(empty($tot_pbirds[$key2]) || $tot_pbirds[$key2] == ""){ $tot_pbirds[$key2] = 0; }
                                if(empty($opn_feed[$key2]) || $opn_feed[$key2] == ""){ $opn_feed[$key2] = 0; }
                                if(empty($bpur_feed[$key2]) || $bpur_feed[$key2] == ""){ $bpur_feed[$key2] = 0; }
                                if(empty($btrin_wfeed[$key2]) || $btrin_wfeed[$key2] == ""){ $btrin_wfeed[$key2] = 0; }
                                if(empty($btrin_ffeed[$key2]) || $btrin_ffeed[$key2] == ""){ $btrin_ffeed[$key2] = 0; }
                                if(empty($btout_feed[$key2]) || $btout_feed[$key2] == ""){ $btout_feed[$key2] = 0; }
                                if(empty($tavl_feed[$key2]) || $tavl_feed[$key2] == ""){ $tavl_feed[$key2] = 0; }

                                if((float)$opn_feed[$key2] == 0 && (float)$bpur_feed[$key2] == 0 && (float)$btrin_wfeed[$key2] == 0 && (float)$btrin_ffeed[$key2] == 0 && (float)$btout_feed[$key2] == 0 && (float)$tavl_feed[$key2] == 0){ }
                                else{
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$branch_name[$bcode].'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_pbirds[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($opn_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($bpur_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btrin_wfeed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btrin_ffeed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btout_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tavl_feed[$key2]).'</td>';
                                    $row_cons = 0;
                                    for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                                        $key3 = date("Y-m-d",$cdate)."@".$bcode;
                                        if(empty($bday_feed[$key3]) || $bday_feed[$key3] == ""){ $bday_feed[$key3] = 0; }
                                        $html .= '<td style="text-align:right;">'.number_format_ind($bday_feed[$key3]).'</td>';
                                        $row_cons += (float)$bday_feed[$key3];
                                        if($showfeedflag > 0){
                                            $fedata1 = $feed1data[$key3];
                                            $fedata2 = $feed2data[$key3];
                                            $feed1qty = $feed1kgs[$key3];
                                            $feed2qty = $feed2kgs[$key3];
                                            $html .= '<td style="text-align:right;">'.$fedata1.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed1qty).'</td>';
                                            $html .= '<td style="text-align:right;">'.$fedata2.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed2qty).'</td>';
                                        }
                                    }
                                    $cls_feed = (float)$tavl_feed[$key2] - (float)$row_cons;
                                    $html .= '<td style="text-align:right;">'.number_format_ind($row_cons).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_feed).'</td>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    else if($fetch_type == "supvr_wise"){
                        foreach($supervisor_code as $bcode){
                            if($bcode == "" || $bcode == "select"){ }
                            else{
                                $key2 = $bcode;
                                if(empty($tot_pbirds[$key2]) || $tot_pbirds[$key2] == ""){ $tot_pbirds[$key2] = 0; }
                                if(empty($opn_feed[$key2]) || $opn_feed[$key2] == ""){ $opn_feed[$key2] = 0; }
                                if(empty($bpur_feed[$key2]) || $bpur_feed[$key2] == ""){ $bpur_feed[$key2] = 0; }
                                if(empty($btrin_wfeed[$key2]) || $btrin_wfeed[$key2] == ""){ $btrin_wfeed[$key2] = 0; }
                                if(empty($btrin_ffeed[$key2]) || $btrin_ffeed[$key2] == ""){ $btrin_ffeed[$key2] = 0; }
                                if(empty($btout_feed[$key2]) || $btout_feed[$key2] == ""){ $btout_feed[$key2] = 0; }
                                if(empty($tavl_feed[$key2]) || $tavl_feed[$key2] == ""){ $tavl_feed[$key2] = 0; }

                                if((float)$opn_feed[$key2] == 0 && (float)$bpur_feed[$key2] == 0 && (float)$btrin_wfeed[$key2] == 0 && (float)$btrin_ffeed[$key2] == 0 && (float)$btout_feed[$key2] == 0 && (float)$tavl_feed[$key2] == 0){ }
                                else{
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$branch_name[$bcode].'</td>';
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_pbirds[$key2])).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($opn_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($bpur_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btrin_wfeed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btrin_ffeed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($btout_feed[$key2]).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tavl_feed[$key2]).'</td>';
                                    $row_cons = 0;
                                    for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                                        $key3 = date("Y-m-d",$cdate)."@".$bcode;
                                        if(empty($bday_feed[$key3]) || $bday_feed[$key3] == ""){ $bday_feed[$key3] = 0; }
                                        $html .= '<td style="text-align:right;">'.number_format_ind($bday_feed[$key3]).'</td>';
                                        $row_cons += (float)$bday_feed[$key3];
                                        if($showfeedflag > 0){
                                            $fedata1 = $feed1data[$key3];
                                            $fedata2 = $feed2data[$key3];
                                            $feed1qty = $feed1kgs[$key3];
                                            $feed2qty = $feed2kgs[$key3];
                                            $html .= '<td style="text-align:right;">'.$fedata1.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed1qty).'</td>';
                                            $html .= '<td style="text-align:right;">'.$fedata2.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed2qty).'</td>';
                                        }
                                    }
                                    $cls_feed = (float)$tavl_feed[$key2] - (float)$row_cons;
                                    $html .= '<td style="text-align:right;">'.number_format_ind($row_cons).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_feed).'</td>';
                                    $html .= '</tr>';
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
                                    $key2 = $bcode;
                                    if(empty($tot_pbirds[$key2]) || $tot_pbirds[$key2] == ""){ $tot_pbirds[$key2] = 0; }
                                    if(empty($opn_feed[$key2]) || $opn_feed[$key2] == ""){ $opn_feed[$key2] = 0; }
                                    if(empty($bpur_feed[$key2]) || $bpur_feed[$key2] == ""){ $bpur_feed[$key2] = 0; }
                                    if(empty($btrin_wfeed[$key2]) || $btrin_wfeed[$key2] == ""){ $btrin_wfeed[$key2] = 0; }
                                    if(empty($btrin_ffeed[$key2]) || $btrin_ffeed[$key2] == ""){ $btrin_ffeed[$key2] = 0; }
                                    if(empty($btout_feed[$key2]) || $btout_feed[$key2] == ""){ $btout_feed[$key2] = 0; }
                                    if(empty($tavl_feed[$key2]) || $tavl_feed[$key2] == ""){ $tavl_feed[$key2] = 0; }
    
                                    if((float)$opn_feed[$key2] == 0 && (float)$bpur_feed[$key2] == 0 && (float)$btrin_wfeed[$key2] == 0 && (float)$btrin_ffeed[$key2] == 0 && (float)$btout_feed[$key2] == 0 && (float)$tavl_feed[$key2] == 0){ }
                                    else{
                                        $brch = $farm_branch[$fcode]; $line = $farm_line[$fcode]; $supr = $farm_supervisor[$fcode];
                                        $pdate = ""; if(date("d.m.Y",strtotime($placed_date[$bcode])) != "01.01.1970"){ $pdate = date("d.m.Y",strtotime($placed_date[$bcode])); }
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$branch_name[$brch].'</td>';
                                        $html .= '<td style="text-align:left;">'.$line_name[$line].'</td>';
                                        $html .= '<td style="text-align:left;">'.$supervisor_name[$supr].'</td>';
                                        $html .= '<td style="text-align:left;">'.$farm_ccode[$fcode].'</td>';
                                        $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                        $html .= '<td style="text-align:left;">'.$batch_name[$bcode].'</td>';
                                        $html .= '<td style="text-align:left;">'.$pdate.'</td>';
                                        $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($brood_age[$bcode])).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_pbirds[$key2])).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($opn_feed[$key2]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($bpur_feed[$key2]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($btrin_wfeed[$key2]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($btrin_ffeed[$key2]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($btout_feed[$key2]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($tavl_feed[$key2]).'</td>';
                                        $row_cons = 0;
                                        for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                                            $key3 = date("Y-m-d",$cdate)."@".$bcode;
                                            if(empty($bday_feed[$key3]) || $bday_feed[$key3] == ""){ $bday_feed[$key3] = 0; }
                                            $html .= '<td style="text-align:right;">'.number_format_ind($bday_feed[$key3]).'</td>';
                                            $row_cons += (float)$bday_feed[$key3];
                                            if($showfeedflag > 0){
                                                $fedata1 = $feed1data[$key3];
                                                $fedata2 = $feed2data[$key3];
                                                $feed1qty = $feed1kgs[$key3];
                                                $feed2qty = $feed2kgs[$key3];
                                                $html .= '<td style="text-align:right;">'.$fedata1.'</td>';
                                                $html .= '<td style="text-align:right;">'.number_format_ind($feed1qty).'</td>';
                                                $html .= '<td style="text-align:right;">'.$fedata2.'</td>';
                                                $html .= '<td style="text-align:right;">'.number_format_ind($feed2qty).'</td>';
                                            }
                                        }
                                        $cls_feed = (float)$tavl_feed[$key2] - (float)$row_cons;
                                        $html .= '<td style="text-align:right;">'.number_format_ind($row_cons).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cls_feed).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                            }
                        }
                    }
                    
                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';

                    if($fetch_type == "farm_wise"){ $html .= '<th style="text-align:left;" colspan="8">Total</th>'; } else{ $html .= '<th style="text-align:left;" colspan="1">Total</th>'; }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tp_birds)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($to_feed)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tp_feed)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttiw_feed)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ttif_feed)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tto_feed)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tta_feed)).'</th>';
                    $trow_cons = 0;
                    for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                        $key3 = date("Y-m-d",$cdate);
                        if(empty($bday_cfeed[$key3]) || $bday_cfeed[$key3] == ""){ $bday_cfeed[$key3] = 0; }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($bday_cfeed[$key3])).'</th>';
                        $trow_cons += (float)$bday_cfeed[$key3];
                        if($showfeedflag > 0){
                            $fedata1 = $feed1data[$key3];
                                            $fedata2 = $feed2data[$key3];
                                            $feed1qty = $feed1kgs[$key3];
                                            $feed2qty = $feed2kgs[$key3];
                                            $html .= '<td style="text-align:right;">'.$fedata1.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed1qty).'</td>';
                                            $html .= '<td style="text-align:right;">'.$fedata2.'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($feed2qty).'</td>';
                        }
                    }
                    $tcls_feed = (float)$tta_feed - (float)$trow_cons;
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($trow_cons)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tcls_feed)).'</th>';
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
                    html += '<?php echo $nhead_html; ?>';
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
                    html += '<?php echo $fhead_html; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
        </script>
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>