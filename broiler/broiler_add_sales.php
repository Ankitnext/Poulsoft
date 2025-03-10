<?php
//broiler_add_devfilename.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['devfilename'];
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
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
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
                            <div class="float-left"><h3 class="card-title">Add Inventory Transfer</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_devfilename.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date[]" id="date[0]" class="form-control datepicker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Dc No.</label>
							                <input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:90px;" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Item<b style="color:red;">&nbsp;*</b></label>
							                <select name="code[]" id="code[0]" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Quantity<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="quantity[]" id="quantity[0]" class="form-control" style="width:90px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Rate</label>
							                <input type="text" name="price[]" id="price[0]" class="form-control" style="width:90px;" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>From Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="fromwarehouse[]" id="fromwarehouse[0]" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>To Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="towarehouse[]" id="towarehouse[0]" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Vehicle</label>
							                <select name="vehicle_code[]" id="vehicle_code[0]" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>"><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Driver</label>
							                <select name="driver_code[]" id="driver_code[0]" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>"><?php echo $emp_name[$driver_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Remarks</label>
							                <textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:120px;height:25px;"></textarea>
                                        </div>
                                        <div class="form-group" id="action[0]" style="padding-top: 12px;"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="p-0 col-md-12" id="row_body">

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
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
                window.location.href = 'broiler_display_devfilename.php?ccid='+ccid;
            }
            function checkval(){
                var item = from_loc = to_loc = ""; var c = quantity = 0; var l = true;
                var a = document.getElementById("incr").value;
                for(var b = 0;b <= a;b++){
                    c = b + 1;
                    item = document.getElementById("code["+b+"]").value;
                    quantity = document.getElementById("quantity["+b+"]").value;
                    from_loc = document.getElementById("fromwarehouse["+b+"]").value;
                    to_loc = document.getElementById("towarehouse["+b+"]").value;
                    if(l == true){
                        if(item.match("select")){
                            alert("Kindly select Item in row: "+c);
                            document.getElementById("code["+b+"]").focus();
                            l = false;
                        }
                        else if(quantity.length == 0 || quantity == 0 || quantity == "" || quantity == "0.00" || quantity == "0" || quantity == 0.00){
                            alert("Kindly enter Quantity in row: "+c);
                            document.getElementById("quantity["+b+"]").focus();
                            l = false;
                        }
                        else if(from_loc.match("select")){
                            alert("Kindly select From Location in row: "+c);
                            document.getElementById("fromwarehouse["+b+"]").focus();
                            l = false;
                        }
                        else if(to_loc.match("select")){
                            alert("Kindly select To Location in row: "+c);
                            document.getElementById("towarehouse["+b+"]").focus();
                            l = false;
                        }
                        else{
                            l = true;
                        }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<div class="row" id="row_no['+d+']">';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Date<b style="color:red;">&nbsp;*</b></label><input type="text" name="date[]" id="date['+d+']" class="form-control datepicker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Dc No.</label><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:90px;" /></div>';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Item<b style="color:red;">&nbsp;*</b></label><select name="code[]" id="code['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Quantity<b style="color:red;">&nbsp;*</b></label><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control" style="width:90px;" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rate</label><input type="text" name="price[]" id="price['+d+']" class="form-control" style="width:90px;" /></div>';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">From Location<b style="color:red;">&nbsp;*</b></label><select name="fromwarehouse[]" id="fromwarehouse['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">To Location<b style="color:red;">&nbsp;*</b></label><select name="towarehouse[]" id="towarehouse['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group" style="width:130px;"><label class="labelrow" style="display:none;">Vehicle</label><select name="vehicle_code[]" id="vehicle_code['+d+']" class="form-control select2" style="width:120px;"><option value="select">select</option><?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>"><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group" style="width:130px;"><label class="labelrow" style="display:none;">Driver</label><select name="driver_code[]" id="driver_code['+d+']" class="form-control select2" style="width:120px;"><option value="select">select</option><?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>"><?php echo $emp_name[$driver_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Remarks</label><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:25px;"></textarea></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2();
                $( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
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
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more indevfilenameion"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more indevfilenameion";
}
?>