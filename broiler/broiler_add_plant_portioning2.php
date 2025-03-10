<?php
//broiler_add_plant_portioning2.php
include "newConfig.php";
include "broiler_generate_trnum_details.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['plant_portioning2'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
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
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $date = date("Y-m-d");
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $fyear = "";
        $trno_dt1 = generate_transaction_details($date,"plant_portioning2","PPG","display",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

        $sql = "SELECT * FROM `main_item_category` WHERE `plant_portioning` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `plant_sort_order`,`description` ASC";
        $query = mysqli_query($conn,$sql); $imcat_code = $imcat_name = array();
        while($row = mysqli_fetch_assoc($query)){ $imcat_code[$row['code']] = $row['code']; $imcat_name[$row['code']] = $row['description']; }

		$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Processing Plant%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $office_code = array();
		while($row = mysqli_fetch_assoc($query)){ $office_code[$row['code']] = $row['code']; }

        $office_list = implode("','", $office_code);
		$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$office_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = "";
		while($row = mysqli_fetch_assoc($query)){ $sector_code = $row['code']; $sector_name = $row['description']; }
?>
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
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <!--<div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Grading</h3></div>
                        </div>-->
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_plant_portioning2.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="custom-control custom-radio"><input type="radio" name="receive_type" id="receive_type1" class="custom-control-input custom-control-input-purple" value="plant_jobwork" onclick="update_link_trnums();" /><label for="receive_type1" class="custom-control-label">Customer</label></div>&ensp;&ensp;
                                                <div class="custom-control custom-radio"><input type="radio" name="receive_type" id="receive_type2" class="custom-control-input custom-control-input-purple" value="plant_received" onclick="update_link_trnums();" checked /><label for="receive_type2" class="custom-control-label">Received Batch</label></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div><br/>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group" style="width:230px;visibility:hidden;" id="customer_selection">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
							                <select name="cus_code" id="cus_code" class="form-control select2" style="width:220px;" data-placeholder="Select Customer" data-dropdown-css-class="select2-purple" onchange="fetch_bird_grading_trnums();">
                                                <option value="select">-select-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:230px;">
                                            <label>Grading No.<b style="color:red;">&nbsp;*</b></label>
							                <select name="batch_no" id="batch_no" class="form-control select2" style="width:220px;" data-placeholder="Select Batch No." data-dropdown-css-class="select2-purple" onchange="fetch_plant_grading_trnum_details();">
                                                <option value="select">-select-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Bill No</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:100px;" />
                                        </div>
                                        <div class="form-group" style="width:140px;">
                                            <label>Batch No.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="mnubch_no" id="mnubch_no" class="form-control" style="width:130px;" />
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <table style="width:50%;">
                                            <tr style="background-color:#85c1e9;">
                                                <th style="width:40%;"><label>Portion Type</label></th>
                                                <td></td>
                                                <td>
                                                    <table>
                                                    <?php
                                                        $c = 0;
                                                        echo "<tr>";
                                                        foreach($imcat_code as $ccode){
                                                            $c++;
                                                            echo '<td><input type="radio" name="portion_type" id="portion_type['.$c.']" class="form-control" style="transform: scale(.7);" value="'.$ccode.'" onclick="clear_portioning_alist();" /></td>';
                                                            echo '<td><label for="portion_type['.$c.']" style="margin-top:5px;">'.$imcat_name[$ccode].'</label></td>';
                                                        }
                                                        echo "</tr>";
                                                    ?>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="m-0 p-0 col-md-12">
                                        <div class="m-0 p-0 row">
                                            <div class="m-0 p-0 col-md-5">
                                                <div class="row justify-content-center align-items-center">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th style="visibility:hidden;">IM</th>
                                                                <th style="width:130px;">Sizes</th>
                                                                <th style="width:90px;">A.Stock</th>
                                                                <th style="width:90px;">No's</th>
                                                                <th style="width:90px;">Kg's</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="isize_items"></tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th></th>
                                                                <th>Total</th>
                                                                <td><input type="text" name="total_stock" id="total_stock" class="form-control text-right" style="width:90px;" readonly /></td>
                                                                <td><input type="text" name="totis_birds" id="totis_birds" class="form-control text-right" style="width:90px;" /></td>
                                                                <td><input type="text" name="totis_weight" id="totis_weight" class="form-control text-right" style="width:90px;" /></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th style="width:160px;">Item Code</th>
                                                            <th style="width:160px;">Item Name</th>
                                                            <th style="width:90px;">Kg's</th>
                                                            <th style="width:90px;">Yield %</th>
                                                            <th style="width:90px;">Remarks</th>
                                                            <th style="width:60px;">+/-</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="portioning_items">
                                                        <tr>
                                                            <td><select name="port_icode1[]" id="port_icode1[0]" class="form-control select2" style="min-width:130px;" onchange="update_row_item(this.id);"><option value="select">-select-</option></select></td>
                                                            <td><select name="port_icode[]" id="port_icode[0]" class="form-control select2" style="min-width:380px;" onchange="update_row_item(this.id);"><option value="select">-select-</option></select></td>
                                                            <td><input type="text" name="port_weight[]" id="port_weight[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_tot_iport_details();" onchange="validateamount(this.id);" /></td>
                                                            <td><input type="text" name="port_yield[]" id="port_yield[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                                            <td><textarea name="remarks2[]" id="remarks2[]" class="form-control" style="width:90px;"></textarea></td>
                                                            <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="2" style="text-align:right;">Total</th>
                                                            <td><input type="text" name="tot_port_weight" id="tot_port_weight" class="form-control text-right" style="width:90px;" readonly /></td>
                                                            <td><input type="text" name="tot_port_yield" id="tot_port_yield" class="form-control text-right" style="width:90px;" /></td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="2" style="text-align:right;">Process Wastage & Loss</th>
                                                            <td><input type="text" name="tot_pl_weight" id="tot_pl_weight" class="form-control text-right" style="width:90px;" readonly /></td>
                                                            <td><input type="text" name="tot_pl_yield" id="tot_pl_yield" class="form-control text-right" style="width:90px;" /></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="remarks" id="remarks" class="form-control" style="width:100%;"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:25px;">
                                            <label>IN</label>
                                            <input type="text" name="cincr" id="cincr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:25px;">
                                            <label>IN</label>
                                            <input type="text" name="pincr" id="pincr" class="form-control" value="<?php echo $c; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:25px;">
                                            <label>IN</label>
                                            <input type="text" name="iincr" id="iincr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:25px;">
                                            <label>EC</label>
                                            <input type="text" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
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
                window.location.href = 'broiler_display_plant_portioning2.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var date = document.getElementById("date").value;
                var mnubch_no = document.getElementById("mnubch_no").value;
                var cincr = document.getElementById("cincr").value; if(cincr == ""){ cincr = 0; }
                var pincr = document.getElementById("pincr").value; if(pincr == ""){ pincr = 0; }
                var iincr = document.getElementById("iincr").value; if(iincr == ""){ iincr = 0; }
                var totis_weight = document.getElementById("totis_weight").value; if(totis_weight == ""){ totis_weight = 0; }
                var tot_port_weight = document.getElementById("tot_port_weight").value; if(tot_port_weight == ""){ tot_port_weight = 0; }
                var l = true;

                if(date == ""){
                    alert("Kindly select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(mnubch_no == ""){
                    alert("Please enter Batch No.");
                    document.getElementById("mnubch_no").focus();
                    l = false;
                }
                else if(parseInt(pincr) == 0){
                    alert("Item Categories are not added in Item category Master. Please check and try again");
                    l = false;
                }
                else if(parseInt(cincr) == 0){
                    alert("Please select Batch No.");
                    l = false;
                }
                /*else if(parseInt(iincr) == 0){
                    alert("Please select atleast one option Type.");
                    l = false;
                }*/
                else if(parseInt(totis_weight) == 0){
                    alert("Please enter atleast one Weight From Item Size Section.");
                    l = false;
                }
                else if(parseInt(tot_port_weight) == 0){
                    alert("Please enter atleast one Potioning Weight From Portion Type Section.");
                    l = false;
                }
                else{ }

                if(l == true){
                    var x = window.confirm("Are You Sure! You want to Save The Transaction.");
                    if(x == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
					    document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function update_link_trnums(){
                var receive_type1 = document.getElementById("receive_type1");
                var receive_type2 = document.getElementById("receive_type2");
                var cus_code = document.getElementById("cus_code").value;
                var receive_type = "";
                if(receive_type1.checked == true){ receive_type = receive_type1.value; } else if(receive_type2.checked == true){ receive_type = receive_type2.value; }
                
                removeAllOptions(document.getElementById("cus_code"));
                removeAllOptions(document.getElementById("batch_no"));
                document.getElementById("isize_items").innerHTML = '';
                document.getElementById("cincr").value = 0;
                cal_tot_isize_details();

                if(receive_type == "plant_jobwork"){
                    document.getElementById("customer_selection").style.visibility = "visible";
                    if(receive_type != ""){
                        var prices = new XMLHttpRequest();
                        var method = "GET";
                        var url = "broiler_fetch_plant_grading_customers.php?receive_type="+receive_type;
                        //window.open(url);
                        var asynchronous = true;
                        prices.open(method, url, asynchronous);
                        prices.send();
                        prices.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var cus_dt1 = this.responseText;
                                $('#cus_code').append(cus_dt1);
                            }
                        }
                    }
                }
                else if(receive_type == "plant_received"){
                    document.getElementById("customer_selection").style.visibility = "hidden";
                    fetch_bird_grading_trnums();
                }
                else{ }
                cal_tot_isize_details();
            }
            function fetch_bird_grading_trnums(){
                var receive_type1 = document.getElementById("receive_type1");
                var receive_type2 = document.getElementById("receive_type2");
                var cus_code = document.getElementById("cus_code").value;
                removeAllOptions(document.getElementById("batch_no"));
                document.getElementById("isize_items").innerHTML = '';
                document.getElementById("cincr").value = 0;
                cal_tot_isize_details();
                var receive_type = "";
                if(receive_type1.checked == true){ receive_type = receive_type1.value; } else if(receive_type2.checked == true){ receive_type = receive_type2.value; }
                
                if(receive_type == "plant_jobwork" && cus_code == "select"){
                    alert("Please select Customer to fetch Details");
                    document.getElementById("cus_code").focus();
                }
                else{
                    if(receive_type != ""){
                        var prices = new XMLHttpRequest();
                        var method = "GET";
                        var url = "broiler_fetch_plant_grading_trnums2.php?cus_code="+cus_code+"&receive_type="+receive_type;
                        //window.open(url);
                        var asynchronous = true;
                        prices.open(method, url, asynchronous);
                        prices.send();
                        prices.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var trnum_dt1 = this.responseText;
                                $('#batch_no').append(trnum_dt1);
                                cal_tot_isize_details();
                            }
                        }
                    }
                }
            }
            function fetch_plant_grading_trnum_details(){
                var batch_no = document.getElementById("batch_no").value;
                document.getElementById("isize_items").innerHTML = '';
                document.getElementById("cincr").value = 0;
                document.getElementById("mnubch_no").readOnly = false;

                cal_tot_isize_details();
                if(batch_no == "" || batch_no == "select"){
                    alert("Please select Transaction No.");
                    document.getElementById("batch_no").focus();
                }
                else{
                    var prices = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_plant_grading_trdetails2.php?batch_no="+batch_no;
                    //window.open(url);
                    var asynchronous = true;
                    prices.open(method, url, asynchronous);
                    prices.send();
                    prices.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var trnum_dt1 = this.responseText;
                            var trnum_dt2 = trnum_dt1.split("[@$&]");
                            var isize_dt = trnum_dt2[0];
                            var cincr = trnum_dt2[1]; if(cincr == ""){ cincr = 0; }
                            var mnubch_no = trnum_dt2[2];
                            //$('#isize_items').append(isize_dt);
                            document.getElementById("isize_items").innerHTML = isize_dt;
                            document.getElementById("cincr").value = parseInt(cincr);
                            cal_tot_isize_details();
                            $('.select2').select2();

                            if(mnubch_no != ""){
                                document.getElementById("mnubch_no").value = mnubch_no;
                                document.getElementById("mnubch_no").readOnly = true;
                            }
                        }
                    }
                }
            }
            function cal_tot_isize_details(){
                var cincr = document.getElementById("cincr").value; if(cincr == ""){ cincr = 0; }
                var astock = isize_birds = isize_weight = tstock = tbirds = tweight = 0;
                if(parseInt(cincr) > 0){
                    for(d = 1;d <= cincr;d++){
                        astock = document.getElementById("astock["+d+"]").value; if(astock == ""){ astock = 0; }
                        isize_birds = document.getElementById("isize_birds["+d+"]").value; if(isize_birds == ""){ isize_birds = 0; }
                        isize_weight = document.getElementById("isize_weight["+d+"]").value; if(isize_weight == ""){ isize_weight = 0; }
                        tstock = parseFloat(tstock) + parseFloat(astock);
                        tbirds = parseFloat(tbirds) + parseFloat(isize_birds);
                        tweight = parseFloat(tweight) + parseFloat(isize_weight);
                    }
                }
                document.getElementById("total_stock").value = parseFloat(tstock).toFixed(2);
                document.getElementById("totis_birds").value = parseFloat(tbirds).toFixed(0);
                document.getElementById("totis_weight").value = parseFloat(tweight).toFixed(2);
                cal_tot_iport_details();
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("iincr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="port_icode1[]" id="port_icode1['+d+']" class="form-control select2" style="min-width:130px;" onchange="update_row_item(this.id);"><option value="select">-select-</option></select></td>';
                html += '<td><select name="port_icode[]" id="port_icode['+d+']" class="form-control select2" style="min-width:380px;" onchange="update_row_item(this.id);"><option value="select">-select-</option></select></td>';
                html += '<td><input type="text" name="port_weight[]" id="port_weight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_tot_iport_details();" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="port_yield[]" id="port_yield['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                html += '<td><textarea name="remarks2[]" id="remarks2['+d+']" class="form-control" style="width:90px;"></textarea></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#portioning_items').append(html);
                $('.select2').select2();
                var prx = "port_icode1["+d+"]"; fetch_item_slist(prx);
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("iincr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                cal_tot_isize_details();
            }
            function clear_portioning_alist(){
                var d = 0;
                document.getElementById("iincr").value = d;
                document.getElementById("portioning_items").innerHTML = '';
                
                var html = '';
                html += '<tr>';
                html += '<td><select name="port_icode1[]" id="port_icode1['+d+']" class="form-control select2" style="min-width:130px;" onchange="update_row_item(this.id);"><option value="select">-select-</option></select></td>';
                html += '<td><select name="port_icode[]" id="port_icode['+d+']" class="form-control select2" style="min-width:380px;" onchange="update_row_item(this.id);"><option value="select">-select-</option></select></td>';
                html += '<td><input type="text" name="port_weight[]" id="port_weight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_tot_iport_details();" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="port_yield[]" id="port_yield['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                html += '<td><textarea name="remarks2[]" id="remarks2['+d+']" class="form-control" style="width:90px;"></textarea></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                html += '</tr>';

                $('#portioning_items').append(html);
                $('.select2').select2();
                var prx = "port_icode1["+d+"]"; fetch_item_slist(prx);
            }
            function update_row_item(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(b[0] == "port_icode1"){
                    var icode = document.getElementById("port_icode1["+d+"]").value;
                    $('#port_icode\\[' + d + '\\]').select2();
                    document.getElementById("port_icode["+d+"]").value = icode;
                    $('#port_icode\\[' + d + '\\]').select2();
                }
                else{
                    var icode = document.getElementById("port_icode["+d+"]").value;
                    $('#port_icode1\\[' + d + '\\]').select2();
                    document.getElementById("port_icode1["+d+"]").value = icode;
                    $('#port_icode1\\[' + d + '\\]').select2();
                }
            }
            function fetch_item_slist(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                removeAllOptions(document.getElementById("port_icode1["+d+"]"));
                removeAllOptions(document.getElementById("port_icode["+d+"]"));
                
                var icat_list = "";
                var pincr = document.getElementById("pincr").value; if(pincr == ""){ pincr = 0; }
                for(e = 1;e <= pincr;e++){
                    if(document.getElementById("portion_type["+e+"]")){
                        if(document.getElementById("portion_type["+e+"]").checked == true){
                            if(icat_list == ""){ icat_list = document.getElementById("portion_type["+e+"]").value; }
                            else{ icat_list = icat_list+"@"+document.getElementById("portion_type["+e+"]").value; }
                        }
                    }
                }
                if(icat_list != ""){
                    var prices = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_catwise_items1.php?icat_list="+icat_list+"&r_cnt="+d;
                    //window.open(url);
                    var asynchronous = true;
                    prices.open(method, url, asynchronous);
                    prices.send();
                    prices.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var trnum_dt1 = this.responseText;
                            var trnum_dt2 = trnum_dt1.split("[@$&]");
                            var itm_scode = trnum_dt2[0];
                            var itm_sname = trnum_dt2[1];
                            var r_cnt = trnum_dt2[2];
                            $('#port_icode1\\[' + r_cnt + '\\]').append(itm_scode);
                            $('#port_icode\\[' + r_cnt + '\\]').append(itm_sname);
                        }
                    }
                }
            }
            /*function fetch_portioning_items(){
                
                document.getElementById("portioning_items").innerHTML = '';

                if(pincr <= 0){
                    alert("Portioning Item Categories are not added from Item Category Master. Please add Categories in Master and try again.");
                }
                else{
                    var icat_list = "";
                    for(d = 1;d <= pincr;d++){
                        if(document.getElementById("portion_type["+d+"]")){
                            if(document.getElementById("portion_type["+d+"]").checked == true){
                                if(icat_list == ""){ icat_list = document.getElementById("portion_type["+d+"]").value; }
                                else{ icat_list = icat_list+"@"+document.getElementById("portion_type["+d+"]").value; }
                            }
                        }
                    }
                    if(icat_list != ""){
                        var prices = new XMLHttpRequest();
                        var method = "GET";
                        var url = "broiler_fetch_plant_portioning_items2.php?icat_list="+icat_list;
                        //window.open(url);
                        var asynchronous = true;
                        prices.open(method, url, asynchronous);
                        prices.send();
                        prices.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var trnum_dt1 = this.responseText;
                                var trnum_dt2 = trnum_dt1.split("[@$&]");
                                var isize_dt = trnum_dt2[0];
                                var iincr = trnum_dt2[1]; if(iincr == ""){ iincr = 0; }
                                //$('#portioning_items').append(isize_dt);
                                document.getElementById("portioning_items").innerHTML = isize_dt;
                                document.getElementById("iincr").value = parseInt(iincr);
                                $('.select2').select2();
                                cal_tot_iport_details();
                            }
                        }
                    }
                    else{ }
                }
            }*/
            function cal_tot_iport_details(){
                var iincr = document.getElementById("iincr").value; if(iincr == ""){ iincr = 0; }
                if(parseInt(iincr) >= 0){
                    var tweight = yield_per = 0;
                    var totis_weight = document.getElementById("totis_weight").value; if(totis_weight == ""){ totis_weight = 0; }
                    for(d = 0;d <= iincr;d++){
                        var port_weight = document.getElementById("port_weight["+d+"]").value; if(port_weight == ""){ port_weight = 0; }
                        if(parseFloat(totis_weight) > 0){
                            yield_per = 0; yield_per = parseFloat(((parseFloat(port_weight) / parseFloat(totis_weight)) * 100)).toFixed(2);
                            document.getElementById("port_yield["+d+"]").value = parseFloat(yield_per).toFixed(2);
                        }
                        else{
                            document.getElementById("port_yield["+d+"]").value = 0;
                        }
                        tweight += parseFloat(port_weight);
                    }
                    var tot_pl_weight = parseFloat(totis_weight) - parseFloat(tweight);
                    if(parseFloat(totis_weight) > 0){
                        yield_per = 0; yield_per = parseFloat(((parseFloat(tweight) / parseFloat(totis_weight)) * 100)).toFixed(2);
                        document.getElementById("tot_port_yield").value = parseFloat(yield_per).toFixed(2);
                        tot_pl_yield = 0; tot_pl_yield = parseFloat(((parseFloat(tot_pl_weight) / parseFloat(totis_weight)) * 100)).toFixed(2);
                        document.getElementById("tot_pl_yield").value = parseFloat(tot_pl_yield).toFixed(2);
                    }
                    else{
                        document.getElementById("tot_port_yield").value = 0;
                        document.getElementById("tot_pl_yield").value = 0;
                    }
                    document.getElementById("tot_port_weight").value = parseFloat(tweight).toFixed(2);
                    document.getElementById("tot_pl_weight").value = parseFloat(tot_pl_weight).toFixed(2);
                    //cal_iport_cwise_item_total();
                }
            }
            /*function cal_iport_cwise_item_total(){
                var iincr = document.getElementById("iincr").value; if(iincr == ""){ iincr = 0; }
                if(parseInt(iincr) > 0){
                    var tweight = yield_per = tcat_pwt = 0; var icats = old_icats = ic_tlist = "";
                    var totis_weight = document.getElementById("totis_weight").value; if(totis_weight == ""){ totis_weight = 0; }
                    for(d = 1;d <= iincr;d++){
                        icats = document.getElementById("icats["+d+"]").value;
                        pweight = document.getElementById("port_weight["+d+"]").value; if(pweight == ""){ pweight = 0; }

                        if(old_icats != icats){
                            tcat_pwt = 0;
                            old_icats = icats;
                        }
                        tcat_pwt = parseFloat(tcat_pwt) + parseFloat(pweight);
                        yield_per = 0;
                        if(parseFloat(totis_weight) > 0){ yield_per = parseFloat(((parseFloat(tcat_pwt) / parseFloat(totis_weight)) * 100)).toFixed(2); }
                        
                        var inx1 = "twht_"+icats; var inx2 = "tyld_"+icats;
                        document.getElementById(inx1).value = parseFloat(tcat_pwt).toFixed(2);
                        document.getElementById(inx2).value = parseFloat(yield_per).toFixed(2);
                    }
                }
            }
            */
            update_link_trnums();
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot2.php"; ?>
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