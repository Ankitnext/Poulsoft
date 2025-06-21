<?php
//chicken_edit_pursale6.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $spzflag = $row['spzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $pst_prate_flag = $row['pst_prate_flag']; }
    if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }

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

    $sql = "SELECT * FROM `chicken_supplier_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $sp_code = $sp_name = array();
    while($row = mysqli_fetch_assoc($query)){$sp_code[$row['code']] = $row['code']; $sp_name[$row['code']] = $row['description'];}
    
    $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){
        if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cont_gname[$row['code']] = $row['groupcode']; } else{ }
        if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
    }
    
    $sql = "SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $group_code = $group_name = array();
    while($row = mysqli_fetch_assoc($query)){ $group_code[$row['code']] = $row['code']; $group_name[$row['code']] = $row['description']; }

    $group_details = array(); $idisplay = ''; $ndisplay = 'style="display:none;';
    $sql = "SELECT code,description FROM `main_groups` WHERE gtype LIKE '%C%'";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $group_details[$row['code']] = $row['description'];
    }
    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

    $colspan = 15;
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                body{
                    overflow: auto;
                }
                /*table,tr,th,td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }*/
                label{
                    font-weight:bold;
                }
            </style>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            if($ids != ""){
                $sql ="SELECT * FROM `customer_sales` WHERE `invoice` = '$ids'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
                        if($ccount > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $fdate = date("d.m.Y",strtotime($row['date']));
                                $link_trnum = $row['link_trnum'];
                                $cnames = $row['customercode'];
                                $cus_iprice = $row['itemprice'];
                                $cus_tamt = $row['totalamt'];
                           
                                $cjval = $row['jals'];
                                $cbval = $row['birds'];
                                $cwval = $row['totalweight'];
                                $cewval = $row['emptyweight'];
                                $cnwval = $row['netweight'];
                                $cus_famt = $row['finaltotal'];
                                $vehicle = $row['vehiclecode'];
                                $driver = $row['drivercode'];
                                $remarks = $row['remarks'];
                                $warehouse = $row['warehouse'];
                            }
                        }
                        $sql ="SELECT * FROM `pur_purchase` WHERE `link_trnum` = '$ids'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
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
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Purchase Sale Stock</div>
                <form action="chicken_modify_pursale6.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                       <div class="row">
                            <div class="form-group" style="100px">
                                <label>Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="pdate" id="pdate" class="form-control datepicker" value="<?php echo $fdate; ?>" style="width:100%;" readonly>
                            </div>
                            <div class="form-group" style="width:250px">
								<label>Item Description<b style="color:red;">&nbsp;*</b></label>
								<select name="scat" id="scat" class="form-control select2" style="width:100%;" value="<?php echo $scat; ?>" onchange="checkitemtype();"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select>
							</div>
                            <div class="form-group" style="width:250px">
                                <div style="width: 100%;">
                                    <label>Warehouse<b style="color:red;">&nbsp;*</b></label>
                                    <select name="warehouse" id="warehouse" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>" <?php if($warehouse == $it) { echo "selected"; } ?>><?php echo $sector_name[$it]; ?></option><?php } ?></select>
                                </div>
							</div>
							<div class="form-group"  style="width:250px">
                                <div style="width: 100%;">
                                    <label>Group<b style="color:red;">&nbsp;*</b></label>
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
							</div>
                            <div class="form-group" style="visibility:visible; width:150px;">
								<label>Customer Price</label>
								<input type="text" class="form-control" name="cus_price" id="cus_price" value="<?php echo $cus_iprice; ?>" style="width:100%; text-align: right;" onkeyup="validatenum(this.id);add_customer_prices();" onchange="validateamount(this.id)" />
							</div>
                        </div>
                        <div class="row">
                            <div style="background-color:#d1ffe4;color:#00722f;text-align:center;width:59%; font-weight: bold;">Supplier Details</div>
                        </div>
                        <div class="row">
							<div class="form-group" style="visibility:visible; width:250px;">
								<label>Supplier<b style="color:red;">&nbsp;*</b></label>
								<select name="snames" id="snames" class="form-control select2" style="width: 100%;" onchange="fetch_sup_branches2(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]; ?>" <?php if($cc == $snames){ echo "selected";} ?>><?php echo $sup_name[$cc]; ?></option><?php } ?></select>
							</div>
							<div class="form-group" style="visibility:visible; width:250px;">
								<label>Branch</label>
                                <select name="supbrh_code" id="supbrh_code" class="form-control select2" style="width: 100%;">
                                    <option value="select">-select-</option>
                                    <?php foreach($sp_code as $scode){ ?>
                                        <option value="<?php echo $scode ?>" <?php if($scode == $supbrh_code){ echo "selected";} ?>><?php echo $sp_name[$scode] ?></option>
                                        <?php } ?>
                                </select>							
                            </div>
							<div class="form-group" style="visibility:visible; width:250px;">
                                <div style="width:100%;">
                                    <label>Bill No.</label>
                                    <input type="text" class="form-control" name="bno" id="bno" value="<?php echo $bno; ?>" style="width:100%;" />
                                </div>
							</div>
							<?php if($ifjbwen == 1 || $ifjbw == 1){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Jals</label>
								<input type="text" class="form-control" name="jval" id="jval" value="<?php echo $jval; ?>" style="width:100%;text-align: right;" onkeyup="" class="form-control amount-format"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Birds</label>
								<input type="text" class="form-control" name="bval" id="bval" value="<?php echo $bval; ?>" style="width:100%;text-align: right;" onkeyup="" class="form-control amount-format"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 ){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>T. Wt.</label>
								<input type="text" class="form-control" name="wval" id="wval" class="form-control amount-format" value="<?php echo $wval; ?>" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 ){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>E. Wt.</label>
								<input type="text" class="form-control" name="ewval" id="ewval" class="form-control amount-format" value="<?php echo $ewval; ?>" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" />
							</div>
							<?php } ?>

							<div class="form-group" style="visibility:visible; width:100px;">
								<label>N. Wt.<b style="color:red;">&nbsp;*</b></label>
								<input type="text" class="form-control" name="nwval" id="nwval" value="<?php echo $nwval; ?>" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"/>
							</div>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Price<b style="color:red;">&nbsp;*</b></label>
								<input type="text" class="form-control" name="sup_iprice" id="sup_iprice" value="<?php echo $sup_iprice; ?>" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"/>
							</div>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Amount<b style="color:red;">&nbsp;*</b></label>
								<input type="text" name="sup_famt" id="sup_famt"  class="form-control amount-format" value="<?php echo $sup_tamt; ?>" style="width:100%;text-align: right;" readonly/>
								<td style="width:1px;visibility:hidden;"><input type="text" name="sup_tamt" id="sup_tamt" class="form-control amount-format" style="width:1px;" readonly></td>							
                            </div>
						</div>
                        
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Customer Details</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;"><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
                                        <?php
                                            if($ifjbwen == 1 || $ifjbw == 1){ echo "<th style='text-align:center;'><label>Jals</label></th>"; }
                                            if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<th style='text-align:center;'><label>Birds</label></th>"; }
                                            if($ifjbwen == 1){ echo "<th style='text-align:center;'><label>T. Wt.</label></th><th style='text-align:center;'><label>E. Wt.</label></th>"; }
                                        ?>
                                        <th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="width:1px;visibility:hidden;text-align:center;"></th>
                                        <th style="text-align:center;"><label>Amount</label></th>
                                        <th style="text-align:center;"><label>Vehicle</label></th>
                                        <th style="text-align:center;"><label>Driver</label></th>
                                        <th style="text-align:center;"><label>Remarks</label></th>
                                        <th style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td style="width: 150px;padding-right:5px;"><select name="cnames" id="cnames" class="form-control select2" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>" <?php if($cc == $cnames){ echo "selected";} ?>><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
                                        <td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cjval" id="cjval" value="<?php echo $cjval; ?>" style="width:100%;" onkeyup="" class="form-control amount-format" /></td>
                                        <td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cbval" id="cbval" value="<?php echo $cbval; ?>" style="width:100%;" onkeyup="" class="form-control amount-format" /></td>
                                        <td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cwval" id="cwval" class="form-control amount-format" value="<?php echo $cwval; ?>" style="width:100%;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" /></td>
                                        <td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cewval" id="cewval" class="form-control amount-format" value="<?php echo $cewval; ?>" style="width:100%;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" /></td>
                                        <td style="width:100px;"><input type="text" name="cus_qty" id="cus_qty" value="<?php echo $cnwval; ?>" style="width:100%;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width:100px;"><input type="text" name="cus_iprice" id="cus_iprice" value="<?php echo $cus_iprice; ?>" style="width:100%;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width:1px;visibility:hidden;"><input type="text" name="cus_tamt" id="cus_tamt" class="form-control amount-format" style="width:1px;" readonly></td>
                                        <td style="width:100px;"><input type="text" name="cus_famt" id="cus_famt" class="form-control amount-format" value="<?php echo $cus_famt; ?>" style="width:100%;" readonly></td>
                                        <td style="width:100px;"><input type="text" name="vehicle" id="vehicle" class="form-control" value="<?php echo $vehicle; ?>" style="width:100%;" ></td>
                                        <td style="width:100px;"><input type="text" name="driver" id="driver" class="form-control" value="<?php echo $driver; ?>" style="width:100%;" ></td>
                                        <td style="width: auto;"><textarea name="remarks" id="remarks" class="form-control" style="height:23px;"><?php echo $remarks; ?></textarea></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $invoice; ?>" style="width:20px;" readonly /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="col-md-4" style="width:auto;visibility:hidden;">
                                <label>Item Field Type</label>
                                <input type="text" name="itemfields" id="itemfields" class="form-control" value="<?php if($ifwt == 1){ echo "WT"; } else if($ifbw == 1){ echo "BAW"; } else if($ifjbw == 1){ echo "JBEW"; } else if($ifjbwen == 1){ echo "JBTEN"; } else { echo "WT"; } ?>" >
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" value="updatepage" id="submit" class="btn btn-sm text-white bg-success">Update</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                 //Date Range selection
                var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
                function return_back(){
                    window.location.href = "chicken_display_pursale6.php";
                }
                function checkval(){
                    var l = true;
                    var date = document.getElementById("pdate").value;
					var vcode = document.getElementById("vcode").value;
					var amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }
					var itemcode = document.getElementById("itemcode").value;
				
                    if(vcode == "select"){
						alert("Please select Customer names in row: ");
						document.getElementById("vcode").focus();
						l = false;
					}
                    else if(itemcode == "select"){
						alert("Please select Item names in row: ");
						document.getElementById("itemcode").focus();
						l = false;
					}
					else if(parseFloat(amount) == 0){
						alert("Please enter Amount in row: ");
						document.getElementById("amount").focus();
						l = false;
					}
					return l;
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
            function checkitemtype(){
				var r = document.getElementById("itemfields").value;
				var b = ""; var c = []; var a = d = 0;
				// var incr = document.getElementById("incr").value;
				b = document.getElementById("scat").value;
				c = b.split("@");
				d = c[1].search(/Birds/i);
				
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
            function add_customer_prices(){
                var cus_price = document.getElementById("cus_price").value;
                // var incr = document.getElementById("incr").value;
                if(cus_price == ""){ }
                else{ document.getElementById("cus_iprice").value = parseFloat(cus_price).toFixed(2); }
                calculatetotal();
            }
            function calculatetotal(){
				// var a = document.getElementById("incr").value;
				// var tds_value = document.getElementById("tds_value").value;
				// var tcs_value = document.getElementById("tcs_value").value;
				// var e = g = h = i = j = k = 0.00; var tds_flag = tds_tamt = tcs_flag = tcs_tamt = sup_famt = cus_famt = 0;
				
					var g = document.getElementById("nwval").value;
					if(g == "" || g == 0 || g == "0.00" || g == 0.00){ g = 0; }
					var j = document.getElementById("sup_iprice").value;
					if(j == "" || j == 0 || j == "0.00" || j == 0.00){ j = 0; }
					var k = parseFloat(g) * parseFloat(j);
					
					document.getElementById("sup_famt").value = parseFloat(k).toFixed(2);
					document.getElementById("sup_tamt").value = k.toFixed(2);
				
					var l = document.getElementById("cus_qty").value;
					var h = document.getElementById("cus_iprice").value;
					if(h == "" || h == 0 || h == "0.00" || h == 0.00){ h = 0; }
					if(l == "" || l == 0 || l == "0.00" || l == 0.00){ l = 0; }
					var i = parseFloat(l) * parseFloat(h);
					document.getElementById("cus_tamt").value = i.toFixed(2);
					document.getElementById("cus_famt").value = parseFloat(i).toFixed(2);
					document.getElementById("cus_famt").value = parseFloat(i).toFixed(2);
			}
            document.addEventListener("keydown", (e) => { var key_search = document.activeElement.id.includes("["); if(key_search == true){ var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0]; document.getElementById("incrs").value = d; } if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
            function validate_count(x){ expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
            function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
            function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
