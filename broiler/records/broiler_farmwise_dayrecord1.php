<?php
//broiler_farmwise_dayrecord1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Daily Entry Report";
    include "header_head.php";
    $form_path = "broiler_farmwise_dayrecord1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Daily Entry Report";
    include "header_head.php";
    $form_path = "broiler_farmwise_dayrecord1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Daily Entry Report";
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

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = "all"; $batch_type = "0"; $excel_type = "display"; $lot_nos = "";
if(isset($_POST['submit_report']) == true){
    $fdate = $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $batch_type = $_POST['batch_type'];
    //$lot_nos = $_POST['lot_nos'];
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
                                <!--<div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php //echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>-->
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
                                <!--<div class="m-2 form-group" style="width: 210px;">
                                    <label for="lot_nos">Lot No</label>
                                    <input type="text" name="lot_nos" id="lot_nos" class="form-control" value="<?php //echo $lot_nos; ?>" style="padding:0;padding-left:2px;width:200px;" />
                                </div>-->
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
            $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>';
            $nhtml .= '<th>Batch</th>'; $fhtml .= '<th id="order">Batch</th>';
            //$nhtml .= '<th>Lot No.</th>'; $fhtml .= '<th id="order">Lot No.</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>Birds</th>'; $fhtml .= '<th id="order_num">Birds</th>';
            $nhtml .= '<th>Mt</th>'; $fhtml .= '<th id="order_num">Mt</th>';
            $nhtml .= '<th>Mt %</th>'; $fhtml .= '<th id="order_num">Mt %</th>';
            $nhtml .= '<th>Feed</th>'; $fhtml .= '<th id="order">Feed</th>';
            $nhtml .= '<th>Total Feed</th>'; $fhtml .= '<th id="order_num">Total Feed</th>';
            $nhtml .= '<th>Total Feed Rec</th>'; $fhtml .= '<th id="order_num">Total Feed Rec</th>';
            $nhtml .= '<th>Bird Sale</th>'; $fhtml .= '<th id="order_num">Bird Sale</th>';
            $nhtml .= '<th>Wt</th>'; $fhtml .= '<th id="order_num">Wt</th>';
            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
            $nhtml .= '<th>Amt</th>'; $fhtml .= '<th id="order_num">Amt</th>';
            $nhtml .= '<th>Avg Wt</th>'; $fhtml .= '<th id="order_num">Avg Wt</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
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
                /*
                $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $medvac_code = $medvac_name = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }
                */
                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = $gc_fltr = $lno_fltr = "";
                if($regions != "all"){ $rgn_fltr = " AND `region_code` = '$regions'"; }
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
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` = '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $batch_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['batch_code']] = $row['batch_code']; }

                    $batch_size = sizeof($batch_alist);
                    if($batch_size > 0){
                        $batch_list = ""; $batch_list = implode("','", $batch_alist); $batch_alist = array();

                        //Purchase
                        $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                        $query = mysqli_query($conn,$sql); $chk_iqty = $chk_aqty = $feed_iqty = $feed_aqty = $tfd_iqty = array();
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['farm_batch']; $icode = $row['icode']; $key = $ibatch;
                            if($row['icode'] == $chick_code || $row['icode'] == $bird_code){
                                $chk_iqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $chk_aqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                            else if(!empty($feed_code[$row['icode']]) && $feed_code[$row['icode']] == $row['icode']){
                                $feed_iqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $feed_aqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $tfd_iqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                        }
                        //Stock-In
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['to_batch']; $icode = $row['code']; $key = $ibatch;
                            //Chick/Bird Calculations
                            if($row['code'] == $chick_code || $row['code'] == $bird_code){
                                $chk_iqty[$key] += (float)$row['quantity'];
                                $chk_aqty[$key] += (float)$row['quantity'];
                            }
                            else if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                                $feed_iqty[$key] += (float)$row['quantity'];
                                $feed_aqty[$key] += (float)$row['quantity'];
                            }
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $tfd_iqty[$key] += (float)$row['quantity'];
                            }
                        }
                        //Day Record
                        $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                        $query = mysqli_query($conn,$sql); $chk_mqty = $dentry_mqty = $dentry_bage = $feed_cqty = $dentry_feed = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $ibatch = $row['batch_code']; $batch_alist[$ibatch] = $ibatch; $key = $ibatch;
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $chk_aqty[$key] -= ((float)$row['mortality'] + (float)$row['culls']);
                            }
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $chk_mqty[$key] += ((float)$row['mortality'] + (float)$row['culls']);
                                $feed_aqty[$key] -= ((float)$row['kgs1'] + (float)$row['kgs2']);
                                $feed_cqty[$key] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                                $dentry_bage[$key] = (float)$row['brood_age'];

                                if(!empty($feed_name[$row['item_code1']]) && $feed_name[$row['item_code1']] != ""){
                                    if(empty($dentry_feed[$key]) || $dentry_feed[$key] == ""){ $dentry_feed[$key] = $feed_name[$row['item_code1']]; }
                                    else{ $dentry_feed[$key] .= ", ".$feed_name[$row['item_code1']]; }
                                }
                                if(!empty($feed_name[$row['item_code2']]) && $feed_name[$row['item_code2']] != ""){
                                    if(empty($dentry_feed[$key]) || $dentry_feed[$key] == ""){ $dentry_feed[$key] = $feed_name[$row['item_code2']]; }
                                    else{ $dentry_feed[$key] .= ", ".$feed_name[$row['item_code2']]; }
                                }
                            }
                            if((float)$row['avg_wt'] > 0){ $dentry_avgwt[$key] = $row['avg_wt']; }
                        }
                        //MedVac Record
                        $sql = "SELECT * FROM `broiler_medicine_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                        $query = mysqli_query($conn,$sql); $medvac_ifcon = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $ibatch = $row['batch_code']; $batch_alist[$ibatch] = $ibatch; $key = $ibatch;
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $medvac_ifcon[$key] += (float)$row['quantity'];
                            }
                        }
                        //Sale
                        $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$chick_code','$bird_code') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                        $query = mysqli_query($conn,$sql); $sold_birds = $sold_weight = $sold_amount = array();
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['farm_batch']; $batch_alist[$ibatch] = $ibatch; $key = $ibatch;
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $chk_aqty[$key] -= ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $sold_birds[$key] += (float)$row['birds'];
                                $sold_weight[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $sold_amount[$key] += (float)$row['item_tamt'];
                            }
                        }
                        //Stock-Out
                        $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                        $query = mysqli_query($conn,$sql); $chk_oqty = $chk_oamt = $feed_oqty = array();
                        while($row = mysqli_fetch_array($query)){
                            $ibatch = $row['from_batch']; $batch_alist[$ibatch] = $ibatch; $key = $ibatch;
                            //Chick/Bird Calculations
                            if($row['code'] == $chick_code || $row['code'] == $bird_code){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $chk_aqty[$key] -= (float)$row['quantity'];
                                }
                                if(strtotime($row['date']) == strtotime($tdate)){
                                    $chk_oqty[$key] += (float)$row['quantity'];
                                    $chk_oamt[$key] += (float)$row['amount'];
                                }
                            }
                            else if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $feed_aqty[$key] -= (float)$row['quantity'];
                                }
                                if(strtotime($row['date']) == strtotime($tdate)){
                                    $feed_oqty[$key] += (float)$row['quantity'];
                                }
                            }
                        }
                        //Bird Transfer to Processing
                        $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `date` <= '$tdate' AND `item_code` IN ('$chick_code','$bird_code') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                        $query = mysqli_query($conn,$sql); $pout_birds = $pout_weight = $pout_amount = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $ibatch = $row['from_batch']; $key = $ibatch;
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $chk_aqty[$key] -= (float)$row['birds'];
                            }
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $pout_birds[$key] += (float)$row['birds'];
                                $pout_weight[$key] += (float)$row['weight'];
                                $pout_amount[$key] += (float)$row['avg_amount'];
                            }
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
                                $farm_acode[$row['code']] = $row['code'];
                                /*Age Wise Sorting*/
                                if(empty($batch_farm2[$row['code']]) || $batch_farm2[$row['code']] == ""){ }
                                else{
                                    $blist = array(); $blist = explode(",",$batch_farm2[$row['code']]);
                                    foreach($blist as $key){ $m_age = $dentry_bage[$key]; $farm_age[$row['code']] = $m_age; }
                                }
                            }
                            /*Age Wise Sorting*/
                            if(sizeof($farm_age) > 0 && sizeof($farm_age) == sizeof($farm_acode)){ arsort($farm_age); $farm_acode = array(); foreach($farm_age as $fcode => $age){ $farm_acode[$fcode] = $fcode; } }
                            
                            $slno = $tco_bds = $tcm_bds = $tfc_qty = $ttfi_qty = $tbs_bds = $tbs_wht = $tbs_amt = 0;
                            foreach($farm_acode as $fcode){
                                if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                                else{
                                    $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                                    foreach($blist as $key){
                                        $bname = $batch_name[$key];
                                        $bmage = $dentry_bage[$key];
                                        $a_bwt = $dentry_avgwt[$key];
                                        $fname = $dentry_feed[$key];

                                        $cp_bds = 0; if(!empty($chk_iqty[$key]) && $chk_iqty[$key] != "" && (float)$chk_iqty[$key] != 0){ $cp_bds = $chk_iqty[$key]; }
                                        $co_bds = 0; if(!empty($chk_aqty[$key]) && $chk_aqty[$key] != "" && (float)$chk_aqty[$key] != 0){ $co_bds = $chk_aqty[$key]; }
                                        $cm_bds = 0; if(!empty($chk_mqty[$key]) && $chk_mqty[$key] != "" && (float)$chk_mqty[$key] != 0){ $cm_bds = $chk_mqty[$key]; }
                                        $cm_per = 0; if((float)$cp_bds != 0){ $cm_per = (((float)$cm_bds / (float)$cp_bds) * 100); }

                                        $fc_qty = 0; if(!empty($feed_cqty[$key]) && $feed_cqty[$key] != "" && (float)$feed_cqty[$key] != 0){ $fc_qty = $feed_cqty[$key]; }
                                        $tfi_qty = 0; if(!empty($tfd_iqty[$key]) && $tfd_iqty[$key] != "" && (float)$tfd_iqty[$key] != 0){ $tfi_qty = $tfd_iqty[$key]; }

                                        $cs_bds = 0; if(!empty($sold_birds[$key]) && $sold_birds[$key] != "" && (float)$sold_birds[$key] != 0){ $cs_bds = $sold_birds[$key]; }
                                        $cs_wht = 0; if(!empty($sold_weight[$key]) && $sold_weight[$key] != "" && (float)$sold_weight[$key] != 0){ $cs_wht = $sold_weight[$key]; }
                                        $cs_amt = 0; if(!empty($sold_amount[$key]) && $sold_amount[$key] != "" && (float)$sold_amount[$key] != 0){ $cs_amt = $sold_amount[$key]; }
                                        $ct_bds = 0; if(!empty($chk_oqty[$key]) && $chk_oqty[$key] != "" && (float)$chk_oqty[$key] != 0){ $ct_bds = $chk_oqty[$key]; }
                                        $ct_amt = 0; if(!empty($chk_oamt[$key]) && $chk_oamt[$key] != "" && (float)$chk_oamt[$key] != 0){ $ct_amt = $chk_oamt[$key]; }
                                        $cg_bds = 0; if(!empty($pout_birds[$key]) && $pout_birds[$key] != "" && (float)$pout_birds[$key] != 0){ $cg_bds = $pout_birds[$key]; }
                                        $cg_wht = 0; if(!empty($pout_weight[$key]) && $pout_weight[$key] != "" && (float)$pout_weight[$key] != 0){ $cg_wht = $pout_weight[$key]; }
                                        $cg_amt = 0; if(!empty($pout_amount[$key]) && $pout_amount[$key] != "" && (float)$pout_amount[$key] != 0){ $cg_amt = $pout_amount[$key]; }

                                        $bs_bds = ((float)$cs_bds + (float)$ct_bds + (float)$cg_bds);
                                        $bs_wht = ((float)$cs_wht + (float)$cg_wht);
                                        $bs_amt = ((float)$cs_amt + (float)$ct_amt + (float)$cg_amt);
                                        $bs_prc = 0; if((float)$bs_wht != 0){ $bs_prc = ((float)$bs_amt / (float)$bs_wht); }

                                        $slno++;
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                        $html .= '<td style="text-align:left;">'.$bname.'</td>';
                                        $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind($bmage)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($co_bds)).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cm_bds)).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($cm_per,2).'</td>';
                                        $html .= '<td style="text-align:left;">'.$fname.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($fc_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($tfi_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($bs_bds)).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($bs_wht).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($bs_prc,2).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($bs_amt).'</td>';
                                        $html .= '<td style="text-align:right;">'.decimal_adjustments($a_bwt,3).'</td>';
                                        $html .= '</tr>';
                                        
                                        //Total Calculations
                                        $tcp_bds += (float)$cp_bds;
                                        $tco_bds += (float)$co_bds;
                                        $tcm_bds += (float)$cm_bds;
                                        $tfc_qty += (float)$fc_qty;
                                        $ttfi_qty += (float)$tfi_qty;
                                        $tbs_bds += (float)$bs_bds;
                                        $tbs_wht += (float)$bs_wht;
                                        $tbs_amt += (float)$bs_amt;
                                    }
                                }
                            }
                        }
                    }
                    $html .= '</tbody>';
                    $tbs_prc = 0; if((float)$tbs_wht != 0){ $tbs_prc = ((float)$tbs_amt / (float)$tbs_wht); }
                    $tcm_per = 0; if((float)$tcp_bds != 0){ $tcm_per = (((float)$tcm_bds / (float)$tcp_bds) * 100); }
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="4">Total</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tco_bds)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tcm_bds)).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tcm_per,2).'</th>';
                    $html .= '<th style="text-align:left;"></th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($tfc_qty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ttfi_qty).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tbs_bds)).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($tbs_wht).'</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tbs_prc,2).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($tbs_amt).'</th>';
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