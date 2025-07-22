<?php
//broiler_add_routeplan1.php

include "newConfig.php";
include "broiler_generate_trnum_details.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['routeplan1'];

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
        //check and fetch date range
        global $drng_cday; $drng_cday = 1; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        include "poulsoft_fetch_daterange_master.php";

        $date = date("Y-m-d");
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $fyear = "";
        $trno_dt1 = generate_transaction_details($date,"routeplan1","RPS","display",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

        $sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE 'Driver'"; $query = mysqli_query($conn,$sql); $desig_code = "";
        while($row = mysqli_fetch_assoc($query)){
            $desig_code = $row['code'];
        }

        $sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' AND `desig_code` = '$desig_code' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $driver_code = $driver_name = array();
        while($row = mysqli_fetch_assoc($query)){
            $driver_code[$row['code']] = $row['code'];
            $driver_name[$row['code']] = $row['name'];
        }

        $sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $vehicle_code = $vehicle_regno = array();
        while($row = mysqli_fetch_assoc($query)){
            $vehicle_code[$row['code']] = $row['code'];
            $vehicle_regno[$row['code']] = $row['registration_number'];
        }

        $sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0' AND `trtype` = 'breedname1' AND `trlink` = 'vendor_display_breedname.php' ORDER BY `id` DESC";
        $query = mysqli_query($conn,$sql); $cuslinecode = $cuslinename = array();
        while($row = mysqli_fetch_assoc($query)){
            $cuslinecode[$row['code']] = $row['code'];
            $cuslinename[$row['code']] = $row['description'];
        }

        
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
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Route Plan</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_routeplan1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Order Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="so_date" id="so_date" class="form-control range_picker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" readonly />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="button" name="fetch_orders" id="fetch_orders" class="btn btn-sm btn-success" onclick="fetch_sale_orders();">Fetch</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Transaction No.<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="invno" id="invno" class="form-control" style="width:110px;" value="<?php echo $trnum; ?>" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Route Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control range_picker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" />
                                        </div>
                                        <!-- <div class="form-group">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle" id="vehicle" class="form-control" style="width:100px;" onkeyup="validatename(this.id);" />
                                        </div> -->
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <select name="vehicle" id="vehicle" class="form-control select2">
                                                <option></option> <!-- Empty placeholder option -->
                                                <?php foreach($vehicle_code as $lcode){ ?>
                                                    <option value="<?php echo $vehicle_regno[$lcode]; ?>"><?php echo $vehicle_regno[$lcode]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver" id="driver" class="form-control" style="width:100px;" onkeyup="validatename(this.id);" />
                                        </div> -->
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <select name="driver" id="driver" class="form-control select2" >   
                                            <option></option>
                                                <?php foreach($driver_code as $lcode){ ?>
                                                <option value="<?php echo $lcode; ?>" ><?php echo $driver_name[$lcode]; ?></option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label>Route/Line</label>
                                            <input type="text" name="route_no" id="route_no" class="form-control" style="width:100px;" onkeyup="validatename(this.id);" />
                                        </div> -->
                                        <div class="form-group">
                                            <label>Route/Line</label>
                                            <select name="route_no" id="route_no" class="form-control select2" >   
                                            <option></option>
                                                <?php foreach($cuslinecode as $lcode){ ?>
                                                <option value="<?php echo $lcode; ?>" ><?php echo $cuslinename[$lcode]; ?></option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Company<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="company" id="company" class="form-control"  />
                                        </div>
                                        <div class="form-group">
                                            <label>Labour 1<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="labour1" id="labour1" class="form-control"  />
                                        </div>
                                        <div class="form-group">
                                            <label>Labour 2<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="labour2" id="labour2" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Lifter<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="lifter" id="lifter" class="form-control"  />
                                        </div>
                                        <div class="form-group">
                                            <label>Lifter Mob<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="lifter_mob" id="lifter_mob" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="p-0 col-md-12" id="row_body">

                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:25px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:25px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:25px;">
                                            <label>EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:25px;" readonly />
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
            //Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_routeplan1.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var l = true; var chk_size = 0;
                var checkboxes = document.querySelectorAll('input[name="slno[]"]');
                for (var d = 0; d < checkboxes.length; d++){ if(l == true){ if(checkboxes[d].checked == true){ chk_size = chk_size + 1; } } }

                if(parseInt(chk_size) <= 0){
                    alert("Please select atleast one sales order transaction to generate route plan.");
                    l = false;
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
            function fetch_sale_orders(){
                var date = document.getElementById("so_date").value;
                document.getElementById("row_body").innerHTML = '';
                if(date == "" || date == "01.01.1970"){
                    alert("Please select appropriate date.");
                    document.getElementById("so_date").focus();
                }
                else{
                    var so_info = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_saleorder_details.php?&date="+date;
                    //window.open(url);
                    var asynchronous = true;
                    so_info.open(method, url, asynchronous);
                    so_info.send();
                    so_info.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            so_dt1 = this.responseText;
                            var so_dt2 = so_dt1.split("[@$%&]");

                            $('#row_body').append(so_dt2[0]);
                            document.getElementById("incr").value = parseInt(so_dt2[1]).toFixed(0);
                            $('.select2').select2();
                        }
                    }
                }
            }
            function check_all_access(){
                var chk_all = document.getElementById("check_all");
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                if(chk_all.checked == true){
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = true;
                    }
                    calculate_totals();
                }
                else{
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = false;
                    }
                    calculate_totals();
                }
            }
            function calculate_totals(){
                var incr = document.getElementById("incr").value;
                var slno = ""; var boxes = order_qty = tot_boxes = tot_oqty = 0;
                for(var d = 0;d <= incr;d++){
                    slno = document.getElementById("slno["+d+"]");
                    if(slno.checked == true){
                        boxes = document.getElementById("boxes["+d+"]").value; if(boxes == ""){ boxes = 0; }
                        order_qty = document.getElementById("order_qty["+d+"]").value; if(order_qty == ""){ order_qty = 0; }
                        tot_boxes = parseFloat(tot_boxes) + parseFloat(boxes);
                        tot_oqty = parseFloat(tot_oqty) + parseFloat(order_qty);
                    }
                }
                document.getElementById("tot_boxes").value = parseFloat(tot_boxes).toFixed(2);
                document.getElementById("tot_oqty").value = parseFloat(tot_oqty).toFixed(2);
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(5); document.getElementById(x).value = parseFloat(b).toFixed(2); }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
        <script>
            //Date Range selection
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
        </script>
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
<script>
$(document).ready(function() {
    $('#vehicle').select2({
        tags: true,
        placeholder: 'Select or enter vehicle',
        allowClear: true,
        width: '100%',
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        },
        insertTag: function (data, tag) {
            // Put custom entry at the end
            data.push(tag);
        }
    });

    $('#driver').select2({
        tags: true,
        placeholder: 'Select or enter driver',
        allowClear: true,
        width: '100%',
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        },
        insertTag: function (data, tag) {
            // Put custom entry at the end
            data.push(tag);
        }
    });

    $('#route_no').select2({
        tags: true,
        placeholder: 'Select or enter route',
        allowClear: true,
        width: '100%',
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        },
        insertTag: function (data, tag) {
            // Put custom entry at the end
            data.push(tag);
        }
    });
});
</script>