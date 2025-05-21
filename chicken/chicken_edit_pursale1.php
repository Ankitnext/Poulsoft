<?php
	//chicken_edit_pursale1.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['disppursale'];
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
			input[type=text] {
				padding: 0;
			}
            .amount-format{
                text-align: right;
            }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Edit Purchase-Sales</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Purchase-Sales</a></li>
				<li class="active">Display</li>
				<li class="active">Edit</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$fdate = date("Y-m-d");
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Warehouse%' AND `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $office_alist = array();
				while($row = mysqli_fetch_assoc($query)){ $office_alist[$row['code']] = $row['code']; }

                $office_list = implode("','",$office_alist);
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `type` IN ('$office_list') ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
				while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $spzflag = $row['spzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $pst_prate_flag = $row['pst_prate_flag']; }
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }

				$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
                $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
				while($row = mysqli_fetch_assoc($query)){
					if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; } else{ }
					if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
				}

				$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
				$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
				$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcsper = $row['tcds']; }
				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body">
                    <?php
                        $id = $_GET['id'];
                        $sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
                        if($ccount > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $fdate = date("d.m.Y",strtotime($row['date']));
                                $link_trnum = $row['link_trnum'];
                                $cnames = $row['customercode'];
                                $cus_iprice = $row['itemprice'];
                                $cus_tamt = $row['totalamt'];
                                $tcs_tamt = $row['tcdsamt'];
                                $cus_famt = $row['finaltotal'];
                                $vehicle = $row['vehiclecode'];
                                $driver = $row['drivercode'];
                                $narr = $row['remarks'];
                                $warehouse = $row['warehouse'];
                            }
                        }
                        $sql ="SELECT * FROM `pur_purchase` WHERE `invoice` = '$link_trnum'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
                        if($ccount > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $snames = $row['vendorcode'];
                                $supbrh_code = $row['supbrh_code'];
                                $bno = $row['bookinvoice'];
                                $scat = $row['itemcode'];
                                $jval = $row['jals'];
                                $bval = $row['birds'];
                                $wval = $row['totalweight'];
                                $ewval = $row['emptyweight'];
                                $nwval = $row['netweight'];
                                $sup_iprice = $row['itemprice'];
                                $sup_tamt = $row['totalamt'];
                                $tds_tamt = $row['tcdsamt'];
                                $sup_famt = $row['finaltotal'];
                            }
                        }
                    ?>
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="chicken_modify_pursale1.php" method="post" role="form" onsubmit="return checkval()">
									<div class="row">
									<div class="form-group col-md-1">
										<label>Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo $fdate; ?>" id="slc_datepickers" readonly>
									</div>
									<div class="form-group col-md-2">
										<label>Warehouse<b style="color:red;">&nbsp;*</b></label>
										<select name="wcodes" id="wcodes" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>" <?php if($warehouse == $it){ echo "selected"; } ?>><?php echo $sector_name[$it]; ?></option><?php } ?></select>
									</div>
									<div class="form-group col-md-2">
										<label>Item Description<b style="color:red;">&nbsp;*</b></label>
										<select name="scat[]" id="scat[0]" class="form-control select2" style="width:150px;" onchange="checkitemtype(this.id);fetchprice(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>" <?php if($scat == $ic){ echo "selected"; } ?>><?php echo $item_name[$ic]; ?></option><?php } ?></select>
									</div>
									<div class="form-group col-md-2">
										<label>Supplier Branch<b style="color:red;">&nbsp;*</b></label>
										<select name="supbrh_code[]" id="supbrh_code[0]" class="form-control select2" style="width:150px;">
											<option value="select">-select-</option>
											<?php
                                            $sql = "SELECT * FROM `chicken_supplier_branch` WHERE `sup_code` LIKE '$	' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                                            $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                            while($row = mysqli_fetch_assoc($query)){
                                                $code = $row['code']; $name = $row['description'];
                                                if($supbrh_code == $code){
                                                    echo '<option value="'.$code.'" selected>'.$name.'</option>';
                                                }
                                                else{
                                                    echo '<option value="'.$code.'">'.$name.'</option>';
                                                }
                                            }
                                            ?>
										</select>
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
												<th style="text-align:center;"><label>Supplier<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Bill No.</label></th>
												<?php
													if($ifjbwen == 1 || $ifjbw == 1){ echo "<th><label>Jals</label></th>"; }
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<th><label>Birds</label></th>"; }
													if($ifjbwen == 1){ echo "<th><label>T. Wt.</label></th><th><label>E. Wt.</label></th>"; }
												?>
												<th style="text-align:center;"><label>N. Wt.<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;" style="width:1px;visibility:hidden;text-align:center;"></th>
												<th style="text-align:center;"><label>TDS</label></th>
												<th style="text-align:center;"><label>Amount</label></th>
												<th style="text-align:center;"><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:1px;visibility:hidden;text-align:center;"></th>
												<th style="text-align:center;"><label>TCS</label></th>
												<th style="text-align:center;"><label>Amount</label></th>
												<th style="text-align:center;"><label>Vehicle</label></th>
												<th style="text-align:center;"><label>Driver</label></th>
												<th style="text-align:center;"><label>Remarks</label></th>
												<th style="text-align:center;"></th>
												<!--<th><label>Outstanding<b style="color:red;">&nbsp;*</b></label</th>>-->
											</tr>
											<tbody id="bodytab">
											<tr id="tblrow[0]" style="margin:5px 0px 5px 0px;">
												<td style="width: 150px;padding-right:5px;"><select name="snames[]" id="snames[0]" class="form-control select2" style="width: 150px;" onchange="fetch_sup_branches(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]."@".$sup_name[$cc]; ?>" <?php if($snames == $cc){ echo "selected"; } ?>><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="bno[]" id="bno[0]" value="<?php echo $bno; ?>" class="form-control" /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval[0]" value="<?php echo $jval; ?>" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval[0]" value="<?php echo $bval; ?>" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval[0]" value="<?php echo $wval; ?>" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval[0]" value="<?php echo $ewval; ?>" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>
												<td><input type="text" name="nwval[]" id="nwval[0]" value="<?php echo $nwval; ?>" style="width:90xp;" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>
												<td><input type="text" name="sup_iprice[]" id="sup_iprice[0]" value="<?php echo $sup_iprice; ?>" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
												<td style="width:1px;visibility:hidden;text-align:center;"><input type="text" name="sup_tamt[]" id="sup_tamt[0]" value="<?php echo $sup_tamt; ?>" class="form-control amount-format" style="width:1px;visibility:hidden;" readonly></td>
												<td>
                                                    <table>
                                                        <tr>
                                                            <td><input type="checkbox" class="form-control1" name="tds_flag[]" id="tds_flag[0]" <?php if($tds_tamt > 0){ echo "checked"; } ?> onchange="calculatetotal()"></td>
                                                            <td style="width:5px;visibility:hidden;"><input type="text" name="tds_tamt[]" id="tds_tamt[0]" value="<?php echo $tds_tamt; ?>" class="form-control" readonly></td>
                                                        </tr>
                                                    </table>
                                                    
                                                </td>
												<td><input type="text" name="sup_famt[]" id="sup_famt[0]" value="<?php echo $sup_famt; ?>" class="form-control amount-format" readonly></td>
												<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 150px;" onchange="fetchprice(this.id)"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>" <?php if($cnames == $cc){ echo "selected"; } ?>><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="cus_iprice[]" id="cus_iprice[0]" value="<?php echo $cus_iprice; ?>" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
												<td style="width:1px;visibility:hidden;text-align:center;"><input type="text" name="cus_tamt[]" id="cus_tamt[0]" value="<?php echo $cus_tamt; ?>" class="form-control amount-format" style="width:1px;visibility:hidden;" readonly></td>
												<td>
                                                    <table>
                                                        <tr>
                                                            <td><input type="checkbox" class="form-control1" name="tcs_flag[]" id="tcs_flag[0]" <?php if($tcs_tamt > 0){ echo "checked"; } ?> onchange="calculatetotal()"></td>
                                                            <td style="width:5px;visibility:hidden;"><input type="text" name="tcs_tamt[]" id="tcs_tamt[0]" value="<?php echo $tcs_tamt; ?>" class="form-control" readonly></td>
                                                        </tr>
                                                    </table>
                                                    
                                                </td>
												<td><input type="text" name="cus_famt[]" id="cus_famt[0]" value="<?php echo $cus_famt; ?>" class="form-control amount-format" readonly></td>
												<td><input type="text" name="vehicle[]" id="vehicle[0]" value="<?php echo $vehicle; ?>" class="form-control" style="width:130px;" ></td>
												<td><input type="text" name="driver[]" id="driver[0]" value="<?php echo $driver; ?>" class="form-control" style="width:130px;" ></td>
												<td style="width: auto;"><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"><?php echo $narr; ?></textarea></td>
												<td style="width: 60px;"></td>
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
												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_jval" id="tot_jval" class="form-control" style="text-align:right;" readonly /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_bval" id="tot_bval" class="form-control" style="text-align:right;" readonly /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_wval" id="tot_wval" class="form-control" style="text-align:right;" readonly /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_ewval" id="tot_ewval" class="form-control" style="text-align:right;" readonly /></td>
												<td><input type="text" name="tot_nwval" id="tot_nwval" class="form-control" style="text-align:right;" readonly /></td>
												<td></td>
												<td><input type="text" name="tot_tamt" id="tot_tamt" class="form-control" style="text-align:right;" readonly></td>
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
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Id Value</label>
												<input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id."@".$link_trnum; ?>" >
											</div>
										</div>
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
					//document.getElementById("submittrans").disabled = true;
					return l;
				}
				else if(l == false){
					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					return l;
				}
				else {
					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					alert("Invalid");
					return false;
				}
			}
			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "chicken_display_pursale1.php?cid="+a;
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
				var incr = document.getElementById("incr").value;
				var tds_value = document.getElementById("tds_value").value; if(tds_value == ""){ tds_value = 0; }
				var tcs_value = document.getElementById("tcs_value").value; if(tcs_value == ""){ tcs_value = 0; }
				var nwval = sup_iprice = sup_tamt = cus_iprice = cus_tamt = tds_flag = tds_tamt = tcs_flag = tcs_tamt = sup_famt = cus_famt = 0;
				for(var d = 0;d <= incr;d++){
					nwval = sup_iprice = sup_tamt = tds_flag = tds_tamt = sup_famt = 0;
					nwval = document.getElementById("nwval["+d+"]").value; if(nwval == ""){ nwval = 0; }
					sup_iprice = document.getElementById("sup_iprice["+d+"]").value; if(sup_iprice == ""){ sup_iprice = 0; }
					sup_tamt = parseFloat(nwval) * parseFloat(sup_iprice); if(sup_tamt == ""){ sup_tamt = 0; }
					document.getElementById("sup_tamt["+d+"]").value = parseFloat(sup_tamt).toFixed(2);
					
					//TDS Calculation
                    if(document.getElementById("tds_flag["+d+"]").checked == true){ tds_flag = 1; }
					if(tds_flag == 1){
						if(parseFloat(tds_value) > 0){
							tds_tamt = (parseFloat(sup_tamt) * (parseFloat(tds_value) / 100)); document.getElementById("tds_tamt["+d+"]").value = parseFloat(tds_tamt).toFixed(2);
							sup_famt = (parseFloat(sup_tamt) + parseFloat(tds_tamt)); document.getElementById("sup_famt["+d+"]").value = parseFloat(sup_famt).toFixed(2);
						}
						else{
							alert("TDS Value not defined in Master, kindly check and try again ...!");
							document.getElementById("tds_tamt["+d+"]").value = 0;
							document.getElementById("sup_famt["+d+"]").value = parseFloat(sup_tamt).toFixed(2);
						}
					}
					else{
						document.getElementById("tds_tamt["+d+"]").value = 0;
						document.getElementById("sup_famt["+d+"]").value = parseFloat(sup_tamt).toFixed(2);
					}
					cus_iprice = cus_tamt = tcs_flag = tcs_tamt = cus_famt = 0;
					cus_iprice = document.getElementById("cus_iprice["+d+"]").value; if(cus_iprice == ""){ cus_iprice = 0; }
					cus_tamt = parseFloat(nwval) * parseFloat(cus_iprice); if(cus_tamt == ""){ cus_tamt = 0; }
					document.getElementById("cus_tamt["+d+"]").value = parseFloat(cus_tamt).toFixed(2);
					
					//TCS Calculation
                    if(document.getElementById("tcs_flag["+d+"]").checked == true){ tcs_flag = 1; }
					if(tcs_flag == 1){
						if(parseFloat(tds_value) > 0){
							tcs_tamt = (parseFloat(cus_tamt) * (parseFloat(tcs_value) / 100)); document.getElementById("tcs_tamt["+d+"]").value = parseFloat(tcs_tamt).toFixed(2);
							cus_famt = (parseFloat(cus_tamt) + (parseFloat(tcs_tamt))); document.getElementById("cus_famt["+d+"]").value = parseFloat(cus_famt).toFixed(2);
						}
						else{
							alert("TCS Value not defined in Master, kindly check and try again ...!");
							document.getElementById("tcs_tamt["+d+"]").value = 0;
							document.getElementById("cus_famt["+d+"]").value = parseFloat(cus_tamt).toFixed(2);
						}
					}
					else{
						document.getElementById("tcs_tamt["+d+"]").value = 0;
						document.getElementById("cus_famt["+d+"]").value = parseFloat(cus_tamt).toFixed(2);
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
			function validate_count(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
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
            function fetch_sup_branches(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var scode = document.getElementById("snames["+d+"]").value;

                removeAllOptions(document.getElementById("supbrh_code["+d+"]"));
                myselect1 = document.getElementById("supbrh_code["+d+"]");
                theOption1 = document.createElement("OPTION");
                theText1 = document.createTextNode("select");
                theOption1.value = "select";
                theOption1.appendChild(theText1);
                myselect1.appendChild(theOption1);
				 
                if(scode != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "chicken_fetch_supplier_branch.php?scode="+scode+"&row_count="+d;
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var brh_dt1 = this.responseText;
                            var brh_dt2 = brh_dt1.split("[@$&]");
                            var count = brh_dt2[0];
                            var rows = brh_dt2[1];
                            var brh_lt1 = brh_dt2[2];
                            if(parseInt(count) > 0){
                                var obj = JSON.parse(brh_lt1);
                                var i = 0; oval = ovl = "";
                                for(i = 0;i < count;i++){
                                    theOption1=document.createElement("OPTION");
                                    theText1=document.createTextNode(obj[i].name);
                                    theOption1.value = obj[i].code;
                                    theOption1.appendChild(theText1);
                                    myselect1.appendChild(theOption1);
                                }
                            }
                            else{
                                alert("Branch details not available, please check and try again.");
                            }
                        }
                    }
                }
            }
            calculatetotal(); calfinaltotal();
		</script>
	</body>
</html>