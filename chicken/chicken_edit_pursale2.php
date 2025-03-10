<?php
	//chicken_edit_pursale2.php
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
			.bg-danger{
				background-color: #dc3545 !important;
			}
			.text-white{
				color: white !important;
			}
			.bg-success {
				background-color: #28a745 !important;
			}
			/* input:focus {
				box-shadow: 0 0 25px rgba(104, 179, 219, 0.5) !important;
				border-color: rgb(104, 179, 219) !important;
			}  */


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

				$sql = "SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $group_code = $group_name = array();
				while($row = mysqli_fetch_assoc($query)){ $group_code[$row['code']] = $row['code']; $group_name[$row['code']] = $row['description']; }

				// $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
                // $query = mysqli_query($conn,$sql); $cont_code = $cont_name = array();
				// while($row = mysqli_fetch_assoc($query)){ $cont_code[$row['code']] = $row['code']; $cont_name[$row['code']] = $row['description']; $cont_gname[$row['code']] = $row['groupcode']; }

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
					if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cont_gname[$row['code']] = $row['groupcode']; } else{ }
					if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
				}
			?>
				<div class="container mt-5">
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
                                
                                $sup_famt = $row['finaltotal'];
                            }
                        }
                    ?>
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="chicken_modify_pursale2.php" method="post" role="form" onsubmit="return checkval()">
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
										<label>Groups<b style="color:red;">&nbsp;*</b></label>
										<select name="gcodes" id="gcodes" class="form-control select2" style="width: 100%;">
											<?php 
											// Get the groupcode for the selected customer
											$selected_groupcode = isset($cont_gname[$cnames]) ? $cont_gname[$cnames] : ''; 
											foreach($group_code as $gt){ 
												// Set the selected option in the group dropdown
												$selected = ($group_code[$gt] == $selected_groupcode) ? "selected" : "";
												echo "<option value='" . $group_code[$gt] . "' $selected>" . $group_name[$gt] . "</option>";
											} 
											?>
										</select>
									</div>
									<div class="form-group col-md-2">
										<label>Item Description<b style="color:red;">&nbsp;*</b></label>
										<select name="scat" id="scat" class="form-control select2" style="width:150px;" onchange="checkitemtype(this.id);fetchprice(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>" <?php if($scat == $ic){ echo "selected"; } ?>><?php echo $item_name[$ic]; ?></option><?php } ?></select>
									</div>
									<div class="form-group col-md-2">
										<label>Supplier Branch<b style="color:red;">&nbsp;*</b></label>
										<select name="supbrh_code" id="supbrh_code" class="form-control select2" style="width:150px;">
											<option value="select">-select-</option>
											<?php
                                            $sql = "SELECT * FROM `chicken_supplier_branch` WHERE `sup_code` LIKE '$snames' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
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
										<table style="width:103%;line-height:30px;" id="tab3" class="table table-bordered table-striped">
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
												
												<th style="text-align:center;"><label>Amount</label></th>
												<th style="text-align:center;"><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:1px;visibility:hidden;text-align:center;"></th>
												
												<th style="text-align:center;"><label>Amount</label></th>
												<th style="text-align:center;"><label>Vehicle</label></th>
												<th style="text-align:center;"><label>Driver</label></th>
												<th style="text-align:center;"><label>Remarks</label></th>
												<th style="text-align:center;"></th>
												<!--<th><label>Outstanding<b style="color:red;">&nbsp;*</b></label</th>>-->
											</tr>
											<tbody id="bodytab">
											<tr id="tblrow" style="margin:5px 0px 5px 0px;">
												<td style="width: 150px;padding-right:5px;"><select name="snames" id="snames" class="form-control select2" style="width: 150px;" onchange="fetch_sup_branches(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]."@".$sup_name[$cc]; ?>" <?php if($snames == $cc){ echo "selected"; } ?>><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="bno" id="bno" value="<?php echo $bno; ?>" class="form-control" /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval" id="jval" value="<?php echo $jval; ?>" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval" id="bval" value="<?php echo $bval; ?>" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval" id="wval" value="<?php echo $wval; ?>" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>
												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval" id="ewval" value="<?php echo $ewval; ?>" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>
												<td><input type="text" name="nwval" id="nwval" value="<?php echo $nwval; ?>" style="width:90xp;" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>
												<td><input type="text" name="sup_iprice" id="sup_iprice" value="<?php echo $sup_iprice; ?>" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
												<td style="width:1px;visibility:hidden;text-align:center;"><input type="text" name="sup_tamt" id="sup_tamt" value="<?php echo $sup_tamt; ?>" class="form-control amount-format" style="width:1px;visibility:hidden;" readonly></td>
												
												<td><input type="text" name="sup_famt" id="sup_famt" value="<?php echo $sup_famt; ?>" class="form-control amount-format" readonly></td>
												<td style="width: 150px;padding-right:5px;"><select name="cnames" id="cnames" class="form-control select2" style="width: 150px;" onchange="fetchprice(this.id)"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>" <?php if($cnames == $cc){ echo "selected"; } ?>><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="cus_iprice" id="cus_iprice" value="<?php echo $cus_iprice; ?>" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
												<td style="width:1px;visibility:hidden;text-align:center;"><input type="text" name="cus_tamt" id="cus_tamt" value="<?php echo $cus_tamt; ?>" class="form-control amount-format" style="width:1px;visibility:hidden;" readonly></td>
												
												<td><input type="text" name="cus_famt" id="cus_famt" value="<?php echo $cus_famt; ?>" class="form-control amount-format" readonly></td>
												<td><input type="text" name="vehicle" id="vehicle" value="<?php echo $vehicle; ?>" class="form-control" style="width:130px;" ></td>
												<td><input type="text" name="driver" id="driver" value="<?php echo $driver; ?>" class="form-control" style="width:130px;" ></td>
												<td style="width: auto;"><textarea name="narr" id="narr" class="form-control" style="height:23px;"><?php echo $narr; ?></textarea></td>
												<td style="width: 60px;"></td>
											</tr>
											</tbody>
										</table><br/>
										<table style="width:100%;line-height:30px;" class="table table-bordered table-striped">
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
												<!--<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding" id="outstanding" style="width:50px;"></td>-->
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
											<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-sm text-white bg-success">
												 Update
											</button>&ensp;&ensp;&ensp;&ensp;
											<button type="button" name="cancelled" id="cancelled" class="btn btn-sm text-white bg-danger" onclick="redirection_page()">
												 Cancel
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
		     function checkval() {
				document.getElementById("submittrans").style.visibility = "hidden";
				var sup_name = net_wht = sup_prc = sup_amt = cus_name = cus_prc = cus_amt = asup_amt = acus_amt = ""; 
				var l = true;

				sup_name = document.getElementById("snames").value;
				net_wht = document.getElementById("nwval").value;
				sup_prc = document.getElementById("sup_iprice").value;
				sup_amt = document.getElementById("sup_tamt").value;
				cus_name = document.getElementById("cnames").value;
				cus_prc = document.getElementById("cus_iprice").value;
				cus_amt = document.getElementById("cus_tamt").value;

				asup_amt = parseFloat(net_wht) * parseFloat(sup_prc);
				acus_amt = parseFloat(net_wht) * parseFloat(cus_prc);

				if (sup_name.match("select")) {
					alert("Please select Supplier");
					document.getElementById("snames").focus();
					l = false;
				} else if (net_wht.length == 0 || net_wht == 0 || net_wht == 0.00 || net_wht == "0" || net_wht == "0.00") {
					alert("Please enter net weight");
					document.getElementById("nwval").focus();
					l = false;
				} else if (sup_prc.length == 0 || sup_prc == 0 || sup_prc == 0.00 || sup_prc == "0" || sup_prc == "0.00") {
					alert("Please enter Supplier Price");
					document.getElementById("sup_iprice").focus();
					l = false;
				} else if (sup_amt.length == 0 || sup_amt == 0 || sup_amt == 0.00 || sup_amt == "0" || sup_amt == "0.00") {
					alert("Please enter Supplier Amount");
					document.getElementById("sup_tamt").value = asup_amt;
					l = false;
				} else if (cus_name.match("select")) {
					alert("Please select Customer");
					document.getElementById("cnames").focus();
					l = false;
				} else if (cus_prc.length == 0 || cus_prc == 0 || cus_prc == 0.00 || cus_prc == "0" || cus_prc == "0.00") {
					alert("Please enter Customer Price");
					document.getElementById("cus_iprice").focus();
					l = false;
				} else if (cus_amt.length == 0 || cus_amt == 0 || cus_amt == 0.00 || cus_amt == "0" || cus_amt == "0.00") {
					alert("Please enter Customer Amount");
					document.getElementById("cus_tamt").value = acus_amt;
					l = false;
				} else if (sup_name == cus_name) {
					alert("Both Supplier and Customer selected same. Kindly change and try again");
					document.getElementById("cnames").focus();
					l = false;
				}

				if (l == true) {
					return l;
				} else {
					document.getElementById("submittrans").style.visibility = "visible";
					return l;
				}
			}

			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "chicken_display_pursale2.php?cid="+a;
			}
			function checkitemtype(x) {
				var r = document.getElementById("itemfields").value;
				var b = document.getElementById("scat").value;
				var d = b.split("@")[1].search(/Birds/i);

				if (r.match("BAW")) {
					document.getElementById("bval").style.visibility = d > 0 ? "visible" : "hidden";
				} else if (r.match("JBEW")) {
					document.getElementById("jval").style.visibility = d > 0 ? "visible" : "hidden";
					document.getElementById("bval").style.visibility = d > 0 ? "visible" : "hidden";
				} else if (r.match("JBTEN")) {
					document.getElementById("jval").style.visibility = d > 0 ? "visible" : "hidden";
					document.getElementById("bval").style.visibility = d > 0 ? "visible" : "hidden";
					document.getElementById("wval").style.visibility = d > 0 ? "visible" : "hidden";
					document.getElementById("ewval").style.visibility = d > 0 ? "visible" : "hidden";
					if (d <= 0) {
						document.getElementById("nwval").readOnly = false;
					}
				}
			}

			function calculatenetwt() {
				var wval = document.getElementById("wval").value;
				if (wval == "" || wval == 0 || wval == "0.00" || wval == 0.00) { wval = 0; }
				
				var ewval = document.getElementById("ewval").value;
				if (ewval == "" || ewval == 0 || ewval == "0.00" || ewval == 0.00) { ewval = 0; }
				
				var net_wt = parseFloat(wval) - parseFloat(ewval);
				document.getElementById("nwval").value = net_wt.toFixed(2);
			}

			function calculatetotal() {
				var net_wht = document.getElementById("nwval").value;
				var sup_iprice = document.getElementById("sup_iprice").value;
				var cus_iprice = document.getElementById("cus_iprice").value;

				// Defaulting to 0 if the values are empty or zero
				net_wht = net_wht == "" || net_wht == 0 || net_wht == "0.00" ? 0 : net_wht;
				sup_iprice = sup_iprice == "" || sup_iprice == 0 || sup_iprice == "0.00" ? 0 : sup_iprice;
				cus_iprice = cus_iprice == "" || cus_iprice == 0 || cus_iprice == "0.00" ? 0 : cus_iprice;

				var sup_tamt = parseFloat(net_wht) * parseFloat(sup_iprice);
				document.getElementById("sup_tamt").value = sup_tamt.toFixed(2);

				var cus_tamt = parseFloat(net_wht) * parseFloat(cus_iprice);
				document.getElementById("cus_tamt").value = cus_tamt.toFixed(2);
			}

			function fetchprice() {
				var pst_prate_flag = '<?php echo $pst_prate_flag; ?>';
				if (pst_prate_flag == 1 || pst_prate_flag == "1") {
					var c_name = document.getElementById("cnames").value;
					var s_cat = document.getElementById("scat").value;

					if (!c_name.match("select") && !s_cat.match("select")) {
						var prices = new XMLHttpRequest();
						var method = "GET";
						var url = "cus_papersaleprice.php?pname=" + c_name + "&iname=" + s_cat;
						var asynchronous = true;

						prices.open(method, url, asynchronous);
						prices.send();

						prices.onreadystatechange = function () {
							if (this.readyState == 4 && this.status == 200) {
								var price = this.responseText;
								var cus_iprice_elem = document.getElementById("cus_iprice");

								if (price == "" || price == 0 || price == 0.00 || price == "0.00" || price == "0") {
									cus_iprice_elem.readOnly = false;
									cus_iprice_elem.value = "";
								} else {
									cus_iprice_elem.readOnly = true;
									cus_iprice_elem.value = price;
								}
								calculatetotal();
							}
						}
					}
				}
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
			function calfinaltotal() {
				var itemType = document.getElementById("itemfields").value;

				if (itemType.match("WT")) {
					var nwht_val = parseFloat(document.getElementById("nwval").value) || 0;
					var tamt_val = parseFloat(document.getElementById("cus_tamt").value) || 0;
					
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				} else if (itemType.match("BAW")) {
					var birds_val = parseFloat(document.getElementById("bval").value) || 0;
					var nwht_val = parseFloat(document.getElementById("nwval").value) || 0;
					var tamt_val = parseFloat(document.getElementById("cus_tamt").value) || 0;
					
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				} else if (itemType.match("JBEW")) {
					var jal_val = parseFloat(document.getElementById("jval").value) || 0;
					var birds_val = parseFloat(document.getElementById("bval").value) || 0;
					var nwht_val = parseFloat(document.getElementById("nwval").value) || 0;
					var tamt_val = parseFloat(document.getElementById("cus_tamt").value) || 0;
					
					document.getElementById("tot_jval").value = jal_val;
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				} else if (itemType.match("JBTEN")) {
					var jal_val = parseFloat(document.getElementById("jval").value) || 0;
					var birds_val = parseFloat(document.getElementById("bval").value) || 0;
					var twht_val = parseFloat(document.getElementById("wval").value) || 0;
					var ewht_val = parseFloat(document.getElementById("ewval").value) || 0;
					var nwht_val = parseFloat(document.getElementById("nwval").value) || 0;
					var tamt_val = parseFloat(document.getElementById("cus_tamt").value) || 0;
					
					document.getElementById("tot_jval").value = jal_val;
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_wval").value = twht_val.toFixed(2);
					document.getElementById("tot_ewval").value = ewht_val.toFixed(2);
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				} else {
					alert("Kindly contact Admin for any support..!");
				}
			}

			function fetch_sup_branches() {
				var scode = document.getElementById("snames").value;

				removeAllOptions(document.getElementById("supbrh_code"));
				var myselect1 = document.getElementById("supbrh_code");
				var theOption1 = document.createElement("OPTION");
				var theText1 = document.createTextNode("select");
				theOption1.value = "select";
				theOption1.appendChild(theText1);
				myselect1.appendChild(theOption1);

				if (scode != "select") {
					var fetch_items = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_fetch_supplier_branch.php?scode=" + scode;
					var asynchronous = true;
					fetch_items.open(method, url, asynchronous);
					fetch_items.send();
					fetch_items.onreadystatechange = function () {
						if (this.readyState == 4 && this.status == 200) {
							var brh_dt1 = this.responseText;
							var brh_dt2 = brh_dt1.split("[@$&]");
							var count = parseInt(brh_dt2[0]);
							var brh_lt1 = brh_dt2[2];

							if (count > 0) {
								var obj = JSON.parse(brh_lt1);
								for (var i = 0; i < count; i++) {
									var theOption = document.createElement("OPTION");
									var theText = document.createTextNode(obj[i].name);
									theOption.value = obj[i].code;
									theOption.appendChild(theText);
									myselect1.appendChild(theOption);
								}
							} else {
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