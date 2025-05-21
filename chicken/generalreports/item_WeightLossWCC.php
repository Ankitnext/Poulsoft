<?php
	//item_WeightLossWCC.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db']; } else{ $db = ''; }
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php";  }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
	$today = date("Y-m-d");
	

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemcodes[$row['code']] = $row['code']; $itemnames[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `inv_sectors` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $officecode[$row['code']] = $row['code']; $officename[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate']; if($fromdate == ""){ $fromdate = $today; } else { $fromdate = $_POST['fromdate']; }
	$todate = $_POST['todate']; if($todate == ""){ $todate = $today; } else { $todate = $_POST['todate']; }
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $ifwlmba = $row['wlmba']; }
	if(isset($_POST['submit']) == true) { $wname = $_POST['wname']; } else { $wname = "select"; } if($wname == "select") { $wnames = $wfnames = $wtnames = ""; } else if($wname == "all") { $wnames = $wfnames = $wtnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; $wfnames = " AND `fromwarehouse` LIKE '$wname'"; $wtnames = " AND `towarehouse` LIKE '$wname'"; }
	if(isset($_POST['submit']) == true) { $idetail = $_POST['iname']; } else { $idetail == "all"; } if($idetail == "all") { $idetails = $iftdetails = ""; } else if($idetail == "") { $idetails = $iftdetails = ""; } else { $idetails = " AND `itemcode` = '$idetail'"; $iftdetails = " AND `code` LIKE '$idetail'"; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=item_WeightLossReportChicken($fromdate-$todate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
			.contentmenu,.contentmenu thead,.contentmenu tr,.contentmenu th,.contentmenu td {
				border: 1px solid black;
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
							<h3>Weight Loss with Chicken Conversion Report</h3>
							<label class="reportheaderlabel"><b style="color: green;">	From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;
							<label class="reportheaderlabel"><b style="color: green;">	To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
						</td>
						<td>
						
						</td>
					</tr>
				</table>
			</header>
		<?php } ?>
			<section class="content" align="center">
				<div class="col-md-18" align="center">
				<?php if($db == ''){?>
				<form action="item_WeightLossWCC.php" method="post">
					<?php } else { ?>
					<form action="item_WeightLossWCC.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="contentmenu">
							<?php if($exoption == "displaypage") { ?>
							<thead style="padding:15px; text-align:left;background-color: #98fb98;">
								<tr>
									<td colspan='25'>&ensp;
										<label class="reportselectionlabel">Date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>&ensp;&ensp;
										<?php if($ifwlmba == 1){ ?>		
											<label class="reportselectionlabel">Item Description</label>&nbsp;
											<select name="iname" id="iname" class="form-control select2">
												<option value="all">-All-</option>
												<?php
													$icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
													$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);$counta = mysqli_num_rows($query);
													if($counta > 0){
														while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
													}else{
														$sql = "SELECT * FROM `item_category` WHERE `description` LIKE 'egg%'"; $query = mysqli_query($conn,$sql);$counta = mysqli_num_rows($query);
														while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
													}
													
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
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="wname" id="wname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($wname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
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
							<?php } ?>
							<?php if($exoption == "exportexcel") { ?>
							<thead>
								<tr>
									<td colspan='22' style='text-align:center;font-weight:bold;font-size:18px;color:red;background-color: #98fb98;'>
										Weight Loss Report for <?php echo $officename[$wname]; ?> on <?php echo $fromdate; ?>
									</td>
								</tr>
							</thead>
							<?php } ?>
							<thead>
								<tr>
									<td colspan='1' style='text-align:center;font-weight:bold;background-color: #98fb98;'></td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Date</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Item</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Opening</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Purchases/Transfer IN</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Sales/Transfer OUT</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Closing</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Actual Closing</td>
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Weight Loss %</td>
									<!--<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Weight Loss %</td>-->
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Spent(Expense)</td>
									<!--<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Received(Cash Back)</td>-->
									<td colspan='2' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Margin</td>
								</tr>
							</thead>
							<thead>
								<tr style='font-weight:bold;background-color: #98fb98;'>
									<td colspan="1">Sl No.</td><td colspan="2"></td><td colspan="2"></td>
									<td>Quantity</td><td>Amount</td>
									<td>Quantity</td><td>Amount</td>
									<td>Quantity</td><td>Amount</td>
									<td>Quantity</td><td>Amount</td>
									<td>Quantity</td><td>Amount</td>
									<td>Percentage(qty)</td><td>Amount</td><!--<td colspan="2"></td>-->
									<td colspan="2"></td><!--<td colspan="2">--></td><td colspan="2"></td>
								</tr>
							</thead>
							<tbody>
								<?php
									if(isset($_POST['submit']) == true){
										$icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
										
										$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);$counta = mysqli_num_rows($query);
										if($counta > 0){
											while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
										}else{
											$sql = "SELECT * FROM `item_category` WHERE `description` LIKE 'egg%'"; $query = mysqli_query($conn,$sql);$counta = mysqli_num_rows($query);
											while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
										}
										
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
										
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$store = date('Y-m-d', $currentDate);
											foreach($itmcode as $ic){
												$mcode = $store."@".$itmcode[$ic];
												$mainfilter[$mcode] = $mcode;
											}
										}
										//Quantity Conversion Ratio
										$sql = "SELECT * FROM `item_qty_conversion` WHERE `active` = '1' ORDER BY `id` ASC"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){ $con_qty[$row['itemcode']] = $row['con_qty']; }
										
										//Items based on chicken
										$cicats = $ckn_codes = "";
										$sql = "SELECT * FROM `item_category` WHERE `description` IN ('Chicken','SKIN OUT','Dressed Chicken') "; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){ $cicats = $row['code']; }
										if($_SESSION['dbase'] == "vpspoulsoft_chicken_ka_starchicken2"){ $item_notin = " AND `description` NOT IN ('WINGS')"; } else{ $item_notin = ""; }
										$sql = "SELECT * FROM `item_details` WHERE `category` LIKE '$cicats'".$item_notin; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){ if($ckn_codes == ""){ $ckn_codes = $row['code']; } else { $ckn_codes = $ckn_codes."','".$row['code']; } }
										
										$ckn_idetails = " AND `itemcode` IN ('$ckn_codes')";
										
										$seq = "SELECT * FROM `item_closingstock` WHERE `date` >='$pdate' AND `date` <= '$todate'";
										$groupby = " ORDER BY `code` ASC";
										$sql = $seq."".$iftdetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$obcode = $row['date']."@".$row['code'];
											$openingdetails[$obcode] = $openingdetails[$obcode] + $row['closedquantity'];
											$openingdetailsp[$obcode] = $openingdetailsp[$obcode] + $row['price'];
											$openingdetailsa[$obcode] = $openingdetailsa[$obcode] + ($row['price'] * $row['closedquantity']);
											$item_count[$obcode] = $item_count[$obcode] + 1;
										}
										//$old_count = "";
										$seq = "SELECT * FROM `item_closingstock` WHERE `date` >='$pdate' AND `date` <= '$todate' AND `code` IN ($icode)";
										$groupby = " ORDER BY `date` ASC";
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
											if($con_qty[$row['itemcode']] == "" || $con_qty[$row['itemcode']] == NULL || $con_qty[$row['itemcode']] == "0" || $con_qty[$row['itemcode']] == "0.00"){ $con_qty[$row['itemcode']] = 1; }
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
													$pur_qty[$obcode] = $pur_qty[$obcode] + ($row['netweight'] + $pur_ckn_inv_qty[$row['date']]);
													$pur_amt[$obcode] = $pur_amt[$obcode] + ($row['totalamt'] + $pur_ckn_inv_amt[$row['date']]);
													$old_date = $row['date'];
												}
												else{
													$pur_qty[$obcode] = $pur_qty[$obcode] + $row['netweight'];
													$pur_amt[$obcode] = $pur_amt[$obcode] + $row['totalamt'];
													$old_date = $row['date'];
												}
											}
											else if(strpos($ckn_codes, $row['itemcode']) !== false && $pur_qty[$row['date']."@BRB-0001"] == ""){
												$pur_price[$row['date']."@BRB-0001"] = $row['itemprice'];
												$pur_qty[$row['date']."@BRB-0001"] = $pur_ckn_inv_qty[$row['date']];
												$pur_amt[$row['date']."@BRB-0001"] = $pur_ckn_inv_amt[$row['date']];
											}
											else{
												$pur_qty[$obcode] = $pur_qty[$obcode] + $row['netweight'];
												$pur_price[$obcode] = $row['itemprice'];
												$pur_amt[$obcode] = $pur_amt[$obcode] + $row['totalamt'];
											}
										}
										//$old_count = "";
										$pur_count = array();
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
											if($con_qty[$row['itemcode']] == "" || $con_qty[$row['itemcode']] == NULL || $con_qty[$row['itemcode']] == "0" || $con_qty[$row['itemcode']] == "0.00"){ $con_qty[$row['itemcode']] = 1; }
											$ckn_inv_qty[$row['date']] = $ckn_inv_qty[$row['date']] + ($row['netweight'] * $con_qty[$row['itemcode']]);
											$ckn_inv_amt[$row['date']] = $ckn_inv_amt[$row['date']] + $row['totalamt'];
										}
										echo "<br/>".$ckn_inv_qty["2023-12-07"];
										$old_date = "";
										$inv_qty = $inv_amt = array();
										$seq = "SELECT * FROM `customer_sales` WHERE `date` >='$fromdate' AND `date` <= '$todate'";
										$groupby = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC,`itemcode` ASC";
										$sql = $seq."".$idetails."".$wnames."".$groupby; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($row['itemcode'] == "BRB-0001"){
												$obcode = $row['date']."@".$row['itemcode'];
												$inv_price[$obcode] = $row['itemprice'];
												if($old_date != $row['date']){
													$inv_qty[$obcode] = $inv_qty[$obcode] + ($row['netweight'] + $ckn_inv_qty[$row['date']]);
													$inv_amt[$obcode] = $inv_amt[$obcode] + ($row['totalamt'] + $ckn_inv_amt[$row['date']]);
													$old_date = $row['date'];
												}
												else{
													$inv_qty[$obcode] = $inv_qty[$obcode] + $row['netweight'];
													$inv_amt[$obcode] = $inv_amt[$obcode] + $row['totalamt'];
													$old_date = $row['date'];
												}
											}
											else if(strpos($ckn_codes, $row['itemcode']) !== false && $inv_qty[$row['date']."@BRB-0001"] == ""){
												$inv_price[$row['date']."@BRB-0001"] = $row['itemprice'];
												$inv_qty[$row['date']."@BRB-0001"] = $ckn_inv_qty[$row['date']];
												$inv_amt[$row['date']."@BRB-0001"] = $ckn_inv_amt[$row['date']];
											}
											else{
												$obcode = $row['date']."@".$row['itemcode'];
												$inv_qty[$obcode] = $inv_qty[$obcode] + $row['netweight'];
												$inv_price[$obcode] = $row['itemprice'];
												$inv_amt[$obcode] = $inv_amt[$obcode] + $row['totalamt'];
											}
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
										$sql = $seq."".$iftdetails."".$wfnames."".$groupby; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$obcode = $row['date']."@".$row['code'];
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
										$sl = 1; $tob_qty = $tob_amt = $tp_qty = $tp_amt = $ts_qty = $ts_amt = $tc_qty = $tc_amt = $ta_qty = $ta_amt = $tw_qty = $tw_amt = $te_amt =  $tr_amt =  $tm_amt = 0; $olddate = "";
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
											
											if(number_format_ind($openingdetails[$pre_code]) == "0.00" && number_format_ind($pur_qty[$id]) == "0.00" && number_format_ind($inv_qty[$id]) == "0.00" && number_format_ind($openingdetails[$id]) == "0.00" && number_format_ind($tin_qty[$id]) == "0.00" && number_format_ind($tou_qty[$id]) == "0.00"){
												
											}
											else{
												echo "<tr>";
												    echo "<td colspan='1'>".$sl++."</td>";
													echo "<td colspan='2'>".date("d.m.Y",strtotime($dates))."</td>";
													echo "<td colspan='2'>".$itemnames[$icodes]."</td>";
													echo "<td align='right'>".number_format_ind($openingdetails[$pre_code])."</td>";
													echo "<td align='right'>".number_format_ind($obl_amt)."</td>";
													$tob_qty = $tob_qty + $openingdetails[$pre_code];
													$tob_amt = $tob_amt + $obl_amt;
													//echo "<br/>".$item_count;
													//echo "<br/>".$item_count[$dates]." | ".$openingdetail_count[$pre_code]." | ".$pur_count[$dates]." | ".$tin_amt[$dates]." |- ".$inv_count[$dates]." | ".$tou_count[$dates]." | ".$openingdetail_count[$dates];
													//$v_count = max($openingdetail_count[$pre_code],$pur_count[$dates],$tin_amt[$dates],$inv_count[$dates],$tou_count[$dates],$openingdetail_count[$dates]);
													$v_count = $item_count[$dates];
													if($wnames == "") {
														$p_qty = $pur_qty[$id];
														$p_amt = $pur_amt[$id];
														$s_qty = $inv_qty[$id];
														$s_amt = $inv_amt[$id];
														
														$tp_qty = $tp_qty + $p_qty; $tp_amt = $tp_amt + $p_amt;
														$ts_qty = $ts_qty + $s_qty; $ts_amt = $ts_amt + $s_amt;
														
														$c_qty = ($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id];
														$c_amt = $act_price * (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id]);
														
														$tc_qty = $tc_qty + $c_qty; $tc_amt = $tc_amt + $c_amt;
														
														$a_qty = $openingdetails[$id];
														//$a_amt = $act_amt;
														$a_amt = $openingdetailsp[$id] * $a_qty;
														
														$ta_qty = $ta_qty + $a_qty; $ta_amt = $ta_amt + $a_amt;
														
														$w_qty = $openingdetails[$id] - (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id]);
														if($w_qty > 0 && ($p_qty + $openingdetails[$pre_code]) > 0){
															$w_per = ($w_qty / ($p_qty + $openingdetails[$pre_code])) * 100;
														}
														else{
															$w_per = 0;
														}
														
														$for_avg_w_per = $for_avg_w_per + $w_per;
														$wfc = $wfc + 1;
														$w_amt = $openingdetailsp[$id] * ($openingdetails[$id] - (($pur_qty[$id] + $openingdetails[$pre_code]) - $inv_qty[$id]));
														
														$tw_qty = $tw_qty + $w_qty; $tw_amt = $tw_amt + $w_amt;
														$link = "cus_chickensaleswithconreport.php?fromdate=$dates&todate=$dates&cname=all&iname=all&wname=all&ucode=all";
														echo "<td align='right'>".number_format_ind($p_qty)."</td>";
														echo "<td align='right'>".number_format_ind($p_amt)."</td>";
														if($itemnames[$icodes] == "Broiler Birds"){
														echo "<td align='right'><a href='$link' target='_BLANK'>".number_format_ind($s_qty)."</a></td>";
														}
														else{
															echo "<td align='right'>".number_format_ind($s_qty)."</td>";
														}
														echo "<td align='right'>".number_format_ind($s_amt)."</td>";
														
														echo "<td align='right'>".number_format_ind($c_qty)."</td>";
														echo "<td align='right'>".number_format_ind($c_amt)."</td>";
														
														echo "<td align='right'>".number_format_ind($a_qty)."</td>";
														echo "<td align='right'>".number_format_ind($a_amt)."</td>";
														
														echo "<td align='right'>".number_format_ind($w_per)."%(".number_format_ind($w_qty).") </td>";
														echo "<td align='right'>".number_format_ind($w_amt)."</td>";
														if($olddate == $dates){
															$m_amt = 0;
															$m_amt = ($inv_amt[$id] - (($pur_amt[$id] + $obl_amt) - $a_amt));
															$tm_amt = $tm_amt + $m_amt;
															echo "<td align='right' colspan='2'>".number_format_ind($m_amt)."</td>";
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
															
															echo "<td rowspan='$v_count' align='right' colspan='2'>".number_format_ind($e_amt)."</td>";
															//echo "<td rowspan='$v_count' align='right' colspan='2'>".number_format_ind($r_amt)."</td>";
															
															echo "<td align='right' colspan='2'>".number_format_ind($m_amt)."</td>";
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
														
														$c_qty = ($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ($inv_qty[$id] + $tou_qty[$id]);
														$c_amt = $act_price * (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ($inv_qty[$id] + $tou_qty[$id]));
														
														$tc_qty = $tc_qty + $c_qty; $tc_amt = $tc_amt + $c_amt;
														
														$a_qty = $openingdetails[$id];
														$a_amt = $openingdetailsp[$id] * $a_qty;
														
														$ta_qty = $ta_qty + $a_qty; $ta_amt = $ta_amt + $a_amt;
														
														$w_qty = $openingdetails[$id] - (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ($inv_qty[$id] + $tou_qty[$id]));
														if(($p_qty + $openingdetails[$pre_code]) > 0){ $w_per = ($w_qty / ($p_qty + $openingdetails[$pre_code])) * 100; } else{ $w_per = 0; }
														
														$for_avg_w_per = $for_avg_w_per + $w_per;
														$wfc = $wfc + 1;
														$w_amt = $openingdetailsp[$id] * ($openingdetails[$id] - (($pur_qty[$id] + $tin_qty[$id] + $openingdetails[$pre_code]) - ($inv_qty[$id] + $tou_qty[$id])));
														
														$tw_qty = $tw_qty + $w_qty; $tw_amt = $tw_amt + $w_amt;
														
														
														$link = "cus_chickensaleswithconreport.php?fromdate=$dates&todate=$dates&cname=all&iname=all&wname=all&ucode=all";
														echo "<td align='right'>".number_format_ind($p_qty)."</td>";
														echo "<td align='right'>".number_format_ind($p_amt)."</td>";
														if($itemnames[$icodes] == "Broiler Birds"){
														echo "<td align='right'><a href='$link' target='_BLANK'>".number_format_ind($s_qty)."</a></td>";
														}
														else{
															echo "<td align='right'>".number_format_ind($s_qty)."</td>";
														}
														echo "<td align='right'>".number_format_ind($s_amt)."</td>";
														
														echo "<td align='right'>".number_format_ind($c_qty)."</td>";
														echo "<td align='right'>".number_format_ind($c_amt)."</td>";
														
														echo "<td align='right'>".number_format_ind($a_qty)."</td>";
														echo "<td align='right'>".number_format_ind($a_amt)."</td>";
														
														echo "<td align='right'>".number_format_ind($w_per)."%(".number_format_ind($w_qty).") </td>";
														echo "<td align='right'>".number_format_ind($w_amt)."</td>";
														if($olddate == $dates){
															$m_amt = (($inv_amt[$id] + $tou_amt[$id]) - (($pur_amt[$id] + $tin_amt[$id] + $obl_amt) - $a_amt));
															$tm_amt = $tm_amt + $m_amt;
															echo "<td align='right' colspan='2'>".number_format_ind($m_amt)."</td>";
														}
														else {
															$e_amt = $pv_amt[$dates];
															//$r_amt = $rv_amt[$dates];
															$r_amt = 0;
															$m_amt = (($inv_amt[$id] + $tou_amt[$id]) - (($pur_amt[$id] + $tin_amt[$id] + $obl_amt) - $a_amt)) - ($pv_amt[$dates] - $rv_amt[$dates]);
															
															$te_amt = $te_amt + $e_amt;
															$tr_amt = $tr_amt + $r_amt;
															$tm_amt = $tm_amt + $m_amt;
															echo "<td rowspan='$v_count' align='right' colspan='2'>".number_format_ind($e_amt)."</td>";
															//echo "<td rowspan='$v_count' align='right' colspan='2'>".number_format_ind($r_amt)."</td>";
															
															echo "<td align='right' colspan='2'>".number_format_ind($m_amt)."</td>";
															$olddate = $dates;
														}
													}
												echo "</tr>";
											}
										}
										echo "<tr style='font-weight:bold;'>";
											echo "<td colspan='5' style='font-weight:bold;text-align:center;'>Grand Total</td>";
											echo "<td align='right'>".number_format_ind($tob_qty)."</td>";
											echo "<td align='right'>".number_format_ind($tob_amt)."</td>";
											echo "<td align='right'>".number_format_ind($tp_qty)."</td>";
											echo "<td align='right'>".number_format_ind($tp_amt)."</td>";
											echo "<td align='right'>".number_format_ind($ts_qty)."</td>";
											echo "<td align='right'>".number_format_ind($ts_amt)."</td>";
											echo "<td align='right'>".number_format_ind($tc_qty)."</td>";
											echo "<td align='right'>".number_format_ind($tc_amt)."</td>";
											echo "<td align='right'>".number_format_ind($ta_qty)."</td>";
											echo "<td align='right'>".number_format_ind($ta_amt)."</td>";
											if(($tob_qty + $tp_qty) > 0){ $fwa_per = ($tw_qty / ($tob_qty + $tp_qty)) * 100; } else{ $fwa_per = 0; }
											
											echo "<td align='right'>".number_format_ind($fwa_per)."%(".number_format_ind($tw_qty).") </td>";
											//echo "<td align='right'>".number_format_ind($tw_qty)."</td>";
											echo "<td align='right'>".number_format_ind($tw_amt)."</td>";
											echo "<td align='right' colspan='2'>".number_format_ind($te_amt)."</td>";
											//echo "<td align='right' colspan='2'>".number_format_ind($tr_amt)."</td>";
											echo "<td align='right' colspan='2'>".number_format_ind($tm_amt)."</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</form>
				</div>
			</section>
	
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	<?php include "header_foot.php"; ?>
</html>
