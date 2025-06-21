<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
$requested_data = json_decode(file_get_contents('php://input'), true);
session_start();

$db = $_SESSION['db'] = $_GET['db'];
if ($db == '') {
	include "../config.php";
	// include "header_head.php";
	include "number_format_ind.php";
} else {
	include "APIconfig.php";
	include "number_format_ind.php";
	// include "header_head.php";
}

$today = date("Y-m-d");
$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$ifwt = $row['wt'];
	$ifbw = $row['bw'];
	$ifjbw = $row['jbw'];
	$ifjbwen = $row['jbwen'];
	$ifctype = $row['ctype'];
}
$idisplay = '';
$ndisplay = 'style="display:none;"';

if (isset($_POST['submit']) == true) {
	if ($_POST['ctype'] == "on" || $_POST['ctype'] == true) {
		$con_type = " AND`contacttype` LIKE '%S%'";
		$con_code = "S&C";
	} else {
		$con_type = " AND`contacttype` LIKE 'S'";
		$con_code = "S";
	}
} else {
	$con_type = " AND`contacttype` LIKE '%S%'";
	$con_code = "S&C";
}
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'" . $con_type . " ORDER BY `name` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$pcode[$row['code']] = $row['code'];
	$pname[$row['code']] = $row['name'];
	$obdate[$row['code']] = $row['obdate'];
	$obtype[$row['code']] = $row['obtype'];
	$obamt[$row['code']] = $row['obamt'];
	$sup_name[$row['code']] = $row['name'];
	$sup_code[$row['code']] = $row['code'];
	$sup_type[$row['code']] = $row['contacttype'];
}

// Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$itemname[$row['code']] = $row['description'];
}
$fromdate = $_POST['fromdate'];
$todate = $_POST['todate'];
if ($fromdate == "") {
	$fromdate = $todate = $today;
} else {
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
}
$cname = $_POST['cname'];
$iname = $_POST['iname'];
if ($cname == "all" || $cname == "select") {
	$cnames = "";
} else {
	$cnames = " AND `customercode` = '$cname'";
}
?>
<?php $expoption = "displaypage";
if (isset($_POST['submit'])) {
	$expoption = $_POST['export'];
}
if ($expoption == "displaypage") {
	$exoption = "displaypage";
} else {
	$exoption = $expoption;
}; ?>
<link rel="stylesheet" type="text/css" href="reportstyle.css">
<?php
$url = "../PHPExcel/Examples/SupplierLedgerReportAll-Excel.php?fromdate=".$fromdate."&todate=".$todate."&cname=".$cname."&iname=".$iname."&ctype=".$_POST['ctype'];
?>
<html>

