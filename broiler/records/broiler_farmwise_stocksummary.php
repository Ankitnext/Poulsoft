<?php
//broiler_farmwise_stocksummary.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    global $page_title; $page_title = "Farm Wise Stock Summary";
    include "header_head.php";
    $form_path = "broiler_farmwise_stocksummary.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    global $page_title; $page_title = "Farm Wise Stock Summary";
    include "header_head.php";
    $form_path = "broiler_farmwise_stocksummary.php?db=$db&userid=".$user_code;
}

$file_name = "Farm Wise Stock Summary";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

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

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $batch_type = "Live"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
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
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
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
                                <div class="m-2 form-group">
                                    <label>Batch Type</label>
                                    <select name="batch_type" id="batch_type" class="form-control select2">
                                        <option value="Live" <?php if($batch_type == "Live"){ echo "selected"; } ?>>-Live-</option>
                                        <option value="Closed" <?php if($batch_type == "Closed"){ echo "selected"; } ?>>-Closed-</option>
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
            if(isset($_POST['submit_report']) == true){
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $chick_code = $chick_name = "";
                while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_name = $row['description']; }

                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $bird_code = $bird_name = "";
                while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }
                                
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'";
                $query = mysqli_query($conn,$sql); $feed_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_alist[$row['code']] = $row['code']; }

                $feed_list = ""; $feed_list = implode("','", $feed_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
                $query = mysqli_query($conn,$sql); $feed_code = $feed_name = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }
                $feed_csize = sizeof($feed_code) + 1;

                $farm_list = ""; $farm_list = implode("','", $farm_code);  $bch_fltr = "";
                if($batch_type == "Live"){ $bch_fltr = " AND `gc_flag` = '0'"; } else if($batch_type == "Closed"){ $bch_fltr = " AND `start_date` <= '$tdate' AND `end_date` >= '$fdate' AND `gc_flag` = '1'"; } else{ }
                $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` IN ('$farm_list')".$bch_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $batch_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code']; }
                
                $batch_size = sizeof($batch_alist);
                if($batch_size > 0){
                    //Fetch Item Details
                    $item_alist = array();
                    $item_alist[$chick_code] = $chick_code; $item_alist[$bird_code] = $bird_code;
                    foreach($feed_code as $icode){ $item_alist[$icode] = $icode; }
                    $item_list = ""; $item_list = implode("','", $item_alist);
                    $batch_list = ""; $batch_list = implode("','", $batch_alist);
                    
                    //Purchase
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $purin_cqty = $placed_cdate = $purin_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['farm_batch']; $items = $row['icode']; $key2 = $key."@".$items;
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){
                            if($items == $chick_code || $items == $bird_code){
                                $purin_cqty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $placed_cdate[$key] = $row['date'];
                            }
                        }
                        else{ $purin_fqty[$key2] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']); }
                    }
                    //Stock-In
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                    $query = mysqli_query($conn,$sql); $stkin_cqty = $stkin_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['to_batch']; $items = $row['code']; $key2 = $key."@".$items;
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){
                            if($items == $chick_code || $items == $bird_code){
                                $stkin_cqty[$key] += (float)$row['quantity'];
                                $placed_cdate[$key] = $row['date'];
                            }
                        }
                        else{ $stkin_fqty[$key2] += (float)$row['quantity']; }
                    }
                    //Sale
                    $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_sale_cqty = $btw_sale_cqty = $sale_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['farm_batch']; $items = $row['icode']; $key2 = $key."@".$items;
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){
                            if($items == $chick_code || $items == $bird_code){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $opn_sale_cqty[$key] += ((float)$row['birds']);
                                }
                                else{
                                    $btw_sale_cqty[$key] += ((float)$row['birds']);
                                }
                            }
                        }
                        else{ $sale_fqty[$key2] += ((float)$row['birds']); }
                    }
                    //Stock-In
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_stkout_cqty = $btw_stkout_cqty = $stkout_fqty = array();
                    while($row = mysqli_fetch_array($query)){
                        $key = $row['from_batch']; $items = $row['code']; $key2 = $key."@".$items;
                        if(empty($feed_code[$items]) || $feed_code[$items] == ""){
                            if($items == $chick_code || $items == $bird_code){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $opn_stkout_cqty[$key] += (float)$row['quantity'];
                                }
                                else{
                                    $btw_stkout_cqty[$key] += (float)$row['quantity'];
                                }
                            }
                        }
                        else{ $stkout_fqty[$key2] += (float)$row['quantity']; }
                    }
                    //In-House: Transfer-Out
                    $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `date` <= '$tdate' AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $opn_prsout_cqty = $btw_prsout_cqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['from_batch']; $items = $row['item_code'];
                        if($items == $chick_code || $items == $bird_code){
                            if(strtotime($row['date']) < strtotime($fdate)){
                                $opn_prsout_cqty[$key] += (float)$row['birds'];
                            }
                            else{
                                $btw_prsout_cqty[$key] += (float)$row['birds'];
                            }
                        }
                    }
                    //Day Record
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` <= '$tdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                    $query = mysqli_query($conn,$sql); $batch_age = $opn_mort_qty = $opn_cull_qty = $opn_dcon_fqty = $btw_mort_qty = $btw_cull_qty = $btw_dcon_fqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key = $row['batch_code']; $batch_age[$key] = $row['brood_age'];
                        if(strtotime($row['date']) < strtotime($fdate)){
                            $opn_mort_qty[$key] += (float)$row['mortality'];
                            $opn_cull_qty[$key] += (float)$row['culls'];

                            $items = $row['item_code1'];  $key2 = $key."@".$items; $opn_dcon_fqty[$key2] += (float)$row['kgs1'];
                            $items = $row['item_code2'];  $key2 = $key."@".$items; $opn_dcon_fqty[$key2] += (float)$row['kgs2'];
                        }
                        else{
                            $btw_mort_qty[$key] += (float)$row['mortality'];
                            $btw_cull_qty[$key] += (float)$row['culls'];

                            $items = $row['item_code1'];  $key2 = $key."@".$items; $btw_dcon_fqty[$key2] += (float)$row['kgs1'];
                            $items = $row['item_code2'];  $key2 = $key."@".$items; $btw_dcon_fqty[$key2] += (float)$row['kgs2'];
                        }
                    }
                    $brh_fltr = $lne_fltr = $sup_fltr = $frm_fltr = "";
                    if($branches != "all"){ $brh_fltr = " AND `branch_code` = '$branches'"; }
                    if($lines != "all"){ $lne_fltr = " AND `line_code` = '$lines'"; }
                    if($supervisors != "all"){ $sup_fltr = " AND `supervisor_code` = '$supervisors'"; }
                    if($farms != "all"){ $frm_fltr = " AND `code` = '$farms'"; }

                    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$brh_fltr."".$lne_fltr."".$sup_fltr."".$frm_fltr." AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $farm_alist = array();
                    while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code']; }

                    $farm_list = ""; $farm_list = implode("','", $farm_alist);
                    $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `farm_code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_book = $batch_farm = array();
                    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_farm[$row['farm_code']] .= $row['code'].","; }
                    
                    $html = '';
                    $html .= '<thead class="thead3">';
                    $html .= '<tr style="text-align:center;" align="center">';
                    $html .= '<th colspan="5">Farm Details</th>';
                    $html .= '<th colspan="13">Chick Details</th>';
                    $html .= '<th colspan="7">Feed Details</th>';
                    $html .= '<th colspan="'.$feed_csize.'">Closing Feed Stock</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<thead class="thead3">';
                    $html .= '<tr style="text-align:center;" align="center">';
                    $html .= '<th style="text-align:center;" id="order_num">Sl.No.</th>';
                    $html .= '<th style="text-align:center;" id="order">Farm Code</th>';
                    $html .= '<th style="text-align:center;" id="order">Farm Name</th>';
                    $html .= '<th style="text-align:center;" id="order">Batch Name</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Age</th>';
                    $html .= '<th style="text-align:center;" id="order_date">Placement Date</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Chick Placed</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Opening Mort.</th>';
                    $html .= '<th style="text-align:center;" id="order_num">B/W Days Mort.</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Cum. Mort.</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Cum. Mort. %</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Opening Outside sales</th>';
                    $html .= '<th style="text-align:center;" id="order_num">B/W Days Outside sales</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Cum. Outside sales</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Opening In-House Transfer</th>';
                    $html .= '<th style="text-align:center;" id="order_num">B/W Days In-House Transfer</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Cum. In-House Transfer</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Closing Birds</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Feed Purchased</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Feed Transferred-In</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Feed Transferred-Out</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Total Available Feed</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Opening Feed Consumed</th>';
                    $html .= '<th style="text-align:center;" id="order_num">B/W Days Feed Consumed</th>';
                    $html .= '<th style="text-align:center;" id="order_num">Total Feed Consumed</th>';
                    foreach($feed_code as $icode){ $html .= '<th style="text-align:center;" id="order_num">'.$feed_name[$icode].'</th>'; }
                    $html .= '<th style="text-align:center;" id="order_num">Total Available Feed</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody class="tbody1" id="tbody1">';

                    $slno = $fp_cqty = $fom_cqty = $fbm_cqty = $fcm_cqty = $fos_bqty = $fbs_bqty = $fcs_bqty = $fop_bqty = $fbp_bqty = $fcp_bqty = $fcc_bqty = 0;
                    $ftf_pin_qty = $ftf_tin_qty = $ftf_tout_qty = $ftf_avl_qty = $ftf_opn_fcqty = $ftf_btw_fcqty = $ftf_tot_fcqty = $ftf_tavl_fqty = 0; $fcls_iqty = array();
                    foreach($farm_alist as $fcode){
                        $blist = array(); $blist = explode(",",$batch_farm[$fcode]);
                        foreach($blist as $bcode){
                            if($bcode == ""){ }
                            else{
                                $key = $bcode;
                                //Chick Calculations
                                if(empty($purin_cqty[$key]) || $purin_cqty[$key] == ""){ $purin_cqty[$key] = 0; }
                                if(empty($stkin_cqty[$key]) || $stkin_cqty[$key] == ""){ $stkin_cqty[$key] = 0; }
                                if(empty($opn_mort_qty[$key]) || $opn_mort_qty[$key] == ""){ $opn_mort_qty[$key] = 0; }
                                if(empty($opn_cull_qty[$key]) || $opn_cull_qty[$key] == ""){ $opn_cull_qty[$key] = 0; }
                                if(empty($btw_mort_qty[$key]) || $btw_mort_qty[$key] == ""){ $btw_mort_qty[$key] = 0; }
                                if(empty($btw_cull_qty[$key]) || $btw_cull_qty[$key] == ""){ $btw_cull_qty[$key] = 0; }
                                if(empty($opn_sale_cqty[$key]) || $opn_sale_cqty[$key] == ""){ $opn_sale_cqty[$key] = 0; }
                                if(empty($btw_sale_cqty[$key]) || $btw_sale_cqty[$key] == ""){ $btw_sale_cqty[$key] = 0; }
                                if(empty($opn_prsout_cqty[$key]) || $opn_prsout_cqty[$key] == ""){ $opn_prsout_cqty[$key] = 0; }
                                if(empty($btw_prsout_cqty[$key]) || $btw_prsout_cqty[$key] == ""){ $btw_prsout_cqty[$key] = 0; }
    
                                $placed_chicks = $opn_mqty = $btw_mqty = $tot_mqty = $cum_mper = $tot_sqty = $tot_ppqty = $cls_birds = 0;
                                $placed_chicks = (float)$purin_cqty[$key] + (float)$stkin_cqty[$key];
                                if(date("d.m.Y",strtotime($placed_cdate[$bcode])) == "01.01.1970"){ $placed_date = ""; }
                                else{ $placed_date = date("d.m.Y",strtotime($placed_cdate[$bcode])); }
                                
                                $opn_mqty = (float)$opn_mort_qty[$key] + (float)$opn_cull_qty[$key];
                                $btw_mqty = (float)$btw_mort_qty[$key] + (float)$btw_cull_qty[$key];
                                $tot_mqty = (float)$opn_mqty + (float)$btw_mqty;
                                if((float)$placed_chicks > 0){ $cum_mper = round((((float)$tot_mqty / (float)$placed_chicks) * 100),2); }
                                $tot_sqty = (float)$opn_sale_cqty[$key] + (float)$btw_sale_cqty[$key];
                                $tot_ppqty = (float)$opn_prsout_cqty[$key] + (float)$btw_prsout_cqty[$key];
                                $cls_birds = (float)$placed_chicks - (float)$tot_mqty - (float)$tot_sqty - (float)$tot_ppqty;
    
                                $fp_cqty += (float)$placed_chicks;
                                $fom_cqty += (float)$opn_mqty;
                                $fbm_cqty += (float)$btw_mqty;
                                $fcm_cqty += (float)$tot_mqty;
                                $fos_bqty += (float)$opn_sale_cqty[$key];
                                $fbs_bqty += (float)$btw_sale_cqty[$key];
                                $fcs_bqty += (float)$tot_sqty;
                                $fop_bqty += (float)$opn_prsout_cqty[$key];
                                $fbp_bqty += (float)$btw_prsout_cqty[$key];
                                $fcp_bqty += (float)$tot_ppqty;
                                $fcc_bqty += (float)$cls_birds;

                                //Feed Calculations
                                $pin_qty = $tin_qty = $tout_qty = $avl_qty = $opn_fcon = $btw_fcon = $tot_fcon = $tavl_fqty = $opn_fcqty = $btw_fcqty = $tot_fcqty = $tavl_fqty = 0; $cls_isqty = array();
                                foreach($feed_code as $icode){
                                    $key2 = $key."@".$icode;
                                    if(empty($purin_fqty[$key2]) || $purin_fqty[$key2] == ""){ $purin_fqty[$key2] = 0; }
                                    if(empty($stkin_fqty[$key2]) || $stkin_fqty[$key2] == ""){ $stkin_fqty[$key2] = 0; }
                                    if(empty($sale_fqty[$key2]) || $sale_fqty[$key2] == ""){ $sale_fqty[$key2] = 0; }
                                    if(empty($stkout_fqty[$key2]) || $stkout_fqty[$key2] == ""){ $stkout_fqty[$key2] = 0; }
                                    if(empty($opn_dcon_fqty[$key2]) || $opn_dcon_fqty[$key2] == ""){ $opn_dcon_fqty[$key2] = 0; }
                                    if(empty($btw_dcon_fqty[$key2]) || $btw_dcon_fqty[$key2] == ""){ $btw_dcon_fqty[$key2] = 0; }

                                    $pin_qty += (float)$purin_fqty[$key2];
                                    $tin_qty += (float)$stkin_fqty[$key2];
                                    $tout_qty += ((float)$sale_fqty[$key2] + (float)$stkout_fqty[$key2]);
    
                                    $opn_fcqty += (float)$opn_dcon_fqty[$key2];
                                    $btw_fcqty += (float)$btw_dcon_fqty[$key2];

                                    $cls_isqty[$key2] = (((float)$purin_fqty[$key2] + (float)$stkin_fqty[$key2]) - ((float)$sale_fqty[$key2] + (float)$stkout_fqty[$key2] + (float)$opn_dcon_fqty[$key2] + (float)$btw_dcon_fqty[$key2]));
                                    $fcls_iqty[$icode] += (((float)$purin_fqty[$key2] + (float)$stkin_fqty[$key2]) - ((float)$sale_fqty[$key2] + (float)$stkout_fqty[$key2] + (float)$opn_dcon_fqty[$key2] + (float)$btw_dcon_fqty[$key2]));
                                }
                                $avl_qty = (((float)$pin_qty + (float)$tin_qty) - (float)$tout_qty);
                                $tot_fcqty = ((float)$opn_fcqty + (float)$btw_fcqty);
                                $tavl_fqty = ((float)$avl_qty - (float)$tot_fcqty);

                                $ftf_pin_qty += (float)$pin_qty;
                                $ftf_tin_qty += (float)$tin_qty;
                                $ftf_tout_qty += (float)$tout_qty;
                                $ftf_avl_qty += (float)$avl_qty;
                                $ftf_opn_fcqty += (float)$opn_fcqty;
                                $ftf_btw_fcqty += (float)$btw_fcqty;
                                $ftf_tot_fcqty += (float)$tot_fcqty;
                                $ftf_tavl_fqty += (float)$tavl_fqty;

                                $slno++;
                                $html .= '<tr>';
                                $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                $html .= '<td style="text-align:left;">'.$farm_ccode[$fcode].'</td>';
                                $html .= '<td style="text-align:left;">'.$farm_name[$fcode].'</td>';
                                $html .= '<td style="text-align:left;">'.$batch_name[$bcode].'</td>';
                                $html .= '<td style="text-align:center;">'.$batch_age[$bcode].'</td>';
                                $html .= '<td style="text-align:left;">'.$placed_date.'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($placed_chicks)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_mqty)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_mqty)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_mqty)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($cum_mper).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_sale_cqty[$key])).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_sale_cqty[$key])).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_sqty)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($opn_prsout_cqty[$key])).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($btw_prsout_cqty[$key])).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_ppqty)).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cls_birds)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($pin_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($tin_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($tout_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($avl_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_fcqty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($btw_fcqty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($tot_fcqty).'</td>';
                                
                                foreach($feed_code as $icode){
                                    $key2 = $key."@".$icode;
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_isqty[$key2]).'</td>';
                                }
                                $html .= '<td style="text-align:right;">'.number_format_ind($tavl_fqty).'</td>';
                                $html .= '</tr>';
                            }
                        }
                    }

                    if((float)$fp_cqty > 0){ $cum_mper = round((((float)$fcm_cqty / (float)$fp_cqty) * 100),2); }
                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="6">Total</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fp_cqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fom_cqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fbm_cqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fcm_cqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($cum_mper).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fos_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fbs_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fcs_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fop_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fbp_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fcp_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($fcc_bqty)).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_pin_qty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_tin_qty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_tout_qty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_avl_qty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_opn_fcqty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_btw_fcqty).'</th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_tot_fcqty).'</th>';
                    foreach($feed_code as $icode){ $html .= '<th style="text-align:right;">'.number_format_ind($fcls_iqty[$icode]).'</th>'; }
                    $html .= '<th style="text-align:right;">'.number_format_ind($ftf_tavl_fqty).'</th>';
                    $html .= '</tr>';
                    $html .= '</tfoot>';

                    echo $html;
                }
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_line[$fcode])){ $l_code = $farm_line[$fcode]; } else{ $l_code = ""; }
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
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
                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                }
                else{ }
            }
        </script>
        <script>
            function fetch_row_height(){
                var table_elements = document.querySelector("table>tbody");
                var i; var max_height = 0;
                for(i = 1; i <= table_elements.rows.length; i++){
                    var row_selector = "table>tbody>tr:nth-child(" + [i] + ")";
                    var table_row = document.querySelector(row_selector);
                    var vertical_spacing = window.getComputedStyle(table_row).getPropertyValue("-webkit-border-vertical-spacing");
                    var margin_top = window.getComputedStyle(table_row).getPropertyValue("margin-top");
                    var margin_bottom = window.getComputedStyle(table_row).getPropertyValue("margin-bottom");
                    var row_height= parseInt(vertical_spacing, 10)+parseInt(margin_top, 10)+parseInt(margin_bottom, 10)+table_row.offsetHeight;
                    if(max_height <= row_height){
                        max_height = row_height;
                    }
                }
                //alert("The height is: "+max_height+"px");
                document.getElementById("thead2_empty_row").style.height = max_height+"px";
            }
            fetch_row_height();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>