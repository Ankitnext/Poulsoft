<?php
    //cus_edit_multisales1.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$emp_code = $_SESSION['userid'];
	$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access']; }
	if($loc_access == "all" || $loc_access == "" || $loc_access == NULL){
		$warehouse_codes = "";
	}
	else{
		$whs_code = "";
		$crp_codes = explode(",",$loc_access);
		foreach($crp_codes as $whs){
			if($whs_code == ""){
				$whs_code = $whs;
			}
			else{
				$whs_code = $whs_code."','".$whs;
			}
		}
		if($whs_code != ""){
			$warehouse_codes = " AND `code` IN ('$whs_code')";
		}
		else{
			$warehouse_codes = "";
		}
	}
	if($cgroup_access == "all" || $cgroup_access == "" || $cgroup_access == NULL){
		$cgroup_codes = "";
	}
	else{
		$crp_code = "";
		$crp_codes = explode(",",$cgroup_access);
		foreach($crp_codes as $cgrps){
			if($crp_code == ""){
				$crp_code = $cgrps;
			}
			else{
				$crp_code = $crp_code."','".$cgrps;
			}
		}
		if($crp_code != ""){
			$cgroup_codes = " AND `groupcode` IN ('$crp_code')";
		}
		else{
			$cgroup_codes = "";
		}
	}
	
