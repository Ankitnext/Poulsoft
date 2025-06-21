<?php
//CustomerLedgerWBR.php With Balance Report
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$pmobile[$row['code']] = $row['mobileno'];
		$pgroup[$row['code']] = $row['groupcode'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];
	}
	
	if(isset($_POST['submit']) == true){
		if($_POST['cname'] != "all"){
			$party_code[$_POST['cname']] = $_POST['cname'];
		}
		else if($_POST['cgrp'] == "all" && $_POST['cname'] == "all"){
			foreach($pcode as $pc){
				$party_code[$pc] = $pc;
			}
		}
		else if($_POST['cgrp'] != "all" && $_POST['cname'] == "all"){
			foreach($pcode as $pc){
				if($pgroup[$pc] == $_POST['cgrp']){
					$party_code[$pc] = $pc;
				}
			}
		}
		else{
			foreach($pcode as $pc){
				$party_code[$pc] = $pc;
			}
		}
	}
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$pmobile[$row['code']] = $row['mobileno'];
		$pgroup[$row['code']] = $row['groupcode'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];
	}
	$sql = "SELECT * FROM `main_groups` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cusgrp_code[$row['code']] = $row['code']; $cusgrp_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate']; $todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $cgrp = $_POST['cgrp'];
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=profitandlossreport($fromdate-$todate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;				
			}		
			.thead2,.tbody1 {
				padding: 1px;
				font-size: 12px;
			}
			.formcontrol {
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				padding-right: 5px;
				text-align: right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly" || $exoption == "") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Customer Ledger With Balance</h3>
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
					<form action="CustomerLedgerWBR.php" method="post" onsubmit="return checkval()">
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf" || $exoption == "") { ?>
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
										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="cgrp" id="cgrp" class="form-control select2" onchange="fetchcustomers()">
											<option value="all" <?php if($$_POST['cgrp'] == $cgrp){ echo 'selected'; } ?>>-All-</option>
											<?php
												foreach($cusgrp_code as $gcode){
											?>
												<option value="<?php echo $gcode; ?>" <?php if($cgrp == $gcode){ echo 'selected'; } ?>><?php echo $cusgrp_name[$gcode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2">
											<option value="all" <?php if($_POST['cname'] == "all"){ echo 'selected'; } ?>>-All-</option>
											<?php
											if(isset($_POST['submit']) == false || $_POST['cgrp'] == "all"){
												foreach($pcode as $pc){
											?>
												<option value="<?php echo $pc; ?>" <?php if($_POST['cname'] == $pc){ echo 'selected'; } ?>><?php echo $pname[$pc]; ?></option>
											<?php
												}
											}
											else{
												foreach($pcode as $pc){
													if($_POST['cgrp'] == $pgroup[$pc]){
											?>
												<option value="<?php echo $pc; ?>" <?php if($_POST['cname'] == $pc){ echo 'selected'; } ?>><?php echo $pname[$pc]; ?></option>
											<?php
													}
												}
											}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Show All</label>&nbsp;
										<input type="checkbox" name="showall" id="showall" <?php if($_POST['showall'] == true || $_POST['showall'] == "on"){ echo "checked"; } ?> />&ensp;&ensp;
										<!--<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php //if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php //if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php //if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;-->
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<tr>
									<th>Sl No.</th>
									<th>Party Code</th>
									<th>Party Name</th>
									<th>Phone No.</th>
									<th>Kgs</th>
									<th>Rate</th>
									<th>Amount</th>
									<th>Previous Balance</th>
									<th>Net Balance</th>
									<th>Received Amount</th>
									<th>Balance Amount</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									$fromdate = $_POST['fromdate']; $todate = $_POST['todate']; if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate'])); }
									if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
									if($cgrp == "all" || $cgrp == "select") { $cgrps = ""; } else { foreach($pcode as $pc){ if($pgroup[$pc] == $cgrp){ if($cgrps == ""){ $cgrps = $pcode[$pc]; } else{ $cgrps = $cgrps."','".$pcode[$pc]; } } } }
									
									if($cname != "all"){ $cnames = " AND `customercode` = '$cname'"; $qccode = " AND `ccode` = '$cname'"; }
									else if($cgrp == "all" && $cname == "all"){ $cnames = ""; $qccode = ""; }
									else if($cgrps == "" && $cname == "all"){ $cnames = " AND `customercode` IN ('$cgrps')"; $qccode = " AND `ccode` IN ('$cgrps')"; }
									//Opening Balance - sales invoice
									$seq = "SELECT * FROM `customer_sales` WHERE `date` < '$fromdate'";
									$active = " AND `active` = '1'"; $orderby = " ORDER BY `customercode` ASC";
									$sql = $seq."".$cnames."".$active."".$orderby; $query = mysqli_query($conn,$sql);
									$old_inv = "";
									while($row = mysqli_fetch_assoc($query)){
										if($row['invoice'] != $old_inv){
										$ob_sales[$row['customercode']] = $ob_sales[$row['customercode']] + $row['finaltotal'];
											$old_inv = $row['invoice'];
										}
										else{ }
									}
									//Opening Balance - Customer Receipt
									$seq = "SELECT * FROM `customer_receipts` WHERE `date` < '$fromdate'";
									$active = " AND `active` = '1'"; $orderby = " ORDER BY `ccode` ASC";
									$sql = $seq."".$qccode."".$active."".$orderby; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$ob_receipts[$row['ccode']] = $ob_receipts[$row['ccode']] + $row['amount'];
									}
									//Opening Balance - Customer CrDr Note
									$seq = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `mode` IN ('CCN','CDN')";
									$active = " AND `active` = '1'"; $orderby = " ORDER BY `ccode` ASC";
									$sql = $seq."".$qccode."".$active."".$orderby; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										if($row['mode'] == "CCN"){
											$ob_ccn[$row['ccode']] = $ob_ccn[$row['ccode']] + $row['amount'];
										}
										else{
											$ob_cdn[$row['ccode']] = $ob_cdn[$row['ccode']] + $row['amount'];
										}
									}
									
									//sales invoice
									$seq = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$active = " AND `active` = '1'"; $orderby = " ORDER BY `customercode` ASC";
									$sql = $seq."".$cnames."".$active."".$orderby; $query = mysqli_query($conn,$sql);
									$old_inv = "";
									while($row = mysqli_fetch_assoc($query)){
										if($row['invoice'] != $old_inv){
										$bt_sales[$row['customercode']] = $bt_sales[$row['customercode']] + $row['finaltotal'];
										$bt_sales_qty[$row['customercode']] = $bt_sales_qty[$row['customercode']] + $row['netweight'];
										$old_inv = $row['invoice'];
										} else{ }
									}
									//Customer Receipt
									$seq = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$active = " AND `active` = '1'"; $orderby = " ORDER BY `ccode` ASC";
									$sql = $seq."".$qccode."".$active."".$orderby; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$bt_receipts[$row['ccode']] = $bt_receipts[$row['ccode']] + $row['amount'];
									}
									//Customer CrDr Note
									$seq = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` IN ('CCN','CDN')";
									$active = " AND `active` = '1'"; $orderby = " ORDER BY `ccode` ASC";
									$sql = $seq."".$qccode."".$active."".$orderby; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										if($row['mode'] == "CCN"){
											$bt_ccn[$row['ccode']] = $bt_ccn[$row['ccode']] + $row['amount'];
										}
										else{
											$bt_cdn[$row['ccode']] = $bt_cdn[$row['ccode']] + $row['amount'];
										}
									}
									$tbw_sale_qty = $tbw_sale_amt =  $tcus_opn_amt =  $tcus_net_amt =  $tcus_rcv_amt =  $tcus_fnl_bal = 0; $sl = 1;
									foreach($party_code as $pcodes){
										if($_POST['showall'] == true || $_POST['showall'] == "on"){
											$cus_name =  $cus_code = $cus_mobl = "";
											$bw_sale_qty = $bw_sale_amt = $bw_sale_price = $ob_cramt = $ob_dramt = $cus_opn_amt = $cus_net_amt = $cus_rcv_amt = $cus_fnl_bal = 0;
											
											$cus_code = $pcodes; $cus_name = $pname[$pcodes]; $cus_mobl = $pmobile[$pcodes];
											
											$bw_sale_qty = $bt_sales_qty[$pcodes];
											if(number_format_ind($bw_sale_qty) == ".00" || number_format_ind($bw_sale_qty) == "NAN.00"){ $bw_sale_qty = 0; }
											$bw_sale_amt = $bt_cdn[$pcodes] + $bt_sales[$pcodes];
											//$bw_sale_amt = $bt_sales[$pcodes];
											if(number_format_ind($bw_sale_amt) == ".00" || number_format_ind($bw_sale_amt) == "NAN.00"){ $bw_sale_amt = 0; }
											$tbw_sale_qty = $tbw_sale_qty + $bt_sales_qty[$pcodes];
											if(number_format_ind($tbw_sale_qty) == ".00" || number_format_ind($tbw_sale_qty) == "NAN.00"){ $tbw_sale_qty = 0; }
											
											$tbw_sale_amt = $tbw_sale_amt + $bt_sales[$pcodes];
											if(number_format_ind($tbw_sale_amt) == ".00" || number_format_ind($tbw_sale_amt) == "NAN.00"){ $tbw_sale_amt = 0; }
											if($bw_sale_amt >0 &&  $bw_sale_qty  >0){
												$bw_sale_price = $bw_sale_amt / $bw_sale_qty;
											}else{
												$bw_sale_price =  0;
											}
											
											if(number_format_ind($bw_sale_price) == ".00" || number_format_ind($bw_sale_price) == "NAN.00"){ $bw_sale_price = 0; }
											
											if($obtype[$pcodes] == "Cr"){
												$ob_cramt = $obamt[$pcodes];
											}
											else {
												$ob_dramt = $obamt[$pcodes];
											}
											$cus_opn_amt = (($ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt) - ($ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt));
											//echo"<br/>".$cus_name."-->".$ob_sales[$pcodes]."+".$ob_cdn[$pcodes]."+".$ob_dramt."(-)".$ob_receipts[$pcodes]."+".$ob_ccn[$pcodes]."+".$ob_cramt;
											$tcus_opn_amt = $tcus_opn_amt + (($ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt) - ($ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt));
											
											$cus_net_amt = $cus_opn_amt + $bw_sale_amt;
											if(number_format_ind($cus_net_amt) == ".00" || number_format_ind($cus_net_amt) == "NAN.00"){ $cus_net_amt = 0;  }
											$tcus_net_amt = $tcus_net_amt + ($cus_opn_amt + $bw_sale_amt);
											if(number_format_ind($tcus_net_amt) == ".00" || number_format_ind($tcus_net_amt) == "NAN.00"){ $tcus_net_amt = 0; }
											
											//$cus_rcv_amt = $bt_receipts[$pcodes];
											$cus_rcv_amt = $bt_ccn[$pcodes] + $bt_receipts[$pcodes];
											if(number_format_ind($cus_rcv_amt) == ".00" || number_format_ind($cus_rcv_amt) == "NAN.00"){ $cus_rcv_amt = 0; }
											$tcus_rcv_amt = $tcus_rcv_amt + $bt_receipts[$pcodes];
											if(number_format_ind($tcus_rcv_amt) == ".00" || number_format_ind($tcus_rcv_amt) == "NAN.00"){ $tcus_rcv_amt = 0; }
											
											$cus_fnl_bal = $cus_net_amt - $cus_rcv_amt;
											if(number_format_ind($cus_fnl_bal) == ".00" || number_format_ind($cus_fnl_bal) == "NAN.00"){ $cus_fnl_bal = 0; }
											$tcus_fnl_bal = $tcus_fnl_bal + ($cus_net_amt - $cus_rcv_amt);
											if(number_format_ind($tcus_fnl_bal) == ".00" || number_format_ind($tcus_fnl_bal) == "NAN.00"){ $tcus_fnl_bal = 0; }
											
											if(number_format_ind($cus_fnl_bal) == ".00" || number_format_ind($cus_fnl_bal) == "NAN.00" ){ } //|| number_format_ind($cus_fnl_bal) == 0
											else{
												echo "<tr>";
												echo "<td style='padding-left:5px;text-align:left;'>".$sl++."</td>";
												echo "<td style='padding-left:5px;text-align:left;'>".$cus_code."</td>";
												echo "<td style='padding-left:5px;text-align:left;'>".$cus_name."</td>";
												echo "<td style='padding-left:5px;text-align:left;'>".$cus_mobl."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($bw_sale_qty)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($bw_sale_price)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($bw_sale_amt)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_opn_amt)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_net_amt)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_rcv_amt)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_fnl_bal)."</td>";
												echo "</tr>";
											}
										}
										else{
											$cus_name =  $cus_code = $cus_mobl = "";
											$bw_sale_qty = $bw_sale_amt = $bw_sale_price = $ob_cramt = $ob_dramt = $cus_opn_amt = $cus_net_amt = $cus_rcv_amt = $cus_fnl_bal = 0;
											
											$cus_code = $pcodes; $cus_name = $pname[$pcodes]; $cus_mobl = $pmobile[$pcodes];
											
											$bw_sale_qty = $bt_sales_qty[$pcodes];
											if(number_format_ind($bw_sale_qty) == ".00" || number_format_ind($bw_sale_qty) == "NAN.00"){ $bw_sale_qty = 0; }
											
											if(number_format_ind($bw_sale_qty) != "0.00"){
											
												$bw_sale_amt = $bt_cdn[$pcodes] + $bt_sales[$pcodes];
												//$bw_sale_amt = $bt_sales[$pcodes];
												if(number_format_ind($bw_sale_amt) == ".00" || number_format_ind($bw_sale_amt) == "NAN.00"){ $bw_sale_amt = 0; }
												$tbw_sale_qty = $tbw_sale_qty + $bt_sales_qty[$pcodes];
												if(number_format_ind($tbw_sale_qty) == ".00" || number_format_ind($tbw_sale_qty) == "NAN.00"){ $tbw_sale_qty = 0; }
												
												$tbw_sale_amt = $tbw_sale_amt + $bt_sales[$pcodes];
												if(number_format_ind($tbw_sale_amt) == ".00" || number_format_ind($tbw_sale_amt) == "NAN.00"){ $tbw_sale_amt = 0; }
												if($bw_sale_amt > 0 && $bw_sale_qty > 0){
													$bw_sale_price = $bw_sale_amt / $bw_sale_qty;
												}else{
													$bw_sale_price = 0;
												}
											
												if(number_format_ind($bw_sale_price) == ".00" || number_format_ind($bw_sale_price) == "NAN.00"){ $bw_sale_price = 0; }
												
												if($obtype[$pcodes] == "Cr"){
													$ob_cramt = $obamt[$pcodes];
												}
												else {
													$ob_dramt = $obamt[$pcodes];
												}
												$cus_opn_amt = (($ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt) - ($ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt));
												//echo"<br/>".$cus_name."-->".$ob_sales[$pcodes]."+".$ob_cdn[$pcodes]."+".$ob_dramt."(-)".$ob_receipts[$pcodes]."+".$ob_ccn[$pcodes]."+".$ob_cramt;
												$tcus_opn_amt = $tcus_opn_amt + (($ob_sales[$pcodes] + $ob_cdn[$pcodes] + $ob_dramt) - ($ob_receipts[$pcodes] + $ob_ccn[$pcodes] + $ob_cramt));
												
												$cus_net_amt = $cus_opn_amt + $bw_sale_amt;
												if(number_format_ind($cus_net_amt) == ".00" || number_format_ind($cus_net_amt) == "NAN.00"){ $cus_net_amt = 0;  }
												$tcus_net_amt = $tcus_net_amt + ($cus_opn_amt + $bw_sale_amt);
												if(number_format_ind($tcus_net_amt) == ".00" || number_format_ind($tcus_net_amt) == "NAN.00"){ $tcus_net_amt = 0; }
												
												//$cus_rcv_amt = $bt_receipts[$pcodes];
												$cus_rcv_amt = $bt_ccn[$pcodes] + $bt_receipts[$pcodes];
												if(number_format_ind($cus_rcv_amt) == ".00" || number_format_ind($cus_rcv_amt) == "NAN.00"){ $cus_rcv_amt = 0; }
												$tcus_rcv_amt = $tcus_rcv_amt + $bt_receipts[$pcodes];
												if(number_format_ind($tcus_rcv_amt) == ".00" || number_format_ind($tcus_rcv_amt) == "NAN.00"){ $tcus_rcv_amt = 0; }
												
												$cus_fnl_bal = $cus_net_amt - $cus_rcv_amt;
												if(number_format_ind($cus_fnl_bal) == ".00" || number_format_ind($cus_fnl_bal) == "NAN.00"){ $cus_fnl_bal = 0; }
												$tcus_fnl_bal = $tcus_fnl_bal + ($cus_net_amt - $cus_rcv_amt);
												if(number_format_ind($tcus_fnl_bal) == ".00" || number_format_ind($tcus_fnl_bal) == "NAN.00"){ $tcus_fnl_bal = 0; }
												
												if(number_format_ind($cus_fnl_bal) == ".00" || number_format_ind($cus_fnl_bal) == "NAN.00" ){ } //|| number_format_ind($cus_fnl_bal) == 0
												else{
													echo "<tr>";
													echo "<td style='padding-left:5px;text-align:left;'>".$sl++."</td>";
													echo "<td style='padding-left:5px;text-align:left;'>".$cus_code."</td>";
													echo "<td style='padding-left:5px;text-align:left;'>".$cus_name."</td>";
													echo "<td style='padding-left:5px;text-align:left;'>".$cus_mobl."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($bw_sale_qty)."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($bw_sale_price)."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($bw_sale_amt)."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_opn_amt)."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_net_amt)."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_rcv_amt)."</td>";
													echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_fnl_bal)."</td>";
													echo "</tr>";
												}
											}
										}
									}
								}
							?>
							</tbody>
							<thead>
								<?php
								if($tbw_sale_amt > 0 && $tbw_sale_qty > 0){
									$result = $tbw_sale_amt / $tbw_sale_qty; 
								}else{
									$result = 0;
								}
								echo "<tr class='foottr' style='background-color: #98fb98;'>";
								echo "<td colspan='4' style='padding:5px;text-align:center;'>Grand Total</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($tbw_sale_qty)."</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($result)."</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($tbw_sale_amt)."</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($tcus_opn_amt)."</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($tcus_net_amt)."</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($tcus_rcv_amt)."</td>";
								echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($tcus_fnl_bal)."</td>";
								echo "</tr>";
								?>
							</thead>
						</table>
					</form>
				</div>
		</section>
		<script>
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
			function fetchcustomers(){
				var a = document.getElementById("cgrp").value;
				removeAllOptions(document.getElementById("checkcname"));
				myselect = document.getElementById("checkcname"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				
				if(a.match("all")){
				<?php
					foreach($pcode as $pc){
				?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $pname[$pc]; ?>"); theOption1.value = "<?php echo $pc; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
					}
				?>
				}
				else{
				<?php
					foreach($pcode as $pc){
						echo "if(a == '$pgroup[$pc]'){";
				?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $pname[$pc]; ?>"); theOption1.value = "<?php echo $pc; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
						echo "}";
					}
				?>	
				}
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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
