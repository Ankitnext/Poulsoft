<?php
//broiler_farmwise_weeklyreport1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_farmwise_weeklyreport1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_farmwise_weeklyreport1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Farm Wise Weekly Report";
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
if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_week_define_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_week_define_master LIKE poulso6_admin_broiler_broilermaster.broiler_week_define_master;"; mysqli_query($conn,$sql1); }

/*Master Report Format
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path); //$href = "broiler_detail_dayrecord_masterreport.php";
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$href%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = ""; $col_count = 0;
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
                //echo "<br/>".$cna."-".$row2[$cna];
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}
*/
/*Check User access Locations*/
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql); $db_emp_code = $sp_emp_code = array();
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $region_code = $region_name = array();
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

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

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $desig_alist = array();
while($row = mysqli_fetch_assoc($query)){ $desig_alist[$row['code']] = $row['code']; }

$desig_list = implode("','",$desig_alist);
$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $supervisor_code = $supervisor_name = array();
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $farmer_name = $farmer_mobile1 = $farmer_mobile2 = array();
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }

$sql = "SELECT * FROM `broiler_diseases` WHERE `dflag` = '0'";
$query = mysqli_query($conn,$sql); $dieases_name = array();
while($row = mysqli_fetch_assoc($query)){ $dieases_name[$row['trnum']] = $row['name']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC";
$query = mysqli_query($conn,$sql); $bstd_body_weight = $bstd_daily_gain = $bstd_avg_daily_gain = $bstd_fcr = $bstd_cum_feed = $bstd_mcum_feed = $bstd_feed_consumed = array(); $p_age = 0;
while($row = mysqli_fetch_assoc($query)){
    $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain'];
    $bstd_fcr[$row['age']] = $row['fcr'];
    $bstd_cum_feed[$row['age']] = $row['cum_feed'];
    //manual Cumulative feed Total
    if((int)$row['age'] == 1){
        $bstd_mcum_feed[$row['age']] = $row['cum_feed'];
    }
    else{
        $p_age = $row['age'] - 1;
        $bstd_mcum_feed[$row['age']] = $bstd_mcum_feed[$p_age] + $row['cum_feed'];
    }
    $bstd_feed_consumed[$row['age']] = (float)$row['feed_consumed'];
}

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $vendor_code = $vendor_name = array();
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $sector_name[$row['code']] = $row['name']; }

//Feed Items
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_acat = array();
while($row = mysqli_fetch_assoc($query)){ $feed_acat[$row['code']] = $row['code']; }
$feed_list = implode("','",$feed_acat);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_code = array();
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }

//MedVac Items
$sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_acat = array();
while($row = mysqli_fetch_assoc($query)){ $medvac_acat[$row['code']] = $row['code']; }
$medvac_list = implode("','",$medvac_acat);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$medvac_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_code = array();
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

