<?php
	//main_stocktransferreport.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	$today = date("Y-m-d");
	$dbname = $_SESSION['dbase'];
	$users_code = $_SESSION['userid'];
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
	$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pname[$row['code']] = $row['name']; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }

	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; $sector_code[$row['code']] = $row['code']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $fwhname = $_POST['fwhname']; $twhname = $_POST['twhname'];
	if($cname == "all") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
	if($iname == "all") { $inames = ""; } else { $inames = " AND `code` = '$iname'"; }
	if($fwhname == "all") { $fwnames = ""; } else { $fwnames = " AND `fromwarehouse` = '$fwhname'"; }
	if($twhname == "all") { $twnames = ""; } else { $twnames = " AND `towarehouse` = '$twhname'"; }
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$saccess = $row['supadmin_access'];
		$aaccess = $row['admin_access'];
		$naccess = $row['normal_access'];
	}
	$utype = "NA";
	if($saccess == 1){
		$utype = "S";
	}
	else if($aaccess == 1){
		$utype = "A";
	}
	else if($naccess == 1){
		$utype = "N";
	}
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
	else{
		$sql = "SELECT * FROM `log_useraccess` WHERE `empcode` = '$users_code' AND `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = " AND `addedemp` LIKE '$users_code'";
	}
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>

<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=cus_salesreport($fromdate-$todate).xls");
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
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;" class="reportheaderlabel">Stock Transaction Report</label>&ensp;
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $pname[$cname]; ?></label>&ensp;
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<form action="main_stocktransferreport.php" method="post">
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="17">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">From Warehouse</label>&nbsp;
										<select name="fwhname" id="fwhname" class="form-control select2">
											<option value="all" <?php if($fwhname == "all") { echo 'selected'; } ?>>-All-</option>
											<?php
												foreach($sector_code as $scode){
											?>
													<option <?php if($fwhname == $scode) { echo 'selected'; } ?> value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Warehouse</label>&nbsp;
										<select name="twhname" id="twhname" class="form-control select2">
											<option value="all" <?php if($twhname == "all") { echo 'selected'; } ?>>-All-</option>
											<?php
												foreach($sector_code as $scode){
											?>
													<option <?php if($twhname == $scode) { echo 'selected'; } ?> value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option>
											<?php
												}
											?>
										</select>
									<br/>
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="iname" id="iname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($iname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
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
										<label class="reportselectionlabel">User</label>&nbsp;
										<select name="ucode" id="ucode" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($user_code as $ucodes){
											?>
													<option <?php if($_POST['ucode'] == $user_code[$ucodes]) { echo 'selected'; } ?> value="<?php echo $user_code[$ucodes]; ?>"><?php echo $user_name[$ucodes]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl No.</th>
								<th>Date</th>
								<th>Transaction No.</th>
								<th>Dc No.</th>
								<th>From Warehouse</th>
								<th>To Warehouse</th>
								<th>Item</th>
								<th>Birds</th>
								<th>Weight</th>
								<th>Price</th>
								<th>Amount</th>
								<th>Narration</th>
								<th>User</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date("Y-m-d",strtotime($todate));
								if($_POST['ucode'] == "all"){ $usr_code = ""; } else{ $usr_code = " AND `addedemp` = '".$_POST['ucode']."'"; }
								$tbirds = $tnetweight = $totalamt = 0;
								$sequence = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
								$flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`id` ASC";
								$sql = $sequence."".$inames."".$fwnames."".$twnames."".$usr_code."".$flags;
								$query = mysqli_query($conn,$sql);
								$totalamt = 0; $sl = 1;
								while($row = mysqli_fetch_assoc($query)){
									echo "<tr>";
									echo "<td style='text-align:left;'>".$sl++."</td>";
									echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
									echo "<td style='text-align:left;'>".$row['trnum']."</td>";
									echo "<td style='text-align:left;'>".$row['dcno']."</td>";
									echo "<td style='text-align:left;'>".$sector_name[$row['fromwarehouse']]."</td>";
									echo "<td style='text-align:left;'>".$sector_name[$row['towarehouse']]."</td>";
									echo "<td style='text-align:left;'>".$itemname[$row['code']]."</td>";
									if(number_format_ind($row['birds']) == ".00"){ $birds = "0.00"; } else{ $birds = $row['birds']; }
									if(number_format_ind($row['quantity']) == ".00"){ $quantity = "0.00"; } else{ $quantity = $row['quantity']; }
									if(number_format_ind($row['price']) == ".00"){ $price = "0.00"; } else{ $price = $row['price']; }
									echo "<td>".number_format_ind($birds)."</td>";
									echo "<td>".number_format_ind($quantity)."</td>";
									echo "<td>".number_format_ind($price)."</td>";
									echo "<td>".number_format_ind($quantity * $price)."</td>";
									echo "<td style='text-align:left;'>".$row['remarks']."</td>";
									echo "<td style='text-align:center;'>".$user_name[$row['addedemp']]."</td>";
									echo "</tr>";
									
									$tbirds = $tbirds + $birds;
									$tnetweight = $tnetweight + $quantity;
									$totalamt = $totalamt + ($quantity * $price);
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="7" align="center"><b>Grand Total</b></td>
									<td><?php echo number_format_ind($tbirds); ?></td>
									<td><?php echo number_format_ind($tnetweight); ?></td>
									<td></td>
									<td><?php echo number_format_ind($totalamt); ?></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
