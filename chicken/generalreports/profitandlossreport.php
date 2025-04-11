<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db']; } else{ $db = ''; }
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php"; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
			
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
	
	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `item_category` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $catname[$row['code']] = $row['description']; $catcode[$row['code']] = $row['code']; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; $itemdetail[$row['code']] = $row['category']."@".$row['code']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$iname = $_POST['iname']; $sector_search = $all_sec_codes = ""; 
	if(isset($_POST['submit']) == true){
		$i = 0; $sec_codes = array();
		foreach($_POST['whname'] as $scodes){
			$i++;
			$sec_codes[$i] = $scodes;
			if($scodes != "all" || $scodes != ""){
				if($all_sec_codes == ""){
					$all_sec_codes = $scodes;
				}
				else{
					$all_sec_codes = $all_sec_codes."','".$scodes;
				}
			}
		}
		if($all_sec_codes != ""){
			$sector_from_search = " AND `fromwarehouse` IN ('$all_sec_codes')";
			$sector_to_search = " AND `towarehouse` IN ('$all_sec_codes')";
		}
		else{
			$sector_from_search = $sector_to_search = "";
		}
	}
	else{
		$sec_codes = array();
		$sec_codes[1] = "all"; $sector_from_search = $sector_to_search = "";
	}
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>
		
