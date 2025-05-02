<?php
//broiler_add_vouchers2.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['vouchers2'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $acc_code =  $acc_name = $sector_code = $sector_name =  $cash_code = $cash_name = $bank_code = $bank_name = array(); $cash_mode = $bank_mode = "";
        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` IN ('C','S') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['name']; }

        $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0' AND `visible_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        //$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'".$branch_access_filter2."".$line_access_filter2."".$farm_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        //while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }

        $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
            /*zoom: 0.8;*/
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Vouchers</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_vouchers2.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group"  style="width:110px;">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:90px;">
                                            <label>Bill No</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:80px;" onkeyup="validatename(this.id);" />
                                        </div>
                                        <div class="form-group"  style="width:190px;">
                                            <label>Farm/Sector<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group"  style="width:150px;">
                                            <label>Voucher Type<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vtype" id="vtype" class="form-control select2" style="width:140px;" onchange="update_account_modes();">
                                                <option value="payment">Payment Voucher</option>
                                                <option value="receipt">Receipt Voucher</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if($cash_mode != ""){ ?>
                                        <div class="form-group" style="width:40px;">
                                            <label for="group_code1">Cash</label>
                                            <input type="radio" name="group_code" id="group_code1" class="form-control" value="<?php echo $cash_mode; ?>" style="transform:scale(0.5);" onchange="update_accounts(this.id);" checked />
                                        </div>
                                        <?php } ?>
                                        <?php if($bank_mode != ""){ ?>
                                        <div class="form-group" style="width:40px;">
                                            <label for="group_code2">Bank</label>
                                            <input type="radio" name="group_code" id="group_code2" class="form-control" value="<?php echo $bank_mode; ?>" style="transform:scale(0.5);" onchange="update_accounts(this.id);" <?php if($cash_mode == ""){ echo "checked"; } ?> />
                                        </div>
                                        <?php } ?>
                                        <div class="form-group"  style="width:190px;">
                                            <label>CoA Account<b style="color:red;">&nbsp;*</b></label>
                                            <select name="coa_code" id="coa_code" class="form-control select2" style="width:180px;" onchange="update_firstrow();">
                                                <option value="select">select</option>
                                                <?php foreach($cash_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cash_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Sl.No.</th>
                                                <th>Description</th>
                                                <th>Dr/Cr</th>
                                                <th>Dr</th>
                                                <th>Cr</th>
                                                <th>Remarks</th>
                                                <th>+/-</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td style="text-align:center;">1</td>
                                                <td><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:180px;"><option value="select">select</option></select></td>
                                                <td><select name="crdr[]" id="crdr[0]" class="form-control select2" style="width:180px;"><option value="Cr">Cr</option></select></td>
                                                <td><input type="text" name="dr_amt[]" id="dr_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>
                                                <td><input type="text" name="cr_amt[]" id="cr_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>
                                                <th><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:28px;"></textarea></th>
                                                <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="text-align:right;">Total</td>
                                                <td><input type="text" name="tdr_amt" id="tdr_amt" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>
                                                <td><input type="text" name="tcr_amt" id="tcr_amt" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>
                                                <th></th>
                                                <td></td>
                                                <td style="visibility:hidden;"><input type="text" name="tamount" id="tamount" class="form-control text-right" style="width:90px;" readonly /></td>
                                            </tr>
                                        </tfoot>
                                    </table><br/>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
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
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_vouchers2.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden"; 
                var l = true; var tcr_amt = tdr_amt = 0;
                var date = document.getElementById("date").value;
                var warehouse = document.getElementById("warehouse").value;
                var coa_code = document.getElementById("coa_code").value;
                tcr_amt = document.getElementById("tcr_amt").value; if(tcr_amt == ""){ tcr_amt = 0; }
                tdr_amt = document.getElementById("tdr_amt").value; if(tdr_amt == ""){ tdr_amt = 0; }

                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(warehouse.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("warehouse").focus();
                    l = false;
                }
                else if(coa_code == "select"){
                    alert("Please select CoA Account");
                    document.getElementById("coa_code").focus();
                    l = false;
                }
                else if(parseFloat(tcr_amt) != parseFloat(tdr_amt)){
                    alert("Cr and Dr Total Amount not matching");
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var vcode = crdr = ""; var cr_amt = dr_amt = 0;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            vcode = document.getElementById("vcode["+d+"]").value;
                            crdr = document.getElementById("crdr["+d+"]").value;
                            cr_amt = document.getElementById("cr_amt["+d+"]").value; if(cr_amt == ""){ cr_amt = 0; }
                            dr_amt = document.getElementById("dr_amt["+d+"]").value; if(dr_amt == ""){ dr_amt = 0; }

                            if(vcode == "select" || vcode == ""){
                                alert("Please select Description");
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else if(crdr == "select" || crdr == ""){
                                alert("Please select CrDr");
                                document.getElementById("crdr["+d+"]").focus();
                                l = false;
                            }
                            else if(crdr == "Cr" && parseFloat(cr_amt) == 0){
                                alert("Please enter Cr Amount");
                                document.getElementById("cr_amt["+d+"]").focus();
                                l = false;
                            }
                            else if(crdr == "Dr" && parseFloat(dr_amt) == 0){
                                alert("Please enter Dr Amount");
                                document.getElementById("dr_amt["+d+"]").focus();
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
                d++; var html = ''; var slno = 0; slno = d + 1;
                document.getElementById("incr").value = d;

                var vtype = document.getElementById("vtype").value;
                if(vtype == "payment"){
                    html += '<tr id="row_no['+d+']">';
                    html += '<td style="text-align:center;">'+slno+'</td>';
                    html += '<td><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($acc_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $acc_name[$acode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="crdr[]" id="crdr['+d+']" class="form-control select2" style="width:180px;"><option value="Dr">Dr</option></select></td>';
                    html += '<td><input type="text" name="dr_amt[]" id="dr_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                    html += '<td><input type="text" name="cr_amt[]" id="cr_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>';
                    html += '<th><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:28px;"></textarea></th>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '</tr>';
                }
                else if(vtype == "receipt"){
                    html += '<tr id="row_no['+d+']">';
                    html += '<td style="text-align:center;">'+slno+'</td>';
                    html += '<td><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($acc_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $acc_name[$acode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="crdr[]" id="crdr['+d+']" class="form-control select2" style="width:180px;"><option value="Cr">Cr</option></select></td>';
                    html += '<td><input type="text" name="dr_amt[]" id="dr_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>';
                    html += '<td><input type="text" name="cr_amt[]" id="cr_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                    html += '<th><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:28px;"></textarea></th>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '</tr>';
                }
                else{ }

                $('#row_body').append(html);
                $('.select2').select2();
                calculate_finam_amt();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function update_account_modes(){
                var vtype = document.getElementById("vtype").value;
                document.getElementById("row_body").innerHTML = '';
                document.getElementById("incr").value = 0;
                var html = '';
                if(vtype == "payment"){
                    html += '<tr>';
                    html += '<td style="text-align:center;">1</td>';
                    html += '<td><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:180px;"><option value="select">select</option></select></td>';
                    html += '<td><select name="crdr[]" id="crdr[0]" class="form-control select2" style="width:180px;"><option value="Cr">Cr</option></select></td>';
                    html += '<td><input type="text" name="dr_amt[]" id="dr_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>';
                    html += '<td><input type="text" name="cr_amt[]" id="cr_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                    html += '<th><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:28px;"></textarea></th>';
                    html += '<td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '</tr>';
                }
                else if(vtype == "receipt"){
                    html += '<tr>';
                    html += '<td style="text-align:center;">1</td>';
                    html += '<td><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:180px;"><option value="select">select</option></select></td>';
                    html += '<td><select name="crdr[]" id="crdr[0]" class="form-control select2" style="width:180px;"><option value="Dr">Dr</option></select></td>';
                    html += '<td><input type="text" name="dr_amt[]" id="dr_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                    html += '<td><input type="text" name="cr_amt[]" id="cr_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly /></td>';
                    html += '<th><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:28px;"></textarea></th>';
                    html += '<td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '</tr>';
                }
                else{ }
                $('#row_body').append(html);
                $('.select2').select2();
                update_firstrow();
                calculate_finam_amt();
            }
            function update_accounts(a){
                var coa_mode = document.getElementById(a).value;

                removeAllOptions(document.getElementById("coa_code"));
                removeAllOptions(document.getElementById("vcode[0]"));

                if(coa_mode != ""){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_cashrbankdetails.php?coa_mode="+coa_mode+"&type=add";
                    //window.open(url);
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var coa_code = this.responseText;
							$('#coa_code').append(coa_code);
						}
					}
				}
            }
            function update_firstrow(){
                var data1 = document.getElementById("coa_code");
                var coa_code = data1.value;
                var coa_name = data1.options[data1.selectedIndex].text;
                removeAllOptions(document.getElementById("vcode[0]"));
                $('#vcode[0]').select2();
                myselect1 = document.getElementById("vcode[0]");
                theOption1=document.createElement("OPTION");
                theText1=document.createTextNode(coa_name);
                theOption1.value = coa_code;
                theOption1.appendChild(theText1);
                myselect1.appendChild(theOption1);
                $('#vcode[0]').select2();
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var crdr = document.getElementById("crdr["+d+"]").value;
                if(crdr == "Cr"){ var amount = document.getElementById("cr_amt["+d+"]").value; if(amount == ""){ amount = 0; } }
                else if(crdr == "Dr"){ var amount = document.getElementById("dr_amt["+d+"]").value; if(amount == ""){ amount = 0; } }
                else{ }
                document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                calculate_finam_amt();
            }
            function calculate_finam_amt(){
                var incr = document.getElementById("incr").value;
                var cr_amt = dr_amt = amount = tcr_amt = tdr_amt = tamount = 0;
                for(var d = 0; d <= incr;d++){
                    cr_amt = document.getElementById("cr_amt["+d+"]").value; if(cr_amt == ""){ cr_amt = 0; }
                    dr_amt = document.getElementById("dr_amt["+d+"]").value; if(dr_amt == ""){ dr_amt = 0; }
                    amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }

                    tcr_amt = parseFloat(tcr_amt) + parseFloat(cr_amt);
                    tdr_amt = parseFloat(tdr_amt) + parseFloat(dr_amt);
                    tamount = parseFloat(tamount) + parseFloat(amount);
                }
                document.getElementById("tcr_amt").value = parseFloat(tcr_amt).toFixed(2);
                document.getElementById("tdr_amt").value = parseFloat(tdr_amt).toFixed(2);
                document.getElementById("tamount").value = parseFloat(tamount).toFixed(2);
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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