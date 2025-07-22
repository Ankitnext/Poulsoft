<?php
//chicken_add_pursale4.php
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
    $sup_ccnt = 5; $cus_ccnt = 7; $tcds_cnt = 3;
    if((int)$jals_flag == 1){ $sup_ccnt++; $cus_ccnt++; $tcds_cnt++; }
    if((int)$birds_flag == 1){ $sup_ccnt++; $cus_ccnt++; $tcds_cnt++; }
    if((int)$tweight_flag == 1){ $sup_ccnt++; $cus_ccnt++; $tcds_cnt++; }
    if((int)$eweight_flag == 1){ $sup_ccnt++; $cus_ccnt++; $tcds_cnt++; }
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
                <div class="card-header">Add Purchase-sale</div>
                <form action="chicken_save_pursale4.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date</label>
                                <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;" readonly />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="date">Vehicle</label>
                                <input type="text" name="vehiclecode" id="vehiclecode" class="form-control" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:210px;">
                                <label for="vendorcode">Purchase Party<b style="color:red;">&nbsp;*</b></label>
                                <select name="vendorcode" id="vendorcode" class="form-control select2" style="width:180px;" onchange="fetch_sup_branches2(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?></select>
                            </div>
                            <div class="form-group" style="width:210px;">
                                <label for="supbrh_code">Branch<b style="color:red;">&nbsp;*</b></label>
                                <select name="supbrh_code" id="supbrh_code" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option></select>
                            </div>
                            <div class="form-group" style="width:20px;visibility:hidden;">
                                <label for="warehouse">Warehouse<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:80px;"><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select>
                            </div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $sup_ccnt; ?>" style="background-color:#f6d1d1;color:#b80000;text-align:center;">Supplier Purchase Details</th>
                                    </tr>
                                    <tr>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Box</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ echo "<th>T.Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ echo "<th>E.Weight</th>"; } ?>
                                        <th>N.Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body1">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="sup_icode[]" id="sup_icode[0]" class="form-control select2" style="width:200px;" onchange="update_srow_fields(this.id);"><option value="select">-select-</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="sup_jals[]" id="sup_jals[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_samt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="sup_birds[]" id="sup_birds[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_samt();" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="sup_tweight[]" id="sup_tweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="sup_eweight[]" id="sup_eweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="sup_nweight[]" id="sup_nweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="sup_prc[]" id="sup_prc[0]" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);check_snrow(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="sup_amt[]" id="sup_amt[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <td id="action1[0]"><a href="javascript:void(0);" id="addrow1[0]" onclick="create_row1(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total: </th>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<th><input type="text" name="sup_tjals" id="sup_tjals" class="form-control text-right" style="width:50px;" readonly /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<th><input type="text" name="sup_tbds" id="sup_tbds" class="form-control text-right" style="width:50px;" readonly /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<th><input type="text" name="sup_ttwt" id="sup_ttwt" class="form-control text-right" style="width:90px;" readonly /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<th><input type="text" name="sup_tewt" id="sup_tewt" class="form-control text-right" style="width:90px;" readonly /></td>'; }
                                        ?>
                                        <th><input type="text" name="sup_tnwt" id="sup_tnwt" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <th></td>
                                        <th><input type="text" name="net_samt1" id="net_samt1" class="form-control text-right" style="width:90px;" readonly /></td>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $tcds_cnt; ?>" style="text-align:right;">
                                            <table align="right">
                                                <tr style="text-align:right;">
                                                    <th><input type="text" name="tds_per" id="tds_per" class="form-control text-right" style="padding:0;width:50px;" readonly /></th>
                                                    <th>TDS:&ensp;<input type="checkbox" name="tds_chk" id="tds_chk" onchange="fetch_tds_per(this.id);" /></th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="tds_amt" id="tds_amt" class="form-control text-right" style="width:90px;" readonly /></td>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $tcds_cnt; ?>" style="text-align:right;">Round-Off: </th>
                                        <th><input type="text" name="roff_samt" id="roff_samt" class="form-control text-right" style="width:90px;" readonly /></td>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $tcds_cnt; ?>" style="text-align:right;">Billing Amount: </th>
                                        <th><input type="text" name="sup_famt" id="sup_famt" class="form-control text-right" style="width:90px;" readonly /></td>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $tcds_cnt; ?>" style="text-align:right;">Line Expense: </th>
                                        <th><input type="text" name="line_sexp" id="line_sexp" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_wlpl_qtyamt();" onchange="validate_amount(this.id);" /></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><br/><br/>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $cus_ccnt; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Customer Sale Details</th>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $cus_ccnt; ?>">
                                            <div class="form-group">
                                                <label for="sdate">Sale Date</label>
                                                <input type="text" name="sdate" id="sdate" class="form-control range_picker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;" readonly />
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Sales Party<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Box</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ echo "<th>T.Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ echo "<th>E.Weight</th>"; } ?>
                                        <th>N.Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>TCS</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body2">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="cus_ccode[]" id="cus_ccode[0]" class="form-control select2" style="width:200px;" onchange="fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $cus_name[$icode]; ?></option><?php } ?></select></td>
                                        <td><select name="cus_icode[]" id="cus_icode[0]" class="form-control select2" style="width:200px;" onchange="update_crow_fields(this.id);fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="cus_jals[]" id="cus_jals[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_camt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="cus_birds[]" id="cus_birds[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_camt();" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="cus_tweight[]" id="cus_tweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="cus_eweight[]" id="cus_eweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="cus_nweight[]" id="cus_nweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="cus_prc[]" id="cus_prc[0]" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);check_cnrow(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>
                                        <td style="text-align:center;"><input type="checkbox" name="tcs_chk[]" id="tcs_chk[0]" onchange="fetch_tcs_per(this.id);" /></td>
                                        <td><input type="text" name="cus_famt[]" id="cus_famt[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <td id="action2[0]"><a href="javascript:void(0);" id="addrow2[0]" onclick="create_row2(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="roff_camt[]" id="roff_camt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="cus_amt[]" id="cus_amt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="tcs_per[]" id="tcs_per[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="tcs_amt[]" id="tcs_amt[0]" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/><br/>
                        <div class="row">
                            <div class="col-md-4 form-group"></div>
                            <div class="col-md-4 form-group">
                                <table>
                                    <tr>
                                        <th rowspan="2">Weight Loss</th>
                                        <th>QTY</th>
                                        <th>%</th>
                                    </tr>
                                    <tr>
                                        <th><input type="text" name="wl_qty" id="wl_qty" class="form-control text-right" style="width:110px;" readonly /></th>
                                        <th><input type="text" name="wl_per" id="wl_per" class="form-control text-right" style="width:110px;" readonly /></th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2">Profit / Loss</th>
                                        <th colspan="2">Amount</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><input type="text" name="pl_amt" id="pl_amt" class="form-control text-right" style="width:220px;" readonly /></th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4 form-group"></div>
                        </div>
                        <div class="row" style="margin-bottom:3px;">
                            <div class="col-md-4 form-group"></div>
                            <div class="col-md-4 form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"></textarea>
                            </div>
                            <div class="col-md-4 form-group"></div>
                        </div>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="pincr" id="pincr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="sincr" id="sincr" class="form-control" value="0" style="width:20px;" readonly />
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
                        </div><br/><br/>
                    </div>
                </form>
            </div>
            <script>
                //Date Range selection
                var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
                function return_back(){
                    window.location.href = "chicken_display_pursale4.php";
                }
                
                function checkval(){
                    update_ebtn_status(1);
                    var date = document.getElementById("date").value;
                    var sdate = document.getElementById("sdate").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var vendorcode = document.getElementById("vendorcode").value;
                    var l = true;

                    if(date == ""){
                        alert("Please select appropriate date.");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(sdate == ""){
                        alert("Please select appropriate sale date.");
                        document.getElementById("sdate").focus();
                        l = false;
                    }
                    else if(warehouse == "" || warehouse == "select"){
                        alert("Please select Vehicle.");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(vendorcode == "" || vendorcode == "select"){
                        alert("Please select Purchase Party.");
                        document.getElementById("vendorcode").focus();
                        l = false;
                    }
                    else{
                        var sup_icode = ""; var c = sup_nweight = sup_prc = sup_amt = 0;
                        var pincr = document.getElementById("pincr").value;
                        for(var d = 0;d <= pincr;d++){
                            if(l == true){
                                c = d + 1;
                                sup_icode = document.getElementById("sup_icode["+d+"]").value;
                                sup_nweight = document.getElementById("sup_nweight["+d+"]").value; if(sup_nweight == ""){ sup_nweight = 0; }
                                sup_prc = document.getElementById("sup_prc["+d+"]").value; if(sup_prc == ""){ sup_prc = 0; }
                                sup_amt = document.getElementById("sup_amt["+d+"]").value; if(sup_amt == ""){ sup_amt = 0; }

                                if(d > 0 && d == pincr && sup_icode == "select" && parseFloat(sup_nweight) == 0 && parseFloat(sup_prc) == 0 && parseFloat(sup_amt) == 0){ destroy_row1("amount["+d+"]"); }
                                else{
                                    if(sup_icode == "" || sup_icode == "select"){
                                        alert("Please select Item in row: "+c);
                                        document.getElementById("sup_icode["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(sup_nweight) == 0){
                                        alert("Please Enter Supplier N.Weight in row: "+c);
                                        document.getElementById("sup_nweight["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(sup_prc) == 0){
                                        alert("Please Enter Supplier Price in row: "+c);
                                        document.getElementById("sup_prc["+d+"]").focus();
                                        l = false;
                                    }
                                    else if(parseFloat(sup_amt) == 0){
                                        alert("Please Enter Supplier N.Weight/Supplier Price in row: "+c);
                                        document.getElementById("sup_prc["+d+"]").focus();
                                        l = false;
                                    }
                                    else{ }
                                }
                            }
                        }
                        if(l == true){
                            var cus_ccode = cus_icode = ""; var c = cus_nweight = cus_prc = cus_famt = 0;
                            var sincr = document.getElementById("sincr").value;
                            for(var d = 0;d <= sincr;d++){
                                if(l == true){
                                    c = d + 1;
                                    cus_ccode = document.getElementById("cus_ccode["+d+"]").value;
                                    cus_icode = document.getElementById("cus_icode["+d+"]").value;
                                    cus_nweight = document.getElementById("cus_nweight["+d+"]").value; if(cus_nweight == ""){ cus_nweight = 0; }
                                    cus_prc = document.getElementById("cus_prc["+d+"]").value; if(cus_prc == ""){ cus_prc = 0; }
                                    cus_famt = document.getElementById("cus_famt["+d+"]").value; if(cus_famt == ""){ cus_famt = 0; }

                                    if(d > 0 && d == sincr && cus_ccode == "select" && cus_icode == "select" && parseFloat(cus_nweight) == 0 && parseFloat(cus_prc) == 0 && parseFloat(cus_famt) == 0){ destroy_row2("amount["+d+"]"); }
                                    else{
                                        if(cus_ccode == "" || cus_ccode == "select"){
                                            alert("Please select Sales Party in row: "+c);
                                            document.getElementById("cus_ccode["+d+"]").focus();
                                            l = false;
                                        }
                                        else if(cus_icode == "" || cus_icode == "select"){
                                            alert("Please select Sales Item in row: "+c);
                                            document.getElementById("cus_icode["+d+"]").focus();
                                            l = false;
                                        }
                                        else if(parseFloat(cus_nweight) == 0){
                                            alert("Please Enter Sales Party N.Weight in row: "+c);
                                            document.getElementById("cus_nweight["+d+"]").focus();
                                            l = false;
                                        }
                                        else if(parseFloat(cus_prc) == 0){
                                            alert("Please Enter Sales Party Price in row: "+c);
                                            document.getElementById("cus_prc["+d+"]").focus();
                                            l = false;
                                        }
                                        else if(parseFloat(cus_famt) == 0){
                                            alert("Please Enter Sales Party N.Weight/Sales Party Price in row: "+c);
                                            document.getElementById("cus_prc["+d+"]").focus();
                                            l = false;
                                        }
                                        else{ }
                                    }
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
                function fetch_tds_per(a){
                    update_ebtn_status(1);
                    var tds_chk = document.getElementById("tds_chk");
                    document.getElementById("tds_per").value = "";
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
                                    document.getElementById("tds_per").value = parseFloat(tds).toFixed(2);
                                    update_ebtn_status(0); calculate_tot_samt();
                                }
                            }
                        }
                    }
                    else{ update_ebtn_status(0); calculate_tot_samt(); }
                }
                function fetch_sup_branches2(selectId) {
                    const ven = document.getElementById(selectId).value;
                    const br  = document.getElementById("supbrh_code");
                    br.options.length = 0;
                    br.add(new Option("-select-", "select"));
                    if (!ven || ven === "select") return;

                    const xhr = new XMLHttpRequest();
                    xhr.open("GET", "chicken_fetch_supplier_branch.php?scode=" + encodeURIComponent(ven), true);
                    xhr.onload = function() {
                        if (xhr.status !== 200) {
                        return alert("Error fetching branches");
                        }

                        const txt = xhr.responseText.trim();
                        let list;

                        try {
                        // try pure JSON
                        list = JSON.parse(txt);
                        } catch (e) {
                        // fallback to your [@$&] format
                        const parts = txt.split("[@$&]");
                        if (+parts[0] === 0) {
                            return alert("Branch details not available, please check and try again.");
                        }
                        list = JSON.parse(parts[2] || "[]");
                        }

                        list.forEach(b => br.add(new Option(b.name, b.code)));
                    };
                    xhr.send();
                }


                function fetch_tcs_per(a){
                    update_ebtn_status(1);
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    if(b[0] == "tcs_chk"){
                        var tcs_chk = document.getElementById("tcs_chk["+d+"]");
                        document.getElementById("tcs_per["+d+"]").value = "";
                        if(tcs_chk.checked == true){
                            var date = document.getElementById("sdate").value;
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
                                        update_ebtn_status(0); calculate_tot_camt();
                                    }
                                }
                            }
                        }
                        else{ update_ebtn_status(0); calculate_tot_camt(); }
                    }
                    else{ update_ebtn_status(0); }
                }
                function check_snrow(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var incr = document.getElementById("pincr").value; if(incr == ""){ incr = 0; }
                    if(d == incr){
                        var sup_icode = document.getElementById("sup_icode["+d+"]").value;
                        var sup_nweight = document.getElementById("sup_nweight["+d+"]").value; if(sup_nweight == ""){ sup_nweight = 0; }
                        var sup_prc = document.getElementById("sup_prc["+d+"]").value; if(sup_prc == ""){ sup_prc = 0; }
                        if(sup_icode != "select" && sup_icode != "" && parseFloat(sup_nweight) > 0 && parseFloat(sup_prc) > 0){ create_row1(a); }
                    }
                }
                function check_cnrow(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var incr = document.getElementById("sincr").value; if(incr == ""){ incr = 0; }
                    if(d == incr){
                        var cus_icode = document.getElementById("cus_icode["+d+"]").value;
                        var cus_nweight = document.getElementById("cus_nweight["+d+"]").value; if(cus_nweight == ""){ cus_nweight = 0; }
                        var cus_prc = document.getElementById("cus_prc["+d+"]").value; if(cus_prc == ""){ cus_prc = 0; }
                        if(cus_icode != "select" && cus_icode != "" && parseFloat(cus_nweight) > 0 && parseFloat(cus_prc) > 0){ create_row2(a); }
                    }
                }
                function create_row1(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action1["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("pincr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>'; if(jals_flag == ""){ jals_flag = 0; }
                    var birds_flag = '<?php echo $birds_flag; ?>'; if(birds_flag == ""){ birds_flag = 0; }
                    var tweight_flag = '<?php echo $tweight_flag; ?>'; if(tweight_flag == ""){ tweight_flag = 0; }
                    var eweight_flag = '<?php echo $eweight_flag; ?>'; if(eweight_flag == ""){ eweight_flag = 0; }

                    html += '<tr id="row_no1['+d+']">';
                    html += '<td><select name="sup_icode[]" id="sup_icode['+d+']" class="form-control select2" style="width:200px;" onchange="update_srow_fields(this.id);"><option value="select">-select-</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="sup_jals[]" id="sup_jals['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_samt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="sup_birds[]" id="sup_birds['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_samt();" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="sup_tweight[]" id="sup_tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="sup_eweight[]" id="sup_eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="sup_nweight[]" id="sup_nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="sup_prc[]" id="sup_prc['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);check_snrow(this.id);calculate_tot_samt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="sup_amt[]" id="sup_amt['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '<td id="action1['+d+']"><!--<a href="javascript:void(0);" id="addrow1['+d+']" onclick="create_row1(this.id)"><i class="fa fa-plus"></i></a>&ensp;--><a href="javascript:void(0);" id="deductrow1['+d+']" onclick="destroy_row1(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';

                    $('#row_body1').append(html);
                    $('.select2').select2();
                    update_srow_fields("sup_icode["+d+"]");
                }
                function destroy_row1(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no1["+d+"]").remove();
                    d--;
                    document.getElementById("pincr").value = d;
                    document.getElementById("action1["+d+"]").style.visibility = "visible";
                    calculate_tot_samt();
                }
                function create_row2(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action2["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("sincr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>'; if(jals_flag == ""){ jals_flag = 0; }
                    var birds_flag = '<?php echo $birds_flag; ?>'; if(birds_flag == ""){ birds_flag = 0; }
                    var tweight_flag = '<?php echo $tweight_flag; ?>'; if(tweight_flag == ""){ tweight_flag = 0; }
                    var eweight_flag = '<?php echo $eweight_flag; ?>'; if(eweight_flag == ""){ eweight_flag = 0; }

                    html += '<tr id="row_no2['+d+']">';
                    html += '<td><select name="cus_ccode[]" id="cus_ccode['+d+']" class="form-control select2" style="width:200px;" onchange="fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="cus_icode[]" id="cus_icode['+d+']" class="form-control select2" style="width:200px;" onchange="update_crow_fields(this.id);fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="cus_jals[]" id="cus_jals['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_camt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="cus_birds[]" id="cus_birds['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_tot_camt();" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="cus_tweight[]" id="cus_tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="cus_eweight[]" id="cus_eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="cus_nweight[]" id="cus_nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="cus_prc[]" id="cus_prc['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);check_cnrow(this.id);calculate_tot_camt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td style="text-align:center;"><input type="checkbox" name="tcs_chk[]" id="tcs_chk['+d+']" onchange="fetch_tcs_per(this.id);" /></td>';
                    html += '<td><input type="text" name="cus_famt[]" id="cus_famt['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '<td id="action2['+d+']"><!--<a href="javascript:void(0);" id="addrow2['+d+']" onclick="create_row2(this.id)"><i class="fa fa-plus"></i></a>&ensp;--><a href="javascript:void(0);" id="deductrow2['+d+']" onclick="destroy_row2(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roff_camt[]" id="roff_camt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="cus_amt[]" id="cus_amt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="tcs_per[]" id="tcs_per['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="tcs_amt[]" id="tcs_amt['+d+']" class="form-control text-right" style="padding:0;width:20px;" readonly /></td>';
                    html += '</tr>';

                    $('#row_body2').append(html);
                    $('.select2').select2();
                    update_crow_fields("cus_icode["+d+"]");
                }
                function destroy_row2(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no2["+d+"]").remove();
                    d--;
                    document.getElementById("sincr").value = d;
                    document.getElementById("action2["+d+"]").style.visibility = "visible";
                    calculate_tot_camt();
                }
                function calculate_tot_samt(){
                    //Supplier Calculations
                    var incr = document.getElementById("pincr").value; if(incr == ""){ incr = 0; }
                    var jals_flag = '<?php echo $jals_flag; ?>'; if(jals_flag == ""){ jals_flag = 0; }
                    var birds_flag = '<?php echo $birds_flag; ?>'; if(birds_flag == ""){ birds_flag = 0; }
                    var tweight_flag = '<?php echo $tweight_flag; ?>'; if(tweight_flag == ""){ tweight_flag = 0; }
                    var eweight_flag = '<?php echo $eweight_flag; ?>'; if(eweight_flag == ""){ eweight_flag = 0; }

                    var jals = birds = tweight = eweight = nweight = sup_prc = sup_amt = tds_per = tds_amt = roff_samt = sup_famt = bird_flag = 0;
                    var tot_jals = tot_bds = tot_twt = tot_ewt = tot_nwt = tsup_amt = 0;
                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = sup_prc = sup_amt = tds_per = tds_amt = bird_flag = 0;
                        icode = document.getElementById("sup_icode["+d+"]");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

                        if(parseInt(jals_flag) == 1){ jals = document.getElementById("sup_jals["+d+"]").value; } if(jals == ""){ jals = 0; }
                        if(parseInt(birds_flag) == 1){ birds = document.getElementById("sup_birds["+d+"]").value; } if(birds == ""){ birds = 0; }
                        if(parseInt(tweight_flag) == 1){ tweight = document.getElementById("sup_tweight["+d+"]").value; } if(tweight == ""){ tweight = 0; }
                        if(parseInt(eweight_flag) == 1){ eweight = document.getElementById("sup_eweight["+d+"]").value; } if(eweight == ""){ eweight = 0; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1 && parseInt(bird_flag) > 0){
                            nweight = parseFloat(tweight) - parseFloat(eweight);
                            document.getElementById("sup_nweight["+d+"]").value = parseFloat(nweight).toFixed(2);
                        }
                        else{
                            nweight = document.getElementById("sup_nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                        }
                        sup_prc = document.getElementById("sup_prc["+d+"]").value; if(sup_prc == ""){ sup_prc = 0; }
                        sup_amt = parseFloat(nweight) * parseFloat(sup_prc);
                        document.getElementById("sup_amt["+d+"]").value = parseFloat(sup_amt).toFixed(2);
                        //calculate total
                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_bds = parseFloat(tot_bds) + parseFloat(birds);
                        tot_twt = parseFloat(tot_twt) + parseFloat(tweight);
                        tot_ewt = parseFloat(tot_ewt) + parseFloat(eweight);
                        tot_nwt = parseFloat(tot_nwt) + parseFloat(nweight);
                        tsup_amt = parseFloat(tsup_amt) + parseFloat(sup_amt);
                    }
                    var tds_chk = document.getElementById("tds_chk");
                    if(tds_chk.checked == true){
                        tds_per = document.getElementById("tds_per").value; if(tds_per == ""){ tds_per = 0; }
                        tds_amt = parseFloat(tsup_amt) * (parseFloat(tds_per) / 100); if(tds_amt == ""){ tds_amt = 0; }
                    }
                    var ramt1 = parseFloat(tsup_amt) + parseFloat(tds_amt);
                    sup_famt = parseFloat(ramt1).toFixed(0);
                    roff_samt = parseFloat(sup_famt) - parseFloat(ramt1);

                    if(parseInt(jals_flag) == 1){ document.getElementById("sup_tjals").value = parseFloat(tot_jals).toFixed(2); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("sup_tbds").value = parseFloat(tot_bds).toFixed(2); }
                    if(parseInt(tweight_flag) == 1){ document.getElementById("sup_ttwt").value = parseFloat(tot_twt).toFixed(2); }
                    if(parseInt(eweight_flag) == 1){ document.getElementById("sup_tewt").value = parseFloat(tot_ewt).toFixed(2); }
                    document.getElementById("sup_tnwt").value = parseFloat(tot_nwt).toFixed(2);
                    document.getElementById("net_samt1").value = parseFloat(tsup_amt).toFixed(2);

                    document.getElementById("tds_amt").value = parseFloat(tds_amt).toFixed(2);
                    document.getElementById("roff_samt").value = parseFloat(roff_samt).toFixed(2);
                    document.getElementById("sup_famt").value = parseFloat(sup_famt).toFixed(2);
                    calculate_wlpl_qtyamt();
                }
                function calculate_tot_camt(){
                    //Supplier Calculations
                    var incr = document.getElementById("sincr").value; if(incr == ""){ incr = 0; }
                    var jals_flag = '<?php echo $jals_flag; ?>'; if(jals_flag == ""){ jals_flag = 0; }
                    var birds_flag = '<?php echo $birds_flag; ?>'; if(birds_flag == ""){ birds_flag = 0; }
                    var tweight_flag = '<?php echo $tweight_flag; ?>'; if(tweight_flag == ""){ tweight_flag = 0; }
                    var eweight_flag = '<?php echo $eweight_flag; ?>'; if(eweight_flag == ""){ eweight_flag = 0; }

                    var jals = birds = tweight = eweight = nweight = cus_prc = cus_amt = tcs_per = tcs_amt = roff_camt = cus_famt = bird_flag = 0;
                    var tot_jals = tot_bds = tot_twt = tot_ewt = tot_nwt = tcus_amt = 0;
                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = cus_prc = cus_amt = tcs_per = tcs_amt = bird_flag = 0;
                        icode = document.getElementById("cus_icode["+d+"]");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

                        if(parseInt(jals_flag) == 1){ jals = document.getElementById("cus_jals["+d+"]").value; } if(jals == ""){ jals = 0; }
                        if(parseInt(birds_flag) == 1){ birds = document.getElementById("cus_birds["+d+"]").value; } if(birds == ""){ birds = 0; }
                        if(parseInt(tweight_flag) == 1){ tweight = document.getElementById("cus_tweight["+d+"]").value; } if(tweight == ""){ tweight = 0; }
                        if(parseInt(eweight_flag) == 1){ eweight = document.getElementById("cus_eweight["+d+"]").value; } if(eweight == ""){ eweight = 0; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1 && parseInt(bird_flag) > 0){
                            nweight = parseFloat(tweight) - parseFloat(eweight);
                            document.getElementById("cus_nweight["+d+"]").value = parseFloat(nweight).toFixed(2);
                        }
                        else{
                            nweight = document.getElementById("cus_nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                        }
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
                        document.getElementById("roff_camt["+d+"]").value = parseFloat(roff_camt).toFixed(2);
                        document.getElementById("cus_famt["+d+"]").value = parseFloat(cus_famt).toFixed(2);
                    }
                    calculate_wlpl_qtyamt();
                }
                function calculate_wlpl_qtyamt(){
                    var sup_tnwt = document.getElementById("sup_tnwt").value; if(sup_tnwt == ""){ sup_tnwt = 0; }
                    var sup_famt = document.getElementById("sup_famt").value; if(sup_famt == ""){ sup_famt = 0; }
                    var line_sexp = document.getElementById("line_sexp").value; if(line_sexp == ""){ line_sexp = 0; }
                    var sincr = document.getElementById("sincr").value; if(sincr == ""){ sincr = 0; }
                    var wl_qty = wl_per = pl_amt = cus_famt = tot_samt = cus_nweight = tot_nwt = 0;
                    for(var d = 0;d <= sincr;d++){
                        cus_famt = document.getElementById("cus_famt["+d+"]").value; if(cus_famt == ""){ cus_famt = 0; }
                        tot_samt = parseFloat(tot_samt) + parseFloat(cus_famt);
                        cus_nweight = document.getElementById("cus_nweight["+d+"]").value; if(cus_nweight == ""){ cus_nweight = 0; }
                        tot_nwt = parseFloat(tot_nwt) + parseFloat(cus_nweight);
                    }
                    //Weight Loss Calculations
                    wl_qty = parseFloat(sup_tnwt) - parseFloat(tot_nwt);
                    if(parseFloat(sup_tnwt) != 0){ wl_per = ((parseFloat(wl_qty) / parseFloat(sup_tnwt)) * 100); }
                    document.getElementById("wl_qty").value = parseFloat(wl_qty).toFixed(2);
                    document.getElementById("wl_per").value = parseFloat(wl_per).toFixed(2);

                    //Profit and Loss Calculations
                    pl_amt = parseFloat(tot_samt) - (parseFloat(sup_famt) + parseFloat(line_sexp));
                    document.getElementById("pl_amt").value = parseFloat(pl_amt).toFixed(2);
                }
                function fetch_latest_customer_paperrate(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("sdate").value;
                    var vcode = document.getElementById("cus_ccode["+d+"]").value;
                    var itemcode = document.getElementById("cus_icode["+d+"]").value;
                    document.getElementById("cus_prc["+d+"]").value = "";

                    if(date == "" || vcode == "select" || itemcode == "select"){ }
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
                                    check_cnrow("cus_prc["+rows+"]"); update_ebtn_status(0); calculate_tot_camt();
                                }
                            }
                            else{ check_cnrow(a); update_ebtn_status(0); }
                        }
                    }
                }
                function update_srow_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("sup_icode["+d+"]");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("sup_jals["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("sup_birds["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("sup_tweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("sup_eweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("sup_nweight["+d+"]").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("sup_jals["+d+"]").style.visibility = "hidden"; document.getElementById("sup_jals["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("sup_birds["+d+"]").style.visibility = "hidden"; document.getElementById("sup_birds["+d+"]").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("sup_tweight["+d+"]").style.visibility = "hidden"; document.getElementById("sup_tweight["+d+"]").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("sup_eweight["+d+"]").style.visibility = "hidden"; document.getElementById("sup_eweight["+d+"]").value = ""; }
                        document.getElementById("sup_nweight["+d+"]").readOnly = false;
                    }
                    calculate_tot_samt();
                }
                function update_crow_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("cus_icode["+d+"]");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("cus_jals["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("cus_birds["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("cus_tweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("cus_eweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("cus_nweight["+d+"]").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("cus_jals["+d+"]").style.visibility = "hidden"; document.getElementById("cus_jals["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("cus_birds["+d+"]").style.visibility = "hidden"; document.getElementById("cus_birds["+d+"]").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("cus_tweight["+d+"]").style.visibility = "hidden"; document.getElementById("cus_tweight["+d+"]").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("cus_eweight["+d+"]").style.visibility = "hidden"; document.getElementById("cus_eweight["+d+"]").value = ""; }
                        document.getElementById("cus_nweight["+d+"]").readOnly = false;
                    }
                    calculate_tot_camt();
                }
                update_srow_fields("sup_icode[0]");
                update_crow_fields("cus_icode[0]");
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
                        const vehiclecode = document.getElementById('vehiclecode');
                        if(vehiclecode && $(vehiclecode).hasClass('select2-hidden-accessible')){
                            $(vehiclecode).select2('open');
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
