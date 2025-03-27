<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else{ $db = ''; }
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php"; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
	 $today = date("Y-m-d");

	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemcodes[$row['code']] = $row['code']; $itemnames[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $coacodes[$row['code']] = $row['code']; $coanames[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $vcodes[$row['code']] = $row['code']; $vnames[$row['code']] = $row['name']; }
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $officecode[$row['code']] = $row['code']; $officename[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate']; if($fromdate == ""){ $fromdate = $today; } else { $fromdate = $_POST['fromdate']; }
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $ifwlmb = $row['wlmb']; }
	if($ifjbwen == 1){ $fch = 20; $sch = 20 / 2; $dc = $sc = $ic = $jc = $bc = $tc = $ec = $nc = $rc = $ac = 1; }
	else if($ifjbw == 1){ $fch = 20; $sch = 20 / 2; $tc = $ec = 0; $dc = $jc = $bc = $nc = $rc = $ac = 1; $sc = $ic = 2; }
	else if($ifbw == 1){ $fch = 20; $sch = 20 / 2; $tc = $ec = $jc = 0; $dc = $bc = $nc = $rc = $ac = 1; $ic = 2; $sc = 3; }
	else if($ifwt == 1){ $fch = 20; $sch = 20 / 2; $tc = $ec = $jc = $bc = 0; $dc = $nc = $rc = 1; $ac = $ic = 2; $sc = 3; }
	else { $fch = 20; $sch = 20 / 2; $tc = $ec = $jc = $bc = 0; $dc = $nc = $rc = 1; $ac = $ic = 2; $sc = 3; }
	if(isset($_POST['submit']) == true) { $wname = $_POST['wname']; } else { $wname = "select"; } if($wname == "select") { $wnames = $wfnames = $wtnames = "all"; } else if($wname == "all") { $wnames = $wfnames = $wtnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; $wfnames = " AND `fromwarehouse` LIKE '$wname'"; $wtnames = " AND `towarehouse` LIKE '$wname'"; }
	if(isset($_POST['submit']) == true) { $idetail = $_POST['iname']; } else { $idetail == "all"; } if($idetail == "all") { $idetails = ""; } else if($idetail == "") { $idetails = ""; } else { $idetails = " AND `itemcode` = '$idetail'"; $iftdetails = " AND `code` LIKE '$idetail'"; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=itm_weightlossreport($fromdate-$todate).xls");
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
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
						$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){ ?>
						<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
						<td><?php echo $row['cdetails']; ?></td> <?php } }?>
						<td align="center">
							<h3>Weight Loss Report</h3>
							<label class="reportheaderlabel"><b style="color: green;">	Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>
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
				<form action="itm_weightlossreport.php" method="post">
					<?php } else { ?>
					<form action="itm_weightlossreport.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="contentmenu">
							<?php if($exoption == "displaypage") { ?>
							<thead style="padding:15px; text-align:left;background-color: #98fb98;">
								<tr>
									<td colspan='<?php echo $fch; ?>'>&ensp;
										<label class="reportselectionlabel">Date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>&ensp;&ensp;
										<?php if($ifwlmb == 1){ ?>		
											<label class="reportselectionlabel">Item Description</label>&nbsp;
											<select name="iname" id="iname" class="form-control select2">
												<option value="all">-All-</option>
												<?php
													$icats = $icode = ""; $c = 0; if($ifwlmb == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
													$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);
													while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = $row['code']; } else { $icats = $icats."','".$row['code']; } }
													//echo $icats;
													$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icats')"; $query = mysqli_query($conn,$sql);
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
									<td colspan='<?php echo $fch; ?>' style='text-align:center;font-weight:bold;font-size:18px;color:red;background-color: #98fb98;'>
										Weight Loss Report for <?php echo $officename[$wname]; ?> on <?php echo $fromdate; ?>
									</td>
								</tr>
							</thead>
							<?php } ?>
							<thead>
								<tr>
									<td colspan='<?php echo $sch; ?>' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Purchases/Transfer IN</td>
									<td colspan='<?php echo $sch; ?>' style='text-align:center;font-weight:bold;background-color: #98fb98;'>Sales/Transfer OUT</td>
								</tr>
							</thead>
							<thead>
								<tr style='font-weight:bold;background-color: #98fb98;'>
									<td colspan='<?php echo $dc; ?>'>Date</td>
									<td colspan='<?php echo $sc; ?>'>Supplier</td>
									<td colspan='<?php echo $ic; ?>'>Item</td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $jc; ?>'>Jals</td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $bc; ?>'>Birds</td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $tc; ?>' title='Total Weight'>T.Weight</td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $ec; ?>' title='Empty Weight'>E.Weight</td>
									<td colspan='<?php echo $nc; ?>' title='Net Weight'>N.Weight</td>
									<td colspan='<?php echo $rc; ?>' title='Item Price'>Price</td>
									<td colspan='<?php echo $ac; ?>' title='Total Amount'>Amount</td>
									
									<td colspan='<?php echo $dc; ?>'>Date</td>
									<td colspan='<?php echo $sc; ?>'>Customer</td>
									<td colspan='<?php echo $ic; ?>'>Item</td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $jc; ?>'>Jals</td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $bc; ?>'>Birds</td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $tc; ?>' title='Total Weight'>T.Weight</td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> colspan='<?php echo $ec; ?>' title='Empty Weight'>E.Weight</td>
									<td colspan='<?php echo $nc; ?>' title='Net Weight'>N.Weight</td>
									<td colspan='<?php echo $rc; ?>' title='Item Price'>Price</td>
									<td colspan='<?php echo $ac; ?>' title='Total Amount'>Amount</td>
								</tr>
							</thead>
							<tbody>
								<?php
									if(isset($_POST['submit']) == true){
										$icats = $icode = ""; $c = 0; if($ifwlmb == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
										$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = $row['code']; } else { $icats = $icats."','".$row['code']; } }

										$sql = "SELECT * FROM `item_category` WHERE `description` LIKE 'Milk'"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = $row['code']; } else { $icats = $icats."','".$row['code']; } }
										//echo $icats;
										$seq = "SELECT * FROM `item_details` WHERE `category` IN ('$icats')"; $sql = $seq."".$iftdetails; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){ if($icode == ""){ $icode = $row['code']; } else { $icode = $icode."','".$row['code']; } }
										//echo $icode;
										
										$fromdate = date("Y-m-d",strtotime($fromdate)); $addeditemcode = array();
										$d = date("d",strtotime($fromdate)); $m = date("m",strtotime($fromdate)); $y = date("Y",strtotime($fromdate)); $d = $d - 1;
										//$pdate = $y."-".$m."-".$d;
										$tot_sin_qty = $tot_sin_amt = $tot_sout_qty = $tot_sout_amt = 0;
										$pdate = date('Y-m-d', strtotime($fromdate. ' - 1 days'));
										$seq = "SELECT code,closedquantity,closedbirds,price FROM `item_closingstock` WHERE `date` LIKE '$pdate' AND `code` IN ('$icode')"; 
										$grpstcode = " GROUP BY `code` ORDER BY `code` ASC";
										$sql = $seq."".$wnames."".$grpstcode; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											//$ciqty[$row['code']] = $row['closedquantity']; $cicode[$row['code']] = $row['code']; $cinos[$row['code']] = $row['closedbirds'];
											echo "<tr>";
												echo "<td colspan='$dc'>".date("d.m.Y",strtotime($pdate))."</td>";
												echo "<td title='Previous Balance' colspan='$sc'>P. Balance</td>";
												echo "<td colspan='$ic'>".$itemnames[$row['code']]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'></td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".number_format_ind($row['closedbirds'])."</td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
												echo "<td colspan='$nc' id='ac'>".number_format_ind($row['closedquantity'])."</td>";
												echo "<td colspan='$rc' id='ac'>".number_format_ind($row['price'])."</td>";
												echo "<td colspan='$ac' id='ac'>".number_format_ind($row['closedquantity'] * $row['price'])."</td>";
												echo "<td colspan='10'></td>";
											echo "</tr>";
											$addeditemcode[$row['code']] = $row['code'];
											$addeditembds[$row['code']] = $row['closedbirds'];
											$addeditemqty[$row['code']] = $row['closedquantity'];
											$addeditemamt[$row['code']] = $row['closedquantity'] * $row['price'];
											$tot_sin_qty += (float)$row['closedquantity'];
											$tot_sin_amt += ((float)$row['closedquantity'] * (float)$row['price']);
										}
										$grpcode = " ORDER BY `itemcode` ASC";
										$seq = "SELECT * FROM `pur_purchase` WHERE `date` = '$fromdate' AND `itemcode` IN ('$icode') AND `active` = '1'"; $sql = $seq."".$wnames."".$grpcode;
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											//$pqty[$row1['itemcode']] = $row1['netweight']; $picode[$row['itemcode']] = $row['itemcode']; $pinos[$row['itemcode']] = $row['birds'];
											echo "<tr>";
												echo "<td colspan='$dc'>".date("d.m.Y",strtotime($fromdate))."</td>";
												echo "<td colspan='$sc'>".$vnames[$row['vendorcode']]."</td>";
												echo "<td colspan='$ic'>".$itemnames[$row['itemcode']]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'>".str_replace(".00","",number_format_ind($row['jals']))."</td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'>".number_format_ind($row['totalweight'])."</td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'>".number_format_ind($row['emptyweight'])."</td>"; }
												echo "<td colspan='$nc' id='ac'>".number_format_ind($row['netweight'])."</td>";
												echo "<td colspan='$rc' id='ac'>".number_format_ind($row['itemprice'])."</td>";
												echo "<td colspan='$ac' id='ac'>".number_format_ind($row['totalamt'])."</td>";
												echo "<td colspan='10'></td>";
											echo "</tr>";
											
											$prc[$row['itemcode']] =  $row['itemprice'];
											$addeditemcode[$row['itemcode']] = $row['itemcode'];
											$addeditemjal[$row['itemcode']] = $addeditemjal[$row['itemcode']] + $row['jals'];
											$addeditembds[$row['itemcode']] = $addeditembds[$row['itemcode']] + $row['birds'];
											$addeditemqty[$row['itemcode']] = $addeditemqty[$row['itemcode']] + $row['netweight'];
											$addeditemamt[$row['itemcode']] = $addeditemamt[$row['itemcode']] + $row['totalamt'];
											$tot_sin_qty += $row['netweight'];
											$tot_sin_amt += $row['totalamt'];
											//echo $addeditemamt[$row['itemcode']]."</br>";
										}
										foreach($addeditemcode as $icodes){
											echo "<tr style='font-weight:bold;'>";
												echo "<td colspan='$dc' style='background-color: #98fb98;'><b>Total Qty</b></td>";
												echo "<td colspan='$sc'></td>";
												echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'>".str_replace(".00","",number_format_ind($addeditemjal[$icodes]))."</td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($addeditembds[$icodes]))."</td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
												echo "<td colspan='$nc' id='ac'>".number_format_ind($addeditemqty[$icodes])."</td>";
												if(number_format_ind($addeditemqty[$icodes]) != "0.00"){
													$t1 = round(((float)$addeditemamt[$icodes] / (float)$addeditemqty[$icodes]),2);
												}
												else{
													$t1 = 0;
												}
												echo "<td colspan='$rc' id='ac'>".number_format_ind($t1)."</td>";
												echo "<td colspan='$ac' id='ac'>".number_format_ind($addeditemamt[$icodes])."</td>";
												echo "<td colspan='$sch'></td>";
											echo "</tr>";
										}
										$seq = "SELECT * FROM `item_stocktransfers` WHERE `date` = '$fromdate' AND `code` IN ('$icode') AND `active` = '1'"; $sql = $seq."".$wtnames;
										$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
										while($row = mysqli_fetch_assoc($query)){
											echo "<tr>";
											echo "<td colspan='$dc'>".date("d.m.Y",strtotime($fromdate))."</td>";
											echo "<td colspan='$sc'>".$officename[$row['fromwarehouse']]."</td>";
											echo "<td colspan='$ic'>".$itemnames[$row['code']]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; }
											if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
											if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
											echo "<td colspan='$nc' id='ac'>".number_format_ind($row['quantity'])."</td>";
											echo "<td colspan='$rc' id='ac'>".number_format_ind($row['price'])."</td>";
											echo "<td colspan='$ac' id='ac'>".number_format_ind($row['quantity'] * $row['price'])."</td>";
											echo "<td colspan='10'></td>";
											echo "</tr>";
											$amt = $row['quantity'] * $row['price'];
											$addeditemcode[$row['code']] = $row['code'];
											$addeditembds[$row['code']] = $addeditembds[$row['code']] + $row['birds'];
											$addeditemqty[$row['code']] = $addeditemqty[$row['code']] + $row['quantity'];
											$addeditemamt[$row['code']] = $addeditemamt[$row['code']] + $amt;
											$tot_sin_qty += (float)$row['quantity'];
											$tot_sin_amt += ((float)$row['quantity'] * (float)$row['price']);
										}
										if($ccount > 0){
											foreach($addeditemcode as $icodes){
												echo "<tr style='font-weight:bold;'>";
													echo "<td colspan='$dc' style='background-color: #98fb98;'><b>Total Qty</b></td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'></td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($addeditembds[$icodes]))."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind($addeditemqty[$icodes])."</td>";
													echo "<td colspan='$rc' id='ac'></td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($addeditemamt[$icodes])."</td>";
													echo "<td colspan='$sch'></td>";
												echo "</tr>";
											}
										}
										$grpcode = " ORDER BY `itemcode` ASC";
										$seq = "SELECT * FROM `customer_sales` WHERE `date` = '$fromdate' AND `itemcode` IN ('$icode') AND `active` = '1'"; $sql = $seq."".$wnames;
										$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
										while($row = mysqli_fetch_assoc($query)){
											echo "<tr>";
											echo "<td colspan='$sch'></td>";
											echo "<td colspan='$dc'>".date("d.m.Y",strtotime($fromdate))."</td>";
											echo "<td colspan='$sc'>".$vnames[$row['customercode']]."</td>";
											echo "<td colspan='$ic'>".$itemnames[$row['itemcode']]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'>".str_replace(".00","",number_format_ind($row['jals']))."</td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; }
											if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'>".number_format_ind($row['totalweight'])."</td>"; }
											if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'>".number_format_ind($row['emptyweight'])."</td>"; }
											echo "<td colspan='$nc' id='ac'>".number_format_ind($row['netweight'])."</td>";
											echo "<td colspan='$rc' id='ac'>".number_format_ind($row['itemprice'])."</td>";
											echo "<td colspan='$ac' id='ac'>".number_format_ind($row['totalamt'])."</td>";
											echo "</tr>";
											
											$addeditemcode[$row['itemcode']] = $row['itemcode'];
											$rmditemjal[$row['itemcode']] = $rmditemjal[$row['itemcode']] + $row['jals'];
											$rmditembds[$row['itemcode']] = $rmditembds[$row['itemcode']] + $row['birds'];
											$rmditemqty[$row['itemcode']] = $rmditemqty[$row['itemcode']] + $row['netweight'];
											$rmditemamt[$row['itemcode']] = $rmditemamt[$row['itemcode']] + $row['totalamt'];
											$tot_sout_qty += $row['netweight'];
											$tot_sout_amt += $row['totalamt'];
										}
										if($ccount > 0){
											foreach($addeditemcode as $icodes){
												echo "<tr style='font-weight:bold;'>";
													echo "<td colspan='$sch'></td>";
													echo "<td colspan='$dc' style='background-color: #98fb98;'><b>Total Qty</b></td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'>".str_replace(".00","",number_format_ind($rmditemjal[$icodes]))."</td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($rmditembds[$icodes]))."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind($rmditemqty[$icodes])."</td>";
													if(number_format_ind($rmditemqty[$icodes]) != "0.00"){
														$t1 = round(((float)$rmditemamt[$icodes] / (float)$rmditemqty[$icodes]),2);
													}
													else{
														$t1 = 0;
													}
													echo "<td colspan='$rc' id='ac'>".number_format_ind($t1)."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($rmditemamt[$icodes])."</td>";
												echo "</tr>";
											}
										}
										$seq = "SELECT * FROM `item_stocktransfers` WHERE `date` = '$fromdate' AND `code` IN ('$icode') AND `active` = '1'"; $sql = $seq."".$wfnames;
										$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
										while($row = mysqli_fetch_assoc($query)){
											echo "<tr>";
											echo "<td colspan='$sch'></td>";
											echo "<td colspan='$dc'>".date("d.m.Y",strtotime($fromdate))."</td>";
											echo "<td colspan='$sc'>".$officename[$row['towarehouse']]."</td>";
											echo "<td colspan='$ic'>".$itemnames[$row['code']]."</td>";
											if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'></td>"; }
											if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; }
											if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
											if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
											echo "<td colspan='$nc' id='ac'>".number_format_ind($row['quantity'])."</td>";
											echo "<td colspan='$rc' id='ac'>".number_format_ind($row['price'])."</td>";
											echo "<td colspan='$ac' id='ac'>".number_format_ind($row['quantity'] * $row['price'])."</td>";
											echo "</tr>";
											$amt = $row['quantity'] * $row['price'];
											$addeditemcode[$row['code']] = $row['code'];
											$rmditembds[$row['code']] = $rmditembds[$row['code']] + $row['birds'];
											$rmditemqty[$row['code']] = $rmditemqty[$row['code']] + $row['quantity'];
											$rmditemamt[$row['code']] = $rmditemamt[$row['code']] + $amt;
											$tot_sout_qty += (float)$row['quantity'];
											$tot_sout_amt += ((float)$row['quantity'] * (float)$row['price']);
										}
										if($ccount > 0){
											foreach($addeditemcode as $icodes){
												echo "<tr style='font-weight:bold;'>";
													echo "<td colspan='$sch'></td>";
													echo "<td colspan='$dc' style='background-color: #98fb98;'><b>Total Qty</b></td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'>".str_replace(".00","",number_format_ind($rmditemjal[$icodes]))."</td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($rmditembds[$icodes]))."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind($rmditemqty[$icodes])."</td>";
													if(number_format_ind($rmditemqty[$icodes]) != "0.00"){
														$t1 = round(((float)$rmditemamt[$icodes] / (float)$rmditemqty[$icodes]),2);
													}
													else{
														$t1 = 0;
													}
													echo "<td colspan='$rc' id='ac'>".number_format_ind($t1)."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($rmditemamt[$icodes])."</td>";
												echo "</tr>";
											}
										}
										$colsize = $qsize = $csize = 0;
										$qsize = sizeof($addeditemcode);
										
										if($qsize == 1) {
											if($ifjbwen == 1){ $fch = 20; $sch = 20 / 2; $dc = $sc = $ic = $jc = $bc = $tc = $ec = $nc = $rc = $ac = 1; }
											else if($ifjbw == 1){ $fch = 20; $sch = 20 / 2; $tc = $ec = 0; $dc = $jc = $bc = $nc = $rc = $ac = 1; $sc = $ic = 2; }
											else if($ifbw == 1){ $fch = 20; $sch = 20 / 2; $tc = $ec = $jc = 0; $dc = $bc = $nc = $rc = $ac = 1; $ic = 2; $sc = 3; }
											else if($ifwt == 1){ $fch = 20; $sch = 20 / 2; $tc = $ec = $jc = $bc = 0; $dc = $nc = $rc = 1; $ac = $ic = 2; $sc = 3; }
											else { $fch = 20; $sch = 20 / 2; $tc = $ec = $jc = $bc = 0; $dc = $nc = $rc = 1; $ac = $ic = 2; $sc = 3; }
											echo "<tr style='text-align:center;font-weight:bold; background-color: #9F81F7;'>";
												echo "<td colspan='$fch'>Final Total</td>";
											echo "</tr>";
											foreach($addeditemcode as $icodes){
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Total Purchase/T. IN</b>&ensp;</td>";
													echo "<td colspan='$dc' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc'></td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($addeditembds[$icodes]))."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind($addeditemqty[$icodes])."</td>";
													echo "<td colspan='$rc'></td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($addeditemamt[$icodes])."&ensp;</td>";
												echo "</tr>";
											}
											foreach($addeditemcode as $icodes){
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Total Sold/T. OUT</b>&ensp;</td>";
													echo "<td colspan='$dc' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc'></td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($rmditembds[$icodes]))."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind($rmditemqty[$icodes])."</td>";
													echo "<td colspan='$rc'></td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($rmditemamt[$icodes])."&ensp;</td>";
												echo "</tr>";
											}
											foreach($addeditemcode as $icodes){
												if(number_format_ind($addeditembds[$icodes]) == number_format_ind($rmditembds[$icodes])){
													$closed_birds = 0;
												}
												else{
													$closed_birds = $addeditembds[$icodes] - $rmditembds[$icodes];
												}
												if(number_format_ind($addeditemqty[$icodes]) == number_format_ind($rmditemqty[$icodes])){
													$closed_weight = $closed_price = 0;
												}
												else{
													$closed_weight = $addeditemqty[$icodes] - (float)$rmditemqty[$icodes];
													$closed_price = $prc[$icodes];
												}
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Closing</b>&ensp;</td>";
													echo "<td colspan='dc' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc'></td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".number_format_ind($closed_birds)."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind(((float)$closed_weight))."</td>";
													echo "<td colspan='$rc' id='ac'>".$closed_price."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($closed_price * ((float)$closed_weight))."&ensp;</td>";
												echo "</tr>";
											}
											$seq = "SELECT code,closedquantity,closedbirds,price FROM `item_closingstock` WHERE `date` LIKE '$fromdate' AND `code` IN ('$icode')"; 
											$grpstcode = " GROUP BY `code` ORDER BY `code` ASC";
											$sql = $seq."".$wnames; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
												//$ciqty[$row['code']] = $row['closedquantity']; $cicode[$row['code']] = $row['code']; $cinos[$row['code']] = $row['closedbirds'];
												echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Actual Closing</b>&ensp;</td>";
												echo "<td colspan='$dc' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
												echo "<td colspan='$sc'></td>";
												echo "<td colspan='$ic'>".$itemnames[$row['code']]."</td>";
												if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc'></td>"; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($row['closedbirds']))."</td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
												if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
												echo "<td colspan='$nc' id='ac'>".number_format_ind($row['closedquantity'])."</td>";
												echo "<td colspan='$rc' id='ac'>".number_format_ind($row['price'])."</td>";
												echo "<td colspan='$ac' id='ac'>".number_format_ind($row['price'] * $row['closedquantity'])."&ensp;</td>";
												echo "</tr>";
												$actitemcode[$row['code']] = $row['code'];
												$actitembds[$row['code']] = $row['closedbirds'];
												$actitemprc[$row['code']] = $row['price'];
												$actitemqty[$row['code']] = $row['closedquantity'];
												$actitemamt[$row['code']] = $row['price'] * $row['closedquantity'];
											}
											$tot_wlos_qty = 0;
											foreach($addeditemcode as $icodes){
												if(number_format_ind($addeditembds[$icodes]) == number_format_ind($rmditembds[$icodes])){
													$closed_birds = 0;
												}
												else{
													$closed_birds = $addeditembds[$icodes] - $rmditembds[$icodes];
												}
												if(number_format_ind($addeditemqty[$icodes]) == number_format_ind($rmditemqty[$icodes])){
													$closed_weight = $closed_price = 0;
												}
												else{
													$closed_weight = $addeditemqty[$icodes] - $rmditemqty[$icodes];
													$closed_price = $prc[$icodes];
												}
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Weight Loss</b>&ensp;</td>";
													echo "<td colspan='$dc'></td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'></td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'>".str_replace(".00","",number_format_ind($actitembds[$icodes] - ($closed_birds)))."</td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													echo "<td colspan='$nc' id='ac'>".number_format_ind($actitemqty[$icodes] - ($closed_weight))."</td>";
													echo "<td colspan='$rc' id='ac'>".$actitemprc[$icodes]."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($actitemprc[$icodes] * ($actitemqty[$icodes] - ($closed_weight)))."&ensp;</td>";
												echo "</tr>";

												$tot_wlos_qty += ((float)$actitemqty[$icodes] - (float)$closed_weight);
											}
											$tot_wlos_qty = str_replace("-","",$tot_wlos_qty);
											foreach($addeditemcode as $icodes){
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Weight Loss %</b>&ensp;</td>";
													echo "<td colspan='$dc'></td>";
													echo "<td colspan='$sc'></td>";
													echo "<td colspan='$ic'>".$itemnames[$icodes]."</td>";
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<td colspan='$jc' id='ac'></td>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td colspan='$bc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$tc' id='ac'></td>"; }
													if($ifjbwen == 1){ echo "<td colspan='$ec' id='ac'></td>"; }
													//number_format_ind((($addeditemqty[$icodes] - $rmditemqty[$icodes] - $actitemqty[$icodes]) / $addeditemqty[$icodes]) * 100);
													$t1 = 0; $t1 = ($addeditemqty[$icodes] - $rmditemqty[$icodes] - $actitemqty[$icodes]);
													$t2 = 0; $t2 = $addeditemqty[$icodes];
													if($t1 > 0 && $t2 > 0){
														echo "<td colspan='$nc' id='ac' title='($addeditemqty[$icodes] - $rmditemqty[$icodes] - $actitemqty[$icodes]) / $addeditemqty[$icodes])'>".number_format_ind((($t1) / $t2) * 100)."</td>";
													}
													else{
														echo "<td colspan='$nc' id='ac' title=''>".number_format_ind(0)."</td>";
													}
													
													echo "<td colspan='$rc' id='ac'></td>";
													echo "<td colspan='$ac' id='ac'></td>";
												echo "</tr>";
											}
											$totalp = $totals = 0;
											foreach($addeditemcode as $icodes){
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Gross Profit</b>&ensp;</td>";
													echo "<td colspan='2' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
													echo "<td colspan='1' align='center'>".$itemnames[$icodes]."</td>";
													echo "<td colspan='7' id='ac' title='Sales - (Purchase - available)'>".number_format_ind($rmditemamt[$icodes] - ($addeditemamt[$icodes] - $actitemamt[$icodes]))."&ensp;</td>";
												echo "</tr>";
												$totalp = $totalp + $addeditemamt[$icodes] - $actitemamt[$icodes];
												$totals = $totals + $rmditemamt[$icodes];
											}
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Total Gross Profit</b>&ensp;</td>";
												echo "<td colspan='2' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
												//echo "<td colspan='5' align='center'>".$itemnames[$icodes]."</td>";
												echo "<td colspan='8' id='ac'>".number_format_ind($totals - $totalp)."&ensp;</td>";
											echo "</tr>";
										}
										else {
											if($qsize == 2) { $sch = 10; $bc = 5; $qc = $wc = 2; $ac = 1; }
											else if($qsize == 3) { $sch = 2; $bc = 6; $qc = $wc = $ac = 2; }
											else if($qsize == 4) { $sch = 4; $bc = 4; $qc = $wc = 2; $ac = 1; }
											else if($qsize == 5) { $sch = 5; $bc = 3; $qc = $wc = $ac = 1; }
											else if($qsize == 6) { $sch = 2; $bc = 3; $qc = $wc = 2; $ac = 1; }
											else { $sch = 10; $bc = 10; $qc = $wc = 4; $ac = 2; }
											echo "<tr style='text-align:center;font-weight:bold; background-color: #9F81F7;'>";
												echo "<td rowspan='2' colspan='$sch'>Final Total</td>";
												foreach($addeditemcode as $icodes){
													echo "<td colspan='$bc'>".$itemnames[$icodes]."</td>";
												}
											echo "</tr>";
											echo "<tr style='text-align:center;font-weight:bold; background-color: #9F81F7;'>";
											foreach($addeditemcode as $icodes){
												echo "<td colspan='$qc'>Birds</td>";
												echo "<td colspan='$wc'>Weight</td>";
												echo "<td colspan='$ac'>Amount</td>";
											}
											echo "</tr>";
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Total Purchase/T. IN</b>&ensp;</td>";
												foreach($addeditemcode as $icodes){
													echo "<td colspan='$qc' id='ac'>".str_replace(".00","",number_format_ind($addeditembds[$icodes]))."</td>";
													echo "<td colspan='$wc' id='ac'>".number_format_ind($addeditemqty[$icodes])."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($addeditemamt[$icodes])."&ensp;</td>";
												}
											echo "</tr>";
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Total Sold/T. OUT</b>&ensp;</td>";
												foreach($addeditemcode as $icodes){
													echo "<td colspan='$qc' id='ac'>".str_replace(".00","",number_format_ind($rmditembds[$icodes]))."</td>";
													echo "<td colspan='$wc' id='ac'>".number_format_ind($rmditemqty[$icodes])."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($rmditemamt[$icodes])."&ensp;</td>";
												}
											echo "</tr>";
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Closing</b>&ensp;</td>";
												foreach($addeditemcode as $icodes){
													echo "<td colspan='$qc' id='ac'>".str_replace(".00","",number_format_ind($addeditembds[$icodes] - $rmditembds[$icodes]))."</td>";
													echo "<td colspan='$wc' id='ac'>".number_format_ind($addeditemqty[$icodes] - $rmditemqty[$icodes])."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($prc[$icodes] * ($addeditemqty[$icodes] - $rmditemqty[$icodes]))."&ensp;</td>";
												}
											echo "</tr>";
											$seq = "SELECT code,closedquantity,closedbirds,price FROM `item_closingstock` WHERE `date` LIKE '$fromdate' AND `code` IN ('$icode')"; $grpstcode = " GROUP BY `code` ORDER BY `code` ASC"; $sql = $seq."".$wnames; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
												$actitemcode[$row['code']] = $row['code'];
												$actitembds[$row['code']] = number_format_ind($row['closedbirds']);
												$actitemqty[$row['code']] = number_format_ind($row['closedquantity']);
												$actitemamt[$row['code']] = number_format_ind($row['price'] * $row['closedquantity']);
											}
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Actual Closing</b>&ensp;</td>";
												foreach($addeditemcode as $icodes){
													/*
													if($actitembds[$icodes] == ".00" || $actitembds[$icodes] == "0" || $actitembds[$icodes] == "0.00" || $actitembds[$icodes] == ""){
														echo "<td colspan='$qc' id='ac'>0.00</td>";
													}
													else{
														echo "<td colspan='$qc' id='ac'>".$actitembds[$icodes]."</td>";
													}
													if($actitembds[$icodes] == ".00" || $actitembds[$icodes] == "0" || $actitembds[$icodes] == "0.00" || $actitembds[$icodes] == ""){
														echo "<td colspan='$qc' id='ac'>0.00</td>";
													}
													else{
														echo "<td colspan='$wc' id='ac'>".$actitemqty[$icodes]."</td>";
													}
													if($actitembds[$icodes] == ".00" || $actitembds[$icodes] == "0" || $actitembds[$icodes] == "0.00" || $actitembds[$icodes] == ""){
														echo "<td colspan='$qc' id='ac'>0.00</td>";
													}
													else{
														echo "<td colspan='$ac' id='ac'>".$actitemamt[$icodes]."&ensp;</td>";
													}*/
													echo "<td colspan='$qc' id='ac'>".$actitembds[$icodes]."</td>";
													echo "<td colspan='$wc' id='ac'>".$actitemqty[$icodes]."</td>";
													echo "<td colspan='$ac' id='ac'>".$actitemamt[$icodes]."&ensp;</td>";
												}
											echo "</tr>";
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Weight Loss</b>&ensp;</td>";
												foreach($addeditemcode as $icodes){
													echo "<td colspan='$qc' id='ac'>".str_replace(".00","",number_format_ind($actitembds[$icodes] - ($addeditembds[$icodes] - $rmditembds[$icodes])))."</td>";
													echo "<td colspan='$wc' id='ac'>".number_format_ind($actitemqty[$icodes] - ($addeditemqty[$icodes] - $rmditemqty[$icodes]))."</td>";
													echo "<td colspan='$ac' id='ac'>".number_format_ind($prc[$icodes] * ($actitemqty[$icodes] - ($addeditemqty[$icodes] - $rmditemqty[$icodes])))."&ensp;</td>";
												}
											echo "</tr>";
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Weight Loss %</b>&ensp;</td>";
												foreach($addeditemcode as $icodes){
													echo "<td colspan='$qc' id='ac'>".str_replace(".00","",number_format_ind($actitembds[$icodes] - ($addeditembds[$icodes] - $rmditembds[$icodes])))."</td>";
													$t1 = 0; $t1 = ($addeditemqty[$icodes] - $rmditemqty[$icodes]);
													$t2 = 0; $t2 = $addeditemqty[$icodes];
													if($t1 > 0 && $t2 > 0){
														echo "<td colspan='$wc' id='ac'>".number_format_ind($actitemqty[$icodes] - ($addeditemqty[$icodes] - $rmditemqty[$icodes]))." (<b>".number_format_ind((($t1) / $t2) * 100)."%</b>)</td>";
													}
													else{
														echo "<td colspan='$wc' id='ac'>".number_format_ind($actitemqty[$icodes] - ($addeditemqty[$icodes] - $rmditemqty[$icodes]))." (<b>".number_format_ind(0)."%</b>)</td>";
													}
													
													echo "<td colspan='$ac' id='ac'>".number_format_ind($prc[$icodes] * ($actitemqty[$icodes] - ($addeditemqty[$icodes] - $rmditemqty[$icodes])))."&ensp;</td>";
												}
											echo "</tr>";
											$totalp = $totals = 0;
											foreach($addeditemcode as $icodes){
												echo "<tr>";
													echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Gross Profit</b>&ensp;</td>";
													echo "<td colspan='2' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
													echo "<td colspan='1' align='center'>".$itemnames[$icodes]."</td>";
													echo "<td colspan='7' id='ac' title='Sales - (Purchase - available)'>".number_format_ind($rmditemamt[$icodes] - ($addeditemamt[$icodes] - $actitemamt[$icodes]))."&ensp;</td>";
												echo "</tr>";
												$totalp = $totalp + $addeditemamt[$icodes] - $actitemamt[$icodes];
												$totals = $totals + $rmditemamt[$icodes];
											}
											echo "<tr>";
												echo "<td colspan='$sch' id='ac' style='background-color: #98fb98;'><b>Total Gross Profit</b>&ensp;</td>";
												echo "<td colspan='2' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
												//echo "<td colspan='5' align='center'>".$itemnames[$icodes]."</td>";
												echo "<td colspan='8' id='ac'>".number_format_ind($totals - $totalp)."&ensp;</td>";
											echo "</tr>";
										}
										echo "<tr style='background-color: #9F81F7;'>";
											echo "<td colspan='20' align='center'><b>Expenses</b></td>";
										echo "</tr>";
										echo "<tr style='background-color: #98fb98;' align='center'>";
											echo "<td colspan=2'><b>Transaction Details</b></td>";
											echo "<td colspan=1'><b>Date</td></b>";
											echo "<td colspan='3'><b>Doc No.</b></td>";
											echo "<td colspan='4'><b>From Account</b></td>";
											echo "<td colspan='4'><b>To Account</b></td>";
											echo "<td colspan='4'><b>Narration Account</b></td>";
											echo "<td colspan='2'><b>Amount</b></td>";
										echo "</tr>";
										
										$sch_code = "";
										$sql = "SELECT * FROM `acc_schedules` WHERE `subtype` LIKE 'COA-0003'"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($sch_code == ""){
												$sch_code = $row['code'];
											}
											else{
												$sch_code = $sch_code."','".$row['code'];
											}
										}
										$exp_coa_code = "";
										$sql = "SELECT * FROM `acc_coa` WHERE `schedules` IN ('$sch_code')"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($exp_coa_code == ""){
												$exp_coa_code = $row['code'];
											}
											else{
												$exp_coa_code = $exp_coa_code."','".$row['code'];
											}
										}
										$ttcoa = 0;
										$seq = "SELECT * FROM `acc_vouchers` WHERE `date` LIKE '$fromdate' AND `prefix` LIKE 'PV' AND `tcoa` IN ('$exp_coa_code') AND `active` = '1'"; $sql = $seq."".$wnames; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											echo "<tr>";
												echo "<td colspan='2' align='center'>".$row['trnum']."</td>";
												echo "<td colspan='1' align='center'>".date("d.m.Y",strtotime($row['date']))."</td>";
												echo "<td colspan='3' align='center'>".$row['dcno']."</td>";
												echo "<td colspan='4' align='center'>".$coanames[$row['fcoa']]."</td>";
												echo "<td colspan='4' align='center'>".$coanames[$row['tcoa']]."</td>";
												echo "<td colspan='4' align='center'>".$row['remarks']."</td>";
												echo "<td colspan='2' align='right'>".$row['amount']."&ensp;</td>";
											echo "</tr>";
											$ttcoa = $ttcoa + $row['amount'];
										}
										echo "<tr style='background-color: #9F81F7;'>";
											echo "<td colspan='20' align='center'><b>Received Back</b></td>";
										echo "</tr>";
										echo "<tr style='background-color: #98fb98;' align='center'>";
											echo "<td colspan=2'><b>Transaction Details</b></td>";
											echo "<td colspan=1'><b>Date</td></b>";
											echo "<td colspan='3'><b>Doc No.</b></td>";
											echo "<td colspan='4'><b>From Account</b></td>";
											echo "<td colspan='4'><b>To Account</b></td>";
											echo "<td colspan='4'><b>Narration Account</b></td>";
											echo "<td colspan='2'><b>Amount</b></td>";
										echo "</tr>";
										
										$seq = "SELECT * FROM `acc_vouchers` WHERE `date` LIKE '$fromdate' AND `prefix` LIKE 'RV' AND `active` = '1'"; $sql = $seq."".$wnames; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											echo "<tr>";
												echo "<td colspan='2' align='center'>".$row['trnum']."</td>";
												echo "<td colspan='1' align='center'>".date("d.m.Y",strtotime($row['date']))."</td>";
												echo "<td colspan='3' align='center'>".$row['dcno']."</td>";
												echo "<td colspan='4' align='center'>".$coanames[$row['fcoa']]."</td>";
												echo "<td colspan='4' align='center'>".$coanames[$row['tcoa']]."</td>";
												echo "<td colspan='4' align='center'>".$row['remarks']."</td>";
												echo "<td colspan='2' align='right'>".$row['amount']."&ensp;</td>";
											echo "</tr>";
											$ttcoa = $ttcoa - $row['amount'];
										}
										echo "<tr>";
											echo "<td colspan='10' align='right' style='background-color: #98fb98;'><b>Total Expenses</b>&ensp;</td>";
											echo "<td colspan='1' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
											//echo "<td colspan='5' align='center'>".$itemnames[$icodes]."</td>";
											echo "<td colspan='9' align='right'><b>".number_format_ind($ttcoa)."</b>&ensp;</td>";
										echo "</tr>";
										
										echo "<tr>";
											echo "<td colspan='10' align='right' style='background-color: #98fb98;'><b>Net Profit</b>&ensp;</td>";
											echo "<td colspan='1' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
											//echo "<td colspan='5' align='center'>".$itemnames[$icodes]."</td>";
											echo "<td colspan='9' align='right'><b>".number_format_ind($totals - $totalp - $ttcoa)."</b>&ensp;</td>";
										echo "</tr>";
										
										$tot_sin_prc = 0; if((float)$tot_sin_qty != 0){ $tot_sin_prc = (float)$tot_sin_amt / (float)$tot_sin_qty; }
										$net_mrgprc = 0; if((float)$tot_sout_qty != 0){ $net_mrgprc = ((((float)$tot_wlos_qty * (float)$tot_sin_prc) + (float)$ttcoa) / (float)$tot_sout_qty); }
										$ns_title = "$net_mrgprc = ((((float)$tot_wlos_qty * (float)$tot_sin_prc) + (float)$ttcoa) / (float)$tot_sout_qty);";
										echo "<tr>";
											echo "<td colspan='10' align='right' style='background-color: #98fb98;'><b>Net Margin</b>&ensp;</td>";
											echo "<td colspan='1' align='center'>".date("d.m.Y",strtotime($fromdate))."</td>";
											echo "<td colspan='9' align='right' title='$ns_title'><b>".number_format_ind($net_mrgprc)."</b>&ensp;</td>";
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
