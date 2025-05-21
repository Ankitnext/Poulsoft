<?php
//item_WeightLossWCC2_ta.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();

include "../broiler_check_tableavailability.php";
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "item_WeightLossWCC2_ta.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "item_WeightLossWCC2_ta.php?db=".$db;
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
$file_name = "Weight Loss with Chicken Conversion Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("font_style_master", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.font_style_master LIKE poulso6_admin_chickenmaster.font_style_master;"; mysqli_query($conn,$sql1); }

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

// if($count53 > 0){
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $ifwlmba = $row['wlmba']; }
// }

$today = date("Y-m-d");
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $itemcodes[$row['code']] = $row['code']; $itemnames[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `inv_sectors` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $officecode[$row['code']] = $row['code']; $officename[$row['code']] = $row['description']; }
$fromdate = $_POST['fromdate']; if($fromdate == ""){ $fromdate = $today; } else { $fromdate = $_POST['fromdate']; }
$todate = $_POST['todate']; if($todate == ""){ $todate = $today; } else { $todate = $_POST['todate']; }

$icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%milk%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
//echo $icats;
 $sql = "SELECT * FROM `item_details` WHERE `category` IN ($icats)"; $query = mysqli_query($conn,$sql); $idetail = $idetail_name = array();
while($row = mysqli_fetch_assoc($query)){  $idetail[$row['code']] = $row['code'];  $idetail_name[$row['code']] = $row['description']; } 

