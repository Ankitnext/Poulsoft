<?php
//chicken_daily_report.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_daily_report.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_daily_report.php?db=".$db;
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
$file_name = "CHICKEN DAILY REPORT";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

// Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

// Warehouse
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $loc_access = ""; $adm_aflag = 0;
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; if((int)$row['supadmin_access'] == 1 || (int)$row['admin_access'] == 1){ $adm_aflag = 1; } }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $csup_alist = $carea_alist = array();
while($row = mysqli_fetch_assoc($query)){ $csup_alist[$row['supr_code']] = $row['supr_code']; $carea_alist[$row['area_code']] = $row['area_code']; }

$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }

//Supervisor Details
$supv_list = implode("','",$csup_alist);
$sql = "SELECT * FROM `chicken_employee` WHERE `code` IN ('$supv_list') AND `dflag`= '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $csupr_code = $csupr_name = array();
while($row = mysqli_fetch_assoc($query)){ $csupr_code[$row['code']] = $row['code']; $csupr_name[$row['code']] = $row['name']; }

//Sector Access Filter
if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
else{
    $loc1 = explode(",",$loc_access); $loc_list = "";
    foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
    $sec_fltr = " AND `code` IN ('$loc_list')";
}
//Sector Details
// $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
// $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
// while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $csup_alist = $carea_alist = array();
while($row = mysqli_fetch_assoc($query)){ $csup_alist[$row['supr_code']] = $row['supr_code']; $carea_alist[$row['area_code']] = $row['area_code']; }

//Area Details
$area_list = implode("','",$carea_alist);
$sql = "SELECT * FROM `main_areas` WHERE `code` IN ('$area_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";$query = mysqli_query($conn,$sql); $area_code = $area_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $area_code[$row['code']] = $row['code']; $area_name[$row['code']] = $row['description']; }

//Fetch User Details
if((int)$adm_aflag == 1){
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `dflag` = '0' ORDER BY `username` ASC";
}
else{
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `empcode` LIKE '$emp_code' AND `dflag` = '0' ORDER BY `username` ASC";
}
$query = mysqli_query($conns,$sql); $usr_code = $usr_name = array();
while($row = mysqli_fetch_assoc($query)){ $usr_code[$row['empcode']] = $row['empcode']; $usr_name[$row['empcode']] = $row['username']; }

