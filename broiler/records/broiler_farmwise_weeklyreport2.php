<?php
//broiler_farmwise_weeklyreport2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_farmwise_weeklyreport2.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_farmwise_weeklyreport2.php?db=$db&userid=".$user_code;
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
if(in_array("broiler_batch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_batch LIKE poulso6_admin_broiler_broilermaster.broiler_batch;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_purchases", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_purchases LIKE poulso6_admin_broiler_broilermaster.broiler_purchases;"; mysqli_query($conn,$sql1); }
if(in_array("item_stocktransfers", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stocktransfers LIKE poulso6_admin_broiler_broilermaster.item_stocktransfers;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_sales", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sales LIKE poulso6_admin_broiler_broilermaster.broiler_sales;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_daily_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_daily_record LIKE poulso6_admin_broiler_broilermaster.broiler_daily_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_medicine_record", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_medicine_record LIKE poulso6_admin_broiler_broilermaster.broiler_medicine_record;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_week_define_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_week_define_master LIKE poulso6_admin_broiler_broilermaster.broiler_week_define_master;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `broiler_batch`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("clot_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_batch` ADD `clot_no` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Chick Received Lot no' AFTER `book_num`"; mysqli_query($conn,$sql); }

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
$query = mysqli_query($conn,$sql); $branch_code = $branch_name = $branch_region = array();
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code']; }

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

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = "all"; $batch_type = "0"; $excel_type = "display"; $lot_nos = "";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $batch_type = $_POST['batch_type'];
    $lot_nos = $_POST['lot_nos'];
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
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="lot_nos">Lot No</label>
                                    <input type="text" name="lot_nos" id="lot_nos" class="form-control" value="<?php echo $lot_nos; ?>" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel2('main_table', '<?php echo $file_name; ?>')">
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
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Supervisor</th>'; $fhtml .= '<th id="order">Supervisor</th>';
            $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>';
            $nhtml .= '<th>Farm Code</th>'; $fhtml .= '<th id="order">Farm Code</th>';
            $nhtml .= '<th>Lot No.</th>'; $fhtml .= '<th id="order">Lot No.</th>';
            $nhtml .= '<th>Placement Date</th>'; $fhtml .= '<th id="order_date">Placement Date</th>';
            $nhtml .= '<th>No. birds Allocated</th>'; $fhtml .= '<th id="order_num">No. birds Allocated</th>';
            $nhtml .= '<th>Weighting Date</th>'; $fhtml .= '<th id="order_date">Weighting Date</th>';
            $nhtml .= '<th>Day on Weighting</th>'; $fhtml .= '<th id="order_num">Day on Weighting</th>';
            $nhtml .= '<th>Week No.</th>'; $fhtml .= '<th id="order_num">Week No.</th>';
            $nhtml .= '<th>Weekly Mortality</th>'; $fhtml .= '<th id="order_num">Weekly Mortality</th>';
            $nhtml .= '<th>No. Mortality Cumulative</th>'; $fhtml .= '<th id="order_num">No. Mortality Cumulative</th>';
            $nhtml .= '<th>Mortality Rate in %</th>'; $fhtml .= '<th id="order_num">Mortality Rate in %</th>';
            $nhtml .= '<th>Total Live birds</th>'; $fhtml .= '<th id="order_num">Total Live birds</th>';
            $nhtml .= '<th>Feed Intake(Kgs)</th>'; $fhtml .= '<th id="order_num">Total Feed Intake(Kgs)</th>';
            $nhtml .= '<th>Feed Intake(gms)</th>'; $fhtml .= '<th id="order_num">Total Feed Intake(gms)</th>';
            $nhtml .= '<th>Avg. Feed Per Bird</th>'; $fhtml .= '<th id="order_num">Avg. Feed Per Bird</th>';
            $nhtml .= '<th>Avg. Weight Per Bird</th>'; $fhtml .= '<th id="order_num">Avg. Weight Per Bird</th>';
            $nhtml .= '<th>FCR</th>'; $fhtml .= '<th id="order_num">FCR</th>';
            $nhtml .= '<th>Standard Weight</th>'; $fhtml .= '<th id="order_num">Standard Weight</th>';
            $nhtml .= '<th>Standard FCR</th>'; $fhtml .= '<th id="order_num">Standard FCR</th>';
            $nhtml .= '<th>CFCR</th>'; $fhtml .= '<th id="order_num">CFCR</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = $gc_fltr = $lno_fltr = "";
                if($regions != "all"){
                    $rbrh_alist = array(); foreach($branch_code as $bcode){ $rcode = $branch_region[$bcode]; if($rcode == $regions){ $rbrh_alist[$bcode] = $bcode; } }
                    $rbrh_list = implode("','",$rbrh_alist);
                    $rgn_fltr = " AND `branch_code` IN ('$rbrh_list')";
                }
                if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                if($supervisors != "all"){ $sup_fltr = " AND `supervisor_code` = '$supervisors'"; }
                if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }
                if($batch_type != "all"){ $gc_fltr = " AND `gc_flag` = '$batch_type'"; }
                if($lot_nos != ""){ $lno_fltr = " AND `clot_no` = '$lot_nos'"; }

                $farm_list = ""; $farm_list = implode("','", $farm_code);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$rgn_fltr."".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                $farm_list = ""; $farm_list = implode("','", $farm_alist);
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')".$gc_fltr."".$lno_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
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
                                $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_lotno[$row['code']] = $row['clot_no'];
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
                            $placed_birds = $wht_date = $wmort_qty = $bmort_qty = $cmort_qty = $thsd_bds = $avlb_qty = $sale_bqty = $bsale_bds = array();
                            $tplcd_bnos = 0;
                            foreach($farm_acode as $fcode){
                                if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                                else{
                                    $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                                    foreach($blist as $bcode){
                                        for($cdate = strtotime($sdate); $cdate <= strtotime($edate); $cdate += (86400)){
                                            $adate = date("Y-m-d",$cdate);
                                            //Chick (OR) Bird Calculations
                                            $key1 = $adate."@".$bcode."@".$chick_code;
                                            $key2 = $adate."@".$bcode."@".$bird_code;
                                            $c_week = $p_week = $m_age = 0;
                                            if(empty($weeks[$dentry_bage[$key1]]) && (float)$sale_birds[$key2] > 0){ $m_age = (INT)((strtotime($adate) - strtotime($sdate)) / 60 / 60 / 24); $c_week = $weeks[$m_age]; }
                                            else{ $c_week = $weeks[$dentry_bage[$key1]]; }
                                            $key3 = ""; $key3 = $c_week."@".$bcode;
                                            $p_week = $c_week - 1;
                                            $key4 = ""; $key4 = $p_week."@".$bcode;
                                            
                                            //Placed Birds
                                            if(empty($pur_qty[$key1]) || $pur_qty[$key1] == ""){ $pur_qty[$key1] = 0; }
                                            if(empty($tin_qty[$key1]) || $tin_qty[$key1] == ""){ $tin_qty[$key1] = 0; }
                                            $thsd_bds[$bcode] += ((float)round($pur_qty[$key1],2) + (float)round($tin_qty[$key1],2));
                                            $placed_birds[$key3] = (float)$thsd_bds[$bcode];

                                            //Weighting Date
                                            if(empty($dentry_agwt[$key1]) || $dentry_agwt[$key1] == ""){ $dentry_agwt[$key1] = 0; }
                                            if((float)$dentry_agwt[$key1] > 0){
                                                $wht_date[$key3] = $adate;
                                                $week_age[$key3] = (float)$dentry_agwt[$key1];
                                            }

                                            //Weekly Mortality
                                            if(empty($dentry_mqty[$key1]) || $dentry_mqty[$key1] == ""){ $dentry_mqty[$key1] = 0; }
                                            if(empty($dentry_cqty[$key1]) || $dentry_cqty[$key1] == ""){ $dentry_cqty[$key1] = 0; }
                                            $wmort_qty[$key3] += (float)round($dentry_mqty[$key1],2) + (float)round($dentry_cqty[$key1],2);
                                            //Cum. Mortality
                                            $bmort_qty[$bcode] += (float)round($dentry_mqty[$key1],2) + (float)round($dentry_cqty[$key1],2);
                                            $cmort_qty[$key3] = (float)$bmort_qty[$bcode];
                                            //Sale /Tout Qty
                                            $bsale_bds[$bcode] += ((float)round($sale_birds[$key1],2) + (float)round($tout_qty[$key1],2) + (float)round($pout_birds[$key1],2));
                                            $bsale_bds[$bcode] += ((float)round($sale_birds[$key2],2) + (float)round($tout_qty[$key2],2) + (float)round($pout_birds[$key2],2));
                                            $sale_bqty[$key3] = (float)$bsale_bds[$bcode];
                                            //Live Birds
                                            $avlb_qty[$key3] = (float)$thsd_bds[$bcode] - (float)$bmort_qty[$bcode] - (float)$bsale_bds[$bcode];

                                            //Actual Feed Consumed
                                            foreach($feed_code as $icode){
                                                $key5 = $adate."@".$bcode."@".$icode;
                                                $fcon_qty[$key3] += (float)round($dcon_qty[$key5],2);
                                            }
                                        }
                                        $tplcd_bnos += (float)$thsd_bds[$bcode];
                                    }
                                }
                            }
                            //Display Values
                            $rw_bnos = $rm_birds = $tm_birds = $rc_mbds = $tc_mbds = $rw_avbds = $rw_sbrds = $tw_sbrds = $tw_avbds = $tw_fdcon = 0;
                            for($wno = 1;$wno <= 10;$wno++){
                                $rw_bnos = $rm_birds = $rc_mbds = $rw_avbds = $rw_sbrds = $rw_fdcon = $data_flag = 0;
                                foreach($farm_acode as $fcode){
                                    if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                                    else{
                                        $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                                        foreach($blist as $bcode){
                                            $key2 = $wno."@".$bcode;

                                            if(empty($placed_date[$bcode]) || (float)$placed_birds[$key2] == 0){ }
                                            else{
                                                $data_flag = 1;
                                                $scode = $farm_supervisor[$fcode];
                                                $p_bird = $placed_birds[$key2];
                                                $p_date = date("d.m.Y",strtotime($placed_date[$bcode]));
                                                $w_date = $wht_date[$key2];
                                                $w_mqty = (float)$wmort_qty[$key2];
                                                $c_mqty = (float)$cmort_qty[$key2];
                                                $w_mper = 0; if((float)$p_bird != 0){ $w_mper = (((float)$w_mqty / (float)$p_bird) * 100); }
                                                $a_bqty = (float)$avlb_qty[$key2];
                                                $s_bqty = (float)$sale_bqty[$key2];
                                                $f_cqty = (float)$fcon_qty[$key2];
                                                $f_cgms = (float)$fcon_qty[$key2] * 1000;
                                                $a_fbds = 0; if((float)$a_bqty != 0){ $a_fbds = round(((float)$fcon_qty[$key2] / (float)$a_bqty * 1000),2); }
                                                $a_wpbd = $week_age[$key2];
                                                $a_bfcr = 0; if(((float)$a_wpbd * (float)$a_bqty) != 0){ $a_bfcr = ((float)$f_cgms / ((float)$a_wpbd * (float)$a_bqty)); }
                                                $s_bbdw = (float)$bstd_body_weight[$wno];
                                                $s_fcr = 0;
                                                $a_cfcr = (((2 - ((float)$a_bqty / 1000)) / 4) + (float)$a_bfcr);

                                                $slno++; $rslno++;
                                                $html .= '<tr>';
                                                $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                                $html .= '<td style="text-align:left;">'.$supervisor_name[$scode].'</td>';
                                                $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                                $html .= '<td style="text-align:left;">'.$farm_ccode[$fcode].'</td>';
                                                $html .= '<td style="text-align:left;">'.$batch_lotno[$bcode].'</td>';
                                                $html .= '<td style="text-align:left;" class="dates">'.$p_date.'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($p_bird)).'</td>';
                                                if(date("d.m.Y",strtotime($w_date)) != "01.01.1970"){ $html .= '<td style="text-align:left;" class="dates">'.date("d.m.Y",strtotime($w_date)).'</td>'; } else{ $html .= '<td style="text-align:left;"></td>'; }
                                                $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($week_alist[$wno])).'</td>';
                                                $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($wno)).'</td>';
                                                
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($w_mqty)).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($c_mqty)).'</td>';
                                                $html .= '<td style="text-align:right;">'.decimal_adjustments($w_mper,2).'</td>';
                                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($a_bqty)).'</td>';
                                                $html .= '<td style="text-align:right;">'.number_format_ind($f_cqty).'</td>';
                                                $html .= '<td style="text-align:right;">'.number_format_ind($f_cgms).'</td>';
                                                $html .= '<td style="text-align:right;">'.number_format_ind($a_fbds).'</td>';
                                                $html .= '<td style="text-align:right;">'.decimal_adjustments($a_wpbd,3).'</td>';
                                                $html .= '<td style="text-align:right;">'.decimal_adjustments($a_bfcr,3).'</td>';
                                                $html .= '<td style="text-align:right;"></td>';
                                                $html .= '<td style="text-align:right;">'.decimal_adjustments($s_fcr,3).'</td>';
                                                $html .= '<td style="text-align:right;">'.decimal_adjustments($a_cfcr,3).'</td>';
                                                $html .= '<td style="text-align:right;"></td>';
                                                $html .= '</tr>';
                                                
                                                //Total Calculations
                                                if(empty($pb_bcnt[$bcode]) || $pb_bcnt[$bcode] == ""){ $tp_birds += (float)$pbirds; $pb_bcnt[$bcode] = $bcode; }
                                                
                                                if((int)$wno == 1){ $rw_bnos += (float)$p_bird; }
                                                $rm_birds += (float)$w_mqty;
                                                $tm_birds += (float)$w_mqty;
                                                $rc_mbds = (float)$c_mqty;
                                                $tc_mbds = (float)$c_mqty;
                                                $rw_avbds += (float)$a_bqty;
                                                $tw_sbrds += (float)$s_bqty;
                                                $rw_fdcon += (float)$f_cqty;
                                                $tw_fdcon += (float)$f_cqty;
                                            }
                                        }
                                    }
                                }
                                if((float)$rw_bnos > 0 && $farms == "all"){
                                    $rw_mper = 0; if((float)$rw_bnos != 0){ $rw_mper = round((((float)$rm_birds / (float)$rw_bnos) * 100),2); }
                                    $a_fbds = 0; if((float)$rw_avbds != 0){ $a_fbds = round(((float)$rw_fdcon / (float)$rw_avbds * 1000),2); }

                                    $html .= '<tr class="thead3">';
                                    if((int)$wno == 1){ $html .= '<th style="text-align:right;" colspan="6">Lot Summary</th>'; } else{ $html .= '<th style="text-align:right;" colspan="6">Weekly Summary</th>'; }
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rw_bnos)).'</th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rm_birds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rc_mbds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_mper,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_avbds,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_fdcon,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(($rw_fdcon * 1000),3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($a_fbds,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($s_bbdw,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                                    $html .= '</tr>';
                                }
                                else if($data_flag == 1 && $farms == "all"){
                                    $rw_mper = 0; if((float)$rw_bnos != 0){ $rw_mper = round((((float)$rm_birds / (float)$rw_bnos) * 100),2); }
                                    $a_fbds = 0; if((float)$rw_avbds != 0){ $a_fbds = round(((float)$rw_fdcon / (float)$rw_avbds * 1000),2); }

                                    $html .= '<tr class="thead3">';
                                    if((int)$wno == 1){ $html .= '<th style="text-align:right;" colspan="6">Lot Summary</th>'; } else{ $html .= '<th style="text-align:right;" colspan="6">Weekly Summary</th>'; }
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rw_bnos)).'</th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:center;"></th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rm_birds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rc_mbds)).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_mper,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_avbds,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_fdcon,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(($rw_fdcon * 1000),3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($a_fbds,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments($s_bbdw,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,2).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                    $html .= '</tbody>';
                    $rw_mper = 0; if((float)$tplcd_bnos != 0){ $rw_mper = round((((float)$tc_mbds / (float)$tplcd_bnos) * 100),2); }
                    $ta_brds = (float)$tplcd_bnos - (float)$tc_mbds - (float)$tw_sbrds;
                    $a_fbds = 0; if((float)$ta_brds != 0){ $a_fbds = round(((float)$tw_fdcon / (float)$ta_brds * 1000),2); }

                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="6">Total</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tplcd_bnos)).'</th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tc_mbds)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tc_mbds)).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($rw_mper,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($ta_brds,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tw_fdcon,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments(($tw_fdcon * 1000),2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($a_fbds,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,2).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments(0,3).'</th>';
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '</tr>';
                    $html .= '</tfoot>';
                    
                    echo $html;
                }
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script type="text/javascript" src="table_sorting_wauto_slno.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_download_excel.js"></script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
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
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>