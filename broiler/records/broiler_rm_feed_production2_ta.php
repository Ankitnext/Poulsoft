<?php
//broiler_rm_feed_production2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_rm_feed_production2_ta.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_rm_feed_production2.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Formula Wise RM Consumption";
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

$feedmill_type_code = "";
$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
}
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') ".$sector_access_filter1." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT DISTINCT(item_code) as item_code FROM `broiler_feed_consumed` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $mi_codes = "";
while($row = mysqli_fetch_assoc($query)){ if($mi_codes == ""){ $mi_codes = $row['item_code']; } else{ $mi_codes = $mi_codes."','".$row['item_code']; } }

$sql = "SELECT * FROM `item_details` WHERE `code` IN ('$mi_codes') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $mi_cat = "";
while($row = mysqli_fetch_assoc($query)){
    $mill_item_code[$row['code']] = $row['code']; $mill_item_name[$row['code']] = $row['description']; $mill_item_category[$row['code']] = $row['category'];
    if($mi_cat == ""){ $mi_cat = $row['category']; } else{ $mi_cat = $mi_cat."','".$row['category']; }
}
$sql = "SELECT * FROM `item_category` WHERE `code` IN ('$mi_cat') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mill_item_cat_code[$row['code']] = $row['code']; $mill_item_cat_name[$row['code']] = $row['description']; }
//------------------------------------------------------------------------------------------------------------------------------------
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

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $vendor_code = $vendor_name = array();
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $sector_name[$row['code']] = $row['name']; }

//Feed Items
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_acat = array();
while($row = mysqli_fetch_assoc($query)){ $feed_acat[$row['code']] = $row['code']; }
$feed_list = implode("','",$feed_acat);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_code = array();
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_code[$row['code']] = $row['code']; }

$item_code = $item_name = array();
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

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

