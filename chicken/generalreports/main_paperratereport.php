<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_name[$row['code']] = $row['description']; $grp_code[$row['code']] = $row['code']; }
	
	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); //`category` IN ('$cat_codes') AND 
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	if(isset($_POST['submit']) == true){
		$fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate']));
		$grp_codes = $_POST['gcodes'];
	}
	else{
		$fromdate = $todate = date("Y-m-d"); $grp_codes = "select";
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
 				background-color: #98fb98;				
			}
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
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;color: green;font-size:18px;">Paper Rate Report</label><br/>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">Group:</b>&nbsp;<?php echo $grp_name[$grp_codes]; ?></label><br/>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
						<form action="main_paperratereport.php" method="post" onsubmit="return checkval()">
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="20">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" style="width: 110px;" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" style="width: 110px;" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="gcodes" id="gcodes" class="form-control select2">
											<!--<option value="all" <?php //if($grp_codes == "all") { echo 'selected'; } ?> >-All-</option>-->
											<option value="select">-Select-</option>
											<?php
												foreach($grp_code as $gcode){
											?>
													<option <?php if($grp_codes == $grp_code[$gcode]) { echo 'selected'; } ?> value="<?php echo $grp_code[$gcode]; ?>"><?php echo $grp_name[$gcode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						</form>
						<?php }
							if(isset($_POST['submit']) == true){
								$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `cgroup` LIKE '$grp_codes' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` DESC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$pitem_code[$row['code']] = $row['code'];
									$pdate_code[$row['date']] = $row['date'];
									$paper_rate[$row['date']."@".$row['code']] = $row['new_price'];
								}
						?>
							<thead class="thead2" style="background-color: #98fb98;">
								<tr>
									<th>Sl.No.</th>
									<th>Dates</th>
									<?php foreach($pitem_code as $ic){ echo "<th>".$item_name[$ic]."</th>"; } ?>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								//$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']);
								$c = 0;
								//for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
								foreach($pdate_code as $currentDate){
									$present_date = date('Y-m-d', strtotime($currentDate));
									$c++;
									echo "<tr>";
									echo "<td style='text-align:center;'>".$c."</td>";
									echo "<td style='padding-left:5px;text-align:left;'>".date("d.m.Y",strtotime($present_date))."</td>";
									foreach($pitem_code as $ic){ echo "<td>".number_format_ind($paper_rate[$present_date."@".$ic])."</td>"; }
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
