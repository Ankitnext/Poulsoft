<?php
	//CustomerLedgerMaster_tn.php
	$requested_data = json_decode(file_get_contents('php://input'),true);

	if(!isset($_SESSION)){ session_start(); }
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] =  $_GET['db']; } else { $db = ''; }
	if($db == ''){
		include "../newConfig.php";
		include "number_format_ind.php"; 
		include "header_head.php"; 
	}
	else{
		//include "../newConfig.php";
		include "APIconfig.php";
		include "number_format_ind.php";
		include "header_head_new.php";
	}
	
	include "../broiler_check_tableavailability.php";

	$cid = $_GET['cid'];
	if($cid == ""){
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
		$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '%CustomerLedgerMaster_tn.php%' AND `activate` = '1'";
		$query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }
	}
	$today = date("Y-m-d");
	
	if(isset($_POST['submit']) == true){
		$fromdate = $_POST['fromdate'];
		$todate = $_POST['todate'];
		$cname = $_POST['cname'];
	}
	else{
		$fromdate = $todate = $today;
		$cname = "select";
	}
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; $cus_code[$row['code']] = $row['code']; $cus_mobile[$row['code']] = $row['mobileno']; $cus_group[$row['code']] = $row['groupcode']; $obdate[$row['code']] = $row['obdate']; $obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt']; }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_code[$row['code']] = $row['code']; }

	$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `ctype` IN ('Cash','Bank') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $coaname[$row['code']] = $row['description']; $coacode[$row['code']] = $row['code']; }

	$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $coaname[$row['code']] = $row['description']; }

    
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '%CustomerLedgerMaster_tn.php%' AND `field_function` LIKE 'Display Avg Price in Customer Ledger'";
	$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $avgprc_flag = 0;
	if($count > 0){
		while($row = mysqli_fetch_assoc($query)){
			$avgprc_flag = $row['flag'];
		}
	} else{
		$sql = "INSERT INTO `extra_access`(field_name,field_function,user_access,flag) VALUES('CustomerLedgerMaster_tn.php','Display Avg Price in Customer Ledger','all','1')";
		$query = mysqli_query($conn,$sql);
		$avgprc_flag = 1;
	}
	if($avgprc_flag == ""){ $avgprc_flag = 0; }


	$sql='SHOW COLUMNS FROM `master_reportfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("purcus_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `purcus_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Customer Name' AFTER `vendor_flag`"; mysqli_query($conn,$sql); }
	if(in_array("salesup_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `salesup_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Supplier Name' AFTER `purcus_flag`"; mysqli_query($conn,$sql); }
	
	$sql = "SELECT * FROM `master_reportfields` WHERE `code` = '$cid' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$type = "type";
		$code = "code";
		$pattern = "pattern";
		$field_details[$row['date_flag']] = "date_flag";
		$field_details[$row['inv_flag']] = "inv_flag";
		$field_details[$row['binv_flag']] = "binv_flag";
		$field_details[$row['vendor_flag']] = "vendor_flag";
		$field_details[$row['salesup_flag']] = "salesup_flag";
		$field_details[$row['item_flag']] = "item_flag";
		$field_details[$row['jals_flag']] = "jals_flag";
		$field_details[$row['birds_flag']] = "birds_flag";
		$field_details[$row['tweight_flag']] = "tweight_flag";
		$field_details[$row['eweight_flag']] = "eweight_flag";
		$field_details[$row['sent_weight']] = "sentwt_flag";
		$field_details[$row['mort_weight']] = "mortwt_flag";
		$field_details[$row['nweight_flag']] = "nweight_flag";
		$field_details[$row['aweight_flag']] = "aweight_flag";
		$field_details[$row['sweight_flag']] = "sweight_flag";
		$field_details[$row['prate_flag']] = "prate_flag";
		$pr1 = explode(":", $row['prate_flag']); $prate_flag = $pr1[1];
		$field_details[$row['price_flag']] = "price_flag";
		$field_details[$row['actual_price_flag']] = "actual_price_flag";
		$field_details[$row['discount_price_flag']] = "discount_price_flag";
		$field_details[$row['freightamt_flag']] = "freightamt_flag";
		//echo "<br/>".$row['freightamt_flag']."-".$row['jfreight_flag'];
		$field_details[$row['tcds_flag']] = "tcds_flag";
		$field_details[$row['discount_flag']] = "discount_flag";
		$field_details[$row['samt_flag']] = "samt_flag"; //echo "<br/>".$row['samt_flag'];
		$field_details[$row['tamt_flag']] = "tamt_flag";
		$field_details[$row['jfreight_flag']] = "jfreight_flag";
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

	$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'CustomerLedgerMaster_tn.php' AND `field_function` LIKE 'Display Final total Avg. Price' AND `user_access` LIKE 'all'";
	$query = mysqli_query($conn,$sql); $cnt = mysqli_num_rows($query); if($cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dtap_flag = $row['flag']; } } else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`field_value`,`user_access`,`flag`) VALUES ('CustomerLedgerMaster_tn.php','Display Final total Avg. Price',NULL,'all','1');"; mysqli_query($conn,$sql); }
	if($dtap_flag == ""){ $dtap_flag = 0; }

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
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname =  "all";
	}
	$url = "../PHPExcel/Examples/CustomerLedgerMasterReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&cname=".$exl_cname."&cid=".$cid;
	
	$sales_text_color = "color:blue";
	$receipt_text_color = "color:green";
	$balance_text_color = "color:red";
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
				font-size: 15px;
				font-weight: bold;
				color: black;
			}
			.thead2,.tbody1 {
				font-size: 15px;
				font-weight: bold;
				padding: 1px;
				color: black;
			}
			.formcontrol {
				font-size: 15px;
				font-weight: bold;
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
				font-size: 15px;
				font-weight: bold;
				color: black;
				padding-right: 5px;
				text-align: right;
			}
			.reportselectionlabel{
				font-size: 15px;
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
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;">Customer Ledger</label>&ensp;&ensp;
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$cname]." (".$cus_mobile[$cname].")"; ?></label>&ensp;&ensp;
						<?php
							}
						?>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="CustomerLedgerMaster_tn.php?cid=<?php echo $cid; ?>" method="post" onSubmit="return checkval()">
					<?php } else { ?>
					<form action="CustomerLedgerMaster_tn.php?cid=<?php echo $cid; ?>&db=<?php echo $db; ?>&emp_code=<?php echo $users_code; ?>" method="post" onSubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="25">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2" style="width:auto;">
											<option value="select">-select-</option>
											<?php
											foreach($cus_code as $cc){
											?>
													<option <?php if($cname == $cc) { echo 'selected'; } ?> value="<?php echo $cc; ?>"><?php echo $cus_name[$cc]; ?></option>
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
								$prev_bal_col = $item_det_col = $bwtd_det_col = $grnd_tot_col = $clsb_tot_col = 0;
								?>
								<thead class="thead2" style="background-color: #98fb98;">
									<?php
									$active_flag = 1;
									for($i = 1;$i <= $col_count;$i++){
										if(!empty($field_details[$i.":".$active_flag])){
											//echo "<br/>".$field_details[$i.":".$active_flag];
											if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<th>Date</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<th>Invoice</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<th>Book Invoice</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<th>Customer</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<th>Company Name</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<th>Item</th>"; $prev_bal_col++; $bwtd_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<th>Jals</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<th>Birds</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<th>T.Wt.</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<th>E.Wt.</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<th>Sent Wt.</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<th>Mort Wt.</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "nweight_flag"){ if($_SESSION['dbase'] == "poulso6_chicken_ka_dharun_feeds"){ echo "<th>No.of Bags</th>"; } else{ echo "<th>N.Wt.</th>"; } $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<th>Avg.Wt.</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<th>Total.Wt.</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<th>Paper Rate</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<th>Price</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<th>Actual Price</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<th>Discount Price</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<th>Freight</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<th>TCS</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<th>Discount</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<th>Amount</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<th>Total Amount</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<th>Transport</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<th>Warehouse</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<th>Remarks</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<th>Vehicle</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<th>Driver</th>"; $item_det_col++; $grnd_tot_col++; $clsb_tot_col++; }
											else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<th>Debit</th>"; }
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<th>Credit</th>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<th>Balance</th>"; }
											else{ }
										}
									}
									
									?>
								</thead>
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
								<?php
									$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
									$tdate = date("Y-m-d",strtotime($_POST['todate']));
									$cus_names = $_POST['cname'];
									
									$ob_sales = $ob_receipt = $ob_mortality = $ob_returns = $ob_ccn = $ob_cdn = $rb_amt = $ob_cramt = $ob_dramt = $ob_rcv = $ob_pid = 0;
									if($count14 > 0){
										$obsql = "SELECT * FROM `customer_sales` WHERE `date` < '$fdate' AND `customercode` = '$cus_names' AND `active` = '1' ORDER BY `invoice` ASC";
										$obquery = mysqli_query($conn,$obsql); $old_inv = "";
										while($obrow = mysqli_fetch_assoc($obquery)){
											if($old_inv != $obrow['invoice']){
												$ob_sales = $ob_sales + $obrow['finaltotal'];
												$old_inv = $obrow['invoice'];
											}
										}
									}
									if($count44 > 0){
										$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_mortality = $ob_mortality + $obrow['amount']; }
									}
									if($count40 > 0){
										$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$cus_names' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = $ob_returns + $obrow['amount']; }
									}
									if($count13 > 0){
										$obsql = "SELECT * FROM `customer_receipts` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `active` = '1'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_receipt = $ob_receipt + $obrow['amount']; }
									}
									if($count32 > 0){
										$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mode` IN ('CCN','CDN') AND `active` = '1'";
										$obquery = mysqli_query($conn,$obsql);
										while($obrow = mysqli_fetch_assoc($obquery)){ if($obrow['mode'] == "CCN"){ $ob_ccn = $ob_ccn + $obrow['amount']; } else { $ob_cdn = $ob_cdn + $obrow['amount']; } }
										if($obtype[$cus_names] == "Cr"){
											$ob_cramt = $obamt[$cus_names];
										}
										else{
											$ob_dramt = $obamt[$cus_names];
										}
									}
									$ob_rcv = $ob_sales + $ob_cdn + $ob_dramt;
									$ob_pid = $ob_receipt + $ob_mortality + $ob_returns + $ob_ccn + $ob_cramt;
									
									if(number_format_ind($ob_rcv) == number_format_ind($ob_pid)){ $ob_rcv = $ob_pid = 0; }
									
									if($ob_rcv >= $ob_pid){
										echo "<tr>";
										echo "<td colspan='".$prev_bal_col."' style='font-weight:bold;'>Previous Balance</td>";
										for($i = 1;$i <= $item_det_col;$i++){ echo "<td></td>"; }
										
										for($i = 1;$i <= $col_count;$i++){
											if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='font-weight:bold;".$sales_text_color."'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='font-weight:bold;".$balance_text_color."'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
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
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='font-weight:bold;".$receipt_text_color."'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='font-weight:bold;".$balance_text_color."'>".number_format_ind($ob_rcv - $ob_pid)."</td>"; }
										}
										echo "</tr>";
										$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
										$ob_pid_amt = $ob_pid - $ob_rcv;
										$ob_rev_amt = 0;
									}
									
									//Sales
									$sii_count = $slc_finaltotal = $sales = $receipts = $mortality = $returns = $ccns = $cdns = array();
									if($count14 > 0){
										$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `customercode` = '$cname' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
										$query = mysqli_query($conn,$sql); $i = 0; $link_trnums = $inv_cus_code = array();
										while($row = mysqli_fetch_assoc($query)){
																						
											$i = $i + 1; $sales[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['customercode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode']."@".$row['drivercode']."@".$row['freight_amount_jal']."@".$row['freight_amt']."@".$row['sent_weight']."@".$row['mort_weight']."@".$row['actual_price']."@".$row['discount_price']."@".$row['sup_code'];

											$sum_sinv_qty[$row['invoice']] = $sum_sinv_qty[$row['invoice']] + $row['netweight'];
											$stotal = $stotal + $row['netweight'];
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
									}
									//Sales
									if(sizeof($link_trnums) > 0){
										$tr_list = implode("','",$link_trnums);
										$sql = "SELECT * FROM `pur_purchase` WHERE `invoice` IN ('$tr_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$key = $row['link_trnum'];
											$inv_cus_code[$key] = $row['vendorcode'];
										}
									}
									
									//Receipts
									if($count13 > 0){
										$rctseq = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
										$rctname = $_POST['cname']; $i = 0; if($rctname == "all") { $rctnames = ""; } else { $rctnames = " AND `ccode` = '$cname'"; } $rctsql = $rctseq."".$rctnames."".$flags; $rctquery = mysqli_query($conn,$rctsql);
										while($row = mysqli_fetch_assoc($rctquery)){
											$docno = str_replace("@","x",$row['docno']);
											$i = $i + 1;  $receipts[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$docno."@".$row['mode']."@".$row['method']."@".$row['type']."@".$row['rcode']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
										}
									}
									//Mortality
									if($count44 > 0){
										$mortsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$cname' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$mortquery = mysqli_query($conn,$mortsql); $i = 0;
										while($row = mysqli_fetch_assoc($mortquery)){
											$avgwt = 0;
											if((float)$row['birds'] != 0){ $avgwt = $row['quantity'] / $row['birds']; } else{ $avgwt = 0; }
											$i = $i + 1;  $mortality[$row['date']."@".$i] = $row['code']."@".$row['date']."@".$row['ccode']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$avgwt."@".$row['price']."@".$row['amount']."@".$row['remarks'];
										}
									}
									//Returns
									if($count40 > 0){
										$rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$cname' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$rtnquery = mysqli_query($conn,$rtnsql); $i = 0;
										while($row = mysqli_fetch_assoc($rtnquery)){
											$avgwt = 0;
											if((float)$row['birds'] != 0){ $avgwt = $row['quantity'] / $row['birds']; } else{ $avgwt = 0; }
											$i = $i + 1;  $returns[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['inv_trnum']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$avgwt."@".$row['price']."@".$row['amount']."@".$row['warehouse'];
										}
									}
									//CRDR NOTE
									if($count32 > 0){
										$crdrseq = "SELECT * FROM `main_crdrnote` WHERE `mode` IN ('CDN','CCN') AND `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
										$crdrname = $_POST['cname']; $i = $j = 0; if($crdrname == "all") { $crdrnames = ""; } else { $crdrnames = " AND `ccode` = '$cname'"; } $crdrsql = $crdrseq."".$crdrnames."".$flags; $crdrquery = mysqli_query($conn,$crdrsql);
										while($row = mysqli_fetch_assoc($crdrquery)){
											if($row['mode'] == "CCN"){
												$i = $i + 1; $ccns[$row['date']."@".$i] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
											}
											else if($row['mode'] == "CDN"){
												$j = $j + 1; $cdns[$row['date']."@".$j] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
											}
											else{ }
										}
									}
									$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = 0; $exi_inv = "";
									for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
										$date_asc = date('Y-m-d', $currentDate);
										if(!empty($sales)){
											$ccount = sizeof($sales); 
											for($j = 0;$j <=$ccount;$j++){
												if($sales[$date_asc."@".$j] != ""){
													$sales_details = explode("@",$sales[$date_asc."@".$j]);
													if(empty($inv_cus_code[$sales_details[1]]) || $inv_cus_code[$sales_details[1]] == ""){
														$cname = $sup_name[$sales_details[29]];
													}
													else{ $cname = $sup_name[$inv_cus_code[$sales_details[1]]]; }
													
													echo "<tr>";
													if($exi_inv != $sales_details[1]){
														$exi_inv = $sales_details[1];
														/*if(number_format_ind($slc_finaltotal[$sales_details[1]]) == number_format_ind($rb_amt)){ $rb_amt = 0; }
														else{ $rb_amt = $rb_amt + $slc_finaltotal[$sales_details[1]]; }*/
														$rb_amt = $rb_amt + (float)$slc_finaltotal[$sales_details[1]];
														$ft_tcds = $ft_tcds + (float)$slc_tcdsamt[$sales_details[1]];
														$ft_roundoff = $ft_roundoff + (float)$slc_roundoff[$sales_details[1]];
														$fst_famt = $fst_famt + (float)$slc_finaltotal[$sales_details[1]];
														
														for($i = 1;$i <= $col_count;$i++){
															if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$sales_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$sales_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$sales_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cname."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$item_name[$sales_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".str_replace('.00','',number_format_ind($sales_details[4]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".str_replace('.00','',number_format_ind($sales_details[8]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($sales_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($sales_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td>".number_format_ind($sales_details[25])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td>".number_format_ind($sales_details[26])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($sales_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){
																if(!empty($sales_details[8]) && $sales_details[8] > 0){
																	echo "<td>".number_format_ind($sales_details[9] / $sales_details[8])."</td>";
																}
																else{
																	echo "<td>".number_format_ind(0)."</td>";
																}
																
															}
															else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($sum_sinv_qty[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$sales_details[10]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td>".$sales_details[27]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td>".$sales_details[28]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td>".number_format_ind($sales_details[24])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td>".number_format_ind($sales_details[23])."</td>"; $total_jfreight += (float)$sales_details[23]; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$sector_name[$sales_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$sales_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td style='text-align:left;'>".$sales_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td style='text-align:left;'>".$sales_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$sales_details[18]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($slc_tcdsamt[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='text-align:right;".$sales_text_color."' rowspan='$sii_count[$exi_inv]'>".number_format_ind($slc_finaltotal[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='text-align:right;".$receipt_text_color."' rowspan='$sii_count[$exi_inv]'></td>"; }
															else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='text-align:right;".$balance_text_color."' rowspan='$sii_count[$exi_inv]'>".number_format_ind($rb_amt)."</td>"; }
															else{ }
														}
													}
													else{
														for($i = 1;$i <= $col_count;$i++){
															if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$sales_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$sales_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$sales_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cname."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$item_name[$sales_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".str_replace('.00','',number_format_ind($sales_details[4]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".str_replace('.00','',number_format_ind($sales_details[8]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($sales_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($sales_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td>".number_format_ind($sales_details[25])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td>".number_format_ind($sales_details[26])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($sales_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){
																if((float)$sales_details[8] != 0){ $t1 = round(($sales_details[9] / $sales_details[8]),2); } else{ $t1 = 0; }
																echo "<td>".number_format_ind($t1)."</td>";
															}
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$sales_details[10]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td>".$sales_details[27]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td>".$sales_details[28]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td>".number_format_ind($sales_details[24])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td>".number_format_ind($sales_details[23])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$sector_name[$sales_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$sales_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td style='text-align:left;'>".$sales_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td style='text-align:left;'>".$sales_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$sales_details[18]."</td>"; }
															else{ }
														}
													}
													
														$tbcount = $tbcount + (float)$sales_details[8];
														$tjcount = $tjcount + (float)$sales_details[4];
														$tscount = $tscount + (float)$sales_details[25];
														$tmcount = $tmcount + (float)$sales_details[26];
														$tncount = $tncount + (float)$sales_details[9];
														$twcount = $twcount + (float)$sales_details[5];
														$tecount = $tecount + (float)$sales_details[6];
														$tdcount = $tdcount + (float)$sales_details[18];
														$ttcount = $ttcount + (float)$sales_details[19];
														$tacount = $tacount + (float)$sales_details[11];
														$tfritacount = $tfritacount + (float)$sales_details[24];
														
													echo "</tr>";
												}
												else{ }
											}
										}
										if(!empty($receipts)){
											$ccount = sizeof($receipts); 
											for($j = 0;$j <=$ccount;$j++){
												if($receipts[$date_asc."@".$j] != ""){
													$receipts_details = explode("@",$receipts[$date_asc."@".$j]);
													if(number_format_ind($receipts_details[10]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = (float)$rb_amt - (float)$receipts_details[10];
													}
													$frt_famt = (float)$frt_famt + (float)$receipts_details[10];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($receipts_details[1]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$receipts_details[0]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$receipts_details[3]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$receipts_details[2]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$coaname[$receipts_details[5]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($receipts_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$sector_name[$receipts_details[13]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$receipts_details[14]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td  style='text-align:right;".$receipt_text_color."'>".number_format_ind($receipts_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td  style='text-align:right;".$balance_text_color."'>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
												}
												else{ }
											}
										}
										if(!empty($returns)){
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
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$return_details[2]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$item_name[$return_details[4]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='text-align:right;'>".str_replace('.00','',number_format_ind($return_details[5]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='text-align:right;'>".str_replace('.00','',number_format_ind($return_details[6]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[8])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td style='text-align:right;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td style='text-align:right;'>".number_format_ind($return_details[9])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($return_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$sector_name[$return_details[11]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td  style='text-align:right;".$receipt_text_color."'>".number_format_ind($return_details[10])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td  style='text-align:right;".$balance_text_color."'>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
													$tjcount = (float)$tjcount - (float)$return_details[5];
													$tbcount = (float)$tbcount - (float)$return_details[6];
													$tncount = (float)$tncount - (float)$return_details[7];
												}
												else{ }
											}
										}
										if(!empty($mortality)){
											$ccount = sizeof($mortality); 
											for($j = 0;$j <=$ccount;$j++){
												if($mortality[$date_asc."@".$j] != ""){
													$mortality_details = explode("@",$mortality[$date_asc."@".$j]);
													if(number_format_ind($mortality_details[8]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = (float)$rb_amt - (float)$mortality_details[8];
													}
													$frt_famt = (float)$frt_famt + (float)$mortality_details[8];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($mortality_details[1]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$mortality_details[0]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$mortality_details[2]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$item_name[$mortality_details[3]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='text-align:right;'>".str_replace('.00','',number_format_ind($mortality_details[4]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($mortality_details[5])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td style='text-align:right;'>".number_format_ind($mortality_details[6])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td style='text-align:right;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td style='text-align:right;'>".number_format_ind($mortality_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($mortality_details[8])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$mortality_details[9]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td  style='text-align:right;".$receipt_text_color."'>".number_format_ind($mortality_details[8])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td  style='text-align:right;".$balance_text_color."'>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
													$tbcount = (float)$tbcount - (float)$mortality_details[4];
													$tncount = (float)$tncount - (float)$mortality_details[5];
												}
												else{ }
											}
										}
										if(!empty($ccns)){
											$ccount = sizeof($ccns); 
											for($j = 0;$j <=$ccount;$j++){
												if($ccns[$date_asc."@".$j] != ""){
													$ccns_details = explode("@",$ccns[$date_asc."@".$j]);
													if(number_format_ind($ccns_details[7]) == number_format_ind($rb_amt)){
														$rb_amt = 0;
													}
													else{
														$rb_amt = (float)$rb_amt - (float)$ccns_details[7];
													}
													$fct_famt = (float)$fct_famt + (float)$ccns_details[7];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($ccns_details[2]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$ccns_details[1]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$ccns_details[4]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$ccns_details[3]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$coaname[$ccns_details[5]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($ccns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$sector_name[$ccns_details[11]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$ccns_details[12]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td  style='text-align:right;".$receipt_text_color."'>".number_format_ind($ccns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td  style='text-align:right;".$balance_text_color."'>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
												}
												else{ }
											}
										}
										if(!empty($cdns)){
											$ccount = sizeof($cdns); 
											for($j = 0;$j <=$ccount;$j++){
												if($cdns[$date_asc."@".$j] != ""){
													$cdns_details = explode("@",$cdns[$date_asc."@".$j]);
													/*if(number_format_ind($cdns_details[7]) == number_format_ind($rb_amt)){ $rb_amt = 0; }
													else{ $rb_amt = (float)$rb_amt + (float)$cdns_details[7]; }*/
													$rb_amt = (float)$rb_amt + (float)$cdns_details[7];
													$fdt_famt = (float)$fdt_famt + (float)$cdns_details[7];
													echo "<tr>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($cdns_details[2]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$cdns_details[1]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$cdns_details[4]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$cus_name[$cdns_details[3]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "salesup_flag"){ echo "<td style='text-align:left;'></td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif'>".$coaname[$cdns_details[5]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($cdns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;font-family:Palatino, URW Palladio L, serif;'>".$sector_name[$cdns_details[11]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$cdns_details[12]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td  style='text-align:right;".$sale_text_color."'>".number_format_ind($cdns_details[7])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td  style='text-align:right;".$balance_text_color."'>".number_format_ind($rb_amt)."</td>"; }
														else{ }
													}
													echo "</tr>";
												}
												else{ }
											}
										}
									}
								?>
								</tbody>
								<thead>
									<tr class="foottr" style="background-color: #98fb98;">
										<td colspan="<?php echo $bwtd_det_col; ?>" align="center"><b>Between Days Total</b></td>
										<?php
										for($i = 1;$i <= $col_count;$i++){
											if($tncount > 0 && $tbcount > 0){
												$result = (float)$tncount / (float)$tbcount;
											}else{
												$result = 0;
											}
											if($fst_famt > 0 && $tncount > 0){
												$result1 = (float)$fst_famt / (float)$tncount;
											}else{
												$result1 = 0;
											}
											if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".str_replace('.00','',number_format_ind($tjcount))."</td>"; }
											else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".str_replace('.00','',number_format_ind($tbcount))."</td>"; }
											else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($twcount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tecount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "sentwt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tscount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "mortwt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tmcount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tncount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "aweight_flag"){
												if($avgprc_flag == 1){
													echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($result)."</td>"; 
												}else{
													echo "<td style='padding: 0 5px;text-align:right;'></td>";
												}
												 
												}
											else if($field_details[$i.":".$active_flag] == "sweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($stotal)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "price_flag"){
												if((int)$dtap_flag == 1){
													echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($result1)."</td>";
												}
												else{
													echo "<td style='padding: 0 5px;text-align:right;'></td>";
												}
											}
											else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tfritacount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ft_tcds)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".number_format_ind($tdcount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "samt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind((float)$tacount)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind((float)$tacount + (float)$fct_famt + (float)$fdt_famt + (float)$frt_famt)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($total_jfreight)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "actual_price_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "discount_price_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
											else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;".$sales_text_color."'>".number_format_ind($fst_famt + $fdt_famt)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;".$receipt_text_color."'>".number_format_ind($frt_famt + $fct_famt)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='padding: 0 5px;text-align:right;color:green;'>".number_format_ind(($fst_famt + $fdt_famt) - ($frt_famt + $fct_famt))."</td>"; }
											else{ }
										}
										?>
									</tr>
									<tr class="foottr" style="background-color: #98fb98;">
										<td colspan="<?php echo $grnd_tot_col; ?>" align="center"><b>Grand Total</b></td>
										<?php
										for($i = 1;$i <= $col_count;$i++){
											if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;".$sales_text_color."'>".number_format_ind(((float)$fst_famt + (float)$fdt_famt) + (float)$ob_rev_amt)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;".$receipt_text_color."'>".number_format_ind(((float)$frt_famt + (float)$fct_famt) + (float)$ob_pid_amt)."</td>"; }
											else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td style='padding: 0 5px;text-align:right;color:red;'>".number_format_ind((((float)$fst_famt + (float)$fdt_famt) + (float)$ob_rev_amt) - (((float)$frt_famt + (float)$fct_famt) + (float)$ob_pid_amt))."</td>"; }
											else{ }
										}
										?>
									</tr>
                                    <?php /*
									<tr class="foottr" style="background-color: #98fb98;">
										<td colspan="<?php echo $clsb_tot_col; ?>" align="center"><b>Closing Balance</b></td>
										<?php
											if(number_format_ind(($fst_famt + $fdt_famt) + $ob_rev_amt) == number_format_ind(((float)$frt_famt + (float)$fct_famt) + (float)$ob_pid_amt)){
												for($i = 1;$i <= $col_count;$i++){
													if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
													else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td>0.00</td>"; }
													else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
													else{ }
												}
											}
											else if((((float)$fst_famt + (float)$fdt_famt) + (float)$ob_rev_amt) > (((float)$frt_famt + (float)$fct_famt) + (float)$ob_pid_amt)){
												$famt = (((float)$fst_famt + (float)$fdt_famt) + (float)$ob_rev_amt) - (((float)$frt_famt + (float)$fct_famt) + (float)$ob_pid_amt);
												for($i = 1;$i <= $col_count;$i++){
													if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;".$sales_text_color."'>".number_format_ind($famt)."</td>"; }
													else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
													else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
													else{ }
												}
											}
											else {
												$famt = (((float)$fst_famt + (float)$fdt_famt) + (float)$ob_rev_amt) - (((float)$frt_famt + (float)$fct_famt) + (float)$ob_pid_amt);
												for($i = 1;$i <= $col_count;$i++){
													if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td></td>"; }
													else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;".$receipt_text_color."'>".number_format_ind($famt)."</td>"; }
													else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
													else{ }
												}
											}
										?>
									</tr>
                                    */ ?>
								</thead>
							<?php
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
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("cname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select customer ..!");
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