//Chick/Bird Items
$sql = "SELECT DISTINCT feed_code FROM `broiler_feed_consumed` WHERE `active` = '1' AND `dflag` = '0' ORDER BY feed_code";
$query = mysqli_query($conn,$sql); $feeds_code =  array();
while($row = mysqli_fetch_assoc($query)){ $feeds_code[$row['feed_code']] = $row['feed_code']; $feeds_name[$row['feed_code']] = $item_name[$row['feed_code']];  }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $bird_code = "";
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$fdate = $tdate = date("Y-m-d"); $sectors = $item_categories = $items = $feeds = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $sectors = $_POST['sectors'];
    $item_categories = $_POST['item_categories'];
    $items = $_POST['items'];
    $feeds = $_POST['feeds'];
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sector_list = "";
    if($sectors == "all"){
        foreach($sector_code as $prod_code){ if($sector_list == ""){ $sector_list = $prod_code; } else{ $sector_list = $sector_list."','".$prod_code; } }
        $inv_mill_filter = " AND `warehouse` IN ('$sector_list')";
        $str_in_mill_filter = " AND `towarehouse` IN ('$sector_list')";
        $str_out_mill_filter = " AND `fromwarehouse` IN ('$sector_list')";
        $consumed_mill_filter = " AND `feed_mill` IN ('$sector_list')";
    }
    else{
        $inv_mill_filter = " AND `warehouse` IN ('$sectors')";
        $str_in_mill_filter = " AND `towarehouse` IN ('$sectors')";
        $str_out_mill_filter = " AND `fromwarehouse` IN ('$sectors')";
        $consumed_mill_filter = " AND `feed_mill` IN ('$sectors')";
    }

    $item_list = "";
    if($items != "all"){
        $item_list = $items;
    }
    else if($item_categories != "all"){
        foreach($mill_item_code as $micode){
            if($item_categories == $mill_item_category[$micode]){
                if($item_list == ""){ $item_list = $micode; }
                else{$item_list = $item_list."','".$micode; }
            }
        }
    }
    else{
        foreach($mill_item_code as $micode){
            if($item_list == ""){ $item_list = $micode; }
            else{$item_list = $item_list."','".$micode; }
        }
    }
    if($item_list == ""){
        $pur_item_filter = "";
        $str_item_filter = "";
        $consumed_item_filter = "";
    }
    else{
        $pur_item_filter = " AND `icode` IN ('$item_list')";
        $str_item_filter = " AND `code` IN ('$item_list')";
        $consumed_item_filter = " AND `item_code` IN ('$item_list')";
    }
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
                                    <label>Feed Mill</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($sectors == $prod_code){ echo "selected"; } ?>><?php echo $sector_name[$prod_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Item Category</label>
                                    <select name="item_categories" id="item_categories" class="form-control select2" onchange="fetch_item_list()">
                                        <option value="all" <?php if($item_categories == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($mill_item_cat_code as $mic_code){ ?><option value="<?php echo $mic_code; ?>" <?php if($item_categories == $mic_code){ echo "selected"; } ?>><?php echo $mill_item_cat_name[$mic_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                   <label>Description</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($mill_item_code as $mi_codes){ ?><option value="<?php echo $mi_codes; ?>" <?php if($items == $mi_codes){ echo "selected"; } ?>><?php echo $mill_item_name[$mi_codes]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                   <label>Formula</label>
                                    <select name="feeds" id="feeds" class="form-control select2">
                                        <option value="all" <?php if($feeds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($feeds_code as $fd_codes){ ?><option value="<?php echo $fd_codes; ?>" <?php if($feeds == $fd_codes){ echo "selected"; } ?>><?php echo $item_name[$fd_codes]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <!-- <div class="m-2 form-group" style="width: 210px;">
                                    <label for="lot_nos">Lot No</label>
                                    <input type="text" name="lot_nos" id="lot_nos" class="form-control" value="<?php echo $lot_nos; ?>" style="padding:0;padding-left:2px;width:200px;" />
                                </div> -->
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

            $nhtml .= '<th>Sl No.</th>'; $fhtml .= '<th id="order">Sl No.</th>';
            $nhtml .= '<th>From Date</th>'; $fhtml .= '<th id="order_date">From Date</th>';
            $nhtml .= '<th>To Date</th>'; $fhtml .= '<th id="order_date">To Date</th>';
            $nhtml .= '<th>Formula Name</th>'; $fhtml .= '<th id="order">Formula Name</th>';
            $nhtml .= '<th>Item Name</th>'; $fhtml .= '<th id="order">Item Name</th>';
            $nhtml .= '<th>Consumed Quantity</th>'; $fhtml .= '<th id="order_num">Consumed Quantity</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $opn_pur_feed_item_qty = $opn_pur_feed_item_amt = $opn_trin_feed_item_qty = $opn_trin_feed_item_amt = $formula_items = $feed_items = array();
                
                /* *****Between Date Transactions***** */
                
                $pitem_count = sizeof($formula_items) * 2; $sln = 1;
                $fd_fltr = ""; if($feeds == "all"){ $fd_fltr = ""; } else { $fd_fltr = "AND `feed_code` IN ('$feeds')";}

                $btw_consumed_feed_item_qty = $btw_consumed_feed_item_amt = array();
                $sql_record = "SELECT feed_code,item_code,SUM(quantity) as quantity FROM `broiler_feed_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$consumed_item_filter."".$consumed_mill_filter."".$fd_fltr." AND `active` = '1' AND `dflag` = '0' GROUP BY `formula_code`,`item_code` ORDER BY `feed_code` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    // echo $itemm_code;
                    // echo $item_name[$itemm_code];
                    $html .= '<tr>';
                    $html .= '<td>' . $sln . '</td>';
                    $html .= '<td style="text-align:right;" class="dates">' . date('d.m.Y', strtotime($fdate)) . '</td>';
                    $html .= '<td style="text-align:right;" class="dates">' . date('d.m.Y', strtotime($tdate)) . '</td>';

                    // Use feed_name and item_name safely with fallback
                    $html .= '<td style="width:100px;">' . $item_name[$row['feed_code']] . '</td>'; // formula/feed name
                    $html .= '<td style="width:100px;">' . $item_name[$row['item_code']] . '</td>';     // item name

                    $html .= '<td style="text-align:right;">' . number_format_ind(round($row['quantity'], 2)) . '</td>';
                    $html .= '</tr>';

                    $tot_oqty += (float)$row['quantity'];
                    $sln++;
                }
            

                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="5">Total</th>';
                // $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_boxes,5))).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind(round($tot_oqty,5)).'</th>';
                $html .= '</tr>';
                $html .= '</tfoot>';
            }
            echo $html;

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
                
                // var worksheet = XLSX.utils.table_to_sheet(table);
                // XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

                 // ─── only this line changed ───
                var worksheet = XLSX.utils.table_to_sheet(table, {
                raw: true,
                cellDates: true,
                dateNF: 'dd.mm.yyyy'
                });

                XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

                // ─── and only this one ───
                XLSX.writeFile(workbook, filename + ".xlsx", {
                dateNF: 'dd.mm.yyyy'
                });

                // XLSX.writeFile(workbook, filename+".xlsx");
                    
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
                       
        </script>
        <script>
            function fetch_item_list(){
                var item_categories = document.getElementById("item_categories").value;
                var items = document.getElementById("items").value;
                var cat_value = '<?php echo $item_categories; ?>';
                var item_value = '<?php echo $items; ?>';
				removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(item_categories.match("all")){
                    <?php
                    foreach($mill_item_code as $micode){
                        //echo "if(fcode == '$micode'){";
                    ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $mill_item_name[$micode]; ?>"); theOption1.value = "<?php echo $micode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        //echo "}";
                    }
                    ?>
                }
                else{
                    <?php
                    foreach($mill_item_code as $micode){
                        echo "if(item_categories == '$mill_item_category[$micode]'){";
                    ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $mill_item_name[$micode]; ?>"); theOption1.value = "<?php echo $micode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        echo "}";
                    }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>