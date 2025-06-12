<?php
//broiler_add_multisales4.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['multisales41'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
$link_active_flag = 1;
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
    $acount = 1;
    if($acount == 1){
        $today = $fdate = date("d.m.Y"); $y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $farm_list = "";
		while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $supervisor_codes[$row['code']] = $row['code']; $supervisor_names[$row['code']] = $row['name']; }
		
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bag%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }
        
        $sql = "SELECT * FROM `main_tcds` WHERE `type` LIKE '%tcs%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $decimal_no = $row['flag']; }
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
        .form-group {
            padding-bottom: 10px;
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
                                <form action="broiler_save_multisales41.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" align="center">
                                            <label>Customer Sale</label>
                                            <input type="radio" name="sale_type" id="sale_type1" class="form-control" value="CusMBSale" style="width:90px;transform: scale(.7);" onclick="fetch_sale_type(this.id)" checked />
                                        </div>
                                        <div class="form-group" align="center">
                                            <label>Farmer Sale</label>
                                            <input type="radio" name="sale_type" id="sale_type2" class="form-control" value="FormMBSale" style="width:90px;transform: scale(.7);" onclick="fetch_sale_type(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:80px;">
                                        </div>
                                    </div><br/>
                                    <div class="row rows" style="margin-bottom:3px;">
                                        <div class="form-group">
                                            <label>Doc No.</label>
                                            <input type="text" name="billno[]" id="billno[0]" class="form-control" style="width:60px;" >
                                        </div>
                                        <div class="form-group" id="customer_sale[0]">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Farm<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Birds</label>
                                            <input type="text" name="birds[]" id="birds[0]" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Gross Weight<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="gross_weight[]" id="gross_weight[0]" class="form-control" placeholder="0.00" style="width:100px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Tare Weight<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="tare_weight[]" id="tare_weight[0]" class="form-control" placeholder="0.00" style="width:100px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Net Wt.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" readonly name="rcd_qty[]" id="rcd_qty[0]" class="form-control" placeholder="0.00" style="width:100px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Avg Wt.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="avg_wt[]" id="avg_wt[0]" class="form-control" placeholder="0.00" style="width:65px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Rate<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rate[]" id="rate[0]" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>TCS</label>
                                            <input type="checkbox" name="tcds_check[]" id="tcds_check[0]" class="form-control" style="transform: scale(.5);" onchange="fetch_tcds_value(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>TCS Amount</label>
                                            <input type="text" name="tcds_amount[]" id="tcds_amount[0]" class="form-control" style="width:90px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="text" name="item_tamt[]" id="item_tamt[0]" class="form-control" placeholder="0.00" style="width:90px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Lifting Supervisor</label>
                                            <select name="supervisor_code[]" id="supervisor_code[0]" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($supervisor_codes as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $supervisor_names[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label >Vehicle</label>
                                            <input type="text" name="vehicle_code[]" id="vehicle_code[0]" class="form-control" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label >Driver</label>
                                            <input type="text" name="driver_code[]" id="driver_code[0]" class="form-control" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:120px;height:25px;"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Receipt</label>
                                            <input type="text" name="receipt[]" id="receipt[0]" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" />
                                        </div>
                                        <div class="form-group" id="action[0]"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="padding-top:15px;width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a>
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label> </label>
                                            <input type="text" name="tcds_value[]" id="tcds_value[0]" class="form-control" style="width:30px;" >
                                        </div>
                                    </div>
                                    <div class="col-md-18" id="row_body"></div><br/><br/>
                                    <div class="row">
                                        <div class="form-group">
                                            <label >Total Birds</label>
                                            <input type="text" name="total_birds" id="total_birds" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Gross Wt.</label>
                                            <input type="text" name="total_gross_wt" id="total_gross_wt" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Tar Wt.</label>
                                            <input type="text" name="total_tare_wt" id="total_tare_wt" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Net Wt.</label>
                                            <input type="text" name="total_netwt" id="total_netwt" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Avg. Wt.</label>
                                            <input type="text" name="total_avgwt" id="total_avgwt" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Avg. Price</label>
                                            <input type="text" name="total_price" id="total_price" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Amount</label>
                                            <input type="text" name="total_amount" id="total_amount" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Receipt Total</label>
                                            <input type="text" name="total_rct_amount" id="total_rct_amount" class="form-control" style="width:120px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
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
                window.location.href = 'broiler_display_multisales41.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value;
                var warehouse = cus_code = ""; var rcd_qty = rate = c = 0;
                var l = true;
                for(var d = 0;d <= incrs;d++){
                    if(l == true){
                        c = d + 1;
                        rcd_qty = document.getElementById("rcd_qty["+d+"]").value;
                        rate = document.getElementById("rate["+d+"]").value;
                        warehouse = document.getElementById("warehouse["+d+"]").value;
                        if(document.getElementById("sale_type1").checked == true){
                            cus_code = document.getElementById("vcode["+d+"]").value;
                            if(cus_code.match("select")){
                                alert("Please select Customer in row:-"+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse.match("select")){
                                alert("Please select Sector/Farm in row:-"+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                                alert("Please enter Rcd Qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00"){
                                alert("Please enter Rate in row:-"+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                        else{
                            if(warehouse.match("select")){
                                alert("Please select Sector/Farm in row:-"+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                                alert("Please enter Rcd Qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00"){
                                alert("Please enter Rate in row:-"+c);
                                document.getElementById("rate["+d+"]").focus();
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
                var today = '<?php echo $today; ?>';
                
                html += '<div class="row rows" id="row_no['+d+']">';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Doc No.</label><input type="text" name="billno[]" id="billno['+d+']" class="form-control" style="width:60px;" ></div>';
                if(document.getElementById("sale_type1").checked == true){
                    html += '<div class="form-group" id="customer_sale['+d+']"><label class="labelrow" style="display:none;">Customer<b style="color:red;">&nbsp;*</b></label><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?></select></div>';
                }
                else{
                    html += '<div class="form-group" id="customer_sale['+d+']" style="display:none;"><label class="labelrow" style="display:none;">Customer<b style="color:red;">&nbsp;*</b></label><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?></select></div>';
                }
                
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Farm<b style="color:red;">&nbsp;*</b></label><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Birds</label><input type="text" name="birds[]" id="birds['+d+']" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Gross Weight<b style="color:red;">&nbsp;*</b></label><input type="text" name="gross_weight[]" id="gross_weight['+d+']" class="form-control" placeholder="0.00" style="width:100px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Tare Weight<b style="color:red;">&nbsp;*</b></label><input type="text" name="tare_weight[]" id="tare_weight['+d+']" class="form-control" placeholder="0.00" style="width:100px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Net Wt.<b style="color:red;">&nbsp;*</b></label><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control" placeholder="0.00" style="width:100px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Avg Wt.<b style="color:red;">&nbsp;*</b></label><input type="text" name="avg_wt[]" id="avg_wt['+d+']" class="form-control" placeholder="0.00" style="width:65px;" readonly ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rate<b style="color:red;">&nbsp;*</b></label><input type="text" name="rate[]" id="rate['+d+']" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">TCS<b style="color:red;">&nbsp;*</b></label><input type="checkbox" name="tcds_check[]" id="tcds_check['+d+']" class="form-control" onchange="fetch_tcds_value(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">TCS Amount<b style="color:red;">&nbsp;*</b></label><input type="text" name="tcds_amount[]" id="tcds_amount['+d+']" class="form-control" style="width:90px;" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Lifting Supervisor</label><select name="supervisor_code[]" id="supervisor_code['+d+']" class="form-control select2" style="width:120px;"><option value="select">select</option><?php foreach($supervisor_codes as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $supervisor_names[$scode]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Vehicle</label><input type="text" name="vehicle_code[]" id="vehicle_code['+d+']" class="form-control" style="width:120px;" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Driver</label><input type="text" name="driver_code[]" id="driver_code['+d+']" class="form-control" style="width:120px;" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Remarks</label><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:25px;"></textarea></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Receipt</label><input type="text" name="receipt[]" id="receipt['+d+']" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '<div class="form-group" style="visibility:hidden;"><label class="labelrow" style="display:none;"> </label><input type="text" name="tcds_value[]" id="tcds_value['+d+']" class="form-control" style="width:30px;" ></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2();
                $( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", maxDate: today, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_netpay();
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];

                var gross_weight = document.getElementById("gross_weight["+d+"]").value; if(gross_weight == ""){ gross_weight = 0; }
                var tare_weight = document.getElementById("tare_weight["+d+"]").value; if(tare_weight == ""){ tare_weight = 0; }

                document.getElementById("rcd_qty["+d+"]").value = (parseFloat(gross_weight) - parseFloat(tare_weight)).toFixed(2);

                var qty = document.getElementById("rcd_qty["+d+"]").value;
                var price = document.getElementById("rate["+d+"]").value;
                var birds = document.getElementById("birds["+d+"]").value;
                var tcds_check = document.getElementById("tcds_check["+d+"]");
                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                if(birds == "" || birds.length == 0 || birds == "0.00" || birds == "0"){ birds = 0; }
                var total_amt = parseFloat(qty) * parseFloat(price);

                /*TCDS Calculations*/
                if(tcds_check.checked == true){
                    var tcds_value = document.getElementById("tcds_value["+d+"]").value;
                    if(tcds_value == "" || tcds_value.length == 0 || tcds_value == "0.00" || tcds_value == "0"){ tcds_value = 0; }
                    if(tcds_value > 0){
                        var tcds_amount = ((parseFloat(tcds_value) / 100) * parseFloat(total_amt));
                    }
                    else{
                        var tcds_amount = 0;
                    }
                    document.getElementById("tcds_amount["+d+"]").value = tcds_amount;
                    total_amt = parseFloat(total_amt) + parseFloat(tcds_amount);
                }
                else{
                    var tcds_value = 0;
                    document.getElementById("tcds_value["+d+"]").value = 0;
                    document.getElementById("tcds_amount["+d+"]").value = 0;
                }
                document.getElementById("item_tamt["+d+"]").value = parseFloat(total_amt).toFixed(2);

                if(qty > 0 && birds > 0){
                    var avgwt = parseFloat(qty) / parseFloat(birds);
                }
                else{
                    var avgwt = 0;
                }
                if(avgwt == "" || avgwt.length == 0 || avgwt == "0.00" || avgwt == "0"){ avgwt = 0; }
                document.getElementById("avg_wt["+d+"]").value = avgwt.toFixed(2);

                var max_incr = document.getElementById("incr").value;
                var total_birds = total_gross_wt = total_tare_wt = total_netwt = total_avgwt = total_price = total_amount = rcts = total_rcts = bb = cc = dd = ee = ff = gg = hh = 0;
                for(var a = 0;a <= max_incr;a++){
                    bb = document.getElementById("birds["+a+"]").value; if(bb == ""){ bb = 0; }
                    total_birds = parseFloat(total_birds) + parseFloat(bb);

                    gg = document.getElementById("gross_weight["+a+"]").value; if(gg == ""){ gg = 0; }
                    total_gross_wt = parseFloat(total_gross_wt) + parseFloat(gg);

                    cc = document.getElementById("tare_weight["+a+"]").value; if(cc == ""){ cc = 0; }
                    total_tare_wt = parseFloat(total_tare_wt) + parseFloat(cc);

                    cc = document.getElementById("rcd_qty["+a+"]").value; if(cc == ""){ cc = 0; }
                    total_netwt = parseFloat(total_netwt) + parseFloat(cc);

                    rcts = document.getElementById("receipt["+a+"]").value; if(rcts == ""){ rcts = 0; }
                    total_rcts = parseFloat(total_rcts) + parseFloat(rcts);

                    //dd = document.getElementById("avg_wt["+a+"]").value; if(dd == ""){ dd = 0; }
                    //total_avgwt = parseFloat(total_avgwt) + parseFloat(dd);

                    //ee = document.getElementById("rate["+a+"]").value; if(ee == ""){ ee = 0; }
                    //total_price = parseFloat(total_price) + parseFloat(ee);
                    
                    ff = document.getElementById("item_tamt["+a+"]").value; if(ff == ""){ ff = 0; }
                    total_amount = parseFloat(total_amount) + parseFloat(ff);
                }
                if(total_netwt > 0 && total_birds > 0){
                    total_avgwt = total_netwt / total_birds;
                }
                else{
                    total_avgwt = 0;
                }
                if(total_netwt > 0 && total_amount > 0){
                    total_price = total_amount / total_netwt;
                }
                else{
                    total_price = 0;
                }
                if(total_birds == ""){ total_birds = 0; }
                if(total_gross_wt == ""){ total_gross_wt = 0; }
                if(total_tare_wt == ""){ total_tare_wt = 0; }
                if(total_netwt == ""){ total_netwt = 0; }
                if(total_avgwt == ""){ total_avgwt = 0; }
                if(total_price == ""){ total_price = 0; }
                if(total_amount == ""){ total_amount = 0; }
                if(total_rcts == ""){ total_rcts = 0; }
                document.getElementById("total_birds").value = total_birds.toFixed(2);
                document.getElementById("total_gross_wt").value = total_gross_wt.toFixed(2);
                document.getElementById("total_tare_wt").value = total_tare_wt.toFixed(2);
                document.getElementById("total_netwt").value = total_netwt.toFixed(2);
                document.getElementById("total_avgwt").value = total_avgwt.toFixed(2);
                document.getElementById("total_price").value = total_price.toFixed(2);
                document.getElementById("total_amount").value = total_amount.toFixed(2);
                document.getElementById("total_rct_amount").value = total_rcts.toFixed(2);

            }
            function fetch_sale_type(){
                var incrs = document.getElementById("incr").value;
                if(document.getElementById("sale_type1").checked == true){
                    for(var incr = 0;incr <= incrs;incr++){
                        document.getElementById("customer_sale["+incr+"]").style.display = "inline";
                        document.getElementById("receipt["+incr+"]").readOnly = false;
                    }
                }
                else{
                    for(var incr = 0;incr <= incrs;incr++){
                        document.getElementById("customer_sale["+incr+"]").style.display = "none";
                        document.getElementById("receipt["+incr+"]").value = "";
                        document.getElementById("receipt["+incr+"]").readOnly = true;
                    }
                } 
            }
            function fetch_tcds_value(a){
                var checkbox =document.getElementById(a);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(checkbox.checked === true){
                    var contact_type = "TCS";
                    var date = document.getElementById("date").value;
                        
                    var tcds_details = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_tcdsmaster.php?contact_type="+contact_type+"&date="+date;
                    //window.open(url);
                    var asynchronous = true;
                    tcds_details.open(method, url, asynchronous);
                    tcds_details.send();
                    tcds_details.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tcds_value = this.responseText;
                            document.getElementById("tcds_value["+d+"]").value = tcds_value;
                            calculate_total_amt(a);
                        }
                    }
                }
                else{
                    var tcds_value = 0;
                    document.getElementById("tcds_value["+d+"]").value = 0;
                    document.getElementById("tcds_amount["+d+"]").value = 0;
                    calculate_total_amt(a);
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                //alert(window.innerWidth);
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