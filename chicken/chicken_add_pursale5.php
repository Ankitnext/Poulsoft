<?php
//chicken_add_pursale5.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Closing Stock"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

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

				// $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
				// $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				// $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
				// $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcsper = $row['tcds']; }
				 $idisplay = ''; $ndisplay = 'style="display:none;';

                $group_details = array();
                $sql = "select code,description from main_groups WHERE gtype LIKE '%C%'";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $group_details[$row['code']] = $row['description'];
                }

    
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Closing Stock</div>
                    <form action="chicken_save_pursale5.php" method="post" role="form" onsubmit="return checkval()">
						<div class="row">
							<div class="form-group col-md-1">
								<label>Date<b style="color:red;">&nbsp;*</b></label>
								<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" id="slc_datepickers" readonly>
							</div>
							<div class="form-group col-md-2">
								<label>Warehouse<b style="color:red;">&nbsp;*</b></label>
								<select name="wcodes" id="wcodes" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select>
							</div>
							<div class="form-group col-md-2">
								<label>Group<b style="color:red;">&nbsp;*</b></label>
								<select name="grpcodes" id="grpcodes" class="form-control select2" style="width: 100%;" onchange="fetch_group_customer(this.id);"> <?php foreach ($group_details as $code => $description) { ?>
								<option value="<?php echo $code; ?>"><?php echo $description; ?></option>
									<?php } ?></select>
							</div>
							<div class="form-group col-md-2">
								<label>Item Description<b style="color:red;">&nbsp;*</b></label>
								<select name="scat" id="scat" class="form-control select2" style="width:100%;" onchange="checkitemtype();"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select>
							</div>
							<!-- <div class="form-group col-md-2" style="visibility:visible;">
								<label>Supplier Price</label>
								<input type="text" class="form-control" name="sup_price" id="sup_price" value="" onkeyup="validatenum(this.id);add_supplier_prices2();" onchange="validateamount(this.id)" />
							</div> -->
							<div class="form-group col-md-2" style="visibility:visible;">
								<label>Customer Price</label>
								<input type="text" class="form-control" name="cus_price" id="cus_price" value="" onkeyup="validatenum(this.id);add_customer_prices();" onchange="validateamount(this.id)" />
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
						<div class="row">
							<div class="form-group col-md-2" style="visibility:visible;">
								<label>Supplier<b style="color:red;">&nbsp;*</b></label>
								<select name="snames" id="snames" class="form-control select2" style="width: 250px;" onchange="fetch_sup_branches2(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select>
							</div>
							<div class="form-group col-md-2" style="visibility:visible;">
								<label>Branch</label>
								<select name="supbrh_code" id="supbrh_code" class="form-control select2" style="width: 250px;"><option value="select">-select-</option></select>
							</div>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>Bill No.</label>
								<input type="text" class="form-control" name="bno" id="bno" value="" />
							</div>
							<?php if($ifjbwen == 1 || $ifjbw == 1){ ?>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>Jals</label>
								<input type="text" class="form-control" name="jval" id="jval" value="" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>Birds</label>
								<input type="text" class="form-control" name="bval" id="bval" value="" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 ){ ?>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>T. Wt.</label>
								<input type="text" class="form-control" name="wval" id="wval" class="form-control amount-format" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 ){ ?>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>E. Wt.</label>
								<input type="text" class="form-control" name="ewval" id="ewval" class="form-control amount-format" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" />
							</div>
							<?php } ?>

							<div class="form-group col-md-1" style="visibility:visible;">
								<label>N. Wt.<b style="color:red;">&nbsp;*</b></label>
								<input type="text" class="form-control" name="nwval" id="nwval" value="" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"/>
							</div>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>Price<b style="color:red;">&nbsp;*</b></label>
								<input type="text" class="form-control" name="sup_iprice" id="sup_iprice" value="" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"/>
							</div>
							<div class="form-group col-md-1" style="visibility:visible;">
								<label>Amount<b style="color:red;">&nbsp;*</b></label>
								<input type="text" name="sup_famt" id="sup_famt" value=""  class="form-control amount-format" readonly/>
								<td style="width:1px;visibility:hidden;"><input type="text" name="sup_tamt" id="sup_tamt" class="form-control amount-format" style="width:1px;" readonly></td>							</div>
						    </div>
						</div>
						<div class="col-md-18 row_body2">
							<table style="width:auto;line-height:30px;" id="tab3">
								<tr style="line-height:30px;">
									<!-- <th style="text-align:center;"><label>Supplier<b style="color:red;">&nbsp;*</b></label></th> -->
									<!-- <th style="text-align:center;"><label>Branch</label></th> -->
									<!-- <th style="text-align:center;"><label>Bill No.</label></th> -->
									<!-- <th style="text-align:center;"><label>N. Wt.<b style="color:red;">&nbsp;*</b></label></th> -->
									<!-- <th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th> -->
									<!-- <th style="width:1px;visibility:hidden;text-align:center;"></th> -->
									<!-- <th style="text-align:center;"><label>Amount</label></th> -->
									<th style="text-align:center;"><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
									<?php
										if($ifjbwen == 1 || $ifjbw == 1){ echo "<th style='text-align:center;'><label>Jals</label></th>"; }
										if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<th style='text-align:center;'><label>Birds</label></th>"; }
										if($ifjbwen == 1){ echo "<th style='text-align:center;'><label>cT. Wt.</label></th><th style='text-align:center;'><label>E. Wt.</label></th>"; }
									?>
									<th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
									<th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
									<th style="width:1px;visibility:hidden;text-align:center;"></th>
									<th style="text-align:center;"><label>Amount</label></th>
									<th style="text-align:center;"><label>Vehicle</label></th>
									<th style="text-align:center;"><label>Driver</label></th>
									<th style="text-align:center;"><label>Remarks</label></th>
									<th style="text-align:center;"></th>
								</tr>
								<tbody id="bodytab">
								<tr id="tblrow[0]" style="margin:5px 0px 2px 0px;">
									<!-- <td style="width: 150px;padding-right:5px;"><select name="snames[]" id="snames[0]" class="form-control select2" style="width: 150px;" onchange="fetch_sup_branches(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]."@".$sup_name[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td> -->
									<!-- <td style="width: 150px;padding-right:5px;"><select name="supbrh_code[]" id="supbrh_code[0]" class="form-control select2" style="width: 150px;"><option value="select">-select-</option></select></td> -->
									<!-- <td><input type="text" name="bno[]" id="bno[0]" class="form-control" /></td> -->
									<!-- <td><input type="text" name="sup_iprice[]" id="sup_iprice[0]" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td> -->
									<!-- <td style="width:1px;visibility:hidden;"><input type="text" name="sup_tamt[]" id="sup_tamt[0]" class="form-control amount-format" style="width:1px;" readonly></td> -->
									<!-- <td><input type="text" name="sup_famt[]" id="sup_famt[0]" class="form-control amount-format" readonly></td> -->
									<!-- <td><input type="text" name="nwval[]" id="nwval[0]" value="" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td> -->
									
									<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cjval[]" id="cjval[0]" value="" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cbval[]" id="cbval[0]" value="" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cwval[]" id="cwval[0]" class="form-control amount-format" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" /></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cewval[]" id="cewval[0]" class="form-control amount-format" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" /></td>
									<td><input type="text" name="cus_qty[]" id="cus_qty[0]" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
									<td><input type="text" name="cus_iprice[]" id="cus_iprice[0]" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
									<td style="width:1px;visibility:hidden;"><input type="text" name="cus_tamt[]" id="cus_tamt[0]" class="form-control amount-format" style="width:1px;" readonly></td>
								
									<td><input type="text" name="cus_famt[]" id="cus_famt[0]" class="form-control amount-format" readonly></td>
									<td><input type="text" name="vehicle[]" id="vehicle[0]" class="form-control" style="width:130px;" ></td>
									<td><input type="text" name="driver[]" id="driver[0]" class="form-control" style="width:130px;" ></td>
									<td style="width: auto;"><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
									<td style="width: 60px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="removerow(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
								</tr>
								</tbody>
							</table><br/>
							<!-- <table style="width:100%;line-height:30px;">
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
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_jval" id="tot_jval" class="form-control amount-format" readonly /></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_bval" id="tot_bval" class="form-control amount-format" readonly /></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_wval" id="tot_wval" class="form-control amount-format" readonly /></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_ewval" id="tot_ewval" class="form-control amount-format" readonly /></td>
									<td><input type="text" name="tot_nwval" id="tot_nwval" class="form-control amount-format" readonly /></td>
									<td></td>
									<td><input type="text" name="tot_tamt" id="tot_tamt" class="form-control amount-format" readonly></td>
									<td style="width: auto;"></td>
									<td style="width: 60px;"></td>
								</tr>
							<table> -->
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
									<button type="submittrans" name="submittrans" id="submit" value="addpage" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
					                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
							</div>
						</div>
					</form>
            </div>

            <script>
                function return_back(){
                    window.location.href = "chicken_display_pursale5.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = code = mtype = ccode = ""; var birds = price = amount = c = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            code = document.getElementById("code["+d+"]").value;
                            birds = document.getElementById("birds["+d+"]").value; if(birds == ""){ birds = 0; }
                            price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            mtype = document.getElementById("mtype["+d+"]").value;
                            ccode = document.getElementById("ccode["+d+"]").value;
                            
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(code == "" || code == "select"){
                                alert("Please select Item in row: "+c);
                                document.getElementById("code["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(birds) == 0){
                                alert("Please enter Birds in row: "+c);
                                document.getElementById("birds["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(price) == 0){
                                alert("Please enter Price in row: "+c);
                                document.getElementById("price["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Birds/Price in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                            }
                            else if(mtype == "" || mtype == "select"){
                                alert("Please select Mortality On in row: "+c);
                                document.getElementById("mtype["+d+"]").focus();
                                l = false;
                            }
                            else if(ccode == "" || ccode == "select"){
                                alert("Please select Customer / Warehouse in row: "+c);
                                document.getElementById("ccode["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                    if(l == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                function rowgen(a){
                    console.log('<?php echo $ifjbwen." ".$ifjbw  ?>');
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				document.getElementById("addval["+d+"]").style.visibility = "hidden";
				document.getElementById("rmval["+d+"]").style.visibility = "hidden";
				d++; var e = d; document.getElementById("incr").value = e;
				html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;" id="tblrow['+e+']">';
				html+= '<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames['+e+']" class="form-control select" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cjval[]" id="cjval['+e+']" value="" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>';
				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cbval[]" id="cbval['+e+']" value="" onkeyup="validate_count(this.id);calfinaltotal();" class="form-control amount-format" /></td>';
				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cwval[]" id="cwval['+e+']" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>';
				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="cewval[]" id="cewval['+e+']" value="" onkeyup="validatenum(this.id);calculatenetwt(this.id);calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format" /></td>';
				html+= '<td><input type="text" name="cus_qty[]" id="cus_qty['+e+']" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>';
				html+= '<td><input type="text" name="cus_iprice[]" id="cus_iprice['+e+']" onkeyup="validatenum(this.id);calculatetotal();calfinaltotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>';
				html+= '<td style="width:1px;visibility:hidden;"><input type="text" name="cus_tamt[]" id="cus_tamt['+e+']" class="form-control amount-format" style="width:1px;" readonly></td>';
				html+= '<td><input type="text" name="cus_famt[]" id="cus_famt['+e+']" class="form-control amount-format" readonly></td>';
				html+= '<td><input type="text" name="vehicle[]" id="vehicle['+e+']" class="form-control" style="width:130px;" ></td>';
				html+= '<td><input type="text" name="driver[]" id="driver['+e+']" class="form-control" style="width:130px;" ></td>';
				html+= '<td style="width: auto;"><textarea name="narr[]" id="narr['+e+']" class="form-control" style="height:23px;"></textarea></td>';
				html+= '<td style="width: 60px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+e+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+e+']" onclick="removerow(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html+= '</tr>';
				$('#bodytab').append(html); $('.select').select2();
				document.getElementById("addval["+e+"]").style.visibility = "visible";
				document.getElementById("rmval["+e+"]").style.visibility = "visible";
                checkitemtype(); add_supplier_prices2(e); add_customer_prices2(e);
                 var x = "addval["+d+"]";
                  // fetch_group_customer(x);
			}

            function calculatenetwt(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var e = document.getElementById("wval").value;
				if(e == "" || e == 0 || e == "0.00" || e == 0.00){ e = 0; }
				var f = document.getElementById("ewval").value;
				if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
				
				var g = parseFloat(e) - parseFloat(f);
				document.getElementById("nwval").value = g.toFixed(2);
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
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }

                 function add_supplier_prices(){
                var sup_price = document.getElementById("sup_price").value;
                var incr = document.getElementById("incr").value;
                if(sup_price == ""){ }
                else{ for(var a = 0;a <= incr;a++){ document.getElementById("sup_iprice").value = parseFloat(sup_price).toFixed(2); } }
                calculatetotal();
            }
            function add_supplier_prices2(a){
                var sup_price = document.getElementById("sup_price").value;
                if(sup_price == ""){ }
                else{ document.getElementById("sup_iprice").value = parseFloat(sup_price).toFixed(2); }
                calculatetotal();
            }
            function add_customer_prices(){
                var cus_price = document.getElementById("cus_price").value;
                var incr = document.getElementById("incr").value;
                if(cus_price == ""){ }
                else{ for(var a = 0;a <= incr;a++){ document.getElementById("cus_iprice["+a+"]").value = parseFloat(cus_price).toFixed(2); } }
                calculatetotal();
            }
            function add_customer_prices2(a){
                var cus_price = document.getElementById("cus_price").value;
                if(cus_price == ""){ }
                else{ document.getElementById("cus_iprice["+a+"]").value = parseFloat(cus_price).toFixed(2); }
                calculatetotal();
            }
            function calculatetotal(){
				var a = document.getElementById("incr").value;
				var tds_value = document.getElementById("tds_value").value;
				var tcs_value = document.getElementById("tcs_value").value;
				var e = g = h = i = j = k = 0.00; var tds_flag = tds_tamt = tcs_flag = tcs_tamt = sup_famt = cus_famt = 0;
				
					g = document.getElementById("nwval").value;
					if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
					j = document.getElementById("sup_iprice").value;
					if(j == "" || j == 0 || j == "0.00" || j == 0.00){ j = 0; }
					k = parseFloat(g) * parseFloat(j);
					
					document.getElementById("sup_famt").value = parseFloat(k).toFixed(2);
					document.getElementById("sup_tamt").value = k.toFixed(2);
				for(e = 0;e <= a;e++){	
					l = document.getElementById("cus_qty["+e+"]").value;
					h = document.getElementById("cus_iprice["+e+"]").value;
					if(h == "" || h == 0 || h == "0.00" || h == 0.00){ h = 0; }
					if(l == "" || l == 0 || l == "0.00" || l == 0.00){ l = 0; }
					i = parseFloat(l) * parseFloat(h);
					document.getElementById("cus_tamt["+e+"]").value = i.toFixed(2);
					document.getElementById("cus_famt["+e+"]").value = parseFloat(i).toFixed(2);
					document.getElementById("cus_famt["+e+"]").value = parseFloat(i).toFixed(2);
				}
			}
			function calculatetotal2() {
				var a = parseInt(document.getElementById("incr").value, 10);
				var tds_value = parseFloat(document.getElementById("tds_value").value) || 0;
				var tcs_value = parseFloat(document.getElementById("tcs_value").value) || 0;

				// var g = h = i = j = k = 0.00;
				

				// Get supplier values (single elements)
				var	g = parseFloat(document.getElementById("nwval").value) || 0;
				var	j = parseFloat(document.getElementById("sup_iprice").value) || 0;
				var	k = g * j;

			alert(k);

			document.getElementById("sup_famt").value = k.toFixed(2);
				// document.getElementById("sup_tamt").value = k.toFixed(2);

				// Loop through customer values (keeping original format for cus_iprice)
				for (var e = 0; e <= a; e++) {
					var cus_iprice = document.getElementById("cus_iprice["+e+"]"); // Keeping original format
					var cus_tamt = document.getElementById("cus_tamt["+e+"]");
					var cus_famt = document.getElementById("cus_famt["+e+"]");

					if (cus_iprice) {
						h = parseFloat(cus_iprice.value) || 0;
						i = g * h;

						if (cus_tamt) cus_tamt.value = i.toFixed(2);
						if (cus_famt) cus_famt.value = i.toFixed(2);
					}
				}
			}

            function checkitemtype(){
				var r = document.getElementById("itemfields").value;
				var b = ""; var c = []; var a = d = 0;
				var incr = document.getElementById("incr").value;
				b = document.getElementById("scat").value;
				c = b.split("@");
				d = c[1].search(/Birds/i);
				
                for(a = 0;a <= incr;a++){
                    if(r.match("BAW")){
                        if(d > 0){
                            document.getElementById("bval").style.visibility = "visible";
                        }
                        else{
                            document.getElementById("bval").style.visibility = "hidden";
                        }
                    }
                    else if(r.match("JBEW")){
                        if(d > 0){
                            document.getElementById("jval").style.visibility = "visible";
                            document.getElementById("bval").style.visibility = "visible";
                        }
                        else{
                            document.getElementById("jval").style.visibility = "hidden";
                            document.getElementById("bval").style.visibility = "hidden";
                        }
                    }
                    else if(r.match("JBTEN")){
                        if(d > 0){
                            document.getElementById("jval").style.visibility = "visible";
                            document.getElementById("bval").style.visibility = "visible";
                            document.getElementById("wval").style.visibility = "visible";
                            document.getElementById("ewval").style.visibility = "visible";
                        }
                        else{
                            document.getElementById("jval").style.visibility = "hidden";
                            document.getElementById("bval").style.visibility = "hidden";
                            document.getElementById("wval").style.visibility = "hidden";
                            document.getElementById("ewval").style.visibility = "hidden";
                            document.getElementById("nwval").readOnly = false;
                        }
                    }
                    else{ }
                }
			}
            function fetch_group_customer(a) {
				var selectedCode = document.getElementById(a).value;
				console.log("Selected Code:", selectedCode);
				var incr = document.getElementById("incr").value;
				
				if (selectedCode != "selected") {
					var fetch_items = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_fetch_customer_bycode.php?scode=" + selectedCode;
					var asynchronous = true;

					fetch_items.open(method, url, asynchronous);
					fetch_items.send();

					fetch_items.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							var customerData = JSON.parse(this.responseText);
							console.log("Customer Data:", customerData);
								
							if (customerData.length > 0) {
								for(var d = 0;d <= incr;d++){
								removeAllOptions(document.getElementById("cnames["+d+"]"));
									myselect = document.getElementById("cnames["+d+"]");
									theOption1=document.createElement("OPTION");
									theText1=document.createTextNode("-select-");
									theOption1.value = "select";
									theOption1.appendChild(theText1);
									myselect.appendChild(theOption1);
									
									customerData.forEach(function(customer) {
										var theOption1 = document.createElement("OPTION");
										var theText1 = document.createTextNode(customer.name);
										theOption1.value = customer.code;
										theOption1.appendChild(theText1);
										myselect.appendChild(theOption1);
									});
								}	

							} else {
								for(var d = 0;d <= incr;d++){
									removeAllOptions(document.getElementById("cnames["+d+"]"));
									myselect = document.getElementById("cnames["+d+"]");
									theOption1=document.createElement("OPTION");
									theText1=document.createTextNode("-select-");
									theOption1.value = "select";
									theOption1.appendChild(theText1);
									myselect.appendChild(theOption1);
									
								}
                                        alert("No customers found for the selected code.");
                            }
						}
					};
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

			function fetch_sup_branches2() {
				var scode = document.getElementById("snames").value;

				var selectElement = document.getElementById("supbrh_code");

				// Clear all existing options
				removeAllOptions(selectElement);

				// Add default "select" option
				var defaultOption = document.createElement("OPTION");
				defaultOption.value = "select";
				defaultOption.text = "select";
				selectElement.appendChild(defaultOption);

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
							var count = brh_dt2[0];
							var brh_lt1 = brh_dt2[2];

							if (parseInt(count) > 0) {
								var obj = JSON.parse(brh_lt1);
								for (var i = 0; i < obj.length; i++) {
									var option = document.createElement("OPTION");
									option.value = obj[i].code;
									option.text = obj[i].name;
									selectElement.appendChild(option);
								}
							} else {
								alert("Branch details not available, please check and try again.");
							}
						}
					};
				}
			}


            function calfinaltotal(){
				var a = document.getElementById("itemfields").value;
				var aa = document.getElementById("incr").value;
				if(a.match("WT")){
					var f = 0; var g = 0;
					var nwht_val = 0; var tamt_val = 0;
					f = document.getElementById("nwval").value;
					if(f == "" || f == 0 || f == "0.00" || f == 0.00){ f = 0; }
					nwht_val = parseFloat(nwht_val) + parseFloat(f);
					for(var j = 0;j <= aa;j++){
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
						c = document.getElementById("bval").value;
						if(c == "" || c == 0 || c == "0.00" || c == 0.00){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						f = document.getElementById("nwval").value;
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
						b = document.getElementById("jval").value;
						if(b == "" || b == 0 || b == "0.00" || b == 0.00){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
						c = document.getElementById("bval").value;
						if(c == "" || c == 0 || c == "0.00" || c == 0.00){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						f = document.getElementById("nwval").value;
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
						b = document.getElementById("jval").value;
						if(b == "" || b == 0 || b == "0.00" || b == 0.00){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
						c = document.getElementById("bval").value;
						if(c == "" || c == 0 || c == "0.00" || c == 0.00){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						d = document.getElementById("wval").value;
						if(d == "" || d == 0 || d == "0.00" || d == 0.00){ d = 0; }
						twht_val = parseFloat(twht_val) + parseFloat(d);
						e = document.getElementById("ewval").value;
						if(e == "" || e == 0 || e == "0.00" || e == 0.00){ e = 0; }
						ewht_val = parseFloat(ewht_val) + parseFloat(e);
						f = document.getElementById("nwval").value;
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

                document.addEventListener("keydown", (e) => { var key_search = document.activeElement.id.includes("["); if(key_search == true){ var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0]; document.getElementById("incrs").value = d; } if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
				function validate_count(x){ expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
				function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
				function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }

                function calculate_row_amt(){
                    var jals_flag = '<?php echo (int)$jals_flag; ?>';
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';
                    var incr = document.getElementById("incr").value;
                    var jals = birds = quantity = price = amount = tot_jals = tot_birds = tot_weight = tot_amount = 0;
                    for(var d = 0;d <= incr;d++){
                        jals = birds = quantity = price = amount = 0;
                        if(parseInt(jals_flag) == 1){ jals = document.getElementById("jals").value; if(jals == ""){ jals = 0; } }
                        if(parseInt(birds_flag) == 1){ birds = document.getElementById("birds").value; if(birds == ""){ birds = 0; } }
                        quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price").value; if(price == ""){ price = 0; }

                        if(parseInt(birds_flag) == 1){ amount = parseFloat(birds) * parseFloat(price); if(amount == ""){ amount = 0; } }
                        else{ amount = parseFloat(quantity) * parseFloat(price); if(amount == ""){ amount = 0; } }
                        document.getElementById("amount").value = parseFloat(amount).toFixed(0);

                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_birds = parseFloat(tot_birds) + parseFloat(birds);
                        tot_weight = parseFloat(tot_weight) + parseFloat(quantity);
                        tot_amount = parseFloat(tot_amount) + parseFloat(amount);
                    }
                    if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(0); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0); }
                    document.getElementById("tot_weight").value = parseFloat(tot_weight).toFixed(2);
                    document.getElementById("tot_amount").value = parseFloat(tot_amount).toFixed(2);
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
