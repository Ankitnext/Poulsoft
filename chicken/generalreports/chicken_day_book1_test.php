<?php
//chicken_day_book1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_day_book1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_day_book1.php?db=".$db;
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
$file_name = "CHICKEN DAY BOOK REPORT";

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

$fdate = date("Y-m-d"); $sectors = $users = $items = "all"; $fstyles = $fsizes = "default"; $types = "tr_date"; $exports = "display"; 
if(isset($_POST['submit']) == true){
    $tdate = $fdate = date("Y-m-d", strtotime($_POST['fdate']));
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
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($sectors == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($items == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="users">user</label>
                                            <select name="users" id="users" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($users == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($usr_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($users == $scode){ echo "selected"; } ?>><?php echo $usr_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:180px; margin-right: 5px;">
                                            <label for="types">Types</label>
                                            <select name="types" id="types" class="form-control select2" style="width:180px;" >
                                                <option value="tr_date" <?php if($types == "tr_date"){ echo "selected"; } ?>>-Transaction Date-</option>
                                                <option value="ad_date" <?php if($types == "ad_date"){ echo "selected"; } ?>>-Added Date-</option>
                                                <option value="up_date" <?php if($types == "up_date"){ echo "selected"; } ?>>-Updated Date-</option>
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
                            $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Opening Stock</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Opening Stock</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';

                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th style="text-align:center;">Stock Point</th>'; $fhtml .= '<th style="text-align:center; id="order">Stock Point</th>';
                            $nhtml .= '<th style="text-align:center;">Item</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item</th>';
                            $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                            $nhtml .= '<th style="text-align:center;" >Quantity</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                            $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                            $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                            $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                            $nhtml .= '<th colspan="4" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="11" style="text-align:center;" id="order">Entry Time</th>';
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

                            $sql1 = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwp_obds = $iwp_oqty = $iwp_oprc = $iwp_opprc = $iwp_oamt = $iwp_bqty = $iwp_bamt = $addedemps = $addedtimes = $snames = $inames = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['itemcode'];
                                    $iwp_obds[$key1] += (float)$row1['birds'];
                                    $iwp_oqty[$key1] += (float)$row1['netweight'];
                                    $iwp_oamt[$key1] += (float)$row1['totalamt'];
                                    $iwp_oprc[$key1] = (float)$row1['itemprice'];
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $inames[$key1] = $item_name[$row1['itemcode']];
                                    $snames[$key1] = $sector_name[$row1['warehouse']];
                                }
                                else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
                                if(strtotime($row1['date']) < strtotime($pdate)){
                                    $iwp_opprc[$key1] = (float)$row1['itemprice'];
                                }
                            }
                            $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate'  AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
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
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $inames[$key1] = $item_name[$row1['itemcode']];
                                    $snames[$key1] = $sector_name[$row1['warehouse']];
                                }
                                else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
                            }
                            $sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate'  AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
                            $query1 = mysqli_query($conn,$sql1); $iwi_oprc = array();
                            while($row1 = mysqli_fetch_assoc($query1)){
                                if(strtotime($row1['date']) < strtotime($fdate)){
                                    $key1 = $row1['code'];
                                    $iws_obds[$key1] += (float)$row1['birds'];
                                    $iws_oqty[$key1] += (float)$row1['quantity'];
                                    $iws_oamt[$key1] += ((float)$row1['quantity'] * (float)$row1['price']);
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
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
                                        $addedtimes[$key1] = $row1['addedtime'];
                                        $addedemps[$key1] = $row1['addedemp'];
                                        $inames[$key1] = $item_name[$row1['itemcode']];
                                        $snames[$key1] = $sector_name[$row1['warehouse']];
                                    }
                                    else if($row1['a_type'] == "deduct"){
                                        $iwsa_dobds[$key1] += (float)$row1['birds'];
                                        $iwsa_doqty[$key1] += (float)$row1['nweight'];
                                        $iwsa_doamt[$key1] += (float)$row1['amount'];
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
                                    $addedtimes[$key1] = $row1['addedtime'];
                                    $addedemps[$key1] = $row1['addedemp'];
                                    $inames[$key1] = $item_name[$row1['itemcode']];
                                    $snames[$key1] = $sector_name[$row1['ccode']];
                                }
                                else{ }
                            }

                            //Opening Calculations
                            $opn_bdss = $opn_qtyy = $opn_amts = array();
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

                               

                                // $birds = round((((float)$opn_bdss[$icode] + (float)$iwsi_bbds[$icode] + (float)$iwsa_abbds[$icode]) - ((float)$iwm_bbds[$icode] + (float)$iwso_bbds[$icode] + (float)$iwsa_dbbds[$icode])),2); if($birds == ""){ $birds = 0; }
                                // $nweight = round((((float)$opn_qtyy[$icode] + (float)$iwsi_bqty[$icode] + (float)$iwsa_abqty[$icode]) - ((float)$iwm_bqty[$icode] + (float)$iwso_bqty[$icode] + (float)$iwsa_dbqty[$icode])),2); if($nweight == ""){ $nweight = 0; }
                                // $price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
                                // $amount = ((float)$price * (float)$nweight);
                                // //$amount = round(((float)$iwso_bamt[$icode] - ((float)$opn_amt[$icode] + (float)$iwsi_bamt[$icode] - (float)$iwm_bamt[$icode])),2); if($amount == ""){ $amount = 0; }
                                // $cls_amt[$icode] += (float)$amount;
                            }
                                //$sname = $sector_name[$row['addedemp']];

                                
                                $slno++; 
                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_bds).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_prc).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_amt).'</td>';
                                $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                $html .= '<td colspan="4" style="text-align:left;">'.$addedtime.'</td>';
                                
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
                                $html .= '<th style="text-align:right;"></th>';
                                $html .= '<th colspan="4" style="text-align:right;"></th>';
                                $html .= '</tr>';
                                $html .= '</thead>';
                                
                              
                            // Purchase -----------------------------------------------------------------------------------------------------------    
                            $nhtml = $fhtml = '';
                            
                            // Purchase Data 
                            $html .= '<thead class="thead4" id="head_names">';
                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Purchase</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Purchase</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';

                            $nhtml .= '<tr >'; 
                            $fhtml .= '<tr >';
                            $nhtml .= '<th style="text-align:center;">Stock Point</th>'; $fhtml .= '<th style="text-align:center; id="order">Stock Point</th>';
                            $nhtml .= '<th style="text-align:center;">Item</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item</th>';
                            $nhtml .= '<th style="text-align:center;" >Jals</th>'; $fhtml .= '<th style="text-align:center;" id="order">Jals</th>';
                            $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                            $nhtml .= '<th style="text-align:center;" >T.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">T.wt</th>';
                            $nhtml .= '<th style="text-align:center;" >E.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">E.wt</th>';
                            $nhtml .= '<th style="text-align:center;" >N.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">N.wt</th>';
                            $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                            $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                            $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                            $nhtml .= '<th style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th style="text-align:center;" id="order">Entry Time</th>';
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
                          
                            $sql = "SELECT * FROM `pur_purchase` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$up_dfltr."".$itm_fltr."".$sec_fltr." AND `active` = '1' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
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
                                $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($jals).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($birds).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($totalweight).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($emptyweight).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($netweight).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                
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
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tjals, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tbirds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ttotalweight, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($temptyweight, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tnetweight, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                $html .= '<th style="text-align:right;"></th>';
                                $html .= '<th style="text-align:right;"></th>';
                                $html .= '</tr>';
                                $html .= '</thead>';


                                 // Sales Data ------------------------------------------------------------------------------------------------------ 
                              
                                // $html = $nhtml = $fhtml = '';
                                $nhtml = $fhtml = ''; // CLEAR previous values


                                $html .= '<thead class="thead4" id="head_names">';
                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Sales</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Sales</th>';
                                $nhtml .= '</tr>';
                                $fhtml .= '</tr>';

                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th style="text-align:center;">Stock Point</th>'; $fhtml .= '<th style="text-align:center; id="order">Stock Point</th>';
                                $nhtml .= '<th style="text-align:center;">Item</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item</th>';
                                $nhtml .= '<th style="text-align:center;" >Jals</th>'; $fhtml .= '<th style="text-align:center;" id="order">Jals</th>';
                                $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                                $nhtml .= '<th style="text-align:center;" >T.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">T.wt</th>';
                                $nhtml .= '<th style="text-align:center;" >E.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">E.wt</th>';
                                $nhtml .= '<th style="text-align:center;" >N.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">N.wt</th>';
                                $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                                $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                $nhtml .= '<th style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th style="text-align:center;" id="order">Entry Time</th>';
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
                            
                                $sql = "SELECT * FROM `customer_sales` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$up_dfltr."".$itm_fltr."".$sec_fltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date` ASC";
                                $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $date = date("d.m.Y",strtotime($row['date']));
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

                                    
                                    $slno++; 
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                    $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($jals).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($birds).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($totalweight).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($emptyweight).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($netweight).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                    $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                    $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                    
                                // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                    $html .= '</tr>';

                                    
                                    // Accumulate overall totals
                                
                                    $tjals_sales += $jals;
                                    $tbirds_sales += $birds;
                                    $ttotalweight_sales += $totalweight;
                                    $temptyweight_sales += $emptyweight;
                                    $tnetweight_sales += $netweight;
                                    $tquantity_sales += $quantity;
                                    $tprice_sales += $price;
                                    $tamount_sales += $amount;
                                }
                                $html .= '</tbody>';
                                    // Add totals row
                                    $html .= '<thead class="tfoot4">';
                                    $html .= '<tr >';
                                    $html .= '<th colspan="2">Total</th>';
                                    // Output the overall total for all weeks
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tjals_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tbirds_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($ttotalweight_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($temptyweight_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tnetweight_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_sales, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';


                                 // Payments Data ------------------------------------------------------------------------------------------------------ 
                              
                                // $html = $nhtml = $fhtml = '';
                                $nhtml = $fhtml = ''; // CLEAR previous values


                                $html .= '<thead class="thead4" id="head_names">';
                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Payments</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Payments</th>';
                                $nhtml .= '</tr>';
                                $fhtml .= '</tr>';

                                $nhtml .= '<tr >'; 
                                $fhtml .= '<tr >';
                                $nhtml .= '<th style="text-align:center;">Supplier Name</th>'; $fhtml .= '<th style="text-align:center; id="order">Supplier Name</th>';
                                $nhtml .= '<th style="text-align:center;">Voucher No.</th>'; $fhtml .= '<th style="text-align:center;" id="order">Voucher No.</th>';
                                $nhtml .= '<th style="text-align:center;" >Payment Mode</th>'; $fhtml .= '<th style="text-align:center;" id="order">Payment Mode</th>';
                                $nhtml .= '<th style="text-align:center;" >Paid Account</th>'; $fhtml .= '<th style="text-align:center;" id="order">Paid Account</th>';
                                $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                $nhtml .= '<th style="text-align:center;" >Remarks</th>'; $fhtml .= '<th style="text-align:center;" id="order">Remarks</th>';
                                $nhtml .= '<th style="text-align:center;" >Sector</th>'; $fhtml .= '<th style="text-align:center;" id="order">Sector</th>';
                                $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                $nhtml .= '<th colspan="3" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="3" style="text-align:center;" id="order">Entry Time</th>';
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
                            
                                $sql = "SELECT * FROM `pur_payments` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date` ASC";
                                $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                while($row = mysqli_fetch_assoc($query)){
                                    $date = date("d.m.Y",strtotime($row['date']));
                                    $addedtime = $row['addedtime'];
                                    $mode = $row['mode'];
                                    $method = $row['method'];
                                  
                                    $amount = $row['amount'];
                                    $remarks = $row['remarks'];
                                    
                                  
                                    $addedemp = $row['addedemp'];
                                    $sname = $sector_name[$row['warehouse']];
                                    $docno = $row['docno'];
                                    $vname = $ven_name[$row['ccode']];

                                    
                                    $slno++; 
                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:left;">'.$vname.'</td>';
                                    $html .= '<td style="text-align:left;">'.$docno.'</td>';
                                    $html .= '<td style="text-align:left;">'.$mode.'</td>';
                                    $html .= '<td style="text-align:left;">'.$method.'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                    $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                                    $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                    $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                    $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                    
                                // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                    $html .= '</tr>';

                                    // Accumulate overall totals
                                    $tamount_pay += $amount;
                                }
                                $html .= '</tbody>';
                                    // Add totals row
                                    $html .= '<thead class="tfoot4">';
                                    $html .= '<tr >';
                                    $html .= '<th colspan="4">Total</th>';
                                    // Output the overall total for all weeks
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_pay, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th  colspan="3" style="text-align:right;"></th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';

                                       // Receipt Data ------------------------------------------------------------------------------------------------------ 
                              
                                    // $html = $nhtml = $fhtml = '';
                                    $nhtml = $fhtml = ''; // CLEAR previous values


                                    $html .= '<thead class="thead4" id="head_names">';
                                    $nhtml .= '<tr >'; 
                                    $fhtml .= '<tr >';
                                    $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Receipt</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Receipt</th>';
                                    $nhtml .= '</tr>';
                                    $fhtml .= '</tr>';

                                    $nhtml .= '<tr >'; 
                                    $fhtml .= '<tr >';
                                    $nhtml .= '<th style="text-align:center;">Supplier Name</th>'; $fhtml .= '<th style="text-align:center; id="order">Supplier Name</th>';
                                    $nhtml .= '<th style="text-align:center;">Voucher No.</th>'; $fhtml .= '<th style="text-align:center;" id="order">Voucher No.</th>';
                                    $nhtml .= '<th style="text-align:center;" >Payment Mode</th>'; $fhtml .= '<th style="text-align:center;" id="order">Payment Mode</th>';
                                    $nhtml .= '<th style="text-align:center;" >Paid Account</th>'; $fhtml .= '<th style="text-align:center;" id="order">Paid Account</th>';
                                    $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                    $nhtml .= '<th style="text-align:center;" >Remarks</th>'; $fhtml .= '<th style="text-align:center;" id="order">Remarks</th>';
                                    $nhtml .= '<th style="text-align:center;" >Sector</th>'; $fhtml .= '<th style="text-align:center;" id="order">Sector</th>';
                                    $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                    $nhtml .= '<th colspan="3" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="3" style="text-align:center;" id="order">Entry Time</th>';
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
                                
                                    $sql = "SELECT * FROM `customer_receipts` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $date = date("d.m.Y",strtotime($row['date']));
                                        $addedtime = $row['addedtime'];
                                        $mode = $row['mode'];
                                        $method = $row['method'];
                                        $amount = $row['amount'];
                                        $remarks = $row['remarks'];
                                        $addedemp = $ven_name[$row['addedemp']];
                                        $sname = $sector_name[$row['warehouse']];
                                        $docno = $row['trnum'];
                                        $vname = $ven_name[$row['ccode']];

                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$vname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$docno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$mode.'</td>';
                                        $html .= '<td style="text-align:left;">'.$method.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                                        $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                        
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        // Accumulate overall totals
                                        $tamount_res += $amount;
                                    }
                                    $html .= '</tbody>';
                                       // Add totals row
                                        $html .= '<thead class="tfoot4">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="4">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_res, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="3" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';
                                            
                                        // Cr Dr Notes Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Cr-Dr Notes/ Expenses / Journal</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Cr-Dr Notes/ Expenses / Journal</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">Paid To</th>'; $fhtml .= '<th style="text-align:center; id="order">Paid To</th>';
                                        $nhtml .= '<th style="text-align:center;">Voucher No.</th>'; $fhtml .= '<th style="text-align:center;" id="order">Voucher No.</th>';
                                        $nhtml .= '<th style="text-align:center;" >Payment Mode</th>'; $fhtml .= '<th style="text-align:center;" id="order">Payment Mode</th>';
                                        $nhtml .= '<th style="text-align:center;" >Paid Account</th>'; $fhtml .= '<th style="text-align:center;" id="order">Paid Account</th>';
                                        $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                        $nhtml .= '<th style="text-align:center;" >Remarks</th>'; $fhtml .= '<th style="text-align:center;" id="order">Remarks</th>';
                                        $nhtml .= '<th style="text-align:center;" >Sector</th>'; $fhtml .= '<th style="text-align:center;" id="order">Sector</th>';
                                        $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                        $nhtml .= '<th colspan="3" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="3" style="text-align:center;" id="order">Entry Time</th>';
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
                                
                                    $sql = "SELECT * FROM `main_crdrnote` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date`,`trnum` ASC";
                                    $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $date = date("d.m.Y",strtotime($row['date']));
                                        $addedtime = $row['addedtime'];
                                        $mode = $row['mode'];
                                        $method = $row['method'];
                                        $amount = $row['amount'];
                                        $remarks = $row['remarks'];
                                        $coa = $row['coa'];
                                        $addedemp = $ven_name[$row['addedemp']];
                                        $sname = $sector_name[$row['warehouse']];
                                        $docno = $row['trnum'];
                                        $vname = $ven_name[$row['ccode']];

                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$vname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$docno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$mode.'</td>';
                                        $html .= '<td style="text-align:left;">'.$coa.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                                        $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                   
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        
                                        // Accumulate overall totals
                                    
                                        $tamount_crdr += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot4">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="4">Total</th>';
                                        // Output the overall total for all weeks
                                          $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_crdr, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="5" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';

   
                                        // Stock Transfer Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Stock Transfer</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Stock Transfer</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">From Sector</th>'; $fhtml .= '<th style="text-align:center; id="order">From Sector</th>';
                                        $nhtml .= '<th style="text-align:center;">Item Name</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item Name</th>';
                                        $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                                        $nhtml .= '<th style="text-align:center;" >Quantity</th>'; $fhtml .= '<th style="text-align:center;" id="order">Quantity</th>';
                                        $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order">Rate</th>';
                                        $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                        $nhtml .= '<th style="text-align:center;" >To Sector</th>'; $fhtml .= '<th style="text-align:center;" id="order">To Sector</th>';
                                        $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                        $nhtml .= '<th colspan="3" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="3" style="text-align:center;" id="order">Entry Time</th>';
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
                                
                                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date`,`trnum` ASC";
                                    $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $date = date("d.m.Y",strtotime($row['date']));
                                        $addedtime = $row['addedtime'];
                                        $birds = $row['birds'];
                                        $quantity = $row['quantity'];
                                        $price = $row['price'];
                                        $coa = $row['coa'];
                                        $addedemp = $ven_name[$row['addedemp']];
                                        $fware = $sector_name[$row['fromwarehouse']];
                                        $tware = $sector_name[$row['towarehouse']];
                                        $iname = $item_name[$row['code']];
                                        $docno = $row['trnum'];
                                        $vname = $ven_name[$row['ccode']];
                                        
                                        $amount = (float)$quantity * (float)$price;
                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$fware.'</td>';
                                        $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$birds.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($quantity).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        $html .= '<td style="text-align:left;">'.$tware.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                   
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        // Accumulate overall totals
                                        $tquantity_stk += $quantity;
                                        $tprice_stk += $price;
                                        $tamount_stk += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot4">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="3">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity_stk, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice_stk, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_stk, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="5" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';
   
                                        // Mortality Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Mortality</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Mortality</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">Sector</th>'; $fhtml .= '<th style="text-align:center; id="order">Sector</th>';
                                        $nhtml .= '<th style="text-align:center;">Item Name</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item Name</th>';
                                        $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                                        $nhtml .= '<th style="text-align:center;" >Quantity</th>'; $fhtml .= '<th style="text-align:center;" id="order">Quantity</th>';
                                        $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order">Rate</th>';
                                        $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                        $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                        $nhtml .= '<th colspan="4" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="4" style="text-align:center;" id="order">Entry Time</th>';
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
                                
                                    $sql = "SELECT * FROM `main_mortality` WHERE `dflag` = '0' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr." AND `active` = '1' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $date = date("d.m.Y",strtotime($row['date']));
                                        $addedtime = $row['addedtime'];
                                        $birds = $row['birds'];
                                        $quantity = $row['quantity'];
                                        $amount = $row['amount'];
                                        $price = $row['price'];
                                        $coa = $row['coa'];
                                        $addedemp = $ven_name[$row['addedemp']];
                                        $ware = $sector_name[$row['warehouse']];
                                        //$tware = $sector_name[$row['towarehouse']];
                                        $iname = $item_name[$row['itemcode']];
                                        $docno = $row['trnum'];
                                        $vname = $ven_name[$row['ccode']];
                                        
                                       // $amount = (float)$quantity * (float)$price;
                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$ware.'</td>';
                                        $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$birds.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($quantity).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                   
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        // Accumulate overall totals
                                        $tquantity_mort += $quantity;
                                        $tprice_mort += $price;
                                        $tamount_mort += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot4">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="3">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity_mort, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice_mort, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_mort, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="5" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';

                                        
                                        // Vouchers Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Vouchers</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Vouchers</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">Stock Point</th>'; $fhtml .= '<th style="text-align:center; id="order">Stock Point</th>';
                                        $nhtml .= '<th style="text-align:center;">Item</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item</th>';
                                        $nhtml .= '<th style="text-align:center;">Jals</th>'; $fhtml .= '<th style="text-align:center;" id="order">Jals</th>';
                                        $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                                        $nhtml .= '<th style="text-align:center;" >T.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">T.wt</th>';
                                        $nhtml .= '<th style="text-align:center;" >E.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">E.wt</th>';
                                        $nhtml .= '<th style="text-align:center;" >N.wt</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">N.wt</th>';                                        
                                        $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order">Rate</th>';
                                        $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                        $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                        $nhtml .= '<th style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th style="text-align:center;" id="order">Entry Time</th>';
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
                                
                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `active` = '1' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr."  ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $date = date("d.m.Y",strtotime($row['date']));
                                        $addedtime = $row['addedtime'];
                                      
                                        $amount = $row['amount'];
                                       
                                       // $coa = $row['coa'];
                                        $addedemp = $ven_name[$row['addedemp']];
                                        $ware = $sector_name[$row['warehouse']];
                                       
                                        $docno = $row['trnum'];
                                        $vname = $ven_name[$row['ccode']];
                                        
                                       // $amount = (float)$quantity * (float)$price;
                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$ware.'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;"></td>';
                                        $html .= '<td style="text-align:right;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedemp.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                   
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        // Accumulate overall totals
                                      
                                        $tamount_voc += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot4">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="6">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount_voc, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="4" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';


                                        // Closing Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >Stock Adjustment / Closing Stock</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">Stock Adjustment / Closing Stock</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">Sector</th>'; $fhtml .= '<th style="text-align:center; id="order">Sector</th>';
                                        $nhtml .= '<th style="text-align:center;">Item Name</th>'; $fhtml .= '<th style="text-align:center;" id="order">Item Name</th>';
                                        $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order">Birds</th>';
                                        $nhtml .= '<th style="text-align:center;" >Quantity</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                                        $nhtml .= '<th style="text-align:center;" >Rate</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Rate</th>';
                                        $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                                        $nhtml .= '<th style="text-align:center;" >User</th>'; $fhtml .= '<th style="text-align:center;" id="order">User</th>';
                                        $nhtml .= '<th colspan="4" style="text-align:center;" >Entry Time</th>'; $fhtml .= '<th colspan="4" style="text-align:center;" id="order">Entry Time</th>';
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
                                                 
                                                

                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cls_bds).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cls_qty).'</td>';
                                        $html .= '<td style="text-align:right;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cls_amts).'</td>';
                                        $html .= '<td style="text-align:left;">'.$uname.'</td>';
                                        $html .= '<td colspan="4" style="text-align:left;">'.$addedtime.'</td>';
                                        
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        
                                        // Accumulate overall totals
                                    
                                        $tclosedbirds += $cls_bds;
                                        $tquantity_stkcl += $cls_qty;
                                        //$tprice_stkcl += $price;
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
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="4" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';


                                         $nhtml = $fhtml = ''; // CLEAR previous values

                                         // got bank and cash coa codes in different array
                                         $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1'";
                                         $query = mysqli_query($conn,$sql);$bankcoa = $cashcoa = $cba = array();
                                         while($row = mysqli_fetch_assoc($query)){
                                            if($row['ctype'] == 'Cash'){ $cashcoa[$row['code']] = $row['code']; $cashcoa_name[$row['code']] = $row['description']; }
                                            if($row['ctype'] == 'Bank'){ $bankcoa[$row['code']] = $row['code']; $bankcoa_name[$row['code']] = $row['description']; }
                                            $cba[$row['code']] = $row['code'];
                                         }

                                         // bank 
                                         $acoa = implode("','",$cba);
                                         // receipt
                                         $sql = "SELECT * FROM `customer_receipts` WHERE `method` IN ('$acoa') AND `date` <= '$fdate' AND `active` = '1' AND `pdflag` = '0' AND `tdflag` = '0'";
                                         $query = mysqli_query($conn,$sql);$op_rct = $bw_rct = array();
                                         while($row = mysqli_fetch_assoc($query)){
                                            $key = $row['method'];
                                            if(strtotime($row['date']) < strtotime($fdate)){
                                                $op_rct[$key] =  $op_rct[$key] + (float)$row['amount'];
                                            }else{
                                                $bw_rct[$key] = $bw_rct[$key] + (float)$row['amount'];
                                            }
                                         }

                                         //pur_payments
                                         $sql = "SELECT * FROM `pur_payments` WHERE `method` IN ('$acoa') AND `date` <= '$fdate' AND `active` = '1'";
                                         $query = mysqli_query($conn,$sql); $op_pur = $bw_pur = array();
                                         while($row = mysqli_fetch_assoc($query)){
                                            $key = $row['method'];
                                            if(strtotime($row['date']) < strtotime($fdate)){
                                                $op_pur[$key] = $op_pur[$key] + (float)$row['amount'];

                                            }else{
                                                $bw_pur[$key] = $bw_pur[$key] + (float)$row['amount'];
                                            }
                                         }

                                         //crdr
                                         $sql = "SELECT * FROM `main_crdrnote` WHERE `coa` IN ('$acoa') AND `date` <= '$fdate' AND `active` = '1'";
                                         $query = mysqli_query($conn,$sql);$op_crdr = $bw_crdr = $opcr = $opdr = $bw_cr = $bw_dr = array();
                                         while($row = mysqli_fetch_assoc($query)){
                                            $key = $row['coa'];
                                            if(strtotime($row['date']) < strtotime($fdate)){
                                               if($row['crdr'] == 'Cr'){ $opcr[$key] = $opcr[$key] + (float)$row['amount']; }
                                               if($row['crdr'] == 'Dr'){ $opdr[$key] = $opdr[$key] + (float)$row['amount']; }
                                            }else{
                                                if($row['crdr'] == 'Cr'){ $bw_cr[$key] + (float)$row['amount']; }
                                                if($row['crdr'] == 'Dr'){ $bw_dr[$key] + (float)$row['amount']; }
                                            }
                                         }
                                         // dr- in cr -out
                                         //vouchers cr
                                         $sql = "SELECT * FROM `acc_vouchers` WHERE `fcoa` IN ('$acoa') AND `date` <= '$fdate' AND `active` = '1'";
                                         $query = mysqli_query($conn,$sql); $op_vcr = $bw_vcr = array(); //fcoa -> cr
                                         while($row = mysqli_fetch_assoc($query)){
                                            $key = $row['fcoa'];
                                            if(strtotime($row['date']) < strtotime($fdate)){
                                                $op_vcr[$key] = $op_vcr[$key] + (float)$row['amount'];

                                            }else{
                                                $bw_vcr[$key] = $bw_vcr[$key] + (float)$row['amount'];
                                            }
                                            
                                         }
                                        // voucher dr
                                          $sql = "SELECT * FROM `acc_vouchers` WHERE `tcoa` IN ('$acoa') AND `date` <= '$fdate' AND `active` = '1'";
                                         $query = mysqli_query($conn,$sql); $op_vdr = $bw_vdr = array(); //fcoa -> cr
                                         while($row = mysqli_fetch_assoc($query)){
                                            $key = $row['tcoa'];
                                            if(strtotime($row['date']) < strtotime($fdate)){
                                                $op_vdr[$key] = $op_vdr[$key] + (float)$row['amount'];

                                            }else{
                                                $bw_vdr[$key] = $bw_vdr[$key] + (float)$row['amount'];
                                            }
                                            
                                         }

                                         $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >BANK</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">BANK</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">Bank Name</th>'; $fhtml .= '<th style="text-align:center; id="order">Bank Name</th>';
                                        $nhtml .= '<th style="text-align:center;">Opening Balance</th>'; $fhtml .= '<th style="text-align:center;" id="order">Opening Balance</th>';
                                        $nhtml .= '<th style="text-align:center;" >Payments</th>'; $fhtml .= '<th style="text-align:center;" id="order">Payments</th>';
                                        $nhtml .= '<th style="text-align:center;" >Receipts</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Receipts</th>';
                                        $nhtml .= '<th style="text-align:center;" >Bank Charges</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Bank Charges</th>';
                                        $nhtml .= '<th colspan="6" style="text-align:center;" >Closing Balance</th>'; $fhtml .= '<th colspan="6" style="text-align:center;" id="order_num">Closing Balance</th>';
                                       
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';
                                        $html .= $fhtml;
                                        $html .= '</thead>';

                                        foreach($bankcoa as $bnk){

                                            $opening = ($op_rct[$bnk] + $opdr[$bnk] + $op_vdr[$bnk]) - ($op_pur[$bnk] + $opcr[$bnk] + $op_vdr[$bnk]);
                                            $closing = $opening + $bw_rct[$bnk] - $bw_pur[$bnk];

                                            $html .= '<tr>';
                                            $html .= '<td style="text-align:left;">'.$bankcoa_name[$bnk].'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($opening).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($bw_pur[$bnk]).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($bw_rct[$bnk]).'</td>';
                                            $html .= '<td style="text-align:right;">0</td>';
                                            $html .= '<td colspan="6" style="text-align:right;">'.number_format_ind($closing).'</td>';
                                            $html .= '</tr>';
     
                                        }

                                        $nhtml = $fhtml = ''; // CLEAR previous values

                                        $html .= '<thead class="thead4" id="head_names">';
                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" >CASH</th>'; $fhtml .= '<th colspan="11" style="text-align:center;border: 2px solid black;" id="order">CASH</th>';
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';

                                        $nhtml .= '<tr >'; 
                                        $fhtml .= '<tr >';
                                        $nhtml .= '<th style="text-align:center;">Bank Name</th>'; $fhtml .= '<th style="text-align:center; id="order">Bank Name</th>';
                                        $nhtml .= '<th style="text-align:center;">Opening Balance</th>'; $fhtml .= '<th style="text-align:center;" id="order">Opening Balance</th>';
                                        $nhtml .= '<th style="text-align:center;" >Payments</th>'; $fhtml .= '<th style="text-align:center;" id="order">Payments</th>';
                                        $nhtml .= '<th style="text-align:center;" >Receipts</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Receipts</th>';
                                        $nhtml .= '<th style="text-align:center;" >Bank Charges</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Bank Charges</th>';
                                        $nhtml .= '<th colspan="6" style="text-align:center;" >Closing Balance</th>'; $fhtml .= '<th colspan="6" style="text-align:center;" id="order_num">Closing Balance</th>';
                                       
                                        $nhtml .= '</tr>';
                                        $fhtml .= '</tr>';
                                        $html .= $fhtml;
                                        $html .= '</thead>';

                                        foreach($cashcoa as $bnk){

                                            $opening = ($op_rct[$bnk] + $opdr[$bnk] + $op_vdr[$bnk]) - ($op_pur[$bnk] + $opcr[$bnk] + $op_vdr[$bnk]);
                                            $closing = $opening + $bw_rct[$bnk] - $bw_pur[$bnk];

                                            $html .= '<tr>';
                                            $html .= '<td style="text-align:left;">'.$cashcoa_name[$bnk].'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($opening).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($bw_pur[$bnk]).'</td>';
                                            $html .= '<td style="text-align:right;">'.number_format_ind($bw_rct[$bnk]).'</td>';
                                            $html .= '<td style="text-align:right;">0</td>';
                                            $html .= '<td colspan="6" style="text-align:right;">'.number_format_ind($closing).'</td>';
                                            $html .= '</tr>';      
                                        }
        
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
