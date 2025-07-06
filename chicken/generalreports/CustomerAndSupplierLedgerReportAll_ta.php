<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;


	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../newConfig.php"; include "header_head.php"; include "number_format_ind.php"; 
		include "../broiler_check_tableavailability.php";}
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php";
		include "../broiler_check_tableavailability.php"; }
			
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $profit_flag = $row['profit_flag']; } if($profit_flag == "" || $profit_flag == NULL || $profit_flag == 0){ $profit_flag == 0; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
	
	if(isset($_POST['submit']) == true){
		if($_POST['ctype'] == "on" || $_POST['ctype'] == true){
			$con_type = " AND `contacttype` LIKE 'S&C'";
			$con_code = "S&C";
		}
		else{
			$con_type = " AND `contacttype` LIKE 'S&C'";
			$con_code = "C";
		}
	}
	else{ $con_type = " AND `contacttype` LIKE 'S&C'"; $con_code = "S&C"; }
	$ab = 0;
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ".$con_type." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];

		if($ab == 0){
			$selected_customers = "'". $row['code']."'";
		}else{
			$selected_customers .= ",'". $row['code']."'";
		}
		$ab++;
	}

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; $sector_code[$row['code']] = $row['code']; }
    
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname'];
	if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
	$cname == "all";
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { 
        $excel_type = $_POST['export']; 

        $sects = array(); $sec_all_flag = 0;
        foreach($_POST['sectors'] as $scts){ $sects[$scts] = $scts; if($scts == "all"){ $sec_all_flag = 1; } }
        $sects_list = implode("','", array_map('addslashes', $sects));
        $secct_fltr = ""; if($sec_all_flag == 1 ){ $secct_fltr = ""; } else { $secct_fltr = "AND `warehouse` IN ('$sects_list')";}
    } else{ 
        $excel_type = "displaypage"; 
    }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname =  "all";
	}
	$url = "../PHPExcel/Examples/BalanceReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&cname=".$exl_cname;
	$url2 = "../PHPExcel/Examples/BalanceReportWODecimal-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&cname=".$exl_cname;
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">

		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			var url2 = '<?php echo $url2; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
			else if(exptype.match("exportnodecimal")){
				window.open(url2,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;
			}		
		</style>
		<style>
			body {
				font-size: 15px;
				font-weight: bold;
			}
			.thead2,.tbody1 {
				padding: 1px;
				font-weight: bold;
				font-size: 15px;
			}
			.formcontrol {
				height: 23px;
				font-weight: bold;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				font-weight: bold;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 15px;
				font-weight: bold;
				padding-right: 5px;
				text-align: right;
			}
			.table1, .table1 thead, .table1 tbody, .table1 tr, .table1 th, .table1 td {
				font-size: 15px;
				font-weight: bold;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Supplier & Customer Ledger</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br/>
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
				<form action="CustomerAndSupplierLedgerReportAll_ta.php" method="post"  onsubmit="return checkval()">
					<?php } else { ?>
					<form action="CustomerAndSupplierLedgerReportAll.php?db=<?php echo $db; ?>" method="post"  onsubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="16">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<!---<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2">
											<option value="select">-select-</option>
											<option value="all" selected>-All-</option>
										</select>&ensp;&ensp;
										 <label class="reportselectionlabel">Customer &amp; Supplier</label>&nbsp;
										<input type="checkbox" name="ctype" id="ctype" class="formcontrol" <?php if($con_code == "S&C"){ echo "checked"; } ?> />
									&ensp;&ensp; --->
                                    <label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="sectors[]" id="sectors[0]" class="form-control select2" style="width:180px;" multiple>
                                                <?php
                                                    // Ensure sectors is always an array
                                                    $selected_sectors = $_POST['sectors'] ?? ['all'];
                                                    if (!is_array($selected_sectors)) {
                                                        $selected_sectors = [$selected_sectors];
                                                    }
                                                ?>
                                                <option value="all" <?php if (in_array("all", $selected_sectors)) echo "selected"; ?>>All</option>
                                                <?php foreach($sector_code as $scode) { ?>
                                                    <option value="<?php echo $scode; ?>" <?php if (in_array($scode, $selected_sectors)) echo "selected"; ?>>
                                                        <?php echo $sector_name[$scode]; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>&ensp;&ensp;
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "exportnodecimal") { echo 'selected'; } ?> value="exportnodecimal">XLS - No Decimal</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<tr>
									<th rowspan="1">Sl No.</th>
									<th rowspan="2">Name</th>
									<th rowspan="2">Opening Balance</th>
									<th colspan="4">Selected Period</th>
									<th rowspan="2">Balance</th>
								</tr>
								<tr>
									<th></th>
									<th>Sales Qty</th>
									<th>Sales</th>
									<th>Receipt</th>
									<th>B/w days balance</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									if($_POST['export'] != "exportexcel"){
										if($cname == "" || $cname == "all" || $cname == "select"){
											$fromdate = $_POST['fromdate'];
											$todate = $_POST['todate'];
											if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate'])); }
											$cname = $_POST['cname']; $iname = $_POST['iname'];
											if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
											
											//sales invoice
											$sql = "SELECT * FROM `customer_sales` WHERE `date` < '$fromdate' AND customercode IN ($selected_customers) AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`customercode` ASC";
											$query = mysqli_query($conn,$sql); $old_inv = ""; $ob_sales = array();
											while($row = mysqli_fetch_assoc($query)){
												if($old_inv != $row['invoice']){
													$ob_sales[$row['customercode']] = $ob_sales[$row['customercode']] + $row['finaltotal'];
													$old_inv = $row['invoice'];
												}
											}
											//Customer Receipt
											$sql = "SELECT * FROM `customer_receipts` WHERE `date` < '$fromdate' AND ccode IN ($selected_customers) AND `active` = '1'".$secct_fltr." ORDER BY `ccode` ASC";
											$query = mysqli_query($conn,$sql); $ob_receipts = array();
											while($row = mysqli_fetch_assoc($query)){
												$ob_receipts[$row['ccode']] = $ob_receipts[$row['ccode']] + $row['amount'];
											}
											//Customer CrDr Note
											$sql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fromdate' AND ccode IN ($selected_customers) AND `mode` IN ('CCN','CDN') AND `active` = '1'".$secct_fltr." ORDER BY `ccode` ASC";
											$query = mysqli_query($conn,$sql); $ob_ccn = $ob_cdn = array();
											while($row = mysqli_fetch_assoc($query)){
												if($row['mode'] == "CCN"){
													$ob_ccn[$row['ccode']] = $ob_ccn[$row['ccode']] + $row['amount'];
												}
												else{
													$ob_cdn[$row['ccode']] = $ob_cdn[$row['ccode']] + $row['amount'];
												}
											}

											if ($count40 > 0) {
												$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fromdate' AND `vcode` IN ($selected_customers) AND `mode` = 'customer' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$ob_returns[$obrow['vcode']] = (float)$ob_returns[$obrow['vcode']] + $obrow['amount'];
												}
											}
											if ($count44 > 0) {
												$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fromdate' AND `ccode` IN ($selected_customers) AND `mtype` = 'customer' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$ob_mortality[$obrow['ccode']] = (float)$ob_mortality[$obrow['amount']] + (float)$obrow['amount'];
												}
											}
	
											$old_inv = "";
											if ($count57 > 0) {
												$obsql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fromdate' AND `vendorcode` IN ($selected_customers) AND `active` = '1'".$secct_fltr." ORDER BY `date`,`invoice` ASC";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													if ($old_inv != $obrow['invoice']) {
														$ob_purchases[$obrow['vendorcode']] = $ob_purchases[$obrow['vendorcode']] + $obrow['finaltotal']; // + $obrow['obftcds'];// + $obrow['obftax'] - $obrow['obfdis'];
														$old_inv = $obrow['invoice'];
														//echo "<br/>".$obrow['invoice']."@".$obrow['finaltotal']."@".$ob_purchases;
													}
												}
											}
											if ($count56 > 0) {
												$obsql = "SELECT * FROM `pur_payments` WHERE `date` < '$fromdate' AND `ccode` IN ($selected_customers) AND `active` = '1'".$secct_fltr."";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$ob_payments[$obrow['ccode']] = $ob_payments[$obrow['ccode']] + $obrow['amount'];
												}
											}
											if ($count40 > 0) {
												$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fromdate' AND `vcode` IN ($selected_customers) AND `mode` = 'supplier' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$ob_sreturns[$obrow['vcode']] =  (float)$ob_sreturns[$obrow['vcode']] + (float)$obrow['amount'];
												}
											}
											if ($count44 > 0) {
												$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fromdate' AND `ccode` IN ($selected_customers) AND `mtype` = 'supplier' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$ob_smortality[$obrow['ccode']] = (float)$ob_smortality[$obrow['ccode']] + (float)$obrow['amount'];
												}
											}
											if ($count32 > 0) {
												$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `ccode` IN ($selected_customers) AND `mode` IN ('SCN','SDN') AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													if ($obrow['mode'] == "SCN") {
														$ob_sup_ccn[$obrow['ccode']] = $ob_sup_ccn[$obrow['ccode']] + $obrow['amount'];
													} else {
														$ob_sup_cdn[$obrow['ccode']] = $ob_sup_cdn[$obrow['ccode']] + $obrow['amount'];
													}
												}
											}
											
											//sales invoice
											$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND customercode IN ($selected_customers) AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
											$query = mysqli_query($conn,$sql); $old_inv = ""; $bt_sales = array();
											while($row = mysqli_fetch_assoc($query)){
												if($old_inv != $row['invoice']){
													$bt_sales[$row['customercode']] = $bt_sales[$row['customercode']] + $row['finaltotal'];
													//echo "<br/>".$row['customercode']."@".$row['finaltotal'];
													$old_inv = $row['invoice'];
												}
												$bt_sales_qty[$row['customercode']] = $bt_sales_qty[$row['customercode']] + $row['netweight'];
											}
											//Customer Receipt
											$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND ccode IN ($selected_customers) AND `active` = '1'".$secct_fltr." ORDER BY `ccode` ASC";
											$query = mysqli_query($conn,$sql); $bt_receipts = array();
											while($row = mysqli_fetch_assoc($query)){
												$bt_receipts[$row['ccode']] = $bt_receipts[$row['ccode']] + $row['amount'];
											}
											//Customer CrDr Note
											$sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND ccode IN ($selected_customers) AND `mode` IN ('CCN','CDN') AND `active` = '1'".$secct_fltr." ORDER BY `ccode` ASC";
											$query = mysqli_query($conn,$sql); $bt_ccn = $bt_cdn = array();
											while($row = mysqli_fetch_assoc($query)){
												if($row['mode'] == "CCN"){
													$bt_ccn[$row['ccode']] = $bt_ccn[$row['ccode']] + $row['amount'];
												}
												else{
													$bt_cdn[$row['ccode']] = $bt_cdn[$row['ccode']] + $row['amount'];
												}
											}
											if ($count40 > 0) {
												$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `vcode` IN ($selected_customers) AND `mode` = 'customer' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$bt_returns[$obrow['vcode']] = (float)$bt_returns[$obrow['vcode']] + $obrow['amount'];
												}
											}
											if ($count44 > 0) {
												$obsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `ccode` IN ($selected_customers) AND `mtype` = 'customer' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$bt_mortality[$obrow['ccode']] = (float)$bt_mortality[$obrow['amount']] + (float)$obrow['amount'];
												}
											}
	
											$old_inv = "";
											if ($count57 > 0) {
												$obsql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `vendorcode` IN ($selected_customers) AND `active` = '1'".$secct_fltr." ORDER BY `date`,`invoice` ASC";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													if ($old_inv != $obrow['invoice']) {
														$bt_purchases[$obrow['vendorcode']] = $bt_purchases[$obrow['vendorcode']] + $obrow['finaltotal']; // + $obrow['obftcds'];// + $obrow['obftax'] - $obrow['obfdis'];
														$old_inv = $obrow['invoice'];
														//echo "<br/>".$obrow['invoice']."@".$obrow['finaltotal']."@".$bt_purchases;
													}
												}
											}
											if ($count56 > 0) {
												$obsql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `ccode` IN ($selected_customers) AND `active` = '1'".$secct_fltr."";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$bt_payments[$obrow['ccode']] = $bt_payments[$obrow['ccode']] + $obrow['amount'];
												}
											}
											if ($count40 > 0) {
												$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `vcode` IN ($selected_customers) AND `mode` = 'supplier' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$bt_sreturns[$obrow['vcode']] =  (float)$bt_sreturns[$obrow['vcode']] + (float)$obrow['amount'];
												}
											}
											if ($count44 > 0) {
												$obsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `ccode` IN ($selected_customers) AND `mtype` = 'supplier' AND `active` = '1'".$secct_fltr." AND `dflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													$bt_smortality[$obrow['ccode']] = (float)$bt_smortality[$obrow['ccode']] + (float)$obrow['amount'];
												}
											}
											if ($count32 > 0) {
												$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `ccode` IN ($selected_customers) AND `mode` IN ('SCN','SDN') AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0'";
												$obquery = mysqli_query($conn, $obsql);
												while ($obrow = mysqli_fetch_assoc($obquery)) {
													if ($obrow['mode'] == "SCN") {
														$bt_sup_ccn[$obrow['ccode']] = $bt_sup_ccn[$obrow['ccode']] + $obrow['amount'];
													} else {
														$bt_sup_cdn[$obrow['ccode']] = $bt_sup_cdn[$obrow['ccode']] + $obrow['amount'];
													}
												}
											}

											
											

											$sl = 1; $ftotal = $ft_ob =  $ft_sq =  $ft_sa =  $ft_rt =  $ft_bb = $total_sales_amt = $total_rct_amt = $total_cdn_amt = $total_ccn_amt = $total_cr_amt = $total_dr_amt = 0;
											foreach($pcode as $pcodes){
												

												echo "<tr>";
												echo "<td style='text-align:left;'>".$sl++."</td>";
												echo "<td style='text-align:left;'>".$pname[$pcodes]."</td>";
												$ob_cramt = $ob_dramt = $ob_dr = $ob_cr = $ob_fcr = $ob_fdr = $bt_dr = $bt_cr = $bt_fcr = $bt_fdr = $balance = 0;
												if($obtype[$pcodes] == "Cr"){
												$ob_cramt = $obamt[$pcodes];
												}
												else {
												$ob_dramt = $obamt[$pcodes];
												}
												if($bt_sales[$pcodes] == ""){ $bt_sales[$pcodes] = 0; }
												$ft_ob = $ft_ob + (($ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt + $ob_sup_cdn[$pcodes] + $ob_payments[$pcodes] + $ob_smortality[$pcodes] + $ob_sreturns[$pcodes] ) - ($ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt+ $ob_purchases[$pcodes] + $ob_sup_ccn[$pcodes] + $ob_mortality[$pcodes] + $ob_returns[$pcodes]));
												$ft_sq = $ft_sq + $bt_sales_qty[$pcodes];
												$ft_sa = $ft_sa + ($bt_sales[$pcodes] + $bt_cdn[$pcodes]);
												$ft_rt = $ft_rt + ($bt_receipts[$pcodes] + $bt_ccn[$pcodes]);
												$ft_bb = $ft_bb + (($bt_sales[$pcodes] + $bt_cdn[$pcodes]  + $bt_sup_cdn[$pcodes] + $bt_payments[$pcodes] + $bt_smortality[$pcodes] + $bt_sreturns[$pcodes]) - ($bt_receipts[$pcodes] + $bt_ccn[$pcodes] + $bt_purchases[$pcodes] + $bt_sup_ccn[$pcodes] + $bt_mortality[$pcodes] + $bt_returns[$pcodes]));

												/* Total Calculations */
												$total_cr_amt = $total_cr_amt + $ob_cramt;
												$total_dr_amt = $total_dr_amt + $ob_dramt;
												//echo "<br/>".$pcodes."@".$bt_sales[$pcodes];
												$total_sales_amt = $total_sales_amt + ($ob_sales[$pcodes] + $bt_sales[$pcodes]);
												$total_rct_amt = $total_rct_amt + ($ob_receipts[$pcodes] + $bt_receipts[$pcodes]);
												$total_cdn_amt = $total_cdn_amt + ($ob_cdn[$pcodes] + $bt_cdn[$pcodes]);
												$total_ccn_amt = $total_ccn_amt + ($ob_ccn[$pcodes] + $bt_ccn[$pcodes]);
												//echo "<br/>(".$ob_sales[$pcodes]."-".$ob_cdn[$pcodes]."-".$ob_dramt.") - (".$ob_receipts[$pcodes]."-".$ob_ccn[$pcodes]."-".$ob_cramt.")";
												echo "<td>".number_format_ind(($ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt + $ob_sup_cdn[$pcodes] + $ob_payments[$pcodes] + $ob_smortality[$pcodes] + $ob_sreturns[$pcodes]) - ($ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt+ $ob_purchases[$pcodes] + $ob_sup_ccn[$pcodes] + $ob_mortality[$pcodes] + $ob_returns[$pcodes] ))."</td>";
												echo "<td>".number_format_ind($bt_sales_qty[$pcodes])."</td>";
												echo "<td>".number_format_ind($bt_sales[$pcodes] + $bt_cdn[$pcodes])."</td>";
												echo "<td>".number_format_ind($bt_receipts[$pcodes] + $bt_ccn[$pcodes])."</td>";
												echo "<td>".number_format_ind(($bt_sales[$pcodes] + $bt_cdn[$pcodes] + $bt_sup_cdn[$pcodes] + $bt_payments[$pcodes] + $bt_smortality[$pcodes] + $bt_sreturns[$pcodes] ) - ($bt_receipts[$pcodes] + $bt_ccn[$pcodes]+ $bt_purchases[$pcodes] + $bt_sup_ccn[$pcodes] + $bt_mortality[$pcodes] + $bt_returns[$pcodes]))."</td>";
												$ob_dr = $ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt  + $ob_sup_cdn[$pcodes] + $ob_payments[$pcodes] + $ob_smortality[$pcodes] + $ob_sreturns[$pcodes];
												$ob_cr = $ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt + $ob_purchases[$pcodes] + $ob_sup_ccn[$pcodes] + $ob_mortality[$pcodes] + $ob_returns[$pcodes];
												if($ob_cr > $ob_dr){
												$ob_fcr = $ob_cr - $ob_dr;
												}
												else{
												$ob_fdr = $ob_dr - $ob_cr;
												}
												$bt_dr = $bt_sales[$pcodes] + $bt_cdn[$pcodes]+ $bt_sup_cdn[$pcodes] + $bt_payments[$pcodes] + $bt_smortality[$pcodes] + $bt_sreturns[$pcodes];
												$bt_cr = $bt_receipts[$pcodes] + $bt_ccn[$pcodes]+ $bt_purchases[$pcodes] + $bt_sup_ccn[$pcodes] + $bt_mortality[$pcodes] + $bt_returns[$pcodes];
												if($bt_cr > $bt_dr){
												$bt_fcr = $bt_cr - $bt_dr;
												}
												else{
												$bt_fdr = $bt_dr - $bt_cr;
												}
												$balance = ($ob_fdr + $bt_fdr) - ($ob_fcr + $bt_fcr);
												$ftotal = $ftotal + $balance;
												echo "<td>".number_format_ind($balance)."</td>";
												echo "</tr>";
											}
											//echo $total_sales_amt."-".$total_cdn_amt."-".$total_dr_amt."-".$total_rct_amt."-".$total_ccn_amt."-".$total_cr_amt;
										}
										else {
										}
									}
								}
							?>
							</tbody>
							<thead>
								<tr class="foottr" style="background-color: #98fb98;">
									<td align="center" colspan="2"><b>Total</b></td>
									<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_ob); ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_sq); ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_sa); ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_rt); ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_bb); ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ftotal); ?></td>
								</tr>
							</thead>
						</table>
						<?php
							if($profit_flag == 1){
								$pre_date = date('Y-m-d', strtotime($fromdate.'-1 days'));
								
								$sql = "SELECT * FROM `item_closingstock` WHERE `date` = '$pre_date' AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0'";
								$query = mysqli_query($conn,$sql);
								$ob_stk_qty = $ob_stk_price = $ob_stk_amt = $ob_stk_fqty = $ob_stk_famt = $bt_pur_amt = $bt_pur_qty = $cls_stk_qty = $cls_stk_price = $cls_stk_amt = $cls_stk_fqty = $cls_stk_famt = 0;
								while($row = mysqli_fetch_assoc($query)){
									$ob_stk_qty = $row['closedquantity'];
									$ob_stk_price = $row['price'];
									$ob_stk_amt = $row['closedquantity'] * $row['price'];
									
									$ob_stk_fqty = $ob_stk_fqty + $ob_stk_qty;
									$ob_stk_famt = $ob_stk_famt + $ob_stk_amt;
								}
								$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1'".$secct_fltr." ORDER BY `date`,`invoice`,`vendorcode` ASC";
								$query = mysqli_query($conn,$sql); $old_inv = "";
								while($row = mysqli_fetch_assoc($query)){
									if($old_inv != $row['invoice']){
										$bt_pur_amt = $bt_pur_amt + $row['finaltotal'];
										$old_inv = $row['invoice'];
									}
									$bt_pur_qty = $bt_pur_qty + $row['netweight'];
								}
								$sql = "SELECT * FROM `item_closingstock` WHERE `date` = '$todate' AND `active` = '1' AND `tdflag` = '0'".$secct_fltr." AND `pdflag` = '0'";
								$query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$cls_stk_qty = $row['closedquantity'];
									$cls_stk_price = $row['price'];
									$cls_stk_amt = $row['closedquantity'] * $row['price'];
									
									$cls_stk_fqty = $cls_stk_fqty + $cls_stk_qty;
									$cls_stk_famt = $cls_stk_famt + $cls_stk_amt;
								}
								$sql = "SELECT * FROM `acc_coa` WHERE `type` = 'COA-0003' AND `active` = '1' ORDER BY `code` ASC";
								$query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ if($coa_code == ""){ $coa_code = $row['code']; } else { $coa_code = $coa_code."','".$row['code']; } }
									
								$sql = "SELECT SUM(amount) as amount FROM `acc_vouchers` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `prefix` ='PV' AND `tcoa` IN ('$coa_code') AND `active` = '1'".$secct_fltr." ORDER BY `date` ASC";
								$query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$texp_amt = $row['amount'];
								}	
								$sql = "SELECT SUM(amount) as amount,SUM(quantity) as quantity FROM `main_mortality` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `active` = '1'".$secct_fltr." ORDER BY `date` ASC";
								$query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$tmort_qty = $row['quantity'];
									$tmort_amt = $row['amount'];
								}
						?>
						<br/><br/><br/>
						<table class="table1" style="width: 50%;">
							<thead>
								<tr class="foottr" style="background-color: #98fb98;">
									<td align="center"><b>Type</b></td>
									<td align="center"><b>Quantity</b></td>
									<td align="center"><b>Amount</b></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Opening + Purchase</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;" title="<?php echo number_format_ind($ob_stk_fqty)."-".number_format_ind($bt_pur_qty); ?>"><?php echo number_format_ind($ob_stk_fqty + $bt_pur_qty); ?></td>
									<td align="center" style="padding: 0 5px;text-align:right;" title="<?php echo number_format_ind($ob_stk_famt)."-".number_format_ind($bt_pur_amt); ?>"><?php echo number_format_ind($ob_stk_famt + $bt_pur_amt); ?></td>
								</tr>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Sales</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;" title="<?php echo number_format_ind($ft_sq); ?>"><?php echo number_format_ind($ft_sq); ?></td>
									<td align="center" style="padding: 0 5px;text-align:right;" title="<?php echo number_format_ind($ft_sa); ?>"><?php echo number_format_ind($ft_sa); ?></td>
								</tr>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Mortality</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;"><?php echo number_format_ind($tmort_qty); ?></td>
									<td align="center" style="padding: 0 5px;text-align:right;"><?php echo number_format_ind($tmort_amt); ?></td>
								</tr>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Closing Stock</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;"><?php echo number_format_ind($cls_stk_fqty); ?></td>
									<td align="center" style="padding: 0 5px;text-align:right;"><?php echo number_format_ind($cls_stk_famt); ?></td>
								</tr>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Margin</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;"></td>
									<td align="center" style="padding: 0 5px;text-align:right;" title="<?php echo "Sale=".$ft_sa."-Opening=".$ob_stk_famt."-Purchase=".$bt_pur_amt."-Closing=".$cls_stk_famt; ?>"><?php echo number_format_ind((($ft_sa) + ($cls_stk_famt)) - (($ob_stk_famt) + ($bt_pur_amt)) + ($tmort_amt)); ?></td>
								</tr>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Expenses</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;"></td>
									<td align="center" style="padding: 0 5px;text-align:right;"><?php echo number_format_ind($texp_amt); ?></td>
								</tr>
								<tr>
									<td align="center" style="padding: 0 5px;text-align:left;background-color: #98fb98;"><b>Profit</b></td>
									<td align="center" style="padding: 0 5px;text-align:right;"></td>
									<td align="center" style="padding: 0 5px;text-align:right;" title="<?php echo "Sale=".$ft_sa."-Opening=".$ob_stk_famt."-Purchase=".$bt_pur_amt."-Closing=".$cls_stk_famt."-Expense=".$texp_amt; ?>"><?php echo number_format_ind((($ft_sa) + ($cls_stk_famt)) - (($ob_stk_famt) + ($bt_pur_amt)) - $texp_amt + ($tmort_amt)); ?></td>
								</tr>
							</tbody>
						</table>
						<?php
							}
						?>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("checkcname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select customer ..!");
					return false;
				}
				else {
					return true;
				}
			}
			function sortTable(n) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = document.getElementById("myTable");
			  switching = true;
			  //Set the sorting direction to ascending:
			  dir = "asc"; 
			  /*Make a loop that will continue until
			  no switching has been done:*/
			  while (switching) {
				//start by saying: no switching is done:
				switching = false;
				rows = table.rows;
				/*Loop through all table rows (except the
				first, which contains table headers):*/
				for (i = 1; i < (rows.length - 1); i++) {
				  //start by saying there should be no switching:
				  shouldSwitch = false;
				  /*Get the two elements you want to compare,
				  one from current row and one from the next:*/
				  x = rows[i].getElementsByTagName("TD")[n];
				  y = rows[i + 1].getElementsByTagName("TD")[n];
				  /*check if the two rows should switch place,
				  based on the direction, asc or desc:*/
				  if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  } else if (dir == "desc") {
					/*if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch = true;
					  break;
					}*/
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  }
				}
				if (shouldSwitch) {
				  /*If a switch has been marked, make the switch
				  and mark that a switch has been done:*/
				  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				  switching = true;
				  //Each time a switch is done, increase this count by 1:
				  switchcount ++;
				  
				} else {
				  /*If no switching has been done AND the direction is "asc",
				  set the direction to "desc" and run the while loop again.*/
				  if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				  }
				}
			  }
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
		<?php
			if($cname == ""){
				
			}
			else {
				echo "<script> sortTable(0); </script>";
			}
		?>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
