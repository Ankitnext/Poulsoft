<?php
	//main_addpursale.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['disppursale'];
	
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1' AND (`sales_sms` = '1' || `sales_wapp` = '1')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ 
        $sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'WAPP-MSG' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
    }
	else{
		$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'SAVE-DATA' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
	}
?>
<html>
	<head>
		<!--<link rel="stylesheet" type="text/css" href="loading_screen.css">-->
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
			input[type=text] {
				padding: 0;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Create Purchase-Sales</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Purchase-Sales</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$item_code[$row['code']] = $row['code'];
					$item_name[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Warehouse"){ if($branches == ""){ $branches = $row['code']; } else{ $branches = $branches."','".$row['code']; } } }
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `type` IN ('$branches') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$sector_code[$row['code']] = $row['code'];
					$sector_name[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$spzflag = $row['spzflag'];
					$ifwt = $row['wt'];
					$ifbw = $row['bw'];
					$ifjbw = $row['jbw'];
					$ifjbwen = $row['jbwen'];
					$ifctype = $row['ctype'];
					$pst_prate_flag = $row['pst_prate_flag'];
				}
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }
				$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){
					$cus_code[$row['code']] = $row['code'];
					$cus_name[$row['code']] = $row['name'];
					}
					if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){
					$sup_code[$row['code']] = $row['code'];
					$sup_name[$row['code']] = $row['name'];
					}
					else{ }
				}
				$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$today' AND `tdate` >= '$today' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
				$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$today' AND `tdate` >= '$today' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
				$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcsper = $row['tcds']; }
				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="main_updatepursale.php" method="post" role="form" onsubmit="return checkval()">
									<div class="row">
									<div class="form-group col-md-1">
										<label>Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo $fdate; ?>" id="slc_datepickers" readonly>
									</div>
									<div class="form-group col-md-2">
										<label>Warehouse<b style="color:red;">&nbsp;*</b></label>
										<select name="wcodes" id="wcodes" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select>
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;">
										<label>TDS Value</label>
										<input type="text" class="form-control" name="tds_value" id="tds_value" value="<?php echo $tdsper; ?>" readonly >
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;">
										<label>TCS Value</label>
										<input type="text" class="form-control" name="tcs_value" id="tcs_value" value="<?php echo $tcsper; ?>" readonly >
									</div>
									<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incr<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incrs<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;">
										<label>ECount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									</div>
									<div class="col-md-18">
										<table style="width:103%;line-height:30px;" id="tab3">
											<tr style="line-height:30px;">
												<th><label>Supplier<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Bill No.</label></th>
												<th><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
												<?php
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<th><label>Jals</label></th>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<th><label>Birds</label></th>"; }
													if($ifjbwen == 1){ echo "<th><label>T. Wt.</label></th><th><label>E. Wt.</label></th>"; }
												?>
												<th><label>N. Wt.<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount</label></th>
												<th><label>TDS</label></th>
												<th><label>Final-amt</label></th>
												<th><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount</label></th>
												<th><label>TCS</label></th>
												<th><label>Final-amt</label></th>
												<th><label>Vehicle</label></th>
												<!-- <th><label>Driver</label></th> -->
												<th><label>Remarks</label></th>
												<th></th>
												<!--<th><label>Outstanding<b style="color:red;">&nbsp;*</b></label</th>>-->
											</tr>
											<tbody id="bodytab">
											<tr id="tblrow[0]" style="margin:5px 0px 5px 0px;">
												<td style="width: 150px;padding-right:5px;"><select name="snames[]" id="snames[0]" class="form-control select2" style="width: 150px;"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]."@".$sup_name[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="bno[]" id="bno[0]" class="form-control" /></td>
												<td style="width: 150px;padding-right:5px;"><select name="scat[]" id="scat[0]" class="form-control select2" style="width:150px;" onchange="checkitemtype(this.id);fetchprice(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval[0]" value="" onkeyup="validatenum(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval[0]" value="" onkeyup="validatenum(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval[0]" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval[0]" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>
												<td><input type="text" name="nwval[]" id="nwval[0]" value="" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>
												<td><input type="text" name="sup_iprice[]" id="sup_iprice[0]" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control"></td>
												<td><input type="text" name="sup_tamt[]" id="sup_tamt[0]" class="form-control" readonly></td>
												<td>
                                                    <table>
                                                        <tr>
                                                            <td><input type="checkbox" class="form-control1" name="tds_flag[]" id="tds_flag[0]" onchange="calculatetotal()"></td>
                                                            <td style="width:5px;visibility:hidden;"><input type="text" name="tds_tamt[]" id="tds_tamt[0]" class="form-control" readonly></td>
                                                        </tr>
                                                    </table>
                                                    
                                                </td>
												<td><input type="text" name="sup_famt[]" id="sup_famt[0]" class="form-control" readonly></td>
												<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 150px;" onchange="fetchprice(this.id)"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="cus_iprice[]" id="cus_iprice[0]" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control"></td>
												<td><input type="text" name="cus_tamt[]" id="cus_tamt[0]" class="form-control" readonly></td>
												<td>
                                                    <table>
                                                        <tr>
                                                            <td><input type="checkbox" class="form-control1" name="tcs_flag[]" id="tcs_flag[0]" onchange="calculatetotal()"></td>
                                                            <td style="width:5px;visibility:hidden;"><input type="text" name="tcs_tamt[]" id="tcs_tamt[0]" class="form-control" readonly></td>
                                                        </tr>
                                                    </table>
                                                    
                                                </td>
												<td><input type="text" name="cus_famt[]" id="cus_famt[0]" class="form-control" readonly></td>
												<td><input type="text" name="vehicle[]" id="vehicle[0]" class="form-control" ></td>
												<!-- <td><input type="text" name="driver[]" id="driver[0]" class="form-control" ></td> -->
												<td style="width: auto;"><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
												<td style="width: 60px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="removerow(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
											</tr>
											</tbody>
										</table><br/>
										<table style="width:100%;line-height:30px;">
											<tr style="line-height:30px;">
												<th></th>
												<th></th>
												<th <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Total Jals</label></th>
												<th <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Total Birds</label></th>
												<th <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Total T. Weight</label></th>
												<th <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Total E. Weight</label></th>
												<th><label>Total N. Weight</label></th>
												<th></th>
												<th><label>Total Amount</label></th>
												<th></th>
												<th></th>
											</tr>
											<tr style="margin:5px 0px 5px 0px;">
												<td></td>
												<td></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_jval" id="tot_jval" class="form-control" readonly /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_bval" id="tot_bval" class="form-control" readonly /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_wval" id="tot_wval" class="form-control" readonly /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_ewval" id="tot_ewval" class="form-control" readonly /></td>
												<td><input type="text" name="tot_nwval" id="tot_nwval" class="form-control" readonly /></td>
												<td></td>
												<td><input type="text" name="tot_tamt" id="tot_tamt" class="form-control" readonly></td>
												<td style="width: auto;"></td>
												<td style="width: 60px;"></td>
												<!--<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding[]" id="outstanding[0]" style="width:50px;"></td>-->
											</tr>
										<table>
										<div class="col-md-12" align="left">
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Item Field Type</label>
												<input type="text" name="itemfields" id="itemfields" class="form-control" value="<?php if($ifwt == 1){ echo "WT"; } else if($ifbw == 1){ echo "BAW"; } else if($ifjbw == 1){ echo "JBEW"; } else if($ifjbwen == 1){ echo "JBTEN"; } else { echo "WT"; } ?>" >
											</div>
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Amount Based</label>
												<input type="text" name="amountbasedon" id="amountbasedon" class="form-control" value="<?php echo $ifctype; ?>" >
											</div>
										</div>
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
			<!--<div class="ring"><?php //echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>-->
		</section>
		<?php include "header_foot.php"; ?>
		<script>
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				var sup_name = net_wht = sup_prc = sup_amt = cus_name = cus_prc = cus_amt = asup_amt = acus_amt = ""; var c = 0;
				l = true;
				for(var b = 0;b<=a;b++){
					if(l == true){
						sup_name = document.getElementById("snames["+b+"]").value;
						net_wht = document.getElementById("nwval["+b+"]").value;
						sup_prc = document.getElementById("sup_iprice["+b+"]").value;
						sup_amt = document.getElementById("sup_tamt["+b+"]").value;
						cus_name = document.getElementById("cnames["+b+"]").value;
						cus_prc = document.getElementById("cus_iprice["+b+"]").value;
						cus_amt = document.getElementById("cus_tamt["+b+"]").value;
						
						asup_amt = parseFloat(net_wht) * parseFloat(sup_prc);
						acus_amt = parseFloat(net_wht) * parseFloat(cus_prc);
						c = b; c++;
						
						if(sup_name.match("select")){
							alert("Please select Supplier in row: "+c);
							document.getElementById("snames["+b+"]").focus();
							l = false;
						}
						else if(net_wht.length == 0 || net_wht == 0 || net_wht == 0.00 || net_wht == "0" || net_wht == "0.00"){
							alert("Please enter net weight in row: "+c);
							document.getElementById("nwval["+b+"]").focus();
							l = false;
						}
						else if(sup_prc.length == 0 || sup_prc == 0 || sup_prc == 0.00 || sup_prc == "0" || sup_prc == "0.00"){
							alert("Please enter Supplier Price in row: "+c);
							document.getElementById("sup_iprice["+b+"]").focus();
							l = false;
						}
						else if(sup_amt.length == 0 || sup_amt == 0 || sup_amt == 0.00 || sup_amt == "0" || sup_amt == "0.00"){
							alert("Please enter Supplier Amount in row: "+c);
							document.getElementById("sup_tamt["+b+"]").value = asup_amt;
							l = false;
						}
						else if(cus_name.match("select")){
							alert("Please select Customer in row: "+c);
							document.getElementById("cnames["+b+"]").focus();
							l = false;
						}
						else if(cus_prc.length == 0 || cus_prc == 0 || cus_prc == 0.00 || cus_prc == "0" || cus_prc == "0.00"){
							alert("Please enter Customer Price in row: "+c);
							document.getElementById("cus_iprice["+b+"]").focus();
							l = false;
						}
						else if(cus_amt.length == 0 || cus_amt == 0 || cus_amt == 0.00 || cus_amt == "0" || cus_amt == "0.00"){
							alert("Please enter Customer Amount in row: "+c);
							document.getElementById("cus_tamt["+b+"]").value = acus_amt;
							l = false;
						}
						else if(sup_name == cus_name){
							alert("Both Supplier and Customer seleted same \n Kindly change and try again: "+c);
							document.getElementById("cnames["+b+"]").value = focus();
							l = false;
						}
						else{
							l = true;
						}
					}
				}
				if(l == true){
					/*document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php //echo $loading_stitle; ?>';*/
					return l;
				}
				else {
					/*document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
					document.getElementById("disp_val").innerHTML = "";*/
					
					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "main_displaypursale.php?cid="+a;
			}
			function checkitemtype(x){
				var w = x.split("["); var y = w[1].split("]");
				var r = document.getElementById("itemfields").value;
				var b = c = ""; var a = e = d = 0;
				a = y[0];
				
				b = document.getElementById("scat["+a+"]").value;
				c = b.split("@");
				d = c[1].search(/Birds/i);
					
				if(r.match("BAW")){
					if(d > 0){
						document.getElementById("bval["+a+"]").style.visibility = "visible";
					}
					else{
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
					}
				}
				else if(r.match("JBEW")){
					if(d > 0){
						document.getElementById("jval["+a+"]").style.visibility = "visible";
						document.getElementById("bval["+a+"]").style.visibility = "visible";
					}
					else{
						document.getElementById("jval["+a+"]").style.visibility = "hidden";
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
					}
				}
				else if(r.match("JBTEN")){
					if(d > 0){
						document.getElementById("jval["+a+"]").style.visibility = "visible";
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						document.getElementById("wval["+a+"]").style.visibility = "visible";
						document.getElementById("ewval["+a+"]").style.visibility = "visible";
					}
					else{
						document.getElementById("jval["+a+"]").style.visibility = "hidden";
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						document.getElementById("wval["+a+"]").style.visibility = "hidden";
						document.getElementById("ewval["+a+"]").style.visibility = "hidden";
						document.getElementById("nwval["+a+"]").readOnly = false;
					}
				}
				else{ }
			}
			function calculatenetwt(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var e = document.getElementById("wval["+d+"]").value;
				if(e == "" || e == 0 || e == "0.00" || e == 0.00){ e = 0; }
				var f = document.getElementById("ewval["+d+"]").value;
				if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
				
				var g = parseFloat(e) - parseFloat(f);
				document.getElementById("nwval["+d+"]").value = g.toFixed(2);
			}
			function calculatetotal(){
				var a = document.getElementById("incr").value;
				var tds_value = document.getElementById("tds_value").value;
				var tcs_value = document.getElementById("tcs_value").value;
				var e = g = h = i = j = k = 0.00; var tds_flag = tds_tamt = tcs_flag = tcs_tamt = sup_famt = cus_famt = 0;
				for(e = 0;e <= a;e++){
					g = document.getElementById("nwval["+e+"]").value;
					if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
					j = document.getElementById("sup_iprice["+e+"]").value;
					if(j == "" || j == 0 || j == "0.00" || j == 0.00){ j = 0; }
					k = parseFloat(g) * parseFloat(j);
					document.getElementById("sup_tamt["+e+"]").value = k.toFixed(2);
					
					//TDS Calculation
                    if(document.getElementById("tds_flag["+e+"]").checked == true){ tds_flag = 1; } else{ tds_flag = 0; }
					if(tds_flag == 1){
						if(parseFloat(tds_value) > 0){
							tds_tamt = (parseFloat(k) * (parseFloat(tds_value) / 100)); document.getElementById("tds_tamt["+e+"]").value = parseFloat(tds_tamt).toFixed(2);
							sup_famt = (parseFloat(k) + (parseFloat(tds_tamt))); document.getElementById("sup_famt["+e+"]").value = parseFloat(sup_famt).toFixed(2);
						}
						else{
							alert("TDS Value not defined in Master, kindly check and try again ...!");
							document.getElementById("tds_tamt["+e+"]").value = 0;
							document.getElementById("sup_famt["+e+"]").value = parseFloat(k).toFixed(2);
						}
					}
					else{
						document.getElementById("tds_tamt["+e+"]").value = 0;
						document.getElementById("sup_famt["+e+"]").value = parseFloat(k).toFixed(2);
					}
					h = document.getElementById("cus_iprice["+e+"]").value;
					if(h == "" || h == 0 || h == "0.00" || h == 0.00){ h = 0; }
					i = parseFloat(g) * parseFloat(h);
					document.getElementById("cus_tamt["+e+"]").value = i.toFixed(2);
					
					//TCS Calculation
                    if(document.getElementById("tcs_flag["+e+"]").checked == true){ tcs_flag = 1; } else{ tcs_flag = 0; }
					if(tcs_flag == 1){
						if(parseFloat(tds_value) > 0){
							tcs_tamt = (parseFloat(i) * (parseFloat(tcs_value) / 100)); document.getElementById("tcs_tamt["+e+"]").value = parseFloat(tcs_tamt).toFixed(2);
							cus_famt = (parseFloat(i) + (parseFloat(tcs_tamt))); document.getElementById("cus_famt["+e+"]").value = parseFloat(cus_famt).toFixed(2);
						}
						else{
							alert("TCS Value not defined in Master, kindly check and try again ...!");
							document.getElementById("tcs_tamt["+e+"]").value = 0;
							document.getElementById("cus_famt["+e+"]").value = parseFloat(i).toFixed(2);
						}
					}
					else{
						document.getElementById("tcs_tamt["+e+"]").value = 0;
						document.getElementById("cus_famt["+e+"]").value = parseFloat(i).toFixed(2);
					}
				}
			}
			function fetchprice(a){
				var pst_prate_flag = '<?php echo $pst_prate_flag; ?>';
				if(pst_prate_flag == 1 || pst_prate_flag == "1"){
					var b = a.split("[");
					var c = b[1].split("]");
					var d = c[0];
					var e = document.getElementById("cnames["+d+"]").value;
					var f = e.split("@");
					var g = f[0];
					var h = document.getElementById("scat["+d+"]").value;
					var i = h.split("@");
					var j = i[0];
					if(!e.match("select") && !h.match("select")){
						var prices = new XMLHttpRequest();
						var method = "GET";
						//var url = "main_getitemprices.php?pname="+g+"&iname="+j;
						var url = "cus_papersaleprice.php?pname="+g+"&iname="+j;
						var asynchronous = true;
						prices.open(method, url, asynchronous);
						prices.send();
						prices.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								var k = this.responseText;
								if(k == "") {
									document.getElementById("cus_iprice["+d+"]").value = "";
									document.getElementById("cus_iprice["+d+"]").readOnly = false;
									calculatetotal();
								}
								else {
									if(k == "" || k == 0 || k == 0.00 || k == "0.00" || k == "0"){
										document.getElementById("cus_iprice["+d+"]").readOnly = false;
										document.getElementById("cus_iprice["+d+"]").value = "";
										calculatetotal();
									}
									else {
										document.getElementById("cus_iprice["+d+"]").readOnly = true;
										document.getElementById("cus_iprice["+d+"]").value = k;
										calculatetotal();
									}
									
								}
							}
						}
					}
					else if(e.match("select") && h.match("select")){
						
					}
					else {
						//alert("Please select Item / Customer first in row: "+d);
					}
				}
				else{ }
			}
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
			function rowgen(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				document.getElementById("addval["+d+"]").style.visibility = "hidden";
				document.getElementById("rmval["+d+"]").style.visibility = "hidden";
				d++; var e = d; document.getElementById("incr").value = e;
				html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;" id="tblrow['+e+']">';
				html+= '<td style="width: 150px;padding-right:5px;"><select name="snames[]" id="snames['+e+']" class="form-control select" style="width: 150px;"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]."@".$sup_name[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td><input type="text" name="bno[]" id="bno['+e+']" class="form-control" /></td>';
				html+= '<td style="width: 150px;padding-right:5px;"><select name="scat[]" id="scat['+e+']" class="form-control select" style="width:150px;" onchange="checkitemtype(this.id);fetchprice(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>';
				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval['+e+']" value="" onkeyup="validatenum(this.id);calfinaltotal();" onchange="validateamount(this.id)" onchange="validateamount(this.id)" class="form-control" /></td>';
				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval['+e+']" value="" onkeyup="validatenum(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>';
				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval['+e+']" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>';
				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval['+e+']" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>';
				html+= '<td><input type="text" name="nwval[]" id="nwval['+e+']" value="" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control" /></td>';
				html+= '<td><input type="text" name="sup_iprice[]" id="sup_iprice['+e+']" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control"></td>';
				html+= '<td><input type="text" name="sup_tamt[]" id="sup_tamt['+e+']" class="form-control" readonly></td>';
				html+= '<td><table><tr><td><input type="checkbox" class="form-control1" name="tds_flag[]" id="tds_flag['+e+']" onchange="calculatetotal()"></td><td style="width:5px;visibility:hidden;"><input type="text" name="tds_tamt[]" id="tds_tamt['+e+']" class="form-control" readonly></td></tr></table></td>';
				html+= '<td><input type="text" name="sup_famt[]" id="sup_famt['+e+']" class="form-control" readonly></td>';
				html+= '<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames['+e+']" class="form-control select" style="width: 150px;" onchange="fetchprice(this.id)"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td><input type="text" name="cus_iprice[]" id="cus_iprice['+e+']" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control"></td>';
				html+= '<td><input type="text" name="cus_tamt[]" id="cus_tamt['+e+']" class="form-control" readonly></td>';
				html+= '<td><table><tr><td><input type="checkbox" class="form-control1" name="tcs_flag[]" id="tcs_flag['+e+']" onchange="calculatetotal()"></td><td style="width:5px;visibility:hidden;"><input type="text" name="tcs_tamt[]" id="tcs_tamt['+e+']" class="form-control" readonly></td></tr></table></td>';
				html+= '<td><input type="text" name="cus_famt[]" id="cus_famt['+e+']" class="form-control" readonly></td>';
				html+= '<td><input type="text" name="vehicle[]" id="vehicle['+e+']" class="form-control" ></td>';
				// html+= '<td><input type="text" name="driver[]" id="driver['+e+']" class="form-control" ></td>';
				html+= '<td style="width: auto;"><textarea name="narr[]" id="narr['+e+']" class="form-control" style="height:23px;"></textarea></td>';
				html+= '<td style="width: 60px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+e+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+e+']" onclick="removerow(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html+= '</tr>';
				$('#bodytab').append(html); $('.select').select2();
				document.getElementById("addval["+e+"]").style.visibility = "visible";
				document.getElementById("rmval["+e+"]").style.visibility = "visible";
			}
			function removerow(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				document.getElementById('tblrow['+d+']').remove();
				d--; var e = d; document.getElementById("incr").value = e;
				if(d > 0){
					document.getElementById("addval["+e+"]").style.visibility = "visible";
					document.getElementById("rmval["+e+"]").style.visibility = "visible";
				}
				else{
					document.getElementById("addval["+e+"]").style.visibility = "visible";
				}
			}
			function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function calfinaltotal(){
				var a = document.getElementById("itemfields").value;
				var aa = document.getElementById("incr").value;
				if(a.match("WT")){
					var f = 0; var g = 0;
					var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						f = document.getElementById("nwval["+j+"]").value;
						if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("cus_tamt["+j+"]").value;
						if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else if(a.match("BAW")){
					var c = 0; var f = 0; var g = 0;
					var birds_val = 0; var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						c = document.getElementById("bval["+j+"]").value;
						if(c == "" || c == 0 || c == "0.00" || c == 0.00){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						f = document.getElementById("nwval["+j+"]").value;
						if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("cus_tamt["+j+"]").value;
						if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else if(a.match("JBEW")){
					var b = 0; var c = 0; var f = 0; var g = 0;
					var jal_val = 0; var birds_val = 0; var twht_val = 0; var ewht_val = 0; var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						b = document.getElementById("jval["+j+"]").value;
						if(b == "" || b == 0 || b == "0.00" || b == 0.00){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
						c = document.getElementById("bval["+j+"]").value;
						if(c == "" || c == 0 || c == "0.00" || c == 0.00){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						f = document.getElementById("nwval["+j+"]").value;
						if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("cus_tamt["+j+"]").value;
						if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_jval").value = jal_val;
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else if(a.match("JBTEN")){
					var b = 0; var c = 0; var d = 0; var e = 0; var f = 0; var g = 0;
					var jal_val = 0; var birds_val = 0; var twht_val = 0; var ewht_val = 0; var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						b = document.getElementById("jval["+j+"]").value;
						if(b == "" || b == 0 || b == "0.00" || b == 0.00){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
						c = document.getElementById("bval["+j+"]").value;
						if(c == "" || c == 0 || c == "0.00" || c == 0.00){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						d = document.getElementById("wval["+j+"]").value;
						if(d == "" || d == 0 || d == "0.00" || d == 0.00){ d = 0; }
						twht_val = parseFloat(twht_val) + parseFloat(d);
						e = document.getElementById("ewval["+j+"]").value;
						if(e == "" || e == 0 || e == "0.00" || e == 0.00){ e = 0; }
						ewht_val = parseFloat(ewht_val) + parseFloat(e);
						f = document.getElementById("nwval["+j+"]").value;
						if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("cus_tamt["+j+"]").value;
						if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_jval").value = jal_val;
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_wval").value = twht_val.toFixed(2);
					document.getElementById("tot_ewval").value = ewht_val.toFixed(2);
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else{
					alert("Kindly contact Admin for any support..!");
				}
			}
		</script>
		<script src="handle_ebtn_as_tbtn.js"></script>
	</body>
</html>