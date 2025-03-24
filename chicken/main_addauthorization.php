<?php
	//main_addauthorization
	session_start();
	include "newConfig.php";
	include "header_head.php";
	include "number_format_ind.php";
	$emp_code = $_SESSION['userid'];
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$whcode = $row['loc_access'];
		$saccess = $row['supadmin_access'];
		$aaccess = $row['admin_access'];
		$naccess = $row['normal_access'];
		$authorize = $row['authorize'];
	}
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$cus_code[$row['code']] = $row['code'];
		$cus_name[$row['code']] = $row['name'];
	}
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$item_code[$row['code']] = $row['code'];
		$item_name[$row['code']] = $row['description'];
	}
	$aut_sec = explode(",",$authorize);
	sort($aut_sec);
	$utype = "NA";
	if($saccess == 1){ $utype = "S"; } else if($aaccess == 1){ $utype = "A"; } else if($naccess == 1){ $utype = "N"; } else{ $utype = "N"; }
	
	if($utype = "S" || $utype = "A"){
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$sec_code[$row['code']] = $row['code'];
			$sec_name[$row['code']] = $row['description'];
		}
	}
	else{
		if($whcode == "all"){
			$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$sec_code[$row['code']] = $row['code'];
				$sec_name[$row['code']] = $row['description'];
			}
		}
		else{
			$seccode = explode(",",$whcode);
			$wh_codes = ""; foreach($seccode as $wcode){ if($wh_codes == ""){ $wh_codes = $wcode; } else{ $wh_codes = $wh_codes."','".$wcode; } }
			$sql = "SELECT * FROM `inv_sectors` WHERE `code` IN ('$wh_codes') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$sec_code[$row['code']] = $row['code'];
				$sec_name[$row['code']] = $row['description'];
			}
		}
	}
	if(isset($_POST['submittrans1']) == true){
		$fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate']));
		$lcode = ""; $wh_all = 0; 
		foreach($_POST['lname'] as $locname){
			if($locname == "all"){ $wh_all = 1; }
			$loc_code[$locname] = $locname;
			if($lcode == ""){ $lcode = $locname; }else{ $lcode = $lcode."','".$locname; }
		}
	}
	else{
		$fromdate = $todate = date("Y-m-d");
	}
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Profile</a></li>
				<li class="active">Authorize</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<form action="main_addauthorization.php" method="post" role="form" onsubmit="return checkval1()">
								<div class="col-md-12">
									<div class="form-group col-md-2">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="form-control" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									</div>
									<div class="form-group col-md-2">;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="form-control" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
										
									</div>
									<div class="form-group col-md-4">
										<label>Location Access<b style="color:red;">&nbsp;*</b></label>
										<select name="lname[]" id="lname[]" multiple class="form-control select">
											<?php
												if($utype = "S" || $utype = "A" || $whcode == "all"){
													echo '<option value="all" selected >All</option>';
												}
												foreach($sec_code as $scode){
											?>
												<option value="<?php echo $sec_code[$scode]; ?>" <?php if($loc_code[$scode] == $sec_code[$scode]){ echo 'selected'; } ?>><?php echo $sec_name[$scode]; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-2">
										<label>Authorize<b style="color:red;">&nbsp;*</b></label>
										<select name="aut_dlt" id="aut_dlt" class="form-control select2">
											<?php
												foreach($aut_sec as $acode){
											?>
												<option value="<?php echo $acode; ?>" <?php if($_POST['aut_dlt'] == $acode){ echo 'selected'; } ?>><?php echo $acode; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>ECount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount1" id="ebtncount1" value="0">
									</div>
								</div>
								<div class="box-body" align="center">
									<button type="submit" name="submittrans1" id="submittrans1" value="fetchdetails" class="btn btn-flat btn-social btn-linkedin">
										<i class="fa fa-save"></i> Fetch Details
									</button>&ensp;&ensp;&ensp;&ensp;
									<button type="button" name="cancelled1" id="cancelled1" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
										<i class="fa fa-trash"></i> Cancel
									</button>
								</div>
							</form>
						</div>
						<?php if(isset($_POST['submittrans1']) == true){ ?>
						<div class="col-md-12">
							<form action="main_updateauthorization.php" method="post" role="form" onsubmit="return checkval()">
								<?php if($_POST['aut_dlt'] == "Receipt"){ ?>
								<div class="col-md-12">
									<table class="table">
										<thead>
											<tr>
												<th>select<br/><input type="checkbox" name="ckall" id="ckall" onchange="checkall(this.id)" /></th>
												<th>Date</th>
												<th>Trnum</th>
												<th>Name</th>
												<th>Method</th>
												<th>Amount</th>
											</tr>
										</thead>
										<tbody>
										<?php
											if($wh_all == 1){ $whs_codes = ""; foreach($sec_code as $gcode){ if($whs_codes ==""){ $whs_codes = $gcode; } else{ $whs_codes = $whs_codes."','".$gcode; } } }
											else{ $whs_codes = $lcode; }
											$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `warehouse` IN ('$whs_codes') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
											$query = mysqli_query($conn,$sql); $c = 0; $inv_no = array();
											while($row = mysqli_fetch_assoc($query)){
												$c++;
												if($c <= 10){
													$inv_no[$row['trnum']] = $row['trnum'];
												}
											}
											$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){ $coa_name[$row['code']] = $row['description']; }
												
											$inv_list = implode("','",$inv_no);
											$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `trnum` IN ('$inv_list') AND `warehouse` IN ('$whs_codes') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql); $c = 0;
											while($row = mysqli_fetch_assoc($query)){ $c = $c + 1;
												$trnum = $row['date']."@".$row['trnum']."@".$row['ccode']."@".$row['docno']."@@@@@".$row['amount']."@".$row['remarks']."@Receipt";
												echo "<tr>";
												echo "<td><input type='checkbox' name='trnums[]' id='trnums[$c]' value='$trnum' /></td>";
												echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
												echo "<td>".$row['trnum']."</td>";
												echo "<td>".$cus_name[$row['ccode']]."</td>";
												echo "<td>".$coa_name[$row['method']]."</td>";
												echo "<td>".$row['amount']."</td>";
												echo "</tr>";
											}
										?>
										</tbody>
									</table>
								</div>
								<?php }
								else if($_POST['aut_dlt'] == "Sale"){ ?>
								<div class="col-md-12">
									<table class="table">
										<thead>
											<tr>
												<th>select<br/><input type="checkbox" name="ckall" id="ckall" onchange="checkall(this.id)" /></th>
												<th>Date</th>
												<th>Trnum</th>
												<th>Name</th>
												<th>Item</th>
												<th>Quantity</th>
												<th>Price</th>
												<th>Amount</th>
												<th>Final Amount</th>
											</tr>
										</thead>
										<tbody>
										<?php
											if($wh_all == 1){ $whs_codes = ""; foreach($sec_code as $gcode){ if($whs_codes ==""){ $whs_codes = $gcode; } else{ $whs_codes = $whs_codes."','".$gcode; } } }
											else{ $whs_codes = $lcode; }
											$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `warehouse` IN ('$whs_codes') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
											$query = mysqli_query($conn,$sql); $c = 0; $inv_no = $inv_iname = $inv_qty = $inv_prc = $inv_iamt = array();
											while($row = mysqli_fetch_assoc($query)){
												$c++;
												if($c <= 10){
													if(empty($inv_iname[$row['invoice']])){ $inv_iname[$row['invoice']] = $item_name[$row['itemcode']]; }
													else{ $inv_iname[$row['invoice']] .= "<br/>".$item_name[$row['itemcode']]; }

													if(empty($inv_qty[$row['invoice']])){ $inv_qty[$row['invoice']] = number_format_ind(round($row['netweight'],2)); }
													else{ $inv_qty[$row['invoice']] .= "<br/>".number_format_ind(round($row['netweight'],2)); }

													if(empty($inv_prc[$row['invoice']])){ $inv_prc[$row['invoice']] = number_format_ind(round($row['itemprice'],2)); }
													else{ $inv_prc[$row['invoice']] .= "<br/>".number_format_ind(round($row['itemprice'],2)); }

													if(empty($inv_iamt[$row['invoice']])){ $inv_iamt[$row['invoice']] = number_format_ind(round($row['totalamt'],2)); }
													else{ $inv_iamt[$row['invoice']] .= "<br/>".number_format_ind(round($row['totalamt'],2)); }
													$inv_no[$row['invoice']] = $row['invoice'];
												}
											}
											$inv_list = implode("','",$inv_no);
											$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `invoice` IN ('$inv_list') AND `warehouse` IN ('$whs_codes') AND `active` = '0' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `invoice` ORDER BY `date`,`invoice` ASC";
											$query = mysqli_query($conn,$sql); $c = 0;
											while($row = mysqli_fetch_assoc($query)){ $c = $c + 1;
												$trnum = $row['date']."@".$row['invoice']."@".$row['customercode']."@".$row['bookinvoice']."@".$row['itemcode']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['finaltotal']."@".$row['remarks']."@Sale";
												echo "<tr>";
												echo "<td><input type='checkbox' name='trnums[]' id='trnums[$c]' value='$trnum' /></td>";
												echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
												echo "<td>".$row['invoice']."</td>";
												echo "<td>".$cus_name[$row['customercode']]."</td>";
												echo "<td>".$inv_iname[$row['invoice']]."</td>";
												echo "<td>".$inv_qty[$row['invoice']]."</td>";
												echo "<td>".$inv_prc[$row['invoice']]."</td>";
												echo "<td>".$inv_iamt[$row['invoice']]."</td>";
												echo "<td>".number_format_ind(round($row['finaltotal'],2))."</td>";
												echo "</tr>";
											}
											echo $_SERVER['REMOTE_ADDR'];
										?>
										</tbody>
									</table>
								</div>
								<?php } ?>
								<div class="col-md-12">
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>ECount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>Aut Type<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="aut_type" id="aut_type" value="<?php echo $_POST['aut_dlt']; ?>" readonly >
									</div>
								</div>
								<div class="box-body" align="center">
									<button type="submit" name="submittrans" id="submittrans" value="authorizerct" class="btn btn-flat btn-social btn-linkedin">
										<i class="fa fa-save"></i> Authorize
									</button>&ensp;&ensp;&ensp;&ensp;
									<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
										<i class="fa fa-trash"></i> Cancel
									</button>
								</div>
							</form>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</section>
		<script>
			function checkval1(){
				document.getElementById("ebtncount1").value = "1"; document.getElementById("submittrans1").style.visibility = "hidden";
				var l = true;
				if(l == true){
					return true;
				}
				else{
                    document.getElementById("submittrans1").style.visibility = "visible";
					document.getElementById("ebtncount1").value = "0";
					return false;
				}
			}
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var l = true;
				var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
				if(checkboxes.length == 0){
					alert("Please select atleast one transaction for approval");
					c = 0;
				}
				else {
					c = checkboxes.length;
				}
				if(c > 0){
					l = true;
				}
				else {
					l = false;
				}
				if(l == true){
					return true;
				}
				else{
                    document.getElementById("submittrans").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
				}
			}
			function redirection_page(){
				window.location.href = "main_displayauthorization.php";
			}
			function validatename(x) {
				expr = /^[a-zA-Z0-9 (.&)_-]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function checkall(a){
				var c = document.getElementById(a).value;
				var selectallbox = document.getElementById(a);
				var checkboxes = document.querySelectorAll('input[type="checkbox"]');
				for (var i = 0; i < checkboxes.length; i++) {
					if(selectallbox.checked == true){
						checkboxes[i].checked = true;
						//alert(i);
					}
					else{
						checkboxes[i].checked = false;
					}
				}
			}
			document.addEventListener("keydown", (e) => {
                if (e.key === "Enter"){
					var ebtncount1 = document.getElementById("ebtncount1").value;
					var ebtncount = document.getElementById("ebtncount").value;
					if(ebtncount > 0 || ebtncount1 > 0){
						event.preventDefault();
					}
					else{
						$(":submit").click(function () {
							$('#submittrans').click();
						});
					}
					if(ebtncount > 0 || ebtncount1 > 0){
						event.preventDefault();
					}
					else{
						$(":submit").click(function () {
							$('#submittrans1').click();
						});
					}
                }
                else{ }
            });
		</script>
		<?php include "header_foot.php"; ?>
	</body>
</html>

