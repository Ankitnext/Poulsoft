<?php
	//pur_addpurchases.php
	session_start(); include "newConfig.php";
	include "header_head.php";
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
			<h1>Create Purchase Invoice</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Purchase</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
			$emp_code = $_SESSION['userid'];
			$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access']; $slae_rate_edit_flag = $row['slae_rate_edit_flag']; }
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
				$fdate = date("Y-m-d");
				$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$fdate' AND `tdate` >= '$fdate'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
				$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$fdate' AND `tdate` >= '$fdate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $purchases = $row['purchases']; } $incr = $purchases + 1;
				if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
				$code = "P".$pfx."-".$incr; $c = 0;

				$item_code = $item_name = array();
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

				$sector_code = $sector_name = array();
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' $warehouse_codes ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

				$tdsper = 0;
				$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
				$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				
				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $ppzflag = $row['ppzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; }
				if($ppzflag == "" || $ppzflag == 0 || $ppzflag == NULL){ $ppzflag = 0; } else{ }
				$idisplay = ''; $ndisplay = 'style="display:none;"';
				if($ifbw == 1 || $ifjbw == 1 || $ifjbwen == 1){ $birds_flag = 1; }
	
				//Fetch Column From CoA Table
				$sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
				while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
				if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
				if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

				$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `transport_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $transporter_code[$row['code']] = $row['code']; $transporter_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Purchase' AND `field_function` LIKE 'Display Avg.Price' AND `user_access` LIKE 'all' AND `flag` = '1'";
				$query = mysqli_query($conn,$sql); $dap_flag = mysqli_num_rows($query);
			?>
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="pur_updatepurchaseinvoice_ta.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-1" style="width: 100px; text-align:Left;">
										<label>Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width: 90px; text-align:Left;" class="form-control" name="pdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" id="pur_datepickers" onchange="fetchtds()" readonly >
									</div>
									<div class="form-group col-md-2"  style="width: 120px;text-align:Left;">
										<label style="padding-left:10px;">Invoice</label>
									<input type="text" name="inv" id="inv" style="width: 100px;background:none;border:none;text-decoration:none;text-align:Left;" class="form-control" value="<?php echo $code; ?>" placeholder="Enter Location..." readonly>
									</div>
									<div class="form-group col-md-3"  style="width: 250px;text-align:Left;">
										<label style="width: 30px;text-align:Left; padding-left:0px;">Supplier<b style="color:red;">&nbsp;*</b></label>
										<select name="pname" id="pname" style="width: 230px;text-align:Left; " class="form-control select2"  onchange="fetch_supplier_outstanding();">
											<option value="select">select</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-4"  style="width: 170px;">
										<label  style="text-align:Left;">Book Invoice</label>
										<input style="width: 150px;text-align:Left;" type="text" name="binv" id="binv"  class="form-control" placeholder="Enter Book Invoice...">
									</div>
									<div class="form-group col-md-2"  style="width: 100px;text-align:Left;">
									<label  style="text-align:Left;">Balance</label>
									<input style="width: 90px;text-align:right;" type="text" name="out_balance" id="out_balance" class="form-control" readonly />
									</div>
									<div class="form-group col-md-6" style="visibility:hidden;">
										<label>incr<b style="color:red;">&nbsp;*</b></label>
										<input type="text"  class="form-control" name="incr" id="incr" value="0">
									</div>
									<div class="col-md-12">
										<table style="line-height:30px;" id="tab3">
											<thead>
												<tr style="line-height:30px;">
													<th style="width: 180px;padding-right:30px;"><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
													<?php if($ifjbwen == 1 || $ifjbw == 1){ echo "<th style= 'width: 90px;padding-right:20px;'><label>Jals</label></th>"; } ?>
													<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<th  style='width: 90px;padding-right:20px;'><label>Birds</label></th>"; } ?>
													<?php if($ifjbwen == 1){ echo "<th  style='width: 90px;padding-right:20px;'><label>T. Weight<b style='color:red;'>&nbsp;*</b></label></th>"; } ?>
													<?php if($ifjbwen == 1){ echo "<th  style='width: 90px;padding-right:20px;'><label>E. Weight</label></th>"; } ?>

													<?php
													if($_SESSION['dbase'] == "poulso6_chicken_ka_dharun_feeds"){
													?>
													<th style="width: 90px;padding-right:20px;"><label>No.of Bags<b style="color:red;">&nbsp;*</b></label></th>
													<?php
													}
													else{
													?>
													<th style="width: 90px;padding-right:20px;"><label>N. Weight<b style="color:red;">&nbsp;*</b></label></th>
													<?php
													}
													?>
													
													<?php if($dap_flag == 1){ echo "<th  style='width: 90px;padding-right:20px;'><label>Avg. Wt.</label></th>"; } ?>
													<th style="width: 90px;padding-right:20px;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
													<th style="width: 110px;padding-right:20px;"><label>Amount</label></th>
													<th style="width: 90px;padding-right:20px;"><label>Warehouse<b style="color:red;">&nbsp;*</b></label></th>
													<th></th>
												</tr>
                                            </thead>
                                            <tbody id="row_body">
												<tr style="margin:5px 0px 5px 0px;">
													<td style="width: 180px;padding-right:20px;text-align:Left;"><select name="scat[]" id="scat[0]" class="form-control select2" style="width: 100%;" onchange="calculatetotal(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $ic."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>
													<!--<td  <?php //if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>  ><input type="text" name="jval[]" id="jval[0]" class="form-control text-right"  style="width: 100px;"  onkeyup="validatebirds(this.id);calculatetotal(this.id);" onchange="validatebirds(this.id);" /></td>
													<td <?php //if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> ><input type="text" name="bval[]" id="bval[0]" class="form-control text-right" style="width: 100px;"  onkeyup="validatebirds(this.id);calculatetotal(this.id);" onchange="validatebirds(this.id);" /></td>
													<td <?php //if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> ><input type="text" name="wval[]" id="wval[0]" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" style="width: 100px;"  onchange="validateamount(this.id);" /></td>
													<td <?php //if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> ><input type="text" name="ewval[]" id="ewval[0]" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" style="width: 100px;"  onchange="validateamount(this.id);" /></td>
													-->
													
													
													<?php if($ifjbwen == 1 || $ifjbw == 1){ 
													echo "<td style='width: 90px;padding-right:20px;'> <input type='text' name='jval[]' id='jval[0]' class='form-control text-right'  style='width: 90px;'  onkeyup='validatebirds(this.id);calculatetotal(this.id);' onchange='validatebirds(this.id);' /></td>"; 
												} if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ 
													echo "<td style='width: 90px;padding-right:20px;'><input type='text' name='bval[]' id='bval[0]' class='form-control text-right' style='width: 90px;'  onkeyup='validatebirds(this.id);calculatetotal(this.id);' onchange='validatebirds(this.id);' /></td>"; 
													} ?>
													<?php if($ifjbwen == 1){ 
													echo "<td style='width: 90px;padding-right:20px;'><input type='text' name='wval[]' id='wval[0]' class='form-control text-right' style='width: 90px;' onkeyup='validatenum(this.id);calculatetotal(this.id);'   onchange='validateamount(this.id);' /></td>";
													echo "<td style='width: 90px;padding-right:20px;'><input type='text' name='ewval[]' id='ewval[0]' class='form-control text-right' style='width: 90px;' onkeyup='validatenum(this.id);calculatetotal(this.id);'  onchange='validateamount(this.id);' /></td>"; 
													} ?>
													
													<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="nwval[]" id="nwval[0]" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" onchange="validateamount(this.id);" /></td>
													<?php if($dap_flag == 1){
														echo "<td style='width: 90px;padding-right:20px;'><input type='text' name='avg_wt[]' id='avg_wt[0]' class='form-control text-right' style='width: 90px;' readonly /></td>";
													} ?>
													<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="iprice[]" id="iprice[0]" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" onchange="validateamount(this.id);"></td>
													<td style="width: 110px;padding-right:20px;"><input style="width: 110px;" type="text" name="tamt[]" id="tamt[0]" class="form-control text-right" readonly></td>
													<td style="width: 150px;padding-right:20px;"><select name="wcodes[]" id="wcodes[0]" class="form-control select2" style="width: 150px;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $it; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select></td>
													<!-- <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td> -->
													<td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)"> <i class="fa fa-plus"></i></i></a></td>
													
                                                </tr>
													
											</tbody>
										</table>
										<br/>
										<?php $bil_pad = 210;
											  if ( $ifjbwen == 1 )
											  { $bil_pad = $bil_pad + 440; ?>
											
										<div class="col-md-12" align="left" style="padding-left:700px">
											<?php }
											  else if ( $ifjbwen != 1 &&  $ifjbw == 1 &&  $ifbw != 1 ) 
											  {  $bil_pad = $bil_pad + 220; ?>
											
										<div class="col-md-12" align="left" style="padding-left:457px">
											<?php }
											  else if ( $ifjbwen != 1 &&  $ifjbw != 1 &&  $ifbw == 1 )
											  {  $bil_pad = $bil_pad + 110; ?>
											  
										<div class="col-md-12" align="left" style="padding-left:337px">
											
											
											<?php } 
											 else if ( $ifjbwen != 1 &&  $ifjbw == 1 &&  $ifbw == 1 )
											 {  $bil_pad = $bil_pad + 110; ?>
											 
									   <div class="col-md-12" align="left" style="padding-left:457px">
										   
										   
										   <?php } 
											else { ?>
											
										<div class="col-md-12" align="left" style="padding-left:217px">
										<?php } ?>

										<!-- <div class="col-md-12" align="left" style="padding-left:210px">
											--><div class="col-md-1" style="width: 90px; text-align:Left;">
												<input type="checkbox" name="tds" id="tds"  onchange="caltds()" >
												<label>TCS</label>
												<input type="text" name="tdsamt" id="tdsamt" class="form-control" style="width: 90px; text-align:right;" readonly >
											</div>
											<div class="col-md-1" style="visibility: hidden;">
												<label>TCS Percentage</label>
												<input type="text" name="tdsperval" id="tdsperval" class="form-control" style="width:auto;" value="<?php echo $tdsper; ?>" readonly >
											</div>
											
											<div class="col-md-1" style="width: 150px; text-align:Left;  ">
												<label>Billing Amount</label>
												<input type="text" name="gtamt" id="gtamt" class="form-control text-right" style="width: 110px; text-align:Right;" readonly>
											</div>
										</div>
										<div class="col-md-12" align="left" style="padding-top:30px;">
											<div class="col-md-1" style="width: 120px; text-align:Left;">
												<label>Vehicle No.</label>
												<input type="text" name="vno" id="vno" class="form-control" style="width: 100px; text-align:Left;" >
											</div>
											<div class="col-md-4" style="width: 120px; text-align:Left;">
												<label>Driver</label>
												<input type="text" name="dname" id="dname" class="form-control"style="width: 100px; text-align:Left;" >
											</div>
											<div class="col-md-4" style="width: 150px; text-align:Left;">
												<label>Transporter</label>
												<select name="transporter_code" id="transporter_code" style="width: 130px;"  class="form-control select2">
													<option value="select">-select-</option>
													<?php foreach($transporter_code as $tcode){ ?><option value="<?php echo $tcode;?>"><?php echo $transporter_name[$tcode];?></option><?php } ?>
												</select>
											</div>
											<div class="col-md-4" style="width: 150px; text-align:Left;">
												<label>Freight Amount</label>
												<input style="width: 120px;"  type="text" name="freight_amount" id="freight_amount" class="form-control text-right" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" />
											</div>
											</div>
										<div class="col-md-12" align="left" >
											<div class="col-md-1" style="width:auto;visibility:hidden;">
												<label>Amount in words</label>
												<textarea type="text" name="gtamtinwords" id="gtamtinwords" class="form-control" style="width:100%;" readonly></textarea>
											</div>
											<div class="col-md-1" style="width:auto;visibility:hidden;">
												<label>Item Field Type</label>
												<input type="text" name="itemfields" id="itemfields" class="form-control" value="<?php if($ifwt == 1){ echo "WT"; } else if($ifbw == 1){ echo "BAW"; } else if($ifjbw == 1){ echo "JBEW"; } else if($ifjbwen == 1){ echo "JBTEN"; } else { echo "WT"; } ?>" >
											</div>
											<div class="col-md-1" style="width:auto;visibility:hidden;">
												<label>ECount<b style="color:red;">&nbsp;*</b></label>
												<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
											</div>
										</div>
										<div class="col-md-16" align="center">
											<label>Remarks</label>
											<textarea name="narr" id="narr" class="form-control" style="width:210px;" ></textarea>
										</div><br/>
										<div class="box-body" align="center">
											<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
												<i class="fa fa-save"></i> Save
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
					var b = document.getElementById("tdsperval").value; if(b == ""){ b = 0; }
					var c = document.getElementById("gtamt").value; if(c == ""){ c = 0; }
					var d = parseFloat(b) / 100;
					var e = (parseFloat(c) * parseFloat(d)).toFixed(2);
					var f = parseFloat(c) + parseFloat(e);
					document.getElementById("tdsamt").value = parseFloat(e).toFixed(2);
					document.getElementById("gtamt").value = parseFloat(f).toFixed(2);
					calculatetotal("scat[0]");
				}
				else {
					document.getElementById("tdsamt").value = "";
					calculatetotal("scat[0]");
				}
			}
			function fetchtds(){
				var a = document.getElementById("pur_datepickers").value;
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
			function getamountinwords() {
				var a = document.getElementById("gtamt").value;
				var b = convertNumberToWords(a);
				document.getElementById("gtamtinwords").value = b;
			}
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var incr = document.getElementById("incr").value;
				var itemfields = document.getElementById("itemfields").value;
				var purchase_price_flag = '<?php echo $ppzflag; ?>';
				var ven_name = item = sector = ""; var c = total_wt = empty_wt = net_wt = price = itm_amt = 0;
				var l = true;
				ven_name = document.getElementById("pname").value;
				if(ven_name == "" || ven_name == "select"){
					alert("Please select Supplier Name");
					document.getElementById("pname").focus();
					l = false;
				}
				else{
					for(var d = 0;d <= incr;d++){
						if(l == true){
							c = d + 1;
							item = document.getElementById("scat["+d+"]").value;
							net_wt = document.getElementById("nwval["+d+"]").value; if(net_wt == ""){ net_wt = 0; }
							price = document.getElementById("iprice["+d+"]").value; if(price == ""){ price = 0; }
							itm_amt = document.getElementById("tamt["+d+"]").value; if(itm_amt == ""){ itm_amt = 0; }
							sector = document.getElementById("wcodes["+d+"]").value;

							if(item == "" || item == "select"){
								alert("Please select Item Description in row: "+c);
								document.getElementById("scat["+d+"]").focus();
								l = false;
							}
							else if(net_wt == "" || parseFloat(net_wt) == 0){
								alert("Please enter Net Weight in row: "+c);
								document.getElementById("nwval["+d+"]").focus();
								l = false;
							}
							// else if(price == "" && parseInt(purchase_price_flag) == 0 || parseFloat(price) == 0 && parseInt(purchase_price_flag) == 0){
							// 	alert("Please enter Item Price in row: "+c);
							// 	document.getElementById("iprice["+d+"]").focus();
							// 	l = false;
							// }
							// else if(itm_amt == "" || parseFloat(itm_amt) == 0){
							// 	alert("Please re-enter Item Price in row: "+c);
							// 	document.getElementById("tamt["+d+"]").focus();
							// 	l = false;
							// }
							else if(sector == "" || sector == "select"){
								alert("Please select Warehouse in row: "+c);
								document.getElementById("wcodes["+d+"]").focus();
								l = false;
							}
							else if(itemfields == "JBTEN" && item.search(/Birds/i) > 0){
								total_wt = document.getElementById("wval["+d+"]").value; if(total_wt == ""){ total_wt = 0; }
								empty_wt = document.getElementById("ewval["+d+"]").value; if(empty_wt == ""){ empty_wt = 0; }
								if(parseFloat(empty_wt) > parseFloat(total_wt)){
									alert("Empty Weight need to be less than or equal to Total Weight in row: "+c);
									document.getElementById("ewval["+d+"]").focus();
									l = false;
								}
							}
						}
					}
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
				window.location.href = "pur_displaypurchases.php";
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
			function create_row(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = ''; var dap_flag = '<?php echo $dap_flag; ?>';
                document.getElementById("incr").value = d;
				html += '<tr id="row_no['+d+']"  style="margin:5px 0px 5px 0px;">';
				html += '<td style="width: 180px;padding-right:20px;"><select name="scat[]" id="scat['+d+']" class="form-control select2" style="width: 100%;" onchange="calculatetotal(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $ic."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>';
				//html += '<td <?php //if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval['+d+']" class="form-control text-right" onkeyup="validatebirds(this.id);calculatetotal(this.id);" onchange="validatebirds(this.id);" /></td>';
				//html += '<td <?php //if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval['+d+']" class="form-control text-right" onkeyup="validatebirds(this.id);calculatetotal(this.id);" onchange="validatebirds(this.id);" /></td>';
				//html += '<td <?php //if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval['+d+']" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" onchange="validateamount(this.id);" /></td>';
				//html += '<td <?php //if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval['+d+']" class="form-control text-right" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>';
				
				
				<?php if($ifjbwen == 1 || $ifjbw == 1){ ?>
				html += '<td style="width: 90px;padding-right:20px;"><input  style="width: 90px;" type="text" name="jval[]" id="jval['+d+']" class="form-control text-right" onkeyup="validatebirds(this.id);calculatetotal(this.id);" onchange="validatebirds(this.id);" /></td>';
				<?php }  if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
				html += '<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="bval[]" id="bval['+d+']" class="form-control text-right" onkeyup="validatebirds(this.id);calculatetotal(this.id);" onchange="validatebirds(this.id);" /></td>';
				<?php } if($ifjbwen == 1) { ?>
				html += '<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="wval[]" id="wval['+d+']" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" onchange="validateamount(this.id);" /></td>';
				html += '<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="ewval[]" id="ewval['+d+']" class="form-control text-right" onchange="validateamount(this.id);calculatetotal(this.id);" /></td>';
				<?php } ?>
				html += '<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="nwval[]" id="nwval['+d+']" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" onchange="validateamount(this.id);" /></td>';
				if(parseInt(dap_flag) == 1){
					html += '<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="avg_wt[]" id="avg_wt['+d+']" class="form-control text-right" readonly /></td>';
				}
				html += '<td style="width: 90px;padding-right:20px;"><input style="width: 90px;" type="text" name="iprice[]" id="iprice['+d+']" class="form-control text-right" onkeyup="validatenum(this.id);calculatetotal(this.id);" onchange="validateamount(this.id);"></td>';
				html += '<td style="width: 110px;padding-right:20px;"><input style="width: 110px;" type="text" name="tamt[]" id="tamt['+d+']" class="form-control text-right" readonly></td>';
				html += '<td style="width: 150px;padding-right:20px;"><select style="width: 150px;" name="wcodes[]" id="wcodes['+d+']" class="form-control select2"><?php foreach($sector_code as $it){ ?><option value="<?php echo $it; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select></td>';
				html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
				$('#row_body').append(html);
                $('.select2').select2();
                calculatetotal("scat["+d+"]");
			}
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculatetotal("scat["+d+"]");
            }
			function calculatetotal(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var scat = document.getElementById("scat["+d+"]").value;
				var scat1 = scat.split("@");
				var bird_flag = scat1[1].search(/Birds/i);
				var r = document.getElementById("itemfields").value;
				var birds_flag = '<?php echo $birds_flag; ?>';
				var dap_flag = '<?php echo $dap_flag; ?>';
				var birds = avg_wt = 0;
				
				if(parseInt(bird_flag) > 0){
					if(r.match("WT")){ }
					else if(r.match("BAW")){
						document.getElementById("bval["+d+"]").style.visibility = "visible";
					}
					else if(r.match("JBEW")){
						document.getElementById("jval["+d+"]").style.visibility = "visible";
						document.getElementById("bval["+d+"]").style.visibility = "visible";
					}
					else if(r.match("JBTEN")){
						document.getElementById("jval["+d+"]").style.visibility = "visible";
						document.getElementById("bval["+d+"]").style.visibility = "visible";
						document.getElementById("wval["+d+"]").style.visibility = "visible";
						document.getElementById("ewval["+d+"]").style.visibility = "visible";
						document.getElementById("nwval["+d+"]").readOnly = true;
					}
				}
				else{
					if(r.match("WT")){ }
					else if(r.match("BAW")){
						document.getElementById("bval["+d+"]").style.visibility = "hidden"; document.getElementById("bval["+d+"]").value = "";
					}
					else if(r.match("JBEW")){
						document.getElementById("jval["+d+"]").style.visibility = "hidden"; document.getElementById("jval["+d+"]").value = "";
						document.getElementById("bval["+d+"]").style.visibility = "hidden"; document.getElementById("bval["+d+"]").value = "";
					}
					else if(r.match("JBTEN")){
						document.getElementById("jval["+d+"]").style.visibility = "hidden"; document.getElementById("jval["+d+"]").value = "";
						document.getElementById("bval["+d+"]").style.visibility = "hidden"; document.getElementById("bval["+d+"]").value = "";
						document.getElementById("wval["+d+"]").style.visibility = "hidden"; document.getElementById("wval["+d+"]").value = "";
						document.getElementById("ewval["+d+"]").style.visibility = "hidden"; document.getElementById("ewval["+d+"]").value = "";
						document.getElementById("nwval["+d+"]").readOnly = false;
					}
				}
				
				var incr = document.getElementById("incr").value;
				var total_wt = empty_wt = net_wt = price = itm_amt = ft_amt = tcds_per = tcds_amt = 0;
				for(var i = 0;i <= incr;i++){
					if(r.match("JBTEN")){
						if(parseInt(bird_flag) > 0){
							total_wt = document.getElementById("wval["+i+"]").value; if(total_wt == ""){ total_wt = 0; }
							empty_wt = document.getElementById("ewval["+i+"]").value; if(empty_wt == ""){ empty_wt = 0; }
							if(parseFloat(empty_wt) > parseFloat(total_wt)){
								alert("Empty Weight need to be less than or equal to Total Weight");
								document.getElementById("ewval["+i+"]").value = "";
								document.getElementById("nwval["+i+"]").value = parseFloat(total_wt).toFixed(2)
							}
							else{
								net_wt = parseFloat(total_wt) - parseFloat(empty_wt);
								document.getElementById("nwval["+i+"]").value = parseFloat(net_wt).toFixed(2);
							}
						}
					}
					net_wt = document.getElementById("nwval["+i+"]").value; if(net_wt == ""){ net_wt = 0; }

					if(parseInt(bird_flag) > 0 && parseInt(birds_flag) > 0 && parseInt(dap_flag) > 0){
						birds = document.getElementById("bval["+d+"]").value; if(birds == ""){ birds = 0; }
						avg_wt = 0; if(parseFloat(birds) != 0){ avg_wt = parseFloat(net_wt) / parseFloat(birds); }
						document.getElementById("avg_wt["+i+"]").value = parseFloat(avg_wt).toFixed(3);
					}
					else{ }

					price = document.getElementById("iprice["+i+"]").value; if(price == ""){ price = 0; }
					itm_amt = parseFloat(net_wt) * parseFloat(price);
					document.getElementById("tamt["+i+"]").value = parseFloat(itm_amt).toFixed(2);

					itm_amt = document.getElementById("tamt["+i+"]").value; if(itm_amt == ""){ itm_amt = 0; }
					ft_amt = parseFloat(ft_amt) + parseFloat(itm_amt);
				}
				document.getElementById("gtamt").value = parseFloat(ft_amt).toFixed(2);

				if(document.getElementById("tds").checked == true){
					tcds_per = document.getElementById("tdsperval").value; if(tcds_per == ""){ tcds_per = 0; }
					ft_amt = document.getElementById("gtamt").value; if(ft_amt == ""){ ft_amt = 0; }
					tcds_per = parseFloat(tcds_per) / 100;
					tcds_amt = parseFloat(ft_amt) * parseFloat(tcds_per);
					ft_amt = parseFloat(ft_amt) + parseFloat(tcds_amt);
					document.getElementById("tdsamt").value = parseFloat(tcds_amt).toFixed(2);
					document.getElementById("gtamt").value = parseFloat(ft_amt).toFixed(2);
				}
				else{
					document.getElementById("tdsamt").value = "";
					document.getElementById("gtamt").value = parseFloat(ft_amt).toFixed(2);
				}
				getamountinwords();
			}
            function fetch_supplier_outstanding(){
                var pname = document.getElementById("pname").value;
                if(!pname.match("select")){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "supplier_fetch_balance.php?pname="+pname;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var dval = this.responseText;
                            document.getElementById("out_balance").value = dval;
                        }
                    }
                }
                else{
                    document.getElementById("out_balance").value = "";
                }
            }
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submittrans').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatebirds(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
		</script>
		<script src="main_numbertoamount.js"></script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>