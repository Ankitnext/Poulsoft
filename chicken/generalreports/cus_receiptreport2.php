<?php 
    //cus_receiptreport2.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php";$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid']; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; $dbname = $db;
		$users_code = $_GET['emp_code'];}
			
	$today = date("Y-m-d");
			
	
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `main_reportfields` WHERE `field` = 'Receipt Report' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $dflag = $row['denomination']; } if($dflag == "" || $dflag == NULL || $dflag == "0" || $dflag == 0){ $dflag = 0; }

	$sql = "SELECT * FROM `acc_modes` WHERE `description` NOT IN ('Cash') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pmname[$row['code']] = $row['description']; $pmcode[$row['code']] = $row['code']; }

	$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `ctype` NOT IN ('Cash') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pmename[$row['code']] = $row['description']; $pmecode[$row['code']] = $row['code']; }
    
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname']; $gname = $_POST['gname'];
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
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
		//$sql = "SELECT * FROM `log_useraccess` WHERE `empcode` = '$users_code' AND `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		//while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		//$addedemp = " AND `addedemp` LIKE '$users_code'";
	}
	
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname']; $exl_pmode = $_POST['pay_mode']; $exl_pcoa = $_POST['pay_coa']; $exl_wname = $_POST['wname'];  $exl_user = $_POST['ucode']; $exl_gname = $_POST['gname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname = $exl_iname = $exl_wname = $exl_user = $exl_user = $exl_gname = "all";
	}
	$url = "../PHPExcel/Examples/ReceiptReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&ccode=".$exl_cname."&pmode=".$exl_pmode."&pcoa=".$exl_pcoa."&sector=".$exl_wname."&user=".$exl_user."&gname=".$exl_gname;
	
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
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Receipt Report</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$cname]; ?></label><br/>
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
				<form action="cus_receiptreport2.php" method="post" >
					<?php } else { ?>
					<form action="cus_receiptreport2.php?db=<?php echo $db; ?>" method="post" >
					<?php } ?>
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="19">
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
								<th>Transaction No.</th>
								<th>Doc No.</th>
								<th>Payment Mode</th>
								<th>Payment Method</th>
								<th>Amount</th>
								<?php
								if($dflag == 1){
								?>
								<th>Coins</th>
								<th>C-10</th>
								<th>C-20</th>
								<th>C-50</th>
								<th>C-100</th>
								<th>C-200</th>
								<th>C-500</th>
								<th>C-2000</th>
								<?php
								}
								?>
								<th>Remarks</th>
								<th>Warehouse</th>
								<th>User</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date("Y-m-d",strtotime($todate));
								if($gname == "all" && $cname == "all"){
										$cnames = "";
									}
									else if($gname == "all" && $cname != "all"){
										$cnames = " AND `ccode` = '$cname'";
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
										$cnames = " AND `ccode` IN ('$ccodes')";
									}
									else if($gname != "all" && $cname != "all"){
										$cnames = " AND `ccode` = '$cname'";
									}
									else{
										$cnames = "";
									}
                                    
								if($_POST['pay_mode'] == "all"){ $pmode = " AND `mode` NOT IN ('Cash','MOD-001')"; } else{ $pmode = " AND `mode` = '".$_POST['pay_mode']."' AND `mode` NOT IN ('Cash','MOD-001')"; }
								if($_POST['pay_coa'] == "all"){ $pcoa = ""; } else{ $pcoa = " AND `method` = '".$_POST['pay_coa']."'"; }
								if($_POST['ucode'] == "all"){ $usr_code = ""; }
								else{
									$usr_code = " AND `addedemp` = '".$_POST['ucode']."'";
									//$usr_code = "";
								}
								$tbirds = $tjals = $ttotalweight = $temptyweight = $tnetweight = $tdiscountamt = $ttaxamount = $ttotalamt = 0;
								$sequence = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
								$flags = " AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
								$sql = $sequence."".$cnames."".$inames."".$wnames."".$pmode."".$pcoa."".$usr_code."".$addedemp."".$flags;
								$query = mysqli_query($conn,$sql);
								$tccoins = $tc10 = $tc20 = $tc50 = $tc100 = $tc200 = $tc500 = $tc2000 = 0; $sl = 1;
								while($row = mysqli_fetch_assoc($query)){
									echo "<tr>";
									echo "<td style='text-align:left;'>".$sl++."</td>";
									echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
									echo "<td style='text-align:left;'>".$cus_name[$row['ccode']]."</td>";
									echo "<td style='text-align:left;'>".$row['trnum']."</td>";
									echo "<td style='text-align:left;'>".$row['docno']."</td>";
									echo "<td style='text-align:left;'>".$pmname[$row['mode']]."</td>";
									echo "<td style='text-align:left;'>".$pmename[$row['method']]."</td>";
									echo "<td>".number_format_ind($row['amount'])."</td>";
									if($dflag == 1){
										echo "<td>".number_format_ind($row['ccoins'])."</td>";
										echo "<td>".number_format_ind($row['c10'])."</td>";
										echo "<td>".number_format_ind($row['c20'])."</td>";
										echo "<td>".number_format_ind($row['c50'])."</td>";
										echo "<td>".number_format_ind($row['c100'])."</td>";
										echo "<td>".number_format_ind($row['c200'])."</td>";
										echo "<td>".number_format_ind($row['c500'])."</td>";
										echo "<td>".number_format_ind($row['c2000'])."</td>";
										$tccoins = $tccoins + $row['ccoins'];
										$tc10 = $tc10 + $row['c10'];
										$tc20 =  $tc20 + $row['c20'];
										$tc50 =  $tc50 + $row['c50'];
										$tc100 =  $tc100 + $row['c100'];
										$tc200 =  $tc200 + $row['c200'];
										$tc500 =  $tc500 + $row['c500'];
										$tc2000 =  $tc2000 + $row['c2000'];
									}
									echo "<td style='text-align:left;'>".$row['remarks']."</td>";
									echo "<td style='text-align:left;'>".$sector_name[$row['warehouse']]."</td>";
									echo "<td style='text-align:center;'>".$user_name[$row['addedemp']]."</td>";
									echo "</tr>";
									$ttotalamt = $ttotalamt + $row['amount'];
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="6" align="center"><b>Grand Total</b></td>
									<td><?php echo number_format_ind($ttotalamt); ?></td>
									<?php
									if($dflag == 1){
									?>
									<td><?php echo number_format_ind($tccoins); ?></td>
									<td><?php echo number_format_ind($tc10); ?></td>
									<td><?php echo number_format_ind($tc20); ?></td>
									<td><?php echo number_format_ind($tc50); ?></td>
									<td><?php echo number_format_ind($tc100); ?></td>
									<td><?php echo number_format_ind($tc200); ?></td>
									<td><?php echo number_format_ind($tc500); ?></td>
									<td><?php echo number_format_ind($tc2000); ?></td>
									<?php
									}
									?>
									<td colspan="3" align="center"></td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<script>
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
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
