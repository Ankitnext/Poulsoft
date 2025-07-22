<?php
//broiler_supervisorwiserealize_masterreport.php
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
    global $page_title; $page_title = "Supervisor Wise Realization Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Supervisor Wise Realization Report";
    include "header_head.php";
    $user_code = $_GET['userid'];
}
include "decimal_adjustments.php";

if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; }

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); 
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}

//$field_href[0] = "broiler_supervisorwiserealize_masterreport.php";
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = $nac_col_numbs = array(); $key_id = "";
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] != 0){ $key_id = $row2[$cna]; $act_col_numbs[$key_id] = $cna; $key_id."-".$act_col_numbs[$key_id]; }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] != 0){ $key_id = $row2[$cna]; $nac_col_numbs[$key_id] = $cna; }
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

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}

$farm_list = implode("','", $farm_code);
$batch_code = $batch_name = $batch_book = $batch_gcflag = array();
$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `farm_code` IN ('$farm_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$branch_list = implode("','", $farm_branch);
$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `code` IN ('$branch_list')".$branch_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }

$line_list = implode("','", $farm_line);
$line_code = $line_name = $line_branch = array();
$sql = "SELECT * FROM `location_line` WHERE `active` = '1' AND `code` IN ('$line_list')".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$bstd_body_weight = $bstd_daily_gain = $bstd_avg_daily_gain = $bstd_fcr = $bstd_cum_feed = array();
$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; }

$emp_list = implode("','", $farm_supervisor);
$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' AND `code` IN ('$emp_list')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_employee` "; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code_display[$row['code']] = $row['code']; $supervisor_name_display[$row['code']] = $row['name']; }

$chick_code = "";
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

