<?php
//broiler_customersales_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];
    $user_name = $_SESSION['users'];
    $dbname = $_SESSION['dbase'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
    $dbname = $db;
}

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }
if(in_array("company_price_list", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.company_price_list LIKE poulso6_admin_broiler_broilermaster.company_price_list;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_cus_lines", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_cus_lines LIKE poulso6_admin_broiler_broilermaster.breeder_cus_lines;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cline_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cline_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `name`"; mysqli_query($conn,$sql); }

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); 
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = ""; $daf_flag = 0;
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
            if($cna == "customer_diff_amt" && $fas_details[1] == "1" && $fas_details[2] > 0){ $daf_flag = 1; }
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
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code'];
    $farm_ccode[$row['code']] = $row['farm_code'];
    $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code'];
    $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `main_access` "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_db_code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `broiler_farmer` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; $item_hsn[$row['code']] = $row['hsn_code']; }

$sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cline_code[$row['code']] = $row['code']; $cline_name[$row['code']] = $row['description']; }

// $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cline_fltr." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
// while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $vendor_ccode[$row['code']] = $row['cus_ccode']; $vendor_mobl[$row['code']] = $row['mobile1']; $vendor_addr[$row['code']] = $row['baddress']; }

$me_flag = $mecount = 0; $me_arr_code = array();
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Sales' AND `field_function` LIKE 'Marketing Executive Field' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $me_flag = mysqli_num_rows($query);

