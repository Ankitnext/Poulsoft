<?php
//chicken_weekly_cussales1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
// $cuss = $_GET['cuss'];
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "CustomerLedgerReportAllNew_nb.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_weekly_cussales1.php?db=".$db;
}
include "number_format_ind.php";
include "decimal_adjustments.php";
$file_name = "Customer Ledger";

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

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

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

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

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

	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }

    // Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

    $idisplay = ''; $ndisplay = 'style="display:none;"';
	$cname = $_POST['cname']; $iname = $_POST['iname'];
	if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `groupcode` = '$cname'"; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` = '1'".$cnames." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$cus_mobile[$row['code']] = $row['mobileno'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];
		$creditamt[$row['code']] = $row['creditamt'];
	}
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
	
	

    $exoption = "displaypage"; $bwd_aflag = 0;

$todate = $fromdate = date("Y-m-d"); $sectors = $cuss = "select"; $fstyles = $fsizes = "default"; $exports = "display"; 
if(isset($_POST['submit']) == true){
    $fromdate = date("Y-m-d",strtotime($_POST['fromdate']));
    $todate = date("Y-m-d",strtotime($_POST['todate']));
	
    $cuss = $_POST['cus_code'];
    
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
    if($_POST['bwd_aflag'] == "on" || $_POST['bwd_aflag'] == 1 || $_POST['bwd_aflag'] == true){ $bwd_aflag = 1; }
    $cname = $_POST['cname']; $iname = $_POST['iname'];
	if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `groupcode` = '$cname'"; }
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` = '1'".$cnames." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$cus_mobile[$row['code']] = $row['mobileno'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];
		$creditamt[$row['code']] = $row['creditamt'];
	}
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
}
?>
<html>
	<head>
        <title><?php echo $file_name; ?></title>
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
                                <td colspan="18" align="center">
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
                                            <input type="text" name="fromdate" id="fromdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div> 
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">To Date</label>
                                            <input type="text" name="todate" id="todate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($todate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div> 
                                        <div class="form-group" style="width:110px;">
                                                <label class="reportselectionlabel">Group</label>&nbsp;
                                            <select name="cname" id="checkcname" class="form-control select2">
                                                <option value="all" selected>-All-</option>
                                                <?php
                                                $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                while($row = mysqli_fetch_assoc($query)){
                                                ?>
                                                    <option value="<?php echo $row['code']; ?>" <?php if($cname == $row['code']){ echo 'selected'; } ?>><?php echo $row['description']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                           <label class="reportselectionlabel">B/w Days</label>&nbsp;
										<input type="checkbox" name="bwd_aflag" id="bwd_aflag" <?php if($bwd_aflag == 1){ echo "checked"; } ?> />
                                        </div>
                                        <!-- <div class="form-group" style="width:290px;">
                                            <label for="cus_code">Customer</label>
                                            <select name="cus_code" id="cus_code" class="form-control select2" style="width:280px;">
                                                <option value="select" <?php if($cuss == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cuss == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div> -->
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
                                            <label>Export</label>
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
                            $html = '';
                            //Sales
                            // $html .= '<tr class="thead2">';
                            // $html .= '<th colspan="8" style="text-align:center;">Customer Weekly Balance Sales</th>';
                            // $html .= '</tr>';
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">Name</th>';
                            $html .= '<th style="text-align:center;">Mobile No</th>';
                            $html .= '<th style="text-align:center;">Opening Balance</th>';
                            $html .= '<th style="text-align:center;">Sales Qty</th>';
                            $html .= '<th style="text-align:center;">Sales</th>';
                            $html .= '<th style="text-align:center;">Receipt</th>';
                            $html .= '<th style="text-align:center;">B/w days balance</th>';
                            $html .= '<th style="text-align:center;">Balance</th>';
                            $html .= '</tr>';

                          $fromdate = $_POST['fromdate'];
										$todate = $_POST['todate'];
										if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate'])); }
										$cname = $_POST['cname']; $iname = $_POST['iname'];
										if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
										
										
										//sales invoice
										$ob_sales = $ob_receipts = $ob_ccn = $ob_cdn = array();
										$sql = "SELECT * FROM `customer_sales` WHERE `date` < '$fromdate' AND `active` = '1' ORDER BY `date`,`invoice`,`customercode` ASC";
										$query = mysqli_query($conn,$sql); $old_inv = "";
										while($row = mysqli_fetch_assoc($query)){
											if($old_inv != $row['invoice']){
												$ob_sales[$row['customercode']] = $ob_sales[$row['customercode']] + $row['finaltotal'];
												$old_inv = $row['invoice'];
											}
										}
										//Customer Receipt
										$sql = "SELECT * FROM `customer_receipts` WHERE `date` < '$fromdate' AND `active` = '1' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$ob_receipts[$row['ccode']] = $ob_receipts[$row['ccode']] + $row['amount'];
										}
										//Customer Returns
										$ob_returns = array();
										$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fromdate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns[$obrow['vcode']] += (float)$obrow['amount']; }

										//Customer Mortality
										$ob_smortality = array();
										$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fromdate' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_smortality[$obrow['ccode']] += (float)$obrow['amount']; }

										//Customer CrDr Note
										$sql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($row['mode'] == "CCN"){
												$ob_ccn[$row['ccode']] = $ob_ccn[$row['ccode']] + $row['amount'];
											}
											else{
												$ob_cdn[$row['ccode']] = $ob_cdn[$row['ccode']] + $row['amount'];
											}
										}
										//sales invoice
										$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1' ORDER BY `date`,`invoice`,`customercode` ASC";
										$query = mysqli_query($conn,$sql); $old_inv = "";
										while($row = mysqli_fetch_assoc($query)){
											if($old_inv != $row['invoice']){
												$bt_sales[$row['customercode']] = $bt_sales[$row['customercode']] + $row['finaltotal'];
												$old_inv = $row['invoice'];
											}
											$bt_sales_qty[$row['customercode']] = $bt_sales_qty[$row['customercode']] + $row['netweight'];
										}
										//Customer Receipt
										$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1' ORDER BY `ccode` ASC";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$bt_receipts[$row['ccode']] = $bt_receipts[$row['ccode']] + $row['amount'];
										}
										//Customer Returns
										$bt_returns = array();
										$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $bt_returns[$obrow['vcode']] += (float)$obrow['amount']; }

										//Customer Mortality
										$bt_smortality = array();
										$obsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $bt_smortality[$obrow['ccode']] += (float)$obrow['amount']; }

										//Customer CrDr Note
										$sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `ccode` ASC";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($row['mode'] == "CCN"){
												$bt_ccn[$row['ccode']] = $bt_ccn[$row['ccode']] + $row['amount'];
											}
											else{
												$bt_cdn[$row['ccode']] = $bt_cdn[$row['ccode']] + $row['amount'];
											}
										}
										$ftotal = $ft_ob =  $ft_sq =  $ft_sa =  $ft_rt =  $ft_bb = 0;

foreach($pcode as $pcodes){
											if((int)$bwd_aflag == 0 || (int)$bwd_aflag == 1 && ((float)$bt_sales_qty[$pcodes] > 0 || ((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]) > 0) || ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]) > 0){
												$html .= "<tr>";
												$html .= "<td style='text-align:left;'>".$pname[$pcodes]."</td>";
												$html .= "<td style='text-align:left;'>".$cus_mobile[$pcodes]."</td>";
												$ob_cramt = $ob_dramt = $ob_dr = $ob_cr = $ob_fcr = $ob_fdr = $bt_dr = $bt_cr = $bt_fcr = $bt_fdr = $balance = 0;
												if($obtype[$pcodes] == "Cr"){
												$ob_cramt = $obamt[$pcodes];
												}
												else {
												$ob_dramt = $obamt[$pcodes];
												}
												$ft_ob = $ft_ob + (((float)$ob_sales[$pcodes] + (float)$ob_cdn[$pcodes] + (float)$ob_dramt) - ((float)$ob_receipts[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_ccn[$pcodes] + (float)$ob_cramt));
												$ft_sq = $ft_sq + (float)$bt_sales_qty[$pcodes];
												$ft_sa = $ft_sa + ((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]);
												$ft_rt = $ft_rt + ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]);
												$ft_bb = $ft_bb + (((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]) - ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]));
												
												$html .= "<td>".number_format_ind(((float)$ob_sales[$pcodes] + (float)$ob_cdn[$pcodes] + (float)$ob_dramt) - ((float)$ob_receipts[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_ccn[$pcodes] + (float)$ob_cramt))."</td>";
												$html .= "<td>".number_format_ind($bt_sales_qty[$pcodes])."</td>";
												$html .= "<td>".number_format_ind((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes])."</td>";
												$html .= "<td>".number_format_ind((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes])."</td>";
												$html .= "<td>".number_format_ind(((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]) - ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]))."</td>";
												$ob_dr = (float)$ob_sales[$pcodes] + (float)$ob_cdn[$pcodes] + (float)$ob_dramt;
												$ob_cr = (float)$ob_receipts[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_ccn[$pcodes] + (float)$ob_cramt;
												if($ob_cr > $ob_dr){
												$ob_fcr = $ob_cr - $ob_dr;
												}
												else{
												$ob_fdr = $ob_dr - $ob_cr;
												}
												$bt_dr = (float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes];
												$bt_cr = (float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes];
												if($bt_cr > $bt_dr){
												$bt_fcr = (float)$bt_cr - (float)$bt_dr;
												}
												else{
												$bt_fdr = (float)$bt_dr - (float)$bt_cr;
												}
												$balance = ((float)$ob_fdr + (float)$bt_fdr) - ((float)$ob_fcr + (float)$bt_fcr);
												$ftotal = (float)$ftotal + (float)$balance;
												if(!empty($creditamt[$pcodes]) && (float)$creditamt[$pcodes] != 0 && (float)$creditamt[$pcodes] < (float)$balance){
													$html .= "<td style='color:red;'>".number_format_ind($balance)."</td>";
												}
												else{
													$html .= "<td>".number_format_ind($balance)."</td>";
												}
												
												$html .= "</tr>";
											}
										}

                            // foreach($week_no as $key => $value){
                            //     if((int)$key == 1){ $ob_amt = $balance; } else{ $ob_amt = $cls_bal; }
                            //     $data1 = array(); $data1 = explode("@",$value);

                            //     $week_amt = $cls_bal = 0;
                            //     $week_amt = ((float)$sql_amt[$key] - (float)$rct_amt[$key]);
                            //     $cls_bal = (((float)$ob_amt + (float)$sql_amt[$key]) - (float)$rct_amt[$key]);

                            //     $slno++;
                            //     $price = 0; if((float)$isale_qty[$key] != 0){ $price = (float)$isale_amt[$key] / (float)$isale_qty[$key]; }
                            //     $html .= '<tr>';
                            //     $html .= '<td style="text-align:right;">'.number_format_ind(round($ob_amt,2)).'</td>';
                            //     $html .= '<td>'.date("d.m.Y",strtotime($data1[0])).'</td>';
                            //     $html .= '<td>'.date("d.m.Y",strtotime($data1[1])).'</td>';
                            //     $html .= '<td style="text-align:right;">'.number_format_ind(round($sql_qty[$key],2)).'</td>';
                            //     $html .= '<td style="text-align:right;">'.number_format_ind(round($sql_amt[$key],2)).'</td>';
                            //     $html .= '<td style="text-align:right;">'.number_format_ind(round($rct_amt[$key],2)).'</td>';
                            //     $html .= '<td style="text-align:right;">'.number_format_ind(round($week_amt,2)).'</td>';
                            //     $html .= '<td style="text-align:right;">'.number_format_ind(round($cls_bal,2)).'</td>';
                            //     $html .= '</tr>';
                                
                            //     $tsale_qty += (float)$sql_qty[$key];
                            //     $tsale_amt += (float)$sql_amt[$key];
                            //     $tsale_ramt += (float)$rct_amt[$key];
                            //     $tsale_wamt += (float)$week_amt;
                            //     $tsale_clb = (float)$cls_bal;
                            // }
                            $html .= '<tr class="thead2">';
                            $html .= '<th colspan="2">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($ft_ob,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($ft_sq,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($ft_sa,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($ft_rt,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($ft_bb,2)).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind(round($ftotal,2)).'</th>';
                            $html .= '</tr>';

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
            function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
