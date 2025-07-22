<?php
//broiler_farmwise_batchhistory1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Farmer Batch History Report";
    include "header_head.php";
    $form_path = "broiler_farmwise_batchhistory1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Farmer Batch History Report";
    include "header_head.php";
    $form_path = "broiler_farmwise_batchhistory1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Farmer Batch History";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_rearingcharge", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_rearingcharge LIKE poulso6_admin_broiler_broilermaster.broiler_rearingcharge;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }

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

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $desig_alist = array();
while($row = mysqli_fetch_assoc($query)){ $desig_alist[$row['code']] = $row['code']; }

$desig_list = implode("','",$desig_alist);
$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $supervisor_code = $supervisor_name = array();
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $region_code = $region_name = array();
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; $branch_region[$row['code']] = $row['region_code'];}

$sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $farmer_name = $farmer_mobile1 = $farmer_mobile2 = array();
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }

$sql = "SELECT * FROM `broiler_diseases` WHERE `dflag` = '0'";
$query = mysqli_query($conn,$sql); $dieases_name = array();
while($row = mysqli_fetch_assoc($query)){ $dieases_name[$row['trnum']] = $row['name']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC";
$query = mysqli_query($conn,$sql); $bstd_body_weight = $bstd_daily_gain = $bstd_avg_daily_gain = $bstd_fcr = $bstd_cum_feed = $bstd_feed_consumed = array();
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; $bstd_feed_consumed[$row['age']] = (float)$row['feed_consumed']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $vendor_code = $vendor_name = array();
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

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
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_category = $row['category']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bird_code = "";
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_cat_coa = array();
while($row = mysqli_fetch_assoc($query)){ $item_cat_coa[$row['code']] = $row['iac']; }

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = $batch_type = "1"; $report_view = "hd"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $report_view = $_REQUEST['report_view'];
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
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
                            </div>
                            <div class="row">
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
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Branch</th>'; $fhtml .= '<th id="order">Branch</th>';
            $nhtml .= '<th>Place</th>'; $fhtml .= '<th id="order">Place</th>';
            $nhtml .= '<th>Supervisor</th>'; $fhtml .= '<th id="order">Supervisor</th>';
            $nhtml .= '<th>Farmer</th>'; $fhtml .= '<th id="order">Farmer</th>';
            $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>';
            $nhtml .= '<th>Hatchery</th>'; $fhtml .= '<th id="order">Hatchery</th>';
            $nhtml .= '<th>Supplier</th>'; $fhtml .= '<th id="order">Supplier</th>';
            $nhtml .= '<th>Batch No.</th>'; $fhtml .= '<th id="order_num">Batch No.</th>';
            $nhtml .= '<th>Placement Date</th>'; $fhtml .= '<th id="order_date">Placement Date</th>';
            $nhtml .= '<th>Liquidation Date</th>'; $fhtml .= '<th id="order_date">Liquidation Date</th>';
            $nhtml .= '<th>Birds Housed</th>'; $fhtml .= '<th id="order_num">Birds Housed</th>';
            $nhtml .= '<th>Birds Liquidated</th>'; $fhtml .= '<th id="order_num">Birds Liquidated</th>';
            $nhtml .= '<th>Mean Age</th>'; $fhtml .= '<th id="order_num">Mean Age</th>';
            $nhtml .= '<th>Mort</th>'; $fhtml .= '<th id="order_num">Mort</th>';
            $nhtml .= '<th>Mort%</th>'; $fhtml .= '<th id="order_num">Mort%</th>';
            $nhtml .= '<th>ABW</th>'; $fhtml .= '<th id="order_num">ABW</th>';
            $nhtml .= '<th>FCR</th>'; $fhtml .= '<th id="order_num">FCR</th>';
            $nhtml .= '<th>CFCR</th>'; $fhtml .= '<th id="order_num">CFCR</th>';
            $nhtml .= '<th>DAY GAIN</th>'; $fhtml .= '<th id="order_num">DAY GAIN</th>';
            $nhtml .= '<th>EEF</th>'; $fhtml .= '<th id="order_num">EEF</th>';
            $nhtml .= '<th>Prod. Cost</th>'; $fhtml .= '<th id="order_num">Prod. Cost</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $date_filter = "";
                if($_REQUEST['report_view'] == "hd"){
                    $date_filter = " AND `start_date` >= '$fdate' AND `start_date` <= '$tdate'";
                }
                else if($_REQUEST['report_view'] == "ld"){
                    $date_filter = " AND `liquid_date` >= '$fdate' AND `liquid_date` <= '$tdate'";
                }
                else if($_REQUEST['report_view'] == "gd"){
                    $date_filter = " AND `date` >= '$fdate' AND `date` <= '$tdate'";
                }
                else{ }

                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = $gc_fltr = "";
                if($regions != "all"){ $rgn_fltr = " AND `region_code` = '$regions'"; }
                if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                if($supervisors != "all"){ $sup_fltr = " AND `supervisor_code` = '$supervisors'"; }
                if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }
                if($batch_type != "all"){ $gc_fltr = " AND `gc_flag` = '$batch_type'"; }

                $farm_list = ""; $farm_list = implode("','", $farm_code);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr."".$rgn_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                $farm_list = ""; $farm_list = implode("','", $farm_alist);
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')".$gc_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $batch_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    
                    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `batch_code` IN ('$batch_list')".$date_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $batch_alist = $placed_date = $liquid_date = $placed_birds = $sold_birds = $avg_wt = $mean_age = $day_gain = $mort_cnt = $mort_per = $fcr = $cfcr = $eef = $aprod_cost = array();
                    while($row = mysqli_fetch_array($query)){
                        $ibatch = $row['batch_code']; $batch_alist[$ibatch] = $ibatch;
                        $key = $ibatch;
                        
                        $placed_date[$key] = $row['start_date'];
                        $liquid_date[$key] = $row['liquid_date'];
                        $placed_birds[$key] = (float)$row['placed_birds'];
                        $sold_birds[$key] = (float)$row['sold_birds'];
                        $avg_wt[$key] = (float)$row['avg_wt'];
                        $mean_age[$key] = (float)$row['mean_age'];
                        $day_gain[$key] = (float)$row['day_gain'];
                        $mort_cnt[$key] = (float)$row['mortality'];
                        $mort_per[$key] = (float)$row['total_mort'];
                        $fcr[$key] = (float)$row['fcr'];
                        $cfcr[$key] = (float)$row['cfcr'];
                        $eef[$key] = (float)$row['eef'];
                        $aprod_cost[$key] = (float)$row['actual_prod_cost'];
                    }
                    /*Chick-In Suppier Name
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                    $query = mysqli_query($conn,$sql); $chk_sname = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['to_batch'];
                        $chk_sname[$key] = $vendor_name[$row['vcode']];
                    }*/

                    //Fetch Hatchery and Supplier Details-1
                    $batch_list = implode("','",$batch_alist); $chick_coa = $item_cat_coa[$chick_category]; 
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
                    if((int)$bsup_flag == 0 && (int)$csn_flag == 1){
                        $sql = "SELECT *  FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $ven_name[$row['code']] = $row['name']; }
    
                        $chick_coa = $item_cat_coa[$chick_category]; 
                        $sql = "SELECT * FROM `account_summary` WHERE `crdr` = 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$blist') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $chick_supplier_name[$row['batch']] = $ven_name[$row['vendor']]; }
                    }

                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_book = $batch_farm1 = $batch_farm2 = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $batch_code[$row['code']] = $row['code'];
                        $batch_name[$row['code']] = $row['description'];
                        $batch_book[$row['code']] = $row['book_num'];
                        $batch_no[$row['code']] = $row['batch_no'];
                        $batch_farm1[$row['code']] = $row['farm_code'];
                        if(empty($batch_farm2[$row['farm_code']]) || $batch_farm2[$row['farm_code']] == ""){
                            $batch_farm2[$row['farm_code']] = $row['code'];
                        }
                        else{
                            $batch_farm2[$row['farm_code']] .= ",".$row['code'];
                        }
                    }
                    
                    $farm_list = ""; $farm_list = implode("','", $farm_code);
                    $farm_list2 = ""; $farm_list2 = implode("','", $batch_farm1);
                    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') AND `code` IN ('$farm_list2')".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $farm_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                    foreach($farm_alist as $fcode){
                        if(empty($batch_farm2[$fcode]) || $batch_farm2[$fcode] == ""){ }
                        else{
                            $blist = array(); $blist = explode(",",$batch_farm2[$fcode]);
                            foreach($blist as $bcode){
                                $slno++;
                                
                                $pdate = date("d.m.Y",strtotime($placed_date[$bcode]));
                                $ldate = date("d.m.Y",strtotime($liquid_date[$bcode]));
                                $pbirds = $placed_birds[$bcode];
                                $sbirds = $sold_birds[$bcode];
                                $mage = $mean_age[$bcode];
                                $mcnt = $mort_cnt[$bcode];
                                $mper = $mort_per[$bcode];
                                $abwt = $avg_wt[$bcode];
                                $afcr = $fcr[$bcode];
                                $acfcr = $cfcr[$bcode];
                                $dgain = $day_gain[$bcode];
                                $aeef = $eef[$bcode];
                                $apcost = $aprod_cost[$bcode];
                                
                                $html .= '<tr>';
                                $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                $html .= '<td style="text-align:left;">'.$branch_name[$farm_branch[$fcode]].'</td>';
                                $html .= '<td style="text-align:left;">'.$line_name[$farm_line[$fcode]].'</td>';
                                $html .= '<td style="text-align:left;">'.$supervisor_name[$farm_supervisor[$fcode]].'</td>';
                                $html .= '<td style="text-align:left;">'.$farmer_name[$farm_farmer[$fcode]].'</td>';
                                $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                $html .= '<td style="text-align:left;">'.$sector_name[$chkin_hcode[$bcode]].'</td>';
                                $html .= '<td style="text-align:left;">'.$sector_name[$chkin_vcode[$bcode]].'</td>';
                                $html .= '<td style="text-align:right;">'.$batch_no[$bcode].'</td>';
                                $html .= '<td style="text-align:left;" class="dates">'.$pdate.'</td>';
                                $html .= '<td style="text-align:left;" class="dates">'.$ldate.'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($pbirds)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($sbirds)).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($mage,3).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($mcnt)).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($mper,2).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($abwt,2).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($afcr,2).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($acfcr,2).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($dgain,2).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($aeef,2).'</td>';
                                $html .= '<td style="text-align:right;">'.decimal_adjustments($apcost,2).'</td>';
                                $html .= '</tr>';

                                //Total Calculations
                                $tp_birds += (float)$pbirds;
                                $ts_birds += (float)$sbirds;
                                $tm_birds += (float)$mcnt;
                            }
                        }
                    }

                    if((float)$tp_birds != 0){ $tm_per = (((float)$tm_birds / (float)$tp_birds) * 100); }
                    if((float)$tp_birds != 0){ $tm_cper = (((float)$tm_cbirds / (float)$tp_birds) * 100); }
                    $tb_sqty = ((float)$tp_birds - ((float)$ts_birds + (float)$tm_birds));

                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="11">Total</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tp_birds)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ts_birds)).'</th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tm_birds)).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($tm_per).'</th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '<th style="text-align:center;"></th>';
                    $html .= '</tr>';
                    $html .= '</tfoot>';
                    
                    echo $html;
                }
            }
        ?>
        </table><br/><br/><br/>
       
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
                    cdate_format1();
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
                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
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