<head>
	<title>Supplier Ledger</title>
     <?php include "header_head.php"; ?>
	<script>
		var exptype = '<?php echo $exoption; ?>';
		var url = '<?php echo $url; ?>';
		if(exptype.match("exportexcel")){
			window.open(url,'_BLANK');
		}
	</script>
	<style>
		.thead2,
		.tbody1 {
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
	<?php if ($exoption == "displaypage" || $exoption == "printerfriendly" || $exoption == "exportexcel") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
					<?php
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' ORDER BY `id` DESC";
					$query = mysqli_query($conn, $sql);
					while ($row = mysqli_fetch_assoc($query)) { ?>
						<td><img src="../<?php echo $row['logopath']; ?>" height="150px" /></td>
						<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Supplier Ledger</h3>
						<?php
						if ($cname == "all" || $cname == "select" || $cname == "") {
						} else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Supplier:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br />
						<?php
						}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y", strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y", strtotime($todate)); ?></label>
					</td>
					<td>

					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
	<section class="content" align="center">
		<div class="col-md-12" align="center">
			<?php if ($db == '') { ?>
				<form action="SupplierLedgerReportAll.php" method="post" onsubmit="return checkval()">
				<?php } else { ?>
					<form action="SupplierLedgerReportAll.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
					<?php } ?>
					<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if ($exoption == "displaypage" || $exoption == "exportexcel") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="16">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y", strtotime($fromdate)); ?>" />
										&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y", strtotime($todate)); ?>" />
										&ensp;&ensp;
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2">
											<option value="select">-select-</option>
											<option value="all" selected>-All-</option>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Customer &amp; Supplier</label>&nbsp;
										<input type="checkbox" name="ctype" id="ctype" class="formcontrol" <?php if ($con_code == "S&C") {
																												echo "checked";
																											} ?> />
										&ensp;&ensp;
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if ($exoption == "displaypage") {
														echo 'selected';
													} ?> value="displaypage">Display</option>
											<option <?php if ($exoption == "exportexcel") {
														echo 'selected';
													} ?> value="exportexcel">Excel</option>
											<option <?php if ($exoption == "printerfriendly") {
														echo 'selected';
													} ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
						<thead class="thead2" style="background-color: #98fb98;">
							<tr>
								<th rowspan="1"></th>
								<th rowspan="2">Name</th>
								<th rowspan="2">Opening Balance</th>
								<th colspan="3">Selected Period</th>
								<th rowspan="2">Balance</th>
							</tr>
							<tr>
								<th>Sl No.</th>
								<th>Purchase Qty</th>
								<th>Purchase</th>
								<th>Payments</th>
								<!--<th>B/w days balance</th>-->
							</tr>
						</thead>
						<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
							if (isset($_POST['submit']) == true) {
								if ($cname == "" || $cname == "all" || $cname == "select") {
									$fromdate = $_POST['fromdate'];
									$todate = $_POST['todate'];
									if ($fromdate == "") {
										$fromdate = $todate = $today;
									} else {
										$fromdate = date("Y-m-d", strtotime($_POST['fromdate']));
										$todate = date("Y-m-d", strtotime($_POST['todate']));
									}
									$cname = $_POST['cname'];
									$iname = $_POST['iname'];
									if ($cname == "all" || $cname == "select") {
										$cnames = "";
									} else {
										$cnames = " AND `vendorcode` = '$cname'";
									}

									//Supplier invoice
									$ob_purchases = array();
									$sql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fromdate' AND `active` = '1' ORDER BY `date`,`invoice`,`vendorcode` ASC";
									$query = mysqli_query($conn, $sql);
									$old_inv = "";
									while ($row = mysqli_fetch_assoc($query)) {
										if ($old_inv != $row['invoice']) {
											$ob_purchases[$row['vendorcode']] = $ob_purchases[$row['vendorcode']] + $row['finaltotal'];
											$old_inv = $row['invoice'];
										}
									}
									//Supplier Receipt
									$ob_payments = array();
									$seq = "SELECT SUM(amount) as amount,ccode FROM `pur_payments` WHERE `date` < '$fromdate'";
									$active = " AND `active` = '1'";
									$orderby = " ORDER BY `ccode` ASC";
									$groupby = " GROUP BY `ccode`";
									$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
									$query = mysqli_query($conn, $sql);
									while ($row = mysqli_fetch_assoc($query)) {
										$ob_payments[$row['ccode']] = $row['amount'];
									}

									//Supplier Returns
									$ob_returns = array();
									$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fromdate' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn, $obsql);
									while ($obrow = mysqli_fetch_assoc($obquery)) {
										$ob_returns[$obrow['vcode']] += (float)$obrow['amount'];
									}

									//Supplier Mortality
									$ob_smortality = array();
									$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fromdate' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn, $obsql);
									while ($obrow = mysqli_fetch_assoc($obquery)) {
										$ob_smortality[$obrow['ccode']] += (float)$obrow['amount'];
									}

									//Supplier CrDr Note
									$ob_scn = $ob_sdn = array();
									$seq = "SELECT SUM(amount) as amount,mode,ccode FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `mode` IN ('SCN','SDN')";
									$active = " AND `active` = '1'";
									$orderby = " ORDER BY `ccode` ASC";
									$groupby = " GROUP BY `ccode`,`mode`";
									$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
									$query = mysqli_query($conn, $sql);
									while ($row = mysqli_fetch_assoc($query)) {
										if ($row['mode'] == "SCN") {
											$ob_scn[$row['ccode']] = $row['amount'];
										} else {
											$ob_sdn[$row['ccode']] = $row['amount'];
										}
									}


									//Supplier invoice
									$bt_purchases = $bt_purchases_qty = array();
									$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1' ORDER BY `date`,`invoice`,`vendorcode` ASC";
									$query = mysqli_query($conn, $sql);
									$old_inv = "";
									while ($row = mysqli_fetch_assoc($query)) {
										if ($old_inv != $row['invoice']) {
											$bt_purchases[$row['vendorcode']] = $bt_purchases[$row['vendorcode']] + $row['finaltotal'];
											$old_inv = $row['invoice'];
										}
										$bt_purchases_qty[$row['vendorcode']] = $bt_purchases_qty[$row['vendorcode']] + $row['netweight'];
									}
									//Supplier Receipt
									$bt_payments = array();
									$seq = "SELECT SUM(amount) as amount,ccode FROM `pur_payments` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$active = " AND `active` = '1'";
									$orderby = " ORDER BY `ccode` ASC";
									$groupby = " GROUP BY `ccode`";
									$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
									$query = mysqli_query($conn, $sql);
									while ($row = mysqli_fetch_assoc($query)) {
										$bt_payments[$row['ccode']] = $row['amount'];
									}
									//Supplier Returns
									$bt_returns = array();
									$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn, $obsql);
									while ($obrow = mysqli_fetch_assoc($obquery)) {
										$bt_returns[$obrow['vcode']] += (float)$obrow['amount'];
									}

									//Supplier Mortality
									$bt_smortality = array();
									$obsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
									$obquery = mysqli_query($conn, $obsql);
									while ($obrow = mysqli_fetch_assoc($obquery)) {
										$bt_smortality[$obrow['ccode']] += (float)$obrow['amount'];
									}

									//Supplier CrDr Note
									$bt_scn = $bt_sdn = array();
									$seq = "SELECT SUM(amount) as amount,mode,ccode FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` IN ('SCN','SDN')";
									$active = " AND `active` = '1'";
									$orderby = " ORDER BY `ccode` ASC";
									$groupby = " GROUP BY `ccode`,`mode`";
									$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
									$query = mysqli_query($conn, $sql);
									while ($row = mysqli_fetch_assoc($query)) {
										if ($row['mode'] == "SCN") {
											$bt_scn[$row['ccode']] = $row['amount'];
										} else {
											$bt_sdn[$row['ccode']] = $row['amount'];
										}
									}
									$ftotal = $ft_ob =  $ft_sq =  $ft_sa =  $ft_rt =  $ft_bb = 0; $sl = 1;
									foreach ($pcode as $pcodes) {

										
										$ob_cramt = $ob_dramt = $ob_dr = $ob_cr = $ob_fcr = $ob_fdr = $bt_dr = $bt_cr = $bt_fcr = $bt_fdr = $balance = 0;
										if ($obtype[$pcodes] == "Cr") {
											$ob_dramt = $obamt[$pcodes];
										} else {
											$ob_cramt = $obamt[$pcodes];
										}
										$ft_ob = $ft_ob + (((float)$ob_purchases[$pcodes] + (float)$ob_scn[$pcodes] + (float)$ob_dramt) - ((float)$ob_payments[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_sdn[$pcodes] + (float)$ob_cramt));
										$ft_sq = $ft_sq + (float)$bt_purchases_qty[$pcodes];
										$ft_sa = $ft_sa + ((float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes]);
										$ft_rt = $ft_rt + ((float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes]);
										$ft_bb = $ft_bb + (((float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes]) - ((float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes]));

										$opn_bal = ((float)$ob_purchases[$pcodes] + (float)$ob_scn[$pcodes] + (float)$ob_dramt) - ((float)$ob_payments[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_sdn[$pcodes] + (float)$ob_cramt);
										$pur_chqty = $bt_purchases_qty[$pcodes];
										$pur_cha = (float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes];
										$pay_ment = (float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes];

										
										//echo "<td>".number_format_ind(($bt_purchases[$pcodes] + $bt_scn[$pcodes]) - ($bt_payments[$pcodes] + $bt_sdn[$pcodes]))."</td>";
										$ob_dr = (float)$ob_purchases[$pcodes] + (float)$ob_scn[$pcodes] + (float)$ob_dramt;
										$ob_cr = (float)$ob_payments[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_sdn[$pcodes] + (float)$ob_cramt;
										if ($ob_cr > $ob_dr) {
											$ob_fcr = (float)$ob_cr - (float)$ob_dr;
										} else {
											$ob_fdr = (float)$ob_dr - (float)$ob_cr;
										}
										$bt_dr = (float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes];
										$bt_cr = (float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes];
										if ($bt_cr > $bt_dr) {
											$bt_fcr = (float)$bt_cr - (float)$bt_dr;
										} else {
											$bt_fdr = (float)$bt_dr - (float)$bt_cr;
										}
										$balance = round(((float)$ob_fdr + (float)$bt_fdr) - ((float)$ob_fcr + (float)$bt_fcr),6);
										//echo "<br/>".$ob_fdr."+".$bt_fdr."-".$ob_fcr."+".$bt_fcr;
										$ftotal = (float)$ftotal + (float)$balance;

										if((float)$balance != 0 || (float)$pur_cha != 0 && (float)$balance != 0 || (float)$pay_ment != 0 && (float)$balance != 0){

										echo "<tr>";
										echo "<td style='text-align:left;'>" . $sl++ . "</td>";
										echo "<td style='text-align:left;'>" . $pname[$pcodes] . "</td>";
										echo "<td>" . number_format_ind($opn_bal) . "</td>";
										echo "<td>" . $pur_chqty . "</td>";
										echo "<td>" . number_format_ind($pur_cha) . "</td>";
										echo "<td>" . number_format_ind($pay_ment) . "</td>";
										echo "<td>" . number_format_ind($balance) . "</td>";
										echo "</tr>";
										}
									}
								} else {
								}
							}
							?>
						</tbody>
						<thead>
							<tr class="foottr" style="background-color: #98fb98;">
								<td align="center" colspan="2"><b>Total</b></td>
								<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_ob); ?></td>
								<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_sq); ?></td>
								<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_sa); ?></td>
								<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_rt); ?></td>
								<!--<td><?php //echo number_format_ind($ft_bb); 
										?></td>-->
								<td style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ftotal); ?></td>
							</tr>
						</thead>
					</table>
					</form>
		</div>
	</section>
	<script type="text/javascript" lahguage="javascript">
		function checkval() {
			var a = document.getElementById("checkcname").value;
			if (a.match("select") || a.match("-select-")) {
				alert("Please select customer ..!");
				return false;
			} else {
				return true;
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
							shouldSwitch = true;
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
							shouldSwitch = true;
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
					switchcount++;

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
	</script>
	<?php if ($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime();
																															$time = explode(' ', $time);
																															$time = $time[1] + $time[0];
																															$finish = $time;
																															$total_time = round(($finish - $start), 4);
																															echo "Loaded in " . $total_time . " seconds."; ?></footer> <?php } ?>
	<script src="../loading_page_out.js"></script>
	<?php //if($cname == ""){ } else { echo "<script> sortTable(0); </script>"; } 
	?>
</body>

</html>
<?php include "header_foot.php"; ?>