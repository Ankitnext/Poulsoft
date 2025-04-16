<?php
//broiler_add_dp_purchase1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['dp_purchase1'];
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
        $today = date("Y-m-d");
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $farm_list = "";
        while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
        
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$today' AND `tdate` >= '$today' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
		$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bag%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }
    
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
            padding-left: 2px;
            padding-right: 0px;
        }
        .form-group{
            margin: 0 3px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Purchases</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_dp_purchase1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;">
                                        </div>
                                        <div class="form-group">
                                            <label>Supplier<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:180px;" onchange="fetch_grn_trnum(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:180px;">
                                            <label>GRN No.</label>
                                            <select name="gr_trnum" id="gr_trnum" class="form-control select2" style="width:170px;" onchange="fetch_grn_details(this.id);">
                                                <option value="select">-select-</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:85px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>trnum</label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $code; ?>" style="width:130px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" style="width:160px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" style="width:160px;" >
                                        </div>
                                        <div class="form-group" style="width:180px;">
                                            <label>Amount On</label>
                                            <select name="amt_cal_basedon" id="amt_cal_basedon" class="form-control select2" style="width:170px;">
                                                <option value="SentQty" selected >Sent Qty</option>
                                                <option value="RcvdQty">Received Qty</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="p-0 row">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th><label>Item<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Sent Qty<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Rcv Qty<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Free Qty</label></th>
                                                    <th><label>Short Qty</label></th>
                                                    <th><label>Rate<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Debit Flag</label></th>
                                                    <th><label>Short Amt</label></th>
                                                    <th><label>Disc. %</label></th>
                                                    <th><label>Disc. &#8377</label></th>
                                                    <th><label>GST</label></th>
                                                    <th><label>Amount</label></th>
                                                    <th><label>Sector/Farm<b style="color:red;">&nbsp;*</b></label></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body"></tbody>
                                        </table>
                                    </div><br/><br/>
                                    <div class="col-md-12" id="freight_body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Type</label>
                                                    <select name="freight_type" id="freight_type" class="form-control select2" onchange="calculate_netpay()">
                                                        <option value="select">select</option>
                                                        <option value="include">Include</option>
                                                        <option value="exclude">Exclude</option>
                                                        <option value="inbill">In Bill</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay Later</label>
                                                    <input type="radio" name="pay_type" id="pay_type1" class="form-control" value="PayLater" style="width:90px;transform: scale(.7);" onclick="fetch_freight_coa_account(this.id)" checked />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay In Bill</label>
                                                    <input type="radio" name="pay_type" id="pay_type2" class="form-control" value="PayInBill" style="width:90px;transform: scale(.7);" onclick="fetch_freight_coa_account(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pay Account</label>
                                                    <select name="freight_pay_acc" id="freight_pay_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Account</label>
                                                    <select name="freight_acc" id="freight_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `acc_coa` WHERE `freight_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Amount</label>
                                                    <input type="text" name="freight_amount" id="freight_amount" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id);calculate_netpay();" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div>
                                    </div><br/><br/>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Bag Type</label>
                                                    <select name="bag_code" id="bag_code" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php foreach($bag_code as $carrier){ ?><option value="<?php echo $carrier; ?>"><?php echo $bag_name[$carrier]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>No.of Bags</label>
                                                    <input type="text" name="bag_count" id="bag_count" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Batch No.</label>
                                                    <input type="text" name="batch_no" id="batch_no" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Expiry Date</label>
                                                    <input type="text" name="exp_date" id="exp_date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>TDS</label>
                                                    <input type="checkbox" name="tcds_per" id="tcds_per" class="form-control" value="<?php echo $tdsper; ?>" style="transform: scale(.7);" onchange="calculate_netpay()" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>TDS Amount</label>
                                                    <input type="text" name="tcds_amount" id="tcds_amount" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Roundoff</label>
                                                    <input type="text" name="round_off" id="round_off" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Net Amount</label>
                                                    <input type="text" name="finl_amt" id="finl_amt" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:visible;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
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
                window.location.href = 'broiler_display_dp_purchase1.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var sup_code = document.getElementById("vcode").value;
                var gr_trnum = document.getElementById("gr_trnum").value;
                var incrs = document.getElementById("incr").value;
                var item_code = warehouse = ""; var rcd_qty = rate = c = 0;
                var l = true;
                
                if (sup_code.match("select")) {
                    alert("Please select Supplier");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else if (gr_trnum.match("select")) {
                    alert("Please select Goods Receipt Transaction No.");
                    document.getElementById("gr_trnum").focus();
                    l = false;
                }
                else {
                    for (var d = 0; d <= incrs; d++) {
                        if (l == true) {
                            c = d + 1;
                            item_code = document.getElementById("icode["+d+"]").value;
                            rcd_qty = document.getElementById("rcd_qty["+d+"]").value;
                            rate = document.getElementById("rate["+d+"]").value;
                            warehouse = document.getElementById("warehouse["+d+"]").value;
                            if (item_code.match("select")) {
                                alert("Please select Item in row:-" + c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if (rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00") {
                                alert("Please enter Rcd Qty in row:-" + c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if (rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00") {
                                alert("Please enter Rate in row:-" + c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else if (warehouse.match("select")) {
                                alert("Please select Sector/Farm in row:-" + c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else { }
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
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var amt_on = document.getElementById("amt_cal_basedon").value;
                var snt_qty = document.getElementById("snt_qty["+d+"]").value;      if(snt_qty == ""){ snt_qty = 0; }
                var rcd_qty = document.getElementById("rcd_qty["+d+"]").value;      if(rcd_qty == ""){ rcd_qty = 0; }
                var price = document.getElementById("rate["+d+"]").value;           if(price == ""){ price = 0; }
                var dis_amt = document.getElementById("dis_amt["+d+"]").value;      if(dis_amt == ""){ dis_amt = 0; }
                var gst_per1 = document.getElementById("gst_per["+d+"]").value;
                if(!gst_per1.match("select")){
                    var gst_per2 = gst_per1.split("@");
                    var gst_per = gst_per2[1];
                }
                else{
                    var gst_per = 0; 
                }
                if(gst_per == "" || gst_per.length == 0 || gst_per == "0.00" || gst_per == "0"){ gst_per = 0; }

                if(amt_on == "SentQty"){
                    var total_amt = parseFloat(snt_qty) * parseFloat(price);
                }
                else{
                    var total_amt = parseFloat(rcd_qty) * parseFloat(price);
                }
                var short_qty = parseFloat(snt_qty) - parseFloat(rcd_qty); if(short_qty == ""){ short_qty = 0; }
                var short_amt = parseFloat(short_qty) * parseFloat(price); if(short_amt == ""){ short_amt = 0; }
                document.getElementById("short_qty["+d+"]").value = parseFloat(short_qty).toFixed(2);
                document.getElementById("short_amt["+d+"]").value = parseFloat(short_amt).toFixed(2);
                if(parseFloat(short_qty) > 0){ document.getElementById("debit_flag["+d+"]").checked = true; }
                else{ document.getElementById("debit_flag["+d+"]").checked = false; }

                if(dis_amt > 0){
                    total_amt = parseFloat(total_amt) - parseFloat(dis_amt);
                }
                if(gst_per > 0){
                    var gst_value = ((parseFloat(gst_per) / 100) * total_amt);
                    total_amt = parseFloat(total_amt) + parseFloat(gst_value);
                }
                document.getElementById("item_tamt["+d+"]").value = total_amt;
                calculate_netpay();
            }
            function fetch_discount_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var amt_on = document.getElementById("amt_cal_basedon["+d+"]").value;
                var snt_qty = document.getElementById("snt_qty["+d+"]").value;      if(snt_qty == ""){ snt_qty = 0; }
                var rcd_qty = document.getElementById("rcd_qty["+d+"]").value;      if(rcd_qty == ""){ rcd_qty = 0; }       
                var price = document.getElementById("rate["+d+"]").value;           if(price == ""){ price = 0; }

                if(amt_on == "SentQty"){
                    var total_amt = parseFloat(snt_qty) * parseFloat(price);        if(total_amt == ""){ total_amt = 0; }
                }
                else{
                    var total_amt = parseFloat(rcd_qty) * parseFloat(price);        if(total_amt == ""){ total_amt = 0; }
                }
                
                if(b[0].match("dis_per")){
                    var dis_per = document.getElementById("dis_per["+d+"]").value;
                    if(dis_per == "" || dis_per.length == 0 || dis_per == "0.00" || dis_per == "0"){
                        document.getElementById("dis_per["+d+"]").value = 0;
                        document.getElementById("dis_amt["+d+"]").value = 0;
                        calculate_total_amt(a);
                    }
                    else{
                        var dis_value = ((parseFloat(dis_per) / 100) * total_amt);
                        if(dis_value == "NaN" || dis_value.length == 0 || dis_value == 0){ dis_value = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_value;
                        calculate_total_amt(a);
                    }
                    
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                    if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){
                        document.getElementById("dis_per["+d+"]").value = 0;
                        document.getElementById("dis_amt["+d+"]").value = 0;
                        calculate_total_amt(a);
                    }
                    else{
                        var dis_per = ((parseFloat(dis_amt) * 100) / total_amt);
                        //var dis_per = (((parseFloat(dis_amt) * 100) / total_amt) * 100);
                        if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                        calculate_total_amt(a);
                    }
                }
            }
            function calculate_netpay() {
                var incr = parseInt(document.getElementById("incr").value);
                var total_amount = 0; var tamt = 0; var net_amount = 0;
                for (var d = 0; d <= incr; d++) {
                    tamt = document.getElementById("item_tamt["+d+"]").value;
                    if (tamt == "" || tamt == "0" || tamt.length == 0 || tamt == "0.00") { tamt = 0; }
                    total_amount = parseFloat(total_amount) + parseFloat(tamt);
                }
                var freight_amount = document.getElementById("freight_amount").value;
                if (freight_amount == "" || freight_amount == "0" || freight_amount.length == 0 || freight_amount == "0.00") { freight_amount = 0; }
                if (freight_amount > 0) {
                    var freight_type = document.getElementById("freight_type").value;
                    if (!freight_type.match("select")) {
                        if (freight_type.match("include")) {
                            net_amount = parseFloat(total_amount) - parseFloat(freight_amount);
                        }
                        else if (freight_type.match("exclude")) {
                            net_amount = parseFloat(total_amount);
                        }
                        else if (freight_type.match("inbill")) {
                            net_amount = parseFloat(total_amount) + parseFloat(freight_amount);
                        }
                        else {
                            net_amount = parseFloat(total_amount);
                        }
                    }
                    else {
                        net_amount = total_amount;
                    }
                }
                else {
                    net_amount = total_amount;
                }
                var tcds_flag = document.getElementById("tcds_per");
                if (tcds_flag.checked == true) {
                    var rqty = rprc = item_amount = 0;
                    for (d = 0; d <= incr; d++) {
                        rqty = document.getElementById("rcd_qty["+d+"]").value;
                        rprc = document.getElementById("rate["+d+"]").value;
                        if (rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00") { rqty = 0; }
                        if (rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00") { rprc = 0; }
                        if (rqty > 0 && rprc > 0) {
                            item_amount = (parseFloat(item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                        }
                    }
                    var tcds_per = document.getElementById("tcds_per").value;
                    var tcds_amount = ((parseFloat(tcds_per) / 100) * item_amount).toFixed(2);
                    document.getElementById("tcds_amount").value = tcds_amount;
                    net_amount = parseFloat(net_amount) + parseFloat(tcds_amount);
                }
                else {
                    document.getElementById("tcds_amount").value = "";
                }
                var final_amt = net_amount.toFixed(0);
                var roundoff = parseFloat(final_amt) - parseFloat(net_amount);

                document.getElementById("round_off").value = roundoff.toFixed(2);
                document.getElementById("finl_amt").value = final_amt;
            }
            function fetch_grn_trnum(a){
                var vcode = document.getElementById(a).value;
                document.getElementById("row_body").innerHTML = "";
                document.getElementById("incr").value = 0;
                removeAllOptions(document.getElementById("gr_trnum"));

                if(vcode != "select"){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_pc_grntrnums.php?vcode="+vcode;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_list = this.responseText;
                            if(item_list.length > 0){
                                $('#gr_trnum').append(item_list);
                            }
                            else{
                                alert("Goods Receipt details are not available for this Supplier \n Kindly check and try again");
                            }
                        }
                    }
                }
            }
            function fetch_grn_details(){
                var vcode = document.getElementById("vcode").value;
                var gr_trnum = document.getElementById("gr_trnum").value;
                document.getElementById("row_body").innerHTML = "";
                document.getElementById("incr").value = 0;

                if(vcode != "select" && gr_trnum != "select"){
                    var fetchgc = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_dp_goodsreceipt_details.php?vcode="+vcode+"&grtrnum="+gr_trnum;
                    //window.open(url);
					var asynchronous = true;
					fetchgc.open(method, url, asynchronous);
					fetchgc.send();
					fetchgc.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var pur_ind = this.responseText;
							var grn_details = pur_ind.split("@$&");
                            if(pur_ind == ""){
                                alert("Goods Receipt Details are not available for this Supplier \n Kindly check and try again");
                                calculate_netpay(); fetch_freight_details();
                            }
                            else{
                                $('#row_body').append(grn_details[0]);
                                document.getElementById("incr").value = grn_details[1];
                                $('.select2').select2();
                                calculate_netpay(); fetch_freight_details();
                            }
                        }
                    }
                }
            }
            function fetch_freight_details(){
                var gr_trnum = document.getElementById("gr_trnum").value;
                document.getElementById("freight_body").innerHTML = "";

                if(gr_trnum != "select"){
                    var fetchgc = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_dp_freight_details.php?grtrnum="+gr_trnum;
                    //window.open(url);
					var asynchronous = true;
					fetchgc.open(method, url, asynchronous);
					fetchgc.send();
					fetchgc.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var pur_ind = this.responseText;
							var grn_details = pur_ind.split("@$&");
                            if(pur_ind == ""){
                                calculate_netpay();
                            }
                            else{
                                $('#freight_body').append(grn_details[0]);
                                $('.select2').select2();
                                calculate_netpay();
                            }
                        }
                    }
                }
            }
            function fetch_freight_coa_account(a){
                removeAllOptions(document.getElementById("freight_pay_acc"));
                myselect = document.getElementById("freight_pay_acc"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(a.match("pay_type1")){
                    <?php
					$sql="SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else{
                    <?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
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