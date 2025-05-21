<?php
//item_DwWeightLossReport.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
include "../config.php"; include "header_head.php"; include "number_format_ind.php"; $today = date("Y-m-d");
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ifwlmba = $row['wlmba']; }
$icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ($icats)"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
if(isset($_POST['submit']) == true) {
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	$iname = $_POST['iname'];
	$wname = $_POST['wname'];
	if($iname == "all"){ $icodes = ""; foreach($item_code as $icode){ if($icodes == ""){ $icodes = $icode; } else{ $icodes = $icodes."','".$icode; } } } else{ $icodes = $iname; }
	if($wname == "all"){ $scodes = ""; foreach($sector_code as $scode){ if($scodes == ""){ $scodes = $scode; } else{ $scodes = $scodes."','".$scode; } } } else{ $scodes = $wname; }
}
else{
	$fromdate = $todate = $today;
	$iname = $wname = "all";
}
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="dist/jquery.table2excel.js"></script>
		<style>
			body {
				overflow: auto;
				
			}
			.contentmenu,.contentmenu thead,.contentmenu tr,.contentmenu th,.contentmenu td {
				font-size: 14px;
				border: 0.1vh solid black;
				border-collapse: collapse;
			}
			.contentmenu {
				width: 100%;
			}
			.contentmenu thead {
				text-align:center;
				font-weight:bold;
			}
			.contentmenu td {
				padding: 2px;
			}
			.contentmenu #ac {
				text-align:right;
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
		</style>
	</head>
	<body>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Date Wise Weight Loss</h3>
						<label class="reportheaderlabel"><b style="color: green;">	From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">	To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
					<td>
						
					</td>
				</tr>
			</table>
		</header>
		<section class="content" align="center">
			<div class="col-md-18" align="center">
				<form action="item_DwWeightLossReport.php" method="post">
					<table class="contentmenu table2excel" data-tableName="Test Table 1">
						<thead class="noExl" style="padding:15px; text-align:left;background-color: #98fb98;">
							<tr>
								<td colspan='26'>&ensp;
									<label class="reportselectionlabel">Date</label>&nbsp;
									<input type="text" name="fromdate" id="datepickers1" class="formcontrol" style="width:100px;" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>&ensp;&ensp;
									<label class="reportselectionlabel">To Date</label>&nbsp;
									<input type="text" name="todate" id="datepickers" class="formcontrol" style="width:100px;" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>&ensp;&ensp;
									<label class="reportselectionlabel">Item Description</label>&nbsp;
									<select name="iname" id="iname" class="form-control select2">
										<option value="all">-All-</option>
										<?php
										foreach($item_code as $icode){
										?>
											<option value="<?php echo $icode; ?>" <?php if($icode == $iname) { echo 'selected'; } ?> ><?php echo $item_name[$icode]; ?></option>
										<?php
											}
										?>
									</select>&ensp;&ensp;	
									<label class="reportselectionlabel">Warehouse</label>&nbsp;
									<select name="wname" id="wname" class="form-control select2">
										<option value="all">-All-</option>
										<?php
										foreach($sector_code as $scode){
										?>
											<option value="<?php echo $scode; ?>" <?php if($scode == $wname) { echo 'selected'; } ?> ><?php echo $sector_name[$scode]; ?></option>
										<?php
											}
										?>
									</select>&ensp;&ensp;
									<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									<?php //if(isset($_POST['submit']) == true){ echo "&ensp;&ensp;<button class='btn btn-success btn-sm exportToExcel'>Excel</button>"; } ?>
								</td>
							</tr>
						</thead>
						<thead>
							<tr>
								<td colspan='1' style='text-align:center;font-weight:bold;background-color: #98fb98;'></td>
								<td colspan='1' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Date</td>
								<td colspan='1' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Item</td>
								<td colspan='3' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Opening</td>
								<td colspan='3' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Purchases/Transfer IN</td>
								<td colspan='3' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Today's</td>
								<td colspan='3' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Sales/Transfer OUT</td>
								<td colspan='3' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Closing</td>
								<td colspan='3' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Actual Closing</td>
								<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Weight Loss %</td>
								<td colspan='1' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Spent(Expense)</td>
								<td colspan='1' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Margin</td>
							</tr>
						</thead>
						<thead>
							<tr style='font-weight:bold;background-color: #98fb98;'>
								<td colspan="1">Sl&nbsp;No.</td><td colspan="1"></td><td colspan="1"></td>
								<td>Kgs</td><td>Rate</td><td>Amount</td>
								<td>Kgs</td><td>Rate</td><td>Amount</td>
								<td>Kgs</td><td>Rate</td><td>Amount</td>
								<td>Kgs</td><td>Rate</td><td>Amount</td>
								<td>Kgs</td><td>Rate</td><td>Amount</td>
								<td>Kgs</td><td>Rate</td><td>Amount</td>
								<td>Percentage(qty)</td><td>Amount</td>
								<td colspan="1"></td><td colspan="1"></td>
							</tr>
						</thead>
						<tbody>
						<?php
							if(isset($_POST['submit']) == true){
								$fromdate = date("Y-m-d",strtotime($fromdate)); $todate = date("Y-m-d",strtotime($todate));
								$d = date("d",strtotime($fromdate)); $m = date("m",strtotime($fromdate)); $y = date("Y",strtotime($fromdate));
								$pdate = date('Y-m-d', strtotime($fromdate.'-1 days')); $fdate = strtotime($fromdate); $tdate = strtotime($todate); $mainfilter = array();
								for($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){ $store = date('Y-m-d', $currentDate); foreach($item_code as $ic){ $mcode = $store."@".$item_code[$ic]; $mainfilter[$mcode] = $mcode; } }
								
								$wnames = " AND `warehouse` IN ('$scodes')"; $wfnames = " AND `fromwarehouse` IN ('$scodes')"; $wtnames = " AND `towarehouse` IN ('$scodes')";
								$idetails = " AND `itemcode` IN ('$icodes')"; $iftdetails = " AND `code` IN ('$icodes')";
								//Get Opening Details
								$old_code = "";
								$seq = "SELECT * FROM `item_closingstock` WHERE `date` >='$pdate' AND `date` <= '$todate'";$groupby = "";
								$sql = $seq."".$iftdetails."".$wnames; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$obcode = $row['date']."@".$row['code'];
									$open_qty[$obcode] = $open_qty[$obcode] + $row['closedquantity'];
									$open_price[$obcode] = $open_price[$obcode] + $row['price'];
									$open_amt[$obcode] = $row['closedquantity'] * $row['price'];
									$product_code[$row['code']] = $row['code'];
									if($old_code == $obcode){ }
									else{ $open_count[$row['date']] = $open_count[$row['date']] + 1; $old_code = $obcode; }
								}
								//Purchases
								$old_code = "";
								$seq = "SELECT * FROM `pur_purchase` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
								$sql = $seq."".$idetails."".$wnames; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$obcode = $row['date']."@".$row['itemcode'];
									$pur_qty[$obcode] = $pur_qty[$obcode] + $row['netweight'];
									$pur_price[$obcode] = $row['itemprice'];
									$pur_amt[$obcode] = $pur_amt[$obcode] + $row['totalamt'];
									$product_code[$row['itemcode']] = $row['itemcode'];
									if($old_code == $obcode){ }
									else{ $pur_count[$row['date']] = $pur_count[$row['date']] + 1; $old_code = $obcode; }
								}
								//Stock Transfer IN
								$old_code = "";
								$seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
								$sql = $seq."".$iftdetails."".$wtnames; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$obcode = $row['date']."@".$row['code'];
									$tin_qty[$obcode] = $tin_qty[$obcode] + $row['quantity'];
									$tin_price[$obcode] = $row['price'];
									$tin_amt[$obcode] = $tin_amt[$obcode] + $row['quantity'] * $row['price'];
									$product_code[$row['code']] = $row['code'];
									if($old_code == $obcode){ }
									else{ $tin_count[$row['date']] = $tin_count[$row['date']] + 1; $old_code = $obcode; }
								}
								//Sales
								$old_code = "";
								$seq = "SELECT * FROM `customer_sales` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
								$sql = $seq."".$idetails."".$wnames; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$obcode = $row['date']."@".$row['itemcode'];
									$inv_qty[$obcode] = $inv_qty[$obcode] + $row['netweight'];
									$inv_price[$obcode] = $row['itemprice'];
									$inv_amt[$obcode] = $inv_amt[$obcode] + $row['totalamt'];
									$product_code[$row['itemcode']] = $row['itemcode'];
									if($old_code == $obcode){ }
									else{ $inv_count[$row['date']] = $inv_count[$row['date']] + 1; $old_code = $obcode; }
								}
								//Stock Transfer Out
								$old_code = "";
								$seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
								$sql = $seq."".$iftdetails."".$wfnames; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$obcode = $row['date']."@".$row['code'];
									$tou_qty[$obcode] = $tou_qty[$obcode] + $row['quantity'];
									$tou_price[$obcode] = $row['price'];
									$tou_amt[$obcode] = $tou_amt[$obcode] + $row['quantity'] * $row['price'];
									$product_code[$row['code']] = $row['code'];
									if($old_code == $obcode){ }
									else{ $tou_count[$row['date']] = $tou_count[$row['date']] + 1; $old_code = $obcode; }
								}
								$seq = "SELECT date,SUM(amount) as pvamt FROM `acc_vouchers` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `prefix` ='PV'";
								$groupby = " GROUP BY `date` ORDER BY `date` ASC";
								$sql = $seq."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$obcode = $row['date'];
									$pv_amt[$obcode] = $row['pvamt'];
								}
								$old_date = "";
							    $sl = 1; $tacls_amt = $tacls_qty = $tcls_amt = $tcls_qty = $tsi_qty = $tsi_amt = $tob_qty = $tob_amt = $tpi_qty = $tpi_amt = $texp_amt = $ttot_mgn = $ttod_amt = $ttod_qty = $ttod_price = 0;
								foreach($mainfilter as $dai){
									$id = array(); $id = explode("@",$dai);
									$cur_date = $id[0]; $cur_item = $id[1];
									
									$pre_date = date('Y-m-d', strtotime($id[0].'-1 days'));
									
									$pre_code = $pre_date."@".$cur_item;
									$cur_code = $cur_date."@".$cur_item;
									
									if(number_format_ind($open_qty[$pre_code]) == "0.00" && number_format_ind($pur_qty[$cur_code]) == "0.00" && number_format_ind($inv_qty[$cur_code]) == "0.00" && number_format_ind($openingdetails[$cur_code]) == "0.00" && number_format_ind($tin_qty[$cur_code]) == "0.00" && number_format_ind($tou_qty[$cur_code]) == "0.00"){
									
									}
									else{
										//Opening Details Fetch
										$ob_qty = $ob_price = $ob_amt = 0;
										$ob_qty = $open_qty[$pre_code];
										if(!empty($open_amt[$pre_code]) && $open_amt[$pre_code] > 0 && !empty($open_qty[$pre_code]) && $open_qty[$pre_code] > 0){
											$ob_price = $open_amt[$pre_code] / $open_qty[$pre_code];
										}
										else{
											$ob_price = 0;
										}
										
										$ob_amt = $open_amt[$pre_code];
										if(number_format_ind($ob_qty) == "NAN.00" || number_format_ind($ob_qty) == ".00"){ $ob_qty = 0; }
										if(number_format_ind($ob_price) == "NAN.00" || number_format_ind($ob_price) == ".00"){ $ob_price = 0; }
										if(number_format_ind($ob_amt) == "NAN.00" || number_format_ind($ob_amt) == ".00"){ $ob_amt = 0; }
										$tob_qty = $tob_qty + $ob_qty;
										$tob_amt = $tob_amt + $ob_amt;
										
										//Purchases
										$pi_qty = $pi_price = $pi_amt = 0;
										$pi_qty = $pur_qty[$cur_code];
										$pi_amt = $pur_amt[$cur_code];
										//$pi_price = $pur_price[$cur_code];
										if(number_format_ind($pi_qty) == "NAN.00" || number_format_ind($pi_qty) == ".00"){ $pi_qty = 0; }
										if(number_format_ind($pi_amt) == "NAN.00" || number_format_ind($pi_amt) == ".00"){ $pi_amt = 0; }
										$tpi_qty = $tpi_qty + $pi_qty;
										$tpi_amt = $tpi_amt + $pi_amt;
										
										//Transfer IN
										$ti_qty = $ti_price = $ti_amt = 0;
										$ti_qty = $tin_qty[$cur_code];
										$ti_amt = $tin_amt[$cur_code];
										//$ti_price = $tin_price[$cur_code];
										if($ti_amt > 0 && $ti_amt > 0){
											$ti_price = $ti_amt / $ti_qty;
										}
										else{
											$ti_price = 0;
										}
										
										if(number_format_ind($ti_qty) == "NAN.00" || number_format_ind($ti_qty) == ".00"){ $ti_qty = 0; }
										if(number_format_ind($ti_price) == "NAN.00" || number_format_ind($ti_price) == ".00"){ $ti_price = 0; }
										if(number_format_ind($ti_amt) == "NAN.00" || number_format_ind($ti_amt) == ".00"){ $ti_amt = 0; }
										$tpi_qty = $tpi_qty + $ti_qty;
										$tpi_amt = $tpi_amt + $ti_amt;
										
										//Purchase Price
										if(($pi_amt + $ti_amt) > 0 && ($pi_qty + $ti_qty) > 0){
											$pi_price = ($pi_amt + $ti_amt) / ($pi_qty + $ti_qty);
										}
										else{
											$pi_price = 0;
										}
										
										if(number_format_ind($pi_price) == "NAN.00" || number_format_ind($pi_price) == ".00"){ $pi_price = 0; }
										
										//Today's stock
										$tod_qty = $tod_price = $tod_amt = 0;
										$tod_qty = $ob_qty + $pi_qty + $ti_qty;
										$tod_amt = $ob_amt + $pi_amt + $ti_amt;
										if($tod_amt > 0 && $tod_qty > 0){
											$tod_price = $tod_amt / $tod_qty;
										}
										else{
											$tod_price = 0;
										}
										
										$ttod_qty = $ttod_qty + $tod_qty;
										$ttod_amt = $ttod_amt + $tod_amt;
										if(number_format_ind($tod_qty) == "NAN.00" || number_format_ind($tod_qty) == ".00"){ $tod_qty = 0; }
										if(number_format_ind($tod_price) == "NAN.00" || number_format_ind($tod_price) == ".00"){ $tod_price = 0; }
										if(number_format_ind($tod_amt) == "NAN.00" || number_format_ind($tod_amt) == ".00"){ $tod_amt = 0; }
										
										//Sales
										$si_qty = $si_price = $si_amt = 0;
										$si_qty = $inv_qty[$cur_code];
										$si_amt = $inv_amt[$cur_code];
										//$si_price = $inv_price[$cur_code];
										if(number_format_ind($si_qty) == "NAN.00" || number_format_ind($si_qty) == ".00"){ $si_qty = 0; }
										if(number_format_ind($si_amt) == "NAN.00" || number_format_ind($si_amt) == ".00"){ $si_amt = 0; }
										$tsi_qty = $tsi_qty + $si_qty;
										$tsi_amt = $tsi_amt + $si_amt;
										
										//Transfer OUT
										$to_qty = $to_price = $to_amt = 0;
										$to_qty = $tou_qty[$cur_code];
										$to_amt = $tou_amt[$cur_code];
										if($to_amt > 0 && $to_qty > 0){
											$to_price = $to_amt / $to_qty;
										}
										else{
											$to_price = 0;
										}
										
										if(number_format_ind($to_qty) == "NAN.00" || number_format_ind($to_qty) == ".00"){ $to_qty = 0; }
										if(number_format_ind($to_price) == "NAN.00" || number_format_ind($to_price) == ".00"){ $to_price = 0; }
										if(number_format_ind($to_amt) == "NAN.00" || number_format_ind($to_amt) == ".00"){ $to_amt = 0; }
										$tsi_qty = $tsi_qty + $to_qty;
										$tsi_amt = $tsi_amt + $to_amt;
										
										//Sales Price
										if(($si_amt + $to_amt) > 0 && ($si_qty + $to_qty) > 0){
											$si_price = ($si_amt + $to_amt) / ($si_qty + $to_qty);
										}
										else{
											$si_price = 0;
										}
										
										if(number_format_ind($si_price) == "NAN.00" || number_format_ind($si_price) == ".00"){ $si_price = 0; }
										
										//Closing Calculations
										$cls_qty = $cls_price = $cls_amt = 0;
										$cls_qty = ($ob_qty + $pi_qty + $ti_qty) - ($si_qty + $to_qty);
										if($pi_price != 0){ $cls_price = $pi_price; }
										else if($ob_price != 0){ $cls_price = $ob_price; }
										else if($si_price != 0){ $cls_price = $si_price; }
										else{ $cls_price = 0; }
										$cls_amt = $cls_qty * $cls_price;
										if(number_format_ind($cls_qty) == "NAN.00" || number_format_ind($cls_qty) == ".00"){ $cls_qty = 0; }
										if(number_format_ind($cls_price) == "NAN.00" || number_format_ind($cls_price) == ".00"){ $cls_price = 0; }
										if(number_format_ind($cls_amt) == "NAN.00" || number_format_ind($cls_amt) == ".00"){ $cls_amt = 0; }
										$tcls_qty = $tcls_qty + $cls_qty;
										$tcls_amt = $tcls_amt + $cls_amt;
										
										//Actual Closing
										$acls_qty = $acls_price = $acls_amt = 0;
										$acls_qty = $open_qty[$cur_code];
										$acls_amt = $open_amt[$cur_code];
										if($acls_amt > 0 && $acls_qty > 0){
											$acls_price = $acls_amt / $acls_qty;
										}
										else{
											$acls_price = 0;
										}
										
										if(number_format_ind($acls_qty) == "NAN.00" || number_format_ind($acls_qty) == ".00"){ $acls_qty = 0; }
										if(number_format_ind($acls_price) == "NAN.00" || number_format_ind($acls_price) == ".00"){ $acls_price = 0; }
										if(number_format_ind($acls_amt) == "NAN.00" || number_format_ind($acls_amt) == ".00"){ $acls_amt = 0; }
										$tacls_qty = $tacls_qty + $acls_qty;
										$tacls_amt = $tacls_amt + $acls_amt;
										
										//Weight Loss Calculations
										$wls_qty = $wls_price = $wls_per = $wls_amt = 0;
										$wls_qty = ($ob_qty + $pi_qty + $ti_qty) - ($si_qty + $to_qty + $acls_qty);
										if(number_format_ind($wls_qty) == "NAN.00" || number_format_ind($wls_qty) == ".00"){ $wls_qty = 0; }
										
										if($ob_price != 0 || $ob_price != ""){ $wls_price = $ob_price; }
										else if($pi_price != 0 || $pi_price != ""){ $wls_price = $pi_price; }
										else if($ti_price != 0 || $ti_price != ""){ $wls_price = $ti_price; }
										else{ $wls_price = 0; }
										
										if(number_format_ind($acls_qty) === number_format_ind($cls_qty) || $wls_price == 0 || $wls_qty == 0){
											$wls_qty = $wls_amt = $wls_per = 0;
										}
										else{
											if($wls_qty > 0 && $tod_qty > 0){
												$wls_per = ($wls_qty / $tod_qty) * 100;
											}
											else{
												$wls_per = 0;
											}
											
											$wls_amt = $wls_qty * $wls_price;
											if(number_format_ind($wls_per) == "-INF.00" || number_format_ind($wls_per) == "INF.00" || number_format_ind($wls_per) == "NAN.00" || number_format_ind($wls_per) == ".00"){ $wls_per = "0.00"; }
										}
										echo "<tr>";
										echo "<td style='width:min-content;'>".$sl++."</td>";
										echo "<td>".date("d.m.Y",strtotime($cur_date))."</td>";
										echo "<td style='width:min-content;'>".$item_name[$cur_item]."</td>";
										//Opening Display
										echo "<td align='right'>".number_format_ind($ob_qty)."</td>";
										echo "<td align='right'>".number_format_ind($ob_price)."</td>";
										echo "<td align='right'>".number_format_ind($ob_amt)."</td>";
										//Purchase & Transfer IN Display
										echo "<td align='right'>".number_format_ind($pi_qty + $ti_qty)."</td>";
										echo "<td align='right'>".number_format_ind($pi_price)."</td>";
										echo "<td align='right'>".number_format_ind($pi_amt + $ti_amt)."</td>";
										//Today's Stock Display
										echo "<td align='right'>".number_format_ind($tod_qty)."</td>";
										echo "<td align='right'>".number_format_ind($tod_price)."</td>";
										echo "<td align='right'>".number_format_ind($tod_amt)."</td>";
										//Sales & Transfer OUT Display
										echo "<td align='right'>".number_format_ind($si_qty + $to_qty)."</td>";
										echo "<td align='right'>".number_format_ind($si_price)."</td>";
										echo "<td align='right'>".number_format_ind($si_amt + $to_amt)."</td>";
										//Closing Display
										echo "<td align='right'>".number_format_ind($cls_qty)."</td>";
										echo "<td align='right'>".number_format_ind($cls_price)."</td>";
										echo "<td align='right'>".number_format_ind($cls_amt)."</td>";
										//Actual-Closing Display
										echo "<td align='right'>".number_format_ind($acls_qty)."</td>";
										echo "<td align='right'>".number_format_ind($acls_price)."</td>";
										echo "<td align='right'>".number_format_ind($acls_amt)."</td>";
										//Weight-Loss Display
										echo "<td align='right'>".number_format_ind($wls_per)."%(".number_format_ind($wls_qty).")</td>";
										echo "<td align='right'>".number_format_ind($wls_amt)."</td>";
										if($old_date == $cur_date){
											
										}
										else{
											//Expenses
											$exp_amt = 0;
											$exp_amt = $pv_amt[$cur_date];
											
											//Margin Calculations
											$tot_pur = $tot_sale = $tot_mgn = 0;
											foreach($product_code as $pcode){
												$tot_pur = $tot_pur + $open_amt[$pre_date."@".$pcode] + $pur_amt[$cur_date."@".$pcode] + $tin_amt[$cur_date."@".$pcode];
												$tot_sale = $tot_sale + $inv_amt[$cur_date."@".$pcode] + $tou_amt[$cur_date."@".$pcode] + $open_amt[$cur_date."@".$pcode];
											}
											$tot_mgn = ($tot_sale - ($tot_pur + $exp_amt));
											//Row count
											$row_count = max($open_count[$pre_date],$pur_count[$cur_date],$tin_count[$cur_date],$inv_count[$cur_date],$tou_count[$cur_date]);
											if(sizeof($product_code) < $row_count){
												$row_count = sizeof($product_code);
											}
											else if($row_count > sizeof($item_code)){
												$row_count = sizeof($item_code);
											}
											//echo "<br/>".sizeof($product_code)."".sizeof($item_code)."".$row_count;
											$old_date = $cur_date;
											
											//Expenses Display
											$texp_amt = $texp_amt + $exp_amt;
											if(number_format_ind($exp_amt) == ".00" || number_format_ind($exp_amt) == "NAN.00"){ $exp_amt = 0; } else{ }
											echo "<td rowspan='$row_count' align='right'>".number_format_ind($exp_amt)."</td>";
											//Margin Display
											$ttot_mgn = $ttot_mgn + $tot_mgn;
											if(number_format_ind($tot_mgn) == ".00" || number_format_ind($tot_mgn) == "NAN.00"){ $tot_mgn = 0; } else{ }
											echo "<td rowspan='$row_count' align='right'>".number_format_ind($tot_mgn)."</td>";
										}
										echo "</tr>";
									}
								}
								
								if($tob_amt > 0 && $tob_qty > 0){ $tob_price = $tob_amt / $tob_qty; } else{ $tob_price = 0; }
								if($tpi_amt > 0 && $tpi_qty > 0){ $tpi_price = $tpi_amt / $tpi_qty; } else{ $tpi_price = 0; }
								if($ttod_amt > 0 && $ttod_qty > 0){ $ttod_price = $ttod_amt / $ttod_qty; } else{ $ttod_price = 0; }
								if($tsi_amt > 0 && $tsi_qty > 0){ $tsi_price = $tsi_amt / $tsi_qty; } else{ $tsi_price = 0; }
								if($tcls_amt > 0 && $tcls_qty > 0){ $tcls_price = $tcls_amt / $tcls_qty; } else{ $tcls_price = 0; }
								if($tacls_amt > 0 && $tacls_qty > 0){ $tacls_price = $tacls_amt / $tacls_qty; } else{ $tacls_price = 0; }
								if(($tcls_qty - $tacls_qty) > 0 && ($tob_qty + $tpi_qty) > 0){ $twls_per = (($tcls_qty - $tacls_qty) / ($tob_qty + $tpi_qty) ) * 100; } else{ $twls_per = 0; }
								
								$twls_qty = $tcls_qty - $tacls_qty;
								
								if($tob_price != 0){ $twls_price = $tob_price; }
								else if($tpi_price != 0){ $twls_price = $tob_price; }
								else{ $twls_price = 0; }
								
								
								if(number_format_ind($tcls_qty) === number_format_ind($tacls_qty) || $twls_price == 0 || $twls_qty == 0){
									$twls_qty = $twls_price = $twls_per = $twls_amt = 0;
								}
								else{
									$twls_amt = $twls_qty * $twls_price;
								}
								if(number_format_ind($tob_qty) == "NAN.00" || number_format_ind($tob_qty) == ".00"){ $tob_qty = 0; }
								if(number_format_ind($tob_amt) == "NAN.00" || number_format_ind($tob_amt) == ".00"){ $tob_amt = 0; }
								if(number_format_ind($tpi_qty) == "NAN.00" || number_format_ind($tpi_qty) == ".00"){ $tpi_qty = 0; }
								if(number_format_ind($ttod_price) == "NAN.00" || number_format_ind($ttod_price) == ".00"){ $ttod_price = 0; }
								if(number_format_ind($ttod_amt) == "NAN.00" || number_format_ind($ttod_amt) == ".00"){ $ttod_amt = 0; }
								if(number_format_ind($ttod_qty) == "NAN.00" || number_format_ind($ttod_qty) == ".00"){ $ttod_qty = 0; }
								if(number_format_ind($tpi_amt) == "NAN.00" || number_format_ind($tpi_amt) == ".00"){ $tpi_amt = 0; }
								if(number_format_ind($tsi_qty) == "NAN.00" || number_format_ind($tsi_qty) == ".00"){ $tsi_qty = 0; }
								if(number_format_ind($tsi_amt) == "NAN.00" || number_format_ind($tsi_amt) == ".00"){ $tsi_amt = 0; }
								if(number_format_ind($tcls_qty) == "NAN.00" || number_format_ind($tcls_qty) == ".00"){ $tcls_qty = 0; }
								if(number_format_ind($tcls_amt) == "NAN.00" || number_format_ind($tcls_amt) == ".00"){ $tcls_amt = 0; }
								if(number_format_ind($tacls_qty) == "NAN.00" || number_format_ind($tacls_qty) == ".00"){ $tacls_qty = 0; }
								if(number_format_ind($tacls_amt) == "NAN.00" || number_format_ind($tacls_amt) == ".00"){ $tacls_amt = 0; }
								if(number_format_ind($twls_per) == "NAN.00" || number_format_ind($twls_per) == ".00"){ $twls_per = 0; }
								if(number_format_ind($texp_amt) == "NAN.00" || number_format_ind($texp_amt) == ".00"){ $texp_amt = 0; }
								if(number_format_ind($ttot_mgn) == "NAN.00" || number_format_ind($ttot_mgn) == ".00"){ $ttot_mgn = 0; }
								if(number_format_ind($tob_price) == "NAN.00" || number_format_ind($tob_price) == ".00"){ $tob_price = 0; }
								if(number_format_ind($tpi_price) == "NAN.00" || number_format_ind($tpi_price) == ".00"){ $tpi_price = 0; }
								if(number_format_ind($tsi_price) == "NAN.00" || number_format_ind($tsi_price) == ".00"){ $tsi_price = 0; }
								if(number_format_ind($tcls_price) == "NAN.00" || number_format_ind($tcls_price) == ".00"){ $tcls_price = 0; }
								if(number_format_ind($tacls_price) == "NAN.00" || number_format_ind($tacls_price) == ".00"){ $tacls_price = 0; }
								if(number_format_ind($twls_per) == "NAN.00" || number_format_ind($twls_per) == ".00"){ $twls_per = 0; }
								if(number_format_ind($twls_qty) == "NAN.00" || number_format_ind($twls_qty) == ".00"){ $twls_qty = 0; }
								if(number_format_ind($twls_amt) == "NAN.00" || number_format_ind($twls_amt) == ".00"){ $twls_amt = 0; }
								echo "<tr style='font-weight:bold;'>"; 
								echo "<td colspan='3' style='font-weight:bold;text-align:center;'>Grand Total</td>";
								echo "<td align='right'>".number_format_ind($tob_qty)."</td>";
								echo "<td align='right'>".number_format_ind($tob_price)."</td>";
								echo "<td align='right'>".number_format_ind($tob_amt)."</td>";
								echo "<td align='right'>".number_format_ind($tpi_qty)."</td>";
								echo "<td align='right'>".number_format_ind($tpi_price)."</td>";
								echo "<td align='right'>".number_format_ind($tpi_amt)."</td>";
								echo "<td align='right'>".number_format_ind($ttod_qty)."</td>";
								echo "<td align='right'>".number_format_ind($ttod_price)."</td>";
								echo "<td align='right'>".number_format_ind($ttod_amt)."</td>";
								echo "<td align='right'>".number_format_ind($tsi_qty)."</td>";
								echo "<td align='right'>".number_format_ind($tsi_price)."</td>";
								echo "<td align='right'>".number_format_ind($tsi_amt)."</td>";
								echo "<td align='right'>".number_format_ind($tcls_qty)."</td>";
								echo "<td align='right'>".number_format_ind($tcls_price)."</td>";
								echo "<td align='right'>".number_format_ind($tcls_amt)."</td>";
								echo "<td align='right'>".number_format_ind($tacls_qty)."</td>";
								echo "<td align='right'>".number_format_ind($tacls_price)."</td>";
								echo "<td align='right'>".number_format_ind($tacls_amt)."</td>";
								echo "<td align='right'>".number_format_ind($twls_per)."%(".number_format_ind($twls_qty).")</td>";
								echo "<td align='right'>".number_format_ind($twls_amt)."</td>";
								echo "<td align='right'>".number_format_ind($texp_amt)."</td>";
								echo "<td align='right'>".number_format_ind($ttot_mgn)."</td>";
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</form>
			</div>
		</section>
		<script>
			$(function() {
				$(".exportToExcel").click(function(e){
					var table = $(this).prev('.table2excel');
					if(table && table.length){
						var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
						$(table).table2excel({
							exclude: ".noExl",
							name: "Excel Document Name",
							filename: "DateWiseWeightLossReport.xls",
							fileext: ".xls",
							exclude_img: true,
							exclude_links: true,
							exclude_inputs: true,
							preserveColors: preserveColors
						});
					}
				});
			});
		</script>
		<footer class="noExl" align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
		<script src="../loading_page_out.js"></script>
		<?php include "header_foot.php"; ?>
	</body>
</html>