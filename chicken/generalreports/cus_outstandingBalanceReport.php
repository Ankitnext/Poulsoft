<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cpname[$row['code']] = $row['name']; $cpcode[$row['code']] = $row['code']; }
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_name[$row['code']] = $row['description']; $grp_code[$row['code']] = $row['code']; }
	
	$todate = $today;
	if($_POST['dates'] != ""){ $todate = $_POST['dates']; } else { $todate = $today; }
	$grp_codes = "all";
	if($_POST['gcodes'] != ""){ $grp_codes = $_POST['gcodes']; } else { $grp_codes = "all"; }
	
	$expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; }
	if($expoption == "displaypage") { $exoption = "displaypage"; }
	else { $exoption = $expoption; };

	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $sales_sms_flag = $row['outstand_sms']; $sales_wapp_flag = $row['outstand_wapp']; } } else { $sales_sms_flag = $sales_wapp_flag = '0'; }
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=OutstandingBalanceReport.xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<?php if($exoption != "printerfriendly"){ echo "<style> .thead2 th { top: 0; position: sticky; background-color: #98fb98; } </style>"; } ?>
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
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Customer Outstanding Balance Report</h3>
						<label class="reportheaderlabel"><b style="color: green;">Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">Group:</b>&nbsp;<?php echo $grp_name[$grp_codes]; ?></label><br/>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
						<form action="cus_outstandingBalanceReport.php" method="post" onsubmit="return checkval()">
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="16">
										<label class="reportselectionlabel">Date</label>&nbsp;
										<input type="text" name="dates" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="gcodes" id="gcodes" class="form-control select2">
											<option value="all" <?php if($grp_codes == "all") { echo 'selected'; } ?> >-All-</option>
											<?php
												foreach($grp_code as $gcode){
											?>
													<option <?php if($grp_codes == $grp_code[$gcode]) { echo 'selected'; } ?> value="<?php echo $grp_code[$gcode]; ?>"><?php echo $grp_name[$gcode]; ?></option>
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
						</form>
						<?php }
							if(isset($_POST['submit']) == true){
						?>
							<form action="cus_sendsms.php" method="post" onsubmit="return checkval()">
								<thead class="thead2" style="background-color: #98fb98;">
									<tr>
										<th>Sl.No.</th>
										<th>Selection</th>
										<th>Customer Name</th>
										<th>Customer Mobile</th>
										<th>OutStanding</th>
									</tr>
								</thead>
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
								<?php if($exoption != "printerfriendly"){ ?>
									<tr>
										<td colspan="3" style="text-align:center;"><input type="checkbox" name="checkall" id="checkall" onchange="checkedall()" /><label class="reportselectionlabel">All Customers</label></td>
										<td colspan="2" style="text-align:left;">
											<select name="send_type" id="send_type" class="form-control select2">
												<?php if($sales_sms_flag == 1){ ?><option value="sms" selected >SMS</option><?php } ?>
												<?php if($sales_wapp_flag == 1){ ?><option value="whatsapp" <?php if($sales_sms_flag == 0 && $sales_wapp_flag == 1){ echo "selected"; } ?>>WhatsApp</option><?php } ?>
												<?php if($sales_sms_flag == 1 && $sales_wapp_flag == 1){ ?><option value="both">Both</option><?php } ?>
											</select>
										</td>
									</tr>
								<?php } ?>
								<?php
									$today = date("Y-m-d",strtotime($todate));
									$grpcode = "";
									foreach($grp_code as $gc){
										if($grpcode == ""){
											$grpcode = $grp_code[$gc];
										}
										else{
											$grpcode = $grpcode."','".$grp_code[$gc];
										}
									}
									if($grp_codes == "all"){ $cus_grp = " AND `groupcode` IN ('$grpcode')"; } else { $cus_grp = " AND `groupcode` IN ('$grp_codes')"; }
									$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%c%' AND `active` = '1'".$cus_grp." ORDER BY `name` ASC";
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$ccodes[$row['code']] = $row['code'];
										$cname[$row['code']] = $row['name'];
										$cnameno[$row['code']] = $row['mobileno'];
										if($row['obtype'] == "Cr"){ $obcramt[$row['code']] = $row['obamt']; $obdramt[$row['code']] = "0.00"; }
										else if($row['obtype'] == "Dr"){ $obdramt[$row['code']] = $row['obamt']; $obcram[$row['code']] = "0.00"; }
										else{ $obdramt[$row['code']] = $obcramt[$row['code']] = "0.00"; }
									}
									$sql = "SELECT SUM(DISTINCT(birds)) as birds,SUM(DISTINCT(netweight)) as netweight,SUM(DISTINCT(totalamt)) as totalamt,SUM(DISTINCT(tcdsamt)) as tcdsamt, SUM(DISTINCT(discountamt)) as discountamt,SUM(DISTINCT(taxamount)) as taxamount,customercode FROM `customer_sales` WHERE `date` <= '$today' AND `active` = '1' GROUP BY `customercode`,`invoice` ORDER BY `customercode` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$birds[$row['customercode']] = $birds[$row['customercode']] + $row['birds']; $netweight[$row['customercode']] = $netweight[$row['customercode']] + $row['netweight']; $totalamt[$row['customercode']] = $totalamt[$row['customercode']] + $row['totalamt']; $tcdsamt[$row['customercode']] = $tcdsamt[$row['customercode']] + $row['tcdsamt']; $discountamt[$row['customercode']] = $discountamt[$row['customercode']] + $row['discountamt']; $taxamount[$row['customercode']] = $taxamount[$row['customercode']] + $row['taxamount'];
									}

									$sql = "SELECT SUM(amount) as amount,ccode FROM `customer_receipts` WHERE `date` <= '$today' AND `active` = 1 GROUP BY `ccode` ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $rct_amt[$row['ccode']] = $row['amount']; }

									$sql = "SELECT SUM(amount) as amount,`mode`,ccode FROM `main_crdrnote` WHERE `mode` IN ('CCN','CDN') AND `date` <= '$today' AND `active` = 1 GROUP BY `mode`,`ccode` ORDER BY `mode`,`ccode` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CCN"){ $ccn_amt[$row['ccode']] = $row['amount']; } else if($row['mode'] == "CDN"){ $cdn_amt[$row['ccode']] = $row['amount']; } else{ }
									}
									$total_tsb = $c = 0;
									foreach($ccodes as $c_code){
										$c = $c + 1;
										$sales = $receipts = $ftotal = 0;
										//echo "<br/>".$totalamt[$c_code]."-".$tcdsamt[$c_code]."-".$taxamount[$c_code]."-".$cdn_amt[$c_code]."-".$obdramt[$c_code];
										$sales = $totalamt[$c_code] + $tcdsamt[$c_code] + $taxamount[$c_code] + $cdn_amt[$c_code] + $obdramt[$c_code];
										//echo "-->".$rct_amt[$c_code]."-".$discountamt[$c_code]."-".$ccn_amt[$c_code]."-".$obcramt[$c_code];
										$receipts = $rct_amt[$c_code] + $discountamt[$c_code] + $ccn_amt[$c_code] + $obcramt[$c_code];

										$ftotal = $sales - $receipts;
										$total_tsb = $total_tsb + $ftotal;
										echo "<tr>";
										$cus_details = $cname[$c_code]."@".$cnameno[$c_code]."@".$ftotal."@".$today."@".$c_code;
										echo "<td>".$c."</td>";
										echo "<td><input type='checkbox' name='c_det[]' id='c_det[]' value='$cus_details' /></td>";
										echo "<td style='width: auto;text-align:left;'>$cname[$c_code]</td>";
										if(strlen($cnameno[$c_code]) >= 10){
											echo "<td><input type='text' name='cmob' id='cmob' class='form-control' style='background:inherit;border:none;' value='$cnameno[$c_code]' readonly /></td>";
										}
										else{
											echo "<td><input type='text' name='cmob' id='cmob' class='form-control' style='background:inherit;border:none;color:red;' value='$cnameno[$c_code]' readonly /></td>";
										}
										
										echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($ftotal)."</td>";
										echo "</tr>";
									}
								?>
									<thead class="thead2" style="background-color: #98fb98;">
										<tr>
											<th colspan="4" style="padding:5px;">Final Outstanding Balance</th>
											<th style='padding-right:5px;text-align:right;'><?php echo number_format_ind($total_tsb); ?></th>
										</tr>
									</thead>
									<?php if($exoption != "printerfriendly"){ ?>
									<tr align="center">
										<th colspan="5" style="padding:10px;text-align:center;"><button type="submit" name="sendsms" id="sendsms" class="btn btn-success btn-md" value="sendsuccess">Send SMS</button></th>
									</tr>
									<?php } ?>
								</tbody>
							</form>
						<?php
						}
							?>
							
					</table>
				</div>
		</section>
		<script>
			function checkedall(){
				var a = document.getElementById("checkall");
				if(a.checked == true){
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 0;c <=b.length;c++){
						b[c].checked = true;
					}
				}
				else{
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 0;c <=b.length;c++){
						b[c].checked = false;
					}
				}
			}
			
			function sortTable(n) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = document.getElementById("myTable");
			  switching = true;
			  //Set the sorting direction to ascending:
			  dir = "asc"; 
			  /*Make a loop that will continue until
			  no switching has been done:*/
			  while (switching) {
				//start by saying: no switching is done:
				switching = false;
				rows = table.rows;
				/*Loop through all table rows (except the
				first, which contains table headers):*/
				for (i = 1; i < (rows.length - 1); i++) {
				  //start by saying there should be no switching:
				  shouldSwitch = false;
				  /*Get the two elements you want to compare,
				  one from current row and one from the next:*/
				  x = rows[i].getElementsByTagName("TD")[n];
				  y = rows[i + 1].getElementsByTagName("TD")[n];
				  /*check if the two rows should switch place,
				  based on the direction, asc or desc:*/
				  if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  } else if (dir == "desc") {
					/*if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch = true;
					  break;
					}*/
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  }
				}
				if (shouldSwitch) {
				  /*If a switch has been marked, make the switch
				  and mark that a switch has been done:*/
				  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				  switching = true;
				  //Each time a switch is done, increase this count by 1:
				  switchcount ++;
				  
				} else {
				  /*If no switching has been done AND the direction is "asc",
				  set the direction to "desc" and run the while loop again.*/
				  if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				  }
				}
			  }
			}
			//sortTable(2);
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
