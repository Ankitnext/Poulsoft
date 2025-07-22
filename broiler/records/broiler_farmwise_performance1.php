<?php
//broiler_farmwise_performance1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_farmwise_performance1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_farmwise_performance1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Farmer Performance Summary Report";
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
if(in_array("broiler_rearingcharge", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_rearingcharge LIKE poulso6_admin_broiler_broilermaster.broiler_rearingcharge;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_hatchentry", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_hatchentry LIKE poulso6_admin_broiler_broilermaster.broiler_hatchentry;"; mysqli_query($conn,$sql1); }

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
$query = mysqli_query($conn,$sql); $supr_code = $supr_name = array();
while($row = mysqli_fetch_assoc($query)){ $supr_code[$row['code']] = $row['code']; $supr_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $farmer_name = $farmer_mobile1 = $farmer_mobile2 = array();
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

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
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $chick_code = $chick_category = "";
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_category = $row['category']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $bird_code = "";
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'poulso6_broiler_wb_motipoultry.php' AND `field_function` = 'Mortality' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $mort_flag = mysqli_num_rows($query);

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $supervisors = $farms = "all"; $batch_type = "1"; $excel_type = "display"; $lot_nos = "";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    //$batch_type = $_POST['batch_type'];
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
                                        <?php foreach($supr_code as $scode){ if($supr_name[$scode] != "" && !empty($farm_svr[$scode])){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supr_name[$scode]; ?></option>
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
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="lot_nos">Lot No</label>
                                    <input type="text" name="lot_nos" id="lot_nos" class="form-control" value="<?php echo $lot_nos; ?>" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>')">
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

            $nhtml .= '<th>Code</th>'; $fhtml .= '<th id="order">Code</th>';
            $nhtml .= '<th>Status</th>'; $fhtml .= '<th id="order">Status</th>';
            $nhtml .= '<th>Line Mgr</th>'; $fhtml .= '<th id="order">Line Mgr</th>';
            $nhtml .= '<th>Name</th>'; $fhtml .= '<th id="order">Name</th>';
            $nhtml .= '<th>Lot No.</th>'; $fhtml .= '<th id="order">Lot No.</th>';
            $nhtml .= '<th>FCR</th>'; $fhtml .= '<th id="order_num">FCR</th>';
            $nhtml .= '<th>Chick Brand</th>'; $fhtml .= '<th id="order">Chick Brand</th>';
            $nhtml .= '<th>Lame birds %</th>'; $fhtml .= '<th id="order_num">Lame birds %</th>';
            $nhtml .= '<th>Lame birds Psc.</th>'; $fhtml .= '<th id="order_num">Lame birds Psc.</th>';
            $nhtml .= '<th>Birds In %</th>'; $fhtml .= '<th id="order_num">Birds In %</th>';
            $nhtml .= '<th>Total Pcs OF Small birds</th>'; $fhtml .= '<th id="order_num">Total Pcs OF Small birds</th>';
            $nhtml .= '<th>Mortality %</th>'; $fhtml .= '<th id="order_num">Mortality %</th>';
            $nhtml .= '<th>Total Pcs OF Mortality</th>'; $fhtml .= '<th id="order_num">Total Pcs OF Mortality</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
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
                $query = mysqli_query($conn,$sql); $batch_alist = $batch_lotno = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; $batch_lotno[$row['code']] = $row['clot_no']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    //Fetch Hatchery and Supplier Details-1
                    $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_cat_coa = array();
                    while($row = mysqli_fetch_assoc($query)){ $item_cat_coa[$row['code']] = $row['iac']; }

                    $chick_coa = $item_cat_coa[$chick_category]; 
                    $sql = "SELECT MIN(`date`) as `sdate`,MAX(`date`) as `edate` FROM `account_summary` WHERE `crdr` LIKE 'DR' AND `coa_code` = '$chick_coa' AND `item_code` = '$chick_code' AND `batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql); $hsdate = $hedate = "";
                    while($row = mysqli_fetch_assoc($query)){ $hsdate = $row['sdate']; $hedate = $row['edate']; }
    
                    $hatch_count = $pur_count = 0; $chkin_vcode = $chkin_hcode = $pur_vcode = $pur_keyset = $hatch_vcode = $hatch_keyset = array();
                    if($hsdate == "" && $hedate == ""){ }
                    else{
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
                        if($row['vcode'] != "" && (empty($chkin_vcode[$row['to_batch']]) || $chkin_vcode[$row['to_batch']] == "")){
                            $chkin_vcode[$row['to_batch']] = $row['vcode'];
                        }
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
                    //Fetch Sale
                    $sql = "SELECT * FROM `broiler_sales` WHERE `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `farm_batch`,`date` ASC";
                    $query = mysqli_query($conn,$sql); $lame_sbds = array();
                    while($row = mysqli_fetch_assoc($query)){
                        if((int)$row['lb_flag'] == 1){
                            $lame_sbds[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $lmbird += (float)$row['birds'];
                        }
                    }
                    
                    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `farm_code`,`date` ASC";
                    $query = mysqli_query($conn,$sql); $frm_cnt = array();
                    while($row = mysqli_fetch_assoc($query)){ $frm_cnt[$row['farm_code']] += 1; }

                    $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `farm_code`,`date` ASC";
                    $query = mysqli_query($conn,$sql); $slno = $f_cnt = 0; $old_farm = "";
                    while($row = mysqli_fetch_assoc($query)){
                        $fcode = $row['farm_code'];
                        $bcode = $row['batch_code'];
                        $fcr = $row['fcr'];
                        $vcode = $chkin_vcode[$bcode];
                        $l_bird = 0; if(!empty($lame_sbds[$row['farm_batch']]) && (float)$lame_sbds[$row['farm_batch']] > 0){ $l_bird = $lame_sbds[$row['farm_batch']]; }
                        $p_bird = $row['placed_birds'];
                        $l_bdsp = 0; if((float)$l_bird > 0){ $l_bdsp = (((float)$p_bird / (float)$l_bird) * 100); }
                        $mort_qty = $row['mortality'];
                        $mort_per = $row['total_mort'];
                        $avg_wt = $row['avg_wt'];
                        $remarks = $row['remarks'];

                        $lm_per = ($lmbird / ($lmbird * $avg_wt)) * 100; 

                        $rl_bird += (float)$l_bird;
                        $rp_bird += (float)$p_bird;
                        $r_fcr += (float)$fcr;
                        $rmort_qty += (float)$mort_qty;

                        $tl_bird += (float)$l_bird;
                        $tp_bird += (float)$p_bird;
                        $t_fcr += (float)$fcr;
                        $tmort_qty += (float)$mort_qty;

                        $slno++; $f_cnt++;
                        $html .= '<tr>';
                        $html .= '<td style="text-align:left;">'.$farm_ccode[$fcode].'</td>';
                        $html .= '<td style="text-align:left;"></td>';
                        $html .= '<td style="text-align:left;">'.$supr_name[$farm_supervisor[$fcode]].'</td>';
                        $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                        $html .= '<td style="text-align:left;">'.$batch_lotno[$bcode].'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($fcr,3).'</td>';
                        $html .= '<td style="text-align:left;">'.$sector_name[$vcode].'</td>';
                        if($mort_flag > 0){
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($lm_per,2).'</td>';
                         } else {
                            $html .= '<td style="text-align:right;">'.decimal_adjustments($l_bdsp,2).'</td>';
                        }
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($l_bird)).'</td>';
                        
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</td>';

                        $html .= '<td style="text-align:right;">'.decimal_adjustments($mort_per,2).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($mort_qty)).'</td>';
                        $html .= '<td style="width:250px;text-align:left;white-space: normal;">'.$remarks.'</td>';
                        $html .= '</tr>';
                        
                        if(!empty($frm_cnt[$row['farm_code']]) && (int)$frm_cnt[$row['farm_code']] == (int)$f_cnt){
                            $rl_bdsp = 0; if((float)$rl_bird != 0){ $rl_bdsp = round((((float)$rp_bird / (float)$rl_bird) * 100),2); }
                            $rmort_per = 0; if((float)$rmort_qty != 0){ $rmort_per = round((((float)$rp_bird / (float)$rmort_qty) * 100),2); }
                            $avg_fcr = 0; if((float)$f_cnt != 0){ $avg_fcr = round(((float)$r_fcr / (float)$f_cnt),3); }
                            $html .= '<tr style="background-color:#dcfdff;">';
                            $html .= '<th style="text-align:left;" colspan="5">Total</th>';
                            $html .= '<th style="text-align:right;">'.decimal_adjustments($avg_fcr,3).'</th>';
                            $html .= '<th style="text-align:right;"></th>';
                            if($mort_flag > 0){
                            $html .= '<th style="text-align:right;">'.decimal_adjustments($lm_per,2).'</th>';
                             } else { 
                            $html .= '<th style="text-align:right;">'.decimal_adjustments($rl_bdsp,2).'</th>';
                             }
                            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rl_bird)).'</th>';
                            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</th>';
                            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</th>';
                          if($mort_flag > 0){ 
                              $html .= '<th style="text-align:right;">'.decimal_adjustments($mort_per,2).'</th>';
                          } else {
                              $html .= '<th style="text-align:right;">'.decimal_adjustments($rmort_per,2).'</th>';
                          }
                            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($rmort_qty)).'</th>';
                            $html .= '<th style="text-align:right;"></th>';
                            $html .= '</tr>';
                            $rl_bdsp = $rl_bird = $rp_bird = $rmort_per = $rmort_qty = $r_fcr = $avg_fcr = $f_cnt = 0;
                        }
                    }

                    $html .= '</tbody>';
                    $tl_bird = 0; if((float)$tl_bdsp != 0){ $tl_bird = round((((float)$tp_bird / (float)$tl_bdsp) * 100),2); }
                    $tmort_per = 0; if((float)$tmort_qty != 0){ $tmort_per = round((((float)$tp_bird / (float)$tmort_qty) * 100),2); }
                    $avg_fcr = 0; if((float)$slno != 0){ $avg_fcr = round(((float)$t_fcr / (float)$slno),3); }

                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="5">Total</th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($avg_fcr,3).'</th>';
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tl_bdsp,2).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tl_bird)).'</th>';
                    
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</th>';

                    $html .= '<th style="text-align:right;">'.decimal_adjustments($tmort_per,2).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tmort_qty)).'</th>';
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
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, filename){
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