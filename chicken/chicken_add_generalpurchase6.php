<?php
//chicken_add_generalpurchase6.php
//if Layer Birds, price calculations on birds instead of weight added
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = "";
    $trno_dt1 = generate_transaction_details($date,"generalpurchase6","LBT","display",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql);$jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = (int)$row['jals_flag']; $birds_flag = (int)$row['birds_flag']; $tweight_flag = (int)$row['tweight_flag']; $eweight_flag = (int)$row['eweight_flag']; }

    $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%S%' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sgrp_code = $sgrp_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sgrp_code[$row['code']] = $row['code']; $sgrp_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; $sup_group[$row['code']] = $row['groupcode']; }
    
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_generalpurchase6.php' AND `field_function` LIKE 'Display Supplier Balance' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $bal_flag = mysqli_num_rows($query);

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                label{
                    font-weight:bold;
                }
            </style>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Purchase</div>
                <form action="chicken_save_generalpurchase6.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly />
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="warehouse">Warehouse/Vehicle<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                    <option value="select">select</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="groups">Group<b style="color:red;">&nbsp;*</b></label>
                                <select name="groups" id="groups" class="form-control select2" style="width:180px;" onchange="filter_group_suppliers(this.id);">
                                    <option value="all">-all-</option>
                                    <?php foreach($sgrp_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sgrp_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:120px;">
                                <label for="bookinvoice">Invoice No.</label>
                                <input type="text" name="bookinvoice" id="bookinvoice" class="form-control" style="width:110px;" />
                            </div>
                        </div>
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width:100px;">Supplier<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$bal_flag == 1){ echo '<th style="width: 150px;padding-right:10px;"><label>Balance</label></th>'; } ?>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ echo "<th>T. Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ echo "<th>E. Weight</th>"; } ?>
                                        <th>N. Weight</th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                        <th style="visibility:hidden;">On Bird</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:250px;" onchange="fetchbalance(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php if($bal_flag == 1){ echo '<td  style= "width: 80px;padding-right:10px;"><input type="text" style="width: 80px;" name="balc[]" id="balc[0]" class="form-control" /></td>'; } ?>
                                        <td><select name="itemcode[]" id="itemcode[0]" class="form-control select2" style="width:180px;" onchange="update_row_fields();"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals[]" id="jals[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds[]" id="birds[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="tweight[]" id="tweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="eweight[]" id="eweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="nweight[]" id="nweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="item_amt[]" id="item_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:25px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        <td style="visibility:hidden"><input type="checkbox" name="prc_obrd[]" id="prc_obrd[0]" /></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right;">Total</th>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<th><input type="text" name="tot_jals" id="tot_jals" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$birds_flag == 1){ echo '<th><input type="text" name="tot_birds" id="tot_birds" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$tweight_flag == 1){ echo '<th><input type="text" name="tot_tweight" id="tot_tweight" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$eweight_flag == 1){ echo '<th><input type="text" name="tot_eweight" id="tot_eweight" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        ?>
                                        <th><input type="text" name="tot_nweight" id="tot_nweight" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_item_amt" id="tot_item_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
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
                function return_back(){
                    window.location.href = "chicken_display_generalpurchase6.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Warehouse");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else{
                        var vcode = itemcode = ""; var c = nweight = price = item_amt = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                vcode = document.getElementById("vcode["+d+"]").value;
                                itemcode = document.getElementById("itemcode["+d+"]").value;
                                /*nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }*/
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }

                                if(vcode == "select"){
                                    alert("Please select Supplier in row: "+c);
                                    document.getElementById("vcode["+d+"]").focus();
                                    l = false;
                                }
                                else if(itemcode == "select"){
                                    alert("Please select Item");
                                    document.getElementById("itemcode["+d+"]").focus();
                                    l = false;
                                }
                                /*else if(parseFloat(nweight) == 0){
                                    alert("Please enter net weight in row: "+c);
                                    document.getElementById("nweight["+d+"]").focus();
                                    l = false;
                                }*/
                                else if(parseFloat(price) == 0){
                                    alert("Please enter price in row: "+c);
                                    document.getElementById("price["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(item_amt) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("item_amt["+d+"]").focus();
                                    l = false;
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

                    var bal_flag = '<?php echo $bal_flag; ?>';
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:250px;" onchange="fetchbalance(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(bal_flag) > 0){ html+= '<td style="width: 100px;padding-right:10px;"><input type="text" name="balc[]" id="balc['+c+']" style="width: 100px;" class="form-control"></td>'; }
                    html += '<td><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" style="width:180px;" onchange="update_row_fields();"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="tweight[]" id="tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="eweight[]" id="eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="item_amt[]" id="item_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:25px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="visibility:hidden"><input type="checkbox" name="prc_obrd[]" id="prc_obrd['+d+']" /></td>';
                    html += '</tr>';

                    $('#row_body').append(html);
                    $('.select2').select2();
                    update_row_fields();
                    var x = "action["+d+"]";
                    filter_group_suppliers(x);
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }
                function calculate_total_amt(){
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    /*Total Calculations*/
                    var incr = document.getElementById("incr").value;
                    var jals = birds = tweight = eweight = nweight = price = item_amt = prc_obrd = 0;
                    var tot_jals = tot_birds = tot_tweight = tot_eweight = tot_nweight = tot_item_amt = tot_tcds_amt = tot_finl_amt = tot_rct_amt1 = tot_rct_amt2 = bird_flag = 0;

                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = price = item_amt = prc_obrd = 0;

                        icode = iname = "";
                        icode = document.getElementById("itemcode["+d+"]");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

                        //Price calculation on bird flag
                        document.getElementById("prc_obrd["+d+"]").checked = false;
                        if(iname == "Layer Birds" && parseInt(birds_flag) == 1){ document.getElementById("prc_obrd["+d+"]").checked = true; prc_obrd = 1; }

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

                        var price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        if(parseInt(prc_obrd) == 1 && parseInt(birds_flag) == 1){ var item_amt = parseFloat(birds) * parseFloat(price); }
                        else{ var item_amt = parseFloat(nweight) * parseFloat(price); }
                        document.getElementById("item_amt["+d+"]").value = parseFloat(item_amt).toFixed(2);

                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_birds = parseFloat(tot_birds) + parseFloat(birds);
                        tot_tweight = parseFloat(tot_tweight) + parseFloat(tweight);
                        tot_eweight = parseFloat(tot_eweight) + parseFloat(eweight);
                        tot_nweight = parseFloat(tot_nweight) + parseFloat(nweight);
                        tot_item_amt = parseFloat(tot_item_amt) + parseFloat(item_amt);
                    }
                    if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(0); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0); }
                    if(parseInt(tweight_flag) == 1){ document.getElementById("tot_tweight").value = parseFloat(tot_tweight).toFixed(2); }
                    if(parseInt(eweight_flag) == 1){ document.getElementById("tot_eweight").value = parseFloat(tot_eweight).toFixed(2); }
                    document.getElementById("tot_nweight").value = parseFloat(tot_nweight).toFixed(2);
                    document.getElementById("tot_item_amt").value = parseFloat(tot_item_amt).toFixed(2);
                }
                function update_row_fields(){
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    var incr = document.getElementById("incr").value;
                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        icode = iname = "";
                        icode = document.getElementById("itemcode["+d+"]");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

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
                    }
                    calculate_total_amt();
                }
                function fetchbalance(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var bal_flag = '<?php echo $bal_flag; ?>';
                    if(parseFloat(bal_flag) > 0){
                        var e = document.getElementById("vcode["+d+"]").value;
                        var f = e.split("@");
                        var g = f[0];
                        if(!e.match("select")){
                            var prices = new XMLHttpRequest();
                            var method = "GET";
                            var url = "chicken_supplier_balances.php?vendors="+g+"&row_cnt="+d;
                            var asynchronous = true;
                            prices.open(method, url, asynchronous);
                            prices.send();
                            prices.onreadystatechange = function(){
                                if(this.readyState == 4 && this.status == 200){
                                    var res = this.responseText;
                                    var info = res.split("[@$&]");
                                    var rows = info[0];
                                    var balance = info[1];
                                    //alert(res);  
                                    if(balance == null || balance == "") {
                                        document.getElementById("balc["+rows+"]").value = "0.00";
                                    }
                                    else {
                                        document.getElementById("balc["+rows+"]").value = balance;
                                    }
                                }
                            }
                        }
                        else { }
                    }
                }
                function filter_group_suppliers(a){
                    if(a == "groups"){
                        var incr = document.getElementById("incr").value;
                        var groups = document.getElementById('groups').value;
                        if(groups == "all"){
                            for(var d = 0;d <= incr;d++){
                                removeAllOptions(document.getElementById("vcode["+d+"]"));
                                myselect = document.getElementById("vcode["+d+"]");
                                theOption1=document.createElement("OPTION");
                                theText1=document.createTextNode("-select-");
                                theOption1.value = "select";
                                theOption1.appendChild(theText1);
                                myselect.appendChild(theOption1);

                                <?php
                                foreach($sup_code as $vcode){
                                ?>
                                    theOption1=document.createElement("OPTION");
                                    theText1=document.createTextNode("<?php echo $sup_name[$vcode]; ?>");
                                    theOption1.value = "<?php echo $vcode; ?>";
                                    theOption1.appendChild(theText1);
                                    myselect.appendChild(theOption1);
                                <?php
                                }
                                ?>
                            }
                        }
                        else{
                            for(var d = 0;d <= incr;d++){
                                removeAllOptions(document.getElementById("vcode["+d+"]"));
                                myselect = document.getElementById("vcode["+d+"]");
                                theOption1=document.createElement("OPTION");
                                theText1=document.createTextNode("-select-");
                                theOption1.value = "select";
                                theOption1.appendChild(theText1);
                                myselect.appendChild(theOption1);

                                <?php
                                foreach($sup_code as $vcode){
                                    $gcode = $sup_group[$vcode];
                                    echo "if(groups == '$gcode'){";
                                    ?>
                                    theOption1=document.createElement("OPTION");
                                    theText1=document.createTextNode("<?php echo $sup_name[$vcode]; ?>");
                                    theOption1.value = "<?php echo $vcode; ?>";
                                    theOption1.appendChild(theText1);
                                    myselect.appendChild(theOption1);
                                    <?php
                                    echo "}";
                                }
                                ?>
                            }
                        }
                    }
                    else{
                        var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                        var groups = document.getElementById('groups').value;

                        removeAllOptions(document.getElementById("vcode["+d+"]"));
                        myselect = document.getElementById("vcode["+d+"]");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-select-");
                        theOption1.value = "select";
                        theOption1.appendChild(theText1);
                        myselect.appendChild(theOption1);

                        if(groups == "all"){
                            <?php
                            foreach($sup_code as $vcode){
                            ?>
                                theOption1=document.createElement("OPTION");
                                theText1=document.createTextNode("<?php echo $sup_name[$vcode]; ?>");
                                theOption1.value = "<?php echo $vcode; ?>";
                                theOption1.appendChild(theText1);
                                myselect.appendChild(theOption1);
                            <?php
                            }
                            ?>
                        }
                        else{
                            <?php
                            foreach($sup_code as $vcode){
                                $gcode = $sup_group[$vcode];
                                echo "if(groups == '$gcode'){";
                                ?>
                                theOption1=document.createElement("OPTION");
                                theText1=document.createTextNode("<?php echo $sup_name[$vcode]; ?>");
                                theOption1.value = "<?php echo $vcode; ?>";
                                theOption1.appendChild(theText1);
                                myselect.appendChild(theOption1);
                                <?php
                                echo "}";
                            }
                            ?>
                        }
                    }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