/* admin cost include flag check*/
$sql3 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Farmwise Realization' AND `field_function` LIKE 'Include Admin Cost'  AND (`user_access` LIKE '%$addedemp%' OR `user_access` = 'all')";
$query3 = mysqli_query($conn, $sql3); $ccount3 = mysqli_num_rows($query3);
if($ccount3 > 0){ while($row3 = mysqli_fetch_assoc($query3)){ $admincost_include_flag = $row3['flag']; } }
else{ mysqli_query($conn, "INSERT INTO `extra_access` ( `field_name`, `field_function`, `user_access`, `flag`) VALUES ( 'Farmwise Realization', 'Include Admin Cost', 'all', '1')"); $admincost_include_flag =  1; }
if($admincost_include_flag == ''){ $admincost_include_flag =  0; }

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display"; $report_view = "hd";
if(isset($_REQUEST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_REQUEST['branches'];
    $lines = $_REQUEST['lines'];
    $supervisors = $_REQUEST['supervisors'];
    $report_view = $_REQUEST['report_view'];
    
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
    //$url = "../PHPExcel/Examples/BroilerWeeklyReport-Excel.php?branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms;
}
if(isset($_POST['submit_report']) == true){
    
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    if ($export_fdate == $export_tdate)
    {$filename = "Supervisor Wise Realization_".$export_tdate; }
    else {
    $filename = "Supervisor Wise Realization_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];

    $esupervisors = $_POST['supervisors'];
    //$export_supervisors = $supervisor_name[$_POST['supervisors']];
    $export_report_view = $POST['report_view'];
    
    if ( $esupervisors == "" || $esupervisors == "all") { $esupervisors = "All"; }
    else {  $esupervisors = $supervisor_name[$_POST['supervisors']]; }
    if ( $export_report_view == "" || $export_report_view == "hd") { $export_report_view = "Housed Date"; }
    else if ( $export_report_view == "ld") { $export_report_view = "Liquidation Date"; }
    else if ( $export_report_view == "gd") { $export_report_view = "GC Saved Date"; }
    

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
                .tbody1 tr td, .tfoot1 tr th { text-align:right; }
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
                .tbody1 tr td, .tfoot1 tr th { text-align:right; }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Supervisor Wise Realization</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_supervisorwiserealize_masterreport.php" method="post">
            <?php } else { ?>
            <form action="broiler_supervisorwiserealize_masterreport.php?db=<?php echo $db; ?>" method="post">
            <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Report View</label>
                                    <select name="report_view" id="report_view" class="form-control select2">
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
                                    <select name="supervisors" id="supervisors" class="form-control select2" >
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $bcode){ if(!empty($supervisor_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($supervisors == $bcode){ echo "selected"; } ?>><?php echo $supervisor_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('mine', 'Supervisor Wise Realization_','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "supervisor_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="supervisor_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor</span>'; }
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
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_medicine_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_medicine_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Medicine Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_medicine_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_medicine_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Medicine Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_admin_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_admin_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Admin Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_admin_swtprc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_admin_swtprc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Admin Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_admin_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_admin_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Std Admin Cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_production_amount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_production_amount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Farmer P.cost</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_prodperkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_prodperkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>F PC/Kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "std_gc_perkg"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="std_gc_perkg" onclick="update_masterreport_status(this.id);" '.$checked.'><span>STD.Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_incentive"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_incentive" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Incentives</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_decentives"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_decentives" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Decentives</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_gc_perkg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_gc_perkg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "farmer_gc_perkg_price2"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="farmer_gc_perkg_price2" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Gc/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "rearing_charges"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="rearing_charges" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Rearing Charges</span>'; }
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
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_batch_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "sold_batch_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sold_batch_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>No. of Batches Sold</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "gc_sale_inc_prc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="gc_sale_inc_prc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Incentives/kg</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "approved_gc_prc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="approved_gc_prc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>PC Incentive Rate</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "lifting_efficiency"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="lifting_efficiency" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Lifting Efficiency</span>'; }
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            
            </div>
        <table id="mine" class="tbl" align="center"  style="width:1300px;">
        <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
          
                <tr align="center">
                    <th colspan="14" align="center"><?php echo $row['cdetails']; ?><h5>Supervisor Wise Realization</h5></th>
                </tr>
            
            <?php } ?>
            <tr>
                       
                       <th colspan="14">
                                   <div class="row">
                                    <div class="m-2 form-group">
                                           <label>Report View: <?php echo $export_report_view; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                       
                                       <div class="m-2 form-group">
                                           <label>Supervisor: <?php echo $esupervisors; ?></label>
                                           
                                       </div>
                                       
                                      <div class="m-2 form-group">
                                           <label><br/></label>
                   
                                       </div>
                                       
                               </th>
                           
                       </tr>
       
            
            </thead>
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                <?php
                $theadc = 0;
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<th id='order_num'>Sl.No</th>"; $theadc++; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<th id='order'>Supervisor</th>"; $theadc++; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<th id='order_num'>Mean Age</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th id='order_num'>Chicks Placement</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th id='order_num'>Mortality</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<th id='order_num'>Mortality%</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count"){ echo "<th id='order_num'>1st week Mort</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per"){ echo "<th id='order_num'>1st week Mort%</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count"){ echo "<th id='order_num'>Up to 30days Mort</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per"){ echo "<th id='order_num'>Up to 30days Mort%</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count"){ echo "<th id='order_num'>After 30days Mort</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per"){ echo "<th id='order_num'>After 30days Mort%</th>"; }
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
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price"){ echo "<th id='order_num'>Std Chick Price</th>"; }
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
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive"){ echo "<th id='order_num'>Incentives</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives"){ echo "<th id='order_num'>Decentives</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price"){ echo "<th id='order_num'>Gc/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2"){ echo "<th id='order_num'>Gc/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges"){ echo "<th id='order_num'>Rearing Charges</th>"; }
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
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_batch_count"){ echo "<th id='order_num'>No. of Batches Sold</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc"){ echo "<th id='order_num'>Sale Incentives/kg</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc"){ echo "<th id='order_num'>PC Incentive Rate</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency"){ echo "<th id='order_num'>Lifting Efficiency</th>"; }
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

                $batch_arr_list = $batch_supervisor = $farmcount = $supervisor_arr_codes = $mage = $chick_placed = $morta_count = $mort7_count = $mort30_count = $mort31g_count = $shortage = 
                $excess = $feed_con_kgs = $std_feed_perbird = $act_feed_perbird = $std_fcr = $act_fcr = $act_cfcr = $act_dgain = $act_eef = $act_sbirds = $act_sweight = 
                $act_samount = $std_chick_amt = $std_feed_amt = $std_medvac_amt = $std_admin_amt = $fmr_prod_amt = $std_gc_amt = $act_gc_amt = $fmr_incentive_amt = $fmr_decentive_amt = $rc_amt = 
                $trc_amt = $fmr_tds_amt = $fmr_odeduct_amt = $fmr_pay_amt = $act_chick_amt = $act_feed_amt = $act_medvac_amt = $act_admin_amt = $fmr_gcpay_amt = $act_prod_amt = 
                $pl_amt = $branch_batch_count = array();

                $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `active` = '1'".$date_filter."".$farm_query." AND `dflag` = '0' ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $batch_arr_list[$row['batch_code']] = $row['batch_code']; }

                $batch_list = "";$batch_list = implode("','",$batch_arr_list);
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `active` = '1' AND `batch_code` IN ('$batch_list') AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $batch_supervisor[$row['batch_code']] = $row['supervisor_code']; }
                
                $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `active` = '1'".$date_filter."".$farm_query." AND `dflag` = '0' ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); $fcount = 0; $supr_tap_amt = $pc_amt = $sal_inv_amt = $supr_lifteff_tval = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key = $batch_supervisor[$row['batch_code']]; $fcount++;
                    if(empty($farmcount[$key])){ $farmcount[$key] = 1; } else{ $farmcount[$key] += 1; }
                    $supervisor_arr_codes[$key] = $batch_supervisor[$row['batch_code']];
                    if(empty($mage[$key])){ $mage[$key] = (float)$row['mean_age']; } else{ $mage[$key] += (float)$row['mean_age']; }
                    if(empty($chick_placed[$key])){ $chick_placed[$key] = (float)$row['placed_birds']; } else{ $chick_placed[$key] += (float)$row['placed_birds']; }
                    if(empty($morta_count[$key])){ $morta_count[$key] = (float)$row['mortality']; } else{ $morta_count[$key] += (float)$row['mortality']; }
                    if(empty($mort7_count[$key])){ $mort7_count[$key] = (float)$row['days7_mort_count']; } else{ $mort7_count[$key] += (float)$row['days7_mort_count']; }
                    if(empty($mort30_count[$key])){ $mort30_count[$key] = (float)$row['days30_mort_count']; } else{ $mort30_count[$key] += (float)$row['days30_mort_count']; }
                    if(empty($mort31g_count[$key])){ $mort31g_count[$key] = (float)$row['days31_mort_count']; } else{ $mort31g_count[$key] += (float)$row['days31_mort_count']; }
                    if(empty($shortage[$key])){ $shortage[$key] = (float)$row['shortage']; } else{ $shortage[$key] += (float)$row['shortage']; }
                    if(empty($excess[$key])){ $excess[$key] = (float)$row['excess']; } else{ $excess[$key] += (float)$row['excess']; }
                    if(empty($feed_con_kgs[$key])){ $feed_con_kgs[$key] = (float)$row['feed_consume_kgs']; } else{ $feed_con_kgs[$key] += (float)$row['feed_consume_kgs']; }
                    if(!empty($bstd_cum_feed[round($row['age'])])){
                        if(empty($std_feed_perbird[$key])){ $std_feed_perbird[$key] = (float)round(($bstd_cum_feed[round($row['age'])] / 1000),3); }
                        else{ $std_feed_perbird[$key] += (float)round(($bstd_cum_feed[round($row['age'])] / 1000),3); }
                    }
                    if(empty($act_feed_perbird[$key])){ $act_feed_perbird[$key] = ((((float)$row['feed_consume_kgs'] / (float)$row['placed_birds']))); }
                    else{ $act_feed_perbird[$key] += ((((float)$row['feed_consume_kgs'] / (float)$row['placed_birds']))); }

                    if(!empty($bstd_fcr[round($row['age'])])){
                        if(empty($std_fcr[$key])){ $std_fcr[$key] = $bstd_fcr[round($row['age'])]; }
                        else{ $std_fcr[$key] += $bstd_fcr[round($row['age'])]; }
                    }
                    if(empty($act_fcr[$key])){ $act_fcr[$key] = $row['fcr']; } else{ $act_fcr[$key] += $row['fcr']; }
                    if(empty($act_cfcr[$key])){ $act_cfcr[$key] = $row['cfcr']; } else{ $act_cfcr[$key] += $row['cfcr']; }
                    if(empty($act_dgain[$key])){ $act_dgain[$key] = $row['day_gain']; } else{ $act_dgain[$key] += $row['day_gain']; }
                    if(empty($act_eef[$key])){ $act_eef[$key] = $row['eef']; } else{ $act_eef[$key] += $row['eef']; }
                    if(empty($act_sbirds[$key])){ $act_sbirds[$key] = $row['sold_birds']; } else{ $act_sbirds[$key] += $row['sold_birds']; }
                    if(empty($act_sweight[$key])){ $act_sweight[$key] = $row['sold_weight']; } else{ $act_sweight[$key] += $row['sold_weight']; }
                    if(empty($act_samount[$key])){ $act_samount[$key] = $row['sale_amount']; } else{ $act_samount[$key] += $row['sale_amount']; }
                    if(empty($std_chick_amt[$key])){ $std_chick_amt[$key] = ($row['chick_cost_amt']); } else{ $std_chick_amt[$key] += $row['chick_cost_amt']; }
                    if(empty($std_feed_amt[$key])){ $std_feed_amt[$key] = ($row['feed_cost_amt']); } else{ $std_feed_amt[$key] += $row['feed_cost_amt']; }
                    if(empty($std_medvac_amt[$key])){ $std_medvac_amt[$key] = ($row['medicine_cost_amt']); } else{ $std_medvac_amt[$key] += $row['medicine_cost_amt']; }
                    if(empty($std_admin_amt[$key])){ $std_admin_amt[$key] = ($row['admin_cost_amt']); } else{ $std_admin_amt[$key] += $row['admin_cost_amt']; }
                    if(empty($fmr_prod_amt[$key])){ $fmr_prod_amt[$key] = ($row['total_cost_amt']); } else{ $fmr_prod_amt[$key] += $row['total_cost_amt']; }
                    if(empty($std_gc_amt[$key])){ $std_gc_amt[$key] = ($row['standard_gc_amt']); } else{ $std_gc_amt[$key] += $row['standard_gc_amt']; }
                    if(empty($act_gc_amt[$key])){ $act_gc_amt[$key] = ($row['total_gc_amt']); } else{ $act_gc_amt[$key] += $row['total_gc_amt']; }

                    if($row['grow_charge_exp_prc'] > 0){ $fmr_incentive_amt[$key] += (float)$row['grow_charge_exp_prc']; } else{ $fmr_decentive_amt[$key] += (float)$row['grow_charge_exp_prc']; }
                    if($row['sales_incentive_prc'] > 0){ $fmr_incentive_amt[$key] += (float)$row['sales_incentive_prc']; } else{ $fmr_decentive_amt[$key] += (float)$row['sales_incentive_prc']; }

                    $pc_amt[$key] += (float)$row['grow_charge_exp_amt'];
                    $sal_inv_amt[$key] += (float)$row['sales_incentive_amt'];

                    if(empty($rc_amt[$key])){ $rc_amt[$key] = ($row['grow_charge_exp_amt']); } else{ $rc_amt[$key] += $row['grow_charge_exp_amt']; }
                    if(empty($trc_amt[$key])){ $trc_amt[$key] = ($row['amount_payable']); } else{ $trc_amt[$key] += $row['amount_payable']; }
                    if(empty($fmr_tds_amt[$key])){ $fmr_tds_amt[$key] = ($row['tds_amt']); } else{ $fmr_tds_amt[$key] += $row['tds_amt']; }
                    if(empty($fmr_odeduct_amt[$key])){ $fmr_odeduct_amt[$key] = ($row['other_deduction']); } else{ $fmr_odeduct_amt[$key] += $row['other_deduction']; }
                    if(empty($fmr_pay_amt[$key])){ $fmr_pay_amt[$key] = ($row['farmer_payable']); } else{ $fmr_pay_amt[$key] += $row['farmer_payable']; }
                    if(empty($act_chick_amt[$key])){ $act_chick_amt[$key] = ($row['actual_chick_cost']); } else{ $act_chick_amt[$key] += $row['actual_chick_cost']; }
                    if(empty($act_feed_amt[$key])){ $act_feed_amt[$key] = ($row['actual_feed_cost']); } else{ $act_feed_amt[$key] += $row['actual_feed_cost']; }
                    if(empty($act_medvac_amt[$key])){ $act_medvac_amt[$key] = ($row['actual_medicine_cost']); } else{ $act_medvac_amt[$key] += $row['actual_medicine_cost']; }
                    if(empty($act_admin_amt[$key])){ $act_admin_amt[$key] = ($row['admin_cost_amt']); } else{ $act_admin_amt[$key] += $row['admin_cost_amt']; }
                    if(empty($fmr_gcpay_amt[$key])){ $fmr_gcpay_amt[$key] = ($row['farmer_payable']); } else{ $fmr_gcpay_amt[$key] += $row['farmer_payable']; }

                    $supr_lifteff_tval[$key] += (float)round($row['lifting_efficiency'],3);
                    $supr_totfarm_cnt[$key] += 1;

                    if((float)$row['total_amount_payable'] > 0){ $total_amount_payable = (float)$row['total_amount_payable']; }else{ $total_amount_payable = 0; }

                    $supr_tap_amt[$key] += (float)$row['total_amount_payable'];
                    if($admincost_include_flag == 1){
                        $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + (float)$total_amount_payable; //(float)$row['farmer_payable'];
                    }
                    else{
                        $total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost']  + (float)$total_amount_payable; //(float)$row['farmer_payable'];
                    }
                    //$total_prod = (float)$row['actual_chick_cost'] + (float)$row['actual_feed_cost'] + (float)$row['actual_medicine_cost'] + (float)$row['admin_cost_amt'] + (float)$total_amount_payable; //+ (float)$row['farmer_payable'];
                    
                    if(empty($act_prod_amt[$key])){ $act_prod_amt[$key] = (float)$total_prod; } else{ $act_prod_amt[$key] += (float)$total_prod; }
                    if(empty($pl_amt[$key])){ $pl_amt[$key] = ((float)$row['sale_amount'] - (float)$total_prod); } else{ $pl_amt[$key] += ((float)$row['sale_amount'] - (float)$total_prod); }

                    if((float)$row['sold_weight'] > 0){ if(empty($branch_batch_count[$key])){ $branch_batch_count[$key] = 1; } else{ $branch_batch_count[$key] = (float)$branch_batch_count[$key] + 1; } }
                }

                $supv_list = ""; $supv_list = implode("','",$supervisor_arr_codes);
                $supv_code = array();
                $sql = "SELECT * FROM `broiler_employee` WHERE `active` = '1' AND `code` IN ('$supv_list') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $supv_code[$row['code']] = $row['code']; }

                $slno = $t_meanage = $t_chick_placed = $t_morta_count = $t_mort7_count = $t_mort30_count = $t_mort31g_count = $t_shortage = $t_excess = $t_feed_con_kgs = 
                $t_std_feed_perbird = $t_act_feed_perbird = $t_sfcr = $t_afcr = $t_acfcr = $t_adgain = $t_aeef = $t_act_sbirds = $t_act_sweight = $t_act_samount = 
                $t_std_chick_amt = $t_std_feed_amt = $t_std_medvac_amt = $t_std_admin_amt = $t_fmr_prod_amt = $t_std_gc_amt = $t_act_gc_amt = $t_fmr_incentive_amt = 
                $t_fmr_decentive_amt = $t_rc_amt = $t_trc_amt = $t_fmr_tds_amt = $t_fmr_odeduct_amt = $t_fmr_pay_amt = $t_act_chick_amt = $t_act_feed_amt = 
                $t_act_medvac_amt = $t_act_admin_amt = $t_fmr_gcpay_amt = $t_act_prod_amt = $t_pl_amt = 0;

                foreach($supv_code as $key){
                    $slno++;
                    $supemp = $supervisor_name_display[$key];
                    if(!empty($farmcount[$key]) && (float)$farmcount[$key] != 0){
                        $ln_meanage = (float)$mage[$key] / (float)$farmcount[$key];
                        $sfpb = $std_feed_perbird[$key] / $farmcount[$key];
                        $afpb = $act_feed_perbird[$key] / $farmcount[$key];
                        $sfcr = $std_fcr[$key] / $farmcount[$key];
                        //$afcr = $act_fcr[$key] / $farmcount[$key];
                        //$acfcr = $act_cfcr[$key] / $farmcount[$key];
                        $adgain = $act_dgain[$key] / $farmcount[$key];
                        $aeef = $act_eef[$key] / $farmcount[$key];
    
                    }
                    else{
                        $ln_meanage = $sfpb = $afpb = $sfcr = $afcr = $acfcr = $adgain = $aeef = 0;    
                    }

                    if(!empty($chick_placed[$key]) && (float)$chick_placed[$key] != 0){
                        $morta_per = round((($morta_count[$key] / $chick_placed[$key]) * 100),2);
                        $mort7_per = round((($mort7_count[$key] / $chick_placed[$key]) * 100),2);
                        $mort30_per = round((($mort30_count[$key] / $chick_placed[$key]) * 100),2);
                        $mort31g_per = round((($mort31g_count[$key] / $chick_placed[$key]) * 100),2);
                        $std_chick_prc = $std_chick_amt[$key] / $chick_placed[$key];
                        $std_admin_prc = $std_admin_amt[$key] / $chick_placed[$key];
                        $act_chick_prc = $act_chick_amt[$key] / $chick_placed[$key];
                        $act_medvac_prc = $act_medvac_amt[$key] / $chick_placed[$key];
                        $act_admin_prc = $act_admin_amt[$key] / $chick_placed[$key];
                    }
                    else{
                        $morta_per = $mort7_per = $mort30_per = $mort31g_per = $std_chick_prc = $std_admin_prc = $act_chick_prc = $act_medvac_prc = $act_admin_prc = 0;
                    }
                    if(!empty($act_sbirds[$key]) && (float)$act_sbirds[$key] != 0){
                        $avg_bwt = round(($act_sweight[$key] / $act_sbirds[$key]),3);
                    }
                    else{
                        $avg_bwt = 0;
                    }

                    if(!empty($act_sweight[$key]) && (float)$act_sweight[$key] != 0){
                        $afcr = round(((float)$feed_con_kgs[$key] / (float)$act_sweight[$key]),3);
                        $acfcr = round((((2 - ((float)$avg_bwt)) / 4) + (float)$afcr),3);
                    }
                    else{
                        $afcr = $acfcr = 0;
                    }

                    if(!empty($act_sweight[$key]) && (float)$act_sweight[$key] != 0){
                        $sale_rate = $act_samount[$key] / $act_sweight[$key];
                        $std_medvac_prc = $std_medvac_amt[$key] / $act_sweight[$key];
                        $fmr_prod_prc = $fmr_prod_amt[$key] / $act_sweight[$key];
                        $std_gc_prc = $std_gc_amt[$key] / $act_sweight[$key];
                        $act_gc_prc = $act_gc_amt[$key] / $act_sweight[$key];
                        $act_prod_prc = $act_prod_amt[$key] / $act_sweight[$key];
                    }
                    else{
                        $sale_rate = $std_medvac_prc = $fmr_prod_prc = $std_gc_prc = $act_gc_prc = $act_prod_prc = 0;
                    }
                    if(!empty($feed_con_kgs[$key]) && (float)$feed_con_kgs[$key] != 0){
                        $std_feed_prc = $std_feed_amt[$key] / $feed_con_kgs[$key];
                        $act_feed_prc = $act_feed_amt[$key] / $feed_con_kgs[$key];
                    }
                    else{
                        $std_feed_prc = $act_feed_prc = 0;
                    }

                    echo "<tr>";
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i;
                        if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No' style='text-align:center;'>".str_replace('.00','',number_format_ind($slno))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "supervisor_name"){ echo "<td title='Supervisor' style='text-align:left;'><a href=/records/broiler_farmwiserealize_masterreport.php?submit_report=true&lines=all&fdate=". date("d.m.Y",strtotime($fdate)) ."&tdate=". date("d.m.Y",strtotime($tdate))."&report_view=". $report_view ."&branches=all&lines=all&supervisors=".$key."&farms=all target='_blank'>".$supemp."</a></td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<td title='Mean Age'>".number_format_ind(round($ln_meanage,3))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<td title='Chicks Placement'>".str_replace('.00','',number_format_ind($chick_placed[$key]))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<td title='Mortality'>".str_replace('.00','',number_format_ind($morta_count[$key]))."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<td title='Mortality%'>".number_format_ind($morta_per)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count"){ echo "<td title='1st week Mort'>".number_format_ind($mort7_count[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per"){ echo "<td title='1st week Mort%'>".number_format_ind($mort7_per)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count"){ echo "<td title='Up to 30days Mort'>".number_format_ind($mort30_count[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per"){ echo "<td title='Up to 30days Mort%'>".number_format_ind($mort30_per)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count"){ echo "<td title='After 30days Mort'>".number_format_ind($mort31g_count[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per"){ echo "<td title='After 30days Mort%'>".number_format_ind($mort31g_per)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_shortage_count"){ echo "<td title='Birds Shortage'>".number_format_ind($shortage[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_excess_count"){ echo "<td title='Birds Excess'>".number_format_ind($excess[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<td title='Feed Consumed Kgs'>".number_format_ind($feed_con_kgs[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<td title='Std. Feed/Bird'>".number_format_ind($sfpb)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_birdsno"){ echo "<td title='Feed/Bird Kgs'>".number_format_ind($afpb)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<td title='Std.FCR'>".number_format_ind($sfcr)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<td title='FCR'>".decimal_adjustments($afcr,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<td title='CFCR'>".decimal_adjustments($acfcr,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_gain"){ echo "<td title='Day Gain'>".number_format_ind($adgain)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<td title='EEF'>".number_format_ind($aeef)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<td title='Sold Birds'>".number_format_ind($act_sbirds[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<td title='Sold Weight'>".number_format_ind($act_sweight[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<td title='Avg. Body Wt'>".decimal_adjustments($avg_bwt,3)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<td title='Sale Price'>".number_format_ind($sale_rate)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_amount"){ echo "<td title='Sale Amount'>".number_format_ind($act_samount[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price"){ echo "<td title='Std Chick Price'>".number_format_ind($std_chick_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_amount"){ echo "<td title='Std Chick Cost'>".number_format_ind($std_chick_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_price"){ echo "<td title='Std Feed Price'>".number_format_ind($std_feed_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_amount"){ echo "<td title='Std Feed Cost'>".number_format_ind($std_feed_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price"){ echo "<td title='Std Medicine Price'>".number_format_ind($std_medvac_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount"){ echo "<td title='Std Medicine Cost'>".number_format_ind($std_medvac_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price"){ echo "<td title='Std Admin Price'>".number_format_ind($std_admin_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc"){
                            if((float)$act_sweight[$key] > 0 && (float)$act_admin_amt[$key] > 0){
                                echo "<td title='Std Admin Price'>".number_format_ind((float)$act_admin_amt[$key] / (float)$act_sweight[$key])."</td>";
                            }
                            else{
                                echo "<td title='Std Admin Price'>".number_format_ind($std_admin_prc)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount"){ echo "<td title='Std Admin Cost'>".number_format_ind($std_admin_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount"){ echo "<td title='Farmer P.cost'>".number_format_ind($fmr_prod_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price"){ echo "<td title='F PC/Kg'>".number_format_ind($fmr_prod_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg"){ echo "<td title='STD.Gc/kg'>".number_format_ind($std_gc_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive"){ echo "<td title='Incentives'>".number_format_ind($fmr_incentive_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives"){ echo "<td title='Decentives'>".number_format_ind($fmr_decentive_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price"){ echo "<td title='Gc/kg'>".number_format_ind($act_gc_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2"){
                            if((float)$supr_tap_amt[$key] != 0 && (float)$act_sweight[$key] > 0){
                                echo "<td title='Gc/kg'>".number_format_ind((float)$supr_tap_amt[$key] / (float)$act_sweight[$key])."</td>";
                            }
                            else{
                                echo "<td title='Gc/kg'>".number_format_ind($act_gc_prc)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges"){ echo "<td title='Rearing Charges'>".number_format_ind($rc_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_rearing_charges"){ echo "<td title='Total Rearing Charges'>".number_format_ind($trc_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_tds_amount"){ echo "<td title='TDS'>".number_format_ind($fmr_tds_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "other_deduction"){ echo "<td title='Other Deductitons'>".number_format_ind($fmr_odeduct_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_payable"){ echo "<td title='Farmer Payable'>".number_format_ind($fmr_pay_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_price"){ echo "<td title='Act Chick Price'>".number_format_ind($act_chick_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_amount"){ echo "<td title='Act Chick Cost'>".number_format_ind($act_chick_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_price"){ echo "<td title='Act Feed Price'>".number_format_ind($act_feed_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_amount"){ echo "<td title='Act Feed Cost'>".number_format_ind($act_feed_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_price"){ echo "<td title='Medicine Price'>".number_format_ind($act_medvac_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_amount"){ echo "<td title='Medicine Cost'>".number_format_ind($act_medvac_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_price"){ echo "<td title='Admin Price'>".number_format_ind($act_admin_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_amount"){ echo "<td title='Admin Cost'>".number_format_ind($act_admin_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "final_farmer_payable"){ echo "<td title='GC Paid to Farmer'>".number_format_ind($fmr_gcpay_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_prod_amount"){ echo "<td title='Actual P.Amount'>".number_format_ind($act_prod_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){ echo "<td title='M PC/Kg'>".number_format_ind($act_prod_prc)."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_sale_amount"){ echo "<td title='Total Sale Amount'>".number_format_ind($act_samount[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "profit_and_loss"){ echo "<td title='Profit/Loss'>".number_format_ind($pl_amt[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_batch_count"){ echo "<td title='Profit/Loss'>".number_format_ind($branch_batch_count[$key])."</td>"; }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc"){
                            if((float)$sal_inv_amt[$key] != 0 && (float)$act_sweight[$key] > 0){
                                echo "<td title='Sale Incentives/kg' style='text-align:right;'>".round(((float)$sal_inv_amt[$key] / (float)$act_sweight[$key]),3)."</td>";
                            }
                            else{
                                echo "<td title='Sale Incentives/kg' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc"){
                            if((float)$pc_amt[$key] != 0 && (float)$act_sweight[$key] > 0){
                                echo "<td title='Sale Incentives/kg' style='text-align:right;'>".round(((float)$pc_amt[$key] / (float)$act_sweight[$key]),3)."</td>";
                            }
                            else{
                                echo "<td title='Sale Incentives/kg' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                        }
                        else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency"){
                            if((float)$supr_totfarm_cnt[$key] != 0 && (float)$supr_lifteff_tval[$key] > 0){
                                echo "<td title='Sale Incentives/kg' style='text-align:right;'>".round(((float)$supr_lifteff_tval[$key] / (float)$supr_totfarm_cnt[$key]),3)."</td>";
                            }
                            else{
                                echo "<td title='Sale Incentives/kg' style='text-align:right;'>".number_format_ind(0)."</td>";
                            }
                        }
                        else{ }
                    }
                    
                    $t_meanage += $mage[$key];
                    $t_chick_placed += $chick_placed[$key];
                    $t_morta_count += $morta_count[$key];
                    $t_mort7_count += $mort7_count[$key];
                    $t_mort30_count += $mort30_count[$key];
                    $t_mort31g_count += $mort31g_count[$key];
                    $t_shortage += $shortage[$key];
                    $t_excess += $excess[$key];
                    $t_feed_con_kgs += $feed_con_kgs[$key];
                    $t_std_feed_perbird += $std_feed_perbird[$key];
                    $t_act_feed_perbird += $act_feed_perbird[$key];
                    $t_sfcr += $std_fcr[$key];
                    $t_afcr += $act_fcr[$key];
                    $t_acfcr += $act_cfcr[$key];
                    $t_adgain += $act_dgain[$key];
                    $t_aeef += $act_eef[$key];
                    $t_act_sbirds += $act_sbirds[$key];
                    $t_act_sweight += $act_sweight[$key];
                    $t_act_samount += $act_samount[$key];
                    $t_std_chick_amt += $std_chick_amt[$key];
                    $t_std_feed_amt += $std_feed_amt[$key];
                    $t_std_medvac_amt += $std_medvac_amt[$key];
                    $t_std_admin_amt += $std_admin_amt[$key];
                    $t_fmr_prod_amt += $fmr_prod_amt[$key];
                    $t_std_gc_amt += $std_gc_amt[$key];
                    $t_act_gc_amt += $act_gc_amt[$key];
                    $t_fmr_incentive_amt += $fmr_incentive_amt[$key];
                    $t_fmr_decentive_amt += $fmr_decentive_amt[$key];
                    $t_rc_amt += $rc_amt[$key];
                    $t_trc_amt += $trc_amt[$key];
                    $t_fmr_tds_amt += $fmr_tds_amt[$key];
                    $t_fmr_odeduct_amt += $fmr_odeduct_amt[$key];
                    $t_fmr_pay_amt += $fmr_pay_amt[$key];
                    $t_act_chick_amt += $act_chick_amt[$key];
                    $t_act_feed_amt += $act_feed_amt[$key];
                    $t_act_medvac_amt += $act_medvac_amt[$key];
                    $t_act_admin_amt += $act_admin_amt[$key];
                    $t_fmr_gcpay_amt += $fmr_gcpay_amt[$key];
                    $t_act_prod_amt += $act_prod_amt[$key];
                    $t_pl_amt += $pl_amt[$key];
                    $t_bb_cnt += $branch_batch_count[$key];
                    $t_supr_tap_amt += $supr_tap_amt[$key];
                    $t_sal_inv_amt += $sal_inv_amt[$key];
                    $t_pc_amt += $pc_amt[$key];
                    $t_supr_lifteff_tval += $supr_lifteff_tval[$key];
                    $t_supr_totfarm_cnt += $supr_totfarm_cnt[$key];
                    echo "</tr>";

                }
                ?>
            </tbody>
            <tfoot class="tfoot1">
                <?php
                if($fcount == "" || (float)$fcount == 0) {
                    $ft_meanage = $t_sfpb = $t_afpb = $ft_sfcr = $ft_afcr = $ft_acfcr = $ft_adgain = $ft_aeef = 0;
                }
                else{
                    $ft_meanage = $t_meanage / $fcount;
                    $t_sfpb = $t_std_feed_perbird / $fcount;
                    $t_afpb = $t_act_feed_perbird / $fcount;
                    $ft_sfcr = $t_sfcr / $fcount;
                    $ft_afcr = $t_afcr / $fcount;
                    $ft_acfcr = $t_acfcr / $fcount;
                    $ft_adgain = $t_adgain / $fcount;
                    $ft_aeef = $t_aeef / $fcount;
                }
                if($t_chick_placed == "" || (float)$t_chick_placed == 0) {
                    $t_morta_per = $t_mort7_per = $t_mort30_per = $t_mort31g_per = $t_act_chick_prc = $t_act_medvac_prc = $t_act_admin_prc = $t_std_chick_prc = $t_std_admin_prc = 0;
                }
                else{
                    $t_morta_per = round((($t_morta_count / $t_chick_placed) * 100),2);
                    $t_mort7_per =round((( $t_mort7_count / $t_chick_placed) * 100),2);
                    $t_mort30_per = round((($t_mort30_count / $t_chick_placed) * 100),2);
                    $t_mort31g_per = round((($t_mort31g_count / $t_chick_placed) * 100),2);
                    $t_act_chick_prc = round(($t_act_chick_amt / $t_chick_placed),3);
                    $t_act_medvac_prc = round(($t_act_medvac_amt / $t_chick_placed),2);
                    $t_act_admin_prc = round(($t_act_admin_amt / $t_chick_placed),2);
                    $t_std_chick_prc = round(($t_std_chick_amt / $t_chick_placed),2);
                    $t_std_admin_prc = round(($t_std_admin_amt / $t_chick_placed),2);
                }
                if($t_chick_placed == "" || (float)$t_chick_placed == 0) {
                    $t_sale_rate = $t_std_medvac_prc = $t_fmr_prod_prc = $t_std_gc_prc = $t_act_gc_prc = $t_act_prod_prc = 0;
                }
                else{
                    $t_sale_rate = round(($t_act_samount / $t_act_sweight),2);
                    $t_std_medvac_prc = round(($t_std_medvac_amt / $t_act_sweight),2);
                    $t_fmr_prod_prc = round(($t_fmr_prod_amt / $t_act_sweight),2);
                    $t_std_gc_prc = round(($t_std_gc_amt / $t_act_sweight),2);
                    $t_act_gc_prc = round(($t_act_gc_amt / $t_act_sweight),2);
                    $t_act_prod_prc = round(($t_act_prod_amt / $t_act_sweight),2);
                }
                if($t_feed_con_kgs == "" || (float)$t_feed_con_kgs == 0) {
                    $t_std_feed_prc = $t_act_feed_prc = 0;
                }
                else{
                    $t_std_feed_prc = round(($t_std_feed_amt / $t_feed_con_kgs),2);
                    $t_act_feed_prc = round(($t_act_feed_amt / $t_feed_con_kgs),2);
                }
                if($t_act_sbirds == "" || (float)$t_act_sbirds == 0) {
                    $t_avg_bwt = 0;
                }
                else{
                    $t_avg_bwt = round(($t_act_sweight / $t_act_sbirds),3);
                }

                if(!empty($t_act_sweight) && (float)$t_act_sweight != 0){
                    $ft_afcr = round(((float)$t_feed_con_kgs / (float)$t_act_sweight),3);
                    $ft_acfcr = round((((2 - ((float)$t_avg_bwt)) / 4) + (float)$ft_afcr),3);
                }
                else{
                    $ft_afcr = $ft_acfcr = 0;
                }

                echo "<tr class='thead4'>";
                echo "<th colspan='".$theadc."' style='text-align:center;'>Total</th>";
                for($i = $theadc + 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mean_age"){ echo "<th>".number_format_ind($ft_meanage)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "chick_placed"){ echo "<th>".number_format_ind($t_chick_placed)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_count"){ echo "<th>".number_format_ind($t_morta_count)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_per"){ echo "<th>".number_format_ind($t_morta_per)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_count"){ echo "<th>".number_format_ind($t_mort7_count)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_1week_per"){ echo "<th>".number_format_ind($t_mort7_per)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_count"){ echo "<th>".number_format_ind($t_mort30_count)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30days_per"){ echo "<th>".number_format_ind($t_mort30_per)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_count"){ echo "<th>".number_format_ind($t_mort31g_count)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mortality_30more_per"){ echo "<th>".number_format_ind($t_mort31g_per)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_shortage_count"){ echo "<th>".number_format_ind($t_shortage)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "bird_excess_count"){ echo "<th>".number_format_ind($t_excess)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "feedconsumed_count"){ echo "<th>".number_format_ind($t_feed_con_kgs)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_birdsno"){ echo "<th>".number_format_ind($t_sfpb)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_birdsno"){ echo "<th>".number_format_ind($t_afpb)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_fcr"){ echo "<th>".number_format_ind($ft_sfcr)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "fcr"){ echo "<th>".decimal_adjustments($ft_afcr,3)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "cfcr"){ echo "<th>".decimal_adjustments($ft_acfcr,3)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "day_gain"){ echo "<th>".number_format_ind($ft_adgain)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "eef"){ echo "<th>".number_format_ind($ft_aeef)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdsno"){ echo "<th>".number_format_ind($t_act_sbirds)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_birdswt"){ echo "<th>".number_format_ind($t_act_sweight)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "avg_bodywt"){ echo "<th>".decimal_adjustments($t_avg_bwt,3)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_perkg_price"){ echo "<th>".number_format_ind($t_sale_rate)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_amount"){ echo "<th>".number_format_ind($t_act_samount)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_perkg_price"){ echo "<th>".number_format_ind($t_std_chick_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_chick_amount"){ echo "<th>".number_format_ind($t_std_chick_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_price"){ echo "<th>".number_format_ind($t_std_feed_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_feed_amount"){ echo "<th>".number_format_ind($t_std_feed_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_price"){ echo "<th>".number_format_ind($t_std_medvac_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_medicine_amount"){ echo "<th>".number_format_ind($t_std_medvac_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_price"){ echo "<th>".number_format_ind($t_std_admin_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_swtprc"){
                        if((float)$t_act_sweight > 0 && (float)$t_act_admin_amt > 0){
                            echo "<th title='Std Admin Price'>".number_format_ind((float)$t_act_admin_amt / (float)$t_act_sweight)."</th>";
                        }
                        else{
                            echo "<th title='Std Admin Price'>".number_format_ind($t_std_admin_prc)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_admin_amount"){ echo "<th>".number_format_ind($t_std_admin_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_production_amount"){ echo "<th>".number_format_ind($t_fmr_prod_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_prodperkg_price"){ echo "<th>".number_format_ind($t_fmr_prod_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "std_gc_perkg"){ echo "<th>".number_format_ind($t_std_gc_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_incentive"){ echo "<th>".number_format_ind($t_fmr_incentive_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_decentives"){ echo "<th>".number_format_ind($t_fmr_decentive_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price"){ echo "<th>".number_format_ind($t_act_gc_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_gc_perkg_price2"){
                        if((float)$t_act_sweight > 0 && (float)$t_supr_tap_amt > 0){
                            echo "<th title='Std Admin Price'>".number_format_ind((float)$t_supr_tap_amt / (float)$t_act_sweight)."</th>";
                        }
                        else{
                            echo "<th title='Std Admin Price'>".number_format_ind($t_act_gc_prc)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "rearing_charges"){ echo "<th>".number_format_ind($t_rc_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_rearing_charges"){ echo "<th>".number_format_ind($t_trc_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_tds_amount"){ echo "<th>".number_format_ind($t_fmr_tds_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "other_deduction"){ echo "<th>".number_format_ind($t_fmr_odeduct_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "farmer_payable"){ echo "<th>".number_format_ind($t_fmr_pay_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_price"){ echo "<th>".number_format_ind($t_act_chick_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_chick_amount"){ echo "<th>".number_format_ind($t_act_chick_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_price"){ echo "<th>".number_format_ind($t_act_feed_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_feed_amount"){ echo "<th>".number_format_ind($t_act_feed_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_price"){ echo "<th>".number_format_ind($t_act_medvac_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_medicine_amount"){ echo "<th>".number_format_ind($t_act_medvac_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_price"){ echo "<th>".number_format_ind($t_act_admin_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_admin_amount"){ echo "<th>".number_format_ind($t_act_admin_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "final_farmer_payable"){ echo "<th>".number_format_ind($t_fmr_gcpay_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "actual_prod_amount"){ echo "<th>".number_format_ind($t_act_prod_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "mgmt_perkg_price"){ echo "<th>".number_format_ind($t_act_prod_prc)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "total_sale_amount"){ echo "<th>".number_format_ind($t_act_samount)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "profit_and_loss"){ echo "<th>".number_format_ind($t_pl_amt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "sold_batch_count"){ echo "<th>".number_format_ind($t_bb_cnt)."</th>"; }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "gc_sale_inc_prc"){
                        if((float)$t_act_sweight > 0 && (float)$t_sal_inv_amt > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($t_sal_inv_amt / $t_act_sweight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "approved_gc_prc"){
                        if((float)$t_act_sweight > 0 && (float)$t_pc_amt > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($t_pc_amt / $t_act_sweight)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                    }
                    else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "lifting_efficiency"){
                        if((float)$t_supr_totfarm_cnt > 0 && (float)$t_supr_lifteff_tval > 0){
                            echo "<th style='text-align:right;'>".number_format_ind($t_supr_lifteff_tval / $t_supr_totfarm_cnt)."</th>";
                        }
                        else{
                            echo "<th style='text-align:right;'>".number_format_ind(0)."</th>";
                        }
                    }
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
                            alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
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

<script type="text/javascript">
              function tableToExcel(table, name, filename, chosen){ 
              
              var uri = 'data:application/vnd.ms-excel;base64,'
                  , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                  , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                  , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
            //  return function(table, name, filename, chosen) {
                  if (chosen === 'excel') { 
                    //$('#header_sorting').empty();
                  if (!table.nodeType) table = document.getElementById(table)
                  var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                  //window.location.href = uri + base64(format(template, ctx))
                  var link = document.createElement("a");
                                  link.download = filename+".xls";
                                  link.href = uri + base64(format(template, ctx));
                                  link.click();
                  
         /*       var html = '';
                html += '<th id="order_num">Sl.No.</th>';
                html += '<th id="order">Branch</th>';
                html += '<th id="order">Line</th>';
                html += '<th id="order">Supervisor</th>';
                html += '<th id="order">Farm Name</th>';
                html += '<th id="order">Batch</th>';
                html += '<th id="order">Book No</th>';
                html += '<th id="order_num">Age</th>';
                html += '<th id="order_num">Opening Birds</th>';
                html += '<th id="order_num">Before Yesterday Mort</th>';
                html += '<th id="order_num">Yesterday Mortality</th>';
                html += '<th id="order_num">Today Mortality</th>';
                html += '<th id="order_num">Mort%</th>';
                html += '<th id="order_num">Balance Birds</th>';
                html += '<th id="order">Diseases Details</th>';
                $('#header_sorting').append(html);
                table_sort();
                table_sort2();
                table_sort3();*/
  
          }
        }
        </script>
        
        <script>
            function fetch_farms_details(a){
                var regions = document.getElementById("regions").value;
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var user_code = '<?php echo $user_code; ?>';
                var rf_flag = bf_flag = lf_flag = sf_flag = ff_flag = 0;
                if(a.match("regions")){ rf_flag = 1; } else if(a.match("branches")){ bf_flag = 1; } else if(a.match("lines")){ lf_flag = 1; } else{ ff_flag = 1; }
                    
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
                            $('#branches').append(brnh_list);
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("lines"));
                            removeAllOptions(document.getElementById("supervisors"));
                            $('#lines').append(line_list);
                            $('#supervisors').append(supr_list);
                        }
                        else if(lf_flag == 1){
                            removeAllOptions(document.getElementById("supervisors"));
                            $('#supervisors').append(supr_list);
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
                else{ }
                
                if(f_cnt <= 3){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>