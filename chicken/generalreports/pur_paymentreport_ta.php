<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php"; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
	
	$today = date("Y-m-d");
			
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pname[$row['code']] = $row['name']; }
	$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pmname[$row['code']] = $row['description']; $pmcode[$row['code']] = $row['code']; }
	$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pmename[$row['code']] = $row['description']; $pmecode[$row['code']] = $row['code']; }

	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description'];}

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $officename[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname'];
	if($cname == "all") { $cnames = ""; } else { $cnames = " AND `ccode` = '$cname'"; }
	if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }
	
	$exoption = "displaypage"; $sectors = array(); $sectors["all"] = "all"; $sec_all_flag = 0;
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname']; $exl_pmode = $_POST['pay_mode']; $exl_pcoa = $_POST['pay_coa']; $exl_wname = $_POST['wname'];
		
		$sectors = array(); $sec_list = "";
        foreach($_POST['sectors'] as $scts){ $sectors[$scts] = $scts; if($scts == "all"){ $sec_all_flag = 1; } }
        $sects_list = implode("','", array_map('addslashes', $sectors));
        $secct_fltr = "";
        if($sec_all_flag == 1 ){ $secct_fltr = ""; $sec_list = "all"; }
        else { $secct_fltr = "AND `warehouse` IN ('$sects_list')"; $sec_list = implode(",",$sectors); }
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname = $exl_iname = $exl_wname = $exl_user = "all"; 
	}
	$url = "../PHPExcel/Examples/PaymentReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&ccode=".$exl_cname."&pmode=".$exl_pmode."&pcoa=".$exl_pcoa."&sector=".$sects_list;
	
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Display TCDS Calculations' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dtcds_flag = mysqli_num_rows($query);
	if((int)$dtcds_flag == 1){
		$hcol_cnt = 12;
	}
	else{
		$hcol_cnt = 9;
	}
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
		</style>
		<style>
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
				text-align:right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
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
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Payment Report</h3><?php
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
				<form action="pur_paymentreport_ta.php" method="post" >
					<?php } else { ?>
					<form action="pur_paymentreport_ta.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="<?php echo $hcol_cnt; ?>">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($cname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Payment Mode</label>&nbsp;
										<select name="pay_mode" id="pay_mode" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($pmcode as $mc){
											?>
													<option <?php if($_POST['pay_mode'] == $pmcode[$mc]) { echo 'selected'; } ?> value="<?php echo $pmcode[$mc]; ?>"><?php echo $pmname[$mc]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;<br/>
										<label class="reportselectionlabel">Cash/Bank</label>&nbsp;
										<select name="pay_coa" id="pay_coa" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($pmecode as $mc){
											?>
													<option <?php if($_POST['pay_coa'] == $pmecode[$mc]) { echo 'selected'; } ?> value="<?php echo $pmecode[$mc]; ?>"><?php echo $pmename[$mc]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="sectors[]" id="sectors[0]" class="form-control select2" style="width:180px;" multiple>
											<option value="all" <?php if (in_array("all", $sectors)) echo "selected"; ?>>All</option>
											<?php foreach($sector_code as $scode) { ?>
												<option value="<?php echo $scode; ?>" <?php if (in_array($scode, $sectors)) echo "selected"; ?>>
													<?php echo $sector_name[$scode]; ?>
												</option>
											<?php } ?>
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
								<th>Sl No.</th>
								<th>Date</th>
								<th>Supplier</th>
								<th>transaction No.</th>
								<th>Doc No.</th>
								<th>Payment Mode</th>
								<th>Payment Method</th>
								<?php
								if((int)$dtcds_flag == 1){
									echo '<th>Base Amount</th><th>TDS %</th><th>TDS Amt</th>';
								}
								?>
								<th>Amount</th>
								<!--<th>Cheque No.</th>
								<th>Cheque Date</th>-->
								<th>Remarks</th>
								<th>Warehouse</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date("Y-m-d",strtotime($todate));
								if($_POST['pay_mode'] == "all"){ $pmode = ""; } else{ $pmode = " AND `mode` = '".$_POST['pay_mode']."'"; }
								if($_POST['pay_coa'] == "all"){ $pcoa = ""; } else{ $pcoa = " AND `method` = '".$_POST['pay_coa']."'"; }
								$tbirds = $tjals = $ttotalweight = $temptyweight = $tnetweight = $tdiscountamt = $ttaxamount = $ttotalamt = 0;
								$sequence = "SELECT * FROM `pur_payments` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
								$flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
								$sql = $sequence."".$cnames."".$inames."".$secct_fltr."".$pmode."".$pcoa."".$flags;
								$query = mysqli_query($conn,$sql); $tamt1 = $ttcds_amt = 0; $sl = 1;
								while($row = mysqli_fetch_assoc($query)){
									echo "<tr>";
									echo "<td style='text-align:left;'>".$sl++."</td>";
									echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
									echo "<td style='text-align:left;'>".$pname[$row['ccode']]."</td>";
									echo "<td style='text-align:left;'>".$row['trnum']."</td>";
									echo "<td style='text-align:left;'>".$row['docno']."</td>";
									echo "<td style='text-align:left;'>".$pmname[$row['mode']]."</td>";
									echo "<td style='text-align:left;'>".$pmename[$row['method']]."</td>";

									if((int)$dtcds_flag == 1){
										echo "<td>".number_format_ind($row['amount1'])."</td>";
										echo "<td>".number_format_ind($row['tcds_per'])."</td>";
										echo "<td>".number_format_ind($row['tcds_amt'])."</td>";
										$tamt1 += (float)$row['amount1'];
										$ttcds_amt += (float)$row['tcds_amt'];
									}
									echo "<td>".number_format_ind($row['amount'])."</td>";
									/*if($row['mode'] == "MOD-002"){
										echo "<td style='text-align:left;'>".$row['cno']."</td>";
										echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($row['cdate']))."</td>";
									}
									else {
										echo "<td></td>";
										echo "<td></td>";
									}*/
									echo "<td style='text-align:left;'>".$row['remarks']."</td>";
									echo "<td style='text-align:left;'>".$officename[$row['warehouse']]."</td>";
									echo "</tr>";
									$ttotalamt = $ttotalamt + $row['amount'];
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="7" align="center"><b>Grand Total</b></td>
									<?php
									if((int)$dtcds_flag == 1){
										echo "<td>".number_format_ind($tamt1)."</td>";
										echo "<td></td>";
										echo "<td>".number_format_ind($ttcds_amt)."</td>";
									}
									?>
									<td><?php echo number_format_ind($ttotalamt); ?></td>
									<td colspan="2" align="center"></td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
		
	</body>
	
</html>
<?php include "header_foot.php"; ?>
