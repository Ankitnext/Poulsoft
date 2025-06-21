<?php 
			$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
			$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php";
		include "header_head.php";
		include "number_format_ind.php"; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
			
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Display TCDS Calculations' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dtcds_flag = mysqli_num_rows($query);
			
			$today = date("Y-m-d");
			$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
			$idisplay = ''; $ndisplay = 'style="display:none;"';
			$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$pname[$row['code']] = $row['name'];
				$obdate[$row['code']] = $row['obdate'];
				$obtype[$row['code']] = $row['obtype'];
				$obamt[$row['code']] = $row['obamt'];
			}
			$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
			$fromdate = $_POST['fromdate'];
			$todate = $_POST['todate'];
			if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
			$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname'];
			if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `vendorcode` = '$cname'"; }
			
			$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
			$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }
			
			$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Supplier Ledger WTDS Report' AND `active` = '1'";
			$query = mysqli_query($conn,$sql); $lg_count = mysqli_num_rows($query);
			if($lg_count > 0){ while($row = mysqli_fetch_assoc($query)){ $prate_flag = $row['prate']; $vehicle_flag = $row['vehicle_flag']; } } else{ $prate_flag = $vehicle_flag = 0; }
			if($vehicle_flag  == "" || $vehicle_flag == 0){ $vehicle_flag  = 0; }
			if($prate_flag == 1 || $prate_flag == "1"){
				$fdate = date("Y-m-d",strtotime($fromdate));
				$tdate = date("Y-m-d",strtotime($todate));
				$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']] = $row['new_price']; }
			}
		?>
		<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; };
			$url = "../PHPExcel/Examples/SupplierLedgerReportAll-Excel.php";
	
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
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Supplier Ledger</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Supplier:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br/>
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
					<form action="pur_supplierledgerNewUpdated.php" method="post">
					<?php } else { ?>
					<form action="pur_supplierledgerNewUpdated.php?db=<?php echo $db; ?>&cid=<?php echo $cid; ?>" method="post"  >
					<?php } ?>
					
						<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="22">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" style="width:100px;" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" style="width:100px;" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2" style="width:auto;">
											<option value="select">-select-</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($cname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
										<br/>
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="sname" id="sname" class="form-control select2" style="width:auto;">
											<option value="all" selected>-All-</option>
											<?php
												foreach($sector_code as $scode){
											?>
												<option value="<?php echo $sector_code[$scode]; ?>" <?php if($sector_code[$scode] == $_POST['sname']){ echo 'selected'; } ?>><?php echo $sector_name[$scode]; ?></option>
											<?php
												}
											?>
										</select>
										&ensp;&ensp;
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
								if($cname == "" || $cname == "select"){ }
								else if($cname == "all"){ }
								else {
							?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th style="padding: 0 5px;">Date</th>
								<!--<th>Supplier</th>-->
								<th style="padding: 0 5px;">Invoice</th>
								<th style="padding: 0 5px;">Book Invoice</th>
								<th style="padding: 0 5px;">Warehouse</th>
								<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<th>Vehicle No.</th>"; } ?>
								<th style="padding: 0 5px;">Item</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1){ ?><th style="padding: 0 5px;">Jals</th><?php } ?>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th style="padding: 0 5px;">Birds</th><?php } ?>
								<?php if($ifjbwen == 1){ ?> <th style="padding: 0 5px;">Total Weight</th><th style="padding: 0 5px;">Empty Weight</th> <?php } ?>
								<th style="padding: 0 5px;">Net Weight</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th style="padding: 0 5px;">Avg.Weight</th><?php } ?>
								<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<th>Paper Rate</th>"; } ?>
								<th style="padding: 0 5px;">Price</th>
								<!--<th style="padding: 0 5px;">Discount</th>
								<th style="padding: 0 5px;">Tax</th>-->
								<th style="padding: 0 5px;">Amount</th>
								<th style="padding: 0 5px;">TCS/TDS</th>
								<!--<th style="padding: 0 5px;">Round Off</th>-->
								<th style="padding: 0 5px;">Purchases</th>
								<th style="padding: 0 5px;">Payments</th>
								<th style="padding: 0 5px;">Running Balance</th>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
								$tdate = date("Y-m-d",strtotime($_POST['todate']));
								$pre_date = date('Y-m-d', strtotime($fdate.'-1 days'));
								$cus_names = $_POST['cname'];  $sname = $_POST['sname'];
								if($sname == "all" || $sname == "select") { $snames = $fsnames = $tsnames = ""; } else { $snames = " AND `warehouse` = '$sname'"; $fsnames = " AND `fromwarehouse` = '$sname'"; $tsnames = " AND `towarehouse` = '$sname'"; }
								
								if($ifjbwen == 1){ $td_col_value = 9; } else if($ifjbw == 1){ $td_col_value = 7; } else if($ifbw == 1){ $td_col_value = 6; } else { $td_col_value = 4; }
								
								$ob_sales = $ob_receipt = $ob_returns = $ob_smortality = $ob_ccn = $ob_cdn = $rb_amt = $ob_cramt = $ob_dramt = $ob_rcv = $ob_pid = 0;
								//$obsql = "SELECT SUM(DISTINCT(finaltotal)) as obftamt FROM `pur_purchase` WHERE `date` < '$fdate' AND `vendorcode` = '$cus_names' AND `active` = '1' GROUP BY `invoice` ORDER BY `date` ASC";
								$obsql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fdate' AND `vendorcode` = '$cus_names' AND `active` = '1'".$snames." ORDER BY invoice ASC";
								$obquery = mysqli_query($conn,$obsql); $old_inv = "";
								while($obrow = mysqli_fetch_assoc($obquery)){
									if($obrow['invoice'] != $old_inv){
										$ob_sales = $ob_sales + $obrow['finaltotal'];// + $obrow['obftcds'];// + $obrow['obftax'] - $obrow['obfdis'];
										$old_inv = $obrow['invoice'];
									}
								}
								$obsql = "SELECT * FROM `pur_payments` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `active` = '1'".$snames;
								$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_receipt = $ob_receipt + $obrow['amount']; }

								$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$cname' AND `warehouse` = '$sname' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
								$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = $ob_returns + $obrow['amount']; }

								$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fdate' AND `ccode` = '$cname' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
								$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_smortality += (float)$obrow['amount']; }

								$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mode` IN ('SCN','SDN') AND `active` = '1'".$snames;
								$obquery = mysqli_query($conn,$obsql);
								while($obrow = mysqli_fetch_assoc($obquery)){ if($obrow['mode'] == "SCN"){ $ob_ccn = $ob_ccn + $obrow['amount']; } else { $ob_cdn = $ob_cdn + $obrow['amount']; } }
								if($obtype[$cus_names] == "Cr"){
									$ob_cramt = $obamt[$cus_names];
								}
								else{
									$ob_dramt = $obamt[$cus_names];
								}
								$ob_rcv = $ob_sales + $ob_ccn + $ob_cramt;
								$ob_pid = $ob_receipt + $ob_returns + $ob_smortality + $ob_cdn + $ob_dramt;
								
								
								
								if($ob_rcv >= $ob_pid){
									echo "<tr>";
									echo "<td colspan='4'>Previous Balance</td>";
									echo "<td></td>";
									for($i = 0;$i < $td_col_value;$i++){ echo "<td></td>"; }
									if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
									if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
									echo "<td>".number_format_ind($ob_rcv - $ob_pid)."</td>";
									echo "<td></td>";
									echo "<td>".number_format_ind($ob_rcv - $ob_pid)."</td>";
									echo "</tr>";
									$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
									$ob_rev_amt = $ob_rcv - $ob_pid;
									$ob_pid_amt = 0;
								}
								else{
									echo "<tr>";
									echo "<td colspan='4'>Previous Balance</td>";
									for($i = 0;$i < $td_col_value + 1;$i++){ echo "<td></td>"; }
									if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
									if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
									echo "<td></td>";
									echo "<td>".number_format_ind($ob_rcv - $ob_pid)."</td>";
									echo "<td>".number_format_ind($ob_rcv - $ob_pid)."</td>";
									echo "</tr>";
									$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
									$ob_pid_amt = $ob_pid - $ob_rcv;
									$ob_rev_amt = 0;
								}
								
								//Sales
								$sequence = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'".$snames;
								$sql = $sequence."".$cnames."".$inames."".$wnames."".$flags." ORDER BY invoice ASC"; $query = mysqli_query($conn,$sql); $i = 0;
								while($row = mysqli_fetch_assoc($query)){
									$i = $i + 1; $sales[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['vendorcode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode'];
								}
								//$sql = "SELECT COUNT(itemcode) as icount,SUM(totalamt) as itotal,invoice,tcdsamt,roundoff,finaltotal FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' GROUP BY `invoice` ORDER BY `invoice`";
								$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'".$snames." ORDER BY `invoice` ASC";
								$query = mysqli_query($conn,$sql); $inv_no = "";
								while($row = mysqli_fetch_assoc($query)){
									/*$slc_icount[$row['invoice']] = $row['icount'];
									$slc_itotal[$row['invoice']] = $row['itotal'];
									$slc_invoice[$row['invoice']] = $row['invoice'];
									if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){
										$slc_tcdsamt[$row['invoice']] = 0.00;
									}
									else{
										$slc_tcdsamt[$row['invoice']] = $row['tcdsamt'];
									}
									if($row['roundoff'] == "" || $row['roundoff'] == NULL){
										$slc_roundoff[$row['invoice']] = 0.00;
									}
									else{
										if(($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']){
											$slc_roundoff[$row['invoice']] = $row['roundoff'];
										}
										else{
											$slc_roundoff[$row['invoice']] = -1 *($row['roundoff']);
										}
										
									}
									$slc_finaltotal[$row['invoice']] = $row['finaltotal'];*/
									if($inv_no == $row['invoice']){
										$slc_icount[$row['invoice']] = $slc_icount[$row['invoice']] + 1;
										$slc_itotal[$row['invoice']] = $slc_itotal[$row['invoice']] + $row['itotal'];
										$slc_invoice[$row['invoice']] = $row['invoice'];
										
										$inv_no = $row['invoice'];
									}
									else{
										$slc_icount[$row['invoice']] = 1;
										$slc_itotal[$row['invoice']] = $row['itotal'];
										$slc_invoice[$row['invoice']] = $row['invoice'];
										
										if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){
											$slc_tcdsamt[$row['invoice']] = 0.00;
										}
										else{
											$slc_tcdsamt[$row['invoice']] = $row['tcdsamt'];
										}
										if($row['roundoff'] == "" || $row['roundoff'] == NULL){
											$slc_roundoff[$row['invoice']] = 0.00;
										}
										else{
											if(($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']){
												$slc_roundoff[$row['invoice']] = $row['roundoff'];
											}
											else{
												$slc_roundoff[$row['invoice']] = -1 *($row['roundoff']);
											}
											
										}
										$slc_finaltotal[$row['invoice']] = $row['finaltotal'];
										
										$inv_no = $row['invoice'];
									}
								}
								//Receipts
								$receipts = array();
								$rctseq = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$snames; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
								$rctname = $_POST['cname']; $i = 0; if($rctname == "all") { $rctnames = ""; } else { $rctnames = " AND `ccode` = '$cname'"; } $rctsql = $rctseq."".$rctnames."".$flags; $rctquery = mysqli_query($conn,$rctsql);
								while($row = mysqli_fetch_assoc($rctquery)){
									$i = $i + 1;  $receipts[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['type']."@".$row['rcode']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['amount1']."@".$row['tcds_per']."@".$row['tcds_amt'];
								}

								//Returns
								$returns = array();
								$rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$cname' AND `warehouse` = '$sname' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
								$rtnquery = mysqli_query($conn,$rtnsql); $i = 0;
								while($row = mysqli_fetch_assoc($rtnquery)){
									$avgwt = 0;
									if($row['birds'] != "" || $row['birds'] != 0 || $row['birds'] != "0.00"){ $avgwt = $row['quantity'] / $row['birds']; } else{ $avgwt = 0; }
									$i = $i + 1;  $returns[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['inv_trnum']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$avgwt."@".$row['price']."@".$row['amount']."@".$row['warehouse'];
								}

								//Mortality
								$smortalities  = array();
								$mortsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$cname' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
								$i = 0; $mortquery = mysqli_query($conn,$mortsql);
								while($row = mysqli_fetch_assoc($mortquery)){
									$i = $i + 1;  $smortalities[$row['date']."@".$i] = $row['code']."@".$row['mtype']."@".$row['date']."@".$row['ccode']."@".$row['invoice']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['remarks'];
								}

								//CRDR NOTE
								$crdrseq = "SELECT * FROM `main_crdrnote` WHERE `mode` IN ('SCN','SDN') AND `date` >= '$fdate' AND `date` <= '$tdate'".$snames; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
								$crdrname = $_POST['cname']; $i = $j = 0; if($crdrname == "all") { $crdrnames = ""; } else { $crdrnames = " AND `ccode` = '$cname'"; } $crdrsql = $crdrseq."".$crdrnames."".$flags; $crdrquery = mysqli_query($conn,$crdrsql);
								while($row = mysqli_fetch_assoc($crdrquery)){
									if($row['mode'] == "SCN"){
										$i = $i + 1;
										$ccns[$row['date']."@".$i] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "SDN"){
										$j = $j + 1;
										$cdns[$row['date']."@".$j] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else{ }
								}
							?>
							<?php
								
								$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = $btds_amt = 0; $exi_inv = "";
								for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
									$date_asc = date('Y-m-d', $currentDate);
									if(!empty($sales)){
										$ccount = sizeof($sales); 
										for($i = 0;$i <=$ccount;$i++){
											if($sales[$date_asc."@".$i] != ""){
												$sales_details = explode("@",$sales[$date_asc."@".$i]);
												
												echo "<tr>";
												echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>";
												echo "<td>".$sales_details[1]."</td>";
												echo "<td>".$sales_details[2]."</td>";
												echo "<td style='text-align:left;'>".$sector_name[$sales_details[16]]."</td>";
												if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td>".$sales_details[21]."</td>"; }
												echo "<td style='text-align:left;'>".$itemname[$sales_details[7]]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($sales_details[4]))."</td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($sales_details[8]))."</td>"; }
												if($ifjbwen == 1){  echo "<td>".number_format_ind($sales_details[5])."</td>"; echo "<td>".number_format_ind($sales_details[6])."</td>"; }
												echo "<td>".number_format_ind($sales_details[9])."</td>";
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
													$tbcount = $tbcount + $sales_details[8];
													$tjcount = $tjcount + $sales_details[4];
													$tncount = $tncount + $sales_details[9];
													$twcount = $twcount + $sales_details[5];
													$tecount = $tecount + $sales_details[6];
													$tdcount = $tdcount + $sales_details[18];
													$ttcount = $ttcount + $sales_details[19];
													$tacount = $tacount + $sales_details[11];
													if(number_format_ind($sales_details[8]) == "0.00"){
														echo "<td>0.00</td>";
													}
													else{
														if($sales_details[9] > 0 && $sales_details[8] > 0){
															$result = $sales_details[9] / $sales_details[8];
														}else{
															$result = 0;
														}
														echo "<td>".number_format_ind($result)."</td>";
													}
												}
												else {
													$tncount = $tncount + $sales_details[9];
													$tdcount = $tdcount + $sales_details[18];
													$ttcount = $ttcount + $sales_details[19];
													$tacount = $tacount + $sales_details[11];
												}
												if($prate_flag == 1 || $prate_flag == "1"){
													$prate_index = $sales_details[0];
													echo "<td>".number_format_ind($prates[$prate_index])."</td>";
												}
												echo "<td>".$sales_details[10]."</td>";
												//echo "<td>".$sales_details[18]."</td>";
												//echo "<td>".$sales_details[19]."</td>";
												echo "<td>".number_format_ind($sales_details[11])."</td>";
												if($exi_inv != $sales_details[1]){
													$exi_inv = $sales_details[1];
													echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($slc_tcdsamt[$sales_details[1]])."</td>";
													//echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($slc_roundoff[$sales_details[1]])."</td>";
													echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($slc_finaltotal[$sales_details[1]])."</td>";
													$rb_amt = $rb_amt + $slc_finaltotal[$sales_details[1]];
													echo "<td rowspan='$slc_icount[$exi_inv]'></td>";
													echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($rb_amt)."</td>";
													
													$ft_tcds = $ft_tcds + $slc_tcdsamt[$sales_details[1]];
													$ft_roundoff = $ft_roundoff + $slc_roundoff[$sales_details[1]];
													$fst_famt = $fst_famt + $slc_finaltotal[$sales_details[1]];
												}
												else{
													
												}
												//echo "<td>".$sales_details[14]."</td>";
												///echo "<td>".$sales_details[15]."</td>";
												//echo "<td>".$sales_details[16]."</td>";
												
												echo "</tr>";
											}
											else{ }
										}
									}
									if(!empty($receipts)){
										$ccount = sizeof($receipts); 
										for($i = 0;$i <=$ccount;$i++){
											if($receipts[$date_asc."@".$i] != ""){
												$receipts_details = explode("@",$receipts[$date_asc."@".$i]);
												
												echo "<tr>";
												echo "<td>".date("d.m.Y",strtotime($receipts_details[1]))."</td>";
												echo "<td>".$receipts_details[0]."</td>";
												echo "<td>".$receipts_details[3]."</td>";
												echo "<td style='text-align:left;'>".$sector_name[$receipts_details[13]]."</td>";
												if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
												echo "<td style='text-align:left;'>".$coa_name[$receipts_details[5]]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
												echo "<td></td>";
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
												echo "<td></td>";
												//echo "<td></td>";
												//echo "<td></td>";
												if((int)$dtcds_flag == 1){
													echo "<td>".number_format_ind($receipts_details[15])."</td>";
													echo "<td>".number_format_ind($receipts_details[17])."</td>";
													$btds_amt += (float)$receipts_details[17];
												}
												else{
													echo "<td>".number_format_ind($receipts_details[10])."</td>";
													echo "<td></td>";
												}
												echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($receipts_details[10])."</td>";
												$rb_amt = $rb_amt - $receipts_details[10];
												$frt_famt = $frt_famt + $receipts_details[10];
												echo "<td>".number_format_ind($rb_amt)."</td>";
												echo "</tr>";
											}
											else{ }
										}
									}
									if(!empty($returns)){
										$ccount = sizeof($returns); 
										for($i = 0;$i <=$ccount;$i++){
											if($returns[$date_asc."@".$i] != ""){
												$returns_details = explode("@",$returns[$date_asc."@".$i]);
												
												echo "<tr>";
												echo "<td>".date("d.m.Y",strtotime($returns_details[1]))."</td>";
												echo "<td>".$returns_details[0]."</td>";
												echo "<td>".$returns_details[3]."</td>";
												echo "<td style='text-align:left;'>".$sector_name[$returns_details[11]]."</td>";
												if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
												echo "<td style='text-align:left;'>".$itemname[$returns_details[4]]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".str_replace('.00','',number_format_ind($returns_details[5]))."</td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace('.00','',number_format_ind($returns_details[6]))."</td>"; }
												if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
												echo "<td>".number_format_ind($returns_details[7])."</td>";
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".number_format_ind($returns_details[8])."</td>"; }
												if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
												echo "<td>".number_format_ind($returns_details[9])."</td>";
												//echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($returns_details[10])."</td>";
												echo "<td></td>";
												echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($returns_details[10])."</td>";
												$rb_amt = $rb_amt - $returns_details[10];
												$frt_famt = $frt_famt + $returns_details[10];
												echo "<td>".number_format_ind($rb_amt)."</td>";
												echo "</tr>";
											}
											else{ }
										}
									}
									if(!empty($smortalities)){
										$ccount = sizeof($smortalities); 
										for($i = 0;$i <=$ccount;$i++){
											if($smortalities[$date_asc."@".$i] != ""){
												$smort_details = explode("@",$smortalities[$date_asc."@".$i]);
												
												echo "<tr>";
												echo "<td>".date("d.m.Y",strtotime($smort_details[2]))."</td>";
												echo "<td>".$smort_details[0]."</td>";
												echo "<td>".$smort_details[4]."</td>";
												echo "<td style='text-align:left;'></td>";
												if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
												echo "<td style='text-align:left;'>".$itemname[$smort_details[5]]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace('.00','',number_format_ind($smort_details[6]))."</td>"; }
												if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
												echo "<td>".number_format_ind($smort_details[7])."</td>";
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
												echo "<td>".number_format_ind($smort_details[8])."</td>";
												//echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($smort_details[9])."</td>";
												echo "<td></td>";
												echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($smort_details[9])."</td>";
												$rb_amt = $rb_amt - $smort_details[9];
												$frt_famt = $frt_famt + $smort_details[9];
												echo "<td>".number_format_ind($rb_amt)."</td>";
												echo "</tr>";
											}
											else{ }
										}
									}
									if(!empty($ccns)){
										$ccount = sizeof($ccns); 
										for($i = 0;$i <=$ccount;$i++){
											if($ccns[$date_asc."@".$i] != ""){
												$ccns_details = explode("@",$ccns[$date_asc."@".$i]);
												
												echo "<tr>";
												echo "<td>".date("d.m.Y",strtotime($ccns_details[2]))."</td>";
												echo "<td>".$ccns_details[1]."</td>";
												echo "<td>".$ccns_details[4]."</td>";
												echo "<td style='text-align:left;'>".$sector_name[$ccns_details[11]]."</td>";
												if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
												echo "<td style='text-align:left;'>Credit Note</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
												echo "<td></td>";
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
												echo "<td></td>";
												//echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($ccns_details[7])."</td>";
												echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($ccns_details[7])."</td>";
												echo "<td></td>";
												$rb_amt = $rb_amt + $ccns_details[7];
												$fct_famt = $fct_famt + $ccns_details[7];
												echo "<td>".number_format_ind($rb_amt)."</td>";
												echo "</tr>";
											}
											else{ }
										}
									}
									if(!empty($cdns)){
										$ccount = sizeof($cdns); 
										for($i = 0;$i <=$ccount;$i++){
											if($cdns[$date_asc."@".$i] != ""){
												$cdns_details = explode("@",$cdns[$date_asc."@".$i]);
												
												echo "<tr>";
												echo "<td>".date("d.m.Y",strtotime($cdns_details[2]))."</td>";
												echo "<td>".$cdns_details[1]."</td>";
												echo "<td>".$cdns_details[4]."</td>";
												echo "<td style='text-align:left;'>".$sector_name[$cdns_details[11]]."</td>";
												if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; }
												echo "<td style='text-align:left;'>Debit Note</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
												echo "<td></td>";
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
												if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; }
												echo "<td></td>";
												//echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($cdns_details[7])."</td>";
												echo "<td></td>";
												echo "<td></td>";
												//echo "<td></td>";
												echo "<td>".number_format_ind($cdns_details[7])."</td>";
												$rb_amt = $rb_amt - $cdns_details[7];
												$fdt_famt = $fdt_famt + $cdns_details[7];
												echo "<td>".number_format_ind($rb_amt)."</td>";
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
									<td colspan="4" align="center"><b>Between Days Total</b></td>
									<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; } ?>
									<td></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding: 0 5px;text-align:right;'><?php echo str_replace(".00","",number_format_ind($tjcount)); ?></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding: 0 5px;text-align:right;'><?php echo str_replace(".00","",number_format_ind($tbcount)); ?></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($twcount); ?></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($tecount); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($tncount); ?></td>
									<?php
									if($tncount > 0 && $tbcount > 0){
										$result = $tncount / $tbcount;
									}else{
										$result = 0;
									}
									?>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($result); ?></td>
									<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; } ?>
									<?php
									if($tacount >0 && $tncount> 0){
										$result1 = $tacount / $tncount;
									}else{
										$result1 = 0;
									}
									?>
									<td><?php echo number_format_ind($result1); ?></td>
									<!--<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($tdcount); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($ttcount); ?></td>-->
									
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind(($tacount + $fct_famt + $fdt_famt + $frt_famt) + $btds_amt); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($ft_tcds + $btds_amt); ?></td>
									<!--<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($ft_roundoff); ?></td>-->
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($fst_famt + $fct_famt); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($frt_famt + $fdt_famt); ?></td>
									<td></td>
								</tr>
								<tr class="foottr" style="background-color: #98fb98;">
									<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
									<td colspan="<?php echo $td_col_value + 2; ?>" align="center"><b>Grand Total</b></td>
									<?php } else { ?>
										<td colspan="<?php echo $td_col_value + 2; ?>" align="center"><b>Grand Total</b></td>
									<?php } ?>
									<td></td><td></td>
									<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; } ?>
									<td></td>
									<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; } ?>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind(($fst_famt + $fct_famt) + $ob_rev_amt); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind(($frt_famt + $fdt_famt) + $ob_pid_amt); ?></td>
									<td></td>
								</tr>
								<tr class="foottr" style="background-color: #98fb98;">
									<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
										<td colspan="<?php echo $td_col_value + 5; ?>" align="center"><b>Closing Balance</b></td>
									<?php } else { ?>
										<td colspan="<?php echo $td_col_value + 5; ?>" align="center"><b>Closing Balance</b></td>
									<?php } ?>
									<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; } ?>
									<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; } ?>
									<?php
										if((($fst_famt + $fct_famt) + $ob_rev_amt) > (($frt_famt + $fdt_famt) + $ob_pid_amt)){
											$famt = (($fst_famt + $fct_famt) + $ob_rev_amt) - (($frt_famt + $fdt_famt) + $ob_pid_amt);
											//echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>";
											echo "<td></td>";
											echo "<td></td>";
										}
										else {
											$famt = (($fst_famt + $fct_famt) + $ob_rev_amt) - (($frt_famt + $fdt_famt) + $ob_pid_amt);
											echo "<td></td>";
											echo "<td></td>";
											//echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>";
										}
										echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>";
									?>
									<!--<td></td>-->
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

		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
