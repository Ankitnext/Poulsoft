<?php
//poulsoft_add_mstpay1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['mstpay1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }
    if($user_type == "S"){ $acount = 1; } else{ foreach($alink as $add_access_flag){ if($add_access_flag == $link_childid){ $acount = 1; } } }
    if($acount == 1){
        //check and fetch date range
        global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        include "poulsoft_fetch_daterange_master.php";
        
        $date = date("d.m.Y");
        $sql = "SELECT * FROM `acc_types` WHERE `active` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $type_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $type_alist[$row['code']] = $row['code']; }

        $type_list = implode("','",$type_alist);
        $sql = "SELECT * FROM `acc_coa` WHERE `type` IN ('$type_list') AND `active` = '1' AND `dflag` = '0' AND `visible_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $acc_code = $acc_name = array();
        while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }

		$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `acc_coa` WHERE (`ctype` LIKE '%Cash%' OR `ctype` LIKE '%Bank%') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $chbk_code = $chbk_name = array();
		while($row = mysqli_fetch_assoc($query)){ $chbk_code[$row['code']] = $row['code']; $chbk_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            font-size: 13px;
        }
        /*::-webkit-scrollbar { width: 8px; height:8px; }
        .row_body2{
            width:100%;
            overflow-y: auto;
        }*/
        .row_body2{
            transform: scale(0.9);
            transform-origin: top left;
        }
        .select2-dropdown {
            transform: scale(0.9);
            transform-origin: top left;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <!--<div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Payment</h3></div>
                        </div>-->
                        <div class="pl-2 card-body">
                            <form action="poulsoft_save_mstpay1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <div class="pl-3 row">
                                        <div class="form-group" style="width:120px;">
                                            <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo $date; ?>" style="width:110px;" onchange="fetch_balace_amt();" readonly />
                                        </div>
                                        <div class="form-group" style="width:90px;">
                                            <label for="billno">Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:80px;" onkeyup="validatename(this.id);" />
                                        </div>
                                        <div class="form-group" style="width:200px;">
                                            <label for="sector">Branch</label>
                                            <select name="sector" id="sector" class="form-control select2" style="width:190px;" onchange="fetch_balace_amt();"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select>
                                        </div>
                                    </div>
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead>
                                            <tr>
                                                <th colspan="7" style="text-align:center;">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="7" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Cash/Bank Details</th>
                                            </tr>
                                            <tr>
                                                <th colspan="7">
                                                    <div class="pt-1 row">
                                                        <div class="form-group" style="width:80px;text-align:right;"><label for="from_account">Account<b style="color:red;">&nbsp;*</b>&ensp;</label></div>
                                                        <div class="form-group" style="width:300px;"><select name="from_account" id="from_account" class="form-control select2" style="width:290px;" onchange="fetch_balace_amt();"><option value="select">-select-</option><?php foreach($chbk_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $chbk_name[$ucode]; ?></option><?php } ?></select></div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr id="acc_bal1" style="visibility:hidden;">
                                                <th colspan="7">
                                                    <div class="row">
                                                        <div class="form-group" style="width:80px;text-align:right;"><label for="acc_bal2">Balance&ensp;&ensp;</label></div>
                                                        <div class="form-group" style="width:200px;"><input type="text" name="acc_bal2" id="acc_bal2" class="form-control" style="width:190px;border:none;background:inherit;color:brown;font-weight:bold;" readonly /></div>
                                                        <div class="form-group" style="width:30px;visibility:hidden;"><input type="text" name="acc_type" id="acc_type" class="form-control" style="width:30px;" readonly /></div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="7" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Payment Details</th>
                                            </tr>
                                        </thead>
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;"><label for="to_account[0]">Particulars<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label for="amount[0]">Amount<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label for="ref_no[0]">Ref. No.</label></th>
                                                <th style="text-align:center;"><label for="remarks[0]">Remarks</label></th>
                                                <th style="visibility:hidden;"><label for="tot_amt">Action</label></th>
                                                <th style="visibility:hidden;" id="c_date1"><label for="cheque_date[0]">Cheque Date</label></th>
                                                <th style="visibility:hidden;" id="p_type1"><label for="pay_type[0]">Pay Type</label></th>
                                                <th style="visibility:hidden;" id="b_type1"><label for="farm_batch[0]">Batch</label></th>
                                                <th style="visibility:hidden;" id="g_type1"><label for="gc_amount[0]">GC-Amount</label></th>
                                                <th style="visibility:hidden;"><label for="v_type[0]">Pay Type</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr style="line-height:30px;">
                                                <td><select name="to_account[]" id="to_account[0]" class="form-control select2" style="width:290px;" onchange="fetch_party_type(this.id);"><option value="select">-select-</option><?php foreach($acc_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $acc_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_tot_amt(this.id);" onchange="validateamount(this.id);"/></td>
                                                <td><input type="text" name="ref_no[]" id="ref_no[0]" class="form-control" style="width:90px;" onkeyup="validatename(this.id);" /></td>
                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>
                                                <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id);" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;" id="c_date2[0]"><input type="text" name="cheque_date[]" id="cheque_date[0]" class="form-control rc_datepicker" style="width:120px;" /></td>
                                                <td style="visibility:hidden;" id="p_type2[0]"><select name="pay_type[]" id="pay_type[0]" class="form-control select2" style="width:110px;"><option value="gc_pay">GC Pay</option><option value="adv_pay">Advance Pay</option><option value="addition_pay">Additional Pay</option></select></td>
                                                <td style="visibility:hidden;" id="b_type2[0]"><select name="farm_batch[]" id="farm_batch[0]" class="form-control select2" style="width:160px;" onchange="fetch_gc_amount(this.id);"><option value="select">select</option></select></td>
                                                <td style="visibility:hidden;" id="g_type2[0]"><input type="text" name="gc_amount[]" id="gc_amount[0]" class="form-control text-right" style="width:100px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="v_type[]" id="v_type[0]" class="form-control" style="width:30px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;">Total</th>
                                                <th><input type="text" name="tot_amt" id="tot_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <span>IN</span>
                                        <input type="text" name="incr" id="incr" class="form-control" value="0" style="padding:0;width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <span>EB</span>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group" align="center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                        <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval(){
				update_ebtn_status(1);
                var l = true;
                var date = document.getElementById("date").value;
               // var sector = document.getElementById("sector").value;
                var from_account = document.getElementById("from_account").value;
                //var acc_bal2 = document.getElementById("acc_bal2").value; if(acc_bal2 == ""){ acc_bal2 = 0; }
                var tot_amt = document.getElementById("tot_amt").value; if(tot_amt == ""){ tot_amt = 0; }
                
                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                // else if(sector == "" || sector == "select"){
                //     alert("Please select Branch");
                //     document.getElementById("sector").focus();
                //     l = false;
                // }
                else if(from_account == "" || from_account == "select"){
                    alert("Please select Account");
                    document.getElementById("from_account").focus();
                    l = false;
                }
                /*else if(parseInt(tot_amt) > parseInt(acc_bal2)){
                    alert("Total Amount is greater than Balance available. Please check once");
                    document.getElementById("tot_amt").focus();
                    l = false;
                }*/
                else{
                    var incr = document.getElementById("incr").value;
                    var to_account = ""; var amount = 0;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            e = d + 1;
                            to_account = document.getElementById("to_account["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }

                            if(to_account == "" || to_account == "select"){
                                alert("Please select Particulars in row: "+e);
                                document.getElementById("to_account["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(amount) == 0){
                                alert("Please enter Amount in row: "+e);
                                document.getElementById("amount["+d+"]").focus();
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
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'poulsoft_display_mstpay1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']" style="line-height:30px;">';
                html += '<td><select name="to_account[]" id="to_account['+d+']" class="form-control select2" style="width:290px;" onchange="fetch_party_type(this.id);"><option value="select">-select-</option><?php foreach($acc_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $acc_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_tot_amt(this.id);" onchange="validateamount(this.id);"/></td>';
                html += '<td><input type="text" name="ref_no[]" id="ref_no['+d+']" class="form-control" style="width:90px;" onkeyup="validatename(this.id);" /></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;" id="c_date2['+d+']"><input type="text" name="cheque_date[]" id="cheque_date['+d+']" class="form-control rc_datepicker" style="width:120px;" /></td>';
                html += '<td style="visibility:hidden;" id="p_type2['+d+']"><select name="pay_type[]" id="pay_type['+d+']" class="form-control select2" style="width:110px;"><option value="gc_pay">GC Pay</option><option value="adv_pay">Advance Pay</option><option value="addition_pay">Additional Pay</option></select></td>';
                html += '<td style="visibility:hidden;" id="b_type2['+d+']"><select name="farm_batch[]" id="farm_batch['+d+']" class="form-control select2" style="width:160px;" onchange="fetch_gc_amount(this.id);"><option value="select">select</option></select></td>';
                html += '<td style="visibility:hidden;" id="g_type2['+d+']"><input type="text" name="gc_amount[]" id="gc_amount['+d+']" class="form-control text-right" style="width:100px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="v_type[]" id="v_type['+d+']" class="form-control" style="width:30px;" readonly /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
                $( ".rc_datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12); } });
                upd_chk_date();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function fetch_balace_amt(){
                update_ebtn_status(1);
                var date = document.getElementById("date").value;
                var sector = document.getElementById("sector").value;
                var from_acc = document.getElementById("from_account").value;
                document.getElementById("acc_bal1").style.visibility = "hidden";
                document.getElementById("acc_bal2").value = 0;
                document.getElementById("acc_type").value = "";

                if(date == "" || sector == "" || sector == "select" || from_acc == "" || from_acc == "select"){ update_ebtn_status(0); upd_chk_date(); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "poulsoft_fetch_chbk_balamt1.php?date="+date+"&sector="+sector+"&from_acc="+from_acc+"&ttype=add";
                    //window.open(url);
                    var asynchronous = true;
                    oldqty.open(method, url, asynchronous);
                    oldqty.send();
                    oldqty.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var abal_dt1 = this.responseText;
                            var abal_dt2 = abal_dt1.split("[@$&]");
                            var err_flag = abal_dt2[0];
                            var err_msg = abal_dt2[1];
                            var acc_bal2 = abal_dt2[2]; if(acc_bal2 == ""){ acc_bal2 = 0; }
                            var acc_type = abal_dt2[3];
                            var crdr = "";
                            if(parseInt(err_flag) == 1){ alert(err_msg); }
                            else{
                                if(parseFloat(acc_bal2) >= 0){ crdr = "(Dr)"; } else{ crdr = "(Cr)"; }
                                if(parseFloat(acc_bal2) != 0){ document.getElementById("acc_bal1").style.visibility = "visible"; }
                                document.getElementById("acc_bal2").value = parseFloat(acc_bal2).toFixed(0)+" "+crdr;
                            }
                            document.getElementById("acc_type").value = acc_type;
                            update_ebtn_status(0);
                            upd_chk_date();
                        }
                    }
                }
            }
            function cal_tot_amt(){
                var incr = document.getElementById("incr").value;
                var amount = tot_amt = 0;
                for(var d = 0;d <= incr;d++){
                    amount = 0; amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                    tot_amt = parseFloat(tot_amt) + parseFloat(amount);
                }
                document.getElementById("tot_amt").value = parseFloat(tot_amt).toFixed(0);
            }
            function upd_chk_date(){
                update_ebtn_status(1);
                var acc_type = document.getElementById("acc_type").value;
                var incr = document.getElementById("incr").value;
                var amount = tot_amt = 0;
                if(acc_type == "bank"){
                    for(var d = 0;d <= incr;d++){
                        document.getElementById("c_date1").style.visibility = "visible";
                        document.getElementById("c_date2["+d+"]").style.visibility = "visible";
                    }
                }
                else{
                    for(var d = 0;d <= incr;d++){
                        document.getElementById("c_date1").style.visibility = "hidden";
                        document.getElementById("c_date2["+d+"]").style.visibility = "hidden";
                        document.getElementById("cheque_date["+d+"]").value = "";
                    }
                }
                update_ebtn_status(0);
            }
			function fetch_party_type(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var from_acc = document.getElementById("from_account").value;
                var to_acc = document.getElementById("to_account["+d+"]").value;
                document.getElementById("v_type["+d+"]").value = "";
                removeAllOptions(document.getElementById("farm_batch["+d+"]"));
                
                if(from_acc != "select" && to_acc != "select" && from_acc == to_acc){
                    alert("Account and Particulars should not be same, please check and try again.");
                    $('#to_account\\['+d+'\\]').select2();
                    document.getElementById("to_account["+d+"]").value = "select";
                    $('#to_account\\['+d+'\\]').select2();
                }
                else if(to_acc == "" || to_acc == "select"){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "poulsoft_fetch_partytype1.php?to_acc="+to_acc+"&sector="+sector+"&r_cnt="+d+"&ttype=add";
                    //window.open(url);
                    var asynchronous = true;
                    oldqty.open(method, url, asynchronous);
                    oldqty.send();
                    oldqty.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var abal_dt1 = this.responseText;
                            var abal_dt2 = abal_dt1.split("[@$&]");
                            var rows = abal_dt2[0];
                            var err_flag = abal_dt2[1];
                            var err_msg = abal_dt2[2];
                            var v_type = abal_dt2[3];
                            var p_amt = abal_dt2[4]; if(p_amt == ""){ p_amt = 0; }
                            var farm_batch = abal_dt2[5];

                            if(parseInt(err_flag) == 1){ alert(err_msg); }
                            else{
                                document.getElementById("v_type["+rows+"]").value = v_type;
                                $('#farm_batch\\['+d+'\\]').append(farm_batch);
                            }
                            update_ebtn_status(0);
                            upd_frmr_details();
                        }
                    }
                }
            }
            function upd_frmr_details(){
                update_ebtn_status(1);
                var incr = document.getElementById("incr").value;
                var f_cnt = 0;
                for(var d = 0;d <= incr;d++){
                    var v_type = document.getElementById("v_type["+d+"]").value;
                    if(v_type == "farmer"){
                        document.getElementById("p_type2["+d+"]").style.visibility = "visible";
                        document.getElementById("b_type2["+d+"]").style.visibility = "visible";
                        document.getElementById("g_type2["+d+"]").style.visibility = "visible";
                        f_cnt++;
                    }
                    else{
                        document.getElementById("p_type2["+d+"]").style.visibility = "hidden";
                        document.getElementById("b_type2["+d+"]").style.visibility = "hidden";
                        document.getElementById("g_type2["+d+"]").style.visibility = "hidden";
                    }
                }
                if(parseInt(f_cnt) > 0){
                    document.getElementById("p_type1").style.visibility = "visible";
                    document.getElementById("b_type1").style.visibility = "visible";
                    document.getElementById("g_type1").style.visibility = "visible";
                }
                else{
                    document.getElementById("p_type1").style.visibility = "hidden";
                    document.getElementById("b_type1").style.visibility = "hidden";
                    document.getElementById("g_type1").style.visibility = "hidden";
                }
                update_ebtn_status(0);
            }
            function fetch_gc_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var farms = document.getElementById("to_account["+d+"]").value;
                var farm_batch = document.getElementById("farm_batch["+d+"]").value;
                var pay_type = document.getElementById("pay_type["+d+"]").value;
                document.getElementById("gc_amount["+d+"]").value = "";
                if(farms != "select" && farm_batch != "select" && pay_type == "gc_pay"){
                    var batch_list = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_batchgcamt.php?farm_code="+farms+"&farm_batch="+farm_batch+"&row="+d;
                    var asynchronous = true;
                    //window.open(url);
                    batch_list.open(method, url, asynchronous);
                    batch_list.send();
                    batch_list.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var batch_dt1 = this.responseText;
                            var batch_dt2 = batch_dt1.split("[@$%&]");
                            var count = batch_dt2[0];
                            var row = batch_dt2[1];
                            var gc_amt = batch_dt2[2];
                            if(parseInt(count) > 0){
                                document.getElementById("gc_amount["+row+"]").value = parseFloat(gc_amt).toFixed(2);
                            }
                            else{
                                alert("Farmer GC not available for this Batch, Please check and try again.");
                            }
                        }
                    }
                }
            }
            function update_ebtn_status(a){
                if(parseInt(a) == 1){ document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden"; }
                else{ document.getElementById("submit").style.visibility = "visible"; document.getElementById("ebtncount").value = "0"; }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }

        </script>
        <script>
            //Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>