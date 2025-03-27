<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
			
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
	$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$ptype[$row['code']] = $row['contacttype'];
	}
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

	$item_codes = $icat_list = "";
	$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Bird%' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($icat_list == ""){ $icat_list = $row['code']; } else{ $icat_list = $icat_list."','".$row['code']; } }

	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Chicken%' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($icat_list == ""){ $icat_list = $row['code']; } else{ $icat_list = $icat_list."','".$row['code']; } }

	$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($item_codes == ""){ $item_codes = $row['code']; } else{ $item_codes = $item_codes."','".$row['code']; } }

	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname'];
	if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
	
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; if($excel_type != "exportexcel"){ $exoption = $_POST['export']; } } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_sname = $_POST['sname']; $exl_cname = $_POST['cname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_sname = $exl_cname = "all";
	}
	$url = "../PHPExcel/Examples/DateWiseLoadReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&sname=".$exl_sname."&cname=".$exl_cname;
	
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
		<style>
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
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Load wise Ledger</h3>
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
					<form action="main_datewiseloadreport.php" method="post" onsubmit="return checkval()">
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="20">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>" style="width:90px;" />
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>" style="width:90px;" />
									&ensp;&ensp;
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
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2" style="width:auto;">
											<option value="all" selected>-All-</option>
											<?php
												foreach($pcode as $pc){
													$word = "S";
													if(strpos($ptype[$pc], $word) !== false){
											?>
												<option value="<?php echo $pcode[$pc]; ?>" <?php if($cname == $pcode[$pc]){ echo 'selected'; } ?>><?php echo $pname[$pc]; ?></option>
											<?php
													}
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
									<th colspan="9">Purchases</th>
									<th colspan="5">Sales</th>
									<th colspan="2">Wt. Loss</th>
									<th colspan="1">Expenses</th>
									<th colspan="2">Profit/Loss</th>
								</tr>
								<tr>
									<th>Date</th>
									<th>Load No</th>
									<th>Warehouse</th>
									<th>Party</th>
									<th>Nos</th>
									<th>Quantity</th>
									<th>Avg Wt</th>
									<th>Rate</th>
									<th>Amount</th>
									<th>Nos</th>
									<th>Quantity</th>
									<th>Avg Wt</th>
									<th>Rate</th>
									<th>Amount</th>
									<th>Wt Loss Kg</th>
									<th>%</th>
									<th>Total Exp</th>
									<th>Amount</th>
									<th>Per Kg</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									$fromdate = $_POST['fromdate'];
									$todate = $_POST['todate'];
									if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate'])); }
									$cname = $_POST['cname']; $sname = $_POST['sname']; $iname = $_POST['iname'];
									if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `vendorcode` = '$cname'"; }
									if($sname == "all" || $sname == "select") { $snames = $fsnames = $tsnames = ""; } else { $snames = " AND `warehouse` = '$sname'"; $fsnames = " AND `fromwarehouse` = '$sname'"; $tsnames = " AND `towarehouse` = '$sname'"; }
										
									/*$sql = "SELECT SUM(birds) as birds, SUM(netweight) as nwt,bookinvoice,vendorcode,date,AVG(itemprice) as itemprice,SUM(totalamt) as totalamt FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `itemcode` IN ('$item_codes') AND `active` = '1'".$cnames." GROUP BY `date`,`bookinvoice` ORDER BY `date`,`bookinvoice`,`vendorcode` ASC";
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$avgWt = 0;
										$avgWt = $row['nwt'] / $row['birds'];
										$pur_docno[$row['date']."&".$row['bookinvoice']] = $row['date']."&".$row['bookinvoice'];
										$pur_details[$row['date']."&".$row['bookinvoice']] = $row['date']."@".$row['bookinvoice']."@".$pname[$row['vendorcode']]."@".$row['birds']."@".$row['nwt']."@".$avgWt."@".$row['itemprice']."@".$row['totalamt'];
									}*/
									$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `itemcode` IN ('$item_codes') AND `active` = '1'".$cnames."".$snames." ORDER BY `date`,`bookinvoice`,`vendorcode`,`invoice` ASC";
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										//$avgWt = 0;
										//$avgWt = $row['nwt'] / $row['birds'];
										$index = $row['date']."&".$row['bookinvoice'];
										$index_sale[$index] = $index;
										$pur_docno[$index] = $index;
										$pur_dates[$index] = $row['date'];
										$pur_bkinv[$index] = $row['bookinvoice'];
										$pur_invoc[$index] = $row['invoice'];
										$pur_cuscd[$index] = $pname[$row['vendorcode']];
										$pur_birds[$index] = $pur_birds[$index] + $row['birds'];
										$pur_totwt[$index] = $pur_totwt[$index] + $row['netweight'];
										$pur_toamt[$index] = $pur_toamt[$index] + $row['totalamt'];
										$pur_price[$index] = $row['itemprice'];
										$pur_sector[$index] = $row['warehouse'];
										//$sal_details[$row['date']."&".$row['bookinvoice']] = $row['birds']."@".$row['nwt']."@".$avgWt."@".$row['itemprice']."@".$row['totalamt'];
									}
									foreach($index_sale as $isal){
										if((float)$pur_totwt[$isal] > 0 && (float)$pur_birds[$isal] > 0){
											$avgWt = $pur_totwt[$isal] / $pur_birds[$isal];
										}
										else{
											$avgWt = 0;
										}
										 
										$pur_details[$isal] = $pur_dates[$isal]."@".$pur_bkinv[$isal]."@".$pur_cuscd[$isal]."@".$pur_birds[$isal]."@".$pur_totwt[$isal]."@".$avgWt."@".$pur_price[$isal]."@".$pur_toamt[$isal];
									}
									//$sql = "SELECT SUM(birds) as birds, SUM(netweight) as nwt,bookinvoice,customercode,date,AVG(itemprice) as itemprice,SUM(totalamt) as totalamt FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `itemcode` IN ('$item_codes') AND `active` = '1' GROUP BY `date`,`bookinvoice` ORDER BY `date`,`bookinvoice`,`customercode` ASC";
									$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `itemcode` IN ('$item_codes') AND `active` = '1'".$snames;
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										//$avgWt = 0;
										//$avgWt = $row['nwt'] / $row['birds'];
										$index = $row['date']."&".$row['bookinvoice'];
										$index_sale[$index] = $index;
										$sale_birds[$index] = $sale_birds[$index] + $row['birds'];
										$sale_totwt[$index] = $sale_totwt[$index] + $row['netweight'];
										$sale_toamt[$index] = $sale_toamt[$index] + $row['totalamt'];
										$sale_price[$index] = $row['itemprice'];
										$sale_sector[$index] = $row['warehouse'];
										//$sal_details[$row['date']."&".$row['bookinvoice']] = $row['birds']."@".$row['nwt']."@".$avgWt."@".$row['itemprice']."@".$row['totalamt'];
									}
									foreach($index_sale as $isal){
										if((float)$sale_totwt[$isal] > 0 && (float)$sale_birds[$isal] > 0){
											$avgWt = $sale_totwt[$isal] / $sale_birds[$isal];
										}
										else{
											$avgWt = 0;
										}
										 
										$sal_details[$isal] = $sale_birds[$isal]."@".$sale_totwt[$isal]."@".$avgWt."@".$sale_price[$isal]."@".$sale_toamt[$isal];
									}
									$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `prefix` = 'PV' AND `active` = '1'".$snames." ORDER BY `date`,`dcno` ASC";
									$query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
										$vou_details[$row['date']."&".$row['dcno']] = $vou_details[$row['date']."&".$row['dcno']] + $row['amount'];
										$vou_sector[$row['date']."&".$row['dcno']] = $row['warehouse'];
									}
									$c = $tot_pur_bds = $tot_pur_qty = $tot_pur_amt = $tot_sal_bds = $tot_sal_qty = $tot_sal_amt = $tot_vou_amt = $tot_wht_qty = 0;
									foreach($pur_docno as $pdno){
										$c = $c + 1;
										$pur_val = explode("@",$pur_details[$pdno]);
										$sal_val = explode("@",$sal_details[$pdno]);
										$vou_val = $vou_details[$pdno];
										echo "<tr>";
										echo "<td style='text-align:left;'>".date("d.m.Y",strtotime($pur_val[0]))."</td>";
										if($exoption == "printerfriendly"){
											echo "<td style='text-align:left;'>".$pur_val[1]."</td>";
										}
										else{
											echo "<td style='text-align:left;'><a href='main_loadwisestockledgerreport.php?id=$pur_val[1]&fromdate=$pur_val[0]' target='_BLANK'>".$pur_val[1]."</a></td>";
										}
										echo "<td style='text-align:left;'>".$sector_name[$pur_sector[$pdno]]."</td>";
										echo "<td style='text-align:left;'>".$pur_val[2]."</td>";
										echo "<td>".number_format_ind($pur_val[3])."</td>";
											$tot_pur_bds = (float)$tot_pur_bds + (float)$pur_val[3];
										echo "<td>".number_format_ind($pur_val[4])."</td>";
											$tot_pur_qty = (float)$tot_pur_qty + (float)$pur_val[4];
										echo "<td>".number_format_ind($pur_val[5])."</td>";
										echo "<td>".number_format_ind($pur_val[6])."</td>";
										echo "<td>".number_format_ind($pur_val[7])."</td>";
											$tot_pur_amt = (float)$tot_pur_amt + (float)$pur_val[7];
										echo "<td>".number_format_ind($sal_val[0])."</td>";
											$tot_sal_bds = (float)$tot_sal_bds + (float)$sal_val[0];
										echo "<td>".number_format_ind($sal_val[1])."</td>";
											$tot_sal_qty = (float)$tot_sal_qty + (float)$sal_val[1];
										echo "<td>".number_format_ind($sal_val[2])."</td>";
										echo "<td>".number_format_ind($sal_val[3])."</td>";
										echo "<td>".number_format_ind($sal_val[4])."</td>";
											$tot_sal_amt = (float)$tot_sal_amt + (float)$sal_val[4];
										echo "<td>".number_format_ind((float)$pur_val[4] - (float)$sal_val[1])."</td>";
											$tot_wht_qty = (float)$tot_wht_qty + ((float)$pur_val[4] - (float)$sal_val[1]);
											if(((float)$pur_val[4] - (float)$sal_val[1]) > 0 && (float)$pur_val[4] > 0){
												echo "<td>".number_format_ind((((float)$pur_val[4] - (float)$sal_val[1]) / (float)$pur_val[4]) * 100)."</td>";
											}
											else{
												echo "<td>".number_format_ind(0)."</td>";
											}
										
										echo "<td>".number_format_ind($vou_val)."</td>";
											$tot_vou_amt = (float)$tot_vou_amt + (float)$vou_val;
										echo "<td>".number_format_ind(((float)$sal_val[4] - (float)$pur_val[7]) - (float)$vou_val)."</td>";
										if((((float)$sal_val[4] - (float)$pur_val[7]) - (float)$vou_val) > 0 && (float)$pur_val[4] > 0){
											echo "<td>".number_format_ind((((float)$sal_val[4] - (float)$pur_val[7]) - (float)$vou_val) / (float)$pur_val[4])."</td>";
										}
										else{
											echo "<td>".number_format_ind(0)."</td>";
										}
										
										echo "</tr>";
									}
								}
							?>
							</tbody>
							<thead>
								<tr class="foottr" style="background-color: #98fb98;">
									<td align="center" colspan="4"><b>Total</b></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_pur_bds) == "NAN.00" || number_format_ind($tot_pur_bds) == ".00" || number_format_ind($tot_pur_bds) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_pur_bds); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_pur_qty) == "NAN.00" || number_format_ind($tot_pur_qty) == ".00" || number_format_ind($tot_pur_qty) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_pur_qty); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if($tot_pur_qty > 0 && $tot_pur_bds > 0){ echo number_format_ind($tot_pur_qty / $tot_pur_bds); } else{ echo number_format_ind(0); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if($tot_pur_amt > 0 && $tot_pur_qty > 0){ echo number_format_ind($tot_pur_amt / $tot_pur_qty); } else{ echo number_format_ind(0); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_pur_amt) == "NAN.00" || number_format_ind($tot_pur_amt) == ".00" || number_format_ind($tot_pur_amt) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_pur_amt); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_sal_bds) == "NAN.00" || number_format_ind($tot_sal_bds) == ".00" || number_format_ind($tot_sal_bds) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_sal_bds); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_sal_qty) == "NAN.00" || number_format_ind($tot_sal_qty) == ".00" || number_format_ind($tot_sal_qty) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_sal_qty); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if($tot_sal_qty > 0 && $tot_sal_bds > 0){ echo number_format_ind($tot_sal_qty / $tot_sal_bds); } else{ echo number_format_ind(0); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if($tot_sal_amt > 0 && $tot_sal_qty > 0){ echo number_format_ind($tot_sal_amt / $tot_sal_qty); } else{ echo number_format_ind(0); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_sal_amt) == "NAN.00" || number_format_ind($tot_sal_amt) == ".00" || number_format_ind($tot_sal_amt) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_sal_amt); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_wht_qty) == "NAN.00" || number_format_ind($tot_wht_qty) == ".00" || number_format_ind($tot_wht_qty) == "0.00"){ echo "0.00"; } else{  echo number_format_ind($tot_wht_qty); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if($tot_wht_qty > 0 && $tot_pur_qty > 0){ echo number_format_ind(($tot_wht_qty / $tot_pur_qty) * 100); } else{ echo number_format_ind(0); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_vou_amt) == "NAN.00" || number_format_ind($tot_vou_amt) == ".00" || number_format_ind($tot_vou_amt) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_vou_amt); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(number_format_ind($tot_sal_amt - ($tot_pur_amt + $tot_vou_amt)) == "NAN.00" || number_format_ind($tot_sal_amt - ($tot_pur_amt + $tot_vou_amt)) == ".00" || number_format_ind($tot_sal_amt - ($tot_pur_amt + $tot_vou_amt)) == "0.00"){ echo "0.00"; } else{ echo number_format_ind($tot_sal_amt - ($tot_pur_amt + $tot_vou_amt)); } ?></td>
									<td style='padding-right: 5px;text-align:right;'><?php if(($tot_sal_amt - ($tot_pur_amt + $tot_vou_amt)) > 0 && $tot_pur_qty > 0){ echo number_format_ind(($tot_sal_amt - ($tot_pur_amt + $tot_vou_amt)) / $tot_pur_qty); } else{ echo number_format_ind(0); } ?></td>
								</tr>
							</thead>
						</table>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("checkcname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select Supplier ..!");
					return false;
				}
				else {
					return true;
				}
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
		<?php //if($cname == ""){ } else { echo "<script> sortTable(0); </script>"; } ?>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
