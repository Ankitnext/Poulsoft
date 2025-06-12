<?php
//broiler_add_generalsales1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['generalsales1'];
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
        $date = date("Y-m-d");
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; } $incr = $sales + 1;

        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'sales' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
        $a = 1; $tr_code = $prefix;
        for($j = 0;$j <= 16;$j++){
            if(!empty($inv_format[$j.":".$a])){
                if($inv_format[$j.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
                else if($inv_format[$j.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
                else if($inv_format[$j.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
                else if($inv_format[$j.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
                else if($inv_format[$j.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
                else if($inv_format[$j.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
                else if($inv_format[$j.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
                else if($inv_format[$j.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
                else if($inv_format[$j.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
                else if($inv_format[$j.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
                else if($inv_format[$j.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
                else if($inv_format[$j.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
                else if($inv_format[$j.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
                else if($inv_format[$j.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
                else if($inv_format[$j.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
                else if($inv_format[$j.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
                else{ }
            }
        }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_tcds_master` WHERE `type` = 'TCS' AND `active` = '1' AND `dflag` = '0' ORDER BY `value` ASC";
		$query = mysqli_query($conn,$sql); $tcds_code = $tcds_name = $tcds_value = array();
        while($row = mysqli_fetch_assoc($query)){ $tcds_code[$row['code']] = $row['code']; $tcds_name[$row['code']] = $row['description']; $tcds_value[$row['code']] = $row['value']; }
		
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
			
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'General Sale-1' AND `field_function` LIKE 'Stock Check' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $stockcheck_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Customer Sale Price' AND `field_function` LIKE 'Fetch Customer Price from Master' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $CusMastPrc_flag = mysqli_num_rows($query);
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
                            <div class="float-left"><h3 class="card-title">Add Sales</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_generalsales1.php" method="post" role="form" onSubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:80px;">
                                        </div>
                                        <div class="form-group">
                                            <label>Transaction No.</label>
                                            <input type="text" name="trno" id="trno" class="form-control" value="<?php echo $trnum; ?>" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $ven_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock Point / Feed Mill<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" style="width:120px;" onkeyup="validatename(this.id);" />
                                        </div>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Item</th>
                                                <th>Qty (In Kgs)</th>
                                                <th>Sale Rate</th>
                                                <th>Amount</th>
                                                <th></th>
                                                <th style="visibility:hidden;">AS</th>
                                                <th style="visibility:hidden;">AP</th>
                                                <th style="visibility:hidden;">AA</th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><select name="icode[]" id="icode[0]" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_customer_pricemaster(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="rcd_qty[]" id="rcd_qty[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="rate[]" id="rate[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="item_tamt[]" id="item_tamt[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="available_stock[]" id="available_stock[0]" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price[0]" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_amount[]" id="avg_amount[0]" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;">Total</th>
                                                <th><input type="text" name="tot_rqty" id="tot_rqty" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th><input type="text" name="tot_ramt" id="tot_ramt" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">
                                                    <div class="row justify-content-right align-items-right">
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>TCS</label>
                                                            <select name="tcds_code" id="tcds_code" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="none">None</option>
                                                                <?php foreach($tcds_code as $tcode){ ?><option value="<?php echo $tcode; ?>"><?php echo $tcds_name[$tcode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>Type</label>
                                                            <select name="tcds_type1" id="tcds_type1" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="add">Add</option>
                                                                <option value="deduct">Deduct</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th><div class="form-group"><label style="visibility:hidden;">Amount</label><input type="text" name="tcds_amt" id="tcds_amt" class="form-control text-right" style="width:90px;" readonly /></div></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">
                                                    <div class="form-group" style="text-align:right;">
                                                        <label>Round-Off</label>
                                                </th>
                                                <th><input type="text" name="round_off" id="round_off" class="form-control text-right" style="width:90px;" readonly /></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">
                                                    <div class="form-group" style="text-align:right;">
                                                        <label>Net Amount</label>
                                                </th>
                                                <th><input type="text" name="finl_amt" id="finl_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                            </tr>
                                        </tfoot>
                                    </table><br/><br/>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:20px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:20px;">
                                            <label>EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onClick="return_back()">Cancel</button>
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
                window.location.href = 'broiler_display_generalsales1.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value; var qty = price = total_amt = c = d = stock = 0; var icode = "";
                var l = true;
                //Re-calculate Item Amount
                for(d = 0;d <= incrs;d++){
                    qty = document.getElementById("rcd_qty["+d+"]").value;
                    price = document.getElementById("rate["+d+"]").value;
                    if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                    if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                    total_amt = parseFloat(qty) * parseFloat(price);
                    document.getElementById("item_tamt["+d+"]").value = total_amt.toFixed(2);
                }
                calculate_final_total_amount();

                var date = document.getElementById("date").value;
                var vcode = document.getElementById("vcode").value;
                var warehouse = document.getElementById("warehouse").value;
                if(date == ""){
                    alert("Kindly enter/select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(vcode.match("select")){
                    alert("Kindly select appropriate Customer");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else if(warehouse.match("select")){
                    alert("Kindly select appropriate Warehouse");
                    document.getElementById("warehouse").focus();
                    l = false;
                }
                else{
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        for(d = 0;d <= incrs;d++){
                            if(l == true){
                                c = d + 1;
                                qty = document.getElementById("rcd_qty["+d+"]").value;
                                stock = document.getElementById("available_stock["+d+"]").value;
                                if(parseFloat(qty) > parseFloat(stock)){
                                    alert("Stock not Available in row: "+c);
                                    document.getElementById("rcd_qty["+d+"]").focus();
                                    l = false;
                                }
                            }
                        }
                    }
                    else{ }

                    //Check Item Details
                    for(d = 0;d <= incrs;d++){
                        if(l == true){
                            c = d + 1;
                            icode = document.getElementById("icode["+d+"]").value;
                            qty = document.getElementById("rcd_qty["+d+"]").value;
                            price = document.getElementById("rate["+d+"]").value;
                            if(icode.match("select")){
                                alert("Kindly select appropriate Item in row: "+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(qty == "" || qty == "0.00" || qty == 0){
                                alert("Kindly enter Quantity in row: "+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            } 
                            else if(price == "" || price == "0.00" || price == 0){
                                alert("Kindly enter Rate in row: "+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                        }
                    }
                }
                
                if(l == true){
                    var answer = window.confirm("Are You Sure! You want to Save The Transaction.");
                    if (answer) {
                        //some code
                        return true;
                    }
                    else {
                        //some code
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
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_customer_pricemaster(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="rate[]" id="rate['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="available_stock[]" id="available_stock['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_amount[]" id="avg_amount['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                var avg_price = document.getElementById("avg_price["+d+"]").value; if(avg_price == ""){ avg_price = 0; }
                
                var item_tamt = parseFloat(rcd_qty) * parseFloat(rate);
                var avg_amount = parseFloat(rcd_qty) * parseFloat(avg_price);
                document.getElementById("item_tamt["+d+"]").value = item_tamt.toFixed(2);
                document.getElementById("avg_amount["+d+"]").value = avg_amount.toFixed(2);
                calculate_final_total_amount();
            }
            function calculate_final_total_amount(){
                var incr = document.getElementById("incr").value; var rcd_qty = tot_rqty = item_tamt = tot_ramt = 0;
                for(var d = 0;d <= incr;d++){
                    rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                    tot_rqty = parseFloat(tot_rqty) + parseFloat(rcd_qty);
                    item_tamt = document.getElementById("item_tamt["+d+"]").value; if(item_tamt == ""){ item_tamt = 0; }
                    tot_ramt = parseFloat(tot_ramt) + parseFloat(item_tamt);
                }
                document.getElementById("tot_rqty").value = tot_rqty.toFixed(2);
                document.getElementById("tot_ramt").value = tot_ramt.toFixed(2);
                //TCS Calculations
                var tcds_per = tcds_amt = net_amt = 0;
                var tcds_code = document.getElementById("tcds_code").value;
                var tcds_type1 = document.getElementById("tcds_type1").value;
                if(tcds_code != "none"){
                    <?php
                        foreach($tcds_code as $tcode){
                            $tvalue = $tcds_value[$tcode];
                            echo "if(tcds_code == '$tcode'){";
                            ?>
                            tcds_per = '<?php echo $tvalue; ?>';
                            <?php
                            echo "}";
                        }
                    ?>
                    tcds_amt = ((parseFloat(tcds_per) / 100) * tot_ramt).toFixed(2);
                    document.getElementById("tcds_amt").value = tcds_amt;
                }
                if(tcds_type1 == "deduct"){
                    net_amt = parseFloat(tot_ramt) - parseFloat(tcds_amt);
                }
                else{
                    net_amt = parseFloat(tot_ramt) + parseFloat(tcds_amt);
                }
                

                //Round-Off
                var round_off = finl_amt = 0;
                //finl_amt = parseFloat(tot_ramt).toFixed(0);
                finl_amt = parseFloat(net_amt).toFixed(0);
                round_off = parseFloat(finl_amt) - parseFloat(net_amt);
                document.getElementById("round_off").value = parseFloat(round_off).toFixed(2);
                

                document.getElementById("finl_amt").value = parseFloat(finl_amt).toFixed(2);
            }
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&trtype=Sale";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");
                            if(parseFloat(item_details[1]) < 0){ item_details[1] = 0; }
                            document.getElementById("available_stock["+d+"]").value = item_details[0];
                            document.getElementById("avg_price["+d+"]").value = item_details[1];
                        }
                        else{
                            //alert("Item Stock not available, Kindly check before saving ...!");
                            document.getElementById("available_stock["+d+"]").value = 0;
                            document.getElementById("avg_price["+d+"]").value = 0;
                        }
                    }
                }
            }
            function fetch_customer_pricemaster(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var cprc_flag = '<?php echo $CusMastPrc_flag; ?>';
                if(parseInt(cprc_flag) == 1){
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var icode = document.getElementById("icode["+d+"]").value;
                    
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_customer_pricemaster.php?vendor="+vcode+"&item_code="+icode+"&date="+date+"&row_count="+d+"&trtype=add";
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var cprc_dt1 = this.responseText;
                            var cprc_dt2 = cprc_dt1.split("@");
                            document.getElementById("rate["+d+"]").value = parseFloat(cprc_dt2[0]).toFixed(2);
                            calculate_total_amt(a);
                        }
                    }
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
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