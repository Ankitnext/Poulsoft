<?php
//broiler_growingcharge_masterreport7.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db']; } else { $db = ''; }
if($db == ''){
    include "../newConfig.php";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    $reloadpath = "broiler_growingcharge_masterreport7.php";
}
else{
    include "APIconfig.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
    $dbname = $db;
    $reloadpath = "broiler_growingcharge_masterreport7.php?db=".$db."&userid=".$user_code;

}

include "../broiler_check_tableavailability.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }


$sql='SHOW COLUMNS FROM `main_access`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("historygathered_rate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `historygathered_rate` INT NOT NULL DEFAULT '1' COMMENT '1 => need to display rate in History Gather'"; mysqli_query($conn,$sql); }
if(in_array("historygathered_amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `historygathered_amount` INT NOT NULL DEFAULT '1' COMMENT '1 => need to display amount in History Gather'"; mysqli_query($conn,$sql); }
if(in_array("historygathered_batchdetails", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `historygathered_batchdetails` INT NOT NULL DEFAULT '1' COMMENT '1 => need to display batch details'"; mysqli_query($conn,$sql); }

$rate_flag = 0;
$amount_flag = 0;
$batchdetails_flag = 0;

$branch_access_code = $line_access_code = $farm_access_code = $sector_access_code = $branch_access_filter1 = $line_access_filter1 = $farm_access_filter1 = "";
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ 
    $branch_access_code = $row['branch_code']; 
    $line_access_code = $row['line_code']; 
    $farm_access_code = $row['farm_code']; 
    $sector_access_code = $row['loc_access']; 
    $rate_flag = $row['historygathered_rate']; 
    $amount_flag = $row['historygathered_amount']; 
    $batchdetails_flag = $row['historygathered_batchdetails']; 
}

if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'".$branch_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$line_code = $line_name = $line_branch = array();
$sql = "SELECT * FROM `location_line` WHERE `active` = '1'".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = $sector_name = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $sector_name[$row['code']] = $row['description'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code']; $farm_farmer[$row['code']] = $row['farmer_code'];
}
$farm_list = implode("','", $farm_code);
$batch_code = $batch_name = $batch_book = $batch_gcflag = $farm_batch = array();
$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `farm_code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; $farm_batch[$row['code']] = $row['farm_code']; }

$emp_list = implode("','", $farm_supervisor);
$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' AND `code` IN ('$emp_list')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$chick_code = $chick_name = $bird_code = $bird_name = "";
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_name = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }

$item_code = $item_name = array();
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$feed_code = $feed_name = array();
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND (`description` LIKE '%Broiler Feed%' OR `description` LIKE '%Breeder Feed%') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Water Soluble%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

$medvac_code = $medvac_name = array();
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bio security%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

$biosec_code = $biosec_name = array();
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $biosec_code[$row['code']] = $row['code']; $biosec_name[$row['code']] = $row['description']; }

$vendor_code = $vendor_name = array();
$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }


if($count54 > 0){
    
$sql='SHOW COLUMNS FROM `broiler_itemreturns`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("gst_per", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `gst_per` double(20,2) NULL DEFAULT NULL COMMENT 'GST Percentage' AFTER `price`"; mysqli_query($conn,$sql); }
if(in_array("farm_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `farm_batch` VARCHAR(250) NULL DEFAULT NULL AFTER `warehouse`"; mysqli_query($conn,$sql); }

}



$farmccodes = $farms = "select"; $batches = $book_nos = "select"; $fetch_type = "farmer"; $excel_type = "display"; $url = "";
if(isset($_POST['submit_report']) == true){
    $farms = $_POST['farms'];
    $farmccodes = $_POST['farmccodes'];
    $batches = $_POST['batches'];
    $book_nos = $_POST['book_nos'];

	$fetch_type = $_POST['fetch_type'];
	$excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/GrowingChargeStatement-Excel.php?batches=".$batches;
    $filename = "Growing Charge Statement"; 
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/8d1790e9c3.js" crossorigin="anonymous"></script>
        <style>
            .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
            .report_head th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
            .header-name{
                color: red;
                font-weight:bold;
            }
            .heading-name{
                color: green;
            }
        </style>
        <?php
          if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; } table { white-space: nowrap; }
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
            table.tbl2 { left:0;margin-right: auto; width:auto; }
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
        <table class="tbl2" align="center">
            <div class="tbl">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:auto;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="21" align="center"><?php echo $row['cdetails']; ?><h5>Growing Charge Statement</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="<?php echo $reloadpath; ?>" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr>
                        <th colspan="23">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" onChange="fetch_farm_batch(this.id)">
                                        <option value="select" <?php if($farms == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($farm_code as $fcode){ if(!empty($farm_name[$fcode])){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm Code</label>
                                    <select name="farmccodes" id="farmccodes" class="form-control select2" onChange="fetch_farm_batch(this.id)">
                                        <option value="all" <?php if($farmccodes == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if(!empty($farm_ccode[$fcode])){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farmccodes == $fcode){ echo "selected"; } ?>><?php echo $farm_ccode[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Batch</label>
                                    <select name="batches" id="batches" class="form-control select2" style="width:160px;" onChange="select_batchbook_list(this.id);">
                                        <option value="select" <?php if($batches == "select"){ echo "selected"; } ?>>-Select-</option>
                                        <?php
                                        foreach($batch_code as $bcode){
                                            if($farms == "all"){
                                                if(!empty($batch_name[$bcode])){
                                        ?>
                                            <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php
                                                }
                                            }
                                            else{
                                                if($farm_batch[$bcode] == $farms){
                                                    if(!empty($batch_name[$bcode])){
                                        ?>
                                            <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Book No</label>
                                    <select name="book_nos" id="book_nos" class="form-control select2" style="width:160px;" onChange="select_batchbook_list(this.id);">
                                        <option value="select" <?php if($book_nos == "select"){ echo "selected"; } ?>>-Select-</option>
                                        <?php
                                        foreach($batch_code as $bcode){
                                            if($farms == "all"){
                                                if(!empty($batch_book[$bcode])){
                                        ?>
                                            <option value="<?php echo $bcode; ?>" <?php if($book_nos == $bcode){ echo "selected"; } ?>><?php echo $batch_book[$bcode]; ?></option>
                                        <?php
                                                }
                                            }
                                            else{
                                                if($farm_batch[$bcode] == $farms){
                                                    if(!empty($batch_book[$bcode])){
                                        ?>
                                            <option value="<?php echo $bcode; ?>" <?php if($book_nos == $bcode){ echo "selected"; } ?>><?php echo $batch_book[$bcode]; ?></option>
                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Fetch Type</label>
                                    <select name="fetch_type" id="fetch_type" class="form-control select2">
                                        <option value="farmer" <?php if($fetch_type == "farmer"){ echo "selected"; } ?>>-Farmer-</option>
                                        <option value="mgmt" <?php if($fetch_type == "mgmt"){ echo "selected"; } ?>>-Management-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Growing Charge Statement','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                </table>
            </form>
            </div>
            <?php
                //$fetch_type = "farmer"; $fetch_type = "mgmt";
                $regions = $branches = "";
                $sql = "SELECT * FROM `broiler_farm` WHERE `code` = '$farms' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){ $regions = $row['region_code']; $branches = $row['branch_code']; $lines = $row['line_code']; $supervisors = $row['supervisor_code']; }
                
                $gc_flag = 0; $sdate = $edate = "";
                $sql = "SELECT * FROM `broiler_batch` WHERE `code` = '$batches' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){ $gc_flag = $row['gc_flag']; $sdate = $row['start_date']; $edate = $row['end_date']; }
                if($gc_flag == 0){
                    
                    //Item CoA Accounts
                    $sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $icat_iac[$row['code']] = $row['iac'];
                    }
                    $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }
                
                    $chick_iac = $icat_iac[$icat_code[$chick_code]];
                    $sql = "SELECT MIN(date) as sdate,MAX(date) as edate FROM `account_summary` WHERE `batch` = '$batches' AND `item_code` = '$chick_code' AND `crdr` = 'DR' AND `coa_code` = '$chick_iac' AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $sdate = $row['sdate']; $edate = $row['edate']; }
                    if($sdate == "" || $edate == ""){
                        $sdate = $edate = date("Y-m-d");
                    }
                }
                
                $sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$regions' AND `branch_code` = '$branches' AND `from_date` <= '$sdate' AND `to_date` >= '$sdate' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); 
                while($row = mysqli_fetch_assoc($query)){
                    $gc_code = $row['code'];
                    $chick_cost = $row['chick_cost'];
                    $feed_cost = $row['feed_cost'];
                    $medicine_cost = $row['medicine_cost'];
                    $med_price = $row['med_price'];
                    $admin_cost = $row['admin_cost'];
                    $standard_prod_cost = $row['standard_prod_cost'];
                    $standard_cost = $row['standard_cost'];
                    $minimum_cost = $row['minimum_cost'];
                    $standard_fcr = $row['standard_fcr'];
                    $standard_mortality = $row['standard_mortality'];
                    $mgmt_admin_prc = $row['mgmt_admin_cost'];
                }
                
            if(isset($_POST['submit_report']) == true){
            ?>
        <table id="main_body" class="tbl" align="center"  style="width:1300px;">
        <tbody class="tbody1">
            <thead class="thead3" align="center">
                <tr>
                    <td colspan="23" style="line-height:1;color:blue;font-weight:bold;font-size:15px;">
                        <div class="row d-flex flex-wrap justify-content-center align-items-center">
                            <label class="header-name"><b class="heading-name">Branch: </b><?php echo $branch_name[$branches]; ?></label>&ensp;
                            <label class="header-name"><b class="heading-name">Line: </b><?php echo $line_name[$lines]; ?></label>&ensp;
                            <label class="header-name"><b class="heading-name">Farm: </b><?php echo $farm_name[$farms]; ?></label>&ensp;
                            <label class="header-name"><b class="heading-name">Supervisor: </b><?php echo $supervisor_name[$supervisors]; ?></label>&ensp;
                            <label class="header-name"><b class="heading-name">Batch No.: </b><?php echo $batch_name[$batches]; ?></label>&ensp;
                            <label class="header-name"><b class="heading-name">Book No.: </b><?php echo $batch_book[$batches]; ?></label>&ensp;
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Chick Placement</td>
                </tr>
                <tr align="center">
                    <th colspan="2">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="2">Supplier/Farm Name</th>
                    <th colspan="2">No.of Chicks Supplied</th>
                    <th colspan="1">Free Quantity</th>
                    <th colspan="2">Transit Mortality</th>
                    <th colspan="3">Actual Chicks Placed</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="3">Rate</th>
                    <?php }else{ ?>
                        <th colspan="3"></th>
                   <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="4">Amount</th>
                        <?php }else{ ?>
                        <th colspan="4"></th>
                   <?php } ?>
                </tr>
            </thead>
            <tbody class="tbody1">
                <?php
                $tsc_qty = $tfc_qty = $tmc_qty = $trc_qty = $trc_amt = 0; $cpdate = "";
                $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `warehouse` = '$farms' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){ $price = $chick_cost; $amount = $price * ($row['rcd_qty'] + $row['fre_qty']); }
                    else if($fetch_type == "mgmt"){ $price = $row['rate']; $amount = $row['item_tamt']; }
                    else{ }
                    $tsc_qty += $row['snt_qty'];
                    $tfc_qty += $row['fre_qty'];
                    $tmc_qty += (($row['snt_qty'] + $row['fre_qty']) - ($row['rcd_qty'] + $row['fre_qty']));
                    $trc_qty += (($row['rcd_qty'] + $row['fre_qty']));
                    $trc_amt += $amount;
                    if($cpdate == ""){ $cpdate = strtotime($row['date']); } else{ if($cpdate > strtotime($row['date'])){ $cpdate = strtotime($row['date']); } }
                ?>
                <tr>
                    <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['billno']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $vendor_name[$row['vcode']]; ?></td>
                    <td style="text-align:right;" colspan="2"><?php echo $row['snt_qty']; ?></td>
                    <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round($row['fre_qty'])); ?></td>
                    <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round((($row['snt_qty'] + $row['fre_qty']) - ($row['rcd_qty'] + $row['fre_qty'])))); ?></td>
                    <td style="text-align:right;" colspan="3"><?php echo number_format_ind(($row['rcd_qty'] + $row['fre_qty'])); ?></td>
                    <?php if($rate_flag == 1){ ?>
                        <td style="text-align:right;" colspan="3"><?php echo number_format_ind($price); ?></td>
                    <?php }else{ ?>
                        <td colspan="3"></td>
                   <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="4"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="4"></th>
                    <?php } ?>
                   
                    
                </tr>
                <?php
                }
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `towarehouse` = '$farms' AND `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){ $price = $chick_cost; $amount = $price * ($row['quantity']); }
                    else if($fetch_type == "mgmt"){ $price = $row['price']; $amount = $row['amount']; }
                    else{ }
                    if($row['trtype'] == 'ChickTransfer') {
                        $tsc_qty += $row['sent_qty'];
                    } else {
                        $tsc_qty += $row['quantity'];
                    }
                    $trc_qty += $row['quantity'];
                    $trc_amt += $amount;
                    if($cpdate == ""){ $cpdate = strtotime($row['date']); } else{ if($cpdate > strtotime($row['date'])){ $cpdate = strtotime($row['date']); } }
                ?>
                <tr>
                    <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['dcno']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
                    <?php if($row['trtype'] == 'ChickTransfer') {?>
                        <td style="text-align:right;" colspan="2"><?php echo $row['sent_qty']; ?></td>
                    <?php } else { ?>
                        <td style="text-align:right;" colspan="2"><?php echo $row['quantity']; ?></td>
                    <?php } ?>
                   
                    <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round(0)); ?></td>
                    <?php if($row['trtype'] == 'ChickTransfer') {?>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($row['mort_qty']+$row['weak_qty']+$row['legw_qty']+$row['cull_qty'])); ?></td>
                    <?php } else { ?>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round((0))); ?></td>
                    <?php } ?>
                    <td style="text-align:right;" colspan="3"><?php echo number_format_ind(($row['quantity'])); ?></td>
                    <?php if($rate_flag == 1){ ?>
                        <td style="text-align:right;" colspan="3"><?php echo number_format_ind($price); ?></td>
                    <?php }else{ ?>
                        <td colspan="3"></td>
                   <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="4"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="4"></td>
                   <?php } ?>
                </tr>
                <?php
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="8" style="text-align:center;">Total</th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsc_qty),2)); ?></th>
                <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round(($tfc_qty),2)); ?></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tmc_qty),2)); ?></th>
                <th style="text-align:right;" colspan="3"><?php echo number_format_ind(round(($trc_qty),2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="3"><?php echo number_format_ind(round(($price),2)); ?></th>
                <?php }else{ ?>
                    <th colspan="3"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="4"><?php echo number_format_ind(round(($trc_amt),2)); ?></th>
                <?php }else{ ?>
                    <th colspan="4"></th>
                <?php } ?>
                
                
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Feed Purchase</td>
                </tr>
                <tr align="center">
                    <th colspan="2">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="2">Feed Name</th>
                    <th colspan="3">From Warehouse / Farm</th>
                    <th colspan="2">Quantity in Bags</th>
                    <th colspan="2">Quantity in Kgs</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="2">Price</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                   <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="2">Amount</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                    <?php } ?>
                    <th colspan="4">Cumulative Feed in Kg</th>
                </tr>
            </thead>
            <tbody class="tbody1">
                <?php
                $feed_list = implode("','",$feed_code); $feed_bqty = 0;
                $feed_cum = $tpfin_qty = $tpfin_amt = 0; $feed_purin_qty = $feed_dwpurin_qty = array();
                $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` IN ('$feed_list') AND `warehouse` = '$farms' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){ $price = $feed_cost; $amount = $price * ($row['rcd_qty'] + $row['fre_qty']); }
                    else if($fetch_type == "mgmt"){ $price = $row['rate']; $amount = $row['item_tamt']; }
                    else{ }
                    if($cpdate > strtotime($row['date'])){ $feed_bqty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']); }
                    ?>
                    <tr>
                        <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['billno']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $item_name[$row['icode']]; ?></td>
                        <td style="text-align:left;" colspan="3"><?php echo $vendor_name[$row['vcode']]; ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['rcd_qty'] + $row['fre_qty']) / 50)); ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['rcd_qty'] + $row['fre_qty']))); ?></td>
                        <?php if($rate_flag == 1){ ?>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($price,2)); ?></td>
                        <?php }else{ ?>
                            <td colspan="2"></td>
                        <?php } ?>
                        <?php if($amount_flag == 1){ ?>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($amount,2)); ?></td>
                        <?php }else{ ?>
                                <td colspan="2"></td>
                        <?php } ?>
                        <td style="text-align:right;" colspan="4"><?php echo number_format_ind($row['rcd_qty'] + $row['fre_qty'] + $feed_cum); ?></td>
                    </tr>
                    <?php
                    $feed_cum += ($row['rcd_qty'] + $row['fre_qty']);
                    $tpfin_qty += ($row['rcd_qty'] + $row['fre_qty']);
                    $tpfin_amt += $amount;

                    $feed_dwpurin_qty[$row['date']] += ($row['rcd_qty'] + $row['fre_qty']);
                    $feed_purin_qty[$row['icode']] += ($row['rcd_qty'] + $row['fre_qty']);
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="11" style="text-align:center;">Total</th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tpfin_qty / 50))); ?></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tpfin_qty),2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tpfin_amt),2)); ?></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
               
                <th style="text-align:right;" colspan="4"></th>
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Feed Transfer-In</td>
                </tr>
                <tr align="center">
                    <th colspan="2">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="2">Feed Name</th>
                    <th colspan="3">From Warehouse / Farm</th>
                    <th colspan="2">Quantity in Bags</th>
                    <th colspan="2">Quantity in Kgs</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="2">Price</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="2">Amount</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                    <?php } ?>
                    <th colspan="4">Cumulative Feed in Kg</th>
                </tr>
            </thead>
            <tbody class="tbody1">
                <?php
                $feed_cum = $tsfin_qty = $tsfin_amt = 0; $feed_trsin_qty = array();
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$feed_list') AND `towarehouse` = '$farms' AND `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){ $price = $feed_cost; $amount = $price * ($row['quantity']); }
                    else if($fetch_type == "mgmt"){ $price = $row['price']; $amount = $row['amount']; }
                    else{ }
                    if($cpdate > strtotime($row['date'])){ $feed_bqty += ((float)$row['quantity']); }
                    ?>
                    <tr>
                        <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['dcno']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $item_name[$row['code']]; ?></td>
                        <td style="text-align:left;" colspan="3"><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']) / 50)); ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']))); ?></td>
                        <?php if($rate_flag == 1){ ?>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($price,2)); ?></td>
                        <?php }else{ ?>
                            <th colspan="2"></th>
                        <?php } ?>
                        <?php if($amount_flag == 1){ ?>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($amount,2)); ?></td>
                        <?php }else{ ?>
                            <th colspan="2"></th>
                        <?php } ?>
                       
                        
                        <td style="text-align:right;" colspan="4"><?php echo number_format_ind($row['quantity'] + $feed_cum); ?></td>
                    </tr>
                    <?php
                    $feed_cum += ($row['quantity']);
                    $tsfin_qty += ($row['quantity']);
                    $tsfin_amt += $amount;

                    $feed_dwpurin_qty[$row['date']] += ($row['quantity']);
                    $feed_trsin_qty[$row['code']] += ($row['quantity']);
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="11" style="text-align:center;">Total</th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsfin_qty / 50))); ?></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsfin_qty),2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsfin_amt),2)); ?></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                
                <th style="text-align:right;" colspan="4"></th>
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Feed Return</td>
                </tr>
                <tr align="center">
                    <th colspan="2">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="2">Feed Name</th>
                    <th colspan="3">From Warehouse / Farm</th>
                    <th colspan="2">Quantity in Bags</th>
                    <th colspan="2">Quantity in Kgs</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="2">Price</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="2">Amount</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                   <?php } ?>
                    <th colspan="4">Cumulative Feed in Kg</th>
                </tr>
            </thead>
            <tbody class="tbody1">
                <?php
                $feed_cum = $tsfout_qty = $tsfout_amt = 0; $feed_trsout_qty = $feed_trfout_qty = array();
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$feed_list') AND `fromwarehouse` = '$farms' AND `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){ $price = $feed_cost; $amount = $price * ($row['quantity']); }
                    else if($fetch_type == "mgmt"){ $price = $row['price']; $amount = $row['amount']; }
                    else{ }
                    if($cpdate > strtotime($row['date'])){ $feed_bqty -= ((float)$row['quantity']); }
                    ?>
                    <tr>
                        <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['dcno']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $item_name[$row['code']]; ?></td>
                        <td style="text-align:left;" colspan="3"><?php echo $sector_name[$row['towarehouse']]; ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']) / 50)); ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']))); ?></td>
                        <?php if($rate_flag == 1){ ?>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($price,2)); ?></td>
                        <?php }else{ ?>
                            <td colspan="2"></td>
                        <?php } ?>
                        <?php if($amount_flag == 1){ ?>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($amount,2)); ?></td>
                        <?php }else{ ?>
                            <td colspan="2"></td>
                        <?php } ?>
                        
                        
                        <td style="text-align:right;" colspan="4"><?php echo number_format_ind($row['quantity'] + $feed_cum); ?></td>
                    </tr>
                    <?php
                    $feed_cum += ($row['quantity']);
                    $tsfout_qty += ($row['quantity']);
                    $tsfout_amt += $amount;
                    if(empty($farm_name[$row['towarehouse']])){
                        $feed_trsout_qty[$row['code']] += ($row['quantity']);
                    }
                    else{
                        $feed_trfout_qty[$row['code']] += ($row['quantity']);
                    }
                    $feed_dwpurin_qty[$row['date']] = $feed_dwpurin_qty[$row['date']] - ($row['quantity']);                    
                }
                ?>
                  <?php
                  if($count54 > 0){
                    $sql = "SELECT * FROM `broiler_itemreturns` WHERE `itemcode` IN ('$feed_list') AND `warehouse` = '$farms' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                    $query = mysqli_query($conn, $sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $price = 0;
                        if($fetch_type == "farmer"){ $price = $feed_cost; $amount = $price * ($row['quantity']); }
                        else if($fetch_type == "mgmt"){ $price = $row['price']; $amount = $row['amount']; }
                        else{ }
                        if($cpdate > strtotime($row['date'])){ $feed_bqty -= ((float)$row['quantity']); }
                        ?>
                        <tr>
                            <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                            <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                            <td style="text-align:left;" colspan="2"><?php echo ""; ?></td>
                            <td style="text-align:left;" colspan="2"><?php echo $item_name[$row['itemcode']]; ?></td>
                            <td style="text-align:left;" colspan="3"><?php echo $sector_name[$row['warehouse']]; ?></td>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']) / 50)); ?></td>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']))); ?></td>
                            <?php if($rate_flag == 1){ ?>
                                <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($price,2)); ?></td>
                            <?php }else{ ?>
                                <td colspan="2"></td>
                            <?php } ?>
                            <?php if($amount_flag == 1){ ?>
                                <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round($amount,2)); ?></td>
                            <?php }else{ ?>
                                <td colspan="2"></td>
                            <?php } ?>
                            
                            
                            <td style="text-align:right;" colspan="4"><?php echo number_format_ind($row['quantity'] + $feed_cum); ?></td>
                        </tr>
                        <?php
                        $feed_cum += ($row['quantity']);
                        $tsfout_qty += ($row['quantity']);
                        $tsfout_amt += $amount;
                        if(empty($farm_name[$row['warehouse']])){
                            $feed_trsout_qty[$row['itemcode']] += ($row['quantity']);
                        }
                        else{
                            $feed_trfout_qty[$row['itemcode']] += ($row['quantity']);
                        }
                        $feed_dwpurin_qty[$row['date']] = $feed_dwpurin_qty[$row['date']] - ($row['quantity']);                    
                    }
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="11" style="text-align:center;">Total</th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsfout_qty / 50))); ?></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsfout_qty),2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($tsfout_amt),2)); ?></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                
                
                <th style="text-align:right;" colspan="4"></th>
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Feed Summary</td>
                </tr>
                <tr align="center">
                    <th colspan="2">Feed Name</th>
                    <th colspan="3">Purchase</th>
                    <th colspan="3">Transfer In</th>
                    <th colspan="3">Feed Consumed</th>
                    <th colspan="3">Feed Return</th>
                    <th colspan="3">Feed Transfer to Other Farms</th>
                    <th colspan="6">Balance</th>
                </tr>
            </thead>
            <thead class="thead4" align="center">
                <tr align="center">
                    <th colspan="2"></th>
                    <th colspan="2">Kgs</th><th colspan="1">Bags</th>
                    <th colspan="2">Kgs</th><th colspan="1">Bags</th>
                    <th colspan="2">Kgs</th><th colspan="1">Bags</th>
                    <th colspan="2">Kgs</th><th colspan="1">Bags</th>
                    <th colspan="2">Kgs</th><th colspan="1">Bags</th>
                    <th colspan="4">Kgs</th><th colspan="2">Bags</th>
                </tr>
            </thead>
            <tbody class="tbody1">
                <?php
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `farm_code` = '$farms' AND `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){ $feed_tcons_qty[$row['item_code1']] += (float)$row['kgs1']; $feed_tcons_qty[$row['item_code2']] += (float)$row['kgs2']; }

                foreach($feed_code as $ficode){
                    if(empty($feed_purin_qty[$ficode]) || (float)$feed_purin_qty[$ficode] == 0 || $feed_purin_qty[$ficode] == ""){ $feed_purin_qty[$ficode] = 0; }
                    if(empty($feed_trsin_qty[$ficode]) || (float)$feed_trsin_qty[$ficode] == 0 || $feed_trsin_qty[$ficode] == ""){ $feed_trsin_qty[$ficode] = 0; }
                    if(empty($feed_tcons_qty[$ficode]) || (float)$feed_tcons_qty[$ficode] == 0 || $feed_tcons_qty[$ficode] == ""){ $feed_tcons_qty[$ficode] = 0; }
                    if(empty($feed_trsout_qty[$ficode]) || (float)$feed_trsout_qty[$ficode] == 0 || $feed_trsout_qty[$ficode] == ""){ $feed_trsout_qty[$ficode] = 0; }
                    if(empty($feed_trfout_qty[$ficode]) || (float)$feed_trfout_qty[$ficode] == 0 || $feed_trfout_qty[$ficode] == ""){ $feed_trfout_qty[$ficode] = 0; }
                    
                    $feed_bal_qty = (((float)$feed_purin_qty[$ficode] + (float)$feed_trsin_qty[$ficode]) - ((float)$feed_tcons_qty[$ficode] + (float)$feed_trsout_qty[$ficode] + (float)$feed_trfout_qty[$ficode]));

                    if(number_format_ind($feed_purin_qty[$ficode]) != "0.00" || number_format_ind($feed_trsin_qty[$ficode]) != "0.00" || number_format_ind($feed_tcons_qty[$ficode]) != "0.00" || number_format_ind($feed_trsout_qty[$ficode]) != "0.00" || number_format_ind($feed_trfout_qty[$ficode]) != "0.00"){
                ?>
                <tr align="center">
                    <td colspan="2"><?php echo $item_name[$ficode]; ?></td>
                    <td colspan="2" style="text-align:right;"><?php echo number_format_ind(round($feed_purin_qty[$ficode],2)); ?></td><td colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($feed_purin_qty[$ficode] / 50),2)); ?></td>
                    <td colspan="2" style="text-align:right;"><?php echo number_format_ind(round($feed_trsin_qty[$ficode],2)); ?></td><td colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($feed_trsin_qty[$ficode] / 50),2)); ?></td>
                    <td colspan="2" style="text-align:right;"><?php echo number_format_ind(round($feed_tcons_qty[$ficode],2)); ?></td><td colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($feed_tcons_qty[$ficode] / 50),2)); ?></td>
                    <td colspan="2" style="text-align:right;"><?php echo number_format_ind(round($feed_trsout_qty[$ficode],2)); ?></td><td colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($feed_trsout_qty[$ficode] / 50),2)); ?></td>
                    <td colspan="2" style="text-align:right;"><?php echo number_format_ind(round($feed_trfout_qty[$ficode],2)); ?></td><td colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($feed_trfout_qty[$ficode] / 50),2)); ?></td>
                    <td colspan="4" style="text-align:right;"><?php echo number_format_ind(round($feed_bal_qty,2)); ?></td><td colspan="2" style="text-align:right;"><?php echo number_format_ind(round(($feed_bal_qty / 50),2)); ?></td>
                </tr>
                <?php
                    $tsin_qty += ((float)$feed_purin_qty[$ficode]);
                    $tfin_qty += ((float)$feed_trsin_qty[$ficode]);
                    $tfcn_qty += (float)$feed_tcons_qty[$ficode];
                    $tfso_qty += (float)$feed_trsout_qty[$ficode];
                    $tffo_qty += (float)$feed_trfout_qty[$ficode];
                    $tfbl_qty += (float)$feed_bal_qty;
                    }
                }
                ?>
                <tr class="thead4" align="center">
                    <th colspan="2" style="text-align:center;">Total</th>
                    <th colspan="2" style="text-align:right;"><?php echo number_format_ind(round($tsin_qty,2)); ?></th><th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tsin_qty / 50),2)); ?></th>
                    <th colspan="2" style="text-align:right;"><?php echo number_format_ind(round($tfin_qty,2)); ?></th><th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tfin_qty / 50),2)); ?></th>
                    <th colspan="2" style="text-align:right;"><?php echo number_format_ind(round($tfcn_qty,2)); ?></th><th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tfcn_qty / 50),2)); ?></th>
                    <th colspan="2" style="text-align:right;"><?php echo number_format_ind(round($tfso_qty,2)); ?></th><th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tfso_qty / 50),2)); ?></th>
                    <th colspan="2" style="text-align:right;"><?php echo number_format_ind(round($tffo_qty,2)); ?></th><th colspan="1" style="text-align:right;"><?php echo number_format_ind(round(($tffo_qty / 50),2)); ?></th>
                    <th colspan="4" style="text-align:right;"><?php echo number_format_ind(round($tfbl_qty,2)); ?></th><th colspan="2" style="text-align:right;"><?php echo number_format_ind(round(($tfbl_qty / 50),2)); ?></th>
                </tr>
            </tbody>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Mortality And Feed Consumption Details</td>
                </tr>
                <tr align="center">
                    <th></th>
                    <th></th>
                    <th>Opening</th>
                    <th colspan="2">Mortality</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Sold</th>
                    <th>Closing</th>
                    <th></th>
                    <th></th>
                    <th colspan="3">Feed 1</th>
                    <th colspan="3">Feed 2</th>
                    <th colspan="2">Cumulative</th>
                    <th colspan="2">Balance Feed</th>
                    <th></th>
                </tr>
                <tr align="center">
                    <th style="width:110px;">Date</th>
                    <th style="width:60px;">Age</th>
                    <th>Birds</th>
                    <th>Count</th>
                    <th>%</th>
                    <th>Culls</th>
                    <th>Cum Mort</th>
                    <th>Cum Mort%</th>
                    <th>Birds</th>
                    <th>Birds</th>
                    <th>Avg.Bw</th>
                    <th>FCR</th>
                    <th>Name</th>
                    <th>Kgs</th>
                    <th>Bags</th>
                    <th>Name</th>
                    <th>Kgs</th>
                    <th>Bags</th>
                    <th>Feed</th>
                    <th>Feed/Bird</th>
                    <th>In Kgs</th>
                    <th>In Bags</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $opn_qty = $trc_qty;
                $sql = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,date FROM `broiler_sales` WHERE `warehouse` = '$farms' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' GROUP BY `date` ORDER BY `date` ASC"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){ $date_sbirds[$row['date']] += $row['birds']; $date_sweights[$row['date']] += $row['rcd_qty']; }

                $sql = "SELECT SUM(birds) as birds,SUM(weight) as rcd_qty,date FROM `broiler_bird_transferout` WHERE `fromwarehouse` = '$farms' AND `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' GROUP BY `date` ORDER BY `date` ASC"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){ $date_sbirds[$row['date']] += $row['birds']; $date_sweights[$row['date']] += $row['rcd_qty']; }

                $sale_cum = $avgwt = $sale_wt = $cum_feed = $week_culls = $week_iqty1 = $week_mort = $week_sbirds = $week_iqty2 = 0; $dstart_date = $mstart_date = "";
                $i = $j = $days7_morts = $days30_mort = $daysge30_mort = 0;
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `farm_code` = '$farms' AND `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    if((int)$row['brood_age'] > 0){
                        $i++;
                    }
                    $date = $row['date'];
                    $brood_age = $row['brood_age'];
                    $mortality = $row['mortality'];
                    if((float)$opn_qty != 0){ $mort_per = (($mortality / $opn_qty) * 100); } else { $mort_per = 0; }
                    $culls = $row['culls'];
                    $total_mort += (float)$mortality;
                    $cum_mort += (float)$mortality + (float)$culls;
                    $week_mort += (float)$mortality;
                    $week_culls += (float)$culls;

                    if( (int)$brood_age > 0 && (int)$brood_age <= 7){ $days7_morts += ((float)$mortality + (float)$culls); $days30_mort += ((float)$mortality + (float)$culls); }
                    else if((int)$brood_age > 7 && (int)$brood_age <= 30){ $days30_mort += ((float)$mortality + (float)$culls); }
                    else if((int)$brood_age > 30){ $daysge30_mort += ((float)$mortality + (float)$culls); }

                    if((float)$trc_qty != 0){ $cum_mort_per = (($cum_mort / $trc_qty) * 100); } else { $cum_mort_per = 0; }
                    if(!empty($date_sbirds[$row['date']])){
                        $sbirds = $date_sbirds[$row['date']];
                        $avgwt = round(((float)$date_sweights[$row['date']] / (float)$date_sbirds[$row['date']]),3);
                        if(number_format_ind($avgwt) == "0.00"){ $avgwt = 0; }
                    }
                    else{
                        $sbirds = 0;
                        if($row['avg_wt'] != "" && (float)$row['avg_wt'] > 0){ $avgwt = round(($row['avg_wt'] / 1000),3); }
                        if(number_format_ind($avgwt) == "0.00"){ $avgwt = 0; }
                    }
                    if(!empty($date_sweights[$row['date']])){ $sale_wt += (float)$date_sweights[$row['date']]; }
                    $cbirds = (float)$opn_qty - (float)$mortality - (float)$culls - (float)$sbirds;
                    
                    $icode1 = $row['item_code1'];
                    $iqty1 = $row['kgs1'];
                    $week_iqty1 += $row['kgs1'];
                    $icode2 = $row['item_code2'];
                    $iqty2 = $row['kgs2'];
                    $week_iqty2 += $row['kgs2'];
                    $cum_feed += ((float)$row['kgs1'] + (float)$row['kgs2']);
                    if((float)$trc_qty != 0){ $cum_feed_bird = round((($cum_feed / $trc_qty) * 1000),2); } else { $cum_feed_bird = 0; }

                    $sale_cum += $sbirds;
                    $week_sbirds += $sbirds;
                    if((float)$avgwt > 0 && (float)$cbirds > 0){ $fcr = $cum_feed / ($avgwt * ((float)$cbirds + (float)$sale_cum)); } else { $fcr = 0; }
                    $fcr_title = "if((float)$avgwt > 0 && (float)$cbirds > 0){ $fcr = $cum_feed / ($avgwt * ((float)$cbirds + (float)$sale_cum)); } else { $fcr = 0; }";
                    
                    if(!empty($feed_dwpurin_qty[$row['date']])){ $feed_bqty += (float)$feed_dwpurin_qty[$row['date']]; } else{ }
                    $feed_bqty = ((float)$feed_bqty - ((float)$iqty1 + (float)$iqty2));
                    $title="$feed_bqty = ((float)$feed_bqty - ((float)$iqty1 + (float)$iqty2))";
                    if($dstart_date == ""){ $dstart_date = $row['date']; } else{ if(strtotime($dstart_date) >= strtotime($row['date'])){ $dstart_date = $row['date']; } }
                    if($row['brood_age'] > 0){ if($mstart_date == ""){ $mstart_date = $row['date']; } else{ if(strtotime($mstart_date) >= strtotime($row['date'])){ $mstart_date = $row['date']; } } }
                ?>
                <tr align="center">
                    <td style="text-align:left;width:110px;"><?php echo date("d.m.Y",strtotime($date)); ?></td>
                    <td style="text-align:right;width:60px;"><?php echo round($brood_age); ?></td>
                    <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($opn_qty,2))); ?></td>
                    <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($mortality))); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($mort_per,2)); ?></td>
                    <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($culls,2))); ?></td>
                    <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cum_mort,2))); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($cum_mort_per,2)); ?></td>
                    <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_sbirds,2))); ?></td>
                    <td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cbirds,2))); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($avgwt,3)); ?></td>
                    <td style="text-align:right;" title="<?php echo $fcr_title; ?>"><?php echo number_format_ind(round($fcr,3)); ?></td>
                    <td style="text-align:left;"><?php echo $item_name[$icode1]; ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($iqty1,2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round(($iqty1 / 50),2)); ?></td>
                    <td style="text-align:left;"><?php echo $item_name[$icode2]; ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($iqty2,2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round(($iqty2 / 50),2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($cum_feed,2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($cum_feed_bird,2)); ?></td>
                    <td style="text-align:right;" title="<?php echo $title; ?>"><?php echo number_format_ind(round($feed_bqty,2)); ?></td>
                    <td style="text-align:right;" title="<?php echo $title; ?>"><?php echo number_format_ind(round(($feed_bqty / 50),2)); ?></td>
                    <td style="text-align:left;"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                if($i == 1){
                    $week_obirds = $opn_qty;
                }
                if($i == 7){
                    $i = 0;
                    $j++;
                    if($j == 1){ $week_name = "1st Week Total"; }
                    else if($j == 2){ $week_name = "2nd Week Total"; }
                    else if($j == 3){ $week_name = "3rd Week Total"; }
                    else{ $week_name = $j."th Week Total"; }
                    ?>
                    <tr align="center">
                        <td style="color:red;font-weight:bold;text-align:left;"><?php echo $week_name; ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_obirds,2))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_mort))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php if((float)$trc_qty != 0){ $t1 = $week_mort / $trc_qty; } else{ $t1 = 0; } echo str_replace(".00","",number_format_ind(round((($t1) * 100),2))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_culls,2))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cum_mort,2))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($cum_mort_per,2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_sbirds,2))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cbirds,2))); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($avgwt,3)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($fcr,3)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:left;"></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($week_iqty1,2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round(($week_iqty1 / 50),2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:left;"></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($week_iqty2,2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round(($week_iqty2 / 50),2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($cum_feed,2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($cum_feed_bird,2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;" title="<?php echo $title; ?>"><?php echo number_format_ind(round($feed_bqty,2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:right;" title="<?php echo $title; ?>"><?php echo number_format_ind(round(($feed_bqty / 50),2)); ?></td>
                        <td style="color:red;font-weight:bold;text-align:left;"></td>
                    </tr>
                    <?php
                    $week_mort = $week_culls = $week_iqty1 = $week_iqty2 = $week_sbirds = 0;
                }
                    $opn_qty = $cbirds;
                    $tculls += $culls;
                    $tkgs1 += $iqty1;
                    $tkgs2 += $iqty2;
                }
                $j++;
                if($i > 0){
                ?>
            <tr align="center">
                <td style="color:red;font-weight:bold;text-align:left;"><?php echo $j."th week Total"; ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_obirds,2))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_mort))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php if((float)$trc_qty != 0){ $t1 = $week_mort / $trc_qty; } else{ $t1 = 0; } echo str_replace(".00","",number_format_ind(round((($t1) * 100),2))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_culls,2))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cum_mort,2))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($cum_mort_per,2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($week_sbirds,2))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cbirds,2))); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($avgwt,3)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($fcr,3)); ?></td>
                <td style="color:red;font-weight:bold;text-align:left;"></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($week_iqty1,2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round(($week_iqty1 / 50),2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:left;"></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($week_iqty2,2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round(($week_iqty2 / 50),2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($cum_feed,2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;"><?php echo number_format_ind(round($cum_feed_bird,2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;" title="<?php echo $title; ?>"><?php echo number_format_ind(round($feed_bqty,2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:right;" title="<?php echo $title; ?>"><?php echo number_format_ind(round(($feed_bqty / 50),2)); ?></td>
                <td style="color:red;font-weight:bold;text-align:left;"></td>
            </tr>
            <?php
                }
            ?>
            </tbody>
            <tr class="thead4" align="center">
                <th colspan="2">Total</th>
                <th style="text-align:right;"><?php echo $trc_qty; ?></th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($total_mort,2))); ?></th>
                <th style="text-align:right;"><?php if((float)$trc_qty != 0){ $t1 = $total_mort / $trc_qty; } else{ $t1 = 0; } echo str_replace(".00","",number_format_ind(round((($t1) * 100),2))); ?></th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($tculls,2))); ?></th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cum_mort,2))); ?></th>
                <th style="text-align:right;"><?php if((float)$trc_qty != 0){ $t1 = ($cum_mort) / $trc_qty; } else{ $t1 = 0; } echo str_replace(".00","",number_format_ind(round((($t1) * 100),2))); ?></th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($sale_cum,2))); ?></th>
                <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($cbirds,2))); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($avgwt,3)); ?></th>
                <th style="text-align:right;"><?php if((float)$sale_wt != 0){ $t1 = $cum_feed / $sale_wt; } else{ $t1 = 0; } echo round($t1,3); ?></th>
                <th style="text-align:left;"></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tkgs1,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tkgs1 / 50),2)); ?></th>
                <th style="text-align:left;"></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tkgs2,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($tkgs2 / 50),2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($cum_feed,2)); ?></th>
                <th style="text-align:right;"><?php if((float)$trc_qty != 0){ $t1 = (($cum_feed / $trc_qty) * 1000); } else{ $t1 = 0; } echo str_replace(".00","",number_format_ind(round($t1,2))); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($feed_bqty,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round(($feed_bqty / 50),2)); ?></th>
                <th style="text-align:left;"></th>
            </tr>
            <?php
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'GC: MedVac Price' AND `field_function` = 'Latest STK-IN Price with Percentage' AND `user_access` = 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $fmvsipwp_flag = 0; if($count > 0){ $fmvsipwp_flag = 1; }

            $medvac_list = implode("','",$medvac_code);
            $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` IN ('$medvac_list') AND `warehouse` = '$farms' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
            $query = mysqli_query($conn, $sql); $count = mysqli_num_rows($query);
            if($count > 0){
            ?>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Purchase</td>
                </tr>
                <tr align="center">
                    <th colspan="1">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="3">From Supplier</th>
                    <th colspan="3">Medicine/Vaccine</th>
                    <th colspan="2">Quantity</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="1">Rate</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="1">Amount</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                   
                    <th colspan="8"></th>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $tpmv_qty = $tpmv_prc = $tpmv_amt = 0; $tmv_price = $tmv_dprice = array();
                
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){
                        if($medicine_cost == "M"){
                            $ficode = $row['code']; $fidate = $row['date'];
                            $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                            $queryf = mysqli_query($conn,$sqlf); $countf = mysqli_num_rows($queryf);
                            
                            if($countf > 0){
                                while($rowf = mysqli_fetch_assoc($queryf)){ $price = $rowf['rate']; }
                            }
                            else{
                                $price = $row['farmer_price'];
                            }
                            /*if($row['farmer_price'] == "" || (float)$row['farmer_price'] == 0){
                                $ficode = $row['icode']; $fidate = $row['date'];
                                $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                                $queryf = mysqli_query($conn,$sqlf); while($rowf = mysqli_fetch_assoc($queryf)){ $price = $rowf['rate']; }
                            }
                            else{
                                $price = $row['farmer_price'];
                            }*/
                        }
                        else if($medicine_cost == "F"){ $price = $med_price; }
                        else if($medicine_cost == "A"){
                            $price = $row['rate'];
                        }
                        else{
                            $price = $row['rate'];
                        }
                        $amount = $price * ($row['rcd_qty'] + $row['fre_qty']);
                    }
                    else if($fetch_type == "mgmt"){
                        $price = $row['rate']; $amount = $row['item_tamt'];
                    }
                    else{ }

                ?>
                <tr>
                    <td style="text-align:left;" colspan="1"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['billno']; ?></td>
                    <td style="text-align:left;" colspan="3"><?php echo $vendor_name[$row['vcode']]; ?></td>
                    <td style="text-align:left;" colspan="3"><?php echo $item_name[$row['icode']]; ?></td>
                    <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['rcd_qty'] + $row['fre_qty']),2)); ?></td>
                    <?php if($rate_flag == 1){ ?>
                        <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round($price,2)); ?></td>
                    <?php }else{ ?>
                        <td colspan="1"></td>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="1"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="1"></td>
                    <?php } ?>
                    
                    
                    <td style="text-align:right;" colspan="8"></td>
                </tr>
            <?php
                $tpmv_qty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                $tpmv_amt += $amount;
                $tmv_price[$row['icode']] = $price;
                $tmv_dprice[$row['icode']."@".$row['date']] = $price;
                }
                if((float)$tpmv_qty != 0){ $tpmv_prc = $tpmv_amt / $tpmv_qty; } else { $tpmv_prc = 0; }
            ?>
            </tbody>
            <tr class="thead4" align="center">
                <th colspan="11"></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round($tpmv_qty,2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round($tpmv_prc,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round($tpmv_amt,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                
                <th colspan="8"></th>
            </tr>
            <?php
            }
            ?>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Transfer In</td>
                </tr>
                <tr align="center">
                    <th colspan="1">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="3">From Warehouse</th>
                    <th colspan="2">Code</th>
                    <th colspan="3">Medicine/Vaccine</th>
                    <th colspan="2">Quantity</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="1">Rate</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="1">Amount</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                    <th colspan="6"></th>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $tsmv_qty = $tsmv_prc = $tsmv_amt = $id = 0;
                $medvac_list = implode("','",$medvac_code);
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$medvac_list') AND `towarehouse` = '$farms' AND `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = $fe_prc = 0;
                    $id++; $value = $row['id']."@".$row['date']."@".$row['fromwarehouse']."@".$row['code']."@".$row['trnum']."@".$row['quantity'];
                    if($fetch_type == "farmer"){
                        if((int)$fmvsipwp_flag == 1){
                            $price = $row['farmer_price'];
                        }
                        else if($medicine_cost == "M"){
                            $ficode = $row['code']; $fidate = $row['date'];
                            $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                            $queryf = mysqli_query($conn,$sqlf); $countf = mysqli_num_rows($queryf);
                            
                            if($countf > 0){
                                while($rowf = mysqli_fetch_assoc($queryf)){ $price = $rowf['rate']; }
                            }
                            else{
                                $price = $row['farmer_price'];
                            }
                            $fe_prc = $row['farmer_price'];
                        }
                        else if($medicine_cost == "F"){ $price = $med_price; }
                        else if($medicine_cost == "A"){
                            $price = $row['price'];
                        }
                        else{
                            $price = $row['price'];
                        }
                        $amount = $price * ($row['quantity']);
                    }
                    else if($fetch_type == "mgmt"){
                        if((float)$row['quantity'] != 0){
                            $price = $row['amount'] / $row['quantity'];
                        }
                        else{
                            $price = 0;
                        }
                        $amount = $row['amount'];
                    }
                    else{ }
                ?>
                <tr>
                    <td style="text-align:left;" colspan="1"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['dcno']; ?></td>
                    <td style="text-align:left;" colspan="3"><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['code']; ?></td>
                    <td style="text-align:left;" colspan="3"><?php echo $item_name[$row['code']]; ?></td>
                    <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']),2)); ?></td>
                    <?php
                    if($rate_flag == 1){
                        if($fetch_type == "farmer"){
                            if((float)$fe_prc == (float)$price){
                                ?>
                                <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round($price,2)); ?></td>
                                <?php
                            }
                            else{
                                ?>
                                <td style="text-align:right;" colspan="1"><b><?php echo number_format_ind(round($price,2)); ?></b></td>
                                <?php
                            }
                        }
                        else{
                            ?>
                            <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round($price,2)); ?></td>
                            <?php
                        }
                    }
                    else{
                    ?>
                    <td colspan="1"></td>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="1"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="1"></td>
                    <?php } ?>
                    
                    
                    <td style="text-align:left;" colspan="6">
                    <?php
                    if(number_format_ind(round($price,2)) == "0.00"){
                        echo '<a href="javascript:void(0)" id="medvac_transferin['.$value.']" value="'.$value.'" onclick="broiler_check_prices(this.id);"><i class="fa-solid fa-rotate" title="Reload" style="color:#0AADD5;"></i></a>';
                    }
                    ?>
                    </td>
                </tr>
            <?php
                $tsmv_qty += ((float)$row['quantity']);
                $tsmv_amt += $amount;
                $tmv_price[$row['code']] = $price;
                $tmv_dprice[$row['code']."@".$row['date']] = $price;
                }
                if((float)$tsmv_qty != 0){ $tsmv_prc = $tsmv_amt / $tsmv_qty; } else { $tsmv_prc = 0; }
            ?>
            </tbody>
            <tr class="thead4" align="center">
                <th colspan="13"></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round($tsmv_qty,2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round($tsmv_prc,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round($tsmv_amt,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                <th colspan="7"></th>
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Consumption</td>
                </tr>
                <tr align="center">
                    <th colspan="1">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">Code</th>
                    <th colspan="3">Medicine/Vaccine</th>
                    <th colspan="2">Quantity</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="1">Rate</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="2">Amount</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                    <?php } ?>
                    <th colspan="11"></th>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $tcmv_qty = $tcmv_prc = $tcmv_amt = 0;
                $medvac_list = implode("','",$medvac_code);
                $sql = "SELECT * FROM `broiler_medicine_record` WHERE `item_code` IN ('$medvac_list') AND `farm_code` = '$farms' AND `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    //echo "<br/>".$tmv_price[$row['item_code']];
                    $price = 0;
                    if(!empty($tmv_dprice[$row['item_code']."@".$row['date']]) && (float)$tmv_dprice[$row['item_code']."@".$row['date']] != 0){
                        $price = $tmv_dprice[$row['item_code']."@".$row['date']];
                    }
                    else{
                        $price = $tmv_price[$row['item_code']];
                    }
                    $amount = $price * $row['quantity'];

                ?>
                <tr>
                    <td style="text-align:left;" colspan="1"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['item_code']; ?></td>
                    <td style="text-align:left;" colspan="3"><?php echo $item_name[$row['item_code']]; ?></td>
                    <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']),2)); ?></td>
                    <?php if($rate_flag == 1){ ?>
                        <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round($price,2)); ?></td>
                    <?php }else{ ?>
                        <td colspan="1"></td>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="2"></td>
                    <?php } ?>
                    <td style="text-align:right;" colspan="11"></td>
                </tr>
            <?php
                $tcmv_qty += ((float)$row['quantity']);
                $tcmv_amt += $amount;
                }
                if((float)$tcmv_qty != 0){ $tcmv_prc = $tcmv_amt / $tcmv_qty; } else { $tcmv_prc = 0; }
            ?>
            </tbody>
            <tr class="thead4" align="center">
                <th colspan="8"></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round($tcmv_qty,2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round($tcmv_prc,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round($tcmv_amt,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                <th colspan="11"></th>
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Bio Security Consumption</td>
                </tr>
                <tr align="center">
                    <th colspan="1">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">Code</th>
                    <th colspan="3">Item</th>
                    <th colspan="2">Quantity</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="1">Rate</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="2">Amount</th>
                    <?php }else{ ?>
                        <th colspan="2"></th>
                    <?php } ?>
                    <th colspan="11"></th>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $tcbs_qty = $tcbs_prc = $tcbs_amt = 0;
                $biosec_list = implode("','",$biosec_code);
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` IN ('$biosec_list') AND `towarehouse` = '$farms' AND `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = $row['price'];
                    $amount = $price * $row['quantity'];

                ?>
                <tr>
                    <td style="text-align:left;" colspan="1"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['code']; ?></td>
                    <td style="text-align:left;" colspan="3"><?php echo $item_name[$row['code']]; ?></td>
                    <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['quantity']),2)); ?></td>
                    <?php if($rate_flag == 1){ ?>
                        <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round($price,2)); ?></td>
                    <?php }else{ ?>
                        <td colspan="1"></td>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="2"></td>
                    <?php } ?>
                    <td style="text-align:right;" colspan="11"></td>
                </tr>
            <?php
                $tcbs_qty += ((float)$row['quantity']);
                $tcbs_amt += $amount;
                }
                if((float)$tcbs_qty != 0){ $tcbs_prc = $tcbs_amt / $tcbs_qty; } else { $tcbs_prc = 0; }
            ?>
            </tbody>
            <tr class="thead4" align="center">
                <th colspan="8"></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round($tcbs_qty,2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round($tcbs_prc,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round($tcbs_amt,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="2"></th>
                <?php } ?>
                <th colspan="11"></th>
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Medicine and Vaccine Return</td>
                </tr>
                <tr align="center">
                    <th colspan="2">Date</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="2">To Warehouse</th>
                    <th colspan="2">Code</th>
                    <th colspan="4">Medicine/Vaccine</th>
                    <th colspan="3">Quantity</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="3">Rate</th>
                    <?php }else{ ?>
                        <th colspan="3"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="3">Amount</th>
                    <?php }else{ ?>
                        <th colspan="3"></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $trmv_qty = $trmv_prc = $trmv_amt = $trbs_qty = $trbs_prc = $trbs_amt = 0;
                $medvac_list = implode("','",$medvac_code);
                $biosec_list = implode("','",$biosec_code);
                $sql = "SELECT * FROM `item_stocktransfers` WHERE (`code` IN ('$medvac_list') OR `code` IN ('$biosec_list')) AND `fromwarehouse` = '$farms' AND `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    $price = 0;
                    if($fetch_type == "farmer"){
                        if($medicine_cost == "M"){
                            $ficode = $row['code']; $fidate = $row['date'];
                            $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                            $queryf = mysqli_query($conn,$sqlf); $countf = mysqli_num_rows($queryf);
                            
                            if($countf > 0){
                                while($rowf = mysqli_fetch_assoc($queryf)){ $price = $rowf['rate']; }
                            }
                            else{
                                $price = $row['farmer_price'];
                            }
                            /*if($row['farmer_price'] == "" || (float)$row['farmer_price'] == 0){
                                $ficode = $row['code']; $fidate = $row['date'];
                                $sqlf = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `itemcode` = '$ficode' AND `date` <= '$fidate' AND `active` = '1' AND `dflag` = '0')";
                                $queryf = mysqli_query($conn,$sqlf); while($rowf = mysqli_fetch_assoc($queryf)){ $price = $rowf['rate']; }
                            }
                            else{
                                $price = $row['farmer_price'];
                            }*/
                        }
                        else if($medicine_cost == "F"){ $price = $med_price; }
                        else if($medicine_cost == "A"){
                            $price = $row['price'];
                        }
                        else{
                            $price = $row['price'];
                        }
                        $amount = $price * ($row['quantity']);
                    }
                    else if($fetch_type == "mgmt"){
                        $price = $row['price']; $amount = $row['amount'];
                    }
                    else{ }
                    if(!empty($biosec_code[$row['code']]) && $biosec_code[$row['code']] == $row['code']){
                        $trbs_qty += (float)$row['quantity'];
                        $trbs_amt += (float)$row['amount'];
                    }
                ?>
                <tr>
                    <td style="text-align:left;" colspan="2"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['dcno']; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $sector_name[$row['towarehouse']]; ?></td>
                    <td style="text-align:left;" colspan="2"><?php echo $row['code']; ?></td>
                    <td style="text-align:left;" colspan="4"><?php echo $item_name[$row['code']]; ?></td>
                    <td style="text-align:right;" colspan="3"><?php echo number_format_ind(round(($row['quantity']),2)); ?></td>
                    <?php if($rate_flag == 1){ ?>
                        <td style="text-align:right;" colspan="3"><?php echo number_format_ind(round($price,2)); ?></td>
                    <?php }else{ ?>
                        <td colspan="3"></td>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <td style="text-align:right;" colspan="3"><?php echo number_format_ind($amount); ?></td>
                    <?php }else{ ?>
                        <td colspan="3"></td>
                    <?php } ?>
                    
                    
                </tr>
            <?php
                $trmv_qty += ((float)$row['quantity']);
                $trmv_amt += $amount;
                }
                if((float)$trmv_qty != 0){ $trmv_prc = $trmv_amt / $trmv_qty; } else { $trmv_prc = 0; }
            ?>
            </tbody>
            <tr class="thead4" align="center">
                <th colspan="14"></th>
                <th style="text-align:right;" colspan="3"><?php echo number_format_ind(round($trmv_qty,2)); ?></th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="3"><?php echo number_format_ind(round($trmv_prc,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="3"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="3"><?php echo number_format_ind(round($trmv_amt,2)); ?></th>
                <?php }else{ ?>
                    <th colspan="3"></th>
                <?php } ?>
                
                
            </tr>
            <thead class="report_head" align="center">
                <tr class="report_head">
                    <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:13px;">Bird Sales</td>
                </tr>
                <tr align="center">
                    <th colspan="1">Date</th>
                    <th colspan="1">Age</th>
                    <th colspan="2">Transaction No.</th>
                    <th colspan="2">DC No.</th>
                    <th colspan="4">Customer</th>
                    <th colspan="2">Birds</th>
                    <th colspan="2">Net Weight</th>
                    <th colspan="1">Avg.Bw</th>
                    <?php if($rate_flag == 1){ ?>
                        <th colspan="1">Rate</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                    <?php } ?>
                    <?php if($amount_flag == 1){ ?>
                        <th colspan="1">TCS Amount</th>
                        <th colspan="2">Amount</th>
                    <?php }else{ ?>
                        <th colspan="1"></th>
                        <th colspan="2"></th>
                    <?php } ?>
                    <th colspan="4">Remarks</th>
                </tr>
            </thead>
            <tbody class="tbody1">
            <?php
                $i = 0;
                $sql = "SELECT * FROM `broiler_sales` WHERE `icode` = '$bird_code' AND `warehouse` = '$farms' AND `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($i == 0){
                        $sale_sdate = date("d.m.Y",strtotime($row['date']));
                        $i = 1;
                    }
                    //Mean Age Calculations
                    if(strtotime($row['date']) >= strtotime($mstart_date)){
                        $dlist = (INT)((strtotime($row['date']) - strtotime($mstart_date)) / 60 / 60 / 24);
                        $dlist2 = $dlist + 1;
                        $sbirds = (float)$row['birds'];
                        $sold_mean_total += ($dlist2 * $sbirds);
                    }
                    ?>
                    <tr>
                        <td style="text-align:left;" colspan="1"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                        <td style="text-align:left;" colspan="1"><?php echo ((strtotime($row['date']) - strtotime($mstart_date)) / 60 / 60 / 24) + 1; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['billno']; ?></td>
                        <td style="text-align:left;" colspan="4"><?php if($row['sale_type'] == "FormMBSale"){ echo $farm_name[$farms]; } else{ echo $vendor_name[$row['vcode']]; } ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo str_replace(".00","",number_format_ind(round(($row['birds'])))); ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind((float)$row['rcd_qty'] + (float)$row['fre_qty']); ?></td>
                        <td style="text-align:right;" colspan="1">
                        <?php
                        if((float)$row['birds'] > 0){
                            echo number_format_ind(((float)$row['rcd_qty'] + (float)$row['fre_qty']) / (float)$row['birds']);
                        }
                        else{
                            echo number_format_ind(0);
                        }
                        
                        ?></td>
                        <!--<td style="text-align:right;" colspan="1">
                        <?php
                        /*if(((float)$row['rcd_qty'] + (float)$row['fre_qty']) > 0){
                            echo number_format_ind(round(((float)$row['item_tamt'] / ((float)$row['rcd_qty'] + (float)$row['fre_qty'])),2));
                        }
                        else{
                            echo number_format_ind(0);
                        }
                         */   
                        ?></td>-->
                        <?php if($rate_flag == 1){ ?>
                            <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round(($row['rate']))); ?></td>
                        <?php }else{ ?>
                            <td colspan="1"></td>
                        <?php } ?>
                        <?php if($amount_flag == 1){ ?>
                            <td style="text-align:right;" colspan="1"><?php echo number_format_ind(round(($row['tcds_amt']))); ?></td>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['item_tamt']))); ?></td>
                        <?php }else{ ?>
                            <td colspan="1"></td>
                            <td colspan="2"></td>
                        <?php } ?>
                        
                        
                        <td style="text-align:right;" colspan="5"></td>
                    </tr>
                    <?php
                    $ts_birds += (float)$row['birds'];
                    $ts_weight += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                    $ts_tcs_amt += (float)$row['tcds_amt'];
                    $ts_amt += (float)$row['item_tamt'];
                }

                if($count131 > 0){
                    $i = 0;
                $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `item_code` = '$bird_code' AND `fromwarehouse` = '$farms' AND `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($query)){
                    if($i == 0){
                        $sale_sdate = date("d.m.Y",strtotime($row['date']));
                        $i = 1;
                    }
                    //Mean Age Calculations
                    if(strtotime($row['date']) >= strtotime($mstart_date)){
                        $dlist = (INT)((strtotime($row['date']) - strtotime($mstart_date)) / 60 / 60 / 24);
                        $dlist2 = $dlist + 1;
                        $sbirds = (float)$row['birds'];
                        $sold_mean_total += ($dlist2 * $sbirds);
                    }
                    ?>
                    <tr>
                        <td style="text-align:left;" colspan="1"><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                        <td style="text-align:left;" colspan="1"><?php echo ((strtotime($row['date']) - strtotime($mstart_date)) / 60 / 60 / 24) + 1; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['trnum']; ?></td>
                        <td style="text-align:left;" colspan="2"><?php echo $row['dcno']; ?></td>
                        <td style="text-align:left;" colspan="4"><?php echo $sector_name[$row['towarehouse']]; ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo str_replace(".00","",number_format_ind(round(($row['birds'])))); ?></td>
                        <td style="text-align:right;" colspan="2"><?php echo number_format_ind($row['weight']); ?></td>
                        <td style="text-align:right;" colspan="1">
                        <?php
                        if((float)$row['birds'] > 0){
                            echo number_format_ind(((float)$row['weight'] ) / (float)$row['birds']);
                        }
                        else{
                            echo number_format_ind(0);
                        }
                        
                        ?></td>

                        <?php if($rate_flag == 1){ ?>
                            <td style="text-align:right;" colspan="1">
                            <?php
                            if(((float)$row['weight']) > 0){
                                echo number_format_ind(round(((float)$row['avg_amount'] / ((float)$row['weight'])),2));
                            }
                            else{
                                echo number_format_ind(0);
                            }
                                
                            ?></td>
                        <?php }else{ ?>
                            <td colspan="1"></td>
                        <?php } ?>
                        <?php if($amount_flag == 1){ ?>
                            <td style="text-align:right;" colspan="1"></td>
                            <td style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($row['avg_amount']))); ?></td>
                        <?php }else{ ?>
                            <td colspan="1"></td>
                            <td colspan="2"></td>
                        <?php } ?>
                        <td style="text-align:right;" colspan="5"></td>
                    </tr>
                    <?php
                    $ts_birds += (float)$row['birds'];
                    $ts_weight += ((float)$row['weight']);
                    $ts_amt += (float)$row['avg_amount'];
                }
                }
            ?>
            </tbody>
            <tr class="thead4">
                <th colspan="10" style="text-align:center;">Total</th>
                <th style="text-align:right;" colspan="2"><?php echo str_replace(".00","",number_format_ind(round(($ts_birds),2))); ?></th>
                <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($ts_weight),2)); ?></th>
                <th style="text-align:right;" colspan="1">
                <?php
                if($ts_birds > 0){
                    echo number_format_ind(round(((float)$ts_weight / (float)$ts_birds),2));
                }
                else{
                    echo number_format_ind(0);
                }
                    
                ?>
                </th>
                <?php if($rate_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1">
                    <?php
                    if($ts_weight > 0){
                        echo number_format_ind(round((((float)$ts_amt - (float)$ts_tcs_amt) / (float)$ts_weight),2));
                    }
                    else{
                        echo number_format_ind(0);
                    }
                        
                    ?>
                    </th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                <?php } ?>
                <?php if($amount_flag == 1){ ?>
                    <th style="text-align:right;" colspan="1"><?php echo number_format_ind(round(($ts_tcs_amt),2)); ?></th>
                    <th style="text-align:right;" colspan="2"><?php echo number_format_ind(round(($ts_amt),2)); ?></th>
                <?php }else{ ?>
                    <th colspan="1"></th>
                    <th colspan="2"></th>
                <?php } ?>
                <th style="text-align:right;" colspan="4"><?php echo $row['remarks']; ?></th>
            </tr>
            <?php if($batchdetails_flag == 1){ ?>
                <thead class="report_head" align="center">
                    <tr class="report_head">
                        <td colspan="23" style="height: 25px;color:blue;font-weight:bold;font-size:15px;">Batch Costing Information and Summary</td>
                    </tr>
                    <?php
                    $sql_record = "SELECT * FROM `broiler_rearingcharge` WHERE `batch_code` LIKE '$batches' AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql_record); $rcount = mysqli_num_rows($query);
                    if($rcount > 0){
                        while($row = mysqli_fetch_assoc($query)){
                            if($fetch_type == "farmer"){
                                $smr_placement_date = date("d.m.Y",strtotime($row['start_date']));
                                $smr_placed_chicks = $row['placed_birds'];
                                $smr_mortality = $cum_mort;
                                $smr_culls = $tculls;
                                $smr_excess = $row['excess'];
                                $smr_shortage = $row['shortage'];
                                $smr_grade = $row['grade'];
                                $smr_days7_mort_per = $row['days7_mort'];
                                $smr_days30_mort_per = $row['days30_mort'];
                                $smr_days31g_mort_per = $row['daysge31_mort'];
                                $smr_avg_wt = $row['avg_wt'];
                                $smr_mean_age = $row['mean_age'];
                                $smr_day_gain = $row['day_gain'];
                                $smr_fcr = $row['fcr'];
                                $smr_sale_sdate = $sale_sdate;
                                $smr_sold_birds = $row['sold_birds'];
                                $smr_sold_weight = $row['sold_weight'];
                                $smr_sale_amount = $row['sale_amount'];
                                $smr_sale_rate = $row['sale_rate'];
                                $smr_eef = $row['eef'];
                                $smr_cfcr = $row['cfcr'];
                                $smr_feed_in = $row['feed_in_kgs'];
                                $smr_feed_consumed = $row['feed_consume_kgs'];
                                $smr_feed_out = $row['feed_out_kgs'];
                                $smr_actual_chick_cost = $row['chick_cost_amt'];
                                $smr_feed_cost_amt = $row['feed_cost_amt'];
                                $smr_total_cost_amt = $row['total_cost_amt'] + ((float)$tcbs_amt - (float)$trbs_amt);
                                $smr_total_cost_unit = $row['total_cost_unit'];
                                $smr_admin_cost_amt = $row['admin_cost_amt'];
                                $smr_admin_cost_unit = $row['admin_cost_unit'];
                                $smr_medvac_in = $row['transfer_in'];
                                $smr_medvac_consumed = $row['consumption'];
                                $smr_medvac_out = $row['transfer_out'];
                                $smr_medvac_amount = $row['medicine_cost_amt'];
                                $smr_standard_prod_cost = $row['standard_prod_cost'];
                            }
                            else if($fetch_type == "mgmt"){
                                $smr_placement_date = date("d.m.Y",strtotime($row['start_date']));
                                $smr_placed_chicks = $row['placed_birds'];
                                $smr_mortality = $cum_mort;
                                $smr_culls = $tculls;
                                $smr_excess = $row['excess'];
                                $smr_shortage = $row['shortage'];
                                $smr_grade = $row['grade'];
                                $smr_days7_mort_per = $row['days7_mort'];
                                $smr_days30_mort_per = $row['days30_mort'];
                                $smr_days31g_mort_per = $row['daysge31_mort'];
                                $smr_avg_wt = $row['avg_wt'];
                                $smr_mean_age = $row['mean_age'];
                                $smr_day_gain = $row['day_gain'];
                                $smr_fcr = $row['fcr'];
                                $smr_sale_sdate = $sale_sdate;
                                $smr_sold_birds = $row['sold_birds'];
                                $smr_sold_weight = $row['sold_weight'];
                                $smr_sale_amount = $row['sale_amount'];
                                $smr_sale_rate = $row['sale_rate'];
                                $smr_eef = $row['eef'];
                                $smr_cfcr = $row['cfcr'];
                                $smr_feed_in = $row['feed_in_kgs'];
                                $smr_feed_consumed = $row['feed_consume_kgs'];
                                $smr_feed_out = $row['feed_out_kgs'];
                                $smr_actual_chick_cost = $row['actual_chick_cost'];
                                $smr_feed_cost_amt = $row['actual_feed_cost'];
                                $smr_total_cost_amt = $row['actual_chick_cost'] + $row['actual_feed_cost'] + $row['actual_medicine_cost'] + $row['mgmt_admin_amt'] + ((float)$tcbs_amt - (float)$trbs_amt);
                                $smr_total_cost_unit = (($row['actual_chick_cost'] + $row['actual_feed_cost'] + $row['actual_medicine_cost'] + $row['mgmt_admin_amt']) / $row['sold_weight']);
                                $smr_admin_cost_amt = $row['mgmt_admin_amt'];
                                $smr_admin_cost_unit = $row['mgmt_admin_prc'];
                                $smr_medvac_in = $row['transfer_in'];
                                $smr_medvac_consumed = $row['consumption'];
                                $smr_medvac_out = $row['transfer_out'];
                                $smr_medvac_amount = $row['actual_medicine_cost'];
                                $smr_standard_prod_cost = $row['standard_prod_cost'];
                            }
                            else{ }
                        }
                    }
                    else{
                        if($cpdate == ""){
                            $smr_placement_date = "<b style='color:red'>Chicks Not Placed</b>";
                        }
                        else{
                            $smr_placement_date = date("d.m.Y",$cpdate);
                        }
                        
                        $smr_sale_sdate = $sale_sdate;
                        $smr_sold_birds = $ts_birds;
                        $smr_sold_weight = $ts_weight;
                        $smr_sale_amount = $ts_amt;

                        $smr_placed_chicks = $trc_qty;
                        $smr_mortality = $cum_mort;
                        $smr_culls = $tculls;
                        if(((float)$trc_qty - ((float)$cum_mort + (float)$tculls + (float)$smr_sold_birds)) >= 0){
                            $smr_shortage = ((float)$trc_qty - ((float)$cum_mort + (float)$tculls + (float)$smr_sold_birds));
                            $sht_title = "$smr_shortage = ((float)$trc_qty - ((float)$cum_mort + (float)$tculls + (float)$smr_sold_birds));";
                        }
                        else{
                            $smr_excess = ((float)$trc_qty - ((float)$cum_mort + (float)$tculls + (float)$smr_sold_birds));
                            $sht_title = "$smr_shortage = ((float)$trc_qty - ((float)$cum_mort + (float)$tculls + (float)$smr_sold_birds));";
                        }
                        $smr_grade = "";
                        if((float)$smr_placed_chicks != 0){
                            $smr_days7_mort_per = round((($days7_morts / $smr_placed_chicks) * 100),2);
                            $smr_days30_mort_per = round((($days30_mort / $smr_placed_chicks) * 100),2);
                            $smr_days31g_mort_per = round((($daysge30_mort / $smr_placed_chicks) * 100),2);
                        }
                        else{
                            $smr_days7_mort_per = $smr_days30_mort_per = $smr_days31g_mort_per = 0;
                        }
                        
                        if((float)$ts_birds != 0){ $smr_avg_wt = round(((float)$ts_weight / (float)$ts_birds),3); } else{ $smr_avg_wt =  0; }
                        if((float)$ts_birds != 0){ $smr_mean_age = round(((float)$sold_mean_total / (float)$ts_birds),2); } else { $smr_mean_age = 0; }
                        if((float)$ts_birds != 0){ $avg_weight = round(((float)$ts_weight / (float)$ts_birds),3); } else { $avg_weight = 0; }
                        if((float)$smr_mean_age != 0 && (float)$avg_weight != 0){ $smr_day_gain = round((((float)$avg_weight * 1000) / (float)$smr_mean_age),2); } else { $smr_day_gain = 0; }
                        
                        
                        if((float)$smr_sold_weight > 0 && (float)$tfcn_qty > 0){
                            $smr_fcr = round(((float)$tfcn_qty / (float)$smr_sold_weight),3);
                        }
                        else{
                            $smr_fcr = $fcr;
                        }
                        if((float)$ts_weight != 0){ $smr_sale_rate = $ts_amt / $ts_weight; } else{ $smr_sale_rate = 0; }

                        //EEF Calculations
                        $t1 = 0; $t1 = ((float)$smr_placed_chicks - (float)$smr_mortality);
                        $t2 = 0; $t2 = (float)$smr_placed_chicks;
                        $t3 = 0; $t3 = (float)$avg_weight;
                        $t4 = 0; $t4 = ((float)$smr_fcr * (float)$smr_mean_age);
                        if($t1 > 0 && $t2 > 0 && $t3 > 0 && $t4 > 0){ $smr_eef = round((((((($t1) / $t2) * 100) * $t3) * 100) / ($t4))); } else{ $smr_eef = 0; }
                        $smr_cfcr = round((((2 - ((float)$avg_weight)) / 4) + (float)$smr_fcr),3);

                        $smr_feed_in = $tpfin_qty + $tsfin_qty;
                        $smr_feed_consumed = (((float)$tpfin_qty + (float)$tsfin_qty) - (float)$tsfout_qty);
                        $smr_feed_out = $tsfout_qty;
                        $smr_actual_chick_cost = $trc_amt;
                        $smr_feed_cost_amt = ((float)$tpfin_amt + (float)$tsfin_amt - (float)$tsfout_amt);
                        if($fetch_type == "farmer"){
                            $smr_admin_cost_amt = $admin_cost * $smr_placed_chicks;
                            $smr_admin_cost_unit = $admin_cost;
                        }
                        else{
                            $smr_admin_cost_amt = $mgmt_admin_prc * $smr_placed_chicks;
                            $smr_admin_cost_unit = $mgmt_admin_prc;
                        }
                        $smr_total_cost_amt = $trc_amt + ((float)$tpfin_amt + (float)$tsfin_amt - (float)$tsfout_amt) + (float)$tcmv_amt + (float)$smr_admin_cost_amt + ((float)$tcbs_amt - (float)$trbs_amt);
                        if((float)$ts_weight != 0){ $smr_total_cost_unit = (($trc_amt + ((float)$tpfin_amt + (float)$tsfin_amt - (float)$tsfout_amt) + (float)$tcmv_amt + (float)$smr_admin_cost_amt) / $ts_weight); } else{ $smr_total_cost_unit = 0; }
                        
                        $smr_medvac_in = $tsmv_qty;
                        $smr_medvac_consumed = $tcmv_qty;
                        $smr_medvac_out = $trmv_qty;
                        $smr_medvac_amount = $tcmv_amt;
                        $smr_standard_prod_cost = $standard_prod_cost;
                    }
                    //if($_SERVER['REMOTE_ADDR'] == "49.205.128.67"){
                    ?>
                    <tr align="left">
                        <th colspan="3">Placement Date</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $smr_placement_date; ?></td>
                        <th colspan="3">1st week Mort%</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_days7_mort_per,2)); ?></td>
                        <th colspan="3">Sale Start Date</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $smr_sale_sdate; ?></td>
                        <th colspan="2">Feed Sent</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_feed_in,2)); ?></td>
                        <th colspan="2">Med/Vac Sent</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_medvac_in,2)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Chicks Placed</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($smr_placed_chicks)); ?></td>
                        <th colspan="3">Upto 30days Mort%</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_days30_mort_per,2)); ?></td>
                        <th colspan="3">Sold Birds</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($smr_sold_birds)); ?></td>
                        <th colspan="2">Feed Consumed</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_feed_consumed,2)); ?></td>
                        <th colspan="2">Med/Vac Consumed</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_medvac_consumed,2)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Mortality</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($smr_mortality)); ?></td>
                        <th colspan="3">After 30days Mort%</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_days31g_mort_per,2)); ?></td>
                        <th colspan="3">Sold Weight</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_sold_weight,2)); ?></td>
                        <th colspan="2">Feed Return</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_feed_out,2)); ?></td>
                        <th colspan="2">Med/Vac Return</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_medvac_out,2)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Culls</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($smr_culls)); ?></td>
                        <th colspan="3">Avg.Body Weight</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo round($smr_avg_wt,3); ?></td>
                        <th colspan="3">Sold Amount</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_sale_amount,2)); ?></td>
                        <th colspan="2">Feed Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_feed_cost_amt,2)); ?></td>
                        <th colspan="2">Med/Vac Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_medvac_amount,2)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Excess Birds</th><td colspan="2" class="thead4" style="text-align:right;" title="<?php echo $sht_title; ?>"><?php echo str_replace(".00","",number_format_ind($smr_excess)); ?></td>
                        <th colspan="3">Mean Age</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_mean_age,2)); ?></td>
                        <th colspan="3">Avg. Sale Rate</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_sale_rate,2)); ?></td>
                        <th colspan="2">Chick Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_actual_chick_cost,2)); ?></td>
                        <th colspan="2">Admin Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_admin_cost_amt,2)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Shortage Birds</th><td colspan="2" class="thead4" style="text-align:right;" title="<?php echo $sht_title; ?>"><?php echo str_replace(".00","",number_format_ind($smr_shortage)); ?></td>
                        <th colspan="3">Day Gain</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_day_gain,2)); ?></td>
                        <th colspan="3">EEF</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($smr_eef,2)); ?></td>
                        <th colspan="2">Bio Security Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo number_format_ind(round(round(((float)$tcbs_amt - (float)$trbs_amt),2),2)); ?></td>
                        <th colspan="2">Totral Production Cost</th><td colspan="2" class="thead4" <?php if($smr_total_cost_amt > $smr_standard_prod_cost){ echo 'style="text-align:right;color:red;font-weight:bold;"'; } else{ echo 'style="text-align:right;color:green;font-weight:bold;"'; } ?>><?php echo number_format_ind(round($smr_total_cost_amt,2)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Grade</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $smr_grade; ?></td>
                        <th colspan="3">FCR</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo round($smr_fcr,3); ?></td>
                        <th colspan="3">CFCR</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo round($smr_cfcr,3); ?></td>
                        <th colspan="2"></th><td colspan="2" class="thead4" style="text-align:right;"></td>
                        <th colspan="2">Production Cost Per Kg</th><td colspan="2" class="thead4" <?php if($smr_total_cost_amt > $smr_standard_prod_cost){ echo 'style="text-align:right;color:red;font-weight:bold;"'; } else{ echo 'style="text-align:right;color:green;font-weight:bold;"'; } ?>><?php echo number_format_ind(round($smr_total_cost_unit,2)); ?></td>
                    </tr>
                    <?php
                    //}
                    /*?>
                    <tr align="left">
                        <th colspan="3">Chicks Placement</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($trc_qty)); ?></td>
                        <th colspan="3">Chick Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $trc_amt; ?></td>
                        <th colspan="3">Birds Sold</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($ts_birds)); ?></td>
                        <th colspan="4">Average Weight</th><td colspan="4" class="thead4" style="text-align:right;"><?php echo number_format_ind(round($avgwt,3)); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Free Chicks</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($row['shortage'])); ?></td>
                        <th colspan="3">Feed Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $row['feed_cost_amt']; ?></td>
                        <th colspan="3">Sold Weight</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $ts_weight; ?></td>
                        <th colspan="4">Production Cost Per Kg</th><td colspan="4" class="thead4" style="text-align:right;"><?php echo $row['actual_prod_cost']; ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Total Chicks Placement</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($trc_qty)); ?></td>
                        <th colspan="3">Medicine/Vacc Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $tcmv_amt; ?></td>
                        <th colspan="3">Average Sale Rate</th><td colspan="2" class="thead4" style="text-align:right;">
                        <?php
                        if($ts_weight > 0){
                            echo round(($ts_amt / $ts_weight),2);
                        }
                        else{
                            echo "0.00";
                        }
                            
                        ?>
                        </td>
                        <th colspan="4">Mean Age</th><td colspan="4" class="thead4" style="text-align:right;"><?php echo $row['mean_age']; ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Feed Sent</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $tsfin_qty + $tpfin_qty; ?></td>
                        <th colspan="3">Admint Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $row['admin_cost_amt']; ?></td>
                        <th colspan="3">Sales Amount</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $ts_amt; ?></td>
                        <th colspan="4">FCR</th><td colspan="4" class="thead4" style="text-align:right;"><?php if((float)$sale_wt != 0){ $t1 = $cum_feed / $sale_wt; } else{ $t1 = 0; } echo round($t1,3); ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Feed Consumed</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $cum_feed; ?></td>
                        <th colspan="3">Vaccinator Charges</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $row['vaccinator_charges']; ?></td>
                        <th colspan="3"></th><td colspan="2" class="thead4" style="text-align:right;"></td>
                        <th colspan="4">CFCR</th><td colspan="4" class="thead4" style="text-align:right;"><?php echo $row['cfcr']; ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Feed Return</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $tsfout_qty; ?></td>
                        <th colspan="3">Equipment Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $row['other_deduction']; ?></td>
                        <th colspan="3"></th><td colspan="2" class="thead4" style="text-align:right;"></td>
                        <th colspan="4">EEF</th><td colspan="4" class="thead4" style="text-align:right;"><?php echo $row['eef']; ?></td>
                    </tr>
                    <tr align="left">
                        <th colspan="3">Med or Vaccine Consumed</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $tcmv_qty; ?></td>
                        <th colspan="3">Totral Production Cost</th><td colspan="2" class="thead4" style="text-align:right;"><?php echo $row['amount_payable']; ?></td>
                        <th colspan="3"></th><td colspan="2" class="thead4" style="text-align:right;"></td>
                        <th colspan="4">Grade</th><td colspan="4" class="thead4" style="text-align:right;"><?php echo $row['grade']; ?></td>
                    </tr>
                    */?>
                </thead>
            <?php
            }
            }
            ?>
        </table>
        <script>
            function checkval(){
                var farms = document.getElementById("farms").value;
                var batches = document.getElementById("batches").value;
                if(farms.match("select")){
                    alert("Kindly select Farm Description");
                    document.getElementById("farms").focus();
                    return false;
                }
                else if(batches.match("select")){
                    alert("Kindly select Farm Batch Description");
                    document.getElementById("batches").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
            function fetch_farm_batch(a){
                var fcode = document.getElementById(a).value;
                removeAllOptions(document.getElementById("batches"));
                removeAllOptions(document.getElementById("book_nos"));
                if(a.match("farmccodes")){
                    $('#farms').select2();
                    document.getElementById("farms").value = fcode;
                    $('#farms').select2();
                }
                else{
                    $('#farmccodes').select2();
                    document.getElementById("farmccodes").value = fcode;
                    $('#farmccodes').select2();
                }
                if(fcode == "all"){
                    myselect = document.getElementById("batches"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-Select-"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                        foreach($batch_code as $batch_no){
                            $farm_codes = $farm_batch[$batch_no];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $batch_name[$batch_no]; ?>"); theOption1.value = "<?php echo $batch_no; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                    myselect2 = document.getElementById("book_nos"); theOption2=document.createElement("OPTION"); theText2=document.createTextNode("-Select-"); theOption2.value = "select"; theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                    <?php
                        foreach($batch_code as $batch_no){
                            $farm_codes = $farm_batch[$batch_no];
                    ?> 
                        theOption2=document.createElement("OPTION"); theText2=document.createTextNode("<?php echo $batch_book[$batch_no]; ?>"); theOption2.value = "<?php echo $batch_no; ?>"; theOption2.appendChild(theText2); myselect2.appendChild(theOption2);	
                    <?php
                        }
                    ?>
                }
                else{
                    myselect = document.getElementById("batches"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-Select-"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                        foreach($batch_code as $batch_no){
                            $farm_codes = $farm_batch[$batch_no];
                            echo "if(fcode == '$farm_codes'){";
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $batch_name[$batch_no]; ?>"); theOption1.value = "<?php echo $batch_no; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                            echo "}";
                        }
                    ?>
                    myselect2 = document.getElementById("book_nos"); theOption2=document.createElement("OPTION"); theText2=document.createTextNode("-Select-"); theOption2.value = "select"; theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                    <?php
                        foreach($batch_code as $batch_no){
                            $farm_codes = $farm_batch[$batch_no];
                            echo "if(fcode == '$farm_codes'){";
                    ?> 
                        theOption2=document.createElement("OPTION"); theText2=document.createTextNode("<?php echo $batch_book[$batch_no]; ?>"); theOption2.value = "<?php echo $batch_no; ?>"; theOption2.appendChild(theText2); myselect2.appendChild(theOption2);	
                    <?php
                            echo "}";
                        }
                    ?>
                }
            }
            function select_batchbook_list(x){
                var code = document.getElementById(x).value;
                if(x.match("batches")){
                    $('#book_nos').select2();
                    document.getElementById("book_nos").value = code;
                    $('#book_nos').select2();
                }
                else if(x.match("book_nos")){
                    $('#batches').select2();
                    document.getElementById("batches").value = code;
                    $('#batches').select2();
                }
                else{

                }
            }
            function broiler_check_prices(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var trans_type = b[0];
                var fetch_type = '<?php echo $fetch_type; ?>';
                var val2 = d.split("@");
                var id = val2[0]; var date = val2[1]; var sector = val2[2]; var item = val2[3]; var trnum = val2[4]; var quantity = val2[5];
                var fetchgc = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemprice_modify.php?id="+id+"&date="+date+"&sector="+sector+"&item="+item+"&trnum="+trnum+"&quantity="+quantity+"&fetch_type="+fetch_type+"&trans_type="+trans_type;
                //window.open(url);
				var asynchronous = true;
				fetchgc.open(method, url, asynchronous);
				fetchgc.send();
				fetchgc.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
                        var err = this.responseText;
                        if(err == 1 || err == "1"){ }
                        else{ document.getElementById(a).style.visibility = "hidden;"; }
                    }
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        
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