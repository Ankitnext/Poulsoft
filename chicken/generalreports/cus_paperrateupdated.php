<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; $cus_code[$row['code']] = $row['code']; $cus_group[$row['code']] = $row['groupcode']; }
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_name[$row['code']] = $row['description']; $grp_code[$row['code']] = $row['code']; }
	
	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); //`category` IN ('$cat_codes') AND 
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	if(isset($_POST['submit']) == true){
		$ccodes = $_POST['ccodes']; $icodes = $_POST['icodes'];
	}
	else{
		$ccodes = "all"; $icodes = "all";
	}
	$exoption = "displaypage";
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=OutstandingBalanceReport($fromdate-$todate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
				padding: 2px 5px;
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
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; }?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;color: green;font-size:18px;">Customer Paper Rate Report</label><br/>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
						<form action="cus_paperrateupdated.php" method="post" onsubmit="return checkval()">
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="20">
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="ccodes" id="ccodes" class="form-control select2">
											<option value="all" <?php if($ccodes == "all") { echo 'selected'; } ?>>-All-</option>
											<?php
												foreach($cus_code as $ccode){
											?>
													<option <?php if($ccodes == $ccode) { echo 'selected'; } ?> value="<?php echo $ccode; ?>"><?php echo $cus_name[$ccode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="icodes" id="icodes" class="form-control select2">
											<option value="all" <?php if($icodes == "all") { echo 'selected'; } ?>>-All-</option>
											<?php
												foreach($item_code as $icode){
											?>
													<option <?php if($icodes == $icode) { echo 'selected'; } ?> value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<!--<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="gcodes" id="gcodes" class="form-control select2">
											<!--<option value="select">-Select-</option>
											<?php
												/*foreach($grp_code as $gcode){
											?>
													<option <?php if($grp_codes == $grp_code[$gcode]) { echo 'selected'; } ?> value="<?php echo $grp_code[$gcode]; ?>"><?php echo $grp_name[$gcode]; ?></option>
											<?php
												}*/
											?>
										</select>&ensp;&ensp;-->
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						</form>
						<?php }
							if(isset($_POST['submit']) == true){
								$sql = "SELECT * FROM `main_dailypaperrate` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `date` DESC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ $paper_rate[$row['date']."@".$row['code']."@".$row['cgroups']] = $row['new_price']; }
								
								if($ccodes == "all"){ $cus_search = ""; } else{ $cus_search = " AND `ccode` = '$ccodes'"; }
								if($icodes == "all"){ $item_search = ""; } else{ $cus_search = " AND `itemcode` = '$icodes'"; }
								/*$sql = "SELECT * FROM `customer_price` WHERE `active` = 1 AND `dflag` = 0".$cus_search." ORDER BY `date` DESC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$pcus_date[$row['date']] = $row['date'];
									$pitm_code[$row['date']."@".$row['itemcode']."@".$row['ccode']] = $row['itemcode'];
									$pcus_code[$row['date']."@".$row['itemcode']."@".$row['ccode']] = $row['ccode'];
									$pcus_pval[$row['date']."@".$row['itemcode']."@".$row['ccode']] = $row['value'];
									$pcus_type[$row['date']."@".$row['itemcode']."@".$row['ccode']] = $row['value'];
								}
								*/
						?>
							<thead class="thead2" style="background-color: #98fb98;">
								<tr>
									<th>Sl.No.</th>
									<th>Dates</th>
									<th>Customer</th>
									<th>Item</th>
									<th>Price Type</th>
									<th>Add/Deduct Value</th>
									<th>Price Type-2</th>
									<th>Add/Deduct Value-2</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								//$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']);
								$c = 0;
								$sql = "SELECT * FROM `customer_price` WHERE `active` = 1 AND `dflag` = 0".$cus_search."".$item_search." ORDER BY `date` DESC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$present_date = date('Y-m-d', strtotime($currentDate));
									$c++;
									echo "<tr>";
									echo "<td style='text-align:center;'>".$c."</td>";
									echo "<td style='padding-left:5px;text-align:left;'>".date("d.m.Y",strtotime($row['date']))."</td>";
									echo "<td style='text-align:left;'>".$cus_name[$row['ccode']]."</td>";
									echo "<td style='text-align:left;'>".$item_name[$row['itemcode']]."</td>";
									if($row['price_type'] == "A"){
										echo "<td style='text-align:center;'>Add</td>";
									}
									else if($row['price_type'] == "D"){
										echo "<td style='text-align:center;'>Deduct</td>";
									}
									else if($row['price_type'] == "M"){
										echo "<td style='text-align:center;'>Multiply</td>";
									}
									else if($row['price_type'] == "F"){
										echo "<td style='text-align:center;'>Fixed</td>";
									}
									else{
										echo "<td style='text-align:center;'></td>";
									}
									echo "<td style='text-align:right;'>".$row['value']."</td>";
									if($row['price_type2'] == "A"){
										echo "<td style='text-align:center;'>Add</td>";
									}
									else if($row['price_type2'] == "D"){
										echo "<td style='text-align:center;'>Deduct</td>";
									}
									else if($row['price_type2'] == "M"){
										echo "<td style='text-align:center;'>Multiply</td>";
									}
									else if($row['price_type2'] == "F"){
										echo "<td style='text-align:center;'>Fixed</td>";
									}
									else{
										echo "<td style='text-align:center;'></td>";
									}
									echo "<td style='text-align:right;'>".$row['value2']."</td>";
									echo "</tr>";
								}
							?>
							</tbody>
						<?php
						}
						?>
					</table>
				</div>
		</section>
		<script>
			function checkval(){
				var a = document.getElementById("gcodes").value;
				if(a.match("select")){
					alert("Please select Customer Group ..!");
					return false;
				}
				else{
					return true;
				}
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
