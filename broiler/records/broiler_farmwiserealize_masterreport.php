<?php
//broiler_farmwiserealize_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
$db = '';
if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; }
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
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}
include "decimal_adjustments.php";
$file_name = "Farm Wise Realization";

/* admin cost include flag check*/
$sql3 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Farmwise Realization' AND `field_function` LIKE 'Include Admin Cost'  AND (`user_access` LIKE '%$addedemp%' OR `user_access` = 'all')";
$query3 = mysqli_query($conn, $sql3); $ccount3 = mysqli_num_rows($query3);
if($ccount3 > 0){ while($row3 = mysqli_fetch_assoc($query3)){ $admincost_include_flag = $row3['flag']; } }
else{ mysqli_query($conn, "INSERT INTO `extra_access` ( `field_name`, `field_function`, `user_access`, `flag`) VALUES ( 'Farmwise Realization', 'Include Admin Cost', 'all', '1')"); $admincost_include_flag =  1; }
if($admincost_include_flag == ''){ $admincost_include_flag =  0; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'broiler_farmwiserealize_masterreport.php' AND `field_function` LIKE 'FarmerPay is zero add totalpayable'"; $query = mysqli_query($conn,$sql);
$parasflag = mysqli_num_rows($query);

/*admin cost include flag check*/
$sql='SHOW COLUMNS FROM `broiler_rearingcharge`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = $bsup_flag = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; if($row['Field'] == "high_chickin_secvcode"){ $bsup_flag = 1; } }
if(in_array("days7_mort_count", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `days7_mort_count` INT(100) NULL DEFAULT '0' COMMENT '' AFTER `age`"; mysqli_query($conn,$sql); }
if(in_array("days30_mort_count", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `days30_mort_count` INT(100) NULL DEFAULT '0' COMMENT '' AFTER `days7_mort`"; mysqli_query($conn,$sql); }
if(in_array("days31_mort_count", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `days31_mort_count` INT(100) NULL DEFAULT '0' COMMENT '' AFTER `days30_mort`"; mysqli_query($conn,$sql); }
if(in_array("week_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `week_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `start_date`"; mysqli_query($conn,$sql); }
if(in_array("sale_start_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `sale_start_date` DATE NULL DEFAULT NULL COMMENT '' AFTER `week_no`"; mysqli_query($conn,$sql); }
if(in_array("sale_end_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `sale_end_date` DATE NULL DEFAULT NULL COMMENT '' AFTER `sale_start_date`"; mysqli_query($conn,$sql); }
           
$sql='SHOW COLUMNS FROM `broiler_payments`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_payments` ADD `farm_batch` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `warehouse`"; mysqli_query($conn,$sql); }
if(in_array("pay_type", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_payments` ADD `pay_type` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `docno`"; mysqli_query($conn,$sql); }

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]);
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = ""; $oldfcr_flag =  $last_lifting_date = $csn_flag = 0;
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] != 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
                $key_id."-".$act_col_numbs[$key_id];
                if($cna == "old_fcr" || $cna == "old_cfcr"){
                    $oldfcr_flag = 1;
                }
                if($cna == "last_lifting_date" || $cna == "last_lifting_date"){
                    $last_lifting_date = 1;
                }
                if($cna == "chick_received_from" || $cna == "chick_received_from"){
                    $csn_flag = 1;
                }
                //echo "<br/>".$cna;
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] != 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $region_code = $region_name = array();
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'  ".$branch_access_filter1."  AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }

$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_category = $row['category']; }

$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_aname[$row['code']] = $row['acc_holder_name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; $farmer_pan[$row['code']] = $row['panno']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_cat_coa = array();
while($row = mysqli_fetch_assoc($query)){ $item_cat_coa[$row['code']] = $row['iac']; }

$sql = "SELECT * FROM `extra_access` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql); 
$pan_flag = mysqli_num_rows($query);
// while($row = mysqli_fetch_assoc($query)){ $item_cat_coa[$row['code']] = $row['iac']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

//Check for brand table and fetch brand details
$tblchk_dbname = $_SESSION['dbase']; $tblchk_tblname = "Tables_in_".$tblchk_dbname;
$sqlt = "SHOW TABLES;"; $queryt = mysqli_query($conn,$sqlt); $brnd_flag = 0;
while($rowt = mysqli_fetch_array($queryt)){ if($rowt[$tblchk_tblname] == "broiler_item_brands"){ $brnd_flag = 1; } }

$sql='SHOW COLUMNS FROM `broiler_rearingcharge`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; if($row['Field'] == "high_chickin_secvcode"){ $brnd_flag = 1; } }

if($brnd_flag == 1){
    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `dflag` = '0' ORDER BY `id` ASC";
    $query = mysqli_query($conn,$sql); $chksup_code = $feedsup_code = array();
    while($row = mysqli_fetch_assoc($query)){ $chksup_code[$row['high_chickin_secvcode']] = $row['high_chickin_secvcode']; $feedsup_code[$row['high_feedin_brand_code']] = $row['high_feedin_brand_code']; }

    $feedsup_list = implode("','",$feedsup_code);
    $sql = "SELECT * FROM `broiler_item_brands` WHERE `code` IN ('$feedsup_list') AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $brand_code = $brand_name = array();
    while($row = mysqli_fetch_assoc($query)){ $brand_code[$row['code']] = $row['code']; $brand_name[$row['code']] = $row['description']; }

    //Chick-In filter Info
    $chiksup_list = implode("','",$chksup_code);
    $sql = "SELECT *  FROM `inv_sectors` WHERE `code` IN ('$chiksup_list') AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $chkin_code = $chkin_name = array();
    while($row = mysqli_fetch_assoc($query)){ $chkin_code[$row['code']] = $row['code']; $chkin_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT *  FROM `main_contactdetails` WHERE `code` IN ('$chiksup_list') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $chkin_code[$row['code']] = $row['code']; $chkin_name[$row['code']] = $row['name']; }
}


$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = $chick_in_from = $brands = "all";
$fcr_type = $cfcr_type = $mortp_type = $above_fcr = $above_cfcr = $above_mortp = "";
$excel_type = "display"; $report_view = "hd";
if(isset($_REQUEST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_REQUEST['branches'];
    $lines = $_REQUEST['lines'];
    $supervisors = $_REQUEST['supervisors'];
    $farms = $_REQUEST['farms'];
    $report_view = $_REQUEST['report_view'];
    $fcr_type = $_REQUEST['fcr_type'];
    $cfcr_type = $_REQUEST['cfcr_type'];
    $mortp_type = $_REQUEST['mortp_type'];
    $above_fcr = $_REQUEST['above_fcr'];
    $above_cfcr = $_REQUEST['above_cfcr'];
    $above_mortp = $_REQUEST['above_mortp'];

    $brands = $_REQUEST['brands']; if($brands == "all"){ $brand_filter = ""; } else{ $brand_filter = " AND `high_feedin_brand_code` = '$brands'"; }
    $chick_in_from = $_REQUEST['chick_in_from']; if($chick_in_from == "all"){ $chkin_filter = ""; } else{ $chkin_filter = " AND `high_chickin_secvcode` = '$chick_in_from'"; }
    if($brnd_flag == 0){ $brand_filter = ""; $chkin_filter = ""; }

    if($above_fcr != ""){ if($fcr_type == "above"){ $fcr_filter = " AND `fcr` >= '$above_fcr'"; } else{ $fcr_filter = " AND `fcr` <= '$above_fcr'"; } } else{ $fcr_filter = ""; }
    if($above_cfcr != ""){ if($cfcr_type == "above"){ $cfcr_filter = " AND `cfcr` >= '$above_cfcr'"; } else{ $cfcr_filter = " AND `cfcr` <= '$above_cfcr'"; } } else{ $cfcr_filter = ""; }
    if($above_mortp != ""){ if($mortp_type == "above"){ $mortp_filter = " AND `total_mort` >= '$above_mortp'"; } else{ $mortp_filter = " AND `total_mort` <= '$above_mortp'"; } } else{ $mortp_filter = ""; }
    
    $farm_query = "";
    if($regions != "all"){
        $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
        $rbrh_list = implode("','",$rbrh_alist);
        $farm_query .= " AND `branch_code` IN ('$rbrh_list')";
    }
    if($branches != "all"){ $farm_query .= " AND `branch_code` LIKE '$branches'"; }
    if($lines != "all"){ $farm_query .= " AND `line_code` LIKE '$lines'"; }
    if($supervisors != "all"){ $farm_query .= " AND `supervisor_code` LIKE '$supervisors'"; }
    if($farms != "all"){ $farm_query .= " AND `farm_code` LIKE '$farms'"; }

    $excel_type = $_REQUEST['export'];
    //$url = "../PHPExcel/Examples/broiler_farmwiserealize_masterreport-Excel.php?branches=".$branches."&report_view=".$report_view."&fdate=".$fdate."&tdate=".$tdate."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms."&brands=".$brands."&chick_in_from=".$chick_in_from."&href=".$field_href[0]."&above_fcr=".$above_fcr."&above_cfcr=".$above_cfcr;
}
else{
    $url = "";
}
$tblcol_size = sizeof($act_col_numbs);
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <!-- Datatable CSS 
        <link href='../../col/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>-->

        <!-- jQuery Library -->
        <script src="../../col/jquery-3.5.1.js"></script>
        
        <!-- Datatable JS -->
        <script src="../../col/jquery.dataTables.min.js"></script>
        <script>
            /*var exptype = '<?php //echo $excel_type; ?>';
            var url = '<?php //echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }*/
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
            table thead,
            table tfoot {
                position: sticky;
            }
            table thead {
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
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
        <table class="tbl" align="center" <?php if($excel_type == "print"){ echo ' id="mine"'; } else{ echo 'width="1300px"'; } ?>>
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="<?php echo $tblcol_size - 2; ?>" align="center"><?php echo $row['cdetails']; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_farmwiserealize_masterreport.php" method="post">
            <?php } else { ?>
            <form action="broiler_farmwiserealize_masterreport.php?db=<?php echo $db; ?>&userid=<?php echo $user_code; ?>" method="post">
            <?php } ?>
            <form action="broiler_farmwiserealize_masterreport.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="<?php echo $tblcol_size; ?>">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Report View</label>
                                    <select name="report_view" id="report_view" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="hd" <?php if($report_view == "hd"){ echo "selected"; } ?>>Housed Date</option>
                                        <option value="ld" <?php if($report_view == "ld"){ echo "selected"; } ?>>Liquidation Date</option>
                                        <option value="gd" <?php if($report_view == "gd"){ echo "selected"; } ?>>GC Saved Date</option>
                                    </select>
                                </div>
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
                                    <select name="regions" id="regions" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($regions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
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
                                <?php if($brnd_flag == 1){ ?>
                                <div class="m-2 form-group">
                                    <label>Chick Supplier</label>
                                    <select name="chick_in_from" id="chick_in_from" class="form-control select2">
                                        <option value="all" <?php if($chick_in_from == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($chkin_code as $fcode){ if($chkin_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($chick_in_from == $fcode){ echo "selected"; } ?>><?php echo $chkin_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Supplier</label>
                                    <select name="brands" id="brands" class="form-control select2">
                                        <option value="all" <?php if($brands == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($brand_code as $fcode){ if($brand_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($brands == $fcode){ echo "selected"; } ?>><?php echo $brand_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <?php } ?>
                                <div class="m-2 form-group">
                                    <label>FCR Type</label>
                                    <select name="fcr_type" id="fcr_type" class="form-control select2">
                                        <option value="above" <?php if($fcr_type == "above"){ echo "selected"; } ?>>Above</option>
                                        <option value="below" <?php if($fcr_type == "below"){ echo "selected"; } ?>>Below</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>FCR</label>
                                    <input type="text" name="above_fcr" id="above_fcr" class="form-control" style="width:70px;" value="<?php echo $above_fcr; ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>CFCR Type</label>
                                    <select name="cfcr_type" id="cfcr_type" class="form-control select2">
                                        <option value="above" <?php if($cfcr_type == "above"){ echo "selected"; } ?>>Above</option>
                                        <option value="below" <?php if($cfcr_type == "below"){ echo "selected"; } ?>>Below</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>CFCR</label>
                                    <input type="text" name="above_cfcr" id="above_cfcr" class="form-control" style="width:70px;" value="<?php echo $above_cfcr; ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Mort% Type</label>
                                    <select name="mortp_type" id="mortp_type" class="form-control select2">
                                        <option value="above" <?php if($mortp_type == "above"){ echo "selected"; } ?>>Above</option>
                                        <option value="below" <?php if($mortp_type == "below"){ echo "selected"; } ?>>Below</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Mort%</label>
                                    <input type="text" name="above_mortp" id="above_mortp" class="form-control" style="width:70px;" value="<?php echo $above_mortp; ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
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
            <?php if($excel_type == "print"){ } else{ ?>
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td>
                <div id='control_sh'>
                    <?php
                        //for($i = 1;$i <= $col_count;$i++){ $key_id = "A:1:".$i; $key_id1 = "A:0:".$i; if(!empty($act_col_numbs[$key_id])){ echo "<br/>".$act_col_numbs[$key_id]."@".$key_id; } else if(!empty($nac_col_numbs[$key_id1])){ echo "<br/>".$nac_col_numbs[$key_id1]."@".$key_id1; } else{ } }
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GC Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "branch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "line_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Line</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_bank_aname" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_bank_aname"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_bank_aname" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Account Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_panno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_panno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_panno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Pan No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "batch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "book_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Book No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "placement_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="placement_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Placement Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "liquidation_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="liquidation_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Liquidation Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "brood_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mean_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mean_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mean Age</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_placed"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_placed" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Chicks Placement</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mortality</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mortality%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_1week_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_1week_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>1st week Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_1week_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_1week_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>1st week Mort%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_30days_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_30days_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Up to 30days Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_30days_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_30days_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Up to 30days Mort%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_30more_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_30more_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>After 30days Mort</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mortality_30more_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mortality_30more_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>After 30days Mort%</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_shortage_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "bird_shortage_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bird_shortage_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Birds Shortage</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_excess_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "bird_excess_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bird_excess_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Birds Excess</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feedconsumed_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feedconsumed_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Consumed Kgs</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_feed_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std. Feed/Bird</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_feed_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed/Bird Kgs</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std.FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "cfcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cfcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>CFCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_gain" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "day_gain"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="day_gain" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Day Gain</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "eef"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="eef" onclick="update_masterreport_status(this.id);" '.$checked.'><span>EEF</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_birdswt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdswt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sold Weight</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "avg_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="avg_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg. Body Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_perkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_chick_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_chick_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Chick Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_chick_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_chick_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Chick Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_feed_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Feed Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_feed_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_feed_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Feed Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_medicine_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_medicine_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Medicine Price/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_medicine_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_medicine_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Medicine Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_admin_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_admin_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Admin Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_admin_swtprc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_admin_swtprc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Admin Price/Kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_admin_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_admin_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Admin Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_production_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_production_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer P.cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_prodperkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_prodperkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>F PC/Kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_gc_perkg"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_gc_perkg" onclick="update_masterreport_status(this.id);" '.$checked.'><span>STD.Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_perkg" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "act_gc_perkg"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="act_gc_perkg" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act.Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_incentive"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_incentive" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total Incentives</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_decentives"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_decentives" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Decentives</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_gc_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_gc_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_gc_perkg_price2"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_gc_perkg_price2" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "rearing_charges"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="rearing_charges" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Basic Rearing Charges</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_rearing_charges" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "total_rearing_charges"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="total_rearing_charges" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total Rearing Charges</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_tds_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_tds_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_tds_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TDS</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "other_deduction" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "other_deduction"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="other_deduction" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Other Deductitons</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_payable" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_payable"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_payable" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Payable</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_chick_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_chick_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act Chick Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_chick_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_chick_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act Chick Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_feed_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act Feed Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_feed_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_feed_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Act Feed Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_medicine_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_medicine_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Medicine Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_medicine_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_medicine_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Medicine Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_admin_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_admin_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Admin Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_admin_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_admin_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Admin Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "final_farmer_payable" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "final_farmer_payable"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="final_farmer_payable" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GC Paid to Farmer</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_prod_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "actual_prod_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="actual_prod_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Actual P.Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "mgmt_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mgmt_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>M PC/Kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_sale_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "total_sale_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="total_sale_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total Sale Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "profit_and_loss" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "profit_and_loss"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="profit_and_loss" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Profit/Loss</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_fcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "old_fcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="old_fcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Old FCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_cfcr" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "old_cfcr"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="old_cfcr" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Old CFCR</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "week1_bodywt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "week1_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="week1_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>1st week B.Wt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_grading" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_grading"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_grading" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Grading</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "last_lifting_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "last_lifting_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="last_lifting_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Last Lifting Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_brand_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "feed_brand_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="feed_brand_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Supplier</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chick_received_from"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chick_received_from" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Chick Supplier</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_paid" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_actpay_paid"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_actpay_paid" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Adjustment Pay</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_diff" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_actpay_diff"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_actpay_diff" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer Payment Difference</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farm_ccode"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_ccode" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm Code</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "paid_to_farmer" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "paid_to_farmer"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="paid_to_farmer" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Paid to Farmer</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pl_on_fmrpay" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "pl_on_fmrpay"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="pl_on_fmrpay" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Profit and Loss</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chickin_hatchery_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chickin_hatchery_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Hatchery</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "chickin_supplier_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="chickin_supplier_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supplier</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "billno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "billno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="billno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>T. Supplier</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "lifting_efficiency"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="lifting_efficiency" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lifting Efficiency</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_week_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_week_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_week_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Week No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_sdate" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_sale_sdate"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_sale_sdate" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lifting Start Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_edate" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_sale_edate"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_sale_edate" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lifting End Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_prc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "act_gc_prc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="act_gc_prc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Acutal GC Rate</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "act_gc_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="act_gc_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Acutal GC Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_sale_inc_prc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_sale_inc_prc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Incentives/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_sale_inc_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_sale_inc_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Incentives</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "approved_gc_prc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="approved_gc_prc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>PC Incentive Rate</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "approved_gc_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="approved_gc_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>PC Incentive Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_inc_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_inc_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_inc_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Incentive Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_dec_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_dec_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_dec_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Decentive Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "high_feedin_gc_sup" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "high_feedin_gc_sup"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="high_feedin_gc_sup" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Feed Supplier Name</span>'; }

                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <input type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
        <table id="main_table" class="tbl" align="center"  style="width:1300px;">
        <?php } ?>
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                <?php
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th id='order_num'>Sl.No</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_date"){ echo "<th id='order_date'>GC Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<th id='order'>Branch</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<th id='order'>Line</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th id='order'>Supervisor</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_bank_aname"){ echo "<th id='order'>Farmer Account Name</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_panno"){ echo "<th id='order'>Farmer Pan No.</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<th id='order'>Farm</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<th id='order'>Batch</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<th id='order'>Book No</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date"){ echo "<th id='order_date'>Placement Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date"){ echo "<th id='order_date'>Liquidation Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<th id='order_num'>Age</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<th id='order_num'>Mean Age</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th id='order_num'>Chicks Placement</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th id='order_num'>Mortality</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<th id='order_num'>Mortality%</th>"; }

                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count"){ echo '<th>1st week Mort</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per"){ echo '<th>1st week Mort%</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count"){ echo '<th>Up to 30days Mort</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per"){ echo '<th>Up to 30days Mort%</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count"){ echo '<th>After 30days Mort</th>'; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per"){ echo '<th>After 30days Mort%</th>'; }

                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_shortage_count"){ echo "<th id='order_num'>Birds Shortage</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_excess_count"){ echo "<th id='order_num'>Birds Excess</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th id='order_num'>Feed Consumed Kgs</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<th id='order_num'>Std. Feed/Bird</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_birdsno"){ echo "<th id='order_num'>Feed/Bird Kgs</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<th id='order_num'>Std.FCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<th id='order_num'>FCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<th id='order_num'>CFCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_gain"){ echo "<th id='order_num'>Day Gain</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<th id='order_num'>EEF</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th id='order_num'>Sold Birds</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th id='order_num'>Sold Weight</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th id='order_num'>Avg. Body Wt</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<th id='order_num'>Sale Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_amount"){ echo "<th id='order_num'>Sale Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price"){ echo "<th id='order'>Std Chick Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_amount"){ echo "<th id='order_num'>Std Chick Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_price"){ echo "<th id='order_num'>Std Feed Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_amount"){ echo "<th id='order_num'>Std Feed Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price"){ echo "<th id='order_num'>Std Medicine Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount"){ echo "<th id='order_num'>Std Medicine Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price"){ echo "<th id='order_num'>Std Admin Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc"){ echo "<th id='order_num'>Std Admin Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount"){ echo "<th id='order_num'>Std Admin Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount"){ echo "<th id='order_num'>Farmer P.cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price"){ echo "<th id='order_num'>F PC/Kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg"){ echo "<th id='order_num'>STD.Gc/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_perkg"){ echo "<th id='order_num'>Act.Gc/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive"){ echo "<th id='order_num'>Total Incentives</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives"){ echo "<th id='order_num'>Decentives</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price"){ echo "<th id='order_num'>Gc/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2"){ echo "<th id='order_num'>Gc/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges"){ echo "<th id='order_num'>Basic Rearing Charges</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_rearing_charges"){ echo "<th id='order_num'>Total Rearing Charges</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_tds_amount"){ echo "<th id='order_num'>TDS</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "other_deduction"){ echo "<th id='order_num'>Other Deductitons</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_payable"){ echo "<th id='order_num'>Farmer Payable</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_price"){ echo "<th id='order_num'>Act Chick Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_amount"){ echo "<th id='order_num'>Act Chick Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_price"){ echo "<th id='order_num'>Act Feed Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_amount"){ echo "<th id='order_num'>Act Feed Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_price"){ echo "<th id='order_num'>Medicine Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_amount"){ echo "<th id='order_num'>Medicine Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_price"){ echo "<th id='order_num'>Admin Price</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_amount"){ echo "<th id='order_num'>Admin Cost</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "final_farmer_payable"){ echo "<th id='order_num'>GC Paid to Farmer</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_prod_amount"){ echo "<th id='order_num'>Actual P.Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){ echo "<th id='order_num'>M PC/Kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_sale_amount"){ echo "<th id='order_num'>Total Sale Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "profit_and_loss"){ echo "<th id='order_num'>Profit/Loss</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_fcr"){ echo "<th id='order_num'>Old FCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_cfcr"){ echo "<th id='order_num'>Old CFCR</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "week1_bodywt"){ echo "<th id='order_num'>1st week B.Wt</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_grading"){ echo "<th id='order_num'>Grading</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "last_lifting_date"){ echo "<th id='order_date'>Last Lifting Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_brand_name"){ echo "<th id='order'>Feed Supplier</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ echo "<th id='order'>Chick Supplier</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_paid"){ echo "<th id='order_num'>Farmer Adjustment Pay</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_diff"){ echo "<th id='order_num'>Farmer Payment Difference</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<th id='order'>Farm Code</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "paid_to_farmer"){ echo "<th id='order'>Paid to Farmer</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pl_on_fmrpay"){ echo "<th id='order'>Profit and Loss</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<th id='order'>Hatchery</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<th id='order'>Supplier</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "billno"){ echo "<th id='order'>T. Supplier</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency"){ echo "<th id='order_num'>Lifting Efficiency</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_week_no"){ echo "<th id='order'>Week No.</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_sdate"){ echo "<th id='order_date'>Lifting Start Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_edate"){ echo "<th id='order_date'>Lifting End Date</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_prc"){ echo "<th id='order_num'>Actual GC Rate</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_amt"){ echo "<th id='order_num'>Actual GC Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc"){ echo "<th id='order_num'>Sale Incentives/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_amt"){ echo "<th id='order_num'>Sale Incentives</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc"){ echo "<th id='order_num'>PC Incentive Rate</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_amt"){ echo "<th id='order_num'>PC Incentive Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_inc_amt"){ echo "<th id='order_num'>Incentive Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_dec_amt"){ echo "<th id='order_num'>Decentive Amount</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "high_feedin_gc_sup"){ echo "<th id='order'>Feed Supplier Name</th>"; }
                    else{ }
                }
                ?>
                </tr>
            </thead>
            
            <?php
            if(isset($_REQUEST['submit_report']) == true){
            ?>
            <tbody class="tbody1" id="tbody1">
                <?php
                $bag_size = 50; $mtsb = 0;
                if($_REQUEST['report_view'] == "hd"){
                    $date_filter = " AND `start_date` >= '$fdate' AND `start_date` <= '$tdate'";
                }
                else if($_REQUEST['report_view'] == "ld"){
                    $date_filter = " AND `liquid_date` >= '$fdate' AND `liquid_date` <= '$tdate'";
                }
                else if($_REQUEST['report_view'] == "gd"){
                    $date_filter = " AND `date` >= '$fdate' AND `date` <= '$tdate'";
                }
                else{}

                $old_fcr = $old_cfcr = array();
                if($oldfcr_flag == 1){
                    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `active` = '1' AND `dflag` = '0' $date_filter $farm_query ORDER BY `id` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $pbatch = $row['batch_code'];
                        $pdate = $row['date'];
                        $pfcode = $row['farm_code'];
                        $sql1 = "SELECT * FROM `broiler_rearingcharge` WHERE `date` <= '$pdate' AND `farm_code` = '$pfcode' AND `active` = '1' AND `dflag` = '0' ORDER BY `start_date` DESC";
                        $query1 = mysqli_query($conn,$sql1); $pbi = 0;
                        while($row1 = mysqli_fetch_assoc($query1)){ $pbi++; if($pbi == 2){ $old_fcr[$pbatch] = $row1['fcr']; $old_cfcr[$pbatch] = $row1['cfcr']; } }
                    }
                }

                $batch_arr_list = array();
                $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `active` = '1' AND `dflag` = '0' $date_filter $farm_query $fcr_filter $cfcr_filter $mortp_filter $brand_filter $chkin_filter ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $batch_arr_list[$row['batch_code']] = $row['batch_code']; }
                $blist = implode("','",$batch_arr_list);
                
                //Fetch Hatchery and Supplier Details-1
                /*Check for Table Availability*/
                $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
                $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
                if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }
                
                $batch_list = implode("','",$batch_arr_list); $chick_coa = $item_cat_coa[$chick_category]; 
                $sql = "SELECT MIN(`date`) as `sdate`,MAX(`date`) as `edate` FROM `account_summary` WHERE `crdr` LIKE 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $hsdate = $hedate = "";
                while($row = mysqli_fetch_assoc($query)){ $hsdate = $row['sdate']; $hedate = $row['edate']; }

                $hatch_count = $pur_count = 0; $chkin_vcode = $chkin_hcode = $pur_vcode = $pur_keyset = $hatch_vcode = $hatch_keyset = $sector_code = $sector_name = array();
                if($hsdate == "" && $hedate == ""){ }
                else{
                    $sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['name']; }
                    
                    $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

                    $hfdate = date("Y-m-d",strtotime($hsdate. '-3 days'));
                    $sector_list = implode("','",$sector_code);
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$hfdate' AND `date` <= '$hedate' AND `icode` = '$chick_code' AND `warehouse` IN ('$sector_list') AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['date']."@".$row['warehouse']."@".$i;
                        $pur_vcode[$key_code] = $row['vcode'];
                        $pur_keyset[$key_code] = $key_code;
                        $i++;
                    } $pur_count = sizeof($pur_vcode);

                    $sql = "SELECT * FROM `broiler_hatchentry` WHERE `hatch_date` >= '$hfdate' AND `hatch_date` <= '$hedate' AND `active` = '1' AND `dflag` = '0' ORDER BY `hatch_date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $i = 0;
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['hatch_date']."@".$row['sector_code']."@".$i;
                        $hatch_vcode[$key_code] = $row['vcode'];
                        $hatch_keyset[$key_code] = $key_code;
                        $i++;
                    } $hatch_count = sizeof($hatch_vcode);
                }

                //Fetch Hatchery and Supplier Details-2
                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){ $chkin_vcode[$row['farm_batch']] = $row['vcode']; }

                $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $i = 1; $chkin_dcno = array();
                while($row = mysqli_fetch_assoc($query)){
                    $chkin_hcode[$row['to_batch']] = $row['fromwarehouse'];
                    $chkin_dcno[$row['to_batch']] = $row['dcno'];
                    if(empty($chkin_vcode[$row['to_batch']]) || $chkin_vcode[$row['to_batch']] == ""){
                        if($hatch_count > 0 && $row['code'] == $chick_code){
                            $ldate = $lsector = $lincr = "";
                            foreach($hatch_keyset as $key1){
                                $key2 = explode("@",$key1); $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                    if($ldate == ""){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                    else if(strtotime($ldate) < strtotime($hdate)){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                }
                            }
                            if($ldate == "" && $lsector == "" && $lincr == ""){ }
                            else{
                                $hkey = $ldate."@".$lsector."@".$lincr;
                                if(empty($hatch_vcode[$hkey]) || $hatch_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                else{ $chkin_vcode[$row['to_batch']] = $hatch_vcode[$hkey]; }
                            }
                        }
                    }

                    if(empty($chkin_vcode[$row['to_batch']]) || $chkin_vcode[$row['to_batch']] == ""){
                        if($pur_count > 0 && $row['code'] == $chick_code){
                            $ldate = $lsector = $lincr = "";
                            foreach($pur_keyset as $key1){
                                $key2 = explode("@",$key1); $hdate = $key2[0]; $hsector = $key2[1]; $hicr = $key2[2];
                                if($hsector == $row['fromwarehouse'] && strtotime($hdate) <= strtotime($row['date'])){
                                    if($ldate == ""){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                    else if(strtotime($ldate) < strtotime($hdate)){ $ldate = $hdate; $lsector = $hsector; $lincr = $hicr; }
                                }
                            }
                            if($ldate == "" && $lsector == "" && $lincr == ""){ }
                            else{
                                $hkey = $ldate."@".$lsector."@".$lincr;
                                if(empty($pur_vcode[$hkey]) || $pur_vcode[$hkey] == ""){ $chkin_vcode[$row['to_batch']] = ""; }
                                else{ $chkin_vcode[$row['to_batch']] = $pur_vcode[$hkey]; }
                            }
                        }
                    }
                    
                }

                $chick_supplier_name = $ven_name = array();
                $sql = "SELECT *  FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $ven_name[$row['code']] = $row['name']; }
                if((int)$bsup_flag == 0 && (int)$csn_flag == 1){

                    $chick_coa = $item_cat_coa[$chick_category]; 
                    $sql = "SELECT * FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$blist') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $chick_supplier_name[$row['batch']] = $ven_name[$row['vendor']]; }
                }

                $sql = "SELECT * FROM `broiler_daily_record` WHERE `active` = '1' AND `batch_code` IN ('$blist') AND `dflag` = '0' ORDER BY `batch_code` ASC";
                $query = mysqli_query($conn,$sql); $days7_mcount = $days30_mcount = $days31_mcount = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['batch_code'];
                    if(empty($days7_mcount[$key]) || $days7_mcount[$key] == ""){ $days7_mcount[$key] = 0; }
                    if(empty($days30_mcount[$key]) || $days30_mcount[$key] == ""){ $days30_mcount[$key] = 0; }
                    if(empty($days31_mcount[$key]) || $days31_mcount[$key] == ""){ $days31_mcount[$key] = 0; }

                    if($row['brood_age'] <= 7){ $days7_mcount[$key] += ((float)$row['mortality'] + (float)$row['culls']); }
                    if($row['brood_age'] <= 30){ $days30_mcount[$key] += ((float)$row['mortality'] + (float)$row['culls']); }
                    if($row['brood_age'] >= 31){ $days31_mcount[$key] += ((float)$row['mortality'] + (float)$row['culls']); }
                    
                }

                $last_sale_date = array();
                if($last_lifting_date == 1){
                    $sql = "SELECT MAX(date) as sm_date FROM `broiler_sales` WHERE `active` = '1' AND `farm_batch` IN ('$blist') AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `id` ASC";
                    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $last_sale_date[$row['batch_code']] = date("d.m.Y",strtotime($row['sm_date'])); }
                }

                $sql = "SELECT SUM(amount) as amount,farm_batch FROM `broiler_payments` WHERE `active` = '1' AND `pay_type` = 'gc_pay' AND `farm_batch` IN ('$blist') AND `vtype` = 'FarmerPay' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $farmer_actpay_paid[$row['farm_batch']] = round($row['amount'],2); }


                $total_gc_per_kg = $total_fpc_per_kg = $total_pcincentive_per_kg = $total_salesincentive_per_kg =  $total_incentive_per_kg = $total_decentive_per_kg =  $total_efficiency_per_kg =  0;
                
                $tot_placed_birds = $tot_mortality = $days7_mort_count = $days30_mort_count = $days31_mort_count = $ftot_sale_amount = $tot_shortage = 
                $tot_excess = $tot_feed_consume_kgs = $tot_sold_birds = $tot_chick_cost_amt = $tot_feed_cost_amt = $tot_medicine_cost_amt = 
                $tot_admin_cost_amt1 = $tot_fpay = $tot_amount_payable = $tot_tds_amt = $tot_actual_chick_cost = $tot_actual_feed_cost = 
                $tot_actual_medicine_cost = $tot_admin_cost_amt2 = $tot_farmer_payable = $tot_total_prod = $tot_actual_prod_cost = $tot_sale_amount = 
                $tot_sold_weight = $other_deduction = $tot_fmrpayable_amt = $tot_fmrpaid_amt = $tot_pl_amt = $tot_rmt = $tot_rmt2 = $tot_actpc_amt = $tot_saleinc_amt = $approved_gc_amt = $gc_inc_amt = $gc_dec_amt = 
                $tfp_bird = $tfcr_val = $tcfcr_val = $tdgain_val = $teef_val = $tstd_mprc = $tstd_aprc = $tot_std_gcprc = $tot_act_gcprc = $incr = 0;
                $high_feedin_gc_sup = "";
                $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `active` = '1' AND `dflag` = '0' $date_filter $farm_query $fcr_filter $cfcr_filter $mortp_filter $brand_filter $chkin_filter ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql); $slno = 0;
                while($row = mysqli_fetch_assoc($query)){ $slno++;
                    $trnum = ""; $trnum = $row['trnum'];
                    $chick_placed = 0; $chick_placed = (float)$row['placed_birds'];
                    $tot_placed_birds += (float)$row['placed_birds'];
                    $tot_mortality += (float)$row['mortality'];
                    $mortality_count = 0; $mortality_count = (float)$row['mortality'];
                    $tot_shortage += (float)$row['shortage'];
                    $bird_shortage_count = 0; $bird_shortage_count = (float)$row['shortage'];
                    $tot_excess += (float)$row['excess'];
                    $bird_excess_count = 0; $bird_excess_count = (float)$row['excess'];
                    $tot_feed_consume_kgs += (float)$row['feed_consume_kgs'];
                    $feedconsumed_count = 0; $feedconsumed_count = (float)$row['feed_consume_kgs'];

                    $closed_birds = ((float)$row['placed_birds']) - ((float)$row['mortality'] + (float)$row['sold_birds']);
                    $incentive =0;$decentive =0;
                    if($row['grow_charge_exp_prc'] > 0){ $incentive += (float)$row['grow_charge_exp_prc']; } else{ $decentive += (float)$row['grow_charge_exp_prc']; }
                    if($row['sales_incentive_prc'] > 0){ $incentive += (float)$row['sales_incentive_prc']; } else{ $decentive += (float)$row['sales_incentive_prc']; }

                    if((float)$row['total_amount_payable'] > 0){
                        $total_amount_payable = (float)$row['total_amount_payable'];
                    }else{
                        $total_amount_payable = 0;
                    }

                    // if($admincost_include_flag == 1){
                    //     $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + (float)$total_amount_payable; //(float)$row['farmer_payable'];
                    // }
                    // else{
                    //     $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost']  + (float)$total_amount_payable; //(float)$row['farmer_payable'];
                    // }

                    if($parasflag > 0){

                        if($admincost_include_flag == 1){
                            if((float)$farmer_actpay_paid[$row['batch_code']] > 0){
                                $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + (float)$farmer_actpay_paid[$row['batch_code']];
                            }else{
                                $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + (float)$total_amount_payable;
                            }
                           
                        }
                        else{
                            if((float)$farmer_actpay_paid[$row['batch_code']] > 0){
                                $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost']  + (float)$farmer_actpay_paid[$row['batch_code']];
                            }else{
                                $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost']  + (float)$total_amount_payable;
                            }
                          //  $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost']  + (float)$farmer_actpay_paid[$row['batch_code']]; //(float)$row['farmer_payable'];
                        }
                    }else{
                        if($admincost_include_flag == 1){
                            $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + (float)$total_amount_payable; //(float)$row['farmer_payable'];
                        }
                        else{
                            $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost']  + (float)$total_amount_payable; //(float)$row['farmer_payable'];
                        }
                    }
                    $profit_loss = (float)$row['sale_amount']- (float)$total_prod;
                    /* Total Mean Age Calculations */
                    $total_meanage += (float)$row['mean_age'];
                    $mage = $row['age']; $msbirds = $row['sold_birds']; $mtsb = $mtsb + ($mage * $msbirds);

                    $tot_rmt += ((float)$row['mean_age'] * (float)$row['sold_weight']);
                    $tot_rmt2 += ((float)$row['mean_age'] * (float)$row['sold_weight']);
                    
                    //Easyfoods
                    $esyt_mean += ((float)$row['mean_age'] * (float)$row['sold_birds']);
                    if(empty($days7_mcount[$row['batch_code']]) || $days7_mcount[$row['batch_code']] == ""){ $days7_mortcs = $row['days7_mort_count']; } else{ $days7_mortcs = $days7_mcount[$row['batch_code']]; }
                    if(empty($days30_mcount[$row['batch_code']]) || $days30_mcount[$row['batch_code']] == ""){ $days30_mortcs = $row['days30_mort_count']; } else{ $days30_mortcs = $days30_mcount[$row['batch_code']]; }
                    if(empty($days31_mcount[$row['batch_code']]) || $days31_mcount[$row['batch_code']] == ""){ $days31_mortcs = $row['days31_mort_count']; } else{ $days31_mortcs = $days31_mcount[$row['batch_code']]; }
                    
                    $days7_mort_count += (float)$days7_mortcs; $days30_mort_count += (float)$days30_mortcs; $days31_mort_count += (float)$days31_mortcs;
                    $tot_sold_birds += (float)$row['sold_birds']; $tot_sold_weight += (float)$row['sold_weight']; $ftot_sale_amount += (float)$row['sale_amount'];
                    $tot_chick_cost_amt += (float)$row['chick_cost_amt']; $tot_feed_cost_amt += (float)$row['feed_cost_amt']; $tot_medicine_cost_amt += (float)$row['medicine_cost_amt'];
                    $tot_admin_cost_amt1 += (float)$row['admin_cost_amt']; $tot_fpay += (float)$row['total_cost_amt']; $tot_amount_payable += (float)$row['total_amount_payable']; $tot_tds_amt += (float)$row['tds_amt']; $other_deduction += (float)$row['other_deduction']; $tot_actual_chick_cost += (float)$row['actual_chick_cost'];
                    $tot_actual_feed_cost += (float)$row['actual_feed_cost']; $tot_actual_medicine_cost += (float)$row['actual_medicine_cost']; $tot_admin_cost_amt2 += round((float)$row['admin_cost_amt'],2); $tot_farmer_payable += (float)$row['farmer_payable']; $tot_total_prod += (float)$total_prod;
                    $tot_actual_prod_cost += (float)$row['actual_prod_cost']; $tot_sale_amount += (float)$row['sale_amount'];
                    
                    $total_day_gain += (float)$row['day_gain'];
                    $total_eef += (float)$row['eef'];
                    $tot_actpc_amt += (float)$row['actual_charge_exp_amt'];
                    $tot_saleinc_amt += (float)$row['sales_incentive_amt'];
                    $approved_gc_amt += (float)$row['grow_charge_exp_amt'];
                    
                    $gc_inc_amt = (float)$row['sales_incentive_amt'] + (float)$row['avgwt_incentive_amt'] + (float)$row['unload_incentive_amt'] + (float)$row['mortality_incentive_amt'] + (float)$row['ifft_charges'] + (float)$row['other_incentive'] + (float)$row['transportation_charges'];
                    $gc_dec_amt = (float)$row['fcr_deduction'] + (float)$row['mortality_deduction'] + (float)$row['birds_shortage'] + (float)$row['farmer_sale_deduction'] + (float)$row['other_deduction'];
                    $tgc_inc_amt += (float)$gc_inc_amt;
                    $tgc_dec_amt += (float)$gc_inc_amt;

                    $high_feedin_gc_sup = $ven_name[$row['high_feedin_sup_code']];

                    //Sale Start and End Dates
                    $sale_sdate = $sale_edate = "";
                    if($row['sale_start_date'] != ""){ $sale_sdate = date("d.m.Y",strtotime($row['sale_start_date'])); }
                    if($row['sale_end_date'] != ""){ $sale_edate = date("d.m.Y",strtotime($row['sale_end_date'])); }

                    //Farmer Payment
                    if(empty($farmer_actpay_paid[$row['batch_code']]) || $farmer_actpay_paid[$row['batch_code']] == ""){ $farmer_actpay_paid[$row['batch_code']] = 0; }
                    $farmer_actpay_diff = 0; $farmer_actpay_diff = (float)$farmer_actpay_paid[$row['batch_code']] - (float)$row['farmer_payable'];
                    $tot_fmrpayable_amt += (float)$row['farmer_payable'];
                    $tot_fmrpaid_amt += (float)$farmer_actpay_paid[$row['batch_code']];

                    //Paras P&L Calculations
                    $tot_fcost = 0; $tot_fcost = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + $farmer_actpay_paid[$row['batch_code']];
                    $pl_on_fpay = 0; $pl_on_fpay = (float)$row['sale_amount']- (float)$tot_fcost; $pl_title = $row['sale_amount']."-".((float)$row['actual_chick_cost']."-".(float)$row['actual_feed_cost']."-".(float)$row['actual_medicine_cost']."-".(float)$row['admin_cost_amt']."-".$farmer_actpay_paid[$row['batch_code']]);
                    $tot_pl_amt += (float)$pl_on_fpay;

                    //Feed/Bird Total
                    $fp_bird = 0; if((float)$row['placed_birds'] != 0){ $fp_bird = round(($row['feed_consume_kgs'] / $row['placed_birds']),5); }
                    $tfp_bird += ((float)$row['sold_weight'] * (float)$fp_bird);

                    //FCR
                    $fcr_val = 0; if((float)$row['fcr'] != 0){ $fcr_val = round(($row['fcr']),3); }
                    $tfcr_val += ((float)$row['sold_weight'] * (float)$fcr_val);

                    //CFCR
                    $cfcr_val = 0; if((float)$row['fcr'] != 0){ $cfcr_val = round(($row['cfcr']),3); }
                    $tcfcr_val += ((float)$row['sold_weight'] * (float)$cfcr_val);

                    //Day Gain
                    $dgain_val = 0; if((float)$row['fcr'] != 0){ $dgain_val = round(($row['day_gain']),3); }
                    $tdgain_val += ((float)$row['sold_weight'] * (float)$dgain_val);

                    //EEF
                    $eef_val = 0; if((float)$row['fcr'] != 0){ $eef_val = round(($row['eef']),3); }
                    $teef_val += ((float)$row['sold_weight'] * (float)$eef_val);

                    //Std. MedVac Price
                    $std_mprc = 0; if((float)$row['sold_weight'] != 0){ $std_mprc = round(($row['medicine_cost_amt'] / $row['sold_weight']),2); }
                    $tstd_mprc += ((float)$row['sold_weight'] * (float)$std_mprc);

                    //Std. Admin Price
                    $std_aprc = 0; if((float)$row['sold_weight'] != 0){ $std_aprc = round(($row['admin_cost_amt'] / $row['sold_weight']),2); }
                    $tstd_aprc += ((float)$row['sold_weight'] * (float)$std_aprc);

                    //Std. GC prc
                    $tot_std_gcprc += (float)$row['standard_gc_prc'];
                    $tot_act_gcprc += (float)$row['actual_charge_exp_prc'];
                    $incr++;
                    echo "<tr>";
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                        if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No'>".$slno."</td>"; } 
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_date"){ echo "<td title='GC Date'>".date('d.m.Y',strtotime($row['date']))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<td title='Branch'>".$branch_name[$row['branch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<td title='Line'>".$line_name[$row['line_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor'>".$supervisor_name[$farm_supervisor[$row['farm_code']]]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_bank_aname"){ echo "<td title='Farm'>".$farmer_aname[$farm_farmer[$row['farm_code']]]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_panno"){ echo "<td title='Farm'>".$farmer_pan[$farm_farmer[$row['farm_code']]]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farm'>".$farm_name[$row['farm_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<td title='Batch'>".$batch_name[$row['batch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<td title='Book No'>".$batch_book[$row['batch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date"){ echo "<td title='Placement Date'>".date('d.m.Y',strtotime($row['start_date']))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date"){ echo "<td title='Liquidation Date'>".date('d.m.Y',strtotime($row['liquid_date']))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<td title='Age' style='text-align:right;'>".round($row['age'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<td title='Mean Age' style='text-align:right;'>".$row['mean_age']."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<td title='Chicks Placement' style='text-align:right;'>".str_replace(".00","",number_format_ind($chick_placed))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<td title='Mortality' style='text-align:right;'>".str_replace(".00","",number_format_ind($mortality_count))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<td title='Mortality%' style='text-align:right;'>".$row['total_mort']."</td>"; }
                        
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count"){ echo "<td title='Mortality%' style='text-align:right;'>".$days7_mortcs."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per"){ echo "<td title='Mortality%' style='text-align:right;'>".$row['days7_mort']."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count"){ echo "<td title='Mortality%' style='text-align:right;'>".$days30_mortcs."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per"){ echo "<td title='Mortality%' style='text-align:right;'>".$row['days30_mort']."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count"){ echo "<td title='Mortality%' style='text-align:right;'>".$days31_mortcs."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per"){ echo "<td title='Mortality%' style='text-align:right;'>".$row['daysge31_mort']."</td>"; }

                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_shortage_count"){ echo "<td title='Birds Shortage' style='text-align:right;'>".str_replace(".00","",number_format_ind($bird_shortage_count))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_excess_count"){ echo "<td title='Birds Excess' style='text-align:right;'>".str_replace(".00","",number_format_ind($bird_excess_count))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<td title='Feed Consumed Kgs' style='text-align:right;'>".number_format_ind(round($feedconsumed_count,2))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){
                            if(!empty($bstd_cum_feed[round($row['age'])])){
                                echo "<td title='Std. Feed/Bird' style='text-align:right;'>".round(($bstd_cum_feed[round($row['age'])] / 1000),3)."</td>";
                            }
                            else{
                                echo "<td title='Std. Feed/Bird' style='text-align:right;'></td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_birdsno"){
                            if(!empty($bstd_cum_feed[round($row['age'])]) && (float)$row['placed_birds'] != 0){
                                if((float)$row['placed_birds'] != 0 && (float)$row['sold_birds'] != 0 && ($bstd_cum_feed[round($row['age'])] / 1000) > ($row['feed_consume_kgs'] / $row['placed_birds'])){
                                    if($_SESSION['dbase'] == 'vpspoulsoft_broiler_tg_komala_poultries'){
                                        echo "<td title='Feed/Bird Kgs' style='text-align:right;color:green;'>".number_format_ind(round(($row['feed_consume_kgs'] / $row['sold_birds']),2))."</td>";
                                    }else{
                                        echo "<td title='Feed/Bird Kgs' style='text-align:right;color:green;'>".number_format_ind(round(($row['feed_consume_kgs'] / $row['placed_birds']),2))."</td>";
                                    }
                                   
                                }
                                else{
                                    if($_SESSION['dbase'] == 'vpspoulsoft_broiler_tg_komala_poultries'){
                                        echo "<td title='Feed/Bird Kgs' style='text-align:right;color:red;'>".number_format_ind(round(($row['feed_consume_kgs'] / $row['sold_birds']),2))."</td>";
                                    }else{
                                        echo "<td title='Feed/Bird Kgs' style='text-align:right;color:red;'>".number_format_ind(round(($row['feed_consume_kgs'] / $row['placed_birds']),2))."</td>";
                                    }
                                  
                                }
                            }
                            else{

                                if($_SESSION['dbase'] == 'vpspoulsoft_broiler_tg_komala_poultries'){
                                    echo "<td title='Feed/Bird Kgs' style='text-align:right;'>".number_format_ind(round(($row['feed_consume_kgs'] / $row['sold_birds']),2))."</td>";
                                }else{
                                    echo "<td title='Feed/Bird Kgs' style='text-align:right;'>".number_format_ind(round(($row['feed_consume_kgs'] / $row['placed_birds']),2))."</td>";
                                }
                                
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){
                            if(!empty($bstd_fcr[round($row['age'])])){
                                echo "<td title='Std.FCR' style='text-align:right;'>".$bstd_fcr[round($row['age'])]."</td>";
                            }
                            else{
                                echo "<td title='Std.FCR' style='text-align:right;'></td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){
                            if(!empty($bstd_fcr[round($row['age'])])){
                                if($bstd_fcr[round($row['age'])] > ($row['fcr'])){
                                    echo "<td title='FCR' style='text-align:right;color:green;'>".decimal_adjustments($row['fcr'],3)."</td>";
                                }
                                else{
                                    echo "<td title='FCR' style='text-align:right;color:red;'>".decimal_adjustments($row['fcr'],3)."</td>";
                                }
                            }
                            else if($row['fcr'] > 0){
                                echo "<td title='FCR' style='text-align:right;'>".decimal_adjustments($row['fcr'],3)."</td>";
                            }
                            else{
                                echo "<td title='FCR' style='text-align:right;color:red;'></td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<td title='CFCR' style='text-align:right;'>".decimal_adjustments($row['cfcr'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_gain"){ echo "<td title='Day Gain' style='text-align:right;'>".number_format_ind($row['day_gain'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<td title='EEF' style='text-align:right;'>".number_format_ind($row['eef'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Sold Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind($row['sold_birds']))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Sold Weight' style='text-align:right;'>".number_format_ind($row['sold_weight'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<td title='Avg. Body Wt' style='text-align:right;'>".decimal_adjustments($row['avg_wt'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<td title='Sale Price' style='text-align:right;'>".number_format_ind($row['sale_rate'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_amount"){ echo "<td title='Sale Amount' style='text-align:right;'>".number_format_ind($row['sale_amount'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price"){
                            if($row['placed_birds'] > 0 && $row['chick_cost_amt'] > 0){
                                echo "<td title='Std Chick Price' style='text-align:right;'>".number_format_ind($row['chick_cost_amt'] / $row['placed_birds'])."</td>";
                            }
                            else{
                                echo "<td title='Std Chick Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_amount"){ echo "<td title='Std Chick Cost' style='text-align:right;'>".number_format_ind($row['chick_cost_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_price"){
                            if($row['feed_cost_amt'] > 0 && $row['feed_consume_kgs'] > 0){
                                echo "<td title='Std Feed Price' style='text-align:right;'>".number_format_ind($row['feed_cost_amt'] / $row['feed_consume_kgs'])."</td>";
                            }
                            else{
                                echo "<td title='Std Feed Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_amount"){ echo "<td title='Std Feed Cost' style='text-align:right;'>".number_format_ind($row['feed_cost_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price"){
                            if($row['medicine_cost_amt'] > 0 && $row['sold_weight'] > 0){
                                echo "<td title='Std Medicine Price' style='text-align:right;'>".number_format_ind($row['medicine_cost_amt'] / $row['sold_weight'])."</td>";
                            }
                            else{
                                echo "<td title='Std Medicine Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount"){ echo "<td title='Std Medicine Cost' style='text-align:right;'>".number_format_ind($row['medicine_cost_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price"){
                            if($row['admin_cost_amt'] > 0 && $row['placed_birds'] > 0){
                                echo "<td title='Std Admin Price' style='text-align:right;'>".number_format_ind($row['admin_cost_amt'] / $row['placed_birds'])."</td>";
                            }
                            else{
                                echo "<td title='Std Admin Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc"){
                            if($row['admin_cost_amt'] > 0 && $row['sold_weight'] > 0){
                                echo "<td title='Std Admin Price' style='text-align:right;'>".number_format_ind($row['admin_cost_amt'] / $row['sold_weight'])."</td>";
                            }
                            else{
                                echo "<td title='Std Admin Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount"){ echo "<td title='Std Admin Cost' style='text-align:right;'>".number_format_ind($row['admin_cost_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount"){ echo "<td title='Farmer P.cost' style='text-align:right;'>".number_format_ind($row['total_cost_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price"){ echo "<td title='F PC/Kg' style='text-align:right;'>".number_format_ind($row['total_cost_unit'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg"){ echo "<td title='STD.Gc/kg' style='text-align:right;'>".number_format_ind($row['standard_gc_prc'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_perkg"){ echo "<td title='STD.Gc/kg' style='text-align:right;'>".decimal_adjustments($row['actual_charge_exp_prc'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive"){ echo "<td title='Total Incentives' style='text-align:right;'>".decimal_adjustments($incentive,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives"){ echo "<td title='Decentives' style='text-align:right;'>".decimal_adjustments($decentive,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price"){ echo "<td title='Gc/kg' style='text-align:right;'>".number_format_ind($row['total_gc_prc'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2"){
                            if($row['sold_weight'] > 0){
                                echo "<td title='Act Chick Price' style='text-align:right;'>".number_format_ind($row['total_amount_payable'] / $row['sold_weight'])."</td>";
                            }
                            else{
                                echo "<td title='Act Chick Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges"){ echo "<td title='Basic Rearing Charges' style='text-align:right;'>".number_format_ind($row['grow_charge_exp_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_rearing_charges"){ echo "<td title='Total Rearing Charges' style='text-align:right;'>".number_format_ind($row['total_amount_payable'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_tds_amount"){ echo "<td title='TDS' style='text-align:right;'>".number_format_ind($row['tds_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "other_deduction"){ echo "<td title='Other Deductitons' style='text-align:right;'>".number_format_ind($row['other_deduction'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_payable"){ echo "<td title='Farmer Payable' style='text-align:right;'>".number_format_ind($row['farmer_payable'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_price"){
                            if($row['placed_birds'] > 0){
                                echo "<td title='Act Chick Price' style='text-align:right;'>".number_format_ind($row['actual_chick_cost'] / $row['placed_birds'])."</td>";
                            }
                            else{
                                echo "<td title='Act Chick Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_amount"){ echo "<td title='Act Chick Cost' style='text-align:right;'>".number_format_ind($row['actual_chick_cost'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_price"){
                            if($row['feed_consume_kgs'] > 0){
                                echo "<td title='Act Feed Price' style='text-align:right;'>".number_format_ind($row['actual_feed_cost'] / $row['feed_consume_kgs'])."</td>";
                            }
                            else{
                                echo "<td title='Act Feed Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_amount"){ echo "<td title='Act Feed Cost' style='text-align:right;'>".number_format_ind($row['actual_feed_cost'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_price"){
                            if($row['placed_birds'] > 0){
                                echo "<td title='Medicine Price' style='text-align:right;'>".number_format_ind($row['actual_medicine_cost'] / $row['placed_birds'])."</td>";
                            }
                            else{
                                echo "<td title='Medicine Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_amount"){ echo "<td title='Medicine Cost' style='text-align:right;'>".number_format_ind($row['actual_medicine_cost'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_price"){
                            if($row['placed_birds'] > 0){
                                echo "<td title='Admin Price' style='text-align:right;'>".number_format_ind($row['admin_cost_amt'] / $row['placed_birds'])."</td>";
                            }
                            else{
                                echo "<td title='Admin Price' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_amount"){ echo "<td title='Admin Cost' style='text-align:right;'>".number_format_ind($row['admin_cost_amt'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "final_farmer_payable"){ echo "<td title='GC Paid to Farmer' style='text-align:right;'>".number_format_ind($row['farmer_payable'])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_prod_amount"){ echo "<td title='Actual P.Amount' style='text-align:right;'>".number_format_ind($total_prod)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){
                            if($row['sold_weight'] > 0){
                                echo "<td title='M PC/Kg' style='text-align:right;'>".number_format_ind(((float)$total_prod / (float)$row['sold_weight']))."</td>";
                            }
                            else{
                                echo "<td title='M PC/Kg' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_sale_amount"){
                            $t1 = 0; $t1 = (float)$row['sale_amount'];
                            echo "<td title='Total Sale Amount' style='text-align:right;'>".number_format_ind($t1)."</td>";
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "profit_and_loss"){
                            if($profit_loss > 0){
                                echo "<td title='Profit/Loss' style='text-align:right;color:green;'>".number_format_ind($profit_loss)."</td>";
                            }
                            else{
                                echo "<td title='Profit/Loss' style='text-align:right;color:red;'>".number_format_ind($profit_loss)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_fcr"){ echo "<td title='Old FCR' style='text-align:right;color:green;'>".$old_fcr[$row['batch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_cfcr"){ echo "<td title='Old CFCR' style='text-align:right;color:green;'>".$old_cfcr[$row['batch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "week1_bodywt"){ echo "<td title='1st week B.Wt' style='text-align:right;color:green;'>".number_format_ind($week1_bwt)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_grading"){ echo "<td title='Grading' style='text-align:right;color:green;'>".$row['grade']."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "last_lifting_date"){ echo "<td title='Grading' style='text-align:right;color:green;'>".$last_sale_date[$row['batch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_brand_name"){ echo "<td title='Grading' style='text-align:left;color:green;'>".$brand_name[$row['high_feedin_brand_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){
                            if((int)$bsup_flag == 0 && (int)$csn_flag == 1){
                                echo "<td title='chick_received_from' style='text-align:left;'>".$chick_supplier_name[$row['batch_code']]."</td>";
                            }
                            else{
                                echo "<td title='chick_received_from' style='text-align:left;color:green;'>".$chkin_name[$row['high_chickin_secvcode']]."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_paid"){ echo "<td title='Farmer Adjustment Payment' style='text-align:right;'>".number_format_ind($farmer_actpay_paid[$row['batch_code']])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_diff"){ echo "<td title='Farmer Adjustment Payment' style='text-align:right;'>".number_format_ind($farmer_actpay_diff)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<td title='Farm Code' style='text-align:left;'>".$farm_ccode[$row['farm_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "paid_to_farmer"){ echo "<td title='Paid to Farmer' style='text-align:right;'>".number_format_ind($farmer_actpay_paid[$row['batch_code']])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pl_on_fmrpay"){ echo "<td title='$pl_title' style='text-align:right;'>".number_format_ind($pl_on_fpay)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<td title='Hatchery Name' style='text-align:left;'>".$sector_name[$chkin_hcode[$row['batch_code']]]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<td title='Chick Supplier Name' style='text-align:left;'>".$sector_name[$chkin_vcode[$row['batch_code']]]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "billno"){ echo "<td title='Today Mortality' style='text-align:left;'>".$chkin_dcno[$row['batch_code']]."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency"){ echo "<td title='Lifiting Efficiency' style='text-align:right;'>".round($row['lifting_efficiency'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_week_no"){ echo "<td title='Week No.' style='text-align:left;'>".$row['week_no']."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_sdate"){ echo "<td title='Lifting Start Date' style='text-align:left;'>".$sale_sdate."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_edate"){ echo "<td title='Lifting End Date' style='text-align:left;'>".$sale_edate."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_prc"){ echo "<td title='Acutal GC Rate' style='text-align:right;'>".round($row['actual_charge_exp_prc'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_amt"){ echo "<td title='Acutal GC Amount' style='text-align:right;'>".round($row['actual_charge_exp_amt'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc"){ echo "<td title='Sale Incentives/kg' style='text-align:right;'>".round($row['sales_incentive_prc'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_amt"){ echo "<td title='Sale Incentives' style='text-align:right;'>".round($row['sales_incentive_amt'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc"){ echo "<td title='PC Incentive Rate' style='text-align:right;'>".round($row['grow_charge_exp_prc'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_amt"){ echo "<td title='PC Incentive Amount' style='text-align:right;'>".round($row['grow_charge_exp_amt'],3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_inc_amt"){ echo "<td title='Incentive Amount' style='text-align:right;'>".round($gc_inc_amt,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_dec_amt"){ echo "<td title='Decentive Amount' style='text-align:right;'>".round($gc_dec_amt,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "high_feedin_gc_sup"){ echo "<td title='Feed Supplier Name'>".$high_feedin_gc_sup."</td>"; }
                        else { }
                    }
                    echo "</tr>";

                    if($row['sold_weight'] > 0){
                        if(number_format_ind($row['total_amount_payable'] / $row['sold_weight']) > 0){
                            $total_gc_per_kg +=  $row['sold_weight'] * number_format_ind($row['total_amount_payable'] / $row['sold_weight']);
                        }
                    }
                    if($row['total_cost_unit'] > 0){
                        $total_fpc_per_kg +=  $row['sold_weight'] * $row['total_cost_unit'];
                    }
                    if($row['grow_charge_exp_prc'] > 0){
                        $total_pcincentive_per_kg +=  $row['sold_weight'] * $row['grow_charge_exp_prc'];
                    }
                    if($row['sales_incentive_prc'] > 0){
                        $total_salesincentive_per_kg +=  $row['sold_weight'] * $row['sales_incentive_prc'];
                    }
                    if($incentive > 0){
                        $total_incentive_per_kg +=  $row['sold_weight'] * $incentive;
                    }
                    if($decentive > 0){
                        $total_decentive_per_kg +=  $row['sold_weight'] * $decentive;
                    }
                    if(round($row['lifting_efficiency'],3) > 0){
                        $total_efficiency_per_kg +=  $row['sold_weight'] * round($row['lifting_efficiency'],3);
                    }
                   
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                    $tmean_age = 0; if((float)$tot_sold_birds != 0){ $tmean_age = (float)$esyt_mean / (float)$tot_sold_birds; }
                    $tmort_per = 0; if((float)$tot_placed_birds != 0){ $tmort_per = (((float)$tot_mortality / (float)$tot_placed_birds) * 100); }
                    $tavg_wht = 0; if((float)$tot_sold_birds != 0){ $tavg_wht = (float)$tot_sold_weight / (float)$tot_sold_birds; }
                    $tavg_fcr = 0; if((float)$tot_sold_weight != 0){ $tavg_fcr = (float)$tfcr_val / (float)$tot_sold_weight; }
                    if(($tmean_age * $tavg_fcr) > 0){
                        $easy_eef = (((100 - $tmort_per) * $tavg_wht) / ($tmean_age * $tavg_fcr) * 100);
                        //echo "<br/>$easy_eef = (((100 - $tmort_per) * $tavg_wht) / ($tmean_age * $tavg_fcr) * 100);";
                    }else{
                        $easy_eef = 0;
                    }
                   
                echo "<tr class='thead4'>";
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th style='text-align:left; border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_date"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_bank_aname"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_panno"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "batch_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "book_no"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "placement_date"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "liquidation_date"){ echo "<th style='text-align:center; border-left: 0px;'>Total</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "brood_age"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){
                        //if($tot_sold_birds > 0){ echo "<th style='text-align:right;'>".number_format_ind(round(($mtsb / $tot_sold_birds),2))."</th>"; }
                        //else{ echo "<th style='text-align:right;'>".number_format_ind(0)."</th>"; }
                        if($_SESSION['dbase'] == "poulso6_broiler_wb_sahapoultry"){
                            if((float)$tot_sold_weight != 0){ $t2 = round(((float)$tot_rmt2 / (float)$tot_sold_weight),2); } else{ $t2 = 0; }
                            echo "<th style='text-align:right;' title='$mtitle'>".number_format_ind($t2)."</th>";
                        }
                        else if($_SESSION['dbase'] == "poulso6_broiler_hr_easyfoods"){
                            echo "<th style='text-align:right;'>".number_format_ind($tmean_age)."</th>";
                        }
                        else{
                            $mtitle = "";
                            if($incr > 0){ $mean_age = 0; $mean_age = $total_meanage / $incr; $mtitle = "$mean_age = $total_meanage / $incr;"; } else{ $mean_age = 0; }
                            echo "<th style='text-align:right;' title='$mtitle'>".number_format_ind($mean_age)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th style='text-align:right;'>".str_replace(".00","",number_format_ind($tot_placed_birds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th style='text-align:right;'>".str_replace(".00","",number_format_ind($tot_mortality))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){
                        if($tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(round((($tot_mortality / $tot_placed_birds) * 100),2))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count"){ echo "<th style='text-align:right;'>".str_replace(".00","",number_format_ind($days7_mort_count))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per"){
                        if($tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(round((($days7_mort_count / $tot_placed_birds) * 100),2))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count"){ echo "<th style='text-align:right;'>".str_replace(".00","",number_format_ind($days30_mort_count))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per"){
                        if($tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(round((($days30_mort_count / $tot_placed_birds) * 100),2))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count"){ echo "<th style='text-align:right;'>".str_replace(".00","",number_format_ind($days31_mort_count))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per"){
                        if($tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(round((($days31_mort_count / $tot_placed_birds) * 100),2))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }

                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_shortage_count"){ echo "<th style='text-align:right;'>".str_replace(".00","",$tot_shortage)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_excess_count"){ echo "<th style='text-align:right;'>".str_replace(".00","",$tot_excess)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_feed_consume_kgs))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_birdsno"){
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if($tot_sold_weight > 0){ 
                                echo "<th style='text-align:right;'>".number_format_ind(round(($tfp_bird / $tot_sold_weight),2))."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                        else{
                            if($tot_placed_birds > 0){
                                echo "<th style='text-align:right;'>".number_format_ind(round(($tot_feed_consume_kgs / $tot_placed_birds),2))."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){
                        if(!empty($bstd_fcr[0])){
                            echo "<th style='text-align:right;'>".$bstd_fcr[0]."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'></th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if($tot_sold_weight > 0){
                                echo "<th style='text-align:right;'>".decimal_adjustments(($tfcr_val / $tot_sold_weight),3)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".decimal_adjustments(0,3)."</th>";
                            }
                        }
                        else{
                            if($tot_sold_weight > 0){
                                echo "<th style='text-align:right;'>".decimal_adjustments(($tot_feed_consume_kgs / $tot_sold_weight),3)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".decimal_adjustments(0,3)."</th>";
                            }
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if($tot_sold_weight > 0){
                                echo "<th style='text-align:right;'>".decimal_adjustments(($tcfcr_val / $tot_sold_weight),3)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".decimal_adjustments(0,3)."</th>";
                            }
                        }
                        else{
                            if($tot_sold_birds > 0){
                                $t1 = 0; $t1 = $tot_sold_weight / $tot_sold_birds;
                            }
                            else{
                                $t1 = 0;
                            }
                            if($tot_sold_weight > 0){
                                $t2 = 0; $t2 = $tot_feed_consume_kgs / $tot_sold_weight;
                            }
                            else{
                                $t2 = 0;
                            }
                            echo "<th style='text-align:right;'>".decimal_adjustments((((2 - (($t1))) / 4) + ($t2)),3)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_gain"){
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if($tot_sold_weight > 0){
                                echo "<th style='text-align:right;'>".decimal_adjustments(($tdgain_val / $tot_sold_weight),3)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".decimal_adjustments(0,3)."</th>";
                            }
                        }
                        else{
                            /*if($tot_sold_birds > 0){ $t1 = 0; $t1 = $tot_sold_weight / $tot_sold_birds; } else{ $t1 = 0; }
                            if($tot_sold_birds > 0){ $t2 = 0; $t2 = $mtsb / $tot_sold_birds; } else{ $t2 = 0; }
                            if($t2 > 0 && $t1 > 0){ echo "<th style='text-align:right;'>".number_format_ind(round((((round(($t1),2)) * 1000) / ($t2)),2))."</th>"; }
                            else{ echo "<th style='text-align:right;'>".number_format_ind(0)."</th>"; }*/
                            if($incr > 0){ $t1 = 0; $t1 = $total_day_gain / $incr; } else{ $t1 = 0; }
                            echo "<th style='text-align:right;'>".number_format_ind($t1)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){
                        if($_SESSION['dbase'] == "poulso6_broiler_wb_sahapoultry"){
                            if($tot_sold_weight > 0){
                                $eef_t = "$teef_val / $tot_sold_weight";
                                echo "<th style='text-align:right;' title='".$eef_t."'>".decimal_adjustments(($teef_val / $tot_sold_weight),3)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".decimal_adjustments(0,3)."</th>";
                            }
                        }
                        else if($_SESSION['dbase'] == "poulso6_broiler_hr_easyfoods"){
                            echo "<th style='text-align:right;'>".decimal_adjustments($easy_eef,3)."</th>";
                        }
                        else if($_SESSION['dbase'] != "poulso6_broiler_hr_easyfoods"){
                            if($tot_placed_birds > 0){ $t1 = ($tot_placed_birds - $tot_mortality) / $tot_placed_birds; } else{ $t1 = 0; }
                            if($tot_sold_birds > 0){ $t2 = $tot_sold_weight / $tot_sold_birds; } else{ $t2 = 0; }
                            if($tot_sold_weight > 0){ $t3 = $tot_feed_consume_kgs / $tot_sold_weight; } else{ $t3 = 0; }
                            if($tot_sold_birds > 0){ $t4 = $mean_age; } else{ $t4 = 0; }
                            //if($tot_sold_birds > 0){ $t4 = $mtsb / $tot_sold_birds; } else{ $t4 = 0; }
                            $title = "round((((((($tot_placed_birds - $tot_mortality) / $tot_placed_birds) * 100) * (round(($tot_sold_weight / $tot_sold_birds),3))) * 100) / (($tot_feed_consume_kgs / $tot_sold_weight) * ($mean_age))))";
                            if($t1 > 0 && $t2 > 0 && $t3 > 0 && $t4 > 0){ echo "<th style='text-align:right;' title='".$title."'>".number_format_ind(round(((((($t1) * 100) * (round(($t2),3))) * 100) / (($t3) * ($t4)))))."</th>"; }
                            else{ echo "<th style='text-align:right;' title='".$title."'>".number_format_ind(0)."</th>"; }
                            //if($incr > 0){ $t1 = 0; $t1 = $total_eef / $incr; } else{ $t1 = 0; }
                            //echo "<th style='text-align:right;'>".number_format_ind($t1)."</th>";
                        }
                        else{
                            $feef = (((100 - (($tot_mortality / $tot_placed_birds) * 100)) * ($tot_sold_weight / $tot_sold_birds))/(((float)$tot_rmt / (float)$tot_sold_birds) * ($tot_feed_consume_kgs / $tot_sold_weight)) * 100);
                            echo "<th style='text-align:right;' title='".$title."'>".number_format_ind($feef)."</th>";
                        }                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th style='text-align:right;'>".str_replace(".00","",number_format_ind($tot_sold_birds))."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th style='text-align:right;'>".number_format_ind($tot_sold_weight)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){
                        if($tot_sold_birds > 0){
                            echo "<th style='text-align:right;'>".decimal_adjustments(($tot_sold_weight / $tot_sold_birds),3)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".decimal_adjustments(0,3)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_perkg_price"){
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($ftot_sale_amount / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_amount"){ echo "<th style='text-align:right;'>".number_format_ind($ftot_sale_amount)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price"){
                        if($tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_chick_cost_amt / $tot_placed_birds)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_chick_cost_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_price"){
                        if($tot_feed_consume_kgs > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_feed_cost_amt / $tot_feed_consume_kgs)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_feed_cost_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price"){
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if($tot_sold_weight > 0){ 
                                echo "<th style='text-align:right;'>".number_format_ind(round(($tstd_mprc / $tot_sold_weight),2))."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                        else{
                            if($tot_sold_weight > 0){
                                echo "<th style='text-align:right;'>".number_format_ind($tot_medicine_cost_amt / $tot_sold_weight)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_medicine_cost_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price"){
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if($tot_sold_weight > 0){ 
                                echo "<th style='text-align:right;'>".number_format_ind(round(($tstd_aprc / $tot_sold_weight),2))."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                        else{
                            if($tot_placed_birds > 0){
                                echo "<th style='text-align:right;'>".number_format_ind($tot_admin_cost_amt1 / $tot_placed_birds)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc"){
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_admin_cost_amt1 / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_admin_cost_amt1)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_fpay)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price"){
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_fpc_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg"){ 
                        if($_SESSION['dbase'] == 'poulso6_broiler_wb_sahapoultry'){
                            if((float)$slno != 0){
                                echo "<th style='text-align:right;'>".number_format_ind($tot_std_gcprc / $slno)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                        else{
                            if($tot_sold_weight > 0){
                                echo "<th style='text-align:right;'>".number_format_ind($total_gc_per_kg / $tot_sold_weight)."</th>";
                            }
                            else{
                                echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                            }
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_perkg"){
                        if((float)$slno != 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_act_gcprc / $slno)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive"){  
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_incentive_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        } 
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives"){   
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_decentive_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        } 
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2"){  
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_gc_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }  
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_rearing_charges"){ echo "<th style='text-align:right;'>".number_format_ind($tot_amount_payable)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_tds_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_tds_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "other_deduction"){ echo "<th style='text-align:right;'>".number_format_ind($other_deduction)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_payable"){ echo "<th style='text-align:right;'>".number_format_ind($tot_farmer_payable)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_price"){
                        if($tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_actual_chick_cost / $tot_placed_birds)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_actual_chick_cost)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_price"){
                        if($tot_actual_feed_cost > 0 && $tot_feed_consume_kgs > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_actual_feed_cost / $tot_feed_consume_kgs)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_actual_feed_cost)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_price"){
                        if($tot_actual_medicine_cost > 0 && $tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_actual_medicine_cost / $tot_placed_birds)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_actual_medicine_cost)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_price"){
                        if($tot_admin_cost_amt2 > 0 && $tot_placed_birds > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($tot_admin_cost_amt2 / $tot_placed_birds)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_admin_cost_amt2)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "final_farmer_payable"){ echo "<th style='text-align:right;'>".number_format_ind($tot_farmer_payable)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_prod_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_total_prod)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind(($tot_total_prod / $tot_sold_weight))."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                        
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_sale_amount"){ echo "<th style='text-align:right;'>".number_format_ind($tot_sale_amount)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "profit_and_loss"){
                        $profit_loss = $ftot_sale_amount - $tot_total_prod;
                        if($profit_loss > 0){
                            echo "<th style='text-align:right;color:green;'>".number_format_ind($profit_loss)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;color:red;'>".number_format_ind($profit_loss)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_fcr"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "old_cfcr"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "week1_bodywt"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_grading"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "last_lifting_date"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feed_brand_name"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_received_from"){ echo "<th></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_paid"){ echo "<th style='text-align:right;'>".number_format_ind($tot_fmrpaid_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_actpay_diff"){ echo "<th style='text-align:right;'>".number_format_ind($tot_fmrpaid_amt - $tot_fmrpayable_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farm_ccode"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "paid_to_farmer"){ echo "<th style='text-align:right;'>".number_format_ind($tot_fmrpaid_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "pl_on_fmrpay"){ echo "<th style='text-align:right;'>".number_format_ind($tot_pl_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_hatchery_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chickin_supplier_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "billno"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency"){
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_efficiency_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        } 
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_week_no"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_sdate"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_edate"){ echo "<th style='text-align:left;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_prc"){ echo "<th style='text-align:right;'></th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "act_gc_amt"){ echo "<th style='text-align:right;'>".number_format_ind($tot_actpc_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc"){
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_salesincentive_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_amt"){ echo "<th style='text-align:right;'>".number_format_ind($tot_saleinc_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc"){ 
                        if($tot_sold_weight > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($total_pcincentive_per_kg / $tot_sold_weight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_amt"){ echo "<th style='text-align:right;'>".number_format_ind($approved_gc_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_inc_amt"){ echo "<th style='text-align:right;'>".number_format_ind($tgc_inc_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_dec_amt"){ echo "<th style='text-align:right;'>".number_format_ind($tgc_dec_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "high_feedin_gc_sup"){ echo "<th style='text-align:right;'></th>"; }
                    else{ }
                }
                echo "</tr>";
                ?>
            </tfoot>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script src="../table_search_filter/Search_Script.js"></script>
        <script>
            function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="table_sorting_wauto_slno.js"></script>
        <script src="table_search_fields.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){ 
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
                }
                else{ }
            }
        </script>
        <script>
            function fetch_farms_details(a){
                var regions = document.getElementById("regions").value;
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;
                var user_code = '<?php echo $user_code; ?>';
                var rf_flag = bf_flag = lf_flag = sf_flag = ff_flag = 0;
                if(a.match("regions")){ rf_flag = 1; } else if(a.match("branches")){ bf_flag = 1; } else if(a.match("lines")){ lf_flag = 1; } else if(a.match("supervisors")){ sf_flag = 1; } else{ ff_flag = 1; }
                    
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_fetch_farm_filter_master.php?regions="+regions+"&branches="+branches+"&lines="+lines+"&supervisors="+supervisors+"&rf_flag="+rf_flag+"&bf_flag="+bf_flag+"&lf_flag="+lf_flag+"&sf_flag="+sf_flag+"&ff_flag="+ff_flag+"&user_code="+user_code;
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var brnh_list = fltr_dt2[3];
                        var line_list = fltr_dt2[0];
                        var supr_list = fltr_dt2[1];
                        var farm_list = fltr_dt2[2];

                        if(rf_flag == 1){
                            removeAllOptions(document.getElementById("branches"));
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#branches').append(brnh_list);
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(lf_flag == 1){
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("farms"));
                            $('#farms').append(farm_list);
                        }
                        else{ }
                    }
                }
            }
            var f_cnt = 0;
            function set_auto_selectors(){
                if(f_cnt == 0){
                    var fx = "regions"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 1){
                    var br_val = brlist = "";
                    $('#branches').select2();
                    for(var option of document.getElementById("branches").options){
                        option.selected = false;
                        br_val = option.value;
                        brlist = ''; brlist = '<?php echo $branches; ?>';
                        if(br_val == brlist){ option.selected = true; }
                    }
                    $('#branches').select2();
                    var fx = "branches"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    var l_val = llist = "";
                    $('#lines').select2();
                    for(var option of document.getElementById("lines").options){
                        option.selected = false;
                        l_val = option.value;
                        llist = ''; llist = '<?php echo $lines; ?>';
                        if(l_val == llist){ option.selected = true; }
                    }
                    $('#lines').select2();
                    var fx = "lines"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    var s_val = slist = "";
                    $('#supervisors').select2();
                    for(var option of document.getElementById("supervisors").options){
                        option.selected = false;
                        s_val = option.value;
                        slist = ''; slist = '<?php echo $supervisors; ?>';
                        if(s_val == slist){ option.selected = true; }
                    }
                    $('#supervisors').select2();
                    var fx = "supervisors"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    var f_val = flist = "";
                    $('#farms').select2();
                    for(var option of document.getElementById("farms").options){
                        option.selected = false;
                        f_val = option.value;
                        flist = ''; flist = '<?php echo $farms; ?>';
                        if(f_val == flist){ option.selected = true; }
                    }
                    $('#farms').select2();
                    var fx = "farms"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 4){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>