$fdate = $tdate = date("Y-m-d"); $items = $wname = $sectors = $idetails =  "all"; $no_weeks = "12"; $fstyles = $fsizes = "default"; $exports = "display"; $area_acode = $item_acode = array(); $area_acode["all"] = "all"; $item_acode["all"] = "all"; $area_fltr = $item_fltr = ""; $aa_flag = $it_flag = 0;
if(isset($_POST['submit']) == true){
   

    $cust = $_POST['cust'];
    $sectors = $_POST['sectors'];
  
    $wname = $_POST['sectors'];
    $idetail = $_POST['iname'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
    
    if($idetail == "all") { $idetails = $iftdetails = ""; } else if($idetail == "") { $idetails = $iftdetails = ""; } else { $idetails = " AND `itemcode` = '$idetail'"; $iftdetails = " AND `code` LIKE '$idetail'"; }
    if($wname == "") { $wnames = $wfnames = $wtnames = ""; } else if($wname == "all") { $wnames = $wfnames = $wtnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; $wfnames = " AND `fromwarehouse` LIKE '$wname'"; $wtnames = " AND `towarehouse` LIKE '$wname'"; }
}
// if(isset($_POST['submit']) == true) { $wname = $_POST['wname']; } else { $wname = "select"; } if($wname == "select") { $wnames = $wfnames = $wtnames = ""; } else if($wname == "all") { $wnames = $wfnames = $wtnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; $wfnames = " AND `fromwarehouse` LIKE '$wname'"; $wtnames = " AND `towarehouse` LIKE '$wname'"; }
// if(isset($_POST['submit']) == true) { $idetail = $_POST['iname']; } else { $idetail == "all"; } if($idetail == "all") { $idetails = $iftdetails = ""; } else if($idetail == "") { $idetails = $iftdetails = ""; } else { $idetails = " AND `itemcode` = '$idetail'"; $iftdetails = " AND `code` LIKE '$idetail'"; }

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
                                            <input type="text" name="fromdate" id="datepickers1" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="todate" id="datepickers" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($todate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                       
                                        <div class="form-group" style="width:190px;">
                                        <?php if($ifwlmba == 1){ ?>		
											<label class="reportselectionlabel">Item Description</label>&nbsp;
											<select name="iname" id="iname" class="form-control select2">
												<option value="all">-All-</option>
												<?php
													$icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
													$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);
													while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }

													$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%milk%'"; $query = mysqli_query($conn,$sql);
													while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
													//echo $icats;
													$sql = "SELECT * FROM `item_details` WHERE `category` IN ($icats)"; $query = mysqli_query($conn,$sql);
													while($row = mysqli_fetch_assoc($query)){
												?>
														<option <?php if($idetail == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
												<?php
													}
												?>
											</select>&ensp;&ensp;
										<?php } ?>		
                                        </div>
                                        
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>All</option>
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

                            $html .= '<thead class="thead2" id="head_names">';
                            $nhtml .= '<tr>';
                            $fhtml .= '<tr>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;">Date</th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_date">Date</th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Items</th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Items</th>';
                            $nhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Opening</th>'; $fhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Opening</th>';
                            $nhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Purchases/Transfer IN</th>'; $fhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Purchases/Transfer IN</th>';
                            $nhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Sales/Transfer OUT</th>'; $fhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Sales/Transfer OUT</th>';
                            $nhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Mortality</th>'; $fhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Mortality</th>';
                            $nhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Closing</th>'; $fhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Closing</th>';
                            $nhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Actual Closing</th>'; $fhtml .= '<th colspan="3" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Actual Closing</th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Weight Loss %</th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Weight Loss %</th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Spent(Expense)</th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Spent(Expense)</th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" >Margin</th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Margin</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';

                            $nhtml .= '<tr>';
                            $fhtml .= '<tr>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;"></th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_date"></th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" ></th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num"></th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Birds</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Birds</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Quantity</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Quantity</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Birds</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Birds</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Quantity</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Quantity</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Birds</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Birds</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Quantity</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Quantity</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Birds</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Birds</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Quantity</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Quantity</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Birds</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Birds</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Quantity</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Quantity</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Birds</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order">Birds</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Quantity</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Quantity</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Percentage(qty)</th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Percentage(qty)</th>';
                            $nhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" >Amount</th>'; $fhtml .= '<th  style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order_num">Amount</th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" ></th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order"></th>';
                            $nhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" ></th>'; $fhtml .= '<th colspan="2" style="text-align:center;font-weight:bold;background-color: #98fb98;" id="order"></th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';
                            $html .= $fhtml;
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';

                            $icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
										
                            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }

                            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%milk%'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
                            //echo $icats;
                            $seq = "SELECT * FROM `item_details` WHERE `category` IN ($icats)"; $sql = $seq."".$iftdetails; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ if($icode == ""){ $icode = "'".$row['code']."'"; } else { $icode = $icode.",'".$row['code']."'"; } $itmcode[$row['code']] = $row['code']; $itmname[$row['code']] = $row['description']; }
                            //echo $icode;
                            $totitems  = sizeof($itmcode);
                            $fromdate = date("Y-m-d",strtotime($fromdate)); $todate = date("Y-m-d",strtotime($todate));
                            $d = date("d",strtotime($fromdate)); $m = date("m",strtotime($fromdate)); $y = date("Y",strtotime($fromdate));
                            if($d == 1) {
                                if($m == 1){
                                    $y = $y - 1;
                                    $m = 12;
                                    $dd = $y."-".$m."-03";
                                    $d = date("t",strtotime(date("Y.m.t",strtotime($dd))));
                                }
                                else {
                                    $m = $m - 1;
                                }
                            }
                            else {
                                $d = $d - 1;
                            }
                            $pdate = $y."-".$m."-".$d;
                            
                            $fdate = strtotime($fromdate);
                            $tdate = strtotime($todate);
                            
                            for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) { $store = date('Y-m-d', $currentDate); foreach($itmcode as $ic){ $mcode = $store."@".$itmcode[$ic]; $mainfilter[$mcode] = $mcode; } }
                            //Quantity Conversion Ratio
                            $con_qty = array();
                            if($count24 > 0){
                                $sql = "SELECT * FROM `item_qty_conversion` WHERE `active` = '1' ORDER BY `id` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $con_qty[$row['itemcode']] = $row['con_qty']; }
                            }
                            //Items based on chicken
                            $cicats = $ckn_codes = "";
                            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE 'Chicken'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $cicats = $row['code']; }
                            $sql = "SELECT * FROM `item_details` WHERE `category` LIKE '$cicats'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ if($ckn_codes == ""){ $ckn_codes = $row['code']; } else { $ckn_codes = $ckn_codes."','".$row['code']; } }
                            
                            $ckn_idetails = " AND `itemcode` IN ('$ckn_codes')";
                            
                            $seq = "SELECT * FROM `item_closingstock` WHERE `date` >='$pdate' AND `date` <= '$todate'";
                            $groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `code` ASC";
                            $sql = $seq."".$iftdetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date']."@".$row['code'];
                                $opn_birds[$obcode] += (float)$row['closedbirds'];
                                $openingdetails[$obcode] = $openingdetails[$obcode] + $row['closedquantity'];
                                $openingdetailsp[$obcode] = $row['price'];
                                $openingdetailsa[$obcode] = $openingdetailsa[$obcode] + ($row['price'] * $row['closedquantity']);
                                $item_count[$obcode] = $item_count[$obcode] + 1;
                            }
                            //$old_count = "";
                            $seq = "SELECT * FROM `item_closingstock` WHERE `date` >='$pdate' AND `date` <= '$todate' AND `code` IN ($icode)";
                            $groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $sql = $seq."".$iftdetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if($old_count[$row['date']."@".$row['code']] != $row['date']."@".$row['code']){
                                    $obcode = $row['date'];
                                    $openingdetail_count[$obcode] = $openingdetail_count[$obcode] + 1;
                                    $item_count[$obcode] = $item_count[$obcode] + 1;
                                    $old_count[$row['date']."@".$row['code']] = $row['date']."@".$row['code'];
                                }
                            }
                            
                            $seq = "SELECT * FROM `pur_purchase` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
                            $groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $sql = $seq."".$ckn_idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if(empty($con_qty[$row['itemcode']]) || $con_qty[$row['itemcode']] == "" || $con_qty[$row['itemcode']] == NULL || $con_qty[$row['itemcode']] == "0" || $con_qty[$row['itemcode']] == "0.00"){ $con_qty[$row['itemcode']] = 1; }
                                $pur_ckn_inv_qty[$row['date']] = $pur_ckn_inv_qty[$row['date']] + ($row['netweight'] * $con_qty[$row['itemcode']]);
                                $pur_ckn_inv_amt[$row['date']] = $pur_ckn_inv_amt[$row['date']] + $row['totalamt'];
                            }
                            $old_date = "";
                            $seq = "SELECT * FROM `pur_purchase` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
                            $groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `itemcode` ASC";
                            $sql = $seq."".$idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date']."@".$row['itemcode'];
                                //$pur_qty[$obcode] = $pur_qty[$obcode] + $row['netweight'];
                                //$pur_price[$obcode] = $row['itemprice'];
                                //$pur_amt[$obcode] = $pur_amt[$obcode] + $row['totalamt'];
                                
                                if($row['itemcode'] == "BRB-0001"){
                                    $pur_price[$obcode] = $row['itemprice'];
                                    if($old_date != $row['date']){
                                        $pur_birds[$obcode] += (float)$row['birds'];
                                        $pur_qty[$obcode] = $pur_qty[$obcode] + ($row['netweight'] + $pur_ckn_inv_qty[$row['date']]);
                                        $pur_amt[$obcode] = $pur_amt[$obcode] + ($row['totalamt'] + $pur_ckn_inv_amt[$row['date']]);
                                        $old_date = $row['date'];
                                    }
                                    else{
                                        $pur_birds[$obcode] += (float)$row['birds'];
                                        $pur_qty[$obcode] = $pur_qty[$obcode] + $row['netweight'];
                                        $pur_amt[$obcode] = $pur_amt[$obcode] + $row['totalamt'];
                                        $old_date = $row['date'];
                                    }
                                }
                                else if(strpos($ckn_codes, $row['itemcode']) !== false && $pur_qty[$row['date']."@BRB-0001"] == ""){
                                    $pur_birds[$row['date']."@BRB-0001"] += (float)$row['birds'];
                                    $pur_price[$row['date']."@BRB-0001"] = $row['itemprice'];
                                    $pur_qty[$row['date']."@BRB-0001"] = $pur_ckn_inv_qty[$row['date']];
                                    $pur_amt[$row['date']."@BRB-0001"] = $pur_ckn_inv_amt[$row['date']];
                                }
                                else{
                                    $pur_birds[$obcode] += (float)$row['birds'];
                                    $pur_qty[$obcode] = $pur_qty[$obcode] + $row['netweight'];
                                    $pur_price[$obcode] = $row['itemprice'];
                                    $pur_amt[$obcode] = $pur_amt[$obcode] + $row['totalamt'];
                                }
                            }
                            //$old_count = "";
                            $seq = "SELECT * FROM `pur_purchase` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `itemcode` IN ($icode)";
                            $groupby = " ORDER BY `date`,`itemcode` ASC";
                            $sql = $seq."".$idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if($old_count[$row['date']."@".$row['itemcode']] != $row['date']."@".$row['itemcode']){
                                    $obcode = $row['date'];
                                    $pur_count[$obcode] = $pur_count[$obcode] + 1;
                                    $item_count[$obcode] = $item_count[$obcode] + 1;
                                    $old_count[$row['date']."@".$row['itemcode']] = $row['date']."@".$row['itemcode'];
                                }
                            }
                            
                            $seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
                            $groupby = " ORDER BY `code` ASC";
                            $sql = $seq."".$iftdetails."".$wtnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date']."@".$row['code'];
                                $tin_birds[$obcode] += (float)$row['birds'];
                                $tin_qty[$obcode] = $tin_qty[$obcode] + $row['quantity'];
                                $tin_price[$obcode] = $row['price'];
                                $tin_amt[$obcode] = $tin_amt[$obcode] + ($row['quantity'] * $row['price']);
                            }
                            //$old_count = "";
                            $seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `code` IN ($icode)";
                            $groupby = " GROUP BY `date` ORDER BY `date` ASC";
                            $sql = $seq."".$iftdetails."".$wtnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if($old_count[$row['date']."@".$row['code']] != $row['date']."@".$row['code']){
                                    $obcode = $row['date'];
                                    $tin_count[$obcode] = $tin_count[$obcode] + 1;
                                    $item_count[$obcode] = $item_count[$obcode] + 1;
                                    $old_count[$row['date']."@".$row['code']] = $row['date']."@".$row['code'];
                                }
                            }
                            
                            $seq = "SELECT * FROM `customer_sales` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
                            $groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                            $sql = $seq."".$ckn_idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if(empty($con_qty[$row['itemcode']]) || $con_qty[$row['itemcode']] == "" || $con_qty[$row['itemcode']] == NULL || $con_qty[$row['itemcode']] == "0" || $con_qty[$row['itemcode']] == "0.00"){ $con_qty[$row['itemcode']] = 1; }
                                $ckn_inv_qty[$row['date']] = $ckn_inv_qty[$row['date']] + ($row['netweight'] * $con_qty[$row['itemcode']]);
                                $ckn_inv_amt[$row['date']] = $ckn_inv_amt[$row['date']] + $row['totalamt'];
                            }
                            $old_date = "";
                            $seq = "SELECT * FROM `customer_sales` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
                            $groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode` ASC";
                            $sql = $seq."".$idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql); $sale_inv_amt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if($row['itemcode'] == "BRB-0001"){
                                    $obcode = $row['date']."@".$row['itemcode'];
                                    $inv_price[$obcode] = $row['itemprice'];
                                    if($old_date != $row['date']){
                                        $inv_birds[$obcode] += (float)$row['birds'];
                                        $inv_qty[$obcode] = $inv_qty[$obcode] + ($row['netweight'] + $ckn_inv_qty[$row['date']]);
                                        $inv_amt[$obcode] = $inv_amt[$obcode] + ($row['totalamt'] + $ckn_inv_amt[$row['date']]);
                                        //echo "<br/>$inv_amt[$obcode] = $inv_amt[$obcode] + (".$row['totalamt'] + $ckn_inv_amt[$row['date']].")";
                                        $old_date = $row['date'];
                                    }
                                    else{
                                        $inv_birds[$obcode] += (float)$row['birds'];
                                        $inv_qty[$obcode] = $inv_qty[$obcode] + $row['netweight'];
                                        $inv_amt[$obcode] = $inv_amt[$obcode] + $row['totalamt'];
                                        $old_date = $row['date'];
                                    }
                                }
                                else if(strpos($ckn_codes, $row['itemcode']) !== false && $inv_qty[$row['date']."@BRB-0001"] == ""){
                                    $inv_birds[$row['date']."@BRB-0001"] += (float)$row['birds'];
                                    $inv_price[$row['date']."@BRB-0001"] = $row['itemprice'];
                                    $inv_qty[$row['date']."@BRB-0001"] = $ckn_inv_qty[$row['date']];
                                    $inv_amt[$row['date']."@BRB-0001"] = $ckn_inv_amt[$row['date']];
                                }
                                else{
                                    $obcode = $row['date']."@".$row['itemcode'];
                                    $inv_birds[$obcode] += (float)$row['birds'];
                                    $inv_qty[$obcode] = $inv_qty[$obcode] + $row['netweight'];
                                    $inv_price[$obcode] = $row['itemprice'];
                                    $inv_amt[$obcode] = $inv_amt[$obcode] + $row['totalamt'];
                                }
                                $sale_inv_qty[$obcode] = $sale_inv_qty[$obcode] + $row['netweight'];
                                $sale_inv_amt[$obcode] = (float)$sale_inv_amt[$obcode] + (float)$row['totalamt'];
                                //echo "<br/>".$sale_inv_amt[$obcode]."-".$inv_amt[$obcode]."-".$row['totalamt']."-".$obcode;
                            }
                            //$old_count = "";
                            $seq = "SELECT * FROM `customer_sales` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `itemcode` IN ($icode)";
                            $groupby = " ORDER BY `date`,`itemcode` ASC";
                            $sql = $seq."".$idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if($old_count[$row['date']."@".$row['itemcode']] != $row['date']."@".$row['itemcode']){
                                    $obcode = $row['date'];
                                    $inv_count[$obcode] = $inv_count[$obcode] + 1;
                                    $item_count[$obcode] = $item_count[$obcode] + 1;
                                    $old_count[$row['date']."@".$row['itemcode']] = $row['date']."@".$row['itemcode'];
                                }
                            }
                            
                            $seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
                            $groupby = " ORDER BY `code` ASC";
                            $sql = $seq."".$iftdetails."".$wfnames."".$groupby; $query = mysqli_query($conn,$sql); $tou_qty = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date']."@".$row['code'];
                                $tou_birds[$obcode] += (float)$row['birds'];
                                $tou_qty[$obcode] = $tou_qty[$obcode] + $row['quantity'];
                                $tou_price[$obcode] = $row['price'];
                                $tou_amt[$obcode] = $tou_amt[$obcode] + ($row['quantity'] * $row['price']);
                            }
                            //$old_count = "";
                            $seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `code` IN ($icode)";
                            $groupby = " ORDER BY `date` ASC";
                            $sql = $seq."".$iftdetails."".$wfnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if($old_count[$row['date']."@".$row['code']] != $row['date']."@".$row['code']){
                                    $obcode = $row['date'];
                                    $tou_count[$obcode] = $tou_count[$obcode] + 1;
                                    $item_count[$obcode] = $item_count[$obcode] + 1;
                                    $old_count[$row['date']."@".$row['code']] = $row['date']."@".$row['code'];
                                }
                            }
                            $sql = "SELECT * FROM `main_mortality` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `itemcode` IN ($icode)".$idetails." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC";
                            $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date']."@".$row['itemcode'];
                                $mort_birds[$obcode] += (float)$row['birds'];
                                $mort_qty[$obcode] += $row['quantity'];
                                $mort_amt[$obcode] += $row['amount'];
                            }
                            
                            $seq = "SELECT * FROM `acc_vouchers` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `prefix` ='PV'";
                            $groupby = " ORDER BY `date` ASC";
                            $sql = $seq."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date'];
                                $pv_amt[$obcode] = $pv_amt[$obcode] + $row['amount'];
                            }
                            $seq = "SELECT * FROM `acc_vouchers` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `prefix` ='RV'";
                            $groupby = " GROUP BY `date` ORDER BY `date` ASC";
                            $sql = $seq."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $obcode = $row['date'];
                                $rv_amt[$obcode] = $rv_amt[$obcode] + $row['amount'];
                            }


                            $tob_birds = $tp_birds = $ts_birds = $tm_birds = $tc_birds = $ta_birds = $tob_qty = $tob_amt = $tp_qty = $tp_amt = $ts_qty = $ts_amt = $tmrt_qty = $tmrt_amt = $tc_qty = $tc_amt = 
                            $ta_qty = $ta_amt = $tw_qty = $tw_amt = $te_amt =  $tr_amt =  $tm_amt = 0; $olddate = "";
                        foreach($mainfilter as $mf){
                            $obl_amt = $id = 0;
                            //echo "<br/>".
                            $id = $mainfilter[$mf];
                            //echo $id."</br>";
                            $ids = explode("@",$id);
                            $dates = $ids[0];
                            $icodes  = $ids[1];
                            
                            $m = date("m",strtotime($dates));
                            $y = date("Y",strtotime($dates));
                            $pre_date = date('Y-m-d', strtotime($ids[0].'-1 days'));
                            $pre_code = $pre_date."@".$icodes;
                            if(empty($openingdetails[$pre_code])){ $openingdetails[$pre_code] = 0; }
                            $pre_price = $pur_price[$pre_code];
                            $obl_amt = $openingdetails[$pre_code] * $openingdetailsp[$pre_code];
                            //echo "<script>alert('$ds---->$olddate');</script>";
                            $act_price = $pur_price[$id];
                            $act_amt = $openingdetails[$id] * $act_price;
                            
                            //Mortality Calculations
                            $mrt_qty = $mrt_amt = 0;
                            if(empty($mort_qty[$id]) || $mort_qty[$id] == ""){ } else{ $mrt_qty = $mort_qty[$id]; }
                            if(empty($mort_amt[$id]) || $mort_amt[$id] == ""){ } else{ $mrt_amt = $mort_amt[$id]; }

                            //Birds Calculations
                            if(empty($opn_birds[$pre_code]) || $opn_birds[$pre_code] == ""){ $opn_birds[$pre_code] = 0; }
                            if(empty($pur_birds[$id]) || $pur_birds[$id] == ""){ $pur_birds[$id] = 0; }
                            if(empty($tin_birds[$id]) || $tin_birds[$id] == ""){ $tin_birds[$id] = 0; }
                            if(empty($inv_birds[$id]) || $inv_birds[$id] == ""){ $inv_birds[$id] = 0; }
                            if(empty($tou_birds[$id]) || $tou_birds[$id] == ""){ $tou_birds[$id] = 0; }
                            if(empty($mort_birds[$id]) || $mort_birds[$id] == ""){ $mort_birds[$id] = 0; }

                            if(number_format_ind($openingdetails[$pre_code]) == "0.00" && number_format_ind($mrt_qty) == "0.00" && number_format_ind($pur_qty[$id]) == "0.00" && number_format_ind($pur_birds[$id]) == "0.00" && number_format_ind($inv_qty[$id]) == "0.00" && number_format_ind($openingdetails[$id]) == "0.00" && number_format_ind($tin_qty[$id]) == "0.00" && number_format_ind($tou_qty[$id]) == "0.00"){
                                //echo "<br/>".$dates;
                            }
                            else{

                                $html .= '<tr>';
                                $html .= '<td colspan="2">'.date("d.m.Y",strtotime($dates)).'</td>';
                                $html .= '<td colspan="2">'.$itemnames[$icodes].'</td>';
                                $html .= '<td>'.str_replace(".00","",number_format_ind($opn_birds[$pre_code])).'</td>';
                                $html .= '<td>'.number_format_ind($openingdetails[$pre_code]).'</td>';
                                $html .= '<td>'.number_format_ind($obl_amt).'</td>';
                                $tob_birds += (float)$opn_birds[$pre_code];
                                $tob_qty = $tob_qty + $openingdetails[$pre_code];
                                $tob_amt = $tob_amt + $obl_amt;
                                $v_count = $item_count[$dates];
                                if($wnames == "") {
                                    $p_qty = $pur_qty[$id];
                                    $p_amt = $pur_amt[$id];
                                    $s_qty = $inv_qty[$id];
                                    $s_amt = $inv_amt[$id];
                                    
                                    $tp_qty = $tp_qty + $p_qty; $tp_amt = $tp_amt + $p_amt;
                                    $ts_qty = $ts_qty + $s_qty; $ts_amt = $ts_amt + $s_amt;
                                    
                                    $c_bds = (((float)$pur_birds[$id] + (float)$tin_birds[$id] + $opn_birds[$pre_code]) - ((float)$inv_birds[$id] + (float)$tou_birds[$id] + (float)$mort_birds[$id]));
                                    $c_qty = ($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id] - $mrt_qty;
                                    $c_amt = $act_price * (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id] - $mrt_qty);
                                    
                                    $tmrt_qty += (float)$mrt_qty; $tmrt_amt += (float)$mrt_amt;
                                    
                                    $tc_qty = $tc_qty + $c_qty; $tc_amt = $tc_amt + $c_amt;
                                    
                                    $a_bds = $opn_birds[$id];
                                    $a_qty = $openingdetails[$id];
                                    //$a_amt = $act_amt;
                                    $a_amt = $openingdetailsp[$id] * $a_qty;
                                    
                                    $ta_qty = $ta_qty + $a_qty; $ta_amt = $ta_amt + $a_amt;
                                    
                                    $tp_birds += ((float)$pur_birds[$id] + (float)$tin_birds[$id]);
                                    $ts_birds += ((float)$inv_birds[$id] + (float)$tou_birds[$id]);
                                    $tm_birds += (float)$mort_birds[$id];
                                    $tc_birds += (float)$c_bds;
                                    $ta_birds += (float)$a_bds;

                                    $w_qty = $openingdetails[$id] - (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id]);
                                    if(($p_qty + $openingdetails[$pre_code]) > 0){
                                        $w_per = ($w_qty / ($p_qty + $openingdetails[$pre_code])) * 100;
                                    }
                                    else{
                                        $w_per = 0;
                                    }
                                    $for_avg_w_per = $for_avg_w_per + $w_per;
                                    $wfc = $wfc + 1;
                                    if((float)$openingdetailsp[$id] != 0){
                                        $w_amt = $openingdetailsp[$id] * ($openingdetails[$id] - (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id]));
                                    }
                                    else{
                                        $w_amt = $pur_price[$id] * ($openingdetails[$id] - (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id]));
                                    }
                                    
                                    $tw_qty = $tw_qty + $w_qty; $tw_amt = $tw_amt + $w_amt;
                                    $link = "cus_chickensaleswithconreport.php?fromdate=$dates&todate=$dates&cname=all&iname=all&wname=all&ucode=all";

                                $html .= '<td>'.str_replace(".00","",number_format_ind(((float)$pur_birds[$id] + (float)$tin_birds[$id]))).'</td>';
                               // $html .= '<td>'.$cus_name[$row['ccode']].'</td>';
                               $html .= '<td style="text-align:right;">'.number_format_ind(round($p_qty,2)).'</td>';
                               $html .= '<td style="text-align:right;">'.number_format_ind(round($p_amt,2)).'</td>';
                               
                               $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(((float)$inv_birds[$id] + (float)$tou_birds[$id]))).'</td>';
                               if($itemnames[$icodes] == "Broiler Birds"){ $html .= '<td style="text-align:right;"><a href="$link" target="_BLANK">'.number_format_ind($s_qty).'</a></td>'; }
                               else { $html .= '<td style="text-align:right;">'.number_format_ind(round($s_qty,2)).'</td>'; }
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($s_amt,2)).'</td>';
                                
                                $html .= '<td>'.str_replace(".00","",number_format_ind($mort_birds[$id])).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($mrt_qty,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($mrt_amt,2)).'</td>';
                                
                                $html .= '<td>'.str_replace(".00","",number_format_ind($c_bds)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($c_qty,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($c_amt,2)).'</td>';
                                
                                $html .= '<td>'.str_replace(".00","",number_format_ind($a_bds)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($a_qty,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($a_amt,2)).'</td>';
                                
                                $html .= '<td>'.number_format_ind($w_per)."%(".number_format_ind($w_qty).')</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($w_amt,2)).'</td>';
                                if($olddate == $dates){
                                    $m_amt = 0;
                                    $m_amt = ($inv_amt[$id] - (($pur_amt[$id] + $obl_amt) - $a_amt));
                                    $tm_amt = $tm_amt + $m_amt;
                                     $html .= '<td align="right" colspan="2">'.number_format_ind($m_amt).'</td>';
                                }
                                else {
                                    $e_amt = $pv_amt[$dates];
                                    //$r_amt = $rv_amt[$dates];
                                    $r_amt = 0;
                                    $m_amt = 0;
                                    $m_amt = ($inv_amt[$id] - (($pur_amt[$id] + $obl_amt) - $a_amt)) - ($pv_amt[$dates] - $rv_amt[$dates]);
                                    
                                    $te_amt = $te_amt + $e_amt;
                                    $tr_amt = $tr_amt + $r_amt;
                                    $tm_amt = $tm_amt + $m_amt;
                                    
                                    $html .= '<td rowspan="'.$v_count.'" align="right" colspan="2">'.number_format_ind($e_amt).'</td>';
                                    $html .= '<td  align="right" colspan="2">'.number_format_ind($m_amt).'</td>';
                                    $olddate = $dates;
                                }
                            }
                            else {
                                $p_qty = $pur_qty[$id] + $tin_qty[$id];
                                $p_amt = $pur_amt[$id] + $tin_amt[$id];
                                $s_qty = $inv_qty[$id] + $tou_qty[$id];
                                $s_amt = $inv_amt[$id] + $tou_amt[$id];
                                //echo "<script>alert('$pur_qty[$id]---->$tin_qty[$id]');</script>";
                                $tp_qty = $tp_qty + $p_qty; $tp_amt = $tp_amt + $p_amt;
                                $ts_qty = $ts_qty + $s_qty; $ts_amt = $ts_amt + $s_amt;
                                
                                if(number_format_ind($openingdetails[$pre_code]) == "0.00"){ $openingdetails[$pre_code] = 0; }
                                if(number_format_ind($pur_qty[$id]) == "0.00"){ $pur_qty[$id] = 0; }
                                if(number_format_ind($tin_qty[$id]) == "0.00"){ $tin_qty[$id] = 0; }
                                if(number_format_ind($inv_qty[$id]) == "0.00"){ $inv_qty[$id] = 0; }
                                if(number_format_ind($tou_qty[$id]) == "0.00"){ $tou_qty[$id] = 0; }
                                if(number_format_ind(((float)$pur_qty[$id] + (float)$tin_qty[$id] + (float)$openingdetails[$pre_code])) == number_format_ind(((float)$inv_qty[$id] + (float)$tou_qty[$id]))){
                                    $c_qty = $c_amt = $w_qty = 0;
                                }
                                else{
                                    $c_bds = (((float)$pur_birds[$id] + (float)$tin_birds[$id] + $opn_birds[$pre_code]) - ((float)$inv_birds[$id] + (float)$tou_birds[$id] + (float)$mort_birds[$id]));
                                    $c_qty = ((float)$pur_qty[$id] + (float)$tin_qty[$id] + (float)$openingdetails[$pre_code]) - ((float)$inv_qty[$id] + (float)$tou_qty[$id] + (float)$mrt_qty);
                                    $c_amt = $act_price * (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ((float)$inv_qty[$id] + (float)$tou_qty[$id] + (float)$mrt_qty));
                                    $w_qty = $openingdetails[$id] - (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ((float)$inv_qty[$id] + (float)$tou_qty[$id] + (float)$mrt_qty));
                                }

                                $tmrt_qty += (float)$mrt_qty; $tmrt_amt += (float)$mrt_amt;
                                
                                $tc_qty = $tc_qty + $c_qty; $tc_amt = $tc_amt + $c_amt;
                                
                                $a_bds = $opn_birds[$id];
                                $a_qty = $openingdetails[$id];
                                $a_amt = $openingdetailsp[$id] * $a_qty;
                                
                                $ta_qty = $ta_qty + $a_qty; $ta_amt = $ta_amt + $a_amt;
                                if(($p_qty + $openingdetails[$pre_code]) > 0){ $w_per = ($w_qty / ($p_qty + $openingdetails[$pre_code])) * 100; } else{ $w_per = 0; }
                                $for_avg_w_per = $for_avg_w_per + $w_per;
                                $wfc = $wfc + 1;
                                if((float)$openingdetailsp[$id] != 0){
                                    $w_amt = $openingdetailsp[$id] * ($openingdetails[$id] - (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) -($inv_qty[$id] + $tou_qty[$id])));
                                }
                                else{
                                    $w_amt = $pur_price[$id] * ($openingdetails[$id] - (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ($inv_qty[$id] + $tou_qty[$id])));
                                }
                                
                                $tp_birds += ((float)$pur_birds[$id] + (float)$tin_birds[$id]);
                                $ts_birds += ((float)$inv_birds[$id] + (float)$tou_birds[$id]);
                                $tm_birds += (float)$mort_birds[$id];
                                $tc_birds += (float)$c_bds;
                                $ta_birds += (float)$a_bds;

                                $tw_qty = $tw_qty + $w_qty; $tw_amt = $tw_amt + $w_amt;
                                
                                
                                $link = "cus_chickensaleswithconreport.php?fromdate=$dates&todate=$dates&cname=all&iname=all&wname=all&ucode=all";

                                $html .= '<td>'.str_replace(".00","",number_format_ind(((float)$pur_birds[$id] + (float)$tin_birds[$id]))).'</td>';
                                // $html .= '<td>'.$cus_name[$row['ccode']].'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($p_qty,2)).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind(round($p_amt,2)).'</td>';
                                
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(((float)$inv_birds[$id] + (float)$tou_birds[$id]))).'</td>';
                                if($itemnames[$icodes] == "Broiler Birds"){ $html .= '<td style="text-align:right;"><a href="$link" target="_BLANK">'.number_format_ind($s_qty).'</a></td>'; }
                                else { $html .= '<td style="text-align:right;">'.number_format_ind(round($s_qty,2)).'</td>'; }
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($s_amt,2)).'</td>';
                                 
                                 $html .= '<td>'.str_replace(".00","",number_format_ind($mort_birds[$id])).'</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($mrt_qty,2)).'</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($mrt_amt,2)).'</td>';
                                 
                                 $html .= '<td>'.str_replace(".00","",number_format_ind($c_bds)).'</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($c_qty,2)).'</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($c_amt,2)).'</td>';
                                 
                                 $html .= '<td>'.str_replace(".00","",number_format_ind($a_bds)).'</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($a_qty,2)).'</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($a_amt,2)).'</td>';
                                 
                                 $html .= '<td>'.number_format_ind($w_per)."%(".number_format_ind($w_qty).')</td>';
                                 $html .= '<td style="text-align:right;">'.number_format_ind(round($w_amt,2)).'</td>';
                                 if($olddate == $dates){
                                    $m_amt = (($inv_amt[$id] + $tou_amt[$id]) - (($pur_amt[$id] + $tin_amt[$id] + $obl_amt) - $a_amt));
									$tm_amt = $tm_amt + $m_amt;
                                     $html .= '<td align="right" colspan="2">'.number_format_ind($m_amt).'</td>';
                                }
                                else {
                                    $e_amt = $pv_amt[$dates];
                                    //$r_amt = $rv_amt[$dates];
                                    $r_amt = 0;
                                    $m_amt = (($inv_amt[$id] + $tou_amt[$id]) - (($pur_amt[$id] + $tin_amt[$id] + $obl_amt) - $a_amt)) - ($pv_amt[$dates] - $rv_amt[$dates]);
                                     
                                    $te_amt = $te_amt + $e_amt;
                                    $tr_amt = $tr_amt + $r_amt;
                                    $tm_amt = $tm_amt + $m_amt;
                                    
                                    $html .= '<td rowspan="'.$v_count.'" align="right" colspan="2">'.number_format_ind($e_amt).'</td>';
                                    $html .= '<td  align="right" colspan="2">'.number_format_ind($m_amt).'</td>';
                                    $olddate = $dates;
                                }

                            }
                            // $html .= '<td style="text-align:right;">'.number_format_ind(round($rsale_amt,2)).'</td>';
                            $html .= '</tr>';
                            }
                        }     
                                
                                
                            
                               $html .= '</tbody>';
                                // Add totals row
                                $html .= '<thead class="tfoot1">';
                                $html .= '<tr >';
                                $html .= '<th colspan="4">Grand Total</th>';
                                // Output the overall total for all weeks
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tob_birds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tob_qty, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tob_amt, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tp_birds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tp_qty, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tp_amt, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ts_birds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ts_qty, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ts_amt, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tm_birds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tmrt_qty, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tmrt_amt, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tc_birds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tc_qty, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($tc_amt, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ta_birds, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ta_qty, 2)) . '</th>';
                                $html .= '<th style="text-align:right;">' . number_format_ind(round($ta_amt, 2)) . '</th>';
                                if(($tob_qty + $tp_qty) > 0){ $fwa_per = ($tw_qty / ($tob_qty + $tp_qty)) * 100; } else{ $fwa_per = 0; }
                                $html .= '<th style="text-align:right;">'.number_format_ind($fwa_per)."%(".number_format_ind($tw_qty).')</th>';

                                $html .= '<th style="text-align:right;">'.number_format_ind($tw_amt).'</th>';
                                $html .= '<th style="text-align:right;" colspan="2">'.number_format_ind($te_amt).'</th>';
                                $html .= '<th style="text-align:right;" colspan="2">'.number_format_ind($tm_amt).'</th>';
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
                var users = document.getElementById("cus_code").value;
                var no_weeks = document.getElementById("no_weeks").value;
                var l = true;
                if(users == "select"){
                    alert("Kindly select Customer");
                    l = false;
                }
                else if(no_weeks == ""){
                    alert("Kindly select Number of Weeks");
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