//Chick/Bird Items
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $chick_code = "";
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bird_code = "";
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = $batch_type = "0"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $batch_type = $_POST['batch_type'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
                            </div>
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Batch Type</label>
                                    <select name="batch_type" id="batch_type" class="form-control select2">
                                        <option value="all" <?php if($batch_type == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="0" <?php if($batch_type == "0"){ echo "selected"; } ?>>Live</option>
                                        <option value="1" <?php if($batch_type == "1"){ echo "selected"; } ?>>Culled</option>
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
            
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $nhtml .= '<th colspan="13"></th>';
            $nhtml .= '<th colspan="10">Weekly</th>';
            $nhtml .= '<th colspan="10">Cumulative</th>';
            $nhtml .= '<th colspan="4">Sales&Diseases</th>';
            $nhtml .= '</tr>';
            
            $fhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<th colspan="13"></th>';
            $fhtml .= '<th colspan="10">Weekly</th>';
            $fhtml .= '<th colspan="10">Cumulative</th>';
            $fhtml .= '<th colspan="4">Sales&Diseases</th>';
            $fhtml .= '</tr>';
            
            $nhtml .= '<tr style="text-align:center;" align="center">';
            $nhtml .= '<th colspan="13"></th>';
            $nhtml .= '<th colspan="2">Weekly Mort</th>';
            $nhtml .= '<th colspan="2">Average Weight</th>';
            $nhtml .= '<th colspan="2">Day Gain</th>';
            $nhtml .= '<th colspan="2">Feed Consumed</th>';
            $nhtml .= '<th colspan="2">Fcr</th>';
            $nhtml .= '<th colspan="2">Cumulative Mort</th>';
            $nhtml .= '<th colspan="2">Average Weight</th>';
            $nhtml .= '<th colspan="2">Day Gain</th>';
            $nhtml .= '<th colspan="2">Feed Consumed</th>';
            $nhtml .= '<th colspan="2">Fcr</th>';
            $nhtml .= '<th colspan="4"></th>';
            $nhtml .= '</tr>';

            $fhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<th colspan="13"></th>';
            $fhtml .= '<th colspan="2">Weekly Mort</th>';
            $fhtml .= '<th colspan="2">Average Weight</th>';
            $fhtml .= '<th colspan="2">Day Gain</th>';
            $fhtml .= '<th colspan="2">Feed Consumed</th>';
            $fhtml .= '<th colspan="2">Fcr</th>';
            $fhtml .= '<th colspan="2">Cumulative Mort</th>';
            $fhtml .= '<th colspan="2">Average Weight</th>';
            $fhtml .= '<th colspan="2">Day Gain</th>';
            $fhtml .= '<th colspan="2">Feed Consumed</th>';
            $fhtml .= '<th colspan="2">Fcr</th>';
            $fhtml .= '<th colspan="4"></th>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Week</th>'; $fhtml .= '<th id="order_num">Week</th>';
            $nhtml .= '<th>Branch</th>'; $fhtml .= '<th id="order">Branch</th>';
            $nhtml .= '<th>Place</th>'; $fhtml .= '<th id="order">Place</th>';
            $nhtml .= '<th>Supervisor</th>'; $fhtml .= '<th id="order">Supervisor</th>';
            //$nhtml .= '<th>Farmer</th>'; $fhtml .= '<th id="order">Farmer</th>';
            $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>';
            $nhtml .= '<th>Hatchery</th>'; $fhtml .= '<th id="order">Hatchery</th>';
            $nhtml .= '<th>Supplier</th>'; $fhtml .= '<th id="order">Supplier</th>';
            $nhtml .= '<th>Flock</th>'; $fhtml .= '<th id="order">Flock</th>';
            $nhtml .= '<th>Placement Date</th>'; $fhtml .= '<th id="order_date">Placement Date</th>';
            $nhtml .= '<th>Housed</th>'; $fhtml .= '<th id="order_num">Housed</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>Actual Age</th>'; $fhtml .= '<th id="order_num">Actual Age</th>';

            $nhtml .= '<th>Mort</th>'; $fhtml .= '<th id="order_num">Mort</th>';
            $nhtml .= '<th>Mort%</th>'; $fhtml .= '<th id="order_num">Mort%</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';

            $nhtml .= '<th>Mort</th>'; $fhtml .= '<th id="order_num">Mort</th>';
            $nhtml .= '<th>Mort%</th>'; $fhtml .= '<th id="order_num">Mort%</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';

            $nhtml .= '<th>Sold Birds</th>'; $fhtml .= '<th id="order_num">Sold Birds</th>';
            $nhtml .= '<th>Sold Weight</th>'; $fhtml .= '<th id="order_num">Sold Weight</th>';
            $nhtml .= '<th>Stock Birds</th>'; $fhtml .= '<th id="order_num">Stock Birds</th>';
            $nhtml .= '<th>Diseases</th>'; $fhtml .= '<th id="order">Diseases</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = $gc_fltr = "";
                if($regions != "all"){ $rgn_fltr = " AND `region_code` = '$regions'"; }
                if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                if($supervisors != "all"){ $sup_fltr = " AND `supervisor_code` = '$supervisors'"; }
                if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }
                if($batch_type != "all"){ $gc_fltr = " AND `gc_flag` = '$batch_type'"; }

                $farm_list = ""; $farm_list = implode("','", $farm_code);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$rgn_fltr."".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                $farm_list = ""; $farm_list = implode("','", $farm_alist);
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')".$gc_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $batch_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $batch_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['batch_code']] = $row['batch_code']; }

                    $batch_size = sizeof($batch_alist);
                    if($batch_size > 0){
                        $batch_list = ""; $batch_list = implode("','", $batch_alist);
                        $sdate = $edate = ""; $batch_alist = array();
                        //Purchase
                        $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                        $query = mysqli_query($conn,$sql); $pur_qty = $placed_date = array();
                        while($row = mysqli_fetch_array($query)){
                            $idate = $row['date']; $icode = $row['icode']; $ibatch = $row['farm_batch']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;
                            
                            $pur_qty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);

                            if($icode == $chick_code){
                                $placed_date[$ibatch] = $row['date'];
                            }
                        
                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }
                        //Stock-In
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                        $query = mysqli_query($conn,$sql); $tin_qty = $chkin_hcode = array();
                        while($row = mysqli_fetch_array($query)){
                            $idate = $row['date']; $icode = $row['code']; $ibatch = $row['to_batch']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;
                            
                            $tin_qty[$key] += (float)$row['quantity'];

                            if($icode == $chick_code){
                                $placed_date[$ibatch] = $row['date'];
                                $chk_scode[$key] = $vendor_name[$row['vcode']];
                                //Hatchery Name-Saha
                                $chkin_hcode[$ibatch] = $row['fromwarehouse'];
                            }
                        
                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }
                        //Sale
                        $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                        $query = mysqli_query($conn,$sql); $sale_birds = $sale_qty = array();
                        while($row = mysqli_fetch_array($query)){
                            $idate = $row['date']; $icode = $row['icode']; $ibatch = $row['farm_batch']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;
                            
                            $sale_birds[$key] += (float)$row['birds'];
                            $sale_qty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        
                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }
                        //Stock-Out
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                        $query = mysqli_query($conn,$sql); $tout_qty = array();
                        while($row = mysqli_fetch_array($query)){
                            $idate = $row['date']; $icode = $row['code']; $ibatch = $row['from_batch']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;
                            
                            $tout_qty[$key] += (float)$row['quantity'];

                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }
                        //Day Record
                        $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                        $query = mysqli_query($conn,$sql); $dcon_qty = $dentry_mqty = $dentry_cqty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['item_code1']; $ibatch = $row['batch_code']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;
                            $dcon_qty[$key] += (float)$row['kgs1'];

                            $idate = $row['date']; $icode = $row['item_code2']; $ibatch = $row['batch_code'];
                            $key = $idate."@".$ibatch."@".$icode;
                            $dcon_qty[$key] += (float)$row['kgs2'];
                            
                            $idate = $row['date']; $icode = $chick_code; $ibatch = $row['batch_code'];
                            $key = $idate."@".$ibatch."@".$icode;
                            $dentry_mqty[$key] += (float)$row['mortality'];
                            $dentry_cqty[$key] += (float)$row['culls'];
                            $dentry_bage[$key] = (float)$row['brood_age'];
                            $dentry_mage[$ibatch] = (float)$row['brood_age'];
                            $dentry_agwt[$key] = (float)$row['avg_wt'];
                            $dentry_dieases[$key] = $dieases_name[$row['dieases_codes']];
                        
                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }
    
                        //MedVac Record
                        $sql = "SELECT * FROM `broiler_medicine_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                        $query = mysqli_query($conn,$sql); $mcon_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['item_code']; $ibatch = $row['batch_code']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;
                            
                            $mcon_qty[$key] += (float)$row['quantity'];
                        
                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }

                        //Bird Transfer to Processing
                        $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `date` <= '$tdate' AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                        $query = mysqli_query($conn,$sql); $pout_birds = $pout_weight = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $idate = $row['date']; $icode = $row['item_code']; $ibatch = $row['from_batch']; $batch_alist[$ibatch] = $ibatch;
                            $key = $idate."@".$ibatch."@".$icode;

                            $pout_birds[$key] += (float)$row['birds'];
                            $pout_weight[$key] += (float)$row['weight'];
                        
                            if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                            if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                        }

                        $batch_size = sizeof($batch_alist);
                        if($batch_size > 0){
                            //Fetch All actively available Farm and Batch List
                            $batch_list = ""; $batch_list = implode("','", $batch_alist);
                            $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_no`,`description` ASC";
                            $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_book = $batch_farm1 = $batch_farm2 = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description'];
                                $batch_book[$row['code']] = $row['book_num']; $batch_farm1[$row['code']] = $row['farm_code'];
                                if(empty($batch_farm2[$row['farm_code']]) || $batch_farm2[$row['farm_code']] == ""){ $batch_farm2[$row['farm_code']] = $row['code']; } else{ $batch_farm2[$row['farm_code']] .= ",".$row['code']; }
                            }
                            $farm_list1 = ""; $farm_list1 = implode("','", $batch_farm1);
                            $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') AND `code` IN ('$farm_list1')".$rgn_fltr."".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                            $query = mysqli_query($conn,$sql); $farm_acode = array();
                            while($row = mysqli_fetch_assoc($query)){ $farm_acode[$row['code']] = $row['code']; }

                            //Defining Weeeks
                            $sql = "SELECT * FROM `broiler_week_define_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `from_age` ASC";
                            $query = mysqli_query($conn,$sql); $weeks = $aweeks = $week_alist = array();
                            while($row = mysqli_fetch_assoc($query)){
                                //Calculatve Age
                                $fage = $row['from_cage']; $tage = $row['to_cage']; $week_no = $row['week_no'];
                                for($j = (int)$fage;$j <= (int)$tage;$j++){ $weeks[$j] = (int)$week_no; }

                                //Actual Age
                                $fage = $row['from_age']; $tage = $row['to_age']; $week_no = $row['week_no']; $week_alist[$week_no] = $tage;
                                for($j = (int)$fage;$j <= (int)$tage;$j++){ $aweeks[$j] = (int)$week_no; }
                            }
                            
                            //Calculations
                            $tot_pbirds = $tot_sbirds = $tot_sweight = $tot_tweight = $tot_tbirds = $tot_mbirds = $tot_cbirds = $week_mort = $week_dieases = $allowed_bweek = array();
                            foreach($farm_acode as $fcode){
                                if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                                else{
                                    $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                                    foreach($blist as $bcode){
                                        for($cdate = strtotime($sdate); $cdate <= strtotime($edate); $cdate += (86400)){
                                            $adate = date("Y-m-d",$cdate);
                                            //Chick (OR) Bird Calculations
                                            $key = $adate."@".$bcode."@".$chick_code;
                                            if(empty($pur_qty[$key]) || $pur_qty[$key] == ""){ $pur_qty[$key] = 0; }
                                            if(empty($tin_qty[$key]) || $tin_qty[$key] == ""){ $tin_qty[$key] = 0; }
                                            if(empty($dentry_mqty[$key]) || $dentry_mqty[$key] == ""){ $dentry_mqty[$key] = 0; }
                                            if(empty($dentry_cqty[$key]) || $dentry_cqty[$key] == ""){ $dentry_cqty[$key] = 0; }
                                            if(empty($sale_birds[$key]) || $sale_birds[$key] == ""){ $sale_birds[$key] = 0; }
                                            if(empty($tout_qty[$key]) || $tout_qty[$key] == ""){ $tout_qty[$key] = 0; }
                                            if(empty($pout_birds[$key]) || $pout_birds[$key] == ""){ $pout_birds[$key] = 0; }
                                            if(empty($pout_weight[$key]) || $pout_weight[$key] == ""){ $pout_weight[$key] = 0; }
                                            $tot_pbirds[$bcode] += ((float)round($pur_qty[$key],2) + (float)round($tin_qty[$key],2));

                                            $tot_sbirds[$bcode] += ((float)round($sale_birds[$key],2) + (float)round($tout_qty[$key],2));
                                            $tot_tbirds[$bcode] += (float)round($pout_birds[$key],2);
                                            $tot_mbirds[$bcode] += (float)round($dentry_mqty[$key],2);
                                            $tot_cbirds[$bcode] += (float)round($dentry_cqty[$key],2);

                                            //Supplier Name
                                            if(empty($week_sname[$bcode]) || $week_sname[$bcode] == ""){
                                                $week_sname[$bcode] = $chk_scode[$key];
                                            }
                                            //Hatchery Name-Saha
                                            $week_hname[$bcode] = $sector_name[$chkin_hcode[$bcode]];

                                            $key = $adate."@".$bcode."@".$bird_code;
                                            if(empty($sale_birds[$key]) || $sale_birds[$key] == ""){ $sale_birds[$key] = 0; }
                                            if(empty($sale_qty[$key]) || $sale_qty[$key] == ""){ $sale_qty[$key] = 0; }
                                            if(empty($tout_qty[$key]) || $tout_qty[$key] == ""){ $tout_qty[$key] = 0; }
                                            if(empty($pout_birds[$key]) || $pout_birds[$key] == ""){ $pout_birds[$key] = 0; }
                                            $tot_sbirds[$bcode] += ((float)round($sale_birds[$key],2) + (float)round($tout_qty[$key],2));
                                            $tot_sweight[$bcode] += (float)round($sale_qty[$key],2);
                                            $tot_tweight[$bcode] += (float)round($pout_weight[$key],2);
                                            $tot_tbirds[$bcode] += (float)round($pout_birds[$key],2);

                                            if(empty($tot_pbirds[$bcode]) || $tot_pbirds[$bcode] == ""){ $tot_pbirds[$bcode] = 0; }
                                            if(empty($dentry_bage[$key]) || $dentry_bage[$key] == ""){ $dentry_bage[$key] = 0; }
                                            $key1 = $adate."@".$bcode."@".$chick_code;
                                            if((float)$dentry_bage[$key1] == 0 && (float)$tot_pbirds[$bcode] == 0){ }
                                            else{
                                                $key3 = $adate."@".$bcode."@".$bird_code;
                                                $c_week = $m_age = 0;
                                                
                                                if(empty($weeks[$dentry_bage[$key1]]) && (float)$sale_birds[$key3] > 0){ $m_age = (INT)((strtotime($adate) - strtotime($sdate)) / 60 / 60 / 24); $c_week = $weeks[$m_age]; }
                                                else{ $c_week = $weeks[$dentry_bage[$key1]]; }
                                                
                                                //Farm wise each week calculations
                                                if(empty($c_week) || $c_week == ""){ }
                                                else if((float)$dentry_mage[$bcode] >= ((float)$c_week * 7)){
                                                    //echo "<br/>1. ".$c_week."@".$dentry_bage[$key1]."@".((float)$c_week * 7)."@".$dentry_mage[$bcode];
                                                    $allowed_bweek[$bcode] = $c_week;
                                                    $key2 = ""; $key2 = $c_week."@".$bcode;
                                                    //echo "<br/>2.$key2 = $c_week@$bcode;";
                                                    if(empty($sale_qty[$key3]) || $sale_qty[$key3] == ""){ $sale_qty[$key3] = 0; }
    
                                                    $placed_birds[$key2] = (float)round($tot_pbirds[$bcode],2);
                                                    $week_bage[$key2] = (float)round($dentry_bage[$key1],2);
                                                    $week_mort[$key2] += ((float)round($dentry_mqty[$key1],2) + (float)round($dentry_cqty[$key1],2));
                                                    $week_cmort[$key2] = ((float)round($tot_mbirds[$bcode],2) + (float)round($tot_cbirds[$bcode],2));
                                                    $week_sbirds[$key2] += ((float)round($sale_birds[$key1],2) + (float)round($tout_qty[$key1],2) + (float)round($pout_birds[$key1],2));
                                                    $week_sbirds[$key2] += ((float)round($sale_birds[$key3],2) + (float)round($tout_qty[$key3],2) + (float)round($pout_birds[$key3],2));
                                                    $week_sweight[$key2] += ((float)round($sale_qty[$key3],2));
                                                    $week_sweight[$key2] += ((float)round($sale_qty[$key1],2));
    
                                                    //Weekly Dieases
                                                    if(empty($dentry_dieases[$key1]) || $dentry_dieases[$key1] == ""){ }
                                                    else{
                                                        if(empty($week_dieases[$key2]) || $week_dieases[$key2] == ""){
                                                            $week_dieases[$key2] = $dentry_dieases[$key1];
                                                        }
                                                        else{
                                                            $week_dieases[$key2] .= ", ".$dentry_dieases[$key1];
                                                        }
                                                    }
                                                    
                                                    //Actual Body Weight
                                                    if(empty($dentry_agwt[$key1]) || $dentry_agwt[$key1] == "" || (float)$dentry_agwt[$key1] == 0){ }
                                                    else{ $week_actwt[$key2] = (float)round($dentry_agwt[$key1],3); }
    
                                                    //Sales Mean Age
                                                    if(empty($placed_date[$bcode]) || $placed_date[$bcode] == ""){ }
                                                    else{
                                                        if(strtotime($adate) >= strtotime($placed_date[$bcode])){
                                                            $dlist = (INT)((strtotime($adate) - strtotime($placed_date[$bcode])) / 60 / 60 / 24);
                                                            $dlist2 = $dlist + 1;
                                                            $sbirds = (float)$sale_birds[$key3];
                                                            $tot_smean[$key2] += ($dlist2 * $sbirds);
                                                        }
                                                    }
                                                    //Actual Feed Consumed
                                                    foreach($feed_code as $ccode){
                                                        $key = $adate."@".$bcode."@".$ccode;
                                                        if(empty($dcon_qty[$key]) || $dcon_qty[$key] == ""){ $dcon_qty[$key] = 0; }
                                                        $tot_fcons[$bcode] += (float)round($dcon_qty[$key],2);
                                                        $week_fcons[$key2] += (float)round($dcon_qty[$key],2);
                                                        $week_cfcons[$key2] = (float)round($tot_fcons[$bcode],2);
                                                    }
                                                    $tot_sfeed_cons[$bcode] += (float)round($bstd_cum_feed[$week_bage[$key2]]);
                                                    $week_sfeed_cons[$key2] += (float)round($bstd_cum_feed[$week_bage[$key2]]);
                                                    $week_csfeed_cons[$key2] = (float)round($tot_sfeed_cons[$bcode],2);
    
                                                    //Closing Stock
                                                    $cls_birds[$key2] = ((float)$placed_birds[$key2] - ((float)$week_cmort[$key2] + (float)$tot_sbirds[$bcode] + (float)$tot_tbirds[$bcode]));    
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //Display Values
                            $tp_birds = $tm_birds = $tm_cbirds = $tm_per = $tm_cper = $tf_cons = $tf_ccons = $ts_birds = $ts_weight = $tb_sqty = $slno = 0;
                            $pb_bcnt = $rpb_bcnt = array();
                            for($wno = 1;$wno <= 10;$wno++){
                                $rtp_birds = $rtm_birds = $rtws_avgwht = $rtwa_avgwht = $rtws_dgain = $rtwa_dgain = $rtws_feed = $rtwa_cons = $rtws_fcr = 
                                $rtwa_fcr = $rtm_cbirds = $rtcs_avgwht = $rtca_avgwht = $rtcs_dgain = $rtca_dgain = $rtcs_feed = $rtca_feed = $rtcs_fcr = 
                                $rtca_fcr = $rts_birds = $rts_weight = $rslno = 0;
                                foreach($farm_acode as $fcode){
                                    if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                                    else{
                                        $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                                        foreach($blist as $bcode){
                                            $key2 = $wno."@".$bcode;
                                            if(empty($placed_birds[$key2]) || $placed_birds[$key2] == ""){ $placed_birds[$key2] = 0; }
                                            if(empty($week_mort[$key2]) || $week_mort[$key2] == ""){ $week_mort[$key2] = 0; }
                                            if(empty($week_cmort[$key2]) || $week_cmort[$key2] == ""){ $week_cmort[$key2] = 0; }
                                            if(empty($week_actwt[$key2]) || $week_actwt[$key2] == ""){ $week_actwt[$key2] = 0; }
                                            if(empty($week_sbirds[$key2]) || $week_sbirds[$key2] == ""){ $week_sbirds[$key2] = 0; }
                                            if(empty($tot_smean[$key2]) || $tot_smean[$key2] == ""){ $tot_smean[$key2] = 0; }
                                            if(empty($week_fcons[$key2]) || $week_fcons[$key2] == ""){ $week_fcons[$key2] = 0; }
                                            if(empty($week_sweight[$key2]) || $week_sweight[$key2] == ""){ $week_sweight[$key2] = 0; }
                                            if(empty($week_cfcons[$key2]) || $week_cfcons[$key2] == ""){ $week_cfcons[$key2] = 0; }
                                            if(empty($week_sfeed_cons[$key2]) || $week_sfeed_cons[$key2] == ""){ $week_sfeed_cons[$key2] = 0; }
                                            if(empty($week_csfeed_cons[$key2]) || $week_csfeed_cons[$key2] == ""){ $week_csfeed_cons[$key2] = 0; }
                                            if(empty($cls_birds[$key2]) || $cls_birds[$key2] == ""){ $cls_birds[$key2] = 0; }

                                            if(empty($placed_date[$bcode]) || (float)$placed_birds[$key2] == 0){ }
                                            else{
                                                $bhs = $farm_branch[$fcode];
                                                $lns = $farm_line[$fcode];
                                                $sps = $farm_supervisor[$fcode];
                                                $frs = $farm_farmer[$fcode];

                                                if(empty($week_bage[$key2]) || $week_bage[$key2] == ""){ $week_bage[$key2] = 0; }

                                                $w_ano = $week_alist[$wno];

                                                $pdate = date("d.m.Y",strtotime($placed_date[$bcode]));
                                                $pbirds = $placed_birds[$key2];
                                                $w_age = $week_bage[$key2];
                                                $w_mort = $week_mort[$key2];
                                                $p_mort = 0; if((float)$pbirds != 0){ $p_mort = round((((float)$w_mort / (float)$pbirds) * 100),2); }
                                                $w_cmort = $week_cmort[$key2];
                                                $p_cmort = 0; if((float)$pbirds != 0){ $p_cmort = round((((float)$w_cmort / (float)$pbirds) * 100),2); }
                                                if((int)$wno == 1){
                                                    $sab_wht = (float)$bstd_body_weight[$w_ano];
                                                }
                                                else{
                                                    $sab_wht = (float)$bstd_body_weight[$w_ano] - (float)$bstd_body_weight[(float)$w_ano - 7];
                                                }
                                                $scb_wht = $bstd_body_weight[$w_ano];
                                               
                                                $w_actwt = $wa_actwt = 0;
                                                if($w_ano == 1){
                                                    $wa_actwt = $week_actwt[$key2];
                                                }
                                                else if($w_ano > 1){
                                                    $wkey = ""; $wkey = ($wno - 1)."@".$bcode;
                                                    $wa_actwt = ((float)$week_actwt[$key2] - (float)$week_actwt[$wkey]);
                                                }
                                                else{ }

                                                $w_actwt = $week_actwt[$key2];
                                                $w_tsmean = $tot_smean[$key2]; $w_sbirds = $week_sbirds[$key2]; $w_cbirds = $cls_birds[$key2];

                                                $s_feed = $wa_feed = $a_feed = 0;
                                                
                                                if((int)$wno == 1){
                                                    $s_feed = $bstd_cum_feed[$w_ano];
                                                }
                                                else{
                                                    $s_feed = (float)$bstd_cum_feed[$w_ano] - (float)$bstd_cum_feed[$w_ano - 7];
                                                }
                                                $wa_feed = (((float)$week_fcons[$key2] / ((float)$pbirds - (float)$w_cmort)) * 1000);
                                                $a_feed = (float)$week_fcons[$key2];

                                                //FCR Calculations
                                                $ws_fcr = $wa_fcr = $a_fcr = $w_sweight = $avgwt = 0;
                                                //if((int)$wno == 1){ $ws_fcr = $bstd_fcr[$w_ano]; } else{ if((float)$sab_wht != 0){ $ws_fcr = (float)$s_feed / (float)$sab_wht; } }
                                                if((float)$sab_wht != 0){ $ws_fcr = (float)$s_feed / (float)$sab_wht; }

                                                if((float)$wa_actwt > 0){ $wa_fcr = ((float)$wa_feed / (float)$wa_actwt); }

                                                $w_sweight = $week_sweight[$key2];
                                                if((float)$week_sbirds[$key2] != 0){ $avgwt = (float)$week_sweight[$key2] / (float)$week_sbirds[$key2]; }

                                                if((float)$avgwt > 0 && ((float)$w_cbirds + (float)$week_sbirds[$key2]) > 0){
                                                    $a_fcr = $a_feed / (($avgwt) * ((float)$w_cbirds + (float)$week_sbirds[$key2]));
                                                    $lst_fct[$bcode] = $a_fcr;
                                                }
                                                else if(!empty($lst_fct[$bcode])){
                                                    $a_fcr = $lst_fct[$bcode];
                                                }
                                                else if((float)$w_actwt > 0 && ((float)$w_cbirds + (float)$week_sbirds[$key2]) > 0){
                                                    $a_fcr = $a_feed / (($w_actwt / 1000) * ((float)$w_cbirds + (float)$week_sbirds[$key2]));
                                                }
                                                else{ }

                                                //Day Gain Calculations
                                                $a_mage = $ws_dgain = $wa_dgain = $sc_dgain = $ac_dgain = $a_dgain = 0;
                                                if((float)$avgwt > 0){
                                                    if($w_sbirds > 0){ $a_mage = round(((float)$w_tsmean / (float)$w_sbirds),2); }
                                                    if($a_mage > 0){ $a_dgain = round((((float)$avgwt * 1000) / (float)$a_mage),2); }
                                                }
                                                
                                                $ws_dgain = round(((float)$sab_wht / 7),2);
                                                $wa_dgain = round(((float)$wa_actwt / 7),3);
                                                $sc_dgain = round(((float)$scb_wht / (float)$w_ano),3);
                                                $ac_dgain = round(((float)$w_actwt / (float)$w_ano),3);

                                                $s_cfeed = $a_cfeed = 0;
                                                $s_cfeed = $bstd_cum_feed[$w_ano];
                                                $a_cfeed = (((float)$week_cfcons[$key2] / ((float)$pbirds - (float)$w_cmort)) * 1000);

                                                $sc_fcr = 0; if((float)$scb_wht != 0){ $sc_fcr = (float)$s_cfeed / (float)$scb_wht; }
                                                $sa_fcr = 0; if((float)$w_actwt != 0){ $sa_fcr = (float)$a_cfeed / (float)$w_actwt; }

                                                $w_dieases = $week_dieases[$key2];
                                                $aweek = 0; $aweek = $allowed_bweek[$bcode];
                                                
                                                if($aweek == $wno){
                                                    $slno++; $rslno++;
                                                    $html .= '<tr>';
                                                    $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                                    $html .= '<td style="text-align:center;">'.$wno.'</td>';
                                                    $html .= '<td style="text-align:left;">'.$branch_name[$bhs].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$line_name[$lns].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$supervisor_name[$sps].'</td>';
                                                    //$html .= '<td style="text-align:left;">'.$farmer_name[$frs].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$week_hname[$bcode].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$week_sname[$bcode].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$batch_name[$bcode].'</td>';
                                                    $html .= '<td style="text-align:left;">'.$pdate.'</td>';
                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($pbirds)).'</td>';
                                                    $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($week_alist[$wno])).'</td>';
                                                    $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($dentry_mage[$bcode])).'</td>';
    
                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($w_mort)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.number_format_ind($p_mort).'</td>';

                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($sab_wht)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.round($wa_actwt,3).'</td>';

                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($ws_dgain)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($wa_dgain,3).'</td>';

                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($s_feed)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($wa_feed,2).'</td>';
                                                    $html .= '<td style="text-align:right;">'.round($ws_fcr,3).'</td>';
                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($wa_fcr,3).'</td>';
    
    
                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($w_cmort)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.number_format_ind($p_cmort).'</td>';

                                                    $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($scb_wht)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.round($w_actwt,3).'</td>';

                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($sc_dgain)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($ac_dgain,3).'</td>';

                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($s_cfeed)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($a_cfeed,2).'</td>';

                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($sc_fcr,3).'</td>';
                                                    $html .= '<td style="text-align:right;">'.decimal_adjustments($sa_fcr,3).'</td>';
    
                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($w_sbirds)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($w_sweight)).'</td>';
                                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($w_cbirds)).'</td>';
                                                    $html .= '<td style="text-align:left;">'.$w_dieases.'</td>';
    
                                                    $html .= '</tr>';
                                                    //Total Calculations
                                                    if(empty($pb_bcnt[$bcode]) || $pb_bcnt[$bcode] == ""){ $tp_birds += (float)$pbirds; $pb_bcnt[$bcode] = $bcode; }
                                                    
                                                    $tm_birds += ((float)$w_mort);
                                                    $tws_avgwht += ((float)$sab_wht);
                                                    $twa_avgwht += ((float)$wa_actwt);
                                                    $tws_dgain += ((float)$ws_dgain);
                                                    $twa_dgain += ((float)$wa_dgain);
                                                    $tws_feed += ((float)$s_feed);
                                                    $twa_cons += (float)$wa_feed;
                                                    $tws_fcr += (float)$ws_fcr;
                                                    $twa_fcr += (float)$wa_fcr;
                                                    
                                                    $tm_cbirds += ((float)$w_cmort);
                                                    $tcs_avgwht += (float)$scb_wht;
                                                    $tca_avgwht += (float)$w_actwt;
                                                    $tcs_dgain += (float)$sc_dgain;
                                                    $tca_dgain += (float)$ac_dgain;
                                                    $tcs_feed += (float)$s_cfeed;
                                                    $tca_feed += (float)$a_cfeed;
                                                    $tcs_fcr += (float)$sc_fcr;
                                                    $tca_fcr += (float)$sa_fcr;
                                                    
                                                    $ts_birds += (float)$w_sbirds;
                                                    $ts_weight += (float)$w_sweight;

                                                    //Week Wise Total Calculations
                                                    if(empty($rpb_bcnt[$bcode]) || $rpb_bcnt[$bcode] == ""){ $rtp_birds += (float)$pbirds; $rpb_bcnt[$bcode] = $bcode; }
                                                    
                                                    $rtm_birds += ((float)$w_mort);
                                                    $rtws_avgwht += ((float)$sab_wht);
                                                    $rtwa_avgwht += ((float)$wa_actwt);
                                                    $rtws_dgain += ((float)$ws_dgain);
                                                    $rtwa_dgain += ((float)$wa_dgain);
                                                    $rtws_feed += ((float)$s_feed);
                                                    $rtwa_cons += (float)$wa_feed;
                                                    $rtws_fcr += (float)$ws_fcr;
                                                    $rtwa_fcr += (float)$wa_fcr;
                                                    
                                                    $rtm_cbirds += ((float)$w_cmort);
                                                    $rtcs_avgwht += (float)$scb_wht;
                                                    $rtca_avgwht += (float)$w_actwt;
                                                    $rtcs_dgain += (float)$sc_dgain;
                                                    $rtca_dgain += (float)$ac_dgain;
                                                    $rtcs_feed += (float)$s_cfeed;
                                                    $rtca_feed += (float)$a_cfeed;
                                                    $rtcs_fcr += (float)$sc_fcr;
                                                    $rtca_fcr += (float)$sa_fcr;
                                                    
                                                    $rts_birds += (float)$w_sbirds;
                                                    $rts_weight += (float)$w_sweight;
                                                }
                                            }
                                        }
                                    }
                                }
                                if((float)$rtp_birds > 0){
                                    $rtm_per = $rtm_cper = $rtb_sqty = $raws_avgwt = $rawa_avgwt = $raws_dgain = $rawa_dgain = $raws_fcr = $rawa_fcr = $rcws_avgwt = 
                                    $rcwa_avgwt = $rcws_dgain = $rcwa_dgain = $rcws_fcr = $rcwa_fcr = 0;
                                    if((float)$rtp_birds != 0){ $rtm_per = (((float)$rtm_birds / (float)$rtp_birds) * 100); }
                                    if((float)$rtp_birds != 0){ $rtm_cper = (((float)$rtm_cbirds / (float)$rtp_birds) * 100); }
                                    $rtb_sqty = ((float)$rtp_birds - ((float)$rts_birds + (float)$rtm_birds));
                
                                    if((float)$rslno != 0){ $raws_avgwt = (((float)$rrtws_avgwht / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rawa_avgwt = (((float)$rtwa_avgwht / (float)$rslno)); }
                                    if((float)$rslno != 0){ $raws_dgain = (((float)$rtws_dgain / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rawa_dgain = (((float)$rtwa_dgain / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rtwa_cons = (((float)$rtwa_cons / (float)$rslno)); }
                                    if((float)$rslno != 0){ $raws_fcr = (((float)$rtws_fcr / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rawa_fcr = (((float)$rtwa_fcr / (float)$rslno)); }
                
                                    if((float)$rslno != 0){ $rcws_avgwt = (((float)$rtcs_avgwht / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rcwa_avgwt = (((float)$rtca_avgwht / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rcws_dgain = (((float)$rtcs_dgain / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rcwa_dgain = (((float)$rtca_dgain / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rtca_feed = (((float)$rtca_feed / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rcws_fcr = (((float)$rtcs_fcr / (float)$rslno)); }
                                    if((float)$rslno != 0){ $rcwa_fcr = (((float)$rtca_fcr / (float)$rslno)); }

                                    $html .= '<tr>';
                                    $html .= '<th style="text-align:right;" colspan="10">Total</th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rtp_birds)).'</th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rtm_birds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rtm_per,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($sab_wht,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rawa_avgwt,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($raws_dgain,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rawa_dgain,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($s_feed,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rtwa_cons,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($raws_fcr,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rawa_fcr,3).'</th>';
                
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rtm_cbirds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.number_format_ind($rtm_cper).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rcws_avgwt,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rcwa_avgwt,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rcws_dgain,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rcwa_dgain,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($s_cfeed,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rtca_feed,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rcws_fcr,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rcwa_fcr,3).'</th>';
                
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rts_birds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.number_format_ind($rts_weight).'</th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",subject: number_format_ind($rtb_sqty)).'</th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    if((float)$tp_birds != 0){ $tm_per = (((float)$tm_birds / (float)$tp_birds) * 100); }
                    if((float)$tp_birds != 0){ $tm_cper = (((float)$tm_cbirds / (float)$tp_birds) * 100); }
                    $tb_sqty = ((float)$tp_birds - ((float)$ts_birds + (float)$tm_birds));

                    if((float)$slno != 0){ $aws_avgwt = (((float)$tws_avgwht / (float)$slno)); }
                    if((float)$slno != 0){ $awa_avgwt = (((float)$twa_avgwht / (float)$slno)); }
                    if((float)$slno != 0){ $aws_dgain = (((float)$tws_dgain / (float)$slno)); }
                    if((float)$slno != 0){ $awa_dgain = (((float)$twa_dgain / (float)$slno)); }
                    if((float)$slno != 0){ $aws_fcr = (((float)$tws_fcr / (float)$slno)); }
                    if((float)$slno != 0){ $awa_fcr = (((float)$twa_fcr / (float)$slno)); }

                    if((float)$slno != 0){ $cws_avgwt = (((float)$tcs_avgwht / (float)$slno)); }
                    if((float)$slno != 0){ $cwa_avgwt = (((float)$tca_avgwht / (float)$slno)); }
                    if((float)$slno != 0){ $cws_dgain = (((float)$tcs_dgain / (float)$slno)); }
                    if((float)$slno != 0){ $cwa_dgain = (((float)$tca_dgain / (float)$slno)); }
                    if((float)$slno != 0){ $cws_fcr = (((float)$tcs_fcr / (float)$slno)); }
                    if((float)$slno != 0){ $cwa_fcr = (((float)$tca_fcr / (float)$slno)); }

                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="10">Total</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tp_birds)).'</th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tm_birds)).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tm_per,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($aws_avgwt,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($awa_avgwt,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($aws_dgain,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($awa_dgain,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tcs_feed,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($twa_cons,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($aws_fcr,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($awa_fcr,3).'</th>';

                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tm_cbirds)).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($tm_cper).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($cws_avgwt,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($cwa_avgwt,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($cws_dgain,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($cwa_dgain,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tws_feed,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tca_feed,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($cws_fcr,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($cwa_fcr,3).'</th>';

                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ts_birds)).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ts_weight).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",subject: number_format_ind($tb_sqty)).'</th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '</tr>';
                    $html .= '</tfoot>';
                    
                    echo $html;
                }
            }
        ?>
        </table><br/><br/><br/>
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
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();

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
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>