if($me_flag > 0){
    $sql = "SELECT DISTINCT(marketing_executive) as empcode FROM `broiler_sales` WHERE `active` = '1' AND `dflag` = '0' GROUP BY `marketing_executive` ORDER BY `marketing_executive` ASC";
    $query = mysqli_query($conn,$sql); $mecount = mysqli_num_rows($query);
    if($mecount > 0){
        while($row = mysqli_fetch_assoc($query)){
            if($row['empcode'] != ""){
                $me_arr_code[$row['empcode']] = $row['empcode'];
            }
        }
        $me_size = sizeof($me_arr_code);
        if($me_size > 0){
            $me_list = implode("','", $me_arr_code);
            $sql = "SELECT * FROM `broiler_employee` WHERE `code` IN ('$me_list') ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $me_emp_code[$row['code']] = $row['code']; $me_emp_name[$row['code']] = $row['name']; }
        }
    }
}
$i = 0;
if($_SERVER['REMOTE_ADDR'] == "49.205.135.183" || $user_name = "paras"){
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial";
    //$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial, sans-serif";
    //$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica";
    //$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica, Arial, sans-serif";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Verdana, sans-serif";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Tahoma, sans-serif";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Trebuchet MS";
    //$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Trebuchet MS', sans-serif";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman'";
    //$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman', serif";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Georgia, serif";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Garamond, serif";
    //$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Courier New', monospace";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Courier, monospace";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Optima";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Segoe";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Calibri";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Candara";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Grande";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Sans Unicode";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Gill Sans";
    $i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Source Sans Pro', 'Arial', sans-serif";

    for($i = 0;$i <= 30;$i++){ $fsizes[$i."px"] = $i."px"; }
}
$fdate = $tdate = date("Y-m-d"); $item_cat = $items = $branches = $lines = $vendors = $regions = $clines = $sectors = $farms = $mark_exec = "all"; $excel_type = "display";
$font_stype = ""; $font_size = "11px";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $item_cat = $_POST['item_cat'];
    $items = $_POST['items'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $vendors = $_POST['vendors'];
    $clines = $_POST['cline'];
    $sectors = $_POST['sectors'];
     $regions = $_POST['regions'];
    $mark_exec = $_POST['mark_exec'];
    if($_SERVER['REMOTE_ADDR'] == "49.205.135.183" || $user_name = "paras"){
        $font_stype = $_POST['font_stype'];
        $font_size = $_POST['font_size'];
    }

       $farm_query = "";
     if($regions != "all"){
        $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
        $rbrh_list = implode("','",$rbrh_alist);
        $farm_query = " AND `branch_code` IN ('$rbrh_list')";
    }
    if($sectors != "all"){
        $sector_filter = " AND `warehouse` = '$sectors'";
        $sector_filter2 = " AND `fromwarehouse` = '$sectors'";
    }
    else{
        $farm_filter = "";
        if($branches != "all"){ $farm_filter .= " AND `branch_code` = '$branches'"; }
        if($lines != "all"){ $farm_filter .= " AND `line_code` = '$lines'"; }
        if($supervisors != "all"){ $farm_filter .= " AND `supervisor_code` = '$supervisors'"; }

        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2."".$farm_filter."".$farm_query." ORDER BY `farm_code` ASC"; $query = mysqli_query($conn,$sql); $farm_list = "";
        while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['code']; } else{ $farm_list = $farm_list."','".$row['code']; } }

        if($branches == "all" && $lines == "all" && $supervisors == "all"){
            $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['code']; } else{ $farm_list = $farm_list."','".$row['code']; } }
        }
        $sector_filter = " AND `warehouse` IN ('$farm_list')";
        $sector_filter2 = " AND `fromwarehouse` IN ('$farm_list')";
    }
    $cline_fltr = "";
    if($clines != "all"){ $cline_fltr = " AND `cline_code` IN ('$clines')"; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cline_fltr." ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $bcodes = ""; $vendor_code = array();
    while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $vendor_ccode[$row['code']] = $row['cus_ccode']; $vendor_mobl[$row['code']] = $row['mobile1']; $vendor_addr[$row['code']] = $row['baddress']; }

    if($vendors != "all"){
        $vendor_filter = " AND `vcode` = '$vendors'";
    }
    else if($clines != "all"){
        $cus_list = implode("','",$vendor_code);
        $vendor_filter = " AND `vcode` IN ('$cus_list')";
    }
    else{ $vendor_filter = ""; }

    if($me_flag > 0 && $me_size > 0){ if($mark_exec == "all"){ $me_filter = ""; } else{ $me_filter = " AND `marketing_executive` = '$mark_exec'"; } } else{ $me_filter = ""; }

    
    if($items != "all"){ $item_filter = " AND `icode` IN ('$items')"; $item_filter2 = " AND `icode` IN ('$items')"; }
    else if($item_cat == "all"){ $item_filter = $item_filter2 = ""; }
    else{
        $icat_list = $item_filter = $item_filter2 = "";
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
        $item_filter = " AND `icode` IN ('$icat_list')";
        $item_filter2 = " AND `item_code` IN ('$icat_list')";
    }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_customersales_masterreport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&items=".$items."&vendors=".$vendors."&branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&sectors=".$sectors;
}
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
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
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
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:'.$font_size.';color:#000000;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:'.$font_size.';color:#000000;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#FFFFFF,#FFFFFF); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:'.$font_size.';color:#000000;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:'.$font_size.';color:#000000;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#FFFFFF,#FFFFFF); color:#000000; }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
            if($font_stype != ""){
                echo "<style>body{ font-family: ".$font_stype."; } </style>";
            }
        ?>
    </head>
    <body align="center" id="main_body">
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="20" align="center"><?php echo $row['cdetails']; ?><h5>Sales Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_customersales_masterreport.php" method="post">
            <?php } else { ?>
            <form action="broiler_customersales_masterreport.php?db=<?php echo $db; ?>&userid=<?php echo $user_code; ?>" method="post">
            <?php } ?>
            <form action="broiler_customersales_masterreport.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                    <th colspan="22">
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
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer Line</label>
                                    <select name="cline" id="cline" class="form-control select2">
                                        <option value="all" <?php if($clines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($cline_code as $bcode){ if($cline_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($clines == $bcode){ echo "selected"; } ?>><?php echo $cline_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                  <div class="m-2 form-group">
                                    <label>Region</label>
                                    <select name="regions" id="regions" class="form-control select2">
                                        <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if(!empty($region_name[$rcode])){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($regions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm/Location</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <?php
                                if($me_flag > 0 && $me_size > 0){
                                ?>
                                <div class="m-2 form-group">
                                    <label>Marketing Executive</label>
                                    <select name="mark_exec" id="mark_exec" class="form-control select2">
                                        <option value="all" <?php if($mark_exec == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($me_emp_code as $vcode){ if($me_emp_name[$vcode] != ""){ ?>
                                        <option value="<?php echo $vcode; ?>" <?php if($mark_exec == $vcode){ echo "selected"; } ?>><?php echo $me_emp_name[$vcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <?php
                                }
                                ?>
                                <div class="m-2 form-group">
                                    <label>Category</label>
                                    <select name="item_cat" id="item_cat" class="form-control select2" onChange="fetch_item_list();">
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
                                <?php
                                if($_SERVER['REMOTE_ADDR'] == "49.205.135.183" || $user_name = "paras"){
                                ?>
                                <div class="m-2 form-group">
                                    <label>Font Style</label>
                                    <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                        <option value="" <?php if($font_stype == ""){ echo "selected"; } ?>>-Defalut-</option>
                                        <?php
                                        foreach($font_family_code as $i){
                                        ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if($font_stype == $font_family_name[$i]){ echo "selected"; } ?>><?php echo $font_family_name[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Size</label>
                                    <select name="font_size" id="font_size" class="form-control select2">
                                        <?php
                                        foreach($fsizes as $i){
                                        ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if($font_size == $fsizes[$i]){ echo "selected"; } ?>><?php echo $fsizes[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                }
                                ?>
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
                            if($act_col_numbs[$key_id] == "date" || $nac_col_numbs[$key_id1] == "date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Date</span>'; }
                            else if($act_col_numbs[$key_id] == "vendor_name" || $nac_col_numbs[$key_id1] == "vendor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="vendor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Customer</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_address" || $nac_col_numbs[$key_id1] == "customer_address"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_address" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Address</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_gst_no" || $nac_col_numbs[$key_id1] == "customer_gst_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_gst_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST No</span>'; }
                            else if($act_col_numbs[$key_id] == "transaction_type" || $nac_col_numbs[$key_id1] == "transaction_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="transaction_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Voucher Type</span>'; }
                            else if($act_col_numbs[$key_id] == "trnum" || $nac_col_numbs[$key_id1] == "trnum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="trnum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Invoice</span>'; }
                            else if($act_col_numbs[$key_id] == "book_no" || $nac_col_numbs[$key_id1] == "book_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Dc No.</span>'; }
                            else if($act_col_numbs[$key_id] == "sold_birdsno" || $nac_col_numbs[$key_id1] == "sold_birdsno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdsno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Birds</span>'; }
                            else if($act_col_numbs[$key_id] == "sold_birdswt" || $nac_col_numbs[$key_id1] == "sold_birdswt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_birdswt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Weight</span>'; }
                            else if($act_col_numbs[$key_id] == "avg_bodywt" || $nac_col_numbs[$key_id1] == "avg_bodywt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="avg_bodywt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg Wt</span>'; }
                            else if($act_col_numbs[$key_id] == "sold_perkg_price" || $nac_col_numbs[$key_id1] == "sold_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Rate</span>'; }
                            else if($act_col_numbs[$key_id] == "sold_amount" || $nac_col_numbs[$key_id1] == "sold_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Amount</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_gst_per" || $nac_col_numbs[$key_id1] == "customer_gst_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_gst_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST %</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_gst_amt" || $nac_col_numbs[$key_id1] == "customer_gst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_gst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST Amt</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_cgst_amt" || $nac_col_numbs[$key_id1] == "customer_cgst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_cgst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>CGST Amt</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_sgst_amt" || $nac_col_numbs[$key_id1] == "customer_sgst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_sgst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>SGST Amt</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_igst_amt" || $nac_col_numbs[$key_id1] == "customer_igst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_igst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>IGST Amt</span>'; }
                            else if($act_col_numbs[$key_id] == "tcds_amount" || $nac_col_numbs[$key_id1] == "tcds_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="tcds_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TCS Amount</span>'; }
                            else if($act_col_numbs[$key_id] == "total_sale_amount" || $nac_col_numbs[$key_id1] == "total_sale_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="total_sale_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total Amount</span>'; }
                            else if($act_col_numbs[$key_id] == "cash_receipt_amt" || $nac_col_numbs[$key_id1] == "cash_receipt_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cash_receipt_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cash Receipt</span>'; }
                            else if($act_col_numbs[$key_id] == "bank_receipt_amt" || $nac_col_numbs[$key_id1] == "bank_receipt_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="bank_receipt_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bank Receipt</span>'; }
                            else if($act_col_numbs[$key_id] == "receipt_amount" || $nac_col_numbs[$key_id1] == "receipt_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="receipt_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Receipt Amount</span>'; }
                            else if($act_col_numbs[$key_id] == "branch_name" || $nac_col_numbs[$key_id1] == "branch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch</span>'; }
                            else if($act_col_numbs[$key_id] == "line_name" || $nac_col_numbs[$key_id1] == "line_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Line</span>'; }
                            else if($act_col_numbs[$key_id] == "supervisor_name" || $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
                            else if($act_col_numbs[$key_id] == "farm_name" || $nac_col_numbs[$key_id1] == "farm_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farm_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farm</span>'; }
                            else if($act_col_numbs[$key_id] == "batch_name" || $nac_col_numbs[$key_id1] == "batch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="batch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch</span>'; }
                            else if($act_col_numbs[$key_id] == "mean_age" || $nac_col_numbs[$key_id1] == "mean_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="mean_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mean Age</span>'; }
                            else if($act_col_numbs[$key_id] == "vehicle_no" || $nac_col_numbs[$key_id1] == "vehicle_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="vehicle_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Vehicle</span>'; }
                            else if($act_col_numbs[$key_id] == "driver_name" || $nac_col_numbs[$key_id1] == "driver_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="driver_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Driver</span>'; }
                            else if($act_col_numbs[$key_id] == "remakrs" || $nac_col_numbs[$key_id1] == "remakrs"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="remakrs" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Remarks</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_marketing_executive" || $nac_col_numbs[$key_id1] == "customer_marketing_executive"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_marketing_executive" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Marketing Executive</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_itemname" || $nac_col_numbs[$key_id1] == "customer_itemname"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_itemname" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Name</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_addedemp" || $nac_col_numbs[$key_id1] == "customer_addedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_addedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry By</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_addedtime" || $nac_col_numbs[$key_id1] == "customer_addedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_addedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Entry Time</span>'; }
                            else if($act_col_numbs[$key_id] == "cus_total_weight" || $nac_col_numbs[$key_id1] == "cus_total_weight"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cus_total_weight" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total Weight</span>'; }
                            else if($act_col_numbs[$key_id] == "cus_empty_weight" || $nac_col_numbs[$key_id1] == "cus_empty_weight"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="cus_empty_weight" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Empty Weight</span>'; }
                            else if($act_col_numbs[$key_id] == "day_gain" || $nac_col_numbs[$key_id1] == "day_gain"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="day_gain" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Day Gain</span>'; }
                            else if($act_col_numbs[$key_id] == "daily_prate" || $nac_col_numbs[$key_id1] == "daily_prate"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="daily_prate" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Daily Paper Rate</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_diff_amt" || $nac_col_numbs[$key_id1] == "customer_diff_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_diff_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Diff</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_supervisor_code" || $nac_col_numbs[$key_id1] == "customer_supervisor_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_supervisor_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lifting Supervisor</span>'; }
                            else if($act_col_numbs[$key_id] == "brood_age" || $nac_col_numbs[$key_id1] == "brood_age"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="brood_age" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mean Age</span>'; }
                            else if($act_col_numbs[$key_id] == "sale_amt_wtcds" || $nac_col_numbs[$key_id1] == "sale_amt_wtcds"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sale_amt_wtcds" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Total with TCS</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_ccode" || $nac_col_numbs[$key_id1] == "customer_ccode"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_ccode" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Customer Code</span>'; }
                            else if($act_col_numbs[$key_id] == "customer_sale_image" || $nac_col_numbs[$key_id1] == "customer_sale_image"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_sale_image" onclick="update_masterreport_status(this.id);" '.$checked.'><span>DC Images</span>'; }
                            else if($act_col_numbs[$key_id] == "item_hsncode" || $nac_col_numbs[$key_id1] == "item_hsncode"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="item_hsncode" onclick="update_masterreport_status(this.id);" '.$checked.'><span>HSN Code</span>'; }
                            
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <input type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
        <table id="mine" class="tbl" align="center"  style="width:1300px;">
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                <?php
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if($act_col_numbs[$key_id] == "date"){ echo "<th id='order_date'>Date</th>"; }
                    else if($act_col_numbs[$key_id] == "vendor_name"){ echo "<th id='order'>Customer</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_address"){ echo "<th id='order'>Address</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_gst_no"){ echo "<th id='order'>GST No</th>"; }
                    else if($act_col_numbs[$key_id] == "transaction_type"){ echo "<th id='order'>Voucher Type</th>"; }
                    else if($act_col_numbs[$key_id] == "trnum"){ echo "<th id='order'>Invoice</th>"; }
                    else if($act_col_numbs[$key_id] == "book_no"){ echo "<th id='order'>Dc No.</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th id='order_num'>Birds</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th id='order_num'>Weight</th>"; }
                    else if($act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th id='order_num'>Avg Wt</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<th id='order_num'>Rate</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_amount"){ echo "<th id='order_num'>Amount</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_gst_per"){ echo "<th id='order_num'>GST %</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_gst_amt"){ echo "<th id='order_num'>GST Amt</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_cgst_amt"){ echo "<th id='order_num'>CGST Amt</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_sgst_amt"){ echo "<th id='order_num'>SGST Amt</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_igst_amt"){ echo "<th id='order_num'>IGST Amt</th>"; }
                    else if($act_col_numbs[$key_id] == "tcds_amount"){ echo "<th id='order_num'>TCS Amount</th>"; }
                    else if($act_col_numbs[$key_id] == "total_sale_amount"){ echo "<th id='order_num'>Total Amount</th>"; }
                    else if($act_col_numbs[$key_id] == "cash_receipt_amt"){ echo "<th id='order_num'>Cash Receipt</th>"; }
                    else if($act_col_numbs[$key_id] == "bank_receipt_amt"){ echo "<th id='order_num'>Bank Receipt</th>"; }
                    else if($act_col_numbs[$key_id] == "receipt_amount"){ echo "<th id='order_num'>Receipt Amount</th>"; }
                    else if($act_col_numbs[$key_id] == "branch_name"){ echo "<th id='order'>Branch</th>"; }
                    else if($act_col_numbs[$key_id] == "line_name"){ echo "<th id='order'>Line</th>"; }
                    else if($act_col_numbs[$key_id] == "supervisor_name"){ echo "<th id='order'>Supervisor</th>"; }
                    else if($act_col_numbs[$key_id] == "farm_name"){ echo "<th id='order'>Farm</th>"; }
                    else if($act_col_numbs[$key_id] == "batch_name"){ echo "<th id='order'>Batch</th>"; }
                    else if($act_col_numbs[$key_id] == "mean_age"){ echo "<th id='order_num'>Mean Age</th>"; }
                    else if($act_col_numbs[$key_id] == "vehicle_no"){ echo "<th id='order'>Vehicle</th>"; }
                    else if($act_col_numbs[$key_id] == "driver_name"){ echo "<th id='order'>Driver</th>"; }
                    else if($act_col_numbs[$key_id] == "remakrs"){ echo "<th id='order'>Remarks</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_marketing_executive"){ echo "<th id='order'>Marketing Executive</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_itemname"){ echo "<th id='order'>Item Name</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_addedemp"){ echo "<th id='order'>Entry By</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_addedtime"){ echo "<th id='order'>Entry Time</th>"; }
                    else if($act_col_numbs[$key_id] == "cus_total_weight"){ echo "<th id='order_num'>Total Weight</th>"; }
                    else if($act_col_numbs[$key_id] == "cus_empty_weight"){ echo "<th id='order_num'>Empty Weight</th>"; }
                    else if($act_col_numbs[$key_id] == "day_gain"){ echo "<th id='order_num'>Day Gain</th>"; }
                    else if($act_col_numbs[$key_id] == "daily_prate"){ echo "<th id='order_num'>Daily Paper Rate</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_diff_amt"){ echo "<th id='order_num'>Diff</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_supervisor_code"){ echo "<th id='order'>Lifting Supervisor</th>"; }
                    else if($act_col_numbs[$key_id] == "brood_age"){ echo "<th id='order_num'>Mean Age</th>"; }
                    else if($act_col_numbs[$key_id] == "sale_amt_wtcds"){ echo "<th id='order_num'>Total with TCS</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_ccode"){ echo "<th id='order_num'>Customer Code</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_sale_image"){ echo "<th id='order_num'>DC Images</th>"; }
                    else if($act_col_numbs[$key_id] == "item_hsncode"){ echo "<th id='order_num'>HSN Code</th>"; }
                    else{ }
                }
                ?>
                </tr>
            </thead>
            
            <?php
            if(isset($_POST['submit_report']) == true && $excel_type != "excel"){
            ?>
            <tbody class="tbody1" id="tbody1">
            <?php
                function decimal_adjustments($a,$b){
                    if($a == ""){ $a = 0; } if($b == ""){ $b = 0; }
                    $a = round($a,$b);
                    $c = explode(".",$a);
                    $ed = "";
                    $iv = 0;
                    if($c[1] == ""){ $iv = 0; }
                    else{ $iv = strlen($c[1]); }
                    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
                    if(str_contains($a, '.')){
                        return $a."".$ed;
                    }
                    else if($b > 0){
                        return $a.".".$ed;
                    }
                    else{
                        return $a;
                    }
                }
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%broiler bird%' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $bird_code = array();
                while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
                
                $sql = "SELECT * FROM `company_price_list` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `item_code` = '$bird_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $daily_paper_rate = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['date'];
                    if((float)$row['rate'] > 0){
                        $daily_paper_rate[$key] = (float)$row['rate'];
                    }
                }
                $sql_record = "SELECT date,ccode,SUM(amount) as amount,mode FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vtype` = 'Customer'  AND `active` = '1' AND `dflag` = '0' GROUP BY `date`,`ccode`,`mode` ORDER BY `date`,`ccode` ASC";
                $query = mysqli_query($conn,$sql_record); $receipt_details = $displayed_crcts = $displayed_brcts = $displayed_rcts = $start_date = $end_date = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['date']."@".$row['ccode'];
                    $receipt_details[$key_index] += $row['amount'];
                    if($row['mode'] == "MOD-001"){
                        $cash_receipt_details[$key_index] += $row['amount'];
                    }
                    else{
                        $bank_receipt_details[$key_index] += $row['amount'];
                    }
                }
                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."".$me_filter."".$item_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $batch_list = ""; $old_inv = ""; $dwise_samt = $dwise_rcnt = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_index1 = $row['farm_batch'];
                    if($row['farm_batch'] == "" || $row['farm_batch'] == "select"){ }
                    else{
                        if($batch_list == ""){ $batch_list = $row['farm_batch']; } else{ $batch_list = $batch_list."','".$row['farm_batch']; }
                        $batches[$key_index1] = $key_index1;
                        if($start_date[$key_index1] == ""){ $start_date[$key_index1] = $row['date']; } else{ if(strtotime($start_date[$key_index1]) >= strtotime($row['date'])){ $start_date[$key_index1] = $row['date']; } }
                        if($end_date[$key_index1] == ""){ $end_date[$key_index1] = $row['date']; } else{ if(strtotime($end_date[$key_index1]) <= strtotime($row['date'])){ $end_date[$key_index1] = $row['date']; } }
                    }
                    //Date Wise Customer Sale Amount
                    $key = $row['date']."@".$row['vcode'];
                    if($old_inv != $row['trnum']){
                        $old_inv = $row['trnum'];
                        $dwise_samt[$key] += (float)$row['finl_amt'];
                    }
                    $dwise_rcnt[$key] += 1;
                }

                $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,date,farm_batch FROM `broiler_sales` WHERE `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `date`,`farm_batch` ORDER BY `date`,`farm_batch` ASC";
                $query = mysqli_query($conn,$sql_record); $datewise_sale = $cum_sale = $tilldate_sale = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['date']."@".$row['farm_batch']; $key_index1 = $row['farm_batch'];
                    if(!empty($datewise_sale[$key_index])){ $datewise_sale[$key_index] = $datewise_sale[$key_index] + ($row['birds']); }
                    else{ $datewise_sale[$key_index] = ($row['birds']); }

                    if(!empty($cum_sale[$key_index1])){ $cum_sale[$key_index1] = $cum_sale[$key_index1] + ($row['birds']); }
                    else{ $cum_sale[$key_index1] = ($row['birds']); }

                    $tilldate_sale[$key_index] = $cum_sale[$key_index1];

                }
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_index = $row['date']."@".$row['batch_code']; $key_index1 = $row['batch_code'];
                    $day_ages[$key_index] = $row['brood_age'];
                    $batches[$key_index1] = $key_index1;

                    if($start_date[$key_index1] == ""){ $start_date[$key_index1] = $row['date']; } else{ if(strtotime($start_date[$key_index1]) >= strtotime($row['date'])){ $start_date[$key_index1] = $row['date']; } }
                    if($end_date[$key_index1] == ""){ $end_date[$key_index1] = $row['date']; } else{ if(strtotime($end_date[$key_index1]) <= strtotime($row['date'])){ $end_date[$key_index1] = $row['date']; } }
                    if($dend_date[$key_index1] == ""){ $dend_date[$key_index1] = $row['date']; } else{ if(strtotime($dend_date[$key_index1]) <= strtotime($row['date'])){ $dend_date[$key_index1] = $row['date']; } }
                }
                //Mean Age Calculation
                foreach($batches as $bhcode){
                    $cum_age = 0;
                    for($currentDate = (strtotime($start_date[$bhcode])); $currentDate <= (strtotime($end_date[$bhcode])); $currentDate += (86400)){
                        $active_date = date("Y-m-d",((int)$currentDate)); $key_index = $active_date."@".$bhcode;
                        if(number_format_ind($day_ages[$key_index]) == "0.00"){
                            if(strtotime($end_date[$bhcode]) > strtotime($dend_date[$bhcode])){
                                $datediff = round((strtotime($end_date[$bhcode]) - strtotime($dend_date[$bhcode]))/ (60 * 60 * 24));
                                $day_ages[$key_index] = $day_ages[$dend_date[$bhcode]."@".$bhcode] + $datediff;
                            }
                        }
                        if(!empty($datewise_sale[$key_index]) && !empty($tilldate_sale[$key_index]) && !empty($day_ages[$key_index])){
                            if(number_format_ind($datewise_sale[$key_index]) != "0.00" && number_format_ind($tilldate_sale[$key_index]) != "0.00" && number_format_ind($day_ages[$key_index]) != "0.00"){
                                $cum_age = $cum_age + ($day_ages[$key_index] * $datewise_sale[$key_index]);
                                if($cum_age > 0 && $tilldate_sale[$key_index] > 0){
                                    $mean_ages[$key_index] = $cum_age / $tilldate_sale[$key_index];
                                }
                                else{
                                    $mean_ages[$key_index] = 0;
                                }
                            }
                            else{ $mean_ages[$key_index] = 0; }
                        }
                        else{ $mean_ages[$key_index] = 0; }
                    }
                }
                
                $tot_gross_weight = $tot_tare_weight = $mage_trow = $tamt_wtcs = 0;
                if($daf_flag == 1){ $sort_order = " ORDER BY `date`,`vcode`,`trnum` ASC"; }
                else{ $sort_order = " ORDER BY `date`,`trnum` ASC"; }
                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND warehouse NOT IN (SELECT code FROM `inv_sectors` WHERE description LIKE '%processing%')".$vendor_filter."".$me_filter."".$item_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0'".$sort_order;
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = $ftot_amt = $ftcds_amt = $tot_rct_amt = $tmage = $tmcnt = $td_prate = $tdp_cnt = 0; $old_inv = $old_date = $odiff_key = "";
                while($row = mysqli_fetch_assoc($query)){
                    $customer_gst_per = $row['gst_per'];
                    $customer_gst_amt = $row['gst_amt'];
                    //$tot_amt = $tot_amt + $row['item_tamt'];
                    $tot_bds = $tot_bds + $row['birds'];
                    $tot_qty = $tot_qty + $row['rcd_qty'] + $row['fre_qty'];
                    $tot_amt = $tot_amt + $row['item_tamt'];
                    $customer_cgst_amt = $customer_sgst_amt = $customer_igst_amt = 0;
                    if($gst_isflag[$row['gst_per']] == 1 || $gst_isflag[$row['gst_per']] == '1'){
                        $customer_igst_amt = $customer_gst_amt;
                    }
                    else{
                        $gamt = 0; $gamt = round(($customer_gst_amt / 2),2);
                        $customer_cgst_amt = $customer_sgst_amt = $gamt;
                    }
                    $tot_customer_gst_per += (float)$customer_gst_per;
                    $tot_customer_gst_amt += (float)$customer_gst_amt;
                    $tot_customer_cgst_amt += (float)$customer_cgst_amt;
                    $tot_customer_sgst_amt += (float)$customer_sgst_amt;
                    $tot_customer_igst_amt += (float)$customer_igst_amt;

                    $tot_gross_weight += (float)$row['gross_weight'];
                    $tot_tare_weight += (float)$row['tare_weight'];

                    $avg_swt = $day_gain = $brood_age = 0;
                    if((float)$row['birds'] != 0){ $avg_swt = (($row['rcd_qty'] + $row['fre_qty']) / $row['birds']); }
                    $brood_age = $day_ages[$row['date']."@".$row['farm_batch']];
                    $mage = $mean_ages[$row['date']."@".$row['farm_batch']];
                    if($mage > 0){ $day_gain = round((((float)$avg_swt * 1000) / (float)$mage),2); }

                    if((float)$mage > 0){
                        $tmage += (float)$mage;
                        $tmcnt++;
                    }

                    $mage_trow += ((float)$row['birds'] * (float)$brood_age);
                    $key2 = $row['date'];
                    if(empty($daily_paper_rate[$key2]) || $daily_paper_rate[$key2] == ""){ $daily_prate = 0; }
                    else{
                        $daily_prate = $daily_paper_rate[$key2];
                    }
                    if($old_date != $key2){
                        $old_date = $key2;
                        $td_prate += (float)$daily_prate;
                        $tdp_cnt++;
                    }

                    if($old_inv != $row['trnum']){
                        $ftcds_amt += (float)$row['tcds_amt'];
                        $ftot_amt += (float)$row['finl_amt'];
                        $old_inv = $row['trnum'];
                    }
                    
                    $key2 = $row['date']."@".$row['vcode']; $diff_amt = $diff_flag = 0; $drc_no = 1;
                    if($odiff_key != $key2){
                        $odiff_key = $key2; $diff_flag = 1;
                        if(empty($receipt_details[$key2]) || $receipt_details[$key2] == ""){ $receipt_details[$key2] = 0; }
                        if(empty($dwise_samt[$key2]) || $dwise_samt[$key2] == ""){ $dwise_samt[$key2] = 0; }
                        if(empty($dwise_rcnt[$key2]) || $dwise_rcnt[$key2] == ""){ $dwise_rcnt[$key2] = 1; }
                        $drc_no = $dwise_rcnt[$key2];
                        $diff_amt = ((float)$dwise_samt[$key2] - (float)$receipt_details[$key2]);
                    }

                    //DC Images
                    $customer_sale_image ="";
                    $fia = $row['sale_image'];
                    if($fia != ""){ $customer_sale_image = "https://broiler.poulsoft.org".$fia; }
                    //Total with TCS
                    $tamt_wtcs += ((float)$row['item_tamt'] + (float)$row['tcds_amt']);
                    echo "<tr>";
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                        if($act_col_numbs[$key_id] == "date"){ echo "<td title='Date'>".date('d.m.Y',strtotime($row['date']))."</td>"; }
                        else if($act_col_numbs[$key_id] == "vendor_name"){ if(!empty($vendor_name[$row['vcode']])){ echo "<td title='Customer'>".$vendor_name[$row['vcode']]."</td>"; } else{ echo "<td title='Customer'>".$farm_name[$row['warehouse']]."</td>"; } }
                        else if($act_col_numbs[$key_id] == "customer_address"){ echo "<td title='Customer'>".$vendor_addr[$row['vcode']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_gst_no"){ echo "<td title='Customer'>".$vendor_gstno[$row['vcode']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "transaction_type"){ echo "<td title='Customer'>Sale Invoice</td>"; }
                        else if($act_col_numbs[$key_id] == "trnum"){ echo "<td title='Invoice'>".$row['trnum']."</td>"; }
                        else if($act_col_numbs[$key_id] == "book_no"){ echo "<td title='Dc No.'>".$row['billno']."</td>"; }
                        else if($act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; }
                        else if($act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Weight' style='text-align:right;'>".number_format_ind($row['rcd_qty'] + $row['fre_qty'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "avg_bodywt"){
                            if(($row['rcd_qty'] + $row['fre_qty']) > 0 && $row['birds'] > 0){
                                echo "<td title='Avg Wt' style='text-align:right;'>".decimal_adjustments((($row['rcd_qty'] + $row['fre_qty']) / $row['birds']),3)."</td>";
                            }
                            else{
                                echo "<td title='Avg Wt' style='text-align:right;'>".decimal_adjustments(0,3)."</td>";
                            }
                            
                        }
                        else if($act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<td title='Rate' style='text-align:right;'>".number_format_ind($row['rate'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "sold_amount"){ echo "<td title='Amount' style='text-align:right;'>".number_format_ind($row['rcd_qty'] * $row['rate'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_gst_per"){ echo "<td title='GST %' style='text-align:right;'>".number_format_ind($customer_gst_per)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_gst_amt"){ echo "<td title='GST Amt' style='text-align:right;'>".number_format_ind($customer_gst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_cgst_amt"){ echo "<td title='CGST Amt' style='text-align:right;'>".number_format_ind($customer_cgst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_sgst_amt"){ echo "<td title='SGST Amt' style='text-align:right;'>".number_format_ind($customer_sgst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_igst_amt"){ echo "<td title='IGST Amt' style='text-align:right;'>".number_format_ind($customer_igst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "tcds_amount"){ echo "<td title='TCS Amount' style='text-align:right;'>".number_format_ind($row['tcds_amt'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "total_sale_amount"){ echo "<td title='Total Amount' style='text-align:right;'>".number_format_ind($row['item_tamt'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "cash_receipt_amt"){
                            $key_index = $row['date']."@".$row['vcode'];
                            if(!empty($cash_receipt_details[$key_index]) || number_format_ind($cash_receipt_details[$key_index]) != "0.00"){
                                if(!empty($displayed_crcts[$key_index])){ echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>"; }
                                else{
                                    $displayed_crcts[$key_index] = $cash_receipt_details[$key_index];
                                    $tot_crct_amt = $tot_crct_amt + $cash_receipt_details[$key_index];
                                    echo "<td title='Receipt Amount' style='text-align:right;'>".number_format_ind($cash_receipt_details[$key_index])."</td>";
                                }
                            }
                            else{ echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>"; }
                        }
                        else if($act_col_numbs[$key_id] == "bank_receipt_amt"){
                            $key_index = $row['date']."@".$row['vcode'];
                            if(!empty($bank_receipt_details[$key_index]) || number_format_ind($bank_receipt_details[$key_index]) != "0.00"){
                                if(!empty($displayed_brcts[$key_index])){ echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>"; }
                                else{
                                    $displayed_brcts[$key_index] = $bank_receipt_details[$key_index];
                                    $tot_brct_amt = $tot_brct_amt + $bank_receipt_details[$key_index];
                                    echo "<td title='Receipt Amount' style='text-align:right;'>".number_format_ind($bank_receipt_details[$key_index])."</td>";
                                }
                            }
                            else{ echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>"; }
                        }
                        else if($act_col_numbs[$key_id] == "receipt_amount"){
                            $key_index = $row['date']."@".$row['vcode'];
                            if(!empty($receipt_details[$key_index]) || number_format_ind($receipt_details[$key_index]) != "0.00"){
                                if(!empty($displayed_rcts[$key_index])){ echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>"; }
                                else{
                                    $displayed_rcts[$key_index] = $receipt_details[$key_index];
                                    $tot_rct_amt = $tot_rct_amt + $receipt_details[$key_index];
                                    echo "<td title='Receipt Amount' style='text-align:right;'>".number_format_ind($receipt_details[$key_index])."</td>";
                                }
                            }
                            else{ echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>"; }
                        }
                        else if($act_col_numbs[$key_id] == "branch_name"){ echo "<td title='Branch'>".$branch_name[$farm_branch[$row['warehouse']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "line_name"){ echo "<td title='Line'>".$line_name[$farm_line[$row['warehouse']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor'>".$supervisor_name[$farm_supervisor[$row['warehouse']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farm'>".$sector_name[$row['warehouse']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "batch_name"){ echo "<td title='Batch'>".$batch_name[$row['farm_batch']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "mean_age"){ echo "<td title='Mean Age' style='text-align:right;'>".number_format_ind($mean_ages[$row['date']."@".$row['farm_batch']])."</td>"; }
                        else if($act_col_numbs[$key_id] == "vehicle_no"){ if(!empty($vehicle_name[$row['vehicle_code']])){ echo "<td title='Vehicle'>".$vehicle_name[$row['vehicle_code']]."</td>"; } else{ echo "<td title='Vehicle'>".$row['vehicle_code']."</td>"; } }
                        else if($act_col_numbs[$key_id] == "driver_name"){ if(!empty($supervisor_name[$row['driver_code']])){ echo "<td title='Driver'>".$supervisor_name[$row['driver_code']]."</td>"; } else if($row['driver_code'] == "select"){ echo "<td title='Driver'></td>"; } else{ echo "<td title='Driver'>".$row['driver_code']."</td>"; } }
                        else if($act_col_numbs[$key_id] == "remakrs"){ echo "<td title='Remarks'>".$row['remarks']."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_marketing_executive"){ echo "<td title='Marketing Executive'>".$me_emp_name[$row['marketing_executive']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_itemname"){ echo "<td title='Item Name'>".$item_name[$row['icode']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_addedemp"){ echo "<td title='Entry By'>".$supervisor_name[$emp_db_code[$row['addedemp']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_addedtime"){ echo "<td title='Entry By'>".date("d.m.Y h:i:sA",strtotime($row['addedtime']))."</td>"; }
                        else if($act_col_numbs[$key_id] == "cus_total_weight"){ echo "<td title='Total Weight' style='text-align:right;'>".number_format_ind($row['gross_weight'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "cus_empty_weight"){ echo "<td title='Empty Weight' style='text-align:right;'>".number_format_ind($row['tare_weight'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "day_gain"){ echo "<td title='Day Gain' style='text-align:right;'>".number_format_ind($day_gain)."</td>"; }
                        else if($act_col_numbs[$key_id] == "daily_prate"){ echo "<td title='Daily Paper Rate' style='text-align:right;'>".number_format_ind($daily_prate)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_diff_amt" && $diff_flag == 1){ echo "<td title='Diff' style='text-align:right;' rowspan=".$drc_no.">".number_format_ind($diff_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_supervisor_code"){
                            if(empty($supervisor_name[$row['lift_supervisor_code']]) || $supervisor_name[$row['lift_supervisor_code']] == ""){
                                if(empty($supervisor_name[$row['supervisor_code']]) || $supervisor_name[$row['supervisor_code']] == ""){
                                    $sname = "";
                                }
                                else{
                                    $sname = $supervisor_name[$row['supervisor_code']];
                                }
                            }
                            else{
                                $sname = $supervisor_name[$row['lift_supervisor_code']];
                            }
                            echo "<td title='Lifting Supervisor' style='text-align:right;'>".$sname."</td>";

                        }
                        else if($act_col_numbs[$key_id] == "brood_age"){ echo "<td title='Mean Age' style='text-align:right;'>".str_replace(".00","",number_format_ind($brood_age))."</td>"; }
                        else if($act_col_numbs[$key_id] == "sale_amt_wtcds"){ echo "<td title='Total with TCS' style='text-align:right;'>".number_format_ind($row['item_tamt'] + $row['tcds_amt'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_ccode"){ echo "<td title='Customer Code'>".$vendor_ccode[$row['vcode']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_sale_image"){
                            if($customer_sale_image != ""){
                            ?><td><a href="javascript:void(0)" onclick="openImage('<?php echo $customer_sale_image; ?>')">DC-Img</a></td><?php 
                            }
                            else{
                                echo "<td></td>";
                            }
                        }
                        else if($act_col_numbs[$key_id] == "item_hsncode"){ echo "<td title='HSN Code'>".$item_hsn[$row['icode']]."</td>"; }
                        else{ }
                    }
                    echo "</tr>";
                }
                /*
                $sql_record = "SELECT * FROM `broiler_bird_transferout` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$item_filter2."".$sector_filter2." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $customer_gst_per = 0;
                    $customer_gst_amt = 0;
                    //$tot_amt = $tot_amt + $row['item_tamt'];
                    $tot_bds = $tot_bds + $row['birds'];
                    $tot_qty = $tot_qty + $row['weight'];
                    $tot_amt = $tot_amt + $row['avg_amount'];
                    $customer_cgst_amt = $customer_sgst_amt = $customer_igst_amt = 0;
                    $tot_customer_gst_per += (float)$customer_gst_per;
                    $tot_customer_gst_amt += (float)$customer_gst_amt;
                    $tot_customer_cgst_amt += (float)$customer_cgst_amt;
                    $tot_customer_sgst_amt += (float)$customer_sgst_amt;
                    $tot_customer_igst_amt += (float)$customer_igst_amt;
                    echo "<tr>";
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                        if($act_col_numbs[$key_id] == "date"){ echo "<td title='Date'>".date('d.m.Y',strtotime($row['date']))."</td>"; }
                        else if($act_col_numbs[$key_id] == "vendor_name"){ if(!empty($vendor_name[$row['vcode']])){ echo "<td title='Customer'>".$sector_name[$row['fromwarehouse']]."</td>"; } else{ echo "<td title='Customer'>".$farm_name[$row['warehouse']]."</td>"; } }
                        else if($act_col_numbs[$key_id] == "customer_address"){ echo "<td title='Customer'></td>"; }
                        else if($act_col_numbs[$key_id] == "customer_gst_no"){ echo "<td title='Customer'></td>"; }
                        else if($act_col_numbs[$key_id] == "transaction_type"){ echo "<td title='Customer'>Bird Sending</td>"; }
                        else if($act_col_numbs[$key_id] == "trnum"){ echo "<td title='Invoice'>".$row['trnum']."</td>"; }
                        else if($act_col_numbs[$key_id] == "book_no"){ echo "<td title='Dc No.'>".$row['bs_no']."</td>"; }
                        else if($act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Birds' style='text-align:right;'>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; }
                        else if($act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Weight' style='text-align:right;'>".number_format_ind($row['weight'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "avg_bodywt"){
                            if(($row['weight']) > 0 && $row['birds'] > 0){
                                echo "<td title='Avg Wt' style='text-align:right;'>".number_format_ind(($row['weight']) / $row['birds'])."</td>";
                            }
                            else{
                                echo "<td title='Avg Wt' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                            
                        }
                        else if($act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<td title='Rate' style='text-align:right;'>".number_format_ind($row['avg_price'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "sold_amount"){ echo "<td title='Amount' style='text-align:right;'>".number_format_ind($row['weight'] * $row['avg_price'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_gst_per"){ echo "<td title='GST %' style='text-align:right;'>".number_format_ind($customer_gst_per)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_gst_amt"){ echo "<td title='GST Amt' style='text-align:right;'>".number_format_ind($customer_gst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_cgst_amt"){ echo "<td title='CGST Amt' style='text-align:right;'>".number_format_ind($customer_cgst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_sgst_amt"){ echo "<td title='SGST Amt' style='text-align:right;'>".number_format_ind($customer_sgst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_igst_amt"){ echo "<td title='IGST Amt' style='text-align:right;'>".number_format_ind($customer_igst_amt)."</td>"; }
                        else if($act_col_numbs[$key_id] == "tcds_amount"){ echo "<td title='TCS Amount' style='text-align:right;'>".number_format_ind(0)."</td>"; }
                        else if($act_col_numbs[$key_id] == "total_sale_amount"){ echo "<td title='Total Amount' style='text-align:right;'>".number_format_ind($row['avg_amount'])."</td>"; }
                        else if($act_col_numbs[$key_id] == "cash_receipt_amt"){
                            echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>";
                        }
                        else if($act_col_numbs[$key_id] == "bank_receipt_amt"){
                            echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>";
                        }
                        else if($act_col_numbs[$key_id] == "receipt_amount"){
                            echo "<td title='Receipt Amount' style='text-align:right;'>0.00</td>";
                        }
                        else if($act_col_numbs[$key_id] == "branch_name"){ echo "<td title='Branch'>".$branch_name[$farm_branch[$row['fromwarehouse']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "line_name"){ echo "<td title='Line'>".$line_name[$farm_line[$row['fromwarehouse']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor'>".$supervisor_name[$farm_supervisor[$row['fromwarehouse']]]."</td>"; }
                        else if($act_col_numbs[$key_id] == "farm_name"){ echo "<td title='Farm'>".$sector_name[$row['fromwarehouse']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "batch_name"){ echo "<td title='Batch'>".$batch_name[$row['from_batch']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "mean_age"){ echo "<td title='Mean Age'>".number_format_ind($mean_ages[$row['date']."@".$row['from_batch']])."</td>"; }
                        else if($act_col_numbs[$key_id] == "vehicle_no"){ if(!empty($vehicle_name[$row['vehicle_code']])){ echo "<td title='Vehicle'>".$vehicle_name[$row['vehicle_code']]."</td>"; } else{ echo "<td title='Vehicle'>".$row['vehicle_code']."</td>"; } }
                        else if($act_col_numbs[$key_id] == "driver_name"){ if(!empty($supervisor_name[$row['driver_code']])){ echo "<td title='Driver'>".$supervisor_name[$row['driver_code']]."</td>"; } else if($row['driver_code'] == "select"){ echo "<td title='Driver'></td>"; } else{ echo "<td title='Driver'>".$row['driver_code']."</td>"; } }
                        else if($act_col_numbs[$key_id] == "remakrs"){ echo "<td title='Remarks'>".$row['remarks']."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_marketing_executive"){ echo "<td title='Marketing Executive'>".$me_emp_name[$row['marketing_executive']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_itemname"){ echo "<td title='Item Name'>".$item_name[$row['item_code']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "customer_addedemp"){ echo "<td title='Item Name'>".$supervisor_name[$emp_db_code[$row['addedemp']]]."</td>"; }
                        else{ }
                    }
                    echo "</tr>";
                    $ftot_amt += (float)$row['avg_amount'];
                }*/
                if($tot_qty != 0){
                    $avg_price = round(($tot_amt / $tot_qty),2);
                }
                else{
                    $avg_price = 0;
                }
                if($tot_bds != 0){
                    $avg_wt = round(($tot_qty / $tot_bds),3);
                }
                else{
                    $avg_wt = 0;
                }
                
                if($tmcnt != 0){
                    $amage = round(($tmage / $tmcnt),2);
                }
                else{
                    $amage = 0;
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                if((float)$tot_bds != 0){ $avg_mage = round((((float)$mage_trow / (float)$tot_bds)),2); } else{ $avg_mage = 0; }
                if($dbname == "poulso6_broiler_wb_sahapoultry"){
                    if((float)$amage != 0){ $avg_dgain = round((((float)$avg_wt / (float)$avg_mage) * 1000),2); } else{ $avg_dgain = 0; }
                }
                else{
                    if((float)$amage != 0){ $avg_dgain = round((((float)$avg_wt / (float)$amage) * 1000),2); } else{ $avg_dgain = 0; }
                }
                
                if((float)$tdp_cnt != 0){ $avg_dprate = round(((float)$td_prate / (float)$tdp_cnt),2); } else{ $avg_dprate = 0; }
                echo "<tr class='thead4'>";
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if($act_col_numbs[$key_id] == "date"){ echo "<th style='text-align:left; border-right: 0px;'></th>"; }
                    else if($act_col_numbs[$key_id] == "vendor_name"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_address"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_gst_no"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if($act_col_numbs[$key_id] == "transaction_type"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if($act_col_numbs[$key_id] == "trnum"){ echo "<th style='text-align:left; border-left: 0px;border-right: 0px;'></th>"; }
                    else if($act_col_numbs[$key_id] == "book_no"){ echo "<th style='text-align:center; border-left: 0px;'>Total</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th style='text-align:right;'>".str_replace('.00','',number_format_ind(round($tot_bds,2)))."</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_qty,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th style='text-align:right;'>".decimal_adjustments($avg_wt,3)."</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<th style='text-align:right;'>".number_format_ind(round($avg_price,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "sold_amount"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_gst_per"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_gst_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_customer_gst_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_cgst_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_customer_cgst_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_sgst_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_customer_sgst_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_igst_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_customer_igst_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "tcds_amount"){ echo "<th style='text-align:right;'>".number_format_ind(round($ftcds_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "total_sale_amount"){ echo "<th style='text-align:right;'>".number_format_ind(round($ftot_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "cash_receipt_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_crct_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "bank_receipt_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_brct_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "receipt_amount"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_rct_amt,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "branch_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "line_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "supervisor_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "farm_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "batch_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "mean_age"){ echo "<th style='text-align:right;'>".number_format_ind(round($amage,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "vehicle_no"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "driver_name"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "remakrs"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_marketing_executive"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_itemname"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_addedemp"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_addedtime"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "cus_total_weight"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_gross_weight,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "cus_empty_weight"){ echo "<th style='text-align:right;'>".number_format_ind(round($tot_tare_weight,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "day_gain"){ echo "<th style='text-align:right;'>".number_format_ind(round($avg_dgain,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "daily_prate"){ echo "<th style='text-align:right;'>".number_format_ind(round($avg_dprate,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_diff_amt"){ echo "<th style='text-align:right;'>".number_format_ind(round(((float)$ftot_amt - (float)$tot_rct_amt),2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_supervisor_code"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "brood_age"){ echo "<th style='text-align:right;'>".number_format_ind(round($avg_mage,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "sale_amt_wtcds"){ echo "<th style='text-align:right;'>".number_format_ind(round($tamt_wtcs,2))."</th>"; }
                    else if($act_col_numbs[$key_id] == "customer_ccode"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "customer_sale_image"){ echo "<th style='text-align:right;'></th>"; }
                    else if($act_col_numbs[$key_id] == "item_hsncode"){ echo "<th style='text-align:right;'></th>"; }
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
            function update_font_family(){
                var font_stype = document.getElementById("font_stype").value;
                document.getElementById("main_body").style.fontFamily = font_stype;

                //var font_size = document.getElementById("font_size").value;
                //document.getElementById("tbody1").style.fontSize = font_size;
                
                /*var elements = document.getElementsByClassName("tbody1");
                for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    element.style.fontSize = font_size+"px";
                }*/
            }
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
                    //slnos();
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
        <script>
        function openImage(url) {
            window.open(url, '_blank');
        }
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>