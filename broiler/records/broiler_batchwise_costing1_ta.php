<?php
//broiler_batchwise_costing1_ta.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_batchwise_costing1_ta.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_batchwise_costing1_ta.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_farm", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_farm LIKE poulso6_admin_broiler_broilermaster.broiler_farm;"; mysqli_query($conn,$sql1); }
// if(in_array("broiler_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_units LIKE poulso6_admin_broiler_broilermaster.broiler_units;"; mysqli_query($conn,$sql1); }
// if(in_array("broiler_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sheds LIKE poulso6_admin_broiler_broilermaster.broiler_sheds;"; mysqli_query($conn,$sql1); }
// if(in_array("broiler_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_shed_allocation LIKE poulso6_admin_broiler_broilermaster.broiler_shed_allocation;"; mysqli_query($conn,$sql1); }

$file_name = "Live Costing Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

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

$regions = $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
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
            $html = $nhtml = $fhtml = ''; $cflag = $i_cnt = 0;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Branch</th>'; $fhtml .= '<th id="order">Branch</th>';
            $nhtml .= '<th>Farm Name</th>'; $fhtml .= '<th id="order_num">Farm Name</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>Housed Chicks</th>'; $fhtml .= '<th id="order_num">Housed Chicks</th>';
            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
            $nhtml .= '<th>Amount</th>'; $fhtml .= '<th id="order_num">Amount</th>';
            $nhtml .= '<th>Feed Issued(bags)</th>'; $fhtml .= '<th id="order_num">Feed Issued(bags)</th>';
            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
            $nhtml .= '<th>Amount</th>'; $fhtml .= '<th id="order_num">Amount</th>';
            $nhtml .= '<th>Medicine Issued</th>'; $fhtml .= '<th id="order_num">Medicine Issued</th>';
            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
            $nhtml .= '<th>Amount</th>'; $fhtml .= '<th id="order_num">Amount</th>';
            // $nhtml .= '<th>Admin Cost</th>'; $fhtml .= '<th id="order_num">Admin Cost</th>';
            $nhtml .= '<th>Production Cost</th>'; $fhtml .= '<th id="order_num">Production Cost</th>';
            $nhtml .= '<th>Sale (Birds Qty)</th>'; $fhtml .= '<th id="order_num">Sale (Birds Qty)</th>';
            $nhtml .= '<th>Sale (Weight)</th>'; $fhtml .= '<th id="order_num">Sale (Weight)</th>';
            $nhtml .= '<th>Rate</th>'; $fhtml .= '<th id="order_num">Rate</th>';
            $nhtml .= '<th>Amount</th>'; $fhtml .= '<th id="order_num">Amount</th>';
            $nhtml .= '<th>Profit & Loss</th>'; $fhtml .= '<th id="order_num">Profit & Loss</th>';
            
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                
                $rgn_fltr = $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = "";
                if($regions != "all"){ $rgn_fltr = " AND `region_code` = '$regions'"; }
                if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                if($supervisors != "all"){ $sup_fltr = " AND `supervisor_code` = '$supervisors'"; }
                if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }

                $farm_list = ""; $farm_list = implode("','", $farm_code);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$rgn_fltr."".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                $farm_list = ""; $farm_list = implode("','", $farm_alist);
                //Fetch Live Batch and Farm codes
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list') AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; 
                $query = mysqli_query($conn,$sql); $batch_alist = $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; $farm_alist[$row['code']] = $row['farm_code']; $batch_farm[$row['farm_code']] = $row['code']; }

                $batch_list = implode("','",$batch_alist); $farm_list = implode("','",$farm_alist);
                
                $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_region = $farm_branch = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $farm_region[$row['code']] = $row['region_code']; $farm_branch[$row['code']] = $row['branch_code']; }

                //Broiler Chick code
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%' AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $chick_code = "";
                while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
                 
                //Broiler Bird code
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $bird_code = "";
                while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
                 
                //Broiler Feed Details
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_alist[$row['code']] = $row['code']; }
                $feed_list = implode("','", $feed_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_code = $feed_name = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }
  
                //Broiler MedVac Details
                $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $medvac_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_alist[$row['code']] = $row['code']; }
                $medvac_list = implode("','", $medvac_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$medvac_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $medvac_code = $medvac_name = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }
  
                //Calculations
                //Purchase
                $sql = "SELECT * FROM `broiler_purchases` WHERE `warehouse` IN ('$farm_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $chk_mdate = $csin_qty = $csin_amt = $fsin_qty = $fsin_amt = $mvsin_qty = $mvsin_amt = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $row['farm_batch'];
                    if($row['icode'] == $chick_code){
                        //Chicks
                        //check and store minimum chick placed/housed/transfer-In date
                        if(empty($chk_mdate[$key1]) || $chk_mdate[$key1] == "" || strtotime($chk_mdate[$key1]) >= strtotime($row['date'])){ $chk_mdate[$key1] = $row['date']; }

                        //Chick-In Quantity and Amount
                        $csin_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $csin_amt[$key1] += (float)$row['item_tamt'];
                    }
                    else if(!empty($feed_code[$row['icode']]) && $feed_code[$row['icode']] == $row['icode']){
                        //Feeds
                        //Feed-In Quantity and Amount
                        $fsin_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $fsin_amt[$key1] += (float)$row['item_tamt'];

                    }
                    else if(!empty($medvac_code[$row['icode']]) && $medvac_code[$row['icode']] == $row['icode']){
                        //Medvacs
                        //Medvac-In Quantity and Amount
                        $mvsin_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $mvsin_amt[$key1] += (float)$row['item_tamt'];
                        
                    }
                    else{ }
                }

                //Stock Transfer-In
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `towarehouse` IN ('$farm_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $row['to_batch'];
                    if($row['code'] == $chick_code){
                        //Chicks
                        //check and store minimum chick placed/housed/transfer-In date
                        if(empty($chk_mdate[$key1]) || $chk_mdate[$key1] == "" || strtotime($chk_mdate[$key1]) >= strtotime($row['date'])){ $chk_mdate[$key1] = $row['date']; }

                        //Chick-In Quantity and Amount
                        $csin_qty[$key1] += (float)$row['quantity'];
                        $csin_amt[$key1] += (float)$row['amount'];
                    }
                    else if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                        //Feeds
                        //Feed-In Quantity and Amount
                        $fsin_qty[$key1] += (float)$row['quantity'];
                        $fsin_amt[$key1] += (float)$row['amount'];

                    }
                    else if(!empty($medvac_code[$row['code']]) && $medvac_code[$row['code']] == $row['code']){
                        //Medvacs
                        //Medvac-In Quantity and Amount
                        $mvsin_qty[$key1] += (float)$row['quantity'];
                        $mvsin_amt[$key1] += (float)$row['amount'];
                        
                    }
                    else{ }
                }

                //Stock Transfer-Out
                $sql = "SELECT * FROM `item_stocktransfers` WHERE `fromwarehouse` IN ('$farm_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql);$fsout_qty = $fsout_amt = $mvsout_qty = $mvsout_amt = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $row['from_batch'];
                    if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                        //Feeds
                        //Feed-In Quantity and Amount
                        $fsout_qty[$key1] += (float)$row['quantity'];
                        $fsout_amt[$key1] += (float)$row['amount'];

                    }
                    else if(!empty($medvac_code[$row['code']]) && $medvac_code[$row['code']] == $row['code']){
                        //Medvacs
                        //Medvac-In Quantity and Amount
                        $mvsout_qty[$key1] += (float)$row['quantity'];
                        $mvsout_amt[$key1] += (float)$row['amount'];
                        
                    }
                    else{ }
                }

                //Fetch Admin Cost Based on Branch and Chick-in Minimum date
                $sql = "SELECT * FROM `broiler_gc_standard` WHERE `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn, $sql); $admin_sprc = array();
                while ($row = mysqli_fetch_assoc($query)) {
                    foreach($farm_code as $fcode){
                        $chk_mdate;
                        $bcode = $batch_farm[$fcode];
                        if(empty($chk_mdate[$bcode]) || $chk_mdate[$bcode] == ""){ }
                        else{
                            $brcode = $farm_branch[$fcode]; $rcode = $farm_region[$fcode];
                            if($brcode == $row['branch_code'] && $rcode == $row['region_code'] && strtotime($row['from_date']) <= strtotime($chk_mdate[$bcode]) && strtotime($row['to_date']) >= strtotime($chk_mdate[$bcode])){
                                $admin_sprc[$bcode] = $row['admin_cost'];
                            }
                        }
                    }
                }

                //Fetch Farm Batch Max Age
                $sql = "SELECT MAX(brood_age) as brood_age,batch_code FROM `broiler_daily_record` WHERE `farm_code` IN ('$farm_list') AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                $query = mysqli_query($conn,$sql); $brood_mage = array();
                while($row = mysqli_fetch_assoc($query)){ $key1 = $row['batch_code']; $brood_mage[$key1] = $row['brood_age']; }

                //Chick/Bird Sale
                $sql = "SELECT * FROM `broiler_sales` WHERE `warehouse` IN ('$farm_list') AND `farm_batch` IN ('$batch_list') AND `icode` IN ('$chick_code','$bird_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $csale_nos = $csale_qty = $csale_amt = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $row['farm_batch'];
                    //Chick-In Quantity and Amount
                    $csale_nos[$key1] = $row['birds'];
                    $csale_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                    $csale_amt[$key1] += (float)$row['item_tamt'];
                }
                //Branch Names
                $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; 
                $query = mysqli_query($conn,$sql); $branch_name = array();
                while($row = mysqli_fetch_assoc($query)){ $branch_name[$row['code']] = $row['description']; }
                
                foreach($farm_code as $fcode){
                    $brname = $branch_name[$farm_branch[$fcode]];
                    $fname = $farm_name[$fcode];
                    $bcode = $batch_farm[$fcode];
                    $max_age = $brood_mage[$bcode];
                    
                    // Chick Calculations
                    $ch_qty = 0; if(!empty($csin_qty[$bcode]) && (float)$csin_qty[$bcode] != 0){ $ch_qty = $csin_qty[$bcode]; }
                    $ch_amt = 0; if(!empty($csin_amt[$bcode]) && (float)$csin_amt[$bcode] != 0){ $ch_amt = $csin_amt[$bcode]; }
                    $ch_prc = 0; if((float)$ch_qty != 0){ $ch_prc = (float)$ch_amt / (float)$ch_qty; }

                    // Feed Calculations 
                    $fd_siqty = 0; if(!empty($fsin_qty[$bcode]) && (float)$fsin_qty[$bcode] != 0){ $fd_siqty = $fsin_qty[$bcode]; }
                    $fd_soqty = 0; if(!empty($fsout_qty[$bcode]) && (float)$fsout_qty[$bcode] != 0){ $fd_soqty = $fsout_qty[$bcode]; }
                    $fd_qty = (float)$fd_siqty - (float)$fd_soqty;
                    
                    $fd_siamt = 0; if(!empty($fsin_amt[$bcode]) && (float)$fsin_amt[$bcode] != 0){ $fd_siamt = $fsin_amt[$bcode]; }
                    $fd_soamt = 0; if(!empty($fsout_amt[$bcode]) && (float)$fsout_amt[$bcode] != 0){ $fd_soamt = $fsout_amt[$bcode]; }
                    $fd_amt = (float)$fd_siamt - (float)$fd_soamt;
                    $fd_prc = 0; if((float)($fd_qty) != 0){ $fd_prc = (float)$fd_amt / (float)$fd_qty; }

                    // Med/Vac Calculations
                    $mv_siqty = 0; if(!empty($mvsin_qty[$bcode]) && (float)$mvsin_qty[$bcode] != 0 ) { $mv_siqty = $mvsin_qty[$bcode]; }
                    $mv_soqty = 0; if(!empty($mvsout_qty[$bcode]) && (float)$mvsout_qty[$bcode] != 0 ) { $mv_soqty = $mvsout_qty[$bcode]; }
                    $mv_qty = (float)$mv_siqty - (float)$mv_soqty;
                    
                    $mv_siamt = 0; if(!empty($mvsin_amt[$bcode]) && (float)$mvsin_amt[$bcode] != 0 ) { $mv_siamt = $mvsin_amt[$bcode]; }
                    $mv_soamt = 0; if(!empty($mvsout_amt[$bcode]) && (float)$mvsout_amt[$bcode] != 0 ) { $mv_soamt = $mvsout_amt[$bcode]; }
                    $mv_amt = (float)$mv_siamt - (float)$mv_soamt;
                    $mv_prc = 0; if((float)($mv_qty) != 0){ $mv_prc = (float)$mv_amt / (float)$mv_qty; }

                    // Admin Price
                    $ad_prc = 0; if(!empty($admin_sprc[$bcode]) && (float)$admin_sprc[$bcode] != 0){ $ad_prc = $admin_sprc[$bcode];}
                    $ad_sprc = (float)$ad_prc * (float)$ch_qty;

                    // Sale 
                    $csale_no = $csale_nos[$bcode];
                    $sale_qty = 0; if(!empty($csale_qty[$bcode]) && (float)$csale_qty[$bcode] != 0){ $sale_qty = $csale_qty[$bcode];}
                    $sale_amt = 0; if(!empty($csale_amt[$bcode]) && (float)$csale_amt[$bcode] != 0){ $sale_amt = $csale_amt[$bcode];}
                    $sale_prc = 0; if((float)($sale_qty) != 0){ $sale_prc = (float)$sale_amt / (float)$sale_qty; }

                    $prd_cost = (float)$ch_amt + (float)$fd_amt + (float)$mv_amt;

                    $profit_loss = (float)$sale_amt - (float)$prd_cost;

                    $html .= '<tr>';
                    $html .= '<td>'.$brname.'</td>';
                    $html .= '<td>'.$fname.'</td>';
                    $html .= '<td style="text-align:center;">'.str_replace(".00","",number_format_ind(round($max_age,2))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ch_qty,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.rtrim(rtrim(number_format_ind(round($ch_prc, 5)), '0'), '.').'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ch_amt,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fd_qty,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fd_prc,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fd_amt,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mv_qty,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mv_prc,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mv_amt,5))).'</td>';
                    // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ad_sprc,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($prd_cost,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($csale_no,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($sale_qty,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($sale_prc,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($sale_amt,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($profit_loss,5))).'</td>';

                    $html .= '</tr>';

                    $tch_qty += (float)$ch_qty; 
                    $tch_prc += (float)$ch_prc;
                    $tch_amt += (float)$ch_amt;
                    $tfd_qty += (float)$fd_qty;
                    $tfd_prc += (float)$fd_prc;
                    $tfd_amt += (float)$fd_amt;
                    $tmv_qty += (float)$mv_qty;
                    $tmv_prc += (float)$mv_prc;
                    $tmv_amt += (float)$mv_amt;
                    // $tad_sprc += (float)$ad_sprc;
                    $tprd_cost += (float)$prd_cost;
                    $tsale_qty += (float)$sale_qty;
                    $tcsale_no += (float)$csale_no;
                    $tsale_prc += (float)$sale_prc;
                    $tsale_amt += (float)$sale_amt;
                    $tprofit_loss += (float)$profit_loss;

                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="3">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tch_qty,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tch_prc,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tch_amt,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfd_qty,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfd_prc,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfd_amt,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmv_qty,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmv_prc,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmv_amt,5))).'</th>';
                // $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tad_sprc,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tprd_cost,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tsale_qty,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tcsale_no,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tsale_prc,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tsale_amt,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tprofit_loss,5))).'</th>';
                $html .= '</tr>';
                $html .= '</tfoot>';
                
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function table_sort() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {

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
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>