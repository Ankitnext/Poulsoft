<?php
//broiler_datewise_batchsummary1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Daily Report";
    include "header_head.php";
    $form_path = "broiler_datewise_batchsummary1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Daily Report";
    include "header_head.php";
    $form_path = "broiler_datewise_batchsummary1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

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

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `active` = '1' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $std_age[$row['age']] = $row['age'];
    $std_body_weight[$row['age']] = $row['body_weight'];
    $std_daily_gain[$row['age']] = $row['daily_gain'];
    $std_avg_daily_gain[$row['age']] = $row['avg_daily_gain'];
    $std_fcr[$row['age']] = $row['fcr'];
    $std_feed_consumed[$row['age']] = $row['feed_consumed'];
    $std_cum_feed[$row['age']] = $row['cum_feed'];
}

$tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = $batch_type = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    //$supervisors = $_POST['supervisors'];
    //$farms = $_POST['farms'];
    $excel_type = $_POST['export'];
}
$file_name = "Daily Report";
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
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="60px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Date</label>
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
                                <!--<div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php /*if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
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
                                        <?php } }*/ ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">-->
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel2('main_table', '<?php echo $file_name; ?>')">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <!--<div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>-->
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
            $html .= '<thead class="thead3">';
            $html .= '<tr style="text-align:center;" align="center">';
            $html .= '<th rowspan="2">Batch No</th>';
            $html .= '<th rowspan="2">Age</th>';
            $html .= '<th colspan="2">Temp./Hum</th>';
            $html .= '<th colspan="2">Weight</th>';
            $html .= '<th colspan="2">Mortality</th>';
            $html .= '<th rowspan="2">Sales</th>';
            $html .= '<th rowspan="2">CI Birds</th>';
            $html .= '<th colspan="3">Feed</th>';
            $html .= '<th rowspan="2">Feed used</th>';
            $html .= '<th rowspan="2">Medicine Used</th>';
            $html .= '<th colspan="9">WEEKLY FEED CONVERTION & FCR</th>';
            $html .= '</tr>';
            $html .= '<tr style="text-align:center;" align="center">';
            $html .= '<th>Min</th>';
            $html .= '<th>Max</th>';
            $html .= '<th>Std</th>';
            $html .= '<th>Act</th>';
            $html .= '<th>Mt</th>';
            $html .= '<th>%</th>';
            $html .= '<th>Cons</th>';
            $html .= '<th>Per</th>';
            $html .= '<th>Std</th>';
            $html .= '<th></th>';
            $html .= '<th>1</th>';
            $html .= '<th>2</th>';
            $html .= '<th>3</th>';
            $html .= '<th>4</th>';
            $html .= '<th>5</th>';
            $html .= '<th>6</th>';
            $html .= '<th>7</th>';
            $html .= '<th>>7</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                //Fetch Item Details
                $item_alist = array();
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $chick_code = "";
                while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $item_alist[$row['code']] = $row['code']; }
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bird_code = "";
                while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $item_alist[$row['code']] = $row['code']; }
                
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_cat = ""; $feed_code = $feed_name = array();
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; $item_alist[$row['code']] = $row['code']; }
                $item_list = implode("','",$item_alist);

                $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_code = $medvac_name = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }
            
                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = $gc_fltr = "";
                if($regions != "all"){
                    $rbrh_alist = array(); foreach($branch_code as $bcode){ if($branch_region[$bcode] == $regions){ $rbrh_alist[$bcode] = $bcode; } }
                    $rbrh_list = implode("','",$rbrh_alist);
                    $rgn_fltr = " AND `branch_code` IN ('$rbrh_list')";
                }
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
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` = '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $batch_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['batch_code']] = $row['batch_code']; }

                    $batch_size = sizeof($batch_alist);
                    if($batch_size > 0){
                        $batch_list = ""; $batch_list = implode("','", $batch_alist);
                        $sdate = $edate = ""; $batch_alist = array();

                        //Purchase
                        $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                        $query = mysqli_query($conn,$sql); $chk_iqty = $feed_iqty = $feed_aqty = array();
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['farm_batch']; $icode = $row['icode']; $key = $ibatch; $key2 = $ibatch."@".$icode;
                            if($row['icode'] == $chick_code || $row['icode'] == $bird_code){
                                $chk_iqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                            else if(!empty($feed_code[$row['icode']]) && $feed_code[$row['icode']] == $row['icode']){
                                $feed_iqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $feed_aqty[$key2] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                        }
                        //Stock-In
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['to_batch']; $icode = $row['code']; $key = $ibatch; $key2 = $ibatch."@".$icode;
                            //Chick/Bird Calculations
                            if($row['code'] == $chick_code || $row['code'] == $bird_code){
                                $chk_iqty[$key] += (float)$row['quantity'];
                            }
                            else if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                                $feed_iqty[$key] += (float)$row['quantity'];
                                $feed_aqty[$key2] += (float)$row['quantity'];
                            }
                        }
                        //Day Record
                        $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                        $query = mysqli_query($conn,$sql); $dentry_pmqty = $dentry_mqty = $dentry_pfcon = $dentry_fcon = $dentry_ifcon = $dentry_age = $dentry_tempc = $dentry_tempf = $dentry_avgwt = $dentry_wavgwt = array();
                        $fc_wk1 = $fc_wk2 = $fc_wk3 = $fc_wk4 = $fc_wk5 = $fc_wk6 = $fc_wk7 = $fc_wk8 = array();
                        $mc_wk1 = $mc_wk2 = $mc_wk3 = $mc_wk4 = $mc_wk5 = $mc_wk6 = $mc_wk7 = $mc_wk8 = array();
                        $abw_wk1 = $abw_wk2 = $abw_wk3 = $abw_wk4 = $abw_wk5 = $abw_wk6 = $abw_wk7 = $abw_wk8 = array();
                        $day1_awht = $day7_awht = $day14_awht = $day21_awht = $day28_awht = $day35_awht = $day42_awht = $day49_awht = $day56_awht = $day63_awht = $day70_awht = $tfeed_cqty = $wcum_fcqty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $ibatch = $row['batch_code']; $batch_alist[$ibatch] = $ibatch; $key = $ibatch;
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $dentry_pmqty[$key] += ((float)$row['mortality'] + (float)$row['culls']);
                                $dentry_pfcon[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                            }
                            $dentry_mqty[$key] += ((float)$row['mortality'] + (float)$row['culls']);
                            $dentry_fcon[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                            $dentry_bage[$key] = (float)$row['brood_age'];
                            $dentry_tempc[$key] = (float)$row['tempminc'];
                            $dentry_tempf[$key] = (float)$row['tempmaxc'];
                            if((float)$row['avg_wt'] > 0){ $dentry_avgwt[$key] = $row['avg_wt']; }

                            //Week Wise Avg Weight
                            if((float)$row['brood_age'] == 1){ $day1_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 7){ $day7_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 14){ $day14_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 21){ $day21_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 28){ $day28_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 35){ $day35_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 42){ $day42_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 49){ $day49_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 56){ $day56_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 63){ $day63_awht[$key] = $row['avg_wt']; }
                            if((float)$row['brood_age'] == 70){ $day70_awht[$key] = $row['avg_wt']; }

                            //Item Wise Feed Consumed
                            if($row['item_code1'] != "" && $row['item_code1'] != "select"){ $feed_aqty[$row['batch_code']."@".$row['item_code1']] -= (float)$row['kgs1']; }
                            if($row['item_code2'] != "" && $row['item_code2'] != "select"){ $feed_aqty[$row['batch_code']."@".$row['item_code2']] -= (float)$row['kgs2']; }
                            if(strtotime($row['date']) == strtotime($tdate)){
                                if($row['item_code1'] != "" && $row['item_code1'] != "select"){ $dentry_ifcon[$row['batch_code']."@".$row['item_code1']] += (float)$row['kgs1']; }
                                if($row['item_code2'] != "" && $row['item_code2'] != "select"){ $dentry_ifcon[$row['batch_code']."@".$row['item_code2']] += (float)$row['kgs2']; }
                            }
                            //Total Feed Consumed
                            $tfeed_cqty[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                            //Week Wise Mort+Culls and Feed Consumed
                            if((float)$row['brood_age'] <= 7){ $wcum_fcqty[$key."@1"] = $tfeed_cqty[$key]; $mc_wk1[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk1[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk1[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 8 && (float)$row['brood_age'] <= 14){ $wcum_fcqty[$key."@2"] = $tfeed_cqty[$key]; $mc_wk2[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk2[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk2[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 15 && (float)$row['brood_age'] <= 21){ $wcum_fcqty[$key."@3"] = $tfeed_cqty[$key]; $mc_wk3[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk3[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk3[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 22 && (float)$row['brood_age'] <= 28){ $wcum_fcqty[$key."@4"] = $tfeed_cqty[$key]; $mc_wk4[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk4[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk4[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 29 && (float)$row['brood_age'] <= 35){ $wcum_fcqty[$key."@5"] = $tfeed_cqty[$key]; $mc_wk5[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk5[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk5[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 36 && (float)$row['brood_age'] <= 42){ $wcum_fcqty[$key."@6"] = $tfeed_cqty[$key]; $mc_wk6[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk6[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk6[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 43 && (float)$row['brood_age'] <= 49){ $wcum_fcqty[$key."@7"] = $tfeed_cqty[$key]; $mc_wk7[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk7[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk7[$key] = $row['avg_wt']; } }
                            if((float)$row['brood_age'] >= 50){ $wcum_fcqty[$key."@8"] = $tfeed_cqty[$key]; $mc_wk8[$key] += ((float)$row['mortality'] + (float)$row['culls']); $fc_wk8[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']); if((float)$row['avg_wt'] > 0){ $abw_wk8[$key] = $row['avg_wt']; } }
                        }
                        //MedVac Record
                        $sql = "SELECT * FROM `broiler_medicine_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                        $query = mysqli_query($conn,$sql); $medvac_ifcon = array();
                        while($row = mysqli_fetch_assoc($query)){
                            if($row['item_code'] != "" && $row['item_code'] != "select" && strtotime($row['date']) == strtotime($tdate)){ $medvac_ifcon[$row['batch_code']."@".$row['item_code']] += (float)$row['quantity']; }
                        }
                        //Sale
                        $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$chick_code','$bird_code') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                        $query = mysqli_query($conn,$sql); $sold_birds = $sold_bds = $sold_weight = array();
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['farm_batch']; $key = $ibatch;
                            //Chick/Bird Calculations
                            if($row['icode'] == $chick_code || $row['icode'] == $bird_code){
                                $sold_birds[$key] += (float)$row['birds'];
                                $sold_weight[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);

                                $key = $row['date']."@".$ibatch;
                                $sold_bds[$key] += (float)$row['birds'];
                            }
                        }
                        //Stock-Out
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                        $query = mysqli_query($conn,$sql); $chk_oqty = $feed_oqty = array();
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['from_batch']; $icode = $row['code']; $key = $ibatch; $key2 = $ibatch."@".$icode;
                            //Chick/Bird Calculations
                            if($row['code'] == $chick_code || $row['code'] == $bird_code){
                                $chk_oqty[$key] += (float)$row['quantity'];
                            }
                            else if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                                $feed_oqty[$key] += (float)$row['quantity'];
                                $feed_aqty[$key2] -= (float)$row['quantity'];
                            }
                        }
                        //Bird Transfer to Processing
                        $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `date` <= '$tdate' AND `item_code` IN ('$chick_code','$bird_code') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                        $query = mysqli_query($conn,$sql); $pout_birds = $pout_weight = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $ibatch = $row['from_batch']; $key = $ibatch;
                            $pout_birds[$key] += (float)$row['birds'];
                            $pout_weight[$key] += (float)$row['weight'];
                        }

                        $batch_size = sizeof($batch_alist);
                        if($batch_size > 0){
                            //Fetch All actively available Farm and Batch List
                            $batch_list = ""; $batch_list = implode("','", $batch_alist);
                            $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_no`,`description` ASC";
                            $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $clot_no = $batch_book = $batch_farm1 = $batch_farm2 = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $clot_no[$row['code']] = $row['clot_no'];
                                $batch_book[$row['code']] = $row['book_num']; $batch_farm1[$row['code']] = $row['farm_code'];
                                if(empty($batch_farm2[$row['farm_code']]) || $batch_farm2[$row['farm_code']] == ""){ $batch_farm2[$row['farm_code']] = $row['code']; } else{ $batch_farm2[$row['farm_code']] .= ",".$row['code']; }
                            }
                            $farm_list1 = ""; $farm_list1 = implode("','", $batch_farm1);
                            $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') AND `code` IN ('$farm_list1')".$rgn_fltr."".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                            $query = mysqli_query($conn,$sql); $farm_acode = $farm_age = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $farm_acode[$row['code']] = $row['code']; $farm_aname[$row['code']] = $row['description'];
                                /*Age Wise Sorting*/
                                if(empty($batch_farm2[$row['code']]) || $batch_farm2[$row['code']] == ""){ }
                                else{
                                    $blist = array(); $blist = explode(",",$batch_farm2[$row['code']]);
                                    foreach($blist as $key){ $m_age = $dentry_bage[$key]; $farm_age[$row['code']] = $m_age; }
                                }
                            }
                            /*Age Wise Sorting*/
                            if(sizeof($farm_age) > 0 && sizeof($farm_age) == sizeof($farm_acode)){ arsort($farm_age); $farm_acode = array(); foreach($farm_age as $fcode => $age){ $farm_acode[$fcode] = $fcode; } }
                            
                            $slno = 0;
                            foreach($farm_acode as $fcode){
                                if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                                else{
                                    $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                                    foreach($blist as $key){
                                        $slno++;
                                        $bname = $batch_name[$key];
                                        $bmage = $dentry_bage[$key];
                                        $tempc = $dentry_tempc[$key];
                                        $tempf = $dentry_tempf[$key];
                                        $s_bwt = $std_body_weight[$bmage];
                                        $a_bwt = $dentry_avgwt[$key];

                                        //Chick Calculations
                                        $ci_qty = 0; if(!empty($chk_iqty[$key]) && (float)$chk_iqty[$key] > 0){ $ci_qty = $chk_iqty[$key]; }
                                        $pm_qty = 0; if(!empty($dentry_pmqty[$key]) && (float)$dentry_pmqty[$key] > 0){ $pm_qty = $dentry_pmqty[$key]; }
                                        $mc_qty = 0; if(!empty($dentry_mqty[$key]) && (float)$dentry_mqty[$key] > 0){ $mc_qty = $dentry_mqty[$key]; }
                                        $mc_per = 0; if((float)$ci_qty != 0){ $mc_per = round((((float)$mc_qty / (float)$ci_qty) * 100),2); }

                                        $csb_qty = 0; if(!empty($sold_birds[$key]) && (float)$sold_birds[$key] > 0){ $csb_qty = $sold_birds[$key]; }
                                        $sb_qty = 0; if(!empty($sold_bds[$tdate."@".$key]) && (float)$sold_bds[$tdate."@".$key] > 0){ $sb_qty = $sold_bds[$tdate."@".$key]; }
                                        $pp_qty = 0; if(!empty($pout_birds[$key]) && (float)$pout_birds[$key] > 0){ $pp_qty = $pout_birds[$key]; }
                                        $cb_qty = 0; $cb_qty = ((float)$ci_qty - (float)$mc_qty - (float)$csb_qty - (float)$pp_qty);

                                        //Feed Calculations
                                        //$fi_qty = 0; if(!empty($feed_iqty[$key]) && (float)$feed_iqty[$key] > 0){ $fi_qty = $feed_iqty[$key]; }
                                        //$fc_qty = 0; if(!empty($dentry_fcon[$key]) && (float)$dentry_fcon[$key] > 0){ $fc_qty = $dentry_fcon[$key]; }
                                        //$fo_qty = 0; if(!empty($feed_oqty[$key]) && (float)$feed_oqty[$key] > 0){ $fo_qty = $feed_oqty[$key]; }
                                        //$ft_qty = ((float)$fi_qty - (float)$fo_qty);
                                        //$pfc_per = 0; if((float)$ft_qty != 0){ $pfc_per = round((((float)$pfc_qty / (float)$ft_qty)),2); }
                                        $pfc_qty = 0; if(!empty($dentry_pfcon[$key]) && (float)$dentry_pfcon[$key] > 0){ $pfc_qty = $dentry_pfcon[$key]; }
                                        $pfc_pbd = 0; if((float)$ci_qty != 0){ $pfc_pbd = round((((float)$pfc_qty / (float)$ci_qty) * 1000),2); }
                                        $fs_qty = 0; if((float)$ci_qty != 0){ $fs_qty = (($std_feed_consumed[round($bmage)])); } // * $ci_qty

                                        $fd_used = $fd_stock = "";
                                        foreach($feed_code as $fd_code){
                                            $fkey = $key."@".$fd_code;
                                            if(empty($dentry_ifcon[$fkey]) || (float)$dentry_ifcon[$fkey] <= 0){ }
                                            else{
                                                if($fd_used == ""){ $fd_used = $feed_name[$fd_code]." - ".$dentry_ifcon[$fkey]; }
                                                else{ $fd_used .= ", ".$feed_name[$fd_code]." - ".$dentry_ifcon[$fkey]; }
                                            }
                                            if(empty($feed_aqty[$fkey]) || (float)$feed_aqty[$fkey] <= 0){ }
                                            else{
                                                if($fd_stock == ""){ $fd_stock = $feed_name[$fd_code]." - ".$feed_aqty[$fkey]; }
                                                else{ $fd_stock .= ", ".$feed_name[$fd_code]." - ".$feed_aqty[$fkey]; }
                                            }
                                        }

                                        //MedVac Calculations
                                        $mv_used = "";
                                        foreach($medvac_code as $mv_code){
                                            $mkey = $key."@".$mv_code;
                                            if(empty($medvac_ifcon[$mkey]) || (float)$medvac_ifcon[$mkey] <= 0){ }
                                            else{
                                                if($mv_used == ""){ $mv_used = $medvac_name[$mv_code]." - ".$medvac_ifcon[$mkey]; }
                                                else{ $mv_used .= ", ".$medvac_name[$mv_code]." - ".$medvac_ifcon[$mkey]; }
                                            }
                                        }
                                        //Weekly CON Calculations
                                        //$fcw_1 = $wcf_qty = 0; if(!empty($wcum_fcqty[$key."@1"]) && (float)$wcum_fcqty[$key."@1"] != 0){ $wcf_qty = $wcum_fcqty[$key."@1"]; } if((float)$wcf_qty != 0){ $fcw_1 = round(((float)$fc_wk1[$key] / (float)$wcf_qty),2); }
                                        $fcw_1 = 0; if((float)$fc_wk1[$key] != 0 && (float)$ci_qty != 0 && (float)$day7_awht[$key] != 0){ $fcw_1 = round(((((float)$day7_awht[$key] - (float)$day1_awht[$key]) / (((float)$fc_wk1[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_2 = 0; if((float)$fc_wk2[$key] != 0 && (float)$ci_qty != 0 && (float)$day14_awht[$key] != 0){ $fcw_2 = round(((((float)$day14_awht[$key] - (float)$day7_awht[$key]) / (((float)$fc_wk2[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_3 = 0; if((float)$fc_wk3[$key] != 0 && (float)$ci_qty != 0 && (float)$day21_awht[$key] != 0){ $fcw_3 = round(((((float)$day21_awht[$key] - (float)$day14_awht[$key]) / (((float)$fc_wk3[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_4 = 0; if((float)$fc_wk4[$key] != 0 && (float)$ci_qty != 0 && (float)$day28_awht[$key] != 0){ $fcw_4 = round(((((float)$day28_awht[$key] - (float)$day21_awht[$key]) / (((float)$fc_wk4[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_5 = 0; if((float)$fc_wk5[$key] != 0 && (float)$ci_qty != 0 && (float)$day35_awht[$key] != 0){ $fcw_5 = round(((((float)$day35_awht[$key] - (float)$day28_awht[$key]) / (((float)$fc_wk5[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_6 = 0; if((float)$fc_wk6[$key] != 0 && (float)$ci_qty != 0 && (float)$day42_awht[$key] != 0){ $fcw_6 = round(((((float)$day42_awht[$key] - (float)$day35_awht[$key]) / (((float)$fc_wk6[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_7 = 0; if((float)$fc_wk7[$key] != 0 && (float)$ci_qty != 0 && (float)$day49_awht[$key] != 0){ $fcw_7 = round(((((float)$day49_awht[$key] - (float)$day42_awht[$key]) / (((float)$fc_wk7[$key] / (float)$ci_qty) * 1000)) * 100),2); }
                                        $fcw_8 = 0; if((float)$fc_wk8[$key] != 0 && (float)$ci_qty != 0 && (float)$day56_awht[$key] != 0){ $fcw_8 = round(((((float)$day56_awht[$key] - (float)$day49_awht[$key]) / (((float)$fc_wk8[$key] / (float)$ci_qty) * 1000)) * 100),2); }

                                        //if(!empty($sold_weight[$key]) && (float)$sold_weight[$key] != 0 && (float)$sold_birds[$key] != 0){ $week_awht = $sold_weight[$key] / (float)$sold_birds[$key]; }
                                        //else if((float)$abw_wk1[$key] > 0){ $sw_qty = round(($abw_wk1[$key] / 1000 * $cb_qty),2); }
                                        
                                        //Weekly FCR Calculations
                                        $cum_fqty = $chk_cqty = $week_awht = 0;
                                        $fcr_1 = 0; $cum_fqty += (float)$fc_wk1[$key]; $week_awht = (float)$abw_wk1[$key] / 1000; $chk_cqty = (float)$ci_qty - (float)$mc_wk1[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_1 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_2 = 0; $cum_fqty += (float)$fc_wk2[$key]; $week_awht = (float)$abw_wk2[$key] / 1000; $chk_cqty -= (float)$mc_wk2[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_2 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_3 = 0; $cum_fqty += (float)$fc_wk3[$key]; $week_awht = (float)$abw_wk3[$key] / 1000; $chk_cqty -= (float)$mc_wk3[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_3 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_4 = 0; $cum_fqty += (float)$fc_wk4[$key]; $week_awht = (float)$abw_wk4[$key] / 1000; $chk_cqty -= (float)$mc_wk4[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_4 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_5 = 0; $cum_fqty += (float)$fc_wk5[$key]; $week_awht = (float)$abw_wk5[$key] / 1000; $chk_cqty -= (float)$mc_wk5[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_5 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_6 = 0; $cum_fqty += (float)$fc_wk6[$key]; $week_awht = (float)$abw_wk6[$key] / 1000; $chk_cqty -= (float)$mc_wk6[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_6 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_7 = 0; $cum_fqty += (float)$fc_wk7[$key]; $week_awht = (float)$abw_wk7[$key] / 1000; $chk_cqty -= (float)$mc_wk7[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_7 = round(((float)$cum_fqty / (float)$sw_qty),3); }
                                        $fcr_8 = 0; $cum_fqty += (float)$fc_wk8[$key]; $week_awht = (float)$abw_wk8[$key] / 1000; $chk_cqty -= (float)$mc_wk8[$key]; $sw_qty = (float)$week_awht * (float)$chk_cqty; if((float)$sw_qty != 0 && (float)$cum_fqty != 0){ $fcr_8 = round(((float)$cum_fqty / (float)$sw_qty),3); }

                                        $html .= '<tr>';
                                        $html .= '<td style="width:150px;white-space: normal;text-align:left;" rowspan="2">'.$bname.'</td>';
                                        $html .= '<td style="text-align:center;" rowspan="2">'.str_replace(".00","",number_format_ind($bmage)).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($tempc,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($tempf,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($s_bwt,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($a_bwt,2).'</td>';
                                        $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($pm_qty)).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($mc_per,2).'</td>';
                                        $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($sb_qty)).'</td>';
                                        $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($cb_qty)).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($pfc_qty,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($pfc_pbd,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fs_qty,2).'</td>';
                                        $html .= '<td style="width:250px;white-space: normal;text-align:left;">'.$fd_used.'</td>';
                                        $html .= '<td style="width:250px;white-space: normal;text-align:left;" rowspan="2">'.$mv_used.'</td>';
                                        $html .= '<td style="text-align:left;"><b>CON</b></td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_1,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_2,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_3,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_4,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_5,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_6,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_7,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcw_8,2).'</td>';
                                        $html .= '</tr>';

                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;" colspan="12"><b>FEED STOCK: </b>'.$fd_stock.'</td>';
                                        $html .= '<td style="text-align:left;"><b>FCR</b></td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_1,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_2,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_3,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_4,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_5,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_6,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_7,3).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr_8,3).'</td>';
                                        $html .= '</tr>';
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
            $html .= '<th style="text-align:left;" colspan="24"></th>';
            /*$html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ta_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.decimal_adjustments($a_wht,2).'</th>';
            $html .= '<th style="text-align:right;">'.decimal_adjustments($tb_wht,2).'</th>';*/
            $html .= '</tr>';
            $html .= '</tfoot>';
            
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_rowwise_colors.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                }
                else{ }
            }
        </script>
        <script>
            function fetch_farms_details(a){
                var regions = document.getElementById("regions").value;
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = "all"; //document.getElementById("supervisors").value;
                var user_code = '<?php echo $user_code; ?>';
                var rf_flag = bf_flag = lf_flag = sf_flag = ff_flag = 0;
                if(a.match("regions")){ rf_flag = 1; } else if(a.match("branches")){ bf_flag = 1; } else if(a.match("lines")){ lf_flag = 1; } else{ ff_flag = 1; } // else if(a.match("supervisors")){ sf_flag = 1; }
                    
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
                        //var supr_list = fltr_dt2[1];
                        //var farm_list = fltr_dt2[2];

                        if(rf_flag == 1){
                            removeAllOptions(document.getElementById("branches"));
                            removeAllOptions(document.getElementById("lines"));
                            //removeAllOptions(document.getElementById("supervisors"));
                            //removeAllOptions(document.getElementById("farms"));
                            $('#branches').append(brnh_list);
                            $('#lines').append(line_list);
                            //$('#supervisors').append(supr_list);
                            //$('#farms').append(farm_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("lines"));
                            //removeAllOptions(document.getElementById("supervisors"));
                            //removeAllOptions(document.getElementById("farms"));
                            $('#lines').append(line_list);
                            //$('#supervisors').append(supr_list);
                            //$('#farms').append(farm_list);
                        }
                        /*else if(lf_flag == 1){
                            removeAllOptions(document.getElementById("supervisors"));
                            removeAllOptions(document.getElementById("farms"));
                            $('#supervisors').append(supr_list);
                            $('#farms').append(farm_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("farms"));
                            $('#farms').append(farm_list);
                        }*/
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
                /*else if(f_cnt == 3){
                    var s_val = slist = "";
                    $('#supervisors').select2();
                    for(var option of document.getElementById("supervisors").options){
                        option.selected = false;
                        s_val = option.value;
                        slist = ''; slist = '<?php //echo $supervisors; ?>';
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
                        flist = ''; flist = '<?php //echo $farms; ?>';
                        if(f_val == flist){ option.selected = true; }
                    }
                    $('#farms').select2();
                    var fx = "farms"; fetch_farms_details(fx); f_cnt = f_cnt + 1;
                }*/
                else{ }
                
                if(f_cnt <= 2){ setTimeout(set_auto_selectors, 300); }
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