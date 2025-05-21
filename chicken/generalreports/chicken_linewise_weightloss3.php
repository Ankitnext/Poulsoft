<?php
//chicken_linewise_weightloss3.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();

if(!empty($_GET['db']) && $_GET['db'] != ""){ $db = $_SESSION['db'] = $_GET['db']; }
if($db == ''){
    include "../config.php";
    $database_name = $_SESSION['dbase']; 
    $users_code = $_SESSION['userid'];
    $form_reload_page = "chicken_linewise_weightloss3.php";
}
else{
    include "APIconfig.php";
    $database_name = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_linewise_weightloss3.php?db=".$db;
}
include "number_format_ind.php";

function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; }
    $a = round($a,$b);
    $c = explode(".",$a);
    $ed = "";
    $iv = 0;
    if($c[1] == ""){ $iv = 1; }
    else{ $iv = strlen($c[1]); }
    if($iv == 0){ $iv = 1; }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    return $a."".$ed;
}
$file_name = "Weight Loss Report";

/*Check for Table Availability*/
$table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("item_stock_adjustment", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stock_adjustment LIKE poulso6_admin_chickenmaster.item_stock_adjustment;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Fetch Column Availability*/
$sql='SHOW COLUMNS FROM `main_mortality`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("warehouse", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_mortality` ADD `warehouse` VARCHAR(300) NULL DEFAULT NULL AFTER `amount`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $loc_access = ""; $adm_aflag = 0;
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; if((int)$row['supadmin_access'] == 1 || (int)$row['admin_access'] == 1){ $adm_aflag = 1; } }

//Sector Access Filter
if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
else{
    $loc1 = explode(",",$loc_access); $loc_list = "";
    foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
    $sec_fltr = " AND `code` IN ('$loc_list')";
}
//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $coa_name = array();
while($row = mysqli_fetch_assoc($query)){ $coa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
$query = mysqli_query($conn,$sql); $ifwt = $ifbw = $ifjbw = $ifjbwen = $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = $ifwlmb = $c_cnt = 0;
while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifwlmb = $row['wlmb']; }
if((float)$ifjbwen == 1 || (float)$ifjbw == 1){ $jals_flag = 1; $c_cnt++; }
if((float)$ifjbwen == 1 || (float)$ifjbw == 1 || (float)$ifbw == 1){ $birds_flag = 1; $c_cnt++; }
if((float)$ifjbwen == 1){ $tweight_flag = $eweight_flag = 1; $c_cnt+= 2; }