<html>
	<head>
	<link rel="stylesheet" type="text/css"href="reportstyle.css">
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
		</style>
		<style>
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
	
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Profit &amp; Loss</h3>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="profitandlossreport.php" method="post" onsubmit="return checkval()">
					<?php } else { ?>
					<form action="profitandlossreport.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="min-width:60%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="16">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" style="width: 90px;" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" style="width: 90px;" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">warehouse</label>&nbsp;
										<select name="whname[]" id="whname[]" style="width: 180px; height: 25px;" class="select" multiple>
											<option value="all" <?php foreach($sec_codes as $sid){ if($sid == "all"){ echo "selected"; } } ?>>-All-</option>
										<?php
											$sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
										?>
											<option value="<?php echo $row['code']; ?>" <?php foreach($sec_codes as $sid){ if($sid == $row['code']){ echo "selected"; } } ?>><?php echo $row['description']; ?></option>
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
							<thead class="thead2" style="background-color: #98fb98;">
								<tr>
									<th colspan="2">Expenses</th>
									<th colspan="2">Revenue</th>
								</tr>
								<tr>
									<!--<th>Code</th>-->
									<th>Description</th>
									<th>Amount</th>
									<!--<th>Code</th>-->
									<th>Description</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									$tcr_amt = $tdr_amt = $trec_amt = $texp_amt = $c = 0; $whcodes = array();
									foreach($_POST['whname'] as $whcode){ if($whcode == "all"){ $c = $w_all = 1; } else{ $whcodes[] = $whcode; } }
									$csize = sizeof($whcodes); $whcode = "";
									for($i = 0;$i <= $csize;$i++){ if($whcodes[$i] == ""){ } else if($whcode == ""){ $whcode = $whcodes[$i]; } else{ $whcode = $whcode."','".$whcodes[$i]; } }
									if($c == 1){ $whname = ""; } else{ $whname = " AND `warehouse` IN ('$whcode')"; }
									$fdate = date("Y-m-d",strtotime($_POST['fromdate'])); $tdate = date("Y-m-d",strtotime($_POST['todate']));
									$pdate = date('Y-m-d', strtotime($fdate.'-1 days'));
									
									$seq = "SELECT * FROM `item_closingstock` WHERE `date` = '$pdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$grp = " ORDER BY `code` ASC";
									$sql = $seq."".$whname."".$grp;
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ 
										$ob_qty[$row['code']] = $row['closedquantity'];
										$ob_amt[$row['code']] = $ob_amt[$row['code']] + ($row['closedquantity'] * $row['price']);
									}
									$seq = "SELECT * FROM `item_closingstock` WHERE `date` = '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$grp = " ORDER BY `code` ASC";
									$sql = $seq."".$whname."".$grp;
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$cb_qty[$row['code']] = $cb_qty[$row['code']] + $row['closedquantity'];
										$cb_amt[$row['code']] = $cb_amt[$row['code']] + ($row['closedquantity'] * $row['price']);
									}
									$seq = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
									$grp = " ORDER BY `itemcode` ASC";
									$sql = $seq."".$whname."".$grp;
									$query = mysqli_query($conn,$sql); $old_inv = "";
									while($row = mysqli_fetch_assoc($query)){
										$pur_qty[$row['itemcode']] = $pur_qty[$row['itemcode']] + $row['netweight'];
										$tcdsper = $tcdsamt = 0; $tcdsper = $row['tcdsper']; $tcdsamt = $row['tcdsamt'];
										if($tcdsper == 0 || $tcdsper == "0" || $tcdsper == "0.00" || $tcdsper == 0.00 || $tcdsamt == 0 || $tcdsamt == "0" || $tcdsamt == "0.00" || $tcdsamt == 0.00){
											$pur_amt[$row['itemcode']] = $pur_amt[$row['itemcode']] + $row['totalamt'];
										}
										else{
											$pur_amt[$row['itemcode']] = ($pur_amt[$row['itemcode']] + $row['totalamt']) + (($tcdsper / 100) * $row['totalamt']);
										}
									}
									$seq = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND  `date` <= '$tdate'";
									$flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `itemcode` ASC";
									$sql = $seq."".$whname."".$flags;
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$sal_qty[$row['itemcode']] = $sal_qty[$row['itemcode']] + $row['netweight'];
										$sal_amt[$row['itemcode']] = $sal_amt[$row['itemcode']] + $row['totalamt'];
									}

									$w_alist = array(); $w_all = 0; foreach($_POST['whname'] as $wcode){ if($wcode == "all"){ $w_all = 1; } $w_alist[$wcode] = $wcode; }
									$w_list = implode("','",$w_alist);
									$w_fltr = ""; if($w_all == 0){ $w_fltr = " AND `mtype` = 'sector' AND `ccode` IN ('$w_list')"; }

									$seq = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND  `date` <= '$tdate'";
									$flags = " AND `active` = '1' AND `dflag` = '0' ORDER BY `itemcode` ASC";
									$sql = $seq."".$w_fltr."".$flags; //".$whname."
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$mort_qty[$row['itemcode']] = $mort_qty[$row['itemcode']] + $row['netweight'];
										$mort_amt[$row['itemcode']] = $mort_amt[$row['itemcode']] + $row['amount'];
									}
									if($sec_codes[1] != "all"){
										$sql = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `active` = 1 AND `tdflag` = 0 AND `pdflag` = 0".$sector_from_search; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$tout_qty[$row['code']] = $tout_qty[$row['code']] + $row['quantity'];
											$tout_amt[$row['code']] = $tout_amt[$row['code']] + ($row['quantity'] * $row['price']);
										}
										$sql = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `active` = 1 AND `tdflag` = 0 AND `pdflag` = 0".$sector_to_search;$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$tin_qty[$row['code']] = $tin_qty[$row['code']] + $row['quantity'];
											$tin_amt[$row['code']] = $tin_amt[$row['code']] + ($row['quantity'] * $row['price']);
										}
									}
									
									$sql = "SELECT * FROM `acc_coa` WHERE `type` = 'COA-0003' AND `active` = '1' ORDER BY `description` ASC";
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ if($coa_code == ""){ $coa_code = $row['code']; $coa[] = $row['code']; $coaname[$row['code']] = $row['description']; } else { $coa_code = $coa_code."','".$row['code']; $coa[] = $row['code']; $coaname[$row['code']] = $row['description']; } }
									
									$seq = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `fcoa` IN ('$coa_code') AND `active` = '1'";
									$grp = " ORDER BY `fcoa` ASC";
									$sql = $seq."".$whname."".$grp;
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$exp_amt[$row['fcoa']] = $exp_amt[$row['fcoa']] + $row['amount'];
										$texp_amt = $texp_amt + $row['amount'];
									}
									$seq = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND  `date` <= '$tdate' AND `tcoa` IN ('$coa_code') AND `active` = '1'";
									$grp = " ORDER BY `tcoa` ASC";
									$sql = $seq."".$whname."".$grp;
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$rec_amt[$row['tcoa']] = $rec_amt[$row['tcoa']] + $row['amount'];
										$trec_amt = $trec_amt + $row['amount'];
									}
									foreach($catcode as $cat_code){
										foreach($itemdetail as $itm_code){
											$spl_code = explode("@",$itm_code);
											if($cat_code == $spl_code[0]){
												$ob_cat_amt[$cat_code] = $ob_cat_amt[$cat_code] + $ob_amt[$spl_code[1]];
												$cb_cat_amt[$cat_code] = $cb_cat_amt[$cat_code] + $cb_amt[$spl_code[1]];
												$pur_cat_amt[$cat_code] = $pur_cat_amt[$cat_code] + $pur_amt[$spl_code[1]];
												$sal_cat_amt[$cat_code] = $sal_cat_amt[$cat_code] + $sal_amt[$spl_code[1]];
												$tin_cat_amt[$cat_code] = $tin_cat_amt[$cat_code] + $tin_amt[$spl_code[1]];
												$tout_cat_amt[$cat_code] = $tout_cat_amt[$cat_code] + $tout_amt[$spl_code[1]];
												$mort_cat_amt[$cat_code] = $mort_cat_amt[$cat_code] + $mort_amt[$spl_code[1]];
											} else { }
										}
									}
									echo "<tr>";
									echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Opening</td>";
									echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Sales</td>";
									echo "</tr>";
									foreach($catcode as $cat_code){
										if(number_format_ind($ob_cat_amt[$cat_code]) == "0.00" && number_format_ind($sal_cat_amt[$cat_code]) == "0.00"){
											$tdr_amt = $tdr_amt + $ob_cat_amt[$cat_code] + $pur_cat_amt[$cat_code];
										}
										else{
											echo "<tr>";
											//echo "<td></td>";
											echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
											echo "<td>".number_format_ind($ob_cat_amt[$cat_code])."</td>";
											//echo "<td></td>";
											echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
											echo "<td>".number_format_ind($sal_cat_amt[$cat_code])."</td>";
											echo "</tr>";
											$tdr_amt = $tdr_amt + $ob_cat_amt[$cat_code] + $pur_cat_amt[$cat_code];
											$t_ob_amt = $t_ob_amt + $ob_cat_amt[$cat_code];
											$t_sl_amt = $t_sl_amt + $sal_cat_amt[$cat_code];
										}
									}
									echo "<tr style='background-color: #98fb98;'>";
									echo "<td style='text-align:center;font-weight:bold;'>Total Opening Balance Amount</td>";
									echo "<td>".number_format_ind($t_ob_amt)."</td>";
									echo "<td style='text-align:center;font-weight:bold;'>Total Sales Amount</td>";
									echo "<td>".number_format_ind($t_sl_amt)."</td>";
									echo "</tr>";
									if($sec_codes[1] != "all"){
										echo "<tr>";
										echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Transfer In</td>";
										echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Transfer Out</td>";
										echo "</tr>";
										foreach($catcode as $cat_code){
											//echo "<br/>".number_format_ind($tout_cat_amt[$cat_code]);
											if(number_format_ind($tout_cat_amt[$cat_code]) == "0.00" && number_format_ind($tin_cat_amt[$cat_code]) == "0.00"){
												
											}
											else{
												echo "<tr>";
												//echo "<td></td>";
												echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
												echo "<td>".number_format_ind($tin_cat_amt[$cat_code])."</td>";
												//echo "<td></td>";
												echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
												echo "<td>".number_format_ind($tout_cat_amt[$cat_code])."</td>";
												echo "</tr>";
												$t_tin_amt = $t_tin_amt + $tin_cat_amt[$cat_code];
												$t_tout_amt = $t_tout_amt + $tout_cat_amt[$cat_code];
												
												$tcr_amt = $tcr_amt + $tout_cat_amt[$cat_code];
												$tdr_amt = $tdr_amt + $tin_cat_amt[$cat_code];
											}
										}
										echo "<tr style='background-color: #98fb98;'>";
										echo "<td style='text-align:center;font-weight:bold;'>Total Transfer In Amount</td>";
										echo "<td>".number_format_ind($t_tin_amt)."</td>";
										echo "<td style='text-align:center;font-weight:bold;'>Total Transfer Out Amount</td>";
										echo "<td>".number_format_ind($t_tout_amt)."</td>";
										echo "</tr>";
									}
									echo "<tr>";
									echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Purchases</td>";
									echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Mortality</td>";
									echo "</tr>";
									foreach($catcode as $cat_code){
										if($pur_cat_amt[$cat_code] == "" && $mort_cat_amt[$cat_code] == ""){
											//$tcr_amt = $tcr_amt + $sal_cat_amt[$cat_code] + $mort_cat_amt[$cat_code];
										}
										else{
											echo "<tr>";
											//echo "<td></td>";
											echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
											echo "<td>".number_format_ind($pur_cat_amt[$cat_code])."</td>";
											//echo "<td></td>";
											echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
											echo "<td>".number_format_ind($mort_cat_amt[$cat_code])."</td>";
											echo "</tr>";
											$tdr_amt = $tdr_amt + $mort_cat_amt[$cat_code];
											$t_pur_amt = $t_pur_amt + $pur_cat_amt[$cat_code];
											$t_mt_amt = $t_mt_amt + $mort_cat_amt[$cat_code];
										}
									}
									echo "<tr style='background-color: #98fb98;'>";
									echo "<td style='text-align:center;font-weight:bold;'>Total Purchase Amount</td>";
									echo "<td>".number_format_ind($t_pur_amt)."</td>";
									echo "<td style='text-align:center;font-weight:bold;'>Total Mortality Amount</td>";
									echo "<td>".number_format_ind($t_mt_amt)."</td>";
									echo "</tr>";

									echo "<tr>";
									echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'></td>";
									echo "<td colspan='2' style='text-align:center;font-size:15px;font-weight:bold;'>Closing</td>";
									echo "</tr>";
									foreach($catcode as $cat_code){
										if($cb_cat_amt[$cat_code] == ""){
											$tcr_amt = $tcr_amt + $sal_cat_amt[$cat_code] + $cb_cat_amt[$cat_code];
										}
										else{
											echo "<tr>";
											//echo "<td></td>";
											echo "<td style='text-align:left;'></td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td style='text-align:left;'>".$catname[$cat_code]."</td>";
											echo "<td>".number_format_ind($cb_cat_amt[$cat_code])."</td>";
											echo "</tr>";
											$tcr_amt = $tcr_amt + $sal_cat_amt[$cat_code] + $cb_cat_amt[$cat_code];
											//$t_pur_amt = $t_pur_amt + $pur_cat_amt[$cat_code];
											$t_cl_amt = $t_cl_amt + $cb_cat_amt[$cat_code];
										}
									}
									echo "<tr style='background-color: #98fb98;'>";
									echo "<td style='text-align:center;font-weight:bold;'></td>";
									echo "<td></td>";
									echo "<td style='text-align:center;font-weight:bold;'>Total Closing Amount</td>";
									echo "<td>".number_format_ind($t_cl_amt)."</td>";
									echo "</tr>";
									
									if($tcr_amt > $tdr_amt){
										echo "<tr style='background-color: #98fb98;'>";
											//echo "<td></td>";
											echo "<td></td>";
											echo "<td></td>";
											//echo "<td></td>";
											echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Gross Profit</td>";
											echo "<td>".number_format_ind($tcr_amt - $tdr_amt)."</td>";
										echo "</tr>";
										$gp_amt = $tcr_amt - $tdr_amt;
										$gl_amt = 0;
									}
									else{
										echo "<tr style='background-color: #98fb98;'>";
											//echo "<td></td>";
											echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Gross Loss</td>";
											echo "<td>".number_format_ind($tdr_amt - $tcr_amt)."</td>";
											//echo "<td></td>";
											echo "<td></td>";
											echo "<td></td>";
										echo "</tr>";
										$gp_amt = 0;
										$gl_amt = $tdr_amt - $tcr_amt;
									}
									echo "<tr>";
									//echo "<td></td>";
									echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Expenses</td>";
									echo "<td></td>";
									//echo "<td></td>";
									echo "<td></td>";
									echo "<td></td>";
									echo "</tr>";
									echo "<tr style='background-color: #98fb98;'>";
									echo "<td colspan='1' style='text-align:center;font-size:15px;font-weight:bold;'>Total</td>";
									echo "<td>".number_format_ind($trec_amt)."</td>";
									echo "<td></td>";
									echo "<td></td>";
									echo "</tr>";
									foreach($coa as $coa_codes){
										if($rec_amt[$coa_codes] == "" || $rec_amt[$coa_codes] == "0.00" || $rec_amt[$coa_codes] == ".00"){
											
										}
										else{
											echo "<tr>";
												//echo "<td></td>";
												echo "<td style='text-align:left;'>".$coaname[$coa_codes]."</td>";
												echo "<td>".number_format_ind($rec_amt[$coa_codes])."</td>";
												//echo "<td></td>";
												echo "<td></td>";
												echo "<td></td>";
											echo "</tr>";
										}
									}
									/*$gp_amt = 0;
										$gl_amt = $tdr_amt - $tcr_amt;
										if($gp_amt == 0 && $gl_amt != 0){}
										else if($gp_amt != 0 && $gl_amt == 0){}*/
									if($tcr_amt > $tdr_amt){
										echo "<tr style='background-color: #98fb98;'>";
											if((($tcr_amt - $tdr_amt) - $trec_amt) > 0){
												echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Net Profit</td>";
												echo "<td style='font-weight:bold;'>".number_format_ind(($tcr_amt - $tdr_amt) - $trec_amt)."</td>";
												echo "<td></td>";
												echo "<td></td>";
												$net_pf_amt = ($tcr_amt - $tdr_amt) - $trec_amt;
												$net_ls_amt = 0;
											}
											else{
												echo "<td></td>";
												echo "<td></td>";
												echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Net Loss</td>";
												echo "<td style='font-weight:bold;'>".number_format_ind(($tdr_amt - $tcr_amt) + $trec_amt)."</td>";
												$net_ls_amt = ($tdr_amt - $tcr_amt) + $trec_amt;
												$net_pf_amt = 0;
											}
											
										echo "</tr>";
									}
									else{
										echo "<tr style='background-color: #98fb98;'>";
											if((($tcr_amt - $tdr_amt) - $trec_amt) > 0){
												echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Net Profit</td>";
												echo "<td style='font-weight:bold;'>".number_format_ind(($tcr_amt - $tdr_amt) - $trec_amt)."</td>";
												echo "<td></td>";
												echo "<td></td>";
												$net_pf_amt = ($tcr_amt - $tdr_amt) - $trec_amt;
												$net_ls_amt = 0;
											}
											else{
												echo "<td></td>";
												echo "<td></td>";
												echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Net Loss</td>";
												echo "<td style='font-weight:bold;'>".number_format_ind(($tdr_amt - $tcr_amt) + $trec_amt)."</td>";
												$net_ls_amt = ($tdr_amt - $tcr_amt) + $trec_amt;
												$net_pf_amt = 0;
											}
											
										echo "</tr>";
									}
									echo "<tr style='background-color: #98fb98;'>";
										echo "<td style='text-align:center;font-size:15px;font-weight:bold;'>Total</td>";
										echo "<td style='font-weight:bold;'>".number_format_ind($trec_amt + $t_pur_amt + $t_tin_amt + $t_ob_amt + $net_pf_amt)."</td>";
										echo "<td></td>";
										echo "<td style='font-weight:bold;'>".number_format_ind($t_sl_amt + $t_tout_amt + $t_cl_amt + $net_ls_amt - $t_mt_amt)."</td>";
									echo "</tr>";
								}
							?>
							</tbody>
						</table>
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
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
		
	</body>
	
</html>
<?php include "header_foot.php"; ?>
