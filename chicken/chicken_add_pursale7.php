<?php
//chicken_add_pursale7.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql); $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; $tweight_flag = $row['tweight_flag']; $eweight_flag = $row['eweight_flag']; }

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` DESC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` DESC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                label{
                    font-weight:bold;
                }
                ::-webkit-scrollbar { width: 8px; height:8px; }
                .row_body2{
                    width:100%;
                    overflow-y: auto;
                }
            </style>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Purchase-sale</div>
                <form action="chicken_save_pursale7.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date</label>
                                <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;" readonly />
                            </div>
                            <div class="form-group" style="width:210px;">
                                <label for="warehouse">Warehouse<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select>
                            </div>
                            <div class="form-group" style="width:210px;">
                                <label for="itemcode">Item</label>
                                <select name="itemcode" id="itemcode" class="form-control select2" style="width:200px;" onchange="update_all_ifields();"><option value="select">-select-</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select>
                            </div>
                        </div>
                        <div class="row row_body2">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Supplier<b style="color:red;">&nbsp;*</b></th>
                                        <th>Branch</th>
                                        <th>Bill No.</th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ echo "<th>T.Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ echo "<th>E.Weight</th>"; } ?>
                                        <th>N.Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>TDS</th>
                                        <th>Amount</th>
                                        <th>Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>TCS</th>
                                        <th>Amount</th>
                                        
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="vendorcode[]" id="vendorcode[0]" class="form-control select2" style="width:180px;" onchange="fetch_sup_branches2(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="supbrh_code[]" id="supbrh_code[0]" class="form-control select2" style="width:90px;"><option value="select">-select-</option></select></td>
                                        <td><input type="text" name="bookinvoice[]" id="bookinvoice[0]" class="form-control" style="width:60px;" /></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals[]" id="jals[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_amt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds[]" id="birds[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_amt();" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="tweight[]" id="tweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="eweight[]" id="eweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="nweight[]" id="nweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="sup_prc[]" id="sup_prc[0]" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td style="text-align:center;"><input type="checkbox" name="tds_chk[]" id="tds_chk[0]" onchange="fetch_tcds_per(this.id);" /></td>
                                        <td><input type="text" name="sup_famt[]" id="sup_famt[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <td><select name="customercode[]" id="customercode[0]" class="form-control select2" style="width:180px;" onchange="fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="cus_prc[]" id="cus_prc[0]" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);calculate_tot_amt();check_nrow(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td style="text-align:center;"><input type="checkbox" name="tcs_chk[]" id="tcs_chk[0]" onchange="fetch_tcds_per(this.id);" /></td>
                                        <td><input type="text" name="cus_famt[]" id="cus_famt[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <!-- <td><input type="text" name="vehiclecode[]" id="vehiclecode[0]" class="form-control" value="<?php echo $vehiclecode[$c]; ?>" style="width:130px;" /></td>
                                        <td><input type="text" name="drivercode[]" id="drivercode[0]" class="form-control" value="<?php echo $drivercode[$c]; ?>" style="width:130px;" /></td> -->
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:130px;height:23px;"><?php echo $remarks[$c]; ?></textarea></td>
                                        
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="roff_samt[]" id="roff_samt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="roff_camt[]" id="roff_camt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="sup_amt[]" id="sup_amt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="cus_amt[]" id="cus_amt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="tds_per[]" id="tds_per[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="tds_amt[]" id="tds_amt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="tcs_per[]" id="tcs_per[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="tcs_amt[]" id="tcs_amt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total: </th>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<th><input type="text" name="tot_jals" id="tot_jals" class="form-control text-right" style="width:50px;" readonly /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<th><input type="text" name="tot_bds" id="tot_bds" class="form-control text-right" style="width:50px;" readonly /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<th><input type="text" name="tot_twt" id="tot_twt" class="form-control text-right" style="width:90px;" readonly /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<th><input type="text" name="tot_ewt" id="tot_ewt" class="form-control text-right" style="width:90px;" readonly /></td>'; }
                                        ?>
                                        <th><input type="text" name="tot_nwt" id="tot_nwt" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <th></td>
                                        <th></td>
                                        <th><input type="text" name="tot_samt" id="tot_sfamt" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <th></td>
                                        <th></td>
                                        <th></td>
                                        <th><input type="text" name="tot_camt" id="tot_cfamt" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <th colspan="12"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><br/>
                        
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
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
                    window.location.href = "chicken_display_pursale7.php";
                }
                
                function checkval(){
                    update_ebtn_status(1);
                    var date = document.getElementById("date").value;
                    var itemcode = document.getElementById("itemcode").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var l = true;

                    if(date == ""){
                        alert("Please select appropriate date.");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(itemcode == "" || itemcode == "select"){
                        alert("Please select Item.");
                        document.getElementById("itemcode").focus();
                        l = false;
                    }
                    else if(warehouse == "" || warehouse == "select"){
                        alert("Please select Warehouse.");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else{
                        var vendorcode = nweight = icode = fsector = tsector = date = ""; var c = jalqty = price = birdqty = qty = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                vendorcode = document.getElementById("vendorcode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                sup_prc = document.getElementById("sup_prc["+d+"]").value; if(sup_prc == ""){ sup_prc = 0; }
                                sup_famt = document.getElementById("sup_famt["+d+"]").value; if(sup_famt == ""){ sup_famt = 0; }

                                customercode = document.getElementById("customercode["+d+"]").value;
                                cus_prc = document.getElementById("cus_prc["+d+"]").value; if(cus_prc == ""){ cus_prc = 0; }
                                cus_famt = document.getElementById("cus_famt["+d+"]").value; if(cus_famt == ""){ cus_famt = 0; }

                                if(d > 0 && d == incr && vendorcode == "select" && parseFloat(nweight) == 0 && parseFloat(sup_famt) == 0 && customercode == "select" && parseFloat(cus_famt) == 0){ destroy_row("amount["+d+"]"); }
                                else{
                                    if(vendorcode == "" || vendorcode == "select"){
                                        alert("Please select Supplier in row: "+c);
                                        document.getElementById("vendorcode["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(nweight) == 0){
                                        alert("Please Enter N.Weight in row: "+c);
                                        document.getElementById("nweight["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(sup_prc) == 0){
                                        alert("Please Enter Supplier Price in row: "+c);
                                        document.getElementById("sup_prc["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(sup_famt) == 0){
                                        alert("Please Enter Supplier N.Weight/Supplier Price in row: "+c);
                                        document.getElementById("sup_prc["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(customercode == "" || customercode == "select"){
                                        alert("Please select Customer in row: "+c);
                                        document.getElementById("customercode["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(cus_prc) == 0){
                                        alert("Please Enter Customer Price in row: "+c);
                                        document.getElementById("cus_prc["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(cus_famt) == 0){
                                        alert("Please Enter Supplier N.Weight/Customer Price in row: "+c);
                                        document.getElementById("cus_prc["+d+"]").focus();
                                        l = false;
                                    }
                                    else{ }
                                }
                            }
                        }
                    }

                    if(l == true){
                        return true;
                    }
                    else{
                        update_ebtn_status(0);
                        return false;
                    }
                }
                function fetch_tcds_per(a){
                    update_ebtn_status(1);
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    if(b[0] == "tds_chk"){
                        var tds_chk = document.getElementById("tds_chk["+d+"]");
                        document.getElementById("tds_per["+d+"]").value = "";
                        if(tds_chk.checked == true){
                            var date = document.getElementById("date").value;
                            var tcds_fch = new XMLHttpRequest();
                            var method = "GET";
                            var url = "main_gettcdsvalue.php?type=TDS&cdate="+date;
                            var asynchronous = true;
                            tcds_fch.open(method, url, asynchronous);
                            tcds_fch.send();
                            tcds_fch.onreadystatechange = function(){
                                if(this.readyState == 4 && this.status == 200){
                                    var tds = this.responseText; if(tds == ""){ tds = 0; }
                                    if(tds != ""){
                                        document.getElementById("tds_per["+d+"]").value = parseFloat(tds).toFixed(2);
                                        update_ebtn_status(0); calculate_tot_amt();
                                    }
                                }
                            }
                        }
                        else{ update_ebtn_status(0); calculate_tot_amt(); }
                    }
                    else if(b[0] == "tcs_chk"){
                        var tcs_chk = document.getElementById("tcs_chk["+d+"]");
                        document.getElementById("tcs_per["+d+"]").value = "";
                        if(tcs_chk.checked == true){
                            var date = document.getElementById("date").value;
                            var tcds_fch = new XMLHttpRequest();
                            var method = "GET";
                            var url = "main_gettcdsvalue.php?type=TCS&cdate="+date;
                            var asynchronous = true;
                            tcds_fch.open(method, url, asynchronous);
                            tcds_fch.send();
                            tcds_fch.onreadystatechange = function(){
                                if(this.readyState == 4 && this.status == 200){
                                    var tcs = this.responseText; if(tcs == ""){ tcs = 0; }
                                    if(tcs != ""){
                                        document.getElementById("tcs_per["+d+"]").value = parseFloat(tcs).toFixed(2);
                                        update_ebtn_status(0); calculate_tot_amt();
                                    }
                                }
                            }
                        }
                        else{ update_ebtn_status(0); calculate_tot_amt(); }
                    }
                    else{ update_ebtn_status(0); }
                }
                function check_nrow(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var incr = document.getElementById("incr").value; if(incr == ""){ incr = 0; }
                    if(d == incr){
                        var vendorcode = document.getElementById("vendorcode["+d+"]").value;
                        var customercode = document.getElementById("customercode["+d+"]").value;
                        var nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                        var sup_prc = document.getElementById("sup_prc["+d+"]").value; if(sup_prc == ""){ sup_prc = 0; }
                        var cus_prc = document.getElementById("cus_prc["+d+"]").value; if(cus_prc == ""){ cus_prc = 0; }
                        if(vendorcode != "select" && vendorcode != "" && customercode != "select" && customercode != "" && parseFloat(nweight) > 0 && parseFloat(sup_prc) > 0 && parseFloat(cus_prc) > 0){ create_row(a); }
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>'; if(jals_flag == ""){ jals_flag = 0; }
                    var birds_flag = '<?php echo $birds_flag; ?>'; if(birds_flag == ""){ birds_flag = 0; }
                    var tweight_flag = '<?php echo $tweight_flag; ?>'; if(tweight_flag == ""){ tweight_flag = 0; }
                    var eweight_flag = '<?php echo $eweight_flag; ?>'; if(eweight_flag == ""){ eweight_flag = 0; }

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="vendorcode[]" id="vendorcode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_sup_branches2(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="supbrh_code[]" id="supbrh_code['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option></select></td>';
                    html += '<td><input type="text" name="bookinvoice[]" id="bookinvoice['+d+']" class="form-control" value="<?php echo $bookinvoice[$c]; ?>" style="width:110px;" /></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_amt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_amt();" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="tweight[]" id="tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="eweight[]" id="eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="sup_prc[]" id="sup_prc['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td style="text-align:center;"><input type="checkbox" name="tds_chk[]" id="tds_chk['+d+']" onchange="fetch_tcds_per(this.id);" /></td>';
                    html += '<td><input type="text" name="sup_famt[]" id="sup_famt['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '<td><select name="customercode[]" id="customercode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="cus_prc[]" id="cus_prc['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);check_nrow(this.id);calculate_tot_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td style="text-align:center;"><input type="checkbox" name="tcs_chk[]" id="tcs_chk['+d+']" onchange="fetch_tcds_per(this.id);" /></td>';
                    html += '<td><input type="text" name="cus_famt[]" id="cus_famt['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '<td><input type="text" name="vehiclecode[]" id="vehiclecode['+d+']" class="form-control" value="<?php echo $vehiclecode[$c]; ?>" style="width:130px;" /></td>';
                    html += '<td><input type="text" name="drivercode[]" id="drivercode['+d+']" class="form-control" value="<?php echo $drivercode[$c]; ?>" style="width:130px;" /></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:130px;height:23px;"><?php echo $remarks[$c]; ?></textarea></td>';
                    
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roff_samt[]" id="roff_samt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roff_camt[]" id="roff_camt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="sup_amt[]" id="sup_amt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="cus_amt[]" id="cus_amt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="tds_per[]" id="tds_per['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="tds_amt[]" id="tds_amt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="tcs_per[]" id="tcs_per['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="tcs_amt[]" id="tcs_amt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '</tr>';

                    $('#row_body').append(html);
                    $('.select2').select2();
                    update_row_fields("vendorcode["+d+"]");
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_tot_amt();
                }
                function calculate_tot_amt(){
                    var incr = document.getElementById("incr").value; if(incr == ""){ incr = 0; }
                    var jals_flag = '<?php echo $jals_flag; ?>'; if(jals_flag == ""){ jals_flag = 0; }
                    var birds_flag = '<?php echo $birds_flag; ?>'; if(birds_flag == ""){ birds_flag = 0; }
                    var tweight_flag = '<?php echo $tweight_flag; ?>'; if(tweight_flag == ""){ tweight_flag = 0; }
                    var eweight_flag = '<?php echo $eweight_flag; ?>'; if(eweight_flag == ""){ eweight_flag = 0; }

                    var jals = birds = tweight = eweight = nweight = sup_prc = sup_amt = tds_per = tds_amt = roff_samt = sup_famt = bird_flag = cus_prc = cus_amt = tcs_per = tcs_amt = roff_camt = cus_famt = 0;
                    var tot_jals = tot_bds = tot_twt = tot_ewt = tot_nwt = tot_sfamt = tot_cfamt = 0;
                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = sup_prc = sup_amt = tds_per = tds_amt = roff_samt = sup_famt = bird_flag = cus_prc = cus_amt = tcs_per = tcs_amt = roff_camt = cus_famt = 0;
                        icode = document.getElementById("itemcode");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

                        if(parseInt(jals_flag) == 1){ jals = document.getElementById("jals["+d+"]").value; } if(jals == ""){ jals = 0; }
                        if(parseInt(birds_flag) == 1){ birds = document.getElementById("birds["+d+"]").value; } if(birds == ""){ birds = 0; }
                        if(parseInt(tweight_flag) == 1){ tweight = document.getElementById("tweight["+d+"]").value; } if(tweight == ""){ tweight = 0; }
                        if(parseInt(eweight_flag) == 1){ eweight = document.getElementById("eweight["+d+"]").value; } if(eweight == ""){ eweight = 0; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1 && parseInt(bird_flag) > 0){
                            nweight = parseFloat(tweight) - parseFloat(eweight);
                            document.getElementById("nweight["+d+"]").value = parseFloat(nweight).toFixed(2);
                        }
                        else{
                            nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                        }

                        //Supplier Calculations
                        sup_prc = document.getElementById("sup_prc["+d+"]").value; if(sup_prc == ""){ sup_prc = 0; }
                        sup_amt = parseFloat(nweight) * parseFloat(sup_prc);
                        var tds_chk = document.getElementById("tds_chk["+d+"]");
                        if(tds_chk.checked == true){
                            tds_per = document.getElementById("tds_per["+d+"]").value; if(tds_per == ""){ tds_per = 0; }
                            tds_amt = parseFloat(sup_amt) * (parseFloat(tds_per) / 100); if(tds_amt == ""){ tds_amt = 0; }
                        }
                        var ramt1 = parseFloat(sup_amt) + parseFloat(tds_amt);
                        sup_famt = parseFloat(ramt1).toFixed(0);
                        roff_samt = parseFloat(sup_famt) - parseFloat(ramt1);

                        document.getElementById("sup_amt["+d+"]").value = parseFloat(sup_amt).toFixed(2);
                        document.getElementById("tds_amt["+d+"]").value = parseFloat(tds_amt).toFixed(2);
                        document.getElementById("roff_samt["+d+"]").value = roff_samt;
                        document.getElementById("sup_famt["+d+"]").value = parseFloat(sup_famt).toFixed(2);

                        //Customer Calculations
                        cus_prc = document.getElementById("cus_prc["+d+"]").value; if(cus_prc == ""){ cus_prc = 0; }
                        cus_amt = parseFloat(nweight) * parseFloat(cus_prc);
                        var tcs_chk = document.getElementById("tcs_chk["+d+"]");
                        if(tcs_chk.checked == true){
                            tcs_per = document.getElementById("tcs_per["+d+"]").value; if(tcs_per == ""){ tcs_per = 0; }
                            tcs_amt = parseFloat(cus_amt) * (parseFloat(tcs_per) / 100); if(tcs_amt == ""){ tcs_amt = 0; }
                        }
                        var ramt1 = parseFloat(cus_amt) + parseFloat(tcs_amt);
                        cus_famt = parseFloat(ramt1).toFixed(0);
                        roff_camt = parseFloat(cus_famt) - parseFloat(ramt1);

                        document.getElementById("cus_amt["+d+"]").value = parseFloat(cus_amt).toFixed(2);
                        document.getElementById("tcs_amt["+d+"]").value = parseFloat(tcs_amt).toFixed(2);
                        document.getElementById("roff_camt["+d+"]").value = roff_camt;
                        document.getElementById("cus_famt["+d+"]").value = parseFloat(cus_famt).toFixed(2);

                        //calculate total
                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_bds = parseFloat(tot_bds) + parseFloat(birds);
                        tot_twt = parseFloat(tot_twt) + parseFloat(tweight);
                        tot_ewt = parseFloat(tot_ewt) + parseFloat(eweight);
                        tot_nwt = parseFloat(tot_nwt) + parseFloat(nweight);
                        tot_sfamt = parseFloat(tot_sfamt) + parseFloat(sup_famt);
                        tot_cfamt = parseFloat(tot_cfamt) + parseFloat(cus_famt);
                    }
                    if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(2); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("tot_bds").value = parseFloat(tot_bds).toFixed(2); }
                    if(parseInt(tweight_flag) == 1){ document.getElementById("tot_twt").value = parseFloat(tot_twt).toFixed(2); }
                    if(parseInt(eweight_flag) == 1){ document.getElementById("tot_ewt").value = parseFloat(tot_ewt).toFixed(2); }
                    document.getElementById("tot_nwt").value = parseFloat(tot_nwt).toFixed(2);
                    document.getElementById("tot_sfamt").value = parseFloat(tot_sfamt).toFixed(2);
                    document.getElementById("tot_cfamt").value = parseFloat(tot_cfamt).toFixed(2);
                }
                
                function fetch_latest_customer_paperrate(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("date").value;
                    var itemcode = document.getElementById("itemcode").value;
                    var vcode = document.getElementById("customercode["+d+"]").value;
                    document.getElementById("cus_prc["+d+"]").value = "";

                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                    }
                    else if(itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode").focus();
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("customercode["+d+"]").focus();
                    }
                    else{
                        update_ebtn_status(1);
                        var inv_items = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_fetch_latest_cuspaperrate.php?date="+date+"&vcode="+vcode+"&icode="+itemcode+"&row_cnt="+d;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var cus_dt1 = this.responseText;
                                var cus_dt2 = cus_dt1.split("@");
                                var price = cus_dt2[0];
                                var rows = cus_dt2[1];
                                if(price != ""){
                                    document.getElementById("cus_prc["+rows+"]").value = parseFloat(price).toFixed(2);
                                    document.getElementById("cus_prc["+rows+"]").select();
                                    check_nrow("cus_prc["+rows+"]"); update_ebtn_status(0); calculate_tot_amt();
                                }
                            }
                            else{ check_nrow(a); update_ebtn_status(0); }
                        }
                    }
                }
                function update_all_ifields(){
                    var incr = document.getElementById("incr").value; if(incr == ""){ incr = 0; }
                    var x = "";
                    for(var d = 0;d <= incr;d++){ x = "jals["+d+"]"; update_row_fields(x); }
                }
                function update_row_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("itemcode");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("nweight["+d+"]").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "hidden"; document.getElementById("jals["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "hidden"; document.getElementById("birds["+d+"]").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "hidden"; document.getElementById("tweight["+d+"]").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "hidden"; document.getElementById("eweight["+d+"]").value = ""; }
                        document.getElementById("nweight["+d+"]").readOnly = false;
                    }
                    calculate_tot_amt();
                }
                
                function fetch_sup_branches2(a) {
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var vendorcode = document.getElementById(`vendorcode[${d}]`).value;
                    var selectElement = document.getElementById(`supbrh_code[${d}]`);
                    selectElement.length = 0;

                    if (vendorcode !== "" && vendorcode !== "select") {
                        var fch_veh = new XMLHttpRequest();
                        var method = "GET";
                        var url = `chicken_fetch_supplier_branch.php?scode=${vendorcode}`;
                        var asynchronous = true;

                        fch_veh.open(method, url, asynchronous);
                        fch_veh.send();

                        fch_veh.onreadystatechange = function (){
                            if (this.readyState === 4 && this.status === 200) {
                                var brh_dt1 = this.responseText;
                                var brh_dt2 = brh_dt1.split("[@$&]");
                                var count = brh_dt2[0];
                                var brh_lt1 = brh_dt2[2];

                                if(parseInt(count) > 0){
                                    var obj = JSON.parse(brh_lt1);
                                    const defaultOption = new Option("Select", "select", true, true);
                                    defaultOption.disabled = true;
                                    selectElement.add(defaultOption);
                                    
                                    obj.forEach(({ code, name }) => {
                                        const option = new Option(name, code);
                                        selectElement.add(option);
                                    });
                                }
                                else{
                                    alert("Branch details not available, please check and try again.");
                                }
                            }
                        };
                    }
                }

                update_row_fields("itemcode[0]");
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let tried = 0;
                    const interval = setInterval(() => {
                        const warehouse = document.getElementById('warehouse');
                        if(warehouse && $(warehouse).hasClass('select2-hidden-accessible')){
                            $(warehouse).select2('open');
                            setTimeout(() => {
                                const searchBox = document.querySelector('.select2-search__field');
                                if (searchBox) searchBox.focus();
                            }, 50);
                            clearInterval(interval);
                        }
                        if(++tried > 10) clearInterval(interval);
                    }, 100);
                });
                
                function update_ebtn_status(a){
                    if(parseInt(a) == 1){
                        document.getElementById("ebtncount").value = "1";
                        document.getElementById("submit").style.visibility = "hidden";
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                    }
                }
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
