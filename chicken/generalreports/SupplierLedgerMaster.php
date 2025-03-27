<?php
	//SupplierLedgerMaster.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	
	session_start();
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db']; } else{ $db = ''; }
	
	if($db == ''){
		include "../newConfig.php";
		include "header_head.php"; 
		include "../broiler_check_tableavailability.php";
		include "number_format_ind.php"; 
		$dbname = $_SESSION['dbase'];
	}
	else{
		include "APIconfig.php";
		include "header_head.php";
		include "../broiler_check_tableavailability.php";
		include "number_format_ind.php";
		$dbname = $db;
	}
	//if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php"; } else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }

	$sql='SHOW COLUMNS FROM `master_reportfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("supbrh_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `supbrh_flag` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Supplier Branch Flag'"; mysqli_query($conn,$sql); }
	
	/*Check for Table Availability*/
	$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
    if(in_array("chicken_supplier_branch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_supplier_branch LIKE poulso6_admin_chickenmaster.chicken_supplier_branch;"; mysqli_query($conn,$sql1); }
    
	$cid = $_GET['cid'];
	$today = date("Y-m-d");
	
	if(isset($_POST['submit']) == true){
		$fromdate = $_POST['fromdate'];
		$todate = $_POST['todate'];
		$sname = $_POST['sname'];
		$sectors = $_POST['sectors'];
	}
	else{
		$fromdate = $todate = $today;
		$sname = "select";
		$sectors = "all";
	}
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sup_name[$row['code']] = $row['name']; $sup_code[$row['code']] = $row['code']; $cus_mobile[$row['code']] = $row['mobileno']; $cus_group[$row['code']] = $row['groupcode']; $obdate[$row['code']] = $row['obdate']; $obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt']; }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

	$sql = "SELECT * FROM `chicken_supplier_branch` WHERE `dflag` = '0' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sbrh_code = $sbrh_name =  $bsup_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sbrh_code[$row['code']] = $row['code']; $sbrh_name[$row['code']] = $row['description']; $bsup_name[$row['code']] = $row['sup_code']; }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_code[$row['code']] = $row['code']; }

	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `ctype` IN ('Cash','Bank') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $coaname[$row['code']] = $row['description']; $coacode[$row['code']] = $row['code']; }

	$sql='SHOW COLUMNS FROM `master_reportfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("purcus_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `purcus_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Customer Name' AFTER `vendor_flag`"; mysqli_query($conn,$sql); }
	
	$sql = "SELECT * FROM `master_reportfields` WHERE `code` = '$cid' AND `active` = '1'";
	$query = mysqli_query($conn,$sql); $supbrh_flag = 0;
	while($row = mysqli_fetch_assoc($query)){
		$type = "type";
		$code = "code";
		$pattern = "pattern";
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
		$field_details[$row['prate_flag']] = "prate_flag";
		$field_details[$row['price_flag']] = "price_flag";
		$field_details[$row['tcds_flag']] = "tcds_flag";
		$field_details[$row['discount_flag']] = "discount_flag";
		$field_details[$row['tamt_flag']] = "tamt_flag";
		$field_details[$row['sector_flag']] = "sector_flag";
		$field_details[$row['remarks_flag']] = "remarks_flag";
		$field_details[$row['vehicle_flag']] = "vehicle_flag";
		$field_details[$row['driver_flag']] = "driver_flag";
		//$field_details[$row['denom_flag']] = "denom_flag";
		$field_details[$row['cr_flag']] = "cr_flag";
		$field_details[$row['dr_flag']] = "dr_flag";
		$field_details[$row['rb_flag']] = "rb_flag";
		$note_flag = $row['note_flag'];
		$note_code = $row['note_code'];
		$vsign_flag = $row['vsign_flag'];
		$csign_flag = $row['csign_flag'];
		$qr_img_flag = $row['qr_img_flag'];
		$col_count = $row['count'];
	}
	$sql = "SELECT * FROM `main_disclaimer` WHERE `code` = '$note_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $disclaimer = $row['note']; }

	if($prate_flag == 1 || $prate_flag == "1"){
		$fdate = date("Y-m-d",strtotime($fromdate)); $tdate = date("Y-m-d",strtotime($todate));
		$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['cgroup']] = $row['new_price']; }
	}

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; $sector_code[$row['code']] = $row['code']; }
	
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; if($excel_type == "exportexcel"){ $exoption = "displaypage"; } else{ $exoption = $_POST['export']; } } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_sname = $_POST['sname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_sname =  "all";
	}
	$url = "../PHPExcel/Examples/SupplierLedgerMasterReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&sname=".$exl_sname."&cid=".$cid;
	
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
			body{
				font-size: 12px;
				color: black;
				overflow: auto;
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
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $company_name = $row['sname']; $qr_img_path = $row['qr_img_path']; ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Supplier Ledger</h3>
						<?php
							if($sname == "all" || $sname == "select" || $sname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Supplier:</b>&nbsp;<?php echo $sup_name[$sname]; ?></label><br/>
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
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
				<form action="SupplierLedgerMaster.php?cid=<?php echo $cid; ?>" method="post"  onsubmit="return checkval()">
					<?php } else { ?>
					<form action="SupplierLedgerMaster.php?db=<?php echo $db; ?>&cid=<?php echo $cid; ?>" method="post"  onsubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="27">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="sname" id="sname" class="form-control select2" style="width:auto;">
											<option value="select">-select-</option>
											<?php
											foreach($sup_code as $cc){
											?>
													<option <?php if($sname == $cc) { echo 'selected'; } ?> value="<?php echo $cc; ?>"><?php echo $sup_name[$cc]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="sectors" id="sectors" class="form-control select2" style="width:auto;">
											<option value="all">-All-</option>
											<?php
											foreach($sector_code as $wcode){
											?>
													<option <?php if($sectors == $wcode) { echo 'selected'; } ?> value="<?php echo $wcode; ?>"><?php echo $sector_name[$wcode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
							<?php }
							if(isset($_POST['submit']) == true){
								if($_POST['export'] != "exportexcel"){
									$prev_bal_col = $item_det_col = $bwtd_det_col = $grnd_tot_col = $clsb_tot_col = 0;
									?>
									<thead class="thead2" style="background-color: #98fb98;">
										<?php
										$active_flag = 1;
										for($i = 1;$i <= $col_count;$i++){
											//if(empty($field_details[$i.":".$active_flag]) || $field_details[$i.":".$active_flag] == ""){ } else{ echo "<br/>".$field_details[$i.":".$active_flag]."@".$i; }
											if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<th>Date</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<th>Invoice</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<th>Book Invoice</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<th>Supplier</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<th>Branch</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<th>Customer</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<th>Item</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<th>Jals</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<th>Birds</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<th>T. Weight</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<th>E. Weight</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<th>N. Weight</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<th>Avg. Weight</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<th>Paper Rate</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<th>Price</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<th>TCS</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<th>Discount</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<th>Total Amount</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<th>Warehouse</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<th>Remarks</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<th>Vehicle</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<th>Driver</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<th>Purchase</th>"; }
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<th>Payments</th>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<th>Running Balance</th>"; }
											else{ }
										}
										
										?>
									</thead>
									<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<?php
										$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
										$tdate = date("Y-m-d",strtotime($_POST['todate']));
										$sup_names = $_POST['sname'];
										$sectors = $_POST['sectors']; if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `warehouse` = '$sectors'"; }
										
										$ob_purchases = $ob_payment = $ob_ccn = $ob_cdn = $ob_smortality = $rb_amt = $ob_cramt = $ob_dramt = $ob_rcv = $ob_pid = 0;
										if($count57 > 0){
											$obsql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fdate' AND `vendorcode` = '$sup_names'".$sector_filter." AND `active` = '1' ORDER BY `date`,`invoice` ASC";
											$obquery = mysqli_query($conn,$obsql); $old_inv = "";
											while($obrow = mysqli_fetch_assoc($obquery)){
												if($old_inv != $obrow['invoice']){
													$ob_purchases = $ob_purchases + $obrow['finaltotal'];
													$old_inv = $obrow['invoice'];
												}
											}
										}
										if($count40 > 0){
											$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$sup_names'".$sector_filter." AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
											$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = $ob_returns + $obrow['amount']; }
										}
										if($count44 > 0){
											$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fdate' AND `ccode` = '$sup_names' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
											$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_smortality += (float)$obrow['amount']; }
										}
										if($count56 > 0){
											$obsql = "SELECT * FROM `pur_payments` WHERE `date` < '$fdate' AND `ccode` = '$sup_names'".$sector_filter." AND `active` = '1'";
											$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_payment = $ob_payment + $obrow['amount']; }
										}
										if($count32 > 0){
											$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fdate' AND `ccode` = '$sup_names'".$sector_filter." AND `mode` IN ('SCN','SDN') AND `active` = '1'";
											$obquery = mysqli_query($conn,$obsql);
											while($obrow = mysqli_fetch_assoc($obquery)){ if($obrow['mode'] == "SCN"){ $ob_ccn = $ob_ccn + $obrow['amount']; } else { $ob_cdn = $ob_cdn + $obrow['amount']; } }
										}
										if($obtype[$sup_names] == "Cr"){
											$ob_cramt = $obamt[$sup_names];
										}
										else{
											$ob_dramt = $obamt[$sup_names];
										}
										$ob_rcv = $ob_purchases + $ob_ccn + $ob_cramt;
										$ob_pid = $ob_payment + $ob_returns + $ob_smortality + $ob_cdn + $ob_dramt;
										
										if($ob_rcv >= $ob_pid){
											echo "<tr>";
											echo "<td colspan='".$prev_bal_col."' style='font-weight:bold;'>Previous Balance</td>";
											for($i = 1;$i <= $item_det_col;$i++){ echo "<td></td>"; }
											
											for($i = 1;$i <= $col_count;$i++){
												if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='font-weight:bold;'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='font-weight:bold;'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
											}
											echo "</tr>";
											$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
											$ob_rev_amt = $ob_rcv - $ob_pid;
											$ob_pid_amt = 0;
										}
										else{
											echo "<tr>";
											echo "<td colspan='".$prev_bal_col."' style='font-weight:bold;'>Previous Balance</td>";
											for($i = 1;$i <= $item_det_col;$i++){ echo "<td></td>"; }
											
											for($i = 1;$i <= $col_count;$i++){
												if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='font-weight:bold;'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='font-weight:bold;'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
											}
											echo "</tr>";
											$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
											$ob_pid_amt = $ob_pid - $ob_rcv;
											$ob_rev_amt = 0;
										}
										
										//purchases
										$sii_count = array();$purchases = array();
										$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vendorcode` = '$sname'".$sector_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
										$query = mysqli_query($conn,$sql); $i = 0; $link_trnums = $inv_cus_code = array();
										while($row = mysqli_fetch_assoc($query)){
											$i = $i + 1; $purchases[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['vendorcode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode']."@".$row['drivercode']."@".$row['supbrh_code'];
											
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
										
										//Payments
										$payments = array();
										$paysql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$sname'".$sector_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
										$payquery = mysqli_query($conn,$paysql); $i = 0;
										while($row = mysqli_fetch_assoc($payquery)){
											$i = $i + 1; $payments[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['type']."@".$row['rcode']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
										}
									
										//Returns
										$returns = array();
										$rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$sname'".$sector_filter." AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
										$rtnquery = mysqli_query($conn,$rtnsql); $i = 0;
										while($row = mysqli_fetch_assoc($rtnquery)){
											$avgwt = 0;
											if($row['birds'] != "" || $row['birds'] != 0 || $row['birds'] != "0.00"){ $avgwt = $row['quantity'] / $row['birds']; } else{ $avgwt = 0; }
											$i = $i + 1;  $returns[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['inv_trnum']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$avgwt."@".$row['price']."@".$row['amount']."@".$row['warehouse'];
										}
										
										//Mortality
										$smortalities  = array();
										if($count13 > 0){
											$mortsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$sname' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
											$i = 0; $mortquery = mysqli_query($conn,$mortsql);
											while($row = mysqli_fetch_assoc($mortquery)){
												$i = $i + 1;  $smortalities[$row['date']."@".$i] = $row['code']."@".$row['mtype']."@".$row['date']."@".$row['ccode']."@".$row['invoice']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['remarks'];
											}
										}

										//CRDR NOTE
										$ccns = array();$cdns = array();
										$crdrseq = "SELECT * FROM `main_crdrnote` WHERE `mode` IN ('SDN','SCN') AND `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = $sector_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
										$crdrname = $_POST['sname']; $i = $j = 0; if($crdrname == "all") { $crdrnames = ""; } else { $crdrnames = " AND `ccode` = '$sname'"; } $crdrsql = $crdrseq."".$crdrnames."".$flags; $crdrquery = mysqli_query($conn,$crdrsql);
										while($row = mysqli_fetch_assoc($crdrquery)){
											if($row['mode'] == "SCN"){
												$i = $i + 1; $ccns[$row['date']."@".$i] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
											}
											else if($row['mode'] == "SDN"){
												$j = $j + 1; $cdns[$row['date']."@".$j] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
											}
											else{ }
										}
										
										$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = 0; $exi_inv = "";
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$date_asc = date('Y-m-d', $currentDate);
											$ccount = sizeof($purchases); 
											for($j = 0;$j <=$ccount;$j++){
												if($purchases[$date_asc."@".$j] != ""){
													$purchases_details = explode("@",$purchases[$date_asc."@".$j]);

													if(empty($inv_cus_code[$purchases_details[1]]) || $inv_cus_code[$purchases_details[1]] == ""){ $cname = ""; }
													else{ $cname = $cus_name[$inv_cus_code[$purchases_details[1]]]; }
													
													echo "<tr>";
													if($exi_inv != $purchases_details[1]){
														$exi_inv = $purchases_details[1];
														if(number_format_ind($slc_finaltotal[$purchases_details[1]]) == number_format_ind($rb_amt)){
															$rb_amt = 0;
														}
														else{
															$rb_amt = $rb_amt + $slc_finaltotal[$purchases_details[1]];
														}
														$ft_tcds = $ft_tcds + (float)$slc_tcdsamt[$purchases_details[1]];
														$ft_roundoff = $ft_roundoff + (float)$slc_roundoff[$purchases_details[1]];
														$fst_famt = $fst_famt + (float)$slc_finaltotal[$purchases_details[1]];
														
														for($i = 1;$i <= $col_count;$i++){
															if($purchases_details[8] != 0 && $purchases_details[8] != 0.00 && $purchases_details[8] != ''){

																if((float)$purchases_details[8] != 0){
																	$avg_wt = (float)$purchases_details[9] / (float)$purchases_details[8];
																}else{
																	$avg_wt = 0;
																}
																
															}else{
																$avg_wt = 0;
															}
															if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($purchases_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$purchases_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$purchases_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$sup_name[$purchases_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'>".$sbrh_name[$purchases_details[23]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'>".$cname."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$purchases_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".number_format_ind($purchases_details[4])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".str_replace(".00","",number_format_ind($purchases_details[8]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($purchases_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($purchases_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($purchases_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td>".number_format_ind($avg_wt)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $purchases_details[0]."@".$cus_group[$purchases_details[3]]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$purchases_details[10]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($purchases_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$purchases_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$purchases_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".$purchases_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".$purchases_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$purchases_details[18]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($slc_tcdsamt[$purchases_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($slc_finaltotal[$purchases_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'></td>"; }
															else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($rb_amt)."</td>"; }
															else{ }
														}
													}
													else{
														for($i = 1;$i <= $col_count;$i++){
															if((float)$purchases_details[8] != 0){
																$avg_wt = (float)$purchases_details[9] / (float)$purchases_details[8];
															}else{
																$avg_wt = 0;
															}
															if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($purchases_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$purchases_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$purchases_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$sup_name[$purchases_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'>".$sbrh_name[$purchases_details[23]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'>".$cname."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$purchases_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".number_format_ind($purchases_details[4])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".str_replace(".00","",number_format_ind($purchases_details[8]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($purchases_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($purchases_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($purchases_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td>".number_format_ind($avg_wt)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $purchases_details[0]."@".$cus_group[$purchases_details[3]]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$purchases_details[10]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($purchases_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$purchases_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$purchases_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".$purchases_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".$purchases_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$purchases_details[18]."</td>"; }
															else{ }
														}
													}
													
														$tbcount += (float)$purchases_details[8];
														$tjcount += (float)$purchases_details[4];
														$tncount += (float)$purchases_details[9];
														$twcount += (float)$purchases_details[5];
														$tecount += (float)$purchases_details[6];
														$tdcount += (float)$purchases_details[18];
														$ttcount += (float)$purchases_details[19];
														$tacount += (float)$purchases_details[11];
														
													echo "</tr>";
												}
												else{ }
											}
											$ccount = sizeof($payments); 
											for($j = 0;$j <=$ccount;$j++){
												if($payments[$date_asc."@".$j] != ""){
													$payments_details = explode("@",$payments[$date_asc."@".$j]);
													if(number_format_ind($payments_details[10]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = (float)$rb_amt - (float)$payments_details[10];
													}
													$frt_famt = (float)$frt_famt + (float)$payments_details[10];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($payments_details[1]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$payments_details[0]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$payments_details[3]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$sup_name[$payments_details[2]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$coaname[$payments_details[5]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($payments_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$payments_details[13]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$payments_details[14]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td>".number_format_ind($payments_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
												}
												else{ }
											}
											$ccount = sizeof($returns); 
											for($j = 0;$j <=$ccount;$j++){
												if($returns[$date_asc."@".$j] != ""){
													$return_details = explode("@",$returns[$date_asc."@".$j]);
													if(number_format_ind($return_details[10]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = (float)$rb_amt - (float)$return_details[10];
													}
													$frt_famt = (float)$frt_famt + (float)$return_details[10];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($return_details[1]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$return_details[0]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$return_details[3]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$return_details[2]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$return_details[4]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[5])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='text-align:right;'>".str_replace(".00","",number_format_ind($return_details[6]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[8])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[9])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($return_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$return_details[11]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td>".number_format_ind($return_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
													$tjcount = $tjcount - (float)$return_details[5];
													$tbcount = $tbcount - (float)$return_details[6];
													$tncount = $tncount - (float)$return_details[7];
												}
												else{ }
											}
											$ccount = sizeof($smortalities); 
											for($j = 0;$j <=$ccount;$j++){
												if($smortalities[$date_asc."@".$j] != ""){
													$smort_details = explode("@",$smortalities[$date_asc."@".$j]);
													if(number_format_ind($smort_details[9]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = (float)$rb_amt - (float)$smort_details[9];
													}
													$frt_famt = (float)$frt_famt + (float)$smort_details[9];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($smort_details[2]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$smort_details[0]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$smort_details[4]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$smort_details[3]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$smort_details[5]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='text-align:right;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='text-align:right;'>".str_replace(".00","",number_format_ind($smort_details[6]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($smort_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td style='text-align:right;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td style='text-align:right;'>".number_format_ind($smort_details[8])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($smort_details[9])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td>".$smort_details[10]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td>".number_format_ind($smort_details[9])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
													$tbcount = $tbcount - (float)$smort_details[6];
													$tncount = $tncount - (float)$smort_details[7];
												}
												else{ }
											}
											$ccount = sizeof($ccns); 
											for($j = 0;$j <=$ccount;$j++){
												if($ccns[$date_asc."@".$j] != ""){
													$ccns_details = explode("@",$ccns[$date_asc."@".$j]);
													if(number_format_ind($ccns_details[7]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = $rb_amt + (float)$ccns_details[7];
													}
													$fct_famt = $fct_famt + (float)$ccns_details[7];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($ccns_details[2]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$ccns_details[1]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$ccns_details[4]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$sup_name[$ccns_details[3]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td>Credit Note</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($ccns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$ccns_details[11]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$ccns_details[12]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td>".number_format_ind($ccns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
												}
												else{ }
											}
											$ccount = sizeof($cdns); 
											for($j = 0;$j <=$ccount;$j++){
												if($cdns[$date_asc."@".$j] != ""){
													$cdns_details = explode("@",$cdns[$date_asc."@".$j]);
													if(number_format_ind($cdns_details[7]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = $rb_amt - (float)$cdns_details[7];
													}
													$fdt_famt = $fdt_famt + (float)$cdns_details[7];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($cdns_details[2]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$cdns_details[1]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$cdns_details[4]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$sup_name[$cdns_details[3]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "supbrh_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "purcus_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td>Debit Note</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($cdns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$cdns_details[11]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$cdns_details[12]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td>".number_format_ind($cdns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
												}
												else{ }
											}
										}
									?>
									</tbody>
									<thead>
										<tr class="foottr" style="background-color: #98fb98;">
											<td colspan="<?php echo $bwtd_det_col; ?>" align="center"><b>Between Days Total</b></td>
											<?php
											for($i = 1;$i <= $col_count;$i++){
												if((float)$tbcount != 0){
													$avg_wt = (float)$tncount / (float)$tbcount;
												}else{
													$avg_wt = 0;
												}
												if((float)$tncount != 0){
													$price2 = (float)$fst_famt / (float)$tncount;
												}else{
													$price2 = 0;
												}
												if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tjcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".str_replace(".00","",number_format_ind($tbcount))."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($twcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tecount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tncount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($avg_wt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".number_format_ind($price2)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ft_tcds)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".number_format_ind($tdcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tacount + $fdt_famt + $fct_famt + $frt_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($fst_famt + $fct_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($frt_famt + $fdt_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
												else{ }
											}
											?>
										</tr>
										<tr class="foottr" style="background-color: #98fb98;">
											<td colspan="<?php echo $grnd_tot_col; ?>" align="center"><b>Grand Total</b></td>
											<?php
											for($i = 1;$i <= $col_count;$i++){
												if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind(($fst_famt + $fct_famt) + $ob_rev_amt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind(($frt_famt + $fdt_famt) + $ob_pid_amt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
												else{ }
											}
											?>
										</tr>
										<tr class="foottr" style="background-color: #98fb98;">
											<td colspan="<?php echo $clsb_tot_col; ?>" align="center"><b>Closing Balance</b></td>
											<?php
												if(number_format_ind(($fst_famt + $fct_famt) + $ob_rev_amt) == number_format_ind(($frt_famt + $fdt_famt) + $ob_pid_amt)){
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td>0.00</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
														else{ }
													}
												}
												else if((($fst_famt + $fct_famt) + $ob_rev_amt) > (($frt_famt + $fdt_famt) + $ob_pid_amt)){
													$famt = (($fst_famt + $fct_famt) + $ob_rev_amt) - (($frt_famt + $fdt_famt) + $ob_pid_amt);
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
														else{ }
													}
												}
												else {
													$famt = (($fst_famt + $fct_famt) + $ob_rev_amt) - (($frt_famt + $fdt_famt) + $ob_pid_amt);
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
														else{ }
													}
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
					<td><?php if($csign_flag == 1){ echo '<center><br/><br/>'.$sup_name[$sname].'<br/><br/>........................................</center>'; } ?></td>
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
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("sname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select Supplier ..!");
					return false;
				}
				else {
					return true;
				}
			}
		</script>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