//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$fdate = $tdate = date("Y-m-d"); $sectors = $users = $items = "all"; $fstyles = $fsizes = "default"; $types = "tr_date"; $exports = "display"; 
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d", strtotime($_POST['fdate']));
    $tdate = date("Y-m-d", strtotime($_POST['tdate']));
    $types = $_POST['types'];
    $users = $_POST['users'];
    $items = $_POST['items'];
    $sectors = $_POST['sectors'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
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
            .thead4{
                background-color: #9cc2d5;
            }
            .tfoot4{
                background-color: #9cc2d5;
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
                            <?php
                                if($dlogo_flag > 0) { ?>
                                    <td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                                <?php }
                                else{ 
                                ?>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; }?></h3>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
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
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($sectors == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        
                                        <?php if((int)$font_fflag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="fstyles">Font-Family</label>
                                            <select name="fstyles" id="fstyles" class="form-control select2" style="width:180px;">
                                                <option value="default" <?php if($fstyles == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_id as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fstyles == $scode){ echo "selected"; } ?>><?php echo $font_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:70px;">
                                            <label for="fsizes">Font-Size</label>
                                            <select name="fsizes" id="fsizes" class="form-control select2" style="width:60px;">
                                                <option value="default" <?php if($fsizes == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_sizes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fsizes == $scode){ echo "selected"; } ?>><?php echo $font_sizes[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="exports">Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
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
                           
                            $html = $nhtml = $fhtml = '';
                            
                            // Opening Data 
                            $html .= '<thead class="thead4" id="head_names">';
                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" >Opening Stock</th>'; $fhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" id="order">Opening Stock</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';

                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th style="text-align:left;width:5px;">Sl No.</th>'; $fhtml .= '<th style="text-align:left;width:5px;" id="order">Sl No.</th>';
                            $nhtml .= '<th style="text-align:center;">Date</th>'; $fhtml .= '<th style="text-align:center; id="order_date">Date</th>';
                            $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Birds</th>';
                            $nhtml .= '<th style="text-align:center;" >Weight</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Weight</th>';
                            $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                            $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';
                            $html .= $fhtml;
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';

                           
                            $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                            $itm_fltr = ""; if($items != "all"){ $itm_fltr = " AND `itemcode` = '$items'"; }
                            $usr_fltr = ""; if($users != "all"){ $usr_fltr = " AND `addedemp` = '$users'"; }
                            $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                            $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                            $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                            $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                          
                            $pdate = date('Y-m-d', strtotime($fdate. ' - 1 days'));
                            if($items != "all"){ $item_list = $items; } else{ $item_list = implode("','",$item_code); }
                            if($sectors != "all"){ $sec_list = $sectors; } else{ $sec_list = implode("','",$sector_code); }
                            $pur_sec = $sale_sec = $item_alist = array();

                            $sql1 = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate' AND `active` = '1' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwp_obds = $iwp_oqty = $iwp_oprc = $iwp_opprc = $iwp_oamt = $iwp_bqty = $iwp_bamt = $addedemps = $addedtimes = $snames = $inames = $date_ct = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['itemcode'];
                                    $iwp_obds[$key1] += (float)$row1['birds'];
                                    $iwp_oqty[$key1] += (float)$row1['netweight'];
                                    $iwp_oamt[$key1] += (float)$row1['totalamt'];
                                    $iwp_oprc[$key1] = (float)$row1['itemprice'];
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $date_ct[$key1] = $row1['date'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $inames[$key1] = $item_name[$row1['itemcode']];
                                    $snames[$key1] = $sector_name[$row1['warehouse']];
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
                                    $date_ct[$key1] = $row1['date'];
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
                                    $date_ct[$key1] = $row1['date'];
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $inames[$key1] = $item_name[$row1['itemcode']];
                                    $snames[$key1] = $sector_name[$row1['warehouse']];
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
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $date_ct[$key1] = $row1['date'];
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
                                        $date_ct[$key1] = $row1['date'];
                                        $addedtimes[$key1] = $row1['addedtime'];
                                        $addedemps[$key1] = $row1['addedemp'];
                                        $inames[$key1] = $item_name[$row1['itemcode']];
                                        $snames[$key1] = $sector_name[$row1['warehouse']];
                                    }
                                    else if($row1['a_type'] == "deduct"){
                                        $iwsa_dobds[$key1] += (float)$row1['birds'];
                                        $iwsa_doqty[$key1] += (float)$row1['nweight'];
                                        $iwsa_doamt[$key1] += (float)$row1['amount'];
                                        $date_ct[$key1] = $row1['date'];
                                        $addedtimes[$key1] = $row1['addedtime'];
                                        $addedemps[$key1] = $row1['addedemp'];
                                        $inames[$key1] = $item_name[$row1['itemcode']];
                                        $snames[$key1] = $sector_name[$row1['warehouse']];
                                    }
                                }
                                else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
                            }
                            //Mortality
                            $sql1 = "SELECT * FROM `main_mortality` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `ccode` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`code` ASC";
                            $query1 = mysqli_query($conn, $sql1); $iwm_obds = $iwm_oqty = $iwm_oamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $key1 = $row1['itemcode'];
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $iwm_obds[$key1] += (float)$row1['birds'];
                                    $iwm_oqty[$key1] += (float)$row1['quantity'];
                                    $iwm_oamt[$key1] += (float)$row1['amount'];
                                    $date_ct[$key1] = $row1['date'];
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $inames[$key1] = $item_name[$row1['itemcode']];
                                    $snames[$key1] = $sector_name[$row1['ccode']];
                                }
                                else{ }
                            }

                            //Opening Calculations
                            $opn_bdss = $opn_qtyy = $opn_amts = array(); $sl = 1;
                            foreach($item_alist as $icode){
                                $op_bds = 0; if(!empty($iwp_obds[$icode]) && $iwp_obds[$icode] != ""){ $op_bds = $iwp_obds[$icode]; }
                                $os_bds = 0; if(!empty($iws_obds[$icode]) && $iws_obds[$icode] != ""){ $os_bds = $iws_obds[$icode]; }
                                $oa_bds = 0; if(!empty($iwsa_aobds[$icode]) && $iwsa_aobds[$icode] != ""){ $oa_bds = $iwsa_aobds[$icode]; }
                                $od_bds = 0; if(!empty($iwsa_dobds[$icode]) && $iwsa_dobds[$icode] != ""){ $od_bds = $iwsa_dobds[$icode]; }
                                $om_bds = 0; if(!empty($iwm_obds[$icode]) && $iwm_obds[$icode] != ""){ $om_bds = $iwm_obds[$icode]; }
                                $ob_bds = 0; $ob_bds = round((((float)$op_bds + (float)$oa_bds) - ((float)$os_bds + (float)$od_bds + (float)$om_bds)),2);
                                $opn_bdss[$icode] += (float)$ob_bds;
                                $opn_bds = $opn_bdss[$icode];

                                $op_qty = 0; if(!empty($iwp_oqty[$icode]) && $iwp_oqty[$icode] != ""){ $op_qty = $iwp_oqty[$icode]; }
                                $os_qty = 0; if(!empty($iws_oqty[$icode]) && $iws_oqty[$icode] != ""){ $os_qty = $iws_oqty[$icode]; }
                                $oa_qty = 0; if(!empty($iwsa_aoqty[$icode]) && $iwsa_aoqty[$icode] != ""){ $oa_qty = $iwsa_aoqty[$icode]; }
                                $od_qty = 0; if(!empty($iwsa_doqty[$icode]) && $iwsa_doqty[$icode] != ""){ $od_qty = $iwsa_doqty[$icode]; }
                                $om_qty = 0; if(!empty($iwm_oqty[$icode]) && $iwm_oqty[$icode] != ""){ $om_qty = $iwm_oqty[$icode]; }
                                $ob_qty = round(((float)$op_qty + (float)$oa_qty) - ((float)$os_qty + (float)$od_qty + (float)$om_qty), 2);
                                $opn_qtyy[$icode] += (float)$ob_qty;
                                $bird_qty = $opn_qtyy[$icode];

                                $op_amt = 0; if(!empty($iwp_oamt[$icode]) && $iwp_oamt[$icode] != ""){ $op_amt = $iwp_oamt[$icode]; }
                                $os_amt = 0; if(!empty($iws_oamt[$icode]) && $iws_oamt[$icode] != ""){ $os_amt = $iws_oamt[$icode]; }
                                $oa_amt = 0; if(!empty($iwsa_aoamt[$icode]) && $iwsa_aoamt[$icode] != ""){ $oa_amt = $iwsa_aoamt[$icode]; }
                                $od_amt = 0; if(!empty($iwsa_doamt[$icode]) && $iwsa_doamt[$icode] != ""){ $od_amt = $iwsa_doamt[$icode]; }
                                $om_amt = 0; if(!empty($iwm_oamt[$icode]) && $iwm_oamt[$icode] != ""){ $om_amt = $iwm_oamt[$icode]; }
                                $ob_amt = 0; $ob_amt = round((((float)$op_amt + (float)$oa_amt) - ((float)$os_amt + (float)$od_amt + (float)$om_amt)),2);
                                $bird_amt = $ob_amt;

                                  // $birds = $opn_bds[$icode]; if($birds == ""){ $birds = 0; }
                                $opn_qty = $nweight = $opn_qtyy[$icode]; if($nweight == ""){ $nweight = 0; }
                                $opn_prc = $price = $iwp_opprc[$icode]; if($price == ""){ $price = 0; }
                                $amount = ((float)$price * (float)$nweight); if($amount == ""){ $amount = 0; }
                        
                                $opn_amt = $opn_amts[$icode] += (float)$amount;
                                $addedtime = $addedtimes[$icode];
                                $addedemp = $addedemps[$icode];
                                $iname = $inames[$icode];
                                $sname = $snames[$icode];
                                $date_cts = $date_ct[$icode];

                               
                                // $birds = round((((float)$opn_bdss[$icode] + (float)$iwsi_bbds[$icode] + (float)$iwsa_abbds[$icode]) - ((float)$iwm_bbds[$icode] + (float)$iwso_bbds[$icode] + (float)$iwsa_dbbds[$icode])),2); if($birds == ""){ $birds = 0; }
                                // $nweight = round((((float)$opn_qtyy[$icode] + (float)$iwsi_bqty[$icode] + (float)$iwsa_abqty[$icode]) - ((float)$iwm_bqty[$icode] + (float)$iwso_bqty[$icode] + (float)$iwsa_dbqty[$icode])),2); if($nweight == ""){ $nweight = 0; }
                                // $price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                // $amount = ((float)$price * (float)$nweight);
                                // //$amount = round(((float)$iwso_bamt[$icode] - ((float)$opn_amt[$icode] + (float)$iwsi_bamt[$icode] - (float)$iwm_bamt[$icode])),2); if($amount == ""){ $amount = 0; }
                                // $cls_amt[$icode] += (float)$amount;
                            
                                //$sname = $sector_name[$row['addedemp']];
                            }
                                
                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$sl++.'</td>';
                                $html .= '<td style="text-align:left;">'.date("d.m.Y",strtotime($date_cts)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_bds).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_prc).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_amt).'</td>';
                                
                               // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                $html .= '</tr>';

                                
                                // Accumulate overall totals
                               
                                $tjals += $jals;
                                $tbirds += $opn_bds;
                               
                                $tquantity_opn += (float)$opn_qty;
                                $tprice_opn += $opn_prc;
                                $tamount_op += (float)$opn_amt;
                            
                               $html .= '</tbody>';
                                // Add totals row
                                $html .= '<thead class="tfoot4">';
                                $html .= '<tr >';
                                $html .= '<th colspan="2">Total</th>';
                                // Output the overall total for all weeks
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tbirds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity_opn, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice_opn, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_op, 2)) . '</th>';
                                $html .= '</tr>';
                                $html .= '</thead>';
                                
                              
                            // Purchase -----------------------------------------------------------------------------------------------------------    
                            $nhtml = $fhtml = '';
                            
                            // Purchase Data 
                            $html .= '<thead class="thead4" id="head_names">';
                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" >Purchase</th>'; $fhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" id="order">Purchase</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';

                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th style="text-align:center;">Sl No.</th>'; $fhtml .= '<th style="text-align:center; id="order">Sl No.</th>';
                            $nhtml .= '<th style="text-align:center;">Date</th>'; $fhtml .= '<th style="text-align:center; id="order_date">Date</th>';
                            $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Birds</th>';
                            $nhtml .= '<th style="text-align:center;" >Weight</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Weight</th>';
                            $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                            $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                       
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';
                            $html .= $fhtml;
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';

                           
                            $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                            $itm_fltr = ""; if($items != "all"){ $itm_fltr = " AND `itemcode` = '$items'"; }
                            $usr_fltr = ""; if($users != "all"){ $usr_fltr = " AND `addedemp` = '$users'"; }
                            $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                            $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                            $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                            $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                          
                            $sql = "SELECT * FROM `pur_purchase` WHERE `tdflag` = '0' AND `date` >= '$fdate' AND `date` <= '$tdate'".$itm_fltr."".$sec_fltr." AND `active` = '1' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl1 = 1;
                            while($row = mysqli_fetch_assoc($query)){
                                $date = date("d.m.Y",strtotime($row['date']));
                                $addedtime = $row['addedtime'];
                                $addedemp = $row['addedemp'];
                                $jals = $row['jals'];
                                $birds = $row['birds'];
                                $totalweight = $row['totalweight'];
                                $emptyweight = $row['emptyweight'];
                                $netweight = $row['netweight'];
                                $quantity = $row['closedquantity'];
                                $price = $row['itemprice'];
                                $amount = $row['totalamt'];
                                $remarks = $row['remarks'];
                                $iname = $item_name[$row['itemcode']];
                                $sname = $sector_name[$row['warehouse']];
                                //$sname = $sector_name[$row['addedemp']];

                                
                                $slno++; 
                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$sl1++.'</td>';
                                $html .= '<td style="text-align:left;">'.$date.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($birds).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($netweight).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                
                               // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                $html .= '</tr>';

                                
                                // Accumulate overall totals
                               
                                $tjals += $jals;
                                $tbirds += $birds;
                                $ttotalweight += $totalweight;
                                $temptyweight += $emptyweight;
                                $tnetweight += $netweight;
                                $tquantity += $quantity;
                                $tprice += $price;
                                $tamount += $amount;
                            }
                               $html .= '</tbody>';
                                // Add totals row
                                $html .= '<thead class="tfoot4">';
                                $html .= '<tr >';
                                $html .= '<th colspan="2">Total</th>';
                                // Output the overall total for all weeks
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tbirds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tnetweight, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                $html .= '</tr>';
                                $html .= '</thead>';


                                 // Sales Data ------------------------------------------------------------------------------------------------------ 
                              
                                // $html = $nhtml = $fhtml = '';
                                $nhtml = $fhtml = ''; // CLEAR previous values


                                $html .= '<thead class="thead4" id="head_names">';
                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" >Sales</th>'; $fhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" id="order">Sales</th>';
                                $nhtml .= '</tr>';
                                $fhtml .= '</tr>';

                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th style="text-align:center; width:5px;">Sl No.</th>'; $fhtml .= '<th style="text-align:center; width:5px;" id="order">Sl No.</th>';
                                $nhtml .= '<th style="text-align:center;">Date</th>'; $fhtml .= '<th style="text-align:center;" id="order_date">Date</th>';
                                $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Birds</th>';
                                $nhtml .= '<th style="text-align:center;" >Weight</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Weight</th>';
                                $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                                $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                $nhtml .= '</tr>';
                                $fhtml .= '</tr>';
                                $html .= $fhtml;
                                $html .= '</thead>';
                                $html .= '<tbody class="tbody1">';

                                $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                                $itm_fltr = ""; if($items != "all"){ $itm_fltr = " AND `itemcode` = '$items'"; }
                                $usr_fltr = ""; if($users != "all"){ $usr_fltr = " AND `addedemp` = '$users'"; }
                                $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                                $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                                $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                                $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                            
                                $sql = "SELECT * FROM `customer_sales` WHERE `tdflag` = '0' AND `date` >= '$fdate' AND `date` <= '$tdate'".$itm_fltr."".$sec_fltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date` ASC";
                                $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl2 = 1;
                                while($row = mysqli_fetch_assoc($query)){
                                    $datee = date("d.m.Y",strtotime($row['date']));
                                    $addedtime = $row['addedtime'];
                                    $jals = $row['jals'];
                                    $birds = $row['birds'];
                                    $totalweight = $row['totalweight'];
                                    $emptyweight = $row['emptyweight'];
                                    $netweight = $row['netweight'];
                                    $iname = $item_name[$row['itemcode']];
                                    $quantity = $row['closedquantity'];
                                    $price = $row['itemprice'];
                                     $amount = $row['totalamt'];
                                    $remarks = $row['remarks'];
                                    $addedemp = $row['addedemp'];
                                    $sname = $sector_name[$row['warehouse']];

                                
                                $tjals_sales += $jals;
                                $tbirds_sales += $birds;
                                $ttotalweight_sales += $totalweight;
                                $temptyweight_sales += $emptyweight;
                                $tnetweight_sales += $netweight;
                                $tquantity_sales += $quantity;
                                $tprice_sales += $price;
                                $tamount_sales += $amount;
                                }
                                    $slno++; 
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$sl2++.'</td>';
                                    $html .= '<td style="text-align:left;">'.$datee.'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tbirds_sales).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tnetweight_sales).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tprice_sales).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($tamount_sales).'</td>';
                                    
                                // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                    $html .= '</tr>';

                                    
                                    // Accumulate overall totals
                                
                                  
                                // }
                                $html .= '</tbody>';
                                    // Add totals row
                                    $html .= '<thead class="tfoot4">';
                                    $html .= '<tr >';
                                    $html .= '<th colspan="2">Total</th>';
                                    // Output the overall total for all weeks
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tbirds_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tnetweight_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_sales, 2)) . '</th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';


                                 // Payments Data ------------------------------------------------------------------------------------------------------ 
                              
                                // $html = $nhtml = $fhtml = '';
                                $nhtml = $fhtml = ''; // CLEAR previous values


                                $html .= '<thead class="thead4" id="head_names">';
                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" >Closing Stock</th>'; $fhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" id="order">Closing Stock</th>';
                                $nhtml .= '</tr>';
                                $fhtml .= '</tr>';

                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th style="text-align:center; width:5px;">Sl No.</th>'; $fhtml .= '<th style="text-align:center; width:5px;" id="order">Sl No.</th>';
                                $nhtml .= '<th style="text-align:center;">Date</th>'; $fhtml .= '<th style="text-align:center;" id="order_date">Date</th>';
                                $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Birds</th>';
                                $nhtml .= '<th style="text-align:center;" >Weight</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Weight</th>';
                                $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                                $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                $nhtml .= '</tr>';
                                $fhtml .= '</tr>';
                                $html .= $fhtml;
                                $html .= '</thead>';
                                $html .= '<tbody class="tbody1">';

                                //$sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                                $usr_fltr = ""; if($users != "all"){ $usr_fltr = " AND `addedemp` = '$users'"; }
                                $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                                $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                                $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                                $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                            
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
                            //Mortality
                            $sql1 = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `ccode` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`code` ASC";
                            $query1 = mysqli_query($conn, $sql1); $iwm_bbds = $iwm_bqty = $iwm_bamt = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                $key1 = $row1['itemcode'];
                                if(strtotime($row1['date']) < strtotime($fdate)){ }
                                else{
                                    $key1 = $row1['itemcode'];
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

                               
                                $fto_jals[$row1['code']] += (float)$jals;
                                $fto_bds[$row1['code']] += (float)$birds;
                                $fto_twt[$row1['code']] += (float)$tweight;
                                $fto_ewt[$row1['code']] += (float)$eweight;
                                $fto_nwt[$row1['code']] += (float)$nweight;
                                $fto_amt[$row1['code']] += (float)$amount;

                                $iwso_bbds[$row1['itemcode']] += (float)$birds;
                                $iwso_bqty[$row1['itemcode']] += (float)$nweight;
                                $iwso_bamt[$row1['itemcode']] += (float)$amount;
                            }

									 
									 foreach($item_alist as $icode){
										// Closing
										$cls_bds = $birds = round((((float)$opn_bdss[$icode] + (float)$iwsi_bbds[$icode] + (float)$iwsa_abbds[$icode]) - ((float)$iwm_bbds[$icode] + (float)$iwso_bbds[$icode] + (float)$iwsa_dbbds[$icode])),2); if($birds == ""){ $birds = 0; }
                                         $cls_qty = $nweight = round((((float)$opn_qtyy[$icode] + (float)$iwsi_bqty[$icode] + (float)$iwsa_abqty[$icode]) - ((float)$iwm_bqty[$icode] + (float)$iwso_bqty[$icode] + (float)$iwsa_dbqty[$icode])),2); if($nweight == ""){ $nweight = 0; }
                                            $price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                            $amount = ((float)$price * (float)$nweight);
                                            //$amount = round(((float)$iwso_bamt[$icode] - ((float)$opn_amt[$icode] + (float)$iwsi_bamt[$icode] - (float)$iwm_bamt[$icode])),2); if($amount == ""){ $amount = 0; }
                                         $cls_amts = $cls_amt[$icode] += (float)$amount;

										// Stock Adjustment 
										$stk_bds = ((float)$iwsa_abbds[$icode] - (float)$iwsa_dbbds[$icode]); if($stk_bds == ""){ $stk_bds = 0; }
										$stk_qty = ((float)$iwsa_abqty[$icode] - (float)$iwsa_dbqty[$icode]); if($stk_qty == ""){ $stk_qty = 0; }
										$stk_amt = ((float)$iwsa_abamt[$icode] - (float)$iwsa_dbamt[$icode]); if($stk_amt == ""){ $stk_amt = 0; }
                                     
									
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$sl3++.'</td>';
                                    $html .= '<td style="text-align:left;">'.$dates_c.'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_bds).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_qty).'</td>';
                                    $html .= '<td style="text-align:right;"></td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($cls_amts).'</td>';
                                    
                                // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                    $html .= '</tr>';

                                     
                                    // Accumulate overall totals
                                
                                    $tclosedbirds += $cls_bds;
                                    $tquantity_stkcl += $cls_qty;
                                    
                                    $tamount_stkcl += $cls_amts;
                                      }
                                $html .= '</tbody>';
                                    // Add totals row
                                    $html .= '<thead class="tfoot4">';
                                    $html .= '<tr >';
                                    $html .= '<th colspan="2">Total</th>';
                                    // Output the overall total for all weeks
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tclosedbirds, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity_stkcl, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_stkcl, 2)) . '</th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';
                                    
                                
                                //old
                                $pdate = date('Y-m-d', strtotime($fdate.'-1 days'));
                                if($sectors == "all"){
                                    $sector_filter1 = "";
                                    $sector_filter2 = "";
                                    $sector_filter3 = "";
                                }
                                else{
                                    $sector_filter1 = " AND `warehouse` = '$sectors'";
                                    $sector_filter2 = " AND `towarehouse` = '$sectors'";
                                    $sector_filter3 = " AND `fromwarehouse` = '$sectors'";
                                }
                                
                                
                                $sql = "SELECT * FROM `pur_purchase` WHERE `date` ='$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                $query = mysqli_query($conn,$sql); $pur_bds = $pur_qty = $pur_prc = $pur_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $pur_bds += (float)$row['birds'];
                                    $pur_qty += (float)$row['netweight'];
                                    $pur_prc = (float)$row['itemprice'];
                                    $pur_amt += (float)$row['totalamt'];
                                    $dates_c = strtotime($row['date']);
                                }
                                
                                $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` ='$tdate' AND `code` IN ('$item_list')".$sector_filter2." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                $query = mysqli_query($conn,$sql); $tin_bds = $tin_qty = $tin_prc = $tin_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $tin_bds += (float)$row['birds'];
                                    $tin_qty += (float)$row['quantity'];
                                    $tin_prc = (float)$row['price'];
                                    $tin_amt += ((float)$row['quantity'] * (float)$row['price']);
                                    $dates_c = strtotime($row['date']);
                                }
                                
                                $sql = "SELECT * FROM `customer_sales` WHERE `date` ='$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                $query = mysqli_query($conn,$sql); $inv_bds = $inv_qty = $inv_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $inv_bds += (float)$row['birds'];
                                    $inv_qty += (float)$row['netweight'];
                                    $inv_amt += (float)$row['totalamt'];
                                    $dates_c = strtotime($row['date']);
                                }
                                $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` ='$tdate' AND `code` IN ('$item_list')".$sector_filter3." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                $query = mysqli_query($conn,$sql); $tou_bds = $tou_qty = $tou_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $tou_bds += (float)$row['birds'];
                                    $tou_qty += (float)$row['quantity'];
                                    $tou_amt += ((float)$row['quantity'] * (float)$row['price']);
                                    $dates_c = strtotime($row['date']);
                                }
                                
                                $sch_code = $ecoa_code = array();
                                $sql = "SELECT * FROM `acc_schedules` WHERE `subtype` LIKE 'COA-0003'"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $sch_code[$row['code']] = $row['code']; }

                                $sch_list = implode("','",$sch_code);
                                $sql = "SELECT * FROM `acc_coa` WHERE `schedules` IN ('$sch_list')"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $ecoa_code[$row['code']] = $row['code']; }

                                $coa_list = implode("','",$ecoa_code);
                                $sql = "SELECT * FROM `acc_vouchers` WHERE `date` ='$tdate' AND `prefix` ='PV' AND `tcoa` IN ('$coa_list')AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                $query = mysqli_query($conn,$sql); $pv_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $pv_amt += (float)$row['amount'];
                                }

                                $sql = "SELECT * FROM `acc_vouchers` WHERE `date` ='$tdate' AND `prefix` ='RV' AND `fcoa` IN ('$coa_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                $query = mysqli_query($conn,$sql); $rv_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $rv_amt += (float)$row['rvamt'];
                                }
                                $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$items')"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $iname = $row['description']; }

                                $tpur_bds = ((float)$pur_bds + (float)$tin_bds);
                                $tpur_qty = ((float)$pur_qty + (float)$tin_qty);
                                $tpur_amt = ((float)$pur_amt + (float)$tin_amt);
                                $tinv_bds = ((float)$inv_bds + (float)$tou_bds);
                                $tinv_qty = ((float)$inv_qty + (float)$tou_qty);
                                $tinv_amt = ((float)$inv_amt + (float)$tou_amt);

                                $acls_bds = (float)$cls_bds;
                                $acls_qty = (float)$cls_qty;
                                $acls_amt = (float)$cls_amt;
                                if((float)$cls_prc > 0){ $acls_prc = (float)$cls_prc; }
                                else if((float)$pur_prc > 0){ $acls_prc = (float)$pur_prc; }
                                else if((float)$opn_prc > 0){ $acls_prc = (float)$opn_prc; }
                                else if((float)$tin_prc > 0){ $acls_prc = (float)$tin_prc; }
                                else{ $acls_prc = 0; }

                                $tcls_bds = (((float)$opn_bds + (float)$tpur_bds) - (float)$tinv_bds);
                                $tcls_qty = (((float)$opn_qty + (float)$tpur_qty) - (float)$tinv_qty);
                                $tcls_amt = ((float)$tcls_qty * (float)$acls_prc);

                                $exp_amt = ((float)$pv_amt - (float)$rv_amt);

                                $wht_qty = ((float)$opn_qty + (float)$tpur_qty) - ((float)$tinv_qty + (float)$acls_qty);
                                $wht_amt = ((float)$wht_qty * (float)$acls_prc);

                                 $chkzero = (int)$tpur_qty + (int)$opn_qty;
                             
                                if( $chkzero != 0 && $chkzero > 0 ){ $wht_per = round((((float)$wht_qty / ((float)$tpur_qty + (float)$opn_qty)) * 100),2); } else{ $wht_per = 0; }

                               // if(((float)$tpur_qty + (float)$opn_qty) > 0 && ((float)$tpur_qty + (float)$opn_qty) != 0 ){ $wht_per = round((((float)$wht_qty / (float)$tpur_qty + (float)$opn_qty) * 100),2); } else{ $wht_per = 0; }

                                $mrg_amt = ((float)$tinv_amt + (float)$acls_amt) - ((float)$opn_amt + (float)$tpur_amt) - ((float)$exp_amt);

                                if((float)$tinv_qty != 0){ $sale_avg = round(((float)$tinv_amt / $tinv_qty),2); } else{ $sale_avg = 0; }
                                if((float)$tpur_qty != 0){ $pur_avg = round((((float)$tpur_amt + (float)$exp_amt) / $tpur_qty),2); } else{ $pur_avg = 0; }

                                if((float)$tinv_qty != 0){ $ppk_prc = round(((float)$mrg_amt / (float)$tinv_qty),2); } else{ $ppk_prc = 0; }

                                    
                                   

                                        // Closing Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" > Weight Loss</th>'; $fhtml .= '<th colspan="6" style="text-align:center;border: 2px solid black;" id="order"> Weight Loss</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;"></th>'; $fhtml .= '<th style="text-align:center; id="order"></th>';
                                        $nhtml .= '<th style="text-align:center;"></th>'; $fhtml .= '<th style="text-align:center;" id="order"></th>';
                                        $nhtml .= '<th style="text-align:center;" ></th>'; $fhtml .= '<th style="text-align:center;" id="order"></th>';
                                        $nhtml .= '<th style="text-align:center;" ></th>'; $fhtml .= '<th style="text-align:center;" id="order_num"></th>';
                                        $nhtml .= '<th style="text-align:center;" >Weight %</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Weight %</th>';
                                        $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';
                                        $html .= $fhtml;
                                        $html .= '</thead>';
                                        $html .= '<tbody class="tbody1">';

                                        //$sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                                        $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                                        $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                                        $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                                        $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                                
                                    // $sql = "SELECT * FROM `item_closingstock` WHERE `tdflag` = '0' AND `date` >= '$fdate' AND `date` <= '$tdate' AND `pdflag` = '0' AND `active` = '1' ORDER BY `date`,`trnum` ASC";
                                    // $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl3 = 1;
                                    // while($row = mysqli_fetch_assoc($query)){
                                    //     $date = date("d.m.Y",strtotime($row['date']));
                                    //     $addedtime = $row['addedtime'];
                                    //     $closedbirds = $row['closedbirds'];
                                    //     $iname = $item_name[$row['code']];
                                    //     $quantity = $row['closedquantity'];
                                    //     $price = $row['price'];
                                    //     $amount = $row['amount'];
                                    //     $remarks = $row['remarks'];
                                    //     //$addedemp = $row['addedemp'];
                                    //     $sname = $sector_name[$row['warehouse']];
                                    //     $uname = $user_name[$row['addedemp']];

                                        
                                        
                                    $slno++; 
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;"></td>';
                                    $html .= '<td style="text-align:left;"></td>';
                                    $html .= '<td style="text-align:left;"></td>';
                                    $html .= '<td style="text-align:left;"></td>';
                                    // $html .= '<td style="text-align:left;"></td>';
                                    // $html .= '<td style="text-align:left;"></td>';
                                    $html .= '<td style="text-align:left;">'.number_format_ind($wht_qty)." (".$wht_per."%)".'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($wht_amt).'</td>';
                                    
                                // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                    $html .= '</tr>';

                                    // Accumulate overall totals
                                    $tamount_pay += $wht_amt;
                                
                                $html .= '</tbody>';
                                    // Add totals row
                                    $html .= '<thead class="tfoot4">';
                                    $html .= '<tr >';
                                    $html .= '<th colspan="5">Total</th>';
                                    // Output the overall total for all weeks
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_pay, 2)) . '</th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';


                                echo $html;
                            }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
        function checkval() {
                
                var date =  document.getElementById("fdate").value;
                var l = true;
                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("fdate").focus();
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
            function fetch_careas(){
                var supervisors = document.getElementById("supervisors").value;
                removeAllOptions(document.getElementById("areas"));
                //if(supervisors == "" || supervisors == "select"){ } else{}
                var fetch_areas = new XMLHttpRequest();
                var method = "GET";
                var url = "chicken_fetch_customer_areas.php?supervisors="+supervisors+"&type=from_emp";
                //window.open(url);
                var asynchronous = true;
                fetch_areas.open(method, url, asynchronous);
                fetch_areas.send();
                fetch_areas.onreadystatechange = function(){
                    if (this.readyState == 4 && this.status == 200) {
                        var area_list = this.responseText;
                        $('#areas').append(area_list);
                    }
                }
            }
        </script>
         <script src="sort_table_columns.js"></script>
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
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
        <script src="../handle_ebtn_as_tbtn.js"></script>
	</body>
	
</html>
