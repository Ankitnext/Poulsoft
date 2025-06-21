<?php 
			$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
			include "../config.php";
			include "header_head.php";
			
			$today = date("Y-m-d");
			$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $itm_name[$row['code']] = $row['description']; $itm_code[$row['code']] = $row['code']; }
			$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $officename[$row['code']] = $row['description']; $officecode[$row['code']] = $row['code']; }
			$fromdate = $_POST['fromdate'];
			$todate = $_POST['todate'];
			if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
			$iname = $_POST['iname']; $wname = $_POST['wname'];
			if($iname == "all") { $inames = ""; } else { $inames = " AND `itemcode` = '$iname'"; $iinames = " AND `code` = '$iname'"; }
			if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }
		?>
<html>
	<head>
		<link rel="stylesheet" type="text/css"href="reportstyle.css">
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;				
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
	<body class="hold-transition skin-blue sidebar-mini">
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?></td>
					<td align="center">
						<h3>Stock Balance Report</h3>
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
					<form action="itm_ledger.php" method="post">
						<table class="table1" style="min-width:90%;line-height:23px;">
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="15">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="iname" id="iname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($itm_code as $wcode){
											?>
													<option <?php if($iname == $itm_code[$wcode]) { echo 'selected'; } ?> value="<?php echo $itm_code[$wcode]; ?>"><?php echo $itm_name[$wcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="wname" id="wname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($officecode as $ocode){
											?>
													<option <?php if($wname == $officecode[$ocode]) { echo 'selected'; } ?> value="<?php echo $officecode[$ocode]; ?>"><?php echo $officename[$ocode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl No.</th>
								<th>Item</th>
								<th colspan="2">Opening</th>
								<th colspan="2">Purchased</th>
								<th colspan="2">Transferred In</th>
								<th colspan="2">Sold</th>
								<th colspan="2">Transferred Out</th>
								<th colspan="2">closing</th>
							</thead>
							<thead class="thead2" style="background-color: #98fb98;">
								<th></th>
								<th></th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Quantity</th>
								<th>Amount</th>
								<th>Quantity</th>
								<th>Amount</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									$fromdate = date("Y-m-d",strtotime($fromdate));
									$todate = date("Y-m-d",strtotime($todate));
									$iname = $_POST['iname']; $wname = $_POST['wname'];
									$itm_list = "";
									foreach($itm_code as $icode){ if($icode !=""){ if($itm_list == ""){ $itm_list = $icode; } else{ $itm_list = $itm_list."','".$icode; } } }
									if($iname == "all"){ $itm_codes = $itm_list; } else { $itm_codes = $iname; }
									foreach($officecode as $wcode){ if($wcode !=""){ if($wh_list == ""){ $wh_list = $wcode; } else{ $wh_list = $wh_list."','".$wcode; } } }
									if($wname == "all"){ $wh_codes = $wh_list; } else { $wh_codes = $wname; }
									$sql = "SELECT MAX(date) as cdate,code,warehouse FROM `item_closingstock` WHERE `code` IN ('$itm_codes') AND `warehouse` IN ('$wh_codes') AND `date` <= '$fromdate' AND `active` = '1' GROUP BY `code`,`warehouse` ORDER BY `code`,`warehouse` ASC"; $query = mysqli_query($conn,$sql);
									/*$csize = mysqli_num_rows($query);
									if($csize > 0){
										while($row = mysql_fetch_assoc($query)){
											$open_det = $row['cdate']."@".$row['code']."@".$row['warehouse'];
											$open_stock[$open_det] = $open_det;
										}
									}
									
									$sql = "SELECT * FROM `item_details` WHERE `flag` = '1'"."".$iinames; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; $itemcodes[$row['code']] = $row['code']; }
									$groups = " AND `flag` = '1' AND `active` = '1' GROUP BY `itemcode`";
									
									$seq = "SELECT itemcode,SUM(netweight) as pqty FROM `pur_purchase` WHERE `date` < '$fromdate'";
									$sql = $seq."".$inames."".$wnames."".$groups; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $o_pur[$row['itemcode']] = $row['pqty']; }
									$seq = "SELECT itemcode,SUM(netweight) as pqty FROM `customer_sales` WHERE `date` < '$fromdate'";
									$sql = $seq."".$inames."".$wnames."".$groups; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $o_inv[$row['itemcode']] = $row['pqty']; }
									$seq = "SELECT code,SUM(quantity) as pqty FROM `item_stocktransfers` WHERE `date` < '$fromdate'";
									$sql = $seq."".$iinames."".$groups; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $o_inv[$row['itemcode']] = $row['pqty']; }
									
									$seq = "SELECT itemcode,SUM(netweight) as pqty FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$sql = $seq."".$inames."".$wnames."".$groups; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $t_pur[$row['itemcode']] = $row['pqty']; }
									$seq = "SELECT itemcode,SUM(netweight) as pqty FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$sql = $seq."".$inames."".$wnames."".$groups; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $t_inv[$row['itemcode']] = $row['pqty']; }
									$seq = "SELECT code,SUM(quantity) as pqty FROM `item_stocktransfers` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$sql = $seq."".$iinames."".$groups; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $o_inv[$row['itemcode']] = $row['pqty']; }
									
									foreach($itemcodes as $icodes){
										$opb_qty = $o_pur[$icodes] - $o_inv[$icodes];
										$csg_qty = $opb_qty + $t_pur[$icodes] - $t_inv[$icodes];
										echo "<tr>";
										echo "<td>".$itemname[$icodes]."</td>";
										echo "<td>".$opb_qty."</td>";
										echo "<td></td>";
										echo "<td>".$t_pur[$icodes]."</td>";
										echo "<td></td>";
										echo "<td>".$t_inv[$icodes]."</td>";
										echo "<td></td>";
										echo "<td>".$csg_qty."</td>";
										echo "<td></td>";
										echo "</tr>";
									}*/
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									
								</tr>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