$icat_fltr = ""; if($ifwlmb == 1){ $icat_fltr = " AND `description` LIKE '%Birds'"; } else{ $icat_fltr = " AND `description` LIKE '%Broiler Birds%'"; }
$sql = "SELECT * FROM `item_category` WHERE `active` = '1'".$icat_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$icat_list = implode("','",$icat_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cat = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cat[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $items = $sectors = "all"; $fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    $fdate = $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $items = $_POST['items'];
    $sectors = $_POST['sectors'];
    $exports = $_POST['exports'];
}
?>
<html>
	<head>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            .main-table { white-space: nowrap; }
            .tbody1{
                color: black;
            }
        </style>
	</head>
	<body>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table <?php if($exports == "print") { echo ' class="main-table"'; } else{ echo ' class="table-sm table-hover main-table2"'; } ?>>
                        <thead class="thead1">
                            <tr>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <!--<div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php //echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>-->
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($items == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Shop/Lines</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                        <div class="form-group" style="width:30px;visibility:hidden;">
                                            <label for="search_table">S</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:30px;" />
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
                    <?php if($exports == "display" || $exports == "exportpdf"){ ?>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
                    <?php } ?>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
							$c_cnt += 6;
                            
                            $html = '';
                            //Sales
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="'.$c_cnt.'" style="text-align:center;">Purchases/Transfer IN</th>';
                            $html .= '<th colspan="'.$c_cnt.'" style="text-align:center;">Sales/Transfer OUT</th>';
                            $html .= '</tr>';
                            $html .= '<tr class="thead2">';

                            $html .= '<th style="text-align:center;">Supplier</th>';
                            $html .= '<th style="text-align:center;">Item</th>';
                            if((int)$jals_flag == 1){ $html .= '<th style="text-align:center;">Jals</th>'; }
                            if((int)$birds_flag == 1){ $html .= '<th style="text-align:center;">Birds</th>'; }
                            if((int)$tweight_flag == 1){ $html .= '<th style="text-align:center;">T.Weight</th>'; }
                            if((int)$eweight_flag == 1){ $html .= '<th style="text-align:center;">E.Weight</th>'; }
                            $html .= '<th style="text-align:center;">N.Weight</th>';
                            $html .= '<th style="text-align:center;">Avg. Wt.</th>';
                            $html .= '<th style="text-align:center;">Price</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';

                            $html .= '<th style="text-align:center;">Customer</th>';
                            $html .= '<th style="text-align:center;">Item</th>';
                            if((int)$jals_flag == 1){ $html .= '<th style="text-align:center;">Jals</th>'; }
                            if((int)$birds_flag == 1){ $html .= '<th style="text-align:center;">Birds</th>'; }
                            if((int)$tweight_flag == 1){ $html .= '<th style="text-align:center;">T.Weight</th>'; }
                            if((int)$eweight_flag == 1){ $html .= '<th style="text-align:center;">E.Weight</th>'; }
                            $html .= '<th style="text-align:center;">N.Weight</th>';
                            $html .= '<th style="text-align:center;">Avg. Wt.</th>';
                            $html .= '<th style="text-align:center;">Price</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';

                            $html .= '</tr>';

                            $pdate = date('Y-m-d', strtotime($fdate. ' - 1 days'));
                            if($items != "all"){ $item_list = $items; } else{ $item_list = implode("','",$item_code); }
                            if($sectors != "all"){ $sec_list = $sectors; } else{ $sec_list = implode("','",$sector_code); }
                            $pur_sec = $sale_sec = $item_alist = array();

                            $sql1 = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwp_obds = $iwp_oqty = $iwp_oprc = $iwp_opprc = $iwp_oamt = $iwp_bqty = $iwp_bamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['itemcode'];
                                    $iwp_obds[$key1] += (float)$row1['birds'];
                                    $iwp_oqty[$key1] += (float)$row1['netweight'];
                                    $iwp_oamt[$key1] += (float)$row1['totalamt'];
                                    $iwp_oprc[$key1] = (float)$row1['itemprice'];
                                }
                                else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
                                if(strtotime($row1['date']) < strtotime($pdate)){
                                    $iwp_opprc[$key1] = (float)$row1['itemprice'];
                                }
                            }
                            $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `towarehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwi_oprc = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['code'];
                                    $iwp_obds[$key1] += (float)$row1['birds'];
                                    $iwp_oqty[$key1] += (float)$row1['quantity'];
                                    $iwp_oamt[$key1] += ((float)$row1['quantity'] * (float)$row1['price']);
                                    $iwi_oprc[$key1] = (float)$row1['price'];
                                }
                                else{ $item_alist[$row1['code']] = $row1['code']; }
                            }
                            $sql1 = "SELECT * FROM `customer_sales` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iws_obds = $iws_oqty = $iws_oamt = $iws_bqty = $iws_bamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['itemcode'];
                                    $iws_obds[$key1] += (float)$row1['birds'];
                                    $iws_oqty[$key1] += (float)$row1['netweight'];
                                    $iws_oamt[$key1] += (float)$row1['totalamt'];
                                }
                                else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
                            }
                            $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `fromwarehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwi_oprc = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['code'];
                                    $iws_obds[$key1] += (float)$row1['birds'];
                                    $iws_oqty[$key1] += (float)$row1['quantity'];
                                    $iws_oamt[$key1] += ((float)$row1['quantity'] * (float)$row1['price']);
                                }
                                else{ $item_alist[$row1['code']] = $row1['code']; }
                            }
                            //Stock Adjustment
                            $sql1 = "SELECT * FROM `item_stock_adjustment` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`trnum` ASC";
                            $query1 = mysqli_query($conn, $sql1); $iwsa_aobds = $iwsa_dobds = $iwsa_aoqty = $iwsa_doqty = $iwsa_aoamt = $iwsa_doamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $key1 = $row1['itemcode'];
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    if($row1['a_type'] == "add"){
                                        $iwsa_aobds[$key1] += (float)$row1['birds'];
                                        $iwsa_aoqty[$key1] += (float)$row1['nweight'];
                                        $iwsa_aoamt[$key1] += (float)$row1['amount'];
                                    }
                                    else if($row1['a_type'] == "deduct"){
                                        $iwsa_dobds[$key1] += (float)$row1['birds'];
                                        $iwsa_doqty[$key1] += (float)$row1['nweight'];
                                        $iwsa_doamt[$key1] += (float)$row1['amount'];
                                    }
                                }
                                else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
                            }
                            //Mortality
                            $sql1 = "SELECT * FROM `main_mortality` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND (`ccode` IN ('$sec_list') OR `warehouse` IN ('$sec_list')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`code` ASC";
                            $query1 = mysqli_query($conn, $sql1); $iwm_obds = $iwm_oqty = $iwm_oamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $key1 = $row1['itemcode'];
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $iwm_obds[$key1] += (float)$row1['birds'];
                                    $iwm_oqty[$key1] += (float)$row1['quantity'];
                                    $iwm_oamt[$key1] += (float)$row1['amount'];
                                }
                                else{ }
                            }

                            //Opening Calculations
                            $opn_bds = $opn_qty = $opn_amt = array();
                            foreach($item_alist as $icode){
                                $op_bds = 0; if(!empty($iwp_obds[$icode]) && $iwp_obds[$icode] != ""){ $op_bds = $iwp_obds[$icode]; }
                                $os_bds = 0; if(!empty($iws_obds[$icode]) && $iws_obds[$icode] != ""){ $os_bds = $iws_obds[$icode]; }
                                $oa_bds = 0; if(!empty($iwsa_aobds[$icode]) && $iwsa_aobds[$icode] != ""){ $oa_bds = $iwsa_aobds[$icode]; }
                                $od_bds = 0; if(!empty($iwsa_dobds[$icode]) && $iwsa_dobds[$icode] != ""){ $od_bds = $iwsa_dobds[$icode]; }
                                $om_bds = 0; if(!empty($iwm_obds[$icode]) && $iwm_obds[$icode] != ""){ $om_bds = $iwm_obds[$icode]; }
                                $ob_bds = 0; $ob_bds = round((((float)$op_bds + (float)$oa_bds) - ((float)$os_bds + (float)$od_bds + (float)$om_bds)),2);
                                $opn_bds[$icode] += (float)$ob_bds;

                                $op_qty = 0; if(!empty($iwp_oqty[$icode]) && $iwp_oqty[$icode] != ""){ $op_qty = $iwp_oqty[$icode]; }
                                $os_qty = 0; if(!empty($iws_oqty[$icode]) && $iws_oqty[$icode] != ""){ $os_qty = $iws_oqty[$icode]; }
                                $oa_qty = 0; if(!empty($iwsa_aoqty[$icode]) && $iwsa_aoqty[$icode] != ""){ $oa_qty = $iwsa_aoqty[$icode]; }
                                $od_qty = 0; if(!empty($iwsa_doqty[$icode]) && $iwsa_doqty[$icode] != ""){ $od_qty = $iwsa_doqty[$icode]; }
                                $om_qty = 0; if(!empty($iwm_oqty[$icode]) && $iwm_oqty[$icode] != ""){ $om_qty = $iwm_oqty[$icode]; }
                                $ob_qty = round(((float)$op_qty + (float)$oa_qty) - ((float)$os_qty + (float)$od_qty + (float)$om_qty), 2);
                                $opn_qty[$icode] += (float)$ob_qty;

                                $op_amt = 0; if(!empty($iwp_oamt[$icode]) && $iwp_oamt[$icode] != ""){ $op_amt = $iwp_oamt[$icode]; }
                                $os_amt = 0; if(!empty($iws_oamt[$icode]) && $iws_oamt[$icode] != ""){ $os_amt = $iws_oamt[$icode]; }
                                $oa_amt = 0; if(!empty($iwsa_aoamt[$icode]) && $iwsa_aoamt[$icode] != ""){ $oa_amt = $iwsa_aoamt[$icode]; }
                                $od_amt = 0; if(!empty($iwsa_doamt[$icode]) && $iwsa_doamt[$icode] != ""){ $od_amt = $iwsa_doamt[$icode]; }
                                $om_amt = 0; if(!empty($iwm_oamt[$icode]) && $iwm_oamt[$icode] != ""){ $om_amt = $iwm_oamt[$icode]; }
                                $ob_amt = 0; $ob_amt = round((((float)$op_amt + (float)$oa_amt) - ((float)$os_amt + (float)$od_amt + (float)$om_amt)),2);
                            }

                            $item_list = implode("','",$item_alist);
                            $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$item_list') AND `active` = '1' ORDER BY `description` ASC";
                            $query = mysqli_query($conn,$sql); $item_alist = array();
                            while($row = mysqli_fetch_assoc($query)){ $item_alist[$row['code']] = $row['code']; }
                            
                            $i = 0; $fti_jals = $fti_bds = $fti_twt = $fti_ewt = $fti_nwt = $fti_amt = array();
                            foreach($item_alist as $icode){
                                $iname = $item_name[$icode];
                                $jals = 0;
                                $birds = $opn_bds[$icode]; if($birds == ""){ $birds = 0; }
                                $tweight = 0;
                                $eweight = 0;
                                $nweight = $opn_qty[$icode]; if($nweight == ""){ $nweight = 0; }
                                $price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                $amount = ((float)$price * (float)$nweight); if($amount == ""){ $amount = 0; }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }
                                //$amount = $opn_amt[$icode]; if($amount == ""){ $amount = 0; }
                                //$price = 0; if((float)$nweight != 0){ $price = ((float)$amount / (float)$nweight); }

                                if((float)$birds == 0 && (float)$nweight == 0 && (float)$amount == 0){ }
                                else{
                                    /*$i++;
                                    $pur_sec[$i] .= '<td style="text-align:left;">Opening</td>';
                                    $pur_sec[$i] .= '<td style="text-align:left;">'.$iname.'</td>';
                                    if((int)$jals_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</td>'; }
                                    if((int)$birds_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</td>'; }
                                    if((int)$tweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($tweight).'</td>'; }
                                    if((int)$eweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($eweight).'</td>'; }
                                    $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                    $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($avg_wt).'</td>';
                                    $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                    $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                    */
                                    $fti_jals[$icode] += (float)$jals;
                                    $fti_bds[$icode] += (float)$birds;
                                    $fti_twt[$icode] += (float)$tweight;
                                    $fti_ewt[$icode] += (float)$eweight;
                                    $fti_nwt[$icode] += (float)$nweight;
                                    $fti_amt[$icode] += (float)$amount;
                                }
                            }

                            //Purchases
                            $sql1 = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwsi_bbds = $iwsi_bqty = $iwsi_bamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $i++;
                                $cname = $ven_name[$row1['vendorcode']];
                                $iname = $item_name[$row1['itemcode']];
                                $jals = $row1['jals']; if($jals == ""){ $jals = 0; }
                                $birds = $row1['birds']; if($birds == ""){ $birds = 0; }
                                $tweight = $row1['totalweight']; if($tweight == ""){ $tweight = 0; }
                                $eweight = $row1['emptyweight']; if($eweight == ""){ $eweight = 0; }
                                $nweight = $row1['netweight']; if($nweight == ""){ $nweight = 0; }
                                $price = $row1['itemprice']; if($price == ""){ $price = 0; }
                                $amount = $row1['totalamt']; if($amount == ""){ $amount = 0; }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }

                                $pur_sec[$i] .= '<td style="text-align:left;">'.$cname.'</td>';
                                $pur_sec[$i] .= '<td style="text-align:left;">'.$iname.'</td>';
                                if((int)$jals_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</td>'; }
                                if((int)$birds_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</td>'; }
                                if((int)$tweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($tweight).'</td>'; }
                                if((int)$eweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($eweight).'</td>'; }
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($avg_wt).'</td>';
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';

                                $fti_jals[$row1['itemcode']] += (float)$jals;
                                $fti_bds[$row1['itemcode']] += (float)$birds;
                                $fti_twt[$row1['itemcode']] += (float)$tweight;
                                $fti_ewt[$row1['itemcode']] += (float)$eweight;
                                $fti_nwt[$row1['itemcode']] += (float)$nweight;
                                $fti_amt[$row1['itemcode']] += (float)$amount;

                                $iwsi_bbds[$row1['itemcode']] += (float)$birds;
                                $iwsi_bqty[$row1['itemcode']] += (float)$nweight;
                                $iwsi_bamt[$row1['itemcode']] += (float)$amount;
                            }
                            //Transfer-In
                            $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `code` IN ('$item_list') AND `towarehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
                            $query1 = mysqli_query($conn,$sql1);
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $i++;
                                $cname = $sector_name[$row1['fromwarehouse']];
                                $iname = $item_name[$row1['code']];
                                $jals = $row1['jals']; if($jals == ""){ $jals = 0; }
                                $birds = $row1['birds']; if($birds == ""){ $birds = 0; }
                                $tweight = $row1['tweight']; if($tweight == ""){ $tweight = 0; }
                                $eweight = $row1['eweight']; if($eweight == ""){ $eweight = 0; }
                                $nweight = $row1['quantity']; if($nweight == ""){ $nweight = 0; }
                                $price = $row1['price']; if($price == ""){ $price = 0; }
                                $amount = ((float)$price * (float)$nweight); if($amount == ""){ $amount = 0; }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }

                                $pur_sec[$i] .= '<td style="text-align:left;">'.$cname.'</td>';
                                $pur_sec[$i] .= '<td style="text-align:left;">'.$iname.'</td>';
                                if((int)$jals_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</td>'; }
                                if((int)$birds_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</td>'; }
                                if((int)$tweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($tweight).'</td>'; }
                                if((int)$eweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($eweight).'</td>'; }
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($avg_wt).'</td>';
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $pur_sec[$i] .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';

                                $fti_jals[$row1['code']] += (float)$jals;
                                $fti_bds[$row1['code']] += (float)$birds;
                                $fti_twt[$row1['code']] += (float)$tweight;
                                $fti_ewt[$row1['code']] += (float)$eweight;
                                $fti_nwt[$row1['code']] += (float)$nweight;
                                $fti_amt[$row1['code']] += (float)$amount;

                                $iwsi_bbds[$row1['itemcode']] += (float)$birds;
                                $iwsi_bqty[$row1['itemcode']] += (float)$nweight;
                                $iwsi_bamt[$row1['itemcode']] += (float)$amount;
                            }
                            //Stock Adjustment
                            $sql1 = "SELECT * FROM `item_stock_adjustment` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`trnum` ASC";
                            $query1 = mysqli_query($conn, $sql1); $iwsa_abbds = $iwsa_dbbds = $iwsa_abqty = $iwsa_dbqty = $iwsa_abamt = $iwsa_dbamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $key1 = $row1['itemcode'];
                                if(strtotime($row1['date']) < strtotime($fdate)){ }
                                else{
                                    if($row1['a_type'] == "add"){
                                        $iwsa_abbds[$key1] += (float)$row1['birds'];
                                        $iwsa_abqty[$key1] += (float)$row1['nweight'];
                                        $iwsa_abamt[$key1] += (float)$row1['amount'];
                                    }
                                    else if($row1['a_type'] == "deduct"){
                                        $iwsa_dbbds[$key1] += (float)$row1['birds'];
                                        $iwsa_dbqty[$key1] += (float)$row1['nweight'];
                                        $iwsa_dbamt[$key1] += (float)$row1['amount'];
                                    }
                                }
                            }
                             //Shortage Screen
                             $sql1 = "SELECT * FROM `item_shortage_screen` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`trnum` ASC";
                             $query1 = mysqli_query($conn, $sql1); $iwsa_abbds = $iwsa_dbbds = $iwsa_abqty = $iwsa_dbqty = $iwsa_abamt = $iwsa_dbamt = array();
                             while($row1 = mysqli_fetch_assoc($query1)){
                                 $key1 = $row1['itemcode'];
                                 if(strtotime($row1['date']) < strtotime($fdate)){ }
                                 else{
                                     if($row1['a_type'] == "deduct"){
                                         $iwss_dbbds[$key1] += (float)$row1['birds'];
                                         $iwss_dbqty[$key1] += (float)$row1['nweight'];
                                         $iwss_dbamt[$key1] += (float)$row1['amount'];
                                     }
                                 }
                             }
                            //Mortality
                            $sql1 = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND (`ccode` IN ('$sec_list') OR `warehouse` IN ('$sec_list')) AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`code` ASC";
                            $query1 = mysqli_query($conn, $sql1); $iwm_bbds = $iwm_bqty = $iwm_bamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $key1 = $row1['itemcode'];
                                if(strtotime($row1['date']) < strtotime($fdate)){ }
                                else{
                                    $key1 = $row1['itemcode'];
                                    if($row1['mtype'] == "customer" || $row1['mtype'] == "supplier"){
                                        $iwcm_bbds[$key1] += (float)$row1['birds'];
                                        $iwcm_bqty[$key1] += (float)$row1['quantity'];
                                        $iwcm_bamt[$key1] += (float)$row1['amount'];
                                    }
                                    $iwm_bbds[$key1] += (float)$row1['birds'];
                                    $iwm_bqty[$key1] += (float)$row1['quantity'];
                                    $iwm_bamt[$key1] += (float)$row1['amount'];
                                }
                            }
                            //Total Stock Available-Purchase Side
                            foreach($item_alist as $icode){
                                $iname = $item_name[$icode];
                                $jals = $fti_jals[$icode]; if($jals == ""){ $jals = 0; }
                                $birds = $fti_bds[$icode]; if($birds == ""){ $birds = 0; }
                                $tweight = $fti_twt[$icode]; if($tweight == ""){ $tweight = 0; }
                                $eweight = $fti_ewt[$icode]; if($eweight == ""){ $eweight = 0; }
                                $nweight = $fti_nwt[$icode]; if($nweight == ""){ $nweight = 0; }
                                $amount = $fti_amt[$icode]; if($amount == ""){ $amount = 0; }
                                $price = 0; if((float)$nweight != 0){ $price = ((float)$amount / (float)$nweight); }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }

                                if((float)$birds == 0 && (float)$nweight == 0 && (float)$amount == 0){ }
                                else{
                                    /*$i++;
                                    $pur_sec[$i] .= '<th style="text-align:left;background-color:#98fb98;">Total</th>';
                                    $pur_sec[$i] .= '<th style="text-align:left;">'.$iname.'</th>';
                                    if((int)$jals_flag == 1){ $pur_sec[$i] .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</th>'; }
                                    if((int)$birds_flag == 1){ $pur_sec[$i] .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</th>'; }
                                    if((int)$tweight_flag == 1){ $pur_sec[$i] .= '<th style="text-align:right;">'.number_format_ind($tweight).'</th>'; }
                                    if((int)$eweight_flag == 1){ $pur_sec[$i] .= '<th style="text-align:right;">'.number_format_ind($eweight).'</th>'; }
                                    $pur_sec[$i] .= '<th style="text-align:right;">'.number_format_ind($nweight).'</th>';
                                    $pur_sec[$i] .= '<th style="text-align:right;">'.number_format_ind($avg_wt).'</th>';
                                    $pur_sec[$i] .= '<th style="text-align:right;">'.number_format_ind($price).'</th>';
                                    $pur_sec[$i] .= '<th style="text-align:right;">'.number_format_ind($amount).'</th>';*/
                                }
                            }

                            $fto_jals = $fto_bds = $fto_twt = $fto_ewt = $fto_nwt = $fto_amt = array();
                            //Sales
                            $sql1 = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
                            $query1 = mysqli_query($conn,$sql1); $j = 0; $iwso_bbds = $iwso_bqty = $iwso_bamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $j++;
                                $cname = $ven_name[$row1['customercode']];
                                $iname = $item_name[$row1['itemcode']];
                                $jals = $row1['jals']; if($jals == ""){ $jals = 0; }
                                $birds = $row1['birds']; if($birds == ""){ $birds = 0; }
                                $tweight = $row1['totalweight']; if($tweight == ""){ $tweight = 0; }
                                $eweight = $row1['emptyweight']; if($eweight == ""){ $eweight = 0; }
                                $nweight = $row1['netweight']; if($nweight == ""){ $nweight = 0; }
                                $price = $row1['itemprice']; if($price == ""){ $price = 0; }
                                $amount = $row1['totalamt']; if($amount == ""){ $amount = 0; }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }

                                $sale_sec[$j] .= '<td style="text-align:left;">'.$cname.'</td>';
                                $sale_sec[$j] .= '<td style="text-align:left;">'.$iname.'</td>';
                                if((int)$jals_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</td>'; }
                                if((int)$birds_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</td>'; }
                                if((int)$tweight_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($tweight).'</td>'; }
                                if((int)$eweight_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($eweight).'</td>'; }
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($avg_wt).'</td>';
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';

                                $fto_jals[$row1['itemcode']] += (float)$jals;
                                $fto_bds[$row1['itemcode']] += (float)$birds;
                                $fto_twt[$row1['itemcode']] += (float)$tweight;
                                $fto_ewt[$row1['itemcode']] += (float)$eweight;
                                $fto_nwt[$row1['itemcode']] += (float)$nweight;
                                $fto_amt[$row1['itemcode']] += (float)$amount;

                                $iwso_bbds[$row1['itemcode']] += (float)$birds;
                                $iwso_bqty[$row1['itemcode']] += (float)$nweight;
                                $iwso_bamt[$row1['itemcode']] += (float)$amount;
                            }
                            //Transfer-Out
                            $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `code` IN ('$item_list') AND `fromwarehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
                            $query1 = mysqli_query($conn,$sql1);
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $j++;
                                $cname = $sector_name[$row1['fromwarehouse']];
                                $iname = $item_name[$row1['code']];
                                $jals = $row1['jals']; if($jals == ""){ $jals = 0; }
                                $birds = $row1['birds']; if($birds == ""){ $birds = 0; }
                                $tweight = $row1['tweight']; if($tweight == ""){ $tweight = 0; }
                                $eweight = $row1['eweight']; if($eweight == ""){ $eweight = 0; }
                                $nweight = $row1['quantity']; if($nweight == ""){ $nweight = 0; }
                                $price = $row1['price']; if($price == ""){ $price = 0; }
                                $amount = ((float)$price * (float)$nweight); if($amount == ""){ $amount = 0; }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }

                                $sale_sec[$j] .= '<td style="text-align:left;">'.$cname.'</td>';
                                $sale_sec[$j] .= '<td style="text-align:left;">'.$iname.'</td>';
                                if((int)$jals_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</td>'; }
                                if((int)$birds_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</td>'; }
                                if((int)$tweight_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($tweight).'</td>'; }
                                if((int)$eweight_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($eweight).'</td>'; }
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($avg_wt).'</td>';
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $sale_sec[$j] .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';

                                $fto_jals[$row1['code']] += (float)$jals;
                                $fto_bds[$row1['code']] += (float)$birds;
                                $fto_twt[$row1['code']] += (float)$tweight;
                                $fto_ewt[$row1['code']] += (float)$eweight;
                                $fto_nwt[$row1['code']] += (float)$nweight;
                                $fto_amt[$row1['code']] += (float)$amount;

                                $iwso_bbds[$row1['code']] += (float)$birds;
                                $iwso_bqty[$row1['code']] += (float)$nweight;
                                $iwso_bamt[$row1['code']] += (float)$amount;
                            }
                            //Total Stock Used-Sales Side
                            foreach($item_alist as $icode){
                                $iname = $item_name[$icode];
                                $jals = $fto_jals[$icode]; if($jals == ""){ $jals = 0; }
                                $birds = $fto_bds[$icode]; if($birds == ""){ $birds = 0; }
                                $tweight = $fto_twt[$icode]; if($tweight == ""){ $tweight = 0; }
                                $eweight = $fto_ewt[$icode]; if($eweight == ""){ $eweight = 0; }
                                $nweight = $fto_nwt[$icode]; if($nweight == ""){ $nweight = 0; }
                                $amount = $fto_amt[$icode]; if($amount == ""){ $amount = 0; }
                                $price = 0; if((float)$nweight != 0){ $price = ((float)$amount / (float)$nweight); }
								$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }

                                if((float)$birds == 0 && (float)$nweight == 0 && (float)$amount == 0){ }
                                else{
                                    $j++;
                                    $sale_sec[$j] .= '<th style="text-align:left;background-color:#98fb98;">Total</th>';
                                    $sale_sec[$j] .= '<th style="text-align:left;">'.$iname.'</th>';
                                    if((int)$jals_flag == 1){ $sale_sec[$j] .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($jals)).'</th>'; }
                                    if((int)$birds_flag == 1){ $sale_sec[$j] .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($birds)).'</th>'; }
                                    if((int)$tweight_flag == 1){ $sale_sec[$j] .= '<th style="text-align:right;">'.number_format_ind($tweight).'</th>'; }
                                    if((int)$eweight_flag == 1){ $sale_sec[$j] .= '<th style="text-align:right;">'.number_format_ind($eweight).'</th>'; }
                                    $sale_sec[$j] .= '<th style="text-align:right;">'.number_format_ind($nweight).'</th>';
                                    $sale_sec[$j] .= '<th style="text-align:right;">'.number_format_ind($avg_wt).'</th>';
                                    $sale_sec[$j] .= '<th style="text-align:right;">'.number_format_ind($price).'</th>';
                                    $sale_sec[$j] .= '<th style="text-align:right;">'.number_format_ind($amount).'</th>';
                                }
                            }

                            if($i < $j){
                                $l = $i;
                                for($k = $l + 1;$k <= $j;$k++){
                                    $i = $k;
                                    $pur_sec[$i] .= '<td style="text-align:left;"></td>';
                                    $pur_sec[$i] .= '<td style="text-align:left;"></td>';
                                    if((int)$jals_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;"></td>'; }
                                    if((int)$birds_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;"></td>'; }
                                    if((int)$tweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;"></td>'; }
                                    if((int)$eweight_flag == 1){ $pur_sec[$i] .= '<td style="text-align:right;"></td>'; }
                                    $pur_sec[$i] .= '<td style="text-align:right;"></td>';
                                    $pur_sec[$i] .= '<td style="text-align:right;"></td>';
                                    $pur_sec[$i] .= '<td style="text-align:right;"></td>';
                                    $pur_sec[$i] .= '<td style="text-align:right;"></td>';
                                }
                            }
                            else if($j < $i){
                                $l = $j;
                                for($k = $l + 1;$k <= $i;$k++){
                                    $j = $k;
                                    $sale_sec[$j] .= '<td style="text-align:left;"></td>';
                                    $sale_sec[$j] .= '<td style="text-align:left;"></td>';
                                    if((int)$jals_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;"></td>'; }
                                    if((int)$birds_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;"></td>'; }
                                    if((int)$tweight_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;"></td>'; }
                                    if((int)$eweight_flag == 1){ $sale_sec[$j] .= '<td style="text-align:right;"></td>'; }
                                    $sale_sec[$j] .= '<td style="text-align:right;"></td>';
                                    $sale_sec[$j] .= '<td style="text-align:right;"></td>';
                                    $sale_sec[$j] .= '<td style="text-align:right;"></td>';
                                    $sale_sec[$j] .= '<td style="text-align:right;"></td>';
                                }
                            }
                            else{ }
                            
                            for($a = 1;$a <= $i;$a++){
                                $html .= '<tr>';
                                $html .= $pur_sec[$a]."".$sale_sec[$a];
                                $html .= '</tr>';
                            }

                            //Item Summary
                            $c_cnt = $c_cnt * 2;
                            $isize = sizeof($item_alist);
                            $i_cnt = $isize * 3;

                            $html .= '<tr>';
                                $html .= '<th colspan="'.$c_cnt.'">';
								if($items == "all"){ $html .= '<table style="width:100%">'; } else{ $html .= '<table>'; }
                                    
                                        $html .= '<tr style="background-color: #9F81F7;">';
                                        $html .= '<th rowspan="2">Final Total</th>';
                                        foreach($item_alist as $icode){ $html .= '<th style="text-align:center;" colspan="3">'.$item_name[$icode].'</th>'; }
                                        $html .= '</tr>';
                                        $html .= '<tr style="background-color: #9F81F7;">';
                                        foreach($item_alist as $icode){
                                            $html .= '<th style="text-align:center;">Birds</th>';
                                            $html .= '<th style="text-align:center;">Weight</th>';
                                            $html .= '<th style="text-align:center;">Amount</th>';
                                        }
                                        $html .= '</tr>';

                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Total Openings</th>';
                                        foreach($item_alist as $icode){
                                            $birds = $opn_bds[$icode]; if($birds == ""){ $birds = 0; }
                                            $nweight = $opn_qty[$icode]; if($nweight == ""){ $nweight = 0; }
                                            $price = $iwp_opprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = ((float)$price * (float)$nweight); if($amount == ""){ $amount = 0; }
                                
                                            $opn_amt[$icode] += (float)$amount;

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        }
                                        $html .= '</tr>';
                                        
                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Total Purchase/T. IN</th>';
                                        foreach($item_alist as $icode){
                                            $birds = $iwsi_bbds[$icode]; if($birds == ""){ $birds = 0; }
                                            $nweight = $iwsi_bqty[$icode]; if($nweight == ""){ $nweight = 0; }
                                            //$price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = $iwsi_bamt[$icode]; if($amount == ""){ $amount = 0; }

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        }
                                        $html .= '</tr>';
                                        $tot_sqty = 0;
                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Total Sold/T. OUT</th>';
                                        foreach($item_alist as $icode){
                                            $birds = $iwso_bbds[$icode]; if($birds == ""){ $birds = 0; }
                                            $nweight = $iwso_bqty[$icode]; if($nweight == ""){ $nweight = 0; }
                                            //$price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = $iwso_bamt[$icode]; if($amount == ""){ $amount = 0; }

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                            $tot_sqty += (float)$nweight;
                                        }
                                        $html .= '</tr>';
                                        
                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Total Mortality</th>';
                                        foreach($item_alist as $icode){
                                            $birds = $iwm_bbds[$icode]; if($birds == ""){ $birds = 0; }
                                            $nweight = $iwm_bqty[$icode]; if($nweight == ""){ $nweight = 0; }
                                            //$price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = $iwm_bamt[$icode]; if($amount == ""){ $amount = 0; }

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        }
                                        $html .= '</tr>';

                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Total Net Sold/T. OUT</th>';
                                        foreach($item_alist as $icode){
                                            $birds = ((float)$iwso_bbds[$icode] - (float)$iwcm_bbds[$icode]); if($birds == ""){ $birds = 0; }
                                            $nweight = ((float)$iwso_bqty[$icode] - (float)$iwcm_bqty[$icode]); if($nweight == ""){ $nweight = 0; }
                                            //$price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = ((float)$iwso_bamt[$icode] - (float)$iwcm_bamt[$icode]); if($amount == ""){ $amount = 0; }

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                            $tot_sqty += (float)$nweight;
                                        }
                                        $html .= '</tr>';
                                        
                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Stock Adjustment</th>';
                                        foreach($item_alist as $icode){
                                            $birds = ((float)$iwsa_abbds[$icode] - (float)$iwsa_dbbds[$icode]); if($birds == ""){ $birds = 0; }
                                            $nweight = ((float)$iwsa_abqty[$icode] - (float)$iwsa_dbqty[$icode]); if($nweight == ""){ $nweight = 0; }
                                            $amount = ((float)$iwsa_abamt[$icode] - (float)$iwsa_dbamt[$icode]); if($amount == ""){ $amount = 0; }

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        }
                                        $html .= '</tr>';

                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Shortage Screen</th>';
                                        foreach($item_alist as $icode){
                                            // $birds = ((float)$iwss_abbds[$icode] - (float)$iwss_dbbds[$icode]); if($birds == ""){ $birds = 0; }
                                            $nweight =  (float)$iwss_dbqty[$icode]; if($nweight == ""){ $nweight = 0; }
                                            $amount =  (float)$iwss_dbamt[$icode]; if($amount == ""){ $amount = 0; }

                                            //$html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;"></td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        }
                                        $html .= '</tr>';
                                        $cls_amt = array();
                                        $html .= '<tr>';
                                        $html .= '<th style="background-color: #9F81F7;">Total Closing</th>';
                                        foreach($item_alist as $icode){
                                            $birds = round((((float)$opn_bds[$icode] + (float)$iwsi_bbds[$icode] + (float)$iwsa_abbds[$icode] + (float)$iwcm_bbds[$icode]) - ((float)$iwm_bbds[$icode] + (float)$iwso_bbds[$icode] + (float)$iwsa_dbbds[$icode])),2); if($birds == ""){ $birds = 0; }
                                            $nweight = round((((float)$opn_qty[$icode] + (float)$iwsi_bqty[$icode] + (float)$iwsa_abqty[$icode] + (float)$iwcm_bqty[$icode]) - ((float)$iwm_bqty[$icode] + (float)$iwso_bqty[$icode] + (float)$iwsa_dbqty[$icode] + (float)$iwss_dbqty[$icode])),2); if($nweight == ""){ $nweight = 0; }
                                            $price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = ((float)$price * (float)$nweight);
                                            //$amount = round(((float)$iwso_bamt[$icode] - ((float)$opn_amt[$icode] + (float)$iwsi_bamt[$icode] - (float)$iwm_bamt[$icode])),2); if($amount == ""){ $amount = 0; }
                                            $cls_amt[$icode] += (float)$amount;

                                            $html .= '<td style="text-align:right;">'.str_replace( ".00","",number_format_ind($birds)).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($nweight).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        }
                                        $html .= '</tr>';
                                        
                                    $html .= '</table>';
                                $html .='</th>';
                            $html .='</tr>';
                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                var users = document.getElementById("users").value;
                var sectors = document.getElementById("sectors").value;
                var l = true;
                if(users == "select"){
                    alert("Kindly select User");
                    l = false;
                }
                else if(sectors == "select"){
                    alert("Kindly select Shop/Outlet");
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();
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
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>