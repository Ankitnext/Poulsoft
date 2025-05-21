<?php
	//cus_salesreport2.php
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
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname']; $gname = $_POST['gname'];
	if($iname == "all") { $inames = ""; } else { $inames = " AND `itemcode` = '$iname'"; }
	if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }
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
	
	$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Sales Report' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $prate_flag = $row['prate']; $vehicle_flag = $row['vehicle_flag']; }
	if($vehicle_flag  == "" || $vehicle_flag == 0){ $vehicle_flag  = 0; }
	if($prate_flag == 1 || $prate_flag == "1"){
		$fdate = date("Y-m-d",strtotime($fromdate));
		$tdate = date("Y-m-d",strtotime($todate));
		$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['cgroup']] = $row['new_price']; }
	}
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname']; $exl_iname = $_POST['iname']; $exl_wname = $_POST['wname']; $exl_user = $_POST['ucode']; $exl_gname = $_POST['gname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname = $exl_iname = $exl_wname = $exl_user = $exl_gname = "all";
	}
	$url = "../PHPExcel/Examples/SalesReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&ccode=".$exl_cname."&item=".$exl_iname."&sector=".$exl_wname."&user=".$exl_user."&gname=".$exl_gname;
	
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
			body{
				color: black;
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
					<td><?php echo $row['cdetails']; ?></td> <?php } ?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;" class="reportheaderlabel">Sales Report</label>&ensp;
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$cname]; ?></label>&ensp;
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
					<form action="cus_salesreport2.php" method="post">
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="20">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="gname" id="gname" class="form-control select2" onchange="groupbycussel()">
											<option value="all">-All-</option>
											<?php
												foreach($grp_code as $gcode){
											?>
													<option <?php if($gname == $gcode) { echo 'selected'; } ?> value="<?php echo $gcode; ?>"><?php echo $grp_name[$gcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($cus_code as $vcode){
											?>
													<option <?php if($cname == $vcode) { echo 'selected'; } ?> value="<?php echo $vcode; ?>"><?php echo $cus_name[$vcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="iname" id="iname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($item_code as $icode){
											?>
													<option <?php if($iname == $icode) { echo 'selected'; } ?> value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option>
											<?php
												}
											?>
										</select>
									<br/>
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="wname" id="wname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($sector_code as $vcode){
											?>
													<option <?php if($wname == $vcode) { echo 'selected'; } ?> value="<?php echo $vcode; ?>"><?php echo $sector_name[$vcode]; ?></option>
											<?php
												}
											?>
										</select>
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
								<th>Customer</th>
								<th>Invoice</th>
								<th>Book Invoice</th>
								<th>Item</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1){ ?><th>Jals</th><?php } ?>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th>Birds</th><?php } ?>
								<?php if($ifjbwen == 1){ ?> <th>Total Wt.</th><th>Empty Wt.</th> <?php } ?>
								<th>Net Wt.</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th>Avg.Wt.</th><?php } ?>
								<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<th>Paper Rate</th>"; } ?>
								<th>Price</th>
								<!--<th>Discount</th>
								<th>Tax</th>-->
								<th>Amount</th>
								<th>Total Amount</th>
								<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<th>Vehicle No.</th>"; } ?>
								<th>Warehouse</th>
								<th>Narration</th>
								<th>User</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								if($_POST['export'] != "exportexcel"){
									$fromdate = date("Y-m-d",strtotime($fromdate));
									$todate = date("Y-m-d",strtotime($todate));
									if($gname == "all" && $cname == "all"){
										$cnames = "";
									}
									else if($gname == "all" && $cname != "all"){
										$cnames = " AND `customercode` = '$cname'";
									}
									else if($gname != "all" && $cname == "all"){
										$ccodes = "";
										foreach($cus_code as $vcode){
											if($gname == $cus_group[$vcode]){
												if($ccodes == ""){
													$ccodes = $vcode;
												}
												else{
													$ccodes = $ccodes."','".$vcode;
												}
											}
										}
										$cnames = " AND `customercode` IN ('$ccodes')";
									}
									else if($gname != "all" && $cname != "all"){
										$cnames = " AND `customercode` = '$cname'";
									}
									else{
										$cnames = "";
									}
									if($_POST['ucode'] == "all"){ $usr_code = ""; } else{ $usr_code = " AND `addedemp` = '".$_POST['ucode']."'"; }
									$tbirds = $tjals = $ttotalweight = $temptyweight = $tnetweight = $tdiscountamt = $ttaxamount = $ttotalamt = 0;
									$sequence = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`updated` ASC";
									$sql = $sequence."".$cnames."".$inames."".$wnames."".$usr_code."".$addedemp."".$flags;
									$query = mysqli_query($conn,$sql); $inv_count = array();
									while($row = mysqli_fetch_assoc($query)){
										$inv_count[$row['invoice']] = $inv_count[$row['invoice']] + 1;
									}
									$sql = $sequence."".$cnames."".$inames."".$wnames."".$usr_code."".$addedemp."".$flags;
									$query = mysqli_query($conn,$sql); $old_inv = ""; $finaltotal = 0; $sl = 1;
									while($row = mysqli_fetch_assoc($query)){
										echo "<tr>";
										echo "<td style='text-align:left;'>".$sl++."</td>";
										echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
										echo "<td style='text-align:left;'>".$cus_name[$row['customercode']]."</td>";
										echo "<td style='text-align:left;'>".$row['invoice']."</td>";
										echo "<td style='text-align:left;'>".$row['bookinvoice']."</td>";
										echo "<td style='text-align:left;'>".$item_name[$row['itemcode']]."</td>";
										if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".number_format_ind($row['jals'])."</td>"; } else {  }
										if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".number_format_ind($row['birds'])."</td>"; } else {  }
										if($ifjbwen == 1){ echo "<td>".number_format_ind($row['totalweight'])."</td>"; } else {  }
										if($ifjbwen == 1){ echo "<td>".number_format_ind($row['emptyweight'])."</td>"; } else {  }
										echo "<td>".number_format_ind($row['netweight'])."</td>";
										if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
											if($row['birds'] == 0 || $row['birds'] == "0.00" || $row['birds'] == ".00" || $row['birds'] == "0"){
												$avg_wt = 0;
											}
											else{
												$avg_wt = (float)$row['netweight'] / (float)$row['birds'];
											}
											echo "<td>".number_format_ind($avg_wt)."</td>";
										}
										if($prate_flag == 1 || $prate_flag == "1"){
											$prate_index = $row['date']."@".$cus_group[$row['customercode']];
											echo "<td>".number_format_ind($prates[$prate_index])."</td>";
										}
										echo "<td>".number_format_ind($row['itemprice'])."</td>";
										//echo "<td>".number_format_ind($row['discountamt'])."</td>";
										//echo "<td>".number_format_ind($row['taxamount'])."</td>";
										echo "<td>".number_format_ind($row['totalamt'])."</td>";
										if($old_inv != $row['invoice']){
											$inv_no = $row['invoice'];
											echo "<td rowspan='".$inv_count[$inv_no]."'>".number_format_ind($row['finaltotal'])."</td>";
											$old_inv = $row['invoice'];
											$finaltotal = $finaltotal + (float)$row['finaltotal'];
										}
										if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td>".$row['vehiclecode']."</td>"; }
										echo "<td style='text-align:left;'>".$sector_name[$row['warehouse']]."</td>";
										echo "<td style='text-align:left;'>".$row['remarks']."</td>";
										echo "<td style='text-align:center;'>".$user_name[$row['addedemp']]."</td>";
										echo "</tr>";
										
										$tbirds = $tbirds + (float)$row['birds'];
										$tjals = $tjals + (float)$row['jals'];
										$ttotalweight = $ttotalweight + (float)$row['totalweight'];
										$temptyweight = $temptyweight + (float)$row['emptyweight'];
										$tnetweight = $tnetweight + (float)$row['netweight'];
										$tdiscountamt = $tdiscountamt + (float)$row['discountamt'];
										$ttaxamount = $ttaxamount + (float)$row['taxamount'];
										$ttotalamt = $ttotalamt + (float)$row['totalamt'];
										if($ttotalamt > 0 && $tnetweight > 0){
											$tavgprice = $ttotalamt / $tnetweight;
										}
										else{
											$tavgprice = 0;
										}
										
									}
								?>
									<tr class="foottr" style="background-color: #98fb98;">
										<td colspan="6" align="center"><b>Grand Total</b></td>
										<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo number_format_ind($tjals); ?></td>
										<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo number_format_ind($tbirds); ?></td>
										<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo number_format_ind($ttotalweight); ?></td>
										<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo number_format_ind($temptyweight); ?></td>
										<td><?php echo number_format_ind($tnetweight); ?></td>
										<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>>
										<?php
										if($tnetweight > 0 && $tbirds > 0){
											echo number_format_ind($tnetweight / $tbirds);
										}
										else{
											echo number_format_ind(0);
										}
										?></td>
										<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; } ?>
										<td><?php if($tavgprice == 0 || $tavgprice == 0.00 || $tavgprice == .00 || $tavgprice == "NAN.00"){ echo "0.00"; } else{ echo number_format_ind($tavgprice); } ?></td>
										<!--<td>< echo number_format_ind($tdiscountamt);</td>
										<td>//echo number_format_ind($ttaxamount);</td>-->
										<td><?php echo number_format_ind($ttotalamt); ?></td>
										<td><?php echo number_format_ind($ttotalamt); ?></td>
										<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; } ?>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<script>
			function checkexcelval(){
				var a = document.getElementById("datepickers").value;
				var b = document.getElementById("datepickers1").value;
				var c = document.getElementById("cname").value;
				var d = document.getElementById("iname").value;
				var e = document.getElementById("wname").value;
				var f = document.getElementById("ucode").value;
				var g = document.getElementById("export").value;
				if(g.match("exportexcel")){
					var h = '<?php echo $url; ?>';
					window.open(h, '_BLANK');
				}
				else{
					
				}
			}
			function groupbycussel(){
				var gname = document.getElementById("gname").value;
				removeAllOptions(document.getElementById("cname"));
				myselect = document.getElementById("cname"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				
				if(gname == "all"){
					<?php
					foreach($cus_code as $vcode){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>"); theOption1.value = "<?php echo $vcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else{
					<?php
					foreach($cus_code as $vcode){ echo "if(gname == '$cus_group[$vcode]'){";?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>"); theOption1.value = "<?php echo $vcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php echo "}"; } ?>
				}
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
		</script>
		<?php if($exoption == "displaypage") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
