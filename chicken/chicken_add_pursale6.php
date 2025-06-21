<?php
//chicken_add_pursale6.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("d.m.Y");
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

    $group_details = array(); $idisplay = ''; $ndisplay = 'style="display:none;';
    $sql = "SELECT code,description FROM `main_groups` WHERE gtype LIKE '%C%'";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $group_details[$row['code']] = $row['description'];
    }
    
    $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){
        if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; } else{ }
        if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
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
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Purchase Sale Stock</div>
                <form action="chicken_save_pursale6.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <div class="form-group" style="100px">
                                <label>Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" style="width:100%;" class="form-control range_picker" name="pdate" value="<?php echo $date; ?>" id="pdate" readonly>
                            </div>
                            <div class="form-group" style="width:250px">
								<label>Item Description<b style="color:red;">&nbsp;*</b></label>
								<select name="scat" id="scat" class="form-control select2" style="width:100%;" onchange="checkitemtype();"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select>
							</div>
                            <div class="form-group" style="width:250px">
                                <div style="width: 100%;">
                                    <label>Warehouse<b style="color:red;">&nbsp;*</b></label>
                                    <select name="warehouse" id="warehouse" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select>
                                </div>
							</div>
							<div class="form-group"  style="width:250px">
                                <div style="width: 100%;">
                                    <label>Group<b style="color:red;">&nbsp;*</b></label>
                                    <select name="grpcodes" id="grpcodes" class="form-control select2" style="width: 100%;" onchange="fetch_group_customer(this.id);"> <?php foreach ($group_details as $code => $description) { ?>
                                    <option value="<?php echo $code; ?>"><?php echo $description; ?></option>
									<?php } ?></select>
                                </div>
							</div>
                            <div class="form-group" style="visibility:visible; width:150px;">
								<label>Customer Price</label>
								<input type="text" class="form-control" name="cus_price" id="cus_price" value="" style="width:100%; text-align: right;" onkeyup="validatenum(this.id);add_customer_prices();" onchange="validateamount(this.id)" />
							</div>
                        </div>
                        <div class="row">
                            <div style="background-color:#d1ffe4;color:#00722f;text-align:center;width:59%; font-weight: bold;">Supplier Details</div>
                        </div>
                        <div class="row">
							<div class="form-group" style="visibility:visible; width:250px;">
								<label>Supplier<b style="color:red;">&nbsp;*</b></label>
								<select name="snames" id="snames" class="form-control select2" style="width: 100%;" onchange="fetch_sup_branches2(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]; ?>"><?php echo $sup_name[$cc]; ?></option><?php } ?></select>
							</div>
							<div class="form-group" style="visibility:visible; width:250px;">
								<label>Branch</label>
								<select name="supbrh_code" id="supbrh_code" class="form-control select2" style="width: 100%;"><option value="select">-select-</option></select>
							</div>
							<div class="form-group" style="visibility:visible; width:250px;">
                                <div style="width:100%;">
                                    <label>Bill No.</label>
                                    <input type="text" class="form-control" name="bno" id="bno" value="" style="width:100%;" />
                                </div>
							</div>
							<?php if($ifjbwen == 1 || $ifjbw == 1){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Jals</label>
								<input type="text" class="form-control" name="jval" id="jval" value="" style="width:100%;text-align: right;" onkeyup="" class="form-control amount-format"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Birds</label>
								<input type="text" class="form-control" name="bval" id="bval" value="" style="width:100%;text-align: right;" onkeyup="" class="form-control amount-format"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 ){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>T. Wt.</label>
								<input type="text" class="form-control" name="wval" id="wval" class="form-control amount-format" value="" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)"/>
							</div>
							<?php } ?>

							<?php if($ifjbwen == 1 ){ ?>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>E. Wt.</label>
								<input type="text" class="form-control" name="ewval" id="ewval" class="form-control amount-format" value="" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" />
							</div>
							<?php } ?>

							<div class="form-group" style="visibility:visible; width:100px;">
								<label>N. Wt.<b style="color:red;">&nbsp;*</b></label>
								<input type="text" class="form-control" name="nwval" id="nwval" value="" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"/>
							</div>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Price<b style="color:red;">&nbsp;*</b></label>
								<input type="text" class="form-control" name="sup_iprice" id="sup_iprice" value="" style="width:100%;text-align: right;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"/>
							</div>
							<div class="form-group" style="visibility:visible; width:100px;">
								<label>Amount<b style="color:red;">&nbsp;*</b></label>
								<input type="text" name="sup_famt" id="sup_famt" value=""  class="form-control amount-format" style="width:100%;text-align: right;" readonly/>
								<td style="width:1px;visibility:hidden;"><input type="text" name="sup_tamt" id="sup_tamt" class="form-control amount-format" style="width:1px;" readonly></td>							</div>
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
                                       <td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
                                        <td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cjval[]" id="cjval[0]" value="" style="width:100%;" onkeyup="" class="form-control amount-format" /></td>
                                        <td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cbval[]" id="cbval[0]" value="" style="width:100%;" onkeyup="" class="form-control amount-format" /></td>
                                        <td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cwval[]" id="cwval[0]" class="form-control amount-format" value="" style="width:100%;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" /></td>
                                        <td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cewval[]" id="cewval[0]" class="form-control amount-format" value="" style="width:100%;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" /></td>
                                        <td style="width:100px;"><input type="text" name="cus_qty[]" id="cus_qty[0]" style="width:100%;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width:100px;"><input type="text" name="cus_iprice[]" id="cus_iprice[0]" style="width:100%;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width:1px;visibility:hidden;"><input type="text" name="cus_tamt[]" id="cus_tamt[0]" class="form-control amount-format" style="width:1px;" readonly></td>
                                    
                                        <td style="width:100px;"><input type="text" name="cus_famt[]" id="cus_famt[0]" class="form-control amount-format" style="width:100%;" readonly></td>
                                        <td style="width:100px;"><input type="text" name="vehicle[]" id="vehicle[0]" class="form-control" style="width:100%;" ></td>
                                        <td style="width:100px;"><input type="text" name="driver[]" id="driver[0]" class="form-control" style="width:100%;" ></td>
                                        <td style="width: auto;"><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="col-md-4" style="width:auto;visibility:hidden;">
                                <label>Item Field Type</label>
                                <input type="text" name="itemfields" id="itemfields" class="form-control" value="<?php if($ifwt == 1){ echo "WT"; } else if($ifbw == 1){ echo "BAW"; } else if($ifjbw == 1){ echo "JBEW"; } else if($ifjbwen == 1){ echo "JBTEN"; } else { echo "WT"; } ?>" >
                            </div>
                            <div class="col-md-4" style="width:auto;visibility:hidden;">
                                <label>Amount Based</label>
                                <input type="text" name="amountbasedon" id="amountbasedon" class="form-control" value="<?php echo $ifctype; ?>" >
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" value="addpage" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
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
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("pdate").value;
                   
                    if(date == ""){
                        alert("Please select Date ");
                        document.getElementById("pdate").focus();
                        l = false;
                    } else {
                    var vcode = itemcode = ""; var amount = 0;
                    var incr = parseInt(document.getElementById("incr").value);
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            vcode = document.getElementById("vcode["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                           
                            if(vcode == "select"){
                                alert("Please select Customer names in row: "+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                                break;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                                break;
                            }
                             else if(itemcode == "select"){
                                alert("Please select Item names in row: "+c);
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                                break;
                            }
                            
                            else{ }
                        }
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
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;
                    html += '<tr id="row_no['+d+']">';
                    html+= '<td style="width: 150px;padding-right:5px;"><select name="cnames[]" id="cnames['+d+']" class="form-control select2" style="width: 150px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
                    html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cjval[]" id="cjval['+d+']" value="" style="width:100%;" onkeyup="" class="form-control amount-format" /></td>';
                    html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cbval[]" id="cbval['+d+']" value="" style="width:100%;" onkeyup="" class="form-control amount-format" /></td>';
                    html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cwval[]" id="cwval['+d+']" value="" style="width:100%;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" class="form-control amount-format" /></td>';
                    html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?> style="width:100px;"><input type="text" name="cewval[]" id="cewval['+d+']" value="" style="width:100%;" onkeyup="validatenum(this.id);;" onchange="validateamount(this.id)" class="form-control amount-format" /></td>';
                    html+= '<td style="width:100px;"><input type="text" name="cus_qty[]" id="cus_qty['+d+']" style="width:100%;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>';
                    html+= '<td style="width:100px;"><input type="text" name="cus_iprice[]" id="cus_iprice['+d+']" style="width:100%;" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" class="form-control amount-format"></td>';
                    html+= '<td style="width:1px;visibility:hidden;"><input type="text" name="cus_tamt[]" id="cus_tamt['+d+']" style="width:100%;" class="form-control amount-format" style="width:1px;" readonly></td>';
                    html+= '<td style="width:100px;"><input type="text" name="cus_famt[]" id="cus_famt['+d+']" class="form-control amount-format" style="width:100%;" readonly></td>';
                    html+= '<td style="width:100px;"><input type="text" name="vehicle[]" id="vehicle['+d+']" class="form-control" style="width:100%;" ></td>';
                    html+= '<td style="width:100px;"><input type="text" name="driver[]" id="driver['+d+']" class="form-control" style="width:100%;" ></td>';
                    html+= '<td style="width: auto;"><textarea name="narr[]" id="narr['+d+']" class="form-control" style="height:23px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;text-align:center;"><input type="checkbox" name="rndoff_chk[]" id="rndoff_chk['+d+']" onchange="" checked /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    // document.getElementById("vcode["+d+"]").focus();
                    checkitemtype(); add_customer_prices(d);
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
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
            function add_customer_prices(){
                var cus_price = document.getElementById("cus_price").value;
                var incr = document.getElementById("incr").value;
                if(cus_price == ""){ }
                else{ for(var a = 0;a <= incr;a++){ document.getElementById("cus_iprice["+a+"]").value = parseFloat(cus_price).toFixed(2); } }
                calculatetotal();
            }
            function calculatetotal(){
				var a = document.getElementById("incr").value;
				// var tds_value = document.getElementById("tds_value").value;
				// var tcs_value = document.getElementById("tcs_value").value;
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
