<?php
//broiler_edit_farmvisit.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['farmvisit'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
$link_active_flag = 1;
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
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
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sector_code = $sector_name = $emp_code = $emp_name = $db_emp_code = array();
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

        $sql = "SELECT * FROM `main_access` ORDER BY `empcode`,`active` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

        
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
        <?php
            $trnum = $_GET['trnum'];
            $sql = "SELECT * FROM `trip_sheet` WHERE `trnum` = '$trnum' AND `dflag` = '0' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $c = 0;
            while($row = mysqli_fetch_assoc($query)){
                $date = date("d.m.Y",strtotime($row['date']));
                $vch_number = $row['vch_number'];
                $meter_reading[$c] = $row['meter_reading'];
                $meter_image[$c] = $row['meter_image'];
                $trip_type[$c] = $row['trip_type'];
                $total_km[$c] = $row['total_km'];
                $remarks[$c] = $row['remarks'];
                $farm_code[$c] = $row['farm_code'];
                $added_empcode = $row['added_empcode'];
                $latitude[$c] = $row['latitude'];
                $longitude[$c] = $row['longitude'];
                $imei[$c] = $row['imei'];
                $c++;
            }
            $c = $c - 1;
            $i = $c;

        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content"> 
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Farm Visit</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_farmvisit.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="p-1 row">
                                        <div class="form-group" style="width:90px;">
                                            <label for="date">Date</label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" style="width:80px;" value="<?php echo $date; ?>" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label for="trnum">Transaction No.</label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" style="width:auto;" value="<?php echo $trnum; ?>" readonly />
                                        </div>
                                        <div class="form-group" style="width:160px;">
                                            <label for="added_empcode">Emp. Name</label>
                                            <select name="added_empcode" id="added_empcode" class="form-control select2" style="width:150px;">
                                                <?php /*foreach($emp_code as $ecode){ ?><option value="<?php echo $ecode; ?>" <?php if($added_empcode == $ecode){ echo "selected"; } ?>><?php echo $emp_name[$ecode]; ?></option><?php }*/ ?>
                                                <?php
                                                $ename = "";
                                                if(!empty($emp_name[$added_empcode])){
                                                    $ename = $emp_name[$added_empcode];
                                                }
                                                else if(!empty($db_emp_code[$added_empcode])){
                                                    $ename = $emp_name[$db_emp_code[$added_empcode]];
                                                }
                                                ?>
                                                <option value="<?php echo $added_empcode; ?>"><?php echo $ename; ?></option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:120px;">
                                            <label for="vch_number">Vehicle No.</label>
                                            <input type="text" name="vch_number" id="vch_number" class="form-control" style="width:110px;" value="<?php echo $vch_number; ?>" />
                                        </div>
                                    </div>
                                    <div class="p-1 row row_body2" style="margin-bottom:3px;">
                                        <table class="p-1">
                                            <thead>
                                                <tr style="text-align:center;">
                                                    <th><label>Trip Type<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Location</label></th>
                                                    <th><label>Meter Reading<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Meter Image<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Total Km</label></th>
                                                    <th><label>Remarks</label></th>
                                                    <th><label>IMEI</label></th>
                                                    <th><label>Latitude</label></th>
                                                    <th><label>Longitude</label></th>
                                                    <th><label>+/-</label></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body">
                                                <?php for($c = 0; $c <= $i;$c++){ ?>
                                                <tr id="row_no[<?php echo $c; ?>]">
                                                    <td><select name="trip_type[]" id="trip_type[<?php echo $c; ?>]" class="form-control select2" style="width:130px;"><option value="Start" <?php if($trip_type[$c] == "Start"){ echo "selected"; } ?>>Start</option><option value="Continue" <?php if($trip_type[$c] == "Continue"){ echo "selected"; } ?>>Continue</option><option value="End" <?php if($trip_type[$c] == "End"){ echo "selected"; } ?>>End</option></select></td>
                                                    <td><select name="farm_code[]" id="farm_code[<?php echo $c; ?>]" class="form-control select2" style="width:200px;"><option value="select" <?php if($farm_code[$c] == "select" || $farm_code[$c] == ""){ echo "selected"; } ?>>select</option><option value="Home" <?php if($farm_code[$c] == "Home"){ echo "selected"; } ?>>Home</option><option value="Office" <?php if($farm_code[$c] == "Office"){ echo "selected"; } ?>>Office</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($farm_code[$c] == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="meter_reading[]" id="meter_reading[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $meter_reading[$c]; ?>" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_kms();" /></td>
                                                    <td style="text-align:center;">
                                                        <div class="row justify-content-center align-items-center" style="text-align:center;" align="center">
                                                            <!--<div class="clip-upload">
                                                                <label for="meter_image1[<?php ///echo $c; ?>]"><i class="fa-solid fa-upload"></i></label>
                                                                <input type="file" class="file-input" name="meter_image1[]" id="meter_image1[<?php //echo $c; ?>]" style="width:10px;visibility:hidden;">
                                                            </div>-->
                                                            <div class="form-group"><a href="<?php echo $meter_image[$c]; ?>" target="_BLANK"><label for="meter_image2[<?php echo $c; ?>]"><i class="fa-solid fa-eye"></i></label></a></div>
                                                            <div class="form-group" style="visibility:hidden;"><input type="text" name="meter_image[]" id="meter_image[<?php echo $c; ?>]" class="form-control" value="<?php echo $meter_image[$c]; ?>" style="width:20px;" readonly /></div>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" name="total_km[]" id="total_km[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $total_km[$c]; ?>" style="width:120px;" readonly /></td>
                                                    <td><textarea name="remarks[]" id="remarks[<?php echo $c; ?>]" class="form-control" style="padding:0;padding-left:2px;width:100px;height:25px;"><?php echo $remarks[$c]; ?></textarea></td>
                                                    <td><input type="text" name="imei[]" id="imei[<?php echo $c; ?>]" class="form-control" value="<?php echo $imei[$c]; ?>" style="width:140px;" readonly /></td>
                                                    <td><input type="text" name="latitude[]" id="latitude[<?php echo $c; ?>]" class="form-control" value="<?php echo $latitude[$c]; ?>" style="width:120px;" readonly /></td>
                                                    <td><input type="text" name="longitude[]" id="longitude[<?php echo $c; ?>]" class="form-control" value="<?php echo $longitude[$c]; ?>" style="width:120px;" readonly /></td>
                                                    <?php
                                                    if($c == $i){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                                    else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                                    echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                                    if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                                    echo '</td>';
                                                    ?>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table><br/>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $i; ?>">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
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
                window.location.href = 'broiler_display_farmvisit.php?ccid='+ccid;
            }
            function checkval(){
                document.getElementById("submit").style.visibility = "hidden";
                item = document.getElementById("code").value;
                var l = true;
                if(item.match("select")){
                    alert("Kindly select Item in row: "+c);
                    document.getElementById("code").focus();
                    l = false;
                }
                else{
                    l = true;
                }
                if(l == true){
                    document.getElementById("submit").style.visibility = "hidden";
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="trip_type[]" id="trip_type['+d+']" class="form-control select2" style="width:130px;"><option value="Start">Start</option><option value="Continue">Continue</option><option value="End">End</option></select></td>';
                html += '<td><select name="farm_code[]" id="farm_code['+d+']" class="form-control select2" style="width:200px;"><option value="select">select</option><option value="Home">Home</option><option value="Office">Office</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="meter_reading[]" id="meter_reading['+d+']" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_kms();" /></td>';
                html += '<td></td>';
                html += '<td><input type="text" name="total_km[]" id="total_km['+d+']" class="form-control text-right" style="width:120px;" readonly /></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;padding-left:2px;width:100px;height:25px;"></textarea></td>';
                html += '<td><input type="text" name="imei[]" id="imei['+d+']" class="form-control" style="width:140px;" readonly /></td>';
                html += '<td><input type="text" name="latitude[]" id="latitude['+d+']" class="form-control" style="width:120px;" readonly /></td>';
                html += '<td><input type="text" name="longitude[]" id="longitude['+d+']" class="form-control" style="width:120px;" readonly /></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var x = confirm("If you delete this row, Meter images which are already stored in this row will be delete, Please click OK to proceed");
                if(x == true){
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                }
            }
            function calculate_total_kms(){
                var incr = document.getElementById("incr").value;
                var d = c = mread = fread = tread = 0;
                for(d = 0;d <= incr;d++){
                    if(d > 0){
                        mread = document.getElementById("meter_reading["+d+"]").value; if(mread == ""){ mread = 0; }
                        c = d - 1;
                        fread = document.getElementById("meter_reading["+c+"]").value; if(fread == ""){ fread = 0; }
                        tread = parseFloat(mread) - parseFloat(fread);
                        document.getElementById("total_km["+d+"]").value = parseFloat(tread).toFixed(0);
                    }
                    else{
                        document.getElementById("total_km["+d+"]").value = 0;
                    }
                }
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
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validatemobile(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 10){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more infarmvisition"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more infarmvisition";
}
?>