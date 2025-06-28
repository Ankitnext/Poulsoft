<?php
    //SuppliercumCustomerLedgerReport1.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db']; } else{ $db = ''; }

	if($db == ''){
		include "../newConfig.php";
		include "header_head.php"; 
		include "../broiler_check_tableavailability.php";
		include "number_format_ind.php"; 
	}
	else{
		include "../broiler_check_tableavailability.php";
		//include "../newConfig.php";
		include "APIconfig.php";
		include "number_format_ind.php";
		include "header_head.php";
	}
	$idisplay = ''; $ndisplay = 'style="display:none;"';
	$today = date("Y-m-d");
	$ifwt = $ifbw = $ifjbw = $ifjbwen = $ifctype = 0;
	if($count53 > 0){
		$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
	}
	$pname = $obdate = $obtype = $obamt = array();
	if($count31 > 0){
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S&C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$pname[$row['code']] = $row['name'];
			$obdate[$row['code']] = $row['obdate'];
			$obtype[$row['code']] = $row['obtype'];
			$obamt[$row['code']] = $row['obamt'];
		}
	}
	$itemname = array();
	if($count22 > 0){
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
	}
	$coa_name = array();
	if($count2 > 0){
		$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $coa_name[$row['code']] = $row['description']; }
	}
	if(isset($_POST['submit']) == true){
		$fromdate = $_POST['fromdate'];
		$todate = $_POST['todate'];
		 $cname = $_POST['cname'];
		$expoption = $_POST['export'];
	}
	else{
		$fromdate = $todate = $today;
		$cname = "all";
		$expoption = "displaypage";
	}
	
	if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; $sup_cnames = " AND `vendorcode` = '$cname'"; }
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; if($excel_type == "exportexcel"){ $exoption = "displaypage"; } else{ $exoption = $_POST['export']; } } else{ $excel_type = "displaypage"; }

	$url = "../PHPExcel/Examples/SuppliercumCustomerLedgerReport-Excel.php?fromdate=".$fromdate."&todate=".$todate."&cname=".$cname."&cid=".$cid;

    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Display TCDS Calculations' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dtcds_flag = mysqli_num_rows($query);
			
