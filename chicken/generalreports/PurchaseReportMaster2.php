<?php
//PurchaseReportMaster22.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
    
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    include "header_head.php"; 
	include "number_format_ind.php"; 
	$dbname = $_SESSION['dbase'];
	$users_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head_new.php";
	$dbname = $db;
	$users_code = $_GET['emp_code'];
}
//Check Column Availability
$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }

	$sql='SHOW COLUMNS FROM `master_reportfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("supbrh_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `supbrh_flag` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Supplier Branch Flag'"; mysqli_query($conn,$sql); }
	if(in_array("purcus_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `purcus_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Customer Name' AFTER `vendor_flag`"; mysqli_query($conn,$sql); }
	if(in_array("salesup_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `salesup_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Supplier Name' AFTER `purcus_flag`"; mysqli_query($conn,$sql); }
	if(in_array("packs_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `packs_flag` varchar(300) NULL DEFAULT NULL COMMENT '' AFTER `salesup_flag`"; mysqli_query($conn,$sql); }
	if(in_array("cases_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `cases_flag` varchar(300) NULL DEFAULT NULL COMMENT '' AFTER `packs_flag`"; mysqli_query($conn,$sql); }
	if(in_array("price1_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `price1_flag` varchar(300) NULL DEFAULT NULL COMMENT '' AFTER `cases_flag`"; mysqli_query($conn,$sql); }
	if(in_array("amount1_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `amount1_flag` varchar(300) NULL DEFAULT NULL COMMENT '' AFTER `price1_flag`"; mysqli_query($conn,$sql); }
	if(in_array("supaddon_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `supaddon_flag` varchar(300) NULL DEFAULT NULL COMMENT '' AFTER `amount1_flag`"; mysqli_query($conn,$sql); }
	
    /*Fetch Column Availability*/
    $sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("packs", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `packs` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `itemcode`"; mysqli_query($conn,$sql); }
    if(in_array("cases", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `cases` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `packs`"; mysqli_query($conn,$sql); }
    if(in_array("price1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `price1` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `cases`"; mysqli_query($conn,$sql); }
    if(in_array("amount1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `amount1` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `price1`"; mysqli_query($conn,$sql); }
    
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("vouexp_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `vouexp_flag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }
    if(in_array("driver_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `driver_flag` INT(100) NOT NULL DEFAULT '0' AFTER `vouexp_flag`"; mysqli_query($conn,$sql); }
    if(in_array("spaof_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `spaof_flag` INT(100) NOT NULL DEFAULT '0' AFTER `driver_flag`"; mysqli_query($conn,$sql); }
    
	/*Check for Table Availability*/
	$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
    if(in_array("chicken_supplier_branch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_supplier_branch LIKE poulso6_admin_chickenmaster.chicken_supplier_branch;"; mysqli_query($conn,$sql1); }
    if(in_array("pur_supplier_addons", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.pur_supplier_addons LIKE poulso6_admin_chickenmaster.pur_supplier_addons;"; mysqli_query($conn,$sql1); }
    
	$today = date("Y-m-d");
	$sql = "SELECT *  FROM `main_linkdetails` WHERE `href` LIKE '%PurchaseReportMaster2.php%'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }
	
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fromdate'])); $tdate =date("Y-m-d",strtotime( $_POST['todate']));
		$uname = $_POST['ucode']; $wname = $_POST['wname']; $selected_sectors = $_POST['sectors'] ?? []; $gname = $_POST['gname']; $cname = $_POST['cname']; $iname = $_POST['iname'];
	}
	else{
		$fdate = $tdate = $today;
		$uname = $wname = $gname = $cname =  $iname = "all";
	}
	
	
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$loc_access = $row['loc_access'];
		$cgroup_access = $row['cgroup_access'];
		if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $utype = "S"; }
		else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $utype = "A"; }
		else if($row['normal_access'] == 1 || $row['normal_access'] == "1"){ $utype = "N"; }
		else{ $utype = "N"; }
	}

	//User Access Filter
	
	
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	}
	else{
		$sql = "SELECT * FROM `log_useraccess` WHERE `empcode` = '$users_code' AND `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	}
	if($uname == "all"){ $ecodes = ""; foreach($user_code as $ecode){ if($ecodes == ""){ $ecodes = $ecode; } else{ $ecodes = $ecodes."','".$ecode; } } $user_filter = " AND a.addedemp IN ('$ecodes')"; }
	else{ $user_filter = " AND a.addedemp = '".$uname."'"; }

		//Location Access Filter
	if (!is_array($selected_sectors)) {
		$selected_sectors = [$selected_sectors];
	}

	if (in_array("all", $selected_sectors)) {
		if ($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL) {
			$sector_filter = $warehouse_filter = "";
		} else {
			$wh_code = str_replace(",", "','", $loc_access);
			$sector_filter = " AND a.warehouse IN ('$wh_code')";
			$warehouse_filter = " AND code IN ('$wh_code')";
		}
	} else {
		// Sanitize and implode selected sectors
		$safe_sectors = array_map('addslashes', $selected_sectors);
		$sector_list = implode("','", $safe_sectors);

		$sector_filter = " AND a.warehouse IN ('$sector_list')";
		$warehouse_filter = " AND code IN ('$sector_list')";
	}
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_filter." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

	//Customer Access Filter
	if($cgroup_access == "all" || $cgroup_access == "All" || $cgroup_access == "" || $cgroup_access == NULL){
		$cgroup_filter = $user_group_codes = "";
	}
	else{
		$cg_code = str_replace(",","','",$cgroup_access);
		$user_group_codes = " AND code IN ('$cg_code')";
		$cgroup_filter = " AND groupcode IN ('$cg_code')";
	}
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%S%'".$user_group_codes." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }

	$sql = "SELECT * FROM `chicken_supplier_branch` WHERE `dflag` = '0' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sbrh_code = $sbrh_name =  $bsup_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sbrh_code[$row['code']] = $row['code']; $sbrh_name[$row['code']] = $row['description']; $bsup_name[$row['code']] = $row['sup_code']; }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $cus_name = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; }

	if($cname == "all"){ $customer_filter = ""; } else{ $customer_filter = " AND a.vendorcode IN ('$cname')"; }
	

	//Item Access Filter
	if($iname == "all") { $item_filter = ""; } else { $item_filter = " AND a.itemcode = '$iname'"; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	//Report Master Access Filter
	$sql = "SELECT * FROM `master_reportfields` WHERE `code` = '$cid' AND `active` = '1'";
	$query = mysqli_query($conn,$sql); $prate_flag = $supaddon_flag = $supbrh_flag = 0;
	while($row = mysqli_fetch_assoc($query)){
		$type = "type";
		$code = "code";
		$pattern = "pattern";
		$field_details[$row['sl_flag']] = "sl_flag";
		$field_details[$row['date_flag']] = "date_flag";
		$field_details[$row['inv_flag']] = "inv_flag";
		$field_details[$row['binv_flag']] = "binv_flag";
		$field_details[$row['vendor_flag']] = "vendor_flag";
		$field_details[$row['supbrh_flag']] = "supbrh_flag"; $sbrh = explode(":",$row['supbrh_flag']); if($sbrh[1] == 1 || $sbrh[1] == "1"){ $supbrh_flag = 1; }
		$field_details[$row['purcus_flag']] = "purcus_flag";
		$field_details[$row['item_flag']] = "item_flag";
		$field_details[$row['jals_flag']] = "jals_flag";
		$field_details[$row['birds_flag']] = "birds_flag";
		$field_details[$row['tweight_flag']] = "tweight_flag";
		$field_details[$row['eweight_flag']] = "eweight_flag";
		$field_details[$row['nweight_flag']] = "nweight_flag";
		$field_details[$row['aweight_flag']] = "aweight_flag";
		$field_details[$row['prate_flag']] = "prate_flag"; $pdet = explode(":",$row['prate_flag']); if($pdet[1] == 1 || $pdet[1] == "1"){ $prate_flag = 1; }
		$field_details[$row['price_flag']] = "price_flag";
		$field_details[$row['tcds_flag']] = "tcds_flag";
		$field_details[$row['discount_flag']] = "discount_flag";
		$field_details[$row['tamt_flag']] = "tamt_flag";
		$field_details[$row['sector_flag']] = "sector_flag";
		$field_details[$row['remarks_flag']] = "remarks_flag";
		$field_details[$row['vehicle_flag']] = "vehicle_flag";
		$field_details[$row['driver_flag']] = "driver_flag";
		$field_details[$row['cr_flag']] = "cr_flag";
		$field_details[$row['dr_flag']] = "dr_flag";
		$field_details[$row['rb_flag']] = "rb_flag";
		$field_details[$row['user_flag']] = "user_flag";
        $field_details[$row['packs_flag']] = "packs_flag";
        $field_details[$row['cases_flag']] = "cases_flag";
        $field_details[$row['price1_flag']] = "price1_flag";
        $field_details[$row['amount1_flag']] = "amount1_flag";
        $field_details[$row['supaddon_flag']] = "supaddon_flag"; $pdet = explode(":",$row['supaddon_flag']); if($pdet[1] == 1 || $pdet[1] == "1"){ $supaddon_flag = 1; }
		$note_flag = $row['note_flag'];
		$note_code = $row['note_code'];
		$vsign_flag = $row['vsign_flag'];
		$csign_flag = $row['csign_flag'];
		$qr_img_flag = $row['qr_img_flag'];
		$col_count = $row['count'];
	}
	
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $exoption = $excel_type = $_POST['export']; } else{ $exoption = $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname']; $exl_iname = $_POST['iname']; $exl_wname = $_POST['wname']; $exl_user = $_POST['ucode']; $exl_gname = $_POST['gname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname = $exl_iname = $exl_wname = $exl_user = $exl_gname = "all";
	}
	$url = "../PHPExcel/Examples/PurchaseReportMaster2-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&ccode=".$exl_cname."&item=".$exl_iname."&sector=".$exl_wname."&user=".$exl_user."&gname=".$exl_gname."&cid=".$cid;
	
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;				
			}
		<style>
		<style>
			body{
				font-size: 12px;
				color: black;
			}
			.thead2,.tbody1 {
				font-size: 12px;
				padding: 1px;
				color: black;
			}
			.formcontrol {
				font-size: 12px;
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 12px;
				color: black;
				padding-right: 5px;
				text-align: right;
			}
			.reportselectionlabel{
				font-size: 12px;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini" align="center">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $qr_img_path = $row['qr_img_path']; ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Purchase Report</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Supplier:</b>&nbsp;<?php echo $cus_name[$cname]; ?></label><br/>
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="PurchaseReportMaster2.php" method="post" onSubmit="return checkval()">
					<?php } else { ?>
					<form action="PurchaseReportMaster2.php?db=<?php echo $db; ?>&emp_code=<?php echo $users_code; ?>" method="post" onSubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="min-width:100%;line-height:23px;">
							<?php if($exoption == "displaypage") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="26">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>"/>
									&ensp;&ensp;
										<!--<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="gname" id="gname" class="form-control select2" onchange="groupbycussel()">
											<option value="all">-All-</option>
											<?php
												/*foreach($grp_code as $gcode){
											?>
													<option <?php if($gname == $gcode) { echo 'selected'; } ?> value="<?php echo $gcode; ?>"><?php echo $grp_name[$gcode]; ?></option>
											<?php
												}*/
											?>
										</select>
									&ensp;&ensp;-->
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($cus_code as $vcode){
											?>
													<option <?php if($cname == $vcode) { echo 'selected'; } ?> value="<?php echo $vcode; ?>"><?php echo $cus_name[$vcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="iname" id="iname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($item_code as $icode){
											?>
													<option <?php if($iname == $icode) { echo 'selected'; } ?> value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option>
											<?php
												}
											?>
										</select>
									<br/>
										<?php
										// Initialize selected sectors
										$selected_sectors = $_POST['sectors'] ?? ['all'];

										// Ensure it's always an array
										if (!is_array($selected_sectors)) {
											$selected_sectors = [$selected_sectors];
										}
										?>
										<label class="reportselectionlabel">Vehicle</label>&nbsp;
										<select name="sectors[]" id="sectors[0]" class="form-control select2" style="width:180px;" multiple>
											<option value="all" <?php if(in_array("all", $selected_sectors)) echo "selected"; ?>>All</option>
											<?php foreach($sector_code as $scode) { ?>
												<option value="<?php echo $scode; ?>" <?php if(in_array($scode, $selected_sectors)) echo "selected"; ?>>
													<?php echo $sector_name[$scode]; ?>
												</option>
											<?php } ?>
										</select>

										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<div style="display:none;">
											<label class="reportselectionlabel">User</label>&nbsp;
										<select name="ucode" id="ucode" class="form-control select2" style="display:none;">
											<option value="all">-All-</option>
											<?php
												foreach($user_code as $ucodes){
											?>
													<option <?php if($_POST['ucode'] == $user_code[$ucodes]) { echo 'selected'; } ?> value="<?php echo $user_code[$ucodes]; ?>"><?php echo $user_name[$ucodes]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										</div>
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
							<?php }
							if(isset($_POST['submit']) == true){
								if($_POST['export'] != "exportexcel"){
									$bwtd_det_col = 0;
										
										if($prate_flag == 1 || $prate_flag == "1"){
											$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['code']] = $row['new_price']; }
										}

										$sbrh_col = ""; if($supbrh_flag == 1){ $sbrh_col = " a.supbrh_code as supbrh_code,"; }
										$sequence = "SELECT a.date as date, a.invoice as invoice, a.link_trnum as link_trnum, a.bookinvoice as bookinvoice, a.vendorcode as vendorcode,".$sbrh_col." a.jals as jals, a.totalweight as totalweight, a.emptyweight as emptyweight, a.itemcode as itemcode, a.birds as birds, a.netweight as netweight, a.itemprice as itemprice, a.totalamt as totalamt, a.tcdsper as tcdsper, a.tcdsamt as tcdsamt, a.roundoff as roundoff, a.finaltotal as finaltotal, a.balance as balance, a.amtinwords as amtinwords, a.flag as flag, a.active as active, a.authorization as authorization, a.warehouse as warehouse, a.tdflag as tdflag, a.packs as packs, a.cases as cases, a.price1 as price1, a.amount1 as amount1, a.pdflag as pdflag, a.drivercode as drivercode, a.vehiclecode as vehiclecode, a.narration as narration, a.discounttype as discounttype, a.discountvalue as discountvalue, a.taxtype as taxtype, a.taxvalue as taxvalue, a.discountamt as discountamt, a.taxamount as taxamount, a.taxcode as taxcode, a.discountcode as discountcode, a.remarks as remarks, a.addedemp as addedemp, a.addedtime as addedtime, b.name as customername FROM `pur_purchase` a, `main_contactdetails` b WHERE a.vendorcode = b.code AND a.date >= '$fdate' AND a.date <= '$tdate'";
										$flags = " AND a.active = '1' AND a.tdflag = '0' AND a.pdflag = '0' ORDER BY a.date,b.name,a.invoice ASC";
										$sql = $sequence."".$customer_filter."".$item_filter."".$sector_filter."".$user_filter."".$flags;
										$query = mysqli_query($conn,$sql); $link_trnums = $inv_cus_code = array();
										$sii_count = $slc_finaltotal = $sales = $slc_tcdsamt = $slc_roundoff = $sup_trnums = array();
										$rb_amt = $i = 0;
										while($row = mysqli_fetch_assoc($query)){
											$i = $i + 1; $sales[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['vendorcode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode']."@".$row['drivercode']."@".$row['addedemp']."@".$row['supbrh_code']."@".$row['packs']."@".$row['cases']."@".$row['price1']."@".$row['amount1'];

											if($sii_count[$row['invoice']] == "" || $sii_count[$row['invoice']] == NULL || $sii_count[$row['invoice']] == 0){
												$sii_count[$row['invoice']] = 1;
												if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){ $slc_tcdsamt[$row['invoice']] = 0.00; } else{ $slc_tcdsamt[$row['invoice']] = $row['tcdsamt']; }
												if($row['roundoff'] == "" || $row['roundoff'] == NULL){ $slc_roundoff[$row['invoice']] = 0.00; } else{ if(($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']){ $slc_roundoff[$row['invoice']] = $row['roundoff']; } else{ $slc_roundoff[$row['invoice']] = -1 *($row['roundoff']); } }
												$slc_finaltotal[$row['invoice']] = $row['finaltotal'];
											}
											else{
												$sii_count[$row['invoice']] = $sii_count[$row['invoice']] + 1;
											}
											$link_trnums[$row['link_trnum']] = $row['link_trnum'];
											$sup_trnums[$row['invoice']] = $row['invoice'];
										}
										//Sales
										if(sizeof($link_trnums) > 0){
											$tr_list = implode("','",$link_trnums);
											$sql = "SELECT * FROM `customer_sales` WHERE `invoice` IN ('$tr_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC";
											$query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
												$key = $row['link_trnum'];
												$inv_cus_code[$key] = $row['customercode'];
											}
										}
										//Supplier Add-ons
										$sacoa_code = $sacoa_name = $coa_amt = $coa_alist = array();
										if((int)$supaddon_flag == 1){
											if(sizeof($sup_trnums) > 0){
												$trno_list = implode("','",$sup_trnums);
												$sql = "SELECT * FROM `pur_supplier_addons` WHERE `trnum` IN ('$trno_list') AND `active` = '1' AND `dflag` = '0'";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['trnum']."@".$row['coa_code'];
													$coa_amt[$key] = round($row['coa_amt'],5);
													$coa_alist[$row['coa_code']] = $row['coa_code'];
												}
												if(sizeof($coa_alist) > 0){
													$coa_list = implode("','",$coa_alist);
													$sql = "SELECT * FROM `acc_coa` WHERE `code` IN ('$coa_list') AND `active` = '1' AND `spaof_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
													while($row = mysqli_fetch_assoc($query)){ $sacoa_code[$row['code']] = $row['code']; $sacoa_name[$row['code']] = $row['description']; }
												}
											
											}											
										}
										?>
									<thead class="thead2" style="background-color: #98fb98;">
										<?php
										$active_flag = 1;
										for($i = 1;$i <= $col_count;$i++){
											//echo "<br/>".$field_details[$i.":".$active_flag];
											if($field_details[$i.":".$active_flag] == "sl_flag"){ echo "<th>Sl&nbsp;No.</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<th>Date</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<th>Invoice</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<th>Book Invoice</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<th>Supplier</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<th>Branch</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<th>Customer</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<th>Item</th>"; $bwtd_det_col++; }
											else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<th>Box</th>"; }
											else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<th>Birds</th>"; }
											else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<th>T. Weight</th>"; }
											else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<th>E. Weight</th>"; }
											else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<th>N. Weight</th>"; }
											else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<th>Avg. Weight</th>"; }
											else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<th>Paper Rate</th>"; }
											else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<th>Price</th>"; }
											else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<th>TCS</th>"; }
											else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<th>Discount</th>"; }
											else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<th>Total Amount</th>"; }
											else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<th>Warehouse</th>"; }
											else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<th>Remarks</th>"; }
											else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<th>Vehicle</th>"; }
											else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<th>Driver</th>"; }
											else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<th>Purchase</th>"; }
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<th>Payment</th>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<th>Running Balance</th>"; }
											else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<th>User</th>"; }
											else if($field_details[$i.":".$active_flag] == "packs_flag"){ echo "<th>Packs</th>"; }
											else if($field_details[$i.":".$active_flag] == "cases_flag"){ echo "<th>Cases</th>"; }
											else if($field_details[$i.":".$active_flag] == "price1_flag"){ echo "<th>Base Price</th>"; }
											else if($field_details[$i.":".$active_flag] == "amount1_flag"){ echo "<th>Base mount</th>"; }
											else if($field_details[$i.":".$active_flag] == "supaddon_flag"){
												foreach($sacoa_code as $scode){
													echo "<th>".$sacoa_name[$scode]."</th>";
												}
											}
											else{ }
										}
										
										?>
									</thead>
									<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<?php
										$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = $tot_packs = $tot_cases = $tot_amount1 = 0; $exi_inv = "";
										$tbcount = $tjcount = $tncount = $twcount = $tecount = $tdcount = $ttcount = $tacount = 0; $tcoa_amt = array(); $sl = 1;
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$date_asc = date('Y-m-d', $currentDate);
											$ccount = sizeof($sales);
											for($j = 0;$j <=$ccount;$j++){
												if($sales[$date_asc."@".$j] != ""){
													$sales_details = explode("@",$sales[$date_asc."@".$j]);

													if(empty($inv_cus_code[$sales_details[1]]) || $inv_cus_code[$sales_details[1]] == ""){ $cname2 = ""; }
													else{ $cname2 = $cus_name[$inv_cus_code[$sales_details[1]]]; }
													
													$d_packs = (float)$sales_details[25];
													$d_cases = (float)$sales_details[26];
													$d_price1 = (float)$sales_details[27];
													$d_amount1 = (float)$sales_details[28];

													$tot_packs += (float)$d_packs;
													$tot_cases += (float)$d_cases;
													$tot_amount1 += (float)$d_amount1;

													if($sales_details[10] == 1){
														echo "<tr style='background-color:red;'>";
													}else{
														echo "<tr>";
													}
													
													if($exi_inv != $sales_details[1]){
														$exi_inv = $sales_details[1];
														if(number_format_ind($slc_finaltotal[$sales_details[1]]) == number_format_ind($rb_amt)){
															$rb_amt = 0;
														}
														else{
															$rb_amt = $rb_amt + $slc_finaltotal[$sales_details[1]];
														}
														$ft_tcds = $ft_tcds + $slc_tcdsamt[$sales_details[1]];
														$ft_roundoff = $ft_roundoff + $slc_roundoff[$sales_details[1]];
														$fst_famt = $fst_famt + $slc_finaltotal[$sales_details[1]];
														
														for($i = 1;$i <= $col_count;$i++){
															if($field_details[$i.":".$active_flag] == "sl_flag"){ echo "<td>".$sl++."</td>"; }
															else if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$sales_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$sales_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$sales_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'>".$sbrh_name[$sales_details[24]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'>".$cname2."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$sales_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".number_format_ind($sales_details[4])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".number_format_ind($sales_details[8])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($sales_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($sales_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($sales_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){
																if(!empty($sales_details[9]) && $sales_details[9] > 0 && !empty($sales_details[8]) && $sales_details[8] > 0){
																	echo "<td>".number_format_ind($sales_details[9] / $sales_details[8])."</td>";
																}
																else{
																	echo "<td>".number_format_ind(0)."</td>";
																}
															}
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$sales_details[7]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$sales_details[10]."</td>";
																
																 }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$sales_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$sales_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".$sales_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".$sales_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$sales_details[18]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td>".$user_name[$sales_details[23]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td>".number_format_ind($slc_tcdsamt[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($slc_finaltotal[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'></td>"; }
															else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($rb_amt)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "packs_flag"){ echo "<td>".number_format_ind($d_packs)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cases_flag"){ echo "<td>".number_format_ind($d_cases)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price1_flag"){ echo "<td>".number_format_ind($d_price1)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "amount1_flag"){ echo "<td>".number_format_ind($d_amount1)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "supaddon_flag"){
																foreach($sacoa_code as $scode){
																	$key = $sales_details[1]."@".$scode;
																	if(empty($coa_amt[$key]) || $coa_amt[$key] == ""){ $coa_amt[$key] = 0; }
																	echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($coa_amt[$key])."</td>";
																	$tcoa_amt[$scode] += (float)$coa_amt[$key];
																}
															}
															else{ }
														}
													}
													else{
														for($i = 1;$i <= $col_count;$i++){
															if($field_details[$i.":".$active_flag] == "sl_flag"){ echo "<td>".$sl++."</td>"; }
															else if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$sales_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$sales_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$sales_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'>".$sbrh_name[$sales_details[24]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'>".$cname2."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$sales_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".number_format_ind($sales_details[4])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".number_format_ind($sales_details[8])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($sales_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($sales_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($sales_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){
																if(!empty($sales_details[9]) && $sales_details[9] > 0 && !empty($sales_details[8]) && $sales_details[8] > 0){
																	echo "<td>".number_format_ind($sales_details[9] / $sales_details[8])."</td>";
																}
																else{
																	echo "<td>".number_format_ind(0)."</td>";
																}
															}
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$sales_details[7]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){
																if($sales_details[10] == 1){
																	 echo "<td style='color:red;'>".$sales_details[10]."</td>";
																}else{
																	 echo "<td>".$sales_details[10]."</td>";
																}
																 }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$sales_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$sales_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".$sales_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".$sales_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$sales_details[18]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td>".$user_name[$sales_details[23]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
															else if($field_details[$i.":".$active_flag] == "packs_flag"){ echo "<td>".number_format_ind($d_packs)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cases_flag"){ echo "<td>".number_format_ind($d_cases)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price1_flag"){ echo "<td>".number_format_ind($d_price1)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "amount1_flag"){ echo "<td>".number_format_ind($d_amount1)."</td>"; }
															else{ }
														}
													}
													
														$tbcount = $tbcount + (float)$sales_details[8];
														$tjcount = $tjcount + (float)$sales_details[4];
														$tncount = $tncount + (float)$sales_details[9];
														$twcount = $twcount + (float)$sales_details[5];
														$tecount = $tecount + (float)$sales_details[6];
														$tdcount = $tdcount + (float)$sales_details[18];
														$ttcount = $ttcount + (float)$sales_details[19];
														$tacount = $tacount + (float)$sales_details[11];
														
													echo "</tr>";
												}
												else{ }
											}
										}
									?>
									</tbody>
									<thead>
										<tr class="foottr" style="background-color: #98fb98;">
											<td colspan="<?php echo $bwtd_det_col; ?>" align="center"><b>Total</b></td>
											<?php
											for($i = 1;$i <= $col_count;$i++){
												if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tjcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tbcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($twcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tecount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tncount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "aweight_flag"){
													if($tncount > 0 && $tbcount > 0){
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tncount / $tbcount)."</td>";
													}
													else{
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind(0)."</td>";
													}
													
												}
												else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "price_flag"){
													if($fst_famt > 0 && $tncount > 0){
														echo "<td>".number_format_ind($fst_famt / $tncount)."</td>";
													}
													else{
														echo "<td>".number_format_ind(0)."</td>";
													}
													
												}
												else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ft_tcds)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".number_format_ind($tdcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tacount + $fct_famt + $fdt_famt + $frt_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($fst_famt + $fdt_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($frt_famt + $fct_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "packs_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_packs)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "cases_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_cases)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "price1_flag"){ echo "<td style='padding: 0 5px;text-align:right;'></td>"; }
												else if($field_details[$i.":".$active_flag] == "amount1_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_amount1)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "supaddon_flag"){
													foreach($sacoa_code as $scode){
														if(empty($tcoa_amt[$scode]) || $tcoa_amt[$scode] == ""){ $tcoa_amt[$scode] = 0; }
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tcoa_amt[$scode])."</td>";
													}
												}
												else{ }
											}
											?>
										</tr>
									</thead>
							<?php
								}
							}
							?>
						</table>
					</form>
				</div>
		</section>
		<?php if($vsign_flag == 1 || $csign_flag == 1 || $qr_img_flag == 1 || $note_flag == 1){ ?>
		<div align="center">
			<table style="width:90%">
				<tr>
					<td colspan="2"><?php if($note_flag == 1){ echo '<footer align="center" style="margin-top:50px;">'.$disclaimer.'</footer>'; } ?></td>
				</tr>
				<tr>
					<td><?php if($vsign_flag == 1){ echo '<center><br/><br/>'.$company_name.'<br/><br/>........................................</center>'; } ?></td>
					<td><?php if($csign_flag == 1){ echo '<center><br/><br/>'.$cus_name[$cname].'<br/><br/>........................................</center>'; } ?></td>
				</tr>
				<?php
					if($qr_img_flag == 1){
				?>
					<tr>
						<td colspan="2"><center><img src="../<?php echo $qr_img_path; ?>" height="150px" /></center></td>
					</tr>
				<?php
					}
				?>
			</table>
		</div>
		<?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
