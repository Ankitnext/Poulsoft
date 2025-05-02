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

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$fdate = date("Y-m-d"); $sectors = "all"; $fstyles = $fsizes = "default"; $types = "tr_date"; $exports = "display"; 
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d", strtotime($_POST['fdate']));
    $types = $_POST['types'];
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
                            
                            // Purchase Data 
                            $html .= '<thead class="thead2" id="head_names">';
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

                           
                            //$sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                            $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                            $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                            $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                            $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                          
                            $sql = "SELECT * FROM `pur_purchase` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$up_dfltr." AND `active` = '1' AND `pdflag` = '0' ORDER BY `date` ASC";
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
                                $html .= '<thead class="tfoot1">';
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


                                $html .= '<thead class="thead2" id="head_names">';
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

                                //$sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }
                                $tr_dfltr = ""; if($types == "tr_date"){ $tr_dfltr = " AND `date` = '$fdate'"; }
                                $et_dfltr = ""; if ($types == "ad_date") { $et_dfltr = " AND `addedtime` >= '$fdate 00:00:00' AND `addedtime` <= '$fdate 23:59:59'"; }
                                $up_dfltr = ""; if ($types == "up_date") { $up_dfltr = " AND `updated` >= '$fdate 00:00:00' AND `updated` <= '$fdate 23:59:59'"; }
                                $ups_dfltr = ""; if ($types == "up_date") { $ups_dfltr = " AND `updatedtime` >= '$fdate 00:00:00' AND `updatedtime` <= '$fdate 23:59:59'"; }
                            
                                $sql = "SELECT * FROM `customer_sales` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$up_dfltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date` ASC";
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
                                    $html .= '<thead class="tfoot1">';
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


                                 // Payments Data ------------------------------------------------------------------------------------------------------ 
                              
                                // $html = $nhtml = $fhtml = '';
                                $nhtml = $fhtml = ''; // CLEAR previous values


                                $html .= '<thead class="thead2" id="head_names">';
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
                                    $tamount += $amount;
                                }
                                $html .= '</tbody>';
                                    // Add totals row
                                    $html .= '<thead class="tfoot1">';
                                    $html .= '<tr >';
                                    $html .= '<th colspan="4">Total</th>';
                                    // Output the overall total for all weeks
                                    $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th style="text-align:right;"></th>';
                                    $html .= '<th  colspan="3" style="text-align:right;"></th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';

                                       // Receipt Data ------------------------------------------------------------------------------------------------------ 
                              
                                    // $html = $nhtml = $fhtml = '';
                                    $nhtml = $fhtml = ''; // CLEAR previous values


                                    $html .= '<thead class="thead2" id="head_names">';
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
                                        $tamount += $amount;
                                    }
                                    $html .= '</tbody>';
                                       // Add totals row
                                        $html .= '<thead class="tfoot1">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="4">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="3" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';
                                            
                                        // Cr Dr Notes Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead2" id="head_names">';
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
                                    
                                        $tamount += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot1">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="4">Total</th>';
                                        // Output the overall total for all weeks
                                          $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="5" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';

   
                                        // Stock Transfer Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead2" id="head_names">';
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
                                        $tquantity += $quantity;
                                        $tprice += $price;
                                        $tamount += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot1">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="3">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="5" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';
   
                                        // Mortality Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead2" id="head_names">';
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
                                        $tquantity += $quantity;
                                        $tprice += $price;
                                        $tamount += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot1">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="3">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="5" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';

                                        
                                        // Vouchers Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead2" id="head_names">';
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
                                      
                                        $tamount += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot1">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="6">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="4" style="text-align:right;"></th>';
                                        $html .= '</tr>';
                                        $html .= '</thead>';


                                        // Closing Data ------------------------------------------------------------------------------------------------------ 
                                
                                        // $html = $nhtml = $fhtml = '';
                                        $nhtml = $fhtml = ''; // CLEAR previous values


                                        $html .= '<thead class="thead2" id="head_names">';
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
                                
                                    $sql = "SELECT * FROM `item_closingstock` WHERE `tdflag` = '0' ".$tr_dfltr."".$et_dfltr."".$ups_dfltr." AND `pdflag` = '0' AND `active` = '1' ORDER BY `date`,`trnum` ASC";
                                    $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $date = date("d.m.Y",strtotime($row['date']));
                                        $addedtime = $row['addedtime'];
                                        $closedbirds = $row['closedbirds'];
                                        $iname = $item_name[$row['code']];
                                        $quantity = $row['closedquantity'];
                                        $price = $row['price'];
                                        $amount = $row['amount'];
                                        $remarks = $row['remarks'];
                                        //$addedemp = $row['addedemp'];
                                        $sname = $sector_name[$row['warehouse']];
                                        $uname = $user_name[$row['addedemp']];

                                        
                                        $slno++; 
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($closedbirds).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($quantity).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                        $html .= '<td style="text-align:left;">'.$uname.'</td>';
                                        $html .= '<td style="text-align:left;">'.$addedtime.'</td>';
                                        
                                    // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                                        $html .= '</tr>';

                                        
                                        // Accumulate overall totals
                                    
                                        $tclosedbirds += $closedbirds;
                                        $tquantity += $quantity;
                                        $tprice += $price;
                                        $tamount += $amount;
                                    }
                                    $html .= '</tbody>';
                                        // Add totals row
                                        $html .= '<thead class="tfoot1">';
                                        $html .= '<tr >';
                                        $html .= '<th colspan="2">Total</th>';
                                        // Output the overall total for all weeks
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tclosedbirds, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tquantity, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tprice, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;">' . number_format_ind(round($tamount, 2)) . '</th>';
                                        $html .= '<th style="text-align:right;"></th>';
                                        $html .= '<th  colspan="4" style="text-align:right;"></th>';
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
