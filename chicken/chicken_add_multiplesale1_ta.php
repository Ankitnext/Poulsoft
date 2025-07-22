<?php
//chicken_add_multiplesale1_ta.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = "";
    $trno_dt1 = generate_transaction_details($date,"multiplesale1_ta","MSII","display",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    $tcds_per = 0;
    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql); $ppzflag = $ifwt = $ifbw = $ifjbw = $ifjbwen = $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $ppzflag = $row['ppzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; }
    if($ifjbwen == 1 || $ifjbw == 1){ $jals_flag = 1; } if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ $birds_flag = 1; } if($ifjbwen == 1){ $tweight_flag = $eweight_flag = 1; } if($ppzflag == ""){ $ppzflag = 0; }

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_code = $bank_name = array();
    while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

    //Store Latest Date to Session
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'chicken_display_multiplesale1.php' AND `field_function` = 'Auto Select Previously Changed Date' AND `user_access` = 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $date_asflag = mysqli_num_rows($query);
    if((int)$date_asflag > 0 && !empty($_SESSION['multiplesale1_asdate']) && $_SESSION['multiplesale1_asdate'] != ""){ $date = date("Y-m-d",strtotime($_SESSION['multiplesale1_asdate'])); }
    
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
                <div class="card-header">Add Multiple-Sales</div>
                <form action="chicken_save_multiplesale1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly />
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="itemcode">Item<b style="color:red;">&nbsp;*</b></label>
                                <select name="itemcode" id="itemcode" class="form-control select2" style="width:180px;" onchange="update_row_fields();">
                                    <option value="select">select</option>
                                    <?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="warehouse">Warehouse<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                    <option value="select">select</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="vehicle">Vehicle</label>
                                <input type="text" name="vehicle" id="vehicle" class="form-control" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="driver">Driver</label>
                                <input type="text" name="driver" id="driver" class="form-control" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:30px;visibility:hidden;">
                                <label for="tcds_per">TP</label>
                                <input type="text" name="tcds_per" id="tcds_per" class="form-control text-right" value="<?php echo $tcds_per; ?>" style="width:100px;" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Dc. No.</th>
                                        <th>Opening</th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ echo "<th>T. Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ echo "<th>E. Weight</th>"; } ?>
                                        <th>N. Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th style="width:50px;text-align:center;">TCS</th>
                                        <th>TCS Amount</th>
                                        <th>Cash Receipt</th>
                                        <th>Bank Method</th>
                                        <th>Bank Receipt</th>
                                        <th>Closing</th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                        <th style="width:20px;visibility:hidden;">RO</th>
                                        <th style="width:20px;visibility:hidden;">NA</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:180px;" onchange="fetch_customer_balance_master(this.id);fetch_paperrate_master(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="bookinvoice[]" id="bookinvoice[0]" class="form-control" style="width:100px;" /></td>
                                        <td><input type="text" name="opn_cbamt[]" id="opn_cbamt[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals[]" id="jals[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds[]" id="birds[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="tweight[]" id="tweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="eweight[]" id="eweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="nweight[]" id="nweight[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="item_amt[]" id="item_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td style="width:50px;text-align:center;"><input type="checkbox" name="tcds_chk[]" id="tcds_chk[0]" onchange="calculate_total_amt();" /></td>
                                        <td><input type="text" name="tcds_amt[]" id="tcds_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><input type="text" name="rct_amt1[]" id="rct_amt1[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><select name="bank_method1[]" id="bank_method1[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($bank_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $bank_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="rct_amt2[]" id="rct_amt2[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="cls_cbamt[]" id="cls_cbamt[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:25px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff[0]" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="finaltotal[]" id="finaltotal[0]" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right;">Total</th>
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
                                        <th><input type="text" name="tot_tcds_amt" id="tot_tcds_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th><input type="text" name="tot_rct_amt1" id="tot_rct_amt1" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_rct_amt2" id="tot_rct_amt2" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="width:20px;visibility:hidden;"></th>
                                        <th style="width:20px;visibility:hidden;"><input type="text" name="tot_finl_amt" id="tot_finl_amt" class="form-control text-right" style="width:20px;" readonly /></th>
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
                    window.location.href = "chicken_display_multiplesale1_ta.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var itemcode = document.getElementById("itemcode").value;
                    var warehouse = document.getElementById("warehouse").value;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Warehouse");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else{
                        var vcode = ""; var c = nweight = price = item_amt = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                vcode = document.getElementById("vcode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }

                                if(vcode == "select"){
                                    alert("Please select Customer in row: "+c);
                                    document.getElementById("vcode["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(nweight) == 0){
                                    alert("Please enter net weight in row: "+c);
                                    document.getElementById("nweight["+d+"]").focus();
                                    l = false;
                                }
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
                function fetch_tcds_per(){
                    var date = document.getElementById("date").value;
                    var tdsper = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TDS&cdate="+date;
                    var asynchronous = true;
                    tdsper.open(method, url, asynchronous);
                    tdsper.send();
                    tdsper.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tds = this.responseText;
                            if(tds != ""){
                                document.getElementById("tcds_per").value = tds;
                            }
                        }
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_customer_balance_master(this.id);fetch_paperrate_master(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="bookinvoice[]" id="bookinvoice['+d+']" class="form-control" style="width:100px;" /></td>';
                    html += '<td><input type="text" name="opn_cbamt[]" id="opn_cbamt['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="tweight[]" id="tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="eweight[]" id="eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="item_amt[]" id="item_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td style="width:50px;text-align:center;"><input type="checkbox" name="tcds_chk[]" id="tcds_chk['+d+']" onchange="calculate_total_amt();" /></td>';
                    html += '<td><input type="text" name="tcds_amt[]" id="tcds_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><input type="text" name="rct_amt1[]" id="rct_amt1['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><select name="bank_method1[]" id="bank_method1['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($bank_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $bank_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="rct_amt2[]" id="rct_amt2['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="cls_cbamt[]" id="cls_cbamt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:25px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="finaltotal[]" id="finaltotal['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '</tr>';

                    $('#row_body').append(html);
                    $('.select2').select2();
                    update_row_fields();
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
                    var jals = birds = tweight = eweight = nweight = price = item_amt = 0;
                    var tot_jals = tot_birds = tot_tweight = tot_eweight = tot_nweight = tot_item_amt = tot_tcds_amt = tot_finl_amt = tot_rct_amt1 = tot_rct_amt2 = bird_flag = 0;

                    icode = iname = "";
                    icode = document.getElementById("itemcode");
                    iname = icode.options[icode.selectedIndex].text;
                    bird_flag = iname.search(/Birds/i);
                    
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = price = item_amt = 0;

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
                        var item_amt = parseFloat(nweight) * parseFloat(price);
                        document.getElementById("item_amt["+d+"]").value = parseFloat(item_amt).toFixed(2);

                        /*TCDS Calculations*/
                        item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }
                        var tcds_chk = document.getElementById("tcds_chk["+d+"]");
                        var tcds_per = tcds_amt = finaltotal = famt1 = 0;
                        if(tcds_chk.checked == true){
                            tcds_per = document.getElementById("tcds_per").value; if(tcds_per == ""){ tcds_per = 0; }
                            if(parseFloat(tcds_per) > 0){
                                tcds_amt = ((parseFloat(tcds_per) / 100) * parseFloat(item_amt));
                            }
                        }
                        document.getElementById("tcds_amt["+d+"]").value = parseFloat(tcds_amt).toFixed(2);

                        tcds_amt = document.getElementById("tcds_amt["+d+"]").value; if(tcds_amt == ""){ tcds_amt = 0; }
                        famt1 = parseFloat(item_amt) + parseFloat(tcds_amt);

                        /*Round-Off Calculations*/
                        finaltotal = parseFloat(famt1).toFixed(0);
                        roundoff = parseFloat(finaltotal) - parseFloat(famt1);

                        document.getElementById("roundoff["+d+"]").value = parseFloat(roundoff).toFixed(2);
                        document.getElementById("finaltotal["+d+"]").value = parseFloat(finaltotal).toFixed(2);

                        /*Closing Balance Calculations*/
                        var cls_cbamt = 0;
                        opn_cbamt = document.getElementById("opn_cbamt["+d+"]").value; if(opn_cbamt == ""){ opn_cbamt = 0; }
                        finaltotal = document.getElementById("finaltotal["+d+"]").value; if(finaltotal == ""){ finaltotal = 0; }
                        rct_amt1 = document.getElementById("rct_amt1["+d+"]").value; if(rct_amt1 == ""){ rct_amt1 = 0; }
                        rct_amt2 = document.getElementById("rct_amt2["+d+"]").value; if(rct_amt2 == ""){ rct_amt2 = 0; }
                        cls_cbamt = (parseFloat(opn_cbamt) + parseFloat(finaltotal)) - (parseFloat(rct_amt1) + parseFloat(rct_amt2));
                        document.getElementById("cls_cbamt["+d+"]").value = parseFloat(cls_cbamt).toFixed(2);

                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_birds = parseFloat(tot_birds) + parseFloat(birds);
                        tot_tweight = parseFloat(tot_tweight) + parseFloat(tweight);
                        tot_eweight = parseFloat(tot_eweight) + parseFloat(eweight);
                        tot_nweight = parseFloat(tot_nweight) + parseFloat(nweight);
                        tot_item_amt = parseFloat(tot_item_amt) + parseFloat(item_amt);
                        tot_tcds_amt = parseFloat(tot_tcds_amt) + parseFloat(tcds_amt);
                        tot_finl_amt = parseFloat(tot_finl_amt) + parseFloat(finaltotal);
                        tot_rct_amt1 = parseFloat(tot_rct_amt1) + parseFloat(rct_amt1);
                        tot_rct_amt2 = parseFloat(tot_rct_amt2) + parseFloat(rct_amt2);
                    }
                    if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(0); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0); }
                    if(parseInt(tweight_flag) == 1){ document.getElementById("tot_tweight").value = parseFloat(tot_tweight).toFixed(2); }
                    if(parseInt(eweight_flag) == 1){ document.getElementById("tot_eweight").value = parseFloat(tot_eweight).toFixed(2); }
                    document.getElementById("tot_nweight").value = parseFloat(tot_nweight).toFixed(2);
                    document.getElementById("tot_item_amt").value = parseFloat(tot_item_amt).toFixed(2);
                    document.getElementById("tot_tcds_amt").value = parseFloat(tot_tcds_amt).toFixed(2);
                    document.getElementById("tot_finl_amt").value = parseFloat(tot_finl_amt).toFixed(2);
                    document.getElementById("tot_rct_amt1").value = parseFloat(tot_rct_amt1).toFixed(2);
                    document.getElementById("tot_rct_amt2").value = parseFloat(tot_rct_amt2).toFixed(2);
                }
                function update_row_fields(){
                    var incr = document.getElementById("incr").value;
                    icode = iname = "";
                    icode = document.getElementById("itemcode");
                    iname = icode.options[icode.selectedIndex].text;
                    bird_flag = iname.search(/Birds/i);

                    for(var d = 0;d <= incr;d++){
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
                    }
                    calculate_total_amt();
                }
                function fetch_customer_balance_master(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var vcode = document.getElementById("vcode["+d+"]").value;
                    document.getElementById("opn_cbamt["+d+"]").value = 0;

                    if(vcode != "select"){
                        var ob_amt = new XMLHttpRequest();
                        var method = "GET";
                        var url = "cus_fetchoutstandingbal.php?cuscode="+vcode+"&row_count="+d;
                        //window.open(url);
                        var asynchronous = true;
                        ob_amt.open(method, url, asynchronous);
                        ob_amt.send();
                        ob_amt.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var cus_bal1 = this.responseText;
                                if(cus_bal1 != ""){
                                    var cus_bal2 = cus_bal1.split("@");
                                    var cob_amt = cus_bal2[0];
                                    var rows = cus_bal2[3];
                                    document.getElementById("opn_cbamt["+rows+"]").value = parseFloat(cob_amt).toFixed(2);
                                }
                            }
                        }
                    }
                }
                function fetch_paperrate_master(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode["+d+"]").value;
                    var icode = document.getElementById("itemcode").value;
                    
                    if(date != "" && vcode != "select" && icode != "select"){
                        var prices = new XMLHttpRequest();
                        var method = "GET";
                        var url = "main_getitemprices_paperrate2.php?pname="+vcode+"&iname="+icode+"&mdate="+date;
                        //window.open(url);
                        var asynchronous = true;
                        prices.open(method, url, asynchronous);
                        prices.send();
                        prices.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var price_dt1 = this.responseText;
                                var price_dt2 = price_dt1.split("@");
                                var prate = price_dt2[0];
                                var ccode = price_dt2[1];
                                if(price_dt1 == "") {
                                    document.getElementById("price["+d+"]").value = "";
                                }
                                else {
                                    document.getElementById("price["+d+"]").value = parseFloat(prate).toFixed(2);
                                }
                            }
                        }
                    }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