?>
<html>
	<head>
	<link rel="stylesheet" type="text/css"href="reportstyle.css">
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
						<h3>Customer &amp; Supplier Ledger</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer &amp; Supplier:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br/>
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
				<form action="SuppliercumCustomerLedgerReport1.php" method="post">
					<?php } else { ?>
					<form action="SuppliercumCustomerLedgerReport1.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="20">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Customer &amp; Supplier</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2">
											<option value="select">-select-</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S&C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($cname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
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
								if($cname == "" || $cname == "select"){ }
								else if($cname == "all"){ }
								else {
							?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th style="padding: 0 5px;">Date</th>
								<!--<th>Customer</th>-->
								<th style="padding: 0 5px;">Vehicle No.</th>
								<th style="padding: 0 5px;">Remarks</th>
								<th style="padding: 0 5px;">Item</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1){ ?><th style="padding: 0 5px;">Jals</th><?php } ?>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th style="padding: 0 5px;">Birds</th><?php } ?>
								<?php if($ifjbwen == 1){ ?> <th style="padding: 0 5px;">Total Weight</th><th style="padding: 0 5px;">Empty Weight</th> <?php } ?>
								<th style="padding: 0 5px;">Net Weight</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th style="padding: 0 5px;">Avg.Weight</th><?php } ?>
								<th style="padding: 0 5px;">Price</th>
								<!--<th style="padding: 0 5px;">Discount</th>
								<th style="padding: 0 5px;">Tax</th>
								<th style="padding: 0 5px;">Amount</th>-->
								<th style="padding: 0 5px;">TCS/TDS</th>
								<!--<th style="padding: 0 5px;">Round Off</th>-->
								<th style="padding: 0 5px;">C/S Received</th>
								<th style="padding: 0 5px;">C/S Paid</th>
								<th style="padding: 0 5px;">Running Balance</th>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
								$tdate = date("Y-m-d",strtotime($_POST['todate']));
								$pre_date = date('Y-m-d', strtotime($fdate.'-1 days'));
								$cus_names = $_POST['cname'];
								
								if($ifjbwen == 1){ $td_col_value = 8; } else if($ifjbw == 1){ $td_col_value = 6; } else if($ifbw == 1){ $td_col_value = 5; } else { $td_col_value = 3; }
								
								$ob_sales = $ob_receipt = $ob_returns = $ob_mortality = $ob_sreturns = $ob_smortality = $ob_ccn = $ob_cdn = $rb_amt = $ob_cramt = $ob_dramt = $ob_purchases = $ob_payments = $ob_sup_cramt = $ob_sup_ccn = $ob_sup_dramt = $rb_amt = $ob_rcv = $ob_pid = 0; $old_inv = "";
								if($count14 > 0){
									$obsql = "SELECT * FROM `customer_sales` WHERE `date` < '$fdate' AND `customercode` = '$cus_names' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
									$obquery = mysqli_query($conn,$obsql);
									while($obrow = mysqli_fetch_assoc($obquery)){
										if($old_inv != $obrow['invoice']){
											$ob_sales = $ob_sales + (float)$obrow['finaltotal'];// + $obrow['obftcds'];// + $obrow['obftax'] - $obrow['obfdis'];
											$old_inv = $obrow['invoice'];
										}
										
									}
								}
								if($count13 > 0){
								$obsql = "SELECT * FROM `customer_receipts` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
								$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_receipt = $ob_receipt + $obrow['amount']; }
								}
								if($count40 > 0){
									$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$cus_names' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns += (float)$obrow['amount']; }
								}
								if($count44 > 0){
									$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_mortality += (float)$obrow['amount']; }
								}
								if($count32 > 0){
								$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mode` IN ('CCN','CDN') AND `active` = '1'";
								$obquery = mysqli_query($conn,$obsql);
								while($obrow = mysqli_fetch_assoc($obquery)){ if($obrow['mode'] == "CCN"){ $ob_ccn = $ob_ccn + $obrow['amount']; } else { $ob_cdn = $ob_cdn + $obrow['amount']; } }
								}
								$old_inv = "";
								if($count57 > 0){
									$obsql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fdate' AND `vendorcode` = '$cus_names' AND `active` = '1' ORDER BY `date`,`invoice` ASC";
									$obquery = mysqli_query($conn,$obsql);
									while($obrow = mysqli_fetch_assoc($obquery)){
										if($old_inv != $obrow['invoice']){
											$ob_purchases = $ob_purchases + $obrow['finaltotal'];// + $obrow['obftcds'];// + $obrow['obftax'] - $obrow['obfdis'];
											$old_inv = $obrow['invoice'];
											//echo "<br/>".$obrow['invoice']."@".$obrow['finaltotal']."@".$ob_purchases;
										}
									}
								}
								if($count56 > 0){
								$obsql = "SELECT * FROM `pur_payments` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `active` = '1'";
								$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_payments = $ob_payments + $obrow['amount']; }
								}
								if($count40 > 0){
									$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$cus_names' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_sreturns += (float)$obrow['amount']; }
								}
								if($count44 > 0){
									$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_smortality += (float)$obrow['amount']; }
								}
								if($count32 > 0){
								$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fdate' AND `ccode` = '$cus_names' AND `mode` IN ('SCN','SDN') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
								$obquery = mysqli_query($conn,$obsql);
								while($obrow = mysqli_fetch_assoc($obquery)){ if($obrow['mode'] == "SCN"){ $ob_sup_ccn = $ob_sup_ccn + $obrow['amount']; } else { $ob_sup_cdn = $ob_sup_cdn + $obrow['amount']; } }
								}
								if($obtype[$cus_names] == "Cr"){
									$ob_cramt = $obamt[$cus_names];
								}
								else{
									$ob_dramt = $obamt[$cus_names];
								}
								$ob_rcv = $ob_sales + $ob_cdn + $ob_sup_cdn + $ob_payments + $ob_smortality + $ob_dramt + $ob_sreturns;
								$ob_pid = $ob_purchases + $ob_sup_ccn + $ob_ccn + $ob_receipt + $ob_mortality + $ob_cramt + $ob_returns;
								
								//echo "<br/>$ob_rcv = $ob_sales + $ob_cdn + $ob_sup_cdn + $ob_payments + $ob_smortality + $ob_dramt + $ob_sreturns<br/>;
								//$ob_pid = $ob_purchases + $ob_sup_ccn + $ob_ccn + $ob_receipt + $ob_mortality + $ob_cramt + $ob_returns;";
								
								
								
								if($ob_rcv >= $ob_pid){
									echo "<tr>";
									echo "<td colspan='3'>Previous Balance</td>";
									echo "<td></td>";
									for($i = 0;$i < $td_col_value;$i++){ echo "<td></td>"; }
									echo "<td>".number_format_ind($ob_rcv - $ob_pid)."</td>";
									echo "<td></td>";
									$rb_amt = ($ob_pid - $ob_rcv);
									echo "<td>".number_format_ind($rb_amt)."</td>";
									echo "</tr>";
									$ob_rev_amt = $ob_rcv - $ob_pid;
									$ob_pid_amt = 0;
								}
								else{
									echo "<tr>";
									echo "<td colspan='3'>Previous Balance</td>";
									for($i = 0;$i < $td_col_value + 1;$i++){ echo "<td></td>"; }
									echo "<td></td>";
									echo "<td>".number_format_ind($ob_pid - $ob_rcv)."</td>";
									$rb_amt = ($ob_pid - $ob_rcv);
									echo "<td>".number_format_ind($rb_amt)."</td>";
									echo "</tr>";
									$ob_pid_amt = $ob_pid - $ob_rcv;
									$ob_rev_amt = 0;
								}
								
								//Sales
								$sales = array();
								if($count14 > 0){
									$sequence = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
									$sql = $sequence."".$cnames."".$inames."".$wnames."".$flags; $query = mysqli_query($conn,$sql); $i = 0;
									while($row = mysqli_fetch_assoc($query)){
										$i = $i + 1; $sales[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['customercode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode'];
									}
								}
								if($count14 > 0){
									$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$slc_icount[$row['invoice']] = $slc_icount[$row['invoice']] + 1;
										$slc_itotal[$row['invoice']] = $slc_itotal[$row['invoice']] + $row['totalamt'];
										$slc_invoice[$row['invoice']] = $row['invoice'];
										if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){ $slc_tcdsamt[$row['invoice']] = 0.00; } else{ $slc_tcdsamt[$row['invoice']] = $row['tcdsamt']; }
										if($row['roundoff'] == "" || $row['roundoff'] == NULL){ $slc_roundoff1[$row['invoice']] = 0.00; }
										else{ $slc_roundoff[$row['invoice']] = $row['roundoff']; }
										$slc_finaltotal[$row['invoice']] = $row['finaltotal'];
									}
									foreach($slc_invoice as $sinv){
										if($slc_roundoff1[$sinv] == "" || $slc_roundoff1[$sinv] == NULL || $slc_roundoff1[$sinv] == "0.00" || $slc_roundoff1[$sinv] == 0.00 || $slc_roundoff1[$sinv] == 0){ $slc_roundoff[$sinv] = 0.00; }
										else{
											if($slc_itotal[$sinv] + $slc_tcdsamt[$sinv] < $slc_finaltotal[$sinv]){
												$slc_roundoff[$sinv] = $slc_roundoff1[$sinv];
											}
											else{
												$slc_roundoff[$sinv] = -1 * ($slc_roundoff1[$sinv]);
											}
										}
									}
								}
								//Receipts
								$receipts  = array();
								if($count13 > 0){
									$rctseq = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$rctname = $_POST['cname']; $i = 0; if($rctname == "all") { $rctnames = ""; } else { $rctnames = " AND `ccode` = '$cname'"; } $rctsql = $rctseq."".$rctnames."".$flags; $rctquery = mysqli_query($conn,$rctsql);
									while($row = mysqli_fetch_assoc($rctquery)){
										$i = $i + 1;  $receipts[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['type']."@".$row['rcode']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
								//Returns
								$returns = array();
								if($count40 > 0){
									$rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$cname' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
									$rtnquery = mysqli_query($conn,$rtnsql); $i = 0;
									while($row = mysqli_fetch_assoc($rtnquery)){
										$avgwt = 0;
										if($row['birds'] != "" || $row['birds'] != 0 || $row['birds'] != "0.00"){ if($row['birds'] > 0){$avgwt = $row['quantity'] / $row['birds'];}else{$avgwt = 0;} } else{ $avgwt = 0; }
										$i = $i + 1;  $returns[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['inv_trnum']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$avgwt."@".$row['price']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
								//Mortality
								$mortalities  = array();
								if($count13 > 0){
									$mortseq = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mtype` = 'customer'"; $flags = " AND `active` = '1' AND `dflag` = '0'";
									$mortname = $_POST['cname']; $i = 0; if($mortname == "all") { $mortnames = ""; } else { $mortnames = " AND `ccode` = '$cname'"; } $mortsql = $mortseq."".$mortnames."".$flags; $mortquery = mysqli_query($conn,$mortsql);
									while($row = mysqli_fetch_assoc($mortquery)){
										$i = $i + 1;  $mortalities[$row['date']."@".$i] = $row['code']."@".$row['mtype']."@".$row['date']."@".$row['ccode']."@".$row['invoice']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['remarks'];
									}
								}
								//CRDR NOTE
								$ccns  = array();$cdns = array();
								if($count32 > 0){
									$crdrseq = "SELECT * FROM `main_crdrnote` WHERE `mode` IN ('CDN','CCN') AND `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$crdrname = $_POST['cname']; $i = $j = 0; if($crdrname == "all") { $crdrnames = ""; } else { $crdrnames = " AND `ccode` = '$cname'"; } $crdrsql = $crdrseq."".$crdrnames."".$flags; $crdrquery = mysqli_query($conn,$crdrsql);
									while($row = mysqli_fetch_assoc($crdrquery)){
										if($row['mode'] == "CCN"){
											$i = $i + 1;
											$ccns[$row['date']."@".$i] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
										}
										else if($row['mode'] == "CDN"){
											$j = $j + 1;
											$cdns[$row['date']."@".$j] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
										}
										else{ }
									}
								}
								//Purchases
								$purchases = array();
								if($count57 > 0){
									$sequence = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
									$sql = $sequence."".$sup_cnames."".$inames."".$wnames."".$flags; $query = mysqli_query($conn,$sql); $i = 0;
									while($row = mysqli_fetch_assoc($query)){
										$i = $i + 1; $purchases[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['vendorcode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode'];
									}
								}
								if($count57 > 0){
									$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' ORDER BY `date`,`invoice` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$pur_icount[$row['invoice']] = $pur_icount[$row['invoice']] + 1;
										$pur_itotal[$row['invoice']] = $pur_itotal[$row['invoice']] + $row['totalamt'];
										$pur_invoice[$row['invoice']] = $row['invoice'];
										if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){ $pur_tcdsamt[$row['invoice']] = 0.00; } else{ $pur_tcdsamt[$row['invoice']] = $row['tcdsamt']; }
										if($row['roundoff'] == "" || $row['roundoff'] == NULL){ $pur_roundoff1[$row['invoice']] = 0.00; }
										else{ $pur_roundoff1[$row['invoice']] = $row['roundoff']; }
										$pur_finaltotal[$row['invoice']] = $row['finaltotal'];
									}
									foreach($pur_invoice as $pinv){
										if($pur_roundoff1[$pinv] == "" || $pur_roundoff1[$pinv] == NULL || $pur_roundoff1[$pinv] == "0.00" || $pur_roundoff1[$pinv] == 0.00 || $pur_roundoff1[$pinv] == 0){ $pur_roundoff[$pinv] = 0.00; }
										else{
											if($pur_itotal[$pinv] + $pur_tcdsamt[$pinv] < $pur_finaltotal[$pinv]){
												$pur_roundoff[$pinv] = $pur_roundoff1[$pinv];
											}
											else{
												$pur_roundoff[$pinv] = -1 * ($pur_roundoff1[$pinv]);
											}
										}
									}
								}
								//Payments
								$payments = array();
								if($count56 > 0){
									$rctseq = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$rctname = $_POST['cname']; $i = 0; if($rctname == "all") { $rctnames = ""; } else { $rctnames = " AND `ccode` = '$cname'"; } $rctsql = $rctseq."".$rctnames."".$flags; $rctquery = mysqli_query($conn,$rctsql);
									while($row = mysqli_fetch_assoc($rctquery)){
										$i = $i + 1;  $payments[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['mode']."@".$row['method']."@".$row['type']."@".$row['rcode']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks']."@".$row['amount1']."@".$row['tcds_per']."@".$row['tcds_amt'];
									}
								}
								//Returns
								$sreturns = array();
								if($count40 > 0){
									$rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$cname' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$rtnquery = mysqli_query($conn,$rtnsql); $i = 0;
									while($row = mysqli_fetch_assoc($rtnquery)){
										$avgwt = 0;
										if($row['birds'] != "" || $row['birds'] != 0 || $row['birds'] != "0.00"){ $avgwt = $row['quantity'] / $row['birds']; } else{ $avgwt = 0; }
										$i = $i + 1;  $sreturns[$row['date']."@".$i] = $row['trnum']."@".$row['date']."@".$row['vcode']."@".$row['inv_trnum']."@".$row['itemcode']."@".$row['jals']."@".$row['birds']."@".$row['quantity']."@".$avgwt."@".$row['price']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
								//Mortality
								$smortalities  = array();
								if($count13 > 0){
									$mortseq = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mtype` = 'supplier'"; $flags = " AND `active` = '1' AND `dflag` = '0'";
									$mortname = $_POST['cname']; $i = 0; if($mortname == "all") { $mortnames = ""; } else { $mortnames = " AND `ccode` = '$cname'"; } $mortsql = $mortseq."".$mortnames."".$flags; $mortquery = mysqli_query($conn,$mortsql);
									while($row = mysqli_fetch_assoc($mortquery)){
										$i = $i + 1;  $smortalities[$row['date']."@".$i] = $row['code']."@".$row['mtype']."@".$row['date']."@".$row['ccode']."@".$row['invoice']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['remarks'];
									}
								}
								//CRDR NOTE
								$pur_ccns = array();$pur_cdns = array();
								if($count32 > 0){
									$crdrseq = "SELECT * FROM `main_crdrnote` WHERE `mode` IN ('SCN','SDN') AND `date` >= '$fdate' AND `date` <= '$tdate'"; $flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$crdrname = $_POST['cname']; $i = $j = 0; if($crdrname == "all") { $crdrnames = ""; } else { $crdrnames = " AND `ccode` = '$cname'"; } $crdrsql = $crdrseq."".$crdrnames."".$flags; $crdrquery = mysqli_query($conn,$crdrsql);
									while($row = mysqli_fetch_assoc($crdrquery)){
										if($row['mode'] == "SCN"){
											$i = $i + 1;
											$pur_ccns[$row['date']."@".$i] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
										}
										else if($row['mode'] == "SDN"){
											$j = $j + 1;
											$pur_cdns[$row['date']."@".$j] = $row['mode']."@".$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['coa']."@".$row['crdr']."@".$row['amount']."@".$row['balance']."@".$row['amtinwords']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
										}
										else{ }
									}
								}
								$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = $btds_amt = 0; $exi_inv = "";
								$tbcount = $tjcount = $tncount = $twcount = $tecount = $tdcount = $ttcount = $tacount = 0;
								for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
									$date_asc = date('Y-m-d', $currentDate);
									$ccount = sizeof($sales); 
									for($i = 0;$i <=$ccount;$i++){
										if($sales[$date_asc."@".$i] != ""){
											$sales_details = explode("@",$sales[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($sales_details[0]))."</td>";
											echo "<td style='text-align:left;'>".$sales_details[21]."</td>";
											echo "<td style='text-align:left;'>".$sales_details[20]."</td>";
											echo "<td style='text-align:left;'>".$itemname[$sales_details[7]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($sales_details[4]))."</td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($sales_details[8]))."</td>"; }
											if($ifjbwen == 1){  echo "<td>".number_format_ind($sales_details[5])."</td>"; echo "<td>".number_format_ind($sales_details[6])."</td>"; }
											echo "<td>".number_format_ind($sales_details[9])."</td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
												$tbcount = $tbcount + (float)$sales_details[8];
												$tjcount = $tjcount + (float)$sales_details[4];
												$tncount = $tncount + (float)$sales_details[9];
												$twcount = $twcount + (float)$sales_details[5];
												$tecount = $tecount + (float)$sales_details[6];
												$tdcount = $tdcount + (float)$sales_details[18];
												$ttcount = $ttcount + (float)$sales_details[19];
												$tacount = $tacount + (float)$sales_details[11];
												if(number_format_ind($sales_details[8]) == "0.00"){
													echo "<td>0.00</td>";
												}
												else{
													if($sales_details[9] > 0 && $sales_details[8] > 0){
														$result = (float)$sales_details[9] / (float)$sales_details[8];
													}else{
														$result = 0;
													}
													echo "<td>".number_format_ind($result)."</td>";
												}
											}
											else {
												$tncount = $tncount + (float)$sales_details[9];
												$tdcount = $tdcount + (float)$sales_details[18];
												$ttcount = $ttcount + (float)$sales_details[19];
												$tacount = $tacount + (float)$sales_details[11];
											}
											
											echo "<td>".number_format_ind($sales_details[10])."</td>";
											if($exi_inv != $sales_details[1]){
												$exi_inv = $sales_details[1];
												echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($slc_tcdsamt[$sales_details[1]])."</td>";
												echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($slc_finaltotal[$sales_details[1]])."</td>";
												$rb_amt = $rb_amt - $slc_finaltotal[$sales_details[1]];
												echo "<td rowspan='$slc_icount[$exi_inv]'></td>";
												echo "<td rowspan='$slc_icount[$exi_inv]'>".number_format_ind($rb_amt)."</td>";
												
												$ft_tcds = (float)$ft_tcds + (float)$slc_tcdsamt[$sales_details[1]];
												$ft_roundoff = (float)$ft_roundoff + (float)$slc_roundoff[$sales_details[1]];
												$fst_famt = (float)$fst_famt + (float)$slc_finaltotal[$sales_details[1]];
											}
											else{
												
											}
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($purchases); 
									for($i = 0;$i <=$ccount;$i++){
										if($purchases[$date_asc."@".$i] != ""){
											$purchase_details = explode("@",$purchases[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($purchase_details[0]))."</td>";
											echo "<td style='text-align:left;'>".$purchase_details[21]."</td>";
											echo "<td style='text-align:left;'>".$purchase_details[20]."</td>";
											echo "<td style='text-align:left;'>".$itemname[$purchase_details[7]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($purchase_details[4]))."/-</td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($purchase_details[8]))."</td>"; }
											if($ifjbwen == 1){  echo "<td>".number_format_ind($purchase_details[5])."</td>"; echo "<td>".number_format_ind($purchase_details[6])."</td>"; }
											echo "<td>".number_format_ind($purchase_details[9])."</td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
												$tbcount = $tbcount + (float)$purchase_details[8];
												$tjcount = $tjcount + (float)$purchase_details[4];
												$tncount = $tncount + (float)$purchase_details[9];
												$twcount = $twcount + (float)$purchase_details[5];
												$tecount = $tecount + (float)$purchase_details[6];
												$tdcount = $tdcount + (float)$purchase_details[18];
												$ttcount = $ttcount + (float)$purchase_details[19];
												$tacount = $tacount + (float)$purchase_details[11];
												if(number_format_ind($purchase_details[8]) == "0.00"){
													echo "<td>0.00</td>";
												}
												else{
													if($purchase_details[9] > 0 && $purchase_details[8] > 0){
														$result1 = (float)$purchase_details[9] / (float)$purchase_details[8];
													}else{
														$result1 = 0;
													}
													echo "<td>".number_format_ind($result1)."</td>";
												}
											}
											else {
												$tncount = (float)$tncount + (float)$purchase_details[9];
												$tdcount = (float)$tdcount + (float)$purchase_details[18];
												$ttcount = (float)$ttcount + (float)$purchase_details[19];
												$tpacount = (float)$tpacount + (float)$purchase_details[11];
											}
											echo "<td>".number_format_ind($purchase_details[10])."</td>";
											if($exi_inv != $purchase_details[1]){
												$exi_inv = $purchase_details[1];
												echo "<td rowspan='$pur_icount[$exi_inv]'>".number_format_ind($pur_tcdsamt[$purchase_details[1]])."</td>";
												echo "<td rowspan='$pur_icount[$exi_inv]'></td>";
												echo "<td rowspan='$pur_icount[$exi_inv]'>".number_format_ind($pur_finaltotal[$purchase_details[1]])."</td>";
												$rb_amt = $rb_amt + $pur_finaltotal[$purchase_details[1]];
												echo "<td rowspan='$pur_icount[$exi_inv]'>".number_format_ind($rb_amt)."</td>";
												
												$ft_tcds = (float)$ft_tcds + (float)$pur_tcdsamt[$purchase_details[1]];
												$ft_roundoff = (float)$ft_roundoff + (float)$pur_roundoff[$purchase_details[1]];
												$fst_sup_famt = (float)$fst_sup_famt + (float)$pur_finaltotal[$purchase_details[1]];
											}
											else{
												
											}
											
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($receipts); 
									for($i = 0;$i <=$ccount;$i++){
										if($receipts[$date_asc."@".$i] != ""){
											$receipts_details = explode("@",$receipts[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($receipts_details[1]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$receipts_details[14]."</td>";
											echo "<td style='text-align:left;'>".$coa_name[$receipts_details[5]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td></td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td></td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($receipts_details[10])."</td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($receipts_details[10])."</td>";
											$rb_amt = (float)$rb_amt + (float)$receipts_details[10];
											$frt_famt = (float)$frt_famt + (float)$receipts_details[10];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($returns); 
									for($i = 0;$i <=$ccount;$i++){
										if($returns[$date_asc."@".$i] != ""){
											$returns_details = explode("@",$returns[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($returns_details[1]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$returns_details[12]."</td>";
											echo "<td style='text-align:left;'>".$itemname[$returns_details[4]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".number_format_ind($returns_details[5])."</td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".number_format_ind($returns_details[6])."</td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td>".number_format_ind($returns_details[7])."</td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".number_format_ind($returns_details[8])."</td>"; }
											echo "<td>".number_format_ind($returns_details[9])."</td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($returns_details[10])."</td>";
											echo "<td></td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($returns_details[10])."</td>";
											$rb_amt = (float)$rb_amt + (float)$returns_details[10];
											$frt_famt = (float)$frt_famt + (float)$returns_details[10];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($sreturns); 
									for($i = 0;$i <=$ccount;$i++){
										if($sreturns[$date_asc."@".$i] != ""){
											$sreturns_details = explode("@",$sreturns[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($sreturns_details[1]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$sreturns_details[12]."</td>";
											echo "<td style='text-align:left;'>".$itemname[$sreturns_details[4]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".number_format_ind($sreturns_details[5])."</td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".number_format_ind($sreturns_details[6])."</td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td>".number_format_ind($sreturns_details[7])."</td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".number_format_ind($sreturns_details[8])."</td>"; }
											echo "<td>".number_format_ind($sreturns_details[9])."</td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($sreturns_details[10])."</td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($sreturns_details[10])."</td>";
											echo "<td></td>";
											$rb_amt = (float)$rb_amt - (float)$sreturns_details[10];
											$fpt_famt = (float)$fpt_famt + (float)$sreturns_details[10];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($smortalities); 
									for($i = 0;$i <=$ccount;$i++){
										if($smortalities[$date_asc."@".$i] != ""){
											$smort_details = explode("@",$smortalities[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($smort_details[2]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$smort_details[10]."</td>";
											echo "<td style='text-align:left;'>".$itemname[$smort_details[5]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($smort_details[6]))."</td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td>".number_format_ind($smort_details[7])."</td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td>".number_format_ind($smort_details[8])."</td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($smort_details[9])."</td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($smort_details[9])."</td>";
											echo "<td></td>";
											$rb_amt = (float)$rb_amt - (float)$smort_details[9];
											$fpt_famt = (float)$fpt_famt + (float)$smort_details[9];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($mortalities); 
									for($i = 0;$i <=$ccount;$i++){
										if($mortalities[$date_asc."@".$i] != ""){
											$mort_details = explode("@",$mortalities[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($mort_details[2]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$mort_details[10]."</td>";
											echo "<td style='text-align:left;'>".$itemname[$mort_details[5]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($mort_details[6]))."</td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td>".number_format_ind($mort_details[7])."</td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td>".number_format_ind($mort_details[8])."</td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($mort_details[9])."</td>";
											echo "<td></td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($mort_details[9])."</td>";
											$rb_amt = (float)$rb_amt + (float)$mort_details[9];
											$frt_famt = (float)$frt_famt + (float)$mort_details[9];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($payments); 
									for($i = 0;$i <=$ccount;$i++){
										if($payments[$date_asc."@".$i] != ""){
											$payment_details = explode("@",$payments[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($payment_details[1]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$payment_details[14]."</td>";
											echo "<td style='text-align:left;'>".$coa_name[$payment_details[5]]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td></td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td></td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($payment_details[10])."</td>";
											if((int)$dtcds_flag == 1){
												echo "<td>".number_format_ind($payment_details[17])."</td>";
												$btds_amt += (float)$payment_details[17];
											}
											else{
												echo "<td></td>";
											}
											//echo "<td></td>";
											echo "<td>".number_format_ind($payment_details[10])."</td>";
											echo "<td></td>";
											$rb_amt = (float)$rb_amt - (float)$payment_details[10];
											$fpt_famt = (float)$fpt_famt + (float)$payment_details[10];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($ccns); 
									for($i = 0;$i <=$ccount;$i++){
										if($ccns[$date_asc."@".$i] != ""){
											$ccns_details = explode("@",$ccns[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($ccns_details[2]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$ccns_details[12]."</td>";
											echo "<td style='text-align:left;'>Credit Note</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td></td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td></td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($ccns_details[7])."</td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($ccns_details[7])."</td>";
											$rb_amt = (float)$rb_amt + (float)$ccns_details[7];
											$fct_famt = (float)$fct_famt + (float)$ccns_details[7];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($cdns); 
									for($i = 0;$i <=$ccount;$i++){
										if($cdns[$date_asc."@".$i] != ""){
											$cdns_details = explode("@",$cdns[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($cdns_details[2]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$cdns_details[12]."</td>";
											echo "<td style='text-align:left;'>Debit Note</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td></td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td></td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($cdns_details[7])."</td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td>".number_format_ind($cdns_details[7])."</td>";
											echo "<td></td>";
											$rb_amt = (float)$rb_amt - (float)$cdns_details[7];
											$fdt_famt = (float)$fdt_famt + (float)$cdns_details[7];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($pur_ccns); 
									for($i = 0;$i <=$ccount;$i++){
										if($pur_ccns[$date_asc."@".$i] != ""){
											$ccns_sup_details = explode("@",$pur_ccns[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($ccns_sup_details[2]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$ccns_sup_details[12]."</td>";
											echo "<td style='text-align:left;'>Credit Note</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td></td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td></td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($ccns_sup_details[7])."</td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td></td>";
											echo "<td>".number_format_ind($ccns_sup_details[7])."</td>";
											$rb_amt = (float)$rb_amt + (float)$ccns_sup_details[7];
											$fdt_sup_famt = (float)$fdt_sup_famt + (float)$ccns_sup_details[7];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
									$ccount = sizeof($pur_cdns); 
									for($i = 0;$i <=$ccount;$i++){
										if($pur_cdns[$date_asc."@".$i] != ""){
											$cdns_sup_details = explode("@",$pur_cdns[$date_asc."@".$i]);
											
											echo "<tr>";
											echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($cdns_sup_details[2]))."</td>";
											echo "<td></td>";
											echo "<td style='text-align:left;'>".$cdns_sup_details[12]."</td>";
											echo "<td style='text-align:left;'>Debit Note</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											if($ifjbwen == 1){  echo "<td></td>"; echo "<td></td>"; }
											echo "<td></td>";
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td></td>"; }
											echo "<td></td>";
											//echo "<td></td>";
											//echo "<td></td>";
											//echo "<td>".number_format_ind($cdns_sup_details[7])."</td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td>".number_format_ind($cdns_sup_details[7])."</td>";
											echo "<td></td>";
											$rb_amt = (float)$rb_amt - (float)$cdns_sup_details[7];
											$fct_sup_famt = (float)$fct_sup_famt + (float)$cdns_sup_details[7];
											echo "<td>".number_format_ind($rb_amt)."</td>";
											echo "</tr>";
										}
										else{ }
									}
								}
							?>
							</tbody>
							<thead>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="4" align="center"><b>Between Days Total</b></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding: 0 5px;text-align:right;'><?php echo str_replace(".00","",number_format_ind($tjcount)); ?></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding: 0 5px;text-align:right;'><?php echo str_replace(".00","",number_format_ind($tbcount)); ?></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($twcount); ?></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($tecount); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($tncount); ?></td>
									<?php
									if($tncount > 0 && $tbcount > 0){
										$result2 = (float)$tncount / (float)$tbcount;
									}else{
										$result2 = 0;
									}
									?>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($result2); ?></td>
									<?php
									if(((float)$fst_famt + (float)$fdt_famt) >0 && $tncount > 0){
										$result3 = ((float)$fst_famt + (float)$fdt_famt) / (float)$tncount;
									}else{
										$result3 = 0;
									}
									?>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($result3); ?></td>
									<!--<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($tdcount); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($ttcount); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($tacount + $fct_famt + $fdt_famt + $frt_famt + $tpacount + $fpt_famt + $fct_sup_famt + $fdt_sup_famt); ?></td>-->
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind($ft_tcds + $btds_amt); ?></td>
									<!--<td style='padding: 0 5px;text-align:right;'><?php //echo number_format_ind($ft_roundoff); ?></td>-->
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind((float)$fst_famt + (float)$fdt_famt + (float)$fct_sup_famt + (float)$fpt_famt); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind((float)$frt_famt + (float)$fct_famt + (float)$fst_sup_famt + (float)$fdt_sup_famt); ?></td>
									<td></td>
								</tr>
								<tr class="foottr" style="background-color: #98fb98;">
									<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
									<td colspan="<?php echo $td_col_value + 1; ?>" align="center"><b>Grand Total</b></td>
									<?php } else { ?>
										<td colspan="<?php echo $td_col_value + 1; ?>" align="center"><b>Grand Total</b></td>
									<?php } ?>
									<td></td>
									<td></td>
									<td></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind(((float)$fst_famt + (float)$fdt_famt + (float)$fct_sup_famt + (float)$fpt_famt) + (float)$ob_rev_amt); ?></td>
									<td style='padding: 0 5px;text-align:right;'><?php echo number_format_ind(((float)$frt_famt + (float)$fct_famt + (float)$fst_sup_famt + (float)$fdt_sup_famt) + (float)$ob_pid_amt); ?></td>
									<td></td>
								</tr>
								<tr class="foottr" style="background-color: #98fb98;">
									<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
										<td colspan="<?php echo $td_col_value + 4; ?>" align="center"><b>Closing Balance</b></td>
									<?php } else { ?>
										<td colspan="<?php echo $td_col_value + 4; ?>" align="center"><b>Closing Balance</b></td>
									<?php } ?>
									<?php
										if((((float)$fst_famt + (float)$fdt_famt + (float)$fct_sup_famt + (float)$fpt_famt) + (float)$ob_rev_amt) > (((float)$frt_famt + (float)$fct_famt + (float)$fst_sup_famt + (float)$fdt_sup_famt) + (float)$ob_pid_amt)){
											$famt = (((float)$fst_famt + (float)$fdt_famt + (float)$fct_sup_famt + (float)$fpt_famt) + (float)$ob_rev_amt) - (((float)$frt_famt + (float)$fct_famt + (float)$fst_sup_famt + (float)$fdt_sup_famt) + (float)$ob_pid_amt);
											echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>";
											echo "<td></td>";
										}
										else {
											$famt = (((float)$fst_famt + (float)$fdt_famt + (float)$fct_sup_famt + (float)$fpt_famt) + (float)$ob_rev_amt) - (((float)$frt_famt + (float)$fct_famt + (float)$fst_sup_famt + (float)$fdt_sup_famt) + (float)$ob_pid_amt);
											echo "<td></td>";
											echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($famt)."</td>";
										}
									?>
									<td></td>
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