?>
<html>
	<head>
		<style>
			.select2-container .select2-selection--single{ box-sizing:border-box; cursor:pointer; display:block; height:23px; user-select:none; -webkit-user-select:none; }
			.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
			.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
			.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
			.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
			.select2-container--default .select2-selection--single .select2-selection__arrow{height:23px;position:absolute;top:1px;right:1px;width:20px}
			.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
			.form-control { width: 85%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Edit Sales Invoice</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Sales</a></li>
				<li class="active">Display</li>
				<li class="active">Edit</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$item_code[$row['code']] = $row['code'];
					$item_name[$row['code']] = $row['description'];
					$itype[$row['code']] = $row['code'];
					$itypes[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Warehouse"){ if($branches == ""){ $branches = $row['code']; } else{ $branches = $branches."','".$row['code']; } } }
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_codes." AND `type` IN ('$branches') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
			?>
			<?php
				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$spzflag = $row['spzflag'];
					$ifwt = $row['wt'];
					$ifbw = $row['bw'];
					$ifjbw = $row['jbw'];
					$ifjbwen = $row['jbwen'];
					$ifctype = $row['ctype'];
				}
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }
				$idisplay = ''; $ndisplay = 'style="display:none;';
				$id = $_GET['id'];
				$sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
				while($row = mysqli_fetch_assoc($query)){
					$sdate = $row['date'];
					$sinv = $row['invoice'];
					$sbinv = $row['bookinvoice'];
					$sccode = $row['customercode'];
					$sdcode = $row['drivercode'];
					$tcdsper = $row['tcdsper'];
					$tcdsamt = $row['tcdsamt'];
					$sbval = $row['finaltotal'];
					$svcode = $row['vehiclecode'];
					$amtinwords = $row['amtinwords'];
					$srcode = $row['remarks'];
					$stcode = $row['addedemp']."@".$row['addedtime'];
				}
				$today = date("Y-m-d");
				$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$today' AND `tdate` >= '$today' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
				$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcdsper = $row['tcds']; }
				$sql1 = "SELECT * FROM `customer_sales` WHERE `invoice` = '$id' ORDER BY `id` ASC"; $query1 = mysqli_query($conn,$sql1);
				//echo $tcdsamt;
			?>
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="cus_modify_multisales1.php" method="post" role="form" onsubmit="return checkval()">
									<div class="form-group col-md-1">
										<label>Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo date("d.m.Y",strtotime($sdate)); ?>" id="slc_datepickers" onchange="fetchtds()" readonly>
									</div>
									<div class="form-group col-md-1">
										<label>Invoice</label>
									<input type="text" name="inv" id="inv" style="width:auto;background:none;border:none;text-decoration:none;" class="form-control" value="<?php echo $sinv; ?>"placeholder="Enter Location..." readonly>
									</div>
									<div class="form-group col-md-3">&ensp;&ensp;
										<label>Customer<b style="color:red;">&nbsp;*</b></label>
										<select name="pname" id="pname" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php
												echo $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cgroup_codes." AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($sccode == $row['code']){ echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-2">
										<label>Book Invoice</label>
									<input type="text" name="binv" id="binv" style="" class="form-control" value="<?php echo $sbinv; ?>" placeholder="Enter Book Invoice...">
									</div>
									<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:visible;"-->
										<label>incr<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="<?php echo $ccount-1; ?>">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incrs<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="<?php echo $ccount-1; ?>">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>Enter Count<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									<div class="col-md-12">
										<table style="width:100%;line-height:30px;" id="tab3">
											<tr style="line-height:30px;">
												<th><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
												<?php
													if($ifjbwen == 1 || $ifjbw == 1){
														echo "<th><label>Jals</label></th>";
													}
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
														echo "<th><label>Birds</label></th>";
													}
													if($ifjbwen == 1){
														echo "<th><label>T. Weight<b style='color:red;'>&nbsp;*</b></label></th>";
														echo "<th><label>E. Weight</label></th>";
													}
												?>
												<th><label>N. Weight<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th style='display:none;'><label>Discount</label></th>
												<th style='display:none;'><label>Tax</label></th>
												<th><label>Total Amount</label></th>
												<th><label>Warehouse<b style="color:red;">&nbsp;*</b></label></th>
												<th></th>
											</tr>
												<?php
												$c = 0;
													while($row1 = mysqli_fetch_assoc($query1)){
														$dcode = $row1['itemcode']."@".$item_name[$row1['itemcode']];
												?>
												<tr style="margin:5px 0px 5px 0px;" id="row_id[<?php echo $c; ?>]">
													<td style="width: 180px;padding-right:30px;"><select name="scat[]" id="scat[<?php echo $c; ?>]" class="form-control select2" style="width: 100%;" onchange="fetchprice(this.id);"><?php foreach($item_code as $ic){ $scode = $item_code[$ic]."@".$item_name[$ic]; ?><option <?php if($scode == $dcode) { echo 'selected'; } ?> value="<?php echo $scode; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>
													<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval[<?php echo $c; ?>]" value="<?php echo $row1['jals']; ?>" class="form-control" onchange="validatenum(this.id);calculatetotal(this.id);" /></td>
													<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval[<?php echo $c; ?>]" value="<?php echo $row1['birds']; ?>" class="form-control" onchange="validatenum(this.id);calculatetotal(this.id);" /></td>
													<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval[<?php echo $c; ?>]" value="<?php echo $row1['totalweight']; ?>" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>
													<!--<td><input type="text" name="twval[]" id="twval[<?php echo $c; ?>]" value="<?php echo $c; ?>" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>-->
													<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval[<?php echo $c; ?>]" value="<?php echo $row1['emptyweight']; ?>" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>
													<td><input type="text" name="nwval[]" id="nwval[<?php echo $c; ?>]" value="<?php echo $row1['netweight']; ?>" class="form-control" onchange="validateamount(this.id);" onkeyup="calculatetotal(this.id);" /></td>
													<td><input type="text" name="iprice[]" id="iprice[<?php echo $c; ?>]" class="form-control" value="<?php echo $row1['itemprice']; ?>"  onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);"></td>
													<td style='display:none;'><input type="text" name="idisc[]" id="idisc[<?php echo $c; ?>]" style="" class="form-control" value="<?php echo $row1['discountamt']; ?>" onchange="validateamount(this.id);calculatetotal(this.id);"></td>
													<td style='display:none;'><input type="text" name="itax[]" id="itax[<?php echo $c; ?>]" style="" class="form-control" value="<?php echo $row1['taxamount']; ?>" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);"></td>
													<td><input type="text" name="tamt[]" id="tamt[<?php echo $c; ?>]" class="form-control" value="<?php echo $row1['totalamt']; ?>" onchange="validateamount(this.id);" readonly></td>
													<td style="width: 150px;padding-right:30px;"><select name="wcodes[]" id="wcodes[<?php echo $c; ?>]" class="form-control select2" style="width: 100%;"><?php foreach($wcode as $it){ ?><option <?php if($row1['warehouse'] == $wcode[$it]) { echo 'selected'; } ?> value="<?php echo $wcode[$it]; ?>"><?php echo $wdesc[$it]; ?></option><?php } ?></select></td>
													<td <?php if($ccount == $c+1) { echo 'style="width: 60px;visibility:visible;"'; } else { echo 'style="width: 60px;visibility:hidden;"'; } ?>><a href="JavaScript:Void(0);" name="addval[]" id="addval[<?php echo $c; ?>]" title="<?php echo $c; ?>" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[<?php echo $c; ?>]" class="delete" onclick="rowdes(this.id)" <?php if($ccount == $c+1) { echo 'style="visibility:visible;"'; } else { echo 'style="visibility:hidden;"'; } ?>><i class="fa fa-minus" style="color:red;"></i></a></td>
												</tr>
												<?php
														$c = $c + 1;
														echo "<script> calculatetotal(this.id); </script>";
													}
												?>
										</table>
										<div class="col-md-12" align="left">
											<div class="col-md-4">
												<input type="checkbox" name="tds" id="tds" style="width:auto;" onchange="caltds()" <?php if($tcdsamt > 0 || $tcdsamt != "0" || $tcdsamt != "0.00"){ echo "checked"; } ?> >
												<label>TCS</label>
												<input type="text" name="tdsamt" id="tdsamt" class="form-control" style="width:auto;" value="<?php echo $tcdsamt; ?>">
											</div>
											<div class="col-md-4" style="visibility: hidden;">
												<label>TCS</label>
												<input type="text" name="tdsperval" id="tdsperval" class="form-control" style="width:auto;" value="<?php echo $tcdsper; ?>">
											</div>
										</div>
										<div class="col-md-12" align="left">
											<div class="col-md-4">
												<label>Vehicle No.</label>
												<input type="text" name="vno" id="vno" class="form-control" value="<?php echo $svcode; ?>" style="width:auto;" >
											</div>
											<div class="col-md-4">
												<label>Driver</label>
												<input type="text" name="dname" id="dname" class="form-control" value="<?php echo $sdcode; ?>" style="width:auto;" >
											</div>
											<div class="col-md-4">
												<label>Billing Amount</label>
												<input type="text" name="gtamt" id="gtamt" class="form-control" value="<?php echo $sbval; ?>" style="width:auto;" readonly>
											</div>
										</div>
										<div class="col-md-12" align="left">
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Item Field Type</label>
												<input type="text" name="itemfields" id="itemfields" class="form-control" value="<?php if($ifwt == 1){ echo "WT"; } else if($ifbw == 1){ echo "BAW"; } else if($ifjbw == 1){ echo "JBEW"; } else if($ifjbwen == 1){ echo "JBTEN"; } else { echo "WT"; } ?>" >
											</div>
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Amount Based</label>
												<input type="text" name="amountbasedon" id="amountbasedon" class="form-control" value="<?php echo $ifctype; ?>" >
											</div>
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Edited By</label>
												<input type="text" name="addedempdetails" id="addedempdetails" class="form-control" value="<?php echo $stcode; ?>" >
											</div>
										</div>
										<div class="col-md-16" align="center">
											<label>Remarks</label>
											<textarea name="narr" id="narr" class="form-control" style="width:210px;" ><?php echo $srcode; ?></textarea>
										</div><br/><br/>
										<div class="box-body" align="center">
											<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-flat btn-social btn-linkedin">
												<i class="fa fa-save"></i> Update
											</button>&ensp;&ensp;&ensp;&ensp;
											<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
												<i class="fa fa-trash"></i> Cancel
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include "header_foot.php"; ?>
		<script>
			function caltds(){
				if(document.getElementById("tds").checked == true){
					var a = document.getElementById("incrs").value;
					var b = document.getElementById("tdsperval").value;
					var c = document.getElementById("gtamt").value;
					var d = b/100;
					var e = (c * d).toFixed(2);
					var f = parseFloat(c) + parseFloat(e);
					document.getElementById("tdsamt").value = e;
					document.getElementById("gtamt").value = f;
				}
				else {
					document.getElementById("tdsamt").value = "";
					calculatetotal("scat["+a+"]");
				}
			}
			function fetchtds(){
				var a = document.getElementById("slc_datepickers").value;
				var tdsper = new XMLHttpRequest();
				var method = "GET";
				var url = "main_gettcdsvalue.php?type=TDS&cdate="+a;
				var asynchronous = true;
				tdsper.open(method, url, asynchronous);
				tdsper.send();
				tdsper.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var b = this.responseText;
						if(b == ""){
							//alert("TDS not defined in masters \n Kindly check TDS masters ..!");
						}
						else {
							document.getElementById("tdsperval").value = b;
						}
					}
				}
			}
			function checkval(){
				var a = document.getElementById("itemfields").value;
				var b = document.getElementById("incr").value;
				document.getElementById("ebtncount").value = "1";
				document.getElementById("submittrans").style.visibility = "hidden";
				var sale_price_flag = '<?php echo $spzflag; ?>';
				var l = true;
					if(a.match("WT")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								var g = document.getElementById("nwval["+j+"]").value;
								var h = document.getElementById("iprice["+j+"]").value;
								var i = document.getElementById("wcodes["+j+"]").value;
								var n = document.getElementById("pname").value;
								if(n.match("select")){
									alert("Please select Name in row: "+k);
									l = false;
								}
								else if(c.match("select")){
									alert("Please select Item description in row: "+k);
									l = false;
								}
								else if(g.length == 0 || g == 0 || g == ""){
									alert("Please Enter the net weight in row: "+k);
									l = false;
								}
								else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
									alert("Please Enter the price in row: "+k);
									l = false;
								}
								else if(i.match("select") || i.length == 0 || i == ""){
									alert("Please select warehouse in row: "+k);
									l = false;
								}
								else {
									l = true;
								}
							}
							else {
								l = false;
							}
						}
					}
					else if(a.match("BAW")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								//var d = document.getElementById("bval["+j+"]").value;
								var g = document.getElementById("nwval["+j+"]").value;
								var h = document.getElementById("iprice["+j+"]").value;
								var i = document.getElementById("wcodes["+j+"]").value;
								var m = c.search(/Birds/i);
								if(m > 0){
									var n = document.getElementById("pname").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										l = false;
									}
									else if(c.match("select")){
											alert("Please select Item description in row: "+k);
										l = false;
									}
									//else if(d.length == 0 || d == 0 || d == ""){
										//alert("Please select Total birds in row: "+k);
										//l = false;
									//}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										l = false;
									}
									else if(i.match("select") || i.length == 0 || i == ""){
										alert("Please select warehouse in row: "+k);
										l = false;
									}
									else {
										l = true;
									}
								}
								else {
									var n = document.getElementById("pname").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										l = false;
									}
									else if(i.match("select") || i.length == 0 || i == ""){
										alert("Please select warehouse in row: "+k);
										l = false;
									}
									else {
										l = true;
									}
								}
							}
							else {
								l = false;
							}
						}
					}
					else if(a.match("JBEW")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								//var d = document.getElementById("bval["+j+"]").value;
								//var e = document.getElementById("jval["+j+"]").value;
								var g = document.getElementById("nwval["+j+"]").value;
								var h = document.getElementById("iprice["+j+"]").value;
								var i = document.getElementById("wcodes["+j+"]").value;
								var m = c.search(/Birds/i);
								if(m > 0){
									var n = document.getElementById("pname").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										l = false;
									}
									/*else if(e.length == 0 || e == 0 || e == ""){
										alert("Please select No. of Jals in row: "+k);
										l = false;
									}*/
									//else if(d.length == 0 || d == 0 || d == ""){
										//alert("Please select Total birds in row: "+k);
										//l = false;
									//}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										l = false;
									}
									else if(i.match("select") || i.length == 0 || i == ""){
										alert("Please select warehouse in row: "+k);
										l = false;
									}
									else {
										l = true;
									}
								}
								else {
									var n = document.getElementById("pname").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										l = false;
									}
									else if(i.match("select") || i.length == 0 || i == ""){
										alert("Please select warehouse in row: "+k);
										l = false;
									}
									else {
										l = true;
									}
								}
							}
							else {
								l = false;
							}
						}
					}
					else if(a.match("JBTEN")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								//var d = document.getElementById("bval["+j+"]").value;
								//var e = document.getElementById("jval["+j+"]").value;
								var f = document.getElementById("wval["+j+"]").value;
								//var p = document.getElementById("ewval["+j+"]").value;
								var g = document.getElementById("nwval["+j+"]").value;
								var h = document.getElementById("iprice["+j+"]").value;
								var i = document.getElementById("wcodes["+j+"]").value;
								var m = c.search(/Birds/i);
								if(m > 0){
									var n = document.getElementById("pname").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										l = false;
									}
									/*else if(e.length == 0 || e == 0 || e == ""){
										alert("Please select No. of Jals in row: "+k);
										l = false;
									}*/
									//else if(d.length == 0 || d == 0 || d == ""){
										//alert("Please select Total birds in row: "+k);
										///l = false;
									//}
									else if(f.length == 0 || f == 0 || f == ""){
										alert("Please Enter the Total weight in row: "+k);
										l = false;
									}
									/*else if(p.length == 0 || p == 0 || p == ""){
										alert("Please Enter the Empty weight in row: "+k);
										l = false;
									}*/
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										l = false;
									}
									else if(i.match("select") || i.length == 0 || i == ""){
										alert("Please select warehouse in row: "+k);
										l = false;
									}
									else {
										l = true;
									}
								}
								else {
									var n = document.getElementById("pname").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										l = false;
									}
									else if(i.match("select") || i.length == 0 || i == ""){
										alert("Please select warehouse in row: "+k);
										l = false;
									}
									else {
										l = true;
									}
								}
							}
							else {
								l = false;
							}
						}
					}
					else {
						return false;
					}
				if(l == true){
					//document.getElementById("submittrans").disabled = true;
					return true;
				}
				else if(l == false){
					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
				else {
					document.getElementById("ebtncount").value = "0";
					alert("Invalid");
					return false;
				}
			}
			function redirection_page(){
				window.location.href = "cus_displaymultisales.php";
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
			function chktype(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("stype["+a+"]").value;
				if(b.match("Birds")){
					document.getElementById("bval["+a+"]").value = '1';
					document.getElementById("wval["+a+"]").value = '1';
				}
				else {
					document.getElementById("qval["+c+"]").value = '1';
				}
			}
			function rowgen(a){
                var b = a.split("["); var d = b[1].split("]"); var c = d[0];
                if(parseInt(c) == 0){
                    document.getElementById("addval["+c+"]").style.visibility = "hidden";
                }
                else{
                    document.getElementById("addval["+c+"]").style.visibility = "hidden";
                    document.getElementById("rmval["+c+"]").style.visibility = "hidden";
                }
				c++;
                document.getElementById("incrs").value = c;
				var html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;" id="row_id['+c+']">';
				html+= '<td style="width: 180px;padding-right:30px;"><select name="scat[]" id="scat['+c+']" class="form-control select" style="width: 100%;" onchange="fetchprice(this.id);"><?php foreach($itype as $ic){ ?><option value="<?php echo $itype[$ic]."@".$itypes[$ic]; ?>"><?php echo $itypes[$ic]; ?></option><?php } ?></select></td>';
				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval['+c+']" value="0" class="form-control" onchange="validatenum(this.id);calculatetotal(this.id);" /></td>';
				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval['+c+']" value="0" class="form-control" onchange="validatenum();calculatetotal(this.id);" /></td>';
				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval['+c+']" value="0" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>';
				//html+= '<td><input type="text" name="twval[]" id="twval['+c+']" value="0" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>';
				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval['+c+']" value="0" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>';
				html+= '<td><input type="text" name="nwval[]" id="nwval['+c+']" value="0" class="form-control" onchange="validateamount(this.id);" onkeyup="calculatetotal(this.id);" /></td>';
				html+= '<td><input type="text" name="iprice[]" id="iprice['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);"></td>';
				html+= '<td style="display:none;"><input type="text" name="idisc[]" style="" id="idisc['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);"></td>';
				html+= '<td style="display:none;"><input type="text" name="itax[]" style="" id="itax['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);"></td>';
				html+= '<td><input type="text" name="tamt[]" id="tamt['+c+']" class="form-control" onchange="validateamount(this.id);" readonly></td>';
				html+= '<td style="width: 150px;padding-right:30px;"><select name="wcodes[]" id="wcodes['+c+']" class="form-control select" style="width: 100%;"><?php foreach($wcode as $it){ ?><option value="<?php echo $wcode[$it]; ?>"><?php echo $wdesc[$it]; ?></option><?php } ?></select></td>';
				html+= '<td style="width: 60px;"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+c+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes(this.id)" title="'+c+'"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html += '</tr>';
				$('#tab3 tbody').append(html);
				var row = $('#row_cnt').val();
				$('#row_cnt').val(parseInt(row) + parseInt(1));
				var newtrlen = $('#tab3 tbody tr').length;
				if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); }
				document.getElementById("incr").value = c; $('.select').select2()
			}
			//$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
            document.addEventListener("keydown", (e) => {
                var key_search = document.activeElement.id.includes("[");
                if(key_search == true){
                    var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0];
                    //alert(e.key+"==="+document.activeElement.id+"==="+key_search+"==="+d);
                    document.getElementById("incrs").value = d;
                }
                /*if (e.key === "Tab"){ } else{ }*/
                if (e.key === "Enter"){
                    //alert(e.key+"==="+document.activeElement.id+"==="+key_search);
					var ebtncount = document.getElementById("ebtncount").value;
					if(ebtncount > 0){
						event.preventDefault();
					}
					else{
						$(":submit").click(function () {
							$('#submittrans').click();
						});
					}
                }
                else{ }
				
            });
            function rowdes(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_id["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
				if(d > 0){
					document.getElementById("rmval["+d+"]").style.visibility = "visible";
				}
				else {
					document.getElementById("rmval["+d+"]").style.visibility = "hidden";
				}
				document.getElementById("addval["+d+"]").style.visibility = "visible";
				calculatetotal("addval["+d+"]");
            }
			function calculatetotal(aa){
                var ab = aa.split("["); var ad = ab[1].split("]"); var ac = ad[0];
				var a = ac;
				var r = document.getElementById("itemfields").value;
				var b = document.getElementById("scat["+a+"]").value;
				var c = b.split("@");
				var d = c[1].search(/Birds/i);
				if(r.match("WT")){
					var s = document.getElementById("incr").value;
					var g = document.getElementById("nwval["+a+"]").value;
					var h = document.getElementById("iprice["+a+"]").value;
					var i = g * h;
					var m = document.getElementById("idisc["+a+"]").value;
					if(m == "" || m == "0.00"){ m = 0; }
					var n = i - m;
					var p = document.getElementById("itax["+a+"]").value;
					if(p == "" || p == "0.00"){ p = 0; }
					var q = parseFloat(n) + parseFloat(p);
					document.getElementById("tamt["+a+"]").value = q.toFixed(2);
					k = l = 0;
					for(var j=0;j<=s;j++){
						k = document.getElementById("tamt["+j+"]").value;
						l = parseFloat(l) + parseFloat(k);
					}
					document.getElementById("gtamt").value = l.toFixed(2);
				}
				else if(r.match("BAW")){
					if(d > 0){
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						var s = document.getElementById("incr").value;
						var t = document.getElementById("amountbasedon").value;
						if(t.match("B") || t.match("b")){
							var g = document.getElementById("bval["+a+"]").value;
						}
						else {
							var g = document.getElementById("nwval["+a+"]").value;
						}
						var h = document.getElementById("iprice["+a+"]").value;
						var i = g * h;
						var m = document.getElementById("idisc["+a+"]").value;
						if(m == "" || m == "0.00"){ m = 0; }
						var n = i - m;
						var p = document.getElementById("itax["+a+"]").value;
						if(p == "" || p == "0.00"){ p = 0; }
						var q = parseFloat(n) + parseFloat(p);
						document.getElementById("tamt["+a+"]").value = q.toFixed(2);
						k = l = 0;
						for(var j=0;j<=s;j++){
							k = document.getElementById("tamt["+j+"]").value;
							l = parseFloat(l) + parseFloat(k);
						}
						document.getElementById("gtamt").value = l.toFixed(2);
					}
					else {
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						var s = document.getElementById("incr").value;
						var g = document.getElementById("nwval["+a+"]").value;
						var h = document.getElementById("iprice["+a+"]").value;
						var i = g * h;
						var m = document.getElementById("idisc["+a+"]").value;
						if(m == "" || m == "0.00"){ m = 0; }
						var n = i - m;
						var p = document.getElementById("itax["+a+"]").value;
						if(p == "" || p == "0.00"){ p = 0; }
						var q = parseFloat(n) + parseFloat(p);
						document.getElementById("tamt["+a+"]").value = q.toFixed(2);
						k = l = 0;
						for(var j=0;j<=s;j++){
							k = document.getElementById("tamt["+j+"]").value;
							l = parseFloat(l) + parseFloat(k);
						}
						document.getElementById("gtamt").value = l.toFixed(2);
					}
				}
				else if(r.match("JBEW")){
					if(d > 0){
						document.getElementById("jval["+a+"]").style.visibility = "visible";
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						
						var s = document.getElementById("incr").value;
						var t = document.getElementById("amountbasedon").value;
						if(t.match("B") || t.match("b")){
							var g = document.getElementById("bval["+a+"]").value;
						}
						else {
							var g = document.getElementById("nwval["+a+"]").value;
						}
						
						var h = document.getElementById("iprice["+a+"]").value;
						var i = g * h;
						var m = document.getElementById("idisc["+a+"]").value;
						if(m == "" || m == "0.00"){ m = 0; }
						var n = i - m;
						var p = document.getElementById("itax["+a+"]").value;
						if(p == "" || p == "0.00"){ p = 0; }
						var q = parseFloat(n) + parseFloat(p);
						document.getElementById("tamt["+a+"]").value = q.toFixed(2);
						k = l = 0;
						for(var j=0;j<=s;j++){
							k = document.getElementById("tamt["+j+"]").value;
							l = parseFloat(l) + parseFloat(k);
						}
						document.getElementById("gtamt").value = l.toFixed(2);
					}
					else {
						document.getElementById("jval["+a+"]").style.visibility = "hidden";
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						
						var s = document.getElementById("incr").value;
						var g = document.getElementById("nwval["+a+"]").value;
						var h = document.getElementById("iprice["+a+"]").value;
						var i = g * h;
						var m = document.getElementById("idisc["+a+"]").value;
						if(m == "" || m == "0.00"){ m = 0; }
						var n = i - m;
						var p = document.getElementById("itax["+a+"]").value;
						if(p == "" || p == "0.00"){ p = 0; }
						var q = parseFloat(n) + parseFloat(p);
						document.getElementById("tamt["+a+"]").value = q.toFixed(2);
						k = l = 0;
						for(var j=0;j<=s;j++){
							k = document.getElementById("tamt["+j+"]").value;
							l = parseFloat(l) + parseFloat(k);
						}
						document.getElementById("gtamt").value = l.toFixed(2);
					}
				}
				else if(r.match("JBTEN")){
					if(d > 0){
						document.getElementById("jval["+a+"]").style.visibility = "visible";
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						document.getElementById("wval["+a+"]").style.visibility = "visible";
						document.getElementById("ewval["+a+"]").style.visibility = "visible";
						var s = document.getElementById("incr").value;
						var e = document.getElementById("wval["+a+"]").value;
						var f = document.getElementById("ewval["+a+"]").value;
						var g = e - f; var g = parseFloat(g).toFixed(2);
						document.getElementById("nwval["+a+"]").readOnly = true;
						document.getElementById("nwval["+a+"]").value = g;
						var h = document.getElementById("iprice["+a+"]").value;
						var i = g * h;
						var m = document.getElementById("idisc["+a+"]").value;
						if(m == "" || m == "0.00"){ m = 0; }
						var n = i - m;
						var p = document.getElementById("itax["+a+"]").value;
						if(p == "" || p == "0.00"){ p = 0; }
						var q = parseFloat(n) + parseFloat(p);
						document.getElementById("tamt["+a+"]").value = q.toFixed(2);
						k = l = 0;
						for(var j=0;j<=s;j++){
							k = document.getElementById("tamt["+j+"]").value;
							l = parseFloat(l) + parseFloat(k);
						}
						document.getElementById("gtamt").value = l.toFixed(2);
					}
					else {
						document.getElementById("jval["+a+"]").style.visibility = "hidden";
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						document.getElementById("wval["+a+"]").style.visibility = "hidden";
						document.getElementById("ewval["+a+"]").style.visibility = "hidden";
						document.getElementById("nwval["+a+"]").readOnly = false;
						
						var s = document.getElementById("incr").value;
						var g = document.getElementById("nwval["+a+"]").value;
						var h = document.getElementById("iprice["+a+"]").value;
						var i = g * h;
						var m = document.getElementById("idisc["+a+"]").value;
						if(m == "" || m == "0.00"){ m = 0; }
						var n = i - m;
						var p = document.getElementById("itax["+a+"]").value;
						if(p == "" || p == "0.00"){ p = 0; }
						var q = parseFloat(n) + parseFloat(p);
						document.getElementById("tamt["+a+"]").value = q.toFixed(2);
						k = l = 0;
						for(var j=0;j<=s;j++){
							k = document.getElementById("tamt["+j+"]").value;
							l = parseFloat(l) + parseFloat(k);
						}
						document.getElementById("gtamt").value = l.toFixed(2);
					}
				}
				else {
					var s = document.getElementById("incr").value;
					var g = document.getElementById("nwval["+a+"]").value;
					var h = document.getElementById("iprice["+a+"]").value;
					var i = g * h;
					var m = document.getElementById("idisc["+a+"]").value;
					if(m == "" || m == "0.00"){ m = 0; }
					var n = i - m;
					var p = document.getElementById("itax["+a+"]").value;
					if(p == "" || p == "0.00"){ p = 0; }
					var q = parseFloat(n) + parseFloat(p);
					document.getElementById("tamt["+a+"]").value = q.toFixed(2);
					k = l = 0;
					for(var j=0;j<=s;j++){
						k = document.getElementById("tamt["+j+"]").value;
						l = parseFloat(l) + parseFloat(k);
					}
					document.getElementById("gtamt").value = l.toFixed(2);
				}
				if(document.getElementById("tds").checked == true){ var b = document.getElementById("tdsperval").value; var c = document.getElementById("gtamt").value; var d = b/100; var e = c * d; var f = parseFloat(c) + parseFloat(e); document.getElementById("tdsamt").value = e; document.getElementById("gtamt").value = f; } else { }
			}
			function calbalamt() { var a = document.getElementById("incr").value; var k = 0; var l = 0; for(var j=0;j<=a;j++){ k = document.getElementById("tamt["+j+"]").value; l = parseFloat(l) + parseFloat(k); } document.getElementById("gtamt").value = l; if(document.getElementById("tds").checked == true){ var b = document.getElementById("tdsperval").value; var c = document.getElementById("gtamt").value; var d = b/100; var e = c * d; var f = parseFloat(c) + parseFloat(e); document.getElementById("tdsamt").value = e; document.getElementById("gtamt").value = f; } else { } }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function fetchprice(aa){
                var ab = aa.split("["); var ad = ab[1].split("]"); var ac = ad[0];
				var b = ac;
				var pdate = document.getElementById("slc_datepickers").value;
				var a = document.getElementById("pname").value;
				document.getElementById("incrs").value = b;
				var c = document.getElementById("scat["+b+"]").value;
				var d = c.split("@");
				var e = d[0];
				if(!a.match("select")){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "main_getitemprices.php?pname="+a+"&iname="+e+"&mdate="+pdate;
					//window.open(url);
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							if(f == "") {
								document.getElementById("iprice["+b+"]").value = "";
								document.getElementById("iprice["+b+"]").readOnly = false;
								calculatetotal("scat["+b+"]");
							}
							else {
								var g = f.split("@");
								var h = g[1];
								var i = g[0];
								if(h == ""){
									document.getElementById("iprice["+b+"]").readOnly = false;
									document.getElementById("iprice["+b+"]").value = i;
									calculatetotal("scat["+b+"]");
								}
								else {
									document.getElementById("iprice["+b+"]").readOnly = true;
									document.getElementById("iprice["+b+"]").value = i;
									calculatetotal("scat["+b+"]");
								}
								
							}
							//alert(url);
						}
					}
				}
				else {
					alert("Please select Customer first ..!");
					document.getElementById("scat["+b+"]").value = "";
				}
			}
			function chktypes(){
				var a = document.getElementById("incrs").value;
				for(var i = 0; i<= a;i++){
					var b = document.getElementById("scat["+i+"]").value;
					var c = b.split("@");
					var d = c[1].search(/Birds/i);
					if(d > 0){ }
					else {
						document.getElementById("jval["+i+"]").style.visibility = "hidden";
						document.getElementById("bval["+i+"]").style.visibility = "hidden";
						document.getElementById("wval["+i+"]").style.visibility = "hidden";
						document.getElementById("ewval["+i+"]").style.visibility = "hidden";
					}
				}
			}
			chktypes();
		</script>
	</body>
</html>