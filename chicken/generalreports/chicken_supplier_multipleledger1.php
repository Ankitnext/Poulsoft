<?php
    //chicken_supplier_multipleledger1.php
    $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
			
	$fdate = $tdate = date("d.m.Y"); $grpdetails = "";
	if(isset($_POST['submit']) == true){
		if($_POST['grpcode'] == "all"){
			$grpdetails = "";
		}
		else{
			$gcode = $_POST['grpcode'];
			$grpdetails = " AND `groupcode` = '$gcode'";
		}
	}
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%'".$grpdetails." AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
	while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
	
	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }
  
    $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%S%' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }
	
?>
		
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
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
				</tr>
				<tr>
					<td align="center" colspan="2">
						<h3>Supplier Multi-Ledger Report</h3>
					</td>
				</tr>
			</table>
		</header>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<table class="table1" style="min-width:auto;line-height:23px;">
					<thead class="thead1" style="background-color: #98fb98;">
						<form action="cus_multiledgerview.php" method="post" onsubmit="return checkval()">
							<tr>
								<td colspan="3">
									<label class="reportselectionlabel">Group</label>&nbsp;
									<select name="grpcode" id="grpcode" class="formcontrol">
									<option value="all" <?php if($_POST['grpcode'] == "all"){ echo 'selected'; } ?>>-All-</option>
									<?php
									foreach($grp_code as $gc){
									?>
									<option value="<?php echo $gc; ?>" <?php if($_POST['grpcode'] == $gc){ echo 'selected'; } ?>><?php echo $grp_name[$gc]; ?></option>
									<?php
									}
									?>
									</select>&ensp;&ensp;
									<label class="reportselectionlabel">Select All</label>&nbsp;
									<input type="checkbox" name="checkall" id="checkall" onchange="checkedall()"/>&ensp;&ensp;
									<button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button>&ensp;&ensp;
								</td>
							</tr>
						</form>
					</thead>
					<form action="chicken_supplier_multipleledger1_view.php" method="post" onsubmit="return checkval()">
						<thead class="thead1" style="background-color: #98fb98;">
							<tr>
								<td colspan="3">
									<label class="reportselectionlabel">From Date</label>&nbsp;
									<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>"/>&ensp;&ensp;
									<label class="reportselectionlabel">To Date</label>&nbsp;
									<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>"/>&ensp;&ensp;
									<label class="reportselectionlabel">View Type</label>&nbsp;
									<!--<select name="view_type" id="view_type" class="form-control1 select2" style="width:150px;">
                                        <option value="normal_print">Normal View</option>
                                        <option value="pdf_print">PDF View</option>
                                    </select>-->
								</td>
							</tr>
						</thead>
						<thead class="thead2" style="background-color: #98fb98;">
							<tr>
								<th>Sl.No.</th>
								<th>Selection</th>
								<th>Supplier Name</th>
							</tr>
						</thead>
						<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							
						<?php
							$c = 0;
							foreach($ven_code as $ccode){
								$c = $c + 1;
								echo "<tr>";
								echo "<td style='width:100px;text-align:center;'>".$c."</td>";
								echo "<td style='width:100px;text-align:center;'><input type='checkbox' name='ven_codes[$c]' id='ven_codes[$c]' value='$ccode' /></td>";
								echo "<td style='padding-left:10px;text-align:left;'>".$ven_name[$ccode]."</td>";
								echo "</tr>";
							}
						?>
							<tr>
								<td colspan="3" style="text-align:center;"><button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button></td>
							</tr>
						</tbody>
					</form>
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